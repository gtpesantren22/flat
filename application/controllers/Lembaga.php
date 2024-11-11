<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lembaga extends CI_Controller
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
        $data['judul'] = 'Lembaga';
        $data['sub'] = 'master';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('satminkal')->result();
        $this->load->view('lembaga', $data);
    }

    public function tambah()
    {
        $data = [
            'nama' => $this->input->post('nama', true),
            'jml_siswa' => $this->input->post('jumlah', true),
        ];

        $this->model->tambah('satminkal', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Lembaga berhasil ditambahkan');
            redirect('lembaga');
        } else {
            $this->session->set_flashdata('ok', 'Lembaga gagal ditambahkan');
            redirect('lembaga');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('satminkal', 'id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Lembaga berhasil dihapus');
            redirect('lembaga');
        } else {
            $this->session->set_flashdata('ok', 'Lembaga gagal dihapus');
            redirect('lembaga');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
            'jml_siswa' => $this->input->post('jumlah', true),
        ];

        $this->model->edit('satminkal', 'id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Lembaga berhasil diupdate');
            redirect('lembaga');
        } else {
            $this->session->set_flashdata('ok', 'Lembaga gagal diupdate');
            redirect('lembaga');
        }
    }
}
