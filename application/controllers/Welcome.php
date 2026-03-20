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
			$totalakhir += $value->total;
			$potongakhir += $value->potongan;
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
