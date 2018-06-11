<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_horarios.php';
$Hor = new Clase_horarios;
if (isset($_GET[id])) {
    echo $id = $_GET[id];
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $text = "where ger_codigo like '%$txt%' or ger_descripcion like '%$txt%'";
    }
    $cns = $Hor->lista_buscardor_criterios($text);
} else {
    $id = 0;
    $cns = $Hor->lista_horarios();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Horarios</title>
    <head>
        <script>
            $(function () {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                hor_si.checked = true;
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function update_horario(id) {
                $.post("actions_horarios.php", {op: 5, id: id},
                function (dt) {
                    dat = dt.split('&');
                    $('#hor_id').val(dat[0]);
                    $('#hor_descripcion').val(dat[1]);
                    $('#hor_h_entrada').val(dat[2]);
                    $('#hor_h_salida').val(dat[3]);
                    if (dat[4] == 1) {
                        $('#hor_si').attr('checked', true);
                        $('#hor_h_inicio').attr('disabled', false);
                        $('#hor_h_final').attr('disabled', false);
                    } else if (dat[4] == 2) {
                        $('#hor_no').attr('checked', true);
                        $('#hor_h_inicio').attr('disabled', true);
                        $('#hor_h_final').attr('disabled', true);
                    }
                    $('#hor_h_inicio').val(dat[5]);
                    $('#hor_h_final').val(dat[6]);
                    $('#hor_h_total').val(dat[7]);
                });
            }

            function guardar() {
                id =  $('#hor_id').val();
                desc = $('#hor_descripcion').val().toUpperCase();
                he = $('#hor_h_entrada').val();
                hs = $('#hor_h_salida').val();
                if (hor_si.checked == true) {
                    sino = 1;
                } else if (hor_no.checked == true) {
                    sino = 2;
                }
                hi = $('#hor_h_inicio').val();
                hf = $('#hor_h_final').val();
                ht = $('#hor_h_total').val();
                fields = '';
                if (desc == '') {
                    $('#hor_descripcion').focus();
                    $('#hor_descripcion').css('border', 'Solid 1px brown');
                } else if (he == '') {
                    $('#hor_h_entrada').focus();
                    $('#hor_h_entrada').css('border', 'Solid 1px brown');
                } else if (hs == '') {
                    $('#hor_h_salida').focus();
                    $('#hor_h_salida').css('border', 'Solid 1px brown');
                } else if (sino == '') {
                    $('#hor_si_no').focus();
                    $('#hor_si_no').css('border', 'Solid 1px brown');
                } else if (hi == '') {
                    $('#hor_h_inicio').focus();
                    $('#hor_h_inicio').css('border', 'Solid 1px brown');
                } else if (hf == '') {
                    $('#hor_h_final').focus();
                    $('#hor_h_final').css('border', 'Solid 1px brown');
                } else if (ht == '') {
                    $('#hor_h_total').focus();
                    $('#hor_h_total').css('border', 'Solid 1px brown');
                } else {
                    data = Array(desc, he, hs, sino, hi, hf, ht);
                    $.post("actions_horarios.php", {op: 0, 'data[]': data, id: id},
                    function (dt) {
                        if (dt != 0) {
                            alert(dt);
                        } else {
                            window.history.go(0);
                        }
                    });
                }
            }

            function restarHoras() {
                hora_entrada = $('#hor_h_entrada').val();
                hora_salida = $('#hor_h_salida').val();
                h_ini_almuerzo = $('#hor_h_inicio').val();
                h_fin_almuerzo = $('#hor_h_final').val();
                ///////////////// HORARIOS ///////////////////////
                inicioMinutos = parseInt(hora_entrada.substr(3, 2));
                inicioHoras = parseInt(hora_entrada.substr(0, 2));

                finMinutos = parseInt(hora_salida.substr(3, 2));
                finHoras = parseInt(hora_salida.substr(0, 2));

                transcurridoMinutos = finMinutos - inicioMinutos;
                transcurridoHoras = finHoras - inicioHoras;

                if (transcurridoMinutos < 0) {
                    transcurridoHoras--;
                    transcurridoMinutos = 60 + transcurridoMinutos;
                }

                horas = transcurridoHoras.toString();
                minutos = transcurridoMinutos.toString();

                if (horas.length < 2) {
                    horas = "0" + horas;
                }

                if (horas.length < 2) {
                    horas = "0" + horas;
                }

                if (minutos == 0) {
                    min = '00';
                } else {
                    min = minutos;
                }

                //////////////// ALMUERZO ///////////////////////
                inicioMinutosAl = parseInt(h_ini_almuerzo.substr(3, 2));
                inicioHorasAl = parseInt(h_ini_almuerzo.substr(0, 2));

                finMinutosAl = parseInt(h_fin_almuerzo.substr(3, 2));
                finHorasAl = parseInt(h_fin_almuerzo.substr(0, 2));

                transcurridoMinutosAl = finMinutosAl - inicioMinutosAl;
                transcurridoHorasAl = finHorasAl - inicioHorasAl;

                if (transcurridoMinutosAl < 0) {
                    transcurridoHorasAl--;
                    transcurridoMinutosAl = 60 + transcurridoMinutosAl;
                }

                horasAl = transcurridoHorasAl.toString();
                minutosAl = transcurridoMinutosAl.toString();

                if (horasAl.length < 2) {
                    horasAl = "0" + horasAl;
                }

                if (horasAl.length < 2) {
                    horasAl = "0" + horasAl;
                }

                if (minutosAl == 0) {
                    minAl = '00';
                } else {
                    minAl = minutosAl;
                }

                /////////// CALCULO HORA TOTAL CON ALMUERZO 
                h_tot = horas + ":" + min;
                h_tot_alm = horasAl + ":" + minAl;

                inicioMinutosTot = parseInt(h_tot_alm.substr(3, 2));
                inicioHorasTot = parseInt(h_tot_alm.substr(0, 2));

                finMinutosTot = parseInt(h_tot.substr(3, 2));
                finHorasTot = parseInt(h_tot.substr(0, 2));

                transcurridoMinutosTot = finMinutosTot - inicioMinutosTot;
                transcurridoHorasTot = finHorasTot - inicioHorasTot;

                if (transcurridoMinutosTot < 0) {
                    transcurridoHorasTot--;
                    transcurridoMinutosTot = 60 + transcurridoMinutosTot;
                }

                horasTot = transcurridoHorasTot.toString();
                minutosTot = transcurridoMinutosTot.toString();

                if (horasTot.length < 2) {
                    horasTot = "0" + horasTot;
                }

                if (horasTot.length < 2) {
                    horasTot = "0" + horasTot;
                }

                if (minutosTot == 0) {
                    minTot = '00';
                } else {
                    minTot = minutosTot;
                }

                if (hor_si.checked == true) {
                    if (horasTot == 'NaN') {
                        horasTotAl = '00';
                    } else {
                        horasTotAl = horasTot;
                    }
                    if (minTot == 'NaN') {
                        minTotAl = '00';
                    } else {
                        minTotAl = minTot;
                    }
                    h_tot_almuerzo = horasTotAl + ":" + minTotAl;
                    h = h_tot_almuerzo.replace("-", "");
                    $('#hor_h_total').val(h);
                    $('#hor_h_inicio').attr('disabled', false);
                    $('#hor_h_final').attr('disabled', false);
                } else if (hor_no.checked == true) {
                    h_ini = '00:00';
                    h_fin = '00:00';
                    $('#hor_h_inicio').val(h_ini);
                    $('#hor_h_final').val(h_fin);
                    $('#hor_h_inicio').attr('disabled', true);
                    $('#hor_h_final').attr('disabled', true);
                    if (horas == 'NaN') {
                        horasNoAlm = '00';
                    } else {
                        horasNoAlm = horas;
                    }
                    if (min == 'NaN') {
                        minNoAlm = '00';
                    } else {
                        minNoAlm = min;
                    }
                    h_tot_almuerzo = horasNoAlm + ":" + minNoAlm;
                    h = h_tot_almuerzo.replace("-", "");
                    $('#hor_h_total').val(h);
                }
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_horarios.php", {op: 4, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
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
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >LISTA HORARIOS</center>
                <center class="cont_finder">
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="4">Horarios</th>
                    <th colspan="3">Almuerzo</th>
                    <th colspan="2"></th>
                </tr>
                <tr>
                    <th></th>
                    <th>Descripcion</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Si/No</th>
                    <th>Hora Inicio</th>
                    <th>Hora Final</th>
                    <th>Hora Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <tr>
                    <td></td>
                    <td align="center">
                        <input type="hidden" size="5" id="hor_id" value="0">
                        <input type="text" id="hor_descripcion" >
                    </td>
                    <td align="center"><input type="text" id="hor_h_entrada" onblur="restarHoras()" /></td>
                    <td align="center"><input type="text" id="hor_h_salida" onblur="restarHoras()" /></td>
                    <td align="center">
                        SI<input type="radio" name="si_no" id="hor_si" onclick="restarHoras()" >
                        &nbsp;&nbsp;&nbsp;
                        NO<input type="radio" name="si_no" id="hor_no" onclick="restarHoras()" >
                    </td>
                    <td align="center"><input type="text" id="hor_h_inicio" onblur="restarHoras()"  ></td>
                    <td align="center"><input type="text" id="hor_h_final" onblur="restarHoras()" ></td>
                    <td align="center"><input type="text" id="hor_h_total" readonly ></td>
                    <td align="center">
                        <img class="axb" src="../img/save.png" title="Guardar" onclick="guardar('<?php echo $rst[obl_codigo] ?>')" /> 
                    </td>
                </tr>
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst['hor_si_no'] == 1) {
                        $sino = 'SI';
                    } else {
                        $sino = 'NO';
                    }
                    ?>
                    <tr style="height: 30px">
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['hor_descripcion'] ?></td>
                        <td align="center" ><?php echo $rst['hor_h_entrada'] ?></td>
                        <td align="center" ><?php echo $rst['hor_h_salida'] ?></td>
                        <td align="center"><?php echo $sino ?></td>
                        <td align="center" ><?php echo $rst['hor_h_inicio'] ?></td>
                        <td align="center" ><?php echo $rst['hor_h_final'] ?></td>
                        <td align="center"><?php echo $rst['hor_h_total'] ?></td>
                        <td align="center">
                            <?php
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rst[hor_id] ?>)">
                                <?php
                            }
                            if ($Prt->edition == 0) {
                                ?>
                                <img src="../img/upd.png" width="12px" class="auxBtn" onclick="update_horario(<?php echo $rst[hor_id] ?>)">
                                <?php
                            }
                            ?>
                        </td> 
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>

