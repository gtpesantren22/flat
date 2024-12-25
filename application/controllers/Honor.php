<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Honor extends CI_Controller
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
        $this->honor_santri = 3000;
        $this->honor_non = 6000;
    }

    public function index()
    {
        $data['judul'] = 'Honor';
        $data['user'] = $this->Auth_model->current_user();

        $data['honorGroup'] = $this->db->query("SELECT * FROM honor GROUP BY honor_id ORDER BY created_at DESC")->result();
        $data['honor'] = $this->model->getData('sik_setting')->result();

        $this->load->view('honor', $data);
    }

    public function update_sik()
    {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        $this->model->edit('sik_setting', 'id', $id, [$field => $value]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function buatBaru()
    {
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
            ];
            $this->model->tambah('honor', $data);
        }
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Honor berhasil dibuat');
            redirect('honor');
        }
    }

    public function rincian()
    {
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
            $this->db->from('honor');
            $this->db->join('guru', 'honor.guru_id=guru.guru_id');
            $this->db->where('honor_id', $honorID);
        } else {
            $honorID = $this->db->query("SELECT honor_id FROM honor GROUP BY honor_id ORDER BY created_at DESC LIMIT 1")->row('honor_id');
            $this->db->from('honor');
            $this->db->join('guru', 'honor.guru_id=guru.guru_id');
            $this->db->where('honor.honor_id', $honorID);
        }


        // Filter search
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('guru.nama', $search_value);
            $this->db->or_like('guru.santri', $search_value);
            $this->db->group_end();
        }

        $total_records = $this->db->count_all_results('', false); // Count total records without limit

        $this->db->limit($length, $start);
        $query = $this->db->get();
        $data = [];
        $row_number = $start + 1;

        foreach ($query->result() as $row) {
            $gruru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            // $hasil_hadir = $row->kehadiran / 4;
            $hasil_hadir = $row->kehadiran;
            $data[] = [
                $row_number++, // 0
                $gruru->nama,  // 1
                $gruru->santri, // 2
                $row->kehadiran, // 3
                $gruru->santri == 'santri' ? $hasil_hadir * $this->honor_santri : $hasil_hadir * $this->honor_non, // 4 
                $row->id, // 5
                bulan($row->bulan) . ' ' . $row->tahun, // 6
                // $row->ijazah, // 7
                // $row->tmt, // 8
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
        $id = $this->input->post('id');
        $jam = $this->input->post('value');
        $dtlHonor = $this->model->getBy('honor', 'id', $id)->row();
        $guru = $this->model->getBy('guru', 'guru_id', $dtlHonor->guru_id)->row();
        $nomBesaran = $guru->santri == 'santri' ? $this->honor_santri : $this->honor_non;

        $this->model->edit('honor', 'id', $id, ['kehadiran' => $jam]);
        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'ok', 'besaran' => $nomBesaran]);
        } else {
            echo json_encode(['status' => 'gagal']);
        }
    }

    public function refresh()
    {
        $id = $this->input->post('id', true);
        $honor = $this->model->getBy('honor', 'id', $id)->row();

        $guru = $this->db->query("SELECT * FROM guru WHERE NOT EXISTS (SELECT 1 FROM honor WHERE honor_id = '$honor->honor_id' AND honor.guru_id = guru.guru_id) AND sik = 'PTTY' ");
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
            if ($this->db->affected_rows() > 0) {
                echo json_encode(['status' => 'ok']);
            } else {
                echo json_encode(['status' => 'gagal']);
            }
        } else {
            echo json_encode(['status' => 'ok']);
        }
    }
}
