<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guru extends MY_Controller
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

        if (!$this->db_active->field_exists('not_in_api', 'guru')) {
            $this->db_active->query("ALTER TABLE guru ADD COLUMN not_in_api INT DEFAULT 0");
        }

        if (!$this->db_active->field_exists('id_jabatan', 'registrasi')) {
            $this->db_active->query("ALTER TABLE registrasi ADD COLUMN id_jabatan INT DEFAULT 0");
        }
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
        WHERE guru.not_in_api = 0
        ORDER BY guru.nama ASC
        ")->result();
        $data['lembagaOpt'] = $this->model->getData('satminkal')->result();
        $data['jabatanOpt'] = $this->model->getData('jabatan')->result();
        $data['ijazahOpt'] = $this->model->getData('ijazah')->result();
        $data['golonganOpt'] = $this->model->getData('golongan')->result();
        $data['kategoriOpt'] = $this->model->getData('kategori')->result();

        // Fetch mismatched gurus (out of sync)
        $data['mismatched_gurus'] = $this->db_active->get_where('guru', ['not_in_api' => 1])->result();

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

    public function sinc_new_page()
    {
        try {
            $token = $this->model->getBy('settings', 'nama', 'token')->row('isi');
            $page = $this->input->post('page', TRUE);
            if (!$page) {
                $page = 1;
            }

            $url = "https://data.ppdwk.com/api/datatables?data=referensi-guru&page=" . $page . "&per_page=10&q=&sortby=nama&sortbydesc=ASC";
            $datas = fetchApiGet($url, $token);
            
            if (!$datas || !isset($datas['data'])) {
                throw new Exception("Gagal mengambil data dari API pusat (Response kosong atau tidak valid).");
            }
            
            $items = $datas['data']['data'] ?? [];
            $last_page = $datas['data']['last_page'] ?? 1;
            
            $synced_ids = [];
            $saved = 0;

            foreach ($items as $item) {
                if (!is_array($item)) continue;
                
                $ptkId = isset($item['ptk_id']) ? $item['ptk_id'] : null;
                if (!$ptkId) continue;

                $santri = (!empty($item['jenis_kesantrian']) && $item['jenis_kesantrian'] === 'Santri')
                    ? 'santri'
                    : 'non-santri';

                $jenisPtkNama = '';
                if (isset($item['jenis_ptk']) && is_array($item['jenis_ptk'])) {
                    $jenisPtkNama = isset($item['jenis_ptk']['nama']) ? $item['jenis_ptk']['nama'] : '';
                }
                
                if ($jenisPtkNama === 'Tendik') {
                    $kriteria = 'Karyawan';
                } elseif ($jenisPtkNama === 'Pengkaderan') {
                    $kriteria = 'Pengabdian';
                } else {
                    $kriteria = 'Guru';
                }

                $sik = (isset($item['status_pegawai']) && $item['status_pegawai'] === 'PTY') ? 'PTY' : 'PTTY';
                
                $golongan = null;
                if (isset($item['jenis_golongan']) && is_array($item['jenis_golongan'])) {
                    $golongan = isset($item['jenis_golongan']['jenis_golongan_id']) ? $item['jenis_golongan']['jenis_golongan_id'] : null;
                }

                $dataSv = [
                    'guru_id'  => $ptkId,
                    'nipy'     => isset($item['niy']) ? $item['niy'] : '',
                    'nik'      => isset($item['nik']) ? $item['nik'] : '',
                    'nama'     => isset($item['nama']) ? $item['nama'] : '',
                    'satminkal'=> '-', // Provide default satminkal for strict SQL insert mode
                    'jabatan'  => 0,   // Provide default jabatan for strict SQL insert mode
                    'ijazah'   => 0,// Provide default ijazah for strict SQL insert mode
                    'santri'   => $santri,
                    'kriteria' => $kriteria,
                    'sik'      => $sik,
                    'golongan' => $golongan,
                    'kategori' => $golongan,
                    'email'    => isset($item['email']) ? $item['email'] : '',
                    'hp'       => isset($item['telpon']) ? $item['telpon'] : '',
                    'rekening' => !empty($item['nomor_rekening']) ? $item['nomor_rekening'] : '',
                    'tmt'      => isset($item['tmt_pengangkatan']) ? $item['tmt_pengangkatan'] : null,
                    'not_in_api' => 0
                ];

                $exists = $this->model->getBy('guru', 'guru_id', $ptkId)->row();
                if ($exists) {
                    // During update, remove the default fields so we do not overwrite existing user choices
                    unset($dataSv['satminkal']);
                    unset($dataSv['jabatan']);
                    unset($dataSv['ijazah']);
                    
                    if (!$this->db_active->where('guru_id', $ptkId)->update('guru', $dataSv)) {
                        $err = $this->db_active->error();
                        throw new Exception("Gagal mengupdate database untuk PTK ID $ptkId: " . ($err['message'] ?? 'Unknown Error'));
                    }
                } else {
                    if (!$this->db_active->insert('guru', $dataSv)) {
                        $err = $this->db_active->error();
                        throw new Exception("Gagal menambahkan ke database untuk PTK ID $ptkId: " . ($err['message'] ?? 'Unknown Error'));
                    }
                }

                $synced_ids[] = $ptkId;
                $saved++;
            }

            echo json_encode([
                'status'       => 'success',
                'current_page' => (int)$page,
                'last_page'    => (int)$last_page,
                'has_more'     => ($page < $last_page),
                'synced_ids'   => $synced_ids,
                'saved'        => $saved
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')'
            ]);
        }
    }

    public function sinc_new_finish()
    {
        $synced_ids = $this->input->post('synced_ids', TRUE);
        if (empty($synced_ids) || !is_array($synced_ids)) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Data ID yang disinkronkan kosong atau tidak valid.'
            ]);
            return;
        }

        // Set not_in_api = 0 for all synced teachers
        $this->db_active->where_in('guru_id', $synced_ids)->update('guru', ['not_in_api' => 0]);

        // Set not_in_api = 1 for teachers not in the synced list
        $this->db_active->where_not_in('guru_id', $synced_ids)->update('guru', ['not_in_api' => 1]);

        echo json_encode([
            'status' => 'success'
        ]);
    }

    public function hapus_mismatch($id)
    {
        $guru = $this->db_active->get_where('guru', ['guru_id' => $id, 'not_in_api' => 1])->row();
        if (!$guru) {
            $this->session->set_flashdata('error', 'Guru tidak ditemukan atau sudah sinkron.');
            redirect('guru');
            return;
        }

        $this->db_active->trans_start();

        // 1. Delete from guru
        $this->db_active->delete('guru', ['guru_id' => $id]);

        // 2. Delete from registrasi
        $this->db_active->delete('registrasi', ['id_guru' => $id]);

        // 3. Delete from active month's salary records (only if unlocked!)
        $active_gaji = $this->db_active->get_where('gaji', ['status !=' => 'kunci'])->row();
        if ($active_gaji) {
            $gaji_id = $active_gaji->gaji_id;
            $bulan = $active_gaji->bulan;
            $tahun = $active_gaji->tahun;

            $this->db_active->delete('gaji_detail', ['guru_id' => $id, 'gaji_id' => $gaji_id]);
            $this->db_active->delete('potongan', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
            $this->db_active->delete('kehadiran', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
            $this->db_active->delete('honor', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
        }

        $this->db_active->trans_complete();

        if ($this->db_active->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menghapus data guru ' . $guru->nama);
        } else {
            $this->session->set_flashdata('ok', 'Berhasil menghapus guru ' . $guru->nama . ' beserta relasi data bulan berjalan.');
        }

        redirect('guru');
    }

    public function hapus_mismatch_all()
    {
        $mismatched = $this->db_active->get_where('guru', ['not_in_api' => 1])->result();
        if (empty($mismatched)) {
            $this->session->set_flashdata('error', 'Tidak ada data guru yang tidak sinkron.');
            redirect('guru');
            return;
        }

        $this->db_active->trans_start();

        $active_gaji = $this->db_active->get_where('gaji', ['status !=' => 'kunci'])->row();

        foreach ($mismatched as $guru) {
            $id = $guru->guru_id;

            $this->db_active->delete('guru', ['guru_id' => $id]);
            $this->db_active->delete('registrasi', ['id_guru' => $id]);

            if ($active_gaji) {
                $gaji_id = $active_gaji->gaji_id;
                $bulan = $active_gaji->bulan;
                $tahun = $active_gaji->tahun;

                $this->db_active->delete('gaji_detail', ['guru_id' => $id, 'gaji_id' => $gaji_id]);
                $this->db_active->delete('potongan', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
                $this->db_active->delete('kehadiran', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
                $this->db_active->delete('honor', ['guru_id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
            }
        }

        $this->db_active->trans_complete();

        if ($this->db_active->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal menghapus seluruh data guru tidak sinkron.');
        } else {
            $this->session->set_flashdata('ok', 'Berhasil menghapus ' . count($mismatched) . ' guru yang tidak sinkron beserta relasi data.');
        }

        redirect('guru');
    }

    public function sync_detail_start()
    {
        $total = $this->db_active->where('not_in_api', 0)->count_all_results('guru');
        echo json_encode([
            'status' => 'success',
            'total' => $total
        ]);
    }

    public function sync_satminkal_batch()
    {
        try {
            $token = $this->model->getBy('settings', 'nama', 'token')->row('isi');
            $offset = $this->input->post('offset', TRUE);
            $limit = $this->input->post('limit', TRUE);
            if ($offset === NULL) $offset = 0;
            if ($limit === NULL) $limit = 10;

            // Get batch of active teachers
            $gurus = $this->db_active->select('guru_id, nama')
                ->where('not_in_api', 0)
                ->limit($limit, $offset)
                ->get('guru')
                ->result();

            $processed = 0;
            $logs = [];

            foreach ($gurus as $guru) {
                $guru_id = $guru->guru_id;
                
                // Fetch detail from API
                $url = "https://data.ppdwk.com/api/ptk/detil-ptk/" . $guru_id;
                $row = fetchApiGet($url, $token);

                if (!$row || (isset($row['status']) && $row['status'] === 'error')) {
                    $logs[] = "Gagal mengambil data detail untuk: " . $guru->nama;
                    continue;
                }

                $satminkalId = '-';
                $registrasi_ptk = isset($row['registrasi_ptk']) && is_array($row['registrasi_ptk'])
                    ? $row['registrasi_ptk']
                    : [];

                foreach ($registrasi_ptk as $r) {
                    // Satminkal (PTK Induk)
                    if ($satminkalId === '-' && isset($r['ptk_induk']) && (string)$r['ptk_induk'] === '1') {
                        $satminkalId = $r['lembaga']['lembaga_id'] ?? '-';
                    }
                }

                // Start transaction for DB integrity
                $this->db_active->trans_start();

                // Update only satminkal column in guru table
                $this->db_active->where('guru_id', $guru_id)->update('guru', [
                    'satminkal' => $satminkalId
                ]);

                // Reset and rebuild registrasi mapping (populating both satminkal and id_jabatan to stay complete)
                $this->db_active->delete('registrasi', ['id_guru' => $guru_id]);
                if (!empty($registrasi_ptk)) {
                    $batch_registrasi = [];
                    foreach ($registrasi_ptk as $r) {
                        $isInduk = (isset($r['ptk_induk']) && (string)$r['ptk_induk'] === '1') ? 1 : 0;
                        $idJabatan = (isset($r['jenis_jabatan']) && is_array($r['jenis_jabatan']) && isset($r['jenis_jabatan']['jenis_jabatan_id']))
                            ? (int)$r['jenis_jabatan']['jenis_jabatan_id']
                            : 0;

                        $batch_registrasi[] = [
                            'id_guru'    => $guru_id,
                            'id_lembaga' => $r['lembaga']['lembaga_id'] ?? '-',
                            'satminkal'  => $isInduk,
                            'id_jabatan' => $idJabatan,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($batch_registrasi)) {
                        $this->db_active->insert_batch('registrasi', $batch_registrasi);
                    }
                }

                $this->db_active->trans_complete();

                if ($this->db_active->trans_status() === FALSE) {
                    $logs[] = "Gagal menyimpan Satminkal ke DB untuk: " . $guru->nama;
                } else {
                    $logs[] = "Sukses menyinkronkan Satminkal: " . $guru->nama;
                }

                $processed++;
            }

            echo json_encode([
                'status'    => 'success',
                'processed' => $processed,
                'logs'      => $logs,
                'has_more'  => (count($gurus) >= $limit)
            ]);

        } catch (Throwable $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')'
            ]);
        }
    }

    public function sync_jabatan_batch()
    {
        try {
            $token = $this->model->getBy('settings', 'nama', 'token')->row('isi');
            $offset = $this->input->post('offset', TRUE);
            $limit = $this->input->post('limit', TRUE);
            if ($offset === NULL) $offset = 0;
            if ($limit === NULL) $limit = 10;

            // Get batch of active teachers
            $gurus = $this->db_active->select('guru_id, nama')
                ->where('not_in_api', 0)
                ->limit($limit, $offset)
                ->get('guru')
                ->result();

            $processed = 0;
            $logs = [];

            foreach ($gurus as $guru) {
                $guru_id = $guru->guru_id;
                
                // Fetch detail from API
                $url = "https://data.ppdwk.com/api/ptk/detil-ptk/" . $guru_id;
                $row = fetchApiGet($url, $token);

                if (!$row || (isset($row['status']) && $row['status'] === 'error')) {
                    $logs[] = "Gagal mengambil data detail untuk: " . $guru->nama;
                    continue;
                }

                $jabatanId = 0;
                $registrasi_ptk = isset($row['registrasi_ptk']) && is_array($row['registrasi_ptk'])
                    ? $row['registrasi_ptk']
                    : [];

                foreach ($registrasi_ptk as $r) {
                    // Jabatan (jenis_tugas == 1)
                    if ($jabatanId === 0 && isset($r['jenis_tugas']) && (string)$r['jenis_tugas'] === '1') {
                        $jabatanId = isset($r['jenis_jabatan']['jenis_jabatan_id']) ? (int)$r['jenis_jabatan']['jenis_jabatan_id'] : 0;
                    }
                }

                // Start transaction for DB integrity
                $this->db_active->trans_start();

                // Update only jabatan column in guru table
                $this->db_active->where('guru_id', $guru_id)->update('guru', [
                    'jabatan' => $jabatanId
                ]);

                // Reset and rebuild registrasi mapping (populating both satminkal and id_jabatan to stay complete)
                $this->db_active->delete('registrasi', ['id_guru' => $guru_id]);
                if (!empty($registrasi_ptk)) {
                    $batch_registrasi = [];
                    foreach ($registrasi_ptk as $r) {
                        $isInduk = (isset($r['ptk_induk']) && (string)$r['ptk_induk'] === '1') ? 1 : 0;
                        $idJabatan = (isset($r['jenis_jabatan']) && is_array($r['jenis_jabatan']) && isset($r['jenis_jabatan']['jenis_jabatan_id']))
                            ? (int)$r['jenis_jabatan']['jenis_jabatan_id']
                            : 0;

                        $batch_registrasi[] = [
                            'id_guru'    => $guru_id,
                            'id_lembaga' => $r['lembaga']['lembaga_id'] ?? '-',
                            'satminkal'  => $isInduk,
                            'id_jabatan' => $idJabatan,
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($batch_registrasi)) {
                        $this->db_active->insert_batch('registrasi', $batch_registrasi);
                    }
                }

                $this->db_active->trans_complete();

                if ($this->db_active->trans_status() === FALSE) {
                    $logs[] = "Gagal menyimpan Jabatan ke DB untuk: " . $guru->nama;
                } else {
                    $logs[] = "Sukses menyinkronkan Jabatan: " . $guru->nama;
                }

                $processed++;
            }

            echo json_encode([
                'status'    => 'success',
                'processed' => $processed,
                'logs'      => $logs,
                'has_more'  => (count($gurus) >= $limit)
            ]);

        } catch (Throwable $e) {
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ')'
            ]);
        }
    }
}
