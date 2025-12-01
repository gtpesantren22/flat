<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perbandingan extends MY_Controller
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

        $this->pengabdian = $this->model->getBy('settings', 'nama', 'pengabdian')->row('isi');
        $this->honor_santri = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $this->honor_non = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
        $ijazah = $this->model->getBy('settings', 'nama', 'ijazah')->row('isi');
        $this->minimum = explode(',', $ijazah);
        $str = $this->model->getBy('settings', 'nama', 'struktural')->row('isi');
        $this->struktural = explode(',', $str);
    }

    public function index()
    {
        $data['judul'] = 'Perbandingan';
        $data['sub'] = '';
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Perbandingan');
        $bulan = date('m');
        $tahun = date('Y');
        $gaji = $this->model->getBy2('gaji', 'tahun', $tahun, 'bulan', $bulan)->row();

        $dataguru = $this->db_active->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id ")->result();
        $kirim = [];
        foreach ($dataguru as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
            $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
            $hadir = $this->model->getBy3('kehadiran', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row();

            if ($guru->sik === 'PTY') {
                $gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok ? $gapok->nominal : 0;
            } else {
                $gapok1 = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = '$tahun' GROUP BY honor.guru_id")->row();
                $gapok = $gapok1 && $guru->kriteria != 'Karyawan' ? $gapok1->nominal : 0;
            }

            $fungsional = $this->model->getBy('fungsional', 'golongan_id', $guru->golongan)->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            if ($guru->kriteria == 'Pengabdian') {
                $struktural = $this->pengabdian;
            } else {
                $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row('nominal');
            }
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'guru_id', $guru->guru_id)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $tambahan = $this->db_active->query("SELECT SUM(tambahan.nominal*tambahan_detail.jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$guru->guru_id' AND gaji_id = '$gaji->gaji_id' ")->row();
            $totalGaji = ($gapok) +
                ($fungsional && $guru->kriteria == 'Guru' && $guru->sik == 'PTY' && in_array($guru->ijazah, $this->minimum) ? $fungsional->nominal : 0) +
                ($kinerja && $guru->kriteria == 'Karyawan' &&  !in_array($guru->jabatan, $this->struktural) ? $kinerja->nominal * ($hadir ? $hadir->kehadiran : 0) : 0) +
                ($struktural ? $struktural : 0) +
                ($bpjs ? $bpjs->nominal : 0) +
                ($walas && !$struktural ? $walas->nominal : 0) +
                ($penyesuaian && $guru->kriteria != 'Pengabdian' ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) +
                $tambahan->total;
            $masaKerja = selisihTahun($guru->tmt);
            if ($masaKerja < 2 && $guru->sik === 'PTY') {
                $totalGaji = $totalGaji * 0.8;
            }
            $kirim[] = [
                'id' =>  $row->id,
                'nama' =>  $guru->nama,
                'guru_id' =>  $guru->guru_id,
                'sik' =>  $guru->sik,
                'lembaga' =>  $satminkal ? $satminkal->nama : '',
                'jabatan' =>  $jabatan ? $jabatan->nama : '',
                'sebelum' =>  $row->nominal,
                'total' => $totalGaji
            ];
        }
        $data['hasil'] = $kirim;

        $this->load->view('perbandingan', $data);
    }

    public function detail()
    {
        $id = $this->input->post('id', 'true');
        $data = $this->model->getBy('perbandingan', 'id', $id)->row();
        $bulan = date('m');
        $tahun = date('Y');
        $gaji = $this->model->getBy2('gaji', 'tahun', $tahun, 'bulan', $bulan)->row();
        $this->Auth_model->log_activity($this->userID, 'Akses detail C: Perbandingan');

        $dataguru = $this->db_active->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id WHERE a.guru_id = '$data->guru_id' ")->row();

        $row = $dataguru;
        $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
        $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
        $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
        $golongan = $this->model->getBy('golongan', 'id', $guru->golongan)->row();
        $ijazah = $this->model->getBy('ijazah', 'id', $guru->ijazah)->row();
        $hadir = $this->model->getBy3('kehadiran', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row();

        if ($guru->sik === 'PTY') {
            $gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $gapok = $gapok ? $gapok->nominal : 0;
        } else {
            $gapok1 = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = '$tahun' GROUP BY honor.guru_id")->row();
            $gapok = $gapok1 && $guru->kriteria != 'Karyawan' ? $gapok1->nominal : 0;
        }

        $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
        $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
        if ($guru->kriteria == 'Pengabdian') {
            $struktural = $this->pengabdian;
        } else {
            $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row('nominal');
        }
        $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
        $walas = $this->model->getBy('walas', 'guru_id', $guru->guru_id)->row();
        $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
        $tambahan = $this->db_active->query("SELECT SUM(tambahan.nominal*tambahan_detail.jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$guru->guru_id' AND gaji_id = '$gaji->gaji_id' ")->row();

        echo json_encode([
            'id' =>  $row->id,
            'nama' =>  $guru->nama,
            'sik' =>  $guru->sik,
            'satminkal' =>  $satminkal->nama,
            'jabatan' =>  $jabatan->nama,
            'golongan' =>  $golongan->nama,
            'ijazah' =>  $ijazah->nama,
            'tmt' =>  $guru->tmt,
            'masa' =>  selisihTahun($guru->tmt),
            'ket' =>  $guru->santri,
            'sebelum' =>  $row->nominal,
            'gapok' =>  $gapok ? $gapok : '0', // 9
            'fungsional' => $fungsional && $guru->kriteria == 'Guru' && $guru->sik == 'PTY' && in_array($guru->ijazah, $this->minimum) ? $fungsional->nominal : 0, // 10
            'kinerja' => $kinerja && $guru->kriteria == 'Karyawan' &&  !in_array($row->jabatan, $this->struktural) ? $kinerja->nominal * ($hadir ? $hadir->kehadiran : 0) : 0, // 11
            'struktural' => $struktural ? $struktural : 0, // 12
            'bpjs' => $bpjs ? $bpjs->nominal : 0, // 13
            'walas' => $walas && !$struktural ? $walas->nominal : 0, // 14
            'penyesuaian' => $penyesuaian && $guru->kriteria != 'Pengabdian' ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0, // 15
            'tambahan' => $tambahan && $tambahan->total != null ? $tambahan->total : 0 // 16
        ]);
    }

    public function sesuaikan()
    {
        $guru_id = $this->input->post('guru_id', 'true');
        $flat = $this->input->post('flat', 'true');
        $sebelum = $this->input->post('sebelum', 'true');
        $this->Auth_model->log_activity($this->userID, 'Akses proses penyesuaian C: Perbandingan');

        $cek = $this->model->getBy('penyesuaian', 'guru_id', $guru_id)->row();
        if ($cek) {
            $this->model->edit('penyesuaian', 'guru_id', $guru_id, ['sebelum' => $sebelum, 'sesudah' => $flat]);
            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data berhasil disesuaikan');
                redirect('perbandingan');
            } else {
                $this->session->set_flashdata('error', 'Data gagal disesuaikan');
                redirect('perbandingan');
            }
        } else {
            $this->model->tambah('penyesuaian', ['guru_id' => $guru_id, 'sebelum' => $sebelum, 'sesudah' => $flat]);
            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data berhasil disesuaikan');
                redirect('perbandingan');
            } else {
                $this->session->set_flashdata('error', 'Data gagal disesuaikan');
                redirect('perbandingan');
            }
        }
    }
}
