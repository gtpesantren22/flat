<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modeldata extends CI_Model
{
    protected $db_active;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Dynamic_db'); // load dulu
        $this->db_active = $this->dynamic_db->connect(); // baru panggil method connect()
    }

    public function tambah($table, $data)
    {
        $this->db_active->insert($table, $data);
    }

    public function getData($table)
    {
        return $this->db_active->get($table);
    }
    public function hapus($table, $where, $dtwhere)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->delete($table);
    }
    public function hapus2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->delete($table);
    }
    public function hapus3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        $this->db_active->delete($table);
    }
    public function edit($table, $where, $dtwhere, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->update($table, $data);
    }
    public function edit2($table, $where, $dtwhere, $where2, $dtwhere2, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->update($table, $data);
    }
    public function edit3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3, $data)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        $this->db_active->update($table, $data);
    }
    public function getBy($table, $where, $dtwhere)
    {
        $this->db_active->where($where, $dtwhere);
        return $this->db_active->get($table);
    }
    public function getBy2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        return $this->db_active->get($table);
    }
    public function getBy3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db_active->where($where, $dtwhere);
        $this->db_active->where($where2, $dtwhere2);
        $this->db_active->where($where3, $dtwhere3);
        return $this->db_active->get($table);
    }
    public function getBySelect($table, $where, $dtwhere, $select)
    {
        $this->db_active->select($select);
        $this->db_active->where($where, $dtwhere);
        return $this->db_active->get($table);
    }
    public function getGroup($table, $groupby)
    {
        $this->db_active->group_by($groupby);
        return $this->db_active->get($table);
    }
    public function getOrder($table, $orderby, $list)
    {
        $this->db_active->order_by($orderby, $list);
        return $this->db_active->get($table);
    }
    public function getOrder2($table, $orderby, $list, $orderby2, $list2)
    {
        $this->db_active->order_by($orderby, $list);
        $this->db_active->order_by($orderby2, $list2);
        return $this->db_active->get($table);
    }
}
