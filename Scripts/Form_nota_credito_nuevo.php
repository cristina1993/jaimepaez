<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_notacredito_nuevo.php';
$Clase_nota_Credito_nuevo = new Clase_nota_Credito_nuevo();
if ($pto_emi > 99) {
    $ems = $pto_emi;
} else if ($pto_emi < 100 && $pto_emi > 9) {
    $ems = '0' . $pto_emi;
} else {
    $ems = '00' . $pto_emi;
}
if ($inv5 == 0) {
    $hidden = '';
    $col = '8';
} else {
    $hidden = 'hidden';
    $col = '7';
}
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst_not = pg_fetch_array($Clase_nota_Credito_nuevo->lista_una_nota_credito($id));
    $comprobante = $rst_not[ncr_numero];
    $cns = $Clase_nota_Credito_nuevo->lista_detalle_nota_credito($id);
    $vnd_id = $rst_not[vnd_id];
    $det = 1;
} else {
    $det = 0;
    $id = 0;
    $rst_det[fac_id] = '0';
    $rst_sec = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_nota_credito($emisor));
    if (empty($rst_sec)) {
        $sec = $rst_mod[emi_sec_notcred];
    } else {
        $dat = explode('-', $rst_sec[ncr_numero]);
        $sec = $dat[2] + 1;
    }
    if ($sec >= 0 && $sec < 10) {
        $tx = '00000000';
    } else if ($sec >= 10 && $sec < 100) {
        $tx = '0000000';
    } else if ($sec >= 100 && $sec < 1000) {
        $tx = '000000';
    } else if ($sec >= 1000 && $sec < 10000) {
        $tx = '00000';
    } else if ($sec >= 10000 && $sec < 100000) {
        $tx = '0000';
    } else if ($sec >= 100000 && $sec < 1000000) {
        $tx = '000';
    } else if ($sec >= 1000000 && $sec < 10000000) {
        $tx = '00';
    } else if ($sec >= 10000000 && $sec < 100000000) {
        $tx = '0';
    } else if ($sec >= 100000000 && $sec < 1000000000) {
        $tx = '';
    }
    $comprobante = $ems . '-001-' . $tx . $sec;
    $rst_not[ncr_fecha_emision] = date('Y-m-d');
    $rst_not[ncr_fecha_emi_comp] = date('Y-m-d');
    $rst_ven = pg_fetch_array($Clase_nota_Credito_nuevo->lista_vendedor(strtoupper($rst_user[usu_person])));
    $vnd_id = $rst_ven[vnd_id];
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
            var usu = '<?php echo $emisor ?>';
            var num = '<?php echo $num_not_credito ?>';
            var det = '<?php echo $det ?>';
            var dec = '<?php echo $dec ?>';
            var dc = '<?php echo $dc ?>';
            var inven = '<?php echo $inv5 ?>';
            var asiento = '<?php echo $asi ?>';
            var vendedor = '<?php echo $vnd_id ?>';
            var ctr_inv = '<?php echo $ctr_inv ?>';
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
                        var tr = $('#detalle').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#descripcion' + a).val().length != 0) {
                            if (this.lang == 0) {
                                clona_fila($('#detalle'));
                            }
                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_emision_comprobante", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (det != 0) {
                    calculo();
                } else {
                    ocultar();
                    calculo();
                }
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
                var data = Array(
                        cli_id.value,
                        usu,
                        num_comprobante.value,
                        descripcion_motivo.value,
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value.toUpperCase(),
                        email_cliente.value,
                        direccion_cliente.value,
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
                        telefono_cliente.value,
                        $('#lbltotal_valor').html().replace(',', ''),
                        $('#lblpropina').html().replace(',', ''),
                        motivo.value,
                        vendedor,
                        fac_id.value,
                        $('#lblsubtotal').html().replace(',', '')
                        );
                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#cantidadf' + n).val() != null) {
                        if (motivo.value == '1') {
                            cod_producto = '';
                            pro_id = '0';
                        } else {
                            cod_producto = $("#cod_producto" + n).val();
                            pro_id = $("#pro_id" + n).val();
                        }
                        cantidad = $("#cantidadf" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val().replace(',', '');
                        precio_unitario = $("#precio_unitario" + n).val().replace(',', '');
                        descuento = $("#descuento" + n).val().replace(',', '');
                        descuent = $("#lbldescuent" + n).html().replace(',', '');
                        precio_total = $("#lblprecio_total" + n).html().replace(',', '');
                        iva = $("#iva" + n).val().replace(',', '');
                        valor_tot = $("#precio_total" + n).val().replace(',', '');
                        ice = $("#lblice" + n).html().replace(',', '');
                        irbpnr = $("#lblirbp" + n).html().replace(',', '');
                        irbpnr_p = $("#irbp_p" + n).val().replace(',', '');
                        ic_p = $('#ice_p' + n).val();
                        ic_cod = $('#ice_cod' + n).val();
                        data1.push(
                                pro_id + '&' +
                                cod_producto + '&' +
                                '' + '&' +
                                cantidad + '&' +
                                descripcion + '&' +
                                precio_unitario + '&' +
                                descuento + '&' +
                                descuent + '&' +
                                precio_total + '&' +
                                iva + '&' +
                                ice + '&' +
                                irbpnr + '&' +
                                precio_unitario + '&' +
                                valor_tot + '&' +
                                ic_p + '&' +
                                ic_cod + '&' +
                                irbpnr_p);
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
//                        amb = $("#ambiente").val();
//                        if (amb == 0) {
//                            secu = $("#num_comprobante").val();
//                            sec = secu.split('-');
//                            ns = sec[2];
//                            nsec = parseInt(ns);
//
//                            if (fecha_emision.value < fec_val_desde.value || fecha_emision.value > fec_val_hasta.value) {
//                                alert('Revisar configuracion autorizacion SRI');
//                                return false;
//                            } else if (nsec < num_val_desde.value || nsec > num_val_hasta.value) {
//                                alert('Revisar configuracion autorizacion SRI');
//                                return false;
//                            }
//                        } else
                        if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            return false;
                        } else if (identificacion.value.length == 0) {
                            $("#identificacion").css({borderColor: "red"});
                            $("#identificacion").focus();
                            return false;
                        } else if (nombre.value.length == 0) {
                            $("#nombre").css({borderColor: "red"});
                            $("#nombre").focus();
                            return false;
                        } else if (direccion_cliente.value.length == 0) {
                            $("#direccion_cliente").css({borderColor: "red"});
                            $("#direccion_cliente").focus();
                            return false;
                        } else if (telefono_cliente.value.length == 0) {
                            $("#telefono_cliente").css({borderColor: "red"});
                            $("#telefono_cliente").focus();
                            return false;
                        } else if (email_cliente.value.length == 0) {
                            $("#email_cliente").css({borderColor: "red"});
                            $("#email_cliente").focus();
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
                                    if (fac_id.value != 0) {
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
                                    }
                                }
                            }
                        }
                        if (vendedor == '') {
                            alert('El usuario no es vendedor');
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_nota_credito_nuevo.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields, x: inven}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            if (asiento == 0) {
                                asientos(dat[1]);
                            } else {
                                cancelar();
                            }
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Factura ya existe \n Debe hacer otra factura con otro Secuencial');
                            loading('hidden');
                        } else if (dat[0] == 2) {
                            alert('Una de las cuentas de la factura esta inactiva');
                            loading('hidden');
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//====================================================================================================================================================

            function cancelar() {
                t = '<?php echo $_GET[txt] ?>';
                d = '<?php echo $_GET[fecha1] ?>';
                h = '<?php echo $_GET[fecha2] ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_credito.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos
                parent.document.getElementById('contenedor2').rows = "*,0%";

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
                if (fac_id.value != 0) {
                    f = obj.lang;
                    c = $("#cantidadf" + f).val();
                    cf = $("#cantidad" + f).val();
                    if (parseFloat(c) > parseFloat(cf)) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        $("#precio_total" + f).val('0.00');
                        calculo();
                        alert('La Cantidad es mayor a la factura');
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
                        ic = 0;
                        ib = 0;
                        pt = 0;
                        vd = 0;
                        pic = 0;
                        pib = 0;
                    } else {
                        ob = $('#iva' + n).val().toUpperCase();
                        d = $('#descuento' + n).val().replace(',', '');
                        can = $('#cantidadf' + n).val().replace(',', '');
                        unit = $('#precio_unitario' + n).val().replace(',', '');
                        vd = (can * unit * d / 100);
                        val = ((can * 1) * (unit * 1)) - (vd * 1);
                        ic = $('#ice_p' + n).val().replace(',', '');
                        ib = $('#irbp_p' + n).val().replace(',', '');
                        pic = (val * 1) * (ic * 1) / 100;
//                        val2 = val * 1 + pic * 1;
                        pib = (can * 1) * (ib * 1);
                        $('#descuent' + n).val(vd.toFixed(dec));
                        $('#lbldescuent' + n).html(vd.toFixed(6));
                        $('#precio_total' + n).val(val.toFixed(dec));
                        $('#lblprecio_total' + n).html(val.toFixed(6));
                        $('#ice' + n).val(pic.toFixed(dec));
                        $('#lblice' + n).html(pic.toFixed(6));
                        $('#irbp' + n).val(pib.toFixed(dec));
                        $('#lblirbp' + n).html(pib.toFixed(6));
                    }

                    tdsc = (tdsc * 1) + (can * unit * d / 100);
                    tic = (tic * 1) + pic;
                    tib = (tib * 1) + pib;

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
                sub = t12 + t0 + tex + tno;
                tiva = ((t12 + tic) * 12 / 100);
                prop = $('#propina').val().replace(',', '');
                $('#lblpropina').html(prop);

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
                if ($('#motivo').val() == '1') {
                    while (n < i) {
                        n++;
                        it = $('#item' + n).val();
                        if (it != null) {
                            if (id == 0) {
                                $('#cod_producto' + n).val('');
                                $('#cantidad' + n).val('');
                                $('#pro_id' + n).val('');
                                $('#descripcion' + n).val('');
                                $('#cantidadf' + n).val('');
                                $('#precio_unitario' + n).val('0');
                                $('#descuento' + n).val('0');
                                $('#descuent' + n).val('0');
                                $('#inventario' + n).val('0');
                                $('#iva' + n).val('0');
                                $('#ice' + n).val('0');
                                $('#irbp' + n).val('0');
                            }
                            $('.td1').hide();
                            $('#descripcion' + n).attr('readonly', false);
                            $('#precio_unitario' + n).attr('readonly', false);
                            $('#descuento' + n).attr('readonly', false);
                            $('#iva' + n).attr('readonly', false);
                            $('#ice' + n).attr('readonly', false);
                            $('#irbp' + n).attr('readonly', false);
//                           
                        }
                    }
                    calculo();
                } else {
                    while (n < i) {
                        n++;
                        it = $('#item' + n).val();
                        if (it != null) {
                            $('.td1').show();
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

            function load_producto(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 3, id: obj.value, x: inven, ctr_inv: ctr_inv, emi: usu},
                        function (dt) {
                            dat = dt.split('&');
                            $('#pro_id' + obj.lang).val(dat[0]);
                            $('#cod_producto' + obj.lang).val(dat[1]);
                            $('#descripcion' + obj.lang).val(dat[2]);
                            $('#descuento' + obj.lang).val('0.00');
                            $('#descuent' + obj.lang).val('0.00');
                            $('#precio_total' + obj.lang).val('0.00');
                            if (dat[3] == '') {
                                $('#precio_unitario' + obj.lang).val('0.00');
                            } else {
                                $('#precio_unitario' + obj.lang).val(dat[3]);
                            }
                            if (dat[6] == '') {
                                $('#inventario' + obj.lang).val('0.00');
                            } else {
                                $('#inventario' + obj.lang).val(dat[6]);

                            }
                            if (dat[8] == '') {
                                $('#iva' + obj.lang).val('0');
                            } else {
                                $('#iva' + obj.lang).val(dat[8]);

                            }
                            if (dat[10] == '') {
                                $('#ice' + obj.lang).val('0.00');
                                $('#ice_p' + obj.lang).val('0');
                            } else {
                                $('#ice' + obj.lang).val('0');
                                $('#ice_p' + obj.lang).val(parseFloat(dat[10]).toFixed(dc));
                            }

                            if (dat[12].length == '0') {
                                $('#ice_cod' + obj.lang).val('0');
                            } else {
                                $('#ice_cod' + obj.lang).val(dat[12]);
                            }

                            if (dat[11] == '') {
                                $('#irbp' + obj.lang).val('0.00');
                                $('#irbp_p' + obj.lang).val('0');
                            } else {
                                $('#irbp_p' + obj.lang).val(dat[11]);
                                $('#irbp' + obj.lang).val('0');
                            }
                            calculo();
                        });
            }

            function load_factura(obj) {
                $.post("actions_nota_credito_nuevo.php", {act: 4, id: obj.value, x: inven, s: dec, c: dc, ctr_inv: ctr_inv, emi: usu},
                        function (dt) {
                            dat = dt.split('&');
                            if (dat[0] != '') {
                                $('#fac_id').val(dat[0]);
                                $('#fecha_emision_comprobante').val(dat[1]);
                                $('#identificacion').val(dat[2]);
                                $('#nombre').val(dat[3]);
                                $('#direccion_cliente').val(dat[4]);
                                $('#telefono_cliente').val(dat[5]);
                                $('#email_cliente').val(dat[6]);
                                $('#cli_id').val(dat[7]);
                                $('#add_row').hide();
                                $('#identificacion').attr('readonly', true);
                                $('#nombre').attr('readonly', true);
                                $('#direccion_cliente').attr('readonly', true);
                                $('#telefono_cliente').attr('readonly', true);
                                $('#email_cliente').attr('readonly', true);
                                $('#propina').val(parseFloat(dat[8]).toFixed(dec));
                                $('#lblpropina').html(dat[8]);

                                $('#subtotal12').val(parseFloat(dat[9]).toFixed(dec));
                                $('#lblsubtotal12').html(dat[9]);

                                $('#subtotal0').val(parseFloat(dat[10]).toFixed(dec));
                                $('#lblsubtotal0').html(dat[10]);

                                $('#subtotalno').val(parseFloat(dat[11]).toFixed(dec));
                                $('#lblsubtotalno').html(dat[11]);

                                $('#subtotalex').val(parseFloat(dat[12]).toFixed(dec));
                                $('#lblsubtotalex').html(dat[12]);

                                $('#total_descuento').val(parseFloat(dat[13]).toFixed(dec));
                                $('#lbltotal_descuento').html(dat[13]);

                                $('#total_ice').val(parseFloat(dat[14]).toFixed(dec));
                                $('#lbltotal_ice').html(dat[14]);

                                $('#total_iva').val(parseFloat(dat[15]).toFixed(dec));
                                $('#lbltotal_iva').html(dat[15]);

                                $('#irbpnr').val(parseFloat(dat[16]).toFixed(dec));
                                $('#lblirbpnr').html(dat[16]);

                                $('#total_valor').val(parseFloat(dat[17]).toFixed(dec));
                                $('#lbltotal_valor').html(dat[17]);

                                $('#subtotal').val(parseFloat(dat[18]).toFixed(dec));
                                $('#lblsubtotal').html(dat[18]);

                                $.post("actions_nota_credito_nuevo.php", {act: 4, id: obj.value, x: inven, s: dec, c: dc, ctr_inv: ctr_inv, emi: usu, data: 1},
                                        function (dt) {
                                            $('#lista').html(dt);
                                        });

                            } else {
                                $('#fac_id').val('0');
                                $('#fecha_emision_comprobante').val('<?php echo $rst_not[ncr_fecha_emi_comp] ?>');
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#direccion_cliente').val('');
                                $('#telefono_cliente').val('');
                                $('#email_cliente').val('');
                                $('#lista').html('');
                                $('#cli_id').val('');
                                $('#add_row').show();
                                $('#identificacion').attr('readonly', false);
                                $('#nombre').attr('readonly', false);
                                $('#direccion_cliente').attr('readonly', false);
                                $('#telefono_cliente').attr('readonly', false);
                                $('#email_cliente').attr('readonly', false);
                                a = '"';
                                var tr = "<tr>" +
                                        "<td><input type=text' size='8'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td> " +
                                        "<td id='codigo1'><input type='text' size='20' id='cod_producto1'  value='' lang='1' list='productos' onblur='this.style.width =" + a + "100px" + a + ",load_producto(this)' onfocus='this.style.width =" + a + "500px" + a + "'/>" +
                                        "<input hidden type='text' size='10' id='pro_id1' lang='1'/></td>" +
                                        "<td><input type='text' size='35' id='descripcion1'  value='' lang='1' readonly/></td>" +
                                        "<td><input id='cantidad1' type='text' lang='1' readonly='' value='' size='8'/></td>" +
                                        "<td <?php echo $hidden ?>><input id='inventario1' <?php echo $hidden ?> type='text' lang='1' readonly='' value='' size='8'/></td>" +
                                        "<td><input type='text' size='7'  id='cantidadf1' onchange='calculo(),comparar(this)'  value='' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' style='text-align:right' lang='1' /></td>" +
                                        "<td><input type='text' size='7'  id='precio_unitario1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, " + a + a + ")' lang='1' onchange='calculo()'/></td>" +
                                        "<td><input type='text' size='7'  id='descuento1'  value=''  style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, " + a + a + ")' lang='1' onchange='calculo()' readonly/></td> " +
                                        "<td>" +
                                        "<input type='text' size='7'  id='descuent1'  value='' lang='1' readonly  />" +
                                        "<label id='lbldescuent1' hidden lang='1'></label>" +
                                        "</td>" +
                                        "<td><input type='text' size='7'  id='iva1'  value='' style='text-align:right' lang='1' onblur='calculo(), this.value = this.value.toUpperCase()' readonly/></td>" +
                                        "<td hidden><input type='text' size='7'  id='ice_p1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' lang='1' onblur='calculo()' /></td>" +
                                        "<td hidden><input type='text' id='ice1' size='5' value='' readonly lang='1'/>" +
                                        "<label hidden id='lblice1' lang='1'></label>" +
                                        "<input type='hidden' id='ice_cod1' size='5' value='' readonly lang='1'/></td>" +
                                        "<td hidden><input type='text' size='7'  id='irbp_p1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' lang='1' onblur='calculo()'/></td>" +
                                        "<td hidden><input type='text' size='7'  id='irbp1'  value='' style='text-align:right' lang='1' readonly/>" +
                                        "<label hidden id='lblirbp1' lang='1'></label></td>" +
                                        "<td><input type='text' size='10'  id='precio_total1'  value='' style='text-align:right' lang='1' readonly/>" +
                                        "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                                        "</td>" +
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

                                $('#subtotal').val(parseFloat('0').toFixed(dec));
                                $('#lblsubtotal').val('0.00');

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
                calculo();
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

            function asientos(id1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 1, id: id1, data: num_comprobante.value, x: id, emi: usu},
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    }
                });
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
                    $('#fac_id').val('0');
                    $('#fecha_emision_comprobante').val('<?php echo $rst_not[ncr_fecha_emi_comp] ?>');
                    $('#identificacion').val('');
                    $('#nombre').val('');
                    $('#direccion_cliente').val('');
                    $('#telefono_cliente').val('');
                    $('#email_cliente').val('');
                    $('#lista').html('');
                    $('#cli_id').val('');
                    $('#add_row').show();
                    $('#identificacion').attr('readonly', true);
                    $('#nombre').attr('readonly', true);
                    $('#direccion_cliente').attr('readonly', true);
                    $('#telefono_cliente').attr('readonly', true);
                    $('#email_cliente').attr('readonly', true);
                    a = '"';
                    var tr = "<tr>" +
                            "<td><input type=text' size='8'  id='item1' class='itm'  lang='1' value='1' readonly  style='text-align:right' /></td> " +
                            "<td id='codigo1'><input type='text' size='20' id='cod_producto1'  value='' lang='1' list='productos' onblur='this.style.width =" + a + "100px" + a + ",load_producto(this)' onfocus='this.style.width =" + a + "500px" + a + "'/>" +
                            "<input hidden type='text' size='10' id='pro_id1' lang='1'/></td>" +
                            "<td><input type='text' size='35' id='descripcion1'  value='' lang='1' readonly/></td>" +
                            "<td><input id='cantidad1' type='text' lang='1' readonly='' value='' size='8'/></td>" +
                            "<td <?php echo $hidden ?>><input id='inventario1' <?php echo $hidden ?> type='text' lang='1' readonly='' value='' size='8'/></td>" +
                            "<td><input type='text' size='7'  id='cantidadf1' onchange='calculo(),comparar(this)'  value='' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' style='text-align:right' lang='1' /></td>" +
                            "<td><input type='text' size='7'  id='precio_unitario1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, " + a + a + ")' lang='1' onchange='calculo()'/></td>" +
                            "<td><input type='text' size='7'  id='descuento1'  value=''  style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, " + a + a + ")' lang='1' onchange='calculo()' readonly/></td> " +
                            "<td>" +
                            "<input type='text' size='7'  id='descuent1'  value='' lang='1' readonly  />" +
                            "<label id='lbldescuent1' hidden lang='1'></label>" +
                            "</td>" +
                            "<td><input type='text' size='7'  id='iva1'  value='' style='text-align:right' lang='1' onblur='calculo(), this.value = this.value.toUpperCase()' readonly/></td>" +
                            "<td hidden><input type='text' size='7'  id='ice_p1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' lang='1' onblur='calculo()' /></td>" +
                            "<td hidden><input type='text' id='ice1' size='5' value='' readonly lang='1'/>" +
                            "<label hidden id='lblice1' lang='1'></label>" +
                            "<input type='hidden' id='ice_cod1' size='5' value='' readonly lang='1'/></td>" +
                            "<td hidden><input type='text' size='7'  id='irbp_p1'  value='' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + ")' lang='1' onblur='calculo()'/></td>" +
                            "<td hidden><input type='text' size='7'  id='irbp1'  value='' style='text-align:right' lang='1' readonly/>" +
                            "<label hidden id='lblirbp1' lang='1'></label></td>" +
                            "<td><input type='text' size='10'  id='precio_total1'  value='' style='text-align:right' lang='1' readonly/>" +
                            "<label hidden id='lblprecio_total1' lang='1'></label></td>" +
                            "</td>" +
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

                    $('#subtotal').val(parseFloat('0').toFixed(dec));
                    $('#lblsubtotal').val('0.00');

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
                } else {
                    load_factura(obj);
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
                width: 230px;
            }
            *{
                font-size: 11px;    
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
                    <tr><th colspan="12" >NOTA DE CREDITO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td width="108">NOTA DE CREDITO NO:</td>                    
                                <td><input type="text" size="30"  id="num_comprobante" value="<?php echo $comprobante ?>" readonly/></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="25"  id="fecha_emision"  readonly value="<?php echo $rst_not[ncr_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1" /></td>
                                <?php
//                                if ($amb == 0) {
                                    ?>
<!--                                    <td>Valido Desde:</td>
                                    <td><input type="text" size="7" id="fec_val_desde" value="<?php echo $rst_mod[emi_fec_vald_ntc_desde] ?>" readonly/></td>
                                    <td>Hasta:</td>
                                    <td>
                                        <input type="text" size="7" id="fec_val_hasta" value="<?php echo $rst_mod[emi_fec_vald_ntc_hasta] ?>" readonly/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="hidden" id="ambiente" value="<?php echo $amb ?>"/>
                                    </td>-->
                                    <?php
//                                }
                                ?>
                            </tr>         
                            <tr>
                                <td width="108">FACTURA NO:</td>                    
                                <td><input type="text" size="30"  id="num_secuencial" value="<?php echo $rst_not['ncr_num_comp_modifica'] ?>" maxlength="17" onchange="num_factura(this)" onkeyup ="this.value = this.value.replace(/[^0-9-]/, '')"/> 
                                    <input type="hidden" id="fac_id" value="<?php echo $rst_not[fac_id] ?>"/></td> 
                                <td>FECHA EMISION FACT.:</td>
                                <td><input type="text" size="25"  id="fecha_emision_comprobante" readonly value="<?php echo $rst_not['ncr_fecha_emi_comp'] ?>" /><img src="../img/calendar.png" id="im-campo2"></td>
                                <?php
//                                if ($amb == 0) {
                                    ?>
<!--                                    <td>N Factura Del:</td>
                                    <td><input type="text" size="7" id="num_val_desde" value="<?php echo $rst_mod[emi_sec_vald_ntc_desde] ?>" readonly/></td>
                                    <td>Al:</td>
                                    <td>
                                        <input type="text" size="7" id="num_val_hasta" value="<?php echo $rst_mod[emi_sec_vald_ntc_hasta] ?>" readonly/>
                                    </td>-->
                                    <?php
//                                }
                                ?>
                            </tr>         
                            <tr>
                                <td style="width:80px">CI/RUC :</td>
                                <td><input type="text" size="30" maxlength="13" id="identificacion" value="<?php echo $rst_not['ncr_identificacion'] ?>" onchange="load_cliente(this)" readonly/></td>
                                <td>CLIENTE :</td>
                                <td><input type="text" size="30"  id="nombre" value="<?php echo $rst_not[ncr_nombre] ?>"  readonly/>
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst_not[cli_id] ?>"  /></td>
                                <?php
//                                if ($amb == 0) {
                                    ?>
<!--                                    <td>Autorizacion SRI:</td>
                                    <td><input type="text" size="8" id="autorizacion_sri" value="<?php echo $rst_mod[emi_num_aut_ntc] ?>" readonly/></td>-->
                                    <?php
//                                }
                                ?>
                            </tr>
                            <tr>
                                <td>DIRECCION :</td>
                                <td><input type="text" size="30"  id="direccion_cliente" value="<?php echo $rst_not['ncr_direccion'] ?>"  readonly/></td>
                                <td>TELÉFONO:</td>
                                <td><input type="text" size="30"  id="telefono_cliente" value="<?php echo $rst_not['nrc_telefono'] ?>"  readonly/></td>
                            </tr>
                            <tr>
                                <td>CORREO:</td>
                                <td><input type="email" style="text-transform:lowercase "  size="30"  id="email_cliente"  value="<?php echo $rst_not['ncr_email'] ?>"  readonly/></td>
                            </tr>
                            <tr>
                                <td>TRANSACCION:</td>                
                                <td> <select id="motivo" onchange="ocultar()">
                                        <option value="12">DEVOLUCION DE VENTA</option>
                                        <option value="13">ANULACION DE VENTA</option>
                                        <option value="1">VARIOS</option>
                                    </select>
                                </td>
                                <td>MOTIVO:</td>
                                <td><input type="text" size="60"  id="descripcion_motivo" value="<?php echo $rst_not['ncr_motivo'] ?>" onblur="this.value = this.value.toUpperCase()"/></td>

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
                            <th class="td1">Codigo</th>
                            <th>Descripcion</th>
                            <th class="td1">Cant. Fac</th>   
                            <th <?php echo $hidden ?>>Inventario</th>   
                            <th>Cantidad</th>   
                            <th>Precio Unit</th>
                            <th>Descuento%</th>
                            <th>Descuento</th>
                            <th>Iva</th>
                            <th hidden>Ice%</th>
                            <th hidden>Ice $</th>
                            <th hidden>Irbpnr</th>
                            <th>Precio Total</th>
                            <th>Accion</th>
                            </thead>  
                            <!------------------------------------->
                            <tbody id="lista">
                                <?PHP
                                if (empty($cns)) {
                                    ?>
                                    <tr>
                                        <td><input type="text" size="8"  id="item1" class="itm"  lang="1" value="1" readonly  style="text-align:right" /></td>  
                                        <td class="td1"><input type="text" size="20" id="cod_producto1"  value="" lang="1" list="productos" onblur="this.style.width = '100px', load_producto(this)" onfocus="this.style.width = '500px'"/>
                                            <input hidden type="text" size="10" id="pro_id1" lang="1"/></td>
                                        <td><input type="text" size="35" id="descripcion1"  value="" lang="1" readonly/></td>  
                                        <td class="td1"><input id="cantidad1" type="text" lang="1" readonly="" value="" size="8"/></td>
                                        <td <?php echo $hidden ?>><input id="inventario1" <?php echo $hidden ?> type="text" lang="1" readonly value="" size="8"/></td>
                                        <td><input type="text" size="7"  id="cantidadf1" onchange="calculo(), comparar(this)"  value="" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" style="text-align:right" lang="1" /></td>
                                        <td><input type="text" size="7"  id="precio_unitario1"  value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onchange="calculo()"/></td>                  
                                        <td><input type="text" size="7"  id="descuento1"  value=""  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onchange="calculo()" readonly/></td>                  
                                        <td>
                                            <input type="text" size="7"  id="descuent1"  value="" lang="1" readonly  />
                                            <label id="lbldescuent1" hidden lang="1"></label>
                                        </td>
                                        <td><input type="text" size="7"  id="iva1"  value="" style="text-align:right" lang="1" onblur="calculo(), this.value = this.value.toUpperCase()" readonly/></td>                  
                                        <td hidden><input type="text" size="7"  id="ice_p1"  value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onblur="calculo()" /></td>
                                        <td hidden><input type="text" id="ice1" size="5" value="" readonly lang="1"/>
                                            <label hidden id="lblice1" lang="1"></label>
                                            <input type="hidden" id="ice_cod1" size="5" value="" readonly lang="1"/>
                                        </td>
                                        <td hidden><input type="text" size="7"  id="irbp_p1"  value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onblur="calculo()"/></td>                  
                                        <td hidden><input type="text" size="7"  id="irbp1"  value="" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="1" onblur="calculo()"/>
                                            <label hidden id="lblirbp1" lang="1"></label></td>                  
                                        <td><input type="text" size="10"  id="precio_total1"  value="" style="text-align:right" lang="1" readonly />                  
                                            <label hidden id="lblprecio_total1" lang="1"></label></td>                  
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" width="12px" src = "../img/del_reg.png"/></td>
                                    </tr>  
                                    <?PHP
                                } else {
                                    $n = 0;

                                    while ($rst = pg_fetch_array($cns)) {
                                        $n++;
                                        if ($inv5 == 0) {
                                            if ($ctr_inv == 0) {
                                                $fra = '';
                                            } else {
                                                $fra = "m.bod_id=$emisor";
                                            }
                                            $rst_i = pg_fetch_array($Clase_nota_Credito_nuevo->total_ingreso_egreso_fact($rst[pro_id]));
                                            $inv = $rst_i[ingreso] - $rst_i[egreso];
                                        }
                                        if ($rst_not[fac_id] != 0) {
                                            $rst_f = pg_fetch_array($Clase_nota_Credito_nuevo->lista_prod_det_factura($rst_not[fac_id], $rst[pro_id]));
                                            $rst_s = pg_fetch_array($Clase_nota_Credito_nuevo->suma_prod_nota_credito($rst_not[fac_id], $rst[pro_id]));
                                            $entr = $rst_f[dfc_cantidad] - $rst_s[sum] + $rst[dnc_cantidad];
                                        } else {
                                            $entr = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td><input type="text" size="8"  id="item<?php echo $n ?>" class="itm"  lang="<?php echo $n ?>" value="<?php echo $n ?>" readonly  style="text-align:right" /></td>  
                                            <td class="td1"><input type="text" size="20" id="cod_producto<?php echo $n ?>"  value="<?php echo $rst[dnc_codigo] ?>" lang="<?php echo $n ?>" list="productos" onblur="this.style.width = '100px', load_producto(this)" onfocus="this.style.width = '500px'" readonly/>
                                                <input hidden type="text" size="10" id="pro_id<?php echo $n ?>" value="<?php echo $rst[pro_id] ?>" lang="<?php echo $n ?>"/></td>
                                            <td><input type="text" size="35" id="descripcion<?php echo $n ?>"  value="<?php echo $rst[dnc_descripcion] ?>" lang="<?php echo $n ?>" readonly/></td>  
                                            <td class="td1"><input id="cantidad<?php echo $n ?>" type="text" lang="<?php echo $n ?>" readonly value="<?php echo str_replace(',', '', number_format($entr, $dc)) ?>" size="8"/></td>
                                            <td <?php echo $hidden ?>><input id="inventario<?php echo $n ?>" <?php echo $hidden ?> type="text" lang="<?php echo $n ?>" readonly value="<?php echo str_replace(',', '', number_format($inv, $dc)) ?>" size="8"/></td>
                                            <td><input type="text" size="7"  id="cantidadf<?php echo $n ?>" onchange="comparar(this), calculo()"  value="<?php echo str_replace(',', '', number_format($rst[dnc_cantidad], $dc)) ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" style="text-align:right" lang="<?php echo $n ?>" /></td>
                                            <td><input type="text" size="7"  id="precio_unitario<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_precio_unit], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onchange="calculo()" readonly/></td>                  
                                            <td><input type="text" size="7"  id="descuento<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_porcentaje_descuento], $dec)) ?>"  style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onchange="calculo()" readonly/></td>                  
                                            <td>
                                                <input type="text" size="7"  id="descuent<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_val_descuento], $dec)) ?>" lang="<?php echo $n ?>"readonly  />
                                                <label id="lbldescuent<?php echo $n ?>" hidden lang="<?php echo $n ?>"><?php echo $rst[dnc_val_descuento] ?></label>
                                            </td>
                                            <td><input type="text" size="7"  id="iva<?php echo $n ?>"  value="<?php echo $rst[dnc_iva] ?>" style="text-align:right" lang="<?php echo $n ?>"onblur="calculo(), this.value = this.value.toUpperCase()" readonly/></td>                  
                                            <td hidden><input type="text" size="7"  id="ice_p<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_p_ice], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onblur="calculo()" /></td>                  
                                            <td hidden><input type="text" id="ice<?php echo $n ?>" size="5" value="<?php echo str_replace(',', '', number_format($rst[dnc_ice], $dec)) ?>" readonly lang="<?php echo $n ?>"/>
                                                <label hidden id="lblice<?php echo $n ?>" lang="<?php echo $rst[dnc_ice] ?>"></label>
                                                <input type="hidden" id="ice_cod<?php echo $n ?>" size="5" value="<?php echo $rst[dnc_cod_ice] ?>" readonly lang="<?php echo $n ?>"/>
                                            </td>

                                            <td hidden><input type="text" size="7"  id="irbp_p<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_p_irbpnr], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onblur="calculo()"/></td>                  
                                            <td hidden><input type="text" size="7"  id="irbp<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_irbpnr], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" lang="<?php echo $n ?>"onblur="calculo()"/>
                                                <label hidden id="lblirbp<?php echo $n ?>" lang="<?php echo $n ?>"><?php echo $rst[dnc_irbpnr] ?></label></td>                  
                                            <td><input type="text" size="10"  id="precio_total<?php echo $n ?>"  value="<?php echo str_replace(',', '', number_format($rst[dnc_precio_total], $dec)) ?>" style="text-align:right" lang="<?php echo $n ?>"readonly />                  
                                                <label hidden id="lblprecio_total<?php echo $n ?>" lang="<?php echo $n ?>"><?php echo $rst[dnc_precio_total] ?></label></td>                  
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
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal 12%:</td>
                                    <td class="sbtls" ><input style="text-align:right" type="text" size="10" id="subtotal12"  value="<?php echo str_replace(',', '', number_format($rst_not[ncr_subtotal12], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal12"><?php echo $rst_not[ncr_subtotal12] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal 0%:</td>
                                    <td class="sbtls" ><input type="text" size="10"  id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst_not[ncr_subtotal0], $dec)) ?>" style="text-align:right" readonly />
                                        <label hidden id="lblsubtotal0"><?php echo $rst_not[ncr_subtotal0] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal No Objeto Iva:</td>
                                    <td class="sbtls" ><input type="text" size="10"   id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst_not[ncr_subtotal_no_iva], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lblsubtotalno"><?php echo $rst_not[ncr_subtotal_no_iva] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal Excento Iva:</td>
                                    <td class="sbtls" ><input type="text" size="10"  id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst_not[ncr_subtotal_ex_iva], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lblsubtotalex"><?php echo $rst_not[ncr_subtotal_ex_iva] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal Sin Impuestos:</td>
                                    <td class="sbtls" ><input type="text" size="10"  id="subtotal" value="<?php echo str_replace(',', '', number_format($rst_not[ncr_subtotal], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lblsubtotal"><?php echo $rst_not[ncr_subtotal] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Total Descuento:</td>
                                    <td class="sbtls" ><input type="text" size="10" id="total_descuento"   value="<?php echo str_replace(',', '', number_format($rst_not[ncr_total_descuento], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lbltotal_descuento"><?php echo $rst_not[ncr_total_descuento] ?> </label></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">ICE:</td>
                                    <td class="sbtls" ><input type="text" size="10" id="total_ice"  value="<?php echo str_replace(',', '', number_format($rst_not[ncr_total_ice], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lbltotal_ice"><?php echo $rst_not[ncr_total_ice] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">IVA 12%:</td>
                                    <td class="sbtls" ><input type="text" size="10" id="total_iva"  value="<?php echo str_replace(',', '', number_format($rst_not[ncr_total_iva], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lbltotal_iva"><?php echo $rst_not[ncr_total_iva] ?> </label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">IRBPNR:</td>
                                    <td class="sbtls" ><input type="text" size="10" id="irbpnr"  value="<?php echo str_replace(',', '', number_format($rst_not[ncr_irbpnr], $dec)) ?>" style="text-align:right" readonly/>
                                        <label hidden id="lblirbpnr"><?php echo $rst_not[ncr_irbpnr] ?> </label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">PROPINA:</td>
                                    <td class="sbtls" ><input type="text" size="13" id="propina"  value="<?php echo str_replace(',', '', number_format($rst_not[nrc_total_propina], $dec)) ?>" style="text-align:right" onchange="calculo()" onkeyup ="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                        <label hidden id="lblpropina"><?php echo $rst_not[nrc_total_propina] ?></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="td1" colspan="2">
                                    <td colspan="<?php echo $col ?>" align="right">Total:</td>
                                    <td class="sbtls"><input type="text" size="10" id="total_valor"  value="<?php echo str_replace(',', '', number_format($rst_not[nrc_total_valor], $dec)) ?>"  style="text-align:right" readonly/>
                                        <label hidden id="lbltotal_valor"><?php echo $rst_not[nrc_total_valor] ?></label></td>
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
                            if ($x != 1) {
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
<datalist id="productos">
    <?php
    $cns_pro = $Clase_nota_Credito_nuevo->lista_producto_total();
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[id] ?>" label="<?php echo $rst_pro[mp_c] . ' ' . $rst_pro[mp_d] ?>" />
        <?php
    }
    ?>
</datalist>
<script>
    var t = '<?php echo $rst_not[trs_id] ?>';
    $('#motivo').val(t);
</script>
