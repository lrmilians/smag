<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class pacmailer_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_configuration_values() {
        return $this->db->get("ebill_mailer")->result_array();
    }
    
    public function set_configuration_values($from, $subject, $body, $host, $port, $auth, $username, $password,$ssl,$path) {
        $data = array(
            'from' => $from,
            'subject' => $subject,
            'body' => $body,
            'host' => $host,
            'port' => $port,
            'auth' => $auth,
            'username' => $username,
            'password' => $password,
            'ssl' => $ssl,
            'path' => $path  
            );
        $this->db->where("id", 1);
        return $this->db->update("ebill_mailer", $data);
    }
}
