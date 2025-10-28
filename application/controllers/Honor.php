<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Honor extends MY_Controller
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

        $this->honor_santri = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $this->honor_non = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
        $this->honor_rami = $this->model->getBy('settings', 'nama', 'honor_rami')->row('isi');
    }

    public function index()
    {
        $data['judul'] = 'Honor';
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Honor');

        $honordata = $this->db_active->query("SELECT * FROM honor GROUP BY honor_id ORDER BY created_at DESC");
        $data['honorGroup'] = $honordata->result();
        $data['honorId'] = $honordata->row('honor_id');
        $data['honor'] = $this->model->getData('sik_setting')->result();
        $data['nominal'] = $this->db_active->query("SELECT * FROM settings WHERE nama LIKE 'honor_%' ")->result();

        $this->load->view('honor', $data);
    }

    public function update_sik()
    {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');
        $this->Auth_model->log_activity($this->userID, 'Akses update SIK C: Honor');

        $this->model->edit('sik_setting', 'id', $id, [$field => $value]);

        if ($this->db_active->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function buatBaru()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses buat data honor baru C: Honor');
        $bulan = $this->input->post('bulan', true);
        $tahun = $this->input->post('tahun', true);
        $guru = $this->model->getBy('guru', 'sik', 'PTTY')->result();
        $at = date('Y-m-d H:i');
        $id = $this->uuid->v4('');
        foreach ($guru as $value) {
            $data = [
                'created_at' => $at,
                'guru_id' => $value->guru_id,
                'honor_id' => $id,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'lembaga' => $value->satminkal,
            ];
            $this->model->tambah('honor', $data);
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Honor berhasil dibuat');
            redirect('honor');
        }
    }

    public function rincian()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses rincian honor C: Honor');
        $id = $this->input->post('id', true);

        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search_value = isset($this->input->post('search')['value']) ? $this->input->post('search')['value'] : '';

        $length = $length > 0 ? $length : 10;
        $start = $start >= 0 ? $start : 0;
        // $bulanIni = date('m');
        if ($id != 0) {
            $honorID = $this->model->getBy('honor', 'id', $id)->row('honor_id');
            $this->db_active->from('honor');
            $this->db_active->join('guru', 'honor.guru_id=guru.guru_id');
            $this->db_active->where('honor_id', $honorID);
            $this->db_active->order_by('guru.nama', 'ASC');
        } else {
            $honorID = $this->db_active->query("SELECT honor_id FROM honor GROUP BY honor_id ORDER BY created_at DESC LIMIT 1")->row('honor_id');
            $this->db_active->from('honor');
            $this->db_active->join('guru', 'honor.guru_id=guru.guru_id');
            $this->db_active->where('honor.honor_id', $honorID);
            $this->db_active->order_by('guru.nama', 'ASC');
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
            $lembaga = $this->model->getBy('satminkal', 'id', $row->lembaga)->row();
            // $total = $this->db_active->query("SELECT kehadiran FROM honor WHERE guru_id = '$row->guru_id' AND honor_id = '$row->honor_id' ")->row();
            // $hasil_hadir = $row->kehadiran;
            // if ($row->lembaga == 8 || $row->lembaga == 9) {
            //     $honorGuru = $row->kehadiran * $this->honor_rami;
            //     $totalHonor = $total ? $total->total * $this->honor_rami : 0;
            // } else {
            //     $honorGuru = $gruru->santri == 'santri' ? $row->kehadiran * $this->honor_santri : $row->kehadiran * $this->honor_non;
            //     $totalHonor = $total ? ($total->total) * ($gruru->santri == 'santri' ?  $this->honor_santri : $this->honor_non) : 0;
            // }

            $data[] = [
                $row_number++, // 0
                $gruru ? $gruru->nama : '',  // 1
                $gruru ? $gruru->santri : '', // 2
                $row->kehadiran ? $row->kehadiran : '', // 3
                $row->nominal ? $row->nominal : 0, // 4 
                $row->id, // 5
                bulan($row->bulan) . ' ' . $row->tahun, // 6
                $lembaga ? $lembaga->nama : '', // 7
                // $row->nominal, // 8
                0, // 8
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
        $this->Auth_model->log_activity($this->userID, 'Akses eidt jam perguru C: Honor');
        $id = $this->input->post('id', true);
        $jam = $this->input->post('value', true);
        $dtlHonor = $this->model->getBy('honor', 'id', $id)->row();
        $guru = $this->model->getBy('guru', 'guru_id', $dtlHonor->guru_id)->row();

        if ($dtlHonor->lembaga == 8 || $dtlHonor->lembaga == 9) {
            $nominal = $jam * $this->honor_rami;
        } else {
            $nominal = $guru->santri == 'santri' ? $jam * $this->honor_santri : $jam * $this->honor_non;
        }

        $this->model->edit('honor', 'id', $id, ['kehadiran' => $jam, 'nominal' => $nominal]);
        if ($this->db_active->affected_rows() > 0) {
            echo json_encode(['status' => 'ok', 'besaran' => $nominal]);
        } else {
            echo json_encode(['status' => 'gagal']);
        }
    }

    public function refresh()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses refresh data guru C: Honor');

        $id = $this->input->post('id', true);
        $honor = $this->model->getBy('honor', 'id', $id)->row();

        $guru = $this->db_active->query("SELECT * FROM guru WHERE NOT EXISTS (SELECT 1 FROM honor WHERE honor_id = '$honor->honor_id' AND honor.guru_id = guru.guru_id) AND sik = 'PTTY' ");
        if ($guru->row()) {
            foreach ($guru->result() as $value) {
                $data = [
                    'guru_id' => $value->guru_id,
                    'honor_id' => $honor->honor_id,
                    'bulan' => $honor->bulan,
                    'tahun' => $honor->tahun,
                    'created_at' => date('Y-m-d H:i'),
                ];
                $this->model->tambah('honor', $data);
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
    public function updateNominal($id)
    {
        $this->Auth_model->log_activity($this->userID, 'Akses update nominal semua guru C: Honor');

        $honor = $this->model->getBy('honor', 'honor_id', $id)->result();
        foreach ($honor as $key) {
            $guru = $this->model->getBy('guru', 'guru_id', $key->guru_id)->row();
            if ($key->lembaga == 8 || $key->lembaga == 9) {
                $nominal = $key->kehadiran * $this->honor_rami;
            } else {
                $nominal = $guru->santri == 'santri' ? $key->kehadiran * $this->honor_santri : $key->kehadiran * $this->honor_non;
            }

            $this->model->edit('honor', 'id', $key->id, ['nominal' => $nominal]);
        }

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Update nominal selesai');
            redirect('honor');
        } else {
            $this->session->set_flashdata('ok', 'Update nominal selesai');
            redirect('honor');
        }
    }
}
