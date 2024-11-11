<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	public function index()
	{
		$data['judul'] = 'Dashboard';
		$this->load->view('index', $data);
	}
}
