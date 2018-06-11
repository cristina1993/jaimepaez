<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_geotextil.php';
$Clase = new Clase();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase->lista_uno($id));
    $cns_combomp1 = $Clase->lista_combomp($rst[pro_id]);
    $cns_combomp2 = $Clase->lista_combomp($rst[pro_id]);
    $cns_combomp3 = $Clase->lista_combomp($rst[pro_id]);
    $cns_combomp4 = $Clase->lista_combomp($rst[pro_id]);
} else {
    $id = 0;
    $rst['opg_num_rollos'] = 0;
    $rst['opg_peso_producir'] = 0;
    $rst['opg_fec_pedido'] = date('Y-m-d');
    $rst['opg_fec_entrega'] = date('Y-m-d');
    $rst['pro_mf1'] = 0;
    $rst['pro_mf2'] = 0;
    $rst['pro_mf3'] = 0;
    $rst['pro_mf4'] = 0;
    $rst['suma'] = 0;
    $rst['opg_kg1'] = 0;
    $rst['opg_kg2'] = 0;
    $rst['opg_kg3'] = 0;
    $rst['opg_kg4'] = 0;
    $rst['opg_ancho_total'] = 0;
    $rst['pro_largo'] = 0;
    $rst['pro_ancho'] = 0;
    $rst['pro_gramaje'] = 0;
    $rst['opg_refilado'] = 0;
    $rst['opg_caja1'] = 0;
    $rst['opg_caja2'] = 0;
    $rst['opg_caja3'] = 0;
    $rst['opg_vel_transporte'] = 0;
    $rst['opg_frecuencia'] = 0;
    $rst['opg_capas'] = 0;
    $rst['opg_doffer'] = 0;
    $rst['opg_front'] = 0;
    $rst['opg_random'] = 0;
    $rst['opg_conveyor'] = 0;
    $rst['opg_compensacion'] = 0;
    $rst['opg_sensor1'] = 0;
    $rst['opg_sensor2'] = 0;
    $rst['opg_sensor3'] = 0;
    $rst['opg_sensor4'] = 0;
    $rst['opg_sensor5'] = 0;
    $rst['opg_sensor6'] = 0;
    $rst['opg_dosi_alimentacion'] = 0;
    $rst['opg_mot_alimentacion'] = 0;
    $rst['opg_mot_carda2'] = 0;
    $rst['opg_mot_cilindro'] = 0;
    $rst['opg_mot_gramaje'] = 0;
    $rst['opg_hz'] = 0;
    $rst['opg_vel_trans_madera'] = 0;
    $rst['opg_vel_trans_caucho'] = 0;
    $rst['opg_num_punzonadora'] = 0;
    $rst['opg_vel_rod_salida'] = 0;
    $rst['opg_vel_rod_compensadores'] = 0;
    $rst['opg_vel_rod_entradawinder'] = 0;
    $rst['opg_numpunzo_winder'] = 0;
    $rst['opg_velrod_salidawinder'] = 0;
    $rst['opg_numgolpes_punzo'] = 0;
    $rst['opg_vel_enrolladora'] = 0;
    $rst['opg_rev_min_calan'] = 0;
}

