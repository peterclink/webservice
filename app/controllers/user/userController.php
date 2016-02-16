<?php
class userController extends controller {

	public function index() {
		$db = new model();
		$db->open();
		$db->set('sql', 'select * from user');
		$data = $db->read();
		var_dump($data);
		$db->close();
		echo "home";
	}

	public function campanha() {
		echo 'indexController<br>';
		echo $this->getParam('input');
	}

	public function get() {
		$db = new model();
		$db->open();
		$db->set('sql', 'select * from user');
		$data = $db->read();
		$db->close();
		/*
		$this->service($data);
		*/


		echo json_encode($data);
	}

}