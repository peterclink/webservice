<?php
class normalize {

	public function _strtoupper($param) {
		return strtoupper($param);
	}

	public function _strtolower($param) {
		return strtolower($param);
	}

	public function _isset($param) {
		return isset($param) ? true : false;
	}

	public function _array_key_exists($key, $search) {
		return array_key_exists($key, $search) ? true : false;
	}

	public function _empty($param) {
		return empty($param) ? true : false;
	}
	
	public function _is_null($param) {
		return is_null($param) ? true : false;
	}

	public function _explode($delimiter, $string) {
		return explode($delimiter, $string);
	}

	public function _count($var) {
		return count($var);
	}

	public function _substr($string, $start, $length = false) {
		return ($length) ? substr($string, $start, $length) : substr($string, $start);
	}

	public function _file_exists($filename) {
		return (file_exists($filename)) ? true : false;
	}

	public function _method_exists($object, $method_name) {
		return (method_exists($object, $method_name)) ? true : false;
	}

	public function _end(array $array) {
		return end($array);
	}

	public function _require($file) {
		require $file;
	}

}