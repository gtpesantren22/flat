<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tambahan extends CI_Controller
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
    }

    public function index()
    {
        $data['judul'] = 'Tunjangan Tambahan';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Tambahan');

        $data['gaji'] = $this->model->getOrder2('gaji', 'tahun', 'DESC', 'bulan', 'DESC')->result();
        $data['data'] = $this->model->getOrder('tambahan', 'id_tambahan', 'DESC')->result();
        $this->load->view('tambahan', $data);
    }

    public function tambah()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses tambah data C: Tambahan');

        $data = [
            'nama' => $this->input->post('nama', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->tambah('tambahan', $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Tunjangan tambahan berhasil ditambahkan');
            redirect('tambahan');
        } else {
            $this->session->set_flashdata('error', 'Tunjangan tambahan gagal ditambahkan');
            redirect('tambahan');
        }
    }

    public function edit()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses ediit data C: Tambahan');

        $id = $this->input->post('id', true);
        $data = [
            'nama' => $this->input->post('nama', true),
            'nominal' => rmRp($this->input->post('nominal', true)),
        ];

        $this->model->edit('tambahan', 'id_tambahan', $id, $data);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'tambahan berhasil diupdate');
            redirect('tambahan');
        } else {
            $this->session->set_flashdata('error', 'tambahan gagal diupdate');
            redirect('tambahan');
        }
    }

    public function hapus($id)
    {
        $this->Auth_model->log_activity($this->userID, 'Akses hpus data C: Tambahan');

        $this->model->hapus('tambahan', 'id_tambahan', $id);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'tambahan berhasil dihapus');
            redirect('tambahan');
        } else {
            $this->session->set_flashdata('error', 'tambahan gagal dihapus');
            redirect('tambahan');
        }
    }

    public function cek($id)
    {
        $data['judul'] = 'Tunjangan Tambahan';
        $data['sub'] = 'tunjangan';
        $data['user'] = $this->Auth_model->current_user();
        $this->Auth_model->log_activity($this->userID, 'Akses index C: Tambahan');

        $datakirim = [];
        $dataguru = $this->db->query("SELECT guru.guru_id, guru.nama, guru.sik, satminkal.nama as lembaga FROM guru JOIN satminkal ON guru.satminkal=satminkal.id ORDER BY nama ASC")->result();
        foreach ($dataguru as $key) {
            $tunjGuru = $this->model->getBy2('tambahan_detail', 'guru_id', $key->guru_id, 'gaji_id', $id)->result();
            $total = $this->db->query("SELECT SUM(tambahan.nominal*jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$key->guru_id' AND gaji_id = '$id' ")->row();
            $datakirim[] = [
                'guru_id' => $key->guru_id,
                'gaji_id' => $id,
                'nama' => $key->nama,
                'sik' => $key->sik,
                'lembaga' => $key->lembaga,
                'listGaji' => $tunjGuru,
                'total' => $total->total,
            ];
        }
        $data['data'] = $datakirim;
        $data['tambahan'] = $this->model->getData('tambahan')->result();
        $data['gaji'] = $this->model->getBy('gaji', 'gaji_id', $id)->row();

        $this->load->view('tambahan_detail', $data);
    }

    public function addAdds()
    {
        $guru_id = $this->input->post('guru_id');
        $gaji_id = $this->input->post('gaji_id');
        $itemId = $this->input->post('itemId');
        $value = $this->input->post('value');

        $cek = $this->model->getBy3('tambahan_detail', 'guru_id', $guru_id, 'gaji_id', $gaji_id, 'id_tambahan', $itemId)->row();
        if ($cek) {
            $this->model->edit3('tambahan_detail', 'guru_id', $guru_id, 'gaji_id', $gaji_id, 'id_tambahan', $itemId, ['jumlah' => $value]);
        } else {
            $this->model->tambah('tambahan_detail', ['guru_id' => $guru_id, 'gaji_id' => $gaji_id, 'id_tambahan' => $itemId, 'jumlah' => $value]);
        }

        $this->Auth_model->log_activity($this->userID, 'Akses add/delete Item tambahan guru C: Tambahan');

        $total = $this->db->query("SELECT SUM(tambahan.nominal*tambahan_detail.jumlah) AS total FROM tambahan_detail JOIN tambahan ON tambahan.id_tambahan=tambahan_detail.id_tambahan WHERE  guru_id = '$guru_id' AND gaji_id = '$gaji_id' ")->row();

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success', 'total' => $total->total]);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
}
