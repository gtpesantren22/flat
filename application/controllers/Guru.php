<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends CI_Controller
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
        $data['judul'] = 'Guru';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db->query("SELECT guru.*, satminkal.nama as nmlembaga, ijazah.nama as nmijazah, golongan.nama as nmgolongan, jabatan.nama as nmjabatan, kategori.nama as nmkategori FROM guru JOIN satminkal ON satminkal.id=guru.satminkal
        JOIN ijazah ON ijazah.id=guru.ijazah
        JOIN golongan ON golongan.id=guru.golongan
        JOIN jabatan ON jabatan.jabatan_id=guru.jabatan
        JOIN kategori ON kategori.id=guru.kategori
        ")->result();
        $data['lembagaOpt'] = $this->model->getData('satminkal')->result();
        $data['jabatanOpt'] = $this->model->getData('jabatan')->result();
        $data['ijazahOpt'] = $this->model->getData('ijazah')->result();
        $data['golonganOpt'] = $this->model->getData('golongan')->result();
        $data['kategoriOpt'] = $this->model->getData('kategori')->result();
        $this->load->view('guru', $data);
    }

    public function tambah()
    {
        $data = [
            'guru_id' => $this->uuid->v4(),
            'nama' => $this->input->post('nama', true),
            'nipy' => $this->input->post('nipy', true),
            'nik' => $this->input->post('nik', true),
            'satminkal' => $this->input->post('satminkal', true),
            'jabatan' => $this->input->post('jabatan', true),
            'kriteria' => $this->input->post('kriteria', true),
            'sik' => $this->input->post('sik', true),
            'ijazah' => $this->input->post('ijazah', true),
            'tmt' => $this->input->post('tmt', true),
            'golongan' => $this->input->post('golongan', true),
            'santri' => $this->input->post('santri', true),
            'kategori' => $this->input->post('kategori', true),
            'email' => $this->input->post('email', true),
            'hp' => $this->input->post('hp', true),
            'rekening' => $this->input->post('rekening', true),
        ];

        $this->model->tambah('guru', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Guru berhasil ditambahkan');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'Guru gagal ditambahkan');
            redirect('guru');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('guru', 'guru_id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'guru berhasil dihapus');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'guru gagal dihapus');
            redirect('guru');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
            'nipy' => $this->input->post('nipy', true),
            'nik' => $this->input->post('nik', true),
            'satminkal' => $this->input->post('satminkal', true),
            'jabatan' => $this->input->post('jabatan', true),
            'kriteria' => $this->input->post('kriteria', true),
            'sik' => $this->input->post('sik', true),
            'ijazah' => $this->input->post('ijazah', true),
            'tmt' => $this->input->post('tmt', true),
            'golongan' => $this->input->post('golongan', true),
            'santri' => $this->input->post('santri', true),
            'kategori' => $this->input->post('kategori', true),
            'email' => $this->input->post('email', true),
            'hp' => $this->input->post('hp', true),
            'rekening' => $this->input->post('rekening', true),
        ];

        $this->model->edit('guru', 'guru_id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'guru berhasil diupdate');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'guru gagal diupdate');
            redirect('guru');
        }
    }
}
