<?php
class inviteModel {
	
	public $_table = "invites";

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
		
		$db->where(array('account' => ':account'));
		$db->params(array(':account' => $id));
		
		$data = $db->get();
		
		$db->close();


		return $data;
	}
}