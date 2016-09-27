<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Session_model extends CI_Model {

    function __construct(){
        // Call the Model constructor
        parent::__construct();

        $this->load->library(array('ion_auth'));
    }

    public function get_ ($data, $table) {
        if(!empty($data['select'])){
            foreach($data['select'] as $value){
                $this->db->select($value);
            }
        }
        switch($table){
            case 'users':
                $this->db->join('groups', 'users.rol_id = groups.id');
                break;
        }
        $this->db->where($data['field'], $data['value']);
        $query = $this->db->get($table);
        if($query->num_rows() > 0){
            return $query->result_array();
        }
        return false;
    }

    public function _update_user($identity, $data) {
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('users', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function update($token, $login, $identity) {
        if($login){
            $date = date('Y-m-d H:i:s');
            $data1 = array (
                'token' => $token,
                'login' => $login,
                'time_session' => strtotime('+ 15 minute', strtotime($date))
            );
            if($this->_update_user($identity, $data1)){
                return array(
                    'result' => true,
                    'data' => $this->get_($identity, 'users'));
            }
            return false;
        } else {
            $data1 = array (
                'token' => '',
                'login' => $login,
                'time_session' => 0
            );
            if($this->_update_user($identity, $data1)){
                return true;
            }
            return false;
        }
    }

    public function delete_($table, $data) {
        $this->db->delete($table, $data);
        if($this->db->affected_rows() >= 0){
            return true;
        }
        return false;
    }

    public function check_session($token){
        $data = array(
            'select' => array('time_session'),
            'field' => 'token',
            'value' => $token
        );
        $result_user = $this->get_($data, 'users');
        $time = strtotime(date('Y-m-d H:i:s'));
        if(!$result_user){
            return -1;
        } else {
            if($time > $result_user[0]['time_session']){
                //Sesion caducada
                $this->ion_auth->logout();
                $data1 = array (
                    'token' => '',
                    'login' => false,
                    'time_session' => 0
                );
                if($this->_update_user($data, $data1)){
                    return 1;
                }
                return -1;
            } else {
                //Extender Sesion
                $data1 = array (
                    'time_session' => strtotime('+ 15 minute', $time)
                );
                if($this->_update_user($data, $data1)){
                    return 0;
                }
                return -1;
            }
        }

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
