<?php
class userController extends controller {

	public function index() {
		$db = new model();
		/*$db->open();
		$db->set('sql', 'select * from user');
		$data = $db->read();
		var_dump($data);
		$db->close();
		echo "home";*/
	}

	protected function get() {
		echo 'metodo get';
	}

	protected function post() {
		echo 'metodo post';
	}

	protected function put() {
		echo 'metodo put';
	}

	protected function delete() {
		echo 'metodo delete';
	}

}