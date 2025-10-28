<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected $db_active;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Dynamic_db');
        $this->db_active = $this->dynamic_db->connect();
    }
}
