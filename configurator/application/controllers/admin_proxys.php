<?php

class Admin_proxys extends CI_Controller
{

    /**
     * name of the folder responsible for the views
     * which are manipulated by this controller
     * @constant string
     */
    const VIEW_FOLDER = 'admin/proxys';

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('proxys_model');

        if (!$this->session->userdata('is_logged_in')) {
            redirect('admin/login');
        }
    }

    /**
     * Load the main view with all the current model model's data.
     * @return void
     */
    public function index()
    {
        $this->_packet();
        if ($resultPacket = $this->session->userdata('uplErr')) {
            $data['flash_message'] = $resultPacket;
        }
        $this->session->unset_userdata('uplErr');
        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 20;

        $config['base_url'] = base_url() . 'admin/proxys';
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        //limit end
        $page = $this->uri->segment(3);

        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }

        //if order type was changed
        if ($order_type) {
            $filter_session_data['order_type'] = $order_type;
        } else {
            //we have something stored in the session? 
            if ($this->session->userdata('order_type')) {
                $order_type = $this->session->userdata('order_type');
            } else {
                //if we have nothing inside session, so it's the default "Asc"
                $order_type = 'Asc';
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type;


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if ($search_string !== false && $order !== false || $this->uri->segment(3) == true) {

            /*
            The comments here are the same for line 79 until 99

            if post is not null, we store it in session data array
            if is null, we use the session data already stored
            we save order into the the var to load the view with the param already selected       
            */
            /*if($search_string){
                $filter_session_data['search_string_selected'] = $search_string;
            }else{
                $search_string = $this->session->userdata('search_string_selected');
            }*/
            $data['search_string_selected'] = $search_string;

            if ($order) {
                $filter_session_data['order'] = $order;
            } else {
                $order = $this->session->userdata('order');
            }
            $data['order'] = $order;

            //save session data into the session
            if (isset($filter_session_data)) {
                $this->session->set_userdata($filter_session_data);
            }

            //fetch sql data into arrays
            $data['count_products'] = $this->proxys_model->count_proxys($search_string, $order);
            $config['total_rows'] = $data['count_products'];

            //fetch sql data into arrays
            if ($search_string) {
                if ($order) {
                    $data['proxys'] = $this->proxys_model->get_proxys($search_string, $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['proxys'] = $this->proxys_model->get_proxys($search_string, '', $order_type, $config['per_page'], $limit_end);
                }
            } else {
                if ($order) {
                    $data['proxys'] = $this->proxys_model->get_proxys('', $order, $order_type, $config['per_page'], $limit_end);
                } else {
                    $data['proxys'] = $this->proxys_model->get_proxys('', '', $order_type, $config['per_page'], $limit_end);
                }
            }

        } else {
            //clean filter data inside section
            $filter_session_data['proxy_selected'] = null;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['order'] = 'id';

            //fetch sql data into arrays
            $data['count_products'] = $this->proxys_model->count_proxys();
            $data['proxys'] = $this->proxys_model->get_proxys('', '', $order_type, $config['per_page'], $limit_end);
            $config['total_rows'] = $data['count_products'];

        }//!isset($search_string) && !isset($order)

        //initializate the panination helper 
        $this->pagination->initialize($config);

        //load the view
        $data['main_content'] = 'admin/proxys/list';
        $this->load->view('includes/template', $data);

    }//index

    private function _packet()
    {

        if ($this->input->server('REQUEST_METHOD') === 'POST' AND $this->input->post('btnUplFile') == 'Обработка...') {

            //if the form has passed through the validation
            if (is_uploaded_file($_FILES["uplFile"]["tmp_name"])) {
                if (mb_strtolower(end(explode(".", $_FILES["uplFile"]['name']))) !== 'txt') {
                    $return = array('msg' => 'Ошибка! Расширение файла должно быть .txt', 'status' => 'error');
                    $this->session->set_userdata(array('uplErr' => $return));
                    redirect('/admin/proxys');
                }
                if (move_uploaded_file($_FILES["uplFile"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . '/tempFiles/' . $_FILES["uplFile"]["name"])) {
                    //if the insert has returned true then we show the flash message
                    if ($this->proxys_model->store_proxys_list($_SERVER['DOCUMENT_ROOT'] . '/tempFiles/' . $_FILES["uplFile"]["name"])) {
                        $return = array('msg' => 'Список успешно обновлен', 'status' => 'ok');
                    } else {
                        $return = array('msg' => 'Ошибка в файле или БД', 'status' => 'error');
                    }
                } else {
                    $return = array('msg' => 'Ошибка! Файл не доступен на сервере', 'status' => 'error');
                }

            } else {
                $return = array('msg' => 'Ошибка! Файл не выбран или ошибка загрузки на сервер', 'status' => 'error');
            }

            $this->session->set_userdata(array('uplErr' => $return));
            redirect('/admin/proxys');
        }
        return array('msg' => '', 'status' => false);
    }

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('pr_ip', 'IP адрес', 'required');
            $this->form_validation->set_rules('pr_port', 'Порт', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');


            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'pr_ip' => $this->input->post('pr_ip'),
                    'pr_port' => $this->input->post('pr_port'),
                    'pr_login' => $this->input->post('pr_login'),
                    'pr_pass' => $this->input->post('pr_pass'),
                    'status' => $this->input->post('status'),
                );
                //if the insert has returned true then we show the flash message
                if ($this->proxys_model->store_proxys($data_to_store)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }

            }

        }
        //load the view
        $data['main_content'] = 'admin/proxys/add';
        $this->load->view('includes/template', $data);
    }

    /**
     * Update item by his id
     * @return void
     */
    public function update()
    {
        //product id 
        $id = $this->uri->segment(4);

        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form validation
            $this->form_validation->set_rules('pr_ip', 'IP адрес', 'required');
            $this->form_validation->set_rules('pr_port', 'Порт', 'required');

            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run()) {

                $data_to_store = array(
                    'pr_ip' => $this->input->post('pr_ip'),
                    'pr_port' => $this->input->post('pr_port'),
                    'pr_login' => $this->input->post('pr_login'),
                    'pr_pass' => $this->input->post('pr_pass'),
                    'status' => $this->input->post('status'),
                );
                //if the insert has returned true then we show the flash message
                if ($this->proxys_model->update_proxy($id, $data_to_store) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/proxys/update/' . $id . '');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data 
        $data['proxy'] = $this->proxys_model->get_proxy_by_id($id);
        //load the view
        $data['main_content'] = 'admin/proxys/edit';
        $this->load->view('includes/template', $data);

    }//update

    /**
     * Delete product by his id
     * @return void
     */
    public function delete()
    {
        $id = $this->uri->segment(4);
        $this->proxys_model->delete_proxy($id);
        redirect('admin/proxys');
    }

    public function reload()
    {
        if ($this->proxys_model->reload_proxys_list()) {
            $return = array('msg' => 'OK! Статусы успешно обновлены', 'status' => 'ok');
        } else {
            $return = array('msg' => 'Ошибка! Не удалось проверить прокси-серверы', 'status' => 'error');
        }
        $this->session->set_userdata(array('uplErr' => $return));
        redirect('/admin/proxys');
    }

}