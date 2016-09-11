<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class creditnote_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_creditnotes() {
        $this->db->order_by("id", "desc");
        return $this->db->get("ebill_signing_creditnotes")->result_array();
    }
    
    public function getMessageByCreditnoteName($creditnote_name){
        $creditnote = $this->getCreditnoteByName($creditnote_name);
        $this->db->select('ebill_signing_sri_validations.code, '
                . 'ebill_signing_sri_validations.code_type, '
                . 'ebill_signing_sri_validations.description, '
                . 'ebill_signing_creditnote_messages.additional_info, '
                . 'ebill_signing_sri_validations.possible_solution');
        $this->db->from('ebill_signing_creditnote_messages');
        $this->db->join('ebill_signing_sri_validations', 'ebill_signing_sri_validations.code = ebill_signing_creditnote_messages.code');
        $this->db->where('ebill_signing_creditnote_messages.creditnote_id', $creditnote->id); 
        return $this->db->get()->result_array();
    } 
    
    public function addCreditnote($data) {
        if($this->db->insert("ebill_signing_creditnotes", $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getCreditnoteById($id) {
        return $this->db->get_where("ebill_signing_creditnotes", array("id" => $id))->row();
    }
    
    public function getCreditnoteByName($creditnote_name) {
        return $this->db->get_where("ebill_signing_creditnotes", array("name" => $creditnote_name))->row();
    }
}
