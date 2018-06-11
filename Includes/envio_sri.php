<?php
set_time_limit(0);
include_once '../Includes/nusoap.php';
include_once '../Clases/Conn.php';
date_default_timezone_set('America/Guayaquil');

class SRI {

    var $con;

    function SRI() {
        $this->con = new Conn();
    }


    function recupera_datos($clave, $amb) {
        if ($amb == 2) { //Produccion
            $wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
            //$wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        } else {      //Pruebas
            $wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl', 'wsdl');
            //$wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        }

        $res = $wsdl->call('autorizacionComprobante', array("claveAccesoComprobante" => $clave));
        $req = $res[RespuestaAutorizacionComprobante][autorizaciones][autorizacion];
        if ($wsdl->fault) {
            $respuesta = array_merge(array('err'), ($res));
        } else {
            $err = $wsdl->getError();
            if ($err) {
                $respuesta = $err;
            } else {
                $respuesta = array($req[estado], $req[numeroAutorizacion], $req[fechaAutorizacion], $req[ambiente], $req[comprobante], $req[mensajes][mensaje][mensaje]);
            }
        }
        return $respuesta;
    }

    function envio_sri($clave, $amb) {
        if ($amb == 2) { //Produccion
            $wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl', 'wsdl');
            //$wsdl = new nusoap_client('https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        } else {      //Pruebas
            $wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl', 'wsdl');
            //$wsdl = new nusoap_client('https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantes?wsdl', 'wsdl');
        }
        $xml = "../xml_docs/$clave.xml";
        $fp = fopen($xml, "r");
        $contents = fread($fp, filesize($xml));
        fclose($fp);
        $dat = base64_encode($contents);
        $res = $wsdl->call('validarComprobante', array("xml" => $dat));
        $req = $res[RespuestaRecepcionComprobante];
        if ($wsdl->fault) {
            $respuesta = array_merge(array('err'), ($res));
        } else {
            $err = $wsdl->getError();
            if ($err) {
                $respuesta = $err;
            } else {
                $respuesta = array($req[estado]);
            }
        }

        if ($respuesta[0] == 'RECIBIDA') {
            return 0;
        } ELSE {
            return $req;
        }
    }

    function documentos_noenviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_factura where ((char_length(fac_autorizacion)<>37 and char_length(trim(fac_autorizacion))<>49) or fac_autorizacion is null) and emi_id=1 limit 1");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_factura 
                SET fac_estado_aut='RECIBIDA $estado',
                    fac_autorizacion='$auto',
                    fac_fec_hora_aut='$fecha',
                    fac_xml_doc='$xml'    
                WHERE fac_id=$id ");
        }
    }

    function registra_errores($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO 
                erp_auditoria(
                usu_id,
                adt_date,
                adt_hour,
                adt_modulo,
                adt_accion,
                adt_documento,
                adt_campo,
                usu_login
                )VALUES(
                '$data[0]',
                '$data[1]',
                '$data[2]',
                '$data[3]',
                '$data[4]',    
                '$data[5]',
                '$data[6]',
                '$data[7]' ) ");
        }
    }

    function upd_documentos($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura 
                set fac_clave_acceso='$dat[0]', 
                fac_estado_aut='$dat[1]', 
                fac_observacion_aut='$dat[2]', 
                fac_autorizacion='$dat[3]',
                fac_fec_hora_aut='$dat[4]',
                fac_xml_doc='$dat[5]'    
                where fac_id=$id ");
        }
    }
    
    function upd_clave($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura 
                set fac_clave_acceso='$dat'
                where fac_id=$id ");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_id=$id");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_id=$id ");
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id=$id");
        }
    }

    function lista_pagos_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura where com_id=$id");
        }
    }

    function sanear_string($string) {

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

    function lista_ambiente() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT con_valor FROM  erp_configuraciones where con_id=5");
        }
    }

    function lista_credenciales($crd) {
        if ($this->con->Conectar() == true) {
            return pg_fetch_array(pg_query("SELECT con_valor2 FROM  erp_configuraciones where con_id=13"));
        }
    }

    function lista_nombre_programa() {
        if ($this->con->Conectar() == true) {
            return pg_fetch_array(pg_query("SELECT con_valor2 FROM  erp_configuraciones where con_id=15"));
        }
    }

    function lis_todos_ice($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum((dfc_cantidad*dfc_precio_unit)-dfc_val_descuento),dfc_cod_ice from erp_det_factura where fac_id=$id and dfc_cod_ice <>0 group by dfc_cod_ice");
        }
    }

    function lista_ice_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from porcentages_retencion where por_id=$id");
        }
    }

    function lista_valores_ice($id_fact, $id_ice) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(cast(dfc_ice as double precision)) from erp_det_factura where fac_id=$id_fact and dfc_cod_ice=$id_ice");
        }
    }

    function lista_irbpnr($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum((dfc_precio_unit*dfc_cantidad)-dfc_val_descuento) from erp_det_factura where fac_id=$id and dfc_irbpnr>0");
        }
    }

    function lista_iva($id, $p) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dfc_precio_total) from erp_det_factura where dfc_iva='$p' and fac_id=$id");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=5");
        }
    }

}

