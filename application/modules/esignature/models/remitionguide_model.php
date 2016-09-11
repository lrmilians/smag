<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class remitionguide_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_remitionguides($db) {
        $db->order_by("id", "desc");
        return $db->get("ebill_signing_remitionguides")->result_array();
    }
    
    public function getMessageByRemitionguideName($db, $remitionguide_name){
        $remitionguide = $this->getRemitionguideByName($db, $remitionguide_name);
        $db->select('ebill_signing_sri_validations.code, '
                . 'ebill_signing_sri_validations.code_type, '
                . 'ebill_signing_sri_validations.description, '
                . 'ebill_signing_remitionguide_messages.additional_info, '
                . 'ebill_signing_sri_validations.possible_solution');
        $db->from('ebill_signing_remitionguide_messages');
        $db->join('ebill_signing_sri_validations', 'ebill_signing_sri_validations.code = ebill_signing_remitionguide_messages.code');
        $db->where('ebill_signing_remitionguide_messages.remitionguide_id', $remitionguide->id); 
        return $db->get()->result_array();
    } 
    
    public function getRemitionguideById($db, $id) {
        return $db->get_where("ebill_signing_remitionguides", array("id" => $id))->row();
    }
    
    public function getRemitionguideByName($db, $remitionguide_name) {
        return $db->get_where("ebill_signing_remitionguides", array("name" => $remitionguide_name))->row();
    }
}
