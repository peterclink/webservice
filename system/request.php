<?php
class request {

	private $_PUT;

	public function __construct() {
		if ($this->server('REQUEST_METHOD') == "PUT") {
			parse_str(file_get_contents("php://input"),$this->_PUT);
		}
	}

	public function get($param = false) {
		return ($param) ? $_GET[$param] : $_GET;
	}

	public function post($param = false) {
		return ($param) ? $_POST[$param] : $_POST;
	}

	public function put($param = false) {
		return ($param) ? $this->_PUT[$param] : $this->_PUT;
	}

	public function server($param = false) {
		return ($param) ? $_SERVER[$param] : $_SERVER;
	}
}