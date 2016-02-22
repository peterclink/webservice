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

	public function validate() {

		$_HEADERS = getallheaders();
		$token = $_HEADERS['HTTP_AUTHORIZATION'];
		//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IkRVeUl3cnN1eCtyVUlXUktHSEF5ZVRqOW9FeVFtckZ0RFwvUSszcTg1MWdZPSJ9.eyJpc3MiOiJ3ZWJzZXJ2aWNlLmxvY2FsaG9zdCIsImF1ZCI6IndlYnNlcnZpY2UubG9jYWxob3N0IiwianRpIjoiRFV5SXdyc3V4K3JVSVdSS0dIQXllVGo5b0V5UW1yRnREXC9RKzNxODUxZ1k9IiwiaWF0IjoxNDU1OTEwNTMyLCJleHAiOjE0NTU5MTA1OTIsImlkIjoxNSwidXNlcm5hbWUiOiJwZXRlcmxpbmsiLCJyb2xlIjoiYWRtaW4ifQ.r-NhT1la8pcPHSvpdgy2u0GLeaAtvcqDo7UFicGfkvKWOSgIkYhnxb3NkQ8c_vLiwFFT7u-pTA6W4Q62SXzZvGFID44BkMLQAN1xr5wS28OJN1lACqhfjIP9PDbqdEPuaoGs9RqpwjFpHqfaX3Q1uVprNAKiBv53PnXQgVHatY70U4Yh6lboBdUlWBlHB_VCcuTgTfvkICSG310km7pHVAt3ENaCN1y7k5PRv3QWM1QnwBrAyy7lC8ELX8z8KYg7XDAnJsxfhXZcZ6CtMB5CoOyt6ekP41HAsiXeZTrebYNnLTEoX54bNpE6bPmGjbYPzKpFfg2Iu9Ub0cDqVUkTrg";

		try {

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

			$serverName = parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST); // same thing here

			$validationData = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
			//$this->log('JwtTokenAuthenticate -> Validating token...');
			$validationData->setIssuer($serverName);
			$validationData->setAudience($serverName); // WARN: Here it's better to see if the client is the right one
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

		} catch (Exception $e) {
			return ['auth' => false, 'error' => $e->getMessage()];
		}
	}
}