$Sri = new SRI();
$rst_am = pg_fetch_array($Sri->lista_configuraciones());
$ambiente = $rst_am[con_valor]; //Pruebas 1    Produccion 2
//$ambiente = 1; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal

$doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813', $ambiente);
$pos = strpos($doc, 'HTTP ERROR'); //Verfifico Conxecion;
if ($ambiente == 0) {//Si el ambiente es Ninguno
    $pos = true;
}
if ($pos == false) {
    $cns = $Sri->documentos_noenviados();
    if (pg_num_rows($cns) > 0) {
        while ($rst = pg_fetch_array($cns)) {
            $rst_cred = explode('&', $Sri->lista_credenciales()[0]);
            $programa = $Sri->lista_nombre_programa()[0];
            $pass = $rst_cred[1];
            $firma = $rst_cred[2];
            $parametros = "<parametros>
                        <keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>
                        <keyStorePassword>changeit</keyStorePassword>
                        <ambiente>$ambiente</ambiente>
                        <pathFirma>/var/www/FacturacionElectronica/Scripts/archivos/$firma</pathFirma>
                        <passFirma>$pass</passFirma>
                        </parametros>";

            if (strlen($rst[fac_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[fac_clave_acceso], $ambiente);

                if (strlen($doc1[1]) == 37 || strlen($doc1[1]) == 49) { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst[fac_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 49) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst[fac_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 37 || strlen($doc1[1]) == 49) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[fac_id])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[fac_clave_acceso], '', 'SuperAdmin');
                            if (!$Sri->registra_errores($data)) {
                                echo pg_last_error();
                            }
                        }
                    }
                }
            }
        }
    }
}

