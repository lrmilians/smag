<?php
defined("BASEPATH") or die("El acceso al script no está permitido");

class User_model extends CI_Model {

    function __construct(){
        parent::__construct();
    }

    public function get_total_rows($table){

        return $this->db->count_all_results($table);
    }

    public function get_user($data, $table, $where = TRUE, $limit = array()) {
        if(!empty($data['select'])){
            foreach($data['select'] as $value){
                $this->db->select($value);
            }
        }
        switch($table){
            case 'users':
                $this->db->join('groups', 'users.rol_id = groups.id');
                break;
        }
        if($where){
            $this->db->where($data['field'], $data['value']);
        }
        if(!empty($limit)){
            $this->db->limit($limit[0], $limit[1]);
        }
        $query = $this->db->get($table);

        if($query->num_rows() > 0){
            return $query->result_array();
        }
        return false;
    }

    public function get_(){
        $query = $this->db->get('users');

        if($query->num_rows() > 0){
            return $query->result_array();
        }
        return false;
    }

    public function add_user($data, $id = FALSE) {
        $this->db->insert('users', $data);
        if($this->db->affected_rows() > 0){
            if($id){
                return $this->db->insert_id();
            }
            return true;
        }
        return false;
    }

    public function update_user($identity, $data) {
        $this->db->where($identity['field'], $identity['value']);
        $this->db->update('users', $data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }

    public function update($token, $login, $identity) {
        if($login){
            $date = date('Y-m-d h:i:s');
            $data1 = array (
                'token' => $token,
                'login' => $login,
                'time_session' => strtotime('+'.TIME_SESSION.' minute', strtotime($date))
            );
            if($this->_update_user($identity, $data1)){
                return array(
                    'result' => true,
                    'data' => $this->get_($identity, 'users'));
            }
            return false;
        } else {
            $data1 = array (
                'token' => '',
                'login' => $login,
                'time_session' => 0
            );
            if($this->_update_user($identity, $data1)){
                return true;
            }
            return false;
        }
    }

    public function delete_($table, $data) {
        $this->db->delete($table, $data);
        if($this->db->affected_rows() >= 0){
            return true;
        }
        return false;
    }


    public function check_session($token){
        $data = array(
            'select' => array('time_session'),
            'field' => 'token',
            'value' => $token
        );
        $result_user = $this->get_($data, 'users');
        $time = strtotime(date('Y-m-d h:i:s'));
        if(!$result_user){
            return -1;
        } else {
            if($time > $result_user[0]['time_session']){
                //Sesion caducada
                $this->ion_auth->logout();
                $data1 = array (
                    'token' => '',
                    'login' => false,
                    'time_session' => 0
                );
                if($this->_update_user($data, $data1)){
                    return 1;
                }
                return -1;
            } else {
                //Extender Sesion
                $data1 = array (
                    'time_session' => strtotime('+'.TIME_SESSION.' minute', $time)
                );
                if($this->_update_user($data, $data1)){
                    return 0;
                }
                return -1;
            }
        }

    }

}
