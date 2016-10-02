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
        $this->load->helper(array('url','language'));
        $this->load->config('myconfig', TRUE);

        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
       // $this->methods['cuentas_post']['limit'] = 100; //500 requests per hour per user/key
        //	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

        $this->load->model(array('auth/session_model','producto_model','admin/tabla_model', 'admin/util_model'));
    }

    function producto_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
        } else {
            if ($session == 0) {
                $data = $this->post();
                $productos = $this->producto_model->get_productos($data);
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

    function setproducto_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
           $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
        } else {
            if ($session == 0) {
                if($this->post('action') !== '-1'){
                    $producto = $this->post('producto');
                    $data = array(
                        'nombre' => $producto['nombre'],
                        'codigo' => $producto['codigo'],
                        'codigo_barras' => $producto['codigo_barras'],
                        'categoria' => $producto['categoria'],
                        'tipo_producto' => $producto['tipo_producto'],
                        'marca' => $producto['marca'],
                        'modelo' => $producto['modelo'],
                        'unidad_medida' => $producto['unidad_medida'],
                        'precio_venta' => $producto['precio_venta'],
                        'iva' => $producto['iva'],
                        'estado' => $producto['estado'],
                        'modificado' => date('Y-m-d H:i:s'),
                        'user_modificado' => $this->post('userId'),
                        'referencia' => $producto['referencia'],
                        'descripcion' => $producto['descripcion'],
                        'stock_actual' => $producto['stock_actual'],
                        'stock_minimo' => $producto['stock_minimo'],
                        'stock_maximo' => $producto['stock_maximo'],
                        'ubicacion' => $producto['ubicacion'],
                        'costo_ultima_compra' => $producto['costo_ultima_compra'],
                        'costo_primera_compra' => $producto['costo_primera_compra'],
                        'ice_compras' => $producto['ice_compras'],
                        'ice_ventas' => $producto['ice_ventas'],
                        'peso' => $producto['peso'],
                        'factor_hora_hombre' => $producto['factor_hora_hombre'],
                        'altura' => $producto['altura'],
                        'longitud' => $producto['longitud'],
                        'profundidad' => $producto['profundidad']
                    );
                    $valida_campos = $this->validar_campos($data);
                    if($valida_campos === true){
                        $data = $this->util_model->set_valor_null($data);
                        if($this->producto_model->update_producto($data, $this->post('action'))){
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
                    $data = $this->post('producto');
                    $data['creado'] = $fecha_creado;
                    $data['modificado'] = $fecha_modificado;
                    $data['user_creado'] = $this->post('userId');
                    $data['user_modificado'] = $this->post('userId');
                    $data = $this->util_model->set_valor_null($data);
                    $valida_campos = $this->validar_campos($data);
                    if($valida_campos === true){
                        if($this->producto_model->add_producto($data)){
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