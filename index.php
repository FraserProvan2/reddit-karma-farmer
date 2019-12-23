<?php
	$username = 'bishop_lizard';
	$password = 'Nexus225';
	$clientId = '03MY2genQTBaUA';
  $clientSecret = 'fmwFYCKzPjCjnz14oKFtpmLjb-E';
  
	// post params 
	$params = array(
		'grant_type' => 'password',
		'username' => $username,
		'password' => $password
  );
  
	// curl settings and call to reddit
	$ch = curl_init( 'https://www.reddit.com/api/v1/access_token' );
	curl_setopt( $ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
  
	// curl response from reddit
	$response_raw = curl_exec( $ch );
  $response = json_decode( $response_raw );

  curl_close($ch);
  
	// display response from reddit
  var_dump( $response );
  