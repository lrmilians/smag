<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Esign extends REST_Controller {

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
        $this->load->model('auth/user_model');
        $this->load->model('esign_model');
        $this->load->helper('download');
        $this->load->library('xml2pdf/xml_pdf');
        $params = array('produccion' => AMBIENTE_PRODUCCION, 'offline' => AMBIENTE_OFFLINE);
        $this->load->library('sri_webservices/sri_webservice', $params);
        $this->load->library('ion_auth');
    }

    function adddoc_post(){
            $token = $this->get('token');
            $session = $this->_checksession($token);
            if($session == -1){
                $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
            } else {
                if($session == 0){
                    if(!empty($_FILES['file'])){
                        $config['upload_path'] = PATH_TMP;
                        $old = umask(0);
                        if(!is_dir(PATH_TMP)){
                            mkdir(PATH_TMP, 0755, true);
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

                        if(!$this->upload->do_upload('file')){
                            $this->response($this->data_error_response('00', 'Error subiendo archivo.'), 500);
                        }else{
                            $data_xml = $this->xml_pdf->get_xml_data(PATH_TMP . '/' . $file_name);
                            if($data_xml == false){
                                $this->response($this->data_error_response('00', 'Error proceso.'), 500);
                            } else {
                                if($this->post('cod_doc') == $data_xml['cod_doc']){
                                    $doc_exist = $this->esign_model->get_doc_exist('ebill_signing_bills', $data_xml['access_key'] . '.xml');
                                    if(!$doc_exist){
                                        $path_date = $data_xml['year'] . '-' . $data_xml['month'] . '-' . $data_xml['day'];
                                        $path_save = PATH_XMLS . '/facturas/' . $data_xml['year'] . '/' . $data_xml['month'] . '/' . $path_date . '/' . 'nofirmada';
                                        if($this->_move_file(PATH_TMP . '/' . $file_name, $path_save, $data_xml['access_key'] . '.xml')){
                                            $data_u = array(
                                                'select' => array('users.id'),
                                                'field' => 'identification',
                                                'value' => $data_xml['client_ident']
                                            );
                                            $user = $this->user_model->get_user($data_u, 'users');
                                            $data = array(
                                                'name' => $data_xml['access_key'] . '.xml',
                                                'processed' => 0,
                                                'state' => 'NO FIRMADO',
                                                'num_doc' => $data_xml['num_doc'],
                                                'created' => date('Y-m-d H:i:s'),
                                                'modified' => date('Y-m-d H:i:s')
                                            );
                                            if($user == FALSE){
                                                $user_id = $this->_add_client($data_xml);
                                                $data['user_id'] = $user_id;
                                                $savedoc_id = $this->esign_model->add_doc('ebill_signing_bills', $data, TRUE);
                                                if($savedoc_id == FALSE){
                                                    $this->response($this->data_error_response('00', 'Error salvando el documento.'), 500);
                                                } else {
                                                    $path_esign =  $data_xml['year'] . '/' . $data_xml['month'] . '/' . $path_date;
                                                    $esign = $this->sign_xml($file_name, $this->get_document_namedir_by_code($data_xml['cod_doc']), $path_esign);
                                                    if ($esign) {
                                                        $esign_result['estado'] = 'FIRMADO';
                                                        $esign_result['mensajes'][0]['identificador'] = '99';
                                                        $esign_result['mensajes'][0]['mensaje'] = 'Documento no firmado.';
                                                        $esign_result['mensajes'][0]['tipo'] = 'ERROR';
                                                        $esign_result['mensajes'][0]['informacion_adicional'] = '';
                                                        $this->update_xml($file_name, $savedoc_id, $esign_result);
                                                        $response['status'] = 'OK';
                                                        $response['message'] = null;
                                                        $response['data'] = null;
                                                        $this->response($response, 200);
                                                    } else {
                                                        $esign_result['estado'] = 'NO FIRMADO';
                                                        $esign_result['mensajes'][0]['identificador'] = '99';
                                                        $esign_result['mensajes'][0]['mensaje'] = 'Documento no firmado.';
                                                        $esign_result['mensajes'][0]['tipo'] = 'ERROR';
                                                        $esign_result['mensajes'][0]['informacion_adicional'] = '';
                                                        $this->update_xml($file_name, $savedoc_id, $esign_result);
                                                        $response['status'] = '-1';
                                                        $response['message'] = 'No firmado';
                                                        $response['data'] = array('firmado' => $esign);
                                                        $this->response($response, 200);
                                                    }
                                                }
                                            } else {
                                                $data['user_id'] = intval($user[0]['id']);
                                                $save = $this->esign_model->add_doc('ebill_signing_bills', $data);
                                                if($save){
                                                    $response['status'] = 'OK';
                                                    $response['message'] = null;
                                                    $response['data'] = null;
                                                    $this->response($response, 200);
                                                }
                                            }
                                        }
                                    } else {
                                        unlink(PATH_TMP . '/' . $file_name);
                                        $this->response($this->data_error_response('00', 'El documento ya existe en la base de datos.'), 500);
                                    }
                                } else {
                                    unlink(PATH_TMP . '/' . $file_name);
                                    $this->response($this->data_error_response('00', 'El documento subido no es una ' . $this->post('doc_type')), 500);
                                }
                            }
                        }
                    } else {
                        $this->response($this->data_error_response('00', 'Error subiendo archivo.'), 500);
                    }
                } else {
                    $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
                }
            }
        }

    function processdocs_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if($session == 0){
                $doc_type = $this->post('doc_type');  //$path_domain
                $xml_name = $this->post('name');
                $xml_id = $this->post('id');
                $access_key = substr($xml_name, 0, 49);
                $xml_year = substr($xml_name, 4, 4);
                $xml_month = substr($xml_name, 2, 2);
                $xml_date = $xml_year . '-' . $xml_month . '-' . substr($xml_name, 0, 2); //$date_now
                $path_full = PATH_XMLS . '/' . $doc_type .'/' . $xml_year . '/' . $xml_month . '/' . $xml_date;

                $path_xml_signed = $path_full . '/firmada';
                $path_xml_back = $path_full . '/devuelta';
                $path_xml_auth = $path_full . '/autorizada';
                $path_xml_pdf = $path_full . '/pdf';

                $validate_bill_result = $this->sri_webservice->validarComprobante($xml_name, $path_xml_signed, $path_xml_back);
                switch ($validate_bill_result['estado']) {
                    case "RECIBIDA":
                        $authorize_bill_result = $this->sri_webservice->autorizarComprobante($access_key, $path_xml_auth);
                        switch ($authorize_bill_result['estado']) {
                            case "AUTORIZADO":
                                $this->update_xml($xml_name, $xml_id, $authorize_bill_result);
                                $this->save_xml_pdf($xml_name, $path_xml_pdf, $path_xml_auth);
                                //$this->delete_xml($xml_name);
                                break;
                            default:
                                $this->update_xml($xml_name, $xml_id, $authorize_bill_result);
                                break;
                        }
                }
                $response['status'] = 'OK';
                $response['message'] = null;
                $response['data']['validate_result'] = $validate_bill_result;
                $response['data']['authorize_result'] = $authorize_bill_result;
                $this->response($response, 200);

            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }
    }

    function esignfile_get(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $file_name = $this->get('name');
                $access_key = str_replace('.xml', '', $file_name);
                switch($this->get('typefile')){
                    case 'xml':
                        $path = PATH_XMLS . '/' . $this->get_document_namedir_by_code($this->get('type')) . '/' . $this->_get_path_xml($file_name) . '/nofirmada/' . $access_key . '.xml';
                        $file_name = $access_key . '.xml';
                        break;
                    case 'pdf':
                        $path = PATH_XMLS . '/' . $this->get_document_namedir_by_code($this->get('type')) . '/' . $this->_get_path_xml($file_name) . '/pdf/' . $access_key . '.pdf';
                        $file_name = $access_key . '.pdf';
                        break;
                    case 'zip':
                        $path = PATH_XMLS . '/' . $this->get_document_namedir_by_code($this->get('type')) . '/' . $this->_get_path_xml($file_name) . '/autorizada/' . $access_key . '.zip';
                        $file_name = $access_key . '.zip';
                        break;
                }

                $data = file_get_contents($path);
                force_download($file_name, $data);
                $response['status'] = 'OK';
                $this->response($response, 200);
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }

    }

    function getdocstat_post(){
        $token = $this->get('token');
        $session = $this->_checksession($token);
        if($session == -1){
            $this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
        } else {
            if ($session == 0) {
                $tables = array(
                    '01' => 'ebill_signing_bills'
                );
                $result = $this->esign_model->get_doc_stat($tables, intval($this->post('user_id')));
                $response['status'] = 'OK';
                $response['message'] = '';
                $response['data'] = $result;
                $this->response($response, 200);
            } else {
                $this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
            }
        }

    }



    private function sign_xml($xml_name, $doc_type, $path_esign) {
        $result = 'Error';
        exec('sh /usr/bin/esignature ' . $xml_name . ' ' . $doc_type . ' ' . $path_esign, $result);
        if(!empty($result) && $result[0] == "Done"){
            return true;
        }
        return false;
    }

    private function update_xml($xml_name, $xml_id,  $authorize_bill_result) {
        $tables_name = $this->get_database_by_document_code($this->get_document_code_by_filename($xml_name));

        $this->esign_model->update_xml($xml_name, $xml_id, $authorize_bill_result, $tables_name);
    }

    private function get_database_by_document_code($code){
        $tables_name = array();
        switch($code){
            case '01':
                $tables_name['0'] = 'ebill_signing_bills';
                $tables_name['1'] = 'ebill_signing_bill_messages';
                $tables_name['2'] = 'bill_id';
                return $tables_name;
            case '04':
                $tables_name['0'] = 'ebill_signing_creditnotes';
                $tables_name['1'] = 'ebill_signing_creditnote_messages';
                $tables_name['2'] = 'creditnote_id';
                return $tables_name;
            case '05':
                $tables_name['0'] = 'ebill_signing_debitnotes';
                $tables_name['1'] = 'ebill_signing_debitnote_messages';
                $tables_name['2'] = 'debitnote_id';
                return $tables_name;
            case '06':
                $tables_name['0'] = 'ebill_signing_remitionguides';
                $tables_name['1'] = 'ebill_signing_remitionguide_messages';
                $tables_name['2'] = 'remitionguide_id';
                return $tables_name;
            case '07':
                $tables_name['0'] = 'ebill_signing_retentions';
                $tables_name['1'] = 'ebill_signing_retention_messages';
                $tables_name['2'] = 'retention_id';
                return $tables_name;
            default:
                break;
        }
    }

    private function get_document_code_by_filename($filename){
        if($filename != ''){
            return substr($filename, 8, 2);
        }
        return '';
    }

    private function get_document_name_by_code($document_code){
        switch ($document_code) {
            case '01':
                return 'FACTURA';
            case '04':
                return 'NOTA DE CREDITO';
            case '05':
                return 'NOTA DE DEBITO';
            case '06':
                return 'GUIA DE REMISION';
            case '07':
                return 'COMPOBANTE DE RETENCION';
            default:
                break;
        }
    }

    private function get_document_namedir_by_code($document_code){
        switch ($document_code) {
            case '01':
                return 'facturas';
            case '04':
                return 'notas_credito';
            case '05':
                return 'notas_debito';
            case '06':
                return 'guia_remision';
            case '07':
                return 'retenciones';
            default:
                break;
        }
    }

    private function save_xml_pdf($xml_name, $path_xml_pdf, $path_xml_auth) {
        if(!is_dir($path_xml_pdf)){
            mkdir($path_xml_pdf, 0777, true);
        }
        $xml_path = $path_xml_auth . '/' . $xml_name;
        $this->xml_pdf->save_xml_pdf($xml_path, $xml_name, $path_xml_pdf);
    }

    private function _add_client($data_xml) {
        $username_s = explode('@', $data_xml['client_email']);
        $data = array (
            'username' => $username_s[0],
            'password' => $this->ion_auth->hash_password($data_xml['client_ident'].'.ec', ''),
            'email' => $data_xml['client_email'],
            'created_on' => strtotime(date('Y-m-d H:i:s')),
            'active' => 1,
            'first_name' => $data_xml['client_name'],
            'phone' => $data_xml['client_phone'],
            'rol_id' => 3,
            'identification' => $data_xml['client_ident'],
            'address' => $data_xml['client_address'],
            'salt' => ''
        );
        $save = $this->user_model->add_user($data, TRUE);
        if($save == false){
            return false;
        }
        return $save;

    }

    private function _move_file($path_o, $path_d, $file_name) {
        $old = umask(0);
        if(!is_dir($path_d)){
            mkdir($path_d, 0755, true);
            umask($old);
        }
        if(rename($path_o, $path_d . '/' .$file_name)){
            return true;
        }
        return false;
    }

    private function _get_path_xml($xml_name){
        $year = substr($xml_name, 4, 4);
        $month = substr($xml_name, 2, 2);
        $day = substr($xml_name, 0, 2);
        $date = $year . '-' . $month . '-' . $day;

        return $year . '/' . $month . '/' . $date;
    }

    private function _checksession($token) {
        //result = 0  Session actived
        //result = 1  Session expired
        //result = -1  Error
        return $this->session_model->check_session($token);
    }
}