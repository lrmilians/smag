<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('display_errors', '1');

class pacmailer extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("pacmailer_model");
    }

    public function index() {
        $this->load->view('../../../client/app/index.html');
    }

    function getConfigurationValues() {
        echo $this->javascript->generate_json($this->pacmailer_model->get_configuration_values());
    }

    function setConfigurationValues() {
        if ($this->pacmailer_model->set_configuration_values(
                $this->input->post('from'),
                $this->input->post('subject'),
                $this->input->post('body'),
                $this->input->post('host'),
                $this->input->post('port'),
                $this->input->post('auth'),
                $this->input->post('username'),
                $this->input->post('password'),
                $this->input->post('ssl'),
                $this->input->post('path')
                )) {
            echo $this->javascript->generate_json(array("code" => "success"));
        } else {
            echo $this->javascript->generate_json(array("code" => "failure"));
        }
    }

}
