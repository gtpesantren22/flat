<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Struktural extends MY_Controller
{
    protected $db_active;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');
        $this->load->library('Dynamic_db');
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

        // $user = $this->Auth_model->current_user();

        // $this->user = $user->nama;
        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }


    public function index()
    {
        $data['judul'] = 'Tunjangan Struktural';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['datas'] = $this->db_active->query("SELECT struktural.*, satminkal.nama as nmsatminkal, jabatan.nama as nmjabatan FROM struktural 
        JOIN satminkal ON satminkal.id=struktural.satminkal_id
        JOIN jabatan ON jabatan.jabatan_id=struktural.jabatan_id
        ORDER BY struktural.nominal ASC
        ")->result();

        $data['jabatanOpt'] = $this->model->getData('jabatan')->result();
        $data['satminkalOpt'] = $this->model->getData('satminkal')->result();
        $this->load->view('struktural', $data);
    }

    public function tambah()
    {
        $satminkal = $this->input->post('satminkal');
        $jabatan_id = $this->input->post('jabatan', true);
        $masa_kerja = $this->input->post('masa_kerja', true);
        $jam_kerja = $this->input->post('jam_kerja', true);
        $nominal = rmRp($this->input->post('nominal', true));

        if (!empty($satminkal)) {
            foreach ($satminkal as $satminkal_id) {
                $cek = $this->model->getBy2('struktural', 'satminkal_id', $satminkal_id, 'jabatan_id', $jabatan_id)->row();
                if (!$cek) {
                    $data = [
                        'satminkal_id' => $satminkal_id,
                        'jabatan_id' => $jabatan_id,
                        'nominal' => $nominal,
                        'masa_kerja' => $masa_kerja,
                        'jam_kerja' => $jam_kerja,
                    ];
                    $this->model->tambah('struktural', $data);
                }
            }
            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'struktural berhasil ditambahkan');
                redirect('struktural');
            } else {
                $this->session->set_flashdata('error', 'struktural gagal ditambahkan');
                redirect('struktural');
            }
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('struktural', 'struktural_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'struktural berhasil dihapus');
            redirect('struktural');
        } else {
            $this->session->set_flashdata('error', 'struktural gagal dihapus');
            redirect('struktural');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'satminkal_id' => $this->input->post('satminkal', true),
            'jabatan_id' => $this->input->post('jabatan', true),
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'jam_kerja' => $this->input->post('jam_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('struktural', 'struktural_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'struktural berhasil diupdate');
            redirect('struktural');
        } else {
            $this->session->set_flashdata('error', 'struktural gagal diupdate');
            redirect('struktural');
        }
    }

    public function reload()
    {
        $gru = $this->model->getData('guru')->result();
        foreach ($gru as $guru) {
            $cek = $this->model->getBy2('struktural', 'satminkal_id', $guru->satminkal, 'jabatan_id', $guru->jabatan)->row();
            if (!$cek) {
                $datasv = [
                    'satminkal_id' => $guru->satminkal,
                    'jabatan_id' => $guru->jabatan
                ];
                $this->model->tambah('struktural', $datasv);
            } else {
                $datasv = [
                    'satminkal_id' => $guru->satminkal,
                    'jabatan_id' => $guru->jabatan
                ];
                $this->model->edit('struktural', 'struktural_id', $cek->struktural_id, $datasv);
            }
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'struktural berhasil diupdate');
            redirect('struktural');
        } else {
            $this->session->set_flashdata('error', 'struktural gagal diupdate');
            redirect('struktural');
        }
    }
}
