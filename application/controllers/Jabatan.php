<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jabatan extends MY_Controller
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
        $data['judul'] = 'Jabatan';
        $data['sub'] = 'master';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('jabatan')->result();
        $this->load->view('jabatan', $data);
    }

    public function tambah()
    {
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->tambah('jabatan', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Jabatan berhasil ditambahkan');
            redirect('jabatan');
        } else {
            $this->session->set_flashdata('error', 'Jabatan gagal ditambahkan');
            redirect('jabatan');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('jabatan', 'jabatan_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Jabatan berhasil dihapus');
            redirect('jabatan');
        } else {
            $this->session->set_flashdata('error', 'Jabatan gagal dihapus');
            redirect('jabatan');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
        ];

        $this->model->edit('jabatan', 'jabatan_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Jabatan berhasil diupdate');
            redirect('jabatan');
        } else {
            $this->session->set_flashdata('error', 'Jabatan gagal diupdate');
            redirect('jabatan');
        }
    }
}
