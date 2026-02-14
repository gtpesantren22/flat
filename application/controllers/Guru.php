<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Gajimodel');
        $this->load->model('Auth_model');

        // $user = $this->Auth_model->current_user();

        // $this->user = $user->nama;
        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }

        $this->token = $this->model->getBy('settings', 'nama', 'token')->row('isi');
    }

    public function index()
    {
        $data['judul'] = 'Guru';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->db_active->query("SELECT guru.*, satminkal.nama as nmlembaga, ijazah.nama as nmijazah, golongan.nama as nmgolongan, jabatan.nama as nmjabatan, golongan.kategori as nmkategori FROM guru 
        LEFT JOIN satminkal ON satminkal.id=guru.satminkal
        LEFT JOIN ijazah ON ijazah.id=guru.ijazah
        LEFT JOIN golongan ON golongan.id=guru.golongan
        LEFT JOIN jabatan ON jabatan.jabatan_id=guru.jabatan
        ")->result();
        $data['lembagaOpt'] = $this->model->getData('satminkal')->result();
        $data['jabatanOpt'] = $this->model->getData('jabatan')->result();
        $data['ijazahOpt'] = $this->model->getData('ijazah')->result();
        $data['golonganOpt'] = $this->model->getData('golongan')->result();
        $data['kategoriOpt'] = $this->model->getData('kategori')->result();
        $this->load->view('guru', $data);
    }

    public function tambah()
    {
        $data = [
            'guru_id' => $this->uuid->v4(),
            'nama' => $this->input->post('nama', true),
            'nipy' => $this->input->post('nipy', true),
            'nik' => $this->input->post('nik', true),
            'satminkal' => $this->input->post('satminkal', true),
            'jabatan' => $this->input->post('jabatan', true),
            'kriteria' => $this->input->post('kriteria', true),
            'sik' => $this->input->post('sik', true),
            'ijazah' => $this->input->post('ijazah', true),
            'tmt' => $this->input->post('tmt', true),
            'golongan' => $this->input->post('golongan', true),
            'santri' => $this->input->post('santri', true),
            'kategori' => $this->input->post('kategori', true),
            'email' => $this->input->post('email', true),
            'hp' => $this->input->post('hp', true),
            'rekening' => $this->input->post('rekening', true),
        ];

        $this->model->tambah('guru', $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Guru berhasil ditambahkan');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'Guru gagal ditambahkan');
            redirect('guru');
        }
    }

    public function hapus($id)
    {
        $this->model->hapus('guru', 'guru_id', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'guru berhasil dihapus');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'guru gagal dihapus');
            redirect('guru');
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
            'nipy' => $this->input->post('nipy', true),
            'nik' => $this->input->post('nik', true),
            'satminkal' => $this->input->post('satminkal', true),
            'jabatan' => $this->input->post('jabatan', true),
            'kriteria' => $this->input->post('kriteria', true),
            'sik' => $this->input->post('sik', true),
            'ijazah' => $this->input->post('ijazah', true),
            'tmt' => $this->input->post('tmt', true),
            'golongan' => $this->input->post('golongan', true),
            'santri' => $this->input->post('santri', true),
            'kategori' => $this->input->post('kategori', true),
            'email' => $this->input->post('email', true),
            'hp' => $this->input->post('hp', true),
            'rekening' => $this->input->post('rekening', true),
        ];

        $this->model->edit('guru', 'guru_id', $id, $data);
        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'guru berhasil diupdate');
            redirect('guru');
        } else {
            $this->session->set_flashdata('error', 'guru gagal diupdate');
            redirect('guru');
        }
    }

    public function datatable()
    {
        $search   = $this->input->get('search') ?? '';
        $page     = max(1, (int) ($this->input->get('page') ?? 1));
        $perPage  = max(1, (int) ($this->input->get('perPage') ?? 10));
        $sortBy   = $this->input->get('sortBy') ?? 'nama';
        $sortDir  = strtoupper($this->input->get('sortDir') ?? 'ASC');

        /* ================= API URL ================= */
        $apiUrl = 'https://data.ppdwk.com/api/datatables?' . http_build_query([
            'data'       => 'referensi-guru',
            'page'       => $page,
            'per_page'   => $perPage,
            'q'          => $search,
            'sortby'     => $sortBy,
            'sortbydesc' => $sortDir,
        ]);

        /* ================= cURL ================= */
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->token,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            // fallback aman
            $result = [
                'data'     => [],
                'total'    => 0,
                'page'     => $page,
                'perPage'  => $perPage,
                'lastPage' => 0,
            ];
        } else {
            $api = json_decode($response, true);

            /* ================= OUTPUT (TIDAK DIUBAH) ================= */
            $total = (int) ($api['data']['total'] ?? 0);

            $rawData = $api['data']['data'] ?? [];

            $data = array_map(function ($row) {
                $dtl = $this->Gajimodel->detailGuru($row['ptk_id'] ?? '');
                $satminkal = '-';
                $jabatan = '-';
                $ijazah_terakhir = '-';

                if (!empty($dtl['registrasi_ptk'])) {

                    foreach ($dtl['registrasi_ptk'] as $r) {

                        // Satminkal (PTK Induk)
                        if ($satminkal === '-' && (string)$r['ptk_induk'] === '1') {
                            $satminkal = $r['lembaga']['nama'] ?? '-';
                        }

                        // Jabatan
                        if ($jabatan === '-' && $r['jenis_tugas'] == 1) {
                            $jabatan = $r['jenis_jabatan']['nama'] ?? '-';
                        }

                        // Stop loop jika semua sudah ketemu
                        if ($satminkal !== '-' && $jabatan !== '-') {
                            break;
                        }
                    }
                }
                if (!empty($dtl['rwy_pend_formal']) && !empty($dtl['pendidikan_terakhir'])) {
                    foreach ($dtl['rwy_pend_formal'] as $r) {
                        if ((string)$r['rwy_pend_formal_id'] === (string)$dtl['pendidikan_terakhir']['rwy_pend_formal_id']) {
                            $ijazah_terakhir = $r['jenjang_pendidikan']['nama'] ?? '-';
                            break;
                        }
                    }
                }

                return [
                    'nama'     => $row['nama'] ?? '',
                    'satminkal'      => $satminkal,
                    'jabatan'    => $jabatan,
                    'kriteria'     => $row['jenis_ptk']['nama'] ?? '',
                    'sik'     => $row['status_pegawai'] ?? '',
                    'ijazah'     => $ijazah_terakhir,
                    'tmt'     => $row['tmt_pengangkatan'] ?? '',
                    'golongan'     => $row['jenis_golongan']['nama'] ?? '',
                    'ket'     => $row['jenis_kesantrian'] ?? '',
                ];
            }, $rawData);


            $result = [
                'data'     => $data,
                'total'    => $total,
                'page'     => $page,
                'perPage'  => $perPage,
                'lastPage' => $perPage > 0 ? ceil($total / $perPage) : 0,
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function cekguru()
    {
        $dtl = $this->Gajimodel->detailGuru('a54323e2-8062-4a29-8f31-84c0433a7567');
        var_dump($dtl);
        exit;
    }
}
