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

    function suma_cuentas($cod, $fec1, $fec2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT(SELECT sum(con_valor_debe) as debe FROM erp_asientos_contables where con_concepto_debe='$cod' and con_fecha_emision between '$fec1' and '$fec2') as debe,
                                   (SELECT sum(con_valor_haber) as debe FROM erp_asientos_contables where con_concepto_haber='$cod' and con_fecha_emision between '$fec1' and '$fec2') as haber");
        }
    }

///REOPORTES POR LOCALES
    function lista_emisores($val) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from emisor 
                $val
                order by cod_orden");
        }
    }

    function lista_tot_tipo_pago($e, $d, $h, $v, $f) {
        if ($this->con->Conectar() == true) {
            return pg_query("select
sum(pf.pag_cant)
from erp_factura c,erp_pagos_factura pf, erp_vendedor v
where c.vnd_id=v.vnd_id and c.fac_id=pf.com_id
and c.emi_id=$e
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and upper(v.vnd_nombre) ='$v'
and pf.pag_forma='$f'
                ");
        }
    }

    function lista_devoluciones_vendedor($e, $d, $h, $v) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
count(*) as nfact,
sum(c.nrc_total_valor) as tventa,
sum(c.ncr_total_descuento) as desc,
sum(c.ncr_subtotal12) as con_iva,
(sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva)) as sin_iva,
(sum(c.ncr_subtotal12)+(sum(c.ncr_subtotal0)+sum(c.ncr_subtotal_ex_iva)+sum(c.ncr_subtotal_no_iva))) as sbt_neto,
sum(c.ncr_total_ice) as ice,
sum(c.ncr_total_iva) as iva,
sum(c.nrc_total_valor) as tventas
from erp_nota_credito c
where c.emi_id=$e
and c.ncr_fecha_emision >='$d'
and c.ncr_fecha_emision <='$h'
and exists(
select * from erp_factura f , erp_vendedor v
where f.vnd_id=v.vnd_id and f.fac_numero=c.ncr_num_comp_modifica
and f.fac_fecha_emision >='$d'
and f.fac_fecha_emision <='$h'
and upper(trim(v.vnd_nombre))='$v'
)");
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

    function lista_ventas_devoluciones_vendedor($e, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("select 
upper(v.vnd_nombre) as vendedor,
count(*) as nfact,
sum(c.fac_total_valor) as tventa,
sum(c.fac_total_descuento) as desc,
sum(c.fac_subtotal12) as con_iva,
(sum(c.fac_subtotal0)+sum(c.fac_subtotal_ex_iva)+sum(c.fac_subtotal_no_iva)) as sin_iva,
(sum(c.fac_subtotal12)+(sum(c.fac_subtotal0)+sum(c.fac_subtotal_ex_iva)+sum(c.fac_subtotal_no_iva))) as sbt_neto,
sum(c.fac_total_ice) as ice,
sum(c.fac_total_iva) as iva,
sum(c.fac_total_valor) as tventas
from erp_factura c, erp_vendedor v
where c.vnd_id=v.vnd_id and c.emi_id=$e
and c.fac_fecha_emision >='$d'
and c.fac_fecha_emision <='$h'
and v.vnd_nombre!=''
group by v.vnd_id
");
        }
    }

    function lista_reporte_productos($d, $h, $fml, $txt, $val) {
        if ($this->con->Conectar() == true) {
//            return pg_query("
//(select 
//pcs.ids,
//split_part(pcs.pro_tipo, '&', 10) AS familia,
//d.cod_producto,
//d.lote,
//pc.pro_t,
//pc.pro_ab,
//d.descripcion,
//sum(d.cantidad) as cantidad,
//sum(d.precio_total) as valor
//from comprobantes c,
//detalle_fact_notdeb_notcre d, 
//erp_productos pc,
//erp_productos_set pcs
//where d.num_camprobante=replace(c.num_documento,'-','')
//and d.cod_producto=pc.pro_a
//and d.lote=pc.pro_ac
//and pc.ids=pcs.ids
//and c.fecha_emision >=$d
//and c.fecha_emision <=$h
//and c.tipo_comprobante=1
//$fml
//$txt    
//$linea
//$talla
//$val    
//group by 
//pcs.ids,
//familia,
//d.cod_producto,
//d.lote,
//pc.pro_t,
//pc.pro_ab,
//d.descripcion)
//union
//(select 
//'0' AS ids,
//'INDUSTRIALES' AS familia,
//d.cod_producto,
//'' AS lote, 
//'' AS pro_t,
//'' AS pro_ab,
//d.descripcion,
//sum(d.cantidad) as cantidad,
//sum(d.precio_total) as valor
//from comprobantes c,
//detalle_fact_notdeb_notcre d, 
//erp_i_productos pi
//where d.num_camprobante=replace(c.num_documento,'-','')
//and d.cod_producto=pi.pro_codigo
//and c.fecha_emision >=$d
//and c.fecha_emision <=$h
//and c.tipo_comprobante=1
//$fml2
//$txt    
//$val
//group by
//d.cod_producto,
//d.descripcion)
//order by familia
//
//
// ");
            return pg_query("select p.mp_c,p.mp_d,sum(d.cantidad) as cantidad,sum(d.precio_total) as valor
                            from comprobantes c,detalle_fact_notdeb_notcre d,erp_mp p
                            where d.num_camprobante=replace(c.num_documento,'-','')
                            and d.cod_producto=p.mp_c
                            and c.fecha_emision >=$d
                            and c.fecha_emision <=$h
                            and c.tipo_comprobante=1
                            $fml
                            $txt    
                            $val
                            group by d.cod_producto,p.mp_c,p.mp_d order by p.mp_d");
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
//            select 
//sum(d.cantidad) as cantidad,
//sum(d.precio_total) as valor
//from comprobantes c,
//detalle_fact_notdeb_notcre d, 
//erp_i_productos pi
//where d.num_camprobante=replace(c.num_documento,'-','')
//and d.cod_producto=pi.pro_codigo
//and c.fecha_emision >=$d
//and c.fecha_emision <=$h
//and c.tipo_comprobante=1
//and c.cod_punto_emision=$e
//and d.cod_producto='$cod'  
            return pg_query("
select 
sum(d.cantidad) as cantidad,
sum(d.precio_total) as valor
from comprobantes c,
detalle_fact_notdeb_notcre d, 
erp_mp p
where d.num_camprobante=replace(c.num_documento,'-','')
and d.cod_producto=p.mp_c
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
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pf.pag_forma='1'
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and pf.pag_banco='$b'
and pf.pag_tarjeta='$t'
and pf.pag_contado='$fp'
and c.cod_punto_emision=$e
and upper(c.vendedor) ='$v'
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
	ELSE 'Error'
END as targeta
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pag_forma='1'
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and pf.pag_banco='$b'
and pf.pag_tarjeta<>'0'
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
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pf.pag_forma='1'
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and pf.pag_banco='$b'
and pf.pag_tarjeta='$tg'
and pf.pag_contado is not null
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
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pag_forma='1'
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and pag_banco<>'0'
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
from erp_pagos_factura pf, comprobantes c
where pf.com_id=c.num_documento
and pag_forma='1'
and c.cod_punto_emision=$e
and c.fecha_emision >=$d
and c.fecha_emision <=$h
and upper(c.vendedor) ='$v'
and pag_banco<>'0'
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

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set order by split_part(mp_tipo,'&',10)");
        }
    }

}

?>
