<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Producto_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_productos($data) {
        $this->db->from('inv_productos');
        if(!empty($data['codigo'])){
            $this->db->like('codigo', $data['codigo']);
        }
        $this->db->order_by('codigo');

        $tempdb = clone $this->db;
        $result['total_records'] = $tempdb->count_all_results();

        if(!empty($data['start']) && !empty($data['size'])){
            $this->db->limit($data['start'],$data['size']);

        }

        $result['data'] = $this->db->get()->result_array();
       // var_dump($this->db->last_query());
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
