<?php

include_once 'Conn.php';

class Clase_ord_pedido_venta {

    var $con;

    function Clase_ord_pedido_venta() {
        $this->con = new Conn();
    }

    function lista_producto_total($inv, $txt) {
        if ($this->con->Conectar() == true) {
//            if ($inv == 0) {
//                return pg_query("select * from erp_mp p,erp_i_movpt_total m,erp_tipos t where t.tps_id=cast(p.mp_a as integer) and p.id=m.pro_id and m.mvt_cant>0 and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0') $txt");
//            } else {
                return pg_query("select * from erp_mp p, erp_tipos t where t.tps_id=cast(p.mp_a as integer) and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0') order by p.mp_c");
//            }
        }
    }

    function lista_detalle_registro_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_ped_venta where ped_id=$id and (det_estado=1 or det_estado=9) ");
        }
    }

    function lista_pagos_registro_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_pedido_venta v, erp_pagos_pedventa p where v.ped_id=p.ped_id and p.ped_id=$id ");
        }
    }

    function lista_un_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_pedido_venta where ped_id=$id ");
        }
    }

    function lista_registro_numero($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_pedido_venta where ped_num_registro='$num' ");
        }
    }

    function lista_registros_completo_pedidos_pendiente() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta where ped_estado='0' and tipo_cliente=0 ORDER BY ped_num_registro");
        }
    }

    function lista_registros_completo_pedidos($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta  where ped_local=$pto and ped_estado='1' ORDER BY ped_num_registro DESC");
        }
    }

    function lista_ultimo_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_pedido_venta order by ped_num_registro desc limit 1");
        }
    }

    function elimina_detalle_pagos_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_pedventa where ped_id=$id; 
                             delete from erp_det_ped_venta where ped_id=$id ");
        }
    }

    function elimina_asientos_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_asientos_contables where con_asiento='$as' ");
        }
    }

    function elimina_registro_detalle_pedido_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_pedventa where ped_id=$id; 
                             delete from erp_det_ped_venta where ped_id=$id;
                             delete from erp_reg_pedido_venta where ped_id=$id    
                                 ");
        }
    }

    function ultimo_asiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function upd_num_asiento($as, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set con_asiento='$as' where reg_id=$id ");
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

    function insert_asiento_mp($val, $doc, $femision, $as = '') {
        if ($this->con->Conectar() == true) {
            if ($as == '') {
                $sec = $this->siguiente_asiento();
            } else {
                $sec = $as;
            }

            $iva = ($val * 0.12);
            $sbt = ($val - $iva);

            return pg_query("INSERT INTO erp_asientos_contables(
            con_asiento,
            con_concepto,
            con_documento,
            con_fecha_emision, 
            con_concepto_debe, 
            con_concepto_haber,
            con_valor_debe, 
            con_valor_haber)
    VALUES ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','1.01.03.01.001','',$sbt,'0'),
           ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','1.01.05.01.010','',$iva,'0'),
           ('$sec','FACTURA COMPRA DE MATERIA PRIMA','$doc','$femision','','2.02.02.01.001','0','$val') ") . '&' . $sec;
        }
    }

    function insert_registro_pedido($data, $id, $tipo) {
        if ($this->con->Conectar() == true) {
            if ($data[24] == '') {
                $cli_id = $id;
            } else {
                $cli_id = $data[24];
            }
            if ($data[25] == '') {
                $cli_tipo = $tipo;
            } else {
                $cli_tipo = $data[25];
            }

            return pg_query("insert into erp_reg_pedido_venta (
            ped_femision,
            ped_num_registro,
            ped_local,
            ped_vendedor, 
            ped_ruc_cc_cliente,
            ped_nom_cliente,
            ped_dir_cliente,
            ped_tel_cliente, 
            ped_email_cliente,
            ped_parroquia_cliente,
            ped_ciu_cliente,
            ped_pais_cliente,
            ped_sbt12,
            ped_sbt0,
            ped_sbt_noiva, 
            ped_sbt_excento,
            ped_sbt,
            ped_tdescuento,
            ped_ice,
            ped_irbpnr, 
            ped_iva12,
            ped_propina,
            ped_total,
            ped_estado,
            ped_observacion,
            cli_id,
            tipo_cliente,
            ped_fecha_hora
            )values(
            '$data[0]',
            '$data[1]',
             $data[2],
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
                '0',
            '$data[23]',
            '$cli_id',
            '$cli_tipo',
            '" . date("Y-m-d") . ' ' . date("H:i:s") . "'
                )
            ");
        }
    }

    function insert_detalle_registro_pedido($data) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_ped_venta(
            det_cod_producto, 
            det_cod_auxiliar, 
            det_descripcion, 
            det_cantidad, 
            det_vunit, 
            det_descuento_porcentaje, 
            det_descuento_moneda, 
            det_total, 
            det_impuesto,
            pro_id,
            det_tab,
            det_unidad,
            det_val_ice,
            det_cod_ice,
            det_irbp,
            det_val_irbp,
            det_p_ice,
            ped_id
            )
    VALUES (
   '$data[0]',    
   '$data[1]',
   '$data[2]',    
   '$data[3]',
    $data[4],
    $data[5],
    $data[6],    
    $data[7],
    $data[8],    
   '$data[9]',
   '$data[10]',
   '$data[11]',
   '$data[12]',
   '$data[13]',
   '$data[14]',
   '$data[15]',
   '$data[16]',
    $data[17])");
        }
    }

    function insert_pagos_registro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pagos_pedventa(
            pag_tipo,
            pag_porcentage,
            pag_dias,
            pag_valor, 
            ped_id )
    VALUES (
    0,
    $data[0],    
    $data[1],
    $data[2],    
       $data[4]  )
