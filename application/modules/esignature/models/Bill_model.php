<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Bill_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_total_rows($table){
        return $this->db->count_all_results($table);
    }

    public function get_bill($data, $table, $where = TRUE, $limit = array()) {
        if(!empty($data['select'])){
            foreach($data['select'] as $value){
                $this->db->select($value);
            }
        }
        if($where){
            foreach($data['where'] as $where){
                $this->db->where($where['field'], $where['value']);
            }
        }
        if(!empty($limit)){
            $this->db->limit($limit[0], $limit[1]);
        }
        $this->db->join('users', 'ebill_signing_bills.user_id = users.id');
        $this->db->order_by('created DESC');
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }

    public function add_bill($data) {
        $this->db->insert('ebill_signing_bills', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function update_bill($identity, $data) {
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('ebill_signing_bills', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function del_bill($id) {
        $this->db->where("id", $id);
        $this->db->delete("ebill_signing_bills");
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

}
