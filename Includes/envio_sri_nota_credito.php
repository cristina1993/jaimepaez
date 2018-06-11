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
            return pg_query("select * from erp_nota_credito where (char_length(ncr_autorizacion)<>37 and char_length(trim(ncr_autorizacion))<>49 ) or  ncr_autorizacion is null");
        }
    }

    function actualizar_datos_documentos($estado, $auto, $fecha, $xml, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nota_credito 
                SET ncr_estado_aut='RECIBIDA $estado',
                    ncr_autorizacion='$auto',
                    ncr_fec_hora_aut='$fecha',
                    ncr_xml_doc='$xml'    
                WHERE ncr_id=$id ");
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
            return pg_query("update erp_nota_credito 
                set ncr_clave_acceso='$dat[0]', 
                ncr_estado_aut='$dat[1] $dat[2]', 
                ncr_observacion_aut='$dat[3]', 
                ncr_autorizacion='$dat[4]',
                ncr_fec_hora_aut='$dat[5]',
                ncr_xml_doc='$dat[6]'    
                where ncr_id=$id ");
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
            return pg_query("update erp_nota_credito 
                set ncr_clave_acceso='$dat'
                where ncr_id=$id ");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_id=$id ");
        }
    }

    function lista_emisor_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_identificacion='$ruc' ");
        }
    }

    ///////////////////nota credito
    function lista_una_nota_credito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id=$id");
        }
    }

    function lista_det_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_nota_credito where ncr_id=$id order by dnc_cod_ice");
        }
    }

    function suma_ice($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dnc_precio_total) FROM  erp_det_nota_credito where ncr_id=$id and dnc_cod_ice='$cod'");
        }
    }

    function lista_un_impuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_impuestos where imp_id=$id");
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

            if (strlen($rst[ncr_clave_acceso]) == 49) { //Si tiene clave de acceso
                $doc1 = $Sri->recupera_datos($rst[ncr_clave_acceso], $ambiente);
                if (strlen($doc1[1]) == 37 || strlen($doc1[1]) == 49) { //Si recupera los datos
                    if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ncr_id])) {
                        $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ncr_clave_acceso], '', 'SuperAdmin');
                        if (!$Sri->registra_errores($data)) {
                            echo pg_last_error();
                        }
                    }
                } else {//Si no recupera los datos.
                    $doc = envio_electronico($rst[ncr_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                    $dc = explode('&', $doc);
                    $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfico Conxecion;
                    if (strlen($dc[0]) == 49 || $err1 == true) {
                        $doc1 = $Sri->recupera_datos($dc[0]);
                        if (strlen($doc1[1]) == 37) {
                            if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ncr_id])) {
                                $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ncr_clave_acceso], '', 'SuperAdmin');
                                if (!$Sri->registra_errores($data)) {
                                    echo pg_last_error();
                                }
                            }
                        }
                    }
                }
            } else { //Si no tiene clave de acceso
                $doc = envio_electronico($rst[ncr_id], $ambiente, $codigo, $tp_emison, $firma, $pass,$programa);
                $dc = explode('&', $doc);
                $err1 = strpos($doc, 'CLAVE ACCESO REGISTRADA'); //Verfifico Conxecion;
                if (strlen($dc[0]) == 49 || $err1 == true) {
                    $doc1 = $Sri->recupera_datos($dc[0]);
                    if (strlen($doc1[1]) == 37) {
                        if (!$Sri->actualizar_datos_documentos($doc1[0], $doc1[1], $doc1[2], $doc1[4], $rst[ncr_id])) {
                            $data = array(1, date('Y-m-d'), date('H:i'), 'Recuperar Datos', 'Error', $rst[ncr_clave_acceso], '', 'SuperAdmin');
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
    $Adt = new Auditoria();
    $Sri = new SRI();
    $rst = pg_fetch_array($Sri->lista_una_nota_credito_id($id));
    $cns_det = $Sri->lista_det_nota_credito($id);
    $cns_det2 = $Sri->lista_det_nota_credito($id);
    $rst_emi = pg_fetch_array($Sri->lista_emisor($rst[emi_id]));
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
    $dir_cliente = $Adt->sanear_string($rst[ncr_direccion]);
    $telf_cliente = $Adt->sanear_string($rst[nrc_telefono]);
    $email_cliente = $Adt->sanear_string($rst[ncr_email]);
    $direccion = $Adt->sanear_string($rst_emi[emi_dir_establecimiento_emisor]);
    $contabilidad = $emis[emi_obligado_llevar_contabilidad];
    $razon_soc_comprador = $Adt->sanear_string($rst[ncr_nombre]);
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
    $xml.="<razonSocial>" . $Adt->sanear_string($rst_emi[emi_nombre]) . "</razonSocial>" . chr(13);
    $xml.="<nombreComercial>" . $Adt->sanear_string($rst_emi[emi_nombre_comercial]) . "</nombreComercial>" . chr(13);
    $xml.="<ruc>" . trim($rst_emi[emi_identificacion]) . "</ruc>" . chr(13);
    $xml.="<claveAcceso>" . $clave . "</claveAcceso>" . chr(13);
    $xml.="<codDoc>" . $cod_doc . "</codDoc>" . chr(13);
    $xml.="<estab>" . $ems . "</estab>" . chr(13);
    $xml.="<ptoEmi>" . $pt_ems . "</ptoEmi>" . chr(13);
    $xml.="<secuencial>" . substr($rst[ncr_numero], -9) . "</secuencial>" . chr(13);
    $xml.="<dirMatriz>" . $Adt->sanear_string($rst_emi[emi_dir_establecimiento_matriz]) . "</dirMatriz>" . chr(13);
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
            $rst_sum = pg_fetch_array($Sri->suma_ice($id, $reg_detalle[dnc_cod_ice]));
            $rst_im = pg_fetch_array($Sri->lista_un_impuesto($reg_detalle[dnc_cod_ice]));
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
            $rst_imp = pg_fetch_array($Sri->lista_un_impuesto($reg_detalle1[dnc_cod_ice]));
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
    $fch = fopen("../xml_docs/" . $clave . ".xml", "w+o");
    fwrite($fch, $xml);
    fclose($fch);
    if (!$Sri->upd_clave($clave, $id)) {
        $sms = 'clave_acceso' . pg_last_error();
    }
    ///envio para firmar
    header("Location: http://186.4.200.125:90/central_xml/envio_sri/firmar.php?clave=$clave&programa=$programa&firma=$firma&password=$pass&ambiente=$ambiente");

//    $comando = 'java -jar /var/www/FacturacionElectronica/digitafXmlSigSend.jar "' . htmlentities($xml, ENT_QUOTES, "UTF-8") . '" "' . htmlentities($parametros, ENT_QUOTES, "UTF-8") . '"';
//    $dat = $clave . '&' . shell_exec($comando);
////    echo $dat . '&' . $xml;
//
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
