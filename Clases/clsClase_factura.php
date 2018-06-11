<?php

include_once 'Conn.php';

class Clase_factura {

    var $con;

    function Clase_factura() {
        $this->con = new Conn();
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_i_cliente c, erp_vendedor v where f.cli_id=c.cli_id and f.vnd_id=v.vnd_id and f.fac_id=$id");
        }
    }

    function lista_una_factura_num($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_numero='$id'");
        }
    }

    function lista_buscador_factura($txt, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura c, erp_emisor e, erp_vendedor v  WHERE c.emi_id=e.emi_id and v.vnd_id=c.vnd_id  $txt AND c.emi_id=$emi order by c.fac_numero");
        }
    }

    function lista_secuencial_documento($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(f.fac_numero,9,9) as secuencial FROM  erp_factura f,erp_emisor e where  f.emi_id=e.emi_id and f.emi_id=$emi order by f.fac_numero desc limit 1");
        }
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

//    function lista_producto_total() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_i_productos where pro_estado=0");
//        }
//    }

    function lista_producto_total($inv, $txt) {///factura
        if ($this->con->Conectar() == true) {
            if ($inv == 0) {
                return pg_query("select * from erp_mp p,erp_i_movpt_total m, erp_tipos t where p.id=m.pro_id and t.tps_id=cast(p.mp_a as integer) and m.mvt_cant>0 and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0') $txt");
            } else {
                return pg_query("select * from erp_mp p, erp_tipos t where t.tps_id=cast(p.mp_a as integer) and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0')");
            }
        }
    }

    function lista_detalle_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$id'");
        }
    }

//    function lista_un_producto_id($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_i_productos where pro_estado=0 and pro_id=$id");
//        }
//    }

    function lista_un_producto_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and id=$id");
        }
    }

    function lista_precio_producto($id, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pro_precios pr, erp_descuentos d where pr.pre_id=d.pre_id and d.ems_id=$emi and pr.pro_id=$id");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin='$data[0]',
            cli_email='$data[1]',
            cli_telefono='$data[2]',
            cli_canton='$data[3]',
            cli_pais='$data[4]',
            cli_parroquia='$data[5]',
                cli_calle_sec='',
                cli_numeracion=''
            WHERE cli_ced_ruc='$id'");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where substr(cli_codigo,1,2)='$tp' order by cli_codigo desc limit 1");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente 
(
  cli_apellidos,
  cli_raz_social,
  cli_fecha,
  cli_estado,
  cli_tipo,
  cli_categoria,
  cli_ced_ruc,
  cli_calle_prin,
  cli_telefono,
  cli_email,
  cli_canton,
  cli_pais,
  cli_codigo,
cli_parroquia
) values ('$data[0]',
    '$data[0]',
'" . date('Y-m-d') . "',
    0,
    0,
    1,
'$data[1]',
'$data[2]',    
'$data[3]',
'$data[4]',
'$data[5]',
'$data[6]',
'$data[7]', 
'$data[8]')");
        }
    }

    function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

    function insert_factura($data, $cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_factura 
(emi_id,
cli_id, 
  vnd_id, 
 ped_id,
 fac_fecha_emision,
 fac_numero, 
 fac_nombre, 
 fac_identificacion, 
 fac_email, 
 fac_direccion, 
 fac_subtotal12, 
 fac_subtotal0, 
 fac_subtotal_ex_iva, 
 fac_subtotal_no_iva, 
 fac_total_descuento, 
 fac_total_ice, 
 fac_total_iva, 
 fac_total_irbpnr, 
 fac_total_propina,
 fac_telefono,
 fac_observaciones,
 fac_total_valor,
 fac_subtotal
) values ($data[0],
'$cli',
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
'$data[18]',
'$data[20]',
'$data[21]',               
'$data[19]',               
'$data[25]')               
                ");
        }
    }

    function insert_detalle_factura($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query($data);
        }
    }

    function insert_movimiento_pt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query($data);
        }
    }

    function elimina_movpt_documento($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt where mov_documento='$num' and trs_id='25'");
        }
    }

    function elimina_detalle_factura($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_det_factura where fac_id='$num'");
        }
    }

    function elimina_factura($nfact) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_factura where fac_id=$nfact");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedor where vnd_nombre='$txt'");
        }
    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

