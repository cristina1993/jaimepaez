<?php

include_once 'Conn.php';

class Clase_rep_ventas {

    var $con;

    function Clase_rep_ventas() {
        $this->con = new Conn();
    }

    function lista_total_ventas($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(fac_total_valor) FROM erp_factura where emi_id=$emi");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor where identificacion='$id'");
        }
    }

    function orden_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_cod_punto_emision=$id");
        }
    }

    //// tablas nuevas////

    function lista_total_venta($emi, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(round(cast(fac_subtotal12 as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as sub12,
                                    (SELECT sum(round(cast(fac_subtotal0 as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as sub0,
                                    (SELECT sum(round(cast(fac_subtotal_ex_iva as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as subex,
                                    (SELECT sum(round(cast(fac_subtotal_no_iva as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as subno,
                                    (SELECT sum(round(cast(fac_total_descuento as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as des,
                                    (SELECT sum(round(cast(fac_total_ice as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as ice,
                                    (SELECT sum(round(cast(fac_total_iva as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as iva,
                                    (SELECT sum(round(cast(fac_total_valor as numeric),2)) FROM erp_factura where emi_id=$emi and fac_fecha_emision between '$fec1' and '$fec2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null))as total");
        }
    }

    function lista_total_devoluciones($emi, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(round(cast(ncr_subtotal12 as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as nsub12,
                                    (SELECT sum(round(cast(ncr_subtotal0 as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as nsub0,
                                    (SELECT sum(round(cast(ncr_subtotal_ex_iva as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as nsubex,
                                    (SELECT sum(round(cast(ncr_subtotal_no_iva as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as nsubno,
                                    (SELECT sum(round(cast(ncr_total_descuento as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as ndes,
                                    (SELECT sum(round(cast(ncr_total_ice as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as nice,
                                    (SELECT sum(round(cast(ncr_total_iva as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as niva,
                                    (SELECT sum(round(cast(nrc_total_valor as numeric),2)) FROM erp_nota_credito where emi_id=$emi and ncr_fecha_emision between '$fec1' and '$fec2' and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null))as ntotal");
        }
    }

    function lista_total_cierre($emi, $f1, $f2) {
        return pg_query("SELECT (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='1' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as credito,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='2' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as debito,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='3' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as cheque,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='4' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as efectivo,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='5' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as certificados,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='6' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as bonos,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='7' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as retencion,
                                (SELECT sum(pag_cant) FROM erp_pagos_factura p, erp_factura c  where p.com_id=c.fac_id and c.emi_id=$emi and p.pag_forma='8' and c.fac_fecha_emision between '$f1' and '$f2' and p.pag_estado=0 and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null))as nota,
                                (SELECT sum(round(cast(fac_total_valor as numeric),2)) from erp_factura where emi_id=$emi and fac_fecha_emision between '$f1' and '$f2' and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null)) as valor");
    }

    function lista_ultima_fecha() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_consulta_inv order by con_fecha desc limit 1");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=1");
        }
    }
    
    function lista_ultimo_pto_emision() {
        if ($this->con->Conectar() == true) {
            return pg_query("select emi_cod_punto_emision from erp_emisor order by emi_cod_punto_emision desc limit 1");
        }
    }
    
    function lista_ptos_emision() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=1) as local1,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=1) as bodega1,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=2) as local2,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=2) as bodega2,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=3) as local3,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=3) as bodega3,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=4) as local4,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=4) as bodega4,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=5) as local5,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=5) as bodega5,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=6) as local6,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=6) as bodega6,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=7) as local7,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=7) as bodega7,
                                    (SELECT emi_nombre_comercial FROM erp_emisor WHERE emi_cod_punto_emision=8) as local8,
                                    (SELECT emi_cod_punto_emision FROM erp_emisor WHERE emi_cod_punto_emision=8) as bodega8");
        }
    }

}

?>
