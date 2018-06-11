<?php

include_once 'Conn.php';

class Clase_reg_nota_debito {

    var $con;

    function Clase_reg_nota_debito() {
        $this->con = new Conn();
    }

    function lista_buscador_notas_debito($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_debito $txt ORDER BY rnd_num_registro");
        }
    }

    function lista_secuencial_nota_debito($bod) {
        if ($this->con->Conectar() == true) {
            if ($this->con->Conectar() == true) {
                return pg_query("SELECT rnd_num_registro as sec FROM  erp_registro_nota_debito order by rnd_num_registro desc limit 1");
            }
        }
    }

    function lista_buscar_clientes($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
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

    function insert_nota_debito($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_registro_nota_debito(
            cli_id, 
            rnd_numero, 
            rnd_motivo, 
            rnd_fecha_emision, 
            rnd_nombre, 
            rnd_identificacion, 
            rnd_denominacion_comprobante, 
            rnd_num_comp_modifica, 
            rnd_fecha_emi_comp, 
            rnd_subtotal12, 
            rnd_subtotal0, 
            rnd_subtotal_ex_iva, 
            rnd_subtotal_no_iva, 
            rnd_total_ice, 
            rnd_total_iva, 
            rnd_total_valor,
            rnd_num_registro,
            rnd_autorizacion,
            rnd_fec_registro,
            rnd_fec_autorizacion,
            rnd_fec_caducidad,
            rnd_subtotal,
            reg_id
            )VALUES(
            '$data[0]',
            '$data[1]',
            '" . strtoupper($data[2]) . "',
            '$data[3]',
            '" . strtoupper($data[4]) . "',
            '" . strtoupper($data[5]) . "',
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
            '$data[22]'            
                        )");
        }
    }

    function insert_det_nota_debito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_det_nota_debito(
                                    rnd_id,
                                    rdd_descripcion,
                                    rdd_precio_total
                                    )VALUES(
                                    $id,
                                   '" . strtoupper($data[0]) . "',
                                   '$data[1]'
                                    )");
        }
    }

    function upd_nota_debito($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_nota_debito SET
           cli_id='$data[0]',
            rnd_numero='$data[1]', 
            rnd_motivo='" . strtoupper($data[2]) . "',  
            rnd_fecha_emision='$data[3]',  
            rnd_nombre='" . strtoupper($data[4]) . "',  
            rnd_identificacion='" . strtoupper($data[5]) . "',  
            rnd_denominacion_comprobante='$data[6]',  
            rnd_num_comp_modifica='$data[7]',  
            rnd_fecha_emi_comp='$data[8]',  
            rnd_subtotal12='$data[9]',  
            rnd_subtotal0='$data[10]',  
            rnd_subtotal_ex_iva='$data[11]',  
            rnd_subtotal_no_iva='$data[12]',  
            rnd_total_ice='$data[13]', 
            rnd_total_iva='$data[14]',  
            rnd_total_valor='$data[15]', 
            rnd_num_registro='$data[16]', 
            rnd_autorizacion='$data[17]', 
            rnd_fec_registro='$data[18]', 
            rnd_fec_autorizacion='$data[19]', 
            rnd_fec_caducidad='$data[20]',
            rnd_subtotal='$data[21]',
            reg_id='$data[22]'
  	    WHERE rnd_id='$id'");
        }
    }

    function delete_det_nota($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_reg_det_nota_debito where rnd_id='$id'");
        }
    }

    function lista_una_nota_debito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_debito where rnd_num_registro='$id'");
        }
    }

    function lista_una_nota_debito_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_debito where rnd_id='$id'");
        }
    }

    function lista_detalle_nota($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_det_nota_debito WHERE rnd_id='$id'");
        }
    }

    function delete_nota_debito($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_registro_nota_debito where rnd_id='$id'");
        }
    }

    function lista_una_factura_nfact($id) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_reg_documentos d, erp_i_cliente c where c.cli_ced_ruc=d.reg_ruc_cliente and d.reg_num_documento='$id' and (reg_estado='1' or reg_estado='4')");
            return pg_query("SELECT * FROM  erp_reg_documentos d, erp_i_cliente c where c.cli_ced_ruc=d.reg_ruc_cliente and d.reg_num_documento='$id' and reg_estado=1");
        }
    }

    function lista_un_regfactura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_reg_documentos d, erp_i_cliente c where c.cli_ced_ruc=d.reg_ruc_cliente and d.reg_id='$id'");
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
                                                                        '$data[9]',
                                                                        '$data[10]',
                                                                        '$data[11]'
                                                                        )");
        }
    }

//    function delete_asientos($id, $doc, $con) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("DELETE FROM  erp_asientos_contables where doc_id='$id' and con_documento='$doc' and con_concepto='$con'");
//        }
//    }

    function delete_ctasxpagar($doc, $con) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_ctasxpagar where doc_id='$doc' and ctp_forma_pago='$con'");
        }
    }

    function lista_nota_deb_duplicada($id, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_registro_nota_debito where rnd_numero='$id' and rnd_identificacion='$ruc' and rnd_estado=1");
        }
    }

    function update_estado_reg_nd($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_registro_nota_debito set rnd_estado='$std' WHERE rnd_id=$id");
        }
    }

    function update_estado_det_nd($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_det_nota_debito set det_estado='$std' WHERE rnd_id=$id");
        }
    }

    function update_ctasxpagar($id, $std) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_ctasxpagar set ctp_estado='$std' WHERE doc_id=$id and ctp_forma_pago='NOTA DE DEBITO'");
        }
    }

    function lista_asientos_ctas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id = c.pln_id and a.emi_id=1 and a.cas_orden_emi=$id and c.pln_estado=0");
        }
    }

    function insert_asiento_mp($data, $sec) {
        if ($this->con->Conectar() == true) {
            if (round($data[0], 2) != 0) {
                $subt = "('$sec', 'REGISTRO DE NOTA DE DEBITO', '$data[1]', '$data[2]', '$data[8]', '', '$data[0]', '0', '$data[12]', '$data[13]', '$data[14]', '$data[15]'),";
            }
            if (round($data[4], 2) != 0) {
                $iva = "('$sec', 'REGISTRO DE NOTA DE DEBITO', '$data[1]','$data[2]','$data[9]','','$data[4]','0', '$data[12]', '$data[13]', '$data[14]', '$data[15]'),";
            }
            if (round($data[5], 2) != 0) {
                $ice = "('$sec', 'REGISTRO DE NOTA DE DEBITO', '$data[1]','$data[2]','$data[10]','','$data[5]','0', '$data[12]', '$data[13]', '$data[14]', '$data[15]'),";
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
            con_estado,
            mod_id,
            doc_id,
            cli_id)
            VALUES
            $subt
            $iva
            $ice
            ('$sec', 'REGISTRO DE NOTA DE DEBITO', '$data[1]', '$data[2]', '', '$data[7]', '0', '$data[11]', '$data[12]', '$data[13]', '$data[14]', '$data[15]')") . '&' . $sec;
        }
    }

    function lista_secuencial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_registro_nota_debito where rnd_num_registro='$id'");
        }
    }

    function lista_asientos_mod($id, $mod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables where doc_id='$id' and mod_id='$mod' and con_estado='1'");
        }
    }

    function delete_asientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_asientos_contables where con_asiento='$id'");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
