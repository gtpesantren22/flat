<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perbandingan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');
        $this->load->model('Gajimodel', 'm_gaji');

        $this->db_utama = $this->load->database('utama', TRUE);

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
        // $gaji = $this->model->getBy2('gaji', 'tahun', $tahun, 'bulan', $bulan)->row();
        $gaji = $this->model->query("SELECT * FROM gaji ORDER BY tahun DESC, bulan DESC LIMIT 1 ")->row();
        $bulan = $gaji->bulan;
        $tahun = $gaji->tahun;

        $dataguru = $this->db_active->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id ")->result();
        $kirim = [];
        foreach ($dataguru as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
            $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();

            $jabatan_old = $this->db_utama
                ->select('j.*')
                ->from('guru g')
                ->join('jabatan j', 'j.jabatan_id = g.jabatan', 'left')
                ->where('g.guru_id', $guru->guru_id_old)
                ->get()
                ->row();

            // Gaji data
            $gapok = $this->m_gaji->gapok($guru->guru_id, $guru->kriteria, $guru->sik, $guru->golongan, $guru->tmt, $guru->jabatan, $bulan, $tahun);
            $fungsional = $this->m_gaji->fungsional($guru->golongan, $guru->kriteria, $guru->sik, $guru->ijazah);
            $kinerja = $this->m_gaji->kinerja($guru->guru_id, $bulan, $tahun, $guru->tmt,  $guru->kriteria,  $guru->jabatan);
            $struktural = $this->m_gaji->struktural($guru->kriteria, $guru->jabatan, $guru->satminkal);
            $bpjs = $this->m_gaji->bpjs($guru->guru_id);
            $penyesuaian = $this->m_gaji->penyesuaian($guru->guru_id,  $guru->kriteria,  $guru->jabatan, $guru->sik);
            $tambahan = $this->m_gaji->tambahan($row->guru_id, $gaji->gaji_id);

            $totalGaji = $gapok + $fungsional + $kinerja + $struktural + $bpjs + $penyesuaian + $tambahan;
            $masaKerja = selisihTahun($guru->tmt);
            if ($masaKerja < 2 && $guru->sik === 'PTY') {
                $totalGaji = $totalGaji * 0.8;
            }
            $kirim[] = [
                'id' =>  $row->id,
                'nama' =>  $guru->nama,
                'guru_id' =>  $guru->guru_id,
                'sik' =>  $guru->sik,
                'lembaga' =>  $satminkal,
                'jabatan' =>  $jabatan ? $jabatan->nama : '',
                'jabatan_old' =>  $jabatan_old ? $jabatan_old->nama : '',
                'sebelum' =>  $row->nominal,
                'total' => $totalGaji,
                'penyesuaian' => $penyesuaian,
            ];
        }
        $data['hasil'] = $kirim;

        $this->load->view('perbandingan', $data);
    }

    public function detail()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses detail C: Perbandingan');
        $id = $this->input->post('id', 'true');
        $data = $this->model->getBy('perbandingan', 'id', $id)->row();

        $gaji = $this->model->query("SELECT * FROM gaji ORDER BY tahun DESC, bulan DESC LIMIT 1 ")->row();
        $bulan = $gaji->bulan;
        $tahun = $gaji->tahun;

        $dataguru = $this->db_active->query("SELECT a.* FROM perbandingan a JOIN guru b ON a.guru_id=b.guru_id WHERE a.guru_id = '$data->guru_id' ")->row();

        $row = $dataguru;
        $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
        $satminkal = $this->model->getBy('satminkal', 'id', $guru->satminkal)->row();
        $jabatan = $this->model->getBy('jabatan', 'jabatan_id', $guru->jabatan)->row();
        $golongan = $this->model->getBy('golongan', 'id', $guru->golongan)->row();
        $ijazah = $this->model->getBy('ijazah', 'id', $guru->ijazah)->row();

        $gapok = $this->m_gaji->gapok($guru->guru_id, $guru->kriteria, $guru->sik, $guru->golongan, $guru->tmt, $guru->jabatan, $bulan, $tahun);
        $fungsional = $this->m_gaji->fungsional($guru->golongan, $guru->kriteria, $guru->sik, $guru->ijazah);
        $kinerja = $this->m_gaji->kinerja($guru->guru_id, $bulan, $tahun, $guru->tmt,  $guru->kriteria,  $guru->jabatan);
        $struktural = $this->m_gaji->struktural($guru->kriteria, $guru->jabatan, $guru->satminkal);
        $bpjs = $this->m_gaji->bpjs($guru->guru_id);
        $penyesuaian = $this->m_gaji->penyesuaian($guru->guru_id,  $guru->kriteria,  $guru->jabatan, $guru->sik);
        $tambahan = $this->m_gaji->tambahan($row->guru_id, $gaji->gaji_id);

        echo json_encode([
            'id' =>  $row->id,
            'nama' =>  $guru->nama,
            'sik' =>  $guru->sik,
            'satminkal' =>  $satminkal ? $satminkal->nama : '',
            'jabatan' =>  $jabatan ? $jabatan->nama : '',
            'golongan' =>  $golongan ? $golongan->nama : '',
            'ijazah' =>  $ijazah ? $ijazah->nama : '',
            'tmt' =>  $guru->tmt,
            'masa' =>  selisihTahun($guru->tmt),
            'ket' =>  $guru->santri,
            'sebelum' =>  $row->nominal,
            'gapok' =>  $gapok, // 9
            'fungsional' => $fungsional, // 10
            'kinerja' => $kinerja, // 11
            'struktural' => $struktural, // 12
            'bpjs' => $bpjs, // 13
            'walas' =>  0, // 14
            'penyesuaian' => $penyesuaian, // 15
            'tambahan' => $tambahan // 16
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

    public function editPenyesuaian()
    {
        $id = $this->input->post('id', TRUE);
        $nominal = rmRp($this->input->post('value', TRUE));

        $cek = $this->model->getBy('penyesuaian', 'guru_id', $id)->row();
        if ($cek) {
            $this->model->edit('penyesuaian', 'guru_id', $id, ['nominal' => $nominal]);
            echo json_encode(['success' => true]);
        } else {
            $this->model->tambah('penyesuaian', ['guru_id' => $id, 'nominal' => $nominal]);
            echo json_encode(['success' => true]);
        }
    }
}
