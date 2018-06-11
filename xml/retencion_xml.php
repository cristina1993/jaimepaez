<?php
session_start();
include_once "../Includes/set_xml.php";
include_once "../Clases/clsSetting.php";
include_once "../Clases/clsClase_retencion.php";
$Set = new Set();
$Clase_retencion = new Clase_retencion();
$id = $_REQUEST[id];
$ndoc = explode('-', $id);
$esp = str_replace('-', '', $id);
$ems = $ndoc[0];
$emisor = intval($ndoc[0]);
$pt_ems = $ndoc[1];
$num_comp = $ndoc[2];
$rst_enc = pg_fetch_array($Clase_retencion->lista_pdf_retencion($esp));
$rst_cli = pg_fetch_array($Clase_retencion->lista_clientes_id($rst_enc[cli_id]));
$emis = pg_fetch_array($Set->lista_emisor($emisor));
$cns = $Clase_retencion->lista_pdf_retencion($esp); // lista detalle retencion
$rst_comp = pg_fetch_array($Clase_retencion->lista_pdf_retencion($esp));
$cod_doc = "07"; //01= factura, 02=nota de credito tabla 4
$f = $rst_enc["fecha_emision"];
$dir_cliente = $rst_cli["cli_calle_prin"] . $rst_cli[cli_numeracion];
$telf_cliente = $rst_cli["cli_telefono"];
$email_cliente = $rst_cli["cli_email"];
$direccion = $emis[dir_establecimiento_emisor];
$contabilidad = "SI";
$tipo_id_comprador = "04"; //Ruc-Pasaporte, etc
$razon_soc_comprador = trim($rst_cli['cli_apellidos']) . trim($rst_cli['cli_nombres']) . trim($rst_cli['cli_raz_social']);
$id_comprador = $rst_cli["cli_ced_ruc"];
$fecha = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
$fecha1 = substr($f, -2) . "/" . substr($f, 4, 2) . "/" . substr($f, 0, 4);

if (strlen($id_comprador) == 13) {
    $tipo_id_comprador = "04"; //RUC 04 
} else if (strlen($id_comprador) == 10) {
    $tipo_id_comprador = "05"; //CEDULA 05 
} else if ($id_comprador == '9999999999999') {
    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
} else {
    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
}
$clave1 = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $num_comp . $codigo . $tp_emison);
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
$clave = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente . $ems . $pt_ems . $num_comp . $codigo . $tp_emison . $digito);

$xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
$xml.="<comprobanteRetencion version='1.0.0' id='comprobante'>" . chr(13);
$xml.="<infoTributaria>" . chr(13);
$xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
$xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
$xml.="<razonSocial>" . $emis[nombre] . "</razonSocial>" . chr(13);
$xml.="<nombreComercial>" . $emis[nombre_comercial] . "</nombreComercial>" . chr(13);
$xml.="<ruc>" . $emis[identificacion] . "</ruc>" . chr(13);
$xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
$xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
$xml.="<estab>" . $ems . "</estab>" . chr(13);
$xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
$xml.="<secuencial>" . $num_comp . "</secuencial>" . chr(13);
$xml.="<dirMatriz>" . $emis[dir_establecimiento_matriz] . "</dirMatriz>" . chr(13);
$xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
$xml.="<infoCompRetencion>" . chr(13);
$xml.="<fechaEmision>" . $fecha1 . "</fechaEmision>" . chr(13);
$xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
$xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
$xml.="<tipoIdentificacionSujetoRetenido>" . $tipo_id_comprador . "</tipoIdentificacionSujetoRetenido>" . chr(13);
$xml.="<razonSocialSujetoRetenido>" . $razon_soc_comprador . "</razonSocialSujetoRetenido>" . chr(13);
$xml.="<identificacionSujetoRetenido>" . $id_comprador . "</identificacionSujetoRetenido>" . chr(13);
$xml.="<periodoFiscal>" . $rst_comp[ejercicio_fiscal] . "</periodoFiscal>" . chr(13);
$xml.="</infoCompRetencion>" . chr(13);

$xml.="<impuestos>" . chr(13);
while ($reg_det = pg_fetch_array($cns)) {
    $rst_cod = pg_fetch_array($Clase_retencion->lista_un_porcentaje($reg_det[por_id]));
    $xml.="<impuesto>" . chr(13);
    if ($reg_det[tipo_impuesto] == "IV") {
        $reg_det[tipo_impuesto] = 2;
    } else if ($reg_det[tipo_impuesto] == "IR") {
        $reg_det[tipo_impuesto] = 1;
    } else if ($reg_det[tipo_impuesto] == "ID") {
        $reg_det[tipo_impuesto] = 3;
    }
    $xml.="<codigo>" . $reg_det[tipo_impuesto] . "</codigo>" . chr(13);
    $xml.="<codigoRetencion>" . $rst_cod[por_codigo] . "</codigoRetencion>" . chr(13);
    $xml.="<baseImponible>" . $reg_det[base_imponible] . "</baseImponible>" . chr(13);
    $xml.="<porcentajeRetener>" . $reg_det[porcentaje_retencion] . "</porcentajeRetener>" . chr(13);
    $xml.="<valorRetenido>" . $reg_det[valor_retenido] . "</valorRetenido>" . chr(13);
    $xml.="<codDocSustento>0" . $reg_det[tipo_comprobante] . "</codDocSustento>" . chr(13);
    $xml.="<numDocSustento>" . $reg_det[num_comp_retenido] . "</numDocSustento>" . chr(13);
    $xml.="<fechaEmisionDocSustento>" . $fecha1 . "</fechaEmisionDocSustento>" . chr(13);
    $xml.="</impuesto>" . chr(13);
}
$xml.="</impuestos>" . chr(13);
$xml.="<infoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Email'>" . $email_cliente . "</campoAdicional>" . chr(13);
$xml.="</infoAdicional>" . chr(13);
$xml.="</comprobanteRetencion>" . chr(13);

if ($xml_generator == 1) {
    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
    $sms = $clave . '&' . shell_exec($comando);
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
