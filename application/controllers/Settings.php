<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
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
    }

    public function index()
    {
        $data['judul'] = 'Settings';
        $data['user'] = $this->Auth_model->current_user();

        $data['sik'] = $this->model->getData('sik_setting')->result();
        $data['hak'] = $this->db->query("SELECT guru.nama, hak_setting.id,
    guru.guru_id,
    MAX(CASE WHEN payment = 'gapok' THEN id END) AS gapok,
    MAX(CASE WHEN payment = 'fungsional' THEN id END) AS fungsional,
    MAX(CASE WHEN payment = 'kinerja' THEN id END) AS kinerja,
    MAX(CASE WHEN payment = 'struktural' THEN id END) AS struktural,
    MAX(CASE WHEN payment = 'bpjs' THEN id END) AS bpjs,
    MAX(CASE WHEN payment = 'walas' THEN id END) AS walas,
    MAX(CASE WHEN payment = 'penyesuaian' THEN id END) AS penyesuaian
FROM 
    hak_setting JOIN guru ON guru.guru_id=hak_setting.guru_id
GROUP BY 
    hak_setting.guru_id")->result();

        $data['honor_non'] = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
        $data['honor_santri'] = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $data['honor_rami'] = $this->model->getBy('settings', 'nama', 'honor_rami')->row('isi');

        $this->load->view('settings', $data);
    }

    public function update_sik()
    {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        $this->model->edit('sik_setting', 'id', $id, [$field => $value]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function update_hak()
    {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        if ($value === 'Y') {
            $this->model->tambah('hak_setting', ['guru_id' => $id, 'payment' => $field]);
        } else {
            $this->model->hapus2('hak_setting', 'guru_id', $id, 'payment', $field);
        }

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function generateAllHak()
    {
        $this->db->query("TRUNCATE hak_setting");
        $guru = $this->model->getData('guru')->result();
        foreach ($guru as $key) {
            if ($key->sik === 'PTY') {
                $haksik = $this->model->getBy('sik_setting', 'pty', 'Y')->result();
            } else {
                $haksik = $this->model->getBy('sik_setting', 'ptty', 'Y')->result();
            }
            foreach ($haksik as $hsk) {
                $data = [
                    'guru_id' => $key->guru_id,
                    'payment' => $hsk->col,
                ];
                $this->model->tambah('hak_setting', $data);
            }
        }
        redirect('settings');
    }

    public function editHak()
    {
        $payment = $this->input->post('payment');
        $ket = $this->input->post('ket');
        if ($ket === 'Y') {
            $this->model->hapus('hak_setting', 'payment', $payment);
            $this->db->query("INSERT INTO hak_setting(guru_id, payment) SELECT guru_id, '$payment' FROM hak_setting WHERE guru_id IS NOT NULL GROUP BY guru_id");
        } else {
            $this->model->hapus('hak_setting', 'payment', $payment);
        }

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'settings berhasil diupdate');
            redirect('settings');
        } else {
            $this->session->set_flashdata('error', 'settings gagal diupdate');
            redirect('settings');
        }
    }

    public function updateInsentif()
    {

        $honor_non = rmRp($this->input->post('honor_non', true));
        $honor_santri = rmRp($this->input->post('honor_santri', true));
        $honor_rami = rmRp($this->input->post('honor_rami', true));


        $this->model->edit('settings', 'nama', 'honor_non', ['isi' => $honor_non]);
        $this->model->edit('settings', 'nama', 'honor_santri', ['isi' => $honor_santri]);
        $this->model->edit('settings', 'nama', 'honor_rami', ['isi' => $honor_rami]);

        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'settings nominal selesai diupdate');
            redirect('settings');
        } else {
            $this->session->set_flashdata('ok', 'settings nominal selesai diupdate');
            redirect('settings');
        }
    }
}
