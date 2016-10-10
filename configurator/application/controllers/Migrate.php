<?php
/**
 * Created by PhpStorm.
 * Author: Arioshkin Evgeniy
 * Date: 09.10.16
 * Time: 16:25
 */

defined("BASEPATH") or exit("No direct script access allowed");

class Migrate extends CI_Controller{

    public function index($version){
        $this->load->library("migration");

        if(!$this->migration->version($version)){
            show_error($this->migration->error_string());
        }
    }

    public function last(){
        $this->load->library("migration");

        if(!$this->migration->last()){
            show_error($this->migration->error_string());
        }
    }
}