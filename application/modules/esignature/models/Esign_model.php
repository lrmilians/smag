<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class Esign_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    public function add_doc($table, $data, $id = FALSE) {
        $this->db->insert($table, $data);
        if($this->db->affected_rows() > 0){
            if($id){
                return $this->db->insert_id();
            }
            return true;
        }
        return false;
    }

    public function get_doc_exist($table, $name) {
        $this->db->where('name', $name);
        $query = $this->db->get($table);
        if($query->num_rows() > 0){
            return true;
        }
        return false;
    }

    public function update_xml($xml_name, $xml_id, $authorize_bill_result, $tables_name) {
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
        if(!empty($authorize_bill_result['mensajes'])){
            foreach ($authorize_bill_result['mensajes'] as $messages) {
                $code = $messages['identificador'];
                $additional_info = str_replace(array("'", '"'), "-", $messages['informacion_adicional']);
                $sql = "INSERT INTO ". $tables_name['1'] ." ($name_field, code, additional_info, created) VALUES ("
                    ."'$xml_id', '$code', '$additional_info', '$created')";
                $this->db->query($sql);
            }
        }


    }

    public function get_doc_stat($tables, $user_id) {
        $result = array();
        foreach($tables as $key=>$value){
            $this->db->from($value);
            $this->db->where('user_id', $user_id);
            $db_results = $this->db->get();
            $result[$value]['count'] = $db_results->num_rows();
        }
        return $result;
    }



   /* public function update_key($identity, $data) {
        if ($data['active']) {
            $this->db->update('ebill_signing_keys', array('active' => 0));
        }
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('ebill_signing_keys', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function del_key($id) {
        $this->db->where("id", $id);
        $this->db->delete("ebill_signing_keys");
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }*/

}