");
        }
    }

    function upd_registro_pedido($data, $id, $cli, $tipo) {
        if ($this->con->Conectar() == true) {
            if ($data[24] == '') {
                $cli_id = $cli;
            } else {
                $cli_id = $data[24];
            }
            if ($data[25] == '') {
                $cli_tipo = $tipo;
            } else {
                $cli_tipo = $data[25];
            }

       return pg_query("UPDATE erp_reg_pedido_venta
       SET ped_femision='$data[0]',
       ped_num_registro='$data[1]',
       ped_local='$data[2]', 
       ped_vendedor='$data[3]',
       ped_ruc_cc_cliente='$data[4]',
       ped_nom_cliente='$data[5]', 
       ped_dir_cliente='$data[6]',
       ped_tel_cliente='$data[7]',
       ped_email_cliente='$data[8]',
       ped_parroquia_cliente='$data[9]', 
       ped_ciu_cliente='$data[10]',
       ped_pais_cliente='$data[11]',
       ped_sbt12='$data[12]',
       ped_sbt0='$data[13]', 
       ped_sbt_noiva='$data[14]',
       ped_sbt_excento='$data[15]',
       ped_sbt='$data[16]',
       ped_tdescuento='$data[17]',
       ped_ice='$data[18]', 
       ped_irbpnr='$data[19]',
       ped_iva12='$data[20]',
       ped_propina='$data[21]',
       ped_total='$data[22]',
       ped_estado='0',
       ped_observacion='$data[23]',
       cli_id='$cli_id',
       tipo_cliente='$cli_tipo',
       ped_fecha_hora='" . date("Y-m-d") . ' ' . date("H:i:s") . "'
 WHERE ped_id=$id
     ");
        }
    }

    function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

    function lista_buscador_orden($ord, $cli, $ruc, $estado, $ven) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta $ord $cli $ruc $estado $ven ORDER BY ped_num_registro DESC");
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("
SELECT * FROM  erp_i_orden_produccion p, erp_empresa e, erp_i_productos pr
where pr.emp_id=e.emp_id and p.pro_id=pr.pro_id
and e.emp_id=$emp ORDER BY p.ord_num_orden DESC LIMIT 1");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_empresa where emp_id=$emp");
        }
    }

    function lista_secuencial_plumon($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("
SELECT * FROM  erp_i_orden_produccion_plumon p, erp_empresa e, erp_i_productos pr
where pr.emp_id=e.emp_id and p.pro_id=pr.pro_id
and e.emp_id=$emp ORDER BY p.orp_num_pedido DESC LIMIT 1");
        }
    }

    function lista_un_pedido_venta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta where ped_id=$id");
        }
    }

    function insert_factura_pedido($data) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO comprobantes 
                           (num_secuencial,
                            nombre,
                            identificacion,
                            fecha_emision,
                            num_guia_remision,
                            cod_numerico,
                            tipo_comprobante,
                            subtotal12,
                            subtotal0,
                            subtotal_exento_iva,
                            subtotal_no_objeto_iva,
                            total_descuento,
                            total_ice,
                            total_iva,
                            total_irbpnr,
                            total_propina,
                            total_valor,
                            direccion_cliente,
                            email_cliente,
                            telefono_cliente,
                            cli_ciudad,
                            cli_pais,
                            cod_establecimiento_emisor,
                            cod_punto_emision,
                            cli_parroquia,
                            vendedor,
                            num_documento,
                            observaciones) 
                            values ($data[0],
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
                                   '$data[18]',
                                   '$data[19]',
                                   '$data[20]',
                                   '$data[21]', $data[22], $data[23], '$data[24]','$data[26]','$data[25]','$data[27]')               
                ");
        }
    }

    function lista_un_detalle_pedido_venta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_det_ped_venta WHERE ped_id=$id");
        }
    }

    function insert_detalle_facturaped($data) {
//        print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO detalle_fact_notdeb_notcre(
            num_camprobante,
            cod_producto,
            cod_aux,
            cantidad,
            descripcion, 
            detalle_adicional1,
            detalle_adicional2,
            precio_unitario,
            descuento, 
            precio_total,
            iva,
            ice,
            descuent,
            lote
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',
             $data[3],
            '$data[4]',
            '$data[5]',
            '$data[6]',
             $data[7],
             $data[8],
             $data[9],
            '$data[10]',
            '$data[11]',
            '$data[12]','$data[13]')");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$code'");
        }
    }

    function lista_un_producto_noperti_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_ac='$lote' and (pro_a='$code' or or cast(id AS VARCHAR)= '$code')");
        }
    }

    function lista_un_producto_noperti($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' or cast(id AS VARCHAR)= '$code'");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function insert_movimiento_pt_ped($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
            pro_id,
            trs_id,
            cli_id,
            bod_id,
            mov_documento,
            mov_guia_transporte, 
            mov_num_trans,
            mov_fecha_trans,
            mov_fecha_registro,
            mov_hora_registro, 
            mov_cantidad,
            mov_tranportista,
            mov_fecha_entrega,
            mov_num_factura, 
            mov_pago,
            mov_direccion,
            mov_val_unit,
            mov_descuento,
            mov_iva, 
            mov_flete,
            mov_tabla)
            values(
$data[0],            
$data[1],
$data[2],
$data[3],            
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
'$data[16]','$data[17]','$data[18]','$data[19]', $data[20])
");
        }
    }

    function lista_un_pago_pedido_venta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_pedventa WHERE ped_id=$id");
        }
    }

    function insert_pagos_pedventa($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pagos_factura(
            com_id,
            pag_tipo,
            pag_porcentage,
            pag_dias, 
            pag_valor,
            pag_fecha_v )
    VALUES ('$data[0]',
    1,
   '$data[1]',
    $data[2],    
   '$data[3]',
   '$data[4]'  )
");
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

    function lista_pedido_bodega($em, $cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta  where tipo_cliente=1 and (ped_local=$em or cli_id=$cli ) ORDER BY ped_num_registro DESC");
        }
    }

    function lista_facturas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from comprobantes where ped_id=$id ");
        }
    }

