<?php
session_start();
include_once "../Includes/set_xml.php";
include_once "../Clases/clsSetting.php";
include_once "../Clases/clsClase_guia_remision.php";
$Set = new Set();
$Clase_guia_remision = new Clase_guia_remision();
$id = $_REQUEST[id];
$cns = $Clase_guia_remision->lista_una_guia($id);
$rst_guia = pg_fetch_array($Clase_guia_remision->lista_una_guia($id));
$num = substr($rst_guia[num_comprobante_venta], 7, 9);
$rst_enc = pg_fetch_array($Clase_guia_remision->lista_una_factura($num, $emisor)); //lista factura
$pt_ems = substr($id, 0, 3);
$ems = substr($id, 3, 3);
$emisor = intval($pt_ems);
$num1 = substr($id, 6, 9);
$emis = pg_fetch_array($Set->lista_emisor($emisor));
$f = $rst_guia["fecha_emision"];
$fecha = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
$cod_doc = "06";
$f1 = $rst_enc["fecha_emision"];
$fecha2 = substr($f1, -2) . "/" . substr($f1, 4, 2) . "/" . substr($f1, 0, 4);
$dir_cliente = $rst_enc["direccion_cliente"];
$telf_cliente = $rst_enc["telefono_cliente"];
$email_cliente = $rst_enc["email_cliente"];
$direccion = $emis[dir_establecimiento_emisor];
$contabilidad = "SI";
//$tipo_id_comprador = "04"; //Ruc-Pasaporte, etc
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


$id_transportista = $rst_guia[identificacion_trasportista];
if (strlen($id_transportista) == 13) {
    $tipo_transportista = "04"; //RUC 04 
} else if (strlen($id_transportista) == 10) {
    $tipo_transportista = "05"; //CEDULA 05 
} else {
    $tipo_transportista = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
}

$nf = $rst_guia[num_comprobante_venta];
$num_comprobante_venta = substr($nf, 0, 3) . "-" . substr($nf, 3, 3) . "-" . substr($nf, 6, 9);
$clave1 = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $pt_ems . $ems . $num1 . $codigo . $tp_emison);
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
$clave = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $pt_ems . $ems . $num1 . $codigo . $tp_emison . $digito);
$xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
$xml.="<guiaRemision version= '1.1.0' id='comprobante'>" . chr(13);
$xml.="<infoTributaria>" . chr(13);
$xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
$xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
$xml.="<razonSocial>" . $emis[nombre] . "</razonSocial>" . chr(13);
$xml.="<nombreComercial>" . $emis[nombre_comercial] . "</nombreComercial>" . chr(13);
$xml.="<ruc>" . $emis[identificacion] . "</ruc>" . chr(13);
$xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
$xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
$xml.="<estab>" . $pt_ems . "</estab>" . chr(13);
$xml.="<ptoEmi>" .$ems. "</ptoEmi>" . chr(13);
$xml.="<secuencial>" . $num1 . "</secuencial>" . chr(13);
$xml.="<dirMatriz>" . $emis[dir_establecimiento_matriz] . "</dirMatriz>" . chr(13);
$xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
$xml.="<infoGuiaRemision>" . chr(13);
////direccion cliente
$xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
$xml.="<dirPartida>" . $rst_guia[punto_partida] . "</dirPartida>" . chr(13);
$rst_trans = pg_fetch_array($Clase_guia_remision->lista_un_transportista($rst_guia[identificacion_trasportista]));
////transportista
$xml.="<razonSocialTransportista>" . $rst_trans[razon_social] . "</razonSocialTransportista>" . chr(13);
$xml.="<tipoIdentificacionTransportista>$tipo_transportista</tipoIdentificacionTransportista>" . chr(13);
$xml.="<rucTransportista>" . $rst_guia[identificacion_trasportista] . "</rucTransportista>" . chr(13);
$xml.="<obligadoContabilidad>SI</obligadoContabilidad>" . chr(13);
$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
$f_ini = $rst_guia[fecha_inicio_transporte];
$fec_ini = substr($f_ini, -2) . "/" . substr($f_ini, 4, 2) . "/" . substr($f_ini, 0, 4);
$xml.="<fechaIniTransporte>$fec_ini</fechaIniTransporte>" . chr(13);
$f_fin = $rst_guia[fecha_fin_transporte];
$fec_fin = substr($f_fin, -2) . "/" . substr($f_fin, 4, 2) . "/" . substr($f_fin, 0, 4);
$xml.="<fechaFinTransporte>$fec_fin</fechaFinTransporte>" . chr(13);
//transportista
$xml.="<placa>$rst_trans[placa]</placa>" . chr(13);
$xml.="</infoGuiaRemision>" . chr(13);
$xml.="<destinatarios>" . chr(13);
$xml.="<destinatario>" . chr(13);
$xml.="<identificacionDestinatario>" . $rst_guia[identificacion_destinario] . "</identificacionDestinatario>" . chr(13);
///cliente
$xml.="<razonSocialDestinatario>" . $rst_guia[nombre_destinatario] . "</razonSocialDestinatario>" . chr(13);
$xml.="<dirDestinatario>" . $rst_guia[destino] . "</dirDestinatario>" . chr(13);
$xml.="<motivoTraslado>" . $rst_guia[motivo_traslado] . "</motivoTraslado>" . chr(13);
if ($rst_guia[documento_aduanero] != '') {
    $xml.="<docAduaneroUnico>" . $rst_guia[documento_aduanero] . "</docAduaneroUnico>" . chr(13);
}
if ($rst_guia[cod_establecimiento_destino] != '') {
    $xml.="<codEstabDestino>" . $rst_guia[cod_establecimiento_destino] . "</codEstabDestino>" . chr(13);
}
$xml.="<codDocSustento>01</codDocSustento>" . chr(13);
$xml.="<numDocSustento>" . $num_comprobante_venta . "</numDocSustento>" . chr(13);
$xml.="<numAutDocSustento>" . $rst_enc["com_autorizacion"] . "</numAutDocSustento>" . chr(13);
$xml.="<fechaEmisionDocSustento>" . $fecha2 . "</fechaEmisionDocSustento>" . chr(13);
$xml.="<detalles>" . chr(13);
while ($rst_det = pg_fetch_array($cns)) {
    $xml.="<detalle>" . chr(13);
    $xml.="<codigoInterno>" . $rst_det[cod_producto] . "</codigoInterno>" . chr(13);
    $xml.="<descripcion>" . $rst_det[descripcion_producto] . "</descripcion>" . chr(13);
    $xml.="<cantidad>" . $rst_det[cantidad] . "</cantidad>" . chr(13);
    $xml.="</detalle>" . chr(13);
}
$xml.="</detalles>" . chr(13);
$xml.="</destinatario>" . chr(13);
$xml.="</destinatarios>" . chr(13);
$xml.="<infoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="<campoAdicional nombre='Email'>" . $email_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="</infoAdicional>" . chr(13);
$xml.="</guiaRemision>" . chr(13);

if ($xml_generator == 1) {
    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $sms =$clave.'&'.shell_exec($comando);
    $dt=  explode('&', $sms);
    $fch = fopen("../xml_docs/".$clave.".xml", "w+o");
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
