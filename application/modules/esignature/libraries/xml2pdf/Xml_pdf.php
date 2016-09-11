<?php

//include 'xml2pdf.php';
//require APPPATH . "modules/esignature/libraries/xml2pdf/mpdf53/mpdf.php";
require APPPATH . "modules/esignature/libraries/xml2pdf/mpdf60/mpdf.php";
require APPPATH . "modules/esignature/libraries/xml2pdf/xml2pdf.php";

class Xml_pdf {

    public function save_xml_pdf($xml_path, $xml_name, $path_save) {
        $obj = new PacXML2PDF($xml_path);
        $mpdf = new mPDF('utf-8', 'A3');
        $styles = $fichero = file_get_contents(APPPATH . 'modules/esignature/libraries/xml2pdf/file_template/01_style.txt', true);
     
        $doc_type = substr($xml_name, 8, 2);
        $logo_path = PATH_LOGO . '/' . LOGO_NAME;
        $html = $this->get_html_by_doc($obj, $doc_type, $logo_path);
        $mpdf->WriteHTML($styles . $html);
        $mpdf->Output($path_save . '/' . str_replace('.xml', '', $xml_name) . '.pdf', 'F');
    }

    public function get_xml_data($xml_path){
        if (file_exists($xml_path)) {
            $xml = simplexml_load_file($xml_path);
            $data = array(
                'access_key' => (string)$xml->infoTributaria->claveAcceso,
                'num_doc' => $xml->infoTributaria->estab . '-' . $xml->infoTributaria->ptoEmi . '-' . $xml->infoTributaria->secuencial,
                'cod_doc' => (string)$xml->infoTributaria->codDoc,
                'client_ident' => (string)$xml->infoFactura->identificacionComprador,
                'client_name' => (string)$xml->infoFactura->razonSocialComprador,
                'year' => substr($xml->infoTributaria->claveAcceso,4,4),
                'month' => substr($xml->infoTributaria->claveAcceso,2,2),
                'day' => substr($xml->infoTributaria->claveAcceso,0,2)
            );
            foreach ($xml->infoAdicional->campoAdicional as $campoAdicional) {
                switch((string) $campoAdicional['nombre']) {
                    case 'Email':
                        $data['client_email'] = (string)$campoAdicional;
                        break;
                    case 'Telefono':
                        $data['client_phone'] = (string)$campoAdicional;
                        break;
                    case 'direccion':
                        $data['client_address'] = (string)$campoAdicional;
                        break;
                }
            }
            return $data;
        }

        return false;

    }
    
