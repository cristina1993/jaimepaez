<?php

set_time_limit(0);
include_once '../Clases/Conn.php';
date_default_timezone_set('America/Guayaquil');
$anio = $_REQUEST[anio];
$mes = $_REQUEST[mes];

class SRI {

    var $con;

    function SRI() {
        $this->con = new Conn();
    }

    function lista_emisor() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_emisor where emi_id=1");
        }
    }

    function lista_emisores() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT count(*) as numero_estab FROM  erp_emisor");
        }
    }

    function lista_ventas0($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (select sum(fac_subtotal) as total_ventas0 FROM  erp_factura where  fac_fecha_emision between '$d' and '$h') as venta,
                (select sum(ncr_subtotal) as total_devolucion0 FROM  erp_nota_credito where  ncr_fecha_emision between '$d' and '$h') as devolucion");
        }
    }

    function lista_compras($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT reg_sustento,reg_ruc_cliente, reg_num_documento, reg_id,reg_femision,tdc_codigo,reg_num_autorizacion,
reg_sbt_noiva,reg_sbt0,reg_sbt12,reg_sbt_excento,reg_ice,reg_iva12,reg_tipo_pago
 FROM erp_reg_documentos r, erp_tip_documentos td where cast(r.reg_tipo_documento as integer)=td.tdc_id and reg_tipo_documento != '44' and reg_tipo_documento != '99' and reg_tipo_documento != '15' and reg_femision between '$d' and '$h' and reg_estado='1'
union 
SELECT '1',rnc_identificacion,rnc_numero,rnc_id,rnc_fecha_emision,'4',rnc_autorizacion,
rnc_subtotal_no_iva,rnc_subtotal0,rnc_subtotal12,rnc_subtotal_ex_iva,rnc_total_ice,rnc_total_iva,'0' FROM erp_registro_nota_credito r where rnc_fecha_emision between '$d' and '$h' and rnc_estado='1'
order by tdc_codigo
");
        }
    }


    function lista_retencion_iva($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=10 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull)) as iva10, 
                             (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=20 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull)) as iva20,
                             (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=30 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull )) as iva30,
                             (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=50 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull )) as iva50, 
                             (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=70 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull )) as iva70,
                             (select sum(dtr_valor) from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IV' and dtr_procentaje_retencion=100 and (char_length(ret_estado_aut)=0 or  ret_estado_aut='RECIBIDA AUTORIZADO' or ret_estado_aut isnull )) as iva100");
        }
    }

    function lista_retencion_renta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion r, erp_det_retencion d where r.ret_id=d.ret_id and reg_id=$id and dtr_tipo_impuesto='IR' ");
        }
    }

    function lista_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion r where reg_id=$id and char_length(ret_numero)=17");
        }
    }

    function lista_ventas_clientes($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.cli_ced_ruc,count(*) as facturas, sum(fac_subtotal12) as sub12, sum(fac_subtotal0) as sub0, sum(fac_subtotal_ex_iva) as subex, sum(fac_subtotal_no_iva) as subno, sum(fac_subtotal) as subtotal, sum(fac_total_iva) as iva, sum(fac_total_ice) as ice,  
                (select sum(drr_valor) from erp_registro_retencion r, erp_det_reg_retencion d, erp_i_cliente clr where r.rgr_id =d.rgr_id and r.cli_id=clr.cli_id and drr_tipo_impuesto='IV' and clr.cli_ced_ruc=c.cli_ced_ruc and rgr_estado=1 and rgr_fecha_emision between '$d' and '$h') as ret_iva,
                (select sum(drr_valor) from erp_registro_retencion r, erp_det_reg_retencion d, erp_i_cliente clr where r.rgr_id =d.rgr_id and r.cli_id=clr.cli_id and drr_tipo_impuesto='IR' and clr.cli_ced_ruc=c.cli_ced_ruc and rgr_estado=1 and rgr_fecha_emision between '$d' and '$h') as ret_renta
                                FROM  erp_factura f , erp_i_cliente c where f.cli_id=c.cli_id and fac_fecha_emision between '$d' and '$h' group by c.cli_ced_ruc 
                    ");
        }
    }


    function lista_notas_creditos_venta($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.cli_ced_ruc,count(*) as notas, sum(ncr_subtotal12) as sub12, sum(ncr_subtotal0) as sub0, sum(ncr_subtotal_ex_iva) as subex, sum(ncr_subtotal_no_iva) as subno, sum(ncr_subtotal) as subtotal, sum(ncr_total_iva) as iva, sum(ncr_total_ice) as ice,  
                ('0') as ret_iva,
                ('0') as ret_renta
                                FROM  erp_nota_credito f , erp_i_cliente c where f.cli_id=c.cli_id and ncr_fecha_emision between '$d' and '$h' group by c.cli_ced_ruc 
                    ");
        }
    }


    function lista_registro_retenciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura where com_id=$id");
        }
    }

    function lista_pagos_cliente($id, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select p.* from erp_pagos_factura p, erp_factura f, erp_i_cliente c where f.fac_id=p.com_id and f.cli_id=c.cli_id and cli_ced_ruc='$id' and fac_fecha_emision between '$d' and '$h' limit 1");
        }
    }

    function lista_ventas_emisor($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select emi_id,sum(fac_subtotal) from erp_factura where fac_fecha_emision between '$d' and '$h' group by emi_id");
        }
    }

    function lista_devoluciones_emisor($d, $h,$emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(ncr_subtotal) from erp_nota_credito where ncr_fecha_emision between '$d' and '$h' and  emi_id=$emi");
        }
    }

    function lista_anulados($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select '01' as tipo, fac_numero, fac_autorizacion  from erp_factura where  fac_estado_aut='ANULADO' and fac_fecha_emision between '$d' and '$h'
                            UNION 
                            select '04' as tipo, ncr_numero, ncr_autorizacion from erp_nota_credito where ncr_estado_aut='ANULADO' and ncr_fecha_emision between '$d' and '$h'
                            UNION 
                            select '05' as tipo, ndb_numero, ndb_autorizacion from erp_nota_debito where ndb_estado_aut='ANULADO' and ndb_fecha_emision between '$d' and '$h'
                            UNION 
                            select '07' as tipo, ret_numero, ret_autorizacion from erp_retencion where ret_estado_aut='ANULADO' and ret_numero<>'' and ret_fecha_emision between '$d' and '$h' 
                            ");
        }
    }

    function lista_reg_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito n, erp_reg_documentos f where f.reg_id=n.reg_id and rnc_id=$id and rnc_estado=1");
        }
    }

    function lista_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function sanear_string($string) {

        $string = trim($string);

        $string = str_str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        $string = str_str_replace(
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

$Sri = new SRI();
///fechas
$fec_ini = $anio . '-' . $mes . '-01';
$month = $anio . '-' . $mes;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$fec_fin = date('Y-m-d', strtotime("{$aux} - 1 day"));

$rst_emi = pg_fetch_array($Sri->lista_emisor());
$rst_nm_emi = pg_fetch_array($Sri->lista_emisores());
$rst_ven0 = pg_fetch_array($Sri->lista_ventas0($fec_ini, $fec_fin));
///emisor
$xml.="<iva>" . chr(13);
$xml.="<TipoIDInformante>" . "R" . "</TipoIDInformante>" . chr(13);
$xml.="<IdInformante>" . trim($rst_emi[emi_identificacion]) . "</IdInformante>" . chr(13);
$xml.="<razonSocial>" . $rst_emi[emi_nombre] . "</razonSocial>" . chr(13);
$xml.="<Anio>" . $anio . "</Anio>" . chr(13);
$xml.="<Mes>" . $mes . "</Mes>" . chr(13);
$rst_nm_emi[numero_estab] =$rst_nm_emi[numero_estab] -1;
if ($rst_nm_emi[numero_estab] < 10) {
    $numero_estab = '00' . $rst_nm_emi[numero_estab];
} else if ($rst_nm_emi[numero_estab] < 100) {
    $numero_estab = '0' . $rst_nm_emi[numero_estab];
} else {
    $numero_estab = $rst_nm_emi[numero_estab];
}
$xml.="<numEstabRuc>" . $numero_estab . "</numEstabRuc>" . chr(13);
$xml.="<totalVentas>" . str_replace(',', '', number_format($rst_ven0[venta]-$rst_ven0[devolucion], 2)) . "</totalVentas>" . chr(13);
$xml.="<codigoOperativo>" . "IVA" . "</codigoOperativo>" . chr(13);

// /// compras  
$cns_cmp = $Sri->lista_compras($fec_ini, $fec_fin);
if (pg_num_rows($cns_cmp) != 0) {
    $xml.="<compras>" . chr(13);
    while ($rst_cmp = pg_fetch_array($cns_cmp)) {
        //    ///documneto de sustento
        if ($rst_cmp[reg_sustento] < 10) {
            $doc_sust = "0" . $rst_cmp[reg_sustento];
        } else {
            $doc_sust = $rst_cmp[reg_sustento];
        }

        //tipo_comprobante 
        if ($rst_cmp[tdc_codigo] < 10) {
            $tipo_comp = "0" . $rst_cmp[tdc_codigo];
        } else {
            $tipo_comp = $rst_cmp[tdc_codigo];
        }

        ///identificacion
        if (strlen(trim($rst_cmp[reg_ruc_cliente])) == 13) {
            $tpIdProv = '01'; //'ruc';
        } else if (strlen(trim($rst_cmp[reg_ruc_cliente])) == 10) {
            $tpIdProv = '02'; //'cedula';
        } else {
            $tpIdProv = '03';
        }

        $num_doc = explode('-', $rst_cmp[reg_num_documento]);
        if($rst_cmp[tdc_codigo]!=4){
            $rst_rti = pg_fetch_array($Sri->lista_retencion_iva($rst_cmp[reg_id]));
            $rst_rtr = pg_fetch_array($Sri->lista_retencion_renta($rst_cmp[reg_id]));
            $rst_ret = pg_fetch_array($Sri->lista_retencion($rst_cmp[reg_id]));
            $num_ret = explode('-', $rst_ret[ret_numero]);
        }else{
            $rst_rti[iva10]=0;
            $rst_rti[iva20]=0;
            $rst_rti[iva30]=0;
            $rst_rti[iva50]=0;
            $rst_rti[iva70]=0;
            $rst_rti[iva100]=0;
        }
        $xml.="<detalleCompras>" . chr(13);
        $xml.="<codSustento>" . $doc_sust . "</codSustento>" . chr(13);
        $xml.="<tpIdProv>" .$tpIdProv . "</tpIdProv>" . chr(13);
        $xml.="<idProv>" . $rst_cmp[reg_ruc_cliente] . "</idProv>" . chr(13);
        $xml.="<tipoComprobante>" . $tipo_comp . "</tipoComprobante>" . chr(13);
        $xml.="<parteRel>" . "NO" . "</parteRel>" . chr(13);
        $xml.="<fechaRegistro>" . date_format(date_create($rst_cmp[reg_femision]), 'd/m/Y') . "</fechaRegistro>" . chr(13);
        $xml.="<establecimiento>" . $num_doc[0] . "</establecimiento>" . chr(13);
        $xml.="<puntoEmision>" . $num_doc[1] . "</puntoEmision>" . chr(13);
        $xml.="<secuencial>" . $num_doc[2] . "</secuencial>" . chr(13);
        $xml.="<fechaEmision>" . date_format(date_create($rst_cmp[reg_femision]), 'd/m/Y') . "</fechaEmision>" . chr(13);
        $xml.="<autorizacion>" . $rst_cmp[reg_num_autorizacion] . "</autorizacion>" . chr(13);
        $xml.="<baseNoGraIva>" . str_replace(',', '', number_format($rst_cmp[reg_sbt_noiva], 2)) . "</baseNoGraIva>" . chr(13);
        $xml.="<baseImponible>" . str_replace(',', '', number_format($rst_cmp[reg_sbt0], 2)) . "</baseImponible>" . chr(13);
        $xml.="<baseImpGrav>" . str_replace(',', '', number_format($rst_cmp[reg_sbt12], 2)) . "</baseImpGrav>" . chr(13);
        $xml.="<baseImpExe>" . str_replace(',', '', number_format($rst_cmp[reg_sbt_excento], 2)) . "</baseImpExe>" . chr(13);
        $xml.="<montoIce>" . str_replace(',', '', number_format($rst_cmp[reg_ice], 2)) . "</montoIce>" . chr(13);
        $xml.="<montoIva>" . str_replace(',', '', number_format($rst_cmp[reg_iva12], 2)) . "</montoIva>" . chr(13);
        $xml.="<valRetBien10>" . str_replace(',', '', number_format($rst_rti[iva10], 2)) . "</valRetBien10>" . chr(13);
        $xml.="<valRetServ20>" . str_replace(',', '', number_format($rst_rti[iva20], 2)) . "</valRetServ20>" . chr(13);
        $xml.="<valorRetBienes>" . str_replace(',', '', number_format($rst_rti[iva30], 2)) . "</valorRetBienes>" . chr(13);
        $xml.="<valRetServ50>" . str_replace(',', '', number_format($rst_rti[iva50], 2)) . "</valRetServ50>" . chr(13);
        $xml.="<valorRetServicios>" . str_replace(',', '', number_format($rst_rti[iva70], 2)) . "</valorRetServicios>" . chr(13);
        $xml.="<valRetServ100>" . str_replace(',', '', number_format($rst_rti[iva100], 2)) . "</valRetServ100>" . chr(13);
        $xml.="<totbasesImpReemb>" . str_replace(',', '', number_format(0, 2)) . "</totbasesImpReemb>" . chr(13);
        $xml.="<pagoExterior>" . chr(13);
        $xml.="<pagoLocExt>" . "01" . "</pagoLocExt>" . chr(13);
        $xml.="<paisEfecPago>" . "NA" . "</paisEfecPago>" . chr(13);
        $xml.="<aplicConvDobTrib>" . "NA" . "</aplicConvDobTrib>" . chr(13);
        $xml.="<pagExtSujRetNorLeg>" . "NA" . "</pagExtSujRetNorLeg>" . chr(13);
        $xml.="<pagoRegFis>" . "NA" . "</pagoRegFis>" . chr(13);
        $xml.= "</pagoExterior>" . chr(13);
        if ($rst_cmp[reg_sbt] > 1000) {
            $tipoPago ='20';
        } else {
            $tipoPago = $rst_cmp[reg_tipo_pago];
            if($tipoPago==0){
                $tipoPago = '01';
            }
        }
        if($rst_cmp[tdc_codigo]==4){
            $tipoPago = '01';
        }
        $xml.= "<formasDePago>" . chr(13);
        $xml.= "<formaPago>" . $tipoPago . "</formaPago>" . chr(13);
        $xml.= "</formasDePago>" . chr(13);
        if($rst_cmp[tdc_codigo]!=4){
            if (!empty($rst_ret)) {
                $xml.= "<air>" . chr(13);
                $xml.= "<detalleAir>" . chr(13);
                $xml.= "<codRetAir>" . $rst_rtr[dtr_codigo_impuesto] . "</codRetAir>" . chr(13);
                $xml.= "<baseImpAir>" . str_replace(',', '', number_format($rst_rtr[dtr_base_imponible],2)) . "</baseImpAir>" . chr(13);
                $xml.= "<porcentajeAir>" . $rst_rtr[dtr_procentaje_retencion] . "</porcentajeAir>" . chr(13);
                $xml.= "<valRetAir>" . str_replace(',', '', number_format($rst_rtr[dtr_valor],2)) . "</valRetAir>" . chr(13);
                $xml.= "</detalleAir>" . chr(13);
                $xml.= "</air>" . chr(13);
                $xml.= "<estabRetencion1>" . $num_ret[0] . "</estabRetencion1>" . chr(13);
                $xml.= "<ptoEmiRetencion1>" . $num_ret[1] . "</ptoEmiRetencion1>" . chr(13);
                $xml.= "<secRetencion1>" . $num_ret[2] . "</secRetencion1>" . chr(13);
                $xml.= "<autRetencion1>" . $rst_ret[ret_autorizacion] . "</autRetencion1>" . chr(13);
                $xml.= "<fechaEmiRet1>" . date_format(date_create($rst_ret[ret_fecha_emision]), 'd/m/Y') . "</fechaEmiRet1>" . chr(13);
            }
        }

///nota de credito
        if($rst_cmp[tdc_codigo]==4){
            $rst_nc = pg_fetch_array($Sri->lista_reg_nota_credito($rst_cmp[reg_id]));
            if (!empty($rst_nc)) {
                $num_nc = explode('-', $rst_nc[reg_num_documento]);
                $xml.= "<docModificado>" . '01' . "</docModificado>" . chr(13);
                $xml.= "<estabModificado>" . $num_nc[0] . "</estabModificado>" . chr(13);
                $xml.= "<ptoEmiModificado>" . $num_nc[1] . "</ptoEmiModificado>" . chr(13);
                $xml.= "<secModificado>" . $num_nc[2] . "</secModificado>" . chr(13);
                $xml.= "<autModificado>" . $rst_nc[reg_num_autorizacion] . "</autModificado>" . chr(13);
            }
        }
        $xml.="</detalleCompras>" . chr(13);
    }
    $xml.="</compras>" . chr(13);
}
///ventas
$cns_vnt = $Sri->lista_ventas_clientes($fec_ini, $fec_fin);
if (pg_num_rows($cns_vnt) != 0) {
    $xml.="<ventas>" . chr(13);
    while ($rst_vnt = pg_fetch_array($cns_vnt)) {
        // //         ///identificacion
        if (trim($rst_vnt[cli_ced_ruc]) == '9999999999' || trim($rst_vnt[cli_ced_ruc]) == '9999999999999') {
            $tpIdCliente = '07'; //'consumidor final';
        } else if (strlen(trim($rst_vnt[cli_ced_ruc])) == 10) {
            $tpIdCliente = '05'; //'Cédula';
        } else if (strlen(trim($rst_vnt[cli_ced_ruc])) == 13) {
            $tpIdCliente = '04'; //'RUC';
        } else {
            $tpIdCliente = '06';
        }
        $xml.="<detalleVentas>" . chr(13);
        $xml.= "<tpIdCliente>" . $tpIdCliente . "</tpIdCliente>" . chr(13);
        $xml.= "<idCliente>" . $rst_vnt[cli_ced_ruc] . "</idCliente>" . chr(13);
        if($tpIdCliente != '07'){
            $xml.= "<parteRelVtas>" . "NO" . "</parteRelVtas>" . chr(13);
        }
        if ($tpIdCliente == '06') {
            $rst_cli = pg_fetch_array($Sri->lista_cliente($rst_vnt[cli_ced_ruc]));
            if($rst_cli[cli_categoria]==0){
                $tp_cl='01';
            }else{
                $tp_cl='02';
            }
            $xml.= "<tipoCliente>" . $tp_cl . "</tipoCliente>" . chr(13);
            $xml.= "<denoCli>" . $rst_cli[cli_raz_social] . "</denoCli>" . chr(13);
        }
        $xml.= "<tipoComprobante>" . "18" . "</tipoComprobante>" . chr(13);
        $xml.= "<tipoEmision>" . "F" . "</tipoEmision>" . chr(13);
        $xml.= "<numeroComprobantes>" . $rst_vnt[facturas] . "</numeroComprobantes>" . chr(13);
        $xml.= "<baseNoGraIva>" . str_replace(',', '', number_format($rst_vnt[subno], 2)) . "</baseNoGraIva>" . chr(13);
        $xml.= "<baseImponible>" . str_replace(',', '', number_format($rst_vnt[sub0], 2)) . "</baseImponible>" . chr(13);
        $xml.= "<baseImpGrav>" . str_replace(',', '', number_format($rst_vnt[sub12], 2)) . "</baseImpGrav>" . chr(13);
        $xml.= "<montoIva>" . str_replace(',', '', number_format($rst_vnt[iva], 2)) . "</montoIva>" . chr(13);
        $xml.= "<montoIce>" . str_replace(',', '', number_format($rst_vnt[ice], 2)) . "</montoIce>" . chr(13);
        $xml.= "<valorRetIva>" . str_replace(',', '', number_format($rst_vnt[ret_iva], 2)) . "</valorRetIva>" . chr(13);
        $xml.= "<valorRetRenta>" . str_replace(',', '', number_format($rst_vnt[ret_renta], 2)) . "</valorRetRenta>" . chr(13);
        $xml.= "<formasDePago>" . chr(13);
        $cns_pg = $Sri->lista_pagos_cliente($rst_vnt[cli_ced_ruc], $fec_ini, $fec_fin);
        $rst_pg = pg_fetch_array($cns_pg);
        if ($rst_pg[pag_forma] == '1') {
            $formap = '19';
        } else if ($rst_pg[pag_forma] == '2') {
            $formap = '16';
        } else if ($rst_pg[pag_forma] == '3') {
            $formap = '02';
        } else if ($rst_pg[pag_forma] == '4') {
            $formap = '01';
        } else if ($rst_pg[pag_forma] == '5') {
            $formap = '01';
        } else if ($rst_pg[pag_forma] == '6') {
            $formap = '01';
        } else if ($rst_pg[pag_forma] == '7') {
            $formap = '01';
        } else if ($rst_pg[pag_forma] == '8') {
            $formap = '15';
        } else if ($rst_pg[pag_forma] == '9') {
            $formap = '20';
        } else {
            $formap = '01';
        }

        $xml.="<formaPago>" . $formap . "</formaPago>" . chr(13);
        $xml.="</formasDePago>" . chr(13);
        $xml.="</detalleVentas>" . chr(13);
    }

    ///detalles de Notas de Creditos
    $cns_ncr=$Sri->lista_notas_creditos_venta($fec_ini, $fec_fin);
    while ($rst_ncr = pg_fetch_array($cns_ncr)) {
        // //         ///identificacion
        if ($rst_ncr[cli_ced_ruc] == '9999999999' || $rst_ncr[cli_ced_ruc] == '9999999999999') {
            $tpIdCliente = '07'; //'consumidor final';
        } else if (strlen($rst_ncr[cli_ced_ruc]) == 10) {
            $tpIdCliente = '05'; //'Cédula';
        } else if (strlen($rst_ncr[cli_ced_ruc]) == 13) {
            $tpIdCliente = '04'; //'RUC';
        } else {
            $tpIdCliente = '06';
        }
        $xml.="<detalleVentas>" . chr(13);
        $xml.= "<tpIdCliente>" . $tpIdCliente . "</tpIdCliente>" . chr(13);
        $xml.= "<idCliente>" . $rst_ncr[cli_ced_ruc] . "</idCliente>" . chr(13);
        if($tpIdCliente != '07'){
            $xml.= "<parteRelVtas>" . "NO" . "</parteRelVtas>" . chr(13);
        }
        if ($tpIdCliente == '06') {
            $rst_cli = pg_fetch_array($Sri->lista_cliente($rst_ncr[cli_ced_ruc]));
            if($rst_cli[cli_categoria]==0){
                $tp_cl='01';
            }else{
                $tp_cl='02';
            }
            $xml.= "<tipoCliente>" . $tp_cl . "</tipoCliente>" . chr(13);
            $xml.= "<DenoCli>" . $rst_cli[cli_raz_social] . "</DenoCli>" . chr(13);
        }
        $xml.= "<tipoComprobante>" . "04" . "</tipoComprobante>" . chr(13);
        $xml.= "<tipoEmision>" . "F" . "</tipoEmision>" . chr(13);
        $xml.= "<numeroComprobantes>" . $rst_ncr[notas] . "</numeroComprobantes>" . chr(13);
        $xml.= "<baseNoGraIva>" . str_replace(',', '', number_format($rst_ncr[subno], 2)) . "</baseNoGraIva>" . chr(13);
        $xml.= "<baseImponible>" . str_replace(',', '', number_format($rst_ncr[sub0], 2)) . "</baseImponible>" . chr(13);
        $xml.= "<baseImpGrav>" . str_replace(',', '', number_format($rst_ncr[sub12], 2)) . "</baseImpGrav>" . chr(13);
        $xml.= "<montoIva>" . str_replace(',', '', number_format($rst_ncr[iva], 2)) . "</montoIva>" . chr(13);
        $xml.= "<montoIce>" . str_replace(',', '', number_format($rst_ncr[ice], 2)) . "</montoIce>" . chr(13);
        $xml.= "<valorRetIva>" . str_replace(',', '', number_format($rst_ncr[ret_iva], 2)) . "</valorRetIva>" . chr(13);
        $xml.= "<valorRetRenta>" . str_replace(',', '', number_format($rst_ncr[ret_renta], 2)) . "</valorRetRenta>" . chr(13);
        $xml.="</detalleVentas>" . chr(13);
    }
    $xml.="</ventas>" . chr(13);
}
///ventasEstablecimiento
$cns_vnt_emi = $Sri->lista_ventas_emisor($fec_ini, $fec_fin);
if (pg_num_rows($cns_vnt_emi) != 0) {
    $xml.="<ventasEstablecimiento>" . chr(13);

    while ($rst_vem = pg_fetch_array($cns_vnt_emi)) {
        if ($rst_vem[emi_id] < 10) {
            $emisor = '00' . $rst_vem[emi_id];
        } else if ($rst_vem[emi_id] < 100) {
            $emisor = '0' . $rst_vem[emi_id];
        } else {
            $emisor = $rst_vem[emi_id];
        }
        $rst_dv=pg_fetch_array($Sri->lista_devoluciones_emisor($fec_ini, $fec_fin,$rst_vem[emi_id]));
        $xml.="<ventaEst>" . chr(13);
        $xml.="<codEstab>" . $emisor . "</codEstab>" . chr(13);
        $xml.="<ventasEstab>" . str_replace(',', '', number_format($rst_vem[sum]-$rst_dv[sum], 2)) . "</ventasEstab>" . chr(13);
        $xml.="</ventaEst>" . chr(13);
    }
    $xml.="</ventasEstablecimiento>" . chr(13);
}
///Anulados
$cns_anulado = $Sri->lista_anulados($fec_ini, $fec_fin);
if (pg_num_rows($result) != 0) {
    $xml.="<anulados>" . chr(13);
    while ($rst_anu = pg_fetch_array($cns_anulado)) {
        $num_dc = explode('-', $rst_anu[fac_numero]);
        $xml.="<detalleAnulados>" . chr(13);
        $xml.="<tipoComprobante>" . $rst_anu[tipo] . "</tipoComprobante>" . chr(13);
        $xml.="<establecimiento>" . $num_dc[0] . "</establecimiento>" . chr(13);
        $xml.="<puntoEmision>" . $num_dc[1] . "</puntoEmision>" . chr(13);
        $xml.="<secuencialInicio>" . $num_dc[2] . "</secuencialInicio>" . chr(13);
        $xml.="<secuencialFin>" . $num_dc[2] . "</secuencialFin>" . chr(13);
        $xml.="<autorizacion>" . $rst_anu[fac_autorizacion] . "</autorizacion>" . chr(13);
        $xml.="</detalleAnulados>" . chr(13);
    }
    $xml.="</anulados>" . chr(13);
}
$xml.="</iva>" . chr(13);
$fch = fopen("../xml_docs/ats" . $anio . "_" . $mes . ".xml", "w+o");
fwrite($fch, $xml);
fclose($fch);
$file = '../xml_docs/ats' . $anio . '_' . $mes . '.xml';
header("Content-type:xml");
header("Content-length:" . filesize($file));
header("Content-Disposition: attachment; filename=ats" . $anio . "_" . $mes . ".xml");
readfile($file);
unlink($file);

