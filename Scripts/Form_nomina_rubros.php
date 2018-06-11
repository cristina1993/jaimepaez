<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_rubros.php';
$Rub = new Clase_nomina_rubros();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Rub->lista_una_nomina_rubros($id));
    $rst_cta = pg_fetch_array($Rub->lista_plan_cuentas_id($rst[rub_cuenta_contable]));
    $fijo = $rst[rub_operacion];
    if ($fijo != 'DT*VD') {
        $operacion = explode("*", $fijo);
        $op1 = $operacion[0];
        $op2 = $operacion[1];
        $op3 = $operacion[2];
    }
} else {
    $id = 0;
    $ope_fijo = pg_fetch_array($Rub->lista_val_operacion_fijo());
    $fijo = $ope_fijo[rub_operacion];
    if ($fijo == '') {
        $rst[rub_codigo] = 'SLD001';
        $rst[rub_grupo] = 'SUELDO';
        $rst[rub_descripcion] = 'SUELDO';
        $rst[rub_valor] = '0';
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario Nomina Rubros</title>
        <script>
            var id =<?php echo $id ?>;
            var fijo = '<?php echo $fijo ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id, fijo);
                });
                if (id == 0) {
                    $("#rub_unidad1").attr('checked', true);
                    $("#rub_tipo1").attr('checked', true);
                    $("#rub_nomina1").attr('checked', true);
                    $("#rub_iess1").attr('checked', true);
                    $("#rub_combo2").attr('checked', true);
                    if (fijo == 'DT*VD') {
                        $("#rub_operacion1").attr('disabled', false);
                        $("#rub_operacion2").attr('disabled', false);
                        $("#rub_operacion3").attr('disabled', false);
                        $("#rub_codigo").attr('disabled', false);
                        $("#rub_grupo").attr('disabled', false);
                        $("#rub_descripcion").attr('disabled', false);
                        $("#rub_valor").attr('disabled', false);
                    } else {
                        $("#rub_operacion1").attr('disabled', true);
                        $("#rub_operacion2").attr('disabled', true);
                        $("#rub_operacion3").attr('disabled', true);
                        $("#rub_codigo").attr('disabled', true);
                        $("#rub_grupo").attr('disabled', true);
                        $("#rub_descripcion").attr('disabled', true);
                        $("#rub_valor").attr('disabled', true);
                    }
                } else {
                    if (fijo == 'DT*VD') {
                        $("#rub_operacion1").attr('disabled', true);
                        $("#rub_operacion2").attr('disabled', true);
                        $("#rub_operacion3").attr('disabled', true);
                        $("#rub_codigo").attr('disabled', true);
                        $("#rub_grupo").attr('disabled', true);
                        $("#rub_descripcion").attr('disabled', true);
                        $("#rub_valor").attr('disabled', true);
                    } else {
                        $("#rub_operacion1").attr('disabled', false);
                        $("#rub_operacion2").attr('disabled', false);
                        $("#rub_operacion3").attr('disabled', false);
                        $("#rub_codigo").attr('disabled', false);
                        $("#rub_grupo").attr('disabled', false);
                        $("#rub_descripcion").attr('disabled', false);
                        $("#rub_valor").attr('disabled', false);
                    }
                }
            });

            function save(id, fijo) {
                if (rub_tipo1.checked == true) {
                    tipo = 1;
                } else if (rub_tipo2.checked == true) {
                    tipo = 2;
                }
                if (rub_nomina1.checked == true) {
                    nomina = 1;
                } else if (rub_nomina2.checked == true) {
                    nomina = 2;
                }
                if (rub_iess1.checked == true) {
                    iess = 1;
                } else if (rub_iess2.checked == true) {
                    iess = 2;
                }
                if (rub_combo1.checked == true) {
                    combo = 1;
                } else if (rub_combo2.checked == true) {
                    combo = 2;
                }
                if (rub_unidad1.checked == true) {
                    unidad = 1;
                } else if (rub_unidad2.checked == true) {
                    unidad = 2;
                }
                if (id == 0) {
                    if (fijo == 'DT*VD') {
                        oper1 = $('#rub_operacion1').val();
                        oper2 = $('#rub_operacion2').val();
                        oper3 = $('#rub_operacion3').val();
                        if (oper1 != 0 && oper2 == 0 && oper3 == 0) {
                            operacion = oper1;
                        } else if (oper1 != 0 && oper2 != 0 && oper3 == 0) {
                            operacion = oper1 + '*' + oper2;
                        } else if (oper1 != 0 && oper2 != 0 && oper3 != 0) {
                            operacion = oper1 + '*' + oper2 + '*' + oper3;
                        }
                        if (oper1 == 0 && oper2 == 0 && oper3 == 0) {
                            operacion = '';
                        }
                    } else {
                        operacion = 'DT*VD';
                    }
                } else {
                    if (fijo == 'DT*VD') {
                        operacion = 'DT*VD';
                    } else {
                        oper1 = $('#rub_operacion1').val();
                        oper2 = $('#rub_operacion2').val();
                        oper3 = $('#rub_operacion3').val();
                        if (oper1 != 0 && oper2 == 0 && oper3 == 0) {
                            operacion = oper1;
                        } else if (oper1 != 0 && oper2 != 0 && oper3 == 0) {
                            operacion = oper1 + '*' + oper2;
                        } else if (oper1 != 0 && oper2 != 0 && oper3 != 0) {
                            operacion = oper1 + '*' + oper2 + '*' + oper3;
                        }
                        if (oper1 == 0 && oper2 == 0 && oper3 == 0) {
                            operacion = '';
                        }
                    }
                }
                var data = Array(
                        rub_codigo.value.toUpperCase(),
                        rub_grupo.value.toUpperCase(),
                        rub_descripcion.value.toUpperCase(),
                        rub_valor.value,
                        tipo,
                        pln_id.value,
                        nomina,
                        iess,
                        combo,
                        unidad,
                        operacion
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
                        if (rub_codigo.value.length == 0) {
                            $("#rub_codigo").css({borderColor: "red"});
                            $("#rub_codigo").focus();
                            return false;
                        } else if (rub_grupo.value.length == 0) {
                            $("#rub_grupo").css({borderColor: "red"});
                            $("#rub_grupo").focus();
                            return false;
                        } else if (rub_descripcion.value.length == 0) {
                            $("#rub_descripcion").css({borderColor: "red"});
                            $("#rub_descripcion").focus();
                            return false;
                        } else if (rub_valor.value.length == 0) {
                            $("#rub_valor").css({borderColor: "red"});
                            $("#rub_valor").focus();
                            return false;
                        } else if (rub_cuenta_contable.value.length == 0) {
                            $("#rub_cuenta_contable").css({borderColor: "red"});
                            $("#rub_cuenta_contable").focus();
                            return false;
                        } else if (operacion != 'DT*VD') {
                            if (rub_operacion1.value == 0) {
                                $("#rub_operacion1").css({borderColor: "red"});
                                $("#rub_operacion1").focus();
                                return false;
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_nomina_rubros.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nomina_rubros.php';
            }

            function load_codigo(obj) {
                $.post("actions_bancos_y_cajas.php", {op: 1, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id').val(dat[0]);
                        $('#rub_cuenta_contable').val(dat[1]);
                        $('#rub_descrip_cta').html(dat[2].substr(0, 30));
                        $('#rub_descrip_cta').attr('title', dat[2]);
                    } else {
                        $('#pln_id').val('');
                        $('#rub_cuenta_contable').val('');
                        $('#rub_descrip_cta').html('');
                        $('#rub_descrip_cta').attr('title', '');
                    }
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function control_iess(obj) {
                i = obj.value.toUpperCase();
                iess = $.trim(i);
                if (iess == 'IESS') {
                    $('#rub_iess2').attr('checked', true);
                    $('#rub_iess1').attr('disabled', true);
                    $('#rub_iess2').attr('disabled', true);
                } else {
                    $('#rub_iess1').attr('disabled', false);
                    $('#rub_iess2').attr('disabled', false);
                }
            }

            function ctrl_cantidad1() {
                val = $('#rub_valor').val();
                if (rub_unidad2.checked == true) {
                    if (val > 100) {
                        alert('El valor en porcentaje es mayor a 100%');
                        $('#rub_valor').val('');
                        $('#rub_valor').focus();
                    }
                }
            }

            function ctrl_cantidad2() {
                val = $('#rub_valor').val();
                if (val > 100) {
                    alert('El valor en porcentaje es mayor a 100%');
                    $('#rub_valor').val('');
                    $('#rub_valor').focus();
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
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="3" >NOMINA RUBROS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>Codigo</td>
                    <td colspan="2"><input type="text" size="34" id="rub_codigo" value="<?php echo $rst[rub_codigo] ?>"></td>
                </tr>
                <tr>
                    <td>Grupo</td>
                    <td colspan="2"><input type="text" size="34" id="rub_grupo" value="<?php echo $rst[rub_grupo] ?>" onblur="control_iess(this)"></td>
                </tr>
                <tr>
                    <td>Descripcion</td>
                    <td colspan="2"><input type="text" size="34" id="rub_descripcion" value="<?php echo $rst[rub_descripcion] ?>" ></td>
                </tr>
                <tr>
                    <td>Valor</td>
                    <td colspan="2">
                        <input type="text" size="8" id="rub_valor" value="<?php echo $rst[rub_valor] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onblur="ctrl_cantidad1()">
                        <input type="radio" size="10"  id="rub_unidad1" name="rub_unidad" value=""/>Unidad
                        <input type="radio" size="10"  id="rub_unidad2" name="rub_unidad" value="" onclick="ctrl_cantidad2()"/>Porcentaje
                    </td>
                </tr>
                <tr>
                    <td>Tipo</td>
                    <td colspan="2">
                        <input type="radio" size="10"  id="rub_tipo1" name="rub_tipo" value=""/>Credito
                        &nbsp;&nbsp;
                        <input type="radio" size="20"  id="rub_tipo2" name="rub_tipo" value=""/>Debito
                    </td>
                </tr>
                <tr>
                    <td>CUENTA CONTABLE:</td>
                    <td colspan="2">
                        <input type="text" size="34" id="rub_cuenta_contable" style="text-align: left" value="<?php echo $rst_cta[pln_codigo] ?>" list="cuentas" onchange="load_codigo(this)" />
                        <input type="hidden" size="5" id="pln_id" value="<?php echo $rst[rub_cuenta_contable] ?>">
                    </td>
                </tr>
                <tr>
                    <td>DESCRIPCION CUENTA:</td>
                    <td colspan="2"><font id="rub_descrip_cta" class="debe_haber" ><?php echo $rst_cta[pln_descripcion] ?></font></td>
                </tr>
                <tr>
                    <td>Nomina</td>
                    <td colspan="2">
                        <input type="radio" size="20"  id="rub_nomina1" name="rub_nomina" value=""/>Si
                        &nbsp;&nbsp;
                        <input type="radio" size="20"  id="rub_nomina2" name="rub_nomina" value=""/>No
                    </td>
                </tr>
                <tr>
                    <td>IESS</td>
                    <td colspan="2">
                        <input type="radio" size="20"  id="rub_iess1" name="rub_iess" value=""/>Si
                        &nbsp;&nbsp;
                        <input type="radio" size="20"  id="rub_iess2" name="rub_iess" value=""/>No
                    </td>
                </tr>
                <tr>
                    <td>COMBO</td>
                    <td colspan="2">
                        <input type="radio" size="20"  id="rub_combo1" name="rub_combo" value=""/>Si
                        &nbsp;&nbsp;
                        <input type="radio" size="20"  id="rub_combo2" name="rub_combo" value=""/>No
                    </td>
                </tr>
                <tr>
                    <td><input type="hidden" id="rub_estado" value="<?php echo $rst[rub_estado] ?>"></td>
                </tr>
                <thead>
                    <tr><th colspan="3" >OPERACION</th></tr>
                </thead>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <select id="rub_operacion1" >
                            <option value="0">SELECCIONE</option>
                            <option value="B">B</option>
                            <option value="VH">VH</option>
                            <option value="VD">VD</option>
                            <option value="V">V</option>
                            <option value="C">C</option>
                            <option value="DT">DT</option>
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x
                    </td>
                    <td>
                        <select id="rub_operacion2" >
                            <option value="0">SELECCIONE</option>
                            <option value="B">B</option>
                            <option value="VH">VH</option>
                            <option value="VD">VD</option>
                            <option value="V">V</option>
                            <option value="C">C</option>
                            <option value="DT">DT</option>
                        </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x
                    </td>
                    <td>
                        <select id="rub_operacion3" >
                            <option value="0">SELECCIONE</option>
                            <option value="B">B</option>
                            <option value="VH">VH</option>
                            <option value="VD">VD</option>
                            <option value="V">V</option>
                            <option value="C">C</option>
                            <option value="DT">DT</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
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
<datalist id="cuentas">
    <?php
    $cns_ctas = $Rub->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
<script>
    var tip =<?php echo $rst[rub_tipo] ?>;
    if (tip == 1) {
        $('#rub_tipo1').attr('checked', true);
    } else if (tip == 2) {
        $('#rub_tipo2').attr('checked', true);
    }
    var nom =<?php echo $rst[rub_nomina] ?>;
    if (nom == 1) {
        $('#rub_nomina1').attr('checked', true);
    } else if (nom == 2) {
        $('#rub_nomina2').attr('checked', true);
    }
    var iess =<?php echo $rst[rub_iess] ?>;
    if (iess == 1) {
        $('#rub_iess1').attr('checked', true);
    } else if (iess == 2) {
        $('#rub_iess2').attr('checked', true);
    }
    var com =<?php echo $rst[rub_combo] ?>;
    if (com == 1) {
        $('#rub_combo1').attr('checked', true);
    } else if (com == 2) {
        $('#rub_combo2').attr('checked', true);
    }
    var uni =<?php echo $rst[rub_tipo_valor] ?>;
    if (uni == 1) {
        $('#rub_unidad1').attr('checked', true);
    } else if (uni == 2) {
        $('#rub_unidad2').attr('checked', true);
    }
    var pro1 = '<?php echo $op1 ?>';
    $('#rub_operacion1').val(pro1);
    var pro2 = '<?php echo $op2 ?>';
    $('#rub_operacion2').val(pro2);
    var pro3 = '<?php echo $op3 ?>';
    $('#rub_operacion3').val(pro3);
</script>
