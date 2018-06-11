<?PHP
include_once '../Includes/permisos.php';
include_once '../Clases/clsPermisosVacaciones.php';
$PerVac = new VacacionesPermisos();
if (isset($_GET[id])) {
    $ids = $_GET[id];
    $rst = pg_fetch_array($PerVac->listUnaVacacionesPermisos($ids));
    if ($rst[reg_vac_recargo] == 0) {
        $rec0 = "checked";
    } else if ($rst[reg_vac_recargo] == 1) {
        $rec1 = "checked";
    } else {
        $rec2 = "checked";
    }
} else {
    $ids = 0;
    $rst[reg_vac_finicio] = date('Y-m-d');
    $rst[reg_vac_ffinal] = date('Y-m-d');
    $rst[reg_vac_hinicio] = date('00:00');
    $rst[reg_vac_hfinal] = date('00:00');
    $rec0 = "checked";
}
?>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var pv_id =<?php echo $ids ?>;
            var patron = new Array(2, 2, 4);

            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "reg_vac_finicio", ifFormat: "%Y-%m-%d", button: "finicio"});
                Calendar.setup({inputField: "reg_vac_ffinal", ifFormat: "%Y-%m-%d", button: "ffinal"});
            });

            function findEmp(id) {
                $.post("actions_permisos_vacaciones.php", {id: id, op: 2},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 1) {
                        $('#id').val('0');
                        $('#emp_id').val('');
                        $('#emp').html('Empleado no existe');
                    } else {
                        $('#id').val(dat[0]);
                        $('#emp_id').val(dat[1]);
                        $('#emp').html(dat[2] + ' ' + dat[3] + ' ' + dat[4]);
                    }
                });
            }


            function documento_automatico(doc) {
                if (doc.checked == true) {
                    $.post("actions_permisos_vacaciones.php", {op: 3},
                    function (dt) {
                        $('#reg_vac_documento').val(dt);
                    });
                } else {
                    $('#reg_vac_documento').val('');
                }
            }

            function mascara(d, sep, pat, nums) {
                if (d.valant != d.value) {
                    val = d.value
                    largo = val.length
                    val = val.split(sep)
                    val2 = ''
                    for (r = 0; r < val.length; r++) {
                        val2 += val[r]
                    }
                    if (nums) {
                        for (z = 0; z < val2.length; z++) {
                            if (isNaN(val2.charAt(z))) {
                                letra = new RegExp(val2.charAt(z), "g")
                                val2 = val2.replace(letra, "")
                            }
                        }
                    }
                    val = ''
                    val3 = new Array()
                    for (s = 0; s < pat.length; s++) {
                        val3[s] = val2.substring(0, pat[s])
                        val2 = val2.substr(pat[s])
                    }
                    for (q = 0; q < val3.length; q++) {
                        if (q == 0) {
                            val = val3[q]
                        }
                        else {
                            if (val3[q] != "") {
                                val += sep + val3[q]
                            }
                        }
                    }
                    d.value = val;
                    d.valant = val;
                }
            }

            function save(pv_id) {

                if ($('#reg_vac_recargo1').attr('checked') == true) {
                    recargo = 0;
                } else if ($('#reg_vac_recargo2').attr('checked') == true) {
                    recargo = 1;
                } else {
                    recargo = 2;
                }
                var data = Array(
                        id.value,
                        con_id.value,
                        reg_vac_finicio.value,
                        reg_vac_ffinal.value,
                        reg_vac_descripcion.value.toUpperCase(),
                        reg_vac_obs.value.toUpperCase(),
                        reg_vac_documento.value.toUpperCase(),
                        reg_vac_hinicio.value,
                        reg_vac_hfinal.value,
                        recargo
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
                        if (emp_id.value.length == 0) {
                            $("#emp_id").css({borderColor: "red"});
                            $("#emp_id").focus();
                            return false;
                        }
                        else if (con_id.value == 0) {
                            $("#con_id").css({borderColor: "red"});
                            $("#con_id").focus();
                            return false;
                        }
                        else if (reg_vac_finicio.value.length == 0) {
                            $("#reg_vac_finicio").css({borderColor: "red"});
                            $("#reg_vac_finicio").focus();
                            return false;
                        }
                        else if (reg_vac_ffinal.value.length == 0) {
                            $("#reg_vac_ffinal").css({borderColor: "red"});
                            $("#reg_vac_ffinal").focus();
                            return false;
                        }
                        else if (reg_vac_descripcion.value.length == 0) {
                            $("#reg_vac_descripcion").css({borderColor: "red"});
                            $("#reg_vac_descripcion").focus();
                            return false;
                        }
                        else if (reg_vac_documento.value.length == 0) {
                            $("#reg_vac_documento").css({borderColor: "red"});
                            $("#reg_vac_documento").focus();
                            return false;
                        }

                    },
                    type: 'POST',
                    url: 'actions_permisos_vacaciones.php',
                    data: {op: 0, 'data[]': data, id: pv_id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
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
//                parent.document.getElementById('mainFrame').src = '../Scripts/reg_permisos_list.php?txt=' + '<?php echo $txt ?>';
                parent.document.getElementById('mainFrame').src = '../Scripts/reg_permisos_list.php';
            }
        </script>
        <style>
            *{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO REGISTRO DE PERMISOS Y VACACIONES<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Empleado</td>
                        <td>
                            <input type="hidden" name="id"  id="id" value="<?php echo $rst[emp_id] ?>" size="3" onkeyup="this.value = this.value.replace(/[^0-9]/, '');" />
                            <input type="text" name="emp_id" id="emp_id" value="<?php echo $rst[emp_codigo] ?>" size="3" maxlength="4" onchange="findEmp(this.value.toUpperCase())" />
                        </td>
                    </tr>
                    <tr>
                        <td id="emp" name="emp" colspan="2" style="font-size:14px" ><?php echo $rst[emp_apellido_paterno] . ' ' . $rst[emp_apellido_materno] . ' ' . $rst[emp_nombres] ?></td>
                    </tr>
                    <tr>
                        <td>Condicion</td>
                        <td>
                            <select name="con_id" id="con_id" style="width: 160px">
                                <option value=0>Seleccione</option>
                                <?php
                                $cnsCon = $PerVac->listConseptoPermisos();
                                while ($rstCon = pg_fetch_array($cnsCon)) {
                                    echo "<option value=$rstCon[crp_id] >$rstCon[crp_descripcion]</option>";
                                }
                                ?>   
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Fecha Inicio</td>
                        <td>
                            <input type="text" id="reg_vac_finicio"  name="reg_vac_finicio" value="<?php echo $rst[reg_vac_finicio] ?>" size="10" />
                            <img src="../img/calendar.png" id="finicio" />
                        </td>
                    </tr>
                    <tr>
                        <td>Fecha Fin</td>
                        <td>
                            <input type="text" id="reg_vac_ffinal"  name="reg_vac_ffinal" value="<?php echo $rst[reg_vac_ffinal] ?>" size="10" size="5" />
                            <img src="../img/calendar.png" id="ffinal" />
                        </td>
                    </tr>
                    <tr>
                        <td>Hora Inicio</td>
                        <td>
                            <input type="text" id="reg_vac_hinicio"  name="reg_vac_hinicio" value="<?php echo $rst[reg_vac_hinicio] ?>" size="5" maxlength="5" onkeyup="mascara(this, ':', patron, true)" />
                        </td>
                    </tr>
                    <tr>
                        <td>Hora Fin</td>
                        <td>
                            <input type="text" id="reg_vac_hfinal"  name="reg_vac_hfinal" value="<?php echo $rst[reg_vac_hfinal] ?>" size="5" maxlength="5" onkeyup="mascara(this, ':', patron, true)" />
                        </td>
                    </tr>

                    <tr>
                        <td>Documento</td>
                        <td>
                            <input name="reg_vac_documento"  id="reg_vac_documento" value="<?php echo $rst[reg_vac_documento] ?>" size="10" onkeyup="this.value = this.value.replace(/[^0-9V]/, '');" />
                            Automatico:<input type="checkbox" <?php echo $chk ?> onchange="documento_automatico(this)"   />
                        </td>
                    </tr>
                    <tr>
                        <td>Descripcion</td>
                        <td><input name="reg_vac_descripcion" id="reg_vac_descripcion" value="<?php echo $rst[reg_vac_descripcion] ?>" size="40" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Sin Descuento:<input <?php echo $rec0 ?> type="radio" name="reg_vac_recargo" id="reg_vac_recargo1" value="0"/>
                            A Horas:<input <?php echo $rec1 ?> type="radio" name="reg_vac_recargo" id="reg_vac_recargo2"  value="1"/>
                            A Vacaciones:<input <?php echo $rec2 ?> type="radio" name="reg_vac_recargo"  id="reg_vac_recargo3" value="2"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Observaciones</td>
                        <td>
                            <textarea name="reg_vac_obs" style="width: 100%" id="reg_vac_obs" ><?php echo $rst[reg_vac_obs] ?></textarea>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 
                                <button id="guardar" onclick="save(<?php echo $ids ?>, 0)">Guardar</button>    
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <script>
            var crt = '<?php echo $rst[con_id] ?>';
            $('#con_id').val(crt);
        </script>