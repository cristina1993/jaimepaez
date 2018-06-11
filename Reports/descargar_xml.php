<?php

include_once '../Clases/clsClaseSri.php';
$SRI = new SRI();
$clave = $_GET[clave];
$id = $_GET[id];
$tp = $_GET[tp];

$rst_cred = explode('&', $SRI->lista_credenciales()[0]);
$program = $SRI->lista_nombre_programa()[0];
$pass = $rst_cred[1];
echo $firma = $rst_cred[2];
$rst_am = pg_fetch_array($SRI->lista_configuraciones());
$ambiente = $rst_am[con_valor]; //Pruebas 1    Produccion 2
//$ambiente = 1; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal

$parametros = "<parametros>
                        <keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>
                        <keyStorePassword>changeit</keyStorePassword>
                        <ambiente>$ambiente</ambiente>
                        <pathFirma>/var/www/FacturacionElectronica/Scripts/archivos/$firma</pathFirma>
                        <passFirma>$pass</passFirma>
                        </parametros>";

function string($string) {

    $string = trim($string);

    $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
    );

    $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
    );

    $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
    );

    $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
    );

    $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
    );

    $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
            array("\\", "¨", "º", "-", "~",
        "#", "@", "|", "!", "\"",
        "·", "$", "%", "&", "/",
        "(", ")", "?", "'", "¡",
        "¿", "[", "^", "`", "]",
        "+", "}", "{", "¨", "´",
        ">", "< ", ";", ",", ":",
        "."), '', $string
    );


    return $string;
}

$dt = $SRI->recupera_datos($clave, $ambiente);

if ($dt[0] == 'AUTORIZADO') {
    $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
      <ns2:RespuestaAutorizacion xsi:type='ns2:autorizacion' xmlns:ns2='http://ec.gob.sri.ws.autorizacion' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'>
      <estado>" . $dt[0] . "</estado>
      <numeroAutorizacion>" . $dt[1] . "</numeroAutorizacion>
      <fechaAutorizacion>" . $dt[2] . "</fechaAutorizacion>
      <ambiente>" . $dt[3] . "</ambiente>
      <comprobante><![CDATA[" . $dt[4] . "]]></comprobante>
      <mensajes/>
      </ns2:RespuestaAutorizacion>";
} else {

    switch ($tp) {
        case 1:
            $data = factura($id, $ambiente, $codigo, $tp_emison);
            $dt = explode('&', $data);
            $xml = $dt[1];
            $clave = $dt[0];
            break;
        case 4:
            $data = nota_credito($id, $ambiente, $codigo, $tp_emison);
            $dt = explode('&', $data);
            $xml = $dt[1];
            $clave = $dt[0];
            break;
        case 5:
            $data = nota_debito($id, $ambiente, $codigo, $tp_emison);
            $dt = explode('&', $data);
            $xml = $dt[1];
            $clave = $dt[0];
            break;
        case 6:
            $data = guia_remision($id, $ambiente, $codigo, $tp_emison);
            $dt = explode('&', $data);
            $xml = $dt[1];
            $clave = $dt[0];
            break;
        case 7:
            $data = retencion($id, $ambiente, $codigo, $tp_emison);
            $dt = explode('&', $data);
            $xml = $dt[1];
            $clave = $dt[0];
            break;
    }
}
$fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
fwrite($fch, $xml);
fclose($fch);
$file = '../xml_docs/' . $clave . '.xml';
header("Content-type:xml");
header("Content-length:" . filesize($file));
header("Content-Disposition: attachment; filename=$clave.xml");
readfile($file);
unlink($file);

