<?php

use Illuminate\Support\Carbon;

class RedditAPI
{
	public $access_token;

	public function __construct()
	{
		session()->flush();

		// handle Oauth2
		$this->access_token = session()->get('access_token');
		if (!$this->access_token) {
			$this->getAccessToken();
		}
		$this->access_token = session()->get('access_token')->access_token;
	}

	public function getAccessToken()
	{
		$username = config('reddit_api.username');
		$password = config('reddit_api.password');
		$client_id = config('reddit_api.client_id');
		$secret = config('reddit_api.secret');

		$post_data = array(
			'grant_type' => 'password',
			'username' => $username,
			'password' => $password
		);

		$ch = curl_init('https://www.reddit.com/api/v1/access_token');
		curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);

		curl_close($ch);

		session()->put('access_token', $response);
	}

	public function get($endpoint)
	{
		$ch = curl_init(config('reddit_api.api_url') . $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, config('reddit_api.user_agent') . $endpoint);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $this->access_token));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);
		curl_close($ch);

		return $response;
	}

	public function post($post_data)
	{
		$ch = curl_init('https://oauth.reddit.com/api/submit');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, config('reddit_api.user_agent'));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $this->access_token));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response_raw = curl_exec($ch);
		$response = json_decode($response_raw);
		curl_close($ch);

		return $response;
	}
}
