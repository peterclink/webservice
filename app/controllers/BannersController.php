<?php
class BannersController extends Controller {

	public function index() {
	}

	public function campanha() {
		echo 'BannersController<br>';
		echo $this->getParam('campanha');
	}

}