function envio_electronico($id, $ambiente, $codigo, $tp_emison, $firma, $pass,$programa) {
//    $Adt = new Auditoria();
    $Sri = new SRI();
    $round = 2;
//    $Sri = new Clase_factura();
    $rst_enc = pg_fetch_array($Sri->lista_una_factura_id($id));
    $ndoc = explode('-', $rst_enc[fac_numero]);
    $nfact = str_replace('-', '', $rst_enc[fac_numero]);
    $ems = $ndoc[0];
    $emisor = intval($ndoc[0]);
    $pt_ems = $ndoc[1];
    $secuencial = $ndoc[2];
    $emis = pg_fetch_array($Sri->lista_emisor($emisor));
    $cns_det = $Sri->lista_detalle_factura($rst_enc[fac_id]);
    $cns_det2 = $Sri->lista_detalle_factura($rst_enc[fac_id]);
    $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
    $fecha = date_format(date_create($rst[fac_fecha_emision]), 'd/m/Y');
    $f2 = date_format(date_create($rst[fac_fecha_emision]), 'dmY');
    $dir_cliente = $Sri->sanear_string($rst_enc[fac_direccion]);
    $telf_cliente = $Sri->sanear_string($rst_enc[fac_telefono]);
    $email_cliente = $Sri->sanear_string($rst_enc[fac_email]);
    $direccion = $Sri->sanear_string($emis[emi_dir_establecimiento_emisor]);
    $contabilidad = "SI";
    $razon_soc_comprador = $Sri->sanear_string($rst_enc[fac_nombre]);
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
    $xml.="<razonSocial>" . $Sri->sanear_string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . $Sri->sanear_string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . $Sri->sanear_string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
    $xml.="</infoTributaria>" . chr(13);
//ENCABEZADO
    $xml.="<infoFactura>" . chr(13);
    $xml.="<fechaEmision>" . $fecha . "</fechaEmision>" . chr(13);
    $xml.="<dirEstablecimiento>" . $direccion . "</dirEstablecimiento>" . chr(13);
    if (!empty($emis[emi_contribuyente_especial])) {
        $xml.="<contribuyenteEspecial>$emis[emi_contribuyente_especial]</contribuyenteEspecial>" . chr(13);
    }

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

        $cns_det_ice = $Sri->lis_todos_ice($rst_enc[fac_id]); //Busco y agrupo todos los ICE por detalle de Factura

        while ($rst_det_ice = pg_fetch_array($cns_det_ice)) {
            $rst_ice = pg_fetch_array($Sri->lista_ice_id($rst_det_ice[dfc_cod_ice])); //Busco el codigo del ICE por id del impuesto
            $rst_valores_ice = pg_fetch_array($Sri->lista_valores_ice($rst_enc[fac_id], $rst_det_ice[dfc_cod_ice])); //Busco los valores que suman por cada ICE
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
        $rst_irbpnr = pg_fetch_array($Sri->lista_irbpnr($rst_enc[fac_id]));
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
    $cns_pg = $Sri->lista_pagos_factura($id);
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

        $xml.="<descripcion>" . trim($Sri->sanear_string($reg_detalle[dfc_descripcion])) . "</descripcion>" . chr(13);
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
            $ice = pg_fetch_array($Sri->lista_ice_id($reg_detalle[dfc_cod_ice])); //
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
    $xml.="<campoAdicional nombre='Direccion'>" . $Sri->sanear_string($dir_cliente) . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Telefono'>" . $telf_cliente . "</campoAdicional>" . chr(13);
    $xml.="<campoAdicional nombre='Email'>" . strtolower($Sri->sanear_string($email_cliente)) . "</campoAdicional>" . chr(13);
    if ($rst_enc[observaciones] != '') {
        $xml.="<campoAdicional nombre='Observaciones'>" . strtoupper($Sri->sanear_string($rst_enc[fac_observaciones])) . "</campoAdicional>" . chr(13);
    }
    $xml.="</infoAdicional>" . chr(13);
    $xml.="</factura>" . chr(13);

    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);
    if (!$Sri->upd_clave($clave, $id)) {
        $sms = 'clave_acceso' . pg_last_error();
    }
    ///envio para firmar
    header("Location: http://186.4.200.125:90/central_xml/envio_sri/firmar.php?clave=$clave&programa=$programa&firma=$firma&password=$pass&ambiente=$ambiente");

//////firmar xml
////    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
//    $path_xml = "/var/www/html/neplast_co_pruebas/xml_docs/" . $clave . ".xml";
//    echo $comando = 'java -jar http://186.4.200.125:90/central_xml/FacturacionElectronica/sri_firma_xml.jar ' . htmlentities($pathSignature, ENT_QUOTES, "UTF-8") . ' ' . htmlentities($passSignature, ENT_QUOTES, "UTF-8") . ' ' . htmlentities($path_xml, ENT_QUOTES, "UTF-8") . ' ' . htmlentities($pathOut, ENT_QUOTES, "UTF-8") . ' ' . htmlentities($clave . '.xml', ENT_QUOTES, "UTF-8");
//    $dat = shell_exec($comando);
////    print_r($dat);
////    if ($dat == 0) {
////        echo $resp = $Sri->envio_sri($clave, $ambiente);
////        if ($resp == 0) {
////            $doc1 = $Sri->recupera_datos($clave, $ambiente);
////            if (strlen($doc1[1]) == 37 || strlen(trim($doc1[1])) == 49) { //Si recupera los datos
////                $data = array(
////                    $clave, //fac_clave_acceso
////                    'RECIBIDA AUTORIZADO', //fac_estado_aut
////                    '', //fac_observacion_aut
////                    trim($doc1[1]), //fac_autorizacion
////                    $doc1[2], //fac_fec_hora_aut
////                    '', //fac_xml_doc
////                );
////                if (!$Sri->upd_documentos($data, $id)) {
////                    $data = array(1, date('Y-m-d'), date('H:i'), 'Envio Electronico Datos', 'Error', $clave, '', 'SuperAdmin');
////                    if (!$Sri->registra_errores($data)) {
////                        echo pg_last_error();
////                    }
////                } else {
////                    $sms = 0;
////                }
////            }
////        } else {
////            $sms = 'envio fallido ' . print_r($resp);
////        }
////    }
}