$cns_combo = $Clase->lista_combo();
$cns_combo1 = $Clase->lista_combocli(1);
$cns_combo2 = $Clase->lista_combopro();
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
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                $('#pro_mf1,#pro_mf2,#pro_mf3,#pro_mf4').change(function () {
                    suma();
                });
                $('#pro_largo,#pro_ancho,#pro_gramaje').change(function () {
                    calcular();
                });
            });
            if (id == 0) {
                load_fabrica(3);
            }
            function save(id) {

                var data = Array(
                        opg_codigo.value,
                        cli_id.value,
                        pro_id.value,
                        opg_num_rollos.value,
                        opg_peso_producir.value,
                        opg_fec_pedido.value,
                        opg_fec_entrega.value,
                        pro_mp1.value,
                        pro_mp2.value,
                        pro_mp3.value,
                        pro_mp4.value,
                        pro_mf1.value,
                        pro_mf2.value,
                        pro_mf3.value,
                        pro_mf4.value,
                        opg_kg1.value,
                        opg_kg2.value,
                        opg_kg3.value,
                        opg_kg4.value,
                        opg_ancho_total.value,
                        opg_prod_principal.value,
                        pro_largo.value,
                        pro_ancho.value,
                        pro_gramaje.value,
                        opg_refilado.value,
                        opg_caja1.value,
                        opg_caja2.value,
                        opg_caja3.value,
                        opg_vel_transporte.value,
                        opg_frecuencia.value,
                        opg_capas.value,
                        opg_doffer.value,
                        opg_front.value,
                        opg_random.value,
                        opg_conveyor.value,
                        opg_compensacion.value,
                        opg_sensor1.value,
                        opg_sensor2.value,
                        opg_sensor3.value,
                        opg_sensor4.value,
                        opg_sensor5.value,
                        opg_sensor6.value,
                        opg_dosi_alimentacion.value,
                        opg_mot_alimentacion.value,
                        opg_mot_carda2.value,
                        opg_mot_cilindro.value,
                        opg_mot_gramaje.value,
                        opg_hz.value,
                        opg_vel_trans_madera.value,
                        opg_vel_trans_caucho.value,
                        opg_num_punzonadora.value,
                        opg_vel_rod_salida.value,
                        opg_vel_rod_compensadores.value,
                        opg_vel_rod_entradawinder.value,
                        opg_numpunzo_winder.value,
                        opg_velrod_salidawinder.value,
                        opg_numgolpes_punzo.value,
                        opg_vel_enrolladora.value,
                        opg_rev_min_calan.value,
                        opg_observaciones.value);
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
//                        Validaciones antes de enviar
                        if (opg_codigo.value.length == 0) {
                            $("#opg_codigo").css({borderColor: "red"});
                            $("#opg_codigo").focus();
                            return false;
                        }
                        else if (cli_id.value == 0) {
                            $("#cli_id").css({borderColor: "red"});
                            $("#cli_id").focus();
                            return false;
                        }
                        else if (pro_id.value == 0) {
                            $("#pro_id").css({borderColor: "red"});
                            $("#pro_id").focus();
                            return false;
                        }
                        else if (opg_num_rollos.value.length == 0) {
                            $("#opg_num_rollos").css({borderColor: "red"});
                            $("#opg_num_rollos").focus();
                            return false;
                        }
                        else if (opg_peso_producir.value.length == 0) {
                            $("#opg_peso_producir").css({borderColor: "red"});
                            $("#opg_peso_producir").focus();
                            return false;
                        }
                        else if (opg_fec_pedido.value.length == 0) {
                            $("#opg_fec_pedido").css({borderColor: "red"});
                            $("#opg_fec_pedido").focus();
                            return false;
                        }
                        else if (opg_fec_entrega.value.length == 0) {
                            $("#opg_fec_entrega").css({borderColor: "red"});
                            $("#opg_fec_entrega").focus();
                            return false;
                        }
                        else if (pro_mf1.value.length == 0) {
                            $("#pro_mf1").css({borderColor: "red"});
                            $("#pro_mf1").focus();
                            return false;
                        }
                        else if (pro_mf2.value.length == 0) {
                            $("#pro_mf2").css({borderColor: "red"});
                            $("#pro_mf2").focus();
                            return false;
                        }
                        else if (pro_mf3.value.length == 0) {
                            $("#pro_mf3").css({borderColor: "red"});
                            $("#pro_mf3").focus();
                            return false;
                        }
                        else if (pro_mf4.value.length == 0) {
                            $("#pro_mf4").css({borderColor: "red"});
                            $("#pro_mf4").focus();
                            return false;
                        }
                        else if ($("#suma").val() != 100) {
                            $("#suma").css({borderColor: "red"});
                            $("#suma").focus();
                            return false;
                        }
                        else if (opg_kg1.value.length == 0) {
                            $("#opg_kg1").css({borderColor: "red"});
                            $("#opg_kg1").focus();
                            return false;
                        }
                        else if (opg_kg2.value.length == 0) {
                            $("#opg_kg2").css({borderColor: "red"});
                            $("#opg_kg2").focus();
                            return false;
                        }
                        else if (opg_kg3.value.length == 0) {
                            $("#opg_kg3").css({borderColor: "red"});
                            $("#opg_kg3").focus();
                            return false;
                        }
                        else if (opg_kg4.value.length == 0) {
                            $("#opg_kg4").css({borderColor: "red"});
                            $("#opg_kg4").focus();
                            return false;
                        }
                        else if (opg_ancho_total.value.length == 0) {
                            $("#opg_ancho_total").css({borderColor: "red"});
                            $("#opg_ancho_total").focus();
                            return false;
                        }
                        else if (opg_prod_principal.value.length == 0) {
                            $("#opg_prod_principal").css({borderColor: "red"});
                            $("#opg_prod_principal").focus();
                            return false;
                        }
                        else if (pro_ancho.value.length == 0) {
                            $("#pro_ancho").css({borderColor: "red"});
                            $("#pro_ancho").focus();
                        }
                    },
                    type: 'POST',
                    url: 'actions_ordenes_geotextil.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
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
            }
            function suma() {
                var sm = parseFloat($('#pro_mf1').val() * 1) + 0;
                sm = sm.toFixed(2);
                $('#suma').val(sm);
                var su = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1);
                su = su.toFixed(2);
                $('#suma').val(su);
                var sum = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1);
                sum = sum.toFixed(2);
                $('#suma').val(sum);
                var s = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1);
                s = s.toFixed(2);
                $('#suma').val(s);
                if (sm == 100) {
                    $('#pro_mp2').hide();
                    $('#pro_mf2').hide();
                    $('#opg_kg2').hide();
                    $('#lblporcentaje2').hide();
                    $('#lblkg2').hide();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#opg_kg3').hide();
                    $('#lblporcentaje3').hide();
                    $('#lblkg3').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opg_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp2').val('0');
                    $('#pro_mf2').val('0');
                    $('#opg_kg2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#opg_kg3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opg_kg4').val('0');
                    $("#suma").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#opg_kg2').show();
                    $('#lblporcentaje2').show();
                    $('#lblkg2').show();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#opg_kg3').hide();
                    $('#lblporcentaje3').hide();
                    $('#lblkg3').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opg_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#opp_kg3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opp_kg4').val('0');
                    $("#suma").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#porcentaje3').show();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#opg_kg4').hide();
                    $('#lblporcentaje4').hide();
                    $('#lblkg4').hide();
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#opg_kg4').val('0');
                    $("#suma").val(sum);
                }
                else if (su < 100 && sum < 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#opg_kg2').show();
                    $('#lblporcentaje2').show();
                    $('#lblkg2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#opg_kg3').show();
                    $('#lblporcentaje3').show();
                    $('#lblkg3').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#opg_kg4').show();
                    $('#lblporcentaje4').show();
                    $('#lblkg4').show();
                }
            }
            function calcular() {
                var c = (parseFloat($('#pro_largo').val() * 1) * parseFloat($('#pro_ancho').val() * 1) * parseFloat($('#pro_gramaje').val() * 1)) / 1000;
                c = c.toFixed(2);
                $('#pro_peso').val(c);
            }
            function load_fabrica(obj) {
                $.post('actions_ordenes_geotextil.php', {id: obj, op: 2}, function (dt) {
                    $('#opg_codigo').val(dt);
                })
            }
            function load_datos(obj) {
                $.post('actions_ordenes_geotextil.php', {id: obj.value, op: 3}, function (dt) {
                    dat = dt.split('&');
                    $('#pro_ancho').val(dat[0]);
                    $('#pro_largo').val(dat[1]);
                    $('#pro_peso').val(dat[2]);
                    $('#pro_gramaje').val(dat[3]);
                    $('#pro_mp1,#pro_mp2,#pro_mp3,#pro_mp4').html(dat[48]);
                    $('#pro_mf1').val(dat[4]);
                    $('#pro_mf2').val(dat[5]);
                    $('#pro_mf3').val(dat[6]);
                    $('#pro_mf4').val(dat[7]);
                    $('#pro_mp1').val(dat[8]);
                    $('#pro_mp2').val(dat[9]);
                    $('#pro_mp3').val(dat[10]);
                    $('#pro_mp4').val(dat[11]);
                    $('#opg_prod_principal').val(dat[12]);
                    $('#opg_caja1').val(dat[13]);
                    $('#opg_caja2').val(dat[14]);
                    $('#opg_caja3').val(dat[15]);
                    $('#opg_vel_transporte').val(dat[16]);
                    $('#opg_frecuencia').val(dat[17]);
                    $('#opg_capas').val(dat[18]);
                    $('#opg_doffer').val(dat[19]);
                    $('#opg_front').val(dat[20]);
                    $('#opg_random').val(dat[21]);
                    $('#opg_conveyor').val(dat[22]);
                    $('#opg_compensacion').val(dat[23]);
                    $('#opg_sensor1').val(dat[24]);
                    $('#opg_sensor2').val(dat[25]);
                    $('#opg_sensor3').val(dat[26]);
                    $('#opg_sensor4').val(dat[27]);
                    $('#opg_sensor5').val(dat[28]);
                    $('#opg_sensor6').val(dat[29]);
                    $('#opg_dosi_alimentacion').val(dat[30]);
                    $('#opg_mot_alimentacion').val(dat[31]);
                    $('#opg_mot_carda2').val(dat[32]);
                    $('#opg_mot_cilindro').val(dat[33]);
                    $('#opg_mot_gramaje').val(dat[34]);
                    $('#opg_hz').val(dat[35]);
                    $('#opg_vel_trans_madera').val(dat[36]);
                    $('#opg_vel_trans_caucho').val(dat[37]);
                    $('#opg_num_punzonadora').val(dat[38]);
                    $('#opg_vel_rod_salida').val(dat[39]);
                    $('#opg_vel_rod_compensadores').val(dat[40]);
                    $('#opg_vel_rod_entradawinder').val(dat[41]);
                    $('#opg_numpunzo_winder').val(dat[42]);
                    $('#opg_velrod_salidawinder').val(dat[43]);
                    $('#opg_numgolpes_punzo').val(dat[44]);
                    $('#opg_vel_enrolladora').val(dat[45]);
                    $('#opg_rev_min_calan').val(dat[46]);
                    $('#opg_observaciones').val(dat[47]);
                    suma();
                })
            }
        </script>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save">
            <style>
                tbody{
                    float:left;
                }
                select{
                    margin:3px; 
                }
                input[type=text]{
                    text-transform: uppercase;
                }
            </style>
            <table id="tbl_form" border='1'>
                <thead >
                    <tr><th colspan="7" >FORMULARIO DE CONTROL </th></tr>
                </thead>
                <tbody>  
                    <tr>
                <td colspan="2" class="sbtitle" >DETALLE DE PRODUCTO</td>
                </tr>
                <tr>
                    <td>ORDEN #:</td>
                    <td><input type="text" size="13"  id="opg_codigo" readonly value="<?php echo $rst['opg_codigo'] ?>"  /></td> 
                </tr>
                <tr>
                    <td>CLIENTE:</td>
                    <td>
                        <select id="cli_id" style="width:200px; ">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combo = pg_fetch_array($cns_combo1)) {
                                echo "<option value='$rst_combo[cli_id]' >$rst_combo[nombres]</option>";
                            }
                            ?>  
                        </select>
                    </td>    
                </tr>
                <tr>
                    <td>PRODUCTO:</td>
                    <td>
                        <select id="pro_id" onchange="load_datos(this);
                                del(this)" >
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_combo = pg_fetch_array($cns_combo2)) {
                                echo "<option value='$rst_combo[pro_id]' >$rst_combo[pro_descripcion]</option>";
                            }
                            ?>  
                        </select>
                    </td>
                </tr> 
                <tr>
                    <td># DE ROLLOS:</td>
                    <td><input type="text" size="15"  id="opg_num_rollos"  value="<?php echo $rst['opg_num_rollos'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>PESO TOTAL A PRODUCIR:</td>
                    <td><input type="text" size="15"  id="opg_peso_producir"  value="<?php echo $rst['opg_peso_producir'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>FECHA PEDIDO:</td>
                    <td><input type="text" size="15"  id="opg_fec_pedido"  value="<?php echo $rst['opg_fec_pedido'] ?>"/></td>
                </tr>
                <tr>
                    <td>FECHA ENTREGA:</td>
                    <td><input type="text" size="15"  id="opg_fec_entrega"  value="<?php echo $rst['opg_fec_entrega'] ?>"  /></td>
                </tr>
                </tbody>
                <tbody style="float:right ">
                    <tr>
                        <td colspan="7" class="sbtitle" >MIX DE FIBRAS</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp1">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp1)) {
                                    echo "<option value='$rst_combomp[mpt_id]' >$rst_combomp[mpt_nombre]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf1"  value="<?php echo $rst['pro_mf1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                        <td><input type="text" size="12"  id="opg_kg1"  value="<?php echo $rst['opg_kg1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Kg</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp2">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp2)) {
                                    echo "<option value='$rst_combomp[mpt_id]' >$rst_combomp[mpt_nombre]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf2"  value="<?php echo $rst['pro_mf2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male"id="lblporcentaje2">%</label></td>
                        <td><input type="text" size="12"  id="opg_kg2"  value="<?php echo $rst['opg_kg2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg2">Kg</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp3">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp3)) {
                                    echo "<option value='$rst_combomp[mpt_id]' >$rst_combomp[mpt_nombre]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf3"  value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male"id="lblporcentaje3">%</label></td>
                        <td><input type="text" size="12"  id="opg_kg3"  value="<?php echo $rst['opg_kg3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg3">Kg</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp4">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp4)) {
                                    echo "<option value='$rst_combomp[mpt_id]' >$rst_combomp[mpt_nombre]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf4"  value="<?php echo $rst['pro_mf4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male"id="lblporcentaje4">%</label></td>
                        <td><input type="text" size="12"  id="opg_kg4"  value="<?php echo $rst['opg_kg4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblkg4">Kg</label></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="text" size="10"  id="suma" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                    </tr>
                </tbody>
                <thead>
                    <tr><td colspan="7" class="sbtitle" >DETALLE DE PRODUCTO </td></tr>
                </thead>
                <tbody>   
                    <tr>
                        <td>ANCHO TOTAL:</td>
                        <td><input type="text" size="12"  id="opg_ancho_total"  value="<?php echo $rst['opg_ancho_total'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>m</td>
                    </tr>
                    <tr>
                        <td>PRODUCTO PRINCIPAL:</td>
                        <td><input type="text" size="12"  id="opg_prod_principal"  value="<?php echo $rst['opg_prod_principal'] ?>" /></td>
                    </tr>
                </tbody>
                <tbody>   
                    <tr>
                        <td>LARGO:</td>
                        <td><input type="text" size="12"  id="pro_largo"  value="<?php echo $rst['pro_largo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>m</td>
                    </tr>
                    <tr>
                        <td>ANCHO:</td>
                        <td><input type="text" size="12"  id="pro_ancho"  value="<?php echo $rst['pro_ancho'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />m</td>
                    </tr>
                </tbody>
                <tbody>   
                    <tr>
                        <td>GRAMAJE:</td>
                        <td><input type="text" size="12"  id="pro_gramaje"  value="<?php echo $rst['pro_gramaje'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>GR/M2</td>
                    </tr>
                    <tr>
                        <td>REFILADO:</td>
                        <td><input type="text" size="12"  id="opg_refilado"  value="<?php echo $rst['opg_refilado'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />m</td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <td colspan="7" class="sbtitle" >SET MAQUINAS</td>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="2" class="sbtitle" >1 ALIMENTACION</td>
                    </tr>
                    <tr>
                        <td>CAJA 1:</td>
                        <td><input type="text" size="12"  id="opg_caja1"  value="<?php echo $rst['opg_caja1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />gr</td>
                    </tr>
                    <tr>
                        <td>CAJA 2:</td>
                        <td><input type="text" size="12"  id="opg_caja2"  value="<?php echo $rst['opg_caja2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />gr</td>
                    </tr>
                    <tr>
                        <td>CAJA 3:</td>
                        <td><input type="text" size="12"  id="opg_caja3"  value="<?php echo $rst['opg_caja3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />gr</td>
                    </tr>
                    <tr>
                        <td>VELOCIDAD DE TRANSPORTE:</td>
                        <td><input type="text" size="12"  id="opg_vel_transporte"  value="<?php echo $rst['opg_vel_transporte'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />m/min</td>
                    </tr>
                    <tr>
                        <td>FRECUENCIA:</td>
                        <td><input type="text" size="12"  id="opg_frecuencia"  value="<?php echo $rst['opg_frecuencia'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Hz</td>
                    </tr>
                    <tr>
                        <td>Nº CAPAS:</td>
                        <td><input type="text" size="12"  id="opg_capas"  value="<?php echo $rst['opg_capas'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                <td colspan="2" class="sbtitle" >CARD-CROSS</td>
                </tr>   
                <tr>
                    <td>DOFFER:</td>
                    <td><input type="text" size="12"  id="opg_doffer"  value="<?php echo $rst['opg_doffer'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                <tr>
                    <td>FRONT:</td>
                    <td><input type="text" size="12"  id="opg_front"  value="<?php echo $rst['opg_front'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>RANDOM:</td>
                    <td><input type="text" size="12"  id="opg_random"  value="<?php echo $rst['opg_random'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                <tr>
                    <td>CONVEYOR :</td>
                    <td><input type="text" size="12"  id="opg_conveyor"  value="<?php echo $rst['opg_conveyor'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>COMPENSADOR:</td>
                    <td><input type="text" size="12"  id="opg_compensacion"  value="<?php echo $rst['opg_compensacion'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                </tbody> 
                <tbody>
                    <tr><td colspan="2" class="sbtitle" >SENSOR</td></tr>
                    <tr>
                        <td>1º</td>
                        <td><input type="text" size="12"  id="opg_sensor1"  value="<?php echo $rst['opg_sensor1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>2º</td>
                        <td><input type="text" size="12"  id="opg_sensor2"  value="<?php echo $rst['opg_sensor2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>3º</td>
                        <td><input type="text" size="12"  id="opg_sensor3"  value="<?php echo $rst['opg_sensor3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>4º</td>
                        <td><input type="text" size="12"  id="opg_sensor4"  value="<?php echo $rst['opg_sensor4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>5º</td>
                        <td><input type="text" size="12"  id="opg_sensor5"  value="<?php echo $rst['opg_sensor5'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>6º</td>
                        <td><input type="text" size="12"  id="opg_sensor6"  value="<?php echo $rst['opg_sensor6'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                </tbody>
                <thead>
                    <tr><td><br></td></tr>
                </thead>
                <tbody>   
                    <tr><td colspan="7" class="sbtitle" >3 VELOCIDAD DE CARDAS</td></tr>
                    <tr>
                        <td>DOSIFICADOR DE  ALIMENTACION:</td>
                        <td><input type="text" size="12"  id="opg_dosi_alimentacion"  value="<?php echo $rst['opg_dosi_alimentacion'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>Hz</td>
                    </tr>
                    <tr>
                        <td>MOTOR CARDA DE ALIMENTACION:</td>
                        <td><input type="text" size="12"  id="opg_mot_alimentacion"  value="<?php echo $rst['opg_mot_alimentacion'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Hz</td>
                    </tr>
                    <tr>
                        <td>MOTOR DE CARTDA Nº 2:</td>
                        <td><input type="text" size="12"  id="opg_mot_carda2"  value="<?php echo $rst['opg_mot_carda2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>Hz</td>
                    </tr>
                    <tr>
                        <td>MOTOR DE CILINDRO LIMPIADOR:</td>
                        <td><input type="text" size="12"  id="opg_mot_cilindro"  value="<?php echo $rst['opg_mot_cilindro'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />Hz</td>
                    </tr>
                    <tr>
                        <td>MOTOR DE +/- VELOCIDAD GRAMAJE:</td>
                        <td><input type="text" size="12"  id="opg_mot_gramaje"  value="<?php echo $rst['opg_mot_gramaje'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>a
                            <input type="text" size="12"  id="opg_hz"  value="<?php echo $rst['opg_hz'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/>Hz</td>
                    </tr>
                </tbody>
                <tbody>   
                    <tr><td colspan="7" class="sbtitle" >4 PUNZONADORAS</td></tr>
                    <tr><td colspan="7" class="sbtitle" >PRE- NEEDLE</td></tr>
                <td>VELOCIDAD TRANSPORTE DE MADERA:</td>
                <td><input type="text" size="12"  id="opg_vel_trans_madera"  value="<?php echo $rst['opg_vel_trans_madera'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                <tr>
                    <td>VELOCIDAD TRANSPORTE DE CAUCHO:</td>
                    <td><input type="text" size="12"  id="opg_vel_trans_caucho"  value="<?php echo $rst['opg_vel_trans_caucho'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>NUMERO DE GOLPES PUNZONADORA 1:</td>
                    <td><input type="text" size="12"  id="opg_num_punzonadora"  value="<?php echo $rst['opg_num_punzonadora'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                <tr>
                    <td>VELOCIDAD DE RODILLOS DE SALIDA:</td>
                    <td><input type="text" size="12"  id="opg_vel_rod_salida"  value="<?php echo $rst['opg_vel_rod_salida'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                </tr>
                <tr>
                    <td>VELOCIDAD DE RODILLOS COMPENSADORES:</td>
                    <td><input type="text" size="12"  id="opg_vel_rod_compensadores"  value="<?php echo $rst['opg_vel_rod_compensadores'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                </tr>
                </tbody>
                <thead>
                    <tr><td><br></td></tr>
                </thead>
                <tbody>   
                    <tr><td colspan="7" class="sbtitle" >5 WINDER</td></tr>
                    <tr>
                        <td>VELOCIDAD DE RODILLOS DE ENTRADA:</td>
                        <td><input type="text" size="12"  id="opg_vel_rod_entradawinder"  value="<?php echo $rst['opg_vel_rod_entradawinder'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>NUMERO DE GOLPES DE PUNZONADORA 2:</td>
                        <td><input type="text" size="12"  id="opg_numpunzo_winder"  value="<?php echo $rst['opg_numpunzo_winder'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>VELOCIDAD DE RODILLOS DE SALIDA:</td>
                        <td><input type="text" size="12"  id="opg_velrod_salidawinder"  value="<?php echo $rst['opg_velrod_salidawinder'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>NUMERO DE GOLPES DE PUNZONADORA 3:</td>
                        <td><input type="text" size="12"  id="opg_numgolpes_punzo"  value="<?php echo $rst['opg_numgolpes_punzo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>VELOCIDAD DE ENROLLADORA:</td>
                        <td><input type="text" size="12"  id="opg_vel_enrolladora"  value="<?php echo $rst['opg_vel_enrolladora'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                    <tr>
                        <td>REVOLUCIONES/MINUTO DE CALANDRA :</td>
                        <td><input type="text" size="12"  id="opg_rev_min_calan"  value="<?php echo $rst['opg_rev_min_calan'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');"/></td>
                    </tr>
                </tbody>
                <thead>
                    <tr><td><br></td></tr>
                </thead>
                <tbody id="tbobservaciones">
                    <tr>
                        <td>OBSERVACIONES:</td>
                        <td><input type="text" size="110"  id="opg_observaciones"  value="<?php echo $rst['opg_observaciones'] ?>"></textarea></td>
                    </tr>    
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php
                            if ($x != 1) {
                                ?>
                                <button id="guardar" >Guardar</button>    
                                <?php
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>    
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<script>

    var emp =<?php echo $rst[emp_id] ?>;
    var cli =<?php echo $rst[cli_id] ?>;
    var pro =<?php echo $rst[pro_id] ?>;
    var mp1 =<?php echo $rst[pro_mp1] ?>;
    var mp2 =<?php echo $rst[pro_mp2] ?>;
    var mp3 =<?php echo $rst[pro_mp3] ?>;
    var mp4 =<?php echo $rst[pro_mp4] ?>;
    var mf1 =<?php echo $rst[pro_mf1] ?>;
    var mf2 =<?php echo $rst[pro_mf2] ?>;
    var mf3 =<?php echo $rst[pro_mf3] ?>;
    var mf4 =<?php echo $rst[pro_mf4] ?>;

    $('#emp_id').val(emp);
    $('#cli_id').val(cli);
    $('#pro_id').val(pro);
    $('#pro_mp1').val(mp1);
    $('#pro_mp2').val(mp2);
    $('#pro_mp3').val(mp3);
    $('#pro_mp4').val(mp4);
    var s = mf1 + mf2 + mf3 + mf4;
    $('#suma').val(s);
    if (mf1 == 100.00) {
        $('#pro_mp2').hide();
        $('#pro_mf2').hide();
        $('#opg_kg2').hide();
        $('#lblporcentaje2').hide();
        $('#lblkg2').hide();
        $('#pro_mp3').hide();
        $('#pro_mf3').hide();
        $('#opg_kg3').hide();
        $('#lblporcentaje3').hide();
        $('#lblkg3').hide();
        $('#pro_mp4').hide();
        $('#pro_mf4').hide();
        $('#opg_kg4').hide();
        $('#lblporcentaje4').hide();
        $('#lblkg4').hide();
    }
    if (mf2 >= 0.00) {
        $('#pro_mp3').hide();
        $('#pro_mf3').hide();
        $('#opg_kg3').hide();
        $('#lblporcentaje3').hide();
        $('#lblkg3').hide();
        $('#pro_mp4').hide();
        $('#pro_mf4').hide();
        $('#opg_kg4').hide();
        $('#lblporcentaje4').hide();
        $('#lblkg4').hide();
    }
    if (mf3 > 0.00) {
        $('#pro_mp3').show();
        $('#pro_mf3').show();
        $('#opg_kg3').show();
        $('#lblporcentaje3').show();
        $('#lblkg3').show();
        $('#pro_mp4').hide();
        $('#pro_mf4').hide();
        $('#opg_kg4').hide();
        $('#lblporcentaje4').hide();
        $('#lblkg4').hide();
    }
    if (mf4 > 0.00) {
        $('#pro_mp4').show();
        $('#pro_mf4').show();
        $('#opg_kg4').show();
        $('#lblporcentaje4').show();
        $('#lblkg4').show();
    }

</script>