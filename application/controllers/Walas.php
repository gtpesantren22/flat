<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Walas extends MY_Controller
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
        $data['judul'] = 'Tunjangan Wali Kelas';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db_active->query("SELECT walas.*, guru.nama as nmguru, satminkal.nama as lembaga FROM walas JOIN guru ON guru.guru_id=walas.guru_id JOIN satminkal ON satminkal.id=guru.satminkal ")->result();

        $data['guruOpt'] = $this->model->getData('guru')->result();
        $this->load->view('walas', $data);
    }

    public function tambah()
    {
        $data = [
            'guru_id' => $this->input->post('guru', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('walas', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'walas berhasil ditambahkan');
            redirect('walas');
        } else {
            $this->session->set_flashdata('error', 'walas gagal ditambahkan');
            redirect('walas');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('walas', 'walas_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Walas berhasil dihapus');
            redirect('walas');
        } else {
            $this->session->set_flashdata('error', 'Walas gagal dihapus');
            redirect('walas');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'guru_id' => $this->input->post('guru', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('walas', 'walas_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Walas berhasil diupdate');
            redirect('walas');
        } else {
            $this->session->set_flashdata('error', 'Walas gagal diupdate');
            redirect('walas');
        }
    }
}
