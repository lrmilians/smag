<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');
ini_set('display_errors', '1');

class pacmailer_unsubscribe extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("pacmailer_unsubscribe_model");
    }

    public function index() {
        $this->load->view('../../../client/app/index.html');
    }

    function getEmail() {
        echo $this->javascript->generate_json($this->pacmailer_unsubscribe_model->get_email(
                $this->input->post('email')
            ));
    }

    function addNewEmail() {
        if ($this->pacmailer_unsubscribe_model->add_new_email(
                $this->input->post('email'),
                $this->input->post('reason')
                )) {
            echo $this->javascript->generate_json(array("code" => "success"));
        } else {
            echo $this->javascript->generate_json(array("code" => "failure"));
        }
    }

}
