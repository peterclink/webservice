<?php
class accountModel {
	
	public $_table = "accounts";

	public function get() {

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