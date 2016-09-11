<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Key_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_total_rows($table){

        return $this->db->count_all_results($table);
    }

    public function get_key($data, $table, $where = TRUE, $limit = array()) {
        if(!empty($data['select'])){
            foreach($data['select'] as $value){
                $this->db->select($value);
            }
        }
        if($where){
            $this->db->where($data['field'], $data['value']);
        }
        if(!empty($limit)){
            $this->db->limit($limit[0], $limit[1]);
        }
        $this->db->order_by('active DESC');
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            return $query->result_array();
        }
        return false;
    }

    public function add_key($data) {
        $this->db->update('ebill_signing_keys', array('active' => 0));
        $this->db->insert('ebill_signing_keys', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function update_key($identity, $data) {
        if ($data['active']) {
            $this->db->update('ebill_signing_keys', array('active' => 0));
        }
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('ebill_signing_keys', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function del_key($id) {
        $this->db->where("id", $id);
        $this->db->delete("ebill_signing_keys");
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

}
