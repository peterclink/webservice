<?php
class accountsController extends controller {

	public function index_get($id = false) {
		$data = $this->model->_get($id);
		$this->json($data);
	}

	public function index_post() {
		
		$data['username'] = $this->request->post('username');
		$data['password'] = $this->request->post('password');
		$data['email'] = $this->request->post('email');

		$this->model->_post($data);

		//$this->json(['Success']);
	}

	public function index_put() {

		$username = $this->request->put('username');
		$data['password'] = $this->request->put('password');
		$data['email'] = $this->request->put('email');

		$this->model->_put($data, $username);
	}
}