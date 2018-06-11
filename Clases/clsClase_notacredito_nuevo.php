<?php

include_once 'Conn.php';

class Clase_nota_Credito_nuevo {

    var $con;

    function Clase_nota_Credito_nuevo() {
        $this->con = new Conn();
    }

    function lista_una_factura_nfact($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_numero='$id' and (fac_estado_aut<>'ANULADO' or fac_estado_aut is null)");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura f, erp_vendedor v where f.vnd_id=v.vnd_id and f.fac_id='$id'");
        }
    }

    function lista_det_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$id'");
        }
    }

    function lista_prod_det_factura($id, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$id' and pro_id=$pro");
        }
    }

    function suma_prod_nota_credito($id, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(dc.dnc_cantidad) FROM  erp_det_nota_credito dc, erp_nota_credito n where dc.ncr_id=n.ncr_id and n.fac_id='$id' and dc.pro_id=$pro and (n.ncr_estado_aut<>'ANULADO' or n.ncr_estado_aut is null)");
        }
    }

//    function lista_costos_mov($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_i_mov_inv_pt where pro_id=$id order by mov_id desc limit 1");
//        }
//    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

    function lista_producto_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp p where p.mp_i='0'");
        }
    }

    function lista_un_producto_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where id=$id and mp_i='0'");
        }
    }

    function insert_nota_credito($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_nota_credito(
                            cli_id,
                            emi_id,
                            vnd_id,
                            ncr_numero,
                            ncr_motivo,
                            ncr_fecha_emision,
                            ncr_nombre,
                            ncr_identificacion,
                            ncr_email,
                            ncr_direccion ,
                            ncr_denominacion_comprobante,
                            ncr_num_comp_modifica ,
                            ncr_fecha_emi_comp,
                            ncr_subtotal12,
                            ncr_subtotal0,
                            ncr_subtotal_ex_iva ,
                            ncr_subtotal_no_iva ,
                            ncr_total_descuento ,
                            ncr_total_ice ,
                            ncr_total_iva ,
                            ncr_irbpnr,
                            nrc_telefono ,
                            nrc_total_valor,        
                            ncr_total_propina,        
                            fac_id,
                            trs_id,
                            ncr_subtotal
                            )VALUES(
                            $data[0],
                            $data[1],
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
                            '$data[19]',
                            '$data[20]',
                            '$data[21]',
                            '$data[22]',
                            '$data[23]',
                            '$data[24]',
                            '$data[25]',
                            '$data[26]')");
        }
    }

    function insert_det_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_nota_credito(
                                   pro_id, 
                                   ncr_id, 
                                   dnc_codigo, 
                                   dnc_cod_aux, 
                                   dnc_cantidad, 
                                   dnc_descripcion, 
                                   dnc_precio_unit, 
                                   dnc_porcentaje_descuento, 
                                   dnc_val_descuento,
                                   dnc_precio_total, 
                                   dnc_iva, 
                                   dnc_ice,
                                   dnc_irbpnr,
                                   dnc_p_ice,
                                   dnc_cod_ice,
                                   dnc_p_irbpnr
                                    )VALUES(
                                   '$data[0]',
                                   '$id',
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
                                   '$data[14]',
                                   '$data[15]',
                                   '$data[16]'
                                     )");
        }
    }

    function lista_un_notac_num($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_numero='$fac'");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedor where vnd_nombre='$txt'");
        }
    }

    function insert_movimiento($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tabla,
                mov_val_unit,
                mov_val_tot
                            )
    VALUES (
                    $data[0],
                    $data[1],
                    $data[2],
                    $data[3],
                    '$data[4]',
                    '$data[5]',
                    '" . date('Y-m-d') . "',
                    '" . date("H:i:s") . "',
                    '$data[6]',
                    '$data[7]',
                    '$data[8]',
                    '$data[9]')");
        }
    }

    function lista_buscador_nota_credito($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_nota_credito cr,erp_emisor e,erp_vendedor v where cr.vnd_id=v.vnd_id and cr.emi_id=e.emi_id $txt ORDER BY cr.ncr_numero asc");
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
  cli_codigo,
  cli_telefono,
  cli_email
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
'$data[5]')");
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

    function lista_un_cliente($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_una_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_nota_credito where ncr_id='$id'");
        }
    }

    function lista_detalle_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_nota_credito WHERE ncr_id='$id'");
        }
    }

    function upd_nota_credito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_nota_credito SET
                            cli_id=$data[0],
                            emi_id=$data[1],
                            vnd_id=$data[2],
                            ncr_numero='$data[3]',
                            ncr_motivo='$data[4]',
                            ncr_fecha_emision='$data[5]',
                            ncr_nombre='$data[6]',
                            ncr_identificacion='$data[7]',
                            ncr_email='$data[8]',
                            ncr_direccion='$data[9]',
                            ncr_denominacion_comprobante='$data[10]',
                            ncr_num_comp_modifica='$data[11]',
                            ncr_fecha_emi_comp='$data[12]',
                            ncr_subtotal12='$data[13]',
                            ncr_subtotal0='$data[14]',
                            ncr_subtotal_ex_iva='$data[15]',
                            ncr_subtotal_no_iva='$data[16]',
                            ncr_total_descuento='$data[17]',
                            ncr_total_ice='$data[18]',
                            ncr_total_iva='$data[19]',
                            ncr_irbpnr='$data[20]',
                            nrc_telefono='$data[21]',
                            nrc_total_valor='$data[22]',        
                            ncr_total_propina='$data[23]',        
                            fac_id='$data[24]',
                            trs_id='$data[25]',	            	          	               
                            ncr_subtotal='$data[26]'	            	          	               
                            WHERE ncr_id=$id");
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

    function delete_movimiento($emi, $num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt WHERE mov_documento='$num_secuencial' and bod_id=$emi and (trs_id=12 or trs_id=13)");
        }
    }

    function delete_det_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_det_nota_credito WHERE ncr_id='$id'");
        }
    }

    function delete_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_nota_credito WHERE ncr_id='$id'");
        }
    }

    function lista_producto_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$id'");
        }
    }

    function lista_i_producto_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$cod'");
        }
    }

    function lista_secuencial_nota_credito($bod) {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT * FROM  erp_nota_credito cr, erp_emisor e where cr.emi_id=e.emi_id and e.emi_id=$bod order by cr.ncr_numero desc limit 1");
            }
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_comprobante($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY num_secuencial desc");
        }
    }

    function lista_buscador_facturas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM comprobantes $txt ORDER BY num_secuencial desc");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////          
    function lista_un_comprobante($id, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$id' and tipo_comprobante=1 and cod_punto_emision=$emi");
        }
    }

    function lista_un_notac_factura($fac) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=4 and num_factura_modifica='$fac' ");
        }
    }

    function lista_detalle_factura($num_secuencial) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM detalle_fact_notdeb_notcre WHERE num_camprobante='$num_secuencial' and tipo_comprobante='1'");
        }
    }

    function lista_inventario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and c.cli_id=m.cli_id and p.pro_codigo='$id' ");
        }
    }

    function lista_motivo($num_secuencial, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and mov_documento='$num_secuencial' and bod_id=$emi");
        }
    }

    function lista_una_factura_numdoc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_documento='$id' and tipo_comprobante=1");
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

    function lista_productos_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos ORDER BY pro_b");
        }
    }

    function lista_productos_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos ORDER BY pro_descripcion ");
        }
    }

    function lista_un_producto_noperti($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code'");
        }
    }

    function lista_un_producto_noperti_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id=$code");
        }
    }

    function lista_asientos_ctas($id, $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id='$id' and a.cas_orden_emi='$ord' and c.pln_estado=0");
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
                                                      pag_id)
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
                                                      '$data[13]'
                                                        )");
        }
    }

    function lista_un_producto_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and id=$id");
        }
    }

    function delete_cobros($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_cheques WHERE doc_id=$id and chq_tipo_doc='3'");
        }
    }

    function lista_cheques_ctasxcob($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_cheques ch, erp_ctasxcobrar c where ch.chq_id=c.chq_id and ch.doc_id=$id and c.cta_estado=0 and ch.chq_estado<>3 and chq_tipo_doc=3");
        }
    }

    function update_estado_nota_credito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_nota_credito set ncr_estado_aut='ANULADO' where ncr_id=$id");
        }
    }

    function update_estado_cheques($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_cheques set chq_estado='3' where doc_id=$id and chq_tipo_doc=3");
        }
    }

    function lista_asientos_mod($id, $mod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables where doc_id='$id' and mod_id='$mod'");
        }
    }

    function lista_costos_mov($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
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
///////////////////////////////////////////////////////////////////////////////////////         
}

?>