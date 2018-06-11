<?php

include_once 'Conn.php';

class Clase_cierres {

    var $con;

    function Clase_cierres() {
        $this->con = new Conn();
    }

    function lista_cierrres() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres order by cie_secuencial desc");
        }
    }

    function lista_secuencial_cierre() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres ORDER BY cie_secuencial DESC LIMIT 1");
        }
    }

    function lista_punto_emision() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM emisor ORDER BY cod_punto_emision");
        }
    }

    function insert_cierre($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_cierres(
                cie_secuencial,
                cie_fecha,
                cie_hora,
                cie_usuario,
                cie_punto_emision,
                cie_fac_emitidas,
                cie_productos_facturados,
                cie_subtotal,
                cie_descuento,
                cie_iva,
                cie_total_facturas,
                cie_total_notas_credito,
                cie_total_tarjeta_credito,
                cie_total_tarjeta_debito,
                cie_total_cheque,
                cie_total_efectivo,
                cie_total_certificados,
                cie_total_bonos,
                cie_total_retencion,
                cie_total_not_credito
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[11]','$data[12]','$data[13]','$data[14]','$data[15]','$data[16]','$data[17]','$data[18]','$data[19]')");
        }
    }

    function upd_cierre_caja($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cierres SET
            cie_fecha='$data[1]',
            cie_hora='$data[2]',
            cie_usuario='$data[3]',
            cie_punto_emision='$data[4]',
            cie_fac_emitidas='$data[5]',
            cie_productos_facturados='$data[6]',
            cie_subtotal='$data[7]',
            cie_descuento='$data[8]',
            cie_iva='$data[9]',
            cie_total_facturas='$data[10]',
            cie_total_notas_credito='$data[11]',
            cie_total_tarjeta_credito='$data[12]',
            cie_total_tarjeta_debito='$data[13]',
            cie_total_cheque='$data[14]',
            cie_total_efectivo='$data[15]',
            cie_total_certificados='$data[16]',
            cie_total_bonos='$data[17]',
            cie_total_retencion='$data[18]',
            cie_total_not_credito='$data[19]'
                
            WHERE cie_id='$id'");
        }
    }

    function upd_totales_cierres($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cierres SET
            
        
            cie_camb_nc='$data[0]',
            cie_camb_tc='$data[1]',
            cie_camb_cheque='$data[2]',
            cie_camb_efectivo='$data[3]',
            cie_camb_certif='$data[4]',
            cie_camb_bonos='$data[5]',
            cie_camb_ret='$data[6]',
            cie_camb_not_cre='$data[7]'
                
            WHERE cie_id='$id'");
        }
    }

    function lista_bodega($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres c, emisor e where e.cod_punto_emision = c.cie_punto_emision and e.cod_punto_emision='$cod'");
        }
    }

    function lista_un_cierre_punto_fecha($pto, $fecha, $vend) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres where cie_punto_emision=$pto and cie_fecha='$fecha' and cie_usuario='$vend'");
        }
    }

    function lista_ultimo_secuencial($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_cierres where cie_punto_emision=$pto order by cie_secuencial desc limit 1");
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_asientos_contables(
                con_asiento,
                con_concepto,
                con_documento,
                con_fecha_emision,
                con_concepto_debe,
                con_concepto_haber,
                con_valor_debe,
                con_valor_haber,
                con_estado
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]',0)");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asiento() {
        if ($this->con->Conectar() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = 'AS'.substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function delete_asiento($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_documento='$sec' and con_concepto='CIERRE CAJA'");
        }
    }

    function delete_cierre_bodega($f, $vend, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_cierres WHERE cie_fecha='$f' and cie_usuario='$vend' and cie_punto_emision=$pto");
        }
    }

    function lista_vendedor($usu) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_users u, erp_vendedores v WHERE upper(u.usu_person)=v.vnd_nombre and u.usu_person='$usu'");
        }
    }

