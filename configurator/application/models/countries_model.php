<?php

class Countries_model extends CI_Model
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
    public function get_country_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('countries');
        $this->db->where('ID', $id);
        $query = $this->db->get();
        return $query->result_array();
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
    public function get_countries($search_string = null, $order = null, $order_type = 'Asc', $limit_start = null, $limit_end = null)
    {

        $this->db->select('*');
        $this->db->from('countries');

        if ($search_string) {
            $this->db->like('full_name', $search_string);
        }
        $this->db->group_by('ID');

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('ID', $order_type);
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
    function count_countries($search_string = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from('countries');
        if ($search_string) {
            $this->db->like('full_name', $search_string);
        }
        if ($order) {
            $this->db->order_by($order, 'Asc');
        } else {
            $this->db->order_by('ID', 'Asc');
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * Store the new item into the database
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function store_countries($data)
    {
        $start_words_arr = explode("\n", $this->input->post('start_words'));

        $insert = $this->db->insert('countries', $data);

        if ($insert) {
            if (isset($start_words_arr)) {

                $new_keys_insert = array();
                foreach ($start_words_arr as $row) {
                    if ($row == "") continue;
                    $new_keys_insert[] = trim($row);
                }

                $redis = new Redis();
                $redis->connect('127.0.0.1');
                foreach ($new_keys_insert as $insert) {

                    $redis->sAdd("keys_status:{$data['short_name']}:1", strtolower($insert));

                }
                $redis->close();
            }
        }

        return $insert;
    }

    /**
     * Update manufacture
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_country($id, $data)
    {
        $query = $this->db->get_where('countries', array('ID' => $id));
        $short_name = $query->row()->short_name;
        $start_words_arr = explode("\n", $this->input->post('start_words'));
        $error = false;
        $this->db->query('BEGIN;SAVEPOINT spcountryUPD;');
        $this->db->where('ID', $id);
        $this->db->update('countries', $data);
        $report = array();
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();
        if ($report['error'] == '') {
            if (isset($start_words_arr)) {
                $new_keys_insert = array();
                foreach ($start_words_arr as $row) {
                    if ($row == "") continue;
                    $new_keys_insert[] = trim($row);
                }
                if (isset($new_keys_insert) AND count($new_keys_insert) > 0) {
                    $redis = new Redis();
                    $redis->connect('127.0.0.1');
                    foreach ($new_keys_insert as $insert) {

                        $redis->sAdd("keys_status:{$short_name}:1", strtolower($insert));

                    }
                    $redis->close();
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
    function delete_country($id)
    {
        $query = $this->db->get_where('countries', array('ID' => $id));
        $short_name = $query->row()->short_name;
        $this->db->query("DROP TABLE IF EXISTS keys_{$short_name} CASCADE");
        $this->db->query("DROP TABLE IF EXISTS keywords_{$short_name} CASCADE");
        $this->db->where('ID', $id);
        $this->db->delete('countries');
    }

}