function factura($id, $ambiente, $codigo, $tp_emison) {
    include_once "../Clases/clsClase_factura.php";
    set_time_limit(0);
    $Set = new Clase_factura();
    $round = 2;
//    $Sri = new Clase_factura();
    $rst_enc = pg_fetch_array($Set->lista_una_factura_id($id));
    $ndoc = explode('-', $rst_enc[fac_numero]);
    $nfact = str_replace('-', '', $rst_enc[fac_numero]);
    $ems = $ndoc[0];
    $emisor = intval($ndoc[0]);
    $pt_ems = $ndoc[1];
    $secuencial = $ndoc[2];
    $emis = pg_fetch_array($Set->lista_emisor($emisor));
    $cns_det = $Set->lista_detalle_factura($rst_enc[fac_id]);
    $cns_det2 = $Set->lista_detalle_factura($rst_enc[fac_id]);
    $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
    $fecha = date_format(date_create($rst[fac_fecha_emision]), 'd/m/Y');
    $f2 = date_format(date_create($rst[fac_fecha_emision]), 'dmY');
    $dir_cliente = string($rst_enc[fac_direccion]);
    $telf_cliente = string($rst_enc[fac_telefono]);
    $email_cliente = string($rst_enc[fac_email]);
    $direccion = string($emis[emi_dir_establecimiento_emisor]);
    $contabilidad = "SI";
    $razon_soc_comprador = string($rst_enc[fac_nombre]);
    $id_comprador = $rst_enc[fac_identificacion];
    if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999' && substr($id_comprador, -3) == '001') {
        $tipo_id_comprador = "04"; //RUC 04 
    } else if (strlen($id_comprador) == 10) {
        $tipo_id_comprador = "05"; //CEDULA 05 
    } else if ($id_comprador == '9999999999999') {
        $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }
    $round = 2;
    $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);

    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<factura version='1.1.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoFactura>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    //$xml.="<contribuyenteEspecial>636</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
    $xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
    $xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
    $xml.="<totalSinImpuestos>" . round($rst_enc[fac_subtotal12] + $rst_enc[fac_subtotal0] + $rst_enc[fac_subtotal_ex_iva] + $rst_enc[fac_subtotal_no_iva], $round) . "</totalSinImpuestos>" . chr(13);
    $xml.="<totalDescuento>" . round($rst_enc[fac_total_descuento], $round) . "</totalDescuento>" . chr(13);
    $xml.="<totalConImpuestos>" . chr(13);
    ////******TODOS LOS IVA****************/////
    if ($rst_enc[fac_subtotal0] > 0) {//IVA 0
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        $xml.="<codigoPorcentaje>0</codigoPorcentaje>" . chr(13);
        $xml.="<baseImponible>" . round($rst_enc[fac_subtotal0], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>0.00</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst_enc[fac_subtotal12] > 0) {//IVA 12
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        $xml.="<codigoPorcentaje>2</codigoPorcentaje>" . chr(13);
        $xml.="<baseImponible>" . round($rst_enc[fac_subtotal12] + $rst_enc[fac_total_ice], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . round($rst_enc[fac_total_iva], $round) . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst_enc[fac_subtotal_no_iva] > 0) { //NO OBJ
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        $xml.="<codigoPorcentaje>6</codigoPorcentaje>" . chr(13);
        $xml.="<baseImponible>" . round($rst_enc[fac_subtotal_no_iva], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>0.00</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst_enc[fac_subtotal_ex_iva] > 0) { //EXC
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        $xml.="<codigoPorcentaje>7</codigoPorcentaje>" . chr(13);
        $xml.="<baseImponible>" . round($rst_enc[fac_subtotal_ex_iva], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>0.00</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }

////******TODOS LOS ICE****************/////  
    if ($rst_enc[fac_total_ice] > 0) {

        $cns_det_ice = $Set->lis_todos_ice($rst_enc[fac_id]); //Busco y agrupo todos los ICE por detalle de Factura

        while ($rst_det_ice = pg_fetch_array($cns_det_ice)) {
            $rst_ice = pg_fetch_array($Set->lista_ice_id($rst_det_ice[dfc_cod_ice])); //Busco el codigo del ICE por id del impuesto
            $rst_valores_ice = pg_fetch_array($Set->lista_valores_ice($rst_enc[fac_id], $rst_det_ice[dfc_cod_ice])); //Busco los valores que suman por cada ICE
            $xml.="<totalImpuesto>" . chr(13);
            $xml.="<codigo>3</codigo>" . chr(13);
            $xml.="<codigoPorcentaje>" . trim($rst_ice[por_codigo]) . "</codigoPorcentaje>" . chr(13);
            $xml.="<baseImponible>" . round($rst_det_ice[sum], $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . round($rst_valores_ice[sum], $round) . "</valor>" . chr(13);
            $xml.="</totalImpuesto>" . chr(13);
        }
    }


////******IRBPNR****************/////  
    if ($rst_enc[fac_total_irbpnr] > 0) {
        $rst_irbpnr = pg_fetch_array($Set->lista_irbpnr($rst_enc[fac_id]));
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>5</codigo>" . chr(13);
        $xml.="<codigoPorcentaje>5001</codigoPorcentaje>" . chr(13);
        $xml.="<baseImponible>" . round($rst_irbpnr[sum], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . round($rst_enc[fac_total_irbpnr], $round) . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }


    $xml.="</totalConImpuestos>" . chr(13);
    $xml.="<propina>0.00</propina>" . chr(13);
    $xml.="<importeTotal>" . round($rst_enc[fac_total_valor], $round) . "</importeTotal>" . chr(13);
    $xml.="<moneda>DOLAR</moneda>" . chr(13);
    $xml.="<pagos>" . chr(13);
    $cns_pg = $Set->lista_pagos_factura($id);
    while ($rst_pg = pg_fetch_array($cns_pg)) {
        if ($rst_pg[pag_forma] == 2) {
            $fp = '16';
        } else if ($rst_pg[pag_forma] == 1) {
            $fp = '19';
        } else if ($rst_pg[pag_forma] == 3 || $rst_pg[pag_forma] == 6) {
            $fp = '20';
        } else {
            $fp = '01';
        }

        $xml.="<pago>" . chr(13);
        $xml.="<formaPago>" . $fp . "</formaPago>" . chr(13);
        $xml.="<total>" . round($rst_pg[pag_cant], $round) . "</total>" . chr(13);
        $xml.="</pago>" . chr(13);
    }

    $xml.="</pagos>" . chr(13);
    $xml.="</infoFactura>" . chr(13);

    $xml.="<detalles>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det2)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoPrincipal>" . trim($reg_detalle[dfc_codigo]) . "</codigoPrincipal>" . chr(13);
        if (strlen(trim($reg_detalle[dfc_cod_aux])) == 0) {
            $reg_detalle[dfc_cod_aux] = $reg_detalle[dfc_codigo];
        }
        $xml.="<codigoAuxiliar>" . trim($reg_detalle[dfc_cod_aux]) . "</codigoAuxiliar>" . chr(13);

        $xml.="<descripcion>" . trim(string($reg_detalle[dfc_descripcion])) . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle[dfc_cantidad], $round) . "</cantidad>" . chr(13);
        $xml.="<precioUnitario>" . round($reg_detalle[dfc_precio_unit], 4) . "</precioUnitario>" . chr(13);
        $xml.="<descuento>" . round($reg_detalle[dfc_val_descuento], $round) . "</descuento>" . chr(13);
        $xml.="<precioTotalSinImpuesto>" . round($reg_detalle[dfc_precio_total], $round) . "</precioTotalSinImpuesto>" . chr(13);
        $xml.="<impuestos>" . chr(13);

        $xml.="<impuesto>" . chr(13);

        $xml.="<codigo>2</codigo>" . chr(13);
        if ($reg_detalle[dfc_iva] == '12') {
            $tarifa = 12;
            $codPorc = 2;
            $valo_iva = round($reg_detalle[dfc_precio_total] + $reg_detalle[dfc_ice] * 12 / 100, $round);
        }

        if ($reg_detalle[dfc_iva] == '0') {
            $tarifa = 0;
            $codPorc = 0;
            $valo_iva = 0.00;
        }
        if ($reg_detalle[dfc_iva] == 'NO') {
            $tarifa = 0;
            $codPorc = 6;
            $valo_iva = 0.00;
        }
        if ($reg_detalle[dfc_iva] == 'EX') {
            $tarifa = 0;
            $codPorc = 7;
            $valo_iva = 0.00;
        }
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13);
        $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle[dfc_precio_total] + $reg_detalle[dfc_ice], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</impuesto>" . chr(13);
        if ($reg_detalle[dfc_ice] > 0) {
            $ice = pg_fetch_array($Set->lista_ice_id($reg_detalle[dfc_cod_ice])); //
            $xml.="<impuesto>" . chr(13);
            $xml.="<codigo>3</codigo>" . chr(13);
            $xml.="<codigoPorcentaje>" . trim($ice[por_codigo]) . "</codigoPorcentaje>" . chr(13);
            $xml.="<tarifa>" . round($reg_detalle[dfc_p_ice], $round) . "</tarifa>" . chr(13);
            $xml.="<baseImponible>" . round($reg_detalle[dfc_precio_total], $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . round($reg_detalle[dfc_ice], $round) . "</valor>" . chr(13);
            $xml.="</impuesto>" . chr(13);
        }

        if ($reg_detalle[dfc_irbpnr] > 0) {
            $xml.="<impuesto>" . chr(13);
            $xml.="<codigo>5</codigo>" . chr(13);
            $xml.="<codigoPorcentaje>5001</codigoPorcentaje>" . chr(13);
            $xml.="<tarifa> 0.02</tarifa>" . chr(13);
            $xml.="<baseImponible>" . round($reg_detalle[dfc_precio_total], $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . round($reg_detalle[dfc_irbpnr], $round) . "</valor>" . chr(13);
            $xml.="</impuesto>" . chr(13);
        }

        $xml.="</impuestos>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);

    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . string($dir_cliente) . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(string($email_cliente)) . "</campoAdicional>" . chr(13);
    if ($rst_enc[observaciones] != '') {
        $xml.="<campoAdicional nombre='Observaciones'>" . strtoupper(string($rst_enc[fac_observaciones])) . "</campoAdicional>" . chr(13);
    }
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</factura>" . chr(13);
    return $clave . '&' . $xml;
}

function nota_credito($id, $ambiente, $codigo, $tp_emison) {
    include_once "../Clases/clsClase_nota.php";
    set_time_limit(0);
    $Set = new Clase_nota_Credito();
    $rst = pg_fetch_array($Set->lista_una_nota_credito_id($id));
    $cns_det = $Set->lista_detalle_nota_credito($id);
    $cns_det2 = $Set->lista_detalle_nota_credito($id);
    $rst_emi = pg_fetch_array($Set->lista_emisor($rst[emi_id]));
    $cod_doc = '04'; //Factura
    if ($rst_emi[emi_cod_establecimiento_emisor] > 0 && $rst_emi[emi_cod_establecimiento_emisor] < 10) {
        $txem = '00';
    } elseif ($rst_emi[emi_cod_establecimiento_emisor] >= 10 && $rst_emi[emi_cod_establecimiento_emisor] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($rst_emi[emi_cod_punto_emision] > 0 && $rst_emi[emi_cod_punto_emision] < 10) {
        $txpe = '00';
    } elseif ($rst_emi[emi_cod_punto_emision] >= 10 && $rst_emi[emi_cod_punto_emision] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $rst_emi[emi_cod_establecimiento_emisor];
    $pt_ems = $txpe . $rst_emi[emi_cod_punto_emision];

    $fecha = date_format(date_create($rst[nrc_fecha_emision]), 'd/m/Y');

    $ndoc = explode('-', $rst[ncr_numero]);
    $secuencial = $ndoc[2];
    $dir_cliente = string($rst[ncr_direccion]);
    $telf_cliente = string($rst[nrc_telefono]);
    $email_cliente = string($rst[ncr_email]);
    $direccion = string($rst_emi[emi_dir_establecimiento_emisor]);
    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = string($rst[ncr_nombre]);
    $id_comprador = $rst[nrc_identificacion];
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
    $clave1 = trim(str_replace('/', '', $fecha) . $cod_doc . $rst_emi[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim(str_replace('/', '', $fecha) . $cod_doc . $rst_emi[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<notaCredito version='1.1.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . string($rst_emi[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . string($rst_emi[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($rst_emi[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . substr($rst[ncr_numero], -9) . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . string($rst_emi[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);

//ENCABEZADO
    $xml.="<infoNotaCredito>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
    $xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
    $xml.="<identificacionComprador>" . $rst[ncr_identificacion] . "</identificacionComprador>" . chr(13);
//    $xml.="<contribuyenteEspecial>" . $rst_emi[emi_contribuyente_especial] . "</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $rst_emi[emi_obligado_llevar_contabilidad] . "</obligadoContabilidad>" . chr(13);
    $xml.="<codDocModificado>0" . $rst[ncr_denominacion_comprobante] . "</codDocModificado>" . chr(13);
    $xml.="<numDocModificado>" . $rst[ncr_num_comp_modifica] . "</numDocModificado>" . chr(13);
    $xml.="<fechaEmisionDocSustento>" . date_format(date_create($rst[ncr_fecha_emi_comp]), 'd/m/Y') . "</fechaEmisionDocSustento>" . chr(13);
    $xml.="<totalSinImpuestos>" . round($rst[ncr_subtotal], $round) . "</totalSinImpuestos>" . chr(13);
    $xml.="<valorModificacion>" . round($rst[nrc_total_valor], $round) . "</valorModificacion>" . chr(13);
    $xml.="<moneda>DOLAR</moneda>" . chr(13);
    $xml.="<totalConImpuestos>" . chr(13);

    $base = 0;

    if ($rst[ncr_subtotal12] != 0) {
        $codPorc = 2;
        $base = $rst[ncr_subtotal12];
        $valo_iva = round($base * 12 / 100, $round);
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst[ncr_subtotal0] != 0) {
        $codPorc = 0;
        $base = $rst[ncr_subtotal0];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst[ncr_subtotal_no_iva] != 0) {
        $codPorc = 6;
        $base = $rst[ncr_subtotal_no_iva];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    if ($rst[ncr_subtotal_ex_iva] != 0) {
        $codPorc = 7;
        $base = $rst[ncr_subtotal_ex_iva];
        $valo_iva = 0;
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }

////////////consultar///////////////////
    $grup = '';
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        if ($reg_detalle[dnc_ice] != 0 && $grup != $reg_detalle[dnc_cod_ice]) {
            $rst_sum = pg_fetch_array($Set->suma_ice($id, $reg_detalle[dnc_cod_ice]));
            $rst_im = pg_fetch_array($Set->lista_un_impuesto($reg_detalle[dnc_cod_ice]));
            $base_ice = $rst_sum[sum];
            $codPrc = $rst_im[imp_codigo];
            $val_ice = round($base_ice * $reg_detalle[dnc_p_ice] / 100, $round);
            $xml.="<totalImpuesto>" . chr(13);
            $xml.="<codigo>3</codigo>" . chr(13); //Tipo de Impuesto
            $xml.="<codigoPorcentaje>" . trim($codPrc) . "</codigoPorcentaje>" . chr(13); //Codigo del
            $xml.="<baseImponible>" . round($base_ice, $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . $val_ice . "</valor>" . chr(13);
            $xml.="</totalImpuesto>" . chr(13);
        }
        if ($reg_detalle[dnc_irbpnr] != 0) {
            $base_irbp = $base_irbp + $reg_detalle[dnc_precio_total];
        }
        $grup = $reg_detalle[dnc_cod_ice];
        $base_ice = 0;
    }

//////////////////////////////////////////////////
    if ($rst[ncr_irbpnr] != 0) {
        $codPorc = 5001;
        $valor_irb = round($base_irbp * 0.02, $round);
        $xml.="<totalImpuesto>" . chr(13);
        $xml.="<codigo>5</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($base_irbp, $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valor_irb . "</valor>" . chr(13);
        $xml.="</totalImpuesto>" . chr(13);
    }
    $xml.="</totalConImpuestos>" . chr(13);
    $xml.="<motivo>" . $rst[ncr_motivo] . "</motivo>" . chr(13);
    $xml.="</infoNotaCredito>" . chr(13);
    $xml.="<detalles>" . chr(13);
    while ($reg_detalle1 = pg_fetch_array($cns_det2)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoInterno>" . trim($reg_detalle1[dnc_codigo]) . "</codigoInterno >" . chr(13);
        if ($reg_detalle1["dnc_cod_aux"] != '') {
            $xml.="<codigoAdicional>" . trim($reg_detalle1["dnc_cod_aux"]) . "</codigoAdicional>" . chr(13);
        }
        $xml.="<descripcion>" . trim($reg_detalle1["dnc_descripcion"]) . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle1["dnc_cantidad"], $round) . "</cantidad>" . chr(13);
        $xml.="<precioUnitario>" . round($reg_detalle1["dnc_precio_unit"], $round) . "</precioUnitario>" . chr(13);
        $xml.="<descuento>" . round($reg_detalle1["dnc_val_descuento"], $round) . "</descuento>" . chr(13);
        $xml.="<precioTotalSinImpuesto>" . round($reg_detalle1["dnc_precio_total"], $round) . "</precioTotalSinImpuesto>" . chr(13);
        $xml.="<impuestos>" . chr(13);
        if ($reg_detalle1[dnc_ice] != 0) {
            $rst_imp = pg_fetch_array($Set->lista_un_impuesto($reg_detalle1[dnc_cod_ice]));
            $codP = $rst_imp[imp_codigo];
            $tarifa = $rst_imp[imp_porcentage];
            $xml.="<impuesto>" . chr(13);
            $xml.="<codigo>3</codigo>" . chr(13);
            $xml.="<codigoPorcentaje>" . trim($codP) . "</codigoPorcentaje>" . chr(13);
            $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
            $xml.="<baseImponible>" . round($reg_detalle1["dnc_precio_total"], $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . round($reg_detalle1["dnc_ice"], $round) . "</valor>" . chr(13);
            $xml.="</impuesto>" . chr(13);
        }
        if ($reg_detalle1[dnc_irbpnr] != 0) {
            $tarifa = '0.02' . chr(13);
            $xml.="<impuesto>" . chr(13);
            $xml.="<codigo>5</codigo>" . chr(13);
            $xml.="<codigoPorcentaje>5001</codigoPorcentaje>" . chr(13);
            $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
            $xml.="<baseImponible>" . round($reg_detalle1["dnc_precio_total"], $round) . "</baseImponible>" . chr(13);
            $xml.="<valor>" . round($reg_detalle1[dnc_irbpnr], $round) . "</valor>" . chr(13);
            $xml.="</impuesto>" . chr(13);
        }

        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>2</codigo>" . chr(13);
        if ($reg_detalle1[dnc_iva] == '12') {
            $codPorc = 2;
            $valo_iva = round($reg_detalle1["dnc_precio_total"] + $reg_detalle1["dnc_ice"] * 12 / 100, $round);
            $tarifa = 12;
        } else if ($reg_detalle1[dnc_iva] == '0') {
            $codPorc = 0;
            $valo_iva = 0.00;
            $tarifa = 0;
        } else if ($reg_detalle1[dnc_iva] == 'NO') {
            $codPorc = 6;
            $valo_iva = 0.00;
            $tarifa = 0;
        } else if ($reg_detalle1[dnc_iva] == 'EX') {
            $codPorc = 7;
            $valo_iva = 0.00;
            $tarifa = 0;
        }
        $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13);
        $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle1["dnc_precio_total"] + $reg_detalle1["dnc_ice"], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
        $xml.="</impuesto>" . chr(13);
        $xml.="</impuestos>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $rst[ncr_direccion] . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $rst[nrc_telefono] . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($rst[ncr_email])) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</notaCredito>" . chr(13);

    return $clave . '&' . $xml;
}

function nota_debito($id, $ambiente, $codigo, $tp_emison) {
    include_once "../Clases/clsClase_nota_debito.php";
    set_time_limit(0);
    $Set = new Clase_nota_debito();
    $rst_enc = pg_fetch_array($Set->lista_una_nota_debito_id($id));
    $emis = pg_fetch_array($Set->lista_emisor($rst_enc[emi_id]));

    if ($emis[emi_cod_establecimiento_emisor] > 0 && $emis[emi_cod_establecimiento_emisor] < 10) {
        $txem = '00';
    } elseif ($emis[emi_cod_establecimiento_emisor] >= 10 && $emis[emi_cod_establecimiento_emisor] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($emis[emi_cod_punto_emision] > 0 && $emis[emi_cod_punto_emision] < 10) {
        $txpe = '00';
    } elseif ($emis[emi_cod_punto_emision] >= 10 && $emis[emi_cod_punto_emision] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $emis[emi_cod_establecimiento_emisor];
    $pt_ems = $txpe . $emis[emi_cod_punto_emision];

    $fecha = date_format(date_create($rst_enc[ndb_fecha_emision]), 'd/m/Y');
    $f2 = date_format(date_create($rst_enc[ndb_fecha_emision]), 'dmY');
    $ndoc = explode('-', $rst_enc[ndb_numero]);
    $nfact = str_replace('-', '', $rst_enc[ndb_numero]);
    $secuencial = $ndoc[2];

    $cns_det = $Set->lista_detalle_nota($id);
    $cod_doc = "05"; //01= factura, 02=nota de credito tabla 4

    $dir_cliente = string($rst_enc[ndb_direccion]);
    $telf_cliente = string($rst_enc[ndb_telefono]);
    $email_cliente = string($rst_enc[ndb_email]);
    $direccion = string($emis[emi_dir_establecimiento_emisor]);

    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = string($rst_enc[ndb_nombre]);
    $id_comprador = $rst_enc[ndb_identificacion];
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
    $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<notaDebito version='1.0.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoNotaDebito>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<tipoIdentificacionComprador>" . $tipo_id_comprador . "</tipoIdentificacionComprador>" . chr(13);
    $xml.="<razonSocialComprador>" . $razon_soc_comprador . "</razonSocialComprador>" . chr(13);
    $xml.="<identificacionComprador>" . $id_comprador . "</identificacionComprador>" . chr(13);
//    $xml.="<contribuyenteEspecial>" . $emis[emi_contribuyente_especial] . "</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<codDocModificado>01</codDocModificado>" . chr(13);
    $xml.="<numDocModificado>$rst_enc[ndb_num_comp_modifica]</numDocModificado>" . chr(13);
    $fec_mod = date_format(date_create($rst_enc[ndb_fecha_emi_comp]), 'd/m/Y');
    $xml.="<fechaEmisionDocSustento>$fec_mod</fechaEmisionDocSustento>" . chr(13);
    $xml.="<totalSinImpuestos>" . round($rst_enc[ndb_subtotal12] + $rst_enc[ndb_subtotal0] + $rst_enc[ndb_subtotal_ex_iva] + $rst_enc[ndb_subtotal_no_iva], $round) . "</totalSinImpuestos>" . chr(13);
    $xml.="<impuestos>" . chr(13);

    if ($rst_enc[ndb_total_ice] != 0) {
        $imp = pg_fetch_array($Set->lista_impuestos($rst_enc[imp_id]));
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>3</codigo>" . chr(13); //Tipo de Impuesto
        $xml.="<codigoPorcentaje>" . trim($imp[imp_codigo]) . "</codigoPorcentaje>" . chr(13); //Codigo del
        $xml.="<tarifa>" . $imp[imp_porcentage] . "</tarifa>" . chr(13); //Codigo del
        $xml.="<baseImponible>" . round($rst_enc[ndb_subtotal12] + $rst_enc[ndb_subtotal0] + $rst_enc[ndb_subtotal_ex_iva] + $rst_enc[ndb_subtotal_no_iva], $round) . "</baseImponible>" . chr(13);
        $xml.="<valor>" . round($rst_enc[ndb_total_ice], $round) . "</valor>" . chr(13);
        $xml.="</impuesto>" . chr(13);
    }
    $base = 0;
    if ($rst_enc[ndb_subtotal12] != 0) {
        $codPorc = 2;
        $base = round($rst_enc[ndb_subtotal12] + $rst_enc[ndb_total_ice], $round);
        $valo_iva = round(($base * 12) / 100, $round);
        $tarifa = '12.00';
    } else if ($rst_enc[ndb_subtotal0] != 0) {
        $codPorc = 0;
        $base = round($rst_enc[ndb_subtotal0] + $rst_enc[ndb_total_ice], $round);
        $valo_iva = '0.00';
        $tarifa = '0.00';
    } else if ($rst_enc[ndb_subtotal_ex_iva] != 0) {
        $codPorc = 7;
        $base = round($rst_enc[ndb_subtotal_ex_iva] + $rst_enc[ndb_total_ice], $round);
        $valo_iva = '0.00';
        $tarifa = '0.00';
    } else if ($rst_enc[ndb_subtotal_no_iva] != 0) {
        $codPorc = 6;
        $base = round($rst_enc[ndb_subtotal_no_iva] + $rst_enc[ndb_total_ice], $round);
        $valo_iva = '0.00';
        $tarifa = '0.00';
    }
    $xml.="<impuesto>" . chr(13);
    $xml.="<codigo>2</codigo>" . chr(13); //Tipo de Impuesto
    $xml.="<codigoPorcentaje>" . $codPorc . "</codigoPorcentaje>" . chr(13); //Codigo del
    $xml.="<tarifa>" . $tarifa . "</tarifa>" . chr(13); //Codigo del
    $xml.="<baseImponible>" . $base . "</baseImponible>" . chr(13);
    $xml.="<valor>" . $valo_iva . "</valor>" . chr(13);
    $xml.="</impuesto>" . chr(13);


    $xml.="</impuestos>" . chr(13);
    $xml.="<valorTotal>" . round($rst_enc[ndb_total_valor], $round) . "</valorTotal>" . chr(13);
    $xml.="</infoNotaDebito>" . chr(13);
    $xml.="<motivos>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        $xml.="<motivo>" . chr(13);
        $xml.="<razon>" . trim($reg_detalle[dnd_descripcion]) . "</razon>" . chr(13);
        $xml.="<valor>" . round($reg_detalle[dnd_precio_total], $round) . "</valor>" . chr(13);
        $xml.="</motivo>" . chr(13);
    }
    $xml.="</motivos>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</notaDebito>" . chr(13);
    return $clave . '&' . $xml;
}

function guia_remision($id, $ambiente, $codigo, $tp_emison) {
    echo $id;
    include_once "../Clases/clsClase_guia_remision.php";
    $Set = new Clase_guia_remision();
    set_time_limit(0);
    $rst_enc = pg_fetch_array($Set->lista_una_guia_id($id));
    $cns_det = $Set->lista_detalle_guia($id);
    $emis = pg_fetch_array($Set->lista_emisor($rst_enc[emi_id]));

    if ($emis[emi_cod_establecimiento_emisor] > 0 && $emis[emi_cod_establecimiento_emisor] < 10) {
        $txem = '00';
    } elseif ($emis[emi_cod_establecimiento_emisor] >= 10 && $emis[emi_cod_establecimiento_emisor] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($emis[emi_cod_punto_emision] > 0 && $emis[emi_cod_punto_emision] < 10) {
        $txpe = '00';
    } elseif ($emis[emi_cod_punto_emision] >= 10 && $emis[emi_cod_punto_emision] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $emis[emi_cod_establecimiento_emisor];
    $pt_ems = $txpe . $emis[emi_cod_punto_emision];

    $fecha = date_format(date_create($rst_enc[gui_fecha_emision]), 'd/m/Y');

    $ndoc = explode('-', $rst_enc[gui_numero]);
    $secuencial = $ndoc[2];
    $cod_doc = "06"; //01= factura, 02=nota de credito tabla 4
    $f2 = date_format(date_create($rst_enc[gui_fecha_emision]), 'dmY');

    $dir_cliente = string($rst_enc[cli_calle_prin]);
    $telf_cliente = string($rst_enc[cli_telefono]);
    $email_cliente = string($rst_enc[cli_email]);
    $direccion = string($emis[emi_dir_establecimiento_emisor]);
    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = string($rst_enc[gui_nombre]);
    $id_comprador = $rst_enc[gui_identificacion];
    if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
        $tipo_id_comprador = "04"; //RUC 04 
    } else if (strlen($id_comprador) == 10) {
        $tipo_id_comprador = "05"; //CEDULA 05 
    } else if ($id_comprador == '9999999999999') {
        $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }
    $id_trans = $rst_enc[gui_identificacion_transp];
    if (strlen($id_trans) == 13 && $id_trans != '9999999999999') {
        $tipo_id_trans = "04"; //RUC 04 
    } else if (strlen($id_trans) == 10) {
        $tipo_id_trans = "05"; //CEDULA 05 
    } else if ($id_trans == '9999999999999') {
        $tipo_id_trans = "07"; //VENTA A CONSUMIDOR FINAL
    } else {
        $tipo_id_trans = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
    }
    $round = 2;
    $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<guiaRemision version='1.1.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoGuiaRemision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    $xml.="<dirPartida>" . string($rst_enc[gui_punto_partida]) . "</dirPartida>" . chr(13);
    $xml.="<razonSocialTransportista>" . string($rst_enc[tra_razon_social]) . "</razonSocialTransportista>" . chr(13);
    $xml.="<tipoIdentificacionTransportista>" . $tipo_id_trans . "</tipoIdentificacionTransportista>" . chr(13);
    $xml.="<rucTransportista>" . $rst_enc[tra_identificacion] . "</rucTransportista>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    //$xml.="<contribuyenteEspecial>" . $emis[emi_contribuyente_especial] . "</contribuyenteEspecial>" . chr(13);
    $f_ini = date_format(date_create($rst_enc[gui_fecha_inicio]), 'd/m/Y');
    $f_fin = date_format(date_create($rst_enc[gui_fecha_fin]), 'd/m/Y');
    $xml.="<fechaIniTransporte>$f_ini</fechaIniTransporte>" . chr(13);
    $xml.="<fechaFinTransporte>$f_fin</fechaFinTransporte>" . chr(13);
    $xml.="<placa>$rst_enc[tra_placa]</placa>" . chr(13);
    $xml.="</infoGuiaRemision>" . chr(13);

    $xml.="<destinatarios>" . chr(13);
    $xml.="<destinatario>" . chr(13);
    $xml.="<identificacionDestinatario>" . $id_comprador . "</identificacionDestinatario>" . chr(13);
    $xml.="<razonSocialDestinatario>" . $razon_soc_comprador . "</razonSocialDestinatario>" . chr(13);
    $xml.="<dirDestinatario>" . $dir_cliente . "</dirDestinatario>" . chr(13);
    $xml.="<motivoTraslado>" . $rst_enc[gui_motivo_traslado] . "</motivoTraslado>" . chr(13);
    if ($rst_enc[gui_doc_aduanero] != '') {
        $xml.="<docAduaneroUnico>" . $rst_enc[gui_doc_aduanero] . "</docAduaneroUnico>" . chr(13);
    }
    $xml.="<codEstabDestino>" . $rst_enc[gui_cod_establecimiento] . "</codEstabDestino>" . chr(13);
    $xml.="<codDocSustento>0" . $rst_enc[gui_denominacion_comp] . "</codDocSustento>" . chr(13);
    $xml.="<numDocSustento>" . $rst_enc[gui_num_comprobante] . "</numDocSustento>" . chr(13);
    if ($rst_enc[gui_aut_comp] != '') {
        $xml.="<numAutDocSustento>" . $rst_enc[gui_aut_comp] . "</numAutDocSustento>" . chr(13);
    }
    $fec_comp = date_format(date_create($rst_enc[gui_fecha_comp]), 'd/m/Y');
    $xml.="<fechaEmisionDocSustento>" . $fec_comp . "</fechaEmisionDocSustento>" . chr(13);
    $xml.="<detalles>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        $xml.="<detalle>" . chr(13);
        $xml.="<codigoInterno>" . $reg_detalle[dtg_codigo] . "</codigoInterno>" . chr(13);
        if ($reg_detalle[dtg_cod_aux] != 0 && $reg_detalle[dtg_cod_aux] != '') {
            $xml.="<codigoAdicional>" . $reg_detalle[dtg_cod_aux] . "</codigoAdicional>" . chr(13);
        }
        $xml.="<descripcion>" . $reg_detalle[dtg_descripcion] . "</descripcion>" . chr(13);
        $xml.="<cantidad>" . round($reg_detalle[dtg_cantidad], $round) . "</cantidad>" . chr(13);
        $xml.="</detalle>" . chr(13);
    }
    $xml.="</detalles>" . chr(13);
    $xml.="</destinatario>" . chr(13);
    $xml.="</destinatarios>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</guiaRemision>" . chr(13);
    return $clave . '&' . $xml;
}

function retencion($id, $ambiente, $codigo, $tp_emison) {
    include_once "../Clases/clsClase_retencion.php";
    set_time_limit(0);
    $Set = new Clase_retencion();
    $rst_enc = pg_fetch_array($Set->lista_retencion_id($id));
    $cns_det = $Set->lista_detalle_retencion($id);
    $ejer = pg_fetch_array($Set->lista_detalle_retencion($id));
    $emis = pg_fetch_array($Set->lista_emisor($rst_enc[emi_id]));
    if ($emis[emi_cod_establecimiento_emisor] > 0 && $emis[emi_cod_establecimiento_emisor] < 10) {
        $txem = '00';
    } elseif ($emis[emi_cod_establecimiento_emisor] >= 10 && $emis[emi_cod_establecimiento_emisor] < 100) {
        $txem = '0';
    } else {
        $txem = '';
    }
    if ($emis[emi_cod_punto_emision] > 0 && $emis[emi_cod_punto_emision] < 10) {
        $txpe = '00';
    } elseif ($emis[emi_cod_punto_emision] >= 10 && $emis[emi_cod_punto_emision] < 100) {
        $txpe = '0';
    } else {
        $txpe = '';
    }
    $ems = $txem . $emis[emi_cod_establecimiento_emisor];
    $pt_ems = $txpe . $emis[emi_cod_punto_emision];

    $fecha = date_format(date_create($rst_enc[ret_fecha_emision]), 'd/m/Y');

    $ndoc = explode('-', $rst_enc[ret_numero]);
    $secuencial = $ndoc[2];

    $cod_doc = "07"; //01= factura, 02=nota de credito tabla 4


    $f2 = date_format(date_create($rst_enc[ret_fecha_emision]), 'dmY');
    $dir_cliente = string($rst_enc[ret_direccion]);
    $telf_cliente = string($rst_enc[ret_telefono]);
    $email_cliente = string($rst_enc[ret_email]);
    $direccion = string($emis[emi_dir_establecimiento_emisor]);
    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = string($rst_enc[ret_nombre]);
    $id_comprador = $rst_enc[ret_identificacion];
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
    $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
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
    $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
    $xml.="<?xml version='1.0' encoding='UTF-8'?>" . chr(13);
    $xml.="<comprobanteRetencion version='1.0.0' id='comprobante'>" . chr(13);
    $xml.="<infoTributaria>" . chr(13);
    $xml.="<ambiente>" . $ambiente . "</ambiente>" . chr(13);
    $xml.="<tipoEmision>" . $tp_emison . "</tipoEmision>" . chr(13);
    $xml.="<razonSocial>" . string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoCompRetencion>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
//    $xml.="<contribuyenteEspecial>" . $emis[emi_contribuyente_especial] . "</contribuyenteEspecial>" . chr(13);
    $xml.="<obligadoContabilidad>" . $contabilidad . "</obligadoContabilidad>" . chr(13);
    $xml.="<tipoIdentificacionSujetoRetenido>" . $tipo_id_comprador . "</tipoIdentificacionSujetoRetenido>" . chr(13);
    $xml.="<razonSocialSujetoRetenido>" . $razon_soc_comprador . "</razonSocialSujetoRetenido>" . chr(13);
    $xml.="<identificacionSujetoRetenido>" . $id_comprador . "</identificacionSujetoRetenido>" . chr(13);
    $xml.="<periodoFiscal>" . $ejer[dtr_ejercicio_fiscal] . "</periodoFiscal>" . chr(13);
    $xml.="</infoCompRetencion>" . chr(13);

    $xml.="<impuestos>" . chr(13);
    while ($reg_detalle = pg_fetch_array($cns_det)) {
        if ($reg_detalle[dtr_tipo_impuesto] == 'IR') {
            $impuesto = '1';
        } else if ($reg_detalle[dtr_tipo_impuesto] == 'IV') {
            $impuesto = '2';
        } else {
            $impuesto = '3';
        }
        $xml.="<impuesto>" . chr(13);
        $xml.="<codigo>" . $impuesto . "</codigo>" . chr(13);
        $xml.="<codigoRetencion>" . $reg_detalle[dtr_codigo_impuesto] . "</codigoRetencion>" . chr(13);
        $xml.="<baseImponible>" . round($reg_detalle[dtr_base_imponible], $round) . "</baseImponible>" . chr(13);
        $xml.="<porcentajeRetener>" . round($reg_detalle[dtr_procentaje_retencion], $round) . "</porcentajeRetener>" . chr(13);
        $xml.="<valorRetenido>" . round($reg_detalle[dtr_valor], $round) . "</valorRetenido>" . chr(13);
        $xml.="<codDocSustento>0" . $rst_enc[ret_denominacion_comp] . "</codDocSustento>" . chr(13);
        $xml.="<numDocSustento>" . str_replace('-', '', $rst_enc[ret_num_comp_retiene]) . "</numDocSustento>" . chr(13);
        $xml.="<fechaEmisionDocSustento>" . $fecha . "</fechaEmisionDocSustento>" . chr(13);
        $xml.="</impuesto>" . chr(13);
    }
    $xml.="</impuestos>" . chr(13);
    $xml.="<infoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Direccion'>" . $dir_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower(utf8_decode($email_cliente)) . "</campoAdicional>" . chr(13);
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</comprobanteRetencion>" . chr(13);

    return $clave . '&' . $xml;
}

?>