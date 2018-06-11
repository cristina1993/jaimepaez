<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
include_once '../Clases/clsClase_pagos.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Clase_pagos = new Clase_pagos();
$Set = new Clase_factura();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$data4 = $_REQUEST[data3];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
$v = $_REQUEST[v];
$s = $_REQUEST[s];
$x = $_REQUEST[x];
$emi = $_REQUEST[emi];
$usu = $_REQUEST[usu];
$ctr_inv = $_REQUEST[ctr_inv];
switch ($op) {
    case 0:
        if ($s == 0) {
            $cns = $Set->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[cli_tipo_cliente] . '&' . $rst[cli_estado];
            }
            echo $sms;
        }

        break;

    case 1:
        if (strlen($id) < 10) {
            $rst = pg_fetch_array($Set->lista_un_producto_id($id));
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto_codigo(strtoupper($id)));
        }
        if ($rst[id] != '') {
            if ($x == 0) {
                if ($ctr_inv == 0) {
                    $fra = '';
                } else {
                    $fra = "and m.bod_id=$emi";
                }
                $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fact($rst[id], $fra));
                $inv = $rst1[ingreso] - $rst1[egreso];
                $rst2 = pg_fetch_array($Set->lista_costos_mov($rst[id], $fra));
                $rst2[mov_val_unit] = (($rst2[ingreso] - $rst2[egreso]) / ($rst2[icnt] - $rst2[ecnt]));
            }
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst_precio[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst2[mov_val_unit] . '&' . $rst[mp_j] . '&' . $rst[mp_k] . '&' . $rst[mp_l] . '&' . $rst[ids];
        }

        break;

    case 2:
        $sms = 0;
        $aud = 0;
        $rst_sec = pg_fetch_array($Set->lista_una_factura_num($data[5]));
        $cli_nac_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '1'));
        $cli_ext_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '2'));
        $ven_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '3'));
        $iva_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '4'));
        $des_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '5'));
        $fle_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '6'));
        $ice_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '70'));
        $irb_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '71'));
        $prop_ctas = pg_fetch_array($Set->lista_asientos_ctas($data[0], '72'));
        if ($cli_nac_ctas[pln_id] == '' || $cli_ext_ctas[pln_id] == '' || $ven_ctas[pln_id] == '' || $iva_ctas[pln_id] == '' || $des_ctas[pln_id] == '' || $ice_ctas[pln_id] == '' || $fle_ctas[pln_id] == '' || $irb_ctas[pln_id] == '' || $prop_ctas[pln_id] == '') {
            $sms = 2;
        } else {
            if (empty($id)) {// Insertar
                if (!empty($rst_sec)) {
                    $sms = 1;
                } else {
                    if (!empty($data[1])) {
                        $data3 = array(
                            strtoupper($data[9]),
                            strtoupper($data[8]),
                            strtoupper($data[20]),
                            strtoupper($data[22]),
                            strtoupper($data[23]),
                            strtoupper($data[24])
                        );
                        if ($Set->upd_email_cliente($data3, $data[7]) == false) {
                            $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                        }
                        $cli = $data[1];
                    } else {
                        if (strlen($data[7]) < 11) {
                            $tipo = 'CN';
                        } else {
                            $tipo = 'CJ';
                        }
                        $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
                        $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

                        if ($sec >= 0 && $sec < 10) {
                            $txt = '0000';
                        } else if ($sec >= 10 && $sec < 100) {
                            $txt = '000';
                        } else if ($sec >= 100 && $sec < 1000) {
                            $txt = '00';
                        } else if ($sec >= 1000 && $sec < 10000) {
                            $txt = '0';
                        } else if ($sec >= 10000 && $sec < 100000) {
                            $txt = '';
                        }

                        $retorno = $tipo . $txt . $sec;

                        $da = array(
                            strtoupper($data[6]),
                            strtoupper($data[7]),
                            strtoupper($data[9]),
                            strtoupper($data[20]),
                            strtoupper($data[8]),
                            strtoupper($data[22]),
                            strtoupper($data[23]),
                            $retorno,
                            strtoupper($data[24])
                        );


                        if ($Set->insert_cliente($da) == false) {
                            $sms = 'Insert_cli' . pg_last_error();
                            $aud = 1;
                        }
                        $rst_cl = pg_fetch_array($Set->lista_clientes_codigo($data[7]));
                        $cli = $rst_cl[cli_id];
                    }

                    if ($aud == 0) {
                        if ($data[27] == 0) {
                            $estp = '4';
                        } else {
                            $estp = '3';
                        }
                        if ($Set->lista_cambia_status($data[3], $estp) == false) {
                            $sms = pg_last_error();
                        }
                    }


                    if ($Set->insert_factura($data, $cli) == false) {
                        $sms = 'Insert' . pg_last_error();
                        $accion = 'Insertar';
                        $aud = 1;
                    } else {
                        $rst_fac = pg_fetch_array($Set->lista_una_factura_num($data[5]));
                        $fac_id = $rst_fac[fac_id];

//                        $dt1 = explode('&', $data4[0]);
                        $m = 0;
                        $h = count($data4);
                        while ($m < $h) {
                            $dt1 = explode('&', $data4[$m]);
                            $pg = 0;
                            if ($dt1[1] == '9') {
                                $nf = strtotime("+$dt1[5] day", strtotime($data[4]));
                                $fec = date('Y-m-j', $nf);
                            } else {
                                $fec = $data[4];
                            }
                            if ($dt1[1] != 0) {
                                if ($dt1[1] != 0) {
                                    $pagos1.="INSERT INTO erp_pagos_factura (
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
                                                                             pag_id_chq
                                                                            )values (
                                                                            '$fac_id',
                                                                            '$pg',
                                                                            '0',
                                                                            '0',
                                                                            '0',
                                                                            '$fec',
                                                                            '$dt1[1]',
                                                                            '$dt1[2]',
                                                                            '$dt1[3]',
                                                                            '$dt1[4]',
                                                                            '$dt1[5]',
                                                                            '$dt1[6]',
                                                                            '$dt1[7]');";
                                }
                            }
                            $m++;
                        }
                        if ($Clase_pagos->insert_pagos($pagos1) == false) {
                            $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                            $aud = 1;
                        }

                        $n = 0;
                        $i = count($data2);
                        while ($n < $i) {
                            $dt = explode('&', $data2[$n]);
                            $detalles.="INSERT INTO erp_det_factura(
                                    fac_id,
                                    pro_id,
                                    dfc_codigo,
                                    dfc_cod_aux,
                                    dfc_cantidad,
                                    dfc_descripcion,
                                    dfc_precio_unit,
                                    dfc_porcentaje_descuento,
                                    dfc_val_descuento,
                                    dfc_precio_total,
                                    dfc_iva,
                                    dfc_ice, 
                                    dfc_p_ice, 
                                    dfc_cod_ice,
                                    dfc_irbpnr,
                                    dfc_p_irbpnr
                                    )VALUES (
                                    $fac_id,'$dt[0]','$dt[1]','$dt[2]','$dt[3]','$dt[4]','$dt[5]','$dt[6]','$dt[7]','$dt[8]','$dt[9]',
                                                   '$dt[10]','$dt[14]','$dt[15]','$dt[13]','$dt[16]');";

                            $n++;
                        }
                        if ($Set->insert_detalle_factura($detalles) == false) {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        } else {
                            if ($x == 0) {
                                $k = 0;
                                $m = 0;
                                $i = count($data2);
                                while ($k < $i) {
                                    $dtm = explode('&', $data2[$k]);
                                    $rst_ids = pg_fetch_array($Set->lista_un_producto_id($dtm[0]));
                                    $p_ids = $rst_ids[ids];
                                    if ($p_ids != 79 && $p_ids != 80) {
                                        $bod = $data[0];
                                        $fec_mov = date('Y-m-d');
                                        $hor_mov = date('H:i:s');
                                        $movimientos.="INSERT INTO erp_i_mov_inv_pt(
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
                                                                            mov_tabla,
                                                                            mov_val_tot)
                                                                            values($dtm[0],
                                                                                    25,
                                                                                    $cli,
                                                                                    $bod,
                                                                                    '$data[5]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$fec_mov',
                                                                                    '$fec_mov',
                                                                                    '$hor_mov',
                                                                                    '$dtm[3]',
                                                                                    '',
                                                                                    '$fec_mov',
                                                                                    '$data[5]',
                                                                                    '',
                                                                                    '',
                                                                                    '$dtm[11]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$dtm[12]');";
                                        $m++;
                                    }

                                    $k++;
                                }
                                if ($m > 0) {
                                    if ($Set->insert_movimiento_pt($movimientos) == false) {
                                        $sms = 'Insert_mov' . pg_last_error();
                                        $aud = 1;
                                    }
                                }
                            }
                        }

                        $cn_pag = $Set->lista_detalle_pagos($fac_id);
                        $r_fac = pg_fetch_array($Set->lista_una_factura_id($fac_id));
                        $cli_as = 1;
                        $tc = 76;
                        $td = 77;
                        $ch = 78;
                        $ef = 79;
                        $rt = 80;
                        $nc = 81;
                        $ct = 82;
                        $bn = 83;

                        while ($r_p = pg_fetch_array($cn_pag)) {
                            //// inserta retencion si el pago es retencion///
                            if ($r_p[pag_forma] == 7) {
                                $rst_rs = pg_fetch_array($Set->lista_secuencial_retencion());
                                if (!empty($rst_rs)) {
                                    $sec = ($rst_rs[sec] + 1);
                                    if ($sec >= 0 && $sec < 10) {
                                        $txt = '000000000';
                                    } else if ($sec >= 10 && $sec < 100) {
                                        $txt = '00000000';
                                    } else if ($sec >= 100 && $sec < 1000) {
                                        $txt = '0000000';
                                    } else if ($sec >= 1000 && $sec < 10000) {
                                        $txt = '000000';
                                    } else if ($sec >= 10000 && $sec < 100000) {
                                        $txt = '00000';
                                    } else if ($sec >= 100000 && $sec < 1000000) {
                                        $txt = '0000';
                                    } else if ($sec >= 1000000 && $sec < 10000000) {
                                        $txt = '000';
                                    } else if ($sec >= 10000000 && $sec < 100000000) {
                                        $txt = '00';
                                    } else if ($sec >= 100000000 && $sec < 1000000000) {
                                        $txt = '0';
                                    } else if ($sec >= 1000000000 && $sec < 10000000000) {
                                        $txt = '';
                                    }
                                } else {
                                    $txt = '0000000001';
                                }
                                $reg_num = $txt . $sec;
                                $dat_ret = array(
                                    $cli,
                                    $r_p[chq_numero],
                                    $data[6],
                                    $data[7],
                                    $data[5],
                                    '',
                                    '1',
                                    $r_p[pag_cant],
                                    $data[4],
                                    $reg_num,
                                    '1900-01-01',
                                    $data[4],
                                    '1900-01-01',
                                    $fac_id,
                                    '2'
                                );
                                if ($Set->insert_reg_retencion($dat_ret) == false) {
                                    $sms = pg_last_error();
                                }
                            }
                            if ($r_p[pag_forma] != 9 && $r_p[pag_forma] != 7) {
                                switch ($r_p[pag_forma]) {
                                    case 1:
                                        $form = 'TARJETA DE CREDITO';
                                        $cts = $tc;
                                        $tip = 6;
                                        break;
                                    case 2:
                                        $form = 'TARJETA DE DEBITO';
                                        $cts = $td;
                                        $tip = 7;
                                        break;
                                    case 3:
                                        $form = 'CHEQUE';
                                        $cts = $ch;
                                        $tip = 1;
                                        break;
                                    case 4:
                                        $form = 'EFECTIVO';
                                        $cts = $ef;
                                        $tip = 10;
                                        break;
                                    case 5:
                                        $form = 'CERTIFICADOS';
                                        $cts = $ct;
                                        $tip = 8;
                                        break;
                                    case 6:
                                        $form = 'BONOS';
                                        $cts = $bn;
                                        $tip = 9;
                                        break;
                                    case 7:
                                        $form = 'RETENCION';
                                        $cts = $rt;
                                        $tip = 5;
                                        break;
                                }
                                switch ($r_p[pag_banco]) {
                                    case 0:
                                        $banco = '';
                                        break;
                                    case 1:
                                        $banco = 'PICHINCHA';
                                        break;
                                    case 2:
                                        $banco = 'PACIFICO';
                                        break;
                                    case 3:
                                        $banco = 'GUAYAQUIL';
                                        break;
                                    case 4:
                                        $banco = 'PRODUBANCO';
                                        break;
                                    case 5:
                                        $banco = 'BOLIVARIANO';
                                        break;
                                    case 6:
                                        $banco = 'INTERNACIONAL';
                                        break;
                                    case 7:
                                        $banco = 'AUSTRO';
                                        break;
                                    case 8:
                                        $banco = 'PROMERICA';
                                        break;
                                    case 9:
                                        $banco = 'MACHALA';
                                        break;
                                    case 10:
                                        $banco = 'BGR';
                                        break;
                                    case 11:
                                        $banco = 'CITIBANK';
                                        break;
                                    case 12:
                                        $banco = 'PROCREDIT';
                                        break;
                                    case 13:
                                        $banco = 'UNIBANCO';
                                        break;
                                    case 14:
                                        $banco = 'SOLIDARIO';
                                        break;
                                    case 15:
                                        $banco = 'LOJA';
                                        break;
                                    case 16:
                                        $banco = 'TERRITORIAL';
                                        break;
                                    case 17:
                                        $banco = 'COOPNACIONAL';
                                        break;
                                    case 18:
                                        $banco = 'AMAZONAS';
                                        break;
                                    case 19:
                                        $banco = 'CAPITAL';
                                        break;
                                    case 20:
                                        $banco = 'D-MIRO';
                                        break;
                                    case 21:
                                        $banco = 'FINCA';
                                        break;
                                    case 22:
                                        $banco = 'COMERCIAL DE MANABI';
                                        break;
                                    case 23:
                                        $banco = 'COFIEC';
                                        break;
                                    case 24:
                                        $banco = 'LITORAL';
                                        break;
                                    case 25:
                                        $banco = 'DELBANK';
                                        break;
                                    case 26:
                                        $banco = 'SUDAMERICANO';
                                        break;
                                }
                                $rst_cta1 = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cts));
                                if ($r_p[pag_forma] != 8 && $r_p[pag_forma] != 7) {
                                    $cheques = Array($cli,
                                        $form,
                                        $banco,
                                        $r_p[chq_numero],
                                        $data[4],
                                        $data[4],
                                        $r_p[pag_cant],
                                        '0',
                                        '',
                                        $tip,
                                        '',
                                        $r_p[pag_cant],
                                        '0',
                                        $r_p[pag_id],
                                        $rst_cta1[pln_codigo],
                                        $rst_cta1[pln_id],
                                        'ABONO FAC. ' . $data[5]
                                    );
                                    if ($Set->insert_cheques($cheques) == false) {
                                        $sms = 'Insert_cheques' . pg_last_error();
                                        $aud = 1;
                                    } else {
                                        $rst_chq = pg_fetch_array($Set->buscar_cheques($r_p[pag_id]));
                                        $chq_id = $rst_chq[chq_id];
                                    }
                                } else {
                                    $rst_chq = pg_fetch_array($Set->lista_cheques_id($r_p[pag_id_chq]));
                                    $cant = $rst_chq[chq_cobro] + $r_p[pag_cant];
                                    if ($Set->upd_cantidad_cheques($cant, $r_p[pag_id_chq]) == false) {
                                        $sms = 'udp_cantidad_cheques' . pg_last_error();
                                    }
                                    $chq_id = $r_p[pag_id_chq];
                                    $form = 'NOTA DE CREDITO';
                                    $cts = $nc;
                                }
                                $rst_cliente = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cli_as));
                                $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cts));

                                $cta = array(
                                    $r_fac[fac_id], //com_id
                                    $data[4], //cta_fec
                                    $r_p[pag_cant], //cta_monto
                                    $form, //forma de pago
                                    $rst_cta[pln_codigo], //cta_banco
                                    $rst_cliente[pln_id], /// pln_id
                                    $data[4], //fec_pag
                                    $r_p[pag_id], //pag_id
                                    $r_p[chq_numero], //num_doc
                                    'ABONO FAC. ' . $data[5], //cta_concepto
                                    '2', //asiento
                                    $chq_id //chq_id
                                );
                                if ($Set->insert_ctasxcobrar($cta) == false) {
                                    $sms = 'Insert_ctasxcobrar' . pg_last_error();
                                    $aud = 1;
                                } else {
                                    $asi = $Set->siguiente_asiento();
                                    $asiento = array(
                                        $asi,
                                        'ABONO FAC. ' . $data[5],
                                        $r_p[chq_numero], //doc
                                        $data[4], //fec
                                        $rst_cta[pln_codigo], //con_debe
                                        $rst_cliente[pln_codigo], //con_haber
                                        $r_p[pag_cant], //val_debe
                                        $r_p[pag_cant], // val_haber
                                        '1', //estado
                                        '9',
                                        $r_p[pag_id],
                                        $cli
                                    );
                                    if ($Set->insert_asientos($asiento) == false) {
                                        $sms = 'Insert_asientos' . pg_last_error();
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
                        $modulo = 'FACTURA';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            } else {// Modificar
                if ($x == 0) {
                    if ($Set->elimina_movpt_documento($data[5]) == false) {
                        $sms = 'del' . pg_last_error();
                        $aud = 1;
                    }
                }

                if ($Set->elimina_detalle_factura($id) == true) {
                    if ($Set->delete_ctasxcobrar($id) == false) {
                        $sms = 'Delete_ctasxcobrar' . pg_last_error();
                        $aud = 1;
                    } else {
                        $cns_pag = $Clase_pagos->lista_detalle_pagos($id);
                        while ($rst_pag = pg_fetch_array($cns_pag)) {
                            if ($rst_pag[pag_forma] != '8') {
                                if ($Set->delete_cheques($rst_pag[pag_id]) == false) {
                                    $sms = 'Delete_cheques' . pg_last_error();
                                    $aud = 1;
                                }
                            } else {
                                $rst_chq = pg_fetch_array($Set->lista_cheques_id($rst_pag[pag_id_chq]));
                                $cant = $rst_chq[chq_cobro] - $rst_pag[pag_cant];
                                if ($Set->upd_cantidad_cheques($cant, $rst_pag[pag_id_chq]) == false) {
                                    $sms = 'udp_cantidad_cheques_del' . pg_last_error();
                                }
                            }
                            $rst_asi_pag = pg_fetch_array($Set->lista_asientos_mod($rst_pag[pag_id], '9'));
                            if ($Set->delete_asientos($rst_asi_pag[con_asiento]) == false) {
                                $sms = 'Delete_cheques' . pg_last_error();
                                $aud = 1;
                            }
                        }
                    }

                    if ($Clase_pagos->delete_pagos($id) == false) {
                        $sms = 'Delete_pagos1' . pg_last_error();
                        $aud = 1;
                    }
                } else {
                    $sms = 'del_det_factura' . pg_last_error();
                    $aud = 1;
                }


                if (!empty($data[1])) {
                    $data3 = array(
                        strtoupper($data[9]),
                        strtoupper($data[8]),
                        strtoupper($data[20]),
                        strtoupper($data[22]),
                        strtoupper($data[23]),
                        strtoupper($data[24])
                    );
                    if ($Set->upd_email_cliente($data3, $data[7]) == false) {
                        $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                    }
                    $cli = $data[1];
                } else {
                    if (strlen($data[7]) < 11) {
                        $tipo = 'CN';
                    } else {
                        $tipo = 'CJ';
                    }
                    $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
                    $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

                    if ($sec >= 0 && $sec < 10) {
                        $txt = '0000';
                    } else if ($sec >= 10 && $sec < 100) {
                        $txt = '000';
                    } else if ($sec >= 100 && $sec < 1000) {
                        $txt = '00';
                    } else if ($sec >= 1000 && $sec < 10000) {
                        $txt = '0';
                    } else if ($sec >= 10000 && $sec < 100000) {
                        $txt = '';
                    }

                    $retorno = $tipo . $txt . $sec;

                    $da = array(
                        strtoupper($data[6]),
                        strtoupper($data[7]),
                        strtoupper($data[9]),
                        strtoupper($data[20]),
                        strtoupper($data[8]),
                        strtoupper($data[22]),
                        strtoupper($data[23]),
                        $retorno,
                        strtoupper($data[24])
                    );
                    if ($Set->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $aud = 1;
                    }
                    $rst_cl = pg_fetch_array($Set->lista_clientes_codigo($data[7]));
                    $cli = $rst_cl[cli_id];
                }

                if ($Set->update_factura($data, $cli, $id) == false) {
                    $sms = 'Update_factura' . pg_last_error();
                    $aud = 1;
                } else {
//                    $rst_fac = pg_fetch_array($Set->lista_una_factura_num($data[5]));
                    $fac_id = $id;

                    $m = 0;
                    $h = count($data4);
                    while ($m < $h) {
                        $dt1 = explode('&', $data4[$m]);
                        $pg = 0;
                        if ($dt1[1] == '9') {
                            $nf = strtotime("+$dt1[5] day", strtotime($data[4]));
                            $fec = date('Y-m-j', $nf);
                        } else {
                            $fec = $data[4];
                        }
                        if ($dt1[1] != 0) {
                            if ($dt1[1] != 0) {
                                $pagos1.="INSERT INTO erp_pagos_factura (
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
                                                                             pag_id_chq
                                                                            )values (
                                                                            '$fac_id',
                                                                            '$pg',
                                                                            '0',
                                                                            '0',
                                                                            '0',
                                                                            '$fec',
                                                                            '$dt1[1]',
                                                                            '$dt1[2]',
                                                                            '$dt1[3]',
                                                                            '$dt1[4]',
                                                                            '$dt1[5]',
                                                                            '$dt1[6]',
                                                                            '$dt1[7]');";
                            }
                        }
                        $m++;
                    }
                    if ($Clase_pagos->insert_pagos($pagos1) == false) {
                        $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                        $aud = 1;
                    }

                    $n = 0;
                    $i = count($data2);
                    while ($n < $i) {
                        $dt = explode('&', $data2[$n]);
                        $detalles.="INSERT INTO erp_det_factura(
                                    fac_id,
                                    pro_id,
                                    dfc_codigo,
                                    dfc_cod_aux,
                                    dfc_cantidad,
                                    dfc_descripcion,
                                    dfc_precio_unit,
                                    dfc_porcentaje_descuento,
                                    dfc_val_descuento,
                                    dfc_precio_total,
                                    dfc_iva,
                                    dfc_ice, 
                                    dfc_p_ice, 
                                    dfc_cod_ice,
                                    dfc_irbpnr,
                                    dfc_p_irbpnr
                                    )VALUES (
                                    $fac_id,'$dt[0]','$dt[1]','$dt[2]','$dt[3]','$dt[4]','$dt[5]','$dt[6]','$dt[7]','$dt[8]','$dt[9]',
                                                   '$dt[10]','$dt[14]','$dt[15]','$dt[13]','$dt[16]');";

                        $n++;
                    }
                    if ($Set->insert_detalle_factura($detalles) == false) {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    } else {
                        if ($x == 0) {
                            $k = 0;
                            $m = 0;
                            $i = count($data2);
                            while ($k < $i) {
                                $dtm = explode('&', $data2[$k]);
                                $rst_ids = pg_fetch_array($Set->lista_un_producto_id($dtm[0]));
                                $p_ids = $rst_ids[ids];
                                if ($p_ids != 79 && $p_ids != 80) {
                                    $bod = $data[0];
                                    $fec_mov = date('Y-m-d');
                                    $hor_mov = date('H:i:s');
                                    $movimientos.="INSERT INTO erp_i_mov_inv_pt(
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
                                                                            mov_tabla,
                                                                            mov_val_tot)
                                                                            values($dtm[0],
                                                                                    25,
                                                                                    $cli,
                                                                                    $bod,
                                                                                    '$data[5]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$fec_mov',
                                                                                    '$fec_mov',
                                                                                    '$hor_mov',
                                                                                    '$dtm[3]',
                                                                                    '',
                                                                                    '$fec_mov',
                                                                                    '$data[5]',
                                                                                    '',
                                                                                    '',
                                                                                    '$dtm[11]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$dtm[12]');";
                                    $m++;
                                }
                                $k++;
                            }
                            
                            if ($m > 0) {
                                if ($Set->insert_movimiento_pt($movimientos) == false) {
                                    $sms = 'Insert_mov' . pg_last_error();
                                    $aud = 1;
                                }
                            }
                        }
                    }

                    $cn_pag = $Set->lista_detalle_pagos($fac_id);
                    $r_fac = pg_fetch_array($Set->lista_una_factura_id($fac_id));
                    $cli_as = 1;
                    $tc = 76;
                    $td = 77;
                    $ch = 78;
                    $ef = 79;
                    $rt = 80;
                    $nc = 81;
                    $ct = 82;
                    $bn = 83;

                    while ($r_p = pg_fetch_array($cn_pag)) {
                        //// inserta retencion si el pago es retencion///
                        if ($r_p[pag_forma] == 7) {
                            $rst_rs = pg_fetch_array($Set->lista_secuencial_retencion());
                            if (!empty($rst_rs)) {
                                $sec = ($rst_rs[sec] + 1);
                                if ($sec >= 0 && $sec < 10) {
                                    $txt = '000000000';
                                } else if ($sec >= 10 && $sec < 100) {
                                    $txt = '00000000';
                                } else if ($sec >= 100 && $sec < 1000) {
                                    $txt = '0000000';
                                } else if ($sec >= 1000 && $sec < 10000) {
                                    $txt = '000000';
                                } else if ($sec >= 10000 && $sec < 100000) {
                                    $txt = '00000';
                                } else if ($sec >= 100000 && $sec < 1000000) {
                                    $txt = '0000';
                                } else if ($sec >= 1000000 && $sec < 10000000) {
                                    $txt = '000';
                                } else if ($sec >= 10000000 && $sec < 100000000) {
                                    $txt = '00';
                                } else if ($sec >= 100000000 && $sec < 1000000000) {
                                    $txt = '0';
                                } else if ($sec >= 1000000000 && $sec < 10000000000) {
                                    $txt = '';
                                }
                            } else {
                                $txt = '0000000001';
                            }
                            $reg_num = $txt . $sec;
                            $dat_ret = array(
                                $cli,
                                $r_p[chq_numero],
                                $data[6],
                                $data[7],
                                $data[5],
                                '',
                                '1',
                                $r_p[pag_cant],
                                $data[4],
                                $reg_num,
                                '1900-01-01',
                                $data[4],
                                '1900-01-01',
                                $fac_id,
                                '2'
                            );
                            if ($Set->insert_reg_retencion($dat_ret) == false) {
                                $sms = pg_last_error();
                            }
                        }
                        if ($r_p[pag_forma] != 9 && $r_p[pag_forma] != 7) {
                            switch ($r_p[pag_forma]) {
                                case 1:
                                    $form = 'TARJETA DE CREDITO';
                                    $cts = $tc;
                                    $tip = 6;
                                    break;
                                case 2:
                                    $form = 'TARJETA DE DEBITO';
                                    $cts = $td;
                                    $tip = 7;
                                    break;
                                case 3:
                                    $form = 'CHEQUE';
                                    $cts = $ch;
                                    $tip = 1;
                                    break;
                                case 4:
                                    $form = 'EFECTIVO';
                                    $cts = $ef;
                                    $tip = 10;
                                    break;
                                case 5:
                                    $form = 'CERTIFICADOS';
                                    $cts = $ct;
                                    $tip = 8;
                                    break;
                                case 6:
                                    $form = 'BONOS';
                                    $cts = $bn;
                                    $tip = 9;
                                    break;
                                case 7:
                                    $form = 'RETENCION';
                                    $cts = $rt;
                                    $tip = 5;
                                    break;
                            }
                            switch ($r_p[pag_banco]) {
                                case 0:
                                    $banco = '';
                                    break;
                                case 1:
                                    $banco = 'PICHINCHA';
                                    break;
                                case 2:
                                    $banco = 'PACIFICO';
                                    break;
                                case 3:
                                    $banco = 'GUAYAQUIL';
                                    break;
                                case 4:
                                    $banco = 'PRODUBANCO';
                                    break;
                                case 5:
                                    $banco = 'BOLIVARIANO';
                                    break;
                                case 6:
                                    $banco = 'INTERNACIONAL';
                                    break;
                                case 7:
                                    $banco = 'AUSTRO';
                                    break;
                                case 8:
                                    $banco = 'PROMERICA';
                                    break;
                                case 9:
                                    $banco = 'MACHALA';
                                    break;
                                case 10:
                                    $banco = 'BGR';
                                    break;
                                case 11:
                                    $banco = 'CITIBANK';
                                    break;
                                case 12:
                                    $banco = 'PROCREDIT';
                                    break;
                                case 13:
                                    $banco = 'UNIBANCO';
                                    break;
                                case 14:
                                    $banco = 'SOLIDARIO';
                                    break;
                                case 15:
                                    $banco = 'LOJA';
                                    break;
                                case 16:
                                    $banco = 'TERRITORIAL';
                                    break;
                                case 17:
                                    $banco = 'COOPNACIONAL';
                                    break;
                                case 18:
                                    $banco = 'AMAZONAS';
                                    break;
                                case 19:
                                    $banco = 'CAPITAL';
                                    break;
                                case 20:
                                    $banco = 'D-MIRO';
                                    break;
                                case 21:
                                    $banco = 'FINCA';
                                    break;
                                case 22:
                                    $banco = 'COMERCIAL DE MANABI';
                                    break;
                                case 23:
                                    $banco = 'COFIEC';
                                    break;
                                case 24:
                                    $banco = 'LITORAL';
                                    break;
                                case 25:
                                    $banco = 'DELBANK';
                                    break;
                                case 26:
                                    $banco = 'SUDAMERICANO';
                                    break;
                            }
                            $rst_cta1 = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cts));
                            if ($r_p[pag_forma] != 8 && $r_p[pag_forma] != 7) {
                                $cheques = Array($cli,
                                    $form,
                                    $banco,
                                    $r_p[chq_numero],
                                    $data[4],
                                    $data[4],
                                    $r_p[pag_cant],
                                    '0',
                                    '',
                                    $tip,
                                    '',
                                    $r_p[pag_cant],
                                    '0',
                                    $r_p[pag_id],
                                    $rst_cta1[pln_codigo],
                                    $rst_cta1[pln_id],
                                    'ABONO FAC. ' . $data[5]
                                );
                                if ($Set->insert_cheques($cheques) == false) {
                                    $sms = 'Insert_cheques' . pg_last_error();
                                    $aud = 1;
                                } else {
                                    $rst_chq = pg_fetch_array($Set->buscar_cheques($r_p[pag_id]));
                                    $chq_id = $rst_chq[chq_id];
                                }
                            } else {
                                $rst_chq = pg_fetch_array($Set->lista_cheques_id($r_p[pag_id_chq]));
                                $cant = $rst_chq[chq_cobro] + $r_p[pag_cant];
                                if ($Set->upd_cantidad_cheques($cant, $r_p[pag_id_chq]) == false) {
                                    $sms = 'udp_cantidad_cheques' . pg_last_error();
                                }
                                $chq_id = $r_p[pag_id_chq];
                                $form = 'NOTA DE CREDITO';
                                $cts = $nc;
                            }
                            $rst_cliente = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cli_as));
                            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($data[0], $cts));

                            $cta = array(
                                $r_fac[fac_id], //com_id
                                $data[4], //cta_fec
                                $r_p[pag_cant], //cta_monto
                                $form, //forma de pago
                                $rst_cta[pln_codigo], //cta_banco
                                $rst_cliente[pln_id], /// pln_id
                                $data[4], //fec_pag
                                $r_p[pag_id], //pag_id
                                $r_p[chq_numero], //num_doc
                                'ABONO FAC. ' . $data[5], //cta_concepto
                                '2', //asiento
                                $chq_id //chq_id
                            );
                            if ($Set->insert_ctasxcobrar($cta) == false) {
                                $sms = 'Insert_ctasxcobrar' . pg_last_error();
                                $aud = 1;
                            } else {
                                $asi = $Set->siguiente_asiento();
                                $asiento = array(
                                    $asi,
                                    'ABONO FAC. ' . $data[5],
                                    $r_p[chq_numero], //doc
                                    $data[4], //fec
                                    $rst_cta[pln_codigo], //con_debe
                                    $rst_cliente[pln_codigo], //con_haber
                                    $r_p[pag_cant], //val_debe
                                    $r_p[pag_cant], // val_haber
                                    '1', //estado
                                    '9',
                                    $r_p[pag_id],
                                    $cli
                                );
                                if ($Set->insert_asientos($asiento) == false) {
                                    $sms = 'Insert_asientos' . pg_last_error();
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
                    $modulo = 'FACTURA';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        $rst_com = pg_fetch_array($Set->lista_una_factura_id($fac_id));
        echo $sms . '&' . $rst_com[fac_id] . '&' . $mesaje;
        break;

    case 3:
        if ($s == 0) {
            $doc = $_REQUEST[doc];

            $rst_idcli = pg_fetch_array($Set->lista_clientes_codigo($id));
            if ($doc == 8) {
                $cns_chq = $Set->lista_notcre_cli($rst_idcli[cli_id]);
            }
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns_chq)) {
                $n++;
                $tot_canti = $rst[chq_monto] - $rst[chq_cobro];
                if ($tot_canti != 0) {
                    $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_notas_credito('$_REQUEST[l]','$rst[chq_id]')" . " /></td><td>$n</td><td>$rst[chq_numero]</td><td>$tot_canti</td></tr>";
                }
            }
            echo $cli;
        } else {
            $sms = 0;
            $rst = pg_fetch_array($Set->lista_cheques_id($id));
            if (!empty($rst)) {
                $tot_cant = $rst[chq_monto] - $rst[chq_cobro];
                $sms = $rst[chq_numero] . '&' . $tot_cant . '&' . $rst[chq_id];
            }
            echo $sms;
        }
        if ($id == '') {
            echo $sms = 1;
        }
        break;

    case 4:
        $tc = 76;
        $td = 77;
        $ch = 78;
        $ef = 79;
        $rt = 80;
        $nc = 81;
        $ct = 82;
        $bn = 83;

        if ($id == 1) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $tc));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 2) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $td));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 3) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $ch));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 4) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $ef));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 5) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $ct));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 6) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $bn));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 7) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $rt));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        } else if ($id == 8) {
            $rst_cta = pg_fetch_array($Set->lista_asientos_ctas($usu, $nc));
            if ($rst_cta[pln_id] == '') {
                $estado = 1;
            } else {
                $estado = 0;
            }
        }
        echo $estado;
        break;
    case 5:
        $cns = $Set->lista_factura_completo();
        $rst_am = pg_fetch_array($Set->lista_configuraciones());
        $ambiente = $rst_am[con_valor];
        $codigo = "12345678";
        $tp_emison = '1';
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[ndb_clave_acceso])) {
                $emis = pg_fetch_array($Set->lista_emisor($rst[emi_id]));
                if ($emis[emi_cod_establecimiento_emisor] > 0 && $emis[emi_cod_establecimiento_emisor] < 10) {
                    $txem = '00';
                } elseif ($emis[emi_cod_establecimiento_emisor] >= 10 && $emis[emi_cod_establecimiento_emisor] < 100) {
                    $txem = '0';
                } else {
                    $txem = '';
                }
                if ($emis[emi_cod_punto_emision] > 0 && $emis[emi_cod_punto_emision] < 10) {
                    $txpe = '00';
                } elseif ($emis[emi_cod_punto_emision] >= 10 && $emis[emi_cod_punto_emision] < 100) {
                    $txpe = '0';
                } else {
                    $txpe = '';
                }
                $ems = $txem . $emis[emi_cod_establecimiento_emisor];
                $pt_ems = $txpe . $emis[emi_cod_punto_emision];

                $fecha = date_format(date_create($rst[fac_fecha_emision]), 'd/m/Y');
                $f2 = date_format(date_create($rst[fac_fecha_emision]), 'dmY');
                $ndoc = explode('-', $rst[fac_numero]);
                $nfact = str_replace('-', '', $rst[fac_numero]);
                $secuencial = $ndoc[2];
                $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
                $id_comprador = $rst[fac_identificacion];
                if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
                    $tipo_id_comprador = "04"; //RUC 04 
                } else if (strlen($id_comprador) == 10) {
                    $tipo_id_comprador = "05"; //CEDULA 05 
                } else if ($id_comprador == '9999999999999') {
                    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
                } else {
                    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
                }
                $round = 2;
                $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
                $cla = strrev($clave1);
                $n = 0;
                $p = 1;
                $i = strlen($clave1);
                $m = 0;
                $s = 0;
                $j = 2;
                while ($n < $i) {
                    $d = substr($cla, $n, 1);
                    $m = $d * $j;
                    $s = $s + $m;
                    $j++;
                    if ($j == 8) {
                        $j = 2;
                    }
                    $n++;
                }
                $div = $s % 11;
                $digito = 11 - $div;
                if ($digito < 10) {
                    $digito = $digito;
                } else if ($digito == 10) {
                    $digito = 1;
                } else if ($digito == 11) {
                    $digito = 0;
                }
                $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);

                $Set->upd_factura_clave_acceso($clave, $rst[fac_id]);
            }
        }
        break;
    case 6:
        $rst_fac = pg_fetch_array($Set->lista_una_factura_id($id));
        $rst_ret = pg_fetch_array($Set->lista_ret_factura($id));
        $rst_nc = pg_fetch_array($Set->lista_nc_factura($id));
        $rst_nd = pg_fetch_array($Set->lista_nd_factura($id));
        $cns_pg = $Set->lista_pagos_credito($id);
        $cta_v = 0;
        while ($rp = pg_fetch_array($cns_pg)) {
            $rst_cta = pg_fetch_array($Set->lista_ctasxcobrar_pagid($rp[pag_id], $id));
            if (!empty($rst_cta)) {
                $cta_v = 1;
            }
        }
        if (empty($rst_ret) && empty($rst_nc) && empty($rst_nd) && $cta_v == 0) {
            /// anula factura ///
            if ($Set->update_estado_factura($id) == false) {
                $sms = 'upd_factura' . pg_last_error();
                $aud = 1;
            } else {
                /////insert inventario///
                $m = 0;
                $cns_det = $Set->lista_detalle_factura($id);
                while ($dtm = pg_fetch_array($cns_det)) {
                    $rst_ids = pg_fetch_array($Set->lista_un_producto_id($dtm[pro_id]));
                    $rst_mov = pg_fetch_array($Set->lista_un_movimiento('25', $rst_fac[fac_numero], $dtm[pro_id]));
                    $p_ids = $rst_ids[ids];
                    if ($p_ids != 79 && $p_ids != 80) {
                        $bod = $rst_fac[emi_id];
                        $fec_mov = date('Y-m-d');
                        $hor_mov = date('H:i:s');
                        $movimientos.="INSERT INTO erp_i_mov_inv_pt(
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
                                                                            mov_tabla,
                                                                            mov_val_tot)
                                                                            values($dtm[pro_id],
                                                                                    13,
                                                                                    $rst_fac[cli_id],
                                                                                    $bod,
                                                                                    '$rst_fac[fac_numero]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$fec_mov',
                                                                                    '$fec_mov',
                                                                                    '$hor_mov',
                                                                                    '$dtm[dfc_cantidad]',
                                                                                    '',
                                                                                    '$fec_mov',
                                                                                    '$rst_fac[fac_numero]',
                                                                                    '',
                                                                                    '',
                                                                                    '$rst_mov[mov_val_unit]',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '0',
                                                                                    '$rst_mov[mov_val_tot]');";
                        $m++;
                    }
                }

                if ($m > 0) {
                    if ($Set->insert_movimiento_pt($movimientos) == false) {
                        $sms = 'Insert_mov' . pg_last_error();
                        $aud = 1;
                    }
                }
                /////anula estado_cobros///
                $cns_ch = $Set->lista_detalle_pagos($id);
                while ($rst_ch = pg_fetch_array($cns_ch)) {
                    if ($rst_ch[pag_forma] != 8 && $rst_ch[pag_forma] != 9) {
                        if ($Set->update_estado_cobros($rst_ch[pag_id]) == false) {
                            $sms = 'upd_cobros' . pg_last_error();
                            $aud = 1;
                        }
                    } else if ($rst_ch[pag_forma] == 8) {
                        $rst_chq = pg_fetch_array($Set->lista_cheques_id($rst_ch[pag_id_chq]));
                        $cant = $rst_chq[chq_cobro] - $rst_ch[pag_cant];
                        if ($Set->upd_cantidad_cheques($cant, $rst_ch[pag_id_chq]) == false) {
                            $sms = 'udp_cantidad_cheques' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
                if ($Set->update_estado_ctasxcobrar($id) == false) {
                    $sms = 'upd_ctasxcobrar' . pg_last_error();
                    $aud = 1;
                } else {
                    /////anula asiento pagos///
                    $cns_p = $Set->lista_detalle_pagos($id);
                    while ($rst_p = pg_fetch_array($cns_p)) {
                        $cns_asi = $Set->lista_asientos_mod($rst_p[pag_id], '9');
                        while ($rst_asi = pg_fetch_array($cns_asi)) {
                            $asi = $Set->siguiente_asiento();
                            $asiento = array(
                                $asi,
                                'ANULACION ' . $rst_asi[con_asiento],
                                $rst_asi[con_documento], //doc
                                date('Y-m-d'), //fec
                                $rst_asi[con_concepto_haber], //con_debe
                                $rst_asi[con_concepto_debe], //con_haber
                                $rst_asi[con_valor_haber], //val_debe
                                $rst_asi[con_valor_debe], // val_haber
                                '2', //estado
                                '9',
                                $rst_p[pag_id],
                                $rst_asi[cli_id]
                            );
                            if ($Set->insert_asientos($asiento) == false) {
                                $sms = 'Insert_asientos' . pg_last_error();
                                $aud = 1;
                            }
                        }
                    }
                    if ($Set->update_estado_pagos($id) == false) {
                        $sms = 'upd_estado_pagos' . pg_last_error();
                        $aud = 1;
                    }
                }

                /////inserta asiento de anulacion de factura////
                $cns_asf = $Set->lista_asientos_mod($id, '1');
                $asf = $Set->siguiente_asiento();
                while ($rst_asf = pg_fetch_array($cns_asf)) {
                    $asiento = array(
                        $asf,
                        'ANULACION ' . $rst_asf[con_asiento],
                        $rst_asf[con_documento], //doc
                        date('Y-m-d'), //fec
                        $rst_asf[con_concepto_haber], //con_debe
                        $rst_asf[con_concepto_debe], //con_haber
                        $rst_asf[con_valor_haber], //val_debe
                        $rst_asf[con_valor_debe], // val_haber
                        '2', //estado
                        '1',
                        $id,
                        $rst_asf[cli_id]
                    );
                    if ($Set->insert_asientos($asiento) == false) {
                        $sms = 'Insert_asientos' . pg_last_error();
                        $aud = 1;
                    }
                }
            }
        } else {
            if (!empty($rst_ret)) {
                $sms = 1;
            } else if (!empty($rst_nc)) {
                $sms = 2;
            } else if (!empty($rst_nd)) {
                $sms = 3;
            }
            if ($cta_v == 1) {
                $sms = 4;
            }
        }
        echo $sms;
        break;
}
?>
