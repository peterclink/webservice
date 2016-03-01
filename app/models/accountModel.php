<?php
class accountModel extends model {
	
	protected $table = "accounts";
	protected $columns = [
		'username',
		'email',
		'password',
		'status'
	];

	public function sets($data) {
		$this->open();

		$this->where(array('id' => 1,'user' => 'peter','email' => 'peter.link'));
		
		$this->OrWhere(array('id' => 1,'user' => 'peter','email' => 'peter.link'));
		
		$this->get();
		
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