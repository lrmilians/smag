<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class retention_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_retentions($db) {
        $db->order_by("id", "desc");
        return $db->get("ebill_signing_retentions")->result_array();
    }
    
    public function getMessageByRetentionName($db, $retention_name){
        $creditnote = $this->getRetentionByName($db, $retention_name);
        $db->select('ebill_signing_sri_validations.code, '
                . 'ebill_signing_sri_validations.code_type, '
                . 'ebill_signing_sri_validations.description, '
                . 'ebill_signing_retention_messages.additional_info, '
                . 'ebill_signing_sri_validations.possible_solution');
        $db->from('ebill_signing_retention_messages');
        $db->join('ebill_signing_sri_validations', 'ebill_signing_sri_validations.code = ebill_signing_retention_messages.code');
        $db->where('ebill_signing_retention_messages.retention_id', $creditnote->id); 
        return $db->get()->result_array();
    } 
    
    public function getRetentionById($db, $id) {
        return $db->get_where("ebill_signing_retentions", array("id" => $id))->row();
    }
    
    public function getRetentionByName($db, $retention_name) {
        return $db->get_where("ebill_signing_retentions", array("name" => $retention_name))->row();
    }
}
