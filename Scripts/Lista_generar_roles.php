<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_rubros.php';
include_once '../Clases/clsClase_nomina_roles.php';
$Clase_nomina_roles = new Clase_nomina_roles();
$Clase_nomina_rubros = new Clase_nomina_rubros();
if (isset($_GET[search])) {
    $text = strtoupper($_GET[text]);
    $sec = $_GET[sec_id];
    $lista = $_GET[lst_roles];
    $an = $_GET[anio];
    if ($text == '' && $sec == '0' && $lista != '0') {
        $texto = "and split_part(prod,'&',6)='$lista' and split_part(prod,'&',12)='$an'";
        $cns = $Clase_nomina_rubros->lista_buscardor_nomina_pagos($texto);
    } else if ($text == '' && $sec != '0' && $lista == '0') {
        $texto = "and split_part(prod,'&',13)='$sec' and split_part(prod,'&',12)='$an'";
        $cns = $Clase_nomina_rubros->lista_buscardor_nomina_pagos($texto);
    } else if ($text != '' && $sec == '0' && $lista == '0') {
        $texto = "and prod like '%$text%' and split_part(prod,'&',12)='$an'";
        $cns = $Clase_nomina_rubros->lista_buscardor_nomina_pagos($texto);
    }
    $fec_reg = date('Y-m-d');
    $anio = date('Y');
    $usuario = strtoupper($rst_user[usu_person]);
    $rst_fec_sld = pg_fetch_array($Clase_nomina_rubros->lista_fecha_pago_sueldo());
    $rst_fec_he = pg_fetch_array($Clase_nomina_rubros->lista_fecha_pago_he());
    switch ($rst_fec_sld[con_valor]) {
        case 0:
            $f_pago = 'MENSUAL';
            break;
        case 1:
            $f_pago = 'QUINCENAL';
            break;
    }
    switch ($rst_fec_he[con_valor]) {
        case 0:
            $he = 'IGUAL AL PERIODO';
            break;
        case 1:
            $he = 'RETRAZADO';
            break;
    }
    $cns_ru = $Clase_nomina_rubros->lista_rubros_nomina_si();
    $rst_rub_sld = pg_fetch_array($Clase_nomina_rubros->lista_rubro_sueldo());
    //////////////////////// CALCULOS FECHA PAGOS EMPLEADO
    if ($rst_fec_sld[con_valor] == 0) {
        //////////////////// CALCULO FECHA SUELDO MENSUAL
        $s_ini = $rst_fec_sld[con_valor2];
        $s_fnl = $rst_fec_sld[con_valor3];
        $a = date('Y');
        $m = date('m');
        $d = date('d');
        if ($s_ini == 1 && $s_fnl <= 31) {
            if ($d == 28 || $d == 29 || $d == 30 || $d == 31) {
                $dia_i = '01';
                $dia_f = $d;
            } else {
                $dia_i = '01';
                $num_d = cal_days_in_month(CAL_GREGORIAN, $m, $a);
                $dia_f = $num_d;
            }
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $a . '-' . $m . '-' . $dia_f;
            setlocale(LC_ALL, "es_ES");
//            $loc = setlocale(LC_TIME, NULL);
            $mesanterior = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
            $mesactual = strtoupper(strftime("%B"));
        } else if ($s_ini > 1 && $_fnl <= 31) {
            if ($s_ini < 10) {
                $dia_i = '0' . $s_ini;
            } else {
                $dia_i = $s_ini;
            }
            if ($s_fnl < 10) {
                $dia_f = '0' . $s_fnl;
            } else {
                $dia_f = $s_fnl;
            }
            $dt_1MesesDespues = date('Y-m', strtotime('+1 month'));
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $dt_1MesesDespues . '-' . $dia_f;
            setlocale(LC_ALL, "es_ES");
//            $loc = setlocale(LC_TIME, NULL);
            $mesanterior = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
            $mesactual = strtoupper(strftime("%B"));
        }
        //////////////////// CALCULO FECHA HORAS EXTRAS SUELDO MENSUAL
        if ($rst_fec_he[con_valor] == 0) {
            $f_ini_he = $f_ini;
            $f_fin_he = $f_fnl;
        } else if ($rst_fec_he[con_valor] == 1) {
            if ($s_ini < 10) {
                $dia_i = '0' . $s_ini;
            } else {
                $dia_i = $s_ini;
            }
            $fec_ini_sld = strtotime('-1 month', strtotime($f_ini));
            $fec_f = explode('-', $f_fnl);
            $fecf = $fec_f[0] . '-' . $fec_f[1];
            if ($fec_f[2] == 28 || $fec_f[2] == 29 || $fec_f[2] == 30 || $fec_f[2] == 31) {
                $fec_fin_sld = strtotime('-1 month', strtotime($fecf));
                $month = date('Y-m-d', $fec_fin_sld);
                $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                $fec_anterior = date('Y-m-d', strtotime("{$aux} - 1 day"));
            } else if ($fec_f[2] > 1 || $fec_f[2] < 28) {
                $fec_fin_sld = strtotime('-1 month', strtotime($fecf));
                $month = date('Y-m', $fec_fin_sld);
                $fec_anterior = $month . '-' . $fec_f[2];
            }
            $f_ini_he = date('Y-m', $fec_ini_sld) . '-' . $dia_i;
            $f_fin_he = $fec_anterior;
        }
    } else if ($rst_fec_sld[con_valor] == 1) {
        //////////////////// CALCULO FECHA SUELDO QUINCENA
        $s_ini = $rst_fec_sld[con_valor2];
        $s_fnl = $rst_fec_sld[con_valor3];
        $a = date('Y');
        $m = date('m');
        $d = date('d');
        if ($s_ini == 1 && $s_fnl == 15) {
            if ($d >= 16 && $d <= 31) {
                $dia_i = '16';
                $month = $a . '-' . $m;
                $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                $dia_f = date('d', strtotime("{$aux} - 1 day"));
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'PRIMERA DE' . ' ' . $mesact;
                $quicena2 = 'SEGUNGA DE' . ' ' . $mesact;
            } else {
                $dia_i = '0' . $s_ini;
                $dia_f = $s_fnl;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'SEGUNDA DE' . ' ' . $mesante;
                $quicena2 = 'PRIMERA DE' . ' ' . $mesact;
            }
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $a . '-' . $m . '-' . $dia_f;
            $mesanterior = $quicena1;
            $mesactual = $quicena2;
        } else if ($s_ini > 1 && $s_fnl <= 31) {
            if ($d >= $s_ini && $d <= $s_fnl) {
                if ($s_ini < 10) {
                    $dia_i = '0' . $s_ini;
                } else {
                    $dia_i = $s_ini;
                }
                $fecha_i = $a . '-' . $m . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $s_fnl;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'PRIMERA DE' . ' ' . $mesact;
                $quicena2 = 'SEGUNDA DE' . ' ' . $mesante;
            } else if ($d < $s_ini) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('-1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $month . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $d_f;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $quicena1 = 'SEGUNDA DE' . ' ' . $mesante;
                $quicena2 = 'PRIMERA DE' . ' ' . $mesante;
            } else if ($d > $s_fnl) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('+1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $a . '-' . $m . '-' . $dia_i;
                $fecha_f = $month . '-' . $d_f;
                $quicena1;
                $quicena2;
            }
            $f_ini = $fecha_i;
            $f_fnl = $fecha_f;
            $mesanterior = $quicena2;
            $mesactual = $quicena1;
        }
        //////////////////////////CALCULO HORAS EXTRAS POR QUINCENA 
        if ($rst_fec_he[con_valor] == 0) {
            $f_ini_he = $f_ini;
            $f_fin_he = $f_fnl;
        } else if ($rst_fec_he[con_valor] == 1) {
            if ($s_ini == 1 && $s_fnl == 15) {
                if ($d > 15 && $d <= 31) {
                    if ($s_ini < 10) {
                        $dia_i = '0' . $s_ini;
                    } else {
                        $dia_i = $s_ini;
                    }
                    $f_ini_he = $a . '-' . $m . '-' . $dia_i;
                    $f_fin_he = $a . '-' . $m . '-' . $s_fnl;
                } else if ($d >= 1 && $d <= 15) {
                    /// fecha inicial
                    $fec_act = $a . '-' . $m;
                    $mes_ant = strtotime('-1 month', strtotime($fec_act));
                    $month = date('Y-m', $mes_ant);
                    /// fecha final
                    $q_ant = date('Y-m-d', $mes_ant);
                    $aux = date('Y-m-d', strtotime("{$q_ant} + 1 month"));
                    $fec_anterior = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    ////////
                    $dia_i = $s_fnl + 1;
                    $f_ini_he = $month . '-' . $dia_i;
                    $f_fin_he = $fec_anterior;
                }
            } else if ($s_ini > 1 && $s_fnl <= 31) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('-1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $month . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $d_f;
                $f_ini_he = $fecha_i;
                $f_fin_he = $fecha_f;
            }
        }
    }
} else {
    $id = 0;
    $readOnly = '';
    $fec_reg = date('Y-m-d');
    $anio = date('Y');
    $usuario = strtoupper($rst_user[usu_person]);
    $rst_fec_sld = pg_fetch_array($Clase_nomina_rubros->lista_fecha_pago_sueldo());
    $rst_fec_he = pg_fetch_array($Clase_nomina_rubros->lista_fecha_pago_he());
    switch ($rst_fec_sld[con_valor]) {
        case 0:
            $f_pago = 'MENSUAL';
            break;
        case 1:
            $f_pago = 'QUINCENAL';
            break;
    }
    switch ($rst_fec_he[con_valor]) {
        case 0:
            $he = 'IGUAL AL PERIODO';
            break;
        case 1:
            $he = 'RETRAZADO';
            break;
    }

    $cns_ru = $Clase_nomina_rubros->lista_rubros_nomina_si();
    $rst_rub_sld = pg_fetch_array($Clase_nomina_rubros->lista_rubro_sueldo());
    //////////////////////// CALCULOS FECHA PAGOS EMPLEADO
    if ($rst_fec_sld[con_valor] == 0) {
        //////////////////// CALCULO FECHA SUELDO MENSUAL
        $s_ini = $rst_fec_sld[con_valor2];
        $s_fnl = $rst_fec_sld[con_valor3];
        $a = date('Y');
        $m = date('m');
        $d = date('d');
        if ($s_ini == 1 && $s_fnl <= 31) {
            if ($d == 28 || $d == 29 || $d == 30 || $d == 31) {
                $dia_i = '01';
                $dia_f = $d;
            } else {
                $dia_i = '01';
                $num_d = cal_days_in_month(CAL_GREGORIAN, $m, $a);
                $dia_f = $num_d;
            }
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $a . '-' . $m . '-' . $dia_f;
            setlocale(LC_ALL, "es_ES");
//            $loc = setlocale(LC_TIME, NULL);
            $mesanterior = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
            $mesactual = strtoupper(strftime("%B"));
        } else if ($s_ini > 1 && $_fnl <= 31) {
            if ($s_ini < 10) {
                $dia_i = '0' . $s_ini;
            } else {
                $dia_i = $s_ini;
            }
            if ($s_fnl < 10) {
                $dia_f = '0' . $s_fnl;
            } else {
                $dia_f = $s_fnl;
            }
            $dt_1MesesDespues = date('Y-m', strtotime('+1 month'));
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $dt_1MesesDespues . '-' . $dia_f;
            setlocale(LC_ALL, "es_ES");
//            $loc = setlocale(LC_TIME, NULL);
            $mesanterior = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
            $mesactual = strtoupper(strftime("%B"));
        }
        //////////////////// CALCULO FECHA HORAS EXTRAS SUELDO MENSUAL
        if ($rst_fec_he[con_valor] == 0) {
            $f_ini_he = $f_ini;
            $f_fin_he = $f_fnl;
        } else if ($rst_fec_he[con_valor] == 1) {
            if ($s_ini < 10) {
                $dia_i = '0' . $s_ini;
            } else {
                $dia_i = $s_ini;
            }
            $fec_ini_sld = strtotime('-1 month', strtotime($f_ini));
            $fec_f = explode('-', $f_fnl);
            $fecf = $fec_f[0] . '-' . $fec_f[1];
            if ($fec_f[2] == 28 || $fec_f[2] == 29 || $fec_f[2] == 30 || $fec_f[2] == 31) {
                $fec_fin_sld = strtotime('-1 month', strtotime($fecf));
                $month = date('Y-m-d', $fec_fin_sld);
                $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                $fec_anterior = date('Y-m-d', strtotime("{$aux} - 1 day"));
            } else if ($fec_f[2] > 1 || $fec_f[2] < 28) {
                $fec_fin_sld = strtotime('-1 month', strtotime($fecf));
                $month = date('Y-m', $fec_fin_sld);
                $fec_anterior = $month . '-' . $fec_f[2];
            }
            $f_ini_he = date('Y-m', $fec_ini_sld) . '-' . $dia_i;
            $f_fin_he = $fec_anterior;
        }
    } else if ($rst_fec_sld[con_valor] == 1) {
        //////////////////// CALCULO FECHA SUELDO QUINCENA
        $s_ini = $rst_fec_sld[con_valor2];
        $s_fnl = $rst_fec_sld[con_valor3];
        $a = date('Y');
        $m = date('m');
        $d = date('d');
        if ($s_ini == 1 && $s_fnl == 15) {
            if ($d >= 16 && $d <= 31) {
                $dia_i = '16';
                $month = $a . '-' . $m;
                $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
                $dia_f = date('d', strtotime("{$aux} - 1 day"));
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'PRIMERA DE' . ' ' . $mesact;
                $quicena2 = 'SEGUNGA DE' . ' ' . $mesact;
            } else {
                $dia_i = '0' . $s_ini;
                $dia_f = $s_fnl;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'SEGUNDA DE' . ' ' . $mesante;
                $quicena2 = 'PRIMERA DE' . ' ' . $mesact;
            }
            $f_ini = $a . '-' . $m . '-' . $dia_i;
            $f_fnl = $a . '-' . $m . '-' . $dia_f;
            $mesanterior = $quicena1;
            $mesactual = $quicena2;
        } else if ($s_ini > 1 && $s_fnl <= 31) {
            if ($d >= $s_ini && $d <= $s_fnl) {
                if ($s_ini < 10) {
                    $dia_i = '0' . $s_ini;
                } else {
                    $dia_i = $s_ini;
                }
                $fecha_i = $a . '-' . $m . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $s_fnl;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $mesact = strtoupper(strftime("%B"));
                $quicena1 = 'PRIMERA DE' . ' ' . $mesact;
                $quicena2 = 'SEGUNDA DE' . ' ' . $mesante;
            } else if ($d < $s_ini) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('-1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $month . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $d_f;
                setlocale(LC_ALL, "es_ES");
//                $loc = setlocale(LC_TIME, NULL);
                $mesante = strtoupper(strftime("%B", mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"))));
                $quicena1 = 'SEGUNDA DE' . ' ' . $mesante;
                $quicena2 = 'PRIMERA DE' . ' ' . $mesante;
            } else if ($d > $s_fnl) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('+1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $a . '-' . $m . '-' . $dia_i;
                $fecha_f = $month . '-' . $d_f;
                $quicena1;
                $quicena2;
            }
            $f_ini = $fecha_i;
            $f_fnl = $fecha_f;
            $mesanterior = $quicena2;
            $mesactual = $quicena1;
        }
        //////////////////////////CALCULO HORAS EXTRAS POR QUINCENA 
        if ($rst_fec_he[con_valor] == 0) {
            $f_ini_he = $f_ini;
            $f_fin_he = $f_fnl;
        } else if ($rst_fec_he[con_valor] == 1) {
            if ($s_ini == 1 && $s_fnl == 15) {
                if ($d > 15 && $d <= 31) {
                    if ($s_ini < 10) {
                        $dia_i = '0' . $s_ini;
                    } else {
                        $dia_i = $s_ini;
                    }
                    $f_ini_he = $a . '-' . $m . '-' . $dia_i;
                    $f_fin_he = $a . '-' . $m . '-' . $s_fnl;
                } else if ($d >= 1 && $d <= 15) {
                    /// fecha inicial
                    $fec_act = $a . '-' . $m;
                    $mes_ant = strtotime('-1 month', strtotime($fec_act));
                    $month = date('Y-m', $mes_ant);
                    /// fecha final
                    $q_ant = date('Y-m-d', $mes_ant);
                    $aux = date('Y-m-d', strtotime("{$q_ant} + 1 month"));
                    $fec_anterior = date('Y-m-d', strtotime("{$aux} - 1 day"));
                    ////////
                    $dia_i = $s_fnl + 1;
                    $f_ini_he = $month . '-' . $dia_i;
                    $f_fin_he = $fec_anterior;
                }
            } else if ($s_ini > 1 && $s_fnl <= 31) {
                $fec_act = $a . '-' . $m;
                $mes_ant = strtotime('-1 month', strtotime($fec_act));
                $month = date('Y-m', $mes_ant);
                $dia_i = $s_fnl + 1;
                $dia_f = $s_ini - 1;
                if ($dia_f < 10) {
                    $d_f = '0' . $dia_f;
                } else {
                    $d_f = $dia_f;
                }
                $fecha_i = $month . '-' . $dia_i;
                $fecha_f = $a . '-' . $m . '-' . $d_f;
                $f_ini_he = $fecha_i;
                $f_fin_he = $fecha_f;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Generar Roles</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_generar_roles.php';//Cambiar Form_bancos_y_cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_generar_roles.php?id=' + id;//Cambiar Form_bancos_cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2:
                        frm.src = '../Scripts/frm_pdf_rol_pago.php?id=' + id;//Cambiar pdf rol pagos 
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }

            function cambiar_estado(std, id) {
                $.post("actions_nomina_rubros.php", {op: 2, std: std, id: id},
                function (dt) {
                    if (dt == 0) {
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_roles.php';
            }

            function general_roles(prd, anio) {
                var data = Array(fec_registro.value,
                        forma_pago.value,
                        nom_periodo.value,
                        fec_desde_prd.value,
                        fec_hasta_prd.value,
                        horas_extras.value,
                        fec_desde_hext.value,
                        fec_hasta_hext.value,
                        usuario.value,
                        anio_pago.value
                        );
                $.post("actions_nomina_rubros.php", {op: 9, 'data[]': data, perd: prd, anio: anio},
                function (dt) {
                    if (dt == 0) {
                        cancelar();
                    } else if (dt == 1) {
                        alert('Ya existen Roles generados con el mes y año seleccionados');
                    } else {
                        alert(dt);
                    }
                });
            }

            function generar(a) {
                an = $('#anio').val();
                prd = $('#lst_roles').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/frm_pdf_rol_pago_masivo.php?periodo=' + prd + '&anio=' + an;//Cambiar a frm_pdf_rol_pago
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }

        </script>
        <style>
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert('¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
        <table style="width: 100%" id="tbl">
            <caption class="tbl_head">
                <center class="cont_menu">
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float: right" onclick="window.print()" title="Imprimir Documento" src="../img/print_iconop.png" width="16px">
                </center>
                <center class="cont_title">GENERAR ROLES</center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:-3px;padding:7px;">
                        <button class="btn" title="Generar Roles" onclick="general_roles(nom_periodo.value, anio_pago.value)">Generar Roles</button>
                    </div>
                    <div style="float:right;margin-top:4px;padding:5px;">
                        <input type="hidden" size="9" id="fec_registro" value="<?php echo $fec_reg ?>" />
                        <input type="hidden" size="20" id="usuario" value="<?php echo $usuario ?>" />
                        <input type="hidden" size="8" id="forma_pago" value="<?php echo $f_pago ?>" />
                        <input type="hidden" size="9" id="fec_desde_prd" value="<?php echo $f_ini ?>" />
                        <input type="hidden" size="9" id="fec_hasta_prd" value="<?php echo $f_fnl ?>" />
                        <input type="hidden" size="20" id="horas_extras" value="<?php echo $he ?>" />
                        <input type="hidden" size="9" id="fec_desde_hext" value="<?php echo $f_ini_he ?>" />
                        <input type="hidden" size="9" id="fec_hasta_hext" value="<?php echo $f_fin_he ?>" />
                        <select id="nom_periodo">
                            <option value="<?php echo $mesactual ?>"><?php echo $mesactual ?></option>
                            <option value="<?php echo $mesanterior ?>"><?php echo $mesanterior ?></option>
                        </select>
                        <select id="anio_pago">
                            <?php
                            $InicioYear = 2015; // Aqui coloca el año de inicio, el que estará más abajo
                            $MinYear = $InicioYear - 1;
                            $ActualYear = $anio; // Aquí coloca el año actual 
                            for ($i = $ActualYear; $i > $MinYear; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>'; // Aqui puedes agregarle cosas como class, name, id.
                            }
                            ?>
                        </select>
                    </div>
                    <a href="#" class="btn" style="float: left;margin-top: 7px;padding: 7px" title="Nuevo Registro" onclick="auxWindow(0)">Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        &nbsp;&nbsp;&nbsp;
                        BUSCAR:
                        <input type="text" id="text" name="text">
                        Seccion:
                        <select name="sec_id" id="sec_id" style="width:150px">
                            <option value="0">Elija un Seccion</option>
                            <?php
                            $cns_sec = $Clase_nomina_rubros->lista_secciones();
                            while ($rst_sec = pg_fetch_array($cns_sec)) {
                                echo "<option value='$rst_sec[sec_id]'>$rst_sec[sec_gerencia] - $rst_sec[sec_area] - $rst_sec[sec_descricpion]</option>";
                            }
                            ?>
                        </select>
                        LISTA DE ROLES:
                        <select id="lst_roles" name="lst_roles">
                            <option value="0">TODO</option>
                            <?php
                            $cns_lst = $Clase_nomina_rubros->lista_periodo_pago();
                            while ($rst_lst = pg_fetch_array($cns_lst)) {
                                echo "<option value='$rst_lst[nom_periodo]'>$rst_lst[nom_periodo]</option>";
                            }
                            ?>
                        </select>
                        AÑO:
                        <select id="anio" name="anio">
                            <?php
                            $cns_anio = $Clase_nomina_rubros->lista_anio_pagos();
                            while ($rst_anio = pg_fetch_array($cns_anio)) {
                                echo "<option value='$rst_anio[nom_anio]'>$rst_anio[nom_anio]</option>";
                            }
                            ?>
                            <option value="2015">2015</option>
                        </select>
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                        <input type="button" onclick="generar(0)" value="IMPRIMIR ROLES" />
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th># Empleado</th>
            <th>Periodo</th>
            <th>Año</th>
            <th>Nombre</th>
            <th>Seccion</th>
            <th>Base</th>
            <th>Dias</th>
            <?php
            $cns1 = $Clase_nomina_rubros->lista_rubros_nomina_si();
            while ($rst_rubros = pg_fetch_array($cns1)) {
                echo "<th class='locales' lang='$rst_rubros[rub_id]'>$rst_rubros[rub_descripcion]</th>";
                ?>
                <?php
            }
            ?>
            <th class="locales" lang="">TOTAL</th> 
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_emp = pg_fetch_array($Clase_nomina_rubros->lista_empleados_id($rst[id]));
                $rst_sec = pg_fetch_array($Clase_nomina_rubros->lista_emp_seccion($rst[seccion]));
                $rst_in_eg = pg_fetch_array($Clase_nomina_rubros->lista_tot_ingresos_egresos($rst[nom_id]));
                $total = $rst_in_eg[ingreso] - $rst_in_eg[egreso];
                echo "<tr class='row' lang='$rst[id]' >
                            <td>$n</td>
                            <td class='desc' >$rst[id]</td>
                            <td class='desc' >$rst[periodo]</td>
                            <td class='desc' >$rst[anio]</td>
                            <td class='desc precio'>$rst[ape_pat] $rst[nombre]</td>
                            <td class='desc' >$rst_sec[sec_descricpion]</td> 
                            <td>$rst[sueldo_base]</td>
                            <td>$rst[dias_trabj]</td>";
                $l = 0;
                $cns1 = $Clase_nomina_rubros->lista_rubros_nomina_si();
                while ($rst_rubros = pg_fetch_array($cns1)) {
                    $l++;
                    $cnt = $rst[loc . $l];
                    echo "<td align='right'  name='$rst_rubros[rub_id]$l' class='cnt$l'  >$cnt</td>";
                }
                echo "<td>$total</td>
                      <td align='center'>
                      <img src='../img/upd.png' width='16px'  class='auxBtn' onclick='auxWindow(1, $rst[nom_id])' />
                      <img src='../img/orden.png' width='16px' class='auxBtn' onclick='auxWindow(2, $rst[nom_id], 0)' />
                      </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<script>
    var prd = '<?php echo $lista ?>';
    $('#lst_roles').val(prd);
    var an = '<?php echo $an ?>';
    $('#anio').val(an);
</script>
