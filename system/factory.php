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
	private $_instance;
	private $_error;
	private $_errorCode;

	public $normalize;
	public $request;

	public function init($routes) {

		$this->request();
		$this->routes($routes);

		if($this->_errorCode > 0) {
			throw new Exception_404($this->_error);
		} else {
			$app = $this->_instance;
			$action = $this->_action;

			$app->normalize = $this->normalize;
			$app->request = $this->request;

			if(!$this->normalize->_is_null($this->_argument)) {
				$app->$action($this->_argument);
			} else {
				$app->$action();
			}
		}
	}

	protected function request() {
		$url = $this->request->get('url');
		$this->_url = ($this->normalize->_isset($url)) ? $url : PAGE_DEFAULT;
		$this->_request = $this->normalize->_strtolower($this->request->server('REQUEST_METHOD'));
	}

	protected function routes($routes) {
		
		$this->_routes = $routes;

		if ($this->normalize->_array_key_exists($this->_url, $this->_routes)) { 
			
			if($this->normalize->_empty($this->_routes[$this->_url]['module'])) {
				$this->_routes[$this->_url]['module'] = $this->_routes[$this->_url]['controller'];
			}

		    $this->setModule($this->_routes[$this->_url]['module']);
			$this->setController($this->_routes[$this->_url]['controller']);
			$this->setAction($this->_routes[$this->_url]['action']);

		} else {
			$this->parameters();
		}
	}

	protected function parameters() {
		
		$this->_parameters = $this->normalize->_explode('/', $this->_url);
		$parametersCount = $this->normalize->_count($this->_parameters);
		
		$this->setModule($this->_parameters[0]);

		if($parametersCount == 1) {
			//Módulo
			$this->setController($this->_parameters[0]);
			$this->setAction();

		} else if($parametersCount > 1) {
			//Controller || Action || Parameters
			if($this->setController($this->_parameters[1])) {

				if($parametersCount > 2) {
					if(!$this->setAction($this->_parameters[2])) {
						$this->setArgument($this->_parameters[2]);
					} 

					if($parametersCount == 4) {
						$this->setArgument($this->_parameters[3]);
					} else {
						$this->setAction();
					}
				} else {
					$this->setAction();
				}

			} else {

				$this->setController($this->_parameters[0]);

				if(!$this->setAction($this->_parameters[1])) {
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
		$this->_module = $this->normalize->_strtolower($module);
		if($this->normalize->_substr($this->_module, -1) == 's') {
			$this->_module = $this->normalize->_substr($this->_module,0,-1);
		}
	}

	private function setArgument( $argument = 'index' ) {
		$this->_argument = $this->normalize->_strtolower($argument);
	}

	private function setController( $controller = 'index' ) {
		$controller = $this->normalize->_strtolower($controller);

		if($this->isController($controller)) {
			$this->_controller = $controller;
			$this->instanceController();
			$this->setError(0);
			return TRUE;
		} else {
			$this->setError(404,'Invalid Controller');
			return FALSE;
		}
	}

	public function isController($controller) {
		$this->_controllerPath = CONTROLLER_DIR . $this->_module . DS . $controller . 'Controller.php';
		return ($this->normalize->_file_exists($this->_controllerPath)) ? TRUE : FALSE;
	}

	private function instanceController() {
		$this->normalize->_require($this->_controllerPath);
		$controller = $this->normalize->_end($this->normalize->_explode('/',$this->_controller)).'Controller';

		$this->_instance = new $controller();
	}

	private function setAction($action = 'index') {

		if($this->normalize->_is_null($action)) {
			$action = 'index';
		}

		$action = $this->normalize->_strtolower($action);

		if($this->isAction($action)) {
			$this->_action = $action;
		} else if($this->isAction($action . '_' . $this->_request)) {
			$this->_action = $action . '_' . $this->_request;
		} else {
			$this->setError(404,'Invalid Action');
			return FALSE;
		}
		$this->setError(0);
		return TRUE;
	}

	private function isAction($action) {
		return ($this->normalize->_method_exists($this->_instance, $action)) ? TRUE : FALSE;
	}

	public function setError($code, $error = 'Success') {
		$this->_errorCode = $code;
		$this->_error = $error;
	}
}