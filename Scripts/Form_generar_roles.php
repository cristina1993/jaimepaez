<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_rubros.php';
$Rub = new Clase_nomina_rubros();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Rub->lista_nomina_id($id));
    $secuencial = $rst[nom_secuencial];
    $fec_reg = $rst[nom_fec_registro];
    $usuario = $rst[nom_usuario];
    $f_pago = $rst[nom_forma_pago];
    $anio = $rst[nom_anio];
    $f_ini = $rst[nom_fp_desde];
    $f_fnl = $rst[nom_fp_hasta];
    $he = $rst[nom_horas_extras];
    $f_ini_he = $rst[nom_fh_desde];
    $f_fin_he = $rst[nom_fh_hasta];
    $rst_emp = pg_fetch_array($Rub->lista_empleados_id($rst[nom_empleado]));
    $empleado = $rst_emp[emp_apellido_paterno] . ' ' . $rst_emp[emp_apellido_materno] . ' ' . $rst_emp[emp_nombres];
    $periodo = $rst[nom_periodo];
    $h_sld = $rst[nom_sueldo_base] / 30 / 8;
    $d_sld = $rst[nom_sueldo_base] / 30;
    $readOnly = 'readOnly';
} else {
    $id = 0;
    $readOnly = '';
    $fec_reg = date('Y-m-d');
    $anio = date('Y');
    $usuario = strtoupper($rst_user[usu_person]);
    $rst_fec_sld = pg_fetch_array($Rub->lista_fecha_pago_sueldo());
    $rst_fec_he = pg_fetch_array($Rub->lista_fecha_pago_he());
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
    $rst_sec = pg_fetch_array($Rub->lista_secuencial_nomina());
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

    $cns_ru = $Rub->lista_rubros_nomina_si();
    $rst_rub_sld = pg_fetch_array($Rub->lista_rubro_sueldo());
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
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario Generar Roles Pago</title>
        <script>
            var id =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                if (id == 0) {
                    $("#rol_desembolso1").attr('checked', true);
                }
                $('#con_clientes').hide();
                posicion_aux_window();
            });

            function save(id) {
                var data = Array(
                        nom_fec_registro.value,
                        nom_forma_pago.value,
                        nom_periodo.value.toUpperCase(),
                        nom_fp_desde.value,
                        nom_fp_hasta.value,
                        nom_horas_extras.value,
                        nom_fh_desde.value,
                        nom_fh_hasta.value,
                        emp_id.value,
                        rol_sld_empleado.value,
                        rol_dias_trab.value,
                        '0',
                        rol_num_desembolso.value,
                        rol_usuario.value,
                        nom_sec_registro.value,
                        nom_anio_pago.value
                        );
                var data2 = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    rubro = $('#rub_id_sld' + n).val();
                    cantidad = $('#rub_val_dif_sld' + n).val();
                    valor_ing = $('#rub_ingreso_sld' + n).val();
                    valor_egr = $('#rub_egreso_sld' + n).val();
                    if (valor_ing != 0) {
                        valor = valor_ing;
                    } else if (valor_egr != 0) {
                        valor = valor_egr;
                    } else {
                        valor = 0;
                    }
                    tipo = $('#rub_credito_sld' + n).val();
                    formula = $('#rub_operac_sld' + n).val();
                    data2.push(
                            rubro + '&' +
                            cantidad + '&' +
                            valor + '&' +
                            tipo + '&' +
                            formula
                            );
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (nom_periodo.value.length == 0) {
                            $("#nom_periodo").css({borderColor: "red"});
                            $("#nom_periodo").focus();
                            return false;
                        } else if (rol_nom_empleado.value.length == 0) {
                            $("#rol_nom_empleado").css({borderColor: "red"});
                            $("#rol_nom_empleado").focus();
                            return false;
                        } else if (rol_sld_empleado.value.length == 0) {
                            $("#rol_sld_empleado").css({borderColor: "red"});
                            $("#rol_sld_empleado").focus();
                            return false;
                        } else if (rol_dias_trab.value.length == 0) {
                            $("#rol_dias_trab").css({borderColor: "red"});
                            $("#rol_dias_trab").focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#rub_val_dif_sld' + n).val() != null) {
                                    if ($('#rub_val_dif_sld' + n).val() == '') {
                                        $('#rub_val_dif_sld' + n).css({borderColor: "red"});
                                        $('#rub_val_dif_sld' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_nomina_rubros.php',
                    data: {op: 4, 'data[]': data, 'data2[]': data2, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else if (dt == 1) {
                            alert('Ya existe generado un Rol con este Empleado, Mes y Año');
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
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

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_empleado(obj) {
                $.post("actions_nomina_rubros.php", {op: 3, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Empleado no existe');
                        $('#rol_nom_empleado').focus();
                        $('#rol_nom_empleado').val('');
                        $('#rol_sld_empleado').val('');
                        $('#emp_id').val('0');
                    }
                });
            }

            function load_empleado2(obj) {
                $.post("actions_nomina_rubros.php", {op: 3, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Empleado no existe');
                        $('#rol_nom_empleado').focus();
                        $('#rol_nom_empleado').val('');
                        $('#rol_sld_empleado').val('');
                        $('#emp_id').val('0');
                    } else {
                        dat = dt.split('&');
                        $('#rol_nom_empleado').val(dat[1]);
                        $('#rol_sld_empleado').val(dat[3]);
                        $('#emp_id').val(dat[2]);
                    }
                    $('#con_clientes').hide();
                }
                );
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }

            function sueldo_a_recibir(obj) {
                dia_t = obj.value;
                sueldo = $('#rol_sld_empleado').val();
                h_sld = sueldo / 30 / 8;
                $('#rol_hora_sld').val(h_sld.toFixed(4));
                d_sld = sueldo / 30;
                $('#rol_dia_sld').val(d_sld.toFixed(4));
                sld_em = dia_t * d_sld;
                $('#rub_val_dif_sld1').val(sld_em.toFixed(2));
                calculos();
            }

            function calculos() {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var b = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        v_ref = 0;
                        ing = 0;
                        egr = 0;
                        $('#rub_val_dif_sld' + n).val(v_ref);
                        $('#rub_ingreso_sld' + n).val(ing);
                        $('#rub_egreso_sld' + n).val(egr);
                    } else {
                        ing_eg = $('#rub_credito_sld' + n).val();
                        porcentaje = $('#rub_porcentaje_sld' + n).val();
                        operacion = $('#rub_operac_sld' + n).val();
                        iess = $('#rub_iess_sld' + n).val();
                        c = $('#rub_val_dif_sld' + n).val();
                        if (porcentaje == 2) {
                            div = 100;
                        } else {
                            div = 1;
                        }
                        if (iess == 1) {
                            b = (b * 1 + c * 1);
                        }
                        v = $('#rub_valor_sld' + n).val();
                        vh = $('#rol_hora_sld').val();
                        vd = $('#rol_dia_sld').val();
                        dt = $('#rol_dias_trab').val();
                        if (ing_eg == 1) {
                            if (operacion == 'DT*VD') {
                                t_ing = (dt * vd) / div;
                                $('#rub_ingreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'VH*V*C') {
                                t_ing = (vh * v * c) / div;
                                $('#rub_ingreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'B*C') {
                                t_ing = (b * c) / div;
                                $('#rub_ingreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'V') {
                                v = c;
                                t_ing = (v) / div;
                                $('#rub_ingreso_sld' + n).val(t_ing.toFixed(2));
                            } else {
                                $('#rub_ingreso_sld' + n).val(0);
                            }
                        } else if (ing_eg == 2) {
                            if (operacion == 'DT*VD') {
                                t_ing = (dt * vd) / div;
                                $('#rub_egreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'VH*V*C') {
                                t_ing = (vh * v * c) / div;
                                $('#rub_egreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'B*C') {
                                t_ing = (b * c) / div;
                                $('#rub_egreso_sld' + n).val(t_ing.toFixed(2));
                            } else if (operacion == 'V') {
                                v = c;
                                t_ing = (v) / div;
                                $('#rub_egreso_sld' + n).val(t_ing.toFixed(2));
                            } else {
                                $('#rub_egreso_sld' + n).val(0);
                            }
                        }
                    }
                }
            }

            function dias_trabajados(obj) {
                dias_t = obj.value;
                f_pago = $('#nom_forma_pago').val();
                if (f_pago == 'MENSUAL') {
                    if (dias_t > 30) {
                        alert("El valor de Dias Trabajados \n Es de máximo hasta 30 dias");
                        $('#rol_dias_trab').val('');
                    }
                } else if (f_pago == 'QUINCENAL') {
                    if (dias_t > 15) {
                        alert("El valor de Dias Trabajados \n Es de máximo hasta 15 dias");
                        $('#rol_dias_trab').val('');
                    }
                }
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            .debe_haber{
                font-size: 10px;
                font-weight:normal;
                text-transform:capitalize; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="8" >ROL DE PAGO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>Fecha Registro</td>
                    <td><input type="text" size="10" id="nom_fec_registro" value="<?php echo $fec_reg ?>" <?php echo $readOnly ?> readonly /></td>
                    <td>Usuario</td>
                    <td colspan="2"><input type="text" size="22" id="rol_usuario" value="<?php echo $usuario ?>" <?php echo $readOnly ?> readonly /></td>
                    <td><input type="hidden" size="10" id="nom_sec_registro" value="<?php echo $secuencial ?>" /></td>
                </tr>
                <tr>
                    <td>Forma Pago</td>
                    <td><input type="text" size="8" id="nom_forma_pago" value="<?php echo $f_pago ?>" readonly /></td>
                    <td>Año</td>
                    <td><input type="text" size="7" id="nom_anio_pago" value="<?php echo $anio ?>" readonly /></td>
                </tr>
                <tr>
                    <td>Periodo</td>
                    <?php
                    if ($id == 0) {
                        ?>
                        <td>
                            <select id="nom_periodo">
                                <option value="<?php echo $mesactual ?>"><?php echo $mesactual ?></option>
                                <option value="<?php echo $mesanterior ?>"><?php echo $mesanterior ?></option>
                            </select>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                            <select id="nom_periodo" disabled>
                                <option value="<?php echo $periodo ?>"><?php echo $periodo ?></option>
                            </select>
                        </td>
                        <?php
                    }
                    ?>
                    <td>Desde</td>
                    <td><input type="text" size="9" id="nom_fp_desde" value="<?php echo $f_ini ?>" <?php echo $readOnly ?> readonly /></td>
                    <td>Hasta</td>
                    <td><input type="text" size="9" id="nom_fp_hasta" value="<?php echo $f_fnl ?>" <?php echo $readOnly ?> readonly /></td>
                </tr>
                <tr>
                    <td>Horas Extras</td>
                    <td><input type="text" size="25" id="nom_horas_extras" value="<?php echo $he ?>" readonly /></td>
                    <td>Desde</td>
                    <td><input type="text" size="9" id="nom_fh_desde" value="<?php echo $f_ini_he ?>" <?php echo $readOnly ?> readonly /></td>
                    <td>Hasta</td>
                    <td><input type="text" size="9" id="nom_fh_hasta" value="<?php echo $f_fin_he ?>" <?php echo $readOnly ?> readonly /></td>
                </tr>
                <tr>
                    <!--<td>Tipo de Desembolso</td>-->
                    <td>
                        <input type="hidden" size="10"  id="rol_desembolso1" name="rol_desembolso" value=""/>
                        <input type="hidden" size="10"  id="rol_desembolso2" name="rol_desembolso" value=""/>
                    </td>
                    <!--<td colspan="4">#Doc Desembolso-->
                <input type="hidden" size="26" id="rol_num_desembolso" value=""/></td>
                </tr>
                <tr>
                    <td>Empleado</td>
                    <td>
                        <input type="text" size="30" id="rol_nom_empleado" value="<?php echo $empleado ?>" onchange="load_empleado(this)" <?php echo $readOnly ?> />
                        <input type="hidden" id="emp_id" value="<?php echo $rst[nom_empleado] ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <table id="tbl_dinamic" lang="0" border="0" cellspacing="0" cellpadding="0" >
                            <thead>
                                <tr>
                                    <th>RUBROS</th>
                                    <th>VALOR REFERENCIAL</th>
                                    <th colspan="8">VALORES CALCULOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SUELDO BASE</td>
                                    <td><input type="text" size="16" id="rol_sld_empleado" style="text-align: right" readonly value="<?php echo $rst[nom_sueldo_base] ?>" <?php echo $readOnly ?> /></td>
                                </tr>
                                <tr>
                                    <td>DIAS TRABAJADOS</td>
                                    <td>
                                        <input type="text" id="rol_dias_trab" style="text-align: right" onblur="sueldo_a_recibir(this)"  value="<?php echo $rst[nom_dias_trabajados] ?>" onkeyup="dias_trabajados(this)" />
                                        <input type="hidden" id="rol_hora_sld" value="<?php echo $h_sld ?>" />
                                        <input type="hidden" id="rol_dia_sld" value="<?php echo $d_sld ?>" />
                                    </td>
                                    <td></td>
                                    <td align="center" bgcolor="#616975"><font color="#FFFFFF">INGRESOS</font></td>
                                    <td align="center" bgcolor="#616975"><font color="#FFFFFF">EGRESOS</font></td>
                                </tr>
                                <?php
                                if ($id == 0) {
                                    $n = 0;
                                    while ($rst_ru = pg_fetch_array($cns_ru)) {
                                        $n++;
                                        if ($rst_ru[rub_grupo] == 'IESS' && $rst_ru[rub_valor] == 9.45) {
                                            $valor = $rst_ru[rub_valor];
                                        } else {
                                            $valor = 0;
                                        }
                                        if ($rst_ru[rub_estado] == 1) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <input type="text" size="12" id="<?php echo 'rub_descr_sld' . $n ?>" value="<?php echo $rst_ru[rub_grupo] ?>" lang="<?PHP echo $n ?>" readonly />
                                                </td>
                                                <td>
                                                    <input type="hidden" size="1" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/>
                                                    <input type="text" size="25" id="<?php echo 'rub_descr_sld' . $n ?>" value="<?php echo $rst_ru[rub_descripcion] ?>" lang="<?PHP echo $n ?>" readonly />
                                                </td>
                                                <td><input type="text" size="10" id="<?php echo 'rub_val_dif_sld' . $n ?>" style="text-align: right" value="<?php echo $valor ?>" onblur="calculos()"></td>
                                                <td><input type="text" size="10" id="<?php echo 'rub_ingreso_sld' . $n ?>" style="text-align: right" value="0" readonly ></td>
                                                <td><input type="text" size="10" id="<?php echo 'rub_egreso_sld' . $n ?>" style="text-align: right" value="0" readonly ></td>
                                                <td><input type="hidden" id="<?php echo 'rub_operac_sld' . $n ?>" value="<?php echo $rst_ru[rub_operacion] ?>" lang="<?PHP echo $n ?>" /></td>
                                                <td><input type="hidden" id="<?php echo 'rub_iess_sld' . $n ?>" value="<?php echo $rst_ru[rub_iess] ?>" lang="<?PHP echo $n ?>" /></td>
                                                <td><input type="hidden" id="<?php echo 'rub_credito_sld' . $n ?>" value="<?php echo $rst_ru[rub_tipo] ?>" lang="<?PHP echo $n ?>" /></td>
                                                <td><input type="hidden" id="<?php echo 'rub_porcentaje_sld' . $n ?>" value="<?php echo $rst_ru[rub_tipo_valor] ?>" lang="<?PHP echo $n ?>" /></td>
                                                <td><input type="hidden" id="<?php echo 'rub_valor_sld' . $n ?>" value="<?php echo $rst_ru[rub_valor] ?>" lang="<?PHP echo $n ?>" /></td>
                                                <td><input type="hidden" id="<?php echo 'rub_id_sld' . $n ?>" value="<?php echo $rst_ru[rub_id] ?>" lang="<?PHP echo $n ?>" /></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } else {
                                    $n = 0;
                                    $cns_det = $Rub->lista_un_det_nomina($id);
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        $rst_rub = pg_fetch_array($Rub->lista_una_nomina_rubros($rst_det[dnm_rubro]));
                                        if ($rst_det[dnm_tipo] == 1) {
                                            $val_ing = $rst_det[dnm_valor];
                                        } else if ($rst_det[dnm_tipo] != 1) {
                                            $val_ing = '0';
                                        }
                                        if ($rst_det[dnm_tipo] == 2) {
                                            $val_egr = $rst_det[dnm_valor];
                                        } else if ($rst_det[dnm_tipo] != 2) {
                                            $val_egr = '0';
                                        }
                                        $rst_ru = pg_fetch_array($Rub->lista_rubros_nomina_si_id($rst_det[dnm_rubro]));
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="text" size="12" id="<?php echo 'rub_descr_sld' . $n ?>" value="<?php echo $rst_ru[rub_grupo] ?>" lang="<?PHP echo $n ?>" readonly />
                                            </td>
                                            <td>
                                                <input type="hidden" size="1" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/>
                                                <input type="text" size="25" id="<?php echo 'rub_descr_sld' . $n ?>" value="<?php echo $rst_rub[rub_descripcion] ?>" lang="<?PHP echo $n ?>" readonly />
                                            </td>
                                            <td><input type="text" size="10" id="<?php echo 'rub_val_dif_sld' . $n ?>" style="text-align: right" value="<?php echo $rst_det[dnm_cantidad] ?>" onblur="calculos()"></td>
                                            <td><input type="text" size="10" id="<?php echo 'rub_ingreso_sld' . $n ?>" style="text-align: right" value="<?php echo $val_ing ?>" readonly ></td>
                                            <td><input type="text" size="10" id="<?php echo 'rub_egreso_sld' . $n ?>" style="text-align: right" value="<?php echo $val_egr ?>" readonly ></td>
                                            <td><input type="hidden" id="<?php echo 'rub_operac_sld' . $n ?>" value="<?php echo $rst_ru[rub_operacion] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type="hidden" id="<?php echo 'rub_iess_sld' . $n ?>" value="<?php echo $rst_ru[rub_iess] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type="hidden" id="<?php echo 'rub_credito_sld' . $n ?>" value="<?php echo $rst_ru[rub_tipo] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type="hidden" id="<?php echo 'rub_porcentaje_sld' . $n ?>" value="<?php echo $rst_ru[rub_tipo_valor] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type="hidden" id="<?php echo 'rub_valor_sld' . $n ?>" value="<?php echo $rst_ru[rub_valor] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type="hidden" id="<?php echo 'rub_id_sld' . $n ?>" value="<?php echo $rst_ru[rub_id] ?>" lang="<?PHP echo $n ?>" /></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <?php
                            if ($x != 1) {
                                ?>
                                <button id="guardar" >Guardar</button>  
                                <?php
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>