//    function lista_costos_mov($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_i_mov_inv_pt where pro_id=$id order by mov_id desc limit 1");
//        }
//    }

    function lista_costos_mov($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_configuraciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='5'");
        }
    }

    function lista_configuraciones_dec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='2'");
        }
    }

    function lista_configuraciones_empresa() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id=15");
        }
    }

    function lista_no_enviados() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura 
                             where (fac_estado_correo<>'ENVIADO' or fac_estado_correo is null ) 
                             and fac_nombre <>'CONSUMIDOR FINAL'
                             and (char_length(fac_autorizacion)=37 or char_length(fac_autorizacion)=49)  LIMIT 1 ");
        }
    }

    function update_status_mail($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura set fac_estado_correo='$sts' where fac_id=$id ");
        }
    }

    function lista_notcre_cli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE cli_id=$id and chq_tipo_doc='3' AND chq_estado<>2");
        }
    }

    function lista_cheques_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_cheques WHERE chq_id=$id");
        }
    }

    function lista_detalle_pagos($fact, $des) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pagos_factura WHERE com_id='$fact' and pag_estado=0 ORDER BY pag_fecha_v $des  ");
        }
    }

    function insert_cheques($data) {
        if ($this->con->Conectar() == true) {
            return pg_query(" INSERT INTO erp_cheques(
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
                                                      pag_id,
                                                      chq_cuenta,
                                                      pln_id,
                                                      chq_concepto)
                                              VALUES (
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
                                                      '$data[11]',
                                                      '$data[12]',
                                                      '$data[13]',
                                                      '$data[14]',
                                                      '$data[15]',
                                                      '$data[16]'
                                                        )");
        }
    }

    function buscar_cheques($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from erp_cheques where pag_id=$id");
        }
    }

    function insert_ctasxcobrar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxcobrar(
                com_id, 
                cta_fecha, 
                cta_monto, 
                cta_forma_pago, 
                cta_banco,
                pln_id,
                cta_fecha_pago,
                pag_id,
                num_documento,
                cta_concepto,
                asiento,
                chq_id
                )
        VALUES ($data[0],
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
                '$data[11]'
               )");
        }
    }

    function upd_cantidad_cheques($cant, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cheques SET chq_cobro='$cant' where chq_id=$id");
        }
    }

    function lista_asientos_ctas($id, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id='$id' and a.cas_orden_emi='$ord' and c.pln_estado=0");
        }
    }

    function insert_asientos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_asientos_contables(
                                                                con_asiento,
                                                                con_concepto,
                                                                con_documento, 
                                                                con_fecha_emision,
                                                                con_concepto_debe,
                                                                con_concepto_haber,
                                                                con_valor_debe,
                                                                con_valor_haber,
                                                                con_estado,
                                                                mod_id,
                                                                doc_id,
                                                                cli_id)
                                                    VALUES (
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
                                                                '$data[11]')");
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

    function lista_factura_completo($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura $txt order by fac_numero");
        }
    }

    //docuemntos secundarios

    function lista_notcre_factura($fac, $den) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito where ncr_num_comp_modifica='$fac' and ncr_denominacion_comprobante=$den  and (ncr_estado_aut<>'ANULADO' OR ncr_estado_aut is null)");
        }
    }

    function lista_retencion_factura($fac, $den) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_retencion where rgr_num_comp_retiene='$fac' and rgr_denominacion_comp=$den and rgr_estado=1");
        }
    }

    function lista_det_ret($ret) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(drr_valor) from erp_det_reg_retencion where rgr_id=$ret  and drr_tipo_impuesto='IV') as iva, 
                                    (select sum(drr_valor) from erp_det_reg_retencion where rgr_id=$ret  and drr_tipo_impuesto='IR') as renta ");
        }
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor order by emi_nombre_comercial");
        }
    }

    function lista_pagos_fecha_emisor($fecha, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query(" SELECT * FROM crosstab('select 
f.fac_fecha_emision,
cast(p.pag_forma as double precision),
sum(p.pag_cant) 
from erp_factura f,
erp_pagos_factura p 
where p.com_id=f.fac_id
and f.emi_id=$emi
and f.fac_fecha_emision=''$fecha''
and (f.fac_estado_aut<>''ANULADO'' OR f.fac_estado_aut is null)
and p.pag_estado=0
group by f.fac_fecha_emision,p.pag_forma 
order by f.fac_fecha_emision,p.pag_forma
'::text, 'select l from generate_series(1,9) l'::text) crosstab(fecha text, tc text, td text, ch text, ef text, cer text, bon text, ret text, nc text, cre text);
");
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

    function upd_factura_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura set fac_clave_acceso='$clave'  where fac_id=$id ");
        }
    }

    function lista_pagos_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_factura WHERE com_id='$id'");
        }
    }

    function lista_pro_ser_var() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp p, erp_tipos t where t.tps_id=cast(p.mp_a as integer) and p.ids>78 and p.ids<81 and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0')");
        }
    }

    function lista_secuencial_retencion() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT rgr_num_registro as sec FROM  erp_registro_retencion order by rgr_num_registro desc limit 1");
        }
    }

    function insert_reg_retencion($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_registro_retencion(
                                                                cli_id,
                                                                rgr_numero, 
                                                                rgr_nombre, 
                                                                rgr_identificacion,
                                                                rgr_num_comp_retiene, 
                                                                rgr_autorizacion,
                                                                rgr_denominacion_comp,
                                                                rgr_total_valor, 
                                                                rgr_fecha_emision, 
                                                                rgr_num_registro, 
                                                                rgr_fec_autorizacion, 
                                                                rgr_fec_registro, 
                                                                rgr_fec_caducidad,
                                                                fac_id,
                                                                rgr_estado)
                                                         VALUES ('$data[0]',
                                                                '$data[1]',
                                                                '" . strtoupper($data[2]) . "',
                                                                '" . strtoupper($data[3]) . "',  
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
                                                                '$data[14]')");
        }
    }

    function lista_nc_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito where fac_id='$id'");
        }
    }

    function lista_nd_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_debito where fac_id='$id'");
        }
    }

    function lista_ret_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_retencion where fac_id='$id' and rgr_estado<>3");
        }
    }

    function lista_asientos_mod($id, $mod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables where doc_id='$id' and mod_id='$mod'");
        }
    }

    function delete_asientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asientos_contables where con_asiento='$id'");
        }
    }

    function delete_ctasxcobrar($pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_ctasxcobrar where com_id='$pag'");
        }
    }

    function delete_cheques($pag) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_cheques where pag_id='$pag'");
        }
    }

    function lista_un_movimiento_pro($pro, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where pro_id='$pro' and mov_documento='$num' and trs_id=25");
        }
    }

    function lista_un_producto_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and mp_c='$id'");
        }
    }

    function lista_pagos_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pagos_factura where pag_forma='9' and com_id='$id'");
        }
    }

    function lista_ctasxcobrar_pagid($id, $fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_ctasxcobrar where pag_id=$id and com_id='$fac' and cta_estado=0");
        }
    }

    function update_estado_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_factura set fac_estado_aut='ANULADO' where fac_id=$id");
        }
    }

    function update_estado_cobros($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_cheques set chq_estado='3' where pag_id=$id");
        }
    }

    function update_estado_ctasxcobrar($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_ctasxcobrar set cta_estado='1' where com_id=$id");
        }
    }

    function update_estado_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_pagos_factura set pag_estado='1' where com_id=$id");
        }
    }

    function lista_un_movimiento($id, $num, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where pro_id='$pro' and trs_id='$id' and mov_documento='$num'");
        }
    }

    function update_factura($data, $cli, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_factura set 
                                                    emi_id='$data[0]',
                                                    cli_id='$cli', 
                                                    vnd_id='$data[2]',   
                                                    ped_id='$data[3]',  
                                                    fac_fecha_emision='$data[4]',  
                                                    fac_numero='$data[5]',   
                                                    fac_nombre='$data[6]',   
                                                    fac_identificacion='$data[7]',   
                                                    fac_email='$data[8]',   
                                                    fac_direccion='$data[9]',   
                                                    fac_subtotal12='$data[10]',   
                                                    fac_subtotal0='$data[11]',   
                                                    fac_subtotal_ex_iva='$data[12]',   
                                                    fac_subtotal_no_iva='$data[13]',   
                                                    fac_total_descuento='$data[14]',   
                                                    fac_total_ice='$data[15]',   
                                                    fac_total_iva='$data[16]',   
                                                    fac_total_irbpnr='$data[17]', 
                                                    fac_total_propina='$data[18]',
                                                    fac_telefono='$data[20]',
                                                    fac_observaciones='$data[21]',
                                                    fac_total_valor='$data[19]',
                                                    fac_subtotal='$data[25]'
                                                    where fac_id=$id    
                                                    ");
        }
    }

    function lista_factura_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT fac_id as id, fac_numero as numero, fac_fecha_emision as fecha,fac_clave_acceso as clave, fac_estado_aut as estado, fac_autorizacion as autorizacion  FROM erp_factura where fac_identificacion='$ci' and fac_numero='$num' ");
        }
    }

    function lista_nota_credito_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ncr_id as id, ncr_numero as numero, ncr_fecha_emision as fecha,ncr_clave_acceso as clave, ncr_estado_aut as estado, ncr_autorizacion as autorizacion FROM erp_nota_credito where ncr_identificacion='$ci' and ncr_numero='$num' ");
        }
    }

    function lista_nota_debito_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ndb_id as id, ndb_numero as numero, ndb_fecha_emision as fecha,ndb_clave_acceso as clave, ndb_estado_aut as estado, ndb_autorizacion as autorizacion  FROM erp_nota_debito where ndb_identificacion='$ci' and ndb_numero='$num' ");
        }
    }

    function lista_guia_remision_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select gui_id as id, gui_numero as numero, gui_fecha_emision as fecha,gui_clave_acceso as clave, gui_estado_aut as estado, gui_autorizacion as autorizacion  FROM erp_guia_remision where gui_identificacion='$ci' and gui_numero='$num'");
        }
    }

    function lista_retencion_clave($ci, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ret_id as id, ret_numero as numero, ret_fecha_emision as fecha, ret_clave_acceso as clave, ret_estado_aut as estado, ret_autorizacion as autorizacion FROM erp_retencion where ret_identificacion='$ci' and ret_numero='$num'");
        }
    }
    
     function lista_clave_acceso($num,$cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT '1' as tipo,fac_id as id, fac_numero as numero, fac_fecha_emision as fecha,fac_clave_acceso as clave, fac_estado_aut as estado, fac_autorizacion as autorizacion  FROM erp_factura where fac_clave_acceso='$num' and fac_identificacion='$cli'
                            union
                            SELECT '4' as tipo,ncr_id as id, ncr_numero as numero, ncr_fecha_emision as fecha,ncr_clave_acceso as clave, ncr_estado_aut as estado, ncr_autorizacion as autorizacion FROM erp_nota_credito where ncr_clave_acceso='$num' and ncr_identificacion='$cli'
                            union
                            SELECT '5' as tipo,ndb_id as id, ndb_numero as numero, ndb_fecha_emision as fecha,ndb_clave_acceso as clave, ndb_estado_aut as estado, ndb_autorizacion as autorizacion  FROM erp_nota_debito where ndb_clave_acceso='$num' and ndb_identificacion='$cli'
                            union
                            select '6' as tipo,gui_id as id, gui_numero as numero, gui_fecha_emision as fecha,gui_clave_acceso as clave, gui_estado_aut as estado, gui_autorizacion as autorizacion  FROM erp_guia_remision where gui_clave_acceso='$num' and gui_identificacion='$cli'
                            union
                            SELECT '7' as tipo,ret_id as id, ret_numero as numero, ret_fecha_emision as fecha, ret_clave_acceso as clave, ret_estado_aut as estado, ret_autorizacion as autorizacion FROM erp_retencion where ret_clave_acceso='$num' and ret_identificacion='$cli'
                            ");
        }
    }
}
