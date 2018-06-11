<?php

include_once 'Conn.php';

class Reportes {

    var $con;

    function Reportes() {
        $this->con = new Conn();
    }

    function lista_asientos_epyg($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("
(select con_concepto_debe from erp_asientos_contables where con_fecha_emision between '$desde' and '$hasta' and con_concepto_debe<>'' and substr(con_concepto_debe,1,1)>'3'  group by con_concepto_debe  )
union
(select con_concepto_haber from erp_asientos_contables where con_fecha_emision between '$desde' and '$hasta' and con_concepto_haber<>'' and substr(con_concepto_haber,1,1)>'3'  group by con_concepto_haber ) order by con_concepto_debe
");
        }
    }

    function lista_una_cuenta_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_codigo='$cod'");
        }
    }

    function lista_cuentas_epyg() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE SUBSTR(pln_codigo,1,1)>'3' ORDER BY pln_codigo ");
        }
    }

    function lista_cuentas_existe($cod, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables WHERE (con_concepto_debe='$cod' or con_concepto_haber='$cod') and con_fecha_emision between '$desde' and '$hasta'");
        }
    }

    function lista_parcial_cuenta($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("");
        }
    }

    ////////////////////////
    function listar_descripcion_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where pln_codigo='$id'");
        }
    }
    
    function listar_descripcion_asiento1($id,$idp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_plan_cuentas where (pln_codigo='$id' or pln_codigo='$idp')");
        }
    }

    function lista_balance_general($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,2)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber1,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,5)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,8)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber3,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe4,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,11)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber4,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables where substr(con_concepto_debe,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe5,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables where substr(con_concepto_haber,1,14)='$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber5");
        }
    }
    
    ///nueva funcion para calculo de cuentas (-)
    function lista_balance_general1($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT sum(con_valor_debe) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe) and  pln_operacion=0 and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber) and  pln_operacion=0 and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber1,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a where not exists (select * from erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe)) and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe2,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a where not exists (select * from erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber)) and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber2,
                            (SELECT sum(con_valor_debe) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_debe) and  pln_operacion=1 and con_concepto_debe like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as debe3,
                            (SELECT sum(con_valor_haber) FROM erp_asientos_contables a, erp_plan_cuentas c where trim(c.pln_codigo)=trim(a.con_concepto_haber) and  pln_operacion=1 and con_concepto_haber like '$cod' and con_fecha_emision between '$fec1' and '$fec2')as haber3");
        }
    }

    function suma_cuentas($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables where con_concepto_debe='$cod' and con_fecha_emision between '$fec1' and '$fec2') as debe,
                                   (SELECT sum(con_valor_haber) as debe FROM erp_asientos_contables where con_concepto_haber='$cod' and con_fecha_emision between '$fec1' and '$fec2') as haber");
        }
    }

