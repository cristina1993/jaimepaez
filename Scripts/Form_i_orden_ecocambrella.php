<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$id = $_GET [id];
$x=$_GET[x];
if (isset($_GET [id])) {
    $rst = pg_fetch_array($Set->lista_una_orden_produccion($id));
} else {
    $cns = $Set->lista_orden_produccion();
    $rst[ord_kg1] = 0;
    $rst[ord_kg2] = 0;
    $rst[ord_kg3] = 0;
    $rst[ord_kg4] = 0;
    $rst[ord_kg5] = 0;
    $rst[ord_kg6] = 0;
    $rst[ord_mf1] = 0;
    $rst[ord_mf2] = 0;
    $rst[ord_mf3] = 0;
    $rst[ord_mf4] = 0;
    $rst[ord_mf5] = 0;
    $rst[ord_mf6] = 0;
    $rst[ord_fec_pedido] = date("Y-m-d");
    $rst[ord_fec_entrega] = date("Y-m-d");
    $rst[ord_pri_ancho] = 0;
    $rst[ord_pri_carril] = 0;
    $rst[ord_pri_faltante] = 0;
    $rst[ord_sec_ancho] = 0;
    $rst[ord_sec_carril] = 0;
    $rst[ord_refilado] = 0;
    $rst[ord_rep_ancho] = 0;
    $rst[ord_rep_carril] = 0;
    $rst[ord_largo] = 0;
    $rst[ord_gramaje] = 0;
    $rst[ord_kgtotal] = '0.0';
    $rst[ord_mftotal] = '0.0';
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden_produccion());
    $cod = substr($rst_sec[ord_num_orden], -5);
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
                Calendar.setup({inputField: "ord_fec_pedido", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "ord_fec_entrega", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                document.getElementById("ord_pro_principal").disabled = true;
                producto(<?php echo $rst[pro_id] ?>);
            });
            function limpiar_datos_detalle()
            {
                ord_num_orden.value = "";
                ord_anc_total.value = "";
                ord_kg1.value = 0;
                ord_kg2.value = 0;
                ord_kg3.value = 0;
                ord_kg4.value = 0;
                ord_kg5.value = 0;
                ord_kg6.value = 0;
                ord_mf1.value = 0;
                ord_mf2.value = 0;
                ord_mf3.value = 0;
                ord_mf4.value = 0;
                ord_mf5.value = 0;
                ord_mf6.value = 0;
                ord_mftotal.value = 0;
                ord_kgtotal.value = 0;
                ord_pri_ancho.value = 0;
                ord_pri_carril.value = 0;
                ord_pri_faltante.value = 0;
                ord_sec_ancho.value = 0;
                ord_sec_carril.value = 0;
                ord_rep_ancho.value = 0;
                ord_rep_carril.value = 0;
                ord_largo.value = 0;
                ord_gramaje.value = 0;
                ord_pro_principal.value = pro_id.value;
            }

            function limpiar()
            {
                pro_id.style.borderColor = "";
                cli_id.style.borderColor = "";
                ord_num_rollos.style.borderColor = "";
                ord_fec_pedido.style.borderColor = "";
                ord_fec_entrega.style.borderColor = "";
                ord_anc_total.style.borderColor = "";
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
                } else if (ord_num_rollos.value.length == 0) {
                    alert('El Número de Rollos es un campo obligatorio.');
                    ord_num_rollos.focus();
                    limpiar();
                    ord_num_rollos.style.borderColor = "red";
                } else if (ord_fec_pedido.value.length == 0) {
                    alert('La Fecha de Pedido es un campo obligatorio.');
                    ord_fec_pedido.focus();
                    limpiar();
                    ord_fec_pedido.style.borderColor = "red";
                } else if (ord_fec_entrega.value.length == 0) {
                    alert('La Fecha de Entrega es un campo obligatorio.');
                    ord_fec_entrega.focus();
                    limpiar();
                    ord_fec_entrega.style.borderColor = "red";
                } else if (ord_anc_total.value.length == 0) {
                    alert('El Ancho Total es un campo obligatorio.');
                    ord_anc_total.focus();
                    limpiar();
                    ord_anc_total.style.borderColor = "red";
                } else if (ord_fec_entrega.value < ord_fec_pedido.value) {
                    alert('El Fecha de Entrega no puede ser antes de la Fecha de Pedido.');
                    ord_fec_entrega.focus();
                    limpiar();
                    ord_fec_entrega.style.borderColor = "red";
                } else if (ord_mftotal.value != 100) {
                    alert('Valor total del porcentaje debe ser 100');
                    ord_mftotal.focus();
                    limpiar();
                    ord_mftotal.style.borderColor = "red";
                } else {


//ord_mftotal
                    var data = Array(
                            ord_num_orden.value,
                            cli_id.value,
                            pro_id.value,
                            ord_num_rollos.value,
                            ord_mp1.value,
                            ord_mp2.value,
                            ord_mp3.value,
                            ord_mp4.value,
                            ord_mf1.value,
                            ord_mf2.value,
                            ord_mf3.value,
                            ord_mf4.value,
                            ord_mftotal.value,
                            ord_kg1.value,
                            ord_kg2.value,
                            ord_kg3.value,
                            ord_kg4.value,
                            ord_kgtotal.value,
                            ord_fec_pedido.value,
                            ord_fec_entrega.value,
                            ord_anc_total.value,
                            ord_refilado.value,
                            ord_pri_ancho.value,
                            ord_pri_carril.value,
                            ord_pri_faltante.value,
                            ord_pro_secundario.value,
                            ord_sec_ancho.value,
                            ord_sec_carril.value,
                            ord_rep_ancho.value,
                            ord_rep_carril.value,
                            ord_largo.value,
                            ord_gramaje.value,
                            ord_zo1.value.toUpperCase(),
                            ord_zo2.value.toUpperCase(),
                            ord_zo3.value.toUpperCase(),
                            ord_zo4.value.toUpperCase(),
                            ord_zo5.value.toUpperCase(),
                            ord_zo6.value.toUpperCase(),
                            ord_spi_temp.value.toUpperCase(),
                            ord_upp_rol_tem_controller.value.toUpperCase(),
                            ord_dow_rol_tem_controller.value.toUpperCase(),
                            ord_spi_tem_controller.value.toUpperCase(),
                            ord_coo_air_temp.value.toUpperCase(),
                            ord_upp_rol_heating.value.toUpperCase(),
                            ord_upp_rol_oil_pump.value.toUpperCase(),
                            ord_dow_rol_heating.value.toUpperCase(),
                            ord_dow_rol_oil_pump.value.toUpperCase(),
                            ord_spi_rol_heating.value.toUpperCase(),
                            ord_spi_rol_oil_pump.value.toUpperCase(),
                            ord_mat_pump.value.toUpperCase(),
                            ord_spi_blower.value.toUpperCase(),
                            ord_sid_blower.value.toUpperCase(),
                            ord_dra_blower.value.toUpperCase(),
                            ord_gsm_setting.value.toUpperCase(),
                            ord_aut_spe_adjust.value.toUpperCase(),
                            ord_spe_mod_auto.value.toUpperCase(),
                            ord_lap_speed.value.toUpperCase(),
                            ord_man_spe_setting.value.toUpperCase(),
                            ord_rol_mill.value.toUpperCase(),
                            ord_win_tensility.value.toUpperCase(),
                            ord_mas_bra_autosetting.value.toUpperCase(),
                            ord_rol_mil_up_down.value.toUpperCase(),
                            ord_observaciones.value.toUpperCase(),
                            ord_mp5.value,
                            ord_mp6.value,
                            ord_mf5.value,
                            ord_mf6.value,
                            ord_kg5.value,
                            ord_kg6.value

                            );

                    $.post("actions.php", {act: 60, 'data[]': data, id: id},
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
            }
            function calculo_porcentage() {
                ord_mftotal.value = (ord_mf1.value * 1 + ord_mf2.value * 1 + ord_mf3.value * 1 + ord_mf4.value * 1 + ord_mf5.value * 1 + ord_mf6.value * 1);
            }
            function calculo_kg() {
                ord_kgtotal.value = (ord_kg1.value * 1 + ord_kg2.value * 1 + ord_kg3.value * 1 + ord_kg4.value * 1 + ord_kg5.value * 1 + ord_kg6.value * 1);
            }
            function calculo() {
                if ((ord_anc_total.value * 1) < (ord_pri_ancho.value * 1)) {
                    alert(' - Tome en cuenta que no puede ingresar un ANCHO menor al ANCHO del producto principal');
                    ord_anc_total.value = "";
                    ord_anc_total.focus();
                    ord_anc_total.style.borderColor = "red";
                    ord_pri_carril.value = 0;
                    ord_pri_faltante.value = 0;
                    ord_pro_secundario.value = 0;
                    ord_sec_ancho.value = 0;
                    ord_sec_carril.value = 0;
                    ord_rep_ancho.value = 0;
                    ord_rep_carril.value = 0;
                    document.getElementById("ord_pro_secundario").disabled = false;
                } else {
                    ord_anc_total.style.borderColor = "";
                    ord_pri_carril.value = (ord_anc_total.value / ord_pri_ancho.value) - ((ord_anc_total.value / ord_pri_ancho.value) - parseInt(ord_anc_total.value / ord_pri_ancho.value));
                    ord_pri_faltante.value = (ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value) * 2).toFixed(1);
                    $.post("actions.php", {act: 52, faltante: ord_pri_faltante.value, gramaje: ord_gramaje.value},
                    function (dt) {
                        if (dt.length == 0) {
                            dt = "<option value='0'> - Ninguno - </option>";
                        }
                        $('#ord_pro_secundario').html(dt);
                        document.getElementById("ord_pro_secundario").disabled = false;
                        z = $('#ord_pro_secundario').val();
                        if (z > 0) {
                            despliegue_ancho_producto_secundario(z);
                        } else if (ord_pri_faltante.value == 0.0 || ord_pri_faltante.value == 0) {
                            ord_sec_ancho.value = 0;
                            ord_sec_carril.value = 0;
                            ord_rep_ancho.value = 0;
                            ord_rep_carril.value = 0;
                        } else {
//                            alert('ord_pri_faltante.value = ' + ord_pri_faltante.value);
                            ord_sec_ancho.value = 0;
                            ord_sec_carril.value = 0;
                            ord_rep_ancho.value = ord_pri_faltante.value;
                            ord_rep_carril.value = 1;
                        }
                    });
                }
            }
            function despliegue_ancho_producto_secundario(id)
            {
                //alert('3 despliegue ' + id);
                $.post("actions.php", {act: 53, id: id},
                function (dt) {
                    // alert('4 = ' + dt);
                    ord_sec_ancho.value = dt;
                    ord_sec_carril.value = (ord_pri_faltante.value / ord_sec_ancho.value) - ((ord_pri_faltante.value / ord_sec_ancho.value) - parseInt(ord_pri_faltante.value / ord_sec_ancho.value));
                    if (ord_pro_secundario.value == 0) {
                        var numero = 0;
                        ord_sec_ancho.value = numero.toFixed(1);
                        ord_sec_carril.value = 0;
                        if (ord_pri_faltante.value > 0) {
                            document.getElementById("ord_pro_secundario").disabled = true;
                            ord_rep_ancho.value = ord_pri_faltante.value;
                            ord_rep_carril.value = 1;
                        } else {
                            if (ord_pri_faltante.value == 0 && ord_pro_secundario.value == 0) {
                                ord_sec_ancho.value = 0;
                                ord_sec_carril.value = 0;
                                ord_rep_ancho.value = 0;
                                ord_rep_carril.value = 0;
                                document.getElementById("ord_pro_secundario").disabled = false; //ojo
                            }
                        }
                    } else {
                        ord_rep_ancho.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_refilado.value * 2) - (ord_sec_ancho.value * ord_sec_carril.value))).toFixed(1);
                        if (ord_rep_ancho.value > 0) {
                            ord_rep_carril.value = ((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(1) / ord_rep_ancho.value) - (((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(1) / ord_rep_ancho.value) - parseInt(((ord_anc_total.value - (ord_pri_ancho.value * ord_pri_carril.value) - (ord_sec_ancho.value * ord_sec_carril.value) - (ord_refilado.value * 2)).toFixed(1) / ord_rep_ancho.value)));
                        } else {
                            ord_rep_carril.value = 0;
                        }
                    }
                });
            }
            function producto(id) {
                if (pro_id.value == 0) {
                    limpiar_datos_detalle();
                } else {
                    pro_id.style.borderColor = "";
                    ord_pro_principal.value = pro_id.value;
                    $.post("actions.php", {act: 51, id: id},
                    function (dt) {
                        dat = dt.split('&');
                        var a = '<?php echo $id ?>';
                        if (a.length == 0) {
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6').html(dat[47]);
                            ord_pri_ancho.value = dat[0];
                            ord_mp1.value = dat[1];
                            ord_mp2.value = dat[2];
                            ord_mp3.value = dat[3];
                            ord_mp4.value = dat[4];
                            ord_mp5.value = dat[5];
                            ord_mp6.value = dat[6];
                            ord_mf1.value = dat[7];
                            ord_mf2.value = dat[8];
                            ord_mf3.value = dat[9];
                            ord_mf4.value = dat[10];
                            ord_mf5.value = dat[11];
                            ord_mf6.value = dat[12];
                            ord_mftotal.value = dat[13];
                            ord_largo.value = dat[14];
                            ord_gramaje.value = dat[15];
                            ord_num_orden.value = 'P-' + dat[16] + '-' + '<?php echo $no_orden ?>';
                            $('#ord_zo1').val(dat[17]);
                            $('#ord_zo2').val(dat[18]);
                            $('#ord_zo3').val(dat[19]);
                            $('#ord_zo4').val(dat[20]);
                            $('#ord_zo5').val(dat[21]);
                            $('#ord_zo6').val(dat[22]);
                            $('#ord_spi_temp').val(dat[19]);
                            $('#ord_upp_rol_tem_controller').val(dat[23]);
                            $('#ord_dow_rol_tem_controller').val(dat[24]);
                            $('#ord_spi_tem_controller').val(dat[25]);
                            $('#ord_coo_air_temp').val(dat[26]);
                            $('#ord_upp_rol_heating').val(dat[27]);
                            $('#ord_upp_rol_oil_pump').val(dat[28]);
                            $('#ord_dow_rol_heating').val(dat[29]);
                            $('#ord_dow_rol_oil_pump').val(dat[30]);
                            $('#ord_spi_rol_heating').val(dat[31]);
                            $('#ord_spi_rol_oil_pump').val(dat[32]);
                            $('#ord_mat_pump').val(dat[33]);
                            $('#ord_spi_blower').val(dat[34]);
                            $('#ord_sid_blower').val(dat[35]);
                            $('#ord_dra_blower').val(dat[36]);
                            $('#ord_gsm_setting').val(dat[37]);
                            $('#ord_aut_spe_adjust').val(dat[38]);
                            $('#ord_spe_mod_auto').val(dat[39]);
                            $('#ord_lap_speed').val(dat[40]);
                            $('#ord_man_spe_setting').val(dat[41]);
                            $('#ord_rol_mill').val(dat[42]);
                            $('#ord_win_tensility').val(dat[43]);
                            $('#ord_mas_bra_autosetting').val(dat[43]);
                            $('#ord_rol_mil_up_down').val(dat[44]);
                        } else {
                            ord_pri_ancho.value = dat[0];
                            $('#ord_mp1,#ord_mp2,#ord_mp3,#ord_mp4,#ord_mp5,#ord_mp6').html(dat[47]);
                            $('#ord_mp1').val(<?php echo $rst[ord_mp1] ?>);
                            $('#ord_mp2').val(<?php echo $rst[ord_mp2] ?>);
                            $('#ord_mp3').val(<?php echo $rst[ord_mp3] ?>);
                            $('#ord_mp4').val(<?php echo $rst[ord_mp4] ?>);
                            $('#ord_mp5').val(<?php echo $rst[ord_mp5] ?>);
                            $('#ord_mp6').val(<?php echo $rst[ord_mp6] ?>);
                        }
                    }
                    );
                }
            }
        </script>
    </head>
    <body>                                          
        <table id="tbl_form" border="1" >
            <thead>
                <tr>
                    <th colspan="3" >
                        Orden de Producción Ecocambrella
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tr>  
                <td colspan="2"  class="sbtitle" >DATOS GENERALES</td>
                <td class="sbtitle" >MIX DE FIBRAS</td>
            </tr>
            <tr>
                <td>Orden # :</td>
                <td><input readonly type="text" name="ord_num_orden" id="ord_num_orden" size="20" value="<?php echo $rst[ord_num_orden] ?>" /></td>                     
                <td rowspan="6">
                    <select name="ord_mp1" id="ord_mp1"style="width:180px">
                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf1" id="ord_mf1" size="10" style="text-align:right" value="<?php echo $rst[ord_mf1] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg1" id="ord_kg1" size="10" style="text-align:right" value="<?php echo $rst[ord_kg1] ?>" /> kg<br />
                    <select name="ord_mp2" id="ord_mp2" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf2" id="ord_mf2" size="10" style="text-align:right" value="<?php echo $rst[ord_mf2] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg2" id="ord_kg2" size="10" style="text-align:right" value="<?php echo $rst[ord_kg2] ?>" /> kg<br />
                    <select name="ord_mp3" id="ord_mp3" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf3" id="ord_mf3" size="10" style="text-align:right" value="<?php echo $rst[ord_mf3] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg3" id="ord_kg3" size="10" style="text-align:right" value="<?php echo $rst[ord_kg3] ?>" /> kg<br />
                    <select name="ord_mp4" id="ord_mp4" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf4" id="ord_mf4" size="10" style="text-align:right" value="<?php echo $rst[ord_mf4] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg4" id="ord_kg4" size="10" style="text-align:right" value="<?php echo $rst[ord_kg4] ?>" />kg<br />               
                    <select name="ord_mp5" id="ord_mp5" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf5" id="ord_mf5" size="10" style="text-align:right" value="<?php echo $rst[ord_mf5] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg5" id="ord_kg5" size="10" style="text-align:right" value="<?php echo $rst[ord_kg5] ?>" />kg<br />               
                    <select name="ord_mp6" id="ord_mp6" style="width:180px">

                    </select>
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_porcentage()" type="text" name="ord_mf6" id="ord_mf6" size="10" style="text-align:right" value="<?php echo $rst[ord_mf6] ?>" /> %
                    <input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_kg()" type="text" name="ord_kg6" id="ord_kg6" size="10" style="text-align:right" value="<?php echo $rst[ord_kg6] ?>" />kg<br />

            </tr>
            <script>
                document.getElementById("ord_mp1").value = '<?php echo $rst[ord_mp1] ?>';
                document.getElementById("ord_mp2").value = '<?php echo $rst[ord_mp2] ?>';
                document.getElementById("ord_mp3").value = '<?php echo $rst[ord_mp3] ?>';
                document.getElementById("ord_mp4").value = '<?php echo $rst[ord_mp4] ?>';
                document.getElementById("ord_mp5").value = '<?php echo $rst[ord_mp5] ?>';
                document.getElementById("ord_mp6").value = '<?php echo $rst[ord_mp6] ?>';
            </script>
            <tr>
                <td>Cliente :</td>
                <td><select name="cli_id" id="cli_id" style="width:200px; ">
                        <option value="0"> - Elija un Cliente - </option>
                        <?php
                        $cns_cli = $Set->lista_clientes_tipo('1');
                        while ($rst_cli = pg_fetch_array($cns_cli)) {
                            echo "<option $sel value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
                        }
                        ?>
                    </select></td>
            </tr>
            <tr>
                <td>Producto :</td>

                <td><select name="pro_id" id="pro_id" style="width:200px; " onchange="producto(pro_id.value)" >
                        <option value="0"> - Elija un Producto - </option>
                        <?php
                        $cns_pro = $Set->lista_producto();
                        while ($rst_pro = pg_fetch_array($cns_pro)) {
                            echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                        }
                        ?>
                    </select></td>
            </tr>
            <script>
                document.getElementById("cli_id").value = '<?php echo $rst[cli_id] ?>';
                document.getElementById("pro_id").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td># de Rollos:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="calculo_peso()" type="text" name="ord_num_rollos" id="ord_num_rollos" style="text-align:right" size="10" value="<?php echo $rst[ord_num_rollos] ?>" /></td>            
            </tr>
            <tr>
                <td>Peso Total a Producir:</td>
                <td><input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_kgtotal" id="ord_kgtotal" size="10" value="<?php echo $rst[ord_kgtotal] ?>" /> kg </td>

            </tr>
            <tr>
                <td>Fecha Pedido:</td>
                <td><input type="text" name="ord_fec_pedido" id="ord_fec_pedido" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_pedido] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-desde" /></td>
            </tr>
            <tr>
                <td>Fecha Entrega:</td>
                <td><input type="text" name="ord_fec_entrega" id="ord_fec_entrega" size="9" style="text-align:right" value="<?php echo $rst[ord_fec_entrega] ?>"/>
                    <img src="../img/calendar.png" width="16"  id="im-hasta" /></td>
                <td>Total 100%: <input  readonly type="text" size="9" name="ord_mftotal" id="ord_mftotal" style="text-align:right" value="<?php echo $rst[ord_mftotal] ?>"/> %</td>                         
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="sbtitle" > Detalle de Producto </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td>Ancho Total :
                    <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_anc_total" id="ord_anc_total" size="10" value="<?php echo $rst[ord_anc_total] ?>"onchange="calculo()"/> m </td>
                <td>Refilado :
                    <input style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_refilado" id="ord_refilado" size="10" value="<?php echo $rst[ord_refilado] ?>"onchange="calculo()"/> m </td>

            </tr> 
            <td>Producto Principal :</td>
            <td><select name="ord_pro_principal" id="ord_pro_principal"  >
                    <option value="0"> - Elija un Producto - </option>
                    <?php
                    $cns_pro = $Set->lista_producto();
                    while ($rst_pro = pg_fetch_array($cns_pro)) {
                        echo "<option $sel value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
                    }
                    ?>
                </select></td>
            <td rowspan="4"> 
                Ancho :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_ancho" id="ord_pri_ancho" size="6" value="<?php echo $rst[ord_pri_ancho] ?>"/> m
                Carriles :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_carril" id="ord_pri_carril" size="6" value="<?php echo $rst[ord_pri_carril] ?>" />
                Faltante :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_pri_faltante" id="ord_pri_faltante" size="6" value="<?php echo $rst[ord_pri_faltante] ?>"/> m<br />
                Ancho :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_sec_ancho" id="ord_sec_ancho" size="6" value="<?php echo $rst[ord_sec_ancho] ?>" /> m
                Carriles :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_sec_carril" id="ord_sec_carril" size="6" value="<?php echo $rst[ord_sec_carril] ?>" /><br />      
                Ancho :
                <input  readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_ancho" id="ord_rep_ancho" size="6" value="<?php echo $rst[ord_rep_ancho] ?>" /> m 
                Carriles :
                <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_rep_carril" id="ord_rep_carril" size="6" value="<?php echo $rst[ord_rep_carril] ?>" /></td>
            <tr>
                <td>Producto Secundario :</td>
                <td>
                    <select name="ord_pro_secundario" id="ord_pro_secundario" style="width:182px" onchange="despliegue_ancho_producto_secundario(ord_pro_secundario.value)" ></select>
                </td>
            </tr>
            <script>
                if (<?php echo $rst[ord_pro_secundario] ?> == 0) {
                    ord_pro_secundario = "<option value='0'> - Ninguno - </option>";
                    $('#ord_pro_secundario').html(ord_pro_secundario);
                    document.getElementById("ord_pro_secundario").disabled = false;
                } else {
                    $.post("actions.php", {act: 52, faltante: <?php echo $rst[ord_pri_faltante] ?>, gramaje: <?php echo $rst[ord_gramaje] ?>},
                    function (dt) {
                        if (dt.length == 0) {
                            dt = "<option value='0'> - Ninguno - </option>";
                        }
                        $('#ord_pro_secundario').html(dt);
                        $('#ord_pro_secundario').val('<?php echo $rst[ord_pro_secundario] ?>');
                    });
                }
                document.getElementById("ord_pro_principal").value = '<?php echo $rst[pro_id] ?>';</script>
            <tr>
                <td>Reproceso :</td>
                <td></td> 
            </tr>
            <tr> </tr>
            <tr> </tr>
            <tr>
                <td>Largo:
                    <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_largo" id="ord_largo" size="10" value="<?php echo $rst[ord_largo] ?>" /> m </td>
                <td>Gramaje :
                    <input readonly style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="ord_gramaje" id="ord_gramaje" size="8" value="<?php echo $rst[ord_gramaje] ?>" /> gr/m2</td>   
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" class="sbtitle" > Set Maquinas </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" align="center">Temperatura</td>
            </tr>
            <tr>
                <td colspan="3" id="ttl_zone" >
                    <table style="width:100% ">
                        <tr>
                            <td align="center" >Zona 1:</td>
                            <td align="center">Zona 2:</td>
                            <td align="center">Zona 3:</td>
                            <td align="center">Zona 4:</td>
                            <td align="center">Zona 5:</td>
                            <td align="center">Zona 6:</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="ord_zo1" id="ord_zo1" size="10"  value="<?php echo $rst[ord_zo1] ?>" /></td>
                            <td><input type="text" name="ord_zo2" id="ord_zo2"  size="10" value="<?php echo $rst[ord_zo2] ?>" /></td>
                            <td><input type="text" name="ord_zo3" id="ord_zo3"  size="10" value="<?php echo $rst[ord_zo3] ?>" /></td>
                            <td><input type="text" name="ord_zo4" id="ord_zo4"  size="10" value="<?php echo $rst[ord_zo4] ?>" /></td>
                            <td><input type="text" name="ord_zo5" id="ord_zo5"  size="10" value="<?php echo $rst[ord_zo5] ?>" /></td>
                            <td><input type="text" name="ord_zo6" id="ord_zo6"  size="10" value="<?php echo $rst[ord_zo6] ?>" /></td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td align="center" style="height:30px " colspan="3">Condiciones de Tabla</td>
            </tr>

            <tr>
                <td>Spinneter Temp : </td>
                <td><input type="text" name="ord_spi_temp" id="ord_spi_temp" size=20" value="<?php echo $rst[ord_spi_temp] ?>" /></td>
                <td>Upper Roller Heating On/ Off: <input style="float:right " type="text" name="ord_upp_rol_heating" id="ord_upp_rol_heating" size="20" value="<?php echo $rst[ord_upp_rol_heating] ?>" /></td>         
            </tr>
            <tr>
                <td>Upper Roller Temp Controller: </td>
                <td><input type="text" name="ord_upp_rol_tem_controller" id="ord_upp_rol_tem_controller" size="20" value="<?php echo $rst[ord_upp_rol_tem_controller] ?>" /></td>
                <td>Upper Roller Oil Pump:<input style="float:right " type="text" name="ord_upp_rol_oil_pump" id="ord_upp_rol_oil_pump" size="20" value="<?php echo $rst[ord_upp_rol_oil_pump] ?>" /></td>
            </tr>
            <tr>
                <td>Down Roller Temp Controller: </td>
                <td><input type="text" name="ord_dow_rol_tem_controller" id="ord_dow_rol_tem_controller" size="20" value="<?php echo $rst[ord_dow_rol_tem_controller] ?>" /></td>
                <td>Down Roller Heating On/ Off:<input style="float:right " type="text" name="ord_dow_rol_heating" id="ord_dow_rol_heating" size="20" value="<?php echo $rst[ord_dow_rol_heating] ?>" /></td>
            </tr>
            <tr>
                <td>Spinneter Temp Controller: </td>
                <td><input type="text" name="ord_spi_tem_controller" id="ord_spi_tem_controller" size="20" value="<?php echo $rst[ord_spi_tem_controller] ?>" /></td>
                <td>Down Roller Oil Pump :<input style="float:right " type="text" name="ord_dow_rol_oil_pump" id="ord_dow_rol_oil_pump" size="20" value="<?php echo $rst[ord_dow_rol_oil_pump] ?>" /></td>
            </tr>
            <tr>
                <td>Cool Air Temp: </td>
                <td><input type="text" name="ord_coo_air_temp" id="ord_coo_air_temp" size="20" value="<?php echo $rst[ord_coo_air_temp] ?>" /></td>
                <td>Spinneter Roller Heating On/ Off: <input style="float:right " type="text" name="ord_spi_rol_heating" id="ord_spi_rol_heating" size="20" value="<?php echo $rst[ord_spi_rol_heating] ?>" /></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Spinneter Roller Oil Pump: <input style="float:right " type="text" name="ord_spi_rol_oil_pump" id="ord_spi_rol_oil_pump" size="20" value="<?php echo $rst[ord_spi_rol_oil_pump] ?>" /></td>
            </tr>
            <!--///gggg-->
            <tr>
                <td>Matering Pump : </td>
                <td><input type="text" name="ord_mat_pump" id="ord_mat_pump" size=20" value="<?php echo $rst[ord_mat_pump] ?>" /></td>
            </tr>
            <tr>
                <td>Spinneter Spinneter Temp : </td>
                <td><input type="text" name="ord_spi_blower" id="ord_spi_blower" size="20" value="<?php echo $rst[ord_spi_blower] ?>" /></td>
                <td>GSM Setting :<input style="float:right " type="text" name="ord_gsm_setting" id="ord_gsm_setting" size="20" value="<?php echo $rst[ord_gsm_setting] ?>" /></td>
            </tr>
            <tr>
                <td>Side Blower : </td>
                <td><input type="text" name="ord_sid_blower" id="ord_sid_blower" size="20" value="<?php echo $rst[ord_sid_blower] ?>" /></td>
                <td>Auto Speed Adjust : <input style="float:right " type="text" name="ord_aut_spe_adjust" id="ord_aut_spe_adjust" size="20" value="<?php echo $rst[ord_aut_spe_adjust] ?>" /></td>
            </tr>
            <tr>
                <td>Draffting Blower : </td>
                <td><input type="text" name="ord_dra_blower" id="ord_dra_blower" size="20" value="<?php echo $rst[ord_dra_blower] ?>" /></td>
                <td>Speed Mode Auto : <input style="float:right " type="text" name="ord_spe_mod_auto" id="ord_spe_mod_auto" size="20" value="<?php echo $rst[ord_spe_mod_auto] ?>" /></td>
            </tr>
            <!--fsdfdsf-->
            <tr>
                <td>Lapper Speed : </td>
                <td><input type="text" name="ord_lap_speed" id="ord_lap_speed" size=20" value="<?php echo $rst[ord_lap_speed] ?>" /></td>
            </tr>
            <tr>
                <td>Manual Speed Setting : </td>
                <td><input type="text" name="ord_man_spe_setting" id="ord_man_spe_setting" size="20" value="<?php echo $rst[ord_man_spe_setting] ?>" /></td>
            </tr>
            <tr>
                <td>Rolling Mill  : </td>
                <td><input type="text" name="ord_rol_mill" id="ord_rol_mill" size="20" value="<?php echo $rst[ord_rol_mill] ?>" /></td>
            </tr>
            <tr>
                <td>Winding  Tensility : </td>
                <td><input type="text" name="ord_win_tensility" id="ord_win_tensility" size="20" value="<?php echo $rst[ord_win_tensility] ?>" /></td>
            </tr>
            <tr>
                <td>MasterBranch Autosetting : </td>
                <td><input type="text" name="ord_mas_bra_autosetting" id="ord_mas_bra_autosetting" size="20" value="<?php echo $rst[ord_mas_bra_autosetting] ?>" /></td>
            </tr>
            <tr>
                <td>Rolling Mill Up/Down : </td>
                <td><input type="text" name="ord_rol_mil_up_down" id="ord_rol_mil_up_down" size="20" value="<?php echo $rst[ord_rol_mil_up_down] ?>" /></td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td colspan="2" ><textarea name="ord_observaciones" id="ord_observaciones" style="width:100%"><?php echo $rst[ord_observaciones] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><?php
                    if ( ($Prt->add == 0 || $Prt->edition == 0) && $x!=1 ) {
                        ?>
                    <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    
                    <button id="cancel"  onclick="cancelar()">Cancelar</button></td>
            </tr>
            <td colspan="6">&nbsp;</td>
        </table>
</html>