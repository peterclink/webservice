<?php
	class model {

		public $isConn;
    	protected $conn;

	    private $host = DB_HOST;
	    private $user = DB_USER;
	    private $pass = DB_PASS;
	    private $db = DB_DATABASE;
	    private $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
	    public $sql;
	    protected $stmt;

	    protected $_columns;
	    protected $_where;
	    protected $_orWhere;
	    protected $_sql;
	    protected $_params;

	    public function __construct() {
	    	//$this->_params = [];
	    }

		public function open() {       
        	try { 
		        $this->isConn = true;
		        $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db};charset=utf8", $this->user, $this->pass, $this->options); 
		        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
		        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		    } catch( PDOException $e ) { 
		        $this->isConn = false;
		        throw new Exception($e->getMessage());
		    }
		}

		public function close() {
	        $this->conn = null;
	        $this->isConn = false;
	    }

	    public function clear() {
	    	$this->_where = null;
	    	$this->_columns = null;
	    	$this->_params = null;
	    }

	    private function querySelect() {
	    	$this->_sql = 'SELECT ' . $this->_columns . ' FROM ' . $this->table . $this->_where;
	    }

	    protected function columns($column) {
	    	if(!$column) {
	    		$this->_columns = implode(',', $this->columns);
	    	} else {
	    		$this->_columns = $column;
	    	}
	    }

		/**
		* Este metodo e para definicao das condicoes na clausula WHERE
		*
		* @uses $this->where(array('id' => 1,'user' => 'peter','email' => 'peter.link'));
		*
		* @param array $data campos => valores.
		* @param mixed  $value The value of the database field.
		* @param operator $operator Comparison operator. Default is =
		* @param conditional $cond Condition of where statement (OR, AND)
		*
		* @return $this
		*/
	    protected function where($data, $conditional = false) {

	    	$fields = count($data);
			$i = 0;

			$this->_where = ' WHERE ';

			foreach ($data as $key => $value) {
				$newKey = ':'.$key;
				if ( $fields == 1) {
					$this->_where .= $key . ' = ' . $newKey;
					$this->_params[$newKey] = $value;
					break;
				} else {
					$this->_params[$newKey] = $value;
					if ( $i+1 == $fields) {
						$this->_where .= $key . ' = ' . $newKey;
					} else {
						$cond = ($conditional) ? $conditional[$i] : 'AND';
						$this->_where .= $key . ' = ' . $newKey . ' ' . $cond . ' ';
					}
				}
				$i++;
			}
	    }

	    /**
		* Este metodo e para definicao das condicoes com operador OR na clausula WHERE
		*
		* @uses $this->OrWhere(array('id' => 1,'user' => 'peter','email' => 'peter.link'));
		*
		* @param array $data campos => valores.
		* @param string  $conditional Opcional - Alterar o operador inicial default.
		*
		* @return $this
		*/
	    public function orWhere($data, $conditional = 'AND') {

	    	$fields = count($data);
			$i = 0;

			if(is_null($this->_where)) {
				$this->_where = ' WHERE ';
			} else {
				$this->_where .= " $conditional ";
			}

			if($fields < 2) {
				throw new Exception('Para utlizar o metodo orWhere é necessário pelo menos 2 clausulas');
			}

			foreach ($data as $key => $value) {
				$newKey = ':'.$key;
				$this->_params[$newKey] = $value;
				if($i == 0) {
					$this->_where .= '(' . $key . ' = ' . $newKey . ' OR ';
				} else if ( $i+1 == $fields) {
					$this->_where .= $key . ' = ' . $newKey . ')';
				} else {
					$this->_where .= $key . ' = ' . $newKey . ' OR ';
				}
				$i++;
			}

			return $this;
	    }

	    public function get($column = false) {

	    	$this->columns($column);
	    	$this->querySelect();

	    	try { 

	            $this->stmt = $this->conn->prepare($this->_sql);
	            $this->stmt->execute($this->_params);
	            return $this->stmt->fetchAll(PDO::FETCH_OBJ);

	        } catch( PDOException $e ) {
	        	echo $this->stmt->queryString;
	            throw new Exception($e->getMessage());
	        }
	    }

	    public function insert(Array $dados) {

        	$column = implode(", ", array_keys($dados));
			$value = "'".implode("', '", array_values($dados))."'";

	        try { 

	            $stmt = $this->conn->prepare("INSERT INTO `{$this->table}` ({$column}) VALUES ({$value})"); 
	            $stmt->execute(array_values($dados));


            } catch( PDOException $e ) {

	            throw new Exception($e->getMessage());

	        }  
	    }

	    public function update($dados) {
	    	var_dump($_REQUEST);
	    	exit;

	    	/*$column = implode(", ", array_keys($dados));
			$value = "'".implode("', '", array_values($dados))."'";

			try { 

	            $stmt = $this->conn->prepare("UPDATE `{$this->_table}` SET {$column} WHERE $where"); 
	            $stmt->execute(array_values($dados));

            } catch( PDOException $e ) {

	            throw new Exception($e->getMessage());

	        }*/

		}

	    /******************************************************************************/

	    public function filter() {
	    	$this->sql = trim(str_replace("\r", " ", $this->sql));
	    }

	    public function query( $mode = PDO::FETCH_OBJ ) {
	    	
	        try { 

	            $this->stmt = $this->conn->prepare($this->_sql);
	            $this->stmt->execute($this->_params);
	            
	            return $this->stmt->fetchAll($mode);

	        } catch( PDOException $e ) {

	            throw new Exception($e->getMessage());
	        }            
	    }

	    public function read( $mode = PDO::FETCH_OBJ ) {

	    	if(empty($this->sql)) {
	    		$this->sql = "Select * from " . $this->_table;
	    	}

	        try { 

	            $this->stmt = $this->conn->prepare($this->sql);
	            $this->stmt->execute($this->params);
	            
	           	//$this->stmt
	            //$del->rowCount()
	            return $this->stmt->fetchAll($mode);

	        } catch(PDOException $e) {

	            throw new Exception($e->getMessage());
	        }       
	    }

	    public function set( $prop, $value ) {
	      /*
	      * Funcao para atribuir valores as propriedades da classe
	      * @param String $prop Nome da propriedade que tem seu valor atribuido
	      * @param String, Array, Object Valor a ser atribu�do
	      * @return void Nao da nenhum retorno
	      */
	      $this->$prop = $value;
	   	}
	}