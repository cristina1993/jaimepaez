<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_nota_debito.php';
$Reg_nota_debito = new Clase_reg_nota_debito();
$emisor = 1;
$id = $_GET[id];
if ($id != '') {
    $rst = pg_fetch_array($Reg_nota_debito->lista_una_nota_debito_id($id));
    $det = $_GET[x];
    $cns = $Reg_nota_debito->lista_detalle_nota($id);
} else {
    $det = 0;
    $id = '0';
    $rst_sec = pg_fetch_array($Reg_nota_debito->lista_secuencial_nota_debito($emisor));
    if (empty($rst_sec)) {
        $sec = 1;
    } else {
        $sec = ($rst_sec[sec] + 1);
    }
    if ($sec >= 0 && $sec < 10) {
        $tx = '000000000';
    } else if ($sec >= 10 && $sec < 100) {
        $tx = '00000000';
    } else if ($sec >= 100 && $sec < 1000) {
        $tx = '0000000';
    } else if ($sec >= 1000 && $sec < 10000) {
        $tx = '000000';
    } else if ($sec >= 10000 && $sec < 100000) {
        $tx = '00000';
    } else if ($sec >= 100000 && $sec < 1000000) {
        $tx = '0000';
    } else if ($sec >= 1000000 && $sec < 10000000) {
        $tx = '000';
    } else if ($sec >= 10000000 && $sec < 100000000) {
        $tx = '00';
    } else if ($sec >= 100000000 && $sec < 1000000000) {
        $tx = '0';
    } else if ($sec >= 1000000000 && $sec < 10000000000) {
        $tx = '';
    }

    $rst[rnd_num_registro] = $tx . $sec;
    $rst[rnd_fecha_emision] = date('Y-m-d');
    $rst[rnd_fec_registro] = date('Y-m-d');
    $rst[cli_id] = '0';
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
            var id = '<?php echo $id ?>';
            var usu =<?php echo $emisor ?>;
            var det = '<?php echo $det ?>';
            var dc = '2';
            var dec = '2';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#detalle').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#descripcion' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#detalle'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "fecha_registro", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_autorizacion", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo3"});
                Calendar.setup({inputField: "fecha_caducidad", ifFormat: "%Y-%m-%d", button: "im-campo4"});
                Calendar.setup({inputField: "fecha_emi_fac", ifFormat: "%Y-%m-%d", button: "im-campo5"});
                posicion_aux_window();

            });
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }

                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                tr.find("select").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }
                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                $('#detalle').find("tbody tr:last").after(tr);
                $('#descripcion' + x).focus();
                $('#cantidad' + x).val('0');
                $('#item' + x).attr('lang', x);
            }
