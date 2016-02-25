<?php
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class authentication {

	private $_request;
	private $_serverName;
	private $_response;

	public function init($routes) {
		$this->serverName = parse_url($this->request->server('HTTP_HOST'), PHP_URL_HOST);

		if(!$this->isPublic($routes)) {
			$this->validate();
		}
	}

	public function isPublic($routes) {
		return (in_array($this->request->get('url'), $routes)) ? TRUE : FALSE;
	}

	public function getToken() {
		/***** não possuimos o metodo request; ***/;
		return $this->request->server('HTTP_AUTHORIZATION');
	}

	public function validate() {
		$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IlJlc1c1a0pYQmE0cmRtSUZQS0tOQzNvTkNuR2RzYmFUcyswWmhuRTIxUmc9In0.eyJpc3MiOiJ3ZWJzZXJ2aWNlLmxvY2FsaG9zdCIsImF1ZCI6IndlYnNlcnZpY2UubG9jYWxob3N0IiwianRpIjoiUmVzVzVrSlhCYTRyZG1JRlBLS05DM29OQ25HZHNiYVRzKzBaaG5FMjFSZz0iLCJpYXQiOjE0NTY0MzUzNzMsImV4cCI6MTQ1NjQzODk3MywiaWQiOjE1LCJ1c2VybmFtZSI6InBldGVybGluayIsInJvbGUiOiJhZG1pbiJ9.WWSN8NDteY8RQCOLZIyPEXEy-dxuArm8vTQA2J5MtdelQeRqHrYTkuVlcCMCamaghI2LqXfdStpsvbK5W0rZ-1SLG3DvCGq2LZzQoxVNshGWGkdSWVKCtSc_K7vQ58S47_IqMmbQ1pXZKQ1Wk87xmX8EhnOSkBUZeNHjn7DF7oM-qT57_IjGF6LB6vNzHAC13WcZm0DFjeonUZ6asDHzqq8qB18PtaS3ZNbOyo443NgHBM_nF3zKrtIJKP8kj7i3OfwvUSjYR7PFI9r2kKn40DKAPXgPRpjROsBNNQpe_ufRpT4pBRXsFXg2ygoOEPbja1AkdCCq55T0R6hK7pfm4Q';
		//$token = $this->getToken();
		$token = (new Parser())->parse((string) $token);
		$publicKey = new Key('file://' . AUTH_KEY_DIR . 'public.key');

		if (!$token->verify(new Sha256(), $publicKey)) {
			throw new Exception('JwtTokenAuthenticate:__findUser: Someone has changed your token mate.');
		}

		if(empty($token)){ // SUGGEST: This is useless since if anything goes wrong during parsing an Exception will be raised
		    throw new Exception('JwtTokenAuthenticate:_findUser: Unable to parse token.');
		}

		$uid = (string) $token->getClaim('username'); // WARN: That's what I was talking before (naive validation =P) 
		if( !$uid ){
			throw new Exception('JwtTokenAuthenticate:_findUser: Unable to find valid id.');
		}

		$time = $token->getClaim('exp'); // WARN: That's what I was talking before (naive validation =P) 
		if( time() > $time ){
			throw new Exception('JwtTokenAuthenticate:_findUser: token expire.');
		}

		$validationData = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		//$this->log('JwtTokenAuthenticate -> Validating token...');
		$validationData->setIssuer($this->_serverName);
		$validationData->setAudience($this->_serverName); // WARN: Here it's better to see if the client is the right one
		//$validationData->setId(15); // WARN (the last about this LOL): here you're validating if the Parser done his job
		//$validationData->setSigner(strval(Configure::read('Security.cipherSeed'))); Signature verification and token validation are different things
		//$validationData->setCurrentTime(time() + 16); // ERR: this shouldn't be called

		$token->validate($validationData);

		return [
			'auth' => true, 
			'duration'=> $time, 
			'expire'=> date('H:i:s', $time),
			'user'=> [
				'login' => $uid,
				'name' => 'Peter Link'
			], 
		];

		//return true;
	}

	public function create($login) {
		$tokenId  = base64_encode(mcrypt_create_iv(32)); // WARN: If you want to be able to validate this it shouldn't be random (otherwise your validation will be naive)
		$serverName = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST); // Let's get just what needed (http://php.net/manual/en/function.parse-url.php)

		$signer = new Sha256();
		$privateKey = new Key('file://' . AUTH_KEY_DIR . 'private.key');
		//$publicKey = new Key('file://' . AUTH_KEY_DIR . 'public.key');
		
		$token = (new Builder())
		->setIssuer($serverName)
		->setAudience($serverName)
		->setId($tokenId, true) // WARN: do you really need to replicate the id as a header? this will increase the token size
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->set('id', 15)
        ->set('username', $login)
        ->set('role', 'admin')
		->sign($signer,  $privateKey)
		->getToken();

		return (string) $token;
		//return json_encode ( ($token->verify($signer, $publicKey)) ? ['result' => 1, 'message' => 'Token generated successfully', 'token' => '' . $token] : ['result' => 2, 'message' => 'Token error']);
	}

	public function show() {
		return $this->_response;
	}
}