<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Utils extends REST_Controller {

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
        $this->load->model(array('util_model'));
    }

    function existecampos_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            //$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
            $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
        } else {
            if ($session == 0) {
                $tabla = $this->post('tabla');
                $campos = $this->post('campos');
                $labels = $this->post('labels');
                $result = $this->util_model->existe_campos($tabla, $campos);
                if(!empty($result)){
                    $response['status'] = '-1';
                    $response['message'] = '<p><strong>Los siguientes datos existen en la base de datos.</strong></p>';
                    foreach($result as $key => $value){
                        $response['message'] .= '<p><strong>Campo: </strong>'.$labels[$key].' <strong>Valor: </strong>'.$value.'</p>';
                    }
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = '';
                }
                $response['data'] = $result;
                $response['total_records'] = '';
                $this->response($response, 200);
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