//====================================================================================================================================================

            function save(id) {
                if (fecha_autorizacion.value == '') {
                    fec_aut = '1990-01-01';
                } else {
                    fec_aut = fecha_autorizacion.value;
                }

                if (fecha_caducidad.value == '') {
                    fec_cad = '1990-01-01';
                } else {
                    fec_cad = fecha_caducidad.value;
                }

                var data = Array(
                        cli_id.value,
                        num_comprobante.value,
                        motivo.value,
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value.toUpperCase(),
                        '1',
                        num_secuencial.value,
                        fecha_emi_fac.value,
                        $('#lblsubtotal12').html(),
                        $('#lblsubtotal0').html(),
                        $('#lblsubtotalex').html(),
                        $('#lblsubtotalno').html(),
                        ice.value,
                        $('#lbltotal_iva').html(),
                        $('#lbltotal_valor').html(),
                        num_registro.value,
                        autorizacion.value,
                        fecha_registro.value,
                        fec_aut,
                        fec_cad,
                        $('#lblsubtotal').html(),
                        fac_id.value
                        );
                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;

                while (n < i) {
                    n++;
                    if ($('#cantidad' + n).val() != null) {
                        cantidad = $("#cantidad" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val();
                        data1.push(
                                descripcion + '&' +
                                cantidad
                                )
                    }
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
//                        Validaciones antes de enviar
                        if ($('#num_comprobante').val().length == 0) {
                            $('#num_comprobante').css({borderColor: "red"});
                            $('#num_comprobante').focus();
                            return false;
                        } else if ($('#autorizacion').val().length == 0 || ($('#autorizacion').val().length != 10 && $('#autorizacion').val().length != 37)) {
                            $('#autorizacion').css({borderColor: "red"});
                            $('#autorizacion').focus();
                            return false;
                        } else if ($('#num_secuencial').val().length == 0) {
                            $('#num_secuencial').css({borderColor: "red"});
                            $('#num_secuencial').focus();
                            return false;
                        } else if ($('#fecha_emi_fac').val().length == 0) {
                            $('#fecha_emi_fac').css({borderColor: "red"});
                            $('#fecha_emi_fac').focus();
                            return false;
                        } else if ($('#identificacion').val().length == 0) {
                            $('#identificacion').css({borderColor: "red"});
                            $('#identificacion').focus();
                            return false;
                        } else if ($('#nombre').val().length == 0) {
                            $('#nombre').css({borderColor: "red"});
                            $('#nombre').focus();
                            return false;
                        }

                        n = 0;
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidad' + n).val() != null) {
                                    if ($('#descripcion' + n).val().length == 0) {
                                        $('#descripcion' + n).css({borderColor: "red"});
                                        $('#descripcion' + n).focus();
                                        return false;
                                    }
                                    if ($('#cantidad' + n).val() == 0 || $('#cantidad' + n).val().length == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_reg_nota_debito.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else if (dt == 1) {
                            alert('Una de las cuentas del Registro de Nota de Debito esta inactiva');
                            loading('hidden');
                        } else if (dt == 3) {
                            alert('Numero Secuencial del Registro ya existe \n Debe hacer otro Registro con otro Secuencial');
                            loading('hidden');
                            $('#guardar').attr('disabled', true);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//====================================================================================================================================================

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_nota_debito.php';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    calculo();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function calculo() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tiva = 0;
                var gtot = 0;
                var ic = 0;
                if (st1.checked == true) {
                    ob = '12';
                } else if (st2.checked == true) {
                    ob = '0';
                } else if (st3.checked == true) {
                    ob = 'NO';
                } else if (st4.checked == true) {
                    ob = 'EX';
                }
                ic = $('#ice').val();

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        val = 0;
                    } else {
                        val = $('#cantidad' + n).val().replace(',', '');
                    }

                    if (ob == '12') {
                        t12 = (t12 * 1 + val * 1);
                    }
                    if (ob == '0') {
                        t0 = (t0 * 1 + val * 1);
                    }
                    if (ob == 'EX') {
                        tex = (tex * 1 + val * 1);
                    }
                    if (ob == 'NO') {
                        tno = (tno * 1 + val * 1);
                    }
                }
                tiva = ((t12 * 1 + ic * 1) * 12 / 100);
                st = (t12 * 1) + (t0 * 1) + (tex * 1) + (tno * 1);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1 + ic * 1);
                $('#subtotal12').val(t12.toFixed(dec));
                $('#lblsubtotal12').html(t12.toFixed(6));
                $('#subtotal0').val(t0.toFixed(dec));
                $('#lblsubtotal0').html(t0.toFixed(6));
                $('#subtotalex').val(tex.toFixed(dec));
                $('#lblsubtotalex').html(tex.toFixed(6));
                $('#subtotalno').val(tno.toFixed(dec));
                $('#lblsubtotalno').html(tno.toFixed(6));
                $('#subtotal').val(st.toFixed(dec));
                $('#lblsubtotal').html(st.toFixed(6));
                $('#total_iva').val(tiva.toFixed(dec));
                $('#lbltotal_iva').html(tiva.toFixed(6));
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot.toFixed(6));
            }

            function load_cliente(obj) {
                $.post("actions_reg_nota_debito.php", {op: 4, id: obj.value, s: 0},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('Cliente no existe \n Porfavor registrelo antes de continuar!!');
                                $('#nombre').focus();
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#cli_id').val('0');
                            }
                        });
            }

            function load_cliente2(obj) {
                $.post("actions_reg_retencion.php", {op: 4, id: obj, s: 1},
                        function (dt) {
                            if (dt == 0) {
                                alert('Cliente no existe \n Porfavor registrelo antes de continuar!!');
                                $('#nombre').focus();
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#cli_id').val('0');
                            } else {
                                dat = dt.split('&');
                                $('#identificacion').val(dat[0]);
                                $('#nombre').val(dat[1]);
                                $('#cli_id').val(dat[2]);
                            }
                            $('#con_clientes').hide();
                        });
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 600) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 590) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }

            function num_comp(obj) {
                comp = obj.value;
                if (comp.length != 17) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple la estructura ejem: 000-000-000000000');
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_factura(obj) {
                if (obj.length == 17) {
                    $.post("actions_reg_nota_debito.php", {op: 5, id: obj, x: 0},
                            function (dt) {
                                dat = dt.split('&&');
                                if (dat[0] == '1') {
                                    $('#con_clientes').css('visibility', 'visible');
                                    $('#con_clientes').show();
                                    $('#clientes').html(dat[1]);
                                } else {
                                    alert('Factura no existe porfavor registrela para continuar');
                                    $('#fac_id').val('0');
                                    $('#fecha_emi_fac').val('');
                                    $('#identificacion').val('');
                                    $('#nombre').val('');
                                    $('#cli_id').val('');
                                    $('#num_secuencial').val('');
                                    $('#fecha_emi_fac').val('');
                                }
                            });
                }
            }

            function load_factura2(obj) {
                $.post("actions_reg_nota_debito.php", {op: 5, id: obj, x: 1},
                        function (dt) {
                            dat = dt.split('&');
                            if (dat[0] != '') {
                                $('#fac_id').val(dat[0]);
                                $('#fecha_emi_fac').val(dat[1]);
                                $('#identificacion').val(dat[2]);
                                $('#nombre').val(dat[3]);
                                $('#cli_id').val(dat[4]);
                            } else {
                                alert('Factura no existe porfavor registrela para continuar');
                                $('#fac_id').val('0');
                                $('#fecha_emi_fac').val('');
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#cli_id').val('');
                                $('#num_secuencial').val('');
                                $('#fecha_emi_fac').val('');
                            }
                            $('#con_clientes').hide();
                            doc_duplicado();
                        });

            }

            function doc_duplicado() {
                num_doc = $('#num_comprobante').val();
                ruc_pro = $('#identificacion').val();
                if (num_doc.length = 17 && ruc_pro.length > 0) {
                    $.post("actions_reg_nota_debito.php", {op: 6, id: num_doc, data: ruc_pro},
                            function (dt) {
                                dat = dt.split('&');
                                if (dat[0] != '') {
                                    alert('EL numero de Documento y el Ruc del Proveedor \n Ya existen en el Registro de Notas de Debito');
                                    $('#fac_id').val('0');
                                    $('#fecha_emi_fac').val('');
                                    $('#identificacion').val('');
                                    $('#nombre').val('');
                                    $('#cli_id').val('');
                                    $('#num_secuencial').val('');
                                    $('#fecha_emi_fac').val('');
                                    $('#num_comprobante').val('');
                                    $('#autorizacion').val('');
                                }
                            });
                }
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 80px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div> 
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" >

                <thead>
                    <tr><th colspan="12" >REGISTRO NOTA DE DEBITO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td>REGISTRO NO:</td>                    
                                <td><input type="text" size="15"  id="num_registro" readonly value="<?php echo $rst[rnd_num_registro] ?>"  /></td> 
                                <td>FECHA DE REGISTRO:</td>
                                <td><input type="text" size="15"  id="fecha_registro" readonly value="<?php echo $rst[rnd_fec_registro] ?>" /><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                                <td>FECHA DE AUTORIZACION:</td>
                                <td><input type="text" size="15"  id="fecha_autorizacion" readonly value="<?php echo $rst[rnd_fec_autorizacion] ?>" /><img src="../img/calendar.png" id="im-campo2" readonly/></td>
                            </tr>         
                            <tr>
                                <td>NOTA DE DEBITO NO:</td>                    
                                <td><input type="text" size="18" maxlength="17" id="num_comprobante" value="<?php echo $rst[rnd_numero] ?>" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" onblur="num_comp(this)" onchange="doc_duplicado(this)"/></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="15"  id="fecha_emision" readonly value="<?php echo $rst[rnd_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo3" readonly/></td>
                                <td>FECHA DE CADUCIDAD:</td>
                                <td><input type="text" size="15"  id="fecha_caducidad"  readonly value="<?php echo $rst[rnd_fec_caducidad] ?>" /><img src="../img/calendar.png" id="im-campo4" readonly/></td>

                            </tr>         
                            <tr>
                                <td>NO AUTORIZACION:</td>                    
                                <td><input type="text" size="18"  maxlength="37" id="autorizacion" value="<?php echo $rst[rnd_autorizacion] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '')"  />
                                <td>FACTURA NO:</td>                    
                                <td><input type="text" size="20"  maxlength="17" id="num_secuencial" value="<?php echo $rst[rnd_num_comp_modifica] ?>" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" onchange="load_factura(this.value)" onblur="num_comp(this)"/>
                                    <input type="hidden" size="10"  id="fac_id"  value="<?php echo $rst[reg_id] ?>"  /></td>
                                <td>FECHA EMISION FACTURA:</td>
                                <td><input type="text" size="15"  id="fecha_emi_fac" readonly value="<?php echo $rst[rnd_fecha_emi_comp] ?>" /></td>
                            </tr>
                            <tr>
                                <td>CI/RUC :</td>
                                <td><input type="text" size="15"  maxlength="13" readonly id="identificacion" value="<?php echo $rst[rnd_identificacion] ?>" onchange="load_cliente(this)"/></td>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="17"  id="nombre"  readonly value="<?php echo $rst[rnd_nombre] ?>"  />
                                    <input type="hidden" size="10"  id="cli_id"  value="<?php echo $rst[cli_id] ?>"  /></td>
                                <td>MOTIVO :</td>
                                <td><input type="text" size="20"  id="motivo" value="<?php echo $rst[rnd_motivo] ?>"  /></td>
                            </tr>
                        </table>
                    </td> 
                </tr>
                <tr>
                    <td>
                        <table id="detalle">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Razon de la Modificacion</th>
                            <th>Val. Modificacion</th>   
                            <th>Accion</th>
                            </thead>
                            <?php
                            if ($det == '0') {
                                ?>
                                <tr>
                                    <td><input type="text" size="8" id="item1" readonly class="itm" lang="1" value="1"   accept=""style="text-align:right" /></td>
                                    <td><input type="text" size="80" id="descripcion1" /></td>
                                    <td><input type="text" size="17" id="cantidad1" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="1"/></td>
                                    <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>

                                </tr>
                                <?php
                            } else {
                                $n = 0;
                                while ($rst_det = pg_fetch_array($cns)) {
                                    $n++;
                                    ?>

                                    <tr>
                                        <td><input type="text" size="8" id="item<?php echo $n ?>" readonly class="itm" lang="<?php echo $n ?>" value="<?php echo $n ?>"   accept=""style="text-align:right" /></td>
                                        <td><input type="text" size="80" id="descripcion<?php echo $n ?>" value="<?php echo $rst_det[rdd_descripcion] ?>" lang="<?php echo $n ?>"/></td>
                                        <td><input type="text" size="17" id="cantidad<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det[rdd_precio_total], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="<?php echo $n ?>"/></td>
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                                    </tr>

                                    <?php
                                }
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal" readonly  value="<?php echo str_replace(',', '', number_format($rst[rnd_subtotal], $dec)) ?>" style="text-align:right"/>
                                        <label hidden id="lblsubtotal"><?php echo $rst[rnd_subtotal] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal12" readonly  value="<?php echo str_replace(',', '', number_format($rst[rnd_subtotal12], $dec)) ?>"style="text-align:right" /><input type="radio" id="st1" name="st" onclick="calculo()"/>
                                        <label hidden id="lblsubtotal12"><?php echo $rst[rnd_subtotal12] ?></label></td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 0%:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst[rnd_subtotal0], $dec)) ?>" style="text-align:right" /><input type="radio" id="st2" name="st" onclick="calculo()"/>
                                        <label hidden id="lblsubtotal0"><?php echo $rst[rnd_subtotal0] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal No Objeto Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst[rnd_subtotal_no_iva], $dec)) ?>" style="text-align:right"/><input type="radio" id="st3" name="st" onclick="calculo()"/>
                                        <label hidden id="lblsubtotalno"><?php echo $rst[rnd_subtotal_no_iva] ?></label></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="right">Total Excento Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst[rnd_subtotal_ex_iva], $dec)) ?>" style="text-align:right"/><input type="radio" id="st4" name="st" onclick="calculo()"/>
                                        <label hidden id="lblsubtotalex"><?php echo $rst[rnd_subtotal_ex_iva] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">ICE:</td>
                                    <td class="sbtls" ><input type="text" size="18" id="ice" value="<?php echo str_replace(',', '', number_format($rst[rnd_total_ice], $dec)) ?>" style="text-align:right" onchange="calculo()"/>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">IVA 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="total_iva" readonly  value="<?php echo str_replace(',', '', number_format($rst[rnd_total_iva], $dec)) ?>" style="text-align:right"/>
                                        <label hidden id="lbltotal_iva"><?php echo $rst[rnd_total_iva] ?></label></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="right">Total:</td>
                                    <td class="sbtls"><input type="text" size="15" id="total_valor" readonly  value="<?php echo str_replace(',', '', number_format($rst[rnd_total_valor], $dec)) ?>"  style="text-align:right"/>
                                        <label hidden id="lbltotal_valor"><?php echo $rst[rnd_total_valor] ?></label></td>
                                    <td></td>
                                </tr>
                        </table>
                    </td>
                </tr>
                </tfoot>
                <!--</tfoot>-->
                <!------------------------------------->
            </table>
        </form>
        <?php
        if ($det < 2) {
            ?>
            <button id="guardar" onclick="save(<?php echo $id ?>)">Guardar</button>
            <?php
        }
        ?>
        <button id="cancelar" >Cancelar</button> 
    </body>
</html>    
<script>
    var iva12 = '<?php echo $rst[rnd_subtotal12] ?>';
    var iva0 = '<?php echo $rst[rnd_subtotal0] ?>';
    var ivaex = '<?php echo $rst[rnd_subtotal_ex_iva] ?>';
    var ivano = '<?php echo $rst[rnd_subtotal_no_iva] ?>';
    if (parseFloat(iva12) > 0) {
        $('#st1').attr('checked', true);
    }
    if (parseFloat(iva0) > 0) {
        $('#st2').attr('checked', true);
    }
    if (parseFloat(ivano) > 0) {
        $('#st3').attr('checked', true);
    }
    if (parseFloat(ivaex) > 0) {
        $('#st4').attr('checked', true);
    }
</script>