<?php
class UsuarioModel extends UsuarioDao {
	
	public $_table = "usuarios";
	public $validate;
	public $id;
	public $nome;
	public $email;
	public $login;
	public $senha;
	public $resenha;
	public $perfil;
	public $_perfis = array(
		1 => "Usuário",
		2 => "Sub-Administrador",
		3 => "Administrador"
	);

	public function __construct() {
		$this->validate = new DataValidatorHelper();
	}

	public function setId($id) {
		$this->validate->set('id', $id)->is_required()->is_integer();
		$this->id = $id;

	}

	public function setNome($nome) {
		$this->validate->set('nome', $nome)->is_required()->min_length(5);
		$this->nome = $nome;
	}

	public function setEmail($email) {
		$this->validate->set('email', $email)->is_email();
		$this->email = $email;

	}

	public function setLogin($login) {
		$this->validate->set('login', $login)->is_required()->min_length(5);
		$this->login = $login;
	}

	public function setSenha($senha) {
		$this->validate->set('senha', $senha)->is_required()->min_length(5);
		$this->senha = $senha;

	}

	public function setReSenha($resenha) {
		$this->validate->set('resenha', $resenha)->is_equals($this->senha);
	}

	public function setPerfil($perfil) {
		$this->validate->set('perfil', $perfil)->is_required()->is_integer();
		$this->perfil = $perfil;

	}

	public function getPerfil($var) {

		return $this->_perfis[$var];
	}
	
}