<?php

defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Gaji extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');

        $api = $this->db->query("SELECT * FROM settings WHERE nama = 'key_api' ")->row();
        $this->api_key = $api->isi;
    }

    public function listgaji_get()
    {
        $key = $this->input->get('key');
        $datgaji = $this->model->getData('gaji')->result();

        if ($key == '' || $key != $this->api_key) {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Infalid key'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            if (!empty($datgaji)) {
                $this->set_response($datgaji, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'User could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
    public function gajidetail_post()
    {
        $key = $this->input->get('key');
        $id = $this->input->post('gaji_id', true);

        $datgaji = $this->model->getBy('gaji_detail', 'gaji_id', $id)->result();

        if ($key == '' || $key != $this->api_key) {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Infalid key'
            ], REST_Controller::HTTP_NOT_FOUND);
        } else {
            if (!empty($datgaji)) {
                $this->set_response($datgaji, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Gaji data could not be found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }
}
