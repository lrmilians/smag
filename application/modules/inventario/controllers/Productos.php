<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Productos extends REST_Controller {

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
        $this->load->model(array('producto_model'));
    }

    function producto_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $data = $this->post();
                $productos = $this->producto_model->get_productos($data);

                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['data'] = $productos['data'];
                    $response['total_records'] = $productos['total_records'];

                    $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function setproducto_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                if($this->post('action') !== '-1'){


                } else {
                    $fecha_creado = $fecha_modificado = date('Y-m-d H:i:s');
                    $producto = $this->post('producto');
                    $data = array(
                        'nombre' => $producto['nombre'],
                        'codigo' => $producto['codigo'],
                        'codigo_barras' => $producto['codigoBarras'],
                        'categoria' => $producto['categoriaProducto'],
                        'tipo_producto' => $producto['tipoProducto'],
                        'marca' => $producto['marcaProducto'],
                        'modelo' => $producto['modeloProducto'],
                        'unidad_medida' => $producto['unidadMedida'],
                        'precio_venta' => $producto['precioVenta'],
                        'iva' => $producto['iva'],
                        'estado' => $producto['estado'],
                        'creado' => $fecha_creado,
                        'modificado' => $fecha_modificado,
                        'user_creado' => $this->post('userId'),
                        'user_modificado' => $this->post('userId'),
                        'referencia' => $producto['referencia'],
                        'descripcion' => $producto['descripcion'],
                        'stock_actual' => $producto['stockActual'],
                        'stock_minimo' => $producto['stockMinimo'],
                        'stock_maximo' => $producto['stockMaximo'],
                        'ubicacion' => $producto['ubicacion'],
                        'costo_ultima_compra' => $producto['costoUltimaCompra'],
                        'costo_primera_compra' => $producto['costoPrimeraCompra'],
                        'ice_compras' => $producto['iceCompra'],
                        'ice_ventas' => $producto['iceVenta'],
                        'peso' => $producto['peso'],
                        'factor_hora_hombre' => $producto['factorHoraHombre']
                    );
                    if($this->producto_model->set_producto($data)){
                        $response['status'] = 'OK';
                        $response['message'] = 'Datos guardados correctamente.';
                    } else {
                        $response['status'] = '-1';
                        $response['message'] = 'Los datos no fueron guardados.';
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