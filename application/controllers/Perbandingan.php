<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perbandingan extends CI_Controller
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

        $dataguru = $this->db->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id ")->result();
        $kirim = [];
        foreach ($dataguru as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
            $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
            $hadir = $this->model->getBy3('kehadiran', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row('kehadiran');

            if ($guru->sik === 'PTY') {
                $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok1 ? $gapok1->nominal : 0;
            } else {
                // $gapok1 = $this->db->query("SELECT SUM(kehadiran) AS kehadiran FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = $tahun ")->row();
                // $gapok2 = $gapok1 ? $gapok1->kehadiran / 4 : 0;
                // $gapok2 = $gapok1 ? $gapok1->kehadiran : 0;
                // $gapok = $guru->santri == 'santri' ? $gapok2 * $this->honor_santri : $gapok2 * $this->honor_non;
                $gapok = $this->db->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = '$tahun' GROUP BY honor.guru_id")->row('nominal');
            }

            // echo $gapok;
            // die();

            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $tambahan = $this->db->query("SELECT SUM(tambahan.nominal) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$guru->guru_id' AND gaji_id = '$gaji->gaji_id' ")->row();

            $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
            $payments = array_column($cek, 'payment');

            $kirim[] = [
                'id' =>  $row->id,
                'nama' =>  $guru->nama,
                'guru_id' =>  $guru->guru_id,
                'sik' =>  $guru->sik,
                'lembaga' =>  $satminkal->nama,
                'jabatan' =>  $jabatan->nama,
                'sebelum' =>  $row->nominal,
                'total' => (in_array('gapok', $payments) ? $gapok : 0) +
                    ($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0) +
                    ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $hadir : 0) +
                    ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0) +
                    ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0) + // 13
                    ($walas && in_array('walas', $payments) ? $walas->nominal : 0) + // 14
                    ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) + $tambahan->total // 15
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

        $dataguru = $this->db->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id WHERE a.guru_id = '$data->guru_id' ")->row();

        $row = $dataguru;
        $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
        $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
        $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
        $golongan = $this->model->getBy('golongan', 'id', $guru->golongan)->row();
        $ijazah = $this->model->getBy('ijazah', 'id', $guru->ijazah)->row();
        $hadir = $this->model->getBy3('kehadiran', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row('kehadiran');

        if ($guru->sik === 'PTY') {
            $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $gapok = $gapok1 ? $gapok1->nominal : 0;
        } else {
            // $gapok1 = $this->db->query("SELECT SUM(kehadiran) AS kehadiran FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = $tahun")->row();
            // $gapok2 = $gapok1 ? $gapok1->kehadiran / 4 : 0;
            // $gapok2 = $gapok1 ? $gapok1->kehadiran : 0;
            // $gapok = $guru->santri == 'santri' ? $gapok2 * $this->honor_santri : $gapok2 * $this->honor_non;
            $gapok = $this->db->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulan AND tahun = '$tahun' GROUP BY honor.guru_id")->row('nominal');
        }

        $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
        $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
        $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
        $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
        $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
        $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
        $tambahan = $this->db->query("SELECT SUM(tambahan.nominal) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$guru->guru_id' AND gaji_id = '$gaji->gaji_id' ")->row();

        $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
        $payments = array_column($cek, 'payment');

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
            'gapok' => (in_array('gapok', $payments) ? $gapok : 0),
            'fungsional' => ($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0),
            'kinerja' => ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $hadir : 0),
            'struktural' => ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0),
            'bpjs' => ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0), // 13
            'walas' => ($walas && in_array('walas', $payments) ? $walas->nominal : 0), // 14
            'penyesuaian' => ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0), // 15
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
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data berhasil disesuaikan');
                redirect('perbandingan');
            } else {
                $this->session->set_flashdata('error', 'Data gagal disesuaikan');
                redirect('perbandingan');
            }
        } else {
            $this->model->tambah('penyesuaian', ['guru_id' => $guru_id, 'sebelum' => $sebelum, 'sesudah' => $flat]);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data berhasil disesuaikan');
                redirect('perbandingan');
            } else {
                $this->session->set_flashdata('error', 'Data gagal disesuaikan');
                redirect('perbandingan');
            }
        }
    }
}
