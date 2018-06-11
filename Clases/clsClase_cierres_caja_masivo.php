<?php

include_once 'Conn.php';

class Clase_cierres_caja_masivo {

    var $con;

    function Clase_cierres_caja_masivo() {
        $this->con = new Conn();
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

    function delete_cierre($f) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_cierres WHERE cie_fecha='$f'");
        }
    }

    function delete_cierre_bodega($f, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_cierres WHERE cie_fecha='$f' and cie_punto_emision=$pto");
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
                cie_total_not_credito,
                cie_total_credito
            )
    VALUES ('$data[0]',
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
                                                '$data[11]',
                                                    '$data[12]',
                                                        '$data[13]',
                                                            '$data[14]',
                                                                '$data[15]',
                                                                    '$data[16]',
                                                                        '$data[17]',
                                                                            '$data[18]','$data[19]','$data[20]')");
        }
    }

    function upd_cierre_caja($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cierres SET
            cie_secuencial='$data[0]',
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

    
    
    function lista_bodega($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres c, erp_emisor e where e.emi_cod_punto_emision = c.cie_punto_emision and e.emi_cod_punto_emision='$cod'");
        }
    }

    function lista_un_cierre_punto_fecha($pto, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres where cie_punto_emision=$pto and cie_fecha='$fecha'");
        }
    }

    function lista_cierres_caja($f, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_cierres where cie_fecha='$f' and cie_punto_emision=$pto");
        }
    }

    ///****************nuevo cierre************


    function lista_cierrres($fec, $vend, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cierres c, erp_vendedor v  where c.cie_usuario=cast(v.vnd_id as varchar) and c.cie_fecha='$fec' and c.cie_usuario='$vend' and c.cie_punto_emision='$pto'");
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
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

/////nuevas tablas///

    function lista_facturas_vendedor($f) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor, emi_id from erp_factura
where fac_fecha_emision='$f' 
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
and vnd_id!='0' group by vendedor, emi_id
UNION
select f.vnd_id as vendedor, f.emi_id from erp_factura f
where exists (select * from erp_nota_credito nc  where nc.fac_id=f.fac_id and nc.ncr_fecha_emision='$f' and (ncr_estado_aut<>'ANULADO' or ncr_estado_aut is null) ) and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)");
        }
    }

    function lista_fechaemi_factura($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select count(*) as nfac from erp_factura where fac_fecha_emision='$f'  and emi_id=$pto and vnd_id='$vend' and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)");
        }
    }

    function lista_cantidad_productos($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dfc_cantidad)as suma_cantidad, sum(dfc_precio_total) as suma_nota_credito from erp_factura c,erp_det_factura dc 
where dc.fac_id= c.fac_id
and fac_fecha_emision='$f' 
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)    
and emi_id=$pto
and vnd_id='$vend'");
        }
    }

    function lista_total_subtotal($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(fac_subtotal) as suma_subtotal, sum(fac_total_descuento) as suma_descuento, sum(fac_total_iva) as suma_iva, sum(fac_total_valor) as suma_total_valor
from erp_factura
where fac_fecha_emision ='$f' 
and emi_id=$pto
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
and vnd_id='$vend'");
        }
    }

    function lista_total_notacredito($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(nc.nrc_total_valor) as suma_total_valor_nc from erp_nota_credito nc
where nc.ncr_fecha_emision='$f'
and nc.emi_id=$pto
and (ncr_estado_aut<>'ANULADO' or ncr_estado_aut is null)
and exists (select * from erp_factura f where nc.fac_id=f.fac_id and f.vnd_id='$vend' and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null))");
        }
    }

    function lista_formas_pago($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(pg.pag_cant) as  tarjeta_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='1'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  tarjeta_debito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='2'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  cheque from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='3'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  efectivo from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='4'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  certificados from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='5'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  bonos from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='6'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  retencion from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='7'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id=$pto and vnd_id='$vend'), 
(select sum(pg.pag_cant) as  nota_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='8'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),
(select sum(pg.pag_cant) as  credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='9'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend')");
        }
    }

    function lista_facturas_vendedor_bodega($f, $pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select vnd_id as vendedor, emi_id from erp_factura
where fac_fecha_emision='$f' 
and emi_id=$pto
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
and vnd_id!='0' group by vnd_id, emi_id
UNION
select f.vnd_id as vendedor, f.emi_id from erp_factura f
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
where exists (select * from erp_nota_credito nc  where nc.fac_id=f.fac_id and nc.ncr_fecha_emision='$f' and emi_id=$pto and (ncr_estado_aut<>'ANULADO' or ncr_estado_aut is null))");
        }
    }
    
     function lista_ultimo_secuencial($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_cierres where cie_punto_emision=$pto order by cie_secuencial desc limit 1");
        }
    }

     function delete_asiento($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables WHERE con_documento='$sec' and con_concepto='CIERRE CAJA'");
        }
    }
    
    function lista_fechaemi_factura_bodega($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select count(*) as nfac from erp_factura where fac_fecha_emision = '$f' and emi_id=$pto and vnd_id='$vend' and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)");
        }
    }

    function lista_cantidad_productos_bodega($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dfc_cantidad)as suma_cantidad, sum(dfc_precio_total) as suma_nota_credito from erp_factura c,erp_det_factura dc 
where dc.fac_id= c.fac_id
and fac_fecha_emision='$f' 
and emi_id = $pto 
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
and vnd_id='$vend' ");
        }
    }
    
    function lista_total_subtotal_bodega($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(fac_subtotal12) as suma_subtotal, sum(fac_total_descuento) as suma_descuento, sum(fac_total_iva) as suma_iva, sum(fac_total_valor) as suma_total_valor
from erp_factura
where fac_fecha_emision ='$f'
and emi_id =$pto
and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)
and vnd_id='$vend'");
        }
    }
    
    function lista_formas_pago_bodega($f, $pto, $vend) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(pg.pag_cant) as  tarjeta_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='1'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto  and vnd_id='$vend'),
(select sum(pg.pag_cant) as  tarjeta_debito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='2'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  cheque from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='3'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  efectivo from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='4'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  certificados from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='5'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  bonos from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='6'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'),
(select sum(pg.pag_cant) as  retencion from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='7'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = $pto and vnd_id='$vend'), 
(select sum(pg.pag_cant) as  nota_credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='8'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend'),  
(select sum(pg.pag_cant) as  credito from erp_factura c, erp_pagos_factura pg
where pg.com_id=c.fac_id
and pg.pag_forma='9'
and pag_estado=0
and c.fac_fecha_emision='$f' and emi_id = '$pto' and vnd_id='$vend')");
        }
    }

}

?>
