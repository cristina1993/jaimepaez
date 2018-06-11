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

    function documentos_noenviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion where (((char_length(ret_autorizacion)<>37 and char_length(ret_autorizacion)<>49) or ret_autorizacion is null) and ret_numero<>'') ");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_retencion
                SET ret_estado_aut='RECIBIDA $estado',
                    ret_autorizacion='$auto',
                    ret_fec_hora_aut='$fecha',
                    ret_xml_doc='$xml'    
                WHERE ret_id='$id' ");
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
            return pg_query("update erp_retencion 
                set ret_clave_acceso='$dat[0]', 
                ret_estado_aut='$dat[1] $dat[2]', 
                ret_observacion_aut='$dat[3]', 
                ret_autorizacion='$dat[4]',
                ret_fec_hora_aut='$dat[5]',
                ret_xml_doc='$dat[6]'    
                where ret_id='$id' ");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=5");
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

    function upd_clave($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_retencion
                set ret_clave_acceso='$dat'
                where ret_id=$id ");
        }
    }


    function lista_emisor($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_id=$cod ");
        }
    }

    function lista_emisor_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_identificacion='$ruc' ");
        }
    }

    ///////////////////retencion
    function lista_una_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_id=$id");
        }
    }

    function lista_detalle_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_retencion where ret_id=$id");
        }
    }

}

/////////*************CLASE AUDITORIA*****************************

class Auditoria {

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

}

////////////////////////EJECUCION FUNCIONES Y CLASES///////////////////
$Sri = new SRI();
$rst_am = pg_fetch_array($Sri->lista_configuraciones());
$ambiente = $rst_am[con_valor]; //Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal

$doc = $Sri->recupera_datos('0605201501179000787100120010010000011161234567813', $ambiente);
$pos = strpos($doc, 'HTTP ERROR'); //Verfifico Conxecion;
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

            if (strlen($rst[ret_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[ret_clave_acceso], $ambiente);
                if (strlen($doc1[1]) == 37 || strlen($doc1[1]) == 49) { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ret_clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst[ret_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 49) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[clave_acceso], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst[ret_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 49) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ret_id])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ret_clave_acceso], '', 'SuperAdmin');
                            if (!$Sri->registra_errores($data)) {
                                echo pg_last_error();
                            }
                        }
                    }
                }
            }
        }
    }
//    else {
//        echo "no hay items";
//    }
}

function envio_electronico($id, $ambiente, $codigo, $tp_emison, $firma, $pass,$programa) {
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst_enc = pg_fetch_array($Sri->lista_una_retencion($id));
    $cns_det = $Sri->lista_detalle_retencion($id);
    $ejer = pg_fetch_array($Sri->lista_detalle_retencion($id));
    $emis = pg_fetch_array($Sri->lista_emisor($rst_enc[emi_id]));
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
    $dir_cliente = $Adt->sanear_string($rst_enc[ret_direccion]);
    $telf_cliente = $Adt->sanear_string($rst_enc[ret_telefono]);
    $email_cliente = $Adt->sanear_string($rst_enc[ret_email]);
    $direccion = $Adt->sanear_string($emis[emi_dir_establecimiento_emisor]);
    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = $Adt->sanear_string($rst_enc[ret_nombre]);
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
    $xml.="<razonSocial>" . $Adt->sanear_string($emis[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . $Adt->sanear_string($emis[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($emis[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . $secuencial . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . $Adt->sanear_string($emis[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
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
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);
    if (!$Sri->upd_clave($clave, $id)) {
        $sms = 'clave_acceso' . pg_last_error();
    }
    header("Location: http://186.4.200.125:90/central_xml/envio_sri/firmar.php?clave=$clave&programa=$programa&firma=$firma&password=$pass&ambiente=$ambiente");
//    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
//    $dat = $clave . '&' . shell_exec($comando);
////    echo $dat.'&'.$xml;
//    $data = explode('&', $dat);
//    $sms = 0;
//    $env = 'Envio SRI';
//    $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
//    $dt1 = $Adt->sanear_string($data[1]); // Recepcion
//    $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
//    $dt3 = $Adt->sanear_string($data[3]); // Mensaje
//    $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
//    $dt5 = $data[5];                      // Hora y fecha Autorizacion
//    $dt6 = $data[6];                      // XML
//    $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);
//    if (!$Sri->upd_documentos($dat, $id)) {
//        $sms = pg_last_error();
//        $env = 'Envio Fallido';
//    }
//    return $sms . '&' . $clave;
}
