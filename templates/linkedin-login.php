<?php
	session_start();
	require_once realpath(__DIR__ . DIRECTORY_SEPARATOR . '/config.php') ;


	$params = array(
		'grant_type' => 'authorization_code',
		'client_id' => $LINKEDIN_API_KEY,
		'client_secret' => $LINKEDIN_API_SECRET,
		'code' => $_GET['code'],
		'redirect_uri' => $LINKEDIN_REDIRECT_URI,
	 );

	$url = 'https://www.linkedin.com/uas/oauth2/accessToken?' . http_build_query($params);
	$response = file_get_contents($url);
	$token = json_decode($response);

	
	if (isset($token->access_token) && !empty($token->access_token)) {
		$_SESSION['linkedin_user'] = array(
			'access_token' => $token->access_token,
			'expires_in' => $token->expires_in,
			'expires_at' => time() + $_SESSION['expires_in']
		);
		
	}







	// $user = fetch('GET', '/v1/people/~:(firstName,lastName)');
	$user = fetch('GET', '/v2/me?projection=(id,firstName,lastName,email,profilePicture(displayImage~:playableStreams))');
	
	print "<pre>";
	print_r( $user);

	function fetch($method, $resource, $body = '') {

		$params = array(
			'oauth2_access_token' => $_SESSION['linkedin_user']['access_token'],
		);
		
		$url = 'https://api.linkedin.com' . $resource . '&' . http_build_query($params);

		$context = stream_context_create(
			array('http' => 
				array('method' => $method,
                )
            )
        );
		$response = file_get_contents($url, false, $context);

		return json_decode($response);
	}

