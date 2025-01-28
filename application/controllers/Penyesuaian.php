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

        $this->honor_santri = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $this->honor_non = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
    }

    public function index()
    {
        $data['judul'] = 'Tunjangan Penyesuaian';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db->query("SELECT penyesuaian.*, guru.nama as nmguru, guru.sik as sik FROM penyesuaian JOIN guru ON guru.guru_id=penyesuaian.guru_id ")->result();

        $data['guruOpt'] = $this->model->getData('guru')->result();
        $this->load->view('penyesuaian', $data);
    }

    public function getGajis()
    {
        $guru_id = $this->input->post('id', true);
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $sik = $guru->sik;

        $hak = $this->db->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");
        // $honor = $this->db->query("SELECT * FROM honor ORDER BY created_at DESC LIMIT 1")->row();
        // $kehadiran = $this->db->query("SELECT * FROM kehadiran ORDER BY created_at DESC LIMIT 1")->row();
        $bulanini = date('m');
        $tahunini = date('Y');
        $sebelum = $this->model->getBy('perbandingan', 'guru_id', $guru_id)->row();

        $no = 1;
        $total = 0;
        $hasilhtml = "
        <table class='table table-borderless table-sm'>";

        foreach ($hak->result() as $hakhasil) {
            if ($hakhasil->payment == 'gapok') {
                if ($sik == 'PTY') {
                    $isi = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row('nominal');
                } else {
                    $isi = $this->db->query("SELECT 
                    CASE 
                        WHEN guru.santri = 'santri' THEN SUM(kehadiran) * $this->honor_santri
                        ELSE SUM(kehadiran) * $this->honor_non
                    END AS nominal
                    FROM honor JOIN guru ON guru.guru_id=honor.guru_id WHERE honor.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' GROUP BY honor.guru_id ")->row('nominal');
                }
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row('nominal');
            } elseif ($hakhasil->payment == 'kinerja') {
                $masa = selisihTahun($guru->tmt);
                $besaran = $this->db->query("SELECT nominal FROM kinerja WHERE masa_kerja = $masa ")->row('nominal');
                $hadir = $this->db->query("SELECT kehadiran FROM kehadiran JOIN guru ON guru.guru_id=kehadiran.guru_id WHERE kehadiran.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini'")->row('kehadiran');
                $isi = $besaran * $hadir;
            } elseif ($hakhasil->payment == 'struktural') {
                $isi = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row('nominal');
            } elseif ($hakhasil->payment == 'bpjs') {
                $isi = $this->model->getBy('bpjs', 'guru_id', $guru_id)->row('nominal');
            } elseif ($hakhasil->payment == 'walas') {
                $isi = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row('nominal');
            }
            $hasilhtml .= "
            <tr>
                <td>" . $no++ . '. ' . $hakhasil->adds . "</td>
                <td>: " . rupiah(isset($isi) ? $isi : 0) . "</td>
            </tr>";
            $total += isset($isi) ? $isi : 0;
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
            'sebelum' => $sebelum ? $sebelum->nominal : 0,
        ]);
    }

    public function showDetail()
    {
        $id = $this->input->post('idSend', true);
        $datas = $this->model->getBy('penyesuaian', 'penyesuaian_id', $id)->row();
        $guru_id =  $datas->guru_id;
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $hak = $this->db->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");
        $bulanini = date('m');
        $tahunini = date('Y');
        $no = 1;
        $total = 0;
        $hasilhtml = "
        <table class='table table-borderless table-sm'>";

        foreach ($hak->result() as $hakhasil) {
            if ($hakhasil->payment == 'gapok') {
                if ($guru->sik == 'PTY') {
                    $isi = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row('nominal');
                } else {
                    $isi = $this->db->query("SELECT 
                    CASE 
                        WHEN guru.santri = 'santri' THEN SUM(kehadiran) * $this->honor_santri
                        ELSE SUM(kehadiran) * $this->honor_non
                    END AS nominal
                    FROM honor JOIN guru ON guru.guru_id=honor.guru_id WHERE honor.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' ")->row('nominal');
                }
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row('nominal');
            } elseif ($hakhasil->payment == 'kinerja') {
                $masa = selisihTahun($guru->tmt);
                $besaran = $this->db->query("SELECT nominal FROM kinerja WHERE masa_kerja = $masa ")->row('nominal');
                $hadir = $this->db->query("SELECT kehadiran FROM kehadiran JOIN guru ON guru.guru_id=kehadiran.guru_id WHERE kehadiran.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini'")->row('kehadiran');
                $isi = $besaran * $hadir;
            } elseif ($hakhasil->payment == 'struktural') {
                $isi = $this->model->getBy3('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal, 'masa_kerja', selisihTahun($guru->tmt))->row('nominal');
            } elseif ($hakhasil->payment == 'bpjs') {
                $isi = $this->model->getBy('bpjs', 'guru_id', $guru_id)->row('nominal');
            } elseif ($hakhasil->payment == 'walas') {
                $isi = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row('nominal');
            }
            $hasilhtml .= "
            <tr>
                <td>" . $no++ . '. ' . $hakhasil->adds . "</td>
                <td>: " . rupiah(isset($isi) ? $isi : 0) . "</td>
            </tr>";
            $total += isset($isi) ? $isi : 0;
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
