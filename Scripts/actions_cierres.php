<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_cierres.php';
$Clase_cierre = new Clase_cierres();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$l = $_REQUEST[l];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:

        if ($s == 0) {
            $doc = $_REQUEST[doc];

            $rst_idcli = pg_fetch_array($Clase_cierre->lista_clientes_codigo($id));
            if ($doc == 8) {
                $cns_chq = $Clase_cierre->lista_notcre_cli($rst_idcli[cli_id]);
            }
            $sms = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns_chq)) {
                $n++;
                $tot_canti = $rst[chq_monto] - $rst[chq_cobro];
                if ($tot_canti != 0) {
                    $sms .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_notas_credito('$_REQUEST[l]','$rst[chq_id]')" . " /></td><td>$n</td><td>$rst[chq_numero]</td><td>$tot_canti</td></tr>";
                }
            }
//            echo $sms;
        } else {
            $sms = 0;
            $rst = pg_fetch_array($Clase_cierre->lista_cheques_id($id));
            if (!empty($rst)) {
                $tot_cant = $rst[chq_monto] - $rst[chq_cobro];
                $sms = $rst[chq_numero] . '&' . $tot_cant . '&' . $rst[chq_id];
            }
//            echo $sms;
        }
        if ($id == '') {
            echo $sms = 1;
        }
