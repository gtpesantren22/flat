<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sinc_guru extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Gajimodel', 'm_gaji');
    }

    public function tambah($id_guru)
    {
        $row = $this->m_gaji->detailGuru($id_guru);

        $satminkal = '-';
        $jabatan = '-';
        $satminkalId = '-';
        $jabatanId = '-';
        $sik = $row['status_pegawai'] ?? '';
        $ijazah = $row['pendidikan_terakhir']['jenjang_pendidikan']['nama'] ?? '';
        $ijazahId = $row['pendidikan_terakhir']['jenjang_pendidikan']['jenjang_pendidikan_id'] ?? '';
        $golongan = $row['jenis_golongan']['nama'] ?? '';
        $golonganId = $row['jenis_golongan']['jenis_golongan_id'] ?? '';
        $tmt = $row['tmt_pengangkatan'] ?? '';
        $ket = $row['jenis_kesantrian'] === 'Santri' ? 'santri' : 'non-santri';

        if ($row['jenis_ptk']['nama'] && $row['jenis_ptk']['nama'] == 'Tendik') {
            $kriteria = 'Karyawan';
        } elseif ($row['jenis_ptk']['nama'] && $row['jenis_ptk']['nama'] == 'Pengkaderan') {
            $kriteria = 'Pengabdian';
        } else {
            $kriteria = 'Guru';
        }

        if (!empty($row['registrasi_ptk'])) {
            foreach ($row['registrasi_ptk'] as $r) {

                // Satminkal (PTK Induk)
                if ($satminkal === '-' && (string)$r['ptk_induk'] === '1') {
                    $satminkal = $r['lembaga']['nama'] ?? '-';
                    $satminkalId = $r['lembaga']['lembaga_id'] ?? '-';
                }

                // Jabatan
                if ($jabatan === '-' && $r['jenis_tugas'] == 1) {
                    $jabatan = $r['jenis_jabatan']['nama'] ?? '-';
                    $jabatanId = $r['jenis_jabatan']['jenis_jabatan_id'] ?? '-';
                }

                // Stop loop jika semua sudah ketemu
                if ($satminkal !== '-' && $jabatan !== '-') {
                    break;
                }
            }
        }

        $dataGuru = [
            'guru_id' => $id_guru,
            'nipy'     => $row['nipy'] ?? '',
            'nik'     => $row['nik'] ?? '',
            'nama'     => $row['nama'] ?? '',
            'satminkal'      => $satminkalId,
            'santri' => $ket,
            'jabatan'    => $jabatanId,
            'kriteria'     => $kriteria,
            'sik'     => $sik,
            'ijazah'     => $ijazahId,
            'tmt'     => $tmt,
            'golongan'     => $golonganId,
            'kategori' => '',
            'email' => $row['email'] ?? '',
            'hp' => $row['telpon'] ?? '',
            'rekening' => $row['nomor_rekening'] ?? '',
        ];

        $cek = $this->db_active->get_where('gaji', ['status !=' => 'kunci'])->row();
        if ($cek) {
            $dataGuruDtl = [
                'gaji_id' => $cek->gaji_id,
                'guru_id' => $id_guru,
                'nama'     => $row['nama'] ?? '',
                'satminkal'      => $satminkal,
                'jabatan'    => $jabatan,
                'golongan'     => $golongan,
                'sik'     => $sik,
                'ijazah'     => $ijazah,
                'tmt'     => $tmt,
                'kriteria'     => $kriteria,
                'santri' => $ket,
                'kategori' => $row['jenis_golongan']['kategori'],
                'email' => $row['email'] ?? '',
                'hp' => $row['telpon'] ?? '',
                'rekening' => $row['nomor_rekening'] ?? '',
                'is_dirty' => 1
            ];
            $this->db_active->insert('gaji_detail', $dataGuruDtl);
        }
        $this->db_active->insert('guru', $dataGuru);
    }
    public function hapus($id_guru)
    {
        $this->db_active->delete('guru', ['guru_id' => $id_guru]);
        $cek = $this->db_active->get_where('gaji', ['status !=' => 'kunci'])->row();
        if ($cek) {
            $this->db_active->delete('gaji_detail', ['guru_id' => $id_guru, 'gaji_id' => $cek->gaji_id]);
            $this->db_active->delete('potongan', ['guru_id' => $id_guru, 'bulan' => $cek->bulan, 'tahun' => $cek->tahun]);
            $this->db_active->delete('kehadiran', ['guru_id' => $id_guru, 'bulan' => $cek->bulan, 'tahun' => $cek->tahun]);
            $this->db_active->delete('honor', ['guru_id' => $id_guru, 'bulan' => $cek->bulan, 'tahun' => $cek->tahun]);
        }
    }
    public function edit($id_guru)
    {
        $row = $this->m_gaji->detailGuru($id_guru);

        $satminkal = '-';
        $jabatan = '-';
        $satminkalId = '-';
        $jabatanId = '-';
        $sik = $row['status_pegawai'] ?? '';
        $ijazah = $row['pendidikan_terakhir']['jenjang_pendidikan']['nama'] ?? '';
        $ijazahId = $row['pendidikan_terakhir']['jenjang_pendidikan']['jenjang_pendidikan_id'] ?? '';
        $golongan = $row['jenis_golongan']['nama'] ?? '';
        $golonganId = $row['jenis_golongan']['jenis_golongan_id'] ?? '';
        $tmt = $row['tmt_pengangkatan'] ?? '';
        $ket = $row['jenis_kesantrian'] === 'Santri' ? 'santri' : 'non-santri';

        if ($row['jenis_ptk']['nama'] && $row['jenis_ptk']['nama'] == 'Tendik') {
            $kriteria = 'Karyawan';
        } elseif ($row['jenis_ptk']['nama'] && $row['jenis_ptk']['nama'] == 'Pengkaderan') {
            $kriteria = 'Pengabdian';
        } else {
            $kriteria = 'Guru';
        }

        if (!empty($row['registrasi_ptk'])) {
            foreach ($row['registrasi_ptk'] as $r) {

                // Satminkal (PTK Induk)
                if ($satminkal === '-' && (string)$r['ptk_induk'] === '1') {
                    $satminkal = $r['lembaga']['nama'] ?? '-';
                    $satminkalId = $r['lembaga']['lembaga_id'] ?? '-';
                }

                // Jabatan
                if ($jabatan === '-' && $r['jenis_tugas'] == 1) {
                    $jabatan = $r['jenis_jabatan']['nama'] ?? '-';
                    $jabatanId = $r['jenis_jabatan']['jenis_jabatan_id'] ?? '-';
                }

                // Stop loop jika semua sudah ketemu
                if ($satminkal !== '-' && $jabatan !== '-') {
                    break;
                }
            }
        }

        $dataGuru = [
            'nipy'     => $row['nipy'] ?? '',
            'nik'     => $row['nik'] ?? '',
            'nama'     => $row['nama'] ?? '',
            'satminkal'      => $satminkalId,
            'santri' => $ket,
            'jabatan'    => $jabatanId,
            'kriteria'     => $kriteria,
            'sik'     => $sik,
            'ijazah'     => $ijazahId,
            'tmt'     => $tmt,
            'golongan'     => $golonganId,
            'kategori' => '',
            'email' => $row['email'] ?? '',
            'hp' => $row['telpon'] ?? '',
            'rekening' => $row['nomor_rekening'] ?? '',
        ];

        $cek = $this->db_active->get_where('gaji', ['status !=' => 'kunci'])->row();
        if ($cek) {
            $dataGuruDtl = [
                'nama'     => $row['nama'] ?? '',
                'satminkal'      => $satminkal,
                'jabatan'    => $jabatan,
                'golongan'     => $golongan,
                'sik'     => $sik,
                'ijazah'     => $ijazah,
                'tmt'     => $tmt,
                'kriteria'     => $kriteria,
                'santri' => $ket,
                'kategori' => $row['jenis_golongan']['kategori'],
                'email' => $row['email'] ?? '',
                'hp' => $row['telpon'] ?? '',
                'rekening' => $row['nomor_rekening'] ?? '',
                'is_dirty' => 1
            ];
            $this->db_active->update('gaji_detail', $dataGuruDtl, ['guru_id' => $id_guru, 'gaji_id' => $cek->gaji_id]);
        }
        $this->db_active->update('guru', $dataGuru, ['guru_id' => $id_guru]);
    }

    public function reloadNominal($gaji_id)
    {
        $cek = $this->model->getBy('gaji', 'gaji_id', $gaji_id)->row();

        if ($cek && $cek->status == 'kunci') {
            $this->session->set_flashdata('error', 'Gaji sudah terkunci');
            redirect('gaji/detail/' . $gaji_id);
            exit;
        }

        $bulan = $cek->bulan;
        $tahun = $cek->tahun;

        $this->db_active->trans_start();

        /* ================= AMBIL DATA SEKALI ================= */

        $dataGaji = $this->db_active
            ->select('gd.id_detail, gd.guru_id, g.kriteria, g.sik, g.golongan, g.tmt, g.jabatan, g.satminkal, g.ijazah')
            ->from('gaji_detail gd')
            ->join('guru g', 'g.guru_id = gd.guru_id')
            ->where('gd.gaji_id', $gaji_id)
            ->get()
            ->result();


        $updateBatch = [];

        foreach ($dataGaji as $row) {

            $gapok = $this->m_gaji->gapok(
                $row->guru_id,
                $row->kriteria,
                $row->sik,
                $row->golongan,
                $row->tmt,
                $row->jabatan,
                $bulan,
                $tahun
            );

            $fungsional = $this->m_gaji->fungsional(
                $row->golongan,
                $row->kriteria,
                $row->sik,
                $row->ijazah
            );

            $kinerja = $this->m_gaji->kinerja(
                $row->guru_id,
                $bulan,
                $tahun,
                $row->tmt,
                $row->kriteria,
                $row->jabatan
            );

            $struktural = $this->m_gaji->struktural(
                $row->kriteria,
                $row->jabatan,
                $row->satminkal
            );

            $bpjs = $this->m_gaji->bpjs($row->guru_id);

            $penyesuaian = $this->m_gaji->penyesuaian(
                $row->guru_id,
                $row->kriteria,
                $row->jabatan,
                $row->sik
            );

            $tambahan = $this->m_gaji->tambahan($row->guru_id, $gaji_id);


            $updateBatch[] = [
                'id_detail' => $row->id_detail,
                'gapok' => $gapok ?? 0,
                'fungsional' => $fungsional ?? 0,
                'kinerja' => $kinerja ?? 0,
                'struktural' => $struktural ?? 0,
                'bpjs' => $bpjs ?? 0,
                'penyesuaian' => $penyesuaian ?? 0,
                'tambahan' => $tambahan ?? 0
            ];
        }

        /* ================= BATCH UPDATE ================= */

        if (!empty($updateBatch)) {
            $this->db_active->update_batch('gaji_detail', $updateBatch, 'id_detail');
        }

        $this->db_active->trans_complete();

        if ($this->db_active->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gaji gagal diupdate');
        } else {
            $this->summary($gaji_id);
            $this->session->set_flashdata('ok', 'Gaji berhasil diupdate');
        }

        redirect('gaji/detail/' . $gaji_id);
    }

    public function reCalcGaji()
    {
        $cek = $this->model->getBy('gaji', 'status !=', 'kunci')->row();

        if ($cek) {
            $gaji_id = $cek->gaji_id;
            $bulan = $cek->bulan;
            $tahun = $cek->tahun;

            $this->db_active->trans_start();

            /* ================= AMBIL DATA SEKALI ================= */

            $dataGaji = $this->db_active
                ->select('gd.id_detail, gd.guru_id, g.kriteria, g.sik, g.golongan, g.tmt, g.jabatan, g.satminkal, g.ijazah')
                ->from('gaji_detail gd')
                ->join('guru g', 'g.guru_id = gd.guru_id')
                ->where('gd.gaji_id', $gaji_id)
                ->where('gd.is_dirty', 1)
                ->get()
                ->result();


            $updateBatch = [];

            foreach ($dataGaji as $row) {

                $gapok = $this->m_gaji->gapok(
                    $row->guru_id,
                    $row->kriteria,
                    $row->sik,
                    $row->golongan,
                    $row->tmt,
                    $row->jabatan,
                    $bulan,
                    $tahun
                );

                $fungsional = $this->m_gaji->fungsional(
                    $row->golongan,
                    $row->kriteria,
                    $row->sik,
                    $row->ijazah
                );

                $kinerja = $this->m_gaji->kinerja(
                    $row->guru_id,
                    $bulan,
                    $tahun,
                    $row->tmt,
                    $row->kriteria,
                    $row->jabatan
                );

                $struktural = $this->m_gaji->struktural(
                    $row->kriteria,
                    $row->jabatan,
                    $row->satminkal
                );

                $bpjs = $this->m_gaji->bpjs($row->guru_id);

                $penyesuaian = $this->m_gaji->penyesuaian(
                    $row->guru_id,
                    $row->kriteria,
                    $row->jabatan,
                    $row->sik
                );

                $tambahan = $this->m_gaji->tambahan($row->guru_id, $gaji_id);

                $updateBatch[] = [
                    'id_detail' => $row->id_detail,
                    'gapok' => $gapok ?? 0,
                    'fungsional' => $fungsional ?? 0,
                    'kinerja' => $kinerja ?? 0,
                    'struktural' => $struktural ?? 0,
                    'bpjs' => $bpjs ?? 0,
                    'penyesuaian' => $penyesuaian ?? 0,
                    'tambahan' => $tambahan ?? 0,
                    'is_dirty' => 0
                ];
            }

            /* ================= BATCH UPDATE ================= */

            if (!empty($updateBatch)) {
                $this->db_active->update_batch('gaji_detail', $updateBatch, 'id_detail');
            }

            $this->db_active->trans_complete();

            if ($this->db_active->trans_status() === FALSE) {
                exit("Gaji gagal diupdate");
            } else {
                $this->summary($gaji_id);
                exit("Gaji berhasil diupdate");
            }
        } else {
            exit("Gaji tidak ditemukan");
        }
    }

    public function summary($gaji_id)
    {
        $this->model->query("
            UPDATE gaji g
            LEFT JOIN (
                SELECT 
                    gaji_id,
                    SUM(fungsional+kinerja+bpjs+struktural+penyesuaian+walas+gapok+tambahan) AS total
                FROM gaji_detail
                GROUP BY gaji_id
            ) gd ON gd.gaji_id = g.gaji_id

            LEFT JOIN (
                SELECT 
                    bulan,
                    tahun,
                    SUM(nominal) AS potongan
                FROM potongan
                GROUP BY bulan,tahun
            ) p ON p.bulan = g.bulan AND p.tahun = g.tahun

            SET 
            g.total = IFNULL(gd.total,0),
            g.potongan = IFNULL(p.potongan,0)

            WHERE g.gaji_id = '$gaji_id';
     ");
    }
}
