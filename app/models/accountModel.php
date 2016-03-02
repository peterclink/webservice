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
		
		$data = $this->insert($data);

		$this->close();
	}

	public function _put() {
		$this->open();
		
		$data = $this->update($data);

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