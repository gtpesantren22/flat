<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perbandingan extends CI_Controller
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
        $this->jamkinerja = 24;
    }

    public function index()
    {
        $data['judul'] = 'Perbandingan';
        $data['sub'] = '';
        $data['user'] = $this->Auth_model->current_user();

        $dataguru = $this->db->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id ")->result();
        $kirim = [];
        foreach ($dataguru as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
            $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();

            if ($guru->sik === 'PTY') {
                $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok1 ? $gapok1->nominal : 0;
            } else {
                $gapok1 = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row();
                // $gapok2 = $gapok1 ? $gapok1->kehadiran / 4 : 0;
                $gapok2 = $gapok1 ? $gapok1->kehadiran : 0;
                $gapok = $guru->santri == 'santri' ? $gapok2 * $this->honor_santri : $gapok2 * $this->honor_non;
            }

            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
            $payments = array_column($cek, 'payment');

            $kirim[] = [
                'id' =>  $row->id,
                'nama' =>  $guru->nama,
                'sik' =>  $guru->sik,
                'lembaga' =>  $satminkal->nama,
                'jabatan' =>  $jabatan->nama,
                'sebelum' =>  $row->nominal,
                'total' => (in_array('gapok', $payments) ? $gapok : 0) +
                    ($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0) +
                    ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $this->jamkinerja : 0) +
                    ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0) +
                    ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0) + // 13
                    ($walas && in_array('walas', $payments) ? $walas->nominal : 0) + // 14
                    ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) // 15
            ];
        }
        $data['hasil'] = $kirim;

        $this->load->view('perbandingan', $data);
    }

    public function detail()
    {
        $id = $this->input->post('id', 'true');
        $data = $this->model->getBy('perbandingan', 'id', $id)->row();

        $dataguru = $this->db->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id WHERE a.guru_id = '$data->guru_id' ")->row();

        $row = $dataguru;
        $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
        $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
        $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
        $golongan = $this->model->getBy('golongan', 'id', $guru->golongan)->row();
        $ijazah = $this->model->getBy('ijazah', 'id', $guru->ijazah)->row();

        if ($guru->sik === 'PTY') {
            $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $gapok = $gapok1 ? $gapok1->nominal : 0;
        } else {
            $gapok1 = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row();
            // $gapok2 = $gapok1 ? $gapok1->kehadiran / 4 : 0;
            $gapok2 = $gapok1 ? $gapok1->kehadiran : 0;
            $gapok = $guru->santri == 'santri' ? $gapok2 * $this->honor_santri : $gapok2 * $this->honor_non;
        }

        $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
        $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
        $struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
        $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
        $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
        $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
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
            'kinerja' => ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $this->jamkinerja : 0),
            'struktural' => ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0),
            'bpjs' => ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0), // 13
            'walas' => ($walas && in_array('walas', $payments) ? $walas->nominal : 0), // 14
            'penyesuaian' => ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) // 15
        ]);
    }
}
