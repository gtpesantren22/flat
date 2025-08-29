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

        $this->honor_santri = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $this->honor_non = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
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

        $jenispotongan =  $this->db->query("SELECT * FROM potongan WHERE potongan_id = '$id' AND ket IS NOT NULL AND ket != '' GROUP BY ket ")->result();
        $datapotongan = [];
        foreach ($jenispotongan as $jenispotonganhasil) {
            $nomPotongan = $this->db->query("SELECT SUM(nominal) AS nominal FROM potongan WHERE potongan_id = '$id' AND ket = '$jenispotonganhasil->ket' ")->row();
            $datapotongan[] = [
                'ket' => $jenispotonganhasil->ket,
                'nominal' => $nomPotongan ? $nomPotongan->nominal : 0,
                'bulan' => $jenispotonganhasil->bulan,
                'tahun' => $jenispotonganhasil->tahun
            ];
        }
        $data['datapotongan'] = $datapotongan;
        $data['id'] = $id;
        $this->load->view('potongandtl', $data);
    }

    public function get_data()
    {
        $id = $this->input->post('id', true);
        $data = $this->model->getBy('potongan', 'id', $id)->row();
        $jml = $this->model->getBy2('potongan', 'guru_id', $data->guru_id, 'potongan_id', $data->potongan_id)->num_rows();
        if ($jml < 2) {
            $jenis = ['Tabungan Wajib', 'SIMPOK', 'SIMWA', 'Koperasi/Cicilan', 'BPJS', 'Insijam', 'Infaq TPP', 'Pulsa', 'Verval TPP', 'Verval SIMPATIKA', 'Pinjaman Bank'];
            foreach ($jenis as $jns) {
                $simpandata = [
                    'potongan_id' => $data->potongan_id,
                    'guru_id' => $data->guru_id,
                    'bulan' => $data->bulan,
                    'tahun' => $data->tahun,
                    'ket' => $jns,
                    'nominal' => 0,
                ];
                $simpan = $this->model->tambah('potongan', $simpandata);
            }
            if ($simpan > 0) {
                $this->model->edit('potongan', 'id', $id, ['ket' => 'Lain-lain']);
                $hasil = $this->model->getBy2('potongan', 'guru_id', $data->guru_id, 'potongan_id', $data->potongan_id)->result();
                echo json_encode(['status' => 'success', 'data' => $hasil]);
            } else {
                echo '<b>Gagal ambil data</b>';
            }
        } else {
            $hasil = $this->model->getBy2('potongan', 'guru_id', $data->guru_id, 'potongan_id', $data->potongan_id)->result();
            echo json_encode(['status' => 'success', 'data' => $hasil]);
        }
    }

    public function updatePotongan()
    {
        $id = $this->input->post('id', true);
        $value = $this->input->post('value', true);
        $varname = $this->input->post('inputName', true);
        if ($varname == 'nominal') {
            $valueOk = rmRp($value);
        } else {
            $valueOk = $value;
        }


        $this->model->edit('potongan', 'id', $id, [$varname => $valueOk]);
        if ($this->db->affected_rows() > 0) {
            $data = $this->model->getBy('potongan', 'id', $id)->row();
            $hasil = $this->model->getBy2('potongan', 'guru_id', $data->guru_id, 'potongan_id', $data->potongan_id)->result();
            echo json_encode(['status' => 'success', 'data' => $hasil]);
        } else {
            echo json_encode(['status' => 'gagal']);
        }
        // echo json_encode(['status' => 'success', 'hasil' => $id]);
    }

    public function del_row()
    {
        $id = $this->input->post('id', true);
        $data = $this->model->getBy('potongan', 'id', $id)->row();

        $this->model->hapus('potongan', 'id', $id);
        if ($this->db->affected_rows() > 0) {
            $hasil = $this->model->getBy2('potongan', 'guru_id', $data->guru_id, 'potongan_id', $data->potongan_id)->result();
            echo json_encode(['status' => 'success', 'data' => $hasil]);
        } else {
            echo json_encode(['status' => 'gagal']);
        }
    }

    public function refresh($id)
    {
        $potongan = $this->model->getBy('potongan', 'potongan_id', $id)->row();

        $guru = $this->db->query("SELECT * FROM guru WHERE NOT EXISTS (SELECT 1 FROM potongan WHERE potongan_id = '$id' AND potongan.guru_id = guru.guru_id) ");
        if ($guru->row()) {
            foreach ($guru->result() as $value) {
                $data = [
                    'guru_id' => $value->guru_id,
                    'potongan_id' => $potongan->potongan_id,
                    'bulan' => $potongan->bulan,
                    'tahun' => $potongan->tahun,
                    'ket' => '',
                    'nominal' => 0,
                ];
                $this->model->tambah('potongan', $data);
            }
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Data potongan diperbarui');
                redirect('potongan/detail/' . $id);
            } else {
                $this->session->set_flashdata('error', 'Data potongan gagal');
                redirect('potongan/detail/' . $id);
            }
        } else {
            $this->session->set_flashdata('ok', 'Data potongan diperbarui');
            redirect('potongan/detail/' . $id);
        }
    }
}
