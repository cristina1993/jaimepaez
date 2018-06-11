<?php

session_start();
include_once "../Includes/set_xml.php";
include_once "../Clases/clsSetting.php";
include_once "../Clases/clsClase_nota.php";
$Set = new Set();
$Clase_nota_Credito = new Clase_nota_Credito();
$id = $_REQUEST[id];
$esp = str_replace("-", "", $id);
$num = substr($esp, 7, 9);
$cns_nota = $Clase_nota_Credito->lista_una_nota_credito($esp, $emisor);
$rst_enc = pg_fetch_array($Clase_nota_Credito->lista_una_nota_credito($esp, $emisor)); //lista factura
$ndoc = explode('-', $rst_enc[num_documento]);
$esp1 = str_replace('-', '', $rst_enc["num_documento"]);
$ems = $ndoc[0];
$emisor = intval($ndoc[0]);
$pt_ems = $ndoc[1];
$num1 = $ndoc[2];
$cns_det_nota = $Clase_nota_Credito->lista_detalle_nota_credito($esp1);
$cns_det_nota1 = $Clase_nota_Credito->lista_detalle_nota_credito($esp1);
$cns_det_nota2 = $Clase_nota_Credito->lista_detalle_nota_credito($esp1);
$rst_det_nota = pg_fetch_array($cns_det_nota); //lista factura
$emis = pg_fetch_array($Set->lista_emisor($emisor));
$cns = $Clase_nota_Credito->lista_detalle_factura($esp); //detalle factura
//$fecha = substr($f1, -2) . "/" . substr($f1, 4, 2) . "/" . substr($f1, 0, 4);
$rst_fac = pg_fetch_array($Clase_nota_Credito->lista_un_comprobante($num, $emisor));
$rst_fec = pg_fetch_array($Clase_nota_Credito->lista_una_nota_credito($esp, $emisor));

$f = $rst_enc["fecha_emision"];
$fecha = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
$fec = substr($f, -2) . "/" . substr($f, 4, 2) . "/" . substr($f, 0, 4);

$f1 = $rst_enc["fecha_emision_comprobante"];
$fecha2 = substr($f1, -2) . "/" . substr($f1, 4, 2) . "/" . substr($f1, 0, 4);
$tipo_emi = 1; //tipo emision 1=normal, 2= por indisponibilidad tabla 2
$cod_doc = "04"; //01= factura, 04=nota de credito tabla 4

$nf = $rst_enc[num_factura_modifica];
$num_factura_modifica = substr($nf, 0, 3) . "-" . substr($nf, 3, 3) . "-" . substr($nf, 6, 9);

$dir_cliente = $rst_enc["direccion_cliente"];
$telf_cliente = $rst_enc["telefono_cliente"];
$email_cliente = $rst_enc["email_cliente"];
$direccion = $emis[dir_establecimiento_emisor];
$contabilidad = "SI";
$razon_soc_comprador = $rst_enc["nombre"];
$id_comprador = $rst_enc["identificacion"];
if (strlen($id_comprador) == 13) {
    $tipo_id_comprador = "04"; //RUC 04 
} else if (strlen($id_comprador) == 10) {
    $tipo_id_comprador = "05"; //CEDULA 05 
} else if ($id_comprador == '9999999999999') {
    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
} else {
    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
}
$clave1 = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $num1 . $codigo . $tp_emison);
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

$clave = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $num1 . $codigo . $tp_emison . $digito);
$xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
$xml.="<notaCredito version= '1.1.0' id='comprobante'>" . chr(13);
$xml.="<infoTributaria>" . chr(13);
$xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
$xml.="<tipoEmision>" . $tipo_emi . "</tipoEmision>" . chr(13);
$xml.="<razonSocial>" . $emis[nombre] . "</razonSocial>" . chr(13);
$xml.="<nombreComercial>" . $emis[nombre_comercial] . "</nombreComercial>" . chr(13);
$xml.="<ruc>" . $emis[identificacion] . "</ruc>" . chr(13);
$xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
$xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
$xml.="<estab>" . $ems . "</estab>" . chr(13);
$xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
$xml.="<secuencial>" . $num1 . "</secuencial>" . chr(13);
$xml.="<dirMatriz>" . $emis[dir_establecimiento_matriz] . "</dirMatriz>" . chr(13);
$xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
$xml.="<infoNotaCredito>" . chr(13);
$xml.="<fechaEmision>" . $fec . "</fechaEmision>" . chr(13);
$xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
$xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
$xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
$xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
$xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
$xml.="<codDocModificado>01</codDocModificado>" . chr(13);
$xml.="<numDocModificado>" . $num_factura_modifica . "</numDocModificado>" . chr(13);
$xml.="<fechaEmisionDocSustento>" . $fecha2 . "</fechaEmisionDocSustento>" . chr(13);
$subtotal = $rst_enc[subtotal12] + $rst_enc[subtotal0] + $rst_enc[subtotal_exento_iva] + $rst_enc[subtotal_no_objeto_iva];
$subtotal = number_format($subtotal, 2, ".", "");
$xml.="<totalSinImpuestos>" . $subtotal . "</totalSinImpuestos>" . chr(13);
$xml.="<valorModificacion>" . number_format($rst_enc[total_valor],2) . "</valorModificacion>" . chr(13);
$xml.="<moneda>DOLAR</moneda>" . chr(13);

