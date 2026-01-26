<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pembanding extends MY_Controller
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
        $data['judul'] = 'Pembanding';
        $data['sub'] = 'master';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db_active->query("SELECT guru.nama, perbandingan.* FROM perbandingan JOIN guru ON guru.guru_id=perbandingan.guru_id ")->result();
        $this->load->view('pembanding', $data);
    }

    public function reload()
    {
        $guru = $this->db_active->query("SELECT * FROM guru WHERE NOT EXISTS (SELECT 1 FROM perbandingan WHERE perbandingan.guru_id = guru.guru_id) ");
        if ($guru->row()) {
            foreach ($guru->result() as $value) {
                $data = [
                    'guru_id' => $value->guru_id,
                    'nominal' => 0,
                ];
                $this->model->tambah('perbandingan', $data);
            }
            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data pembanding diperbarui');
                redirect('pembanding');
            } else {
                $this->session->set_flashdata('error', 'Data pembanding gagal');
                redirect('pembanding');
            }
        } else {
            $this->session->set_flashdata('ok', 'Data pembanding diperbarui');
            redirect('pembanding');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('perbandingan', 'id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Pembanding berhasil dihapus');
            redirect('pembanding');
        } else {
            $this->session->set_flashdata('error', 'Pembanding gagal dihapus');
            redirect('pembanding');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('perbandingan', 'id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Pembanding berhasil diupdate');
            redirect('pembanding');
        } else {
            $this->session->set_flashdata('error', 'Pembanding gagal diupdate');
            redirect('pembanding');
        }
    }
}