    private function get_html_by_doc($obj, $doc_type, $logo_path) {
        switch ($doc_type) {
            case '01':
                $html = '<br/><br/><br/>
                    <div style="padding-left: 125px;padding-right: 125px">
                        <div style="float: left;  height: 443px; width: 400px">
                            <div style="height: 265px; width: 300px;text-align:center">
                                <div id="over">
                                    <img src="' . $logo_path . '">
                                </div>
                            </div>
                            <div style="border: 1px solid #000; height: 130px; width: 360px;padding:10px">
                                <b><span style="font-size:14px;">' . $obj->getRazonSocial($obj->getSubXML(), 'razonSocial') . '</span></b><br/><br/>
                                Dir. Matriz: ' . $obj->getDireccionMatriz($obj->getSubXML(), 'dirMatriz') . '<br/><br/>
                                Dir. Sucursal:  <br/><br/>
                                Contribuyente Especial N&uacute;mero: ' . $obj->getContribuyenteEspecial($obj->getSubXML(), 'contribuyenteEspecial') . ' <br/>
                                OBLIGADO A LLEVAR CONTABILIDAD: ' . $obj->getObligadoContabilidad($obj->getSubXML(), 'obligadoContabilidad') . '
                            </div>
                        </div>
                        <div style="float: right; border: 1px solid #000; height: 400px; width: 330px;padding:10px">
                            R.U.C.: ' . $obj->getRUC($obj->getSubXML(), 'ruc') . '<br/><br/><br/>
                            <b><span style="font-size:15px">F A C T U R A</span></b><br/><br/>
                            No. ' . $obj->getEstablecimiento($obj->getSubXML(),'estab'). '-' . $obj->getPuntoDeEmision($obj->getSubXML(),'ptoEmi') . '-' . $obj->getSecuencial($obj->getSubXML(),'secuencial') . ' <br/><br/>
                            N&Uacute;MERO DE AUTORIZACI&Oacute;N:<br/><br/> ' . $obj->getNumeroDeAutorizacion('numeroAutorizacion') . '<br/><br/>
                            FECHA Y HORA DE AUTORIZACI&Oacute;N: ' . $obj->getFecha('fechaAutorizacion') . ' ' . $obj->getTiempo('fechaAutorizacion') . '<br/><br/>
                            AMBIENTE: ' . $obj->getAmbienteString($obj->getAmbiente($obj->getSubXML(), 'ambiente')) . '<br/><br/>
                            EMISI&Oacute;N: ' . $obj->getTipoDeEmisionString($obj->getTipoDeEmision($obj->getSubXML(), 'tipoEmision')) . '<br/><br/><br/><br/>
                            CLAVE DE ACCESO:<br/>
                            <div class="barcodecell">
                                <barcode code="' . $obj->getClaveDeAcceso($obj->getSubXML(), 'claveAcceso') . '" type="I25" size="0.57" height="2"/><br/> 
                                <span style="font-size:10px">' . $obj->getClaveDeAcceso($obj->getSubXML(), 'claveAcceso') . '</span>
                            </div>
                        </div>
                        <div style="clear: both"></div>
                        <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">
                            <table>
                                <tr>
                                    <td width="500px">Raz&oacute;n Social / Nombres y Apellidos: ' . $obj->getRazonSocialDelComprador($obj->getSubXML(), 'razonSocialComprador') . '</td>
                                    <td width="300px">RUC / CI: ' . $obj->getIdentificacionDelComprador($obj->getSubXML(), 'identificacionComprador') . '</td>
                                </tr>
                                <tr>
                                    <td width="500px"><br/></td>
                                     <td width="300px"><br/></td>
                                </tr>
                                <tr>
                                    <td width="500px">Fecha Emisi&oacute;n: ' . $obj->getFechaDeEmision($obj->getSubXML(), 'fechaEmision') . '</td>
                                     <td width="300px">Gu&iacute;a Remisi&oacute;n:</td>
                                </tr>
                            </table>
                        </div>
                        <br/>
                        <table class="table table-bordered">
                            <thead>
                             <tr>
                                <th class="text-small">Cod. Principal</th>
                                <th class="text-small">Cod. Auxiliar</th>
                                <th class="text-small">Cant.</th>
                                <th class="text-small">Descripci&oacute;n</th>
                                <th class="text-small">Detalle Adicional</th>
                                <th class="text-small">Detalle Adicional</th>
                                <th class="text-small">Detalle Adicional</th>
                                <th class="text-small">Precio Unitario</th>
                                <th class="text-small">Descuento</th>
                                <th class="text-small">Precio Total</th>
                             </tr>
                            </thead>
 
                        <tbody>';

                $arr = $obj->readDetalles($obj->getSubXML());
                for ($i = 0; $i < count($arr); $i++) {
                    $html = $html . '<tr style="border: 1px solid black;">';
                    for ($j = 0; $j < count($arr[$i]); $j++) {
                        $html = $html . '<td style="border: 1px solid black;padding:5px">' . $arr[$i][$j] . '</td>';
                    }
                    $html = $html . '</tr>';
                }

                $html = $html . '</tbody></table>
                                <div style="float: left;  height: 443px; width: 400px">
                                    <div style="width: 300px;text-align:center;border: 1px solid #000;padding:10px">
                                        Informacion Adicional:
                                    </div>
                                     <div style="width: 300px;text-align:center;padding:10px;font-size:30px;color:#DCDCDC">
                                            LA INFORMACION IMPRESA NO TIENE VALIDEZ
                                     </div>
                                </div>
                                <div style="float: right;  height: 400px; width: 330px;">
                                    <table class="table table-bordered text-right">
                                        <tbody>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px;width:200px">SUBTOTAL 12%</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getProductosConIVA($obj->getSubXML(), 'CON_IVA'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">SUBTOTAL 0%</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getProductosConIVA($obj->getSubXML(), 'SIN_IVA'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">SUBTOTAL No sujeto de IVA</td>
                                            <td style="border: 1px solid black;padding:5px">00.00</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">SUBTOTAL SIN IMPUESTOS</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getTotalSinImpuestos($obj->getSubXML(), 'totalSinImpuestos'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">DESCUENTO</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getTotalDeDescuento($obj->getSubXML(), 'totalDescuento'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">ICE</td>
                                            <td style="border: 1px solid black;padding:5px">00.00</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">IVA 12%</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getValueOfChildGivenParent($obj->getSubXML(), 'totalImpuesto', 'valor'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">PROPINA</td>
                                            <td style="border: 1px solid black;padding:5px">' . number_format($obj->getTotalDeDescuento($obj->getSubXML(), 'propina'), 2, '.', ' ') . '</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid black;padding:5px">VALOR TOTAL</td>
                                            <td style="border: 1px solid black;padding:5px"><b>' . number_format($obj->getTotalDeDescuento($obj->getSubXML(), 'importeTotal'), 2, '.', ' ') . '</b></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div style="clear: both"></div>
                            </div>';
                break;  
            case '04':
                $html = '<br/><br/><br/>
                            <div style="padding-left: 125px;padding-right: 125px">
                                <div style="float: left;  height: 443px; width: 400px">
                                    <div style="height: 265px; width: 300px;text-align:center">
                                        <div id="over">
                                            <img src="' . $logo_path . '">
                                        </div>
                                    </div>
                                    <div style="border: 1px solid #000; height: 130px; width: 360px;padding:10px">
                                        <span style="font-size:14px;">'.$obj->getRazonSocial($obj->getSubXML(),'razonSocial').'</span><br/><br/>
                                        Dir. Matriz: '.$obj->getDireccionMatriz($obj->getSubXML(),'dirMatriz').'<br/><br/>
                                        Dir. Sucursal:  <br/><br/>  
                                        Contribuyente Especial N&uacute;mero: '.$obj->getContribuyenteEspecial($obj->getSubXML(),'contribuyenteEspecial').' <br/>
                                        OBLIGADO A LLEVAR CONTABILIDAD: '.$obj->getObligadoContabilidad($obj->getSubXML(),'obligadoContabilidad').'
                                    </div>
                                </div>
                                <div style="float: right; border: 1px solid #000; height: 400px; width: 330px;padding:10px">
                                    R.U.C.: '. $obj->getRUC($obj->getSubXML(),'ruc') .'<br/><br/><br/>
                                    <span style="font-size:15px">N O T A  &nbsp; D E &nbsp; C R E D I T O</span><br/><br/>
                                    No. ' .$obj->getEstablecimiento($obj->getSubXML(),'estab'). '-' . $obj->getPuntoDeEmision($obj->getSubXML(),'ptoEmi') . '-' .$obj->getSecuencial($obj->getSubXML(),'secuencial').' <br/><br/>
                                    N&Uacute;MERO DE AUTORIZACI&Oacute;N:<br/><br/> '.$obj->getNumeroDeAutorizacion('numeroAutorizacion').'<br/><br/>
                                    FECHA Y HORA DE AUTORIZACI&Oacute;N: '.$obj->getFecha('fechaAutorizacion').' '.$obj->getTiempo('fechaAutorizacion'). '<br/><br/>
                                    AMBIENTE: '.$obj->getAmbienteString($obj->getAmbiente($obj->getSubXML(),'ambiente')).'<br/><br/>
                                    EMISI&Oacute;N: '.$obj->getTipoDeEmisionString($obj->getTipoDeEmision($obj->getSubXML(),'tipoEmision')).'<br/><br/><br/><br/>
                                    CLAVE DE ACCESO:<br/>
                                    <div class="barcodecell">
                                        <barcode code="'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'" type="I25" size="0.57" height="2"/><br/> 
                                        <span style="font-size:10px">'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'</span>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                                <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">
                                    <table>
                                        <tr>
                                            <td width="500px">Raz&oacute;n Social / Nombres y Apellidos: '. $obj->getRazonSocialDelComprador($obj->getSubXML(),'razonSocialComprador') .'</td>
                                            <td width="300px">RUC / CI: '. $obj->getIdentificacionDelComprador($obj->getSubXML(),'identificacionComprador').'</td>
                                        </tr>
                                        <tr>
                                            <td width="500px"><br/></td>
                                            <td width="300px"><br/></td>
                                        </tr>
                                        <tr>
                                            <td width="500px">Fecha Emisi&oacute;n: '. $obj->getFechaDeEmision($obj->getSubXML(),'fechaEmision') .'</td>
                                            <td width="300px"></td>
                                        </tr>
                                    </table>
                                </div>
                                <br/>
                                <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">
                                    <table>
                                        <tr>
                                            <td width="400px">Comprobante que se modifica:</td>
                                            <td width="400px">'. $obj->getValueFromCDATA($obj->getSubXML(),'numDocModificado') .'</td>
                                        </tr>
                                        <tr>
                                            <td width="200px"><br/></td>
                                             <td width="200px"><br/></td>
                                        </tr>
                                        <tr>
                                            <td width="400px">Fecha Emisi&oacute;n: (Comprobante a modificar)</td>
                                            <td width="400px">'. $obj->getValueFromCDATA($obj->getSubXML(),'fechaEmisionDocSustento') .'</td>
                                        </tr>
                                        <tr>
                                            <td width="200px"><br/></td>
                                             <td width="200px"><br/></td>
                                        </tr>
                                        <tr>
                                            <td width="400px">Raz&oacute;n de Modificaci&oacute;n:</td>
                                            <td width="400px">'. $obj->getValueFromCDATA($obj->getSubXML(),'motivo') .'</td>
                                        </tr>
                                    </table>
                                </div>
                                <br/>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-small">C&oacute;digo</th>
                                            <th class="text-small">C&oacute;digo Auxiliar</th>
                                            <th class="text-small">Cantidad</th>
                                            <th class="text-small">Descripci&oacute;n</th>
                                            <th class="text-small">Detalle Adicional</th>
                                            <th class="text-small">Detalle Adicional</th>
                                            <th class="text-small">Detalle Adicional</th>
                                            <th class="text-small">Descuento</th>
                                            <th class="text-small">Precio Unitario</th>
                                            <th class="text-small">Precio Total</th>
                                        </tr>
                                    </thead>
                            <tbody>';
                $arr = $obj->readDetallesNotaDeCredito($obj->getSubXML());
                for($i = 0; $i < count($arr); $i++){
                    $html = $html . '<tr style="border: 1px solid black;">';
                    for($j = 0; $j < count($arr[$i]); $j++){
                        $html = $html . '<td style="border: 1px solid black;padding:5px">'. $arr[$i][$j] . '</td>';
                    }
                    $html = $html . '</tr>';
                }
                $html = $html . '</tbody></table>
                                    <div style="float: left;  height: 443px; width: 400px">
                                        <div style="width: 300px;text-align:center;border: 1px solid #000;padding:10px">
                                            Informacion Adicional:
                                        </div> 
                                        
                                         <div style="width: 300px;text-align:center;padding:10px;font-size:30px;color:#DCDCDC">
                                            LA INFORMACION IMPRESA NO TIENE VALIDEZ
                                        </div>
                                    </div>
                                    <div style="float: right;  height: 400px; width: 330px;">
                                        <table class="table table-bordered text-right">
                                            <tbody>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px;width:200px">SUBTOTAL 12%</td>
                                                    <td style="border: 1px solid black;padding:5px">'.number_format($obj->getProductosConIVA($obj->getSubXML(),'CON_IVA'), 2, '.', ' ').'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">SUBTOTAL 0%</td>
                                                    <td style="border: 1px solid black;padding:5px">'.number_format($obj->getProductosConIVA($obj->getSubXML(),'SIN_IVA'), 2, '.', ' ').'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">SUBTOTAL No sujeto de IVA</td>
                                                    <td style="border: 1px solid black;padding:5px">0.00</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">SUBTOTAL</td>
                                                    <td style="border: 1px solid black;padding:5px">'.number_format($obj->getTotalSinImpuestos($obj->getSubXML(),'totalSinImpuestos'), 2, '.', ' ').'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">TOTAL Descuento</td>
                                                    <td style="border: 1px solid black;padding:5px">'.number_format($obj->getTotalDeDescuento($obj->getSubXML(),'totalDescuento'), 2, '.', ' ').'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">IVA 12%</td>
                                                    <td style="border: 1px solid black;padding:5px">'.number_format($obj->getValueOfChildGivenParent($obj->getSubXML(),'totalImpuesto','valor'), 2, '.', ' ').'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">ICE</td>
                                                    <td style="border: 1px solid black;padding:5px">0.00</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid black;padding:5px">VALOR TOTAL</td>
                                                    <td style="border: 1px solid black;padding:5px"><b>'.number_format($obj->getTotalDeDescuento($obj->getSubXML(),'valorModificacion'), 2, '.', ' ').'</b></td>
                                                </tr>
                                            </tbody>
                                        </table>    
                                    </div>
                                    <div style="clear: both"></div>
                                </div>';
                break;
            case '05':
                $html = '<br/><br/><br/>
                    <div style="padding-left: 125px;padding-right: 125px">
                        <div style="float: left;  height: 443px; width: 400px">
                            <div style="height: 265px; width: 300px;text-align:center">
                            <div id="over">
                                <img src="' . $logo_path . '">
                            </div>
                            </div>
                            <div style="border: 1px solid #000; height: 130px; width: 360px;padding:10px">

                               <span style="font-size:14px;">'.$obj->getRazonSocial($obj->getSubXML(),'razonSocial').'</span><br/><br/>
                                Dir. Matriz: '.$obj->getDireccionMatriz($obj->getSubXML(),'dirMatriz').'<br/><br/>
                                Dir. Sucursal:  <br/><br/>

                                Contribuyente Especial N&uacute;mero: '.$obj->getContribuyenteEspecial($obj->getSubXML(),'contribuyenteEspecial').' <br/>
                                OBLIGADO A LLEVAR CONTABILIDAD: '.$obj->getObligadoContabilidad($obj->getSubXML(),'obligadoContabilidad').'
                            </div>

                        </div>

                        <div style="float: right; border: 1px solid #000; height: 400px; width: 330px;padding:10px">

                            R.U.C.: '. $obj->getRUC($obj->getSubXML(),'ruc') .'<br/><br/><br/>
                            <span style="font-size:15px">N O T A  &nbsp; D E &nbsp; D E B I T O</span><br/><br/>
                           No. ' .$obj->getEstablecimiento($obj->getSubXML(),'estab'). '-' . $obj->getPuntoDeEmision($obj->getSubXML(),'ptoEmi') . '-' .$obj->getSecuencial($obj->getSubXML(),'secuencial').' <br/><br/>
                           N&Uacute;MERO DE AUTORIZACI&Oacute;N:<br/><br/> '.$obj->getNumeroDeAutorizacion('numeroAutorizacion').'<br/><br/>
                           FECHA Y HORA DE AUTORIZACI&Oacute;N: '.$obj->getFecha('fechaAutorizacion').' '.$obj->getTiempoNotaDebito('fechaAutorizacion'). '<br/><br/>
                           AMBIENTE: '.$obj->getAmbienteString($obj->getAmbiente($obj->getSubXML(),'ambiente')).'<br/><br/>
                           EMISI&Oacute;N: '.$obj->getTipoDeEmisionString($obj->getTipoDeEmision($obj->getSubXML(),'tipoEmision')).'<br/><br/><br/><br/>
                           CLAVE DE ACCESO:<br/>
                           <div class="barcodecell">
                           <barcode code="'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'" type="I25" size="0.57" height="2"/><br/> 
                           <span style="font-size:10px">'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'</span>
                           </div>
                        </div>
                        <div style="clear: both"></div>

                        <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">

                            <table>
                                <tr>
                                    <td width="500px">
                                    Raz&oacute;n Social / Nombres y Apellidos: '. $obj->getRazonSocialDelComprador($obj->getSubXML(),'razonSocialComprador') .'
                                    </td>
                                     <td width="300px">
                                     RUC / CI: '. $obj->getIdentificacionDelComprador($obj->getSubXML(),'identificacionComprador').'
                                     </td>
                                </tr>
                                <tr>
                                    <td width="500px">
                                    <br/>
                                    </td>
                                     <td width="300px">
                                     <br/>
                                     </td>
                                </tr>
                                <tr>
                                    <td width="500px">
                                    Fecha Emisi&oacute;n: '. $obj->getFechaDeEmision($obj->getSubXML(),'fechaEmision') .'
                                    </td>
                                     <td width="300px">

                                     </td>
                                </tr>
                            </table>

                        </div>
                        <br/>

                        <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">

                            <table>
                                <tr>
                                    <td width="400px">
                                    Comprobante que se modifica: 
                                    </td>
                                     <td width="400px">
                                     '. $obj->getValueFromCDATA($obj->getSubXML(),'numDocModificado') .'
                                     </td>
                                </tr>
                                <tr>
                                    <td width="200px">
                                    <br/>
                                    </td>
                                     <td width="200px">
                                     <br/>
                                     </td>
                                </tr>
                                <tr>
                                    <td width="400px">
                                    Fecha Emisi&oacute;n: (Comprobante a modificar)
                                    </td>
                                     <td width="400px">
                                     '. $obj->getValueFromCDATA($obj->getSubXML(),'fechaEmisionDocSustento') .'
                                     </td>
                                </tr>
                                <tr>
                                    <td width="200px">
                                    <br/>
                                    </td>
                                     <td width="200px">
                                     <br/>
                                     </td>
                                </tr>
                                <tr>
                                    <td width="400px">
                                    Raz&oacute;n de Modificaci&oacute;n:
                                    </td>
                                     <td width="400px">
                                     '. $obj->getValueOfChildGivenParent($obj->getSubXML(),'motivo','razon') .'
                                     </td>
                                </tr>

                            </table>

                        </div>

                        <div style="border: 1px solid #000;height: 20px; width: 100%;padding:0px;border-top:none;">

                            <div style="border-right: 1px solid #000; height: 20px; width: 370px;padding:5px;float:left;text-align:center">
                              RAZON DE LA MODIFICACION
                            </div>
                            <div style="border-right: 1px solid #000; height: 20px; width: 310px;padding:5px;float:right;text-align:center">
                              VALOR DE LA MODIFICACION
                            </div>

                        </div>
                        <div style="border: 1px solid #000;height: 20px; width: 100%;padding:0px;border-top:none;">
                            <div style="border-right: 1px solid #000; height: 20px; width: 370px;padding:5px;float:left;text-align:center">
                              '. $obj->getValueOfChildGivenParent($obj->getSubXML(),'motivo','razon') .'
                            </div>
                            <div style="border-right: 1px solid #000; height: 20px; width: 310px;padding:5px;float:right;text-align:center">
                              '. $obj->getValueOfChildGivenParent($obj->getSubXML(),'motivo','valor') .'
                            </div>
                        </div>
                        <br/>


                        <div style="float: left;  height: 443px; width: 400px">

                            <div style="width: 300px;text-align:center;border: 1px solid #000;padding:10px">
                                Informacion Adicional:
                            </div>

                            <div style="width: 300px;text-align:center;padding:10px;font-size:30px;color:#DCDCDC">
                                            LA INFORMACION IMPRESA NO TIENE VALIDEZ
                            </div>
                        </div>

                        <div style="float: right;  height: 400px; width: 330px;">

                        <table class="table table-bordered text-right">
                            <tbody>
                            <tr>
                                <td style="border: 1px solid black;padding:5px;width:200px">SUBTOTAL 12%</td>
                                <td style="border: 1px solid black;padding:5px">'.number_format($obj->getProductosConIVADebito($obj->getSubXML(),'CON_IVA'), 2, '.', '').'</td>
                            </tr>

                            <tr>
                                <td style="border: 1px solid black;padding:5px">SUBTOTAL 0%</td>
                                <td style="border: 1px solid black;padding:5px">'.number_format($obj->getProductosConIVADebito($obj->getSubXML(),'SIN_IVA'), 2, '.', '').'</td>
                            </tr>

                            <tr>
                                <td style="border: 1px solid black;padding:5px">SUBTOTAL No sujeto de IVA</td>
                                <td style="border: 1px solid black;padding:5px">0.00</td>
                            </tr>


                            <tr>
                                <td style="border: 1px solid black;padding:5px">SUBTOTAL</td>
                                <td style="border: 1px solid black;padding:5px">'.number_format($obj->getTotalSinImpuestos($obj->getSubXML(),'totalSinImpuestos'), 2, '.', '').'</td>
                            </tr>


                            <tr>
                                <td style="border: 1px solid black;padding:5px">TOTAL Descuento</td>
                                <td style="border: 1px solid black;padding:5px">'.number_format($obj->getTotalDeDescuento($obj->getSubXML(),'totalDescuento'), 2, '.', '').'</td>
                            </tr>

                            <tr>
                                <td style="border: 1px solid black;padding:5px">IVA 12%</td>
                                <td style="border: 1px solid black;padding:5px">'.number_format($obj->getValueOfChildGivenParent($obj->getSubXML(),'impuesto','valor'), 2, '.', '').'</td>
                            </tr>

                            <tr>
                                <td style="border: 1px solid black;padding:5px">ICE</td>
                                <td style="border: 1px solid black;padding:5px">0.00</td>
                            </tr>

                            <tr>
                                <td style="border: 1px solid black;padding:5px">VALOR TOTAL</td>
                                <td style="border: 1px solid black;padding:5px"><b>'.number_format(($obj->getProductosConIVADebito($obj->getSubXML(),'CON_IVA')+$obj->getProductosConIVADebito($obj->getSubXML(),'SIN_IVA')+$obj->getValueOfChildGivenParent($obj->getSubXML(),'impuesto','valor')), 2, '.', '').'</b></td>
                            </tr>

                            </tbody>
                        </table>
                        </div>
                        <div style="clear: both"></div>
                    </div>';
                break;
            case '06':
                $html = '<br/><br/><br/>
                <div style="padding-left: 125px;padding-right: 125px">
                        <div style="float: left;  height: 443px; width: 400px">
                                <div style="height: 249px; width: 300px;text-align:center">
                                    <div id="over">
                                        <img src="' . $logo_path . '">
                                    </div>
                                </div>

                                <div style="border: 1px solid #000; height: 130px; width: 360px;padding:10px">
                                   <span style="font-size:14px;">'.$obj->getRazonSocial($obj->getSubXML(),'razonSocial').'</span><br/><br/>
                                        Dir. Matriz: '.$obj->getDireccionMatriz($obj->getSubXML(),'dirMatriz').'<br/><br/>
                                        Dir. Sucursal:  <br/><br/>
                                        Fecha Inicio Transporte: '. $obj->getValueFromCDATA($obj->getSubXML(),'fechaIniTransporte').'<br/><br/>
                                        Fecha Fin Transporte: '. $obj->getValueFromCDATA($obj->getSubXML(),'fechaFinTransporte').'
                                </div>

                        </div>
                        <div style="float: right; border: 1px solid #000; height: 400px; width: 330px;padding:10px">

                           R.U.C.: '. $obj->getRUC($obj->getSubXML(),'ruc') .'<br/><br/><br/>
                           <span style="font-size:15px">G U I A  &nbsp; D E &nbsp; R E M I S I O N</span><br/><br/>
                           No. ' .$obj->getEstablecimiento($obj->getSubXML(),'estab'). '-' . $obj->getPuntoDeEmision($obj->getSubXML(),'ptoEmi') . '-' .$obj->getSecuencial($obj->getSubXML(),'secuencial').' <br/><br/>
                           N&Uacute;MERO DE AUTORIZACI&Oacute;N:<br/><br/> '.$obj->getNumeroDeAutorizacion('numeroAutorizacion').'<br/><br/>
                           FECHA Y HORA DE AUTORIZACI&Oacute;N: '.$obj->getFecha('fechaAutorizacion').' '.$obj->getTiempoNotaDebito('fechaAutorizacion'). '<br/><br/>
                           AMBIENTE: '.$obj->getAmbienteString($obj->getAmbiente($obj->getSubXML(),'ambiente')).'<br/><br/>
                           EMISI&Oacute;N: '.$obj->getTipoDeEmisionString($obj->getTipoDeEmision($obj->getSubXML(),'tipoEmision')).'<br/><br/><br/><br/>
                           CLAVE DE ACCESO:<br/>
                           <div class="barcodecell">
                           <barcode code="'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'" type="I25" size="0.57" height="2"/><br/> 
                           <span style="font-size:10px">'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'</span>
                           </div>
                        </div>
                        <div style="clear: both"></div>

                        <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">
                                <table>
                                        <tr>
                                                <td width="500px">
                                                RUC/CI(Transportista): 
                                                </td>
                                                 <td width="300px">

                                                  '. $obj->getIdentificacionDelComprador($obj->getSubXML(),'rucTransportista').'
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                                <br/>
                                                </td>
                                                 <td width="300px">
                                                 <br/>
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                          Raz&oacute;n Social / Nombres y Apellidos: 
                                                </td>
                                                 <td width="300px">
                                                 '. $obj->getRazonSocialDelComprador($obj->getSubXML(),'razonSocialTransportista') .'
                                                 </td>
                                        </tr>

                                         <tr>
                                                <td width="500px">
                                                <br/>
                                                </td>
                                                 <td width="300px">
                                                 <br/>
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                          Placa: 
                                                </td>
                                                 <td width="300px">
                                                 '. $obj->getValueFromCDATA($obj->getSubXML(),'placa').'
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                                <br/>
                                                </td>
                                                 <td width="300px">
                                                 <br/>
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                          Punto de Partida: 
                                                </td>
                                                 <td width="300px">
                                                 '. $obj->getValueFromCDATA($obj->getSubXML(),'dirPartida').'
                                                 </td>
                                        </tr>
                                </table>
                        </div>
                        <br/>

                        <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">

                        <table>
                                        <tr>
                                                <td width="500px">
                                                Documento: '.  $obj->getDatosGuiaDeRemision(4) .'
                                                <br><br><br>
                                                </td>
                                                 <td width="300px">
                                                  Fecha de Emisi&oacute;n: '.  $obj->getDatosGuiaDeRemision(5) .'
                                                  <br><br><br>
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                                Motivo de Traslado: '.  $obj->getDatosGuiaDeRemision(3) .'
                                                <br><br><br>
                                                </td>
                                                 <td width="300px">
                                                  Destino: '.  $obj->getDatosGuiaDeRemision(2) .'
                                                  <br><br><br>
                                                 </td>
                                        </tr>
                                        <tr>
                                                <td width="500px">
                                                RUC/CI (Destinatario): '.  $obj->getDatosGuiaDeRemision(0) .'
                                                <br><br><br>
                                                </td>
                                                 <td width="300px">
                                                  Raz&oacute;n Social (Destinatario): '.  $obj->getDatosGuiaDeRemision(1) .'
                                                  <br><br><br>
                                                 </td>
                                        </tr>


                                </table>

                        <div style="height: 60px; width: 100%;padding:10px">

                                        <table class="table table-bordered">
                                                 <thead>
                                                  <tr>
                                                         <th class="text-small">C&oacute;digo Principal</th>
                                                         <th class="text-small">Descripci&oacute;n</th>

                                                         <th class="text-small">Cantidad</th>
                                                  </tr>
                                                 </thead>

                                                <tbody>
                                                        ';

                                                        $arr = $obj->getDetallesGuiaDeRemision(6);

                                                        for($i = 0; $i < count($arr); $i++){
                                                                $html = $html . '<tr style="border: 1px solid black;">';
                                                                for($j = 0; $j < count($arr[$i]); $j++){
                                                                        $html = $html . '<td style="border: 1px solid black;padding:5px">'. $arr[$i][$j] . '</td>';
                                                                }
                                                                $html = $html . '</tr>';
                                                        }

                                                        $html = $html . '
                                                </tbody>
                                        </table>
                        </div>
                        </div>
                        <br/>
                        <div style="width: 300px;text-align:center;padding:10px;font-size:30px;color:#DCDCDC">
                                LA INFORMACION IMPRESA NO TIENE VALIDEZ
                        </div>
                </div>';
                break;
            case '07':
                $html = '<br/><br/><br/>
                            <div style="padding-left: 125px;padding-right: 125px">
                                <div style="float: left;  height: 443px; width: 400px">
                                    <div style="height: 265px; width: 300px;text-align:center">
                                        <div id="over">
                                            <img src="' . $logo_path . '">
                                        </div>
                                    </div>
                                    <div style="border: 1px solid #000; height: 130px; width: 360px;padding:10px">
                
                                        <span style="font-size:14px;">'.$obj->getRazonSocial($obj->getSubXML(),'razonSocial').'</span><br/><br/>
                                        Dir. Matriz: '.$obj->getDireccionMatriz($obj->getSubXML(),'dirMatriz').'<br/><br/>
                                        Dir. Sucursal:  <br/><br/>
                
                                        Contribuyente Especial N&uacute;mero: '.$obj->getContribuyenteEspecial($obj->getSubXML(),'contribuyenteEspecial').' <br/>
                                        OBLIGADO A LLEVAR CONTABILIDAD: '.$obj->getObligadoContabilidad($obj->getSubXML(),'obligadoContabilidad').'
                                    </div>

                                </div>
        
                                <div style="float: right; border: 1px solid #000; height: 400px; width: 330px;padding:10px">

                                    R.U.C.: '. $obj->getRUC($obj->getSubXML(),'ruc') .'<br/><br/><br/>
                                    <span style="font-size:15px">C O M P R O B A N T E  &nbsp; D E &nbsp; R E T E N C I &Oacute; N</span><br/><br/>
                                    No. ' .$obj->getEstablecimiento($obj->getSubXML(),'estab'). '-' . $obj->getPuntoDeEmision($obj->getSubXML(),'ptoEmi') . '-' .$obj->getSecuencial($obj->getSubXML(),'secuencial').' <br/><br/>
                                    N&Uacute;MERO DE AUTORIZACI&Oacute;N:<br/><br/> '.$obj->getNumeroDeAutorizacion('numeroAutorizacion').'<br/><br/>
                                    FECHA Y HORA DE AUTORIZACI&Oacute;N: '.$obj->getFecha('fechaAutorizacion').' '.$obj->getTiempo('fechaAutorizacion'). '<br/><br/>
                                    AMBIENTE: '.$obj->getAmbienteString($obj->getAmbiente($obj->getSubXML(),'ambiente')).'<br/><br/>
                                    EMISI&Oacute;N: '.$obj->getTipoDeEmisionString($obj->getTipoDeEmision($obj->getSubXML(),'tipoEmision')).'<br/><br/><br/><br/>
                                    CLAVE DE ACCESO:<br/>
                                    <div class="barcodecell">
                                    <barcode code="'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'" type="I25" size="0.57" height="2"/><br/> 
                                    <span style="font-size:10px">'.$obj->getClaveDeAcceso($obj->getSubXML(),'claveAcceso').'</span>
                                    </div>
                                </div>
                                <div style="clear: both"></div>
                                <div style="border: 1px solid #000;height: 60px; width: 100%;padding:10px">
        
                                    <table>
                                        <tr>
                                            <td width="500px">
                                            Raz&oacute;n Social / Nombres y Apellidos: '. $obj->getValueFromCDATA($obj->getSubXML(),'razonSocialSujetoRetenido') .'
                                            </td>
                                                <td width="300px">
                                                RUC / CI: '. $obj->getValueFromCDATA($obj->getSubXML(),'identificacionSujetoRetenido').'
                                                </td>
                                        </tr>
                                        <tr>
                                            <td width="500px">
                                            <br/>
                                            </td>
                                                <td width="300px">
                                                <br/>
                                                </td>
                                        </tr>
                                        <tr>
                                            <td width="500px">
                                            Fecha Emisi&oacute;n: '. $obj->getFechaDeEmision($obj->getSubXML(),'fechaEmision') .'
                                            </td>
                                                <td width="300px">
                     
                                                </td>
                                        </tr>
                                    </table>

                                </div>
                                <br/>
        
        
                                <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-small">Comprobante</th>
                                    <th class="text-small">N&uacute;mero de Comprobante</th>
                                    <th class="text-small">Fecha de Emisi&oacute;n</th>
                                    <th class="text-small">Ejercicio Fiscal</th>
                                    <th class="text-small">Base Imponible para la Retenci&oacute;n</th>
                                    <th class="text-small">Impuesto</th>
                                    <th class="text-small">Porcentaje de Retenci&oacute;n</th>
                                    <th class="text-small">Valor Retenido</th>
                                </tr>
                                </thead>
 
                                <tbody>
                                ';

                                        $arr = $obj->readDetallesRetencion($obj->getSubXML());

                                        for($i = 0; $i < count($arr); $i++){
                                            $html = $html . '<tr style="border: 1px solid black;">';
                                            for($j = 0; $j < count($arr[$i]); $j++){
                                                $html = $html . '<td style="border: 1px solid black;padding:5px">'. $arr[$i][$j] . '</td>';
                                            }
                                            $html = $html . '</tr>';
                                        }

                                        $html = $html . '
                                </tbody>
                            </table>

                            <div style="float: left;  height: 443px; width: 400px">

                                        <div style="width: 300px;text-align:center;border: 1px solid #000;padding:10px">
                                            Informacion Adicional:
                                        </div>
            
                                        <div style="width: 300px;text-align:center;padding:10px;font-size:30px;color:#DCDCDC">
                                            LA INFORMACION IMPRESA NO TIENE VALIDEZ
                                        </div>
                                    </div>
        
                                    <div style="float: right;  height: 400px; width: 330px;">
        
                                    <table class="table table-bordered text-right">
                                        <tbody>
                                            <tr>
                                                <td style="border: 1px solid black;width:200px;padding:11px">TOTAL RETENIDO:</td>
                                                <td style="border: 1px solid black;padding:10px">'.number_format($obj->getTotalRetenido($obj->getSubXML()), 2, '.', ' ').'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                <div style="clear: both"></div>
                            </div>';
                break;
            default:
                break;
        }
        return $html;
    }

}