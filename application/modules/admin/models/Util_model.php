<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Util_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function existe_campos($tabla ,$campos) {
        $result = array();
        foreach($campos as $key => $value){
            $this->db->where($key, $value);
            if($this->db->count_all_results($tabla) > 0){
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public function set_valor_null($data){
        if(!empty($data)){
            foreach($data as $key=>$value){
                if(empty($value)){
                    $data[$key] = NULL;
                }
            }
            return $data;
        }
        return false;
    }



}
