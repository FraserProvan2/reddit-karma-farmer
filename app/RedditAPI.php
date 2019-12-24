<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RedditAPI
{
	/*--------------------------------------------------------------------------
	| Properties
	|--------------------------------------------------------------------------*/

	private $access_token;
	private $expires;

	/**
	 * Construct, handle Auth per request, use cached access token
	 * if avaliable, keep track if expired 
	 */
	public function __construct()
	{
		$this->handleAuth();
	}

	/*--------------------------------------------------------------------------
	| Authentication
	|--------------------------------------------------------------------------*/

	private function handleAuth()
	{
		// session()->flush();

		// expired or new session
		if (!session()->get('access_token') || !session()->get('expires') || now() > session()->get('expires')) {
			$access_token_obj = $this->getAccessToken();
			$this->expires = Carbon::now()->addSeconds($access_token_obj->expires_in);

			session()->put('access_token', $access_token_obj->access_token);
			session()->put('expires', $this->expires);

			Log::debug('RedditAPI: new access token created');
		}  else {
			Log::debug('RedditAPI: using cached access token');
		}
	
		$this->access_token = session()->get('access_token');
		$this->expires = session()->get('expires');
	}

	/**
	 * Gets a new access token
	 * 
	 * @return string access_token Instace
	 */
	private function getAccessToken()
	{
		return $this->curl('https://www.reddit.com/api/v1/access_token', [
			'grant_type' => 'password',
			'username' => config('reddit_api.username'),
			'password' => config('reddit_api.password')
		], true);
	}

	/*--------------------------------------------------------------------------
	| API resource requests
	|--------------------------------------------------------------------------*/

	/**
	 * Generic GET request for reddit API
	 * 
	 * @param string endpoint
	 * @return object response
	 */
	public function get(string $endpoint)
	{
		return $this->curl(config('reddit_api.api_url') . $endpoint);
	}

	/**
	 * Create a new reddit post
	 * 
	 * @param array post data
	 * @return object response
	 */
	public function createPost(array $post_data)
	{
		return $this->curl('https://oauth.reddit.com/api/submit', $post_data);
	}

	/*--------------------------------------------------------------------------
	| Utility
	|--------------------------------------------------------------------------*/

	/**
	 * Base cURL function
	 * 
	 * @param string URL
	 * @param array post data
	 * @param boolean is Oauth2 request
	 * @return object response
	 */
	public function curl(string $url, array $post_data = null, bool $auth_request = null)
	{
		Log::debug('RedditAPI: curl : ' . $url . ' ' . json_encode($post_data));

		$ch = curl_init($url);
	
		// either us userpwd to get access token, or use access_token for non Oauth2 auth related request
		if ($auth_request) {
			curl_setopt($ch, CURLOPT_USERPWD, config('reddit_api.client_id') . ':' . config('reddit_api.secret'));
		} else {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $this->access_token));
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, config('reddit_api.user_agent'));

		if (isset($post_data)) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);
		curl_close($ch);

		return $response;
	}
}
