<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clientes extends REST_Controller {

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
        $this->load->helper(array('url','language'));
        $this->load->config('myconfig', TRUE);

        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
       // $this->methods['cuentas_post']['limit'] = 100; //500 requests per hour per user/key
        //	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        $this->load->model(array('auth/session_model','cliente_model','admin/tabla_model', 'admin/util_model'));
    }

    function cliente_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
        } else {
            if ($session == 0) {
                $data = $this->post();
                $productos = $this->cliente_model->get_clientes($data);
                $catalogos = $this->tabla_model->get_catalogos($data['catalogos']);
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $productos['data'];
                    $response['catalogos'] = $catalogos;
                    $response['total_records'] = $productos['total_records'];

                    $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function setcliente_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
           $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
        } else {
            if ($session == 0) {
                if($this->post('action') !== '-1'){
                    $post_data = $this->post('cliente');
                   // echo '<pre>';
        //print_r($post_data);
        /*var_dump($post_data);
        echo '<pre>';die();*/
                    $data['persona']['codigo'] = $post_data['codigo'];
                    $data['persona']['direccion'] = $post_data['direccion'];
                    $data['persona']['email'] = $post_data['email'];
                    $data['persona']['identificacion'] = $post_data['identificacion'];
                    $data['persona']['telefono'] = $post_data['telefono'];
                    $data['persona']['tipo_identificacion'] = $post_data['tipo_identificacion'];
                    $data['persona']['razon_social'] = $post_data['razon_social'];
                    $data['persona']['modificado'] = date('Y-m-d H:i:s');
                    $data['persona']['user_modificado'] = $this->post('userId');

                    $data['cliente']['parte_relacionada'] = $post_data['parte_relacionada'];
                    $data['cliente']['condicion_pago'] = $post_data['condicion_pago'];

                    $valida_campos = true;
                    //$valida_campos = $this->validar_campos($data);
                    if($valida_campos === true){
                        $data['persona'] = $this->util_model->set_valor_null($data['persona']);
                        $data['cliente'] = $this->util_model->set_valor_null($data['cliente']);
                        if($this->cliente_model->update_cliente($data, $this->post('action'))){
                            $response['status'] = 'OK';
                            $response['message'] = 'Datos actualizados correctamente.';
                        } else {
                            $response['status'] = '-1';
                            $response['message'] = 'Los datos no fueron actualizados.';
                        }
                    } else {
                        $response['status'] = '-1';
                        $response['message'] = '<p>Valores de campos no validos.</p>';
                        foreach($valida_campos as $campo){
                            $response['message'] .= '<p><strong>Campo: </strong>' . $campo .'<p/>';
                        }
                    }
                } else {
                    $fecha_creado = $fecha_modificado = date('Y-m-d H:i:s');
                    $post_data = $this->post('cliente');
                    $data['persona']['codigo'] = $post_data['codigo'];
                    $data['persona']['direccion'] = $post_data['direccion'];
                    $data['persona']['email'] = $post_data['email'];
                    $data['persona']['identificacion'] = $post_data['identificacion'];
                    $data['persona']['telefono'] = $post_data['telefono'];
                    $data['persona']['tipo_identificacion'] = $post_data['tipo_identificacion'];
                    $data['persona']['razon_social'] = $post_data['razon_social'];
                    $data['persona']['creado'] = $fecha_creado;
                    $data['persona']['modificado'] = $fecha_modificado;
                    $data['persona']['user_creado'] = $this->post('userId');
                    $data['persona']['user_modificado'] = $this->post('userId');

                    $data['cliente']['parte_relacionada'] = $post_data['parte_relacionada'];
                    $data['cliente']['condicion_pago'] = $post_data['condicion_pago'];

                    $data['persona'] = $this->util_model->set_valor_null($data['persona']);
                    $data['cliente'] = $this->util_model->set_valor_null($data['cliente']);
                    $valida_campos = true;
                    //$valida_campos = $this->validar_campos($data['persona']);
                    if($valida_campos === true){
                        if($this->cliente_model->add_cliente($data)){
                            $response['status'] = 'OK';
                            $response['message'] = 'Datos guardados correctamente.';
                        } else {
                            $response['status'] = '-1';
                            $response['message'] = 'Los datos no fueron guardados.';
                        }
                    } else {
                        $response['status'] = '-1';
                        $response['message'] = '<p>Valores de campos no validos.</p>';
                        foreach($valida_campos as $campo){
                            $response['message'] .= '<p><strong>Campo: </strong>' . $campo .'<p/>';
                        }
                    }
                }
                $response['data'] = '';
                $response['total_records'] = '';
                $this->response($response, 200);
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }


    private function validar_campos($data){
        if(!empty($data)){
            $expresiones_regulares = $this->config->item('expresiones_regulares', 'myconfig');
            $result = array();

            if(!preg_match($expresiones_regulares['nombreProducto'], $data['nombre']) && !empty($data['nombre'])){
                $result[] = 'Nombre';
            }
            if(!preg_match($expresiones_regulares['codigoProducto'], $data['codigo']) && !empty($data['codigo'])){
                $result[] = 'Codigo';
            }
            if(!preg_match($expresiones_regulares['codigoBarrasProducto'], $data['codigo_barras']) && !empty($data['codigo_barras'])){
                $result[] = 'Codigo Barras';
            }
            if(!preg_match($expresiones_regulares['referenciaProducto'], $data['referencia']) && !empty($data['referencia'])){
                $result[] = 'Referencia';
            }
            if(!preg_match($expresiones_regulares['descripcionProducto'], $data['descripcion']) && !empty($data['descripcion'])){
                $result[] = 'Descripcion';
            }
            if(!preg_match($expresiones_regulares['decimal186'], $data['precio_venta']) && !empty($data['precio_venta'])){
                $result[] = 'Precio Venta';
            }
            if(!preg_match($expresiones_regulares['decimal126'], $data['stock_actual']) && !empty($data['stock_actual'])){
                $result[] = 'Stock Actual';
            }
            if(!preg_match($expresiones_regulares['decimal126'], $data['stock_minimo']) && !empty($data['stock_minimo'])){
                $result[] = 'Stock Minimo';
            }
            if(!preg_match($expresiones_regulares['decimal126'], $data['stock_maximo']) && !empty($data['stock_maximo'])){
                $result[] = 'Stock Maximo';
            }
            if(!preg_match($expresiones_regulares['texto'], $data['ubicacion']) && !empty($data['ubicacion'])){
                $result[] = 'Ubicacion';
            }
            if(!preg_match($expresiones_regulares['decimal186'], $data['costo_ultima_compra']) && !empty($data['costo_ultima_compra'])){
                $result[] = 'Costo Ultima Compra';
            }
            if(!preg_match($expresiones_regulares['decimal186'], $data['costo_primera_compra']) && !empty($data['costo_primera_compra'])){
                $result[] = 'Costo Primera Compra';
            }
            if(!preg_match($expresiones_regulares['decimal102'], $data['altura']) && !empty($data['altura'])){
                $result[] = 'Altura';
            }
            if(!preg_match($expresiones_regulares['decimal102'], $data['longitud']) && !empty($data['longitud'])){
                $result[] = 'Longitud';
            }
            if(!preg_match($expresiones_regulares['decimal102'], $data['profundidad']) && !empty($data['profundidad'])){
                $result[] = 'Profundidad';
            }
            if(!preg_match($expresiones_regulares['decimal166'], $data['peso']) && !empty($data['peso'])){
                $result[] = 'Peso';
            }
            if(!preg_match($expresiones_regulares['decimal62'], $data['factor_hora_hombre']) && !empty($data['factor_hora_hombre'])){
                $result[] = 'Factor hora hombre';
            }
            if(!empty($result)){
                return $result;
            }
        }
        return true;
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