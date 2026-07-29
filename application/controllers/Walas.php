<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Walas extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');

        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }

        // Self-healing database migration for satminkal column in walas table
        if (!$this->db_active->field_exists('satminkal', 'walas')) {
            $this->db_active->query("ALTER TABLE walas ADD COLUMN satminkal VARCHAR(100) NOT NULL DEFAULT '-'");
            // Fill existing records based on teacher's main satminkal
            $this->db_active->query("UPDATE walas JOIN guru ON guru.guru_id = walas.guru_id SET walas.satminkal = guru.satminkal WHERE walas.satminkal = '-' OR walas.satminkal = '' OR walas.satminkal IS NULL");
        }
    }

    public function index()
    {
        $data['judul'] = 'Tunjangan Wali Kelas';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        // Join satminkal using walas.satminkal instead of guru.satminkal to respect specific penugasan
        $data['data'] = $this->db_active->query("SELECT walas.*, guru.nama as nmguru, satminkal.nama as lembaga FROM walas JOIN guru ON guru.guru_id=walas.guru_id LEFT JOIN satminkal ON satminkal.id=walas.satminkal ")->result();

        // Query unique satminkal currently assigned to teachers in the walas table
        $data['lembagaList'] = $this->db_active->query("
            SELECT DISTINCT walas.satminkal, satminkal.nama,
                   (SELECT nominal FROM walas w2 WHERE w2.satminkal = walas.satminkal LIMIT 1) as nominal
            FROM walas
            JOIN satminkal ON satminkal.id = walas.satminkal
        ")->result();

        $data['guruOpt'] = $this->model->getData('guru')->result();
        $this->load->view('walas', $data);
    }

    public function tambah()
    {
        $guru_id = $this->input->post('guru', true);
        $guru = $this->db_active->get_where('guru', ['guru_id' => $guru_id])->row();
        $satminkal = $guru ? $guru->satminkal : '-';

        $data = [
            'guru_id' => $guru_id,
            'satminkal' => $satminkal,
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
        $guru_id = $this->input->post('guru', true);
        $guru = $this->db_active->get_where('guru', ['guru_id' => $guru_id])->row();
        $satminkal = $guru ? $guru->satminkal : '-';

        $data = [
            'guru_id' => $guru_id,
            'satminkal' => $satminkal,
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

    public function sync()
    {
        // 1. Find the jabatan_id of "Wali Kelas" in the local jabatan table
        $jabatan = $this->db_active->like('nama', 'Wali Kelas')->get('jabatan')->row();
        
        if (!$jabatan) {
            $this->session->set_flashdata('error', 'Jabatan "Wali Kelas" tidak ditemukan di tabel jabatan. Silakan lakukan sinkronisasi jabatan terlebih dahulu.');
            redirect('walas');
            return;
        }

        // 2. Fetch all entries from registrasi table with this id_jabatan
        $registrasis = $this->db_active->where('id_jabatan', $jabatan->jabatan_id)->get('registrasi')->result();

        if (empty($registrasis)) {
            $this->session->set_flashdata('error', 'Tidak ada data guru dengan jabatan "Wali Kelas" di tabel registrasi.');
            redirect('walas');
            return;
        }

        $inserted = 0;
        foreach ($registrasis as $reg) {
            // Check if combination already exists in walas table
            $exists = $this->db_active->get_where('walas', [
                'guru_id' => $reg->id_guru,
                'satminkal' => $reg->id_lembaga
            ])->row();

            if (!$exists) {
                $this->db_active->insert('walas', [
                    'guru_id' => $reg->id_guru,
                    'satminkal' => $reg->id_lembaga,
                    'nominal' => 0
                ]);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            $this->session->set_flashdata('ok', 'Berhasil menambahkan ' . $inserted . ' data tunjangan Wali Kelas baru.');
        } else {
            $this->session->set_flashdata('ok', 'Data tunjangan Wali Kelas sudah sinkron.');
        }

        redirect('walas');
    }

    public function update_nominal_lembaga()
    {
        $nominals = $this->input->post('nominal', true);
        if (is_array($nominals)) {
            $this->db_active->trans_start();
            foreach ($nominals as $satminkal_id => $nominal) {
                $nominal_clean = rmRp($nominal);
                $this->db_active->where('satminkal', $satminkal_id)->update('walas', ['nominal' => $nominal_clean]);
            }
            $this->db_active->trans_complete();

            if ($this->db_active->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Gagal memperbarui nominal per lembaga.');
            } else {
                $this->session->set_flashdata('ok', 'Berhasil memperbarui nominal tunjangan Walas per lembaga.');
            }
        }
        redirect('walas');
    }
}
