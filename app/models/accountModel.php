<?php
class accountModel extends model {
	
	protected $table = "accounts";
	protected $columns = [
		'id',
		'username',
		'email',
		'password',
		'status'
	];

	public function _get($id) {
		$this->open();
		
		if($id) {
			$this->where(array('id' => $id));
		} 
		
		$data = $this->get();

		$this->close();

		return $data;
	}

	public function _post($data) {
		$this->open();

		$this->validate->set('username',$data['username'])->is_required()->min_length(5)->max_length(20);
		$this->validate->set('password',$data['password'])->is_required()->min_length(5)->max_length(20);
		$this->validate->set('email',$data['email'])->is_email();

		$this->validate->validate();
		
		$this->insert($data);

		$this->close();
	}

	public function _put($data, $id) {
		$this->open();

		//$this->validate->set('username',$data['username'])->is_required()->min_length(5)->max_length(20);
		$this->validate->set('password',$data['password'])->is_required()->min_length(5)->max_length(20);
		$this->validate->set('email',$data['email'])->is_email();

		$this->validate->validate();
		
		$this->where(array('username' => $id));
		$this->update($data);

		$this->close();
	}

	public function _delete() {

	}

	public function sets($data) {
		$this->open();

		$this->where(array('id' => 3, 'status' => 1));
		
		var_dump($this->get());
		
		$this->close();
	}

	public function getAccounts() {

		$db = new model($this->_table);
		$db->open();
		$data = $db->get();
		$db->close();

		return $data;
	}

	public function getWhere($id) {
		
		$db = new model($this->_table);
		
		$db->open();
		
		$db->where(array('id' => ':id'));
		$db->params(array(':id' => $id));
		
		$data = $db->get();
		
		$db->close();


		return $data;
	}
}