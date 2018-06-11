<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_bancos_y_cajas.php';
$Clase_bancos_y_cajas = new Clase_bancos_y_cajas();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_bancos_y_cajas->lista_un_reg_banco_caja($id));
    $rst_cta = pg_fetch_array($Clase_bancos_y_cajas->lista_plan_cuentas_id($rst[byc_id_cuenta]));
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
                if (id == 0) {
                    $("#byc_tipos1").attr('checked', true);
                } else {
                    habilitar();
                }
            });

            function save(id) {
                if (byc_tipos1.checked == true) {
                    tipos = 1;
                } else if (byc_tipos2.checked == true) {
                    tipos = 2;
                } else if (byc_tipos3.checked == true) {
                    tipos = 3;
                }
                var data = Array(
                        tipos,
                        byc_referencia.value,
                        byc_num_cuenta.value,
                        byc_tipo.value,
                        byc_num_documento.value,
                        byc_saldo.value,
                        byc_cuenta_contable.value,
                        pln_id.value);
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (byc_referencia.value == '') {
                            $("#byc_referencia").css({borderColor: "red"});
                            $("#byc_referencia").focus();
                            return false;
                        }
                        if ($('#byc_tipos1').attr('checked') == true) {
                            if (byc_tipo.value == 0) {
                                $("#byc_tipo").css({borderColor: "red"});
                                $("#byc_tipo").focus();
                                return false;
                            }
                            else if (byc_num_documento.value == '') {
                                $("#byc_num_documento").css({borderColor: "red"});
                                $("#byc_num_documento").focus();
                                return false;
                            }
                        }
                        if (byc_saldo.value == '') {
                            $("#byc_saldo").css({borderColor: "red"});
                            $("#byc_saldo").focus();
                            return false;
                        } else if (byc_cuenta_contable.value == '') {
                            $("#byc_cuenta_contable").css({borderColor: "red"});
                            $("#byc_cuenta_contable").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_bancos_y_cajas.php',
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_bancos_y_cajas.php';
            }

            function load_codigo(obj) {
                $.post("actions_bancos_y_cajas.php", {op: 1, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id').val(dat[0]);
                        $('#byc_cuenta_contable').val(dat[1]);
                        $('#byc_descrip_cta').html(dat[2].substr(0, 30));
                        $('#byc_descrip_cta').attr('title', dat[2]);
                    } else {
                        $('#pln_id').val('');
                        $('#byc_cuenta_contable').val('');
                        $('#byc_descrip_cta').html('');
                        $('#byc_descrip_cta').attr('title', '');
                    }
                });
            }

            function habilitar() {
                if ($('#byc_tipos1').attr('checked') == true) {
                    $('#byc_tipo').attr('disabled', false);
                    $('#byc_num_documento').attr('disabled', false);
                } else {
                    $('#byc_tipo').attr('disabled', true);
                    $('#byc_num_documento').attr('disabled', true);
                    $('#byc_tipo').val(0);
                    $('#byc_num_documento').val('');
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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
                    <tr><th colspan="9" >FORMULARIO BANCOS Y CAJAS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td><input type="radio" size="20"  id="byc_tipos1" name="byc_tipos" value="<?php echo $rst[byc_tipo] ?>" onclick="habilitar()"/>Banco</td>
                    <td><input type="radio" size="20"  id="byc_tipos2" name="byc_tipos" value="<?php echo $rst[byc_tipo] ?>" onclick="habilitar()"/>Caja</td>
                    <td><input type="radio" size="20"  id="byc_tipos3" name="byc_tipos" value="<?php echo $rst[byc_tipo] ?>" onclick="habilitar()"/>Caja Chica</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>REFERENCIA:</td>
                    <td colspan="2"><input type="text" size="30" id="byc_referencia" value="<?php echo $rst[byc_referencia] ?>" onblur="this.value = this.value.toUpperCase()" /></td>
                </tr>
                <tr>
                    <td># CUENTA:</td>
                    <td colspan="2"><input type="text" size="30"  id="byc_num_cuenta" value="<?php echo $rst[byc_num_cuenta] ?>" /></td>
                </tr>
                <tr>
                    <td>TIPO:</td>
                    <td>
                        <select id="byc_tipo">
                            <option value="0">SELECCIONE</option>
                            <option value="1">CORRIENTE</option>
                            <option value="2">AHORROS</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td># DOCUMENTO:</td>
                    <td colspan="2"><input type="text" size="30"  id="byc_num_documento" value="<?php echo $rst[byc_documento] ?>"/></td>
                </tr>
                <tr>
                    <td>SALDO:</td>
                    <td colspan="2"><input type="text" size="30"  id="byc_saldo" value="<?php echo $rst[byc_saldo] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                </tr>
                <tr>
                    <td>CUENTA CONTABLE:</td>
                    <td colspan="2">
                        <input type="text" size="30" id="byc_cuenta_contable" style="text-align: left" value="<?php echo $rst[byc_cuenta_contable] ?>" list="cuentas" onchange="load_codigo(this)" />
                        <input type="hidden" size="5" id="pln_id" value="<?php echo $rst[byc_id_cuenta] ?>">
                    </td>
                </tr>
                <tr>
                    <td>DESCRIPCION CUENTA:</td>
                    <td colspan="2"><font id="byc_descrip_cta" class="debe_haber" ><?php echo $rst_cta[pln_descripcion] ?></font></td>
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
    $cns_ctas = $Clase_bancos_y_cajas->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
<script>
    var rel =<?php echo $rst[byc_tipo] ?>;
    if (rel == 1) {
        $('#byc_tipos1').attr('checked', true);
    } else if (rel == 2) {
        $('#byc_tipos2').attr('checked', true);
    } else if (rel == 3) {
        $('#byc_tipos3').attr('checked', true);
    }

    var tip = '<?php echo $rst[byc_tipo_cuenta] ?>';
    $('#byc_tipo').val(tip);
</script>