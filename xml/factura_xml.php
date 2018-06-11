<?php

session_start();
include_once "../Includes/set_xml.php";
include_once "../Clases/clsSetting.php";
include_once "../Clases/clsClase_pagos.php";
$Set = new Set();
$Clase_pagos = new Clase_pagos();
$id = $_REQUEST[id];
$rst_enc = pg_fetch_array($Set->lista_una_factura_id($id));
$ndoc = explode('-', $rst_enc[num_documento]);
$nfact = str_replace('-', '', $rst_enc["num_documento"]);
$ems = $ndoc[0];
$emisor = intval($ndoc[0]);
$pt_ems = $ndoc[1];
$secuencial = $ndoc[2];
$emis = pg_fetch_array($Set->lista_emisor($emisor));
$cns_det = $Set->lista_detalle_factura(trim($nfact));
$cns_det2 = $Set->lista_detalle_factura(trim($nfact));
$cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
$f = $rst_enc['fecha_emision'];
$fecha = substr($f, -2) . '/' . substr($f, 4, 2) . '/' . substr($f, 0, 4);
$f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
$dir_cliente = string($rst_enc["direccion_cliente"]);
$telf_cliente = string($rst_enc["telefono_cliente"]);
$email_cliente = string($rst_enc["email_cliente"]);
$direccion = string($emis[dir_establecimiento_emisor]);
$contabilidad = "SI";
$razon_soc_comprador = string($rst_enc["nombre"]);
$id_comprador = $rst_enc["identificacion"];
$num_guia_remision = $rst_enc["num_guia_remision"];
if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
    $tipo_id_comprador = "04"; //RUC 04 
} else if (strlen($id_comprador) == 10) {
    $tipo_id_comprador = "05"; //CEDULA 05 
} else if ($id_comprador == '9999999999999') {
    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
} else {
    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
}

$round = 2;

$clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);