$xml.="<totalConImpuestos>" . chr(13);
while ($rst_det1 = pg_fetch_array($cns_nota)) {
    $xml.="<totalImpuesto>" . chr(13);
    if ($rst_det1[total_ice] != 0) {
        $cod = "3";
    } else if ($rst_det1[total_iva] != 0) {
        $cod = "2";
    } else if ($rst_det1[total_irbpnr] != 0) {
        $cod = "5";
    }
    $xml.="<codigo>" . $cod . "</codigo>" . chr(13);
    if ($rst_det1["iva"] == "0") {
        $iva = "0";
        $iv = 0;
    } else if ($rst_det_nota["iva"] == "12") {
        $iva = "2";
        $iv = 12;
    } else if ($rst_det_nota["iva"] != "EX") {
        $iva = "7";
        $iv = 0;
    } else if ($rst_det_nota["iva"] != "NO") {
        $iva = "6";
        $iv = 0;
    }
    $xml.="<codigoPorcentaje>" . $iva . "</codigoPorcentaje>" . chr(13);
    $xml.="<baseImponible>" . $subtotal . "</baseImponible>" . chr(13);
    $valor = ( $subtotal * $iv / 100);
    $xml.="<valor>" . round($valor, 2) . "</valor>" . chr(13); ////valor preguntar
}
$xml.="</totalImpuesto>" . chr(13);
$xml.="</totalConImpuestos>" . chr(13);
$xml.="<motivo>" . $rst_det_nota[descripcion_motivo] . "</motivo>" . chr(13);
$xml.="</infoNotaCredito>" . chr(13);

$xml.="<detalles>" . chr(13);

while ($rst_det5 = pg_fetch_array($cns_det_nota1)) {
    $xml.="<detalle>" . chr(13);
    if ($rst_det5[cod_producto] != '') {
        $xml.="<codigoInterno>" . $rst_det5[cod_producto] . "</codigoInterno>" . chr(13);
    }
    $xml.="<descripcion>" . $rst_det5[descripcion] . "</descripcion>" . chr(13);
    $xml.="<cantidad>" . number_format($rst_det5[cantidad], 2, ".", "") . "</cantidad>" . chr(13);
    $xml.="<precioUnitario>" . $rst_det5[precio_unitario] . "</precioUnitario>" . chr(13);
    $xml.="<descuento>" . number_format($rst_det5[descuent], 4, ".", "") . "</descuento>" . chr(13);
    $xml.="<precioTotalSinImpuesto>" . $rst_det5[precio_total] . "</precioTotalSinImpuesto>" . chr(13);
    $xml.="<impuestos>" . chr(13);
    $xml.="<impuesto>" . chr(13);
    if ($rst_det5["ice"] != '') {
        $cod1 = "3";
    } else if ($rst_det5["iva"] != '') {
        $cod1 = "2";
    }
    if ($rst_det5["iva"] == "0") {
        $iva1 = "0";
        $vi = 0;
    } else if ($rst_det5["iva"] == "12") {
        $iva1 = "2";
        $vi = 12;
    } else if ($rst_det5["iva"] == "EX") {
        $iva1 = "7";
        $vi = 0;
    } else if ($rst_det5["iva"] == "NO") {
        $iva1 = "6";
        $vi = 0;
    }
    $xml.="<codigo>" . $cod1 . "</codigo>" . chr(13);
    $xml.="<codigoPorcentaje>" . $iva1 . "</codigoPorcentaje>" . chr(13);
    $xml.="<tarifa>" . $vi . "</tarifa>" . chr(13);
    $xml.="<baseImponible>" . $rst_det5[precio_total] . "</baseImponible>" . chr(13);
    $val = $rst_det5[precio_total] * $vi / 100;
    $xml.="<valor>" . round($val, 4) . "</valor>" . chr(13);
    $xml.="</impuesto>" . chr(13);
    $xml.="</impuestos>" . chr(13);
    $xml.="</detalle>" . chr(13);
}

$xml.="</detalles>" . chr(13);
$xml.="<infoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="<campoAdicional nombre='Email'>" . $email_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="</infoAdicional>" . chr(13);
$xml.="</notaCredito>" . chr(13);


if ($xml_generator == 1) {
    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $sms = $clave . '&' . shell_exec($comando);
    $dt = explode('&', $sms);
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $dt[6]);
    fclose($fch);
} else {
    $fch = fopen($clave . ".xml", "w+");
    fwrite($fch, $xml);
    fclose($fch);
    $sms = 'Local';
}
echo $sms;
?>
