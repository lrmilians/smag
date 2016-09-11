<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class pacmailer_subscribe_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_emails() {
        return $this->db->get("ebill_mailer_unsubscribe")->result_array();
    }
    
    public function subscribe_email($email) {
      return $this->db->delete('ebill_mailer_unsubscribe',array ('email'=>$email));
    }
}
