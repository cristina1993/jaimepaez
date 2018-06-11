<?php
include_once("../Clases/clsUsers.php");
include_once '../Includes/permisos.php';
$cns = $User->lista_configuraciones1();
$cns1 = $User->lista_configuraciones1();
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Formulas</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function auxWindow(a, id, sts)
            {
                frm = parent.document.getElementById('bottomFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_usuario.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_usuario.php?id=' + id;
                        break;
                    case 2://Cambiar Estado
                        if (confirm('Esta seguro de cambiar de Estado a este Usuario?') == true)
                        {
                            if (sts == 't')
                            {
                                sts = 'f';
                            } else {
                                sts = 't';
                            }
                            data = Array(sts);
                            $.post("actions.php", {act: 9, 'data[]': data, id: id},
                            function (dt) {
                                if (dt == 0)
                                {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_usuarios.php';
                                    parent.document.getElementById('bottomFrame').src = '';
                                } else {
                                    alert(dt);
                                }
                            })

                        }

                        break;
                    case 3://Permisos
                        frm.src = '../Scripts/Form_permisos.php?id=' + id;
                        break;
                    case 4://Permisos
                        frm.src = '../Scripts/Form_certificados.php?cr=' + id;
                        break;
                    case 5://Permisos
                        frm.src = '../Scripts/Form_conf_email.php?cr=' + id;
                        break;

                }

            }
//            function delete_all()
//            {
//                if (prompt("Advertencia Este proceso Eliminara todos los registros de la DB; \n Si es Usuario Autorizado Ingrese el Codigo para Ejecutar") == 1234)
//                {
//                    $.post("actions.php", {act: 14},
//                    function (dt) {
//                        if (dt == 0)
//                        {
//                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_usuarios.php';
//                        }
//                    });
//                }
//
//            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_cuentas(obj) {
                $.post("actions.php", {act: 84, id: obj.value},
                function (dt) {
                    if (dt == 0)
                    {

                    }
                });

            }
