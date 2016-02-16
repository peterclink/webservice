<?php
class mainController extends controller {

	public function index() {
		$array = array(
			array('login' => "peter", "sobrenome" => "link" ),
			array('login' => "rhane", "sobrenome" => "link" ),
		);
		echo json_encode($array);
	}
}