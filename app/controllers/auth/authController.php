<?php
class authController extends controller {

	private $message;
	private $credentials = [];

	public function __construct() {
		//$this->credentials = array('auth' => false);
	}

	public function index() {

		$login = $_POST['login'];
		$password = $_POST['password'];

		if( $login == 'peterlink' && $password == '123456' ) {
			
			$jwt = new jwt();
			$token = $jwt->create();
			
			$this->credentials['auth'] = true;
			$this->credentials['token'] = $token;

		} else {
			$this->credentials['auth'] = false;
		}
		
		$this->json($this->credentials);
	}

	public function isAuthenticated() {

		$jwt = new jwt();
		$token = $jwt->validate();
		$this->json($token);
	}
}