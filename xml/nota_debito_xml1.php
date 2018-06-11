<?php
session_start();
include_once "../Includes/set_xml.php";
include_once "../Clases/clsSetting.php";
include_once "../Clases/clsClase_nota_debito.php";
$Set = new Set();
$Clase_nota_debito = new Clase_nota_debito();
$id = $_REQUEST[id];
$esp = str_replace("-", "", $id);
$num = substr($esp, 7, 9);
$rst_enc = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito($esp, $emisor)); //lista factura
$rst_fac = pg_fetch_array($Clase_nota_debito->lista_una_factura($num, $emisor));
$ndoc = explode('-', $rst_enc[num_documento]);
$esp1 = str_replace('-', '', $rst_enc["num_documento"]);
$ems = $ndoc[0];
$emisor = intval($ndoc[0]);
$pt_ems = $ndoc[1];
$num1 = $ndoc[2];


//$num1 = substr($esp1, 6, 9);
$emis = pg_fetch_array($Set->lista_emisor($emisor));
$f = $rst_enc["fecha_emision"];
$fecha = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
$fecha1 = substr($f, -2) . "/" . substr($f, 4, 2) . "/" . substr($f, 0, 4);
$descuento == "0";
$cns_det = $Clase_nota_debito->lista_detalle_nota($esp1); //lista factura
$cns_det1 = $Clase_nota_debito->lista_detalle_nota($esp1); //lista factura

$nf=$rst_enc[num_factura_modifica];
$num_factura_modifica=substr($nf, 0,3) . "-" . substr($nf, 3, 3) . "-" . substr($nf,6 , 9);

$tipo_emi = 1; //tipo emision 1=normal, 2= por indisponibilidad tabla 2
$cod_doc = "05"; //01= factura, 02=nota de credito tabla 4
$f1 = $rst_enc["fecha_emision_comprobante"];
$fecha2 = substr($f, -2) . "/" . substr($f, 4, 2) . "/" . substr($f, 0, 4);
$f3 = $rst_fac["fecha_emision"];
$fecha3 = substr($f3, -2) . "/" . substr($f3, 4, 2) . "/" . substr($f3, 0, 4);
$dir_cliente = $rst_enc["direccion_cliente"];
$telf_cliente = $rst_enc["telefono_cliente"];
$email_cliente = $rst_enc["email_cliente"];
$direccion = $emis[dir_establecimiento_emisor];
$contabilidad = "SI";
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
$razon_soc_comprador = $rst_enc["nombre"];
$id_comprador = $rst_enc["identificacion"];


$clave1 = trim($fecha . $cod_doc . $emis[identificacion] . $ambiente .$ems. $pt_ems .$num1. $codigo . $tp_emison);
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

$clave = $fecha . $cod_doc . $emis[identificacion] . $ambiente .$ems. $pt_ems .$num1. $codigo . $tp_emison . $digito;

$xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
//$xml.="<notaDebito version= '1.1.0' id='comprobante'>" . chr(13);
$xml.="<notaDebito version='1.0.0' id='comprobante'>" . chr(13);
$xml.="<infoTributaria>" . chr(13);
$xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
$xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
$xml.="<razonSocial>" . $emis[nombre] . "</razonSocial>" . chr(13);
$xml.="<nombreComercial>" . $emis[nombre_comercial] . "</nombreComercial>" . chr(13);
$xml.="<ruc>" . $emis[identificacion] . "</ruc>" . chr(13);
$xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
$xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
$xml.="<estab>001</estab>" . chr(13);
$xml.="<ptoEmi>" . $ems . "</ptoEmi>" . chr(13);
$xml.="<secuencial>" . $num1 . "</secuencial>" . chr(13);
$xml.="<dirMatriz>" . $emis[dir_establecimiento_matriz] . "</dirMatriz>" . chr(13);
$xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
$xml.="<infoNotaDebito>" . chr(13);
$xml.="<fechaEmision>" . $fecha1 . "</fechaEmision>" . chr(13);
$xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
$xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
$xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
$xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
$xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
$xml.="<codDocModificado>01</codDocModificado>" . chr(13);
$xml.="<numDocModificado>" . $num_factura_modifica . "</numDocModificado>" . chr(13);
$xml.="<fechaEmisionDocSustento>" . $fecha3 . "</fechaEmisionDocSustento>" . chr(13);

$subtotal = $rst_enc["subtotal12"] + $rst_enc["subtotal0"] + $rst_enc["subtotal_exento_iva"] + $rst_enc["subtotal_no_objeto_iva"];


$xml.="<totalSinImpuestos>" . $subtotal . "</totalSinImpuestos>" . chr(13);


$xml.="<impuestos>" . chr(13);
while ($rst_det = pg_fetch_array($cns_det)) {
    $xml.="<impuesto>" . chr(13);
    if ($rst_det[iva] != "") {
        $imp = 2;
        $iva = $rst_det[iva];
        if ($rst_det[iva] == "0") {
            $por = 0;
        } else if ($rst_det[iva] == "12") {
            $por = 2;
        } else if ($rst_det[iva] == "NO") {
            $por = 6;
        } else if ($rst_det[iva] == "EX") {
            $por = 7;
        }
    } else if ($rst_det[ice] != "") {
        $imp = 3;
        $iva = $rst_det[ice];
    }

    $xml.="<codigo>" . $imp . "</codigo>" . chr(13);
    $xml.="<codigoPorcentaje>" . $por . "</codigoPorcentaje>" . chr(13);
    $xml.="<tarifa>" . $iva . "</tarifa>" . chr(13);
    $xml.="<baseImponible>" . $rst_det[precio_total] . "</baseImponible>" . chr(13);
    $valor = ($rst_det[precio_total] * $iva) / 100;
    $xml.="<valor>" . $valor . "</valor>" . chr(13);
    $xml.="</impuesto>" . chr(13);
}
$xml.="</impuestos>" . chr(13);
$xml.="<valorTotal>" . $rst_enc[total_valor] . "</valorTotal>" . chr(13);
$xml.="</infoNotaDebito>" . chr(13);
$xml.="<motivos>" . chr(13);
while ($rst_det1 = pg_fetch_array($cns_det1)) {
    $xml.="<motivo>" . chr(13);
    $xml.="<razon>" . $rst_det1[descripcion] . "</razon>" . chr(13);
    $xml.="<valor>" . $rst_det1[precio_total] . "</valor>" . chr(13);
    $xml.="</motivo>" . chr(13);
}
$xml.="</motivos>" . chr(13);
$xml.="<infoAdicional>" . chr(13);
$xml.="<campoAdicional nombre='Direccion'>".$dir_cliente."</campoAdicional>".$reg.chr(13);
$xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="<campoAdicional nombre='Email'>" . $email_cliente . "</campoAdicional>" . $reg . chr(13);
$xml.="</infoAdicional>" . chr(13);
$xml.="</notaDebito>" . chr(13);

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
