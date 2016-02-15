<?php

	class factory {

		private $_url;
		private $_action;
		private static $_params;
		private $_explode;
		private $_controller;
		private $_module;
		private $_language;

		public function __construct() {

			$this->setUrl();
			$this->setExplode();
			$this->setController();
			$this->setAction();
			$this->setParams();
		}

		protected function setUrl() {

			$this->_url = $_GET['url'] = (isset($_GET['url'])) ? $_GET['url'] : PAGE_DEFAULT;

		}

		protected function getUrl() {
			return $this->_url;
		}

		protected function setExplode() {

			$this->_explode = explode('/', $this->_url);

		}

		protected function setController() {

			$this->_controller =strtolower($this->_explode[0]);

		}

		private function setAction() {

			$action = (!isset($this->_explode[1]) || $this->_explode[1] == null || $this->_explode[1] == "index") ? "index" : $this->_explode[1];
			$this->_action = $action;

		}

		private function setParams() {


			unset( $this->_explode[0], $this->_explode[1] );

			if ( empty(end($this->_explode)) )
				array_pop($this->_explode);


			$i = 0;
			if ( !empty($this->_explode) ) {

				if( count($this->_explode) > 1 ) {

					foreach ( $this->_explode as $val ) {
						
						if ( $i % 2 == 0 )
							$index[] = $val;
						else
							$value[] = $val;						

						$i++;

					}

				} else if (count($this->_explode) > 0 ) {
					foreach ( $this->_explode as $val ) {
						$index[] = $this->_action;
						$value[] = $val;
					}
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
			
			$controller_path = CONTROLLER_DIR . $this->_controller . 'Controller.php';

			if ( isset($this->_module) )
				$controller_path = str_replace("Controllers", 'controllers/' . $this->_module, $controller_path);

			if ( !file_exists($controller_path) )
				throw new Exception_404($this->_controller);

			require_once($controller_path);

			$controller = @end(explode('/',$this->_controller)).'Controller';

			return new $controller();
		}

		public function run() {

			$app = $this->requireController();

			$this->_action = strtolower($this->_action);

			if( !method_exists($app, $this->_action) )
				throw new Exception_404($this->_action);

			$action = $this->_action;
			$app->$action();
		}

	}