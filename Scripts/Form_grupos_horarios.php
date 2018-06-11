<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_horarios.php';
$Hor = new Clase_horarios;
$txt = $_GET[txt];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Hor->lista_un_grupo_horario($id));
} else {
    $id = 0;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario Grupos Horarios</title>
        <script>
            var id =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });
            function save(id) {
                var data = Array();
                $("#tbl_form").find('.hor_id').each(function () {
                    n = this.lang;
                    var chbox = document.getElementById('selec_hor' + n).checked;
                    if (chbox == true) {
                        dat = $('#id_horarios' + n).val();
                        data.push(dat);
                    }
                });
                if (gru_lunes.checked == true) {
                    lunes = 1;
                } else {
                    lunes = 0;
                }
                if (gru_martes.checked == true) {
                    martes = 1;
                } else {
                    martes = 0;
                }
                if (gru_miercoles.checked == true) {
                    miercoles = 1;
                } else {
                    miercoles = 0;
                }
                if (gru_jueves.checked == true) {
                    jueves = 1;
                } else {
                    jueves = 0;
                }
                if (gru_viernes.checked == true) {
                    viernes = 1;
                } else {
                    viernes = 0;
                }
                if (gru_sabado.checked == true) {
                    sabado = 1;
                } else {
                    sabado = 0;
                }
                if (gru_domingo.checked == true) {
                    domingo = 1;
                } else {
                    domingo = 0;
                }
                var data1 = Array(grupo.value.toUpperCase(),
                        horas_semana.value,
                        lunes,
                        martes,
                        miercoles,
                        jueves,
                        viernes,
                        sabado,
                        domingo
                        );
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                if ($('#grupo').val() == '') {
                    $('#grupo').focus();
                    $('#grupo').css('border', 'Solid 1px brown');
                } else if ($('#horas_semana').val() == '') {
                    $('#horas_semana').focus();
                    $('#horas_semana').css('border', 'Solid 1px brown');
                } else {
                    $.post("actions_horarios.php", {op: 2, 'data[]': data, 'data1[]': data1, 'fields[]': fields, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_grupos_horarios.php';
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE GRUPOS HORARIOS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>GRUPO:</td>
                    <td colspan="2"><input type="text" size="20"  id="grupo" value="<?php echo $rst[gru_horarios] ?>"/></td>
                </tr>
                <thead>
                    <tr>
                        <th>Descripcion</th>
                        <th>Entrada/Salida</th>
                        <th>Almuerzo</th>
                        <th></th>
                    </tr>
                </thead>
                <?php
                $cns = $Hor->lista_horarios();
                $n = 0;
                while ($rst1 = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst1['hor_si_no'] == 1) {
                        $sino = 'SI';
                    } else {
                        $sino = 'NO';
                    }
                    ?>
                    <tr>
                        <td>
                            <label for="descripcion"><?php echo $rst1[hor_descripcion] ?></label>
                        </td>
                        <td align="center">
                            <label for="hora"><?php echo $rst1[hor_h_entrada] ?> a <?php echo $rst1[hor_h_salida] ?></label>
                        </td>
                        <td align="center">
                            <label for="almu"><?php echo $sino ?></label>
                        </td>
                        <td colspan="2">
                            <input type="hidden" size="1" id="<?php echo 'id_horarios' . $rst1[hor_id] ?>" value="<?php echo $rst1[hor_id] ?>" class="hor_id" lang="<?php echo $rst1[hor_id] ?>" >
                            <input type="checkbox" id="<?php echo 'selec_hor' . $rst1[hor_id] ?>" lang="<?php echo $rst1[hor_id] ?>" >
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>HORAS SEMANA:</td>
                    <td colspan="2"><input type="text" id="horas_semana" value="<?php echo $rst[gru_hrs_semana] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"></td>
                </tr>
                <tr>
                    <td>LUNES</td>
                    <td><input type="checkbox" id="gru_lunes"></td>
                </tr>
                <tr>
                    <td>MARTES</td>
                    <td><input type="checkbox" id="gru_martes"></td>
                </tr>
                <tr>
                    <td>MIERCOLES</td>
                    <td><input type="checkbox" id="gru_miercoles"></td>
                </tr>
                <tr>
                    <td>JUEVES</td>
                    <td><input type="checkbox" id="gru_jueves"></td>
                </tr>
                <tr>
                    <td>VIERNES</td>
                    <td><input type="checkbox" id="gru_viernes"></td>
                </tr>
                <tr>
                    <td>SABADO</td>
                    <td><input type="checkbox" id="gru_sabado"></td>
                </tr>
                <tr>
                    <td>DOMINGO</td>
                    <td><input type="checkbox" id="gru_domingo"></td>
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
    id_g = '<?php echo $rst[gru_id_horarios] ?>';
    idg = id_g.split(',');
    $("#tbl_form").find('.hor_id').each(function () {
        n = this.lang;
        i = 0;
        while (i < idg.length) {
            var a = idg[i];
            if (a == n) {
                $('#selec_hor' + n).attr('checked', true);
            }
            i++;
        }
    });
    var lun = <?php echo $rst[gru_lunes] ?>;
    if (lun == 1) {
        $('#gru_lunes').attr('checked', true);
    }
    var mar = <?php echo $rst[gru_martes] ?>;
    if (mar == 1) {
        $('#gru_martes').attr('checked', true);
    }
    var mie = <?php echo $rst[gru_miercoles] ?>;
    if (mie == 1) {
        $('#gru_miercoles').attr('checked', true);
    }
    var jue = <?php echo $rst[gru_jueves] ?>;
    if (jue == 1) {
        $('#gru_jueves').attr('checked', true);
    }
    var vie = <?php echo $rst[gru_viernes] ?>;
    if (vie == 1) {
        $('#gru_viernes').attr('checked', true);
    }
    var sab = <?php echo $rst[gru_sabado] ?>;
    if (sab == 1) {
        $('#gru_sabado').attr('checked', true);
    }
    var dom = <?php echo $rst[gru_domingo] ?>;
    if (dom == 1) {
        $('#gru_domingo').attr('checked', true);
    }
</script>

