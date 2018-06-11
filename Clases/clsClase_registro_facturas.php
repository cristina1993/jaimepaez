<?php

include_once 'Conn.php';

class Clase_registro_facturas {

    var $con;

    function Clase_registro_facturas() {
        $this->con = new Conn();
    }

    function lista_proveedores() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where CAST(cli_tipo AS Integer)>0 order by CAST(cli_tipo AS Integer)");
        }
    }

    function lista_cliente_ruc($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where cli_ced_ruc='$ruc' ");
        }
    }

    function lista_detalle_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_det_documentos where reg_id=$id ");
        }
    }

    function lista_pagos_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_pagos_documentos where reg_id=$id ");
        }
    }

    function lista_un_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_id=$id ");
        }
    }

    function lista_registro_numero($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos where reg_num_registro='$num' ");
        }
    }

    function lista_registros_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos order by reg_femision,reg_num_registro ");
        }
    }

    function lista_ultimo_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos order by reg_num_registro desc ");
        }
    }

    function elimina_detalle_pagos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_documentos where reg_id=$id; 
                             delete from erp_reg_det_documentos where reg_id=$id ");
        }
    }

    function elimina_asientos_asiento($as) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_asientos_contables where con_asiento='$as' ");
        }
    }

    function elimina_registro_detalle_pagos_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_pagos_documentos where reg_id=$id; 
                             delete from erp_reg_det_documentos where reg_id=$id;
                             delete from erp_reg_documentos where reg_id=$id    
                                 ");
        }
    }

    function elimina_registro_detalle_pagos($num) {
        if ($this->con->Conectar() == true) {
            $sms = 0;
            $rst = pg_fetch_array($this->lista_registro_numero($num));
            if (!empty($rst)) {
                if (pg_query("delete from erp_pagos_documentos where reg_id=$rst[reg_id] ") == true) {
                    if (pg_query("delete from erp_reg_det_documentos where reg_id=$rst[reg_id] ") == true) {
                        if (pg_query("delete from erp_reg_documentos where reg_id=$rst[reg_id] ") == false) {
                            $sms = 'Del Reg ' . pg_last_error();
                        }
                    } else {
                        $sms = 'Del Det ' . pg_last_error();
                    }
                } else {
                    $sms = 'Del Pag ' . pg_last_error();
                }
            } else {
                $sms = 'No Existe Documento ' . $num;
            }
            return $sms;
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
                $n_sec = 'AS' . substr($rst[con_asiento], 2, (10 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function insert_asiento_mp($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[9], 2) != 0) {
                $desc = "('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[15]', '0', '$data[9]', $data[18],$data[19],$data[20],$data[21]),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec','$data[17]','$data[1]','$data[2]','$data[11]','','$data[4]','0', $data[18],$data[19],$data[20],$data[21]),";
            }
            if (round($data[6], 2) != 0) {
                $ice = "('$sec','$data[17]','$data[1]','$data[2]','$data[13]','','$data[6]','0', $data[18],$data[19],$data[20],$data[21]),";
            }
            if (round($data[7], 2) != 0) {
                $ibr = "('$sec','$data[17]','$data[1]','$data[2]','$data[14]','','$data[7]','0', $data[18],$data[19],$data[20],$data[21]),";
            }
            if (round($data[8], 2) != 0) {
                $prop = "('$sec','$data[17]','$data[1]','$data[2]','$data[16]','','$data[8]','0', $data[18],$data[19],$data[20],$data[21]),";
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
            ('$sec', '$data[17]', '$data[1]', '$data[2]', '', '$data[12]', '0', '$data[5]', $data[18],$data[19],$data[20],$data[21])") . '&' . $sec;
        }
    }

    function insert_asiento_det($data) {
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
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','','$data[5]','0','$data[6]','$data[7]','$data[8]','$data[9]')");
        }
    }

//    function insert_asiento_anulacion($data, $sec) {
//        if ($this->con->Conectar() == true) {
//            if (round($data[9], 2) != 0) {
//                $desc = "('$sec', 'ANULACION FACTURA COMPRA', '$data[1]', '$data[2]','$data[15]','', '$data[9]','0','0'),";
//            }
//            if (round($data[4], 2) != 0) {
//                $iva = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[11]','0','$data[4]','0'),";
//            }
//            if (round($data[6], 2) != 0) {
//                $ice = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[13]','0','$data[6]','0'),";
//            }
//            if (round($data[7], 2) != 0) {
//                $ibr = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[14]','0','$data[7]','0'),";
//            }
//            if (round($data[8], 2) != 0) {
//                $prop = "('$sec','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[16]','0','$data[8]','0'),";
//            }
//            $val = $data[0] + $data[9];
//            return pg_query("INSERT INTO erp_asientos_contables(
//            con_asiento,
//            con_concepto,
//            con_documento,
//            con_fecha_emision, 
//            con_concepto_debe, 
//            con_concepto_haber,
//            con_valor_debe, 
//            con_valor_haber,
//            con_estado)
//            VALUES
//            $desc
//            $iva
//            $ice
//            $ibr
//            $prop
//            ('$sec', 'ANULACION FACTURA COMPRA', '$data[1]', '$data[2]',  '$data[12]','','$data[5]','0', '0')") . '&' . $sec;
//        }
//    }

    function insert_asiento_anulacion($data) {
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
                            VALUES(
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
                            '$data[11]'
                            )");
        }
    }

    function insert_asiento_anulacion_det($data) {
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
            con_estado)
            VALUES ('$data[0]','ANULACION FACTURA COMPRA','$data[1]','$data[2]','','$data[3]','0','$data[4]','0')");
        }
    }

    function insert_registro($data, $cli_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into erp_reg_documentos (
            reg_fregistro,
            reg_femision,
            reg_fvencimiento,
            reg_tipo_documento, 
            reg_num_documento,
            reg_num_autorizacion,
            reg_fautorizacion,
            reg_fcaducidad, 
            reg_tpcliente,
            reg_concepto,
            reg_sbt12,
            reg_sbt0,
            reg_sbt_noiva, 
            reg_sbt_excento,
            reg_sbt,
            reg_tdescuento,
            reg_ice,
            reg_irbpnr, 
            reg_iva12,
            reg_propina,
            reg_total,
            reg_ruc_cliente,
            reg_num_registro,
            reg_estado,
            reg_sustento,
            cli_id,
            reg_importe,
            imp_id,
            reg_tipo_pago,
            reg_forma_pago,
            reg_pais_importe,
	    reg_relacionado
            )values(
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
            '$data[16]',
            '$data[17]',
            '$data[18]',
            '$data[19]',
            '$data[20]',                
            '$data[21]',
            '$data[22]',
                '1',
            '$data[27]',
            $cli_id,
            '$data[29]',
            '$data[30]',
            '$data[31]',
            '$data[32]',
            '$data[33]',
	    '$data[34]'
                )
            
            ");
        }
    }

    function insert_detalle_registro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_det_documentos(
            det_codigo_empresa, 
            det_descripcion, 
            det_cantidad, 
            det_vunit, 
            det_descuento_porcentaje, 
            det_descuento_moneda, 
            det_total, 
            det_impuesto, 
            det_tipo, 
            det_codigo_externo,
            det_tab,
            pln_id,
            reg_codigo_cta,
            pro_id,
            reg_id)
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
    '$data[12]',
    '$data[13]',
    '$data[14]',
    '$data[17]',
    '$data[18]')");
        }
    }

    function insert_pagos_registro($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_pagos_documentos(
            pag_tipo,
            pag_porcentage,
            pag_dias,
            pag_valor, 
            pag_fecha_v,
            reg_id )
    VALUES (
    0,
    $data[0],    
    $data[1],
    $data[2],    
   '$data[3]',
    $data[4]  )
");
        }
    }

    function upd_registro($data, $id, $cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos
       SET reg_fregistro='$data[0]',
       reg_femision='$data[1]',
       reg_fvencimiento='$data[2]', 
       reg_tipo_documento='$data[3]',
       reg_num_documento='$data[4]',
       reg_num_autorizacion='$data[5]', 
       reg_fautorizacion='$data[6]',
       reg_fcaducidad='$data[7]',
       reg_tpcliente='$data[8]',
       reg_concepto='$data[9]', 
       reg_sbt12='$data[10]',
       reg_sbt0='$data[11]',
       reg_sbt_noiva='$data[12]',
       reg_sbt_excento='$data[13]', 
       reg_sbt='$data[14]',
       reg_tdescuento='$data[15]',
       reg_ice='$data[16]',
       reg_irbpnr='$data[17]',
       reg_iva12='$data[18]', 
       reg_propina='$data[19]',
       reg_total='$data[20]',
       reg_ruc_cliente='$data[21]',
       reg_num_registro='$data[22]',
       reg_estado='1',
       reg_sustento='$data[27]',
       reg_importe='$data[29]',
       imp_id='$data[30]',
       cli_id='$cli',
       reg_tipo_pago='$data[31]',
       reg_forma_pago='$data[32]',
       reg_pais_importe='$data[33]',
       reg_relacionado='$data[34]'
 WHERE reg_id=$id
     ");
        }
    }

    function lista_buscador_reg_fac($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos d, erp_i_cliente c where d.reg_ruc_cliente=c.cli_ced_ruc $txt         
                             union
                             SELECT * from erp_reg_documentos d, erp_i_cliente c where d.reg_ruc_cliente=c.cli_ced_ruc and d.reg_estado=2 order by reg_num_registro");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id = $id");
        }
    }

    function lista_plan_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_estado='0' ORDER BY pln_codigo");
        }
    }

    function lista_secuencial_cliente($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_cliente where substr(cli_codigo, 1, 2) = '$tp' order by cli_codigo desc limit 1");
        }
    }

    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente
            (
            cli_nom_comercial,
            cli_raz_social,
            cli_fecha,
            cli_estado,
            cli_tipo,
            cli_categoria,
            cli_ced_ruc,
            cli_calle_prin,
            cli_telefono,
            cli_email,
            cli_codigo,
            cli_tipo_cliente
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
            '$data[6]'
            )");
        }
    }

    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin = '$data[0]',
            cli_email = '$data[1]',
            cli_telefono = '$data[2]',
            cli_calle_sec = '',
            cli_numeracion = ''
            WHERE cli_ced_ruc = '$id'");
        }
    }

    function lista_un_registro_factura($doc, $ruc, $tip) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where reg_num_documento = '$doc' and reg_ruc_cliente = '$ruc' and reg_tipo_documento = '$tip' and reg_estado<>3");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id = c.pln_id and a.emi_id=1 and a.cas_orden_emi=$id and c.pln_estado=0");
        }
    }

    function lista_sum_cuentas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select pln_id,reg_codigo_cta, sum(det_total) as dtot, sum(det_descuento_moneda) as ddesc  from erp_reg_det_documentos where reg_id=$id group by pln_id,reg_codigo_cta");
        }
    }

    function buscar_retencion($num, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where ret_num_comp_retiene='$num' and ret_identificacion='$ruc' and ret_estado_aut<>'ANULADO'");
        }
    }

    function lista_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_retencion where reg_id='$id' and (ret_estado_aut is null or ret_estado_aut<>'ANULADO') ");
        }
    }

    function lista_una_nota_cred($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito where reg_id = '$id' and rnc_estado<>'3'");
        }
    }

    function lista_una_nota_deb($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_debito where reg_id = '$id' and rnd_estado<>'3'");
        }
    }

    function update_estado_reg_factura($id, $std, $fec) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set reg_estado = '$std', reg_femision = '$fec' WHERE reg_id = $id");
        }
    }

    function update_estado_det_factura($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_det_documentos set det_estado = '$std' WHERE reg_id = $id");
        }
    }

    function lista_ultimo_codigo_pro($tp) {
        if ($this->con->Conectar() == true) {
            switch ($tp) {
                case '26'://Productos
                    $query = pg_query("SELECT mp_c as cod FROM erp_mp WHERE mp_c like'V%' and char_length(mp_c)=5 ORDER BY mp_c DESC LIMIT 1");
                    break;
                case '27'://Insumos
                    $query = pg_query("SELECT mp_c as cod FROM erp_mp WHERE mp_c like'IO%' and char_length(mp_c)=6 ORDER BY mp_c DESC LIMIT 1");
                    break;
                case '69'://Materia Prima
                    $query = pg_query("SELECT mp_c as cod FROM erp_mp WHERE mp_c like'MP%' and char_length(mp_c)=6 ORDER BY mp_c DESC LIMIT 1");
                    break;
            }
            return $query;
        }
    }

    function lista_productos_insumosotros_matpri() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ids as tbl, id, mp_c as cod, mp_d as dsc FROM erp_mp ORDER BY cod");
        }
    }

    function lista_producto_insumosotros_matpri_id($tbl, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ids as tbl, mp_c as cod, mp_d as dsc, mp_a,mp_b FROM erp_mp WHERE id=$id and ids=$tbl");
        }
    }

    function insert_producto_insumo_matpri($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_mp (ids,mp_c,mp_d,mp_a,mp_b,mp_i)values($data[0],'$data[1]','$data[2]','$data[3]','$data[4]','0')");
        }
    }

    function lista_producto_insumosotros_matpri_cod($tbl, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ids as tbl, mp_c as cod, mp_d as dsc, id FROM erp_mp WHERE ids=$tbl and mp_c='$id'");
        }
    }

    function lista_producto_insumos_otros_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_c='$id'");
        }
    }

    function lista_secuencial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_reg_documentos WHERE reg_num_registro='$id'");
        }
    }

    function lista_ultima_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_retencion WHERE reg_id=$id order by ret_id desc limit 1");
        }
    }

    function lista_asiento_retencion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_documento='$id' and con_concepto='ANULACION RETENCION'");
        }
    }

    function update_asiento_anulacion($asi, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_documentos set reg_asi_anulacion = '$asi' WHERE reg_id = $id");
        }
    }

    function lista_todo_registro($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos r, erp_i_cliente c, erp_reg_det_documentos d, erp_mp p where r.reg_ruc_cliente=c.cli_ced_ruc and r.reg_id=d.reg_id and d.det_codigo_empresa=p.mp_c and r.reg_id=$id");
        }
    }

    function ultimo_costo($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
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

    function lista_un_movimiento_fac($id, $num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where mov_num_fac_entrega='$id' and mov_documento='$num'");
        }
    }

    function lista_encabezdo_ant($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos where cli_id=$id and (reg_estado=1 or reg_estado=4) order by reg_num_registro desc limit 1");
        }
    }

    function lista_producto_ant($id, $cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_reg_documentos r, erp_reg_det_documentos d, erp_plan_cuentas p where d.reg_id=r.reg_id and p.pln_id=d.pln_id and d.pro_id=$id and (r.reg_estado=1 or r.reg_estado=4) order by r.reg_num_registro desc limit 1");
        }
    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set order by split_part(mp_tipo,'&',10)");
        }
    }

    function lista_un_tipo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_tipos where tps_relacion='$id' order by tps_nombre");
        }
    }

    function lista_producto_cod($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ids as tbl, mp_c as cod, mp_d as dsc, id FROM erp_mp WHERE mp_c='$cod'");
        }
    }

    function lista_un_movimiento_fac_pro($id, $num, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inv_pt where mov_num_fac_entrega='$id' and mov_documento='$num' and pro_id=$pro");
        }
    }

    function insert_transferencia($data) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_val_unit,
                mov_val_tot,
                mov_usuario,
                mov_num_fac_entrega,
                mov_doc_tipo
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
            '$f',
            '$h',
            '$data[9]',
            '$data[10]',
            '$usu',
            '$data[11]',
            '$data[12]')");
        }
    }

    function lista_secuencial_mov() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt WHERE char_length(mov_documento)=14 ORDER BY mov_documento DESC LIMIT 1");
        }
    }

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function lista_un_asiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asientos_contables where con_asiento='$id'");
        }
    }

    ////// REPORTE FACTURA COMPRA///// 
    function lista_notcre_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_registro_nota_credito where reg_id='$id' and rnc_estado=1");
        }
    }

    function lista_retencion_factura($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_retencion where reg_id=$id and (char_length(trim(ret_estado_aut))=0 or ret_estado_aut is null)");
        }
    }

    function lista_det_ret($ret) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(dtr_valor) from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IV') as iva, 
                                    (select sum(dtr_valor) from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IR') as renta,
                                    (select dtr_procentaje_retencion from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IV' limit 1) as p_iva,
                                    (select dtr_procentaje_retencion from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IR' limit 1) as p_renta,"
                    . "             (select dtr_codigo_impuesto from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IV' limit 1) as cod_iva,
                                    (select dtr_codigo_impuesto from erp_det_retencion where ret_id=$ret  and dtr_tipo_impuesto='IR' limit 1) as cod_renta");
        }
    }


    function lista_registros_factura($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos f, erp_i_cliente c where c.cli_id=f.cli_id $txt order by f.reg_femision,f.reg_num_registro ");
        }
    }

    function lista_tipo_documentos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_tip_documentos where tdc_factura=1");
        }
    }

    function lista_sustento_documentos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_doc_sustento");
        }
    }

    function lista_un_tipo_documentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_tip_documentos where tdc_id=$id");
        }
    }

    function lista_un_sustento_documentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_doc_sustento where sus_id=$id");
        }
    }

    function lista_formas_pago() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_formas_pago");
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
            con_estado)
            VALUES
            ('$sec', 'RETENCION', '$data[1]', '$data[2]', '$data[3]','', '$data[0]', '0',  '0')");
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
            ('$sec', '$data[4]', '$data[1]', '$data[2]', '','$data[3]', '0', '$data[0]',  $data[5],$data[6],$data[7],$data[8])");
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

    function lista_detalle_retencion($ret) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_retencion where ret_id=$ret");
        }
    }

    function lista_paises($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_paises");
        }
    }
    
    function insert_retencion($data,$cli) {
        
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
            $cli,
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

}

?>
