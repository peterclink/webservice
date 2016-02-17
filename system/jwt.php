<?php
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

class jwt {

	public function jwt() {
	}

	public function createToken() {
		
		$token = (new Builder())->setIssuer('JWT Example')
		->setAudience('JWT Example')
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->getToken();

		return json_encode(['result' => 1, 'message' => 'Token generated successfully', 'token' => '' . $token,]);
	}

	public function validate() {
		$token = $_SERVER['HTTP_AUTHORIZATION'];

		try {
			$token = (new Parser())->parse($token);
		} catch (Exception $exception) {
			//return false;
		}

		$validationData = new ValidationData();
		$validationData->setIssuer('JWT Example');
		$validationData->setAudience('JWT Example');

		if (!$token) {
			return json_encode(['Error' => 'Token Invalido']);
		} else if ($token->validate($validationData)) {
			return json_encode(['result' => 1, 'message' => 'The admin\'s password is: ']);
		} else {
			return json_encode(['result' => 0, 'message' => 'Invalid token']);
		}
	}
}