<?php
	class autoLoader {

		private $_type; 
		private $_extension = ".php";
        private $_path;
		private $_className;

		public function __construct($AutoLoad) {
			$this->_type = $AutoLoad;
            spl_autoload_register(array($this, 'loader'));
        }

        private function loader($className) {
            $this->_className = $className;
            $this->classType();     
            $this->includeClass();     
        }

        public function classType() {

        	foreach ($this->_type as $key => $value) {
        		
        		if ( strstr($this->_className, $key)) {
        			foreach ($value as $path) {
                        if ( $this->fileExists($path . $this->_className . $this->_extension) )
                            return;
	        		}
        		}
        	}
        }

        public function fileExists($path) {
            
            $this->_path = null;

            if ( file_exists( $path ) ) {
                $this->_path = $path;
                return true;            
            }
            
            return false;
        }

        public function includeClass() {
            if($this->_path <> null)
                require_once $this->_path;
            else
                self::throwFileNotFoundException($this->_className);
        }

        public static function throwFileNotFoundException($className) {
            throw new Exception("O arquivo com a classe <strong>$className</strong> não foi encontrado.<br>");
        }

	}