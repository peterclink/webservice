<?php
class accountsController extends controller {

	public function index_get($id = false) {
		$model = new accountModel();
		$dados = ($id) ? $model->getWhere($id) : $model->get(); 
		$this->json($dados);
	}

	public function list_get($id = false) {
		echo 'action list<Br>';
		$model = new accountModel();
		$dados = ($id) ? $model->getWhere($id) : $model->get(); 
		$this->json($dados);
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