<?php

class Proxys_model extends CI_Model
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
    public function get_proxy_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('proxys');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetch proxys data from the database
     * possibility to mix search, filter and order
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_proxys($search_string = null, $order = null, $order_type = 'Asc', $limit_start = null, $limit_end = null, $in_work = false)
    {

        $this->db->select('*');
        $this->db->from('proxys');

        if ($search_string) {
            $this->db->like('pr_ip', $search_string);
        }
        $this->db->group_by('id');

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('id', $order_type);
        }

        if ($in_work) {
            $this->db->where('busy', 0);
            $this->db->where('status', 0);
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
    function count_proxys($search_string = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from('proxys');
        if ($search_string) {
            $this->db->like('pr_id', $search_string);
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
     * Store the new item into the database
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function store_proxys($data)
    {
        $insert = $this->db->insert('proxys', $data);
        return $insert;
    }

    /**
     * Update manufacture
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_proxy($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('proxys', $data);
        $report = array();
        $report['error'] = $this->db->_error_number();
        $report['message'] = $this->db->_error_message();
        if ($report !== 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete manufacturer
     * @param int $id - manufacture id
     * @return boolean
     */
    function delete_proxy($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('proxys');
    }

    function store_proxys_list($file_name)
    {
        if (!$f = fopen($file_name, 'r')) return FALSE;
        while (!feof($f)) {
            $row = fgets($f);
            $row = trim($row);
            $inf = explode(":", $row);
            if ($inf[0] != "") {
                $this->serverPing($inf);
            }
        }
        fclose($f);
        return true;
    }

    function reload_proxys_list()
    {
        $proxys = $this->get_proxys();

        if (count($proxys) > 0) {
            foreach ($proxys as $proxy) {
                $inf[0] = $proxy['pr_ip'];
                $inf[1] = $proxy['pr_port'];
                $inf[2] = $proxy['pr_login'];
                $inf[3] = $proxy['pr_pass'];
                $this->serverPing($inf);
            }
            return true;
        }
        return false;
    }

    private function serverPing($inf)
    {
        $data['pr_ip'] = $inf[0];
        $data['pr_port'] = $inf[1];
        $data['pr_login'] = $inf[2];
        $data['pr_pass'] = $inf[3];
        $data['errno'] = 0;
        $data['errmsg'] = '';

        $proxy_name = $data['pr_ip'];
        $proxy_port = $data['pr_port'];
        $proxy_user = $data['pr_login'];
        $proxy_pass = $data['pr_pass'];

        $ch = curl_init("http://ya.ru");
        curl_setopt($ch, CURLOPT_PROXY, $proxy_name);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($ch, CURLOPT_CONNECT_ONLY, 1);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_user . ':' . $proxy_pass);

        if (curl_exec($ch) !== FALSE) {
            $data['status'] = 0;
        } else {
            $data['status'] = 1;

        }

        $data['errmsg'] = curl_error($ch);
        $data['errno'] = 0;

        curl_close($ch);

        $this->db->select('id');
        $this->db->from('proxys');
        $this->db->like('pr_ip', $proxy_name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $this->db->where('id', $query->row()->id);
            $this->db->update('proxys', $data);
        } else {
            $this->db->insert('proxys', $data);
        }
    }

}