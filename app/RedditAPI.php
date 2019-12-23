<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RedditAPI
{
	/*--------------------------------------------------------------------------
	| Properties
	|--------------------------------------------------------------------------*/

	public $access_token;
	private $expires;

	/*--------------------------------------------------------------------------
	| Authentication
	|--------------------------------------------------------------------------*/

	/**
	 * Construct, handle Auth per request, use cached access token
	 * if avaliable, keep track if expired 
	 */
	public function __construct()
	{
		// handle Oauth2
		$this->access_token = session()->get('access_token')->access_token;
		$this->expires = session()->get('expires');

		// create new token
		if (!isset($this->access_token) || now() < $this->expires) {
			$this->access_token = $this->getAccessToken()->access_token;
			$this->expires = Carbon::now()->addSeconds($this->access_token->expires_in);

			session()->put('access_token', $this->access_token);
			session()->put('expires', $this->expires);

			Log::debug('new access token created');
		} 
		// use valid cached access token
		else {
			Log::debug('using cached token');
		}
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
		]);
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
	 * @param boolean user password
	 * @return object response
	 */
	public function curl(string $url, array $post_data = null, boolean $userpwd = null)
	{
		Log::debug('curl request: ' . $url . ' ' . json_encode($post_data));

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, config('reddit_api.user_agent'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $this->access_token));

		if (isset($post_data)) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}

		if (isset($userpwd)) {
			curl_setopt($ch, CURLOPT_USERPWD, config('reddit_api.client_id') . ':' . config('reddit_api.secret'));
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);
		curl_close($ch);

		return $response;
	}
}