$cla = strrev($clave1);
$n = 0;
$p = 1;
$i = strlen($clave1);
$m = 0;
$s = 0;
$j = 2;
while ($n < $i) {
    $d = substr($cla, $n, 1);
    $m = $d * $j;
    $s = $s + $m;
    $j++;
    if ($j == 8) {
        $j = 2;
    }
    $n++;
}
$div = $s % 11;
$digito = 11 - $div;
if ($digito < 10) {
    $digito = $digito;
} else if ($digito == 10) {
    $digito = 1;
} else if ($digito == 11) {
    $digito = 0;
}
$clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
$xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
$xml.="<factura version='1.1.0' id='comprobante'>" . chr(13);
$xml.="<infoTributaria>" . chr(13);
$xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
$xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
$xml.="<razonSocial>" . string($emis[nombre]) . "</razonSocial>" . chr(13);
$xml.="<nombreComercial>" . string($emis[nombre_comercial]) . "</nombreComercial>" . chr(13);
$xml.="<ruc>" . trim($emis[identificacion]) . "</ruc>" . chr(13);
$xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
$xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
$xml.="<estab>" . $ems . "</estab>" . chr(13);
$xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
$xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
$xml.="<dirMatriz>" . string($emis[dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
$xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
$xml.="<infoFactura>" . chr(13);
$xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
$xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
$xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
$xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
$xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
$xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
$xml.="<totalSinImpuestos>" . round($rst_enc["subtotal12"] + $rst_enc["subtotal0"] + $rst_enc["subtotal_exento_iva"] + $rst_enc["subtotal_no_objeto_iva"], $round) . "</totalSinImpuestos>" . chr(13);
$xml.="<totalDescuento>" . round($rst_enc["total_descuento"], $round) . "</totalDescuento>" . chr(13);
$xml.="<totalConImpuestos>" . chr(13);
$base = 0;
while ($reg_detalle = pg_fetch_array($cns_det)) {
    if($reg_detalle[iva]==12){
        $codPorc=2;
    }else{
        $codPorc=0;
    }
    
    $base = $base + round($reg_detalle["precio_total"], $round);
}
    if($codPorc==2){
        $valo_iva=round($base * 12 / 100, $round);
    }else{
        $valo_iva=0.00;
    }

$xml.="<totalImpuesto>" . chr(13);
$xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
$xml.="<codigoPorcentaje>".$codPorc."</codigoPorcentaje>" . chr(13); //Codigo del
$xml.="<baseImponible>" . $base . "</baseImponible>" . chr(13);
$xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
$xml.="</totalImpuesto>" . chr(13);
$xml.="</totalConImpuestos>" . chr(13);
$xml.="<propina>0.00</propina>" . chr(13);
$xml.="<importeTotal>" . round($rst_enc["total_valor"], $round) . "</importeTotal>" . chr(13);
$xml.="<moneda>DOLAR</moneda>" . chr(13);
$xml.="</infoFactura>" . chr(13);

$xml.="<detalles>" . chr(13);
while ($reg_detalle = pg_fetch_array($cns_det2)) {
    $xml.="<detalle>" . chr(13);
    $xml.="<codigoPrincipal>" . trim($reg_detalle["cod_producto"]) . "</codigoPrincipal>" . chr(13);
    
        if (strlen(trim($reg_detalle["cod_aux"]))==0) {
            $reg_detalle["cod_aux"] = $reg_detalle["cod_producto"];
        }
        $xml.="<codigoAuxiliar>" . trim($reg_detalle["cod_aux"]) . "</codigoAuxiliar>" . chr(13);
        
        
    $xml.="<descripcion>" . trim(string($reg_detalle["descripcion"])) . "</descripcion>" . chr(13);
    $xml.="<cantidad>" . round($reg_detalle["cantidad"], $round) . "</cantidad>" . chr(13);
    $xml.="<precioUnitario>" . round($reg_detalle["precio_unitario"], 4) . "</precioUnitario>" . chr(13);
    $xml.="<descuento>" . round($reg_detalle["descuent"], $round) . "</descuento>" . chr(13);
    $xml.="<precioTotalSinImpuesto>" . round($reg_detalle["precio_total"], $round) . "</precioTotalSinImpuesto>" . chr(13);
    $xml.="<impuestos>" . chr(13);
    $xml.="<impuesto>" . chr(13);
    $xml.="<codigo>2</codigo>" . chr(13);
    if($reg_detalle[iva]==12){
        $codPorc=2;
        $valo_iva=round($reg_detalle["precio_total"] * 12 / 100, $round);
    }else{
        $codPorc=0;
        $valo_iva=0.00;
    }
    $xml.="<codigoPorcentaje>".$codPorc."</codigoPorcentaje>" . chr(13);
    $xml.="<tarifa>" . $reg_detalle[iva] . "</tarifa>" . chr(13);
    $xml.="<baseImponible>" . round($reg_detalle["precio_total"], $round) . "</baseImponible>" . chr(13);
    $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
    $xml.="</impuesto>" . chr(13);
    $xml.="</impuestos>" . chr(13);
    $xml.="</detalle>" . chr(13);
}
$xml.="</detalles>" . chr(13);
$xml.="<infoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
if ($rst_enc[observaciones] != '') {
    $xml.="<campoAdicional nombre='Observaciones'>" . strtoupper($rst_enc[observaciones]) . "</campoAdicional>" . chr(13);
}
//$cns_pag = $Clase_pagos->lista_pago_factura("001" . $ems . $secuencial);
//while ($rst_pag = pg_fetch_array($cns_pag)) {
//    $xml.="<campoAdicional nombre='fechaPago'>" . $rst_pag[fecha_pago] . "</campoAdicional>" . $reg . chr(13);
//    $xml.="<campoAdicional nombre='Valor'>" . $rst_pag[valor_pago] . "</campoAdicional>" . $reg . chr(13);
//}
$xml.="</infoAdicional>" . chr(13);
$xml.="</factura>" . chr(13);


if ($xml_generator == 1 && $_REQUEST[gnr] != 1) {
    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $sms = $clave . '&' . shell_exec($comando);
    $dt = explode('&', $sms);
    $f1 = explode('T', trim($dt[5]));
    $fh = $f1[0] . ' ' . substr($f1[1], 0, 8);
    
    $xml_autorizado = '<?xml version="1.0" encoding="UTF-8" ?> 
                        <autorizacion> 
                            <estado>' . $dt[2] . '</estado> 
                            <numeroAutorizacion>' . $dt[4] . '</numeroAutorizacion> 
                            <fechaAutorizacion class="fechaAutorizacion">' . $fh . '</fechaAutorizacion> 
                            <comprobante>' . $dt[6] . '</comprobante> 
                        </autorizacion>';
    
    $sms = $dt[0].'&'.$dt[1].'&'.$dt[2].'&'.$dt[3].'&'.$dt[4].'&'.$fh.'&'.$xml_autorizado;
    
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml_autorizado);
    fclose($fch);
    
} else {
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);
    $sms = $clave;
}
echo $sms;
?>
