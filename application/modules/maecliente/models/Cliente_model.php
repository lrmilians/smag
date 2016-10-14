<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Cliente_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_clientes($data) {
        $this->db->from('personas');
        $this->db->join('cli_clientes', 'personas.id = cli_clientes.id');

        if(!empty($data['codigo'])){
            $this->db->like('codigo', $data['codigo']);
        }
        if(!empty($data['identificacion'])){
            $this->db->like('identificacion', $data['identificacion']);
        }
        if(!empty($data['razon_social'])){
            $this->db->like('razon_social', $data['razon_social']);
        }
        if(!empty($data['email'])){
            $this->db->like('email', $data['email']);
        }
        $this->db->order_by('razon_social');

        $tempdb = clone $this->db;
        $result['total_records'] = $tempdb->count_all_results();

        if(!empty($data['start']) && !empty($data['size'])){
            $this->db->limit($data['start'],$data['size']);
        }

        $result['data'] = $this->db->get()->result_array();
       // var_dump($this->db->last_query());
        return $result;

    }

    public function add_cliente($data) {
        $this->db->insert('personas', $data['persona']);
        if($this->db->affected_rows() > 0){
            $data['cliente']['id'] = $this->db->insert_id();
            $this->db->insert('cli_clientes', $data['cliente']);
            if($this->db->affected_rows() > 0){

                return true;
            }
        }
        return false;
    }

    public function update_cliente($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('personas', $data['persona']);
        $this->db->where('id', $id);
        $this->db->update('cli_clientes', $data['cliente']);
        if($this->db->affected_rows() >= 0){
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
