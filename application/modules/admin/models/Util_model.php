<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Util_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function existe_campos($tabla ,$campos) {
        $result = array();
        /*echo '<pre>';
        print_r($campos);
        echo '<pre>';die();*/
        foreach($campos as $key => $value){
            $this->db->where($key, $value);
            if($this->db->count_all_results($tabla) > 0){
                $result[$key] = $value;
            }
        }
        return $result;

    }

}
