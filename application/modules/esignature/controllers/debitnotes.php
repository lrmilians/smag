<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    ini_set('display_errors', '1');

class debitnotes extends MY_Controller {

    public function __construct() {
        parent::__construct();      
        $this->load->library('connection');
        $this->load->model("debitnote_model"); 
    }
    
    public function index() {
        $this->load->view('../../../client/app/index.html');
    }   
    
    public function getAllDebitNotes($name_db) {   
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->debitnote_model->get_all_debitnotes($db));
    }
    
    public function getMessagesByDebitnoteName($name_db, $debitnote_name) {
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->debitnote_model->getMessageByDebitnoteName($db, $debitnote_name));
    }
    
}