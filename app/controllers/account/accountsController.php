<?php
class accountsController extends controller {

	public function index_get($id = false) {
		$model = new accountModel();
		$dados = ($id) ? $model->getWhere($id) : $model->get(); 
		$this->json($dados);
	}

	public function index_post() {
		
		$data['username'] = $this->request->post('username');
		$data['password'] = $this->request->post('password');
		$data['email'] = $this->request->post('email');

		$this->model = new accountModel();

		$this->model->sets($data);

		//$this->json(['Success']);
	}
}