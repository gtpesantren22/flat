<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ijazah extends CI_Controller
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
        $data['judul'] = 'Ijazah';
        $data['sub'] = 'master';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('ijazah')->result();
        $this->load->view('ijazah', $data);
    }

    public function tambah()
    {
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->tambah('ijazah', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Ijazah berhasil ditambahkan');
            redirect('ijazah');
        } else {
            $this->session->set_flashdata('error', 'Ijazah gagal ditambahkan');
            redirect('ijazah');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('ijazah', 'id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Ijazah berhasil dihapus');
            redirect('ijazah');
        } else {
            $this->session->set_flashdata('error', 'Ijazah gagal dihapus');
            redirect('ijazah');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->edit('ijazah', 'id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Ijazah berhasil diupdate');
            redirect('ijazah');
        } else {
            $this->session->set_flashdata('error', 'Ijazah gagal diupdate');
            redirect('ijazah');
        }
    }
}