///REOPORTES POR LOCALES
    function lista_emisores($val) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_emisor 
                $val
                order by emi_cod_orden");
        }
    }

    function lista_tot_tipo_pago($e, $d, $h, $v, $f) {
        if ($this->con->Conectar() == true) {
            return pg_query("select
sum(pf.pag_cant)
from erp_factura c,erp_pagos_factura pf
where c.fac_id=pf.com_id
and c.emi_id=$e and (c.fac_estado_aut<>'ANULADO' or c.fac_estado_aut is null)
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and c.vnd_id ='$v'
and pf.pag_forma='$f'
and pf.pag_estado=0
");
        }
    }

    function lista_devoluciones_vendedor($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select count(*) as nfact,
                                    sum(c.nrc_total_valor) as tventa,
                                    sum(c.ncr_total_descuento) as desc,
                                    sum(c.ncr_subtotal12) as con_iva,
                                    (sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva)) as sin_iva,
                                    (sum(c.ncr_subtotal12)+(sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva))) as sbt_neto,
                                    sum(c.ncr_total_ice) as ice,
                                    sum(c.ncr_total_iva) as iva,
                                    sum(c.nrc_total_valor) as tventas
                            from erp_nota_credito c
                            where emi_id=$e and  (c.ncr_estado_aut<>'ANULADO' or c.ncr_estado_aut is null) 
                            and ncr_fecha_emision between '$d' and '$h'
                            and vnd_id=$v
                            ");
        }
    }

    function lista_devoluciones_tot($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
count(*) as nfact,
sum(c.total_valor) as tventa,
sum(c.total_descuento) as desc,
sum(c.subtotal12) as con_iva,
(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva)) as sin_iva,
(sum(c.subtotal12)+(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva))) as sbt_neto,
sum(c.total_ice) as ice,
sum(c.total_iva) as iva,
sum(c.total_valor) as tventas
from comprobantes c
where c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=4
and exists(
select * from comprobantes f 
where replace(f.num_documento,'-','')=c.num_factura_modifica
and f.fecha_emision >=$d
and f.fecha_emision <=$h
and f.tipo_comprobante=1
)");
        }
    }

    function lista_ventas_devoluciones_total($e, $d, $h, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
count(*) as nfact,
sum(c.total_valor) as tventa,
sum(c.total_descuento) as desc,
sum(c.subtotal12) as con_iva,
(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva)) as sin_iva,
(sum(c.subtotal12)+(sum(c.subtotal0)+sum(c.subtotal_exento_iva)+sum(c.subtotal_no_objeto_iva))) as sbt_neto,
sum(c.total_ice) as ice,
sum(c.total_iva) as iva,
sum(c.total_valor) as tventas
from comprobantes c
where c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=$t
");
        }
    }

    function lista_ventas_devoluciones_vendedor($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor,
                                    count(*) as nfact,
                                    sum(round(cast(fac_total_valor as numeric),2)) as tventa,
                                    sum(round(cast(fac_total_descuento as numeric),2)) as desc,
                                    sum(round(cast(fac_subtotal12 as numeric),2)) as con_iva,
                                    (sum(round(cast(fac_subtotal0 as numeric),2))+sum(round(cast(fac_subtotal_ex_iva as numeric),2))+sum(round(cast(fac_subtotal_no_iva as numeric),2))) as sin_iva,
                                    sum(round(cast(fac_subtotal as numeric),2)) as sbt_neto,
                                    sum(round(cast(fac_total_ice as numeric),2)) as ice,
                                    sum(round(cast(fac_total_iva as numeric),2)) as iva,
                                    sum(round(cast(fac_total_valor as numeric),2)) as tventas
                            from erp_factura
                            where emi_id=$e and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
                            and fac_fecha_emision between '$d' and '$h'
                            and vnd_id=$v
                                group by vnd_id
                            ");
        }
    }

    function lista_reporte_productos($d, $h, $fml, $txt, $linea, $talla, $fml2, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("
(select 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
d.cod_producto,
d.lote,
pc.pro_t,
pc.pro_ab,
d.descripcion,
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and char_length(d.lote)>3
$fml
$txt    
$linea
$talla
$val    
group by 
pcs.ids,
familia,
d.cod_producto,
d.lote,
pc.pro_t,
pc.pro_ab,
d.descripcion)
union
(select 
'0' AS ids,
'INDUSTRIALES' AS familia,
d.cod_producto,
'' AS lote, 
'' AS pro_t,
'' AS pro_ab,
d.descripcion,
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_i_productos pi
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pi.pro_codigo
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and char_length(d.lote)<3
$fml2
$txt    
$val
group by
d.cod_producto,
d.descripcion)
order by familia


 ");
        }
    }

    function lista_reporte_productos_totales($d, $h, $e, $cod, $lt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
and d.cod_producto='$cod'
and d.lote='$lt'
");
        }
    }

    function lista_reporte_productos_totales_ind($d, $h, $e, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_i_productos pi
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pi.pro_codigo
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
and d.cod_producto='$cod'                
                ");
        }
    }

    function lista_reporte_productos_totales_general($d, $h, $e) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_productos pc,
erp_productos_set pcs
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=pc.pro_a
and d.lote=pc.pro_ac
and pc.ids=pcs.ids
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and c.tipo_comprobante=1
and c.cod_punto_emision=$e
");
        }
    }

    function lista_familias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.pro_tipo, '&', 10) AS protipo ,ps.* FROM erp_productos_set ps order by protipo");
        }
    }

    function update_vendedores($vnd1, $vnd2) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set vendedor='$vnd2' where vendedor = '$vnd1'   ");
        }
    }

    function lista_vnd_fact($vnd) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  comprobantes where vendedor='$vnd'  ");
        }
    }

    function lista_valor_targeta_contado($d, $h, $b, $t, $fp, $e, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
sum(pf.pag_cant)
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and pf.pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta='$t'
and pf.pag_contado='$fp'
and c.emi_id=$e
and c.vnd_id ='$v'
and pag_estado=0
and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null)    
");
        }
    }
    function lista_tc_general($d, $h, $b) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
