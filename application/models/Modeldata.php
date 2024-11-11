<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modeldata extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function tambah($table, $data)
    {
        $this->db->insert($table, $data);
    }

    public function getData($table)
    {
        return $this->db->get($table);
    }
    public function hapus($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        $this->db->delete($table);
    }
    public function hapus2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->delete($table);
    }
    public function  edit($table, $where, $dtwhere, $data)
    {
        $this->db->where($where, $dtwhere);
        $this->db->update($table, $data);
    }
    public function getBy($table, $where, $dtwhere)
    {
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }
    public function getBy2($table, $where, $dtwhere, $where2, $dtwhere2)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        return $this->db->get($table);
    }
    public function getBy3($table, $where, $dtwhere, $where2, $dtwhere2, $where3, $dtwhere3)
    {
        $this->db->where($where, $dtwhere);
        $this->db->where($where2, $dtwhere2);
        $this->db->where($where3, $dtwhere3);
        return $this->db->get($table);
    }
    public function getBySelect($table, $where, $dtwhere, $select)
    {
        $this->db->select($where);
        $this->db->where($where, $dtwhere);
        return $this->db->get($table);
    }
    public function getGroup($table, $groupby)
    {
        $this->db->group_by($groupby);
        return $this->db->get($table);
    }
    public function getOrder($table, $orderby, $list)
    {
        $this->db->order_by($orderby, $list);
        return $this->db->get($table);
    }
}
