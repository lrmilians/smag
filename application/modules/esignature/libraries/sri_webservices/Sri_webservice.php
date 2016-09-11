<?php
require APPPATH . "modules/esignature/libraries/sri_webservices/RecepcionComprobantesService.php";
require APPPATH . "modules/esignature/libraries/sri_webservices/AutorizacionComprobantesService.php";
require APPPATH . "modules/esignature/libraries/zip/Zip.php";

/*ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);*/


class Sri_webservice {
    
    protected $wsdlRecepcion;
    protected $wsdlAutorizacion;
    
    public function __construct($params) {
        if ($params['produccion']){
            if($params['offline']){
                $this->wsdlRecepcion = WS_PRODUCCION_RECEPCION_OFFLINE;
                $this->wsdlAutorizacion = WS_PRODUCCION_AUTORIZACION_OFFLINE;
            } else {
                $this->wsdlRecepcion = WS_PRODUCCION_RECEPCION;
                $this->wsdlAutorizacion = WS_PRODUCCION_AUTORIZACION;
            }
        } else {
            if($params['offline']){
                $this->wsdlRecepcion = WS_PRUEBA_RECEPCION_OFFLINE;
                $this->wsdlAutorizacion = WS_PRUEBA_AUTORIZACION_OFFLINE;
            } else {
                $this->wsdlRecepcion = WS_PRUEBA_RECEPCION;
                $this->wsdlAutorizacion = WS_PRUEBA_AUTORIZACION;
            }
        }
    }

    public function validarComprobante($nombreComprobante, $path_xml_signed, $path_xml_back) {
        try { 
            $nombreComprobantePath = $path_xml_signed . '/' . $nombreComprobante;
            $comprobante = fopen($nombreComprobantePath, 'r');
            $contenidoComprobante = fread($comprobante, filesize($nombreComprobantePath));
            fclose($comprobante);
            $recepcion = new RecepcionComprobantesService($this->wsdlRecepcion);
            $xml = new validarComprobante();
            $xml->xml = $contenidoComprobante;
            $respuestaRecepcion = $recepcion->validarComprobante($xml);
            if (!is_soap_fault($respuestaRecepcion)){
                $estado = $respuestaRecepcion->RespuestaRecepcionComprobante->estado;  
                switch ($estado) {
                    case "RECIBIDA":
                        $resultado['estado'] = $estado;
                        break; 
                    case "DEVUELTA":
                        $resultado['estado'] = $estado;
                        $mensaje = $respuestaRecepcion->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje;
                        $resultado['mensajes'][0]['identificador'] = $mensaje->identificador;
                        $resultado['mensajes'][0]['mensaje'] = $mensaje->mensaje;
                        $resultado['mensajes'][0]['tipo'] = $mensaje->tipo;
                        $resultado['mensajes'][0]['informacion_adicional'] = $mensaje->informacionAdicional;
                        if(!is_dir($path_xml_back)){      
                            mkdir($path_xml_back, 0777, true);
                        }
                        $this->crearXMLRespuestaRecepcionSRI($respuestaRecepcion->RespuestaRecepcionComprobante, $path_xml_signed, $nombreComprobante, $path_xml_back);
                        break;
                    default:
                        $resultado['estado'] = "ERROR PROCESO";
                        $resultado['mensajes'][0]['identificador'] = '98';
                        $resultado['mensajes'][0]['mensaje'] = 'Documento no devuelto ni recibido.';
                        $resultado['mensajes'][0]['tipo'] = 'ERROR';
                        $resultado['mensajes'][0]['informacion_adicional'] = '';
                        break;
                }
                return $resultado;
            } else {
                $resultado['estado'] = "FUERA DE SERVICIO";
                $resultado['mensajes'][0]['identificador'] = '97';
                $resultado['mensajes'][0]['mensaje'] = 'Fuera de Servicio.';
                $resultado['mensajes'][0]['tipo'] = 'ERROR';
                $resultado['mensajes'][0]['informacion_adicional'] = '';
            }  
        } catch (Exception $ex) {
            $resultado['estado'] = "FUERA DE SERVICIO";
            $resultado['mensajes'][0]['identificador'] = '97';
            $resultado['mensajes'][0]['mensaje'] = 'Fuera de Servicio.';
            $resultado['mensajes'][0]['tipo'] = 'ERROR';
            $resultado['mensajes'][0]['informacion_adicional'] = '';
        }
        return $resultado;    
    }

