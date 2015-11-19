<?php

/**
 * Created by PhpStorm.
 * User: arion
 * Date: 25.03.15
 * Time: 18:49
 */
class Admin_main extends CI_Controller
{
    const VIEW_FOLDER = 'admin/main';

    /**
     * Responsable for auto load the model
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('main_model');
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
        $config['base_url'] = base_url() . 'admin/params';
        $config['full_tag_open'] = '<ul>';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $data['params'] = $this->main_model->get_all_params();

        if ($resultPacket = $this->session->userdata('uplErr')) {
            $data['flash_message'] = $resultPacket;
        }
        $this->session->unset_userdata('uplErr');

        $data['countries'] = $this->countries_model->get_countries();
        $data['main_content'] = 'admin/main';
        $this->load->view('includes/template', $data);
    }

    public function add()
    {
        //if save button was clicked, get the data sent via post
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form validation
            $this->form_validation->set_rules('thr_cnt', 'Количество потоков', 'required');
            $this->form_validation->set_rules('country_id', 'Страна', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a><strong>', '</strong></div>');


            //if the form has passed through the validation
            if ($this->form_validation->run()) {
                $data_to_store = array(
                    'thr_cnt' => $this->input->post('thr_cnt'),
                    'status' => $this->input->post('status'),
                    'country_id' => $this->input->post('country_id'),
                );
                if ($this->input->post('status') == 0) {
                    $data_to_store['last_time'] = 0;
                }
                //if the insert has returned true then we show the flash message
                if ($this->main_model->update_params($data_to_store)) {
                    if ($this->input->post('status') == 1) {
                        exec('php -q --no-header /home/arion/Projects/PHP/graber/html/startDaemon.php < /dev/null > /home/arion/Projects/PHP/graber/html/script.log &');
                    }
                    $return = TRUE;
                } else {
                    $return = FALSE;
                }

            }

        }
        $this->session->set_userdata(array('uplErr' => $return));
        redirect('/admin/main');
    }
}