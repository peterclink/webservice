<?php
class controller  {

	public $https_required = FALSE;
	public $authentication_required = FALSE;
	public $api_response_code;
	public $response;

	public function service( $response ) {
		$this->response['code'] = 0;
		$this->response['status'] = 200;
		$this->response['data'] = $response;

		$this->api_response_code = array(
			0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
			1 => array('HTTP Response' => 200, 'Message' => 'Success'),
			2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
			3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
			4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
			5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
			6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
		);

		//$this->isAuthentication();


		// Method A: Say Hello to the API
		/*if( strcasecmp($_GET['method'],'hello') == 0){
			$this->response['code'] = 1;
			$this->response['status'] = $this->api_response_code[ $this->response['code'] ]['HTTP Response'];
			$this->response['data'] = 'Hello World';
		}
*/
		// --- Step 4: Deliver Response

		// Return Response to browser
		$this->response('json', $this->response);
	}
	
	public function response( $format, $api_response ) {
		// Define HTTP responses
		$http_response_code = array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found'
		);

		// Set HTTP Response
		header('HTTP/1.1 '.$api_response['status'].' '.$http_response_code[ $api_response['status'] ]);

		// Process different content types
		if( strcasecmp($format,'json') == 0 ){

			// Set HTTP Response Content Type
			header('Content-Type: application/json; charset=utf-8');

			// Format data into a JSON response
			$json_response = json_encode($api_response['data']);

			// Deliver formatted data
			echo $json_response;

		}elseif( strcasecmp($format,'xml') == 0 ){

			// Set HTTP Response Content Type
			header('Content-Type: application/xml; charset=utf-8');

			// Format data into an XML response (This is only good at handling string data, not arrays)
			$xml_response = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
			'<response>'."\n".
			"\t".'<code>'.$api_response['code'].'</code>'."\n".
			"\t".'<data>'.json_encode($api_response['data']).'</data>'."\n".
			'</response>';

			// Deliver formatted data
			echo $xml_response;

		}else{

			// Set HTTP Response Content Type (This is only good at handling string data, not arrays)
			header('Content-Type: text/html; charset=utf-8');

			// Deliver formatted data
			echo $api_response['data'];

		}
	}

	public function json($data) {
		return print(json_encode($data));
	}

	public function isHttps() {

		if( $this->https_required && $_SERVER['HTTPS'] != 'on' ) {
			$this->response['code'] = 2;
			$this->response['status'] = $this->api_response_code[ $this->response['code'] ]['HTTP Response'];
			$this->response['data'] = $this->api_response_code[ $this->response['code'] ]['Message'];

			// Return Response to browser. This will exit the script.
			$this->response($_GET['format'], $this->response);
		}
	}

	public function isAuthentication() {
		// Optionally require user authentication
		if( $this->authentication_required ){

			if( empty($_POST['username']) || empty($_POST['password']) ){
				$this->response['code'] = 3;
				$this->response['status'] = $this->api_response_code[ $this->response['code'] ]['HTTP Response'];
				$this->response['data'] = $this->api_response_code[ $this->response['code'] ]['Message'];

				// Return Response to browser
				//$this->response($_GET['format'], $this->response);

			}

			// Return an error response if user fails authentication. This is a very simplistic example
			// that should be modified for security in a production environment
			elseif( $_POST['username'] != 'foo' && $_POST['password'] != 'bar' ){
				$this->response['code'] = 4;
				$this->response['status'] = $this->api_response_code[ $this->response['code'] ]['HTTP Response'];
				$this->response['data'] = $this->api_response_code[ $this->response['code'] ]['Message'];

				// Return Response to browser
				//$this->response($_GET['format'], $this->response);

			}

		}
	}
}