pf.pag_tarjeta,
CASE 
	WHEN pag_tarjeta='1' THEN 'VISA'
	WHEN pag_tarjeta='2' THEN 'MASTER CARD'
	WHEN pag_tarjeta='3' THEN 'AMERICAN EXPRESS'
	WHEN pag_tarjeta='4' THEN 'DINNERS'
	WHEN pag_tarjeta='5' THEN 'DISCOVER'
	WHEN pag_tarjeta='6' THEN 'CUOTAFACIL'
	ELSE 'Error'
END as targeta
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta<>'0'
and pag_estado=0
and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null)
group by pf.pag_tarjeta
ORDER BY targeta");
        }
    }
    
function lista_pag_general($d, $h, $b, $tg) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
pf.pag_contado,
CASE 
	WHEN pf.pag_contado='1' THEN 'CONTADO'
	WHEN pf.pag_contado='2' THEN '3 MESES'
	WHEN pf.pag_contado='3' THEN '6 MESES'
	WHEN pf.pag_contado='4' THEN '9 MESES'
	WHEN pf.pag_contado='5' THEN '12 MESES'
	WHEN pf.pag_contado='6' THEN '18 MESES'
	WHEN pf.pag_contado='7' THEN '36 MESES'
	ELSE 'Error'
END as pago,
sum(pf.pag_cant)
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and pf.pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pf.pag_banco='$b'
and pf.pag_tarjeta='$tg'
and pf.pag_contado is not null
and pag_estado=0
and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null)
group by pf.pag_contado");
        }
    }

 function lista_bancos_desgloce_general_tc($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pag_banco, 
CASE 
	WHEN pag_banco='1' THEN 'Banco Pichincha'
	WHEN pag_banco='2' THEN 'Banco del Pacífico'
	WHEN pag_banco='3' THEN 'Banco de Guayaquil'
	WHEN pag_banco='4' THEN 'Produbanco'
	WHEN pag_banco='5' THEN 'Banco Bolivariano'
	WHEN pag_banco='6' THEN 'Banco Internacional'
	WHEN pag_banco='7' THEN 'Banco del Austro'
	WHEN pag_banco='8' THEN 'Banco Promerica'
	WHEN pag_banco='9' THEN 'Banco de Machala'
	WHEN pag_banco='10' THEN 'BGR'
	WHEN pag_banco='11' THEN 'Citibank (Ecuador)'
	WHEN pag_banco='12' THEN 'Banco ProCredit (Ecuador)'
	WHEN pag_banco='13' THEN 'UniBanco'
	WHEN pag_banco='14' THEN 'Banco Solidario'
	WHEN pag_banco='15' THEN 'Banco de Loja'
	WHEN pag_banco='16' THEN 'Banco Territorial'
	WHEN pag_banco='17' THEN 'Banco Coopnacional'
	WHEN pag_banco='18' THEN 'Banco Amazonas'
	WHEN pag_banco='19' THEN 'Banco Capital'
	WHEN pag_banco='20' THEN 'Banco D-MIRO'
	WHEN pag_banco='21' THEN 'Banco Finca'
	WHEN pag_banco='22' THEN 'Banco Comercial de Manabí'
	WHEN pag_banco='23' THEN 'Banco COFIEC'
	WHEN pag_banco='24' THEN 'Banco del Litoral'
	WHEN pag_banco='25' THEN 'Banco Delbank'
	WHEN pag_banco='26' THEN 'Banco Sudamericano'
	ELSE 'Error'
END as banco
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and pag_forma='1'
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and pag_banco<>'0'
and pag_estado=0
and (c.fac_estado_aut<>'ANULADO' OR c.fac_estado_aut is null)
group by pag_banco
");
        }
    }

    function lista_bancos_desgloce_tc($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pag_banco, 
CASE 
	WHEN pag_banco='1' THEN 'Banco Pichincha'
	WHEN pag_banco='2' THEN 'Banco del Pacífico'
	WHEN pag_banco='3' THEN 'Banco de Guayaquil'
	WHEN pag_banco='4' THEN 'Produbanco'
	WHEN pag_banco='5' THEN 'Banco Bolivariano'
	WHEN pag_banco='6' THEN 'Banco Internacional'
	WHEN pag_banco='7' THEN 'Banco del Austro'
	WHEN pag_banco='8' THEN 'Banco Promerica'
	WHEN pag_banco='9' THEN 'Banco de Machala'
	WHEN pag_banco='10' THEN 'BGR'
	WHEN pag_banco='11' THEN 'Citibank (Ecuador)'
	WHEN pag_banco='12' THEN 'Banco ProCredit (Ecuador)'
	WHEN pag_banco='13' THEN 'UniBanco'
	WHEN pag_banco='14' THEN 'Banco Solidario'
	WHEN pag_banco='15' THEN 'Banco de Loja'
	WHEN pag_banco='16' THEN 'Banco Territorial'
	WHEN pag_banco='17' THEN 'Banco Coopnacional'
	WHEN pag_banco='18' THEN 'Banco Amazonas'
	WHEN pag_banco='19' THEN 'Banco Capital'
	WHEN pag_banco='20' THEN 'Banco D-MIRO'
	WHEN pag_banco='21' THEN 'Banco Finca'
	WHEN pag_banco='22' THEN 'Banco Comercial de Manabí'
	WHEN pag_banco='23' THEN 'Banco COFIEC'
	WHEN pag_banco='24' THEN 'Banco del Litoral'
	WHEN pag_banco='25' THEN 'Banco Delbank'
	WHEN pag_banco='26' THEN 'Banco Sudamericano'
	ELSE 'Error'
END as banco
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and (c.fac_estado_aut<>'ANULADO' or c.fac_estado_aut is null)
and pag_forma='1'
and c.emi_id=$e
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and c.vnd_id ='$v'
and pf.pag_banco<>'0'
and pf.pag_estado=0
group by pag_banco

");
        }
    }

    ///////////////******INVENTARIOS***************************/////////////

    function lista_inventario_producto($h, $fml, $txt, $linea, $talla, $fml2, $val, $txt1) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
