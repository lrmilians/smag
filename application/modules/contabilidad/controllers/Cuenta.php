<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cuenta extends REST_Controller {

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
        $this->methods['cuentas_post']['limit'] = 100; //500 requests per hour per user/key
        //	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        $this->load->model('auth/session_model');
        $this->load->model('cuenta_model');
    }

    function cuentas_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $data = array('select' => '');
                $cuentas = $this->cuenta_model->get_cuenta($data, 'conta_cuentas', FALSE);
                if($cuentas === false){
                    $this->response($this->data_error_response('00', 'Error recuperando las cuentas.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['total_rows'] = $this->cuenta_model->get_total_rows('conta_cuentas');
                    $response['data'] = $cuentas;
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }


    function getcuenta_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        $cuenta_id = $this->get('id');
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $cuenta_data = $this->cuenta_model->get_cuenta_by_id($cuenta_id);
                if($cuenta_data === false){
                    $this->response($this->data_error_response('00', 'Error recuperando las cuentas.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['total_rows'] = $cuenta_data['count'];
                    $response['data'] = $cuenta_data['data'];
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