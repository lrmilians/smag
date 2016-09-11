<?php 
class PacXML2PDF {
    
    var $xmlFile;
    var $dir;
    
    public function __construct($dir) {
        $this->dir = $dir;
        $this->xmlFile = new XMLReader();

        $fichero = file_get_contents($dir, true);
        $this->xmlFile->xml($fichero);
    }

    function getEstado($element) {
        $stringXML = $this->__construct($this->dir);
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                return $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return null;
    }
	
    function getNumeroDeAutorizacion($element) {
        $stringXML = $this->__construct($this->dir);
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                return $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
	function getFechaAutorizacion($element) {
        $stringXML = $this->__construct($this->dir);
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                return $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getFecha($element) {
        $stringXML = $this->__construct($this->dir);
        $value = '';
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                $value = $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return substr($value, 0, 10);
    }
    
    
    function getTiempo($element) {
        $stringXML = $this->__construct($this->dir);
        $value = '';
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                $value = $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return substr($value, 11, 8);
    }
    
	
	function getSubXML() {
        $document = new DomDocument("1.0", "UTF-8");
        $stringXML = $this->__construct($this->dir);
        
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::CDATA) {
                $stringXML = $this->xmlFile->value;
				break;
            }
        }
        
        $document->loadXML($stringXML);
        
        return $document; 
    }
    
    function readMensajes(){
        $stringXML = $this->__construct($this->dir);
        
        $identificador = array();
        $mensaje = array();
        $tipo = array();
        
        while ( $this->xmlFile->read() ) {
            
            if ( $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === 'identificador') {
                $identificador[] = $this->xmlFile->expand()->textContent;
            }
            if ( $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === 'mensaje') {
                if (preg_match("/<\/identificador>(.*?)<mensaje>(.*?)<\/mensaje>/s", $this->xmlFile->readInnerXML(),$match)) {
                    if (!in_array($match[2], $mensaje)){
                        $mensaje[] = $match[2];
                    }
                }
            }
            if ( $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === 'tipo') {
                $tipo[] = $this->xmlFile->expand()->textContent;
            }
        }
        
        $result = array();
        $result[] = $identificador;
        $result[] = $mensaje;
        $result[] = $tipo;
        
        return $result;
    }
    
