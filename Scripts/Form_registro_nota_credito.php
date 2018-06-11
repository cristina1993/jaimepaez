<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_nota_credito.php';
$Reg_nota_credito = new Clase_reg_nota_credito();
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
$det = $_GET[x];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Reg_nota_credito->lista_una_nota_credito($id));
    $cns = $Reg_nota_credito->lista_detalle_nota_credito($id);
    $col = '7';
    $dec = 2;
} else {
    $det = '0';
    $id = '0';
    $dec = 2;
    $rst_det[fac_id] = '0';
    $rst_sec = pg_fetch_array($Reg_nota_credito->lista_secuencial_nota_credito($emisor));
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
    $rst[rnc_num_registro] = $tx . $sec;
    $rst[rnc_fecha_emision] = date('Y-m-d');
    $rst[rnc_fec_registro] = date('Y-m-d');
    $col = '7';
//    $rst[trs_id] ='0';
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
            var det = '<?php echo $det ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            inven = '<?php echo $inv5 ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        if ($('#add_row').attr('style') != 'display: none;') {
                            var tr = $('#detalle').find("tbody tr:last");
                            var a = tr.find("input").attr("lang");
                            if ($('#descripcion' + a).val().length != 0) {
                                if (this.lang == 0) {
                                    clona_fila($('#detalle'));
                                }
                            }
                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_registro", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_autorizacion", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                Calendar.setup({inputField: "fecha_caducidad", ifFormat: "%Y-%m-%d", button: "im-campo3"});
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo4"});
                Calendar.setup({inputField: "fecha_emision_comprobante", ifFormat: "%Y-%m-%d", button: "im-campo5"});
                posicion_aux_window();
                $('#add_row').hide();
                if (id != 0) {
                    ocultar_mod();
                }
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
                tr.find("label").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        $(this).html(x);
                    }
                    if (parts[1] != 'item') {
                        $(this).html(x);
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
                $('#cod_producto' + x).focus();
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
                        descripcion_motivo.value,
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value,
                        '1', //denominacion
                        num_secuencial.value,
                        fecha_emision_comprobante.value,
                        $('#lblsubtotal12').html().replace(',', ''),
                        $('#lblsubtotal0').html().replace(',', ''),
                        $('#lblsubtotalex').html().replace(',', ''),
                        $('#lblsubtotalno').html().replace(',', ''),
                        $('#lbltotal_descuento').html().replace(',', ''),
                        $('#lbltotal_ice').html().replace(',', ''),
                        $('#lbltotal_iva').html().replace(',', ''),
                        $('#lblirbpnr').html().replace(',', ''),
                        $('#lblpropina').html().replace(',', ''),
                        $('#autorizacion').val(),
                        $('#lbltotal_valor').html().replace(',', ''),
                        motivo.value,
                        $('#lblsubtotal').html().replace(',', ''),
                        num_registro.value,
                        fecha_registro.value,
                        fec_aut,
                        fec_cad,
                        $("#fac_id").val()
                        );
                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                val_dat = 0;
                while (n < i) {
                    n++;
                    if ($('#cantidadf' + n).val() != null) {
                        if (motivo.value == '1') {
                            cod_producto = '';
                            cod_externo = '';
                            pro_id = '0';
                        } else {
                            cod_producto = $("#cod_producto" + n).val();
                            cod_externo = $("#cod_externo" + n).val();
                            pro_id = $("#pro_id" + n).val();
                        }
                        cantidad = $("#cantidadf" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val().replace(',', '');
                        precio_unitario = $("#precio_unitario" + n).val().replace(',', '');
                        descuento = $("#descuento" + n).val().replace(',', '');
                        descuent = $("#lbldescuent" + n).html().replace(',', '');
                        precio_total = $("#lblprecio_total" + n).html().replace(',', '');
                        iva = $("#iva" + n).val().replace(',', '');
                        pln = $('#pln_id' + n).val();
                        cta = $('#cuenta' + n).val();
                        if ($('#pln_id' + n).val() == '0' || $('#pln_id' + n).val().length == 0) {
                            val_dat = 1;
                        }
                        data1.push(
                                pro_id + '&' +
                                cod_producto + '&' +
                                cod_externo + '&' +
                                cantidad + '&' +
                                descripcion + '&' +
                                precio_unitario + '&' +
                                descuento + '&' +
                                descuent + '&' +
                                precio_total + '&' +
                                iva + '&' +
                                pln + '&' +
                                cta
                                );
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
                        doc = document.getElementsByClassName('itm');
                        var tr = $('#detalle').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (num_comprobante.value.length == 0) {
                            $("#num_comprobante").css({borderColor: "red"});
                            $("#num_comprobante").focus();
                            return false;
                        } else if ($('#autorizacion').val().length == 0 || ($('#autorizacion').val().length != 10 && $('#autorizacion').val().length != 37 && $('#autorizacion').val().length != 49)) {
                            $('#autorizacion').css({borderColor: "red"});
                            $('#autorizacion').focus();
                            return false;
                        } else if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            return false;
                        } else if (fecha_emision_comprobante.value.length == 0) {
                            $("#fecha_emision_comprobante").css({borderColor: "red"});
                            $("#fecha_emision_comprobante").focus();
                            return false;
                        } else if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            return false;
                        } else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        } else if (descripcion_motivo.value.length == 0) {
                            $("#descripcion_motivo").css({borderColor: "red"});
                            $("#descripcion_motivo").focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidadf' + n).val() != null) {
                                    if (motivo.value != '1') {
                                        if ($('#cod_producto' + n).val().length == 0) {
                                            $('#cod_producto' + n).css({borderColor: "red"});
                                            $('#cod_producto' + n).focus();
                                            return false;
                                        }
                                    }
                                    if ($('#descripcion' + n).val().length == 0) {
                                        $('#descripcion' + n).css({borderColor: "red"});
                                        $('#descripcion' + n).focus();
                                        return false;
                                    } else if ($('#cantidadf' + n).val().length == 0 || $('#cantidadf' + n).val() == 0) {
                                        $('#cantidadf' + n).css({borderColor: "red"});
                                        $('#cantidadf' + n).focus();
                                        return false;
                                    }
                                    if ($('#precio_unitario' + n).val().length == 0) {
                                        $('#precio_unitario' + n).css({borderColor: "red"});
                                        $('#precio_unitario' + n).focus();
                                        return false;
                                    } else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    } else if ($('#iva' + n).val().length == 0) {
                                        $('#iva' + n).css({borderColor: "red"});
                                        $('#iva' + n).focus();
                                        return false;
                                    }
                                    if (motivo.value != '1') {
                                        c = $("#cantidadf" + n).val();
                                        cf = $("#cantidad" + n).val();
                                        if (parseFloat(c) > parseFloat(cf)) {
                                            $("#cantidadf" + n).val('');
                                            $("#cantidadf" + n).focus();
                                            $("#cantidadf" + n).css({borderColor: "red"});
                                            $("#cantidadf" + n).val('0.00');
                                            calculo();
                                            alert('La Cantidad es mayor a la factura');
                                            return false;
                                        }
                                    }

                                    if (inven == 0) {
                                        if (motivo.value != '1') {
                                            if ($('#inventario' + n).val().trim().length != 0) {
                                                if (parseFloat($('#inventario' + n).val()) < parseFloat($("#cantidadf" + n).val())) {
                                                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                                                    $("#cantidadf" + n).val('');
                                                    $("#cantidadf" + n).focus();
                                                    $("#cantidadf" + n).css({borderColor: "red"});
                                                    return false;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actions_reg_nota_credito.php',
                    data: {act: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields, vd: val_dat}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            cancelar();
                        } else if (dat[0] == 1) {
                            alert('Una de las cuentas del Registro de Nota de Credito esta inactiva');
                            loading('hidden');
                        } else if (dat[0] == 3) {
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
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_nota_credito.php?txt=' + '' + '&fecha1=<?php echo $fec1 ?>&fecha2=<?php echo $fec2 ?>';
            }

            function cerrar_ventana() {
                $('#con_clientes').hide();
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo();
            }

            function comparar(obj) {
                if ($('#motivo').val() != '1') {
                    if (fac_id.value != 0) {
                        f = obj.lang;
                        c = $("#cantidadf" + f).val();
                        cf = $("#cantidad" + f).val();
                        if (parseFloat(c) > parseFloat(cf)) {
                            $(obj).val('');
                            $(obj).focus();
                            $(obj).css({borderColor: "red"});
                            $("#precio_total" + f).val('0.00');
                            alert('La Cantidad es mayor a la factura');
                        }
                        calculo();
                    }
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
                var tdsc = 0;
                var tiva = 0;
                var tic = 0;
                var tib = 0;
                var gtot = 0;
                var prop = 0;
                var sub = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        val2 = 0;
                        d = 0;
                        can = 0;
                        unit = 0;
                        vd = 0;
                    } else {
                        ob = $('#iva' + n).val().toUpperCase();
                        d = $('#descuento' + n).val().replace(',', '');
                        can = $('#cantidadf' + n).val().replace(',', '');
                        unit = $('#precio_unitario' + n).val().replace(',', '');
                        vd = (can * unit * d / 100);
                        val = ((can * 1) * (unit * 1)) - (vd * 1);
                        $('#descuent' + n).val(vd.toFixed(dec));
                        $('#lbldescuent' + n).html(vd.toFixed(6));
                        $('#precio_total' + n).val(val.toFixed(dec));
                        $('#lblprecio_total' + n).html(val.toFixed(6));
                    }
                    sub = sub + val;
                    tdsc = (tdsc * 1) + (can * unit * d / 100);
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
                tic = parseFloat($('#total_ice').val().replace(',', ''));
                tib = parseFloat($('#irbpnr').val().replace(',', ''));
                prop = parseFloat($('#propina').val().replace(',', ''));
                $('#lblpropina').html(prop);
                tiva = ((t12 + tic * 1) * 12 / 100);
                gtot = (sub + tiva * 1 + tic * 1 + tib * 1 + prop * 1);
                $('#subtotal12').val(t12.toFixed(dec));
                $('#lblsubtotal12').html(t12.toFixed(6));
                $('#subtotal0').val(t0.toFixed(dec));
                $('#lblsubtotal0').html(t0.toFixed(6));
                $('#subtotalex').val(tex.toFixed(dec));
                $('#lblsubtotalex').html(tex.toFixed(6));
                $('#subtotalno').val(tno.toFixed(dec));
                $('#lblsubtotalno').html(tno.toFixed(6));
                $('#subtotal').val(sub.toFixed(dec));
                $('#lblsubtotal').html(sub.toFixed(6));
                $('#total_ice').val(tic.toFixed(dec));
                $('#lbltotal_ice').html(tic.toFixed(6));
                $('#irbpnr').val(tib.toFixed(dec));
                $('#lblirbpnr').html(tib.toFixed(6));
                $('#total_descuento').val(tdsc.toFixed(dec));
                $('#lbltotal_descuento').html(tdsc.toFixed(6));
                $('#total_iva').val(tiva.toFixed(dec));
                $('#lbltotal_iva').html(tiva.toFixed(6));
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot.toFixed(6));
            }

            function ocultar() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                c = '"';
                if ($('#motivo').val() == '1') {
                    var tr = "<tr>" +
                            "<td><input type='text' size='5'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td>" +
                            "<td><input type='text' size='40' id='descripcion1' lang='1'/>" +
                            "</td>" +
                            "<td><input type='text' size='7' id='cantidadf1' onchange='calculo(), comparar(this)' onkeyup='this.value = this.value.replace(/[^0-9.]/," + c + c + ")'/></td>" +
                            "<td><input type='text' size='7' id='precio_unitario1' style='text-align:right' lang='1' onchange='calculo()' onkeyup='this.value = this.value.replace(/[^0-9.]/," + c + c + ")'/></td>" +
                            "<td><input type='text' size='7' id='descuento1' style='text-align:right' onchange='calculo()' onkeyup='this.value = this.value.replace(/[^0-9.]/," + c + c + ")'/></td>" +
                            "<td>" +
                            "<input type='text' size='7'  id='descuent1'  lang='1'  readonly/>" +
                            "<label id='lbldescuent1' hidden lang='1'></label>" +
                            "</td>" +
                            "<td><input type='text' size='7'  id='iva1' style='text-align:right' lang='1' onchange='calculo()' onblur='this.value = this.value.toUpperCase()'/></td>" +
                            "<td><input type='text' size='10' readonly id='precio_total1'  style='text-align:right' lang='1' readonly />" +
                            "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                            "<td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>" +
                            "</tr>";
                    $('#lista').html(tr);
                    $('#precio_unitario1').val('0');
                    $('#descuento1').val('0');
                    $('#descuent1').val('0');
                    $('#lbldescuent1').html('0');
                    $('#iva1').val('0');
                    $('#precio_total1').val('0.00');
                    $('#lblprecio_total1').html('0');
                    $('#subtotal12').val('0.00');
                    $('#lblsubtotal12').html('0');
                    $('#subtotal0').val('0.00');
                    $('#lblsubtotal0').html('0');
                    $('#subtotalno').val('0.00');
                    $('#lblsubtotalno').html('0');
                    $('#subtotalex').val('0.00');
                    $('#lblsubtotalex').html('0');
                    $('#subtotal').val('0.00');
                    $('#lblsubtotal').html('0');
                    $('#total_descuento').val('0.00');
                    $('#lbltotal_descuento').html('0');
                    $('#total_ice').val('0.00');
                    $('#lbltotal_ice').html('0');
                    $('#total_iva').val('0.00');
                    $('#lbltotal_iva').html('0');
                    $('#irbpnr').val('0.00');
                    $('#lblirbpnr').html('0');
                    $('#propina').val('0.00');
                    $('#lblpropina').html('0');
                    $('#total_valor').val('0.00');
                    $('#lbltotal_valor').html('0');
                    $('.td1').hide();
                    $('#add_row').show();
                } else {
                    $('.td1').show();
                    $('#add_row').hide();
                    if (id == 0) {
                        load_factura($('#num_secuencial').val());
                    }
                }
                calculo();
            }

            function ocultar_mod() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if ($('#motivo').val() == '1') {
                    $('#motivo').attr('disabled', true);
                    $('.td1').hide();
                    $('#add_row').show();
                    while (n < i) {
                        n++;
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('#descripcion' + n).attr('readonly', false);
                            $('#descripcion' + n).attr('size', '70');
                            $('#precio_unitario' + n).attr('readonly', false);
                            $('#iva' + n).attr('readonly', false);
                            $('#descuento' + n).attr('readonly', false);
                        }
                    }
                }
            }

            function load_cliente(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 1, id: obj.value, s: 0},
                        function (dt) {
                            if (dt != 1) {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#nombre').focus();
                                $('#nombre').val('');
                                $('#direccion_cliente').val('');
                                $('#telefono_cliente').val('');
                                $('#email_cliente').val('');
                                $('#cli_id').val('0');
                            }
                        });
            }

            function load_cliente2(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 1, id: obj, s: 1},
                        function (dt) {
                            if (dt == 0) {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#nombre').focus();
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#direccion_cliente').val('');
                                $('#telefono_cliente').val('');
                                $('#email_cliente').val('');
                                $('#cli_id').val('0');
                            } else {
                                dat = dt.split('&');
                                $('#identificacion').val(dat[0]);
                                $('#nombre').val(dat[1]);
                                $('#direccion_cliente').val(dat[2]);
                                $('#telefono_cliente').val(dat[3]);
                                $('#email_cliente').val(dat[4]);
                                $('#cli_id').val(dat[5]);
                            }
                            $('#con_clientes').hide();
                        });
            }


            function load_factura(obj) {
                if (obj.length == 17) {
                    $.post("actions_reg_nota_credito.php", {act: 4, id: obj, x: 0},
                            function (dt) {
                                dat = dt.split('&&');
                                if (dat[0] == '1') {
                                    $('#con_clientes').css('visibility', 'visible');
                                    $('#con_clientes').show();
                                    $('#clientes').html(dat[1]);
                                } else {
                                    alert('Factura no existe porfavor registrela para continuar');
                                    $('#fac_id').val('0');
                                    $('#fecha_emision_comprobante').val('');
                                    $('#identificacion').val('');
                                    $('#nombre').val('');
                                    $('#lista').html('');
                                    $('#cli_id').val('');
                                    $('#add_row').show();
                                    a = '"';
                                    var tr = "<tr>" +
                                            "<td><input type='text' size='5'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td>" +
                                            "<td><input type='text' size='10' id='tipo1'  readonly lang='1'/></td>" +
                                            "<td><input type='text' size='13' id='cuenta1'  readonly lang='1'/>" +
                                            "<input hidden type='text' size='10' id='pln_id1' lang='1'/></td>" +
                                            "<td><input type='text' size='12' id='cod_producto1' readonly lang='1' list='productos' onblur='this.style.width = '100px', load_producto(this)' onfocus='this.style.width = '500px''/>" +
                                            "<input hidden type='text' size='10' id='pro_id1' lang='1' /></td>" +
                                            "<td><input type='text' size='10' id='cod_externo'  readonly lang='1'/></td>" +
                                            "<td><input type='text' size='15' id='descripcion1'  readonly lang='1'/>" +
                                            "</td>" +
                                            "<td><input type='text' size='7' id='inventario1'  readonly lang='1'/></td>" +
                                            "<td><input id='cantidad1' type='text' lang='1' readonly size='7'/></td>" +
                                            "<td><input type='text' size='7' readonly id='cantidadf1' onchange='calculo(), comparar(this)'/></td>" +
                                            "<td><input type='text' size='7' readonly id='precio_unitario1' style='text-align:right' lang='1' onchange='calculo()'/></td>" +
                                            "<td><input type='text' size='7'  readonly id='descuento1' style='text-align:right'/></td>" +
                                            "<td>" +
                                            "<input type='text' size='7'  id='descuent1'  lang='1' readonly  />" +
                                            "<label id='lbldescuent1' hidden lang='1'></label>" +
                                            "</td>" +
                                            "<td><input type='text' size='7'  readonly id='iva1' style='text-align:right' lang='1'/></td>" +
                                            "<td><input type='text' size='10'  id='precio_total1'  style='text-align:right' lang='1' readonly />" +
                                            "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                                            "<td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>" +
                                            "</tr>";
                                    $('#lista').html(tr);
                                    $('#propina').val(parseFloat('0').toFixed(dec));
                                    $('#lblpropina').val('0.00');
                                    $('#subtotal12').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotal12').val('0.00');
                                    $('#subtotal0').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotal0').val('0.00');
                                    $('#subtotalno').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotalno').val('0.00');
                                    $('#subtotalex').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotalex').val('0.00');
                                    $('#total_descuento').val(parseFloat('0').toFixed(dec));
                                    $('#total_descuento').val('0.00');
                                    $('#total_ice').val(parseFloat('0').toFixed(dec));
                                    $('#total_ice').val('0.00');
                                    $('#total_iva').val(parseFloat('0').toFixed(dec));
                                    $('#lbltotal_iva').val('0.00');
                                    $('#irbpnr').val(parseFloat('0').toFixed(dec));
                                    $('#lblirbpnr').val('0.00');
                                    $('#total_valor').val(parseFloat('0').toFixed(dec));
                                    $('#lbltotal_valor').val('0.00');
                                }
                            });
                }
            }

            function load_factura2(obj) {
                $.post("actions_reg_nota_credito.php", {act: 4, id: obj, s: dec, c: dc, x: 1},
                        function (dt) {
                            $('.td1').show();
//                    $('#motivo').val('12');
                            dat = dt.split('&');
                            if (dat[0] != '') {
                                $('#fac_id').val(dat[0]);
                                $('#fecha_emision_comprobante').val(dat[1]);
                                $('#identificacion').val(dat[2]);
                                $('#nombre').val(dat[3]);
                                $('#lista').html(dat[4]);
                                $('#cli_id').val(dat[5]);
                                $('#add_row').hide();
                                $('#propina').val(parseFloat(dat[6]).toFixed(dec));
                                $('#lblpropina').html(dat[6]);
                                $('#subtotal12').val(parseFloat(dat[7]).toFixed(dec));
                                $('#lblsubtotal12').html(dat[7]);
                                $('#subtotal0').val(parseFloat(dat[8]).toFixed(dec));
                                $('#lblsubtotal0').html(dat[8]);
                                $('#subtotalno').val(parseFloat(dat[9]).toFixed(dec));
                                $('#lblsubtotalno').html(dat[9]);
                                $('#subtotalex').val(parseFloat(dat[10]).toFixed(dec));
                                $('#lblsubtotalex').html(dat[10]);
                                $('#total_descuento').val(parseFloat(dat[11]).toFixed(dec));
                                $('#lbltotal_descuento').html(dat[11]);
                                $('#total_ice').val(parseFloat(dat[12]).toFixed(dec));
                                $('#lbltotal_ice').html(dat[12]);
                                $('#total_iva').val(parseFloat(dat[13]).toFixed(dec));
                                $('#lbltotal_iva').html(dat[13]);
                                $('#irbpnr').val(parseFloat(dat[14]).toFixed(dec));
                                $('#lblirbpnr').html(dat[14]);
                                $('#total_valor').val(parseFloat(dat[15]).toFixed(dec));
                                $('#lbltotal_valor').html(dat[15]);
                                $('#con_clientes').hide();
                                calculo();
                            } else {
                                alert('Factura no existe porfavor registrela para continuar');
                                $('#fac_id').val('0');
                                $('#fecha_emision_comprobante').val('');
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#lista').html('');
                                $('#cli_id').val('');
                                $('#add_row').show();
                                a = '"';
                                var tr = "<tr>" +
                                        "<td><input type='text' size='5'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td>" +
                                        "<td><input type='text' size='10' id='producto1'  readonly lang='1'/></td>" +
                                        "<td><input type='text' size='10' id='tipo1'  readonly lang='1'/></td>" +
                                        "<td><input type='text' size='13' id='cuenta1'  readonly lang='1'/>" +
                                        "<input hidden type='text' size='10' id='pln_id1' lang='1'/></td>" +
                                        "<td><input type='text' size='12' id='cod_producto1' readonly lang='1' list='productos' onblur='this.style.width = '100px', load_producto(this)' onfocus='this.style.width = '500px''/>" +
                                        "<input hidden type='text' size='10' id='pro_id1' lang='1' /></td>" +
                                        "<td><input type='text' size='10' id='cod_externo'  readonly lang='1'/></td>" +
                                        "<td><input type='text' size='15' id='descripcion1'  readonly lang='1'/>" +
                                        "</td>" +
                                        "<td><input type='text' size='7' id='inventario1'  readonly lang='1'/></td>" +
                                        "<td><input id='cantidad1' type='text' lang='1' readonly size='7'/></td>" +
                                        "<td><input type='text' size='7' readonly id='cantidadf1' onchange='calculo(), comparar(this)'/></td>" +
                                        "<td><input type='text' size='7' readonly id='precio_unitario1' style='text-align:right' lang='1' onchange='calculo()'/></td>" +
                                        "<td><input type='text' size='7'  readonly id='descuento1' style='text-align:right'/></td>" +
                                        "<td>" +
                                        "<input type='text' size='7'  id='descuent1'  lang='1' readonly  />" +
                                        "<label id='lbldescuent1' hidden lang='1'></label>" +
                                        "</td>" +
                                        "<td><input type='text' size='7'  readonly id='iva1' style='text-align:right' lang='1'/></td>" +
                                        "<td><input type='text' size='10'  id='precio_total1'  style='text-align:right' lang='1' readonly />" +
                                        "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                                        "<td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>" +
                                        "</tr>";
                                $('#lista').html(tr);
                                $('#propina').val(parseFloat('0').toFixed(dec));
                                $('#lblpropina').val('0.00');
                                $('#subtotal12').val(parseFloat('0').toFixed(dec));
                                $('#lblsubtotal12').val('0.00');
                                $('#subtotal0').val(parseFloat('0').toFixed(dec));
                                $('#lblsubtotal0').val('0.00');
                                $('#subtotalno').val(parseFloat('0').toFixed(dec));
                                $('#lblsubtotalno').val('0.00');
                                $('#subtotalex').val(parseFloat('0').toFixed(dec));
                                $('#lblsubtotalex').val('0.00');
                                $('#total_descuento').val(parseFloat('0').toFixed(dec));
                                $('#total_descuento').val('0.00');
                                $('#total_ice').val(parseFloat('0').toFixed(dec));
                                $('#total_ice').val('0.00');
                                $('#total_iva').val(parseFloat('0').toFixed(dec));
                                $('#lbltotal_iva').val('0.00');
                                $('#irbpnr').val(parseFloat('0').toFixed(dec));
                                $('#lblirbpnr').val('0.00');
                                $('#total_valor').val(parseFloat('0').toFixed(dec));
                                $('#lbltotal_valor').val('0.00');
                                $('#con_clientes').hide();
                            }
                            doc_duplicado();
                        });

            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 530) / 2);
                obj.css('left', (wndW - 0.1) / 2);
                objtx.css('top', (wndH - 520) / 2);
                objtx.css('left', (wndW + 700) / 2);
            }


            function num_factura(obj) {
                nfac = obj.value;
                dt = nfac.split('-');
                if (nfac.length != 17 || dt[0].length != 3 || dt[1].length != 3 || dt[2].length != 9) {
                    $(obj).val('');
                    $('fac_id').val('0');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple con la estructura ejem: 000-000-000000000');
                }
            }

            function doc_duplicado() {
                num_doc = $('#num_comprobante').val();
                ruc_pro = $('#identificacion').val();
                if (num_doc.length = 17 && ruc_pro.length > 0) {
                    $.post("actions_reg_nota_credito.php", {act: 2, id: num_doc, data: ruc_pro},
                            function (dt) {
                                dat = dt.split('&');
                                if (dat[0] != '') {
                                    alert('EL numero de Documento y el Ruc del Proveedor \n Ya existen en el Registro de Notas de Credito');
                                    $('#fac_id').val('0');
                                    $('#identificacion').val('');
                                    $('#nombre').val('');
                                    $('#cli_id').val('');
                                    $('#autorizacion').val('');
                                    $('#num_comprobante').val('');
                                    $('#num_secuencial').val('');
                                    $('#fecha_emision_comprobante').val('');
                                    a = '"';
                                    var tr = "<tr>" +
                                            "<td><input type='text' size='3'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td>" +
                                            "<td><input type='text' size='10' id='tipo1'  readonly lang='1'/></td>" +
                                            "<td><input type='text' size='13' id='cuenta1'  readonly lang='1'/>" +
                                            "<input hidden type='text' size='10' id='pln_id1' lang='1'/></td>" +
                                            "<td><input type='text' size='12' id='cod_producto1' readonly lang='1' list='productos' onblur='this.style.width = '100px', load_producto(this)' onfocus='this.style.width = '500px''/>" +
                                            "<input hidden type='text' size='10' id='pro_id1' lang='1' /></td>" +
                                            "<td><input type='text' size='10' id='cod_externo'  readonly lang='1'/></td>" +
                                            "<td><input type='text' size='15' id='descripcion1'  readonly lang='1'/>" +
                                            "</td>" +
                                            "<td><input type='text' size='7' id='inventario1'  readonly lang='1'/></td>" +
                                            "<td><input id='cantidad1' type='text' lang='1' readonly size='7'/></td>" +
                                            "<td><input type='text' size='7' readonly id='cantidadf1' onchange='calculo(), comparar(this)'/></td>" +
                                            "<td><input type='text' size='7' readonly id='precio_unitario1' style='text-align:right' lang='1' onchange='calculo()'/></td>" +
                                            "<td><input type='text' size='7'  readonly id='descuento1' style='text-align:right'/></td>" +
                                            "<td>" +
                                            "<input type='text' size='7'  id='descuent1'  lang='1' readonly  />" +
                                            "<label id='lbldescuent1' hidden lang='1'></label>" +
                                            "</td>" +
                                            "<td><input type='text' size='7'  readonly id='iva1' style='text-align:right' lang='1'/></td>" +
                                            "<td><input type='text' size='10'  id='precio_total1'  style='text-align:right' lang='1' readonly />" +
                                            "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                                            "<td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>" +
                                            "</tr>";
                                    $('#lista').html(tr);
                                    $('#propina').val(parseFloat('0').toFixed(dec));
                                    $('#lblpropina').val('0.00');
                                    $('#subtotal12').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotal12').val('0.00');
                                    $('#subtotal0').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotal0').val('0.00');
                                    $('#subtotalno').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotalno').val('0.00');
                                    $('#subtotalex').val(parseFloat('0').toFixed(dec));
                                    $('#lblsubtotalex').val('0.00');
                                    $('#total_descuento').val(parseFloat('0').toFixed(dec));
                                    $('#total_descuento').val('0.00');
                                    $('#total_ice').val(parseFloat('0').toFixed(dec));
                                    $('#total_ice').val('0.00');
                                    $('#total_iva').val(parseFloat('0').toFixed(dec));
                                    $('#lbltotal_iva').val('0.00');
                                    $('#irbpnr').val(parseFloat('0').toFixed(dec));
                                    $('#lblirbpnr').val('0.00');
                                    $('#total_valor').val(parseFloat('0').toFixed(dec));
                                    $('#lbltotal_valor').val('0.00');
                                }
                            });
                }
            }

            function load_codigo(obj) {
                n = obj.lang;
                $.post("actions_reg_nota_credito.php", {act: 7, id: obj.value},
                        function (dt) {
                            dat = dt.split('&');
                            if (dat[0] != '') {
                                $('#pln_id' + n).val(dat[0]);
                                $('#reg_codigo_cta' + n).val(dat[1]);
                            } else {
                                $('#pln_id' + n).val('');
                                $('#reg_codigo_cta' + n).val('');
                            }
                        });
            }

            function inventario(obj) {
                if (inven == 0) {
                    if (motivo.value != '1') {
                        n = obj.lang;
                        if ($('#inventario' + n).val().trim().length != 0) {
                            if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                                alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                                $(obj).val('');
                                $(obj).focus();
                                $(obj).css({borderColor: "red"});
                                comparar(obj);
                            }
                        }
                    }
                }
                comparar(obj);
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
                width: 230px;
            }
            *{
                font-size:11px; 
            }
            #motivo{
                width:162px; 
            }

        </style>
    </head>
    <body>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" >
                <thead>
                    <tr><th colspan="12" >REGISTRO NOTA DE CREDITO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td>REGISTRO NO:</td>                    
                                <td><input type="text" size="20"  id="num_registro" value="<?php echo $rst[rnc_num_registro] ?>" readonly/></td> 
                            </tr>         
                            <tr>
                                <td>FECHA DE REGISTRO:</td>
                                <td><input type="text" size="15"  id="fecha_registro"  readonly value="<?php echo $rst[rnc_fec_registro] ?>" /><img src="../img/calendar.png" id="im-campo1" /></td>
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="15"  id="fecha_emision"  readonly value="<?php echo $rst[rnc_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo4" /></td>
                                <td>FECHA DE AUTORIZACION:</td>
                                <td><input type="text" size="15"  id="fecha_autorizacion"  readonly value="<?php echo $rst[rnc_fec_autorizacion] ?>" /><img src="../img/calendar.png" id="im-campo2" /></td>
                                <td>FECHA DE CADUCIDAD:</td>
                                <td><input type="text" size="15"  id="fecha_caducidad"  readonly value="<?php echo $rst[rnc_fec_caducidad] ?>" /><img src="../img/calendar.png" id="im-campo3" /></td>

                            </tr>         
                            <tr>
                                <td>NOTA DE CREDITO NO:</td>                    
                                <td><input type="text" size="25"  id="num_comprobante" maxlength="17" value="<?php echo $rst[rnc_numero] ?>" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" onchange="doc_duplicado(), num_factura(this)"/></td> 
                                <td>AUTORIZACION NO:</td>                    
                                <td><input type="text" size="35"  id="autorizacion" maxlength="49" value="<?php echo $rst[rnc_autorizacion] ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '')"/></td> 
                                <td>FACTURA NO:</td>                    
                                <td><input type="text" size="25"  id="num_secuencial" value="<?php echo $rst[rnc_num_comp_modifica] ?>"  onblur="num_factura(this)" maxlength="17" onchange="load_factura(this.value)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/> 
                                    <input type="hidden" id="fac_id" value="<?php echo $rst[reg_id] ?>"/></td> 
                                <td>FECHA EMISION FACTURA:</td>
                                <td><input type="text" size="15"  id="fecha_emision_comprobante" readonly value="<?php echo $rst[rnc_fecha_emi_comp] ?>" /></td>
                            </tr>         
                            <tr>
                                <td>CI/RUC :</td>
                                <td><input type="text" size="20" readonly maxlength="13" id="identificacion" value="<?php echo $rst[rnc_identificacion] ?>" onchange="load_cliente(this)" /></td>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="28" readonly id="nombre" value="<?php echo $rst[rnc_nombre] ?>"  />
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"  /></td>
                                <td>TRANSACCION:</td>                
                                <td> <select id="motivo" onchange="ocultar()">
                                        <option value="6">DEVOLUCION DE COMPRA</option>
                                        <option value="7">ANULACION DE COMPRA</option>
                                        <?php
                                        if ($rst[trs_id] == 1 || $id == 0) {
                                            ?>
                                            <option value="1">VARIOS</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            <script>
                                var t = '<?php echo $rst[trs_id] ?>';
                                $('#motivo').val(t);
                            </script>
                            <td>MOTIVO:</td>
                            <td><input type="text" size="25"  id="descripcion_motivo" value="<?php echo $rst[rnc_motivo] ?>" onblur="this.value = this.value.toUpperCase()"/></td>
                </tr>
            </table>
        </td> 
    </tr>
    <tr>
        <td>
            <table id="detalle">
                <tr id="head">
                <thead id="tabla">
                <th style="width: 5px;">Item</th>
                <th class="td1">Tipo</th>
                <th class="td1">Cuenta Contable</th>
                <th class="td1">Cod.Empresa</th>
                <th class="td1">Cod.Externo</th>
                <th>Descripcion</th>
                <th class="td1">Inventario</th>   
                <th class="td1">Cant. Fac</th>   
                <th>Cantidad</th>   
                <th>V.Unitario</th>
                <th>Descuento%</th>
                <th>Descuento$</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Accion</th>
                </thead>  
                <!------------------------------------->
                <tbody id="lista">
                    <?PHP
                    if (empty($cns)) {
                        ?>
                        <tr>
                            <td><input type="text" size="3"  id="item1" class="itm"  lang="1" value="1" readonly  style="text-align:right" /></td>  
                            <td><input type="text" size="10" id="tipo1"  readonly value="" lang="1"/></td>   
                            <td><input type="text" size="13" id="cuenta1"  readonly value="" lang="1"/>
                                <input hidden type="text" size="10" id="pln_id1" lang="1"/></td>
                            <td class="td1"><input type="text" size="10" id="cod_producto1"  readonly value="" lang="1" list="productos" onblur="this.style.width = '100px', load_producto(this)" onfocus="this.style.width = '500px'"/>
                                <input hidden type="text" size="10" id="pro_id1" lang="1"/></td>
                            <td><input type="text" size="10" id="cod_externo"  readonly value="" lang="1"/></td> 
                            <td><input type="text" size="15" id="descripcion1" readonly value="" lang="1"/></td>  
                            <td><input id="cantidad1" type="text" lang="1" readonly value="" size="7"/></td>
                            <td class="td1"><input type="text" size="7" id="inventario1"  readonly value="" lang="1"/></td>   
                            <td><input type="text" size="7"  id="cantidadf1" readonly onchange="inventario(this)"  value="" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" style="text-align:right" lang="1" /></td>
                            <td><input type="text" size="7"  id="precio_unitario1"  readonly value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onchange="calculo()"/></td>                  
                            <td><input type="text" size="7"  id="descuento1"  readonly  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onchange="calculo()"/></td>                  
                            <td>
                                <input type="text" size="7"  id="descuent1" readonly lang="1" readonly  />
                                <label id="lbldescuent1" hidden lang="1"></label>
                            </td>
                            <td><input type="text" size="7"  id="iva1"  readonly style="text-align:right" lang="1" onblur="calculo(), this.value = this.value.toUpperCase()" /></td>                  
                            <td><input type="text" size="7"  id="precio_total1"  value="" style="text-align:right" lang="1" readonly />                  
                                <label hidden id="lblprecio_total1" lang="1"></label></td>                  
                            <td onclick = "elimina_fila(this)" ><img class = "auxBtn" width="12px" src = "../img/del_reg.png"/></td>
                        </tr>  
                        <?PHP
                    } else {
                        $n = 0;
                        while ($rst2 = pg_fetch_array($cns)) {
                            $n++;
                            $rst_dfc = pg_fetch_array($Reg_nota_credito->lista_detall_factura($rst[reg_id], $rst2[pro_id]));
                            $rst_s = pg_fetch_array($Reg_nota_credito->suma_prod_nota_credito($rst[reg_id], $rst2[pro_id]));
                            $rst_rg = pg_fetch_array($Reg_nota_credito->lista_un_regfactura_id($rst[reg_id]));
                            if (!empty($rst_rg[reg_num_ingreso])) {
                                $fra = "and m.bod_id=1";
                                $rst_inv = pg_fetch_array($Reg_nota_credito->total_ingreso_egreso_fact($rst2[pro_id], $fra));
                                $inv = $rst_inv[ingreso]+ $rst2[drc_cantidad] - $rst_inv[egreso];
                            } else {
                                $inv = '';
                            }
                            if (empty($rst_s)) {
                                $entr = $rst_dfc[det_cantidad];
                            } else {
                                $entr = $rst_dfc[det_cantidad] - $rst_s[sum] + $rst2[drc_cantidad];
                            }
                            switch ($rst_dfc[det_tipo]) {
                                case 26:
                                    $nom_tipo = 'PRODUCTO TERMINADO';
                                    break;
                                case 69:
                                    $nom_tipo = 'MATERIA PRIMA';
                                    break;
                                case 79:
                                    $nom_tipo = 'SERVICIO';
                                    break;
                                case 80:
                                    $nom_tipo = 'OTROS';
                                    break;
                            }
                            if ($rst[rnc_estado] == 5 || $rst[rnc_estado] == 4) {
                                $rst2[pln_id] = $rst_dfc[pln_id];
                                $rst2[drc_codigo_cta] = $rst_dfc[reg_codigo_cta];
                            }
                            ?>
                            <tr>
                                <td><input type="text" size="5"  id="item<?php echo $n ?>" class="itm"  lang="<?php echo $n ?>" value="<?php echo $n ?>" readonly  style="text-align:right" /></td>    
                                <td class="td1">
                                    <input type="text" size="13" id="nom_tipo<?php echo $n ?>"  readonly value="<?php echo $nom_tipo ?>" lang="1"/>
                                    <input type="hidden" size="13" id="tipo<?php echo $n ?>"  readonly value="<?php echo $rst_dfc[det_tipo] ?>" lang="1"/>
                                </td> 
                                <td><input type="text" size="13" id="cuenta<?php echo $n ?>"  readonly value="<?php echo $rst2[drc_codigo_cta] ?>" lang="<?php echo $n ?>"/>
                                    <input hidden type="text" size="10" id="pln_id<?php echo $n ?>" lang="<?php echo $n ?>" value="<?php echo $rst2[pln_id] ?>"/></td>
                                <td class="td1"><input type="text" size="10" id="cod_producto<?php echo $n ?>"  readonly value="<?php echo $rst2[drc_codigo] ?>" lang="<?php echo $n ?>" list="productos" onblur="this.style.width = '100px', load_producto(this)" onfocus="this.style.width = '500px'"/>
                                    <input type="hidden" size="10" id="pro_id<?php echo $n ?>" value="<?php echo $rst2[pro_id] ?>" lang="<?php echo $n ?>"/>
                                </td>
                                <td class="td1"><input type="text" size="10" id="cod_externo<?php echo $n ?>"  readonly value="<?php echo $rst2[drc_cod_aux] ?>" lang="1"/></td> 
                                <td><input type="text" size="15" id="descripcion<?php echo $n ?>"  readonly value="<?php echo $rst2[drc_descripcion] ?>" lang="<?php echo $n ?>"/></td>  
                                <td class="td1"><input type="text" size="7" id="inventario<?php echo $n ?>"  readonly value="<?php echo $inv ?>" lang="<?php echo $n ?>"/></td>  
                                <td class="td1"><input id="cantidad<?php echo $n ?>" type="text" lang="<?php echo $n ?>" readonly value="<?php echo str_replace(',', '', number_format($entr, $dc)) ?>" size="8"/></td>
                                <td><input type="text" size="8"  id="cantidadf<?php echo $n ?>" onchange="inventario(this)"  value="<?php echo str_replace(',', '', number_format($rst2[drc_cantidad], $dc)) ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" style="text-align:right" lang="<?php echo $n ?>" /></td>
                                <td><input type="text" size="8"  id="precio_unitario<?php echo $n ?>" readonly value="<?php echo str_replace(',', '', number_format($rst2[drc_precio_unit], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onchange="calculo()"/></td>                  
                                <td><input type="text" size="8"  id="descuento<?php echo $n ?>" readonly value="<?php echo str_replace(',', '', number_format($rst2[drc_porcentaje_descuento], $dec)) ?>"  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onchange="calculo()"/></td>                  
                                <td>
                                    <input type="text" size="8"  id="descuent<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst2[drc_val_descuento], $dec)) ?>" lang="<?php echo $n ?>"readonly  />
                                    <label id="lbldescuent<?php echo $n ?>" hidden lang="<?php echo $n ?>"><?php echo $rst2[drc_val_descuento] ?></label>
                                </td>
                                <td><input type="text" size="8"  id="iva<?php echo $n ?>"  readonly value="<?php echo $rst2[drc_iva] ?>" style="text-align:right" lang="<?php echo $n ?>"onblur="calculo(), this.value = this.value.toUpperCase()" /></td>                  
                                <td><input type="text" size="10"  id="precio_total<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst2[drc_precio_total], $dec)) ?>" style="text-align:right" lang="<?php echo $n ?>"readonly />                  
                                    <label hidden id="lblprecio_total<?php echo $n ?>" lang="<?php echo $n ?>"><?php echo $rst2[drc_precio_total] ?></label></td>                  
                                <td onclick = "elimina_fila(this)" ><img class = "auxBtn" width="12px" src = "../img/del_reg.png"/></td>
                            </tr>  
                            <?PHP
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Subtotal 12%:</td>
                        <td class="sbtls" ><input style="text-align:right" type="text" size="10" id="subtotal12"  value="<?php echo str_replace(',', '', number_format($rst[rnc_subtotal12], $dec)) ?>" readonly/>
                            <label hidden id="lblsubtotal12"><?php echo $rst[rnc_subtotal12] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Subtotal 0%:</td>
                        <td class="sbtls" ><input type="text" size="10"  id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst[rnc_subtotal0], $dec)) ?>" style="text-align:right" readonly />
                            <label hidden id="lblsubtotal0"><?php echo $rst[rnc_subtotal0] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Subtotal No Objeto Iva:</td>
                        <td class="sbtls" ><input type="text" size="10"   id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst[rnc_subtotal_no_iva], $dec)) ?>" style="text-align:right" readonly/>
                            <label hidden id="lblsubtotalno"><?php echo $rst[rnc_subtotal_no_iva] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Subtotal Excento Iva:</td>
                        <td class="sbtls" ><input type="text" size="10"  id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst[rnc_subtotal_ex_iva], $dec)) ?>" style="text-align:right" readonly/>
                            <label hidden id="lblsubtotalex"><?php echo $rst[rnc_subtotal_ex_iva] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Subtotal Sin Impuestos:</td>
                        <td class="sbtls" ><input type="text" size="10"  id="subtotal" value="<?php echo str_replace(',', '', number_format($rst[rnc_subtotal], $dec)) ?>" style="text-align:right" readonly/>
                            <label hidden id="lblsubtotal"><?php echo $rst[rnc_subtotal] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Total Descuento:</td>
                        <td class="sbtls" ><input type="text" size="10" id="total_descuento"   value="<?php echo str_replace(',', '', number_format($rst[rnc_total_descuento], $dec)) ?>" style="text-align:right" readonly/>
                            <label hidden id="lbltotal_descuento"><?php echo $rst[rnc_total_descuento] ?> </label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">ICE:</td>
                        <td class="sbtls" ><input type="text" size="13" id="total_ice"  value="<?php echo str_replace(',', '', number_format($rst[rnc_total_ice], $dec)) ?>" style="text-align:right" onchange="calculo()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                            <label hidden id="lbltotal_ice"><?php echo $rst[rnc_total_ice] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">IVA 12%:</td>
                        <td class="sbtls" ><input type="text" size="10" id="total_iva"  value="<?php echo str_replace(',', '', number_format($rst[rnc_total_iva], $dec)) ?>" style="text-align:right" readonly/>
                            <label hidden id="lbltotal_iva"><?php echo $rst[rnc_total_iva] ?> </label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">IRBPNR:</td>
                        <td class="sbtls" ><input type="text" size="13" id="irbpnr"  value="<?php echo str_replace(',', '', number_format($rst[rnc_irbpnr], $dec)) ?>" style="text-align:right" onchange="calculo()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                            <label hidden id="lblirbpnr"><?php echo $rst[rnc_irbpnr] ?> </label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">PROPINA:</td>
                        <td class="sbtls" ><input type="text" size="13" id="propina"  value="<?php echo str_replace(',', '', number_format($rst[rnc_total_propina], $dec)) ?>" style="text-align:right" onchange="calculo()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                            <label hidden id="lblpropina"><?php echo $rst[rnc_total_propina] ?></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="td1" colspan="6">
                        <td colspan="<?php echo $col ?>" align="right">Total:</td>
                        <td class="sbtls"><input type="text" size="10" id="total_valor"  value="<?php echo str_replace(',', '', number_format($rst[rnc_total_valor], $dec)) ?>"  style="text-align:right" readonly/>
                            <label hidden id="lbltotal_valor"><?php echo $rst[rnc_total_valor] ?></label></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tfoot>
        <tr>
            <td colspan="2">
                <?PHP
                if ($det != 1) {
                    ?> 
                    <button id="guardar" onclick="frm_save.lang = 1">Guardar</button>   
                    <?PHP
                }
                ?>
                <button id="cancelar" >Cancelar</button>   
            </td>
        </tr>
    </tfoot>
    <!------------------------------------->
</table>
</form>
</body>
</html>  

<datalist id="cuentas">
    <?php
    $cns_ctas = $Reg_nota_credito->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>