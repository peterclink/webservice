<?php
class accountsController extends controller {

	public function index_get($id = false) {
		$model = new accountModel();
		$data = $model->_get($id);
		$this->json($data);
	}

	public function index_post() {
		
		$data['username'] = $this->request->post('username');
		$data['password'] = $this->request->post('password');
		$data['email'] = $this->request->post('email');

		$this->model = new accountModel();

		$this->model->_post($data);

		$this->json(['Success']);
	}

	public function index_put() {
		
		$data['username'] = $this->request->post('username');
		$data['password'] = $this->request->post('password');
		$data['email'] = $this->request->post('email');

		$this->model = new accountModel();

		$this->model->_put($data);

		//$this->json(['Success']);
	}
}