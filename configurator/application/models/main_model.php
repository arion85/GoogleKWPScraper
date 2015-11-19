<?php

class Main_model extends CI_Model
{

    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct()
    {
        $this->load->database();
    }

    public function get_all_params()
    {

        $query = $this->db->get('params', 1, 0);
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();

        return $query->row();
    }

    function update_params($data)
    {
        $this->db->select('id');
        $this->db->from('params');
        $this->db->limit(1, 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $id = $query->row()->id;
        } else {
            $id = false;
        }


        if ($id) {
            $this->db->where('id', $id);
            $this->db->update('params', $data);
            $report = array();
            $report['error'] = $this->db->_error_number();
            $report['message'] = $this->db->_error_message();
            if ($report !== 0) {
                return true;
            } else {
                return false;
            }
        } else {
            $insert = $this->db->insert('params', $data);
            return $insert;
        }

    }
}