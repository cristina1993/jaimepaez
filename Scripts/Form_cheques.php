<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cheques.php';
$Clase_cheques = new Clase_cheques();
$txt = trim(strtoupper($_GET[txt]));
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_cheques->lista_un_cheque($id));
} else {
    $id = 0;
    $rst[chq_recepcion] = date('Y-m-d');
    $rst[chq_fecha] = date('Y-m-d');
    $rst['mov_cantidad1'] = 0;
    $fila = 0;
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
                });

                Calendar.setup({inputField: "chq_recepcion", ifFormat: "%Y-%m-%d", button: "im-chq_recepcion"});
                Calendar.setup({inputField: "chq_fecha", ifFormat: "%Y-%m-%d", button: "im-chq_fecha"});
                posicion_aux_window();

            });
            function save(id) {
                var data = Array(
                        cli_id.value,
                        chq_nombre.value,
                        chq_banco.value,
                        chq_numero.value,
                        chq_recepcion.value,
                        chq_fecha.value,
                        chq_monto.value,
                        chq_tipo_doc.value,
                        codigo_cta.value,
                        pln_id.value,
                        chq_concepto.value
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
                        if (chq_tipo_doc.value == 0) {
                            $("#chq_tipo_doc").css({borderColor: "red"});
                            $("#chq_tipo_doc").focus();
                            return false;
                        }
                        else if (cli_id.value.length == 0 || cli_id.value == '0') {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        }
                        else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        }
                        else if (chq_nombre.value.length == 0) {
                            $("#chq_nombre").css({borderColor: "red"});
                            $("#chq_nombre").focus();
                            return false;
                        }
                        else if (chq_banco.value.length == 0) {
                            $("#chq_banco").css({borderColor: "red"});
                            $("#chq_banco").focus();
                            return false;
                        }
                        else if (chq_numero.value.length == 0) {
                            $("#chq_numero").css({borderColor: "red"});
                            $("#chq_numero").focus();
                            return false;
                        }
                        else if (chq_monto.value.length == 0) {
                            $("#chq_monto").css({borderColor: "red"});
                            $("#chq_monto").focus();
                            return false;
                        }
                        else if (codigo_cta.value.length == 0) {
                            $("#codigo_cta").css({borderColor: "red"});
                            $("#codigo_cta").focus();
                            return false;
                        }
                        else if (pln_id.value.length == 0 || pln_id.value == '0') {
                            $("#codigo_cta").css({borderColor: "red"});
                            $("#codigo_cta").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_cheques.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            cancelar();
//                            parent.document.getElementById('bottomFrame').src = '../Scripts/Form_control_cobros.php?cli=' + cli_id.value + '&ch=' + dat[1];
                        } else {
                            alert(dat[0]); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cheques.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
            }

            function load_cliente(obj) {
                $.post("actions_cheques.php", {op: 2, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#nombre').focus();
                        $('#nombre').val('');
                        $('#cli_id').val('');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_cheques.php", {op: 2, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#nombre').focus();
                        $('#nombre').val('');
                        $('#cli_id').val('');
                    } else {
                        dat = dt.split('&');
                        $('#nombre').val(dat[1]);
                        $('#cli_id').val(dat[8]);
                    }
                    $('#con_clientes').hide();
                });
            }
            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_codigo(obj) {
                n = obj.lang;
                $.post("actions_cheques.php", {op: 4, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id' + n).val(dat[0]);
                        $('#codigo_cta' + n).val(dat[1]);
                    } else {
                        alert('Cuenta no existe o esta inactiva');
                        $('#pln_id' + n).val('');
                        $('#codigo_cta' + n).val('');
                    }
                });
            }


        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="proceso" >   
        </div>
        <div id="cargando"></div>

        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>FECHA RECEPCION:</td>
                    <td>
                        <input type="text" size="10" id="chq_recepcion" readonly value="<?php echo $rst[chq_recepcion] ?>" />
                        <img src="../img/calendar.png" id="im-chq_recepcion" />
                    </td>
                </tr>
                <tr>
                    <td>FECHA CHEQUE:</td>
                    <td>
                        <input type="text" size="10" id="chq_fecha" readonly value="<?php echo $rst[chq_fecha] ?>" />
                        <img src="../img/calendar.png" id="im-chq_fecha" />
                    </td>
                </tr>
                <tr>
                    <td>TIPO DOCUMENTO</td>
                    <td>
                        <select id="chq_tipo_doc" lang="1" style="width: 260px" onblur="habilitar(this), cambio_cmb(this)">
                            <option value="0">SELECCIONE</option>
                            <option value="1">CHEQUE A LA FECHA</option>
                            <option value="2">CHEQUE POSTFECHADO</option>
                            <!--<option value="3">NOTA CREDITO</option>-->
                            <!--<option value="4">NOTA DEBITO</option>-->
                            <!--<option value="5">RETENCION</option>-->
                            <option value="6">TARJETA DE CREDITO</option>
                            <option value="7">TARJETA DE DEBITO</option>
                            <option value="8">CERTIFICADOS</option>
                            <option value="9">BONOS</option>
                            <option value="10">EFECTIVO</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>CLIENTE:</td>
                    <td><input type="text" size="40"  id="nombre" value="<?php echo $rst[cli_raz_social] ?>" onchange="load_cliente(this)"  />
                        <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"/></td>
                </tr>
                <tr>
                    <td>NOMBRE DEL CHEQUE:</td>
                    <td><input type="text" size="40"  id="chq_nombre" value="<?php echo $rst[chq_nombre] ?>" /></td>
                </tr>
                <tr>
                    <td>CONCEPTO:</td>
                    <td><input type="text" size="40"  id="chq_concepto" value="<?php echo $rst[chq_concepto] ?>"/></td>
                </tr>
                <tr>
                    <td>BANCO:</td>
                    <td><input type="text" size="40"  id="chq_banco" value="<?php echo $rst[chq_banco] ?>"/></td>
                </tr>
                <tr>
                    <td># CHEQUE:</td>
                    <td><input type="text" size="40"  id="chq_numero" value="<?php echo $rst[chq_numero] ?>"/></td>
                </tr>

                <tr>
                    <td>MONTO:</td>
                    <td><input type="text" size="40"  id="chq_monto" value="<?php echo $rst[chq_monto] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                </tr>

                <tr>
                    <td>CUENTA:</td>
                    <td><input class="dt_input" type="text" id="codigo_cta" size="40"  list="cuentas" onchange="load_codigo(this)"  value="<?php echo $rst[chq_cuenta] ?>"/>
                        <input type="hidden" id="pln_id" size="10" value="<?php echo $rst[pln_id] ?>" </td>
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
    var tip = '<?php echo $rst[chq_tipo_doc] ?>';
    $('#chq_tipo_doc').val(tip);
</script>

<datalist id="cuentas">
    <?php
    $cns_ctas = $Clase_cheques->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>