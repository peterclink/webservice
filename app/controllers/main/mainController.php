<?php
class mainController extends controller {

	public function index() {
		$array = array(
			array('login' => "peter", "sobrenome" => "link" ),
			array('login' => "rhane", "sobrenome" => "link" ),
		);
		echo 'date: ', date("Y-m-d H:i:s");
		echo '<br>time: ', time();
		echo '<br>time: ', date('H:i:s', time());
	}
}