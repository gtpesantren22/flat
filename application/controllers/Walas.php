<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Walas extends CI_Controller
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

        $data['data'] = $this->db->query("SELECT walas.*, satminkal.nama as nmsatminkal FROM walas JOIN satminkal ON satminkal.id=walas.satminkal_id ")->result();

        $data['satminkalOpt'] = $this->model->getData('satminkal')->result();
        $this->load->view('walas', $data);
    }

    public function tambah()
    {
        $data = [
            'satminkal_id' => $this->input->post('satminkal', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('walas', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'walas berhasil ditambahkan');
            redirect('walas');
        } else {
            $this->session->set_flashdata('error', 'walas gagal ditambahkan');
            redirect('walas');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('bpjs', 'bpjs_id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'bpjs berhasil dihapus');
            redirect('bpjs');
        } else {
            $this->session->set_flashdata('error', 'bpjs gagal dihapus');
            redirect('bpjs');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'guru_id' => $this->input->post('guru', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('bpjs', 'bpjs_id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'bpjs berhasil diupdate');
            redirect('bpjs');
        } else {
            $this->session->set_flashdata('error', 'bpjs gagal diupdate');
            redirect('bpjs');
        }
    }
}
