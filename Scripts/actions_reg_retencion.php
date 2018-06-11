<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_retencion.php';
$Reg_retencion = new Clase_reg_retencion();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
$s = $_REQUEST[s];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;

        if (empty($id)) {
            $rst_dpl = pg_fetch_array($Reg_retencion->lista_retencion_duplicada($data[1], $data[3]));
            if (!empty($rst_dpl)) {
                $sms = 5;
            } else {
                $rst_sec = pg_fetch_array($Reg_retencion->lista_secuencial($data[9]));
                if (!empty($rst_sec)) {
                    $sms = 3;
                } else {
                    $cli_ctas = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '65'));
                    if ($cli_ctas[pln_id] == '') {
                        $sms = 1;
                    } else {
                        if ($Reg_retencion->insert_retencion($data) == false) {
                            $sms = pg_last_error() . 'ins_ret';
                            $aud = 1;
                        } else {
                            $asi = $Reg_retencion->siguiente_asiento();
                            $n = 0;
                            $rst = pg_fetch_array($Reg_retencion->lista_retencion_numero($data[9]));
                            $id = $rst[rgr_id];
                            while ($n < count($data2)) {
                                $dt = explode('&', $data2[$n]);
                                if ($Reg_retencion->insert_det_retencion($dt, $id) == false) {
                                    $sms = pg_last_error() . 'ins_det_ret';
                                    $aud = 1;
                                }
                                $rst_idcta = pg_fetch_array($Reg_retencion->lista_id_cuenta($dt[0]));
                                $rst_cta = pg_fetch_array($Reg_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                                $data1 = Array($asi,
                                    'REGISTRO DE RETENCION',
                                    $data[1],
                                    $data[8],
                                    $rst_cta[pln_codigo],
                                    '',
                                    $dt[5],
                                    '0',
                                    '1',
                                    $id,
                                    '8',
                                    $id,
                                    $data[0]
                                );
                                if ($Reg_retencion->insert_asientos($data1) == false) {
                                    $sms = 'Insert_asientos' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                            $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '1')); ////revisar cta_cliente
                            $dat0 = Array($asi,
                                'REGISTRO DE RETENCION',
                                $data[1],
                                $data[8],
                                '',
                                $rst_cliente[pln_codigo],
                                '0',
                                $data[7],
                                '1',
                                $id,
                                '8',
                                $id,
                                $data[0]
                            );


                            if ($Reg_retencion->insert_asientos($dat0) == false) {
                                $sms = 'Insert_asientos1' . pg_last_error();
                                $aud = 1;
                            } else {
                                if ($Reg_retencion->update_asiento_ret($id, $asi) == false) {
                                    $sms = 'Insert_asientos1' . pg_last_error();
                                    $aud = 1;
                                }
                            }

                            ////// inserta la retencion en control de cobros///////
                            $cobros = Array(
                                $data[0],
                                'RETENCION',
                                '0',
                                $data[1],
                                date('Y-m-d'),
                                $data[8],
                                $data[7],
                                '5',
                                $data[7],
                                $id
                            );
                            if ($Reg_retencion->insert_cobros($cobros) == false) {
                                $sms = pg_last_error() . 'ins_cobros';
                                $aud = 1;
                            }
///////nuevo algoritmo de ctasxcobrar/////
                            $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '2'));
                            $rst_cta = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '80'));
                            $rst_ch = pg_fetch_array($Reg_retencion->lista_cobro_tip($id));
                            $monto = $data[7];
                            $cns_pid = $Reg_retencion->lista_pagos_act($data[13]);
                            while ($rst_p = pg_fetch_array($cns_pid)) {
                                $rst_pag = pg_fetch_array($Reg_retencion->lista_saldo_pago($rst_p[pag_id]));
                                $t_pag = ($rst_pag[pag_cant] + $rst_pag[debito]) - $rst_pag[credito];
                                if ($monto > 0) {
                                    if ($t_pag != 0) {
                                        if ($t_pag <= $monto) {
                                            $cta = array(
                                                $data[13], //com_id
                                                $data[8], //cta_fec
                                                $t_pag, //cta_monto
                                                'RETENCION', //forma de pago
                                                $rst_cta[pln_codigo], //cta_banco
                                                $rst_cliente[pln_id], /// pln_id
                                                $data[8], //fec_pag
                                                $rst_pag[pag_id], //pag_id
                                                $data[1], //num_doc
                                                'RETENCION', //cta_concepto
                                                '2', //asiento
                                                $rst_ch[chq_id] //chq_id
                                            );
                                            if ($Reg_retencion->insert_ctasxcobrar($cta) == false) {
                                                $sms = 'Insert_ctasxcobrar2' . pg_last_error();
                                                $aud = 1;
                                            }
                                            $monto = $monto - $t_pag;
                                        } else {
                                            $cta = array(
                                                $data[13], //com_id
                                                $data[8], //cta_fec
                                                $monto, //cta_monto
                                                'RETENCION', //forma de pago
                                                $rst_cta[pln_codigo], //cta_banco
                                                $rst_cliente[pln_id], /// pln_id
                                                $data[8], //fec_pag
                                                $rst_pag[pag_id], //pag_id
                                                $data[1], //num_doc
                                                'RETENCION', //cta_concepto
                                                '2', //asiento
                                                $rst_ch[chq_id] //chq_id
                                            );
                                            if ($Reg_retencion->insert_ctasxcobrar($cta) == false) {
                                                $sms = 'Insert_ctasxcobrar2' . pg_last_error();
                                                $aud = 1;
                                            }
                                            $monto = $monto - $monto;
                                        }
                                    }
                                }
                            }
                        }
                        if ($aud == 0) {
                            $n = 0;
                            while ($n < count($fields)) {
                                $f = $f . strtoupper($fields[$n] . '&');
                                $n++;
                            }
                            $modulo = 'REG. RETENCION';
                            $accion = 'INSERTAR';
                            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[9]) == false) {
                                $sms = "Auditoria" . pg_last_error() . 'ok2';
                            }
                        }
                    }
                }
            }
        } else {///Modificar
            $rst_rgr = pg_fetch_array($Reg_retencion->lista_una_retencion($id));
            if ($rst_rgr[rgr_estado] == 2) {
                $rst_dpl = pg_fetch_array($Reg_retencion->lista_retencion_duplicada($data[1], $data[3]));
                if (!empty($rst_dpl)) {
                    $sms = 5;
                }
            }
            if ($sms == 0) {
                if ($Reg_retencion->update_retencion($data, $id) == false) {
                    $sms = pg_last_error() . 'upd_ret';
                    $aud = 1;
                } else {

                    $rst_ch = pg_fetch_array($Reg_retencion->lista_cheques($id));
                    if (!empty($rst_ch)) {
                        if ($Reg_retencion->delete_ctasxcobrar($rst_ch[chq_id]) == false) {
                            $sms = 'delete_ctasxcob' . pg_last_error();
                            $aud = 1;
                        }
                        if ($Reg_retencion->delete_cobros($id) == false) {
                            $sms = 'delete_cobros' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    $rst_as = pg_fetch_array($Reg_retencion->lista_un_asiento_reg_retencion($id, '8'));
                    if ($Reg_retencion->delete_asientos($rst_as[con_asiento]) == false) {
                        $sms = 'delete_asientos' . pg_last_error();
                        $aud = 1;
                    }


                    if ($Reg_retencion->delete_det_retencion($id) == false) {
                        $sms = pg_last_error() . 'delete_ret';
                        $aud = 1;
                    } else {
                        $n = 0;
                        while ($n < count($data2)) {
                            $dt = explode('&', $data2[$n]);
                            if ($Reg_retencion->insert_det_retencion($dt, $id) == false) {
                                $sms = pg_last_error() . 'ins_det_ret';
                                $aud = 1;
                            }
                            $n++;
                        }
                        //////////////inserta control de cobros////
                        $cheques = Array($data[0],
                            'RETENCION',
                            '',
                            $data[1],
                            $data[8],
                            $data[8],
                            $data[7],
                            '0',
                            '',
                            '5',
                            '',
                            $data[7],
                            $id,
                            '0'///pag_id
                        );
                        if ($Reg_retencion->insert_cheques($cheques) == false) {
                            $sms = 'Insert_cheques' . pg_last_error();
                            $aud = 1;
                        }

                        ////// inserta ctasxcobrar///////
                        $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '1')); ////revisar cliente
                        $rst_cta = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '80'));
                        $rst_ch = pg_fetch_array($Reg_retencion->lista_cobro_tip($id));
                        $monto = $data[7];
                        $cns_pid = $Reg_retencion->lista_pagos_act($data[13]);
                        $vp = 0;
                        while ($rst_p = pg_fetch_array($cns_pid)) {
                            $rst_pag = pg_fetch_array($Reg_retencion->lista_saldo_pago($rst_p[pag_id]));
                            $t_pag = ($rst_pag[pag_cant] + $rst_pag[debito]) - $rst_pag[credito];
                            if ($monto > 0) {
                                if ($t_pag != 0) {
                                    if ($t_pag <= $monto) {
                                        $cta = array(
                                            $data[13], //com_id
                                            $data[8], //cta_fec
                                            $t_pag, //cta_monto
                                            'RETENCION', //forma de pago
                                            $rst_cta[pln_codigo], //cta_banco
                                            $rst_cliente[pln_id], /// pln_id
                                            $data[8], //fec_pag
                                            $rst_pag[pag_id], //pag_id
                                            $data[1], //num_doc
                                            'RETENCION', //cta_concepto
                                            '2', //asiento
                                            $rst_ch[chq_id] //chq_id
                                        );
                                        if ($Reg_retencion->insert_ctasxcobrar($cta) == false) {
                                            $sms = 'Insert_ctasxcobrar2' . pg_last_error();
                                            $aud = 1;
                                        }
                                        $monto = $monto - $t_pag;
                                    } else {
                                        $cta = array(
                                            $data[13], //com_id
                                            $data[8], //cta_fec
                                            $monto, //cta_monto
                                            'RETENCION', //forma de pago
                                            $rst_cta[pln_codigo], //cta_banco
                                            $rst_cliente[pln_id], /// pln_id
                                            $data[8], //fec_pag
                                            $rst_pag[pag_id], //pag_id
                                            $data[1], //num_doc
                                            'RETENCION', //cta_concepto
                                            '2', //asiento
                                            $rst_ch[chq_id] //chq_id
                                        );
                                        if ($Reg_retencion->insert_ctasxcobrar($cta) == false) {
                                            $sms = 'Insert_ctasxcobrar2' . pg_last_error();
                                            $aud = 1;
                                        }
                                        $monto = $monto - $monto;
                                    }
                                }
                            }
                            if ($rst_p[pag_forma] == 7 && $vp == 0) {
                                $m_pag = $rst_p[pag_id];
                                $vp = 1;
                            }
                        }
                        if ($vp == 1) {
                            if ($Reg_retencion->update_numero_pagos($data[1], $m_pag) == false) {
                                $sms = 'Insert_ctasxcobrar' . pg_last_error();
                                $aud = 1;
                            }
                        }

                        //////////////asiento retencion////
                        $asi = $Reg_retencion->siguiente_asiento();
                        $rst_cliente = pg_fetch_array($Reg_retencion->lista_asientos_ctas('1', '1')); /////revisar cliente
                        $dat0 = Array($asi,
                            'REGISTRO DE RETENCION',
                            $data[1],
                            $data[8],
                            '',
                            $rst_cliente[pln_codigo],
                            '0',
                            $data[7],
                            '1',
                            $id,
                            '8',
                            $id,
                            $data[0]
                        );

                        if ($Reg_retencion->insert_asientos($dat0) == false) {
                            $sms = 'Insert_asientos_enc' . pg_last_error();
                            $aud = 1;
                        } else {
                            /////inserta asiento del detalle/////
                            $n = 0;
                            while ($n < count($data2)) {
                                $dt = explode('&', $data2[$n]);
                                $rst_idcta = pg_fetch_array($Reg_retencion->lista_id_cuenta($dt[0]));
                                $rst_cta = pg_fetch_array($Reg_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                                $data1 = Array($asi,
                                    'REGISTRO DE RETENCION',
                                    $data[1],
                                    $data[8],
                                    $rst_cta[pln_codigo],
                                    '',
                                    $dt[5],
                                    '0',
                                    '1',
                                    $id,
                                    '8',
                                    $id,
                                    $data[0]
                                );
                                if ($Reg_retencion->insert_asientos($data1) == false) {
                                    $sms = 'Insert_asientos_det' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                            if ($Reg_retencion->update_asiento_ret($id, $asi) == false) {
                                $sms = 'update_asientos' . pg_last_error();
                                $aud = 1;
                            }
                        }
                    }
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'REG. RETENCION';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[9]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }


        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Reg_retencion->delete_cobros($id) == false) {
            $sms = pg_last_error() . 'del_cobro';
        } else {
            if ($Reg_retencion->delete_det_retencion($id) == false) {
                $sms = pg_last_error();
            } else {
                if ($Reg_retencion->delete_retencion($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $n = 0;
                    $f = $data;
                    $modulo = 'REG. RETENCION';
                    $accion = 'ELIMINAR';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }

        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($Reg_retencion->lista_datos_porcentaje($id));
        $rst_cuenta = pg_fetch_array($Reg_retencion->lista_cuentas_act_inac($rst[cta_id]));
        $descripcion = $rst[por_descripcion];
        $porcentaje = $rst[por_porcentage];
        $cod = $rst[por_codigo];
        $por_id = $rst[por_id] . '_' . $rst[por_siglas];
        echo $descripcion . '&' . $porcentaje . '&' . $cod . '&' . $por_id . '&' . $rst[por_siglas] . '&' . $rst[cta_id] . '&' . $rst_cuenta[pln_estado];
        break;

    case 4:
        if ($s == 0) {
            $cns = $Reg_retencion->lista_buscar_clientes(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[cli_apellidos] . ' ' . $rst[cli_nombres] . ' ' . $rst[cli_raz_social]);
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Reg_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_id];
            }
            echo $sms;
        }

        break;

    case 5:
        $rst = pg_fetch_array($Reg_retencion->lista_una_factura_nfact($id));
//        $base=$rst[subtotal] + $rst[subtotal0] + $rst[subtotal_exento_iva] + $rst[subtotal_no_objeto_iva];
        $base = $rst[fac_subtotal];
        echo $rst[fac_id] . '&' .
        $rst[fac_fecha_emision] . '&' .
        $rst[fac_identificacion] . '&' .
        $rst[fac_nombre] . '&' .
        $rst[cli_id] . '&' .
        $rst[fac_numero] . '&' .
        $rst[fac_total_iva] . '&' .
        $base;
        break;

    case 6:
        $rst = pg_fetch_array($Reg_retencion->lista_retencion_duplicada($id, $data));
        $rst_fac = pg_fetch_array($Reg_retencion->lista_una_retencion_fac($data2));
        $rst_pag = pg_fetch_array($Reg_retencion->suma_pagos_factura($data2));
        $saldo = (round($rst_pag[cantidad], 2) + round($rst_pag[debito], 2)) - round($rst_pag[credito], 2);
        echo $rst[rgr_identificacion] . '&' . $rst[rgr_numero] . '&' . $rst_fac[rgr_numero] . '&' . $saldo;
        break;

    case 7:
        $sms = 0;
        $rst_ret = pg_fetch_array($Reg_retencion->lista_una_retencion($_REQUEST[md_id]));
        $rst_pag = pg_fetch_array($Reg_retencion->lista_pagos_ret($rst_ret[fac_id]));
        if ($_REQUEST[estado] == 2) {
            if ($Reg_retencion->update_estado_reg_retencion($_REQUEST[md_id], '3') == false) {
                $sms = 'Update_estado_ret' . pg_last_error();
                $aud = 1;
            }
        } else {
            if ($Reg_retencion->update_estado_reg_retencion($_REQUEST[md_id], '3') == true) {
                if ($Reg_retencion->update_estado_det_retencion($_REQUEST[md_id], '3') == false) {
                    $sms = 'Update_reg_det' . pg_last_error();
                    $aud = 1;
                } else {
                    if ($Reg_retencion->update_estado_cobros($_REQUEST[md_id], '3') == false) {
                        $sms = 'update_ctasxcobrar ' . pg_last_error();
                        $aud = 1;
                    } else {
                        $rst_ch = pg_fetch_array($Reg_retencion->lista_cobro_tip($_REQUEST[md_id]));
                        if ($Reg_retencion->update_estado_ctasxcobrar($rst_ch[chq_id], '1') == false) {
                            $sms = 'update_ctasxcobrar ' . pg_last_error();
                            $aud = 1;
                        } else {
                            $asi = $Reg_retencion->siguiente_asiento();
                            $rst_ndoc = pg_fetch_array($Reg_retencion->lista_una_retencion($_REQUEST[md_id]));
                            $cns_asi = $Reg_retencion->lista_un_asiento_reg_retencion($_REQUEST[md_id], '8');
                            $n = 0;
                            while ($rst = pg_fetch_array($cns_asi)) {
                                $dat = Array($asi,
                                    'ANULACION ' . $rst[con_asiento],
                                    $rst[con_documento],
                                    date('Y-m-d'),
                                    $rst[con_concepto_haber],
                                    $rst[con_concepto_debe],
                                    $rst[con_valor_haber],
                                    $rst[con_valor_debe],
                                    '2',
                                    $rst[reg_retencion_id],
                                    '8',
                                    $_REQUEST[md_id],
                                    $rst[cli_id]
                                );
                                if ($Reg_retencion->insert_asientos($dat) == false) {
                                    $sms = 'Insert_asientos_anulacion' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                        }
                    }
                }
            } else {
                $sms = 'Update_reg_encab' . pg_last_error();
                $aud = 1;
            }
        }
        if (!empty($rst_pag)) {
            if ($Reg_retencion->update_pagos($rst_pag[pag_id], '9', '', '0') == false) {
                $sms = 'update_pagos' . pg_last_error();
                $aud = 1;
            }
        }
        if ($aud != 1) {
            $f = $rst_ret[rgr_num_registro] . '&' . $rst_ret[rgr_numero] . '&' . $rst_ret[rgr_nombre] . '&' . $rst_ret[rgr_identificacion] . '&' . $rst_ret[rgr_total_valor] . '& &';
            $modulo = 'REG. RETENCION';
            $accion = 'ANULACION';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $rst_ret[rgr_num_registro]) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>
