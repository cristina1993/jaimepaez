<?php

session_start();
include_once '../Clases/clsClase_cuentasxpagar.php';
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$Cxp = new CuentasPagar();
switch ($op) {
    case 0:
        $sms = 0;
        $rst_obl = pg_fetch_array($Cxp->lista_secuencial_obligaciones());
        if (empty($rst_obl)) {
            $sec = 'OP000001';
        } else {
            $txt = '000000';
            $x = substr($rst_obl[obl_codigo], -6);
            $x++;
            $sec = 'OP' . substr($txt, 0, (6 - strlen($x))) . $x;
        }
        $n = 0;
        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);
            if (!$Cxp->inser_pago_obligaciones(array($dat[0], $sec, $dat[1], $dat[2]))) {
                $sms = pg_last_error();
                $n = count($data);
            }
            $n++;
        }

        echo $sms;
        break;
   case 1:
        $sms = 0;
        if ($data[0] == 3) {
            $rst_last_egr = pg_fetch_array($Cxp->lista_ultimo_num_egreso());
            if (empty($rst_last_egr)) {
                $n_egr = '0000000001';
            } else {
                $txt = "0000000000";
                $num = strlen(round($rst_last_egr[obl_num_egreso]));
                $n_egr = substr($txt, 0, (10 - $num)) . ($rst_last_egr[obl_num_egreso] + 1);
            }
        } else {
            $n_egr = '';
        }
        if (!$Cxp->cambia_estado_obligaciones($data[0], $data[1], $data[2], $n_egr)) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        $cod = $data[0];
        $cns = $Cxp->lista_obl_cod($cod);
        $dat = array();
        $cuenta = explode('-', $data[4]);
        $prov = pg_fetch_array($Cxp->lista_asientos_ctas('26'));
        while ($rst = pg_fetch_array($cns)) {
//            $data2 = array($rst[reg_id],
//                $rst[pag_fecha_v],
//                $rst[obl_cantidad],
//                $data[1],
//                '-',
//                $data[4],
//                date('Y-m-d'),
//                $rst[pag_id],
//                $data[3],
//                $rst[reg_num_documento],
//                0,
//                $data[2],
//                2);
            $dt = $rst[reg_id] . '&' .
                    $rst[pag_fecha_v] . '&' .
                    $rst[obl_cantidad] . '&' .
                    $data[1] . '&' .
                    $cuenta[0] . '&' .
                    $prov[pln_codigo] . '&' .
                    $data[5] . '&' .
                    $rst[pag_id] . '&' .
                    $data[3] . '&' .
                    $rst[reg_num_documento] . '&' .
                    '0' . '&' .
                    $data[2] . '&' .
                    '2'. '&' .
                    $data[6];
            array_push($dat, $dt);
        }
        echo json_encode($dat);
        break;

    case 3:
        $sms = 0;
        $cta = explode('-', $data[4]);
        $rst_last_egr = pg_fetch_array($Cxp->lista_ultimo_num_egreso());
        $txt = "000000000";
        $n_egr = substr($txt, 0, strlen($rst_last_egr[obl_num_egreso])) . ($rst_last_egr[obl_num_egreso] + 1);
        if (!$Cxp->cambia_estado_obligacion_pago('3', $data[0], date('Y-m-d'), $data[1], $data[2], $data[3], $cta[0], $n_egr)) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 4:
        $sms = 0;
        if ($x != 0) {
            $rst = pg_fetch_array($Cxp->lista_obligacion_cod($id));
            if ($Cxp->delete_asientos($rst[con_asiento]) == true) {
                if ($Cxp->delete_obligacion($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $sms = 0;
                    $f = $id . '-' . $doc;
                    $modulo = 'GENERAR PAGOS';
                    $accion = 'ANULAR PAGO';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            } else {
                $sms = pg_last_error();
            }
        } else {
            $cns = $Cxp->lista_una_obligacion_cod($id, $doc);
            while ($rst_obl = pg_fetch_array($cns)) {
                $rst_asiento = pg_fetch_array($Cxp->lista_un_asiento_pag_id($rst_obl[pag_id], $rst_obl[obl_fecha_pago]));
                if ($Cxp->delete_ctasxpagar($rst_obl[pag_id], $rst_obl[obl_fecha_pago]) == false) {
                    $sms = pg_last_error();
                }

                if ($Cxp->delete_asientos_pagid($rst_asiento[con_asiento], $rst_obl[pag_id]) == true) {
                    if ($Cxp->delete_obligacion_pagid($id, $rst_obl[pag_id]) == false) {
                        $sms = pg_last_error();
                    } else {
                        $sms = 0;
                        $f = $id . '-' . $doc;
                        $modulo = 'GENERAR PAGOS';
                        $accion = 'ANULAR PAGO';
                        if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                } else {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms;
        break;
}