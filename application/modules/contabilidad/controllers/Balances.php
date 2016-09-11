<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Balances extends REST_Controller {

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
       // $this->methods['cuentas_post']['limit'] = 100; //500 requests per hour per user/key
        //	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        $this->load->model('auth/session_model');
        $this->load->model(array('mayor_model', 'balancesituacion_model'));
    }

    function comprobacion_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $balance_comprobacion = $this->mayor_model->get_balance_comprobacion();
                if($balance_comprobacion === false){
                    $this->response($this->data_error_response('00', 'Error recuperando las llaves.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $balance_comprobacion;
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function situacion_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $data['select'] = array('id', 'fecha', 'activo', 'pasivo');
                $balances_situacion = $this->balancesituacion_model->get_balance($data, false);
                if($balances_situacion === false){
                    $this->response($this->data_error_response('00', 'Error recuperando las llaves.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $balances_situacion['data'];
                    $response['total_rows'] = $balances_situacion['count'];
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function situaciondetalle_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $balance_situacion_id = $this->get('id');
                $data['select'] = array('id', 'fecha', 'activo', 'pasivo');
                $balance_situacion_detalle = $this->balancesituacion_model->get_balance_detalle($balance_situacion_id);
                if($balance_situacion_detalle === false){
                    $this->response($this->data_error_response('00', 'Error recuperando el detalle del balance de situacion.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $balance_situacion_detalle['data'];
                    $response['total_rows'] = $balance_situacion_detalle['count'];
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

    private function _get_id_login_user($token){
        $data = array(
            'select' => array('users.id'),
            'field' => 'token',
            'value' => $token
        );
        $result_user = $this->session_model->get_($data, 'users');

        if($result_user == false){
            return false;
        }

        return $result_user[0]['id'];
    }
}