//        echo '&' . $sms;
        echo $sms;
        break;

    case 1:
        $sms = 0;
        $pag_id = $data[0];
        $rst = pg_fetch_array($Clase_cierre->lista_pagos_id($pag_id));
        $pag_ant = $rst[pag_forma];
        $monto_ant = $rst[pag_cant];
        $r_fac = pg_fetch_array($Clase_cierre->lista_factura_id($data[8]));
        $chq = pg_fetch_array($Clase_cierre->lista_cheques_id($rst[pag_id_chq]));
        $monto = $chq[chq_cobro] - $rst[pag_cant];

        $cli1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '1'));
        $tc1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '76'));
        $td1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '77'));
        $ch1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '78'));
        $ef1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '79'));
        $rt1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '80'));
        $nc1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '81'));
        $ct1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '82'));
        $bn1 = pg_fetch_array($Clase_cierre->lista_asientos_ctas_orden($r_fac[emi_id], '83'));

        $cli = $cli1[cas_id];
        $tc = $tc1[cas_id];
        $td = $td1[cas_id];
        $ch = $ch1[cas_id];
        $ef = $ef1[cas_id];
        $rt = $rt1[cas_id];
        $nc = $nc1[cas_id];
        $ct = $ct1[cas_id];
        $bn = $bn1[cas_id];

        if (!empty($rst[chq_numero])) {
            if ($Clase_cierre->update_cheques_mto($chq[chq_id], $monto) == false) {
                $sms = 'upd_chq' . pg_last_error();
            }
        }
        if ($sms == 0) {
            if ($data[3] == 8) {
                $chq1 = pg_fetch_array($Clase_cierre->lista_cheques_id($data[2]));
                $mto = $chq1[chq_cobro] + $data[7];
                if ($Clase_cierre->update_cheques_mto($chq1[chq_id], $mto) == false) {
                    $sms = 'upd_chq' . pg_last_error();
                }
                $form = 'NOTA DE CREDITO';
                $cts = $nc;

                if ($rst[pag_forma] != 8) {
                    $dch = pg_fetch_array($Clase_cierre->lista_cheques_pagid($data[0]));
                    if (empty($rst[pag_id_chq])) {
                        if ($Clase_cierre->delete_cheques($dch[chq_id]) == false) {
                            $sms = 'del_chq' . pg_last_error();
                        }
                    }
                }
            } else {
                if($data[3] != 9) {
                    switch ($data[3]) {
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


                    if (!empty($data[0])) {
                        $dch = Array($r_fac[cli_id],
                            $data[1],
                            $data[9],
                            $data[9],
                            $data[7],
                            $tip,
                            $data[7],
                            $data[0],
                            $form
                        );
                        $chq2 = pg_fetch_array($Clase_cierre->lista_cheques_pagid($data[0]));
                        if (empty($chq2)) {
                            if ($Clase_cierre->insert_cheque($dch) == false) {
                                $sms = 'insert_cheques' . pg_last_error();
                            }
                        } else {
                            if ($Clase_cierre->update_cheques($dch, $chq2[chq_id]) == false) {
                                $sms = 'upd_cheques' . pg_last_error();
                            }
                        }
                    }
                }
            }
        }
        $rst_cliente = pg_fetch_array($Clase_cierre->lista_asientos_ctas($cli));
        $rst_cta = pg_fetch_array($Clase_cierre->lista_asientos_ctas($cts));
        if ($sms == 0) {
            if (!empty($data[0])) {
                if ($Clase_cierre->update_pagos($data) == false) {
                    $sms = 'upd_pagos' . pg_last_error();
                } else {
                    $rst_pag = pg_fetch_array($Clase_cierre->lista_pagos_id($data[0]));
                    $cta = pg_fetch_array($Clase_cierre->lista_ctaxcobrar_pag_id($data[0]));
                    if (empty($rst_pag[pag_id_chq])) {
                        $chq3 = pg_fetch_array($Clase_cierre->lista_cheques_pagid($data[0]));
                        $ch_id = $chq3[chq_id];
                    } else {
                        $ch_id = $rst_pag[pag_id_chq];
                    }
                    $cuenta = Array(
                        $data[7],
                        $form,
                        $rst_cta[pln_codigo], //cta_banco
                        $rst_cliente[pln_id],
                        $pag_id,
                        $data[1],
                        $ch_id
                    );
                    if ($Clase_cierre->update_ctaxcobrar($cuenta, $data[0]) == false) {
                        $sms = 'upd_ctasxcobrar' . pg_last_error();
                    } else {
                        //////buscar cta anulacion de pagos /////////
                        switch ($pag_ant) {
                            case 1:
                                $form_ant = 'TARJETA DE CREDITO';
                                $cts_ant = $tc;
                                break;
                            case 2:
                                $form_ant = 'TARJETA DE DEBITO';
                                $cts_ant = $td;
                                break;
                            case 3:
                                $form_ant = 'CHEQUE';
                                $cts_ant = $ch;
                                break;
                            case 4:
                                $form_ant = 'EFECTIVO';
                                $cts_ant = $ef;
                                break;
                            case 5:
                                $form_ant = 'CERTIFICADOS';
                                $cts_ant = $ct;
                                break;
                            case 6:
                                $form_ant = 'BONOS';
                                $cts_ant = $bn;
                                break;
                            case 7:
                                $form_ant = 'RETENCION';
                                $cts_ant = $rt;
                                break;
                            case 8:
                                $form_ant = 'NOTA DE CREDITO';
                                $cts_ant = $nc;
                                break;
                        }
                        $rst_cta_ant = pg_fetch_array($Clase_cierre->lista_asientos_ctas($cts_ant));
                        $asi = $Clase_cierre->siguiente_asiento();
                        $asiento = array(
                            $asi,
                            'ANULACION CUENTAS X COBRAR',
                            $r_fac[fac_numero], //doc
                            $data[9], //fec
                            $rst_cta_ant[pln_codigo], //con_debe
                            $rst_cliente[pln_codigo], //con_haber
                            $monto_ant, // val_debe
                            $monto_ant, //val_haber
                            '0' //estado
                        );
                        if ($Clase_cierre->insert_asientos($asiento) == false) {
                            $sms = 'Insert_asientos_anulacionctas' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
            } else {
                if ($Clase_cierre->insert_pagos($data) == false) {
                    $sms = 'insrt_pagos' . pg_last_error();
                } else {

                    $rst_pag = pg_fetch_array($Clase_cierre->lista_ultimo_pago_fac($data[8]));
                    $dch1 = Array($r_fac[cli_id],
                        $data[1],
                        $data[9],
                        $data[9],
                        $data[7],
                        $tip,
                        $data[7],
                        $rst_pag[pag_id],
                        $form
                    );
                    if ($Clase_cierre->insert_cheque($dch1) == false) {
                        $sms = 'insert_cheques' . pg_last_error();
                    } else {
                        $chq4 = pg_fetch_array($Clase_cierre->lista_cheques_pagid($rst_pag[pag_id]));
                        $cuenta1 = Array(
                            $data[8],
                            $data[9],
                            $data[7],
                            $form,
                            $rst_cta[pln_codigo], //cta_banco
                            $rst_cliente[pln_id],
                            $rst_pag[pag_id],
                            $data[9],
                            $data[1],
                            'PAGO FACTURACION',
                            '0',
                            $chq4[chq_id]
                        );
                        if ($Clase_cierre->insert_ctaxcobrar($cuenta1) == false) {
                            $sms = 'insert_ctasxcobrar' . pg_last_error();
                        }
                    }
                }
            }

            if ($sms == 0) {
                $asi = $Clase_cierre->siguiente_asiento();
                $asiento = array(
                    $asi,
                    'CUENTAS X COBRAR',
                    $r_fac[fac_numero], //doc
                    $data[9], //fec
                    $rst_cta[pln_codigo], //con_debe
                    $rst_cliente[pln_codigo], //con_haber
                    $data[7], // val_debe
                    $data[7], //val_haber
                    '0' //estado
                );
                if ($Clase_cierre->insert_asientos($asiento) == false) {
                    $sms = 'Insert_asientos' . pg_last_error();
                    $aud = 1;
                }
            }
        }

        if ($sms == 0) {
            $m = 0;
            while ($m < count($fields)) {
                $f = $f . strtoupper($fields[$m] . '&');
                $m++;
            }
            $modulo = 'PAGOS FACTURA';
            $accion = 'MODIFICAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $fields[0]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }

        echo $sms;
        break;

    case 2:
        $cta = pg_fetch_array($Clase_cierre->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;

    case 3:
        $sms = 0;
        if ($Clase_cierre->update_arqueo_caja($data, $id) == false) {
            $sms = 'updta_cierres' . pg_last_error();
        } else {
            $n = 0;
            while ($n < count($data2)) {
                $ncr .=$data2[$n] . '&';
                $n++;
            }
            if ($Clase_cierre->update_notas_arqueo_caja($ncr, $id) == false) {
                $sms = 'updta_notas_cierre' . pg_last_error();
            } else {
                $m = 0;
                while ($m < count($fields)) {
                    $f = $f . strtoupper($fields[$m] . '&');
                    $m++;
                }
                $modulo = 'ARQUEO DE CAJA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
}
?>
