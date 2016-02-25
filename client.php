<?php
// https://github.com/lcobucci/jwt
define('SERVER_URL', 'http://webservice.localhost:8090');

function http($url, $method, $data = [], $headers = []) {
	$url = SERVER_URL . $url;

	$options = [
		'http' => [
			'method'  => $method,
			'header'  => array_merge(['Content-type: application/x-www-form-urlencoded'], $headers),
			'content' => http_build_query($data),
		],
	];

	$context  = stream_context_create($options);

	return file_get_contents($url, false, $context);
}

$response = http('/auth', 'post', ['login' => 'peterlink', 'password' => '123456']);

echo $response;

/*echo $response = http($url, ['username' => 'admin', 'password' => 'p4ssw0rd'], array(
	'AUTHORIZATION: Bearer '
));*/
//$response = json_decode($response);