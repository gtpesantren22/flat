<?php

class Auth_model extends CI_Model
{
    private $_table = "user";
    const SESSION_KEY = 'id_user';

    public function rules()
    {
        return [
            [
                'field' => 'username',
                'label' => 'Username or Email',
                'rules' => 'required'
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required|max_length[255]'
            ]
        ];
    }

    public function login($username, $password)
    {
        $this->db->where('username', $username);
        $query = $this->db->get($this->_table);
        $user = $query->row();

        // cek apakah user sudah terdaftar?
        if (!$user) {
            return FALSE;
        }

        // cek apakah passwordnya benar?
        if (!password_verify($password, $user->password)) {
            return FALSE;
        }

        // cek apakah user aktif?
        if ($user->aktif === 'T') {
            return FALSE;
        }
        $this->log_activity($user->id_user, 'User logged in');
        // bikin session
        $this->session->set_userdata([self::SESSION_KEY => $user->id_user]);
        // $this->_update_last_login($user->id_user);

        return $this->session->has_userdata(self::SESSION_KEY);
    }

    public function current_user()
    {
        if (!$this->session->has_userdata(self::SESSION_KEY)) {
            return null;
        }

        $id_user = $this->session->userdata(self::SESSION_KEY);
        $query = $this->db->get_where($this->_table, ['id_user' => $id_user]);
        return $query->row();
    }

    public function logout()
    {
        $this->log_activity($this->session->userdata(self::SESSION_KEY), 'User Logout');
        $this->session->unset_userdata(self::SESSION_KEY);
        return !$this->session->has_userdata(self::SESSION_KEY);
    }

    // private function _update_last_login($id)
    // {
    //     $data = [
    //         'last_login' => date("Y-m-d H:i:s"),
    //     ];

    //     return $this->db->update($this->_table, $data, ['id' => $id]);
    // }

    function cekUser($str)
    {
        $this->db->where('username', $str);
        $this->db->from('user');
        return $this->db->get();
    }

    function tambah($table, $data)
    {
        $this->db->insert($table, $data);
    }

    public function log_activity($user_id, $activity)
    {
        $data = array(
            'user_id' => $user_id,
            'activity' => $activity
        );
        $this->db->insert('activity_logs', $data);
    }
}
