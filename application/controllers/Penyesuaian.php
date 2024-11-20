<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penyesuaian extends CI_Controller
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
        $data['judul'] = 'Tunjangan Penyesuaian';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db->query("SELECT penyesuaian.*, guru.nama as nmguru FROM penyesuaian JOIN guru ON guru.guru_id=penyesuaian.guru_id ")->result();

        $data['guruOpt'] = $this->model->getData('guru')->result();
        $this->load->view('penyesuaian', $data);
    }

    public function getGajis()
    {
        $guru_id = $this->input->post('id', true);
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $sik = $guru->sik;

        $hak = $this->db->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");
        $honor = $this->db->query("SELECT * FROM honor ORDER BY created_at DESC LIMIT 1")->row();

        $no = 1;
        $total = 0;
        $hasilhtml = "
        <table class='table table-borderless table-sm'>";

        foreach ($hak->result() as $hakhasil) {
            if ($hakhasil->payment == 'gapok') {
                if ($sik == 'PTY') {
                    $isi = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                } else {
                    $isi = $this->db->query("SELECT 
                    CASE 
                        WHEN guru.santri = 'santri' THEN kehadiran * $this->honor_santri
                        ELSE kehadiran * $this->honor_non
                    END AS nominal
                    FROM honor JOIN guru ON guru.guru_id=honor.guru_id WHERE honor.guru_id = '$guru->guru_id' AND bulan = $honor->bulan AND tahun = '$honor->tahun'")->row();
                }
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
            } elseif ($hakhasil->payment == 'kinerja') {
                $masa = selisihTahun($guru->tmt);
                $isi = $this->db->query("SELECT nominal * $this->jamkinerja as nominal FROM kinerja WHERE masa_kerja = $masa ")->row();
            } elseif ($hakhasil->payment == 'struktural') {
                $isi = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
            } elseif ($hakhasil->payment == 'bpjs') {
                $isi = $this->model->getBy('bpjs', 'guru_id', $guru_id)->row();
            } elseif ($hakhasil->payment == 'walas') {
                $isi = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            }
            $hasilhtml .= "
            <tr>
                <td>" . $no++ . '. ' . $hakhasil->adds . "</td>
                <td>: " . rupiah(isset($isi) ? $isi->nominal : 0) . "</td>
            </tr>";
            $total += isset($isi) ? $isi->nominal : 0;
        }
        $hasilhtml .= "
            <tr>
                <th>Nominal Flat</th>
                <th>: " . rupiah($total) . "</th>
            </tr>
        </table>
        ";

        echo json_encode([
            'hasil' => $hasilhtml,
            'total' => $total
        ]);
    }

    public function showDetail()
    {
        $id = $this->input->post('idSend', true);
        $datas = $this->model->getBy('penyesuaian', 'penyesuaian_id', $id)->row();
        $guru_id =  $datas->guru_id;
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $hak = $this->db->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");

        $no = 1;
        $total = 0;
        $hasilhtml = "
        <table class='table table-borderless table-sm'>";

        foreach ($hak->result() as $hakhasil) {
            if ($hakhasil->payment == 'gapok') {
                $isi = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            } elseif ($hakhasil->payment == 'kinerja') {
                $isi = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            } elseif ($hakhasil->payment == 'struktural') {
                $isi = $this->model->getBy3('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal, 'masa_kerja', selisihTahun($guru->tmt))->row();
            } elseif ($hakhasil->payment == 'bpjs') {
                $isi = $this->model->getBy('bpjs', 'guru_id', $guru_id)->row();
            } elseif ($hakhasil->payment == 'walas') {
                $isi = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            }
            $hasilhtml .= "
            <tr>
                <td>" . $no++ . '. ' . $hakhasil->adds . "</td>
                <td>: " . rupiah(isset($isi) ? $isi->nominal : 0) . "</td>
            </tr>";
            $total += isset($isi) ? $isi->nominal : 0;
        }
        $hasilhtml .= "
            <tr>
                <th>Nominal Flat</th>
                <th>: " . rupiah($total) . "</th>
            </tr>
        </table>
        ";

        echo json_encode([
            'hasil' => $hasilhtml,
            'total' => $total,
            'nama' => $guru->nama,
            'sebelum' => $datas->sebelum,
            // 'hasil' => 'Output : ' . $gpok
        ]);
    }

    public function tambah()
    {
        $cek = $this->model->getBy('penyesuaian', 'guru_id', $this->input->post('guru', true))->row();
        $data = [
            'guru_id' => $this->input->post('guru', true),
            'sebelum' => rmRp($this->input->post('sebelum', true)),
            'sesudah' => rmRp($this->input->post('sesudah', true)),
        ];
        if ($cek) {
            $this->session->set_flashdata('error', 'Data sudah ada!');
            redirect('penyesuaian');
        } else {
            $this->model->tambah('penyesuaian', $data);
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'penyesuaian berhasil ditambahkan');
                redirect('penyesuaian');
            } else {
                $this->session->set_flashdata('error', 'penyesuaian gagal ditambahkan');
                redirect('penyesuaian');
            }
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('penyesuaian', 'penyesuaian_id', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'penyesuaian berhasil dihapus');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'penyesuaian gagal dihapus');
            redirect('penyesuaian');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'sebelum' => rmRp($this->input->post('sebelum', true)),
            'sesudah' => rmRp($this->input->post('sesudah', true)),
        ];

        $this->model->edit('penyesuaian', 'penyesuaian_id', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'penyesuaian berhasil diupdate');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'penyesuaian gagal diupdate');
            redirect('penyesuaian');
        }
    }
}