inv.pro_id,
inv.pro_tbl,
pc.pro_a,
pc.pro_b,
pc.pro_ac,
pc.pro_ab,
pc.pro_t,
pr.pre_precio
from 
erp_consulta_inv inv
join
erp_pro_precios pr on(
inv.pro_id=pr.pro_id
and inv.pro_tbl=pr.pro_tabla 
and inv.con_fecha='$h'
and inv.mvt_cant>0
and inv.pro_tbl=1
)
join erp_productos pc on(pc.id=inv.pro_id)
join erp_productos_set pcs on(
pc.ids=pcs.ids
$fml    
$txt
$linea
$talla
$val
)
group by 
pcs.ids,
pcs.pro_tipo,
inv.pro_id,
inv.pro_tbl,
pc.pro_a,
pc.pro_b,
pc.pro_ac,
pc.pro_ab,
pc.pro_t,
pr.pre_precio
union
select 
0 as ids,
'INDUSTRIAL' AS familia,
inv.pro_id,
inv.pro_tbl,
pc.pro_codigo,
pc.pro_descripcion,
'-' as pro_ac,
'' AS pro_ab,
'' AS pro_t,
pr.pre_precio
from 
erp_consulta_inv inv
join
erp_pro_precios pr on(
inv.pro_id=pr.pro_id
and inv.pro_tbl=pr.pro_tabla 
and inv.con_fecha='$h'
and inv.mvt_cant>0
and inv.pro_tbl=0
)
join erp_i_productos pc on(
pc.pro_id=inv.pro_id
$fml2
$txt1    
)
group by 
inv.pro_id,
inv.pro_tbl,
pc.pro_codigo,
pc.pro_descripcion,
pr.pre_precio
order by familia,pro_b

