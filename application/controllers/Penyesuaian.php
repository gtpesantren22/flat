<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penyesuaian extends MY_Controller
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
        $this->honor_rami = $this->model->getBy('settings', 'nama', 'honor_rami')->row('isi');

        $this->pengabdian = $this->model->getBy('settings', 'nama', 'pengabdian')->row('isi');
        $ijazah = $this->model->getBy('settings', 'nama', 'ijazah')->row('isi');
        $this->minimum = explode(',', $ijazah);
        $str = $this->model->getBy('settings', 'nama', 'struktural')->row('isi');
        $this->struktural = explode(',', $str);
    }

    public function index()
    {
        $data['judul'] = 'Tunjangan Penyesuaian';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Penyesuaian');

        $dataGru = $this->db_active->query("SELECT * FROM guru WHERE sik = 'PTY' ")->result();
        $gajis = $this->model->getBy2('gaji', 'bulan', date('m'), 'tahun', date('Y'))->row();
        $dataKirim = [];
        foreach ($dataGru as $row) {
            $kehadiran = $this->model->getBy3('kehadiran', 'guru_id', $row->guru_id, 'bulan', $gajis->bulan, 'tahun', $gajis->tahun)->row();
            $perbandingan = $this->model->getBy('perbandingan', 'guru_id', $row->guru_id)->row();

            if ($row->sik === 'PTY') {
                $gapok = $this->model->getBy2('gapok', 'golongan_id', $row->golongan, 'masa_kerja', selisihTahun($row->tmt))->row();
                $gapok = $gapok &&  !in_array($row->jabatan, $this->struktural) ? $gapok->nominal : 0;
            } else {
                $gapok1 = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$row->guru_id' AND bulan = $gajis->bulan AND tahun = '$gajis->tahun' GROUP BY honor.guru_id")->row();
                $gapok = $gapok1 &&  !in_array($row->jabatan, $this->struktural) && $row->kriteria != 'Karyawan' ? $gapok1->nominal : 0;
            }

            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $row->golongan, 'kategori', $row->kategori)->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($row->tmt))->row();
            if ($row->kriteria == 'Pengabdian') {
                $struktural = $this->pengabdian;
            } else {
                $struktural = $this->model->getBy2('struktural', 'jabatan_id', $row->jabatan, 'satminkal_id', $row->satminkal)->row('nominal');
            }
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $row->guru_id)->row();
            $walas = $this->model->getBy('walas', 'guru_id', $row->guru_id)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $row->guru_id)->row();
            $tambahan = $this->db_active->query("SELECT SUM(tambahan.nominal*tambahan_detail.jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$row->guru_id' AND gaji_id = '$gajis->gaji_id' ")->row();

            $totalFlat =
                ($gapok) +
                ($fungsional && $row->kriteria == 'Guru' && $row->sik == 'PTY' && in_array($row->ijazah, $this->minimum) ? $fungsional->nominal : 0) +
                ($kinerja && $row->kriteria == 'Karyawan' &&  !in_array($row->jabatan, $this->struktural) ? $kinerja->nominal * ($kehadiran ? $kehadiran->kehadiran : 0) : 0) +
                ($struktural ? $struktural : 0) +
                ($bpjs ? $bpjs->nominal : 0) +
                ($walas && !$struktural ? $walas->nominal : 0) +
                ($penyesuaian && $row->kriteria != 'Pengabdian' &&  !in_array($row->jabatan, $this->struktural) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) +
                $tambahan->total;

            $dataKirim[] = [
                'guru_id' => $row->guru_id,
                'nama' => $row->nama,
                'sik' => $row->sik,
                'sebelum' => $perbandingan ? $perbandingan->nominal : 0,
                'sesudah' => $totalFlat,
            ];
        }

        $data['data'] = $dataKirim;

        $this->load->view('penyesuaian', $data);
    }
    public function sesuaikan()
    {
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Sesuaikan data. C: Penyesuaian');

        $dataGru = $this->db_active->query("SELECT * FROM guru WHERE sik = 'PTY' ")->result();
        $gajis = $this->model->getBy2('gaji', 'bulan', date('m'), 'tahun', date('Y'))->row();

        foreach ($dataGru as $row) {
            $kehadiran = $this->model->getBy3('kehadiran', 'guru_id', $row->guru_id, 'bulan', $gajis->bulan, 'tahun', $gajis->tahun)->row();
            $perbandingan = $this->model->getBy('perbandingan', 'guru_id', $row->guru_id)->row();

            if ($row->sik === 'PTY') {
                $gapok = $this->model->getBy2('gapok', 'golongan_id', $row->golongan, 'masa_kerja', selisihTahun($row->tmt))->row();
                $gapok = $gapok &&  !in_array($row->jabatan, $this->struktural) ? $gapok->nominal : 0;
            } else {
                $gapok1 = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$row->guru_id' AND bulan = $gajis->bulan AND tahun = '$gajis->tahun' GROUP BY honor.guru_id")->row();
                $gapok = $gapok1 &&  !in_array($row->jabatan, $this->struktural) && $row->kriteria != 'Karyawan' ? $gapok1->nominal : 0;
            }

            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $row->golongan, 'kategori', $row->kategori)->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($row->tmt))->row();
            if ($row->kriteria == 'Pengabdian') {
                $struktural = $this->pengabdian;
            } else {
                $struktural = $this->model->getBy2('struktural', 'jabatan_id', $row->jabatan, 'satminkal_id', $row->satminkal)->row('nominal');
            }
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $row->guru_id)->row();
            $walas = $this->model->getBy('walas', 'guru_id', $row->guru_id)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $row->guru_id)->row();
            $tambahan = $this->db_active->query("SELECT SUM(tambahan.nominal*tambahan_detail.jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$row->guru_id' AND gaji_id = '$gajis->gaji_id' ")->row();

            $totalFlat =
                ($gapok) +
                ($fungsional && $row->kriteria == 'Guru' && $row->sik == 'PTY' && in_array($row->ijazah, $this->minimum) ? $fungsional->nominal : 0) +
                ($kinerja && $row->kriteria == 'Karyawan' &&  !in_array($row->jabatan, $this->struktural) ? $kinerja->nominal * ($kehadiran ? $kehadiran->kehadiran : 0) : 0) +
                ($struktural ? $struktural : 0) +
                ($bpjs ? $bpjs->nominal : 0) +
                ($walas && !$struktural ? $walas->nominal : 0) +
                ($penyesuaian && $row->kriteria != 'Pengabdian' &&  !in_array($row->jabatan, $this->struktural) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0) +
                $tambahan->total;

            if ($totalFlat < $perbandingan->nominal) {
                $dataSave = [
                    'guru_id' => $row->guru_id,
                    'sebelum' => $perbandingan->nominal,
                    'sesudah' => $totalFlat,
                ];
                $this->model->tambah('penyesuaian', $dataSave);
            }
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Proses penyesuaian berhasil');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'Proses penyesuaian gagal');
            redirect('penyesuaian');
        }
    }

    public function getGajis()
    {
        $guru_id = $this->input->post('id', true);
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $sik = $guru->sik;
        $this->Auth_model->log_activity($this->userID, 'Akses getGajis C: Penyesuaian');

        $hak = $this->db_active->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");

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
                    // $query = $this->db_active->query("SELECT honor.*, guru.santri AS santri
                    // CASE 
                    //     WHEN guru.santri = 'santri' THEN SUM(kehadiran) * $this->honor_santri
                    //     ELSE SUM(kehadiran) * $this->honor_non
                    // END AS nominal
                    // FROM honor JOIN guru ON guru.guru_id=honor.guru_id WHERE honor.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' GROUP BY honor.guru_id ")->row();
                    // if ($query->lembaga == 8 || $query->lembaga == 9) {
                    //     $isi = $query->kehadiran * $this->honor_rami;
                    // } else {
                    //     $isi = $query->santri == 'santri' ? $query->kehadiran * $this->honor_santri : $query->kehadiran * $this->honor_non;
                    // }
                    $isi = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' GROUP BY honor.guru_id")->row('nominal');
                }
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row('nominal');
            } elseif ($hakhasil->payment == 'kinerja') {
                $masa = selisihTahun($guru->tmt);
                $besaran = $this->db_active->query("SELECT nominal FROM kinerja WHERE masa_kerja = $masa ")->row('nominal');
                $hadir = $this->db_active->query("SELECT kehadiran FROM kehadiran JOIN guru ON guru.guru_id=kehadiran.guru_id WHERE kehadiran.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini'")->row('kehadiran');
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
        $this->Auth_model->log_activity($this->userID, 'Akses show details C: Penyesuaian');
        $id = $this->input->post('idSend', true);
        $datas = $this->model->getBy('penyesuaian', 'penyesuaian_id', $id)->row();
        $guru_id =  $datas->guru_id;
        $guru = $this->model->getBy('guru', 'guru_id', $guru_id)->row();
        $hak = $this->db_active->query("SELECT a.*, b.adds FROM hak_setting a JOIN sik_setting b ON a.payment=b.col WHERE guru_id = '$guru_id' and payment != 'penyesuaian'");
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
                    // $query = $this->db_active->query("SELECT honor.*, guru.santri AS santri
                    // CASE 
                    //     WHEN guru.santri = 'santri' THEN SUM(kehadiran) * $this->honor_santri
                    //     ELSE SUM(kehadiran) * $this->honor_non
                    // END AS nominal
                    // FROM honor JOIN guru ON guru.guru_id=honor.guru_id WHERE honor.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' GROUP BY honor.guru_id ")->row();
                    // if ($query->lembaga == 8 || $query->lembaga == 9) {
                    //     $isi = $query->kehadiran * $this->honor_rami;
                    // } else {
                    //     $isi = $query->santri == 'santri' ? $query->kehadiran * $this->honor_santri : $query->kehadiran * $this->honor_non;
                    // }
                    $isi = $this->db_active->query("SELECT SUM(nominal) AS nominal FROM honor WHERE guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini' GROUP BY honor.guru_id")->row('nominal');
                }
            } elseif ($hakhasil->payment == 'fungsional') {
                $isi = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row('nominal');
            } elseif ($hakhasil->payment == 'kinerja') {
                $masa = selisihTahun($guru->tmt);
                $besaran = $this->db_active->query("SELECT nominal FROM kinerja WHERE masa_kerja = $masa ")->row('nominal');
                $hadir = $this->db_active->query("SELECT kehadiran FROM kehadiran JOIN guru ON guru.guru_id=kehadiran.guru_id WHERE kehadiran.guru_id = '$guru->guru_id' AND bulan = $bulanini AND tahun = '$tahunini'")->row('kehadiran');
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
        $this->Auth_model->log_activity($this->userID, 'Akses tambah data C: Penyesuaian');
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
            if ($this->db_active->affected_rows() > 0) {
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
        $this->Auth_model->log_activity($this->userID, 'Akses hapus data C: Penyesuaian');
        $this->model->hapus('penyesuaian', 'penyesuaian_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'penyesuaian berhasil dihapus');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'penyesuaian gagal dihapus');
            redirect('penyesuaian');
        }
    }

    public function edit()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses edit data C: Penyesuaian');
        $id = $this->input->post('id', true);
        $data = [
            'sebelum' => rmRp($this->input->post('sebelum', true)),
            'sesudah' => rmRp($this->input->post('sesudah', true)),
        ];

        $this->model->edit('penyesuaian', 'penyesuaian_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'penyesuaian berhasil diupdate');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'penyesuaian gagal diupdate');
            redirect('penyesuaian');
        }
    }
    public function reset()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses Reset data C: Penyesuaian');

        $dataGuru = $this->model->getBy('guru', 'sik', 'PTY')->result();
        foreach ($dataGuru as $row) {
            $this->model->hapus('penyesuaian', 'guru_id', $row->guru_id);
        }
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Reset data berhasil');
            redirect('penyesuaian');
        } else {
            $this->session->set_flashdata('error', 'Reset data gagal');
            redirect('penyesuaian');
        }
    }
}
