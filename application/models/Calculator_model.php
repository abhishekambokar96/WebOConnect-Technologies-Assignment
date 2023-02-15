<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calculator_model extends CI_Model
{
    private $_calculations = 'calculations';
    public function __construct()
    {
        parent::__construct();
    }

    public function get_last_calculations()
    {
        $this->db->order_by('id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get($this->db->dbprefix($this->_calculations));
        return $query->result();
    }

    public function save_calculation($data)
    {
        return $this->db->insert($this->db->dbprefix($this->_calculations), $data);
    }

    public function delete_calculation($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->db->dbprefix($this->_calculations));
    }
}
