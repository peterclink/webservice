<?php
class indexController extends controller {

	public function index() {
	}

	public function campanha() {
		echo 'indexController<br>';
		echo $this->getParam('input');
	}

}