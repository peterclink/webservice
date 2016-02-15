<?php
class LoginModel extends Model {
	
	protected $_table = "usuarios";
	protected $_columns = array(
        'usuario' => 'login',
        'senha' => 'senha'
    );
    protected $_dados = array('id', 'nome','login');
    protected $_filter = true;
    protected $_caseSensitive = true;
    protected $_login;
    protected $_pass;
    private $_binary;
    private $_encrypt = false;

    protected function getDataSession($filter = false) {
        
        $this->sessionStart();
        
        if (!$filter)
           return $_SESSION;
        else
            return $_SESSION[$filter];
        
    }

    protected function validarUsuario($usuario, $senha) {

    	$this->_login = $usuario; 
    	$this->_pass = $senha; 

    	if ($_encrypt) {
    		$this->isEncrypt();
    	}

    	if ($_caseSensitive) {
    		$this->isCaseSensitive();
    	}

    	if ($_filter) {
    		$this->isFilterData();
    	}

    	$this->set('sql',"
    		SELECT 
    			 COUNT(*) AS total
                FROM `{$this->_table}`
                WHERE
                    {$this->_binary} `{$this->_columns['usuario']}` = ?
                    AND {$this->_binary} `{$this->_columns['senha']}` = ?
        ");
		$this->set('params', array($this->_login,$this->_pass));
		$read = $this->read(PDO::FETCH_COLUMN);

		return ($read[0] == 1) ? true : false;

    }

    protected function getData() {
    	if ($this->_dados != false) {
            // Monta o formato SQL da lista de campos
            $dados = '`' . join('`, `', array_unique($this->_dados)) . '`';

            $this->set('sql',"
	    		SELECT 
	    			 {$dados}
	                FROM `{$this->_table}`
	                WHERE
	                    {$this->_binary} `{$this->_columns['usuario']}` = ?
	        ");
			$this->set('params', array($this->_login));
			$dados = $this->read();

			return ($dados) ? $dados : false;
        }
    }

    protected function isFilterData() {
    	$this->_login = mysql_escape_string($this->_login);
        $this->_pass = mysql_escape_string($this->_pass);
    }

    protected function isCaseSensitive() {
    	$this->_binary = 'BINARY';
    }

     protected function isEncrypt() {
        $this->_pass = md5($this->_pass);
    }
}