//////////////////////////////////////////////////////////////////////////////// Arqueo de Cierre de Caja








    function lista_arqueo_caja($fec, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_arqueo_caja where arq_fecha_emision='$fec' and arq_punto_emision=$pto");
        }
    }

    ///tablas nuevas////

    function lista_cierres_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres c, erp_vendedores v  $txt order by c.cie_secuencial desc");
        }
    }

    function lista_cierres_caja_todas($from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres c, erp_vendedores v where  c.cie_usuario=cast(v.vnd_id as varchar) and c.cie_fecha between '$from' and '$until' order by c.cie_fecha desc");
        }
    }

    function lista_cierres_caja($from, $until, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres c, erp_vendedores v where  c.cie_usuario=cast(v.vnd_id as varchar) and c.cie_fecha between '$from' and '$until' and c.cie_punto_emision=$pto order by c.cie_secuencial desc");
        }
    }

    function lista_cierres_caja_masivo($from, $until, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres c, erp_vendedores v where  c.cie_usuario=cast(v.vnd_id as varchar) and c.cie_fecha between '$from' and '$until' order by c.cie_secuencial desc");
        }
    }
    
    function lista_locales() { 
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor ORDER BY emi_cod_punto_emision");
        }
    }

    function lista_buscador_arq_caja($d, $h, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_arqueo_caja where arq_fecha_emision between '$d' and '$h' and arq_punto_emision=$pto");
        }
    }

    function lista_num_facturas($fec, $usu, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura  
where fac_fecha_emision='$fec' 
and emi_id<>1 
and emi_id<>10 
and vnd_id='$usu' 
and emi_id='$pto'
UNION
select * from erp_factura f
where f.emi_id<>1 
and f.emi_id<>10 
and vnd_id='$usu'
and exists (select * from erp_nota_credito nc  where nc.fac_id=f.fac_id and nc.ncr_fecha_emision='$fec' 
and nc.emi_id=$pto and nc.ncr_estado_aut<>'ANULADO')");
        }
    }

    function lista_fechaemi_factura($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select count(*) as nfac from erp_factura where fac_fecha_emision = '$f' and emi_id=$pto and vnd_id='$vend'");
        }
    }

    function lista_cantidad_productos($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dfc_cantidad)as suma_cantidad, sum(dfc_precio_total) as suma_nota_credito from erp_factura c,erp_det_factura dc 