    private function crearXMLRespuestaRecepcionSRI($respuestaRecepcion, $path_xml_signed, $nombreComprobante, $directorioAlmacenamiento) {
        $directorioComprobante = $path_xml_signed . '/' . $nombreComprobante;
        
        $comprobante = new DOMDocument('1.0', 'UTF-8');
        $comprobante->load($directorioComprobante);
        $factura = $comprobante->getElementsByTagName($this->obtenerTipoDocumento($nombreComprobante));

        $respuesta = $comprobante->createElement("respuestaSolicitud");
        $estado = $comprobante->createElement("estado", $respuestaRecepcion->estado);
        $respuesta->appendChild($estado);

        $comprobantes = $comprobante->createElement("comprobantes");
        $datosComprobante = $comprobante->createElement("comprobante");

        $comprobanteRespuesta = $respuestaRecepcion->comprobantes->comprobante;
        $claveAcceso = $comprobante->createElement("claveAcceso", $comprobanteRespuesta->claveAcceso);
        $datosComprobante->appendChild($claveAcceso);

        $mensajes = $comprobante->createElement('mensajes');
        $mensajesRespuesta = $comprobanteRespuesta->mensajes;
        foreach ($mensajesRespuesta as $datosMensaje) {
            $bloqueMensaje = $comprobante->createElement('mensaje');
            $bloqueMensaje->appendChild($comprobante->createElement('identificador', $datosMensaje->identificador));
            $bloqueMensaje->appendChild($comprobante->createElement('mensaje', $datosMensaje->mensaje));
            $bloqueMensaje->appendChild($comprobante->createElement('informacionAdicional', $datosMensaje->informacionAdicional));
            $bloqueMensaje->appendChild($comprobante->createElement('tipo', $datosMensaje->tipo));
            $mensajes->appendChild($bloqueMensaje);
        }
        $datosComprobante->appendChild($mensajes);
        $comprobantes->appendChild($datosComprobante);
        $respuesta->appendChild($comprobantes);
        $factura->item(0)->appendChild($respuesta);

        $comprobante->formatOutput = true;
        $comprobante->save($directorioAlmacenamiento . '/' . $nombreComprobante);
    }
    
    private function obtenerTipoDocumento($nombreXML){
        if($nombreXML != ''){
            $codigoDocumento = substr($nombreXML, 8, 2);
        }       
        switch ($codigoDocumento) {
            case '01':
                return 'factura';
            case '04':
                return 'notaCredito';
            case '05':
                return 'notaDebito';
            case '06':
                return 'guiaRemision';
            case '07':
                return 'comprobanteRetencion';
            default:
                break;
        }
        return '';
    }

    public function autorizarComprobante($claveAccesoComprobante, $path_xml_result) {
        $resultado = '';
        try {
            $autorizacion = new AutorizacionComprobantesService($this->wsdlAutorizacion);
            $clave = new autorizacionComprobante();
            $clave->claveAccesoComprobante = $claveAccesoComprobante;
            $respuestaSRI = $autorizacion->autorizacionComprobante($clave);
            if (!is_soap_fault($respuestaSRI)){
                if ($respuestaSRI->RespuestaAutorizacionComprobante->numeroComprobantes != '0') {
                    $respuestaAutorizacion = $respuestaSRI->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;
                    if (is_array($respuestaAutorizacion)) {
                       $respuestaAuto = $respuestaAutorizacion[0];
                    } else {
                        $respuestaAuto = $respuestaAutorizacion;
                    }     
                    if(!is_dir($path_xml_result)){      
                        mkdir($path_xml_result, 0777);
                    }
                    $this->crearXMLRespuestaAutorizacionSRI($respuestaAuto, $claveAccesoComprobante, $path_xml_result);
                    $resultado['estado'] = $respuestaAuto->estado;
                    if (is_array($respuestaAuto->mensajes->mensaje)) { 
                        $i = 0;
                        foreach ($respuestaAuto->mensajes->mensaje as $datosMensaje) {
                            $resultado['mensajes'][$i]['identificador'] = $datosMensaje->identificador;
                            $resultado['mensajes'][$i]['mensaje'] = $datosMensaje->mensaje;
                            $resultado['mensajes'][$i]['tipo'] = $datosMensaje->tipo;
                            $resultado['mensajes'][$i]['informacion_adicional'] = $datosMensaje->informacionAdicional;
                            $i++;
                        }
                    } else {
                        $resultado['mensajes'][0]['identificador'] = $respuestaAuto->mensajes->mensaje->identificador;
                        $resultado['mensajes'][0]['mensaje'] = $respuestaAuto->mensajes->mensaje->mensaje;
                        $resultado['mensajes'][0]['tipo'] = $respuestaAuto->mensajes->mensaje->tipo;
                        $resultado['mensajes'][0]['informacion_adicional'] = $respuestaAuto->mensajes->mensaje->informacionAdicional;
                    }
                    return $resultado;
                } else {
                    $resultado['estado'] = "ERROR PROCESO";
                    $resultado['mensajes'][0]['identificador'] = '96';
                    $resultado['mensajes'][0]['mensaje'] = 'Error en la autorización del comprobante.';
                    $resultado['mensajes'][0]['tipo'] = 'ERROR';
                    $resultado['mensajes'][0]['informacion_adicional'] = 'No se devuelven comprobantes dada la clave de acceso consultada.';
                }
            } else {
                $resultado['estado'] = "FUERA DE SERVICIO";
                $resultado['mensajes'][0]['identificador'] = '97';
                $resultado['mensajes'][0]['mensaje'] = 'Fuera de Servicio.';
                $resultado['mensajes'][0]['tipo'] = 'ERROR';
                $resultado['mensajes'][0]['informacion_adicional'] = 'Problemas de conexión con el WS del SRI.';
            }     
        } catch (Exception $ex) {
            $resultado['estado'] = "FUERA DE SERVICIO";
            $resultado['mensajes'][0]['identificador'] = '97';
            $resultado['mensajes'][0]['mensaje'] = 'Fuera de Servicio.';
            $resultado['mensajes'][0]['tipo'] = 'ERROR';
            $resultado['mensajes'][0]['informacion_adicional'] = 'Problemas de conexión con el WS del SRI.';
        }
        return $resultado;
    }

    private function crearXMLRespuestaAutorizacionSRI($autorizacion, $claveAccesoComprobante, $directorioAlmacenamiento) {
        $xml = new DomDocument('1.0', 'UTF-8');
        $root = $xml->createElement('autorizacion');
        $root = $xml->appendChild($root);
        $estado = $xml->createElement('estado', $autorizacion->estado);
        $root->appendChild($estado);

        if ($autorizacion->numeroAutorizacion) {
            $numeroAutorizacion = $xml->createElement('numeroAutorizacion', $autorizacion->numeroAutorizacion);
            $root->appendChild($numeroAutorizacion);
        }

        $fechaAutorizacion = $xml->createElement('fechaAutorizacion', $autorizacion->fechaAutorizacion);
        $classFechaAutorizacion = $xml->createAttribute('class');
        $classFechaAutorizacion->appendChild($xml->createTextNode('fechaAutorizacion'));
        $fechaAutorizacion->appendChild($classFechaAutorizacion);
        $root->appendChild($fechaAutorizacion);

        $datosComprobante = $xml->createCDATASection($autorizacion->comprobante);
        $comprobante = $xml->createElement('comprobante');
        $comprobante->appendChild($datosComprobante);
        $root->appendChild($comprobante);

        $mensajes = $xml->createElement('mensajes');
        $mensaje = $xml->createElement('mensaje');
        if (is_array($autorizacion->mensajes->mensaje)) {
            foreach ($autorizacion->mensajes->mensaje as $datosMensaje) {
                $bloqueMensaje = $xml->createElement('mensaje');
                $bloqueMensaje->appendChild($xml->createElement('identificador', $datosMensaje->identificador));
                $bloqueMensaje->appendChild($xml->createElement('mensaje', $datosMensaje->mensaje));
                $bloqueMensaje->appendChild($xml->createElement('tipo', $datosMensaje->tipo));
                $mensaje->appendChild($bloqueMensaje);
            }
        } else {
            $datosMensaje = $autorizacion->mensajes->mensaje;
            $bloqueMensaje = $xml->createElement('mensaje');
            $bloqueMensaje->appendChild($xml->createElement('identificador', $datosMensaje->identificador));
            $bloqueMensaje->appendChild($xml->createElement('mensaje', $datosMensaje->mensaje));
            $bloqueMensaje->appendChild($xml->createElement('tipo', $datosMensaje->tipo));
            $mensaje->appendChild($bloqueMensaje);
        }
        if (!is_null($autorizacion->mensajes->mensaje)) {
            $mensajes->appendChild($mensaje);
            $root->appendChild($mensajes);
        }
        $xml->formatOutput = true;
        $xml_file_path = $directorioAlmacenamiento . '/' . $claveAccesoComprobante . '.xml';
        $xml->save($xml_file_path);
        
        $zip = new Archive_Zip($directorioAlmacenamiento . '/' . $claveAccesoComprobante . '.zip');
        $zip->create(array($directorioAlmacenamiento . '/' . $claveAccesoComprobante . '.xml'), array('remove_all_path' => TRUE));
    }

}
