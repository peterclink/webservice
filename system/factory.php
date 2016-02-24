<?php
class factory {

	private $_url;
	private $_request;
	private $_parameters;
	private $_module;
	private $_controller;
	private $_action;
	private $_argument;
	private $_routes;
	private $_controllerPath;

	private $_explode;
	private $_params;
	private $_language;

	public function __construct($routes) {
		//$this->setUrl();
		//$this->setExplode();
		//$this->setParams();
		//$this->log();
		//$this->setController();
		//$this->setAction();
		$this->request();
		$this->routes($routes);
	}

	protected function request() {
		$this->_url = $_GET['url'] = (isset($_GET['url'])) ? $_GET['url'] : PAGE_DEFAULT;
		$this->_request = strtolower($_SERVER['REQUEST_METHOD']);
	}

	protected function routes($routes) {
		
		$this->_routes = $routes;

		if (array_key_exists($this->_url, $this->_routes)) { 
			
			if( empty($this->_routes[$this->_url]['module']) ) {
				$this->_routes[$this->_url]['module'] = $this->_routes[$this->_url]['controller'];
			}

		    $this->setModule($this->_routes[$this->_url]['module']);
			$this->setController($this->_routes[$this->_url]['controller']);
			$this->setAction($this->_routes[$this->_url]['action'], false);

		} else {
			$this->parameters();
		}
	}

	protected function parameters() {
		
		$this->_parameters = explode('/', $this->_url);
		$parametersCount = count($this->_parameters);
		
		$this->setModule($this->_parameters[0]);

		if($parametersCount == 1) {
			//Módulo

			$this->setController($this->_parameters[0]);
			$this->setAction();

		} else if($parametersCount >= 2) {
			//Controller || Action || Parameters

			if($this->isController($this->_parameters[1])) {

				$this->setController($this->_parameters[1]);
				$this->setAction();

				if($parametersCount >= 3) {

					$controller = $this->requireController();

					if($this->isAction($controller, $this->_parameters[2])) {
						$this->setAction($this->_parameters[2]);
					} else {
						$this->setArgument($this->_parameters[2]);
					}

					if($parametersCount == 4) {
						$this->setArgument($this->_parameters[3]);
					}

				}

			} else {

				$this->setController($this->_parameters[0]);
				$controller = $this->requireController();

				if($this->isAction($controller, $this->_parameters[1])) {
					$this->setAction($this->_parameters[1]);
				} else {
					$this->setAction();
					$this->setArgument($this->_parameters[1]);
				}

				if($parametersCount == 3) {

					$this->setArgument($this->_parameters[2]);

				}
			}

		} 
	}

	private function setModule( $module = 'index' ) {
		$this->_module = strtolower($module);
		if(substr($this->_module, -1) == 's') {
			$this->_module = substr($this->_module,0,-1);
		}
	}

	private function setController( $controller = 'index' ) {
		$this->_controller = strtolower($controller);
	}

	private function setAction( $action = 'index', $rest = true ) {

		if(is_null($action)) {
			$action = 'index';
		}

		if($rest) {
			$action .= '_' . $this->_request;
		}

		$this->_action = strtolower($action);
	}

	private function setArgument( $argument = 'index' ) {
		$this->_argument = strtolower($argument);
	}

	public function isController( $controller ) {

		$this->_controllerPath = CONTROLLER_DIR . $this->_module . DS . $controller . 'Controller.php';

		return (file_exists($this->_controllerPath)) ? true : false;
	}

	public function isAction( $controller, $action ) {
		return (method_exists($controller, $action. '_' . $this->_request)) ? true : false;
	}

	public function requireController() {
		
		$controller_path = CONTROLLER_DIR . $this->_module . DS . $this->_controller . 'Controller.php';

		if ( !file_exists($controller_path) ) {
			throw new Exception_404($this->_controller);
		}

		require_once($controller_path);

		$controller = @end(explode('/',$this->_controller)).'Controller';

		return new $controller();
	}

	public function run() {

		$app = $this->requireController();

		if( !method_exists($app, $this->_action) )
			throw new Exception_404($this->_action);

		$action = $this->_action;

		if(!is_null($this->_argument)) {
			$app->$action($this->_argument);
		} else {
			$app->$action();
		}
		
	}


	/*
	protected function setUrl() {
		$this->_url = $_GET['url'] = (isset($_GET['url'])) ? $_GET['url'] : PAGE_DEFAULT;
	}

	protected function getUrl() {
		return $this->_url;
	}

	protected function setExplode() {
		
		$this->_explode = explode('/', $this->_url);
		$modules = (count($this->_explode) > 3) ? 3 : count($this->_explode);

		switch ($modules) {
			case 1:
				$this->setModule($this->_explode[0]);
				$this->setController($this->_explode[0]);
				$this->setAction();
				break;
			case 2:
				$this->setModule($this->_explode[0]);
				$this->setController($this->_explode[0]);
				$this->setAction($this->_explode[1]);
				break;
			case 3:
				$this->setModule($this->_explode[0]);
				$this->setController($this->_explode[1]);
				$this->setAction($this->_explode[2]);
				break;
			default:
				$this->setModule($this->_explode[0]);
				$this->setController($this->_explode[0]);
				$this->setAction();
				break;
		}
	}

	private function setModule( $module = 'index' ) {
		$this->_module = strtolower($module);
	}

	

	

	private function setParams() {

		$params = $this->_explode;

		unset($params[0],$params[1],$params[2]);

		if (empty(end($params))) array_pop($params);

		$paramsCount = count($params);

		if ( $paramsCount > 1 ) {
			$i = 0;
			foreach ( $params as $val ) {
				if ( $i % 2 == 0 )
					$index[] = $val;
				else
					$value[] = $val;						

				$i++;
			}
		} else if ( $paramsCount == 1 ) {
			foreach ( $params as $val ) {
				$index[] = $this->_action;
				$value[] = $val;
			}
		} else {
			$index = array();
			$value = array();
		}

		if( count($index) == count($value) && !empty($index) && !empty($value) )
			$this->_params = array_combine($index, $value);
		else
			$this->_params = array();
	}

	public function getParam( $name = false ) {
		return ($name) ? $this->_params[$name] : $this->_params;
	}
	*/
	

}