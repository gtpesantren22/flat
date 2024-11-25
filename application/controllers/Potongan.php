<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Potongan extends CI_Controller
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
        $data['judul'] = 'Potongan';
        $data['sub'] = '';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db->query("SELECT * FROM potongan GROUP BY potongan_id ORDER BY tahun DESC, bulan DESC")->result();

        $this->load->view('potongan', $data);
    }

    public function index2()
    {
        $data['judul'] = 'Potongan';
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
                'nama' =>  $guru->nama, // 9
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

        $this->load->view('potongan', $data);
    }

    public function tambah()
    {
        $id = $this->uuid->v4();
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $potongan_id = $this->uuid->v4();

        $cek = $this->model->getBy2('potongan', 'bulan', $bulan, 'tahun', $tahun)->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'Data potongan sudah ada');
            redirect('gaji');
        }

        $guru = $this->model->getData('guru')->result();
        foreach ($guru as $guruhasil) {
            $data = [
                'guru_id' => $guruhasil->guru_id,
                'potongan_id' => $potongan_id,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ];
            $this->model->tambah('potongan', $data);
        }
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Potongan berhasil di generate');
            redirect('potongan');
        } else {
            $this->session->set_flashdata('error', 'Potongan gagal di generate');
            redirect('potongan');
        }
    }

    public function detail($id)
    {
        $data['judul'] = 'Potongan';
        $data['sub'] = '';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db->query("SELECT potongan.*, guru.nama, SUM(potongan.nominal) as total FROM potongan JOIN guru  ON guru.guru_id=potongan.guru_id WHERE potongan_id = '$id' GROUP BY potongan.guru_id ")->result();
        $this->load->view('potongandtl', $data);
    }

    public function get_data()
    {
        $id = $this->input->post('id', 'true');
        $data = $this->model->getBy('potongan', 'id', $id)->row();
        $rinci = $this->model->getBy2('potongan', 'potongan_id', $data->potongan_id, 'guru_id', $data->guru_id)->result();

        $hasil = "<table class='table table-sm'>";
        foreach ($rinci as $value) {
            $hasil .= '<tr>';
            $hasil .= '<td>' . $value->ket . '</td>';
            $hasil .= '<td>' . rupiah($value->nominal) . '</td>';
            $hasil .= '</tr>';
        }
        $hasil .= '</table>';

        echo json_encode($hasil);
    }
}
