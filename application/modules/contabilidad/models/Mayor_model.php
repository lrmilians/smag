<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Mayor_model extends CI_Model {

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
        $this->db->order_by('id');
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }

    public function add_asiento($data) {
        $this->db->insert_batch('conta_mayor', $data);
        if($this->db->affected_rows() > 0){
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_asiento($identity, $data) {
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('conta_mayor', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function update_batch_asiento($data) {
        $this->db->update_batch('conta_mayor', $data, 'id');
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function get_balance_comprobacion(){
        $query =  $this->db->query('select conta_cuentas.codigo,balance.cuenta_id,balance.suma_debe,balance.suma_haber,balance.saldo ' .
            'from (select  conta_mayor.cuenta_id, sum(importe_debe) as suma_debe, sum(importe_haber) as suma_haber, '.'
                    (sum(importe_debe) - sum(importe_haber)) as saldo from conta_mayor where conta_mayor.activo = TRUE group by conta_mayor.cuenta_id) balance '.'
                    inner join conta_cuentas on (balance.cuenta_id = conta_cuentas.id) order by conta_cuentas.codigo');

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }


    public function desactivar_mayor(){
        $this->db->update('conta_mayor', array('activo' => FALSE));
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }


}
