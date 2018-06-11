<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_reg_nota_credito.php';
$Reg_nota_credito = new Clase_reg_nota_credito();
$Adt = new Auditoria();
$act = $_REQUEST[act]; //Accion
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$x = $_REQUEST[x];
$c = $_REQUEST[c];
$ctr_inv = $_REQUEST[ctr_inv];
$fields = $_REQUEST[fields];
$emi = $_REQUEST[emi];
switch ($act) {
    case 0:
        $sms = 0;
        $aud = 0;
        $ctsxpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('38'));
        $iva = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('39'));
        $ice = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('40'));
        $des = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('41'));
        $irb = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('42'));
        $pro = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('43'));
        $ctpag = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('44'));

        if ($ctsxpag[pln_id] == '' || $iva[pln_id] == '' || $ice[pln_id] == '' || $des[pln_id] == '' || $irb[pln_id] == '' || $pro[pln_id] == '' || $ctpag[pln_id] == '') {
            $sms = 1;
        } else {
            if (empty($id)) {
                $rst_sec = pg_fetch_array($Reg_nota_credito->lista_secuencial($data[22]));
                if (!empty($rst_sec)) {
                    $sms = 3;
                } else {
                    $rst_sal = pg_fetch_array($Reg_nota_credito->lista_saldo($data[26]));
                    $saldo = $rst_sal[total] + $rst_sal[debito] - $rst_sal[credito];

                    if ($Reg_nota_credito->insert_nota_credito($data) == TRUE) {
                        $nc = pg_fetch_array($Reg_nota_credito->lista_un_notac_num($data[22]));
                        $nrc_id = $nc[rnc_id];
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_credito->insert_det_nota_credito($dt, $nrc_id) == FALSE) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                    } else {
                        $sms = 'Insert' . pg_last_error();
                        $aud = 1;
                    }

                    //////se inserta movimientos cuando la factura tiene numero de ingreso///
                    $rst_rg = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($data[26]));
                    if (!empty($rst_rg[reg_num_ingreso])) {
                        $cns_det = $Reg_nota_credito->lista_detalle_nota_credito($nc[rnc_id]);
                        while ($rst_d = pg_fetch_array($cns_det)) {
                            $rst_pro = pg_fetch_array($Reg_nota_credito->lista_un_producto($rst_d[pro_id]));
                            if ($rst_pro[ids] != 79 && $rst_pro[ids] != 80) {
                                $rst_c = pg_fetch_array($Reg_nota_credito->ultimo_costo($rst_d[pro_id], "and m.bod_id=1"));
                                $rst_c[mov_val_unit] = (($rst_c[ingreso] - $rst_c[egreso]) / ($rst_c[icnt] - $rst_c[ecnt]));

                                $dat_mov = Array(
                                    $rst_pro[id],
                                    '06',
                                    $nc[cli_id],
                                    '1',
                                    $nc[rnc_num_registro],
                                    '',
                                    $nc[rnc_fecha_emision],
                                    $rst_d[drc_cantidad],
                                    '0',
                                    round($rst_c[mov_val_unit], 2),
                                    (round($rst_d[drc_cantidad], 2) * round($rst_c[mov_val_unit], 2)),
                                    '',
                                    '0'
                                );
                                if (!$Reg_nota_credito->insert_transferencia($dat_mov)) {
                                    $sms = 'insert_movimientos' . pg_last_error();
                                }
                            }
                        }
                    }


                    /////inserta asiento cuando esta completo el detalle /////
                    $nc_as = 0;
                    if ($_REQUEST[vd] != 1) {
                        $asiento = $Reg_nota_credito->siguiente_asiento();
                        $cns_sum = $Reg_nota_credito->lista_sum_cuentas($nc[rnc_id]);
                        while ($rst1 = pg_fetch_array($cns_sum)) {
                            $dat_asi_det = array(
                                $asiento,
                                'DEVOLUCION COMPRA',
                                $nc[rnc_numero],
                                $nc[rnc_fecha_emision],
                                '',
                                $rst1[drc_codigo_cta],
                                0,
                                round($rst1[dtot] + $rst1[ddesc], 2),
                                '1',
                                '6',
                                $nc[rnc_id],
                                $nc[cli_id]
                            );

                            if ($Reg_nota_credito->insert_asientos($dat_asi_det) == false) {
                                $sms = 'asi_detalle' . pg_last_error();
                                $aud = 1;
                            }
                        }

                        $dat_asi = array(
                            $nc[rnc_subtotal],
                            $nc[rnc_numero],
                            $nc[rnc_fecha_emision],
                            '',
                            $nc[rnc_total_iva],
                            $nc[rnc_total_valor],
                            $nc[rnc_total_ice],
                            $nc[rnc_irbpnr],
                            $nc[rnc_total_propina],
                            $nc[rnc_total_descuento],
                            '0',
                            $iva[pln_codigo],
                            $ctsxpag[pln_codigo],
                            $ice[pln_codigo],
                            $irb[pln_codigo],
                            $des[pln_codigo],
                            $pro[pln_codigo],
                            'DEVOLUCION COMPRA',
                            '1',
                            '6',
                            $nc[rnc_id],
                            $nc[cli_id]
                        );
                        if ($Reg_nota_credito->insert_asiento_mp($dat_asi, $asiento) == false) {
                            $sms = 'insert_asiento_mp ' . pg_last_error();
                            $aud = 1;
                        }
                        $nc_as = 1;
                    }
                    /// inserta ctasxpagar cuando sea mayor o igual al valor de la nota///   
                    $pag = 0;
                    if (round($saldo, 2) >= round($data[19], 2)) {
                        if ($aud == 0) {
                            $monto = $data[19];
                            $cns_pid = $Reg_nota_credito->lista_pagos($nc[reg_id]);
                            $banco = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('44'));
                            while ($rst_p = pg_fetch_array($cns_pid)) {
                                $rst_pag = pg_fetch_array($Reg_nota_credito->lista_saldo_pagos($rst_p[pag_id]));
                                $t_pag = ($rst_pag[total] + $rst_pag[debito]) - $rst_pag[credito];
                                if ($monto > 0) {
                                    if ($t_pag != 0) {
                                        if ($t_pag <= $monto) {
                                            $cta = array(
                                                $nc[reg_id], //com_id
                                                $data[23], //cta_fec
                                                $t_pag, //cta_monto
                                                'NOTA DE CREDITO', //forma de pago
                                                $banco[pln_codigo], //cta_banco
                                                $ctsxpag[pln_id], /// pln_id
                                                $data[23], //fec_pag
                                                $rst_p[pag_id], //pag_id
                                                $nc[rnc_numero], //num_doc
                                                'ABONO REG_FACTURA', //cta_concepto
                                                '2', //asiento
                                                '0', //chq_id
                                                $nrc_id //doc_id
                                            );
                                            if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                                                $sms = 'ctasxpagar ' . pg_last_error();
                                                $aud = 1;
                                            }
                                            $monto = $monto - $t_pag;
                                        } else {
                                            $cta = array(
                                                $nc[reg_id], //com_id
                                                $data[23], //cta_fec
                                                $monto, //cta_monto
                                                'NOTA DE CREDITO', //forma de pago
                                                $banco[pln_codigo], //cta_banco
                                                $ctsxpag[pln_id], /// pln_id
                                                $data[23], //fec_pag
                                                $rst_p[pag_id], //pag_id
                                                $nc[rnc_numero], //num_doc
                                                'ABONO REG_FACTURA', //cta_concepto
                                                '2', //asiento
                                                '0', //chq_id
                                                $nrc_id //doc_id
                                            );
                                            if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                                                $sms = 'ctasxpagar ' . pg_last_error();
                                                $aud = 1;
                                            }
                                            $monto = $monto - $monto;
                                        }
                                    }
                                }
                            }
                            ///asiento_ctasxpagar
                            $asi = $Reg_nota_credito->siguiente_asiento();
                            $asiento = array(
                                $asi,
                                'ABONO REG_FAC. ' . $data[7],
                                $nc[rnc_numero], //doc
                                $data[23], //fec
                                $banco[pln_codigo], //con_debe
                                $ctsxpag[pln_codigo], //con_haber
                                $data[19], //val_debe
                                $data[19], // val_haber
                                '1',
                                '13',
                                $nc[rnc_id],
                                $nc[cli_id]
                            );
                            if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                                $sms = 'asientos ' . pg_last_error();
                                $aud = 1;
                            } else {
                                $pag = 1;
                            }
                        }
                    }
                    ////modifica estados de la nota de credito
                    if ($nc_as == 0 && $pag == 0) {
                        $sts = 4; ///sin cobro S/A
                    } else if ($nc_as == 1 && $pag == 0) {
                        $sts = 0; ///sin cobro
                    } else if ($nc_as == 0 && $pag == 1) {
                        $sts = 5; /// registrado S/A
                    } else if ($nc_as == 1 && $pag == 1) {
                        $sts = 1; /// registrado 
                    }
                    if ($Reg_nota_credito->update_estado_reg_nc($nc[rnc_id], $sts) == false) {
                        $sms = 'asientos ' . pg_last_error();
                        $aud = 1;
                    }

                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REG. NOTA DE CREDITO';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[22]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            } else {/////modificar
                $nrc_id = $id;
                $nc = pg_fetch_array($Reg_nota_credito->lista_una_nota_credito($id));
                $rst_sal = pg_fetch_array($Reg_nota_credito->lista_saldo($data[26]));
                $saldo = $rst_sal[total] + $rst_sal[debito] - ($rst_sal[credito] - $nc[rnc_total_valor]);
                if ($Reg_nota_credito->update_nota_credito($data, $id) == FALSE) {
                    $sms = pg_last_error() . 'updnota_credito';
                    $aud = 1;
                } else {
                    $rst_rg = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($data[26]));
                    if (!empty($rst_rg[reg_num_ingreso])) {
                        if ($Reg_nota_credito->delete_movimiento($data[22]) == FALSE) {
                            $sms = pg_last_error() . 'delet_detalle';
                            $aud = 1;
                        }
                    }

                    if ($Reg_nota_credito->delete_ctasxpagar($id, 'NOTA DE CREDITO') == FALSE) {
                        $sms = pg_last_error() . 'delet_ctasxcobrar';
                        $aud = 1;
                    }

                    $rst_as = pg_fetch_array($Reg_nota_credito->lista_asientos_mod($id, '13'));
                    if ($Reg_nota_credito->delete_asientos($rst_as[con_asiento]) == FALSE) {
                        $sms = pg_last_error() . 'delet_asiento_ctasxpagar';
                        $aud = 1;
                    }

                    $rst_asn = pg_fetch_array($Reg_nota_credito->lista_asientos_mod($id, '6'));
                    if (!empty($rst_asn)) {
                        if ($Reg_nota_credito->delete_asientos($rst_asn[con_asiento]) == FALSE) {
                            $sms = pg_last_error() . 'delet_asiento';
                            $aud = 1;
                        }
                    }

                    if ($Reg_nota_credito->delete_det_nota_credito($id) == FALSE) {
                        $sms = pg_last_error() . 'delet_detalle';
                        $aud = 1;
                    } else {
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Reg_nota_credito->insert_det_nota_credito($dt, $nrc_id) == FALSE) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                        $nc = pg_fetch_array($Reg_nota_credito->lista_una_nota_credito($id));
                        if (!empty($rst_rg[reg_num_ingreso])) {
                            $cns_det = $Reg_nota_credito->lista_detalle_nota_credito($nc[rnc_id]);
                            while ($rst_d = pg_fetch_array($cns_det)) {
                                $rst_pro = pg_fetch_array($Reg_nota_credito->lista_un_producto($rst_d[pro_id]));
                                if ($rst_pro[ids] != 79 && $rst_pro[ids] != 80) {
                                    $rst_c = pg_fetch_array($Reg_nota_credito->ultimo_costo($rst_d[pro_id], "and m.bod_id=1"));
                                    $rst_c[mov_val_unit] = (($rst_c[ingreso] - $rst_c[egreso]) / ($rst_c[icnt] - $rst_c[ecnt]));
                                    $dat_mov = Array(
                                        $rst_pro[id],
                                        '06',
                                        $nc[cli_id],
                                        '1',
                                        $nc[rnc_num_registro],
                                        '',
                                        $nc[rnc_fecha_emision],
                                        $rst_d[drc_cantidad],
                                        '0',
                                        round($rst_c[mov_val_unit], 2),
                                        (round($rst_d[drc_cantidad], 2) * round($rst_c[mov_val_unit], 2)),
                                        '',
                                        '0'
                                    );
                                    if (!$Reg_nota_credito->insert_transferencia($dat_mov)) {
                                        $sms = 'insert_movimientos' . pg_last_error();
                                    }
                                }
                            }
                        }
                    }
                }

                /////inserta asiento cuando esta completo el detalle /////
                $nc_as = 0;
                if ($_REQUEST[vd] != 1) {
                    $asiento = $Reg_nota_credito->siguiente_asiento();
                    $cns_sum = $Reg_nota_credito->lista_sum_cuentas($nc[rnc_id]);
                    while ($rst1 = pg_fetch_array($cns_sum)) {
                        $dat_asi_det = array(
                            $asiento,
                            'DEVOLUCION COMPRA',
                            $nc[rnc_numero],
                            $nc[rnc_fecha_emision],
                            '',
                            $rst1[drc_codigo_cta],
                            0,
                            round($rst1[dtot] + $rst1[ddesc], 2),
                            '1',
                            '6',
                            $nc[rnc_id],
                            $nc[cli_id]
                        );

                        if ($Reg_nota_credito->insert_asientos($dat_asi_det) == false) {
                            $sms = 'asi_detalle' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    $dat_asi = array(
                        $nc[rnc_subtotal],
                        $nc[rnc_numero],
                        $nc[rnc_fecha_emision],
                        '',
                        $nc[rnc_total_iva],
                        $nc[rnc_total_valor],
                        $nc[rnc_total_ice],
                        $nc[rnc_irbpnr],
                        $nc[rnc_total_propina],
                        $nc[rnc_total_descuento],
                        '0',
                        $iva[pln_codigo],
                        $ctsxpag[pln_codigo],
                        $ice[pln_codigo],
                        $irb[pln_codigo],
                        $des[pln_codigo],
                        $pro[pln_codigo],
                        'DEVOLUCION COMPRA',
                        '1',
                        '6',
                        $nc[rnc_id],
                        $nc[cli_id]
                    );
                    if ($Reg_nota_credito->insert_asiento_mp($dat_asi, $asiento) == false) {
                        $sms = 'insert_asiento_mp ' . pg_last_error();
                        $aud = 1;
                    }
                    $nc_as = 1;
                }

                /// inserta ctasxpagar cuando sea mayor o igual al valor de la nota///   
                $pag = 0;
                if (round($saldo, 2) >= round($data[19], 2)) {
                    if ($aud == 0) {
                        $monto = $data[19];
                        $cns_pid = $Reg_nota_credito->lista_pagos($nc[reg_id]);
                        $banco = pg_fetch_array($Reg_nota_credito->lista_asientos_ctas('44'));
                        while ($rst_p = pg_fetch_array($cns_pid)) {
                            $rst_pag = pg_fetch_array($Reg_nota_credito->lista_saldo_pagos($rst_p[pag_id]));
                            $t_pag = ($rst_pag[total] + $rst_pag[debito]) - $rst_pag[credito];
                            if ($monto > 0) {
                                if ($t_pag != 0) {
                                    if ($t_pag <= $monto) {
                                        $cta = array(
                                            $nc[reg_id], //com_id
                                            $data[23], //cta_fec
                                            $t_pag, //cta_monto
                                            'NOTA DE CREDITO', //forma de pago
                                            $banco[pln_codigo], //cta_banco
                                            $ctsxpag[pln_id], /// pln_id
                                            $data[23], //fec_pag
                                            $rst_p[pag_id], //pag_id
                                            $nc[rnc_numero], //num_doc
                                            'ABONO REG_FACTURA', //cta_concepto
                                            '2', //asiento
                                            '0', //chq_id
                                            $nrc_id //doc_id
                                        );
                                        if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                                            $sms = 'ctasxpagar ' . pg_last_error();
                                            $aud = 1;
                                        }
                                        $monto = $monto - $t_pag;
                                    } else {
                                        $cta = array(
                                            $nc[reg_id], //com_id
                                            $data[23], //cta_fec
                                            $monto, //cta_monto
                                            'NOTA DE CREDITO', //forma de pago
                                            $banco[pln_codigo], //cta_banco
                                            $ctsxpag[pln_id], /// pln_id
                                            $data[23], //fec_pag
                                            $rst_p[pag_id], //pag_id
                                            $nc[rnc_numero], //num_doc
                                            'ABONO REG_FACTURA', //cta_concepto
                                            '2', //asiento
                                            '0', //chq_id
                                            $nrc_id //doc_id
                                        );
                                        if ($Reg_nota_credito->insert_ctasxpagar($cta) == false) {
                                            $sms = 'ctasxpagar ' . pg_last_error();
                                            $aud = 1;
                                        }
                                        $monto = $monto - $monto;
                                    }
                                }
                            }
                        }
                        ///asiento_ctasxpagar
                        $asi = $Reg_nota_credito->siguiente_asiento();
                        $asiento = array(
                            $asi,
                            'ABONO REG_FAC. ' . $data[7],
                            $nc[rnc_numero], //doc
                            $data[23], //fec
                            $banco[pln_codigo], //con_debe
                            $ctsxpag[pln_codigo], //con_haber
                            $data[19], //val_debe
                            $data[19], // val_haber
                            '1',
                            '13',
                            $nc[rnc_id],
                            $nc[cli_id]
                        );
                        if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                            $sms = 'asientos ' . pg_last_error();
                            $aud = 1;
                        } else {
                            $pag = 1;
                        }
                    }
                }

                if ($nc_as == 0 && $pag == 0) {
                    $sts = 4; ///sin cobro S/A
                } else if ($nc_as == 1 && $pag == 0) {
                    $sts = 0; ///sin cobro
                } else if ($nc_as == 0 && $pag == 1) {
                    $sts = 5; /// registrado S/A
                } else if ($nc_as == 1 && $pag == 1) {
                    $sts = 1; /// registrado 
                }

                if ($Reg_nota_credito->update_estado_reg_nc($nc[rnc_id], $sts) == false) {
                    $sms = 'asientos ' . pg_last_error();
                    $aud = 1;
                }

                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'REG. NOTA DE CREDITO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[22]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms . '&' . $nrc_id;
        break;
    case 1:
        if ($s == 0) {
            $cns = $Reg_nota_credito->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
            echo $sms = 1;
        } else {
            $sms;
            $rst = pg_fetch_array($Reg_nota_credito->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . trim($rst[cli_id]);
            }
            echo $sms;
        }
        break;

    case 2:
        $rst = pg_fetch_array($Reg_nota_credito->lista_nota_cred_duplicada($id, $data));
        echo $rst[rnc_identificacion] . '&' . $rst[rnc_numero];
        break;

    case 4:
        if ($x == 0) {
            $cns = $Reg_nota_credito->lista_una_factura_nfact($id);
            $fac = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[reg_id];
                $fac .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_factura2('$rst[reg_id]')" . " /></td><td>$n</td><td>$rst[reg_num_documento]</td><td>$rst[cli_raz_social]</td></tr>";
                $sms = 1;
            }
            echo $sms . '&&' . $fac;
        } else {
            $rst = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($id));
            if ($rst[reg_id] != '') {
                $cns = $Reg_nota_credito->lista_det_factura($rst[reg_id]);
                while ($rst2 = pg_fetch_array($cns)) {
                    $n++;
                    $rst_s = pg_fetch_array($Reg_nota_credito->suma_prod_nota_credito($rst2[reg_id], $rst2[pro_id]));
                    if (empty($rst_s)) {
                        $entr = $rst2[det_cantidad];
                    } else {
                        $entr = $rst2[det_cantidad] - $rst_s[sum];
                    }
                    $rst_rg = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($rst2[reg_id]));
                    if (!empty($rst_rg[reg_num_ingreso])) {
                        $fra = "and m.bod_id=1";
                        $rst_inv = pg_fetch_array($Reg_nota_credito->total_ingreso_egreso_fact($rst2[pro_id], $fra));
                        $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                    } else {
                        $inv = '';
                    }

                    switch ($rst2[det_tipo]) {

                        case 26:
                            $nom_tipo = 'PRODUCTO TERMINADO';
                            break;
                        case 69:
                            $nom_tipo = 'MATERIA PRIMA';
                            break;
                        case 79:
                            $nom_tipo = 'SERVICIO';
                            $inv = '';
                            break;
                        case 80:
                            $nom_tipo = 'OTROS';
                            $inv = '';
                            break;
                    }
                    if (empty($rst2[pln_id])) {
                        $rst2[pln_id] = 0;
                    }
                    $a = '"';
                    $lista.="<tr>
                                        <td><input type='text' size='3'  id='item$n' class='itm'  lang='$n' value='$n' readonly  style='text-align:right' /></td>   
                                        <td>
                                            <input type='text' size='10' id='nom_tipo$n'  readonly value='$nom_tipo' lang='$n'/>
                                            <input type='hidden' size='10' id='tipo$n'  readonly value='$rst2[det_tipo]' lang='$n'/>
                                        </td>  
                                        <td><input type='text' size='13' id='cuenta$n'  readonly lang='$n' value='$rst2[reg_codigo_cta]'/>
                                            <input hidden type='text' size='10' id='pln_id$n' lang='$n' value='$rst2[pln_id]'/></td>
                                        <td class='td1'><input type='text' size='12' id='cod_producto$n' readonly value='$rst2[det_codigo_empresa]' lang='$n' list='productos' onblur='this.style.width = '100px', load_producto(this)' onfocus='this.style.width = '500px''/>
                                            <input hidden type='text' size='10' id='pro_id$n' lang='$n' value='$rst2[pro_id]'/>
                                                </td>
                                        <td><input type='text' size='10' id='cod_externo$n'  readonly value='$rst2[det_codigo_externo]' lang='$n'/></td> 
                                        <td><input type='text' size='15' id='descripcion$n'  readonly value='$rst2[det_descripcion]' lang='$n'/></td>  
                                        <td><input type='text' size='7' id='inventario$n'  readonly lang='$n' value='$inv'/></td>
                                        <td class='td1'><input id='cantidad$n' type='text' lang='$n' readonly value='" . str_replace(',', '', number_format($entr, $s)) . "' size='7'/></td>
                                        <td><input type='text' size='7'  id='cantidadf$n' onchange='inventario(this)'  value='" . str_replace(',', '', number_format($entr, $s)) . "' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' style='text-align:right' lang='$n' /></td>
                                        <td><input type='text' size='7' readonly id='precio_unitario$n'  value='" . str_replace(',', '', number_format($rst2[det_vunit], $s)) . "' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' lang='$n' onchange='calculo()'/></td>                  
                                        <td><input type='text' size='7'  readonly id='descuento$n'  value='" . str_replace(',', '', number_format($rst2[det_descuento_porcentaje], $s)) . "'  style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' lang='$n' onchange='calculo()'/></td>                  
                                        <td>
                                            <input type='text' size='7'  id='descuent$n'  value='" . str_replace(',', '', number_format($rst2[det_descuento_moneda], $s)) . "' lang='$n' readonly  />
                                            <label hidden id='lbldescuent$n' lang='$n'></label>
                                        </td>
                                        <td><input type='text' size='7'  readonly id='iva$n'  value='$rst2[det_impuesto]' style='text-align:right' lang='$n' onblur='calculo(), this.value = this.value.toUpperCase()' /></td>                  
                                        <td><input type='text' size='7'  id='precio_total$n'  value='" . str_replace(',', '', number_format($rst2[det_total], $s)) . "' style='text-align:right' lang='$n' readonly />                  
                                            <label  hidden id='lblprecio_total$n' lang='$n'></label></td>               
                                        <td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>
                                  </tr>";
                }
            }
            echo $rst[reg_id] . '&' .
            str_replace('&', '', $rst[reg_femision]) . '&' .
            str_replace('&', '', $rst[reg_ruc_cliente]) . '&' .
            str_replace('&', '', $rst[cli_raz_social]) . '&' .
            str_replace('&', '', $lista) . '&' .
            $rst[cli_id] . '&' .
            str_replace(',', '', number_format($rst[reg_propina], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt12], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal0], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt_noiva], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_sbt_excento], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_tdescuento], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_ice], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_iva12], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_irbpnr], $s)) . '&' .
            str_replace(',', '', number_format($rst[reg_total], $s));
        }
        break;

    case 5:
        $sms = 0;
        if ($Reg_nota_credito->delete_asientos($id, $data1, 'DEVOLUCION COMPRA') == false) {
            $sms = pg_last_error();
        } else {
            if ($Reg_nota_credito->delete_asientos($id, $data1, 'CUENTAS X PAGAR') == false) {
                $sms = 'ctasxpagar ' . pg_last_error();
            } else {
                if ($Reg_nota_credito->delete_ctasxpagar($id, 'NOTA DE CREDITO') == false) {
                    $sms = 'ctasxpagar ' . pg_last_error();
                } else {
                    if ($Reg_nota_credito->delete_movimiento($data) == false) {

                        $sms = pg_last_error();
                    } else {
                        if ($Reg_nota_credito->delete_det_nota_credito($id) == false) {
                            $sms = pg_last_error();
                        } else {
                            if ($Reg_nota_credito->delete_nota_credito($id) == false) {
                                $sms = pg_last_error();
                            } else {
                                $modulo = 'REG. NOTA DE CREDITO';
                                $accion = 'ELIMINAR';
                                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                                    $sms = "Auditoria" . pg_last_error();
                                }
                            }
                        }
                    }
                }
            }
        }

        echo $sms;
        break;

    case 6:
        $sms = 0;
        if ($Reg_nota_credito->update_estado_reg_nc($_REQUEST[md_id], $_REQUEST[estado]) == true) {
            if ($Reg_nota_credito->update_estado_det_nc($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                $sms = 'Update_reg_det' . pg_last_error();
            } else {
                //////se inserta movimientos cuando la factura tiene numero de ingreso///
                $rst_nc = pg_fetch_array($Reg_nota_credito->lista_una_nota_credito($_REQUEST[md_id]));
                $rst_rg = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($rst_nc[reg_id]));
                if (!empty($rst_rg[reg_num_ingreso])) {
                    $cns_det = $Reg_nota_credito->lista_detalle_nota_credito($_REQUEST[md_id]);
                    while ($rst_d = pg_fetch_array($cns_det)) {
                        $rst_pro = pg_fetch_array($Reg_nota_credito->lista_un_producto($rst_d[pro_id]));
                        if ($rst_pro[ids] != 79 && $rst_pro[ids] != 80) {
                            $rst_m = pg_fetch_array($Reg_nota_credito->lista_movimiento_nc($rst_nc[rnc_num_registro], '06', $rst_pro[id]));
                            $dat_mov = Array(
                                $rst_pro[id],
                                '02',
                                $rst_nc[cli_id],
                                '1',
                                $rst_nc[rnc_num_registro],
                                '',
                                $rst_nc[rnc_fecha_emision],
                                $rst_d[drc_cantidad],
                                '0',
                                $rst_m[mov_val_unit],
                                ($rst_d[drc_cantidad] * $rst_m[mov_val_unit]),
                                '',
                                '0'
                            );
                            if (!$Reg_nota_credito->insert_transferencia($dat_mov)) {
                                $sms = 'insert_movimientos' . pg_last_error();
                            }
                        }
                    }
                }

                $cns = $Reg_nota_credito->lista_asientos_mod($_REQUEST[md_id], '6');
                $asi = $Reg_nota_credito->siguiente_asiento();
                while ($rst_as = pg_fetch_array($cns)) {
                    $asiento = array(
                        $asi,
                        'ANULACION ' . $rst_as[con_asiento],
                        $rst_as[con_documento], //doc
                        date('Y-m-d'), //fec
                        $rst_as[con_concepto_haber], //con_debe
                        $rst_as[con_concepto_debe], //con_haber
                        $rst_as[con_valor_haber], //val_debe
                        $rst_as[con_valor_debe], // val_haber
                        '2',
                        '6',
                        $_REQUEST[md_id],
                        $rst_as[cli_id]
                    );
                    if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                        $sms = 'asientos ' . pg_last_error();
                        $aud = 1;
                    }
                }


                if ($Reg_nota_credito->update_ctasxpagar($_REQUEST[md_id], '1') == false) {
                    $sms = 'ctasxpagar ' . pg_last_error();
                } else {
                    $cns = $Reg_nota_credito->lista_asientos_mod($_REQUEST[md_id], '13');
                    $asic = $Reg_nota_credito->siguiente_asiento();
                    while ($rst_asc = pg_fetch_array($cns)) {
                        $asiento = array(
                            $asic,
                            'ANULACION ' . $rst_asc[con_asiento],
                            $rst_asc[con_documento], //doc
                            date('Y-m-d'), //fec
                            $rst_asc[con_concepto_haber], //con_debe
                            $rst_asc[con_concepto_debe], //con_haber
                            $rst_asc[con_valor_haber], //val_debe
                            $rst_asc[con_valor_debe], // val_haber
                            '2',
                            '13',
                            $_REQUEST[md_id],
                            $rst_asc[cli_id]
                        );
                        if ($Reg_nota_credito->insert_asientos($asiento) == false) {
                            $sms = 'asientos ' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
            }
        } else {
            $sms = 'Update_reg_encab' . pg_last_error();
        }
        echo $sms;
        break;
    case 7:
        $cta = pg_fetch_array($Reg_nota_credito->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;
}
?>



















