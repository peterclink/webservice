<?php
header('Content-Type: text/html; charset=utf-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');  
header('Last Modified: '. gmdate('D, d M Y H:i:s') .' GMT');  
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');  
header('Pragma: no-cache');  
header("Cache: no-cache");    
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','On');
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

/** Configurações gerais **/
define('DS', DIRECTORY_SEPARATOR);

/** Diretorios do Projeto **/
define('PROJECT_DIR', $_SERVER['DOCUMENT_ROOT'] . DS);
define('SYS_HELPER_DIR', PROJECT_DIR . 'system'. DS . 'helpers'. DS);
define('SYS_EXCEPTION_DIR', PROJECT_DIR . 'system'. DS .'exceptions'. DS);
define('SYS_MODEL_DIR', PROJECT_DIR . 'system'. DS .'models'. DS);

/** Diretorios da Aplicação **/
define('CONTROLLER_DIR', PROJECT_DIR . 'app'. DS .'Controllers'. DS);
define('VIEW_DIR', PROJECT_DIR . 'app'. DS .'Views'. DS);
define('MODEL_DIR', PROJECT_DIR . 'app'. DS .'Models'. DS);
define('DAO_DIR', MODEL_DIR . 'dao'. DS);
define('CONTENT_DIR', PROJECT_DIR . 'content'. DS);
define('IMG_DIR', CONTENT_DIR . 'img'. DS);

/** Class AutoLoad **/
define('AUTOLOAD', 
	serialize(
		array(
			"Helper" => array(
				"SYS" => SYS_HELPER_DIR
			),
			"Model" => array(
				"APP" => MODEL_DIR,
				"SYS" => SYS_MODEL_DIR
			),
			"Exception" => array(
				"SYS" => SYS_EXCEPTION_DIR
			),
			"Dao" => array(
				"APP" => DAO_DIR
			)
		)
	)
);

/** Configurações de Acesso ao banco de dados **/

define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_DATABASE", "myFrameWork");

/** Configurações Gerais **/
define('TITLE', 'Sistema de Banners');
define('PAGE_DEFAULT', 'Index');