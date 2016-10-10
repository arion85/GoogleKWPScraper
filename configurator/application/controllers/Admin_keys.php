<?php

class Admin_keys extends CI_Controller
{

    /**
     * name of the folder responsible for the views
     * which are manipulated by this controller
     * @constant string
     */
    const VIEW_FOLDER = 'admin/keys';

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('keys_model');
        $this->load->model('countries_model');

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

        //all the posts sent by the view
        $search_string = $this->input->post('search_string');
        $country = $this->input->post('country');
        $order = $this->input->post('order');
        $order_type = $this->input->post('order_type');

        //pagination settings
        $config['per_page'] = 25;

        $config['base_url'] = base_url() . 'admin/keys';
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

        //if order type was changed
        /*if($country == $this->session->userdata('country')){
            $filter_session_data['country'] = $country;

        }elseif($country!=0 AND $this->session->userdata('country')==0){
            $filter_session_data['country'] = $country;
        }elseif($country==0){
            $filter_session_data['country'] = 0;
        }else{
            $filter_session_data['country'] = $this->session->userdata('country');
            $country = $this->session->userdata('country');
        }*/
        if ($country != 0 AND $country != $this->session->userdata('country')) {
            $filter_session_data['country'] = $country;
            $data['country_selected'] = $country;
        } else {
            $country = $this->session->userdata('country');
            $filter_session_data['country'] = $this->session->userdata('country');
            $data['country_selected'] = $this->session->userdata('country');
        }

        //make the data type var avaible to our view


        //we must avoid a page reload with the previous session data
        //if any filter post was sent, then it's the first time we load the content
        //in this case we clean the session filter data
        //if any filter post was sent but we are in some page, we must load the session data

        //filtered && || paginated
        if ($country != 0) {
            if ($search_string !== false || $order !== false || $this->uri->segment(3) == true) {

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
                $data['count_products'] = 250;
                $config['total_rows'] = $data['count_products'];

                //fetch sql data into arrays
                if ($search_string) {
                    if ($order) {
                        $data['keys'] = $this->keys_model->get_keys($search_string, $country, $order, $order_type, $config['per_page'], $limit_end);
                    } else {
                        $data['keys'] = $this->keys_model->get_keys($search_string, $country, '', $order_type, $config['per_page'], $limit_end);
                    }
                } else {
                    if ($order) {
                        $data['keys'] = $this->keys_model->get_keys('', $country, $order, $order_type, $config['per_page'], $limit_end);
                    } else {
                        $data['keys'] = $this->keys_model->get_keys('', $country, '', $order_type, $config['per_page'], $limit_end);
                    }

                    $data['count_keys'] = $this->keys_model->count_keys('', $country);
                    $data['count_coml_keys'] = $this->keys_model->count_keys('', $country, true);
                }
            } else {
                $data['keys'] = $this->keys_model->get_keys('', $country, '', $order_type, $config['per_page'], $limit_end);
                //clean filter data inside section
                //$filter_session_data['country_selected'] = null;
                $filter_session_data['search_string_selected'] = null;
                //$filter_session_data['country_selected'] = 0;
                $filter_session_data['order'] = null;
                $filter_session_data['order_type'] = null;
                $this->session->set_userdata($filter_session_data);

                //pre selected options
                $data['search_string_selected'] = '';
                $data['country_selected'] = $country;
                $data['order'] = 'id';

                //fetch sql data into arrays
                $data['count_keys'] = $this->keys_model->count_keys('', $country);
                $data['count_coml_keys'] = $this->keys_model->count_keys('', $country, true);
                $config['total_rows'] = 250;
            }
        } else {
            //clean filter data inside section
            $filter_session_data['country_selected'] = 0;
            $filter_session_data['search_string_selected'] = null;
            $filter_session_data['country_selected'] = 0;
            $filter_session_data['order'] = null;
            $filter_session_data['order_type'] = null;
            $this->session->set_userdata($filter_session_data);

            //pre selected options
            $data['search_string_selected'] = '';
            $data['country_selected'] = 0;
            $data['order'] = 'id';
            $data['count_keys'] = 0;
            $data['count_coml_keys'] = 0;

            $data['keys'] = false;
            $config['total_rows'] = 0;

        }//!isset($search_string) && !isset($order)

