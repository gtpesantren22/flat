<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $cek = $this->model->getBy('gaji_detail', 'gaji_id', $id)->row();
        if ($cek) {
            $data['datagaji'] = $this->model->getBy('gaji', 'gaji_id', $id)->row();
            $this->load->view('gajidetail', $data);
        } else {
            redirect('gaji/generate/' . $id);
        }
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
            $guru = $this->db->query("SELECT guru.guru_id, guru.nama, guru.sik, guru.santri, guru.tmt, satminkal.nama as satminkal, jabatan.nama as jabatan, ijazah.nama as ijazah, golongan.nama as golongan FROM guru
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
                    'santri' => $guruhasil->santri,
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
        $guru = $this->db->query("SELECT guru.guru_id, guru.nama, guru.sik, guru.santri, guru.tmt, satminkal.nama as satminkal, jabatan.nama as jabatan, ijazah.nama as ijazah, golongan.nama as golongan FROM guru
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
                'santri' => $guruhasil->santri,
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
        $gajis = $this->model->getBy('gaji', 'gaji_id', $id)->row();

        foreach ($query->result() as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();
            if ($guru->sik === 'PTY') {
                $gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok ? $gapok->nominal : 0;
            } else {
                $gapok = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', $gajis->bulan, 'tahun', $gajis->tahun)->row();
                $gapok = $gapok ? $gapok->kehadiran : 0;
                $gapok = $guru->santri == 'santri' ? $gapok * 6000 : $gapok * 12000;
            }

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
                in_array('gapok', $payments) ? $gapok : 0, // 9
                $fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0, // 10
                $kinerja && in_array('kinerja', $payments) ? $kinerja->nominal : 0, // 11
                $struktural && in_array('struktural', $payments) ? $struktural->nominal : 0, // 12
                $bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0, // 13
                $walas && in_array('walas', $payments) ? $walas->nominal : 0, // 14
                $penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0, // 15
                (
                    (in_array('gapok', $payments) ? $gapok : 0) +
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
        $blnpak = $cek->bulan;
        $thnpak = $cek->tahun;
        if ($cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Data gaji sudah terkunci');
            redirect('gaji/detail/' . $id);
        }
        $gajidata = $this->model->getBy('gaji_detail', 'gaji_id', $id);
        foreach ($gajidata->result() as $row) {
            $guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();

            if ($guru->sik === 'PTY') {
                $gapok1 = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
                $gapok = $gapok1 ? $gapok1->nominal : 0;
            } else {
                $gapok1 = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', $blnpak, 'tahun', $thnpak)->row();
                $gapok2 = $gapok1 ? $gapok1->kehadiran : 0;
                $gapok = $guru->santri == 'santri' ? $gapok2 * 6000 : $gapok2 * 12000;
            }

            $fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
            $struktural = $this->model->getBy3('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal, 'masa_kerja', selisihTahun($guru->tmt))->row();
            $bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
            $walas = $this->model->getBy('walas', 'satminkal_id', $guru->satminkal)->row();
            $penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();
            $cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
            $payments = array_column($cek, 'payment');

            $data = [
                'gapok' =>  in_array('gapok', $payments) ? $gapok : 0, // 9
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

    public function exportGaji($id)
    {
        $datagaji =  $this->model->getBy('gaji', 'gaji_id', $id)->row();
        $spreadsheet = new Spreadsheet();

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        $jmlsatminkal = $this->db->query("SELECT * FROM gaji_detail WHERE gaji_id = '$id' GROUP BY satminkal")->result();
        $sheetIndex = 0;
        foreach ($jmlsatminkal as $satminkal) {
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet = $spreadsheet->getActiveSheet();
            $datagaji2 =  $this->db->query("SELECT * FROM gaji_detail WHERE gaji_id = '$id' AND satminkal = '$satminkal->satminkal' ORDER BY nama ASC ")->result();

            $sheet->setCellValue('A1', "DAFTAR GAJI GURU & KARYAWAN"); // Set kolom A1 dengan tulisan "DATA SISWA"
            $sheet->mergeCells('A1:T1'); // Set Merge Cell pada kolom A1 sampai E1

            $sheet->setCellValue('A2', "PONDOK PESANTREN DARUL LUGHAH WAL KAROMAH"); // Set kolom A1 dengan tulisan "DATA SISWA"
            $sheet->mergeCells('A2:T2'); // Set Merge Cell pada kolom A1 sampai E1

            $sheet->setCellValue('A3', ""); // Set kolom A1 dengan tulisan "DATA SISWA"
            $sheet->mergeCells('A3:T3'); // Set Merge Cell pada kolom A1 sampai E1

            $sheet->setCellValue('M4', "GAJI/HONOR"); // Set kolom A1 dengan tulisan "DATA SISWA"
            $sheet->mergeCells('M4:S4'); // Set Merge Cell pada kolom A1 sampai E1
            $sheet->getStyle('M4:S4')->applyFromArray($style_col);

            $spreadsheet->getActiveSheet()->getStyle('A4:T4')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F7EF00');
            $spreadsheet->getActiveSheet()->getStyle('A5:T5')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F7EF00');

            $sheet->mergeCells('A4:A5');
            $sheet->mergeCells('B4:B5');
            $sheet->mergeCells('C4:C5');
            $sheet->mergeCells('D4:D5');
            $sheet->mergeCells('E4:E5');
            $sheet->mergeCells('F4:F5');
            $sheet->mergeCells('G4:G5');
            $sheet->mergeCells('H4:H5');
            $sheet->mergeCells('I4:I5');
            $sheet->mergeCells('J4:J5');
            $sheet->mergeCells('K4:K5');
            $sheet->mergeCells('L4:L5');

            $sheet->getStyle('A4:A5')->applyFromArray($style_col);
            $sheet->getStyle('B4:B5')->applyFromArray($style_col);
            $sheet->getStyle('C4:C5')->applyFromArray($style_col);
            $sheet->getStyle('D4:D5')->applyFromArray($style_col);
            $sheet->getStyle('E4:E5')->applyFromArray($style_col);
            $sheet->getStyle('F4:F5')->applyFromArray($style_col);
            $sheet->getStyle('G4:G5')->applyFromArray($style_col);
            $sheet->getStyle('H4:H5')->applyFromArray($style_col);
            $sheet->getStyle('I4:I5')->applyFromArray($style_col);
            $sheet->getStyle('J4:J5')->applyFromArray($style_col);
            $sheet->getStyle('K4:K5')->applyFromArray($style_col);
            $sheet->getStyle('L4:L5')->applyFromArray($style_col);
            $sheet->getStyle('M4:M5')->applyFromArray($style_col);
            $sheet->getStyle('N4:N5')->applyFromArray($style_col);
            $sheet->getStyle('O4:O5')->applyFromArray($style_col);
            $sheet->getStyle('P4:P5')->applyFromArray($style_col);
            $sheet->getStyle('Q4:Q5')->applyFromArray($style_col);
            $sheet->getStyle('R4:R5')->applyFromArray($style_col);
            $sheet->getStyle('S4:S5')->applyFromArray($style_col);
            $sheet->getStyle('T4:T5')->applyFromArray($style_col);

            // Buat header tabel nya pada baris ke 3
            $sheet->setCellValue('A4', "NO");
            $sheet->setCellValue('B4', "BULAN");
            $sheet->setCellValue('C4', "TAHUN");
            $sheet->setCellValue('D4', "NAMA GURU/KARYAWAN");
            $sheet->setCellValue('E4', "SATMINKAL");
            $sheet->setCellValue('F4', "JABATAN");
            $sheet->setCellValue('G4', "GOLONGAN");
            $sheet->setCellValue('H4', "SIK");
            $sheet->setCellValue('I4', "IJAZAH");
            $sheet->setCellValue('J4', "TMT");
            $sheet->setCellValue('K4', "MASA KERJA");
            $sheet->setCellValue('L4', "KET");
            $sheet->setCellValue('M5', "GAPOK");
            $sheet->setCellValue('N5', "T. FUNGSIONAL");
            $sheet->setCellValue('O5', "T. KINERJA");
            $sheet->setCellValue('P5', "T. BPJS");
            $sheet->setCellValue('Q5', "T. STRUKTURAL");
            $sheet->setCellValue('R5', "T. WALI KELAS");
            $sheet->setCellValue('S5', "T. PENYESUAIAN");
            $sheet->setCellValue('T5', "TOTAL GAJI");


            // Apply style header yang telah kita buat tadi ke masing-masing kolom header
            $sheet->getStyle('A4')->applyFromArray($style_col);
            $sheet->getStyle('B4')->applyFromArray($style_col);
            $sheet->getStyle('C4')->applyFromArray($style_col);
            $sheet->getStyle('D4')->applyFromArray($style_col);
            $sheet->getStyle('E4')->applyFromArray($style_col);
            $sheet->getStyle('F4')->applyFromArray($style_col);
            $sheet->getStyle('G4')->applyFromArray($style_col);
            $sheet->getStyle('H5')->applyFromArray($style_col);
            $sheet->getStyle('I5')->applyFromArray($style_col);
            $sheet->getStyle('J5')->applyFromArray($style_col);
            $sheet->getStyle('K4')->applyFromArray($style_col);
            $sheet->getStyle('L4')->applyFromArray($style_col);
            $sheet->getStyle('M4')->applyFromArray($style_col);

            // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya


            $no = 1; // Untuk penomoran tabel, di awal set dengan 1
            $numrow = 6; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach ($datagaji2 as $hasil) { // Lakukan looping pada variabel siswa
                $totalgaji = $hasil->gapok + $hasil->fungsional + $hasil->kinerja + $hasil->bpjs + $hasil->struktural + $hasil->walas + $hasil->penyesuaian;
                $sheet->setCellValue('A' . $numrow, $no);
                $sheet->setCellValue('B' . $numrow, bulan($datagaji->bulan));
                $sheet->setCellValue('C' . $numrow, $datagaji->tahun);
                $sheet->setCellValue('D' . $numrow, $hasil->nama);
                $sheet->setCellValue('E' . $numrow, $hasil->satminkal);
                $sheet->setCellValue('F' . $numrow, $hasil->jabatan);
                $sheet->setCellValue('G' . $numrow, $hasil->golongan);
                $sheet->setCellValue('H' . $numrow, $hasil->sik);
                $sheet->setCellValue('I' . $numrow, $hasil->ijazah);
                $sheet->setCellValue('J' . $numrow, $hasil->tmt);
                $sheet->setCellValue('K' . $numrow, selisihTahun($hasil->tmt));
                $sheet->setCellValue('L' . $numrow, $hasil->santri);
                $sheet->setCellValue('M' . $numrow, $hasil->gapok);
                $sheet->setCellValue('N' . $numrow, $hasil->fungsional);
                $sheet->setCellValue('O' . $numrow, $hasil->kinerja);
                $sheet->setCellValue('P' . $numrow, $hasil->bpjs);
                $sheet->setCellValue('Q' . $numrow, $hasil->struktural);
                $sheet->setCellValue('R' . $numrow, $hasil->walas);
                $sheet->setCellValue('S' . $numrow, $hasil->penyesuaian);
                $sheet->setCellValue('T' . $numrow, $totalgaji);

                // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                $sheet->getStyle('A' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('B' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('C' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('D' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('E' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('F' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('G' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('H' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('I' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('J' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('K' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('L' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('M' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('N' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('O' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('P' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('Q' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('R' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('S' . $numrow)->applyFromArray($style_row);
                $sheet->getStyle('T' . $numrow)->applyFromArray($style_row);

                $no++; // Tambah 1 setiap kali looping
                $numrow++; // Tambah 1 setiap kali looping
            }

            // $sheet = $spreadsheet->getActiveSheet();
            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
            $sheet->getDefaultRowDimension()->setRowHeight(-1);

            // Set orientasi kertas jadi LANDSCAPE
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

            // Set judul file excel nya
            $sheet->setTitle("$satminkal->satminkal");
            $sheetIndex++;
        }

        // Proteksi
        // $protection = $sheet->getProtection();
        // $protection->setPassword('psb2023');
        // $protection->setSheet(true);
        // $protection->setSort(true);
        // $protection->setInsertRows(true);
        // $protection->setFormatCells(true);

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Data Gaji Guru dan Karyawan (' . bulan($datagaji->bulan) . ' ' . $datagaji->tahun . ').xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
