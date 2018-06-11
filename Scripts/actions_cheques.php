<?php

include_once '../Clases/clsClase_cheques.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cheques = new Clase_cheques();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$fec = $_REQUEST[fec];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Clase_cheques->insert_cheque($str) == FALSE) {
                $sms = pg_last_error();
            } else {
                $j = 0;
                while ($j < count($fields)) {
                    $f = $f . strtoupper($fields[$j] . '&');
                    $j++;
                }
                $modulo = 'CHEQUES';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
            $cheq = pg_fetch_array($Clase_cheques->lista_ultimo_cheque($str[0], $str[2], $str[3]));
            $id = $cheq[chq_id];
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Clase_cheques->upd_cheques($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $j = 0;
                while ($j < count($fields)) {
                    $f = $f . strtoupper($fields[$j] . '&');
                    $j++;
                }
                $modulo = 'CHEQUES';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms . '&' . $id;
        break;
    case 1:
        if ($Clase_cheques->delete_cheque($id) == false) {
            $sms = pg_last_error();
        } else {
            $fields = str_replace("&", ",", $fields[0]);
            $modulo = 'CHEQUES';
            $accion = 'DELETE';
            if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:
        if ($s == 0) {
            $cns = $Clase_cheques->lista_clientes_search(strtoupper($id));
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
            echo $id;
            $rst = pg_fetch_array($Clase_cheques->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id];
            }
            echo $sms;
        }


        break;

    case 3:
        $sms = 0;
        $data = strtoupper($data);
        if ($s == 1) {
            $txt = "set chq_deposito='$data', chq_estado='$s', chq_fecha='$fec' ";
        } else {
//            $texto='PAG'.$id;
//            if ($Clase_cheques->delete_asientos($texto) == FALSE) {
//                $sms = pg_last_error();
//            }else{
//             if ($Clase_cheques->delete_pagos($id) == FALSE) {
//                $sms = pg_last_error();
//            }   
//            }
            $txt = "set chq_observacion='$data', chq_estado='$s', chq_fecha='$fec'";
        }
        if ($Clase_cheques->upd_estado($id, $txt) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 4:
        $cta = pg_fetch_array($Clase_cheques->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;

    case 5:
        $sms = 0;
        $n = 0;
        while ($n < count($data)) {
            $dt = explode('&', $data[$n]);

            switch ($dt[4]) {
                case 1:
                    $fp = 'CHEQUE';
                    break;
                case 2:
                    $fp = 'CHEQUE';
                    break;
                case 3:
                    $fp = 'NOTA CREDITO';
                    break;
                case 4:
                    $fp = 'NOTA DEBITO';
                    break;
                case 5:
                    $fp = 'NOTA DEBITO';
                    break;
                case 6:
                    $fp = 'TARJETA DE CREDITO';
                    break;
                case 7:
                    $fp = 'TARJETA DE DEBITO';
                    break;
                case 8:
                    $fp = 'CERTIFICADOS';
                    break;
                case 9:
                    $fp = 'BONOS';
                    break;
                case 10:
                    $fp = 'EFECTIVO';
                    break;
            }

            $rst_tip = pg_fetch_array($Clase_cheques->lista_clientes_codigo($dt[5]));
            if ($rst_tip[cli_tipo_cliente] == 0) {
                $cli = 1;
            } else {
                $cli = 2;
            }
            $rst_cli = pg_fetch_array($Clase_cheques->lista_asientos_ctas($cli));

            $fd = pg_num_rows($Clase_cheques->listar_una_cta_comid($dt[1]));
            $fc = pg_num_rows($Clase_cheques->lista_pagos($dt[1]));
            if ($fd == $fc || $fc == 1) {
                $rst2 = pg_fetch_array($Clase_cheques->lista_pagos($dt[1], 'desc'));
            } else {
                $rst2 = pg_fetch_array($Clase_cheques->buscar_un_pago($dt[1]));
            }
            if (empty($rst2)) {
                $rst2[pag_id] = 0;
            }

            $data1 = Array(
                $dt[1],
                date('Y-m-d'),
                $dt[2],
                $fp,
                $dt[7],
                $rst_cli[pln_id],
                $dt[3],
                $rst2[pag_id],
                $dt[6],
                'PAGO FACTURACION',
                '0',
                $dt[0]
            );
            if ($Clase_cheques->insert_ctasxcobrar($data1) == false) {
                $sms = 'ctas x cob' . pg_last_error();
            } else {
                $rst_chq = pg_fetch_array($Clase_cheques->lista_un_cheque($dt[0]));
                $mto = $rst_chq[chq_cobro] + $dt[2];
                if ($Clase_cheques->updte_cheque($dt[0], $mto) == false) {
                    $sms = 'upd_cob_chq' . pg_last_error();
                } else {
                    $asiento = $Clase_cheques->siguiente_asiento();
                    $dat = Array(
                        $asiento,
                        $dt[9],
                        $dt[8],
                        $dt[3],
                        $dt[7],
                        $rst_cli[pln_codigo],
                        $dt[2],
                        $dt[2],
                        '0'
                    );
                    if ($Clase_cheques->insert_asientos($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        $j = 0;
                        while ($j < count($fields)) {
                            $f = $f . strtoupper($fields[$j] . '&');
                            $j++;
                        }
                        $modulo = 'COTROL DE COBROS';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            }
            $n++;
        }
        echo $sms;
        break;
}
?>