        //initializate the panination helper
        $this->pagination->initialize($config);

        //load the view
        $data['main_content'] = 'admin/keys/list';
        $data['countries'] = $this->countries_model->get_countries();
        $this->load->view('includes/template', $data);

    }//index

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('full_name', 'Полное название', 'required');
            $this->form_validation->set_rules('short_name', 'Короткое название', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');


            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'full_name' => $this->input->post('full_name'),
                    'short_name' => $this->input->post('short_name'),
                );
                //if the insert has returned true then we show the flash message
                if ($this->countries_model->store_countries($data_to_store)) {
                    $data['flash_message'] = TRUE;
                } else {
                    $data['flash_message'] = FALSE;
                }

            }

        }
        //load the view
        $data['main_content'] = 'admin/countries/add';
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
            $this->form_validation->set_rules('full_name', 'Полное название', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');
            //if the form has passed through the validation
            if ($this->form_validation->run()) {

                $data_to_store = array(
                    'full_name' => $this->input->post('full_name'),
                );
                //if the insert has returned true then we show the flash message
                if ($this->countries_model->update_country($id, $data_to_store) == TRUE) {
                    $this->session->set_flashdata('flash_message', 'updated');
                } else {
                    $this->session->set_flashdata('flash_message', 'not_updated');
                }
                redirect('admin/countries/update/' . $id . '');

            }//validation run

        }

        //if we are updating, and the data did not pass trough the validation
        //the code below wel reload the current data

        //product data
        $data['country'] = $this->countries_model->get_country_by_id($id);
        //load the view
        $data['main_content'] = 'admin/countries/edit';
        $this->load->view('includes/template', $data);

    }//update

    /**
     * Delete product by his id
     * @return void
     */
    public function delete()
    {
        //product id
        $id = $this->uri->segment(4);
        $this->keys_model->delete_key($id);
        redirect('admin/keys');
    }//edit

    public function keywords()
    {

        $order = $this->input->post('order');
        $order_type_keywords = $this->input->post('order_type_keywords');

        $key_id = $this->uri->segment(4);
        //limit end
        $page = $this->uri->segment(5);

        //pagination settings
        $config['per_page'] = 20;

        $config['base_url'] = base_url() . 'admin/keys/keywords/' . $key_id;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 20;
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['uri_segment'] = 5;


        //math to get the initial record to be select in the database
        $limit_end = ($page * $config['per_page']) - $config['per_page'];
        if ($limit_end < 0) {
            $limit_end = 0;
        }

        //if order type was changed
        if ($order_type_keywords) {
            $filter_session_data['order_type_keywords'] = $order_type_keywords;
        } else {
            //we have something stored in the session?
            if ($this->session->userdata('order_type_keywords')) {
                $order_type_keywords = $this->session->userdata('order_type_keywords');
            } else {
                //if we have nothing inside session, so it's the default "Asc"
                $order_type_keywords = 'Desc';
            }
        }
        //make the data type var avaible to our view
        $data['order_type_selected'] = $order_type_keywords;

        if ($order) {
            $filter_session_data['order'] = $order;
        } else {
            if ($this->session->userdata('order')) {
                $order = $this->session->userdata('order');
            } else {
                $order = 'AMS';
            }
        }
        $data['order'] = $order;

        if (isset($filter_session_data) AND count($filter_session_data) > 0)
            $this->session->set_userdata($filter_session_data);

        $config['total_rows'] = $this->keys_model->get_keywords_count('', $key_id);

        $data['total_rows'] = $config['total_rows'];
        $data['keywords'] = $this->keys_model->get_keywords('', $key_id, $order, $order_type_keywords, $config['per_page'], $limit_end);
        $data['key_id'] = $key_id;
        $data['key'] = $this->keys_model->get_key_by_id($key_id)->key;

        $this->pagination->initialize($config);

        $data['main_content'] = 'admin/keys/keywords';
        $this->load->view('includes/template', $data);
    }

}