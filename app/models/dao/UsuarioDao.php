<?php
class UsuarioDao extends Model {
	
	public function salvar() {

		$this->validarRegistros();

		$dados = array(
			"nome" => $this->nome,
			"email" => $this->email,
			"login" => $this->login,
			"senha" => $this->senha,
			"ativo" => 1
		);

		$this->log(__METHOD__,$dados);
		$this->insert($dados);
	}

	public function atualizar() {

		$this->set('sql',"
			UPDATE `{$this->_table}` SET 
				`nome`= :nome,
				`email`= :email,
				`senha`= :senha,
				`perfil`= :perfil,
				`ativo`= :ativo
				WHERE `id` = :id
        ");

		$this->set('params', array(
			":nome" => $this->nome,
			":email" => $this->email,
			":senha" => $this->senha,
			":perfil" => $this->perfil,
			":ativo" => 1,
			":id" => $this->id
		));

		$this->query();
	}

	public function remover() {

		$this->set('sql',"
			UPDATE `{$this->_table}` SET 
				`ativo`= :ativo
				WHERE `id` = :id
        ");

		$this->set('params', array(
			":ativo" => 0,
			":id" => $this->id
		));

		$this->query();
	}

	public function validarRegistros() {

		$this->set('sql',"
    		SELECT 
    			 COUNT(*) AS total
                FROM `{$this->_table}`
                WHERE
                    `login` = ?
        ");
		$this->set('params', array($this->login));
		$read = $this->read(PDO::FETCH_COLUMN);

		if( $read[0] > 0 )
			throw new Exception_Form("O login <strong>$this->login</strong> não está disponível.");
		else return true;
	}

	public function getUsuarios($id = false) {

		if($id) {
			$this->set('sql',"
	    		SELECT `id`, `nome`, `email`, `login`, `senha`, `perfil`, `ativo` FROM `usuarios` WHERE `id` = :id
	        ");
	        $this->set('params', array(":id"=>$id));
		} else {
			$this->set('sql',"
	    		SELECT `id`, `nome`, `email`, `login`, `senha`, `perfil`, `ativo` FROM `usuarios` WHERE `ativo` = 1
	        ");
		}

		$read = $this->read();

		return $read;
	}
}