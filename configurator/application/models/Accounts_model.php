<?php

class Accounts_model extends CI_Model
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
    public function get_account_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('accounts');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Fetch accounts data from the database
     * possibility to mix search, filter and order
     * @param int $manufacuture_id
     * @param string $search_string
     * @param strong $order
     * @param string $order_type
     * @param int $limit_start
     * @param int $limit_end
     * @return array
     */
    public function get_accounts($search_string = null, $order = null, $order_type = 'Asc', $limit_start, $limit_end)
    {
        $this->db->select('*');
        $this->db->from('accounts');

        if ($search_string) {
            $this->db->like('gm_login', $search_string);
        }

        $this->db->group_by('id');

        if ($order) {
            $this->db->order_by($order, $order_type);
        } else {
            $this->db->order_by('id', $order_type);
        }


        $this->db->limit($limit_start, $limit_end);
        //$this->db->limit('4', '4');


        $query = $this->db->get();

        return $query->result_array();
    }

    /**
     * Count the number of rows
     * @param int $manufacture_id
     * @param int $search_string
     * @param int $order
     * @return int
     */
    function count_accounts($search_string = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from('accounts');

        if ($search_string) {
            $this->db->like('gm_login', $search_string);
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
    function store_account($data)
    {
        $insert = $this->db->insert('accounts', $data);
        return $insert;
    }

    /**
     * Update product
     * @param array $data - associative array with data to store
     * @return boolean
     */
    function update_account($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('accounts', $data);
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
     * Delete product
     * @param int $id - product id
     * @return boolean
     */
    function delete_account($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('accounts');
    }

    function store_accounts_list($file_name)
    {
        $objReader = PHPExcel_IOFactory::createReaderForFile($file_name);
        $objReader->setReadDataOnly(true);

        $objPHPExcel = $objReader->load($file_name);

        $objPHPExcel->getSheet(0);

        $aSheet = $objPHPExcel->getActiveSheet();

        $array = array();
        //получим итератор строки и пройдемся по нему циклом
        foreach ($aSheet->getRowIterator() as $row) {
            //получим итератор ячеек текущей строки
            $cellIterator = $row->getCellIterator();
            //пройдемся циклом по ячейкам строки
            //этот массив будет содержать значения каждой отдельной строки
            $item = array();
            foreach ($cellIterator as $cell) {
                //заносим значения ячеек одной строки в отдельный массив
                array_push($item, iconv('utf-8', 'cp1251', $cell->getValue()));
            }
            //заносим массив со значениями ячеек отдельной строки в "общий массв строк"
            array_push($array, $item);
        }
        if (isset($array[0])) {
            unset($array[0]);
            $array = array_filter($array);
            $array = array_values($array);
        }
        if (count($array) > 0) $this->checkAccounts($array);
        return true;
    }

    private function checkAccounts($accs)
    {
        $this->load->helper('file');

        $query = $this->db->get_where('proxys', array('status' => 0, 'busy' => 0));

        if ($query->num_rows > 0) {
            $res = $query->result_array();
            $country_id = $this->input->post('country_id');
            foreach ($accs as $account) {
                $acc_query = $this->db->get_where('accounts', array('gm_login' => $account[0]));
                if ($acc_query->num_rows() > 0) {

                    $upd_data = array('status' => 0, 'proxy_ip' => $proxy['id']);
                    $this->db->update('accounts', $upd_data, "id = " . $acc_query->row()->id);

                } else {
                    $proxy = array_pop($res);
                    $ins_data = array(
                        'gm_login' => $account[0],
                        'gm_pass' => $account[1] . '1',
                        'gm_tel' => $account[3],
                        'gm_recovery_email' => $account[2],
                        'status' => 0,
                        'proxy_ip' => $proxy['id'],
                        'country_id' => $country_id,);
                    $this->db->insert('accounts', $ins_data);

                    $this->db->update('proxys', array('busy' => 1), "id = " . $proxy['id']);
                }
            }
        }
    }
}