<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Modeldata', 'model');
        $this->load->model('Auth_model');

        $user = $this->Auth_model->current_user();
        $this->token = $this->model->getBy('settings', 'nama', 'token')->row('isi');

        $this->userID = $user->id_user;
        if (!$this->Auth_model->current_user()) {
            redirect('login/logout');
        }
    }

    public function index()
    {
        $data['judul'] = 'Settings';
        $data['sub'] = 'settings';
        $data['user'] = $this->Auth_model->current_user();

        // $data['sik'] = $this->model->getData('sik_setting')->result();
        //         $data['hak'] = $this->db->query("SELECT guru.nama, hak_setting.id, satminkal.nama as lembaga, jabatan.nama as jabatan, guru.sik,
        //     guru.guru_id,
        //     MAX(CASE WHEN payment = 'gapok' THEN hak_setting.id END) AS gapok,
        //     MAX(CASE WHEN payment = 'fungsional' THEN hak_setting.id END) AS fungsional,
        //     MAX(CASE WHEN payment = 'kinerja' THEN hak_setting.id END) AS kinerja,
        //     MAX(CASE WHEN payment = 'struktural' THEN hak_setting.id END) AS struktural,
        //     MAX(CASE WHEN payment = 'bpjs' THEN hak_setting.id END) AS bpjs,
        //     MAX(CASE WHEN payment = 'walas' THEN hak_setting.id END) AS walas,
        //     MAX(CASE WHEN payment = 'penyesuaian' THEN hak_setting.id END) AS penyesuaian
        // FROM hak_setting 
        // JOIN guru ON guru.guru_id=hak_setting.guru_id
        // JOIN satminkal ON guru.satminkal=satminkal.id
        // JOIN jabatan ON guru.jabatan=jabatan.jabatan_id
        // GROUP BY 
        //     hak_setting.guru_id")->result();

        $data['honor_non'] = $this->model->getBy('settings', 'nama', 'honor_non')->row('isi');
        $data['honor_santri'] = $this->model->getBy('settings', 'nama', 'honor_santri')->row('isi');
        $data['honor_rami'] = $this->model->getBy('settings', 'nama', 'honor_rami')->row('isi');
        $data['honordata'] = $this->db_active->query("SELECT * FROM honor GROUP BY honor_id ORDER BY created_at DESC")->result();
        $data['db_list'] = $this->db->query("SELECT * FROM list_db")->result();

        $this->Auth_model->log_activity($this->userID, 'Akses index Settings');
        $this->load->view('settings', $data);
    }

    public function update_sik()
    {
        $id = $this->input->post('id');
        $field = $this->input->post('field');
        $value = $this->input->post('value');

        $this->model->edit('sik_setting', 'id', $id, [$field => $value]);
        $this->Auth_model->log_activity($this->userID, 'Akses update SIK C: Settings');

        if ($this->db_active->affected_rows() > 0) {
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

        $this->Auth_model->log_activity($this->userID, 'Akses update Hak per-guru C: Settings');

        if ($this->db_active->affected_rows() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function generateAllHak()
    {
        $this->db_active->query("TRUNCATE hak_setting");
        $this->Auth_model->log_activity($this->userID, 'Akses generate All hak C: Settings');

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
        $this->Auth_model->log_activity($this->userID, 'Akses edit Hak C: Settings');

        $payment = $this->input->post('payment');
        $ket = $this->input->post('ket');
        if ($ket === 'Y') {
            $this->model->hapus('hak_setting', 'payment', $payment);
            $this->db_active->query("INSERT INTO hak_setting(guru_id, payment) SELECT guru_id, '$payment' FROM hak_setting WHERE guru_id IS NOT NULL GROUP BY guru_id");
        } else {
            $this->model->hapus('hak_setting', 'payment', $payment);
        }

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'settings berhasil diupdate');
            redirect('settings');
        } else {
            $this->session->set_flashdata('error', 'settings gagal diupdate');
            redirect('settings');
        }
    }

    public function updateInsentif()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses update insentif C: Settings');

        $honor_non = rmRp($this->input->post('honor_non', true));
        $honor_santri = rmRp($this->input->post('honor_santri', true));
        $honor_rami = rmRp($this->input->post('honor_rami', true));
        $honor_id = ($this->input->post('honor_id', true));


        $this->model->edit('settings', 'nama', 'honor_non', ['isi' => $honor_non]);
        $this->model->edit('settings', 'nama', 'honor_santri', ['isi' => $honor_santri]);
        $this->model->edit('settings', 'nama', 'honor_rami', ['isi' => $honor_rami]);

        if ($this->db_active->affected_rows() > 0) {
            // $this->session->set_flashdata('ok', 'settings nominal selesai diupdate');
            redirect('honor/updateNominal/' . $honor_id);
        } else {
            // $this->session->set_flashdata('ok', 'settings nominal selesai diupdate');
            redirect('honor/updateNominal/' . $honor_id);
        }
    }

    public function user()
    {
        $data['judul'] = 'Data User';
        $data['sub'] = 'settings';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('user')->result();

        $this->load->view('users', $data);
    }

    public function addUser()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses tambah user C; Settings');
        $nama = $this->input->post('nama', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $level = $this->input->post('level', true);
        $aktif = $this->input->post('aktif', true);

        $cek = $this->model->getBy('user', 'username', $username)->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'Maaf username sudah terpakai');
            redirect('settings/user');
        } else {
            $data = [
                'nama' => $nama,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'level' => $level,
                'aktif' => $aktif
            ];
            $this->model->tambah('user', $data);

            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'User berhasil ditambahkan');
                redirect('settings/user');
            } else {
                $this->session->set_flashdata('error', 'User gagal ditambahkan');
                redirect('settings/user');
            }
        }
    }
    public function updateUser()
    {
        $this->Auth_model->log_activity($this->userID, 'Akses tambah user C; Settings');
        $id = $this->input->post('id', true);
        $nama = $this->input->post('nama', true);
        $username = $this->input->post('username', true);
        $level = $this->input->post('level', true);
        $aktif = $this->input->post('aktif', true);

        $cek = $this->model->getBy2('user', 'username', $username, 'id_user !=', $id)->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'Maaf username sudah terpakai');
            redirect('settings/user');
        } else {
            $data = [
                'nama' => $nama,
                'username' => $username,
                'level' => $level,
                'aktif' => $aktif
            ];
            $this->model->edit('user', 'id_user', $id, $data);

            if ($this->db_active->affected_rows() > 0) {
                $this->session->set_flashdata('ok', 'User berhasil diperbarui');
                redirect('settings/user');
            } else {
                $this->session->set_flashdata('error', 'User gagal diperbarui');
                redirect('settings/user');
            }
        }
    }

    public function delUser($id)
    {
        $this->model->hapus('user', 'id_user', $id);

        if ($this->db_active->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'User berhasil dihapus');
            redirect('settings/user');
        } else {
            $this->session->set_flashdata('error', 'User gagal dihapus');
            redirect('settings/user');
        }
    }

    public function sinkron()
    {
        $data['judul'] = 'Sinkronisasi Data';
        $data['sub'] = 'settings';
        $data['user'] = $this->Auth_model->current_user();

        $data['data'] = $this->model->getData('sinkron')->result();

        $this->load->view('sinkron', $data);
    }

    public function sinc_satminkal()
    {
        $id = $this->input->post('id', TRUE);
        $cekdata = $this->model->getBy('sinkron', 'id', $id)->row();
        $url = $cekdata->url;
        $token = $this->token;

        $decoded = fetchApiGet($url, $token);
        $items = $decoded['data']['data'] ?? [];

        $saved = 0;
        foreach ($items as $item) {
            // cek apakah lembaga_id sudah ada
            $exists = $this->model->getBy('satminkal', 'id', $item['lembaga_id'])->row();

            $data = [
                'id' => $item['lembaga_id'],
                'nama'       => $item['nama'],
            ];

            if ($exists) {
                $this->db_active->where('id', $item['lembaga_id'])
                    ->update('satminkal', ['nama' => $item['nama']]);
            } else {
                $this->db_active->insert('satminkal', $data);
            }

            $saved++;
        }

        echo json_encode([
            'status' => 'success',
            'total'  => count($items),
            'saved'  => $saved
        ]);
    }
    public function sinc_golongan()
    {
        $id = $this->input->post('id', TRUE);
        $cekdata = $this->model->getBy('sinkron', 'id', $id)->row();
        $url = $cekdata->url;
        $token = $this->token;

        $decoded = fetchApiGet($url, $token);
        $items = $decoded['data']['data'] ?? [];

        $saved = 0;
        foreach ($items as $item) {
            // cek apakah lembaga_id sudah ada
            $exists = $this->model->getBy('golongan', 'id', $item['jenis_golongan_id'])->row();

            $data = [
                'id' => $item['jenis_golongan_id'],
                'nama'       => $item['nama'],
            ];

            if ($exists) {
                $this->db_active->where('id', $item['jenis_golongan_id'])
                    ->update('golongan', ['nama' => $item['nama']]);
            } else {
                $this->db_active->insert('golongan', $data);
            }

            $saved++;
        }

        echo json_encode([
            'status' => 'success',
            'total'  => count($items),
            'saved'  => $saved
        ]);
    }
    public function sinc_jabatan()
    {
        $id = $this->input->post('id', TRUE);
        $cekdata = $this->model->getBy('sinkron', 'id', $id)->row();
        $url = $cekdata->url;
        $token = $this->token;

        $decoded = fetchApiGet($url, $token);
        $items = $decoded['data']['data'] ?? [];

        $saved = 0;
        foreach ($items as $item) {
            // cek apakah lembaga_id sudah ada
            $exists = $this->model->getBy('jabatan', 'jabatan_id', $item['jenis_jabatan_id'])->row();

            $data = [
                'jabatan_id' => $item['jenis_jabatan_id'],
                'nama'       => $item['nama'],
            ];

            if ($exists) {
                $this->db_active->where('jabatan_id', $item['jenis_jabatan_id'])
                    ->update('jabatan', ['nama' => $item['nama']]);
            } else {
                $this->db_active->insert('jabatan', $data);
            }

            $saved++;
        }

        echo json_encode([
            'status' => 'success',
            'total'  => count($items),
            'saved'  => $saved
        ]);
    }
    public function sinc_guru()
    {
        $id = $this->input->post('id', TRUE);
        $cekdata = $this->model->getBy('sinkron', 'id', $id)->row();
        $url = $cekdata->url;
        $token = $this->token;

        $datas = fetchApiGet($url, $token);
        $items = $datas['data']['data'] ?? [];

        $saved = 0;
        foreach ($items as $item) {
            // cek apakah lembaga_id sudah ada
            $exists = $this->model->getBy('guru', 'nik', $item['nik'])->row();

            $data = [
                'nama' => $item['nama'],
                'nipy' => $item['niy'],
                'satminkal' => $item['niy'],
            ];

            if ($exists) {
                $this->db_active->where('jabatan_id', $item['ptk_id'])
                    ->update('jabatan', ['nama' => $item['nama']]);
            } else {
                $this->db_active->insert('jabatan', $data);
            }

            $saved++;
        }

        echo json_encode([
            'status' => 'success',
            'total'  => count($items),
            'saved'  => $saved
        ]);
    }

    public function setDb()
    {
        $db_id = $this->input->post('db_id', TRUE);
        $dbhasil = $this->db->query("SELECT * FROM list_db WHERE id = $db_id ")->row();

        $this->db->update('list_db', ['aktif' => 0]);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('ok', 'Pindah Database berhasi');
            $this->db->where('id', $db_id);
            $this->db->update('list_db', ['aktif' => 1]);

            $this->session->set_userdata('db_selected', $dbhasil->db_name);
            $this->session->set_userdata('db_name', $dbhasil->name);
            redirect('settings');
        } else {
            $this->session->set_flashdata('error', 'Pindah Database gagal');
            redirect('settings');
        }
    }
}