//                if (confirm('Esta seguro de cambiar de ambiente?') == true) {
//                    var fields = Array();
//                    $("#form_save").find(':input').each(function () {
//                        var elemento = this;
//                        des = elemento.id + "=" + elemento.value;
//                        fields.push(des);
//                    });
//                    fields.push('');
//                    $.post("actions.php", {act: 81, id: am, s: 1, 'fields[]': fields},
//                    function (dt) {
//                        if (dt == 0)
//                        {
//                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_configuraciones.php';
//                        }
//                    });
//                } else {
//                    return false;
//                }
//            }

            function cambios() {
                if (confirm('Esta seguro de realizar los cambios?') == true) {
                    mon = $('#moneda').val();
                    can = $('#cantidad').val();
                    if ($('#con_asiento1').attr('checked') == true) {
                        asiento = 0;
                    } else {
                        asiento = 1;
                    }

                    if ($('#con_inventario1').attr('checked') == true) {
                        inventario = 0;
                    } else {
                        inventario = 1;
                    }
                    if ($('#con_ambiente0').attr('checked') == true) {
                        ambiente = 0;
                    } else if ($('#con_ambiente1').attr('checked') == true) {
                        ambiente = 1;
                    } else {
                        ambiente = 2;
                    }
                    if ($('#inv_gen1').attr('checked') == true) {
                        inv_gen = 0;
                    } else {
                        inv_gen = 1;
                    }
                    if ($('#pago_sueldo1').attr('checked') == true) {
                        pag_sld = 0;
                    } else {
                        pag_sld = 1;
                    }
                    if ($('#pago_he1').attr('checked') == true) {
                        pag_he = 0;
                    } else {
                        pag_he = 1;
                    }
                    id_conf = 16;
                    fec_ini = $('#inicio').val();
                    fec_fnl = $('#final').val();
                    id_conf1 = 17;
                    fec_ini1 = '0';
                    fec_fnl1 = '0';
                    var data = Array(
                            can,
                            mon,
                            inventario,
                            asiento,
                            ambiente,
                            inv_gen
                            );
                    var data2 = Array(
                            id_conf,
                            pag_sld,
                            fec_ini,
                            fec_fnl
                            );
                    var data3 = Array(
                            id_conf1,
                            pag_he,
                            fec_ini1,
                            fec_fnl1
                            );
                    var data4 = Array(sueldo_basico.value);
                    var fields = Array();
                    $("#form_save").find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                    fields.push('');

                    $.post("actions.php", {act: 81, 'data[]': data, 'data2[]': data2, 'data3[]': data3, 'data4[]': data4, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_configuraciones.php';
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
            #mn268{
                background:black;
                color:white;
                border: solid 1px white;
            }
            table tbody tr td{
                height:30px; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
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
                <center class="cont_title" >LISTA DE CONFIGURACIONES GENERALES</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>-->
                    <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Guardar Cambios" onclick="cambios()" >GUARDAR</a>

<!--                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Usuario:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  -->
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Nombre</th>
            <th align="left" colspan="3">Parametros</th>
        </thead>
        <tbody id="form_save">
            <?php
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $a = '""';
//                if (($rst[con_id] >= 6 && $rst[con_id] <= 12) || ($rst[con_id] >= 16 && $rst[con_id] <= 22)) {
//                    $op = 6;
//                } else {
                $op = $rst[con_id];
                if ($op != 16 && $op != 17) {
//                }
                    switch ($op) {
                        case '1':
                            $text = "<input type='text' id='cantidad'  value='$rst[con_valor]' size='10' style='text-align: right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a);'/>";
                            break;
                        case '2':
                            $text = "<input type='text' id='moneda'  value='$rst[con_valor]' size='10' style='text-align: right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a);'/>";
                            break;
                        case '3':
                            if ($rst[con_valor] == 0) {
                                $check1 = 'checked';
                                $check2 = '';
                            } else {
                                $check2 = 'checked';
                                $check1 = '';
                            }
                            $text = "<input type='radio' name='inventario' id='con_inventario1'  $check1 '/>SI
                        <input type='radio' name='inventario' id='con_inventario2' $check2 '/>NO";
                            break;
                        case '4':
                            if ($rst[con_valor] == 0) {
                                $check3 = 'checked';
                                $check4 = '';
                            } else {
                                $check4 = 'checked';
                                $check3 = '';
                            }
                            $text = "<input type='radio' name='asientos' id='con_asiento1'  $check3 />SI
                        <input type='radio' name='asientos' id='con_asiento2' $check4/>NO";
                            break;
                        case '5':
                            if ($rst[con_valor] == 0) {
                                $chec4 = 'checked';
                                $check5 = '';
                                $check6 = '';
                            } else if ($rst[con_valor] == 1) {
                                $check5 = 'checked';
                                $chec4 = '';
                                $check6 = '';
                            } else {
                                $check6 = 'checked';
                                $check5 = '';
                                $chec4 = '';
                            }
                            $text = "<input type='radio' name='ambiente' id='con_ambiente0'  $chec4 />NINGUNO
                                 <input type='radio' name='ambiente' id='con_ambiente1'  $check5 />PRUEBAS
                                 <input type='radio' name='ambiente' id='con_ambiente2' $check6/>PRODUCCION";
                            break;
                        case '6':
//                        $rst_c = pg_fetch_array($User->lista_cuentas_cod($rst[con_valor]));
//                        $text = "<input type='text' id='cuenta$rst[con_id]'  value='$rst_c[pln_codigo]' size='40' list='cuentas'/>";
                            if ($rst[con_valor] == 0) {
                                $check7 = 'checked';
                                $check8 = '';
                            } else {
                                $check8 = 'checked';
                                $check7 = '';
                            }
                            $text = "<input type='radio' name='inv_gen' id='inv_gen1'  $check7/> GENERAL
                        <input type='radio' name='inv_gen' id='inv_gen1' $check8/>POR PTO EMISION";
                            break;

                        case 8:
                            if (empty($rst[con_valor2])) {
                                $rst[con_valor2] = 'No existen Credenciales Asignadas';
                            } else {
                                $cr = explode('&', $rst[con_valor2]);
                                $rst[con_valor2] = $cr[0] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(' . $cr[2] . ')';
                            }
                            $text = "<font>$rst[con_valor2]</font><img class='auxBtn' width='16px' src='../img/upd.png' onclick='auxWindow(5,8)' />";
                            break;
                        case 13:
                            if (empty($rst[con_valor2])) {
                                $rst[con_valor2] = 'No existen Credenciales Asignadas';
                            } else {
                                $cr = explode('&', $rst[con_valor2]);
                                $rst[con_valor2] = $cr[0] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(' . $cr[2] . ')';
                            }
                            $text = "<font>$rst[con_valor2]</font><img class='auxBtn' width='16px' src='../img/upd.png' onclick='auxWindow(4,13)' />";
                            break;
                        case 14:
                            if (empty($rst[con_valor2])) {
                                $rst[con_valor2] = 'No existen Credenciales Asignadas';
                            } else {
                                $cr = explode('&', $rst[con_valor2]);
                                $rst[con_valor2] = $cr[0] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(' . $cr[2] . ')';
                            }
                            $text = "<font>$rst[con_valor2]</font><img class='auxBtn' width='16px' src='../img/upd.png' onclick='auxWindow(4,14)' />";
                            break;
                        case 15:
                            $text = "<input type='text' id='empresa' readonly  value='$rst[con_valor2]' size='40' />";
                            break;
                        default :
                            $text = '';
                            break;
                    }
                    if ($rst[con_id] != 14 && $rst[con_id] != 7) {
                        ?>
                        <tr>
                            <td  align="left"style='width: 1%'><?PHP echo $n ?></td>
                            <td style='width: 20%'><?PHP echo $rst[con_nombre] ?></td>
                            <td align="left" colspan="2" style='width: 20%'><?PHP echo $text ?></td>
                            <td align="left" colspan="2" style='width: 70%'></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </tbody>
        <thead>
        <th align="center" colspan="5">RRHH</th>
    </thead>
    <tbody>
        <?php
        $j = 0;
        while ($rst1 = pg_fetch_array($cns1)) {
            $j++;
            $op1 = $rst1[con_id];
            if($op1 == 18){
                $s_basico = $rst1[con_valor2];
            }
            if ($op1 >= 16 && $op1 <= 17) {
                switch ($op1) {
                    case '16':
                        if ($rst1[con_valor] == 0) {
                            $check1 = 'checked';
                            $check2 = '';
                        } else {
                            $check2 = 'checked';
                            $check1 = '';
                        }
                        $text = "<input type='radio' name='pago_sueldo' id='pago_sueldo1'  $check1 '/>MENSUAL
                                 <input type='radio' name='pago_sueldo' id='pago_sueldo2' $check2 '/>QUINCENAL";
                        $fecha = "INICIO<input type='text' name='inicio' id='inicio' value='$rst1[con_valor2]'>
                                  FINAL<input type='text' name='final' id='final' value='$rst1[con_valor3]'> MES";
                        break;
                    case '17':
                        if ($rst1[con_valor] == 0) {
                            $check3 = 'checked';
                            $check4 = '';
                        } else {
                            $check4 = 'checked';
                            $check3 = '';
                        }
                        $text = "<input type='radio' name='pago_he' id='pago_he1'  $check3 />IGUAL AL PERIODO
                                 <input type='radio' name='pago_he' id='pago_he2' $check4/>RETRAZO";
                        $fecha = "";
                        break;
                }
                echo"<tr>
                        <td>$j</td>
                        <td>$rst1[con_nombre]</td>
                        <td>$text</td>
                        <td>$fecha</td> 
                        <td></td> 
                    </tr>";
            }
        }
        ?>
        <tr>
            <td  align="left">5</td>
            <td>SUELDO BASICO</td>
            <td>
                <input type="text" name="sueldo_basico" id="sueldo_basico" value="<?php echo $s_basico ?>"/>
            </td>
            <td></td>
        </tr>
    </tbody>
</table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>
<datalist id="cuentas">
    <?php
    $cns_ctas = $User->lista_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_codigo]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
