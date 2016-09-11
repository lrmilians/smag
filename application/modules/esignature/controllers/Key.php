<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Key extends REST_Controller {

    protected $allowed_http_methods = array('get', 'delete', 'post', 'put');

    function __construct() {
        //header('Access-Control-Allow-Origin:');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        //$this->load->library(array('ion_auth','form_validation'));
        $this->load->helper(array('url','language'));

        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['keys_post']['limit'] = 100; //500 requests per hour per user/key
        //	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        $this->load->model('auth/session_model');
        $this->load->model('key_model');
        $this->load->helper('download');
    }

    function keys_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $data = array('select' => '');
                $keys = $this->key_model->get_key($data, 'ebill_signing_keys', FALSE);
                if($keys === false){
                    $this->response($this->data_error_response('00', 'Error recuperando las llaves.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['total_rows'] = $this->key_model->get_total_rows('ebill_signing_keys');
                    $response['data'] = $keys;
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function key_post(){
            $token = $this->get('token');
            $session = $this->_checksession($token);
            if($session == -1){
                $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
            } else {
                if($session == 0){
                    if(!empty($_FILES['file'])){
                        $config['upload_path'] = PATH_KEYS;
                        $old = umask(0);
                        if(!is_dir(PATH_KEYS)){
                            mkdir(PATH_KEYS, 0755, true);
                            umask($old);
                        }
                        $config['allowed_types'] = '*';
                        $config['max_size']	= '5120';
                        $config['overwrite'] = TRUE;
                        $this->load->helper('inflector');
                        $file_name = underscore($_FILES['file']['name']);
                        $config['file_name'] = $file_name;

                        $this->load->library('upload');
                        $this->upload->initialize($config);
                    }

                    if($this->post('action') == -1){
                        if(!$this->upload->do_upload('file')){
                            $this->response($this->data_error_response('00', 'Error subiendo archivo.'), 500);
                        }else{
                            $data = array(
                                'name' => $file_name,
                                'password' => $this->post('password'),
                                'expired_date' => $this->post('expired_date'),
                                'active' => 1,
                                'created' => date('Y-m-d H:i:s'),
                                'modified' => date('Y-m-d H:i:s')
                            );
                            $save = $this->key_model->add_key($data);
                            if($save == false){
                                $this->response($this->data_error_response('00', 'Error salvando la llave.'), 500);
                            } else {
                                $response['status'] = 'OK';
                                $response['message'] = null;
                                $response['data'] = null;
                                $this->response($response, 200);
                            }
                        }
                    } else {
                        if(!empty($_FILES['file'])){
                            if(!$this->upload->do_upload('file')){
                                $this->response($this->data_error_response('00', 'Error subiendo archivo.'), 500);
                            }else{
                                unlink(PATH_KEYS . '/' . $this->post('name_file_old'));
                                $identity = array(
                                    'field' => 'id',
                                    'value' => intval($this->post('action'))
                                );
                                $data = array(
                                    'name' => $file_name,
                                    'password' => $this->post('password'),
                                    'expired_date' => $this->post('expired_date'),
                                    'active' => intval($this->post('active')),
                                    'modified' => date('Y-m-d H:i:s')
                                );
                                $save = $this->key_model->update_key($identity, $data);
                                if($save == false){
                                    $this->response($this->data_error_response('00', 'Error salvando la llave.'), 500);
                                } else {
                                    $response['status'] = 'OK';
                                    $response['message'] = null;
                                    $response['data'] = null;
                                    $this->response($response, 200);
                                }
                            }
                        } else {
                            $identity = array(
                                'field' => 'id',
                                'value' => intval($this->post('action'))
                            );
                            $data = array(
                                'password' => $this->post('password'),
                                'expired_date' => $this->post('expired_date'),
                                'active' => intval($this->post('active')),
                                'modified' => date('Y-m-d H:i:s')
                            );
                            $save = $this->key_model->update_key($identity, $data);
                            if($save == false){
                                $this->response($this->data_error_response('00', 'Error salvando la llave.'), 500);
                            } else {
                                $response['status'] = 'OK';
                                $response['message'] = null;
                                $response['data'] = null;
                                $this->response($response, 200);
                            }
                        }
                    }
                } else {
                    $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
                }
            }
        }

    function keyfile_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $file_name = $this->get('name');
                $data = file_get_contents(PATH_KEYS . $file_name);
                force_download($file_name, $data);
                $response['status'] = 'OK';
                $this->response($response, 200);
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }

    }

    function keydel_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if ($session == -1) {
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $del = $this->key_model->del_key(intval($this->post('id')));
                if($del == false){
                    $this->response($this->data_error_response('00', 'Error salvando la llave.'), 500);
                } else {
                    unlink(PATH_KEYS . '/' . $this->post('name'));
                    $response['status'] = 'OK';
                    $response['message'] = null;
                    $response['data'] = null;
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    private function _checksession($token) {
        //result = 0  Session actived
        //result = 1  Session expired
        //result = -1  Error
        return $this->session_model->check_session($token);
    }
}