<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');

        $user = $this->Auth_model->current_user();
        $this->userID = $user->id_user;

        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }

    public function index()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Kinerja');

        $data['judul'] = 'Tunjangan Kinerja';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getdata('kinerja')->result();
        $data['absen'] = $this->model->getGroup('kehadiran', 'kehadiran_id')->result();

        $this->load->view('kinerja', $data);
    }

    public function tambah()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses tambah data C: Kinerja');

        $data = [
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('kinerja', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil ditambahkan');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal ditambahkan');
            redirect('kinerja');
        }
    }

    public function hapus($id)
    {
        $this->Auth_model->log_activity($this->userID, 'Akses hpus data C: Kinerja');

        $this->model->hapus('kinerja', 'kinerja_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil dihapus');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal dihapus');
            redirect('kinerja');
        }
    }

    public function edit()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses ediit data C: Kinerja');

        $id = $this->input->post('id', true);
        $data = [
            'masa_kerja' => $this->input->post('masa_kerja', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('kinerja', 'kinerja_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kinerja berhasil diupdate');
            redirect('kinerja');
        } else {
            $this->session->set_flashdata('error', 'kinerja gagal diupdate');
            redirect('kinerja');
        }
    }
    public function buatBaru()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses baut data baru C: Kinerja');

        $bulan = $this->input->post('bulan', true);
        $tahun = $this->input->post('tahun', true);
        $guru = $this->db_active->query("SELECT guru_id FROM guru JOIN kategori ON guru.kategori=kategori.id WHERE kategori.nama = 'Karyawan' ")->result();
        $at = date('Y-m-d H:i');
        $id = $this->uuid->v4('');
        foreach ($guru as $value) {
            $data = [
                'created_at' => $at,
                'guru_id' => $value->guru_id,
                'kehadiran_id' => $id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ];
            $this->model->tambah('kehadiran', $data);
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'kehadiran berhasil dibuat');
            redirect('kinerja');
        }
    }

    public function rincian()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses data rincian C: Kinerja');

        $id = $this->input->post('id', true);

        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search_value = isset($this->input->post('search')['value']) ? $this->input->post('search')['value'] : '';

        $length = $length > 0 ? $length : 10;
        $start = $start >= 0 ? $start : 0;
        // $bulanIni = date('m');
        if ($id != 0) {
            $kehadiranID = $this->model->getBy('kehadiran', 'id', $id)->row('kehadiran_id');
            $this->db_active->from('kehadiran');
            $this->db_active->join('guru', 'kehadiran.guru_id=guru.guru_id');
            $this->db_active->where('kehadiran_id', $kehadiranID);
            $this->db_active->where('guru.kategori', 5);
        } else {
            $kehadiranID = $this->db_active->query("SELECT kehadiran_id FROM kehadiran GROUP BY kehadiran_id ORDER BY created_at DESC LIMIT 1")->row('kehadiran_id');
            $this->db_active->from('kehadiran');
            $this->db_active->join('guru', 'kehadiran.guru_id=guru.guru_id');
            $this->db_active->where('kehadiran.kehadiran_id', $kehadiranID);
            $this->db_active->where('guru.kategori', 5);
        }


        // Filter search
        if (!empty($search_value)) {
            $this->db_active->group_start();
            $this->db_active->like('guru.nama', $search_value);
            $this->db_active->or_like('guru.santri', $search_value);
            $this->db_active->group_end();
        }

        $total_records = $this->db_active->count_all_results('', false); // Count total records without limit

        $this->db_active->limit($length, $start);
        $query = $this->db_active->get();
        $data = [];
        $row_number = $start + 1;

        foreach ($query->result() as $row) {
            $gruru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $lembaga = $this->model->getBy('satminkal', 'id', $gruru->satminkal)->row();
            // $hasil_hadir = $row->kehadiran / 4;
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($gruru->tmt))->row();
            $jumlah = $row->kehadiran * ($kinerja ? $kinerja->nominal : 0);
            $data[] = [
                $row_number++, // 0
                $gruru->nama,  // 1
                selisihTahun($gruru->tmt), // 2
                $row->kehadiran, // 3
                $jumlah, // 4 
                $row->id, // 5
                bulan($row->bulan) . ' ' . $row->tahun, // 6
                $kinerja ? $kinerja->nominal : 0, // 7
                $lembaga->nama, // 8
            ];
        }

        $output = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        ];

        // Set content-type header and return JSON data
        header('Content-Type: application/json');
        echo json_encode($output);
        // var_dump($output);
    }

    public function editJam()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses edit jam kerja C: Kinerja');

        $id = $this->input->post('id');
        $jam = $this->input->post('value');
        $dtlHonor = $this->model->getBy('kehadiran', 'id', $id)->row();
        $guru = $this->model->getBy('guru', 'guru_id', $dtlHonor->guru_id)->row();
        $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
        $nomBesaran = $kinerja ? $kinerja->nominal : 0;

        $this->model->edit('kehadiran', 'id', $id, ['kehadiran' => $jam]);
        if ($this->db_active->affected_rows() > 0) {
            echo json_encode(['status' => 'ok', 'besaran' => $nomBesaran]);
        } else {
            echo json_encode(['status' => 'gagal']);
        }
    }
    public function refresh()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses refresh data C: Kinerja');

        $id = $this->input->post('id', true);
        $kehadiran = $this->model->getBy('kehadiran', 'id', $id)->row();

        $guru = $this->db_active->query("SELECT * FROM guru WHERE NOT EXISTS (SELECT 1 FROM kehadiran WHERE kehadiran_id = '$kehadiran->kehadiran_id' AND kehadiran.guru_id = guru.guru_id) AND kategori = 5 ");
        if ($guru->row()) {
            foreach ($guru->result() as $value) {
                $data = [
                    'guru_id' => $value->guru_id,
                    'kehadiran_id' => $kehadiran->kehadiran_id,
                    'bulan' => $kehadiran->bulan,
                    'tahun' => $kehadiran->tahun,
                    'created_at' => date('Y-m-d H:i'),
                ];
                $this->model->tambah('kehadiran', $data);
            }
            if ($this->db_active->affected_rows() > 0) {
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'gagal']);
            }
        } else {
            echo json_encode(['status' => 'ok']);
        }
    }
}
