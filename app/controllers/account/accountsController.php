<?php
class accountsController extends controller {

	public function accountsController() {
		$db = new model();
		/*$db->open();
		$db->set('sql', 'select * from user');
		$data = $db->read();
		var_dump($data);
		$db->close();
		echo "home";*/

		echo 'accountsController<br>';
	}

	public function index_get($id = false) {
		echo 'action index<Br>';
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