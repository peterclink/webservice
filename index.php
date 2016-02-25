<?php
require_once('system/settings.php');
require_once('system/autoLoader.php');
require_once('system/factory.php');
require_once('system/controller.php');
require_once('system/model.php');
require_once('system/authentication.php');
require_once('system/normalize.php');
require_once('system/request.php');
require_once('app/config/routes.php');

$autoloader = new autoLoader(unserialize(AUTOLOAD));
$app = new factory();
$auth = new authentication();

$normalize = new normalize();
$request = new request();

try {

	$auth->normalize = $normalize;
	$auth->request = $request;
	$auth->init($routesPublic);

	$app->normalize = $normalize;
	$app->request = $request;
	$app->init($routes);

} catch ( Exception_404 $e ) {

	echo 'Page not found<br>' . $e->getMessage();
	
} catch ( Exception_Login $e ) {

	$app->view('Exceptions/Login', false);
	$app->show(array('ERROR_MSG' => $e->getMessage()));

} catch (Exception $e) {
		
	echo $e->getMessage();

}