<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    ini_set('display_errors', '1');

class retentions extends MY_Controller {

    public function __construct() {
        parent::__construct();   
        $this->load->library('connection');
        $this->load->model("retention_model"); 
    }
    
    public function index() {
        $this->load->view('../../../client/app/index.html');
    }   
    
    public function getAllRetentions($name_db) {  
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->retention_model->get_all_retentions($db));
    }
    
    public function getMessagesByRetentionName($name_db, $retention_name) {
        $db_settings = $this->connection->change($name_db);
        $db = $this->load->database($db_settings, true);
        echo $this->javascript->generate_json($this->retention_model->getMessageByRetentionName($db, $retention_name));
    }
    
}