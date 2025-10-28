<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Golongan extends MY_Controller
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
        $data['judul'] = 'Golongan';
        $data['sub'] = 'master';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('golongan')->result();
        $this->load->view('golongan', $data);
    }

    public function tambah()
    {
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->tambah('golongan', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Golongan berhasil ditambahkan');
            redirect('golongan');
        } else {
            $this->session->set_flashdata('error', 'Golongan gagal ditambahkan');
            redirect('golongan');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('golongan', 'id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Golongan berhasil dihapus');
            redirect('golongan');
        } else {
            $this->session->set_flashdata('error', 'Golongan gagal dihapus');
            redirect('golongan');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->edit('golongan', 'id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Golongan berhasil diupdate');
            redirect('golongan');
        } else {
            $this->session->set_flashdata('error', 'Golongan gagal diupdate');
            redirect('golongan');
        }
    }
}
