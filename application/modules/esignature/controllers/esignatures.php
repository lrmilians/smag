<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('display_errors', '1');

class esignatures extends MY_Controller {
   
    public function __construct() {
        parent::__construct();      
        $this->load->model("bill_model");
        $this->load->model("esignature_model");
        $params = array('produccion' => FALSE);
        $this->load->library('sri_webservices/sri_webservice', $params);
	    $this->load->helper(array('download', 'file', 'url', 'html', 'form'));
    }
    
    
    function process(){
        $date_now = date("Y-m-d");
        $xml_name = $this->input->post('name');
        $xml_id = $this->input->post('id');
        $path_domain = $this->input->post('path_domain');
        $path_full = $this->config->item('path_xml'). '/' . $path_domain .'/' . substr($date_now,0,4) . '/' . $date_now;
       
        $path_xml_signed = $path_full . '/firmada';
        $path_xml_back = $path_full . '/devuelta';
        $path_xml_auth = $path_full . '/autorizada';
        $path_xml_pdf = $path_full . '/pdf';
        $logo_name = 'asdfsdfdsfds.png';
       
        if ($this->sign_xml($xml_name, $path_domain)) {
            $validate_bill_result = $this->sri_webservice->validarComprobante($xml_name, $path_xml_signed, $path_xml_back);
            switch ($validate_bill_result['estado']) {
                case "RECIBIDA":
                    $authorize_bill_result = $this->sri_webservice->autorizarComprobante(str_replace('.xml', '', $xml_name), $path_xml_auth);
                    switch ($authorize_bill_result['estado']) {
                        case "AUTORIZADO":
                            $this->update_xml($xml_name, $xml_id, $authorize_bill_result);
                            $this->save_xml_pdf($xml_name, $path_xml_pdf, $path_xml_auth, $logo_name);
                            //$this->delete_xml($xml_name);
                            return true;
                        default:
                            $this->update_xml($xml_name, $xml_id, $authorize_bill_result);
                            return false;
                    }
                    break;
                default:
                    $this->update_xml($xml_name, $xml_id, $validate_bill_result);
                    return false;
            }
        } else {
            $esign_result['estado'] = 'NO FIRMADO';
            $esign_result['mensajes'][0]['identificador'] = '99';
            $esign_result['mensajes'][0]['mensaje'] = 'Documento no firmado.';
            $esign_result['mensajes'][0]['tipo'] = 'ERROR';
            $esign_result['mensajes'][0]['informacion_adicional'] = '';
            $this->update_xml($xml_name, $xml_id, $esign_result);
            return false;
        }
    }
   
    
    private function sign_xml($xml_name, $path_domain) {
        $result = 'Error';
        exec('sh /usr/bin/esignature ' . $xml_name . ' ' . 'esignature' . ' ' . $path_domain, $result);
        if(!empty($result) && $result[0] == "Done"){
            return true; 
        } 
        return false;
    }
    
    private function update_xml($xml_name, $xml_id,  $authorize_bill_result) {
        $tables_name = $this->get_database_by_document_code($this->get_document_code_by_filename($xml_name));
        
        $this->esignature_model->updateXML($xml_name, $xml_id, $authorize_bill_result, $tables_name);
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
    
    private function save_xml_pdf($xml_name, $path_xml_pdf, $path_xml_auth, $logo_name) {
        if(!is_dir($path_xml_pdf)){      
            mkdir($path_xml_pdf, 0777, true);
        }      
        $xml_path = $path_xml_auth . '/' . $xml_name;
        $this->load->library('xml2pdf/xml_pdf');
        $this->Xml_pdf->save_xml_pdf($xml_path, $xml_name, $path_xml_pdf, $logo_name);
    }

public function download($domain, $year, $day, $xml_name, $type){
	$path = 'uploads/xml/' . $domain . '/' .  $year . '/' .  $day . '/' . $type . '/';        
        $data = file_get_contents($path . $xml_name);
	force_download($xml_name, $data);
    }
    
}