");
        }
    }

    function lista_inv_cant_prod($f, $p, $t, $pe) {
        if ($this->con->Conectar() == true) {
            return pg_query("
select 
inv.mvt_cant,
pr.pre_precio,
(inv.mvt_cant*pr.pre_precio) as valor 
from 
erp_consulta_inv inv
join erp_pro_precios pr
on(
pr.pro_id=inv.pro_id
and inv.con_fecha='2015-05-07'
and inv.pro_id=$p
and inv.pro_tbl=$t
and pr.pre_precio>0
and inv.cod_punto_emision=$pe)
limit 1

    ");
        }
    }

/////LISTAS INVENTARIOS 


    function lista_inv_costo_local($e, $tx1, $tx2, $ln, $tll) {
        if ($this->con->Conectar() == true) {
            return pg_query("
SELECT 
pcs.ids,
split_part(pcs.pro_tipo, '&', 10) AS familia,
pro.pro_a,
pro.pro_ac,
pro.pro_b,
pro.pro_ab,
pro.pro_t,
inv.mvt_cant,
(select pre_precio from erp_pro_precios pr where pr.pro_id=inv.pro_id and pr.pro_tabla=1 limit 1) as precio
FROM 
erp_i_movpt_total inv,
erp_productos pro,
erp_productos_set pcs
WHERE inv.pro_tbl=1
AND inv.pro_id=pro.id
AND pcs.ids=pro.ids 
AND inv.cod_punto_emision=$e
AND inv.mvt_cant >0
$tx1
$ln
$tll    
UNION
SELECT 
0,
'INDUSTRIAL',
pro.pro_codigo,
'0',
pro.pro_descripcion,
'0',
'0',
inv.mvt_cant,
(select pre_precio from erp_pro_precios pr where pr.pro_id=inv.pro_id and pr.pro_tabla=0 limit 1) as precio
FROM 
erp_i_movpt_total inv,
erp_i_productos pro
WHERE inv.pro_tbl=0
AND inv.pro_id=pro.pro_id
AND inv.cod_punto_emision=$e
AND inv.mvt_cant >0
$tx2
order by familia

    ");
        }
    }

    function lista_todos_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select pro.pro_a,pro.pro_b,pro.pro_ac, 1 as tbl, id from erp_productos pro
                             union all
                             select pro.pro_codigo,pro.pro_descripcion,'0', 0 as tbl, pro_id from erp_i_productos pro");
        }
    }

    function lista_precios_producto($pro, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pro_precios where pro_id=$pro and pro_tabla=$tbl order by pre_id ");
        }
    }

    function elimina_duplicados_precios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                delete from erp_descuentos where pre_id=$id;
                delete from erp_pro_precios where pre_id=$id;
                ");
        }
    }

