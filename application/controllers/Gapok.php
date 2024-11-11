<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gapok extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        // $this->load->model('Auth_model');

        // $user = $this->Auth_model->current_user();

        // $this->user = $user->nama;
        // if (!$this->Auth_model->current_user() || $user->level != 'adm' && $user->level != 'admin') {
        //     redirect('login/logout');
        // }
    }

    public function index()
    {
        $data['judul'] = 'Gaji Pokok';

        $data['data'] = $this->db->query("SELECT gapok.*, ijazah.nama as nmijazah, golongan.nama as nmgolongan FROM gapok 
        JOIN ijazah ON ijazah.id=gapok.ijazah_id
        JOIN golongan ON golongan.id=gapok.golongan_id
        ")->result();

        $data['ijazahOpt'] = $this->model->getData('ijazah')->result();
        $data['golonganOpt'] = $this->model->getData('golongan')->result();
        $this->load->view('gapok', $data);
    }

    public function tambah()
    {
        $data = [
            'ijazah_id' => $this->input->post('ijazah', true),
            'golongan_id' => $this->input->post('golongan', true),
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('gapok', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Gapok berhasil ditambahkan');
            redirect('gapok');
        } else {
            $this->session->set_flashdata('error', 'Gapok gagal ditambahkan');
            redirect('gapok');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('gapok', 'gapok_id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Gapok berhasil dihapus');
            redirect('gapok');
        } else {
            $this->session->set_flashdata('error', 'Gapok gagal dihapus');
            redirect('gapok');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'ijazah_id' => $this->input->post('ijazah', true),
            'golongan_id' => $this->input->post('golongan', true),
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('gapok', 'gapok_id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Gapok berhasil diupdate');
            redirect('gapok');
        } else {
            $this->session->set_flashdata('error', 'Gapok gagal diupdate');
            redirect('gapok');
        }
    }
}