where dc.fac_id= c.fac_id
and fac_fecha_emision='$f' and emi_id= '$pto' and vnd_id='$vend'");
        }
    }

    function lista_total_subtotal($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(fac_subtotal12) as suma_subtotal, sum(fac_total_descuento) as suma_descuento, sum(fac_total_iva) as suma_iva, sum(fac_total_valor) as suma_total_valor 
from erp_factura
where fac_fecha_emision = '$f' 
and emi_id= '$pto'
and vnd_id='$vend'");
        }
    }

    function lista_total_notacredito($fec, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(nc.nrc_total_valor) as suma_total_valor_nc from erp_nota_credito nc
where nc.ncr_fecha_emision='$fec'
and nc.emi_id=$pto
and exists (select * from erp_factura f where nc.fac_id=f.fac_id and f.vnd_id='$vend') and nc.ncr_estado_aut<>'ANULADO'");
        }
    }

    function lista_formas_pago($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(pg.pag_cant) as  tarjeta_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='1'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  tarjeta_debito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='2'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  cheque from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='3'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  efectivo from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='4'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  certificados from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='5'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  bonos from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='6'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  retencion from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='7'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'), 
(select sum(pg.pag_cant) as  nota_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=cast(c.fac_id as varchar)
and pg.pag_forma='8'
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend')");
        }
    }

    function lista_vendedores($user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores where upper(vnd_nombre)='$user'");
        }
    }

    function lista_totales_forma_pago($d, $h, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(cie_total_tarjeta_credito) as tarjeta_credito,
                                    sum(cie_total_tarjeta_debito) as tarjeta_debito,
                                    sum(cie_total_cheque) as cheque,
                                    sum(cie_total_efectivo) as efectivo,
                                    sum(cie_total_certificados) as certificados,
                                    sum(cie_total_bonos) as bonos,
                                    sum(cie_total_retencion) as retencion,
                                    sum(cie_total_not_credito) as nota_credito
                            FROM erp_cierres where cie_fecha between '$d' and '$h' and cie_punto_emision='$pto'");
        }
    }

    function lista_fac_desde_hasta($d, $h, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT (SELECT fac_numero as fac_desde FROM  erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$pto order by fac_numero asc limit 1) ,
                                    (SELECT fac_numero as fac_hasta FROM  erp_factura where fac_fecha_emision between '$d' and '$h' and emi_id=$pto order by fac_numero desc limit 1)");
        }
    }

    function lista_secuencial_arqueo($emi) { /// cambiar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(aqr_num_documento,5,9) as secuencial FROM erp_arqueo_caja WHERE substr(aqr_num_documento,1,3)='$emi' order by substr(aqr_num_documento,5,9) desc limit 1");
        }
    }

    //////////////////////////////Cierres Arqueos /////////////////////////////////////////

    function lista_buscador_arqueos($d, $h, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_arqueo_caja where arq_fecha_emision between '$d' and '$h' $txt order by aqr_num_documento desc");
        }
    }

    function lista_un_arqueo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_arqueo_caja where arq_id=$id");
        }
    }

    function lista_tarjetas_de_credito($d, $h, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pf.pag_banco,
CASE 
	WHEN pf.pag_banco='1' THEN 'Banco Pichincha'
	WHEN pf.pag_banco='2' THEN 'Banco del Pacífico'
	WHEN pf.pag_banco='3' THEN 'Banco de Guayaquil'
	WHEN pf.pag_banco='4' THEN 'Produbanco'
	WHEN pf.pag_banco='5' THEN 'Banco Bolivariano'
	WHEN pf.pag_banco='6' THEN 'Banco Internacional'
	WHEN pf.pag_banco='7' THEN 'Banco del Austro'
	WHEN pf.pag_banco='8' THEN 'Banco Promerica'
	WHEN pf.pag_banco='9' THEN 'Banco de Machala'
	WHEN pf.pag_banco='10' THEN 'BGR'
	WHEN pf.pag_banco='11' THEN 'Citibank (Ecuador)'
	WHEN pf.pag_banco='12' THEN 'Banco ProCredit (Ecuador)'
	WHEN pf.pag_banco='13' THEN 'UniBanco'
	WHEN pf.pag_banco='14' THEN 'Banco Solidario'
	WHEN pf.pag_banco='15' THEN 'Banco de Loja'
	WHEN pf.pag_banco='16' THEN 'Banco Territorial'
	WHEN pf.pag_banco='17' THEN 'Banco Coopnacional'
	WHEN pf.pag_banco='18' THEN 'Banco Amazonas'
	WHEN pf.pag_banco='19' THEN 'Banco Capital'
	WHEN pf.pag_banco='20' THEN 'Banco D-MIRO'
	WHEN pf.pag_banco='21' THEN 'Banco Finca'
	WHEN pf.pag_banco='22' THEN 'Banco Comercial de Manabí'
	WHEN pf.pag_banco='23' THEN 'Banco COFIEC'
	WHEN pf.pag_banco='24' THEN 'Banco del Litoral'
	WHEN pf.pag_banco='25' THEN 'Banco Delbank'
	WHEN pf.pag_banco='26' THEN 'Banco Sudamericano'
	ELSE 'Error'
END as banco,
pf.pag_tarjeta,
CASE 
	WHEN pf.pag_tarjeta='1' THEN 'VISA'
	WHEN pf.pag_tarjeta='2' THEN 'MASTER CARD'
	WHEN pf.pag_tarjeta='3' THEN 'AMERICAN EXPRESS'
	WHEN pf.pag_tarjeta='4' THEN 'DINNERS'
	WHEN pf.pag_tarjeta='5' THEN 'DISCOVER'
	ELSE 'Error'
END as tarjeta
from erp_pagos_factura pf, erp_factura c
where pf.com_id=c.fac_id
and pf.pag_forma='1'
and c.emi_id=$pto
and c.fac_fecha_emision >='$d'	
and c.fac_fecha_emision <='$h'
and pf.pag_banco<>'0'
group by pf.pag_banco, pf.pag_tarjeta order by pf.pag_banco");
        }
    }

    function lista_totales_tarjetas($ban, $tar, $d, $h, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(pag_cant) as contado
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='1' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0'),      
                                    (select sum(pag_cant) as tres_meses
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='2' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0'),
                                    (select sum(pag_cant) as seis_meses
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='3' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0'),
                                    (select sum(pag_cant) as nueve_meses
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='4' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0'),
                                    (select sum(pag_cant) as doce_meses
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='5' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0'),
                                    (select sum(pag_cant) as docho_meses
                                     from erp_pagos_factura pf, erp_factura c
                                     where pf.com_id=c.fac_id
                                     and pf.pag_estado=0
                                     and pf.pag_forma='1' and pf.pag_banco='$ban' and pf.pag_tarjeta='$tar' and pf.pag_contado='6' and c.emi_id=$pto and c.fac_fecha_emision >='$d' and c.fac_fecha_emision <='$h' and pf.pag_banco<>'0')
                    ");
        }
    }

    function lista_factura($d, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f where f.fac_fecha_emision ='$d' and f.emi_id=$pto and (f.fac_estado_aut<>'ANULADO' OR f.fac_estado_aut is null) order by f.fac_numero asc");
        }
    }

    function lista_pagos_fac($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura  where com_id='$id' and pag_estado=0 order by pag_id");
        }
    }

    function lista_cheques_pagid($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cheques  where pag_id='$id'");
        }
    }

    function lista_cheques_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cheques  where chq_id='$id'");
        }
    }

    function lista_notcre_cli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE cli_id=$id and chq_tipo_doc='3' AND chq_estado<>2");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function lista_pagos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura  where pag_id='$id'");
        }
    }

    function update_cheques_mto($id, $cob) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques SET chq_cobro='$cob' where chq_id='$id'");
        }
    }

    function update_pagos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pagos_factura SET 
                                                 pag_forma='$data[3]',                          
                                                 pag_banco='$data[4]',                          
                                                 pag_tarjeta='$data[5]',
                                                 pag_cant='$data[7]',                          
                                                 pag_contado='$data[6]',
                                                 chq_numero='$data[1]',
                                                 pag_id_chq='$data[2]'
                                            where pag_id='$data[0]'");
        }
    }

    function insert_pagos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pagos_factura ( 
                                                 com_id,                          
                                                 pag_tipo, 
                                                 pag_porcentage,
                                                 pag_dias,
                                                 pag_valor,
                                                 pag_fecha_v,
                                                 pag_forma,
                                                 pag_banco,
                                                 pag_tarjeta,
                                                 pag_cant,
                                                 pag_contado,
                                                 chq_numero,
                                                 pag_id_chq)
                                                 values(
                                                 '$data[8]',
                                                 '0',
                                                 '0',
                                                 '0',
                                                 '0',
                                                 '$data[9]',
                                                 '$data[3]',
                                                 '$data[4]',
                                                 '$data[5]',
                                                 '$data[7]',
                                                 '$data[6]',
                                                 '$data[1]',
                                                 '$data[2]'
                                                 )
                                           ");
        }
    }

    function lista_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_id='$id'");
        }
    }

    function insert_cheque($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_cheques(
                                                      cli_id,
                                                      chq_nombre,
                                                      chq_banco,
                                                      chq_numero,
                                                      chq_recepcion,
                                                      chq_fecha, 
                                                      chq_monto,
                                                      chq_estado,
                                                      chq_observacion,
                                                      chq_tipo_doc,
                                                      chq_deposito,
                                                      chq_cobro,
                                                      doc_id,
                                                      pag_id)
                                              VALUES (
                                                      '$data[0]',
                                                      '$data[8]',
                                                      '',
                                                      '$data[1]',
                                                      '$data[2]',
                                                      '$data[3]',
                                                      '$data[4]',
                                                      '0',
                                                      '',
                                                      '$data[5]',
                                                      '',
                                                      '$data[6]',
                                                      '0',
                                                      '$data[7]'
                                                        )");
        }
    }

    function update_cheques($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques SET  
                                                      chq_numero='$data[1]',
                                                      chq_recepcion='$data[2]',
                                                      chq_fecha='$data[3]', 
                                                      chq_monto='$data[4]',
                                                      chq_tipo_doc='$data[5]',
                                                      chq_cobro='$data[6]',
                                                      pag_id='$data[7]',
                                                      chq_nombre='$data[8]'
                                                      where chq_id='$id'");
        }
    }

    function lista_ultimo_pago_fac($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura where com_id='$id' order by pag_id desc limit 1");
        }
    }

    function lista_ctaxcobrar_pag_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_ctasxcobrar where pag_id='$id'");
        }
    }

    function update_ctaxcobrar($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_ctasxcobrar set
                                    cta_monto='$data[0]',
                                    cta_forma_pago='$data[1]',
                                    cta_banco='$data[2]',
                                    pln_id='$data[3]',
                                    pag_id='$data[4]',
                                    num_documento='$data[5]',
                                    chq_id='$data[6]'
                                    where pag_id='$id'");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_id='$id' and c.pln_estado=0");
        }
    }
    
    function lista_asientos_ctas_orden($id, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.cas_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id='$id' and a.cas_orden_emi='$ord' and c.pln_estado=0");
        }
    }

    function insert_ctaxcobrar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar (
                                    com_id,
                                    cta_fecha,
                                    cta_monto,
                                    cta_forma_pago,
                                    cta_banco,
                                    pln_id,
                                    pag_id,
                                    cta_fecha_pago,
                                    num_documento,
                                    cta_concepto,
                                    asiento,
                                    chq_id)
                                    values(
                                    '$data[0]',
                                    '$data[1]',
                                    '$data[2]',
                                    '$data[3]',
                                    '$data[4]',
                                    '$data[5]',
                                    '$data[6]',
                                    '$data[7]',
                                    '$data[8]',
                                    '$data[9]',
                                    '$data[10]',
                                    '$data[11]')
                                   ");
        }
    }

    function lista_notas_credito($f, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_fecha_emision='$f' and emi_id=$emi and (ncr_estado_aut<>'ANULADO' or ncr_estado_aut is null)");
        }
    }

    function lista_plan_cuentas() {
        if (
                $this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas p, erp_bancos_y_cajas where p.pln_id=byc_id_cuenta and p.pln_estado='0' ORDER BY p.pln_codigo");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id = $id and pln_estado='0'");
        }
    }

    function update_arqueo_caja($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_arqueo_caja SET 
                    aqr_num_documento='$data[0]',
                    arq_fecha_emision='$data[1]',
                    arq_punto_emision='$data[2]',
                    aqr_fac_desde='$data[3]',
                    aqr_fac_hasta='$data[4]',
                    arq_tot_tcredito='$data[5]',
                    arq_tot_tdebito='$data[6]',
                    arq_tot_cheque='$data[7]',
                    arq_deposito='$data[8]',
                    arq_tot_efectivo='$data[9]',
                    arq_tot_certificados='$data[10]',
                    arq_tot_bonos='$data[11]',
                    arq_tot_retencion='$data[12]',
                    arq_tot_notcredito='$data[13]',
                    arq_tot_cierre='$data[14]',
                    arq_observaciones='" . strtoupper($data[15]) . "',
                    arq_cuenta='$data[16]',
                    pln_id='$data[17]',
                    arq_tot_credito='$data[18]'
                    where arq_id=$id");
        }
    }

    function update_notas_arqueo_caja($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_arqueo_caja SET arq_notas_credito='$data' where arq_id=$id");
        }
    }

    function delete_cheques($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete FROM  erp_cheques where chq_id='$id'");
        }
    }

}

?>