///NUEVAS FUNCIONES DE REPORTES DE VENTAS POR PRODUCTOS


    function lista_reporte_ventas_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as familia,
                            split_part(prod,'&',2) as cod,
                            split_part(prod,'&',3) as descr,
                            split_part(prod,'&',4) as lote,
                            to_char(cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as precio,
                            split_part(prod,'&',6) as ids,
                            split_part(prod,'&',7) as linea,
                            split_part(prod,'&',8) as talla,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v3,
                            loc4,
                            to_char(cast(loc4 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v4,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v13,
                            loc14,
                            to_char(cast(loc14 as double precision)*cast(split_part(prod,'&',5) as double precision),'99,999,990.00') as v14
                            from ventas_producto where prod is not null ");
        }
    }

    function lista_reporte_ventas_productos_buscador($txt, $fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as id,
                            split_part(prod,'&',2) as cod,
                            split_part(prod,'&',3) as descr,
                            split_part(prod,'&',5) as ids,
                            to_char(cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as precio,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v3,
                            loc4,
                            to_char(cast(loc4 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v4,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v13,
                            loc14,
                            to_char(cast(loc14 as double precision)*cast(split_part(prod,'&',4) as double precision),'99,999,990.00') as v14
                            from ventas_producto where prod is not null
                            $txt order by descr
                             ");
        }
    }

    ///////////////////////////////////////////////// ventas por productos

    function lista_reporte_productos_locales($txt, $d, $h) {
        if ($this->con->Conectar() == TRUE) {
            return pg_query("select 
                                    split_part(prod,'&',1) as id,
                                    split_part(prod,'&',2) as cod,
                                    split_part(prod,'&',3) as descr,
                                    split_part(prod,'&',4) as fecha,
                                    to_char(cast(split_part(loc1,'&',1) as double precision),'99,999,990.00') as loc1,
                                    to_char(cast(split_part(loc1,'&',2) as double precision),'99,999,990.00') as v1,
                                    to_char(cast(split_part(loc2,'&',1) as double precision),'99,999,990.00') as loc2,
                                    to_char(cast(split_part(loc2,'&',2) as double precision),'99,999,990.00') as v2,
                                    to_char(cast(split_part(loc3,'&',1) as double precision),'99,999,990.00') as loc3,
                                    to_char(cast(split_part(loc3,'&',2) as double precision),'99,999,990.00') as v3,
                                    to_char(cast(split_part(loc4,'&',1) as double precision),'99,999,990.00') as loc4,
                                    to_char(cast(split_part(loc4,'&',2) as double precision),'99,999,990.00') as v4,
                                    to_char(cast(split_part(loc5,'&',1) as double precision),'99,999,990.00') as loc5,
                                    to_char(cast(split_part(loc5,'&',2) as double precision),'99,999,990.00') as v5,
                                    to_char(cast(split_part(loc6,'&',1) as double precision),'99,999,990.00') as loc6,
                                    to_char(cast(split_part(loc6,'&',2) as double precision),'99,999,990.00') as v6,
                                    to_char(cast(split_part(loc7,'&',1) as double precision),'99,999,990.00') as loc7,
                                    to_char(cast(split_part(loc7,'&',2) as double precision),'99,999,990.00') as v7,
                                    to_char(cast(split_part(loc8,'&',1) as double precision),'99,999,990.00') as loc8,
                                    to_char(cast(split_part(loc8,'&',2) as double precision),'99,999,990.00') as v8,
                                    to_char(cast(split_part(loc9,'&',1) as double precision),'99,999,990.00') as loc9,
                                    to_char(cast(split_part(loc9,'&',2) as double precision),'99,999,990.00') as v9,
                                    to_char(cast(split_part(loc10,'&',1) as double precision),'99,999,990.00') as loc10,
                                    to_char(cast(split_part(loc10,'&',2) as double precision),'99,999,990.00') as v10,
                                    to_char(cast(split_part(loc11,'&',1) as double precision),'99,999,990.00') as loc11,
                                    to_char(cast(split_part(loc11,'&',2) as double precision),'99,999,990.00') as v11,
                                    to_char(cast(split_part(loc12,'&',1) as double precision),'99,999,990.00') as loc12,
                                    to_char(cast(split_part(loc12,'&',2) as double precision),'99,999,990.00') as v12,
                                    to_char(cast(split_part(loc13,'&',1) as double precision),'99,999,990.00') as loc13,
                                    to_char(cast(split_part(loc13,'&',2) as double precision),'99,999,990.00') as v13,
                                    to_char(cast(split_part(loc14,'&',1) as double precision),'99,999,990.00') as loc14,
                                    to_char(cast(split_part(loc14,'&',2) as double precision),'99,999,990.00') as v14
       
                                from ventas_por_producto where prod is not null  
                                and split_part(prod,'&',4)>='$d' and split_part(prod,'&',4)<='$h'
                                $txt
                                $fml");
        }
    }
    
    function lista_reporte_productos_locales_agrup($txt, $d, $h) {
        if ($this->con->Conectar() == TRUE) {
            return pg_query("select 
                                    split_part(prod,'&',1) as id,
                                    split_part(prod,'&',2) as cod,
                                    split_part(prod,'&',3) as descr,
                                    split_part(prod,'&',5) as ids,
                                    to_char((sum(cast(split_part(loc1,'&',1) as double precision))),'99,999,990.00') as loc1,
                                    to_char((sum(cast(split_part(loc1,'&',2) as double precision))),'99,999,990.00') as v1,
                                    to_char((sum(cast(split_part(loc2,'&',1) as double precision))),'99,999,990.00') as loc2,
                                    to_char((sum(cast(split_part(loc2,'&',2) as double precision))),'99,999,990.00') as v2,
                                    to_char((sum(cast(split_part(loc3,'&',1) as double precision))),'99,999,990.00') as loc3,
                                    to_char((sum(cast(split_part(loc3,'&',2) as double precision))),'99,999,990.00') as v3,
                                    to_char((sum(cast(split_part(loc4,'&',1) as double precision))),'99,999,990.00') as loc4,
                                    to_char((sum(cast(split_part(loc4,'&',2) as double precision))),'99,999,990.00') as v4,
                                    to_char((sum(cast(split_part(loc5,'&',1) as double precision))),'99,999,990.00') as loc5,
                                    to_char((sum(cast(split_part(loc5,'&',2) as double precision))),'99,999,990.00') as v5,
                                    to_char((sum(cast(split_part(loc6,'&',1) as double precision))),'99,999,990.00') as loc6,
                                    to_char((sum(cast(split_part(loc6,'&',2) as double precision))),'99,999,990.00') as v6,
                                    to_char((sum(cast(split_part(loc7,'&',1) as double precision))),'99,999,990.00') as loc7,
                                    to_char((sum(cast(split_part(loc7,'&',2) as double precision))),'99,999,990.00') as v7,
                                    to_char((sum(cast(split_part(loc8,'&',1) as double precision))),'99,999,990.00') as loc8,
                                    to_char((sum(cast(split_part(loc8,'&',2) as double precision))),'99,999,990.00') as v8,
                                    to_char((sum(cast(split_part(loc9,'&',1) as double precision))),'99,999,990.00') as loc9,
                                    to_char((sum(cast(split_part(loc9,'&',2) as double precision))),'99,999,990.00') as v9,
                                    to_char((sum(cast(split_part(loc10,'&',1) as double precision))),'99,999,990.00') as loc10,
                                    to_char((sum(cast(split_part(loc10,'&',2) as double precision))),'99,999,990.00') as v10,
                                    to_char((sum(cast(split_part(loc11,'&',1) as double precision))),'99,999,990.00') as loc11,
                                    to_char((sum(cast(split_part(loc11,'&',2) as double precision))),'99,999,990.00') as v11,
                                    to_char((sum(cast(split_part(loc12,'&',1) as double precision))),'99,999,990.00') as loc12,
                                    to_char((sum(cast(split_part(loc12,'&',2) as double precision))),'99,999,990.00') as v12,
                                    to_char((sum(cast(split_part(loc13,'&',1) as double precision))),'99,999,990.00') as loc13,
                                    to_char((sum(cast(split_part(loc13,'&',2) as double precision))),'99,999,990.00') as v13,
                                    to_char((sum(cast(split_part(loc14,'&',1) as double precision))),'99,999,990.00') as loc14,
                                    to_char((sum(cast(split_part(loc14,'&',2) as double precision))),'99,999,990.00') as v14
                                    
                                from ventas_por_producto where prod is not null  
                                and split_part(prod,'&',4)>='$d' and split_part(prod,'&',4)<='$h' $txt group by id,cod,descr,ids order by descr
                                
                                ");
        }
    }

    function lista_vendedores_fac_not($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor from erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$e and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)                             
                             union 
                             select vnd_id as vendedor from erp_nota_credito c where ncr_fecha_emision between '$d' and '$h' and emi_id=$e and (ncr_estado_aut<>'ANULADO' or  ncr_estado_aut is null)
                             group by vnd_id order by vendedor
                            ");
        }
    }

    function lista_vendedores_factura($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor from erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$e  
                                and (fac_estado_aut<>'ANULADO' OR fac_estado_aut is null)                          
                                                         group by vnd_id order by vendedor
                            ");
        }
    }

    function lista_vendedores($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_vendedor where vnd_id=$id ");
        }
    }

    
      function lista_reporte_ventas_diarias_buscador($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(fecha, '&', 1) as fecha,
                                to_char(sum(cast(loc1 as double precision)),'99,999,990.00') as loc1,
                                to_char(sum(cast(loc2 as double precision)),'99,999,990.00') as loc2,
                                to_char(sum(cast(loc3 as double precision)),'99,999,990.00') as loc3,
                                to_char(sum(cast(loc4 as double precision)),'99,999,990.00') as loc4,
                                to_char(sum(cast(loc5 as double precision)),'99,999,990.00') as loc5,
                                to_char(sum(cast(loc6 as double precision)),'99,999,990.00') as loc6,
                                to_char(sum(cast(loc7 as double precision)),'99,999,990.00') as loc7,
                                to_char(sum(cast(loc8 as double precision)),'99,999,990.00') as loc8,
                                to_char(sum(cast(loc9 as double precision)),'99,999,990.00') as loc9,
                                to_char(sum(cast(loc10 as double precision)),'99,999,990.00') as loc10,
                                to_char(sum(cast(loc11 as double precision)),'99,999,990.00') as loc11,
                                to_char(sum(cast(loc12 as double precision)),'99,999,990.00') as loc12,
                                to_char(sum(cast(loc13 as double precision)),'99,999,990.00') as loc13,
                                to_char(sum(cast(loc14 as double precision)),'99,999,990.00') as loc14
                              from ventas_netas2 where split_part(fecha, '&', 1) between '$desde' and '$hasta' group by  split_part(fecha, '&', 1) order by split_part(fecha, '&', 1)
                                
                             ");
        }
    }
    // se modifico al query funtcion lista_reporte_costos_productos_buscador
    /*
     * 
     * select split_part(prod,'&',1) as cod,
                            split_part(prod,'&',2) as descr,
                            split_part(prod,'&',6) as cod_aux,
                            to_char(cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as precio,
                            split_part(prod,'&',4) as id,
                            loc1,
                            to_char(cast(loc1 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v1,
                            loc2,
                            to_char(cast(loc2 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v2,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v3,
                            loc3,
                            to_char(cast(loc3 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v3,
                            loc5,
                            to_char(cast(loc5 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v5,
                            loc6,
                            to_char(cast(loc6 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v6,
                            loc7,
                            to_char(cast(loc7 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v7,
                            loc8,
                            to_char(cast(loc8 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v8,
                            loc9,
                            to_char(cast(loc9 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v9,
                            loc10,
                            to_char(cast(loc10 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v10,
                            loc11,
                            to_char(cast(loc11 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v11,
                            loc12,
                            to_char(cast(loc12 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v12,
                            loc13,
                            to_char(cast(loc13 as double precision)*cast(split_part(prod,'&',3) as double precision),'99,999,990.00') as v13
                            
                            from costos_producto where prod is not null
     */
    
    function lista_reporte_costos_productos_buscador($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(p.prod,'&',1) as cod,
                            split_part(p.prod,'&',2) as descr,
                            split_part(p.prod,'&',6) as cod_aux,
                            to_char(cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as precio,
                            split_part(p.prod,'&',4) as id,
                            p.loc1,s.loc1 as c1,
                            to_char(cast(p.loc1 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v1,
                            p.loc2,s.loc2 as c2,
                            to_char(cast(p.loc2 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v2,
                            p.loc3,s.loc3 as c3,
                            to_char(cast(p.loc3 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v3,
                            p.loc4,s.loc4 as c4,
                            to_char(cast(p.loc4 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v4,
                            p.loc5,s.loc5 as c5,
                            to_char(cast(p.loc5 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v5,
                            p.loc6,s.loc6 as c6,
                            to_char(cast(p.loc6 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v6,
                            p.loc7,s.loc7 as c7,
                            to_char(cast(p.loc7 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v7,
                            p.loc8,s.loc8 as c8,
                            to_char(cast(p.loc8 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v8,
                            p.loc9,s.loc9 as c9,
                            to_char(cast(p.loc9 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v9,
                            p.loc10,s.loc10 as c10,
                            to_char(cast(p.loc10 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v10,
                            p.loc11,s.loc11 as c11,
                            to_char(cast(p.loc11 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v11,
                            p.loc12,s.loc12 as c12,
                            to_char(cast(p.loc12 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v12,
                            p.loc13,s.loc13 as c13,
                            to_char(cast(p.loc13 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v13,
                            p.loc14,s.loc14 as c14,
                            to_char(cast(p.loc13 as double precision)*cast(split_part(p.prod,'&',3) as double precision),'99,999,990.00') as v14
                            from costos_producto p, costos_producto_s s where p.prod = s.prod and p.prod is not null
                            $txt
                             ");
        }
    }

     function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set WHERE ids<>79 and ids<>80 order by split_part(mp_tipo,'&',10) desc");
        }
    }
    
    function lista_un_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_id='$id'");
        }
    }
    
     function lista_reporte_cheques($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE chq_fecha between '$d' and '$h' and chq_tipo_doc>=1 and chq_tipo_doc<=2 and chq_estado<2 ORDER BY chq_fecha");
        }
    }

    function lista_clientes($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_raz_social FROM erp_i_cliente WHERE cli_id=$id");
        }
    }

    function lista_reporte_pagos_cheques($d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_ctasxcobrar WHERE cta_fecha between '$d' and '$h' and cta_forma_pago<>'RETENCION' ORDER BY cta_fecha, com_id");
        }
    }

    function lista_facturas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT fac_numero, cli_id FROM erp_factura WHERE fac_id=$id");
        }
    }
}

?>
