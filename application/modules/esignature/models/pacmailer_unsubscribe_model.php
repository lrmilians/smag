<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class pacmailer_unsubscribe_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_email($email) {
        $this->db->where("email", $email);
        return $this->db->get("ebill_mailer_unsubscribe")->result_array();
    }
    
    public function add_new_email($mail, $reason) {
        $data = array(
            'email' => $mail,
            'reason' => $reason
            );
        return $this->db->insert("ebill_mailer_unsubscribe", $data);
    }
}
