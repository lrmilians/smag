<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Tabla_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_tablas($data) {
        //$this->db->limit($data['inicio'], $data['fin']);
        $this->db->from('config_tablas');
        if(!empty($data[0]['numero'])){
            if($data[0]['like']){
                $this->db->like('numero', $data[0]['numero']);
            } else {
                $this->db->where('numero', $data[0]['numero']);
            }
        }

        $this->db->order_by('numero');
        $this->db->order_by('codigo');

        $tempdb = clone $this->db;
        $result['total_records'] = $tempdb->count_all_results();

        if(!empty($data[13]['start']) && !empty($data[14]['size'])){
            $this->db->limit($data[13]['start'],$data[14]['size']);

        }

        $result['data'] = $this->db->get()->result_array();
       // var_dump($this->db->last_query());
        return $result;

    }

    public function get_catalogos($data) {
        $result = array();
        if(!empty($data)){
            foreach($data as $dat){
                $this->db->where('numero', $dat);
                $this->db->where('codigo <>', '-');
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
