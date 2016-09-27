<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Tabla_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_tablas($data) {
        $this->db->from('config_tablas');
        if(!empty($data['numero'])){
            $this->db->where('numero', $data['numero']);
        }
        if(!empty($data['codigo'])){
            $this->db->where('codigo', $data['codigo']);

        }
        if(!empty($data['nombre'])){
            $this->db->where("lower(nombre) like concat('%',lower('".$data['nombre']."'),'%')");
        }
        $this->db->order_by('numero');
        $this->db->order_by('codigo');

        $tempdb = clone $this->db;
        $result['total_records'] = $tempdb->count_all_results();

        if(!empty($data['start']) && !empty($data['size'])){
            $this->db->limit($data['start'],$data['size']);

        }

        $result['data'] = $this->db->get()->result_array();
        return $result;
    }

    public function get_tabla($data) {
        $this->db->from('config_tablas');
        if(!empty($data['numero'])){
            $this->db->where('numero', $data['numero']);
        }

        $this->db->order_by('numero');
        $this->db->order_by('codigo');

        $result['data'] = $this->db->get()->result_array();
        return $result;
    }

    public function get_catalogos($data) {
        $result = array();
        if(!empty($data)){
            foreach($data as $dat){
                $this->db->where('numero', $dat);
                $this->db->where('codigo <>', '-');
                if($dat == '106'){
                    $this->db->where('dato1', 1);
                }
                $result[$dat] = $this->db->get('config_tablas')->result_array();
            }
        }
        return $result;
    }

    public function set_tablas($data) {
        $this->db->insert_batch('config_tablas', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function existe_tabla($num, $cod){
        $this->db->from('config_tablas');
        $this->db->where('numero', $num);
        $this->db->where('codigo', $cod);
        $result = $this->db->get()->result_array();
        if(!empty($result)){
            return true;
        }
        return false;
    }

    public function del_tabla($num){
        $this->db->where('numero', $num);
        $this->db->delete('config_tablas');
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }





}
