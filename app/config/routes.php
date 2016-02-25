<?php

$routes[''] = array('controller' => 'accounts');
//$routes['auth'] = array('controller' => 'auth');
//$routes['auth/isAuthenticated'] = array('controller' => 'auth', 'action' => 'isAuthenticated');

$routesPublic = array(
	'auth',
	'auth/isAuthenticated'
);