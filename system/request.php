<?php
class request {

	public function post($param = false) {
		return ($param) ? $_POST[$param] : $_POST;
	}

	public function get($param = false) {
		return ($param) ? $_GET[$param] : $_GET;
	}

	public function server($param = false) {
		return ($param) ? $_SERVER[$param] : $_SERVER;
	}
}