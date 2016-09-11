<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Srivalidation_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_total_rows($table){

        return $this->db->count_all_results($table);
    }

    public function get_srivalidation($data, $table, $where = TRUE, $limit = array()) {
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
        $this->db->order_by('description');
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            return $query->result_array();
        }
        return false;
    }

}
