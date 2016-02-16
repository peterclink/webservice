<?php
class factory {

	private $_url;
	private $_explode;
	private $_module;
	private $_controller;
	private $_action;
	private $_params;
	private $_language;

	public function __construct() {
		$this->setUrl();
		$this->setExplode();
		$this->setParams();
		//$this->log();
		//$this->setController();
		//$this->setAction();
	}

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

	private function setController( $controller = 'index' ) {
		$this->_controller = strtolower($controller);
	}

	private function setAction( $action = 'index' ) {
		$this->_action = strtolower($action);
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
		$app->$action();
	}

}