<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class esignature_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

       

    /*public function updateXML($id, $data, $tables_name) {
        $this->db->where("id", $id);

        $this->db->update($tables_name['0'], $data);
    }*/
    
    
    
    public function updateXML($xml_name, $xml_id, $authorize_bill_result, $tables_name) {
        $data = array(
            'state'  =>  $authorize_bill_result['estado'],
            'processed'  =>  1,
            'modified'   => date("Y-m-d H:i:s")
        );
        $sql = "UPDATE ". $tables_name['0'] ." SET ";
        $i = 1;
        foreach ($data as $field => $value) {
            if ($field == 'processed') {
                $sql .= " $field = $value";
            } else {
                $sql .= " $field = '$value'";
            }  
            if (count($data) > $i) {
                $sql .= ", ";
            }
            $i++;
        }
        $sql .= " where name = '$xml_name'"; 
        $this->db->query($sql);
        
        //$xml_id = $this->get_xmlid_by_name($tables_name['0'], $xml_name);
        $created = $data['modified'];
        $name_field = $tables_name['2'];
        foreach ($authorize_bill_result['mensajes'] as $messages) {
            $code = $messages['identificador'];
            $additional_info = str_replace(array("'", '"'), "-", $messages['informacion_adicional']);
            $sql = "INSERT INTO ". $tables_name['1'] ." ($name_field, code, additional_info, created) VALUES ("
                ."'$xml_id', '$code', '$additional_info', '$created')";
            $this->db->query($sql);
        }    
                
    }
    
    private function get_xmlid_by_name($table_name, $xml_name){
        $sql = "SELECT id FROM `" . $table_name . "` WHERE `name` = '$xml_name'";
        $id = $this->db->query($sql);
        
        return $id;   
    } 

   
}
