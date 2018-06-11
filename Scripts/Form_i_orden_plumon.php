<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion_plumon($id));
} else {
    $cns = $Set->lista_orden_produccion_plumon();
    $emp_id = $_GET [emp_id];
    $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($emp_id));
    $primero = 'P-' . $rst_fbc[emp_sigla] . '-';
    $rst[orp_num_pedido] = $primero;
    $rst[orp_pro_peso] = 0;
    $rst[orp_kg1] = 0;
    $rst[orp_kg2] = 0;
    $rst[orp_kg3] = 0;
    $rst[orp_kg4] = 0;
    $rst[orp_mf1] = 0;
    $rst[orp_mf2] = 0;
    $rst[orp_mf3] = 0;
    $rst[orp_mf4] = 0;
    $rst[orp_fec_pedido] = date("Y-m-d");
    $rst[orp_fec_entrega] = date("Y-m-d");
    $rst[orp_mftotal] = '0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion_plumon());
    $cod = substr($rst_sec[orp_num_pedido], -5);
    $sec = ($cod + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "0000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "00";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "0";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "";
    }
    $no_orden = $tx_trs . $sec;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "orp_fec_pedido", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "orp_fec_entrega", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                producto(<?php echo $rst[pro_id] ?>);
            });
            function limpiar_datos_detalle()
            {
                orp_num_pedido.value = '<?php echo $primero ?>';
                orp_pro_ancho.value = 0;
                orp_kg1.value = 0;
                orp_kg2.value = 0;
                orp_kg3.value = 0;
                orp_kg4.value = 0;
                orp_mf1.value = 0;
                orp_mf2.value = 0;
                orp_mf3.value = 0;
                orp_mf4.value = 0;
                orp_mftotal.value = 0;
                orp_kgtotal.value = 0;
                orp_pro_largo.value = 0;
                orp_pro_peso.value = 0;
                orp_pro_gramaje.value = 0;
            }

            function limpiar()
            {
                pro_id.style.borderColor = "";
                cli_id.style.borderColor = "";
                orp_cantidad.style.borderColor = "";
                orp_fec_pedido.style.borderColor = "";
                orp_fec_entrega.style.borderColor = "";
                orp_mftotal.style.borderColor = "";
            }

            function save(id)
            {
                if (cli_id.value == 0) {
                    alert('El Cliente es un campo obligatorio.');
                    cli_id.focus();
                    limpiar();
                    cli_id.style.borderColor = "red";
                } else if (pro_id.value == 0) {
                    alert('El Producto es un campo obligatorio.');
                    pro_id.focus();
                    limpiar();
                    pro_id.style.borderColor = "red";
                } else if (orp_cantidad.value.length == 0) {
                    alert('La Cantidad es un campo obligatorio.');
                    orp_cantidad.focus();
                    limpiar();
                    orp_cantidad.style.borderColor = "red";
                } else if (orp_fec_pedido.value.length == 0) {
                    alert('La Fecha de Pedido es un campo obligatorio.');
                    orp_fec_pedido.focus();
                    limpiar();
                    orp_fec_pedido.style.borderColor = "red";
                } else if (orp_fec_entrega.value.length == 0) {
                    alert('La Fecha de Entrega es un campo obligatorio.');
                    orp_fec_entrega.focus();
                    limpiar();
                    orp_fec_entrega.style.borderColor = "red";
                } else if (orp_fec_entrega.value < orp_fec_pedido.value) {
                    alert('El Fecha de Entrega no puede ser antes de la Fecha de Pedido.');
                    orp_fec_entrega.focus();
                    limpiar();
                    orp_fec_entrega.style.borderColor = "red";
                } else if (orp_mftotal.value == 0.0) {
                    alert('El valor total del Mix de Fibra no puede ser [ 0.0 ]');
                    limpiar();
                    orp_mftotal.focus();
                } else if (orp_mftotal.value > 100) {
                    alert('El valor total del Mix de Fibra no puede ser mayor a [ 100 ]%');
                    limpiar();
                    orp_mftotal.focus();
                    orp_mftotal.style.borderColor = "red";
                } else if (orp_mftotal.value < 100) {
                    alert('El valor total del Mix de Fibra no puede ser menor a [ 100 ]%');
                    limpiar();
                    orp_mftotal.focus();
                    orp_mftotal.style.borderColor = "red";
                } else {
                    var data = Array(
                            orp_num_pedido.value.toUpperCase(),
                            cli_id.value,
                            pro_id.value,
                            orp_pro_ancho.value,
                            orp_pro_largo.value,
                            orp_pro_peso.value,
                            orp_pro_gramaje.value,
                            orp_cantidad.value,
                            orp_mp1.value,
                            orp_mp2.value,
                            orp_mp3.value,
                            orp_mp4.value,
                            orp_mf1.value,
                            orp_mf2.value,
                            orp_mf3.value,
                            orp_mf4.value,
                            orp_mftotal.value,
                            orp_kg1.value,
                            orp_kg2.value,
                            orp_kg3.value,
                            orp_kg4.value,
                            orp_kgtotal.value,
                            orp_fec_pedido.value,
                            orp_fec_entrega.value,
                            orp_capa.value,
                            orp_espesor.value.toUpperCase(),
                            orp_med_vueltas.value,
                            orp_paquetes.value.toUpperCase(),
                            orp_temperatura.value,
                            orp_agua.value,
                            orp_resina.value,
                            orp_observaciones.value.toUpperCase());
                    $.post("actions.php", {act: 57, 'data[]': data, id: id},
                    function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    }
                    );
                }
            }
            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';

            }
            function calculo_porcentage() {
                orp_mftotal.value = (orp_mf1.value * 1 + orp_mf2.value * 1 + orp_mf3.value * 1 + orp_mf4.value * 1);
            }
            function calculo_kg() {
                orp_kgtotal.value = (orp_kg1.value * 1 + orp_kg2.value * 1 + orp_kg3.value * 1 + orp_kg4.value * 1);
            }
            function producto(id) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    pro_id.style.borderColor = "";
                    $.post("actions.php", {act: 59, id: id},
                    function (dt) {
                        dat = dt.split('&');
                        var a = '<?php echo $id ?>';
                        if (a.length == 0) {
                            document.getElementById("pro_id").disabled = false;
                            document.getElementById("cli_id").disabled = false;
                            document.getElementById("orp_fec_pedido").disabled = false;
                            $('#orp_mp1,#orp_mp2,#orp_mp3,#orp_mp4').html(dat[17]);
                            orp_pro_ancho.value = dat[0];
                            orp_mp1.value = dat[1];
                            orp_mp2.value = dat[2];
                            orp_mp3.value = dat[3];
                            orp_mp4.value = dat[4];
                            orp_mf1.value = dat[5];
                            orp_mf2.value = dat[6];
                            orp_mf3.value = dat[7];
                            orp_mf4.value = dat[8];
                            orp_mftotal.value = dat[9];
                            orp_pro_largo.value = dat[10];
                            orp_pro_peso.value = dat[11];
                            orp_pro_gramaje.value = dat[12];
                            orp_num_pedido.value = '<?php echo $primero ?>' + dat[13] + '-' + '<?php echo $no_orden ?>';
                            $('#orp_temperatura').val(dat[14]);
                            $('#orp_agua').val(dat[15]);
                            $('#orp_resina').val(dat[16]);
                        } else {
                            document.getElementById("pro_id").disabled = true;
                            document.getElementById("cli_id").disabled = true;
                            document.getElementById("orp_fec_pedido").disabled = true;
                            orp_pro_ancho.value = dat[0];
                            $('#orp_mp1,#orp_mp2,#orp_mp3,#orp_mp4').html(dat[17]);
                            $('#orp_mp1').val(<?php echo $rst[orp_mp1] ?>);
                            $('#orp_mp2').val(<?php echo $rst[orp_mp2] ?>);
                            $('#orp_mp3').val(<?php echo $rst[orp_mp3] ?>);
                            $('#orp_mp4').val(<?php echo $rst[orp_mp4] ?>);
                        }
                    }
                    );
                }
            }
        </script>
    </head>
    <body>                                          
        <table id="tbl_form" border='1' >
            <thead>
                <tr>
                    <th colspan="8" >Orden de Producción</th>
                </tr>
            </thead>
            <tr> 
                <td class="sbtitle" >DATOS GENERALES</td>
                <td class="sbtitle" ></td>
                <td class="sbtitle" >DETALLE PRODUCTOS </td>
                <td class="sbtitle" ></td>
                <td class="sbtitle" >MATERIAS PRIMAS </td>          
            </tr>
            <tr>
                <td>Pedido :</td>
                <td><input readonly type="text" name="orp_num_pedido" id="orp_num_pedido" size="25" value="<?php echo $rst[orp_num_pedido] ?>" /></td>
                <td>Ancho :</td>
                <td><input readonly style="text-align:right" name="orp_pro_ancho" id="orp_pro_ancho" size="10" value="<?php echo $rst[orp_pro_ancho] ?>" />  m </td>
                <td rowspan="4">
                    <select name="orp_mp1" id="orp_mp1"style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf1" id="orp_mf1" size="10" style="text-align:right" value="<?php echo $rst[orp_mf1] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg1" id="orp_kg1" size="10" style="text-align:right" value="<?php echo $rst[orp_kg1] ?>" /> kg<br />
                    <select name="orp_mp2" id="orp_mp2" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf2" id="orp_mf2" size="10" style="text-align:right" value="<?php echo $rst[orp_mf2] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg2" id="orp_kg2" size="10" style="text-align:right" value="<?php echo $rst[orp_kg2] ?>" /> kg<br />
                    <select name="orp_mp3" id="orp_mp3" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf3" id="orp_mf3" size="10" style="text-align:right" value="<?php echo $rst[orp_mf3] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg3" id="orp_kg3" size="10" style="text-align:right" value="<?php echo $rst[orp_kg3] ?>" /> kg<br />
                    <select name="orp_mp4" id="orp_mp4" style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="orp_mf4" id="orp_mf4" size="10" style="text-align:right" value="<?php echo $rst[orp_mf4] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="orp_kg4" id="orp_kg4" size="10" style="text-align:right" value="<?php echo $rst[orp_kg4] ?>" />kg<br />               
            </tr>
            <script>
                document.getElementById("orp_mp1").value = '<?php echo $rst[orp_mp1] ?>';
                document.getElementById("orp_mp2").value = '<?php echo $rst[orp_mp2] ?>';
                document.getElementById("orp_mp3").value = '<?php echo $rst[orp_mp3] ?>';
                document.getElementById("orp_mp4").value = '<?php echo $rst[orp_mp4] ?>';</script>
            <tr>
                <td>Cliente :</td>
                <td><select name="cli_id" id="cli_id" style="width:200px; " >
                        <option value="0"> - Elija un Cliente - </option>
                        <?php
                        $cns_cli = $Set->lista_clientes_tipo(1);
                        while ($rst_cli = pg_fetch_array($cns_cli)) {
                            echo "<option $sel value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
                        }
                        ?>
                    </select></td>
                <td>Largo:</td>
                <td><input readonly style="text-align:right" type="text" name="orp_pro_largo" id="orp_pro_largo" size="10" value="<?php echo $rst[orp_pro_largo] ?>" /> m </td>
            </tr>
            <tr>
                <td>Producto :</td>
                <td><select name="pro_id" id="pro_id" onchange="producto(pro_id.value)" >
                        <option value="0"> - Elija un Producto - </option>
                        <?php
                        $cns_pro = $Set->lista_producto();
                        while ($rst_pro = pg_fetch_array($cns_pro)) {
                            echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                        }
                        ?>
                    </select></td>
                <td>Peso:</td>
                <td> <input readonly style="text-align:right" type="text" name="orp_pro_peso" id="orp_pro_peso" size="10" value="<?php echo $rst[orp_pro_peso] ?>" /> kg </td>
            </tr>
            <script>
                document.getElementById("cli_id").value = '<?php echo $rst[cli_id] ?>';
                document.getElementById("pro_id").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td>Cantidad:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_peso()" type="text" name="orp_cantidad" id="orp_cantidad" style="text-align:right" size="10" value="<?php echo $rst[orp_cantidad] ?>" /></td>
                <td>Gramaje :</td>
                <td><input readonly style="text-align:right" type="text" name="orp_pro_gramaje" id="orp_pro_gramaje" size="10" value="<?php echo $rst[orp_pro_gramaje] ?>" /> gr / m² </td>
            </tr>
            <tr>
                <td>Fecha Pedido:</td>
                <td><input type="text" name="orp_fec_pedido" id="orp_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[orp_fec_pedido] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-desde" /></td>
                <td>Capa:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  style="text-align:right" type="text" name="orp_capa" id="orp_capa" size="12" value="<?php echo $rst[orp_capa] ?>" /></td>
                <td>Total 100%:  <input  readonly type="text" size="5" name="orp_mftotal" id="orp_mftotal" style="text-align:right" value="<?php echo $rst[orp_mftotal] ?>"/> %   Total : <input  readonly type="text" size="5" name="orp_kgtotal" id="orp_kgtotal" style="text-align:right" value="<?php echo $rst[orp_kgtotal] ?>"/> kg</td>               
            </tr>
            <tr>
                <td>Fecha Entrega:</td>
                <td><input type="text" name="orp_fec_entrega" id="orp_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[orp_fec_entrega] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-hasta" /></td>
                <td>Espesor:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" style="text-align:right" type="text" name="orp_espesor" id="orp_espesor" size="12" value="<?php echo $rst[orp_espesor] ?>" /> m /cm </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Medidor de Vueltas:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" style="text-align:right" type="text" name="orp_med_vueltas" id="orp_med_vueltas" size="12" value="<?php echo $rst[orp_med_vueltas] ?>" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Paquetes:</td>
                <td><input style="text-align:right" type="text" name="orp_paquetes" id="orp_paquetes" size="12" value="<?php echo $rst[orp_paquetes] ?>" /></td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" class="sbtitle" > Condición de Maquina </td>
            </tr>
            <tr>
                <td>Temperatura: </td><td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"  type="text" name="orp_temperatura" id="orp_temperatura" size="10" value="<?php echo $rst[orp_temperatura] ?>" /> °C </td>
                <td>Agua: <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="orp_agua" id="orp_agua" size="10" value="<?php echo $rst[orp_agua] ?>" />  Lt</td>
                <td>Resina: <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="orp_resina" id="orp_resina" size="10" value="<?php echo $rst[orp_resina] ?>" />  Lt</td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td colspan="4" ><textarea name="orp_observaciones" id="orp_observaciones" style="width:100%"><?php echo $rst[orp_observaciones] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="6"><?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel"  onclick="cancelar()">Cancelar</button>
                </td>
            </tr>
        </table>
</html>