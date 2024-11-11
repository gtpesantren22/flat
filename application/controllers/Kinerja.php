<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja extends CI_Controller
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
        $data['judul'] = 'Tunjangan Kinerja';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();
        
        $data['data'] = $this->model->getdata('kinerja')->result();

        $this->load->view('kinerja', $data);
    }

    public function tambah()
    {
        $data = [
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('kinerja', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil ditambahkan');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal ditambahkan');
            redirect('kinerja');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('kinerja', 'kinerja_id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil dihapus');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal dihapus');
            redirect('kinerja');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('kinerja', 'kinerja_id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil diupdate');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal diupdate');
            redirect('kinerja');
        }
    }
}
