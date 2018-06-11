<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_empleados.php';
$Clase_empleados = new Clase_empleados();
$txt = $_GET[txt];
$sms = 0;
if (isset($_POST[id])) {
    $id = $_POST[id];
    $fot = 1;
} else {
    $id = $_GET[id];
    $fot = 0;
}
if (!empty($_FILES['file']['name'])) {
    $file = $_FILES[file];
    $tmp = $file[tmp_name];
    $name = $file[name];
    $extfoto = pathinfo($name);
    if ($extfoto['extension'] == "jpg" || $extfoto['extension'] == "JPG") {
        $url = "../Empimg/$name";
        move_uploaded_file($tmp, $url);
    } else {
        $sms = "Error en Formato de Imagen";
    }
}

if (!empty($id)) {
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_empleados->lista_un_empleado($id));
    if ($rst[emp_estado] == 0) {
        $act = 'checked';
        $inact = '';
    } else {
        $act = '';
        $inact = 'checked';
    }

    if ($rst['emp_act'] == 't') {
        $actualizado = 'checked';
    } else {
        $actualizado = '';
    }

    if ($rst['emp_disc'] == 't') {
        $disc = 'checked';
    } else {
        $disc = '';
    }

    if ($rst['emp_timbrar'] == '0') {
        $timbrar = 'checked';
    } else {
        $timbrar = '';
    }


    if ($rst[emp_sexo] == 't') {
        $m = 'checked';
        $f = '';
    } else {
        $m = '';
        $f = 'checked';
    }

    if ($rst[emp_hijo1_sexo] == 't') {
        $sx1h = 'checked';
        $sx1m = '';
    } else {
        $sx1h = '';
        $sx1m = 'checked';
    }

    if ($rst[emp_hijo2_sexo] == 't') {
        $sx2h = 'checked';
        $sx2m = '';
    } else {
        $sx2h = '';
        $sx2m = 'checked';
    }
    if ($rst[emp_hijo3_sexo] == 't') {
        $sx3h = 'checked';
        $sx3m = '';
    } else {
        $sx3h = '';
        $sx3m = 'checked';
    }
    if ($rst[emp_hijo4_sexo] == 't') {
        $sx4h = 'checked';
        $sx4m = '';
    } else {
        $sx4h = '';
        $sx4m = 'checked';
    }
    if ($rst[emp_hijo5_sexo] == 't') {
        $sx5h = 'checked';
        $sx5m = '';
    } else {
        $sx5h = '';
        $sx5m = 'checked';
    }
    if ($fot == 0) {
        $url = $rst[emp_foto];
    }
    if (!empty($rst[emp_hijo1_nombres])) {
        $fnac = date("d-m-Y", strtotime($rst['emp_hijo1_fnac']));
        $aFecha = explode('-', $fnac);
        $edh1 = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372) . ' Años';
    }
    if (!empty($rst[emp_hijo2_nombres])) {
        $fnac = date("d-m-Y", strtotime($rst['emp_hijo1_fnac']));
        $aFecha = explode('-', $fnac);
        $edh2 = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372) . ' Años';
    }
    if (!empty($rst[emp_hijo3_nombres])) {
        $fnac = date("d-m-Y", strtotime($rst['emp_hijo1_fnac']));
        $aFecha = explode('-', $fnac);
        $edh3 = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372) . ' Años';
    }
    if (!empty($rst[emp_hijo4_nombres])) {
        $fnac = date("d-m-Y", strtotime($rst['emp_hijo1_fnac']));
        $aFecha = explode('-', $fnac);
        $edh4 = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372) . ' Años';
    }
    if (!empty($rst[emp_hijo5_nombres])) {
        $fnac = date("d-m-Y", strtotime($rst['emp_hijo1_fnac']));
        $aFecha = explode('-', $fnac);
        $edh5 = floor(( (date("Y") - $aFecha[2] ) * 372 + ( date("m") - $aFecha[1] ) * 31 + Date("d") - $aFecha[0] ) / 372) . ' Años';
    }
    $rst_s = pg_fetch_array($Clase_empleados->lista_sueldo_basico());
    $sueldo_b = $rst_s[con_valor2];
} else {
    $id = 0;
    $act = 'checked';
    $m = 'checked';
    $sx1h = 'checked';
    $sx2h = 'checked';
    $sx3h = 'checked';
    $sx4h = 'checked';
    $sx5h = 'checked';
    $rst['emp_fregistro'] = date('Y-m-d');
    $rst['emp_fret'] = date('Y-m-d');
    $rst['emp_fnacimiento'] = date('Y-m-d');
    $rst['emp_hijo1_fnac'] = date('Y-m-d');
    $rst['emp_hijo2_fnac'] = date('Y-m-d');
    $rst['emp_hijo3_fnac'] = date('Y-m-d');
    $rst['emp_hijo4_fnac'] = date('Y-m-d');
    $rst['emp_hijo5_fnac'] = date('Y-m-d');
    $rst['emp_hor_from'] = date('Y-m-d');
    $rst['emp_hor_until'] = date('Y-m-d');
    $rst_s = pg_fetch_array($Clase_empleados->lista_sueldo_basico());
    $sueldo_b = $rst_s[con_valor2];
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id ?>;
            var sms = '<?php echo $sms ?>';
            var sld ='<?php echo $sueldo_b ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "emp_fregistro", ifFormat: "%Y-%m-%d", button: "im-emp_fregistro"});
                Calendar.setup({inputField: "emp_hijo1_fnac", ifFormat: "%Y-%m-%d", button: "im-emp_hijo1_fnac"});
                Calendar.setup({inputField: "emp_hijo2_fnac", ifFormat: "%Y-%m-%d", button: "im-emp_hijo2_fnac"});
                Calendar.setup({inputField: "emp_hijo3_fnac", ifFormat: "%Y-%m-%d", button: "im-emp_hijo3_fnac"});
                Calendar.setup({inputField: "emp_hijo4_fnac", ifFormat: "%Y-%m-%d", button: "im-emp_hijo4_fnac"});
                Calendar.setup({inputField: "emp_hijo5_fnac", ifFormat: "%Y-%m-%d", button: "im-emp_hijo5_fnac"});
                Calendar.setup({inputField: "emp_hor_from", ifFormat: "%Y-%m-%d", button: "im-emp_hor_from"});
                Calendar.setup({inputField: "emp_hor_until", ifFormat: "%Y-%m-%d", button: "im-emp_hor_until"});
                Calendar.setup({inputField: "emp_fnacimiento", ifFormat: "%Y-%m-%d", button: "im-emp_fnacimiento"});
                Calendar.setup({inputField: "emp_fret", ifFormat: "%Y-%m-%d", button: "im-emp_fret"});
               
                if (sms != '0') {
                    alert(sms);
                }
            });
            function save(id) {
                if ($('#emp_sexo1').attr('checked') == true) {
                    sexo = 't';
                } else {
                    sexo = 'f';
                }
                if ($('#emp_hijo1_sexo1').attr('checked') == true) {
                    sh1 = 't';
                } else {
                    sh1 = 'f';
                }
                if ($('#emp_hijo2_sexo1').attr('checked') == true) {
                    sh2 = 't';
                } else {
                    sh2 = 'f';
                }
                if ($('#emp_hijo3_sexo1').attr('checked') == true) {
                    sh3 = 't';
                } else {
                    sh3 = 'f';
                }
                if ($('#emp_hijo4_sexo1').attr('checked') == true) {
                    sh4 = 't';
                } else {
                    sh4 = 'f';
                }
                if ($('#emp_hijo5_sexo1').attr('checked') == true) {
                    sh5 = 't';
                } else {
                    sh5 = 'f';
                }

                if ($('#emp_disc').attr('checked') == true) {
                    disc = 't';
                } else {
                    disc = 'f';
                }
                if ($('#emp_act').attr('checked') == true) {
                    act = 't';
                } else {
                    act = 'f';
                }

                if ($('#emp_timbrar').attr('checked') == true) {
                    timbrar = '0';
                } else {
                    timbrar = '1';
                }
                if ($('#emp_estado1').attr('checked') == true) {
                    estado = '0';
                } else {
                    estado = '1';
                }
                if($('#s_basico').attr('checked') == true){
                    sueldo = sld;
                    s_basico = 1;
                } else {
                    sueldo = $('#emp_sueldo_inicial').val();
                    s_basico = 0;
                }
                var data = Array(
                        emp_fregistro.value,
                        emp_foto2.value,
                        emp_documento.value.toUpperCase(),
                        't', //emp_tipo_documento.value,
                        sexo,
                        emp_apellido_paterno.value.toUpperCase(),
                        emp_apellido_materno.value.toUpperCase(),
                        emp_nombres.value.toUpperCase(),
                        emp_fnacimiento.value,
                        emp_provincia.value.toUpperCase(),
                        emp_canton.value.toUpperCase(),
                        emp_parroquia.value.toUpperCase(),
                        emp_direccion.value.toUpperCase(),
                        emp_telefono.value,
                        emp_celular.value,
                        emp_estado_civil.value,
                        emp_cta_bancaria.value,
                        '', //emp_cta_banco.value, /// ver
                        'X', //emp_licencia_tipo.value,
                        emp_nivel_instruccion.value,
                        emp_titulo.value.toUpperCase(),
                        'A', //emp_estudia.value,
                        estado,
                        emp_codigo.value.toUpperCase(),
                        emp_nacionalidad.value.toUpperCase(),
                        emp_d_provincia.value.toUpperCase(),
                        emp_d_canton.value.toUpperCase(),
                        emp_d_parroquia.value.toUpperCase(),
                        emp_d_sector.value.toUpperCase(),
                        emp_conyugue.value.toUpperCase(),
                        sh1,
                        emp_hijo1_nombres.value.toUpperCase(),
                        emp_hijo1_fnac.value,
                        sh2,
                        emp_hijo2_nombres.value.toUpperCase(),
                        emp_hijo2_fnac.value,
                        sh3,
                        emp_hijo3_nombres.value.toUpperCase(),
                        emp_hijo3_fnac.value,
                        sh4,
                        emp_hijo4_nombres.value.toUpperCase(),
                        emp_hijo4_fnac.value,
                        sh5,
                        emp_hijo5_nombres.value.toUpperCase(),
                        emp_hijo5_fnac.value,
                        disc,
                        emp_d_disc.value.toUpperCase(),
                        emp_empresa1.value.toUpperCase(),
                        emp_cargo1.value.toUpperCase(),
                        emp_t_trabajo1.value.toUpperCase(),
                        emp_t_telefono1.value.toUpperCase(),
                        emp_empresa2.value.toUpperCase(),
                        emp_cargo2.value.toUpperCase(),
                        emp_t_trabajo2.value.toUpperCase(),
                        emp_t_telefono2.value.toUpperCase(),
                        emp_empresa3.value.toUpperCase(),
                        emp_cargo3.value.toUpperCase(),
                        emp_t_trabajo3.value.toUpperCase(),
                        emp_t_telefono3.value.toUpperCase(),
                        sueldo,
                        emp_tipo_sangre.value.toUpperCase(),
                        emp_restriccion.value.toUpperCase(),
                        emp_epp.value.toUpperCase(),
                        emp_obs.value.toUpperCase(),
                        '<?php echo date('d-m-Y') ?>', //emp_act_fecha.value,
                        act,
                        grp_id.value,
                        sec_id.value,
                        emp_cargo.value.toUpperCase(),
                        '0', //rev.value,
                        emp_fret.value.toUpperCase(),
                        emp_sub_sec.value,
                        emp_hor_from.value,
                        emp_hor_until.value,
                        timbrar,
                        emp_rf1_nombre.value.toUpperCase(),
                        emp_rf2_nombre.value.toUpperCase(),
                        emp_rf3_nombre.value.toUpperCase(),
                        emp_rf1_parentezco.value.toUpperCase(),
                        emp_rf2_parentezco.value.toUpperCase(),
                        emp_rf3_parentezco.value.toUpperCase(),
                        emp_rf1_telefono.value.toUpperCase(),
                        emp_rf2_telefono.value.toUpperCase(),
                        emp_rf3_telefono.value.toUpperCase(),
                        emp_motivo_retiro.value.toUpperCase(),
                        '', ///emp_codigo_encriptado.value
                        emp_email.value.toLowerCase(),
                        s_basico 
                        );

                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (emp_fregistro.value.length == 0) {
                            $("#emp_fregistro").css({borderColor: "red"});
                            $("#emp_fregistro").focus();
                            return false;
                        }
//                        else if (emp_fret.value.length == 0) {
//                            $("#emp_fret").css({borderColor: "red"});
//                            $("#emp_fret").focus();
//                            return false;
//                        }
                        else if (emp_codigo.value.length == 0) {
                            $("#emp_codigo").css({borderColor: "red"});
                            $("#emp_codigo").focus();
                            return false;
                        }
                        else if (emp_sueldo_inicial.value.length == 0) {
                            $("#emp_sueldo_inicial").css({borderColor: "red"});
                            $("#emp_sueldo_inicial").focus();
                            return false;
                        }
                        else if (sec_id.value == 0) {
                            $("#sec_id").css({borderColor: "red"});
                            $("#sec_id").focus();
                            return false;
                        }
                        else if (emp_cargo.value.length == 0) {
                            $("#emp_cargo").css({borderColor: "red"});
                            $("#emp_cargo").focus();
                            return false;
                        }
//                         else if (grp_id.value== 0) {
//                            $("#grp_id").css({borderColor: "red"});
//                            $("#grp_id").focus();
//                            return false;
//                        }
                        else if (emp_hor_from.value.length == 0) {
                            $("#emp_hor_from").css({borderColor: "red"});
                            $("#emp_hor_from").focus();
                            return false;
                        }
                        else if (emp_hor_until.value.length == 0) {
                            $("#emp_hor_until").css({borderColor: "red"});
                            $("#emp_hor_until").focus();
                            return false;
                        }
                        else if (emp_apellido_paterno.value.length == 0) {
                            $("#emp_apellido_paterno").css({borderColor: "red"});
                            $("#emp_apellido_paterno").focus();
                            return false;
                        }
                        else if (emp_nombres.value.length == 0) {
                            $("#emp_nombres").css({borderColor: "red"});
                            $("#emp_nombres").focus();
                            return false;
                        }
                        else if (emp_documento.value.length == 0) {
                            $("#emp_documento").css({borderColor: "red"});
                            $("#emp_documento").focus();
                            return false;
                        }
                        else if (emp_canton.value.length == 0) {
                            $("#emp_canton").css({borderColor: "red"});
                            $("#emp_canton").focus();
                            return false;
                        }
                        else if (emp_direccion.value.length == 0) {
                            $("#emp_direccion").css({borderColor: "red"});
                            $("#emp_direccion").focus();
                            return false;
                        }
                        else if (emp_fnacimiento.value.length == 0) {
                            $("#emp_fnacimiento").css({borderColor: "red"});
                            $("#emp_fnacimiento").focus();
                            return false;
                        }
                        else if (emp_nacionalidad.value.length == 0) {
                            $("#emp_nacionalidad").css({borderColor: "red"});
                            $("#emp_nacionalidad").focus();
                            return false;
                        }
                        else if (emp_d_canton.value.length == 0) {
                            $("#emp_d_canton").css({borderColor: "red"});
                            $("#emp_d_canton").focus();
                            return false;
                        }
                        else if (emp_d_parroquia.value.length == 0) {
                            $("#emp_d_parroquia").css({borderColor: "red"});
                            $("#emp_d_parroquia").focus();
                            return false;
                        }
                        else if ($("#emp_disc").attr('checked') == true && emp_d_disc.value.length == 0) {
                            $("#emp_d_disc").css({borderColor: "red"});
                            $("#emp_d_disc").focus();
                            return false;
                        }

                    },
                    type: 'POST',
                    url: 'actions_empleados.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_empleados.php?txt=' + '<?php echo $txt ?>';
            }

            function edad(f, v) {
//                var f1 = f.split("-");
//                var year = f1[0];
//                var month = f1[1];
//                var day = f1[2];
//                var today = new Date();
//                var age = today.getFullYear() - year;
//                if (today.getMonth() < month || (today.getMonth() == month && today.getDate() < day)) {
//                    age--;
//                }
//                if (age < 0) {
//                    age = 0;
//                }
//                var e = age + ' Años';
//                

                var fecha = f;
                // Si la fecha es correcta, calculamos la edad
                var values = fecha.split("-");
                var dia = values[2];
                var mes = values[1];
                var ano = values[0];
                // cogemos los valores actuales
                var fecha_hoy = new Date();
                var ahora_ano = fecha_hoy.getYear();
                var ahora_mes = fecha_hoy.getMonth() + 1;
                var ahora_dia = fecha_hoy.getDate();
                // realizamos el calculo
                var edad = (ahora_ano + 1900) - ano;
                if (ahora_mes < mes) {
                    edad--;
                }

                if ((mes == ahora_mes) && (ahora_dia < dia)) {
                    edad--;
                }

                if (edad > 1900) {
                    edad -= 1900;
                }

                e = edad + ' Años';
                switch (v) {
                    case 1:
                        $('#edadh1').html(e);
                        break;
                    case 2:
                        $('#edadh2').html(e);
                        break;
                    case 3:
                        $('#edadh3').html(e);
                        break;
                    case 4:
                        $('#edadh4').html(e);
                        break;
                    case 5:
                        $('#edadh5').html(e);
                        break;
                }
            }

            function contro_sueldo(){         
                if($('#s_basico').attr('checked') == true){
                    $('#emp_sueldo_inicial').val(0);
                    $('#emp_sueldo_inicial').attr('disabled', true);
                } else {
                    $('#emp_sueldo_inicial').val('');
                    $('#emp_sueldo_inicial').attr('disabled', false);
                }
            }

            function load_file() {
                $('#frm_file').submit();
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            .trhead{
                background-color:#568da7;
                height:17px;
                box-shadow:0px 3px 5px 0px #bbb;
            }
            .tdhead{
                font-weight: bolder;
                text-align: center;
                border-collapse: collapse;
            }
            .lblhead{
                font-size:12px;
                color:white;
            }
        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE EMPLEADOS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td colspan="9">
                        <table style="width: 100%">
                            <tr>
                                <td style="width: 100px" rowspan="3">
                                    <img src="<?php echo $url ?>" width="80px" height="90px" id="foto"></img>
                                </td>
                                <td>FECHA INGRESO</td>
                                <td>
                                    <input type="text" name="emp_fregistro"  value="<?php echo $rst['emp_fregistro'] ?>" id="emp_fregistro" />
                                    <img src="../img/calendar.png" id="im-emp_fregistro" />
                                </td>   

                                </form>
                                <td>
                                    Fecha Retiro:
                                </td>
                                <td>
                                    <input type="text" name="emp_fret"  value="<?php echo $rst['emp_fret'] ?>" id="emp_fret"/>
                                    <img src="../img/calendar.png" id="im-emp_fret" />
                                </td>
                                <td align="left">
                                    ACTIVO<input <?php echo $act ?> type="radio" name="emp_estado" id="emp_estado1" value=0 />
                                    RETIRADO<input <?php echo $inact ?> type="radio" name="emp_estado" id="emp_estado2" value=1 />
                                </td>
                            </tr> 
                            <tr>
                                <td>CODIGO</td>
                                <td>
                                    <input type="text" id="emp_codigo" name="emp_codigo" value="<?php echo $rst['emp_codigo'] ?>"/>                
                                </td> 
                                <td>
                                    Motivo:
                                </td>                
                                <td>
                                    <input type="text" id="emp_motivo_retiro" name="emp_motivo_retiro" value="<?php echo $rst['emp_motivo_retiro'] ?>"/>
                                </td>
                            </tr> 
                            <tr>
                                <td>
                                    <form id="frm_file" name="frm_file" style="float:right" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                        <div class="upload">
                                            <input type="text" hidden  name="id" id="id" value="<?php echo $id ?>" >
                                            <input type="file" style="width:98px" value="" size="3" name="file" id="file" onchange="load_file()"/>
                                            <input type="" hidden value="<?php echo $url ?>" name="emp_foto2" id="emp_foto2"/>
                                        </div>
                                    </form>
                                </td>
                                <td>Sueldo Inicial:</td>
                                <td>
                                    <input type="text" value="<?php echo $rst[emp_sueldo_inicial] ?>" name="emp_sueldo_inicial" id="emp_sueldo_inicial" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"  />$
                                </td>
                                <td>
                                    Sueldo Basico
                                    <input type="checkbox"  id="s_basico" id="s_basico" onclick="contro_sueldo()">
                                </td>
                                <td>
                                    Tipo Sangre:

                                    <input type="text" value="<?php echo $rst[emp_tipo_sangre] ?>" maxlength="2" size="10" name="emp_tipo_sangre" id="emp_tipo_sangre"  />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="trhead">
                    <td class="tdhead" colspan="4" ><label class="lblhead" >Datos Laborales</label></td>
                </tr> 
                <tr>
                    <td colspan="9">
                        <table style="width: 100%">
                            <tr>
                                <td>
                                    Seccion:
                                </td>
                                <td>
                                    <select name="sec_id" id="sec_id" style="width:150px">
                                        <option value="0">Elija un Seccion</option>
                                        <?php
                                        $cns_sec = $Clase_empleados->lista_secciones();
                                        while ($rst_sec = pg_fetch_array($cns_sec)) {
                                            echo "<option value='$rst_sec[sec_id]'>$rst_sec[sec_gerencia] - $rst_sec[sec_area] - $rst_sec[sec_descricpion]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    Sub_Seccion:
                                </td>
                                <td>
                                    <select name="emp_sub_sec" id="emp_sub_sec" style="width: 200px">
                                        <option  value="0">Ninguna</option>
                                        <?php
                                        $cns_sbs = $Clase_empleados->lista_subsecciones();
                                        while ($rst_sbs = pg_fetch_array($cns_sbs)) {
                                            echo "<option value='$rst_sbs[sbs_id]'>$rst_sbs[sbs_codigo] - $rst_sbs[sbs_descripcion]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    Cargo:
                                </td>
                                <td colspan="3">
                                    <input type="text" name="emp_cargo" id="emp_cargo" value="<?php echo $rst['emp_cargo'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Timbra:
                                </td>
                                <td>
                                    <input type="checkbox" <?php echo $timbrar ?> id="emp_timbrar" name="emp_timbrar"  />
                                </td>
                                <td>
                                    Horario:
                                </td>
                                <td>
                                    <select name="grp_id" id="grp_id" style="width:200px">
                                        <option value="0">Elija un Horario</option>
                                        <?php
                                        $cns_hor = $Clase_empleados->lista_grupo_horarios();
                                        while ($rst_hor = pg_fetch_array($cns_hor)) {
                                            echo "<option $sel value='$rst_hor[gru_id]'>$rst_hor[gru_horarios]</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    Inicio:
                                </td>
                                <td>
                                    <input type="text" name="emp_hor_from" id="emp_hor_from" value="<?php echo $rst['emp_hor_from'] ?>"/>
                                    <img src="../img/calendar.png" id="im-emp_hor_from" />
                                </td>
                                <td>
                                    Fin:
                                </td>
                                <td>
                                    <input type="text" name="emp_hor_until" id="emp_hor_until" value="<?php echo $rst['emp_hor_until'] ?>"/>
                                    <img src="../img/calendar.png" id="im-emp_hor_until" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Datos personales</label></td>
                </tr>  
                <tr>
                    <td colspan="9">
                        <table style="width: 100%" >
                            <tr>
                                <td>Apellido Paterno</td>
                                <td><input type="text" name="emp_apellido_paterno" id="emp_apellido_paterno" value="<?php echo $rst['emp_apellido_paterno'] ?>"  /></td>
                                <td>Apellido Materno</td>
                                <td><input type="text" name="emp_apellido_materno" id="emp_apellido_materno" value="<?php echo $rst['emp_apellido_materno'] ?>"  /></td>
                                <td>Nombres</td>
                                <td colspan="2"><input type="text"  id="emp_nombres" name="emp_nombres" value="<?php echo $rst['emp_nombres'] ?>"  size="45" /></td>
                            </tr>
                            <tr>
                                <td>CEDULA</td> 
                                <td><input type="text" name="emp_documento" id="emp_documento" value="<?php echo $rst['emp_documento'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9]/, '');" onblur="cedula(this.frmEmpleado)" /></td>
                                <td>SEXO</td>
                                <td>
                                    <input type="radio" <?php echo $m ?> name="emp_sexo" id="emp_sexo1" value="t"></input>H
                                    <input type="radio" <?php echo $f ?> name="emp_sexo" id="emp_sexo2" value="f"></input>M
                                </td>
                                <td>EST/CIVL</td>
                                <td>
                                    <select name="emp_estado_civil" id="emp_estado_civil" onchange="estCivil(this.value)" style="width: 160px">
                                        <option value="SOLTERO">SOLTERO</option>    
                                        <option value="CASADO">CASADO</option>    
                                        <option value="VIUDO">VIUDO</option>    
                                        <option value="DIVORCIADO">DIVORCIADO</option>    
                                        <option value="UNION LIBRE">UNION LIBRE</option>    
                                    </select>
                                </td>
                                <td>CONYUGUE</td>
                                <td><input type="text" value="<?php echo $rst['emp_conyugue'] ?>" id="emp_conyugue" name="emp_conyugue" /></td>
                            </tr>
                            <tr>
                                <td>TELEFONO</td>   
                                <td><input type="text" name="emp_telefono" id="emp_telefono" value="<?php echo $rst['emp_telefono'] ?>"/></td>
                                <td>CELULAR</td>
                                <td><input type="text" name="emp_celular"  id="emp_celular" value="<?php echo $rst['emp_celular'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9]/, '');" /></td>
                                <td>EMAIL</td>
                                <td><input type="email" name="emp_email" id="emp_email" value="<?php echo $rst['emp_email'] ?>" style="text-transform: lowercase"/></td>                        
                                <td>CNTA.BANCARIA</td>
                                <td><input type="text" name="emp_cta_bancaria" id="emp_cta_bancaria" value="<?php echo $rst['emp_cta_bancaria'] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" /></td>                        

                            </tr>        
                            <tr>
                                <td>PROVINCIA</td>
                                <td>
                                    <select  name="emp_provincia" id="emp_provincia" style="width: 160px">
                                        <option value="PICHINCHA" >Pichincha</option>    
                                        <option value="AZUAY">Azuay</option>
                                        <option value="BOLIVAR">Bolivar</option>
                                        <option value="CANAR">Canar</option>
                                        <option value="CARCHI">Carchi</option>
                                        <option value="CHIMBORAZO">Chimborazo</option>
                                        <option value="COTOPAXI">Cotopaxi</option>
                                        <option value="EL ORO">El Oro</option>
                                        <option value="ESMERALDAS">Esmeraldas</option>
                                        <option value="GALAPAGOS">Galapagos</option>
                                        <option value="GUAYAS">Guayas</option>
                                        <option value="IMBABURA">Imbabura</option>
                                        <option value="LOJA">Loja</option>
                                        <option value="LOS RIOS">Los Rios</option>
                                        <option value="MANABI">Manabi</option>
                                        <option value="MORONA-Santiago">Morona-Santiago</option>
                                        <option value="NAPO">Napo</option>
                                        <option value="ORELLANA">Orellana</option>
                                        <option value="PASTAZA">Pastaza</option>
                                        <option value="SANTA ELENA">Santa Elena</option>
                                        <option value="SANTO DOMINGO DE LOS TSACHILAS">Santo Domingo de los Tsachilas</option>
                                        <option value="SUCUMBIOS">Sucumbios</option>
                                        <option value="TUNGURAHUA">Tungurahua</option>
                                        <option value="ZAMORA-CHINCHIPE">Zamora-Chinchipe</option>

                                    </select>
                                </td>
                                <td>CANTON</td> 
                                <td><input type="text" id="emp_canton" name="emp_canton" value="<?php echo $rst['emp_canton'] ?>"  /></td> 
                                <td>PARROQUIA</td> 
                                <td><input type="text" id="emp_parroquia" name="emp_parroquia" value="<?php echo $rst['emp_parroquia'] ?>"  /></td>  
                                <td>SECTOR</td>
                                <td ><input type="text" id="emp_d_sector" name="emp_d_sector" value="<?php echo $rst['emp_d_sector'] ?>"  /></td>            
                            </tr>
                            <tr>
                                <td>DIRECCION</td>
                                <td colspan="2"><input type="text" id="emp_direccion" name="emp_direccion" size="45"  value="<?php echo $rst['emp_direccion'] ?>"  ></td>
                                <td>DISCAPACIDAD            
                                    <input <?php echo $disc ?>  type="checkbox" name="emp_disc" id="emp_disc" onclick="habilita(this.form)" /></td>
                                <td colspan="2"><input type="text" name="emp_d_disc" id="emp_d_disc" size="40"  value="<?php echo $rst['emp_d_disc'] ?>"  /></td>
                        </table>
                    </td>
                </tr>
                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Lugar y Fecha de Nacimiento</label></td>
                </tr>        
                <tr>
                    <td colspan="9">
                        <table style="width: 100%">
                            <tr>
                                <td>FECHA/NAC</td>
                                <td><input type="text" name="emp_fnacimiento" value="<?php echo $rst['emp_fnacimiento'] ?>" id="emp_fnacimiento"/>
                                    <img src="../img/calendar.png" id="im-emp_fnacimiento" /></td> 
                                <td>NACIONALIDAD</td>
                                <td><input type="text" name="emp_nacionalidad" id="emp_nacionalidad" value="<?php echo $rst['emp_nacionalidad'] ?>"  /></td>             

                            </tr>
                            <tr>
                                <td>PROVINCIA</td>
                                <td><select  name="emp_d_provincia" id="emp_d_provincia" style="width: 160px">
                                        <option value="PICHINCHA" >Pichincha</option>    
                                        <option value="AZUAY">Azuay</option>
                                        <option value="BOLIVAR">Bolivar</option>
                                        <option value="CANAR">Canar</option>
                                        <option value="CARCHI">Carchi</option>
                                        <option value="CHIMBORAZO">Chimborazo</option>
                                        <option value="COTOPAXI">Cotopaxi</option>
                                        <option value="EL ORO">El Oro</option>
                                        <option value="ESMERALDAS">Esmeraldas</option>
                                        <option value="GALAPAGOS">Galapagos</option>
                                        <option value="GUAYAS">Guayas</option>
                                        <option value="IMBABURA">Imbabura</option>
                                        <option value="LOJA">Loja</option>
                                        <option value="LOS RIOS">Los Rios</option>
                                        <option value="MANABI">Manabi</option>
                                        <option value="MORONA-Santiago">Morona-Santiago</option>
                                        <option value="NAPO">Napo</option>
                                        <option value="ORELLANA">Orellana</option>
                                        <option value="PASTAZA">Pastaza</option>
                                        <option value="SANTA ELENA">Santa Elena</option>
                                        <option value="SANTO DOMINGO DE LOS TSACHILAS">Santo Domingo de los Tsachilas</option>
                                        <option value="SUCUMBIOS">Sucumbios</option>
                                        <option value="TUNGURAHUA">Tungurahua</option>
                                        <option value="ZAMORA-CHINCHIPE">Zamora-Chinchipe</option>

                                    </select>
                                </td>
                                <td >
                                    CANTON
                                </td>
                                <td >
                                    <input type="text" name="emp_d_canton" id="emp_d_canton" value="<?php echo $rst['emp_d_canton'] ?>"  />
                                </td>
                                <td >
                                    PARROQUIA
                                </td>
                                <td >
                                    <input type="text" name="emp_d_parroquia" id="emp_d_parroquia" value="<?php echo $rst['emp_d_parroquia'] ?>"  />
                                </td>
                            </tr>      
                            <tr>
                                <td>ESTUDIOS</td>
                                <td>   <select name="emp_nivel_instruccion" id="emp_nivel_instruccion" style="width:160px ">
                                        <option value="EG.BASICA">EG.BASICA</option>    
                                        <option value="BACHILLERATO">BACHILLERATO</option>    
                                        <option value="SUPERIOR TECNICO">SUPERIOR TECNICO</option>    
                                        <option value="SUPERIOR 3ER NIVEL MEDIO">SUPERIOR 3ER NIVEL MEDIO</option>    
                                        <option value="SUPERIOR 3ER NIVEL">SUPERIOR 3ER NIVEL</option>    
                                        <option value="SUPERIOR 4TO NIVEL">SUPERIOR 4TO NIVEL</option>    
                                        <option value="SUPERIOR 5TO NIVEL">SUPERIOR 5TO NIVEL</option>    
                                        <option value="NINGUNO">NINGUNO</option>    
                                    </select>
                                </td>    
                                <td>TITULO OBTENIDO</td>
                                <td colspan="3" ><input type="text" name="emp_titulo" id="emp_titulo" size="50" value="<?php echo $rst['emp_titulo'] ?>" ></input></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Cargas Familiares</label></td></tr>                

                <tr>
                    <td align="center">Sexo</td>
                    <td>Nombre y Apellido</td>
                    <td>F/nac</td>
                    <td>Edad</td>
                </tr>
                <tr>
                    <td align="center">
                        H<input <?php echo $sx1h ?> type="radio" name="emp_hijo1_sexo" id="emp_hijo1_sexo1" value="t" />
                        M<input <?php echo $sx1m ?> type="radio" name="emp_hijo1_sexo" id="emp_hijo1_sexo2" value="f" />
                    </td>
                    <td><input type="text" name="emp_hijo1_nombres" id="emp_hijo1_nombres" value="<?php echo $rst[emp_hijo1_nombres] ?>" /></td>
                    <td><input type="date" name="emp_hijo1_fnac" id="emp_hijo1_fnac" onchange=" return edad(this.value, 1)" size="10" readonly value="<?php echo $rst[emp_hijo1_fnac] ?>" />
                        <img src="../img/calendar.png" id="im-emp_hijo1_fnac" /></td> 
                    <td id="edadh1"><?php echo $edh1 ?></td>

                </tr>        
                <tr>
                    <td align="center">
                        H<input <?php echo $sx2h ?> type="radio" name="emp_hijo2_sexo"  id="emp_hijo2_sexo1" value="t"></input>
                        M<input <?php echo $sx2m ?> type="radio" name="emp_hijo2_sexo" id="emp_hijo2_sexo2" value="f"></input>
                    </td>
                    <td><input type="text" name="emp_hijo2_nombres" id="emp_hijo2_nombres" value="<?php echo $rst[emp_hijo2_nombres] ?>" ></input></td>
                    <td><input type="text" name="emp_hijo2_fnac" id="emp_hijo2_fnac" onchange="edad(this.value, 2)" size="10" readonly value="<?php echo $rst[emp_hijo2_fnac] ?>" ></input>
                        <img src="../img/calendar.png" id="im-emp_hijo2_fnac" /></td> 
                    <td id="edadh2"><?php echo $edh2 ?></td>

                </tr>        
                <tr>
                    <td align="center">
                        H<input <?php echo $sx3h ?> type="radio" name="emp_hijo3_sexo" id="emp_hijo3_sexo1" value="t"></input>
                        M<input <?php echo $sx3m ?> type="radio" name="emp_hijo3_sexo" id="emp_hijo3_sexo2" value="f"></input>
                    </td>
                    <td><input type="text" name="emp_hijo3_nombres" id="emp_hijo3_nombres" value="<?php echo $rst[emp_hijo3_nombres] ?>" ></input></td>
                    <td><input type="text" name="emp_hijo3_fnac" id="emp_hijo3_fnac" onchange="edad(this.value, 3)" size="10" readonly value="<?php echo $rst[emp_hijo3_fnac] ?>" ></input>
                        <img src="../img/calendar.png" id="im-emp_hijo3_fnac" /></td> 
                    <td id="edadh3"><?php echo $edh3 ?></td>

                </tr>        
                <tr>
                    <td align="center">
                        H<input <?php echo $sx4h ?> type="radio" name="emp_hijo4_sexo" id="emp_hijo4_sexo1" value="t"></input>
                        M<input <?php echo $sx4m ?> type="radio" name="emp_hijo4_sexo" id="emp_hijo4_sexo2" value="f"></input>
                    </td>
                    <td><input type="text" name="emp_hijo4_nombres" id="emp_hijo4_nombres" value="<?php echo $rst[emp_hijo4_nombres] ?>" ></input></td>
                    <td><input type="text" name="emp_hijo4_fnac" id="emp_hijo4_fnac" onchange="edad(this.value, 4)" size="10" readonly value="<?php echo $rst[emp_hijo4_fnac] ?>" ></input>
                        <img src="../img/calendar.png" id="im-emp_hijo4_fnac" /></td> 
                    <td id="edadh4"><?php echo $edh4 ?></td>
                </tr>        

                <tr>
                    <td align="center">
                        H<input <?php echo $sx5h ?> type="radio" name="emp_hijo5_sexo" id="emp_hijo5_sexo1" value="t"></input>
                        M<input <?php echo $sx5m ?> type="radio" name="emp_hijo5_sexo" id="emp_hijo5_sexo2" value="f"></input>
                    </td>
                    <td><input type="text" name="emp_hijo5_nombres" id="emp_hijo5_nombres" value="<?php echo $rst[emp_hijo5_nombres] ?>" ></input></td>
                    <td><input type="text" name="emp_hijo5_fnac" id="emp_hijo5_fnac"  onchange="edad(this.value, 5)" size="10" readonly value="<?php echo $rst[emp_hijo5_fnac] ?>" ></input>
                        <img src="../img/calendar.png" id="im-emp_hijo5_fnac" /></td> 
                    <td id="edadh5"><?php echo $edh5 ?></td>
                </tr>        
                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Referencias Familiares</label></td></tr>                
                <tr>
                    <td>Nombres</td>
                    <td>Parentezco</td>
                    <td>Telefono/Celular</td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_rf1_nombre" size="50" value="<?php echo $rst[emp_rf1_nombre] ?>" id="emp_rf1_nombre" /></td>
                    <td><input type="text" name="emp_rf1_parentezco" value="<?php echo $rst[emp_rf1_parentezco] ?>" id="emp_rf1_parentezco" /></td>
                    <td colspan="2"><input type="text" name="emp_rf1_telefono" size="35" value="<?php echo $rst[emp_rf1_telefono] ?>" id="emp_rf1_telefono" /></td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_rf2_nombre" size="50" value="<?php echo $rst[emp_rf2_nombre] ?>" id="emp_rf2_nombre" /></td>
                    <td><input type="text" name="emp_rf2_parentezco" value="<?php echo $rst[emp_rf2_parentezco] ?>" id="emp_rf2_parentezco" /></td>
                    <td colspan="2"><input type="text" name="emp_rf2_telefono" size="35" value="<?php echo $rst[emp_rf2_telefono] ?>" id="emp_rf2_telefono" /></td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_rf3_nombre" size="50" value="<?php echo $rst[emp_rf3_nombre] ?>" id="emp_rf3_nombre" /></td>
                    <td><input type="text" name="emp_rf3_parentezco" value="<?php echo $rst[emp_rf3_parentezco] ?>" id="emp_rf3_parentezco" /></td>
                    <td colspan="2"><input type="text" name="emp_rf3_telefono" size="35" value="<?php echo $rst[emp_rf3_telefono] ?>" id="emp_rf3_telefono" /></td>
                </tr>

                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Referencias Laborales</label></td></tr>                
                <tr>
                    <td>Empresa</td>
                    <td>Cargo</td>
                    <td>Tiempo de Trabajo</td>
                    <td>Telefono</td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_empresa1" value="<?php echo $rst[emp_empresa1] ?>" id="emp_empresa1" /></td>
                    <td><input type="text" name="emp_cargo1" value="<?php echo $rst[emp_cargo1] ?>" id="emp_cargo1" /></td>
                    <td><input type="text" name="emp_t_trabajo1" value="<?php echo $rst[emp_t_trabajo1] ?>" id="emp_t_trabajo1" /></td>
                    <td><input type="text" name="emp_t_telefono1" value="<?php echo $rst[emp_t_telefono1] ?>" id="emp_t_telefono1" /></td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_empresa2" value="<?php echo $rst[emp_empresa2] ?>" id="emp_empresa2" /></td>
                    <td><input type="text" name="emp_cargo2" value="<?php echo $rst[emp_cargo2] ?>" id="emp_cargo2" /></td>
                    <td><input type="text" name="emp_t_trabajo2" value="<?php echo $rst[emp_t_trabajo2] ?>" id="emp_t_trabajo2" /></td>
                    <td><input type="text" name="emp_t_telefono2" value="<?php echo $rst[emp_t_telefono2] ?>" id="emp_t_telefono2" /></td>
                </tr>
                <tr>
                    <td><input type="text" name="emp_empresa3" value="<?php echo $rst[emp_empresa3] ?>" id="emp_empresa3" /></td>
                    <td><input type="text" name="emp_cargo3" value="<?php echo $rst[emp_cargo3] ?>" id="emp_cargo3" /></td>
                    <td><input type="text" name="emp_t_trabajo3" value="<?php echo $rst[emp_t_trabajo3] ?>" id="emp_t_trabajo3" /></td>
                    <td><input type="text" name="emp_t_telefono3" value="<?php echo $rst[emp_t_telefono3] ?>" id="emp_t_telefono3" /></td>
                </tr>
                </tbody> 
                <tr class="trhead">
                    <td  class="tdhead" colspan="4" ><label class="lblhead">Adicionales</label></td></tr>                
                <tr>
                    <td>Restriccion<input type="text" name="emp_restriccion" value="<?php echo $rst[emp_restriccion] ?>" id="emp_restriccion" /></td>
                    <td>EPP<input type="text" name="emp_epp" value="<?php echo $rst[emp_epp] ?>" id="emp_epp" /></td>
                </tr>
                <tr>
                    <td >Observaciones</td>
                    <td colspan="5"><textarea cols="60" name="emp_obs" id="emp_obs" rows="2"><?php echo $rst[emp_obs] ?></textarea></td>
                </tr>
                <tr>
                    <td>Actualizado:<input <?php echo $actualizado ?> type="checkbox" name="emp_act" id="emp_act" /></td>
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>  
<script>
    var sec = '<?php echo $rst[sec_id] ?>';
    $('#sec_id').val(sec);
    var sbs = '<?php echo $rst[emp_sub_sec] ?>';
    $('#emp_sub_sec').val(sbs);
    var hor = '<?php echo $rst[grp_id] ?>';
    $('#grp_id').val(hor);
    var escv = '<?php echo $rst[emp_estado_civil] ?>';
    $('#emp_estado_civil').val(escv);
    var prov = '<?php echo $rst[emp_provincia] ?>';
    $('#emp_provincia').val(prov);
    var prov2 = '<?php echo $rst[emp_d_provincia] ?>';
    $('#emp_d_provincia').val(prov2);
    var ins = '<?php echo $rst[emp_nivel_instruccion] ?>';
    $('#emp_nivel_instruccion').val(ins);
    var s_bsc = '<?php echo $rst[emp_sueldo_basico] ?>';
    if(s_bsc == 1){
        $('#s_basico').attr('checked', true);
        $('#s_basico').attr('checked', true);
        $('#emp_sueldo_inicial').val(0);
        $('#emp_sueldo_inicial').attr('disabled', true);
    } else {
        $('#s_basico').attr('checked', false);
        $('#emp_sueldo_inicial').attr('disabled', false);
    }
</script>
