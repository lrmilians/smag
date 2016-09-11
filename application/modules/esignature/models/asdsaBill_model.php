<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Bill_model extends CI_Model {

    function __construct(){
        // Call the Model constructor
        parent::__construct();
    }

    public function get_all_bills($limit) {
        $this->db->order_by("id", "desc");
        $this->db->limit($limit);
        $query = $this->db->get('ebill_signing_bills');
        return $query->result();
    }
            
    /*public function getMessageByBillName($bill_name){
        $bill = $this->getBillByName($bill_name);
        $this->db->select('ebill_signing_sri_validations.code, '
                . 'ebill_signing_sri_validations.code_type, '
                . 'ebill_signing_sri_validations.description, '
                . 'ebill_signing_bill_messages.additional_info, '
                . 'ebill_signing_sri_validations.possible_solution');
        $this->db->from('ebill_signing_bill_messages');
        $this->db->join('ebill_signing_sri_validations', 'ebill_signing_sri_validations.code = ebill_signing_bill_messages.code');
        $this->db->where('ebill_signing_bill_messages.bill_id', $bill->id); 
        return $this->db->get()->result_array();
    }      

    public function updateBill($id, $data) {
        $this->db->where("id", $id);

        return $this->db->update("ebill_signing_bills", $data);
    }

    public function deleteBill($id) {
        $this->db->where("id", $id);
        
        return $this->db->delete("ebill_signing_bills");
    }
    
    public function addBill($data) {
        if($this->db->insert("ebill_signing_bills", $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getBillById($id) {
        return $this->db->get_where("ebill_signing_bills", array("id" => $id))->row();
    }
    
    public function getBillByName($bill_name) {
        return $this->db->get_where("ebill_signing_bills", array("name" => $bill_name))->row();
    }*/
}
