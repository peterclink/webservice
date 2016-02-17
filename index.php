<?php
require_once('system/settings.php');
require_once('system/autoLoader.php');
require_once('system/factory.php');
require_once('system/controller.php');
require_once('system/model.php');
require_once('system/jwt.php');

$autoloader = new autoLoader(unserialize(AUTOLOAD));
$app = new factory();

try {

	$app->run();

} catch ( Exception_404 $e ) {

	echo 'Page not found<br>' . $e->getMessage();
	
} catch ( Exception_Login $e ) {

	$app->view('Exceptions/Login', false);
	$app->show(array('ERROR_MSG' => $e->getMessage()));

} catch (Exception $e) {
		
	echo $e->getMessage();

}