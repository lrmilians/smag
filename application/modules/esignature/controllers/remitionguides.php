<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
   // ini_set('display_errors', '1');

class remitionguides extends MY_Controller {

    public function __construct() {
        parent::__construct();     
        $this->load->library('connection');
        $this->load->model("remitionguide_model"); 
    }
    
    public function index() {
        $this->load->view('../../../client/app/index.html');
    }   
    
    public function getAllRemitionguides($name_db){  
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->remitionguide_model->get_all_remitionguides($db));
    }
    
    public function getMessagesByRemitionguideName($name_db, $remitionguide_name) {
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->remitionguide_model->getMessageByRemitionguideName($db, $remitionguide_name));
    }
    
}