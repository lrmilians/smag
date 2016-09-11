<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Diario_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_total_rows($table){
        return $this->db->count_all_results($table);
    }

    public function get_asiento($data, $table, $where = TRUE, $limit = array()) {
        if(!empty($data['select'])){
            $this->db->select($data['select']);
        }
        if($where){
            foreach($data['where'] as $where){
                $this->db->where($where['field'], $where['value']);
            }
        }
        if(!empty($limit)){
            $this->db->limit($limit[0], $limit[1]);
        }
        $this->db->join('users', 'conta_diario.user_id = users.id');
       // $this->db->join('conta_mayor', 'conta_diario.id = conta_mayor.diario_id');
        //$this->db->join('conta_cuentas', 'conta_mayor.cuenta_id = conta_cuentas.id');
        $this->db->order_by('conta_diario.fecha');
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }

    public function get_asientos_mayor($select, $diario_id) {
        $this->db->select($select);
        $this->db->where('conta_diario.id', $diario_id);
        $this->db->join('conta_mayor', 'conta_diario.id = conta_mayor.diario_id');
        $this->db->join('conta_cuentas', 'conta_mayor.cuenta_id = conta_cuentas.id');
        $this->db->order_by('conta_diario.fecha');
        $query = $this->db->get('conta_diario');

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }

    public function add_asiento($data) {
        $this->db->insert('conta_diario', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_asiento($identity, $data) {
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('conta_diario', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function del_asiento($id) {
        $this->db->where("id", $id);
        $this->db->delete("conta_diario");
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function desactivar_diario(){
        $this->db->update('conta_diario', array('activo' => FALSE));
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

}
