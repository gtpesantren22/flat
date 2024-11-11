<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Modeldata', 'model');
		$this->load->model('Auth_model');

		// $user = $this->Auth_model->current_user();

		// $this->user = $user->nama;
		if (!$this->Auth_model->current_user()) {
			redirect('login/logout');
		}
	}
	public function index()
	{
		$data['judul'] = 'Dashboard';
		$data['user'] = $this->Auth_model->current_user();
		$this->load->view('index', $data);
	}
}
