<?php

if (!defined('BASEPATH'))
exit('No direct script access allowed');
ini_set('display_errors', '1');

class pacmailer_subscribe extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("pacmailer_subscribe_model");
    }

    public function index() {
        $this->load->view('../../../client/app/index.html');
    }

    function getAllEmails() {
        echo $this->javascript->generate_json($this->pacmailer_subscribe_model->get_all_emails());
    }

    function subscribeEmail() {
        if ($this->pacmailer_subscribe_model->subscribe_email(
                $this->input->post('email')
                )) {
            echo $this->javascript->generate_json(array("code" => "success"));
        } else {
            echo $this->javascript->generate_json(array("code" => "failure"));
        }
    }

}
