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
        $this->honor_santri = 7000;
        $this->honor_non = 14000;
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

            if ($guru->sik === 'PTY') {
                $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok1 ? $gapok1->nominal : 0;
            } else {
                $gapok1 = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', date('m'), 'tahun', date('Y'))->row();
                $gapok2 = $gapok1 ? $gapok1->kehadiran / 4 : 0;
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
                'nama' =>  $guru->nama, // 9
                'sik' =>  $guru->sik, // 9
                'lembaga' =>  $satminkal->nama, // 9
                'sebelum' =>  $row->nominal, // 9
                'total' => (in_array('gapok', $payments) ? $gapok : 0) + // 9
                    ($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0) + // 10
                    ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $this->jamkinerja : 0) + // 11
                    ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0) + // 12
                    ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0) + // 13
                    ($walas && in_array('walas', $payments) ? $walas->nominal : 0) + // 14
                    ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) // 15
            ];
        }
        $data['hasil'] = $kirim;

        $this->load->view('perbandingan', $data);
    }
}
