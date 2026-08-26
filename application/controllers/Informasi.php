<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Informasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');

        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }

    public function slip($id_detail)
    {
        // 1. Fetch detailed salary information
        $detail = $this->db_active->select('gd.*, g.bulan, g.tahun')
            ->from('gaji_detail gd')
            ->join('gaji g', 'g.gaji_id = gd.gaji_id')
            ->where('gd.id_detail', $id_detail)
            ->get()
            ->row_array();

        if (empty($detail)) {
            show_404();
            return;
        }

        $guru_id = $detail['guru_id'];
        $gaji_id = $detail['gaji_id'];

        // 2. Fetch potongan (deductions)
        $potongan = $this->db_active->get_where('potongan', [
            'bulan' => $detail['bulan'],
            'tahun' => $detail['tahun'],
            'guru_id' => $guru_id
        ])->result_array();

        // 3. Fetch tambahan (additions)
        $tambahan = $this->db_active->query("
            SELECT tambahan.nama, (tambahan.nominal * tambahan_detail.jumlah) AS nominal 
            FROM tambahan_detail 
            JOIN tambahan ON tambahan.id_tambahan = tambahan_detail.id_tambahan 
            WHERE tambahan_detail.guru_id = ? AND tambahan_detail.gaji_id = ?
        ", [$guru_id, $gaji_id])->result_array();

        $data['detail'] = $detail;
        $data['data'] = $detail; // $data uses the same array
        $data['potongan'] = $potongan;
        $data['tambahan'] = $tambahan;

        $this->load->view('slip_gaji', $data);
    }

    public function get_slips_by_satminkal()
    {
        $gaji_id = $this->input->post('gaji_id', true);
        $satminkal = $this->input->post('satminkal', true);

        if (empty($gaji_id) || empty($satminkal)) {
            echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
            return;
        }

        // 1. Fetch all detailed salary records for this satminkal
        $details = $this->db_active->select('gd.*, g.bulan, g.tahun')
            ->from('gaji_detail gd')
            ->join('gaji g', 'g.gaji_id = gd.gaji_id')
            ->where('gd.gaji_id', $gaji_id)
            ->where('gd.satminkal', $satminkal)
            ->get()
            ->result_array();

        $slips = [];
        foreach ($details as $detail) {
            $guru_id = $detail['guru_id'];

            // 2. Fetch potongan (deductions)
            $potongan = $this->db_active->get_where('potongan', [
                'bulan' => $detail['bulan'],
                'tahun' => $detail['tahun'],
                'guru_id' => $guru_id
            ])->result_array();

            // 3. Fetch tambahan (additions)
            $tambahan = $this->db_active->query("
                SELECT tambahan.nama, (tambahan.nominal * tambahan_detail.jumlah) AS nominal 
                FROM tambahan_detail 
                JOIN tambahan ON tambahan.id_tambahan = tambahan_detail.id_tambahan 
                WHERE tambahan_detail.guru_id = ? AND tambahan_detail.gaji_id = ?
            ", [$guru_id, $gaji_id])->result_array();

            $slips[] = [
                'detail' => $detail,
                'potongan' => $potongan,
                'tambahan' => $tambahan
            ];
        }

        echo json_encode([
            'status' => 'success',
            'slips' => $slips,
            'bulan_nama' => bulan($details[0]['bulan'] ?? 1),
            'tahun' => $details[0]['tahun'] ?? ''
        ]);
    }
}
