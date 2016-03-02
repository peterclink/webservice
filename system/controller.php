<?php
class controller  {

	protected $responseCode;
	protected $response;

	protected function json($data) {

		try {

			$this->responseValidation($data);
			header('Content-Type: application/json; charset=utf-8');
			return print(json_encode($data));

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	protected function responseValidation($data) {
		if(isset($data) && !empty($data) && count($data) > 0) {
			$this->responseHeader(200);
		} else {
			$this->responseHeader(400);
			throw new Exception('Nenhum dado encontrado');
		}
	}

	protected function responseHeader($status) {
		$this->responseCode = array(
		    // Informational 1xx
		    100 => 'Continue',
		    101 => 'Switching Protocols',

		    // Success 2xx
		    200 => 'OK',
		    201 => 'Created',
		    202 => 'Accepted',
		    203 => 'Non-Authoritative Information',
		    204 => 'No Content',
		    205 => 'Reset Content',
		    206 => 'Partial Content',

		    // Redirection 3xx
		    300 => 'Multiple Choices',
		    301 => 'Moved Permanently',
		    302 => 'Found',  // 1.1
		    303 => 'See Other',
		    304 => 'Not Modified',
		    305 => 'Use Proxy',
		    // 306 is deprecated but reserved
		    307 => 'Temporary Redirect',

		    // Client Error 4xx
		    400 => 'Bad Request',
		    401 => 'Unauthorized',
		    402 => 'Payment Required',
		    403 => 'Forbidden',
		    404 => 'Not Found',
		    405 => 'Method Not Allowed',
		    406 => 'Not Acceptable',
		    407 => 'Proxy Authentication Required',
		    408 => 'Request Timeout',
		    409 => 'Conflict',
		    410 => 'Gone',
		    411 => 'Length Required',
		    412 => 'Precondition Failed',
		    413 => 'Request Entity Too Large',
		    414 => 'Request-URI Too Long',
		    415 => 'Unsupported Media Type',
		    416 => 'Requested Range Not Satisfiable',
		    417 => 'Expectation Failed',

		    // Server Error 5xx
		    500 => 'Internal Server Error',
		    501 => 'Not Implemented',
		    502 => 'Bad Gateway',
		    503 => 'Service Unavailable',
		    504 => 'Gateway Timeout',
		    505 => 'HTTP Version Not Supported',
		    509 => 'Bandwidth Limit Exceeded'
		);

		header('HTTP/1.1 '.$status.' '. $this->responseCode[$status]);
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
}