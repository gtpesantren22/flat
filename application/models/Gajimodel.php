<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gajimodel extends CI_Model
{
    protected $db_active;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()

        $ijazah = $this->model->getBy('settings', 'nama', 'ijazah')->row('isi');
        $this->minimum = explode(',', $ijazah);
        $str = $this->model->getBy('settings', 'nama', 'struktural')->row('isi');
        $this->struktural = explode(',', $str);

        $this->token = $this->model->getBy('settings', 'nama', 'token')->row('isi');
    }

    public function gapok($guru_id, $kriteria, $sik, $golongan, $tmt, $jabatan, $bulan, $tahun)
    {
        if ($sik === 'PTY') {
            $gapok = $this->db_active
                ->where('golongan_id', $golongan)
                ->where('masa_kerja', selisihTahun($tmt))
                ->get('gapok')
                ->row();
            $gapok = $gapok &&  !in_array($jabatan, $this->struktural) ? $gapok->nominal : 0;
        } else {
            $gapok1 = $this->db_active
                ->select_sum('nominal')
                ->where([
                    'guru_id' => $guru_id,
                    'bulan'   => $bulan,
                    'tahun'   => $tahun
                ])
                ->get('honor')
                ->row();
            $gapok = $gapok1 &&  !in_array($jabatan, $this->struktural) && $kriteria != 'Karyawan' ? $gapok1->nominal : 0;
        }

        return $gapok;
    }

    public function fungsional($golongan, $kriteria, $sik, $ijazah)
    {
        $fungsional = $this->db_active
            ->where('golongan_id', $golongan)
            ->get('fungsional')
            ->row();
        $nominal = $fungsional && $kriteria == 'Guru' && $sik == 'PTY' && in_array($ijazah, $this->minimum) ? $fungsional->nominal : 0;
        return $nominal;
    }

    public function kirnerja($guru_id, $bulan, $tahun, $tmt, $kriteria, $jabatan)
    {
        $kehadiran = $this->db_active
            ->where('guru_id', $guru_id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get('kehadiran')
            ->row();

        $kinerja = $this->db_active
            ->where('masa_kerja', selisihTahun($tmt))
            ->get('kinerja')
            ->row();

        $nominal = $kinerja && $kriteria == 'Karyawan' &&  !in_array($jabatan, $this->struktural) ? $kinerja->nominal * ($kehadiran ? $kehadiran->kehadiran : 0) : 0;
        return $nominal;
    }

    public function struktural($kriteria, $jabatan, $satminkal)
    {
        if ($kriteria == 'Pengabdian') {
            $struktural = $this->pengabdian;
        } else {
            $struktural = $this->db_active
                ->select('nominal')
                ->where('jabatan_id', $jabatan)
                ->where('satminkal_id', $satminkal)
                ->get('struktural')
                ->row('nominal');
        }
        $nominal = $struktural ? $struktural : 0;
        return $nominal;
    }

    public function penyesuaian($guru_id, $kriteria, $jabatan, $sik)
    {
        $penyesuaian = $this->db_active
            ->where('guru_id', $guru_id)
            ->get('penyesuaian')
            ->row();

        $nominal = $penyesuaian && $kriteria != 'Pengabdian' &&  !in_array($jabatan, $this->struktural) && $sik === 'PTY' ? $penyesuaian->nominal : 0;
        return $nominal;
    }

    public function tambahan($guru_id, $gaji_id)
    {
        $tambahan = $this->db_active
            ->select('SUM(tambahan.nominal * tambahan_detail.jumlah) AS total', false)
            ->from('tambahan_detail')
            ->join('tambahan', 'tambahan.id_tambahan = tambahan_detail.id_tambahan')
            ->where('guru_id', $guru_id)
            ->where('gaji_id', $gaji_id)
            ->get()
            ->row();

        return $tambahan->total;
    }

    public function detailGuru($guru_id)
    {
        $apiUrl = 'https://data.ppdwk.com/api/ptk/show/' . $guru_id;

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null; // ✅ cukup return data
        }

        $api = json_decode($response, true);
        return $api ?? null;
    }
}
