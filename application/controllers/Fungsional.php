<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fungsional extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');
        $this->db_active = $this->dynamic_db->connect();
        // $user = $this->Auth_model->current_user();

        // $this->user = $user->nama;
        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }

    public function index()
    {
        $data['judul'] = 'Tunjangan Fungsional';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db_active->query("SELECT fungsional.*, golongan.nama as nmgolongan, golongan.kategori as nmkategori FROM fungsional 
        JOIN golongan ON golongan.id=fungsional.golongan_id
        ")->result();

        $data['golonganOpt'] = $this->model->getData('golongan')->result();
        $data['kategoriOpt'] = $this->model->getData('kategori')->result();
        $this->load->view('fungsional', $data);
    }

    public function tambah()
    {
        $data = [
            'golongan_id' => $this->input->post('golongan', true),
            'kategori' => $this->input->post('kategori', true),
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('fungsional', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Fungsional berhasil ditambahkan');
            redirect('fungsional');
        } else {
            $this->session->set_flashdata('error', 'Fungsional gagal ditambahkan');
            redirect('fungsional');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('fungsional', 'fungsional_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Fungsional berhasil dihapus');
            redirect('fungsional');
        } else {
            $this->session->set_flashdata('error', 'Fungsional gagal dihapus');
            redirect('fungsional');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            // 'kategori' => $this->input->post('kategori', true),
            'golongan_id' => $this->input->post('golongan', true),
            // 'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('fungsional', 'fungsional_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Fungsional berhasil diupdate');
            redirect('fungsional');
        } else {
            $this->session->set_flashdata('error', 'Fungsional gagal diupdate');
            redirect('fungsional');
        }
    }

    public function cek()
    {
        $datas = $this->model->getData('golongan')->result();
        foreach ($datas as $data) {
            $cek = $this->model->getBy('fungsional', 'golongan_id', $data->id)->row();
            if (!$cek) {
                $this->model->tambah('fungsional', ['golongan_id' => $data->id]);
            } else {
                $this->model->edit('fungsional', 'golongan_id', $data->id, ['golongan_id' => $data->id]);
            }
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Fungsional berhasil direload');
            redirect('fungsional');
        } else {
            $this->session->set_flashdata('error', 'Fungsional gagal direload');
            redirect('fungsional');
        }
    }
}
