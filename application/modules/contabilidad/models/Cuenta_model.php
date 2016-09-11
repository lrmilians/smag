<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Cuenta_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_total_rows($table){

        return $this->db->count_all_results($table);
    }

    public function get_cuenta($data, $table, $where = TRUE, $limit = array()) {
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
        $query = $this->db->get($table);

        if($query->num_rows() >= 0){
            return $query->result_array();
        }
        return false;
    }


    public function get_cuenta_by_id($id) {
        $this->db->select(array('conta_cuentas.nombre','conta_cuentas.codigo','conta_mayor.fecha',
            'round(conta_mayor.importe_debe, 2) as importe_debe','round(conta_mayor.importe_haber, 2) as importe_haber',
            'conta_diario.descripcion'));
        $this->db->where('conta_cuentas.id', $id);
        $this->db->where('conta_mayor.activo', TRUE);
        $this->db->join('conta_mayor', 'conta_mayor.cuenta_id = conta_cuentas.id');
        $this->db->join('conta_diario', 'conta_mayor.diario_id = conta_diario.id');
        $this->db->order_by('conta_mayor.fecha');
        $query = $this->db->get('conta_cuentas');

        if($query->num_rows() >= 0){
            $result['data'] = $query->result_array();
            $result['count'] = $query->num_rows();

            return $result;
        }
        return false;
    }

    public function get_cuentas_dado_grupo($grupo) {
        $this->db->select(array('conta_mayor.cuenta_id','sum(conta_mayor.importe_debe) as suma_debe','sum(conta_mayor.importe_haber) as suma_haber'));
        $this->db->where_in('conta_cuentas.grupo', $grupo);
        $this->db->where('conta_mayor.activo', TRUE);
        $this->db->join('conta_mayor', 'conta_mayor.cuenta_id = conta_cuentas.id');
        $this->db->group_by('conta_mayor.cuenta_id');
        $query = $this->db->get('conta_cuentas');

        if($query->num_rows() >= 0){
            $result_aux = $query->result_array();
            $result['suma_saldos'] = 0;
            $i = 0;
            foreach($result_aux as $value){
                $saldo = $value['suma_debe'] - $value['suma_haber'];
                if(abs($saldo) != 0){
                    $result['data'][$i] = $value;
                    $result['data'][$i]['saldo'] =  abs($saldo);
                    $result['data'][$i]['tipo_saldo'] = 1; //ACREEDOR
                    if($saldo > 0){
                        $result['data'][$i]['tipo_saldo'] = 0; //DEUDOR
                    }
                    $result['suma_saldos'] =  $result['suma_saldos']  + $result['data'][$i]['saldo'];
                    $i++;
                }
            }
            $result['count'] = $i;

            return $result;
        }
        return false;
    }

}
