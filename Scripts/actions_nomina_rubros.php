<?php

include_once '../Clases/clsClase_nomina_rubros.php';
include_once("../Clases/clsAuditoria.php");
$Clase_nomina_rubros = new Clase_nomina_rubros();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$fields = $_REQUEST[fields];
$perd = $_REQUEST[perd];
$anio = $_REQUEST[anio];
switch ($op) {
    case 0:
        $sms = 0;
        if ($id == 0) {
            if ($Clase_nomina_rubros->insert_nomina_rubros($data) == FALSE) {
                $sms = 'Insert Nomina Rubros' . pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'NOMINA RUBROS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok1';
                }
            }
        } else {
            if ($Clase_nomina_rubros->update_nomina_rubros($data, $id) == FALSE) {
                $sms = 'Update Nomina Rubros' . pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'NOMINA RUBROS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $cta = pg_fetch_array($Clase_nomina_rubros->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;
    case 2:
        $sms = 0;
        $std = $_REQUEST[std];
        if ($Clase_nomina_rubros->upd_estado_nomina_rubros($std, $id) == false) {
            $sms = 'Update estado nomina rubros' . pg_last_error();
        }
        echo $sms;
        break;
    case 3:
        if ($s == 0) {
            $cns = $Clase_nomina_rubros->lista_empleados_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[emp_apellido_paterno] . ' ' . $rst[emp_apellido_materno] . ' ' . $rst[emp_nombres];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_empleado2('$rst[emp_documento]')" . " /></td><td>$n</td><td>$rst[emp_documento]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Clase_nomina_rubros->lista_empleados_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[emp_documento] . '&' . $rst[emp_apellido_paterno] . ' ' . $rst[emp_apellido_materno] . ' ' . $rst[emp_nombres] . '&' . $rst[emp_id] . '&' . $rst[emp_sueldo_inicial];
            }
            echo $sms;
        }
        break;
    case 4:
        $sms = 0;
        $aud = 0;
        if ($id == 0) {
            $rst = pg_fetch_array($Clase_nomina_rubros->lista_nomina_mes_anio($data[2], $data[15], $data[8]));
            if (!empty($rst)) {
                $sms = 1;
            } else {
                if ($Clase_nomina_rubros->insert_nomina($data) == TRUE) {
                    $nma = pg_fetch_array($Clase_nomina_rubros->lista_una_nomina($data[14]));
                    $nomina_id = $nma[nom_id];
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_nomina_rubros->insert_det_nomina($dt, $nomina_id) == false) {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert_enc' . pg_last_error();
                    $aud = 1;
                }
                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'ROL DE PAGOS';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[14]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        } else {
            if ($Clase_nomina_rubros->upd_nomina($data, $id) == TRUE) {
                $n = 0;
                while ($n < count($data2)) {
                    $dt = explode('&', $data2[$n]);
                    if ($Clase_nomina_rubros->upd_nomina_det($dt, $id) == false) {
                        $sms = 'Upd_det' . pg_last_error();
                        $aud = 1;
                    }
                    $n++;
                }
            } else {
                $sms = 'Upd_enc' . pg_last_error();
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'ROL DE PAGOS';
                $accion = 'EDITAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[14]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 5:
        $sms = 0;
        $n = 0;
        $i = count($data);
        while ($n < $i) {
            $dt = explode('&', $data[$n]);
            $dat = array(
                $dt[1],
                strtoupper($dt[2]),
                $dt[3],
                $dt[4],
                $dt[5],
                $dt[6]
            );
            if ($Clase_nomina_rubros->upd_encabezado_nomina($dat, $dt[0]) == false) {
                $sms = 'Upd_encb_nomina' . pg_last_error();
            }
            $n++;
        }
        echo $sms;
        break;
    case 6:
        $sms = 0;
        $aud = 0;
        $dat = Array(
            $data[1],
            $data[2],
            $data[3],
            $data[4],
            date('Y-m-d'),
            $data[5]
        );
        if ($Clase_nomina_rubros->upd_encabezado_nomina($dat, $data[0]) == false) {
            $sms = 'Upd_encb_nomina2' . pg_last_error();
            $aud = 1;
        }
        if ($aud == 0) {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'PAGOS A ROLES';
            $accion = 'MODIFICAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 7:
        $sms = 0;
        if ($Clase_nomina_rubros->upd_chq_tran_imp() == true) {
            $n = 0;
            $i = count($data);
            while ($n < $i) {
                $dt = explode('&', $data[$n]);
                if ($Clase_nomina_rubros->upd_chq_tran_imp_id($dt[1], $dt[0]) == false) {
                    $sms = 'Upd_val_chq_trans' . pg_last_error();
                }
                $n++;
            }
        } else {
            $sms = 'upd_val_chqtrans_0' . pg_last_error();
        }
        echo $sms;
        break;
    case 8:
        $sms = 0;
        $std = $_REQUEST[std];
        if ($Clase_nomina_rubros->upd_estado_nomina($std, $id) == false) {
            $sms = 'Update estado nomina rubros' . pg_last_error();
        }
        echo $sms;
        break;
    case 9:
        $sms = 0;
        $n = 0;
        $cns_emp = $Clase_nomina_rubros->lista_empleados_activos();
        while ($rst = pg_fetch_array($cns_emp)) {
            $n++;
            $empleado = $rst[emp_id];
            $rst_nom = pg_fetch_array($Clase_nomina_rubros->lista_nomina_mes_anio($perd, $anio, $empleado));
            if (empty($rst_nom)) {
                $sueldo = $rst[emp_sueldo_inicial];
                $rst_sec = pg_fetch_array($Clase_nomina_rubros->lista_secuencial_nomina());
                if (empty($rst_sec)) {
                    $sec = 1;
                } else {
                    $sec = ($rst_sec[nom_secuencial] + 1);
                }
                if ($sec >= 0 && $sec < 10) {
                    $tx = '00000000';
                } else if ($sec >= 10 && $sec < 100) {
                    $tx = '0000000';
                } else if ($sec >= 100 && $sec < 1000) {
                    $tx = '000000';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $tx = '00000';
                } else if ($sec >= 10000 && $sec < 100000) {
                    $tx = '0000';
                } else if ($sec >= 100000 && $sec < 1000000) {
                    $tx = '000';
                } else if ($sec >= 1000000 && $sec < 10000000) {
                    $tx = '00';
                } else if ($sec >= 10000000 && $sec < 100000000) {
                    $tx = '0';
                } else if ($sec >= 100000000 && $sec < 1000000000) {
                    $tx = '';
                }
                $secuencial = $tx . $sec;
                if ($data[1] == 'MENSUAL') {
                    $dias_trab = 30;
                } else if ($data[1] == 'QUINCENAL') {
                    $dias_trab = 15;
                }
                $dat = Array(
                    $data[0],
                    $data[1],
                    $data[2],
                    $data[3],
                    $data[4],
                    $data[5],
                    $data[6],
                    $data[7],
                    $empleado,
                    $sueldo,
                    $dias_trab,
                    '0',
                    '0',
                    $data[8],
                    $secuencial,
                    $data[9]
                );
                if ($Clase_nomina_rubros->insert_nomina($dat) == true) {
                    $nma = pg_fetch_array($Clase_nomina_rubros->lista_una_nomina($secuencial));
                    $cns_ru = $Clase_nomina_rubros->lista_rubros_nomina_si();
                    $id_nom = $nma[nom_id];
                    //////// PROGRAMACION NUEVA
                    if ($data[1] == 'MENSUAL') {
                        $dias_trab = 30;
                    } else if ($data[1] == 'QUINCENAL') {
                        $dias_trab = 15;
                    }
                    $h_sld = $sueldo / 30 / 8;
                    $d_sld = $sueldo / 30;
                    $sld_em = $dias_trab * $d_sld;

                    ///////////////////////////
                    while ($rst_ru = pg_fetch_array($cns_ru)) {
                        $id_rub = $rst_ru[rub_id];
                        if ($id_rub == 1) {
                            $cantidad = $sld_em;
                            $valor = $sld_em;
                            $tipo = 1;
                            $formula = 'DT*VD';
                        } else {
                            $cantidad = '0';
                            $valor = '0';
                            $tipo = '0';
                            $formula = '0';
                        }
                        if ($rst_ru[rub_grupo] == 'IESS' && $rst_ru[rub_valor] == 9.45 && $id_rub != 1) {
                            $cantidad = $rst_ru[rub_valor];
                            $valor = $sld_em * $rst_ru[rub_valor] / 100;
                            $tipo = 2;
                            $formula = 'B*C';
                        }
                        $dt = Array(
                            $id_rub,
                            $cantidad,
                            $valor,
                            $tipo,
                            $formula
                        );
                        if ($Clase_nomina_rubros->insert_det_nomina($dt, $id_nom) == false) {
                            $sms = 'Insert_gen_detnom' . pg_last_error();
                        }
                    }
                } else {
                    $sms = 'insert_generar_nom' . pg_last_error();
                }
            }
        }
        echo $sms;
        break;
}
?>