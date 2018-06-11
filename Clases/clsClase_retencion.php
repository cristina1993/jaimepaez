<?php

include_once 'Conn.php';

class Clase_retencion {

    var $con;

    function Clase_retencion() {
        $this->con = new Conn();
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where cli_codigo like '$tp%' order by cli_codigo desc limit 1");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente 
(
  cli_apellidos,
  cli_raz_social,
  cli_nom_comercial,
  cli_fecha,
  cli_estado,
  cli_tipo,
  cli_categoria,
  cli_ced_ruc,
  cli_calle_prin,
  cli_telefono,
  cli_email,
  cli_codigo                       
) values ('$data[0]',
    '$data[0]',
        '$data[0]',
'" . date('Y-m-d') . "',
    0,
    1,
   '$data[6]',
'$data[1]', 
'$data[2]',
'$data[3]',
'$data[4]',
'$data[5]')");
        }
    }

    function lista($emi) {
        if ($this->con->Conectar($emi) == true) {
            if ($emi >= 10) {
                $bd = '0' . $emi;
            } else {
                $bd = '00' . $emi;
            }
            if ($this->con->Conectar() == true) {
                return pg_query("select num_comprobante,num_comp_retenido,tipo_comprobante,fecha_emision,cli_id,com_estado,com_observacion,com_autorizacion,ret_estado_correo,vendedor from detalle_retencion where  substr(num_comprobante,1,3)='$bd' group by num_comprobante,num_comp_retenido,tipo_comprobante,fecha_emision,cli_id,com_estado,com_observacion,com_autorizacion, ret_estado_correo,vendedor order by substr(num_comprobante,7,9) desc ");
            }
        }
    }

    function lista_retencion_fecha($desde, $hasta, $emi) {
        if ($this->con->Conectar($emi) == true) {
            if ($emi >= 10) {
                $bd = '0' . $emi;
            } else {
                $bd = '00' . $emi;
            }
            if ($this->con->Conectar() == true) {
                return pg_query("select num_comprobante,num_comp_retenido,tipo_comprobante,fecha_emision,cli_id,com_estado,com_observacion,com_autorizacion,ret_estado_correo, vendedor from detalle_retencion where  substr(num_comprobante,1,3)='$bd' and fecha_emision between'$desde' and '$hasta' group by num_comprobante,num_comp_retenido,tipo_comprobante,fecha_emision,cli_id,com_estado,com_observacion,com_autorizacion,ret_estado_correo,vendedor order by substr(num_comprobante,7,9) desc ");
            }
        }
    }

    function lista_pdf_retencion($id) {
//        print_r($id);
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  detalle_retencion where num_comprobante='$id'");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_retencion_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  detalle_retencion where num_comp_retenido='$id'");
        }
    }

    function lista_ingreso_num_retencion($id, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial='$id' and tipo_comprobante=1 and cod_punto_emision=$emi ORDER BY nombre");
        }
    }

    function lista_ingreso_porcentaje($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_id='$id' and  ORDER BY por_porcentage");
        }
    }

    function lista_porcentaje() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion  ORDER BY por_porcentage");
        }
    }

    function lista_un_porcentaje($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_id='$id'");
        }
    }

    function lista_datos_porcentaje($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  porcentages_retencion where por_id=$id");
        }
    }

    function delete_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM detalle_retencion WHERE num_comprobante ='$id'");
        }
    }

    function lista_buscar_clientes($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where cli_tipo>'0' and 
                (cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%') 
                                                        Order by cli_nombres");
        }
    }

    function lista_clientes_cedula($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id'");
        }
    }

    function lista_clientes_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_id='$id'");
        }
    }

    function upd_cliente($id, $data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET  cli_telefono='$data[0]',cli_email='$data[1]' where cli_id=$id");
        }
    }

    function upd_cod_cliente($id, $data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET  cli_tipo=2, cli_codigo='$data' where cli_id=$id");
        }
    }

    function lista_retencion_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion order by ret_numero desc ");
        }
    }

    function upd_retencion_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_retencion
                set ret_clave_acceso='$clave'  where ret_id='$id'");
        }
    }

    function upd_retencion_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_retencion 
                set ret_estado_aut='RECIBIDA AUTORIZADO', ret_fec_hora_aut='$fh' , ret_autorizacion='$na'  where ret_clave_acceso='$id' ");
        }
    }

    function suma_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(valor_retenido) as suma from detalle_retencion where num_comprobante='$id'");
        }
    }

    function upd_estado_retencion($id) {

        if ($this->con->Conectar() == true) {
            return pg_query("update erp_retencion set ret_estado_aut='ANULADO' where ret_id='$id'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////     
    ///tablas nuevas//

    function lista_buscador_retencion($txt) {
        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_retencion r, erp_vendedores v where r.vnd_id=v.vnd_id  $txt ORDER BY r.ret_numero");
            return pg_query("select * from erp_retencion r where $txt ORDER BY r.ret_numero");
        }
    }

    function lista_retencion_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_id='$id'");
        }
    }

    function lista_det_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_det_retencion r, porcentages_retencion i where r.por_id=i.por_id and r.ret_id='$id'");
        }
    }

    function lista_vendedor($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM  erp_vendedores where vnd_nombre='$txt'");
        }
    }

    function lista_secuencial_retencion($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where emi_id='$bod' order by ret_numero desc limit 1");
        }
    }

    function insert_retencion($data, $cli_id, $comp) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_retencion(
            cli_id, 
            emi_id, 
            vnd_id, 
            ret_numero, 
            ret_nombre, 
            ret_identificacion, 
            ret_direccion, 
            ret_email, 
            ret_num_comp_retiene,
            ret_denominacion_comp,
            ret_telefono,
            ret_total_valor,
            ret_fecha_emision,
            reg_id
                      )
    VALUES (
            $cli_id,
            '$data[1]',
            '$data[2]',
            '$data[3]',  
            '" . strtoupper($data[4]) . "', 
            '" . strtoupper($data[5]) . "',
            '" . strtoupper($data[6]) . "',
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

    function insert_det_retencion($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_det_retencion(
            ret_id,
            por_id, 
            dtr_ejercicio_fiscal, 
            dtr_base_imponible, 
            dtr_tipo_impuesto, 
            dtr_codigo_impuesto, 
            dtr_procentaje_retencion, 
            dtr_valor
                      )
    VALUES (
            $id,
            '$data[0]',
            '$data[1]',
            '$data[2]',
            '$data[3]',  
            '$data[4]', 
            '$data[5]',
            '$data[6]'
            )");
        }
    }

    function lista_retencion_numero($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_numero='$id'");
        }
    }

    function update_retencion($data, $id, $cli_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_retencion SET
            cli_id=$cli_id, 
            emi_id='$data[1]', 
            vnd_id='$data[2]', 
            ret_numero='$data[3]', 
            ret_nombre='$data[4]', 
            ret_identificacion='$data[5]', 
            ret_direccion='$data[6]', 
            ret_email='$data[7]', 
            ret_num_comp_retiene='$data[8]',
            ret_denominacion_comp='$data[9]',
            ret_telefono='$data[10]',
            ret_total_valor='$data[11]',
            ret_fecha_emision='$data[12]'
            where ret_id=$id      
            ");
        }
    }

    function delete_det_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_det_retencion WHERE ret_id='$id'");
        }
    }

    function lista_secuencial_locales($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT r.ret_numero as secuencial FROM  erp_retencion r, emisor e where r.emi_id=e.cod_punto_emision and r.emi_id=$emi order by r.ret_numero desc limit 1");
        }
    }

    function lista_reg_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos d, erp_i_cliente c where d.reg_ruc_cliente=c.cli_ced_ruc and reg_id='$id'");
        }
    }

    function lista_reg_facturas($tp, $doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_tipo_documento='$tp' and reg_num_documento='$doc' order by reg_id");
        }
    }

    function lista_proveedores($ced) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ced'");
        }
    }

    function lista_id_reg_factura($doc, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_num_documento='$doc' and reg_ruc_cliente='$ruc'");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id=1 and a.cas_orden_emi=$id and c.pln_estado=0");
        }
    }

    function lista_usuario_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_id='$id'");
        }
    }

    function suma_det_retencion($id, $tip) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(dtr_valor) as val from erp_det_retencion where ret_id=$id and dtr_tipo_impuesto='$tip'");
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

    function insert_asiento($data, $sec) {
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
            con_estado,
            mod_id,
            doc_id,
            cli_id)
            VALUES
            ('$sec', 'RETENCION', '$data[1]', '$data[2]', '$data[3]','', '$data[0]', '0',  $data[4],$data[5],$data[6],$data[7])");
        }
    }

    function update_asiento($data, $sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_asientos_contables SET con_valor_haber=(con_valor_haber-$data[0]) WHERE con_concepto_haber='$data[5]' and con_asiento='$sec'");
        }
    }

    function buscar_un_pago_doc($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE p.reg_id='$id' and not exists(SELECT * FROM erp_ctasxpagar c where c.pag_id=p.pag_id) order by p.pag_id");
        }
    }

    function buscar_un_pago_doc1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pagos_documentos p WHERE p.reg_id='$id' order by p.pag_id desc");
        }
    }

    function insert_ctasxpagar($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_ctasxpagar(
                                                        reg_id, 
                                                        ctp_fecha, 
                                                        ctp_monto, 
                                                        ctp_forma_pago, 
                                                        ctp_banco,
                                                        pln_id,
                                                        ctp_fecha_pago,
                                                        pag_id,
                                                        num_documento,
                                                        ctp_concepto,
                                                        asiento,
                                                        chq_id,
                                                        doc_id)
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
                                                        '$data[12]'
                                                        )");
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
                                                                cli_id
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
                                                                         $data[9],
                                                                         $data[10],
                                                                         $data[11]
                                                                        )");
        }
    }

    function lista_reg_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_id='$id'");
        }
    }

    function insert_asiento_anulacion($data, $sec) {
        if ($this->con->Conectar() == true) {
            if ($data[3] != 'no') {
                $iva = ",('$sec','ANULACION DE RETENCION','$data[1]','$data[2]','$data[6]','','$data[3]','0','0')";
            }
            if ($data[4] != 'no') {
                $renta = ",('$sec','ANULACION DE RETENCION','$data[1]','$data[2]','$data[7]','','$data[4]','0','0')";
            }

            return pg_query("INSERT INTO erp_asientos_contables(
            con_asiento,
            con_concepto,
            con_documento,
            con_fecha_emision, 
            con_concepto_debe, 
            con_concepto_haber,
            con_valor_debe, 
            con_valor_haber,
            con_estado)
            VALUES
            ('$sec', 'ANULACION DE RETENCION', '$data[1]', '$data[2]', '','$data[5]','0','$data[0]',  '0')
            $iva
            $renta");
        }
    }

    function update_ctasxpagar($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("Update erp_ctasxpagar set ctp_estado='1' WHERE ctp_id='$doc'");
        }
    }

    function lista_ctasxpagar1($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from erp_ctasxpagar WHERE doc_id='$doc' and ctp_forma_pago='RETENCION'");
        }
    }

    function lista_id_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM porcentages_retencion WHERE por_id=$id");
        }
    }

    function lista_cuenta_contable($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_id=$id");
        }
    }

    function insert_asientos_ret($data, $sec) {
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
            con_estado,
            mod_id,
            doc_id,
            cli_id)
            VALUES
            ('$sec', '$data[4]', '$data[1]', '$data[2]', '','$data[3]', '0', '$data[0]',  '$data[5]',  '$data[6]',  '$data[7]',  '$data[8]')");
        }
    }

    function lista_cuentas_act_inac($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id and pln_estado='0'");
        }
    }

    ////****************////REPORTE DE RETENCION//////******************

    function lista_reporte_retencion($txt, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            if (!empty($txt)) {
                return pg_query("select * from erp_retencion  
                                 where (ret_numero like'%$txt%' or ret_identificacion like'%$txt%' or  ret_nombre like'%$txt%') ");
            } else {
                return pg_query("select * from erp_retencion  
                             where ret_fecha_emision 
                             between '$desde' and '$hasta'");
            }
        }
    }

    function lista_detalle_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_retencion  where ret_id=$id order by dtr_tipo_impuesto ");
        }
    }

    function lista_retencion_completo_noaut($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion where ret_estado_aut<>'RECIBIDA AUTORIZADO' order by ret_numero desc");
        }
    }

    function lista_configuraciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=$id");
        }
    }

    function lista_ultima_retencion_anulada($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where reg_id='$id' and ret_estado_aut='ANULADO' ORDER BY ret_numero desc limit 1");
        }
    }

    function insert_asientos_ret_anulacion($data, $sec) {
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
            con_estado,
            mod_id,
            doc_id,
            cli_id)
            VALUES
            ('$sec', '$data[4]', '$data[1]', '$data[2]', '$data[3]','', '$data[0]','0', '$data[5]', '$data[6]', '$data[7]', '$data[8]')");
        }
    }

    function elimina_asientos_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_asientos_contables where con_asiento='$as' ");
        }
    }

    function lista_sum_cuentas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pln_id,reg_codigo_cta, sum(det_total) as dtot, sum(det_descuento_moneda) as ddesc  from erp_reg_det_documentos where reg_id=$id group by pln_id,reg_codigo_cta");
        }
    }

    function insert_asiento_mp($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[9], 2) != 0) {
                $desc = "('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[15]', '0', '$data[9]', '$data[18]', '$data[19]', '$data[20]', '$data[21]'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec','$data[17]','$data[1]','$data[2]','$data[11]','','$data[4]','0', '$data[18]', '$data[19]', '$data[20]', '$data[21]'),";
            }
            if (round($data[6], 2) != 0) {
                $ice = "('$sec','$data[17]','$data[1]','$data[2]','$data[13]','','$data[6]','0', '$data[18]', '$data[19]', '$data[20]', '$data[21]'),";
            }
            if (round($data[7], 2) != 0) {
                $ibr = "('$sec','$data[17]','$data[1]','$data[2]','$data[14]','','$data[7]','0', '$data[18]', '$data[19]', '$data[20]', '$data[21]'),";
            }
            if (round($data[8], 2) != 0) {
                $prop = "('$sec','$data[17]','$data[1]','$data[2]','$data[16]','','$data[8]','0', '$data[18]', '$data[19]', '$data[20]', '$data[21]'),";
            }
            $val = $data[0] + $data[9];
            return pg_query("INSERT INTO erp_asientos_contables(
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
            VALUES
            $desc
            $iva
            $ice
            $ibr
            $prop
            ('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[12]', '0', '$data[5]', '$data[18]', '$data[19]', '$data[20]', '$data[21]')") . '&' . $sec;
        }
    }

    function lista_asiento_doc($mod, $id, $e) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where mod_id='$mod' and doc_id=$id and con_estado='$e' limit 1");
        }
    }

    function delete_ctasxpagar($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_ctasxpagar where doc_id='$id' and ctp_forma_pago='RETENCION'");
        }
    }


}

?>
