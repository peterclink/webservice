<?php
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class authentication {

	private $token;
	private $_request;
	private $_serverName;
	private $_response = [];
	private $_tokenUid;
	private $_tokenExpiration;

	public function __construct($routes = false) {
		$this->request = new request();
		if($routes && !$this->isPublic($routes)) {
			$this->validate();
		}
	}

	public function serverName() {
		$this->_serverName = parse_url($this->request->server('HTTP_HOST'), PHP_URL_HOST);
	}

	public function isPublic($routes) {
		return (in_array($this->request->get('url'), $routes)) ? TRUE : FALSE;
	}

	public function getToken() {
		return $this->request->server('HTTP_AUTHORIZATION');
	}

	public function publicKey() {
		return new Key('file://' . AUTH_KEY_DIR . 'public.key');
	}

	public function privateKey() {
		return new Key('file://' . AUTH_KEY_DIR . 'private.key');
	}

	public function validateToken() {
		if(empty($this->token)){ // SUGGEST: This is useless since if anything goes wrong during parsing an Exception will be raised
		    throw new Exception_Jwt('JwtTokenAuthenticate:_findUser: Unable to parse token.');
		}
	}

	public function validateSignature() {
		if (!$this->token->verify(new Sha256(), $this->publicKey())) {
			throw new Exception_Jwt('JwtTokenAuthenticate:__findUser: Someone has changed your token mate.');
		}
	}

	public function validateUid() {
		$this->_tokenUid = (string) $this->token->getClaim('username'); // WARN: That's what I was talking before (naive validation =P) 
		if( !$this->_tokenUid ){
			throw new Exception_Jwt('JwtTokenAuthenticate:_findUser: Unable to find valid id.');
		}
	}

	public function validateExpiration() {
		$this->_tokenExpiration = $this->token->getClaim('exp'); // WARN: That's what I was talking before (naive validation =P) 
		if( time() > $this->_tokenExpiration ){
			throw new Exception_Jwt('JwtTokenAuthenticate:_findUser: token expire.');
		}
	}

	public function validateData() {
		$validationData = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		//$this->log('JwtTokenAuthenticate -> Validating token...');
		$validationData->setIssuer($this->_serverName);
		$validationData->setAudience($this->_serverName); // WARN: Here it's better to see if the client is the right one
		//$validationData->setId(15); // WARN (the last about this LOL): here you're validating if the Parser done his job
		//$validationData->setSigner(strval(Configure::read('Security.cipherSeed'))); Signature verification and token validation are different things
		//$validationData->setCurrentTime(time() + 16); // ERR: this shouldn't be called

		return $validationData;
	}

	public function validate() {
		$token = $this->getToken();
		//$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IlJlc1c1a0pYQmE0cmRtSUZQS0tOQzNvTkNuR2RzYmFUcyswWmhuRTIxUmc9In0.eyJpc3MiOiJ3ZWJzZXJ2aWNlLmxvY2FsaG9zdCIsImF1ZCI6IndlYnNlcnZpY2UubG9jYWxob3N0IiwianRpIjoiUmVzVzVrSlhCYTRyZG1JRlBLS05DM29OQ25HZHNiYVRzKzBaaG5FMjFSZz0iLCJpYXQiOjE0NTY0MzUzNzMsImV4cCI6MTQ1NjQzODk3MywiaWQiOjE1LCJ1c2VybmFtZSI6InBldGVybGluayIsInJvbGUiOiJhZG1pbiJ9.WWSN8NDteY8RQCOLZIyPEXEy-dxuArm8vTQA2J5MtdelQeRqHrYTkuVlcCMCamaghI2LqXfdStpsvbK5W0rZ-1SLG3DvCGq2LZzQoxVNshGWGkdSWVKCtSc_K7vQ58S47_IqMmbQ1pXZKQ1Wk87xmX8EhnOSkBUZeNHjn7DF7oM-qT57_IjGF6LB6vNzHAC13WcZm0DFjeonUZ6asDHzqq8qB18PtaS3ZNbOyo443NgHBM_nF3zKrtIJKP8kj7i3OfwvUSjYR7PFI9r2kKn40DKAPXgPRpjROsBNNQpe_ufRpT4pBRXsFXg2ygoOEPbja1AkdCCq55T0R6hK7pfm4Q';
		$this->token = (new Parser())->parse((string) $token);

		$this->validateToken();
		$this->validateSignature();
		$this->validateUid();
		$this->validateExpiration();

		$this->token->validate($this->validateData());

		$this->_response['auth'] = TRUE;
		$this->_response['duration'] = $this->_tokenExpiration;
		$this->_response['expire'] = $this->_tokenExpiration;
		$this->_response['user'] = [
			'login' => $this->_tokenUid,
			'name' => 'Peter Link'
		];

		return $this->_response;
	}

	public function create($login) {
		$tokenId  = base64_encode(mcrypt_create_iv(32)); // WARN: If you want to be able to validate this it shouldn't be random (otherwise your validation will be naive)
		$signer = new Sha256();

		$token = (new Builder())
		->setIssuer($this->_serverName)
		->setAudience($this->_serverName)
		->setId($tokenId, true) // WARN: do you really need to replicate the id as a header? this will increase the token size
		->setIssuedAt(time())
		->setExpiration(time() + 3600)
		->set('id', 15)
        ->set('username', $login)
        ->set('role', 'admin')
		->sign($signer,  $this->privateKey())
		->getToken();

		return (string) $token;
		//return json_encode ( ($token->verify($signer, $publicKey)) ? ['result' => 1, 'message' => 'Token generated successfully', 'token' => '' . $token] : ['result' => 2, 'message' => 'Token error']);
	}
}