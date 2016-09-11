<?php
class validarComprobante {
  public $xml;
}

class validarComprobanteResponse {
  public $RespuestaRecepcionComprobante;
}

class respuestaSolicitud {
  public $estado;
  public $comprobantes;
}

class comprobantes {
  public $comprobante;
}

class comprobante {
  public $claveAcceso;
  public $mensajes; 
}

class mensajes {
  public $mensaje;
}

class mensaje {
  public $identificador;
  public $mensaje; 
  public $informacionAdicional; 
  public $tipo; 
}

class RecepcionComprobantesService extends SoapClient {

  private static $classmap = array(
                                'validarComprobante' => 'validarComprobante',
                                'validarComprobanteResponse' => 'validarComprobanteResponse',
                                'respuestaSolicitud' => 'respuestaSolicitud',
                                'comprobantes' => 'comprobantes',
                                'comprobante' => 'comprobante',
                                'mensajes' => 'mensajes',
                                'mensaje' => 'mensaje',
                               );

  public function RecepcionComprobantesService($wsdl, $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    $option ["exceptions"] = true;
    $option ["connection_timeout"] = 60;
    $option ["style"] = SOAP_RPC;
    $option ["use"] = SOAP_ENCODED;

    parent::__construct($wsdl, $options);
  }

  public function validarComprobante(validarComprobante $parameters) {
    return $this->__soapCall('validarComprobante', array($parameters), array(
            'uri' => 'http://ec.gob.sri.ws.recepcion',
            'soapaction' => ''
           )
      );
  }

}