/////funciones para movil

    function lista_buscador_orden_movil($desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta where  ped_femision between '$desde' and '$hasta' ");
        }
    }

    function lista_un_pedido_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta where ped_num_registro='$cod' ");
        }
    }

    /////////// sumas descuento y suma total

    function lista_suma_descuento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(det_descuento_moneda) as suma_descuento FROM erp_reg_pedido_venta p, erp_det_ped_venta d WHERE p.ped_id=d.ped_id and p.ped_id=$id");
        }
    }

    function lista_suma_total($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(det_total) as suma_total FROM erp_reg_pedido_venta p, erp_det_ped_venta d WHERE p.ped_id=d.ped_id and p.ped_id=$id");
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
            return pg_query("SELECT * FROM erp_i_cliente where cli_id='$id' ");
        }
    }

    function lista_un_ruc_cliente($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ruc'");
        }
    }

    function lista_ultimo_registro_ordped_venta() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM erp_sec_ordped_venta order by sec_id desc limit 1");
        }
    }

    function insert_sec_ord_ped_venta($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_sec_ordped_venta(sec_ord_ped_venta) VALUES ('$data')");
        }
    }

    function lista_una_ordped_id($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_sec_ordped_venta where sec_ord_ped_venta='$sec'");
        }
    }

    function upd_sec_ord_ped_venta($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_sec_ordped_venta
                             SET sec_estado='$sts'
                             WHERE sec_id=$id
     ");
        }
    }

    

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote' and pro_estado=0");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_una_bodega($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_pedido_venta where ped_id=$id");
        }
    }

    function lista_ultimo_registro_sec() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_pedido_venta order by ped_num_registro desc limit 1");
        }
    }

    
     function lista_registro_sec($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_pedido_venta where ped_num_registro='$id'");
        }
    }
///// tablas nuevas ///    

    function lista_secuencial_documento($ems) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(fac_numero,9,9) as secuencial FROM  erp_factura where emi_id='$ems' order by fac_numero desc limit 1");
        }
    }

    function sum_entregado($id, $pro, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(d.dfc_cantidad) as suma from erp_factura c, erp_det_factura d where c.fac_id=d.fac_id and c.ped_id=$id and d.pro_id=$pro");
        }
    }

    /////////////////////// ///////////////////////////////////

    function lista_bodegas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor ORDER BY emi_cod_punto_emision");
        }
    }

    function lista_vendedor($usu) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_users u, erp_vendedor v WHERE upper(u.usu_person)=v.vnd_nombre and upper(u.usu_person)='$usu'");
        }
    }

    function lista_vendedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_users u, erp_vendedor v WHERE upper(u.usu_person)=v.vnd_nombre ORDER BY vnd_nombre");
        }
    }

    function lista_un_producto_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and id=$id");
        }
    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }
    
    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_cod_punto_emision='$id'");
        }
    }
    
     function lista_emisor_cod_cli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_emisor where emi_cod_cli='$id'");
        }
    }

     function lista_configuraciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=$id");
        }
    }
    
    function lista_pagos_vencidos($id, $fini, $ffin) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v between '$fini' and '$ffin'");
        }
    }
    
    function lista_pagos_vencidos_m120($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v<'$fec'");
        }
    }
    
    function lista_pag_porvencer($id, $fec){
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(pag_cant) as cantidad, sum(credito) as credito, sum(debito) as debito from pagos where cli_id=$id and pag_fecha_v>'$fec'");
        }
    }

}
?>
