<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Diario extends REST_Controller {

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
        $this->load->model(array('diario_model', 'mayor_model', 'cuenta_model', 'balancesituacion_model'));
    }

    function asientos_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
               /* $data = array('select' => array('conta_diario.id','conta_diario.fecha','conta_diario.descripcion','round(conta_diario.importe, 2) as importe','round(conta_mayor.importe_debe, 2) as importe_debe','conta_cuentas.codigo as cuenta_debe',
                    'conta_cuentas.codigo as cuenta_haber','round(conta_mayor.importe_haber, 2) as importe_haber','conta_diario.created','conta_diario.modified','users.username', 'conta_cuentas.id as cuenta_id'));*/
                $data = array('select' => array('conta_diario.id','conta_diario.fecha','conta_diario.descripcion','round(conta_diario.importe, 2) as importe',
                    'conta_diario.created','conta_diario.modified','users.username'));
                $where = FALSE;
                $i = 0;
                $post_values = array(
                    'fecha' => $this->post('fecha'),
                    'descripcion' => $this->post('descripcion'),
                    'importe_debe' => $this->post('importe_debe'),
                    'importe_haber' => $this->post('importe_haber'),
                   // 'conta_cuentas.codigo' => $this->post('codigo_cuenta'),
                    'user_id' => $this->post('user_id'),
                    'created' => $this->post('created'),
                    'modified' => $this->post('modified'),
                    'conta_diario.activo' => TRUE
                );
                foreach($post_values as $key=>$value){
                    if($value != ''){
                        $where = TRUE;
                        $data['where'][$i]['field'] = $key;
                        $data['where'][$i]['value'] = $value;
                        $i++;
                    }
                }
                $asientos = $this->diario_model->get_asiento($data, 'conta_diario', $where, array($this->post('start'), $this->post('size')));

                if($asientos === false){
                    $this->response($this->data_error_response('00', 'Error recuperando los asientos.'), 500);
                } else {
                    $i = 0;
                    $select = array('round(conta_mayor.importe_debe, 2) as importe_debe','conta_cuentas.codigo as cuenta_debe',
                        'conta_cuentas.codigo as cuenta_haber','round(conta_mayor.importe_haber, 2) as importe_haber','conta_cuentas.id as cuenta_id');
                    foreach($asientos['data'] as $value){
                        $asientos_mayor = $this->diario_model->get_asientos_mayor($select, $value['id']);
                        $k = $l = 0;
                        foreach($asientos_mayor['data'] as $value_mayor){
                            if($value_mayor['importe_haber'] == 0.00){
                                $asientos['data'][$i]['cuentas_debe'][$k]['id'] = $value_mayor['cuenta_id'];
                                $asientos['data'][$i]['cuentas_debe'][$k]['codigo'] = $value_mayor['cuenta_debe'];
                                $asientos['data'][$i]['cuentas_debe'][$k]['importe'] = (double)$value_mayor['importe_debe'];
                                $k++;
                            } else {
                                $asientos['data'][$i]['cuentas_haber'][$l]['id'] = $value_mayor['cuenta_id'];
                                $asientos['data'][$i]['cuentas_haber'][$l]['codigo'] = $value_mayor['cuenta_haber'];
                                $asientos['data'][$i]['cuentas_haber'][$l]['importe'] = (double)$value_mayor['importe_haber'];
                                $l++;
                            }
                        }
                        $i++;
                    }
  //                  echo '<pre>';print_r($asientos_array);echo '</pre>'; die();
                    $response['status'] = 'OK';
                    $response['message'] = '';
                    $response['total_rows'] = $asientos['count'];
                    $response['data'] = $asientos['data'];
                    $this->response($response, 200);
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function asiento_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        $id_login_user = $this->_get_id_login_user($token);

        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $fecha = $this->post('fecha');
		$fecha = substr($fecha, 6, 4) . '-' . substr($fecha, 3, 2) . '-' . substr($fecha, 0, 2);
                $descripcion = $this->post('descripcion');
                $cuentas_debe = $this->post('cuentas_debe');
                $cuentas_haber = $this->post('cuentas_haber');
                $importe = (double)$this->post('importe');
                $created = $modified = date('Y-m-d H:i:s');
                $action = intval($this->post('action'));
                if($action == -1){
                    $data = array(
                        'fecha' => $fecha,
                        'descripcion' => $descripcion,
                        'importe' => $importe,
                        'user_id' => $id_login_user,
                        'created' => $created,
                        'modified' => $modified,
                        'activo' => TRUE
                    );
                    $save_id = $this->diario_model->add_asiento($data);
                    if($save_id == false){
                        $this->response($this->data_error_response('00', 'Error registrando el asiento.'), 500);
                    } else {
                        $data_mayor = array();
                        $i = 0;
                        foreach($cuentas_debe as $cuenta_debe){
                            $data_mayor[$i]['fecha'] = $fecha;
                            $data_mayor[$i]['importe_debe'] = (double)$cuenta_debe['importe'];
                            $data_mayor[$i]['importe_haber'] = 0;
                            $data_mayor[$i]['cuenta_id'] = intval($cuenta_debe['id']);
                            $data_mayor[$i]['diario_id'] = $save_id;
                            $data_mayor[$i]['user_id'] = $id_login_user;
                            $data_mayor[$i]['created'] = $created;
                            $data_mayor[$i]['modified'] = $modified;
                            $data_mayor[$i]['activo'] = TRUE;
                            $i++;
                        }
                        foreach($cuentas_haber as $cuenta_haber){
                            $data_mayor[$i]['fecha'] = $fecha;
                            $data_mayor[$i]['importe_debe'] = 0;
                            $data_mayor[$i]['importe_haber'] = (double)$cuenta_haber['importe'];
                            $data_mayor[$i]['cuenta_id'] = intval($cuenta_haber['id']);
                            $data_mayor[$i]['diario_id'] = $save_id;
                            $data_mayor[$i]['user_id'] = $id_login_user;
                            $data_mayor[$i]['created'] = $created;
                            $data_mayor[$i]['modified'] = $modified;
                            $data_mayor[$i]['activo'] = TRUE;
                            $i++;
                        }
                        $save_mayor_id = $this->mayor_model->add_asiento($data_mayor);
                        if($save_mayor_id == false){
                            $this->response($this->data_error_response('00', 'Error registrando el asiento.'), 500);
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
                        'value' => $action
                    );
                    $data = array(
                        'fecha' => $fecha,
                        'descripcion' => $descripcion,
                        'importe_debe' => $importe,
                        'importe_haber' => $importe,
                        'cuenta_debe_id' => $cuenta_debe_id,
                        'cuenta_haber_id' => $cuenta_haber_id,
                        'user_id' => $id_login_user,
                        'modified' => $modified,
                        'activo' => TRUE
                    );

                    $save = $this->diario_model->update_asiento($identity, $data);
                    if($save == false){
                        $this->response($this->data_error_response('00', 'Error salvando el asiento en Diario.'), 500);
                    } else {
                        $data= array(
                            'select' => 'id',
                            'where' => array(
                                0 => array(
                                    'field' => 'diario_id',
                                    'value' => $action
                                )
                            )
                        );
                        $result = $this->mayor_model->get_asiento($data, 'conta_mayor', true);
                        if($result['count'] > 0){
                            $data_mayor = array(
                                array(
                                    'id' => $result['data'][0]['id'],
                                    'fecha' => $fecha,
                                    'importe_debe' => $importe,
                                    'importe_haber' => 0,
                                    'cuenta_id' => $cuenta_debe_id,
                                    'user_id' => $id_login_user,
                                    'modified' => $modified,
                                    'activo' => TRUE
                                ),
                                array(
                                    'id' => $result['data'][1]['id'],
                                    'fecha' => $fecha,
                                    'importe_debe' => 0,
                                    'importe_haber' => $importe,
                                    'cuenta_id' => $cuenta_haber_id,
                                    'user_id' => $id_login_user,
                                    'modified' => $modified,
                                    'activo' => TRUE
                                )
                            );
                            $update_mayor = $this->mayor_model->update_batch_asiento($data_mayor);
                            if($update_mayor == false){
                                $this->response($this->data_error_response('00', 'Error registrando el asiento.'), 500);
                            } else {
                                $response['status'] = 'OK';
                                $response['message'] = null;
                                $response['data'] = null;
                                $this->response($response, 200);
                            }
                        } else {
                            $this->response($this->data_error_response('00', 'Error registrando el asiento.'), 500);
                        }
                    }
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function delasiento_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if ($session == -1) {
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $id = $this->post('id');
                $del = $this->diario_model->del_asiento($id);
                if($del == false){
                    $this->response($this->data_error_response('00', 'Error eliminando asiento.'), 500);
                } else {
                    $response['status'] = 'OK';
                    $response['message'] = null;
                    $response['data'] = null;
                    $this->response($response, 200);
                }
            }
        }
    }

    function cierrecontable_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        $id_login_user = $this->_get_id_login_user($token);
        $created = $modified = date('Y-m-d H:i:s');
        $fecha_post = $this->post('fecha');
	$fecha = substr($fecha_post,6,4) . '-'  . substr($fecha_post,3,2) . '-' .  substr($fecha_post,0,2) . ' 00:00:00';
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $save_mayor_grupo_6 = $this->_regularizar(array('6'), $fecha, $created, $modified, $id_login_user);
                if($save_mayor_grupo_6 == false){
                    $this->response($this->data_error_response('00', 'Error registrando regularizacion en mayor del grupo 6.'), 500);
                } else {
                    $save_mayor_grupo_7 = $this->_regularizar(array('7'), $fecha, $created, $modified, $id_login_user);
                    if($save_mayor_grupo_7 == false){
                        $this->response($this->data_error_response('00', 'Error registrando regularizacion en mayor del grupo 7.'), 500);
                    } else {
                        $cuentas_grupos_1_5 = $this->cuenta_model->get_cuentas_dado_grupo(array('1','2','3','4','5'));

                        //Balance de situación
                        $balance_situacion_detalle = $this->_balance_situacion($cuentas_grupos_1_5, $fecha, $created, $modified, $id_login_user);
                        if($balance_situacion_detalle){
                            //Asiento cierre
                            $save_mayor = $this->_asiento_cierre_apertura(TRUE, $cuentas_grupos_1_5, $fecha, $created, $modified, $id_login_user);
                            if($save_mayor === false){
                                $this->response($this->data_error_response('00', 'Error realizando asiento cierre.'), 500);
                            } else {
                                //Asiento apertura
                                $save_mayor = $this->_asiento_cierre_apertura(FALSE, $cuentas_grupos_1_5, $fecha, $created, $modified, $id_login_user);
                                if($save_mayor === false){
                                    $this->response($this->data_error_response('00', 'Error realizando asiento cierre.'), 500);
                                } else {
                                    $response['status'] = 'OK';
                                    $response['message'] = null;
                                    $response['data'] = null;
                                    $this->response($response, 200);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }


    private function _regularizar($grupo, $fecha, $created, $modified, $id_login_user){
        $cuentas_grupo = $this->cuenta_model->get_cuentas_dado_grupo($grupo);
        $data = array(
            'fecha' => $fecha,
            'descripcion' => 'Regularización de Resultados. Grupo ' . $grupo[0],
            'importe' => $cuentas_grupo['suma_saldos'],
            'user_id' => $id_login_user,
            'created' => $created,
            'modified' => $modified,
            'activo' => TRUE
        );
        $save_id = $this->diario_model->add_asiento($data);
        if($save_id === false){
            return false;
        } else {
            switch($grupo[0]){
                case '6':
                    $data_mayor = array();
                    $i = 0;
                    $data_mayor[$i]['fecha'] = $fecha;
                    $data_mayor[$i]['importe_debe'] = (double)$cuentas_grupo['suma_saldos'];
                    $data_mayor[$i]['importe_haber'] = 0;
                    $data_mayor[$i]['cuenta_id'] = 24;
                    $data_mayor[$i]['diario_id'] = $save_id;
                    $data_mayor[$i]['user_id'] = $id_login_user;
                    $data_mayor[$i]['created'] = $created;
                    $data_mayor[$i]['modified'] = $modified;
                    $data_mayor[$i]['activo'] = TRUE;

                    $i++;
                    foreach ($cuentas_grupo['data'] as $cuenta_haber) {
                        $data_mayor[$i]['fecha'] = $fecha;
                        $data_mayor[$i]['importe_debe'] = 0;
                        $data_mayor[$i]['importe_haber'] = (double)$cuenta_haber['saldo'];
                        $data_mayor[$i]['cuenta_id'] = intval($cuenta_haber['cuenta_id']);
                        $data_mayor[$i]['diario_id'] = $save_id;
                        $data_mayor[$i]['user_id'] = $id_login_user;
                        $data_mayor[$i]['created'] = $created;
                        $data_mayor[$i]['modified'] = $modified;
                        $data_mayor[$i]['activo'] = TRUE;
                        $i++;
                    }
                    $save_mayor_id = $this->mayor_model->add_asiento($data_mayor);
                    break;
                case '7':
                    $data_mayor = array();
                    $i = 0;
                    foreach ($cuentas_grupo['data'] as $cuenta_debe) {
                        $data_mayor[$i]['fecha'] = $fecha;
                        $data_mayor[$i]['importe_debe'] = (double)$cuenta_debe['saldo'];
                        $data_mayor[$i]['importe_haber'] = 0;
                        $data_mayor[$i]['cuenta_id'] = intval($cuenta_debe['cuenta_id']);
                        $data_mayor[$i]['diario_id'] = $save_id;
                        $data_mayor[$i]['user_id'] = $id_login_user;
                        $data_mayor[$i]['created'] = $created;
                        $data_mayor[$i]['modified'] = $modified;
                        $data_mayor[$i]['activo'] = TRUE;
                        $i++;
                    }

                    $data_mayor[$i]['fecha'] = $fecha;
                    $data_mayor[$i]['importe_debe'] = 0;
                    $data_mayor[$i]['importe_haber'] = (double)$cuentas_grupo['suma_saldos'];
                    $data_mayor[$i]['cuenta_id'] = 24;
                    $data_mayor[$i]['diario_id'] = $save_id;
                    $data_mayor[$i]['user_id'] = $id_login_user;
                    $data_mayor[$i]['created'] = $created;
                    $data_mayor[$i]['modified'] = $modified;
                    $data_mayor[$i]['activo'] = TRUE;

                    $save_mayor_id = $this->mayor_model->add_asiento($data_mayor);
                    break;
            }

            return $save_mayor_id;
        }
    }

    private function _balance_situacion($cuentas_grupos_1_5, $fecha, $created, $modified, $id_login_user){
        $suma_activo = $suma_pasivo = $i = 0;
        foreach($cuentas_grupos_1_5['data'] as $cuenta){
            if($cuenta['tipo_saldo'] == 0){
                $suma_activo = $suma_activo + $cuenta['saldo'];
                $cuentas_grupos_1_5['data'][$i]['saldo_deudor'] = $cuenta['saldo'];
                $cuentas_grupos_1_5['data'][$i]['saldo_acreedor'] = 0;
            } else {
                $suma_pasivo = $suma_pasivo + $cuenta['saldo'];
                $cuentas_grupos_1_5['data'][$i]['saldo_deudor'] = 0;
                $cuentas_grupos_1_5['data'][$i]['saldo_acreedor'] = $cuenta['saldo'];
            }
            $i++;
        }
        $data = array(
            'fecha' => $fecha,
            'activo' => $suma_activo,
            'pasivo' => $suma_pasivo,
            'user_id' => $id_login_user,
            'created' => $created,
            'modified' => $modified
        );
        $balance_situacion_id = $this->balancesituacion_model->add_balance($data);
        $i = 0;
        $data = array();
        foreach($cuentas_grupos_1_5['data'] as $cuenta){
            $data[$i]['cuenta_id'] = $cuenta['cuenta_id'];
            $data[$i]['balance_situacion_id'] = $balance_situacion_id;
            $data[$i]['saldo_deudor'] = $cuenta['saldo_deudor'];
            $data[$i]['saldo_acreedor'] = $cuenta['saldo_acreedor'];
            $i++;
        }
        $balance_situacion_detalle = $this->balancesituacion_model->add_balance_detalle($data);

        return $balance_situacion_detalle;
    }

    private function _asiento_cierre_apertura($cierre,$cuentas_grupos_1_5, $fecha, $created, $modified, $id_login_user){
        $descripción = 'Asiento Cierre';
        $fecha_contable = $fecha;
        if(!$cierre){
            $descripción = 'Asiento Apertura';

            //$fecha_aux = new DateTime(substr($fecha,6,4) . '-' . substr($fecha,3,2) . '-' . substr($fecha,0,2));
            $fecha_aux = new DateTime($fecha);
	    $fecha_aux->modify('+1 day');
            $fecha_contable = $fecha_aux->format('Y-m-d');
        }
        $data = array(
            'fecha' => $fecha_contable,
            'descripcion' => $descripción,
            'importe' => $cuentas_grupos_1_5['suma_saldos'],
            'user_id' => $id_login_user,
            'created' => $created,
            'modified' => $modified,
            'activo' => TRUE
        );
        $save_id = $this->diario_model->add_asiento($data);
        if($save_id === false){
            return false;
        } else {
            $i = 0;
            foreach ($cuentas_grupos_1_5['data'] as $cuenta) {
                $data_mayor[$i]['fecha'] = $fecha_contable;
                if ($cuenta['tipo_saldo'] == 0) {
                    if($cierre){
                        $data_mayor[$i]['importe_debe'] = 0;
                        $data_mayor[$i]['importe_haber'] = (double)$cuenta['saldo'];
                    } else {
                        $data_mayor[$i]['importe_debe'] = (double)$cuenta['saldo'];
                        $data_mayor[$i]['importe_haber'] = 0;
                    }
                } else {
                    if($cierre){
                        $data_mayor[$i]['importe_debe'] = (double)$cuenta['saldo'];
                        $data_mayor[$i]['importe_haber'] = 0;
                    } else {
                        $data_mayor[$i]['importe_debe'] = 0;
                        $data_mayor[$i]['importe_haber'] = (double)$cuenta['saldo'];
                    }
                }
                $data_mayor[$i]['cuenta_id'] = intval($cuenta['cuenta_id']);
                $data_mayor[$i]['diario_id'] = $save_id;
                $data_mayor[$i]['user_id'] = $id_login_user;
                $data_mayor[$i]['created'] = $created;
                $data_mayor[$i]['modified'] = $modified;
                $data_mayor[$i]['activo'] = TRUE;
                $i++;
            }
            $save_mayor = $this->mayor_model->add_asiento($data_mayor);
            if($save_mayor == false){
                return false;
            } else {
                if($cierre){
                    $this->mayor_model->desactivar_mayor();
                    $this->diario_model->desactivar_diario();
                }
            }

            return $save_mayor;
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

        return intval($result_user[0]['id']);
    }
}