    function getAmbiente($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getTipoDeEmision($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getRazonSocial($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getRUC($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getClaveDeAcceso($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getCodigoDeDocumento($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getEstablecimiento($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getPuntoDeEmision($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getSecuencial($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getDireccionMatriz($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getFechaDeEmision($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getDireccionDeEstablecimiento($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getContribuyenteEspecial($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getObligadoContabilidad($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getTipoDeIdentificacionDelComprador($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getRazonSocialDelComprador($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getIdentificacionDelComprador($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getTotalSinImpuestos($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getTotalDeDescuento($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getValueOfChildGivenParent($dom,$parent,$child){
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader2 = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === $parent ) {
				$eldom = new DomDocument("1.0", "UTF-8");
				$eldom->importNode($reader->expand(),true);
				$eldom->appendChild($reader->expand());
                $newDom->loadXML($eldom->saveXML());
				break;
            }
        }
        //var_dump($newDom->saveXML());die();
        $reader2->xml($newDom->saveXML($newDom));
        while ( $reader2->read() ) {
            if (  $reader2->nodeType == XMLReader::ELEMENT && $reader2->name === $child ) {
                return $reader2->expand()->textContent;
				break;
            }
        }
        
        
    }
    
    function readDetalles($dom){
        
        $codigo = array();
        $codigoSecundario = array();
        $descripcion = array();
        $detalleAdicional_1 = array();
        $detalleAdicional_2 = array();
        $detalleAdicional_3 = array();
        $cantidad = array();
        $precioUnitario = array();
        $descuento = array();
        $precioSinImpuesto = array();
        $impuesto = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'codigoPrincipal') {
                $codigo[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'descripcion') {
                $descripcion[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'cantidad') {
                $cantidad[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'precioUnitario') {
                $precioUnitario[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'descuento') {
                $descuento[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'precioTotalSinImpuesto') {
                $precioSinImpuesto[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'impuesto') {
                $tmp = new DomDocument("1.0", "UTF-8");
                $tmp->importNode($reader->expand(),true);
                $tmp->appendChild($reader->expand());
                $tmp->loadXML($tmp->saveXML());
                $impuesto[] = number_format($this->getValueOfChildGivenParent($tmp,'impuesto','valor'),2, '.', ' ');
            }
            $codigoSecundario[] = '';
            $detalleAdicional_1[] = '';
            $detalleAdicional_2[] = '';
            $detalleAdicional_3[] = '';
        }
        
        $result = array();
        $d = array();
        
        $result[] = $codigo;
        $result[] = $codigoSecundario;
        $result[] = $cantidad;
        $result[] = $descripcion;
        $result[] = $detalleAdicional_1;
        $result[] = $detalleAdicional_2;
        $result[] = $detalleAdicional_3;
        $result[] = $precioUnitario;
        $result[] = $descuento;
        $result[] = $precioSinImpuesto;
        //$result[] = $impuesto;
        
        $t = 0;
        for($i = 0; $i < count($codigo); $i++){
            $tmp = array();
            
            $tmp[] = $codigo[$t];
            $tmp[] = $codigoSecundario[$t];
            $tmp[] = $cantidad[$t];
            $tmp[] = $descripcion[$t];
            $tmp[] = $detalleAdicional_1[$t];
            $tmp[] = $detalleAdicional_2[$t];
            $tmp[] = $detalleAdicional_3[$t];
            $tmp[] = $precioUnitario[$t];
            $tmp[] = $descuento[$t];
            $tmp[] = $precioSinImpuesto[$t];
            
            $d[] = $tmp;
            $t++;
            
        }
        
        return $d;
    }
    
    function getValueWithAttribute($dom,$element,$attr){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            echo  $reader->getAttribute($attr);
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element && $reader->getAttribute('nombre') == $attr) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function getAmbienteString($str){
        if($str === '1'){
            return 'Prueba';
        }
        
        if($str === '2'){
            return 'Producci&oacute;n';
        }
        
        return $str;
    }
    
    function getTipoDeEmisionString($str){
        if($str === '1'){
            return 'Normal';
        }
        
        if($str === '2'){
            return 'Indisponibilidad del Sistema';
        }
        
        return $str;
    }
    
    function getProductosConIVA($dom,$cual){
        
        $conIVA = array();
        $sinIVA = array();
        $tarifa = array();
        $precioSinImpuesto = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'precioTotalSinImpuesto') {
                $precioSinImpuesto[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'impuesto') {
                $tmp = new DomDocument("1.0", "UTF-8");
                $tmp->importNode($reader->expand(),true);
                $tmp->appendChild($reader->expand());
                $tmp->loadXML($tmp->saveXML());
                $tarifa[] = $this->getValueOfChildGivenParent($tmp,'impuesto','tarifa');
            }
        }
        
        for($i = 0;$i < count($precioSinImpuesto); $i++){
            if($tarifa[$i] === '0.00'){
                $sinIVA[] = $precioSinImpuesto[$i];
            }else{
                $conIVA[] = $precioSinImpuesto[$i];
            }
        }
        
        $resultSinIVA = 0;
        $resultConIVA = 0;
        
        for($i = 0;$i < count($sinIVA); $i++){
            $resultSinIVA += $sinIVA[$i];
        }
        
        for($i = 0;$i < count($conIVA); $i++){
            $resultConIVA += $conIVA[$i];
        }
        
        
        if($cual === 'CON_IVA')
            return $resultConIVA;
        else if($cual === 'SIN_IVA')
            return $resultSinIVA;
    }
    
    function readDetallesNotaDeCredito($dom){
        
        $codigo = array();
        $codigoSecundario = array();
        $descripcion = array();
        $detalleAdicional_1 = array();
        $detalleAdicional_2 = array();
        $detalleAdicional_3 = array();
        $cantidad = array();
        $precioUnitario = array();
        $descuento = array();
        $precioSinImpuesto = array();
        $impuesto = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {  
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'codigoInterno') {
                $codigo[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'descripcion') {
                $descripcion[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'cantidad') {
                $cantidad[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'precioUnitario') {
                $precioUnitario[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'descuento') {
                $descuento[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'precioTotalSinImpuesto') {
                $precioSinImpuesto[] = number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'impuesto') {
                $tmp = new DomDocument("1.0", "UTF-8");
                $tmp->importNode($reader->expand(),true);
                $tmp->appendChild($reader->expand());
                $tmp->loadXML($tmp->saveXML());
                $impuesto[] = number_format($this->getValueOfChildGivenParent($tmp,'impuesto','valor'),2, '.', ' ');
            }
            $codigoSecundario[] = '';
            $detalleAdicional_1[] = '';
            $detalleAdicional_2[] = '';
            $detalleAdicional_3[] = '';
        }
        
        $result = array();
        $d = array();
        
        $result[] = $codigo;
        $result[] = $codigoSecundario;
        $result[] = $cantidad;
        $result[] = $descripcion;
        $result[] = $detalleAdicional_1;
        $result[] = $detalleAdicional_2;
        $result[] = $detalleAdicional_3;
        $result[] = $descuento;
        $result[] = $precioUnitario;
        $result[] = $precioSinImpuesto;
        //$result[] = $impuesto;
        
        $t = 0;
        for($i = 0; $i < count($codigo); $i++){
            $tmp = array();
            
            $tmp[] = $codigo[$t];
            $tmp[] = $codigoSecundario[$t];
            $tmp[] = $cantidad[$t];
            $tmp[] = $descripcion[$t];
            $tmp[] = $detalleAdicional_1[$t];
            $tmp[] = $detalleAdicional_2[$t];
            $tmp[] = $detalleAdicional_3[$t];
            $tmp[] = $descuento[$t];
            $tmp[] = $precioUnitario[$t];
            $tmp[] = $precioSinImpuesto[$t];
            
            $d[] = $tmp;
            $t++;
            
        }
        
        return $d;
    }
    
    
    function getValueFromCDATA($dom, $element){
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === $element ) {
                return $reader->expand()->textContent;
				break;
            }
        }
        return null;
    }
    
    function readDetallesRetencion($dom){
        
        $comprobante = array();
        $numeroDeComprobante = array();
        $fechaDeEmision = array();
        $ejercicioFiscal = array();
        $baseImponible = array();
        $impuesto = array();
        $porcentajeDeRetencion = array();
        $valorRetenido = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'numDocSustento') {
                $numeroDeComprobante[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'fechaEmisionDocSustento') {
                $fechaDeEmision[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'periodoFiscal') {
                $ejercicioFiscal[] = $reader->expand()->textContent;
                if($ejercicioFiscal[0] != '')
                    $ejercicioFiscal[] = $ejercicioFiscal[0];
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'baseImponible') {
                $baseImponible[] =  number_format($reader->expand()->textContent,2, '.', ' ');
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'codigo') {
                $tmp = $reader->expand()->textContent;
                if ($tmp == '1') 
                    $impuesto[] = 'RENTA';
                if ($tmp == '2') 
                    $impuesto[] = 'IVA';
                if ($tmp == '6') 
                    $impuesto[] = 'ISD';
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'porcentajeRetener') {
                $porcentajeDeRetencion[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'valorRetenido') {
                $valorRetenido[] = number_format($reader->expand()->textContent,2, '.', ' ');
                $comprobante[] = 'FACTURA';
            }
            
            
        }
        
        $result = array();
        $d = array();
        
        $result[] = $comprobante;
        $result[] = $numeroDeComprobante;
        $result[] = $fechaDeEmision;
        $result[] = $ejercicioFiscal;
        $result[] = $baseImponible;
        $result[] = $impuesto;
        $result[] = $porcentajeDeRetencion;
        $result[] = $valorRetenido;
        
        $t = 0;
        for($i = 0; $i < count($comprobante); $i++){
            $tmp = array();
            
            $tmp[] = $comprobante[$t];
            $tmp[] = $numeroDeComprobante[$t];
            $tmp[] = $fechaDeEmision[$t];
            $tmp[] = $ejercicioFiscal[$t];
            $tmp[] = $baseImponible[$t];
            $tmp[] = $impuesto[$t];
            $tmp[] = $porcentajeDeRetencion[$t];
            $tmp[] = $valorRetenido[$t];
            
            $d[] = $tmp;
            $t++;
            
        }
        
        return $d;
    }
    
    function getTotalRetenido($dom){
        
        $valorRetenido = array();
        $suma = 0;
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'valorRetenido') {
                $suma += $reader->expand()->textContent;
            }
        }
        
        return $suma;
    }
    
    function getMailFromXML(){
        $email = '';
        while ( $this->xmlFile->read() ) {
           if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === 'campoAdicional' && $this->xmlFile->getAttribute('nombre') == 'Email') {
                $email = $this->xmlFile->expand()->textContent;
		break;
            }
        }
        return $email;
    }
    
    
    function getTiempoNotaDebito($element){
        $stringXML = $this->__construct($this->dir);
        $value = '';
        while ( $this->xmlFile->read() ) {
            if (  $this->xmlFile->nodeType == XMLReader::ELEMENT && $this->xmlFile->name === $element ) {
                $value = $this->xmlFile->expand()->textContent;
				break;
            }
        }
        return substr($value, -11);
    }
    
    function getProductosConIVADebito($dom,$cual){
        
        $conIVA = array();
        $sinIVA = array();
        $tarifa = array();
        $precioSinImpuesto = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'totalSinImpuestos') {
                $precioSinImpuesto[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'impuesto') {
                $tmp = new DomDocument("1.0", "UTF-8");
                $tmp->importNode($reader->expand(),true);
                $tmp->appendChild($reader->expand());
                $tmp->loadXML($tmp->saveXML());
                $tarifa[] = $this->getValueOfChildGivenParent($tmp,'impuesto','tarifa');
            }
        }
        
        for($i = 0;$i < count($precioSinImpuesto); $i++){
            if($tarifa[$i] === '0.00'){
                $sinIVA[] = $precioSinImpuesto[$i];
            }else{
                $conIVA[] = $precioSinImpuesto[$i];
            }
        }
        
        $resultSinIVA = 0;
        $resultConIVA = 0;
        
        for($i = 0;$i < count($sinIVA); $i++){
            $resultSinIVA += $sinIVA[$i];
        }
        
        for($i = 0;$i < count($conIVA); $i++){
            $resultConIVA += $conIVA[$i];
        }
        
        
        if($cual === 'CON_IVA')
            return $resultConIVA;
        else if($cual === 'SIN_IVA')
            return $resultSinIVA;
    }
	
	function readDestinatarios($dom){
        
        $identificacionDestinatario = array();
        $razonSocialDestinatario = array();
        $dirDestinatario = array();
        $motivoTraslado = array();
        $numDocSustento = array();
        $fechaEmisionDocSustento = array();
        $detalles = array();
        
        $newDom = new DomDocument("1.0", "UTF-8");
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        
        while ( $reader->read() ) {  
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'identificacionDestinatario') {
                $identificacionDestinatario[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'razonSocialDestinatario') {
                $razonSocialDestinatario[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'dirDestinatario') {
                $dirDestinatario[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'motivoTraslado') {
                $motivoTraslado[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'numDocSustento') {
                $numDocSustento[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'fechaEmisionDocSustento') {
                $fechaEmisionDocSustento[] = $reader->expand()->textContent;
            }
            
            if ( $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'detalle') {
         
				$tmp = new DomDocument("1.0", "UTF-8");
                $tmp->importNode($reader->expand(),true);
                $tmp->appendChild($reader->expand());
                $tmp->loadXML($tmp->saveXML());
                
                $detalles[] = $this->getDetailsFromDestinatarios($tmp);
            }
        }
        
       
        
        $result = array();
        
        $result[] = $identificacionDestinatario;
        $result[] = $razonSocialDestinatario;
        $result[] = $dirDestinatario;
        $result[] = $motivoTraslado;
        $result[] = $numDocSustento;
        $result[] = $fechaEmisionDocSustento;
        $result[] = $detalles;

        return $result;
    }
    
    function getDetailsFromDestinatarios($dom){
        $tmp = array();
        
        $reader = new XMLReader();
        $reader->xml($dom->saveXML($dom));
        
        while ( $reader->read() ) {
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'codigoInterno' ) {
                $tmp[] = $reader->expand()->textContent;
            }
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'descripcion' ) {
                $tmp[] = $reader->expand()->textContent;
            }
            if (  $reader->nodeType == XMLReader::ELEMENT && $reader->name === 'cantidad' ) {
                $tmp[] = $reader->expand()->textContent;
            }
        }
        
        return $tmp;
    }
    
    function getDatosGuiaDeRemision($num){
        $tmp = $this->readDestinatarios($this->getSubXML());
        return $tmp[$num][0];
    }
    
    function getDetallesGuiaDeRemision($num){
        $tmp = $this->readDestinatarios($this->getSubXML());
        return $tmp[$num];
    }
}