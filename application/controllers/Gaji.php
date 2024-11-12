<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gaji extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');

        // $user = $this->Auth_model->current_user();

        // $this->user = $user->nama;
        $this->tahun = '2024/2025';
        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }

    public function index()
    {
        $data['judul'] = 'Master Gaji';
        $data['user'] = $this->Auth_model->current_user();

        $data['gaji'] = $this->model->getOrder('gaji', 'created_at', 'DESC')->result();

        $this->load->view('gaji', $data);
    }
    public function detail($id)
    {
        $data['judul'] = 'Master Gaji';
        $data['user'] = $this->Auth_model->current_user();
        $data['idgaji'] = $id;
        // $data['gaji_list'] = [];

        $data['datagaji'] = $this->model->getBy('gaji', 'gaji_id', $id)->row();

        // echo var_dump($cek);
        $this->load->view('gajidetail', $data);
    }

    public function tambah()
    {
        $id = $this->uuid->v4();
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $this->model->tambah('gaji', ['gaji_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun, 'tapel' => $this->tahun, 'created_at' => date('Y-m-d H:i:s')]);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Gaji berhasil ditambahkan');
            redirect('gaji');
        } else {
            $this->session->set_flashdata('error', 'Gaji gagal ditambahkan');
            redirect('gaji');
        }
    }

    public function generate($id)
    {
        $cek = $this->model->getData('gaji', 'gaji_id', $id)->row();
        if ($cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Data gaji sudah terkunci');
            redirect('gaji');
        }
        $gajidata = $this->model->getBy('gaji_detail', 'gaji_id', $id)->row();
        if ($gajidata) {
            $this->session->set_flashdata('error', 'Gaji sudah digenerate');
            redirect('gaji');
        } else {
            $guru = $this->db->query("SELECT guru.guru_id, guru.nama, guru.sik, guru.tmt, satminkal.nama as satminkal, jabatan.nama as jabatan, ijazah.nama as ijazah, golongan.nama as golongan FROM guru
        LEFT JOIN satminkal ON guru.satminkal=satminkal.id
        LEFT JOIN jabatan ON guru.jabatan=jabatan.jabatan_id
        LEFT JOIN ijazah ON guru.ijazah=ijazah.id
        LEFT JOIN golongan ON guru.golongan=golongan.id
        ")->result();
            foreach ($guru as $guruhasil) {
                $data = [
                    'guru_id' => $guruhasil->guru_id,
                    'nama' => $guruhasil->nama,
                    'sik' => $guruhasil->sik,
                    'tmt' => $guruhasil->tmt,
                    'satminkal' => $guruhasil->satminkal,
                    'jabatan' => $guruhasil->jabatan,
                    'ijazah' => $guruhasil->ijazah,
                    'golongan' => $guruhasil->golongan,
                    'gaji_id' => $id,
                ];
                $this->model->tambah('gaji_detail', $data);
            }
            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'Gaji berhasil di generate');
                redirect('gaji/detail/' . $id);
            } else {
                $this->session->set_flashdata('error', 'Gaji gagal di generate');
                redirect('gaji/detail/' . $id);
            }
        }
    }
    public function regenerate($id)
    {
        $cek = $this->model->getData('gaji', 'gaji_id', $id)->row();
        if ($cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Data gaji sudah terkunci');
            redirect('gaji/detail/' . $id);
        }
        $this->model->hapus('gaji_detail', 'gaji_id', $id);
        $guru = $this->db->query("SELECT guru.guru_id, guru.nama, guru.sik, guru.tmt, satminkal.nama as satminkal, jabatan.nama as jabatan, ijazah.nama as ijazah, golongan.nama as golongan FROM guru
        LEFT JOIN satminkal ON guru.satminkal=satminkal.id
        LEFT JOIN jabatan ON guru.jabatan=jabatan.jabatan_id
        LEFT JOIN ijazah ON guru.ijazah=ijazah.id
        LEFT JOIN golongan ON guru.golongan=golongan.id
        ")->result();
        foreach ($guru as $guruhasil) {
            $data = [
                'guru_id' => $guruhasil->guru_id,
                'nama' => $guruhasil->nama,
                'sik' => $guruhasil->sik,
                'tmt' => $guruhasil->tmt,
                'satminkal' => $guruhasil->satminkal,
                'jabatan' => $guruhasil->jabatan,
                'ijazah' => $guruhasil->ijazah,
                'golongan' => $guruhasil->golongan,
                'gaji_id' => $id,
            ];
            $this->model->tambah('gaji_detail', $data);
        }
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Gaji berhasil di generate');
            redirect('gaji/detail/' . $id);
        } else {
            $this->session->set_flashdata('error', 'Gaji gagal di generate');
            redirect('gaji/detail/' . $id);
        }
    }

    public function detail2($id)
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search_value = isset($this->input->post('search')['value']) ? $this->input->post('search')['value'] : '';

        $length = $length > 0 ? $length : 10;
        $start = $start >= 0 ? $start : 0;
        // $bulanIni = date('m');

        $this->db->from('gaji_detail');
        $this->db->where('gaji_id', $id);

        // Filter search
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('nama', $search_value);
            $this->db->or_like('satminkal', $search_value);
            $this->db->group_end();
        }

        $total_records = $this->db->count_all_results('', false); // Count total records without limit

        $this->db->limit($length, $start);
        $query = $this->db->get();
        $data = [];
        $row_number = $start + 1;

        foreach ($query->result() as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            $struktural = $this->model->getBy3('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
            $payments = array_column($cek, 'payment');

            $data[] = [
                $row_number++, // 0
                $row->gaji_id,  // 1
                $row->nama, // 2
                $row->satminkal, // 3
                $row->jabatan, // 4 
                $row->golongan, // 5
                $row->sik, // 6
                $row->ijazah, // 7
                $row->tmt, // 8
                $gapok && in_array('gapok', $payments) ? $gapok->nominal : 0, // 9
                $fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0, // 10
                $kinerja && in_array('kinerja', $payments) ? $kinerja->nominal : 0, // 11
                $struktural && in_array('struktural', $payments) ? $struktural->nominal : 0, // 12
                $bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0, // 13
                $walas && in_array('walas', $payments) ? $walas->nominal : 0, // 14
                $penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0, // 15
                (
                    ($gapok && in_array('gapok', $payments) ? $gapok->nominal : 0) +
                    ($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0) +
                    ($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal : 0) +
                    ($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0) +
                    ($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0) +
                    ($walas && in_array('walas', $payments) ? $walas->nominal : 0) +
                    ($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0)
                ) // 16
            ];
        }

        $output = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        ];

        // Set content-type header and return JSON data
        header('Content-Type: application/json');
        echo json_encode($output);
        // var_dump($output);
    }
    public function detail3($id)
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search_value = isset($this->input->post('search')['value']) ? $this->input->post('search')['value'] : '';

        $length = $length > 0 ? $length : 10;
        $start = $start >= 0 ? $start : 0;
        // $bulanIni = date('m');

        $this->db->from('gaji_detail');
        $this->db->where('gaji_id', $id);

        // Filter search
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('nama', $search_value);
            $this->db->or_like('satminkal', $search_value);
            $this->db->group_end();
        }

        $total_records = $this->db->count_all_results('', false); // Count total records without limit

        $this->db->limit($length, $start);
        $query = $this->db->get();
        $data = [];
        $row_number = $start + 1;

        foreach ($query->result() as $row) {

            $data[] = [
                $row_number++, // 0
                $row->gaji_id,  // 1
                $row->nama, // 2
                $row->satminkal, // 3
                $row->jabatan, // 4 
                $row->golongan, // 5
                $row->sik, // 6
                $row->ijazah, // 7
                $row->tmt, // 8
                $row->gapok, // 9
                $row->fungsional, // 10
                $row->kinerja, // 11
                $row->struktural, // 12
                $row->bpjs, // 13
                $row->walas, // 14
                $row->penyesuaian, // 15
                (
                    ($row->gapok) +
                    ($row->fungsional) +
                    ($row->kinerja) +
                    ($row->struktural) +
                    ($row->bpjs) +
                    ($row->walas) +
                    ($row->penyesuaian)
                ) // 16
            ];
        }

        $output = [
            "draw" => $draw,
            "recordsTotal" => $total_records,
            "recordsFiltered" => $total_records,
            "data" => $data
        ];

        // Set content-type header and return JSON data
        header('Content-Type: application/json');
        echo json_encode($output);
        // var_dump($output);
    }

    public  function kunci($id)
    {
        $cek = $this->model->getData('gaji', 'gaji_id', $id)->row();
        if ($cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Data gaji sudah terkunci');
            redirect('gaji/detail/' . $id);
        }
        $gajidata = $this->model->getBy('gaji_detail', 'gaji_id', $id);
        foreach ($gajidata->result() as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            $gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            $struktural = $this->model->getBy3('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
            $payments = array_column($cek, 'payment');

            $data = [
                'gapok' => $gapok && in_array('gapok', $payments) ? $gapok->nominal : 0, // 9
                'fungsional' => $fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0, // 10
                'kinerja' => $kinerja && in_array('kinerja', $payments) ? $kinerja->nominal : 0, // 11
                'struktural' => $struktural && in_array('struktural', $payments) ? $struktural->nominal : 0, // 12
                'bpjs' => $bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0, // 13
                'walas' => $walas && in_array('walas', $payments) ? $walas->nominal : 0, // 14
                'penyesuaian' => $penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0, // 15
            ];
            $this->model->edit('gaji_detail', 'id_detail', $row->id_detail, $data);
        }
        if ($this->db->affected_rows() > 0) {
            $this->model->edit('gaji', 'gaji_id', $id, ['status' => 'kunci']);
            $this->session->set_flashdata('ok', 'Data Gaji berhasil dikunci');
            redirect('gaji/detail/' . $id);
        } else {
            $this->session->set_flashdata('error', 'Data Gaji gagal dikunci');
            redirect('gaji/detail/' . $id);
        }
    }

    public function hapus($id)
    {
        $cek = $this->model->getData('gaji', 'gaji_id', $id)->row();
        if ($cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Data gaji sudah terkunci');
            redirect('gaji');
        } else {

            $this->model->hapus('gaji', 'gaji_id', $id);
            $this->model->hapus('gaji_detail', 'gaji_id', $id);

            if ($this->db->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'gaji berhasil dihapus');
                redirect('gaji');
            } else {
                $this->session->set_flashdata('error', 'gaji gagal dihapus');
                redirect('gaji');
            }
        }
    }
}
