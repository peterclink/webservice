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

		public function __construct($table = false) {
			$this->_table = $table;
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

	    public function get() {

	    	$this->_sql = 'SELECT * FROM ' . $this->_table . $this->_where;

	    	try { 

	            $this->stmt = $this->conn->prepare($this->_sql);
	            $this->stmt->execute($this->_params);
	            
	            return $this->stmt->fetchAll(PDO::FETCH_OBJ);

	        } catch( PDOException $e ) {

	            throw new Exception($e->getMessage());
	        }
	    }

	    public function where($dados) {
 
			$fields = count($dados);
			$i = 0;

			$this->_where = ' WHERE ';

			foreach ($dados as $key => $value) {
				if ( $fields == 1) {
					$this->_where .= $key . ' = ' . $value;
					break;
				} else {
					if ( $i+1 == $fields) {
						$this->_where .= $key . ' = ' . $value;
					} else {
						$this->_where .= $key . ' = ' . $value . ' AND ';
					}
				}
				$i++;
			}
	    }

	    public function params($params) {
			$this->_params = $params;
	    }


	    /******************************************************************************/

	    public function filter() {
	    	$this->sql = trim(str_replace("\r", " ", $this->sql));
	    }

	    public function query( $mode = PDO::FETCH_OBJ ) {
	    	
	        try { 

	            $this->stmt = $this->conn->prepare($this->sql);
	            $this->stmt->execute($this->params);
	            
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

	    public function insert( Array $dados ) {

        	$column = implode(", ", array_keys($dados));
			$value = "'".implode("', '", array_values($dados))."'";

	        try { 

	            $stmt = $this->conn->prepare("INSERT INTO `{$this->_table}` ({$column}) VALUES ({$value})"); 
	            $stmt->execute(array_values($dados));


            } catch( PDOException $e ) {

	            throw new Exception($e->getMessage());

	        }  
	    }

	    public function update( Array $dados ) {

	    	$column = implode(", ", array_keys($dados));
			$value = "'".implode("', '", array_values($dados))."'";

			try { 

	            $stmt = $this->conn->prepare("UPDATE `{$this->_table}` SET {$column} WHERE $where"); 
	            $stmt->execute(array_values($dados));

            } catch( PDOException $e ) {

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