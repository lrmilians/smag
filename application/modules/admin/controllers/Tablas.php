<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tablas extends REST_Controller {

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
        $this->load->model(array('tabla_model'));
    }

    function tabla_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $data = $this->post();
                $tablas = $this->tabla_model->get_tablas($data);

                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $tablas['data'];
                    $response['total_records'] = $tablas['total_records'];

                    $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function settabla_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $del = true;
                if($this->post('action') !== '-1'){
                    $del = $this->tabla_model->del_tabla($this->post('action'));
                }
                if($del){
                    $fecha_modificado = date('Y-m-d H:i:s');
                    $data = array();
                    foreach($this->post('tablas') as $tabla){
                        if(!$this->tabla_model->existe_tabla($tabla['numero'], '-')){
                            $data[]= array (
                                'numero' => $tabla['numero'],
                                'codigo' => '-',
                                'nombre' => $tabla['nombre'],
                                'dato1' => null,'dato2' => null,'dato3' => null,'dato4' => null,'dato5' => null,'dato6' => null,'dato7' => null,'dato8' => null,'dato9' => null,'dato10' => null,
                                'dato11' => null, 'dato12' => null, 'dato13' => null, 'dato14' => null, 'dato15' => null,'fecha_modificado' => $fecha_modificado, 'user_modificado' => $this->post('userId')
                            );
                            if(!empty($tabla['subtablas'])){
                                foreach($tabla['subtablas'] as $subtabla){
                                    if(!$this->tabla_model->existe_tabla($subtabla['numero'], $subtabla['codigo'])){
                                        $data[]= array (
                                            'numero' => $subtabla['numero'],
                                            'codigo' => $subtabla['codigo'],
                                            'nombre' => $subtabla['nombre'],
                                            'dato1' => $subtabla['dato1'],'dato2' => $subtabla['dato2'],'dato3' => $subtabla['dato3'],'dato4' => $subtabla['dato4'],
                                            'dato5' => $subtabla['dato5'],'dato6' => $subtabla['dato6'],'dato7' => $subtabla['dato7'],'dato8' => $subtabla['dato8'],
                                            'dato9' => $subtabla['dato9'],'dato10' => $subtabla['dato10'],'dato11' => $subtabla['dato11'],'dato12' => $subtabla['dato12'],
                                            'dato13' => $subtabla['dato13'],'dato14' => $subtabla['dato14'], 'dato15' => $subtabla['dato15'],
                                            'fecha_modificado' => $fecha_modificado, 'user_modificado' => $this->post('userId')
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $response['status'] = '-1';
                    $response['message'] = 'Los datos no fueron guardados.';
                    if(!empty($data)){
                        $result = $this->tabla_model->set_tablas($data);
                        if($result){
                            $response['status'] = 'OK';
                            $response['message'] = 'Datos guardados correctamente.';
                        }
                    } else {
                        $response['message'] .= 'Ya existen las tablas.';
                    }
                } else {
                    $response['status'] = '-1';
                    $response['message'] = 'Los datos no fueron guardados.';
                }

                $response['data'] = '';
                $response['total_records'] = '';

                $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }


    function deltabla_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                if($this->tabla_model->del_tabla($this->post('numero'))){
                    $response['status'] = 'OK';
                    $response['message'] = 'Datos eliminados correctamente.';
                } else {
                    $response['status'] = '-1';
                    $response['message'] = 'Los datos no fueron guardados.';
                }
                $response['data'] = '';
                $response['total_records'] = '';

                $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function catalogos_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $catalogos = $this->tabla_model->get_catalogos($this->post());

                $response['status'] = 'OK';
                $response['message'] = '';
                $response['data'] = $catalogos;
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