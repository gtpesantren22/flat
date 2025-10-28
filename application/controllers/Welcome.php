<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
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
		$data['judul'] = 'Dashboard';
		$data['user'] = $this->Auth_model->current_user();

		$this->load->view('index', $data);
	}

	public function loadNominal()
	{

		$totalakhir = 0;
		$potongakhir = 0;
		// var_dump($gajiAwal);
		$gajiAwal = $this->model->getOrder2('gaji', 'tahun', 'DESC', 'bulan', 'DESC')->result();

		foreach ($gajiAwal as  $value) {
			$totalawal = 0;
			$potongawal = 0;
			if ($value->status == 'kunci') {
				$datatotal = $this->db_active->query("SELECT SUM(fungsional) AS fungsional, SUM(kinerja) AS kinerja, SUM(bpjs) AS bpjs, SUM(struktural) AS struktural, SUM(penyesuaian) AS penyesuaian, SUM(walas) AS walas, SUM(gapok) AS gapok FROM gaji_detail WHERE gaji_id = '$value->gaji_id' ")->row();
				$potong = $this->db_active->query("SELECT SUM(nominal) as total FROM potongan WHERE bulan = ? AND tahun = ?", [
					$value->bulan,
					$value->tahun
				])->row();
				$totalawal += $datatotal->fungsional + $datatotal->kinerja + $datatotal->bpjs + $datatotal->struktural + $datatotal->penyesuaian + $datatotal->walas + $datatotal->gapok;
				$potongawal += $potong ? $potong->total : 0;
			} else {

				$query = $this->model->getBy('gaji_detail', 'gaji_id', $value->gaji_id);

				if ($query->row()) {
					foreach ($query->result() as $row) {
						$guru = $this->model->getBy('guru', 'guru_id', $row->guru_id)->row();

						if (!$guru) {
							continue; // Skip jika data guru tidak ditemukan
						}

						// Hitung gaji pokok (gapok)
						if ($guru->sik === 'PTY') {
							$gapok = $this->model->getBy2('gapok', 'golongan_id', $guru->golongan, 'masa_kerja', selisihTahun($guru->tmt))->row();
							$gapok = $gapok ? $gapok->nominal : 0;
						} else {
							$gapokData = $this->model->getBy3('honor', 'guru_id', $guru->guru_id, 'bulan', $value->bulan, 'tahun', $value->tahun)->row();
							$gapok = $gapokData ? ($gapokData->kehadiran / 4) : 0;
							$gapok = $guru->santri === 'santri' ? $gapok * $this->honor_santri : $gapok * $this->honor_non;
						}

						// Data tunjangan lainnya
						$fungsional = $this->model->getBy2('fungsional', 'golongan_id', $guru->golongan, 'kategori', $guru->kategori)->row();
						$kinerja = $this->model->getBy('kinerja', 'masa_kerja', selisihTahun($guru->tmt))->row();
						$struktural = $this->model->getBy2('struktural', 'jabatan_id', $guru->jabatan, 'satminkal_id', $guru->satminkal)->row();
						$bpjs = $this->model->getBy('bpjs', 'guru_id', $guru->guru_id)->row();
						$walas = $this->model->getBy('walas', 'guru_id', $guru->guru_id)->row();
						$penyesuaian = $this->model->getBy('penyesuaian', 'guru_id', $guru->guru_id)->row();

						// Hitung total potongan
						$potong = $this->db_active->query("SELECT SUM(nominal) as total FROM potongan WHERE guru_id = ? AND bulan = ? AND tahun = ?", [
							$row->guru_id,
							$value->bulan,
							$value->tahun
						])->row();

						// Ambil hak pembayaran
						$cek = $this->model->getBy('hak_setting', 'guru_id', $guru->guru_id)->result_array();
						$payments = array_column($cek, 'payment');

						// Hitung total awal
						$totalawal += (in_array('gapok', $payments) ? $gapok : 0) +
							($fungsional && in_array('fungsional', $payments) ? $fungsional->nominal : 0) +
							($kinerja && in_array('kinerja', $payments) ? $kinerja->nominal * $this->jamkinerja : 0) +
							($struktural && in_array('struktural', $payments) ? $struktural->nominal : 0) +
							($bpjs && in_array('bpjs', $payments) ? $bpjs->nominal : 0) +
							($walas && in_array('walas', $payments) ? $walas->nominal : 0) +
							($penyesuaian && in_array('penyesuaian', $payments) ? $penyesuaian->sebelum - $penyesuaian->sesudah : 0);

						$potongawal += $potong ? $potong->total : 0;
					}
				}
			}

			$totalakhir += $totalawal;
			$potongakhir += $potongawal;
		}
		$pagu = 3500000000;
		$pakai = $totalakhir - $potongakhir;
		$sisa = $pagu - $pakai;
		echo json_encode([
			'all' => formatUangSingkat($pagu),
			'pakai' => formatUangSingkat($pakai),
			'sisa' => formatUangSingkat($sisa),
		]);
	}

	public function sendNota()
	{
		$psn = 'Hai selamat malam1';

		$proses = kirim_person('f4064efa9d05f66f9be6151ec91ad846', '085236924510', $psn);
		$responseArray = json_decode($proses, true);
		echo $responseArray['query']['to'];
	}

	public function converImage()
	{
		$data['data'] = $this->db_active->query("SELECT * FROM gaji_detail WHERE gaji_id = 'e613c73e-308f-490a-8981-277f91fc1d20' LIMiT 1 ")->row();
		$this->load->view('example_view', $data);
	}

	public function saveImage()
	{
		// Pastikan request berasal dari AJAX
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$imgData = $this->input->post('image'); // Ambil data gambar dari request
			$imgData = str_replace('data:image/png;base64,', '', $imgData);
			$imgData = str_replace(' ', '+', $imgData);
			$imageData = base64_decode($imgData);

			$filename = 'screenshot_' . time() . '.png'; // Buat nama file unik
			$filePath = FCPATH . 'assets/img/nota/' . $filename; // Path lengkap penyimpanan

			// Simpan file gambar
			if (file_put_contents($filePath, $imageData)) {
				kirim_media('f4064efa9d05f66f9be6151ec91ad846', '085236924510', base_url('assets/img/nota/' . $filename), 0, 'Slip gaji');
				echo json_encode(['status' => 'success', 'file' => base_url('assets/img/nota/' . $filename)]);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan gambar']);
			}
		}
	}
	public function lirim()
	{
		$tes = kirim_media('f4064efa9d05f66f9be6151ec91ad846', '085236924510', 'https://allthingsd.com/files/2011/09/free.png', 0, 'Slip gaji');

		echo $tes;
	}
}
