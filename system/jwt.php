<?php
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class jwt {

	private $log = [];

	public function jwt() {
	}

	public function createToken() {
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
        ->set('username', 'peterlink')
        ->set('role', 'admin')
		->sign($signer,  $privateKey)
		->getToken();

		return (string) $token;
		//return json_encode ( ($token->verify($signer, $publicKey)) ? ['result' => 1, 'message' => 'Token generated successfully', 'token' => '' . $token] : ['result' => 2, 'message' => 'Token error']);
	}

	public function validate() {

		$_HEADERS = getallheaders();
		$token = $_HEADERS['HTTP_AUTHORIZATION'];

		try {

			$token = (new Parser())->parse((string) $token);
			$publicKey = new Key('file://' . AUTH_KEY_DIR . 'public.key');

			if (!$token->verify(new Sha256(), $publicKey)) {
			    $this->log('JwtTokenAuthenticate:__findUser: Someone has changed your token mate.');
			    return $this->log();
			}

			if(empty($token)){ // SUGGEST: This is useless since if anything goes wrong during parsing an Exception will be raised
			    //return json_decode(json_encode("Unable to parse token."), true);
				$this->log('JwtTokenAuthenticate:_findUser: Unable to parse token.');
			    return $this->log();
			}

			$uid = (string) $token->getClaim('username'); // WARN: That's what I was talking before (naive validation =P) 
			if( !$uid ){
				$this->log('JwtTokenAuthenticate:_findUser: Unable to find valid id.');
			    return $this->log();
			}

			$serverName = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST); // same thing here

			$validationData = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
			//$this->log('JwtTokenAuthenticate -> Validating token...');
			$validationData->setIssuer($serverName);
			$validationData->setAudience($serverName); // WARN: Here it's better to see if the client is the right one
			//$validationData->setId(15); // WARN (the last about this LOL): here you're validating if the Parser done his job
			//$validationData->setSigner(strval(Configure::read('Security.cipherSeed'))); Signature verification and token validation are different things

			$validationData->setCurrentTime(time()); // ERR: this shouldn't be called

			$token->validate($validationData);

			return json_encode(['result' => 1, 'message' => 'TOKEN VALIDADO ', 'TOKEN' => $_HEADERS['HTTP_AUTHORIZATION']]);

		} catch (Exception $exception) {
			$this->log('A identidade do token foi alterada');
		    return $this->log();
		}
	}

	public function log($log = false) {
		if($log)
			array_push($this->log, $log);
		return json_encode([$this->log]);
	}
}