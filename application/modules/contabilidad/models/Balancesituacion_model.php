<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Balancesituacion_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_balance($data, $where = TRUE, $limit = array()) {
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
        $this->db->order_by('id');
        $query = $this->db->get('conta_balance_situacion');

        $rows = $query->num_rows();

        if($rows >= 0){
            $result['data'] =  $query->result_array();
            $result['count'] =  $rows;

            return $result;
        }
        return false;
    }

    public function get_balance_detalle($id) {
        $this->db->select(array('conta_cuentas.codigo','conta_cuentas.nombre','conta_cuenta_balance_situacion.balance_situacion_id', 'conta_cuenta_balance_situacion.saldo_deudor', 'conta_cuenta_balance_situacion.saldo_acreedor'));
        $this->db->where('conta_cuenta_balance_situacion.balance_situacion_id', $id);
        $this->db->join('conta_cuentas', 'conta_cuenta_balance_situacion.cuenta_id = conta_cuentas.id');
        $this->db->order_by('conta_cuenta_balance_situacion.id');
        $query = $this->db->get('conta_cuenta_balance_situacion');

        $rows = $query->num_rows();

        if($rows >= 0){
            $result['data'] =  $query->result_array();
            $result['count'] =  $rows;

            return $result;
        }
        return false;
    }

    public function add_balance($data) {
        $this->db->insert('conta_balance_situacion', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }
        return false;
    }

    public function add_balance_detalle($data) {
        $this->db->insert_batch('conta_cuenta_balance_situacion', $data);
        if($this->db->affected_rows() == count($data)){
            return true;
        }
        return false;
    }




}
