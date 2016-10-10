<?php

class Keys_model extends CI_Model
{

    /**
     * Responsable for auto load the database
     * @return void
     */
    public function __construct()
    {
        $this->load->database();
    }

    /**
     * Get product by his is
     * @param int $product_id
     * @return array
     */
    public function get_key_by_id($id)
    {
        $country = $this->session->userdata('country');
        $query = $this->db->get_where('countries', array('ID' => $country));
        $short_name = $query->row()->short_name;

        $this->db->select('*');
        $this->db->from('keys_' . $short_name);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Fetch countries data from the database
     * possibility to mix search, filter and order
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_keys($search_string = null, $country = 0, $order = null, $order_type = 'Asc', $limit_start = null, $limit_end = null)
    {
        $query = $this->db->get_where('countries', array('ID' => $country), 1);
        $this->db->select('k.*,count(kw.*) AS total_count');
        $this->db->from('keys_' . $query->row()->short_name . ' k');
        $this->db->join('keywords_' . $query->row()->short_name . ' kw', 'kw.parent = k.id', 'left');
        $this->db->group_by(array("k.id", "k.key", "k.status", "k.currency", "k.AMS", "k.competition", "k.suggested_bid", "k.time"));

        if ($search_string) {
            $this->db->like('k.key', $search_string);
        }

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('k.id', $order_type);
        }

        if ($limit_start && $limit_end) {
            $this->db->limit($limit_start, $limit_end);
        }

        if ($limit_start != null) {
            $this->db->limit($limit_start, $limit_end);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_keys($search_string = null, $country = 0, $compl = false, $order = null)
    {
        $query = $this->db->get_where('countries', array('ID' => $country), 1);
        $this->db->select('*');
        $this->db->from('keys_' . $query->row()->short_name);
        if ($search_string) {
            $this->db->like('key', $search_string);
        }
        if ($compl) {
            $this->db->where('status', 0);
        }
        if ($order) {
            $this->db->order_by($order, 'Asc');
        } else {
            $this->db->order_by('id', 'Asc');
        }
        $query = $this->db->get();
        return $query->num_rows();
    }


    /**
     * Update manufacture
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_country($id, $data)
    {
        $query = $this->db->get_where('countries', array('ID' => $id));
        $tbl_name = 'keys_' . $query->row()->short_name;
        $start_words_arr = explode("\n", $this->input->post('start_words'));
        $error = false;
        $this->db->query('BEGIN;SAVEPOINT spcountryUPD;');
        $this->db->where('ID', $id);
        $this->db->update('countries', $data);
        $report = array();
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();
        if ($report !== 0) {
            if (count($start_words_arr) > 0) {
                $multi_insert = array();
                foreach ($start_words_arr as $row) {
                    $multi_insert[] = array('key' => trim($row), 'status' => 1);
                }
                if (!$this->db->insert_batch($tbl_name, $multi_insert)) {
                    $error = true;
                }
            }
        } else {
            $error = true;
        }
        if ($error) {
            $this->db->query('ROLLBACK TO SAVEPOINT spcountryUPD;');
        } else {
            $this->db->query('COMMIT;');
        }
        return !$error;
    }

    /**
     * Delete manufacturer
     * @param int $id - manufacture id
     * @return boolean
     */
    function delete_key($id)
    {
        $country = $this->session->userdata('country');
        $query = $this->db->get_where('countries', array('ID' => $country));
        $short_name = $query->row()->short_name;
        $this->db->where('id', $id);
        $this->db->delete('keys_' . $short_name);
        $this->db->where('parent', $id);
        $this->db->delete('keywords_' . $short_name);
    }

    function get_keywords($search_string = null, $key_id = 0, $order = null, $order_type = 'Asc', $limit_start = null, $limit_end = null)
    {
        $country = $this->session->userdata('country');
        $query = $this->db->get_where('countries', array('ID' => $country));
        $short_name = $query->row()->short_name;

        $this->db->select('*');
        $this->db->from('keywords_' . $short_name);

        /*if($search_string){
            $this->db->like('full_name', $search_string);
        }*/
        //$this->db->group_by('key');

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('id', $order_type);
        }

        if ($limit_start && $limit_end) {
            $this->db->limit($limit_start, $limit_end);
        }

        if ($limit_start != null) {
            $this->db->limit($limit_start, $limit_end);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    function get_keywords_count($search_string = null, $key_id = 0)
    {
        $country = $this->session->userdata('country');
        $query = $this->db->get_where('countries', array('ID' => $country));
        $short_name = $query->row()->short_name;

        $this->db->select('count(*) OVER() AS total_count');
        $this->db->where('parent', $key_id);
        $this->db->from('keywords_' . $short_name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->total_count;
        }
        return 0;
    }

}