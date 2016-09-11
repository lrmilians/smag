<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class debitnote_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_debitnotes() {
        $this->db->order_by("id", "desc");
        return $this->db->get("ebill_signing_debitnotes")->result_array();
    }
    
    public function getMessageByDebitnoteName($db, $debitnote_name){
        $debitnote = $this->getDebitnoteByName($db, $debitnote_name);
        $db->select('ebill_signing_sri_validations.code, '
                . 'ebill_signing_sri_validations.code_type, '
                . 'ebill_signing_sri_validations.description, '
                . 'ebill_signing_debitnote_messages.additional_info, '
                . 'ebill_signing_sri_validations.possible_solution');
        $db->from('ebill_signing_debitnote_messages');
        $db->join('ebill_signing_sri_validations', 'ebill_signing_sri_validations.code = ebill_signing_debitnote_messages.code');
        $db->where('ebill_signing_debitnote_messages.debitnote_id', $debitnote->id); 
        return $db->get()->result_array();
    } 
    
    public function getDebitnoteById($db, $id) {
        return $db->get_where("ebill_signing_debitnotes", array("id" => $id))->row();
    }
    
    public function getDebitnoteByName($db, $debitnote_name) {
        return $db->get_where("ebill_signing_debitnotes", array("name" => $debitnote_name))->row();
    }
}
