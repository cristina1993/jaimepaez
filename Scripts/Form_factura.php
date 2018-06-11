<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
include_once '../Clases/clsClase_factura.php';
$Clase_pagos = new Clase_pagos();
$Set = new Clase_factura();
if ($pto_emi > 99) {
    $ems = $pto_emi;
} else if ($pto_emi < 100 && $pto_emi > 9) {
    $ems = '0' . $pto_emi;
} else {
    $ems = '00' . $pto_emi;
}

if ($inv5 == 0) {
    $hidden = '';
    $col = '3';
} else {
    $hidden = 'hidden';
    $col = '2';
}
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Set->lista_una_factura_id($id));
    $rst[num_secuencial] = $rst[fac_numero];
    $cns_pagos = $Clase_pagos->lista_detalle_pagos($id);
    $rst['vendedor'] = $rst[vnd_nombre];
    $ven_id = $rst[vnd_id];
} else {
    $rst_sec = pg_fetch_array($Set->lista_secuencial_documento($emisor));
    $rst_ven = pg_fetch_array($Set->lista_vendedor(strtoupper($rst_user[usu_person])));
    if (empty($rst_sec)) {
        $sec = $rst_mod[emi_sec_factura];
    } else {
        $sec = ($rst_sec[secuencial] + 1);
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
    $rst[fac_numero] = $ems . '-001-' . $tx . $sec;
    $rst[fac_fecha_emision] = date('Y-m-d');
    $id = 0;
    $rst['vendedor'] = strtoupper($rst_user[usu_person]);
    $num_pagos = 0;
    $rst_ven = pg_fetch_array($Set->lista_vendedor(strtoupper($rst_user[usu_person])));
    $ven_id = $rst_ven[vnd_id];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">
        <meta charset="utf-8">
        <title>Factura</title>
        <script>
            id = '<?php echo $id ?>';
            emi = '<?php echo $emisor ?>';
            ven_id = '<?php echo $ven_id ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            inven = '<?php echo $inv5 ?>';
            asiento = '<?php echo $asi ?>';
            ctr_inv = '<?php echo $ctr_inv ?>';
            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#pro_descripcion' + a).val().length != 0 && $('#cantidad' + a).val().length != 0) {
                            if (a < 16) {
                                clona_fila('#tbl_form');
                            }
                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                posicion_aux_window();
                if (id == 0) {
                    $('#pago_forma1').val('4');
                }
            });


            function eliminaDuplicados(arr) {
                var i,
                        len = arr.length,
                        out = [],
                        obj = {};

                for (i = 0; i < len; i++) {
                    obj[arr[i]] = 0;
                }
                for (i in obj) {
                    out.push(i);
                }
                return out;
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a) {
                    case 0://pdf
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_factura.php?id=' + id;
                        break;
                    case 1://talonario
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_talonario_factura.php?id=' + id + '&det=1';
                        break;
                }
            }
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
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }
                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                obj = $(table).find(".itm");
                idt = obj[(obj.length - 1)].lang;
                $('#pro_descripcion' + idt).focus();
            }
            ;
            function save(id) {
                var data = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                data = Array(
                        emi,
                        cli_id.value,
                        ven_id,
                        '0',
                        fecha_emision.value,
                        secuencial.value,
                        nombre.value,
                        identificacion.value,
                        email_cliente.value,
                        direccion_cliente.value,
                        $('#lblsubtotal12').html().replace(',', ''), //subtotal12.value,
                        $('#lblsubtotal0').html().replace(',', ''), //subtotal0.value,
                        $('#lblsubtotalex').html().replace(',', ''), //subtotal_exento_iva.value,
                        $('#lblsubtotalno').html().replace(',', ''), //subtotal_no_objeto_iva.value,
                        $('#lbltotal_descuento').html().replace(',', ''),
                        $('#lbltotal_ice').html().replace(',', ''), //total_ice.value,
                        $('#lbltotal_iva').html().replace(',', ''), //total_iva.value,
                        $('#lbltotal_irbpnr').html().replace(',', ''), //total_irbpnr.value,
                        $('#lbltotal_propina').html().replace(',', ''), //total_propina.value,
                        $('#lbltotal_valor').html().replace(',', ''),
                        telefono_cliente.value,
                        observacion.value,
                        cli_ciudad.value,
                        cli_pais.value,
                        cli_parroquia.value,
                        $('#lblsubtotal').html().replace(',', '')
                        );
                var data2 = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#pro_descripcion' + n).val() != null) {
                        cod = $('#pro_descripcion' + n).val();
                        desc = $('#pro_referencia' + n).val();
                        cnt = $('#cantidad' + n).val();
                        pr = $('#pro_precio' + n).val();
                        dsc = $('#descuento' + n).val();
                        iva = $('#iva' + n).val().trim();
                        ice = $('#lblice' + n).html();
                        pt = $('#lblvalor_total' + n).html().replace(',', '');
                        dsc0 = $('#lbldescuent' + n).html().replace(',', '');
                        pro_id = $('#pro_aux' + n).val();
                        uni = $('#mov_cost_unit' + n).val();
                        ctot = $('#mov_cost_tot' + n).val();
                        irbrp = $('#lblirbp' + n).html();
                        irbp_p = $('#irbp_p' + n).val();
                        ic_p = $('#ice_p' + n).val();
                        ic_cod = $('#ice_cod' + n).val();
                        data2.push(
                                pro_id + '&' +
                                cod + '&' + //cod_producto,
                                '' + '&' + //cod_aux,
                                cnt + '&' + //cantidad,
                                desc + '&' + //descripcion,
                                pr + '&' +
                                dsc + '&' +
                                dsc0 + '&' +
                                pt + '&' +
                                iva + '&' +
                                ice + '&' +
                                uni + '&' +
                                ctot + '&' +
                                irbrp + '&' +
                                ic_p + '&' +
                                ic_cod + '&' +
                                irbp_p
                                );
                    }

                }
                var data3 = Array();
                n = 0;
                while (n < 4) {
                    n++;
                    pag_forma = $('#pago_forma' + n).val();
                    pag_banco = $('#pago_banco' + n).val();
                    pag_tarjeta = $('#pago_tarjeta' + n).val();
                    pag_cantidad = $('#lblpago_cantidad' + n).html();
                    pag_contado = $('#pago_contado' + n).val();
                    nc_num = $('#num_nota_credito' + n).val();
                    id_ntc = $('#id_nota_credito' + n).val();
                    val_ntc = $('#val_nt_cre' + n).val();
                    data3.push(
                            emi + '&' +
                            pag_forma + '&' +
                            pag_banco + '&' +
                            pag_tarjeta + '&' +
                            pag_cantidad + '&' +
                            pag_contado + '&' +
                            nc_num + '&' +
                            id_ntc + '&' +
                            val_ntc
                            );
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        pag = document.getElementsByClassName('itme');
                        n = 0;
                        j = 0;
                        if (secuencial.value.length != 17) {
                            $("#secuencial").css({borderColor: "red"});
                            $("#secuencial").focus();
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
                        } else if (cli_parroquia.value.length == 0) {
                            $("#cli_parroquia").css({borderColor: "red"});
                            $("#cli_parroquia").focus();
                            return false;
                        } else if (cli_ciudad.value.length == 0) {
                            $("#cli_ciudad").css({borderColor: "red"});
                            $("#cli_ciudad").focus();
                            return false;
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pro_descripcion' + n).val() != null) {
                                    if ($('#pro_descripcion' + n).val() == 0) {
                                        $('#pro_descripcion' + n).css({borderColor: "red"});
                                        $('#pro_descripcion' + n).focus();
                                        return false;
                                    } else if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    } else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    } else if ($('#pro_precio' + n).val() == 0) {
                                        $('#pro_precio' + n).css({borderColor: "red"});
                                        $('#pro_precio' + n).focus();
                                        return false;
                                    }

                                }
                            }
                        }
                        if ($('#total_valor').val() > 200 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $200');
                            return false;
                        }
                        if ($('#vendedor').val() == 0 || ven_id == '') {
                            $('#vendedor').css({borderColor: "red"});
                            $('#vendedor').focus();
                            alert('Vendedor no existe');
                            return false;
                        }
                        j = 0;
                        while (j < 5) {
                            j++;
                            if ($('#pago_cantidad' + j).val() != 0) {
                                if ($('#pago_forma' + j).val() == 0) {
                                    $('#pago_forma' + j).css({borderColor: "red"});
                                    $('#pago_forma' + j).focus();
                                    return false;
                                }
                                if ($('#pago_forma' + j).val() == '7' && $('#num_nota_credito' + j).val().length == 0) {
                                    $('#num_nota_credito' + j).focus();
                                    $('#num_nota_credito' + j).css({borderColor: "red"});
                                    return false;
                                }
                                if ($('#pago_forma' + j).val() == '7' || $('#pago_forma' + j).val() == '8') {
                                    dt = $('#num_nota_credito' + j).val().split('-');
                                    if ($('#num_nota_credito' + j).val().length != 17 || dt[0].length != 3 || dt[1].length != 3 || dt[2].length != 9) {
                                        $('#num_nota_credito' + j).val('');
                                        $('#num_nota_credito' + j).focus();
                                        $('#num_nota_credito' + j).css({borderColor: "red"});
                                        alert('No cumple con la estructura ejem: 000-000-000000000');
                                        return false;
                                    }
                                }
                                if ($('#pago_forma' + j).val() == '3' || $('#pago_forma' + j).val() == '5' || $('#pago_forma' + j).val() == '8') {
                                    if ($('#num_nota_credito' + j).val().length == 0) {
                                        $('#num_nota_credito' + j).val('');
                                        $('#num_nota_credito' + j).focus();
                                        $('#num_nota_credito' + j).css({borderColor: "red"});
                                        return false;
                                    }
                                }
                                if ($('#pago_banco' + j).val() == 0 && $('#pago_banco' + j).attr('disabled') == false) {
                                    $('#pago_banco' + j).css({borderColor: "red"});
                                    $('#pago_banco' + j).focus();
                                    return false;
                                }

                                if ($('#pago_tarjeta' + j).val() == 0 && $('#pago_tarjeta' + j).attr('disabled') == false) {
                                    $('#pago_tarjeta' + j).css({borderColor: "red"});
                                    $('#pago_tarjeta' + j).focus();
                                    return false;
                                }
                                if ($('#pago_contado' + j).val() == 0 && $('#pago_contado' + j).attr('disabled') == false) {
                                    $('#pago_contado' + j).css({borderColor: "red"});
                                    $('#pago_contado' + j).focus();
                                    return false;
                                }
                            }
                        }

                        sp = (parseFloat($('#pago_cantidad1').val()) * 1) + (parseFloat($('#pago_cantidad2').val()) * 1) + (parseFloat($('#pago_cantidad3').val()) * 1) + (parseFloat($('#pago_cantidad4').val()) * 1);
                        if (sp.toFixed(dec) != $('#total_valor').val().replace(',', '')) {
                            alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                            return false;
                        }

                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_factura.php',
                    data: {op: 2, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: id, 'fields[]': fields, x: inven},
                    success: function (dt) {
                        dat = dt.split('&');
                        switch (parseInt(dat[0])) {
                            case 0:
                                if (asiento == 0) {
                                    asientos(dat[2], dat[1]);
                                } else {
                                    loading('hidden');
                                    cancelar();
                                }
                                break;
                            case 1:
                                alert('Numero Secuencial de la Factura ya existe \n Debe hacer otra factura con otro Secuencial');
                                loading('hidden');
                                break;
                            case 2:
                                alert('Una de las cuentas de la factura esta inactiva');
                                loading('hidden');
                                break;
                            default :
                                loading('hidden');
                                alert(dat[0]);
                                break;
                        }

                    }
                })
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_cliente(obj) {
                $.post("actions_factura.php", {op: 0, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Se creará uno nuevo');
                        $('#nombre').focus();
                        $('#identificacion').focus();
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_parroquia').val('');
                        $('#cli_ciudad').val('');
                        $('#cli_pais').val('');
                        $('#cli_id').val('0');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_factura.php", {op: 0, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Se creará uno nuevo');
                        $('#nombre').focus();
                        $('#identificacion').focus();
                        $('#identificacion').val('');
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_parroquia').val('');
                        $('#cli_ciudad').val('');
                        $('#cli_pais').val('');
                        $('#cli_id').val('0');
                    } else {
                        dat = dt.split('&');
                        if (dat[10] == 1 || dat[10] == 2) {
                            alert('Cliente se encuentra Inactivo o Suspendido');
                            $('#identificacion').focus();
                            $('#identificacion').val('');
                            $('#nombre').val('');
                            $('#direccion_cliente').val('');
                            $('#telefono_cliente').val('');
                            $('#email_cliente').val('');
                            $('#cli_parroquia').val('');
                            $('#cli_ciudad').val('');
                            $('#cli_pais').val('');
                            $('#cli_id').val('0');
                        } else {
                            $('#identificacion').val(dat[0]);
                            $('#nombre').val(dat[1]);
                            $('#direccion_cliente').val(dat[2]);
                            $('#telefono_cliente').val(dat[3]);
                            $('#email_cliente').val(dat[4]);
                            $('#cli_parroquia').val(dat[5]);
                            $('#cli_ciudad').val(dat[6]);
                            $('#cli_pais').val(dat[7]);
                            $('#cli_id').val(dat[8]);
                        }
                    }
                    $('#con_clientes').hide();
                }
                );
            }



            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo('1');
            }


            function calculo(obj) {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tdsc = 0;
                var tiva = 0;
                var gtot = 0;
                var tice = 0;
                var tib = 0;
                var sub = 0;

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        val2 = 0;
                        d = 0;
                        cnt = 0;
                        pr = 0;
                        d = 0;
                        vtp = 0;
                        vt = 0;
                        ic = 0;
                        ib = 0;
                    } else {
                        cnt = $('#cantidad' + n).val().replace(',', '');
                        pr = $('#pro_precio' + n).val().replace(',', '');
                        d = $('#descuento' + n).val().replace(',', '');
                        vtp = cnt * pr; //Valor total parcial
                        vt = (vtp * 1) - (vtp * d / 100);
                        ic = $('#ice_p' + n).val().replace(',', '');
                        ib = $('#irbp_p' + n).val().replace(',', '');
                        pic = (vt * ic) / 100;
                        pib = (cnt * ib);
//                        vt2 = pic + vt;
                        $('#descuent' + n).val((vtp * d / 100).toFixed(dec));
                        $('#lbldescuent' + n).html((vtp * d / 100).toFixed(6));
                        $('#valor_total' + n).val(vt.toFixed(dec));
                        $('#lblvalor_total' + n).html(vt.toFixed(6));
                        ob = $('#iva' + n).val();
                        val = $('#valor_total' + n).val().replace(',', '');
                        d = $('#descuent' + n).val().replace(',', '');
                        $('#ice' + n).val(pic.toFixed(dec));
                        $('#lblice' + n).html(pic.toFixed(6));
                        $('#irbp' + n).val(pib.toFixed(dec));
                        $('#lblirbp' + n).html(pib.toFixed(6));

                    }



                    prop = $('#total_propina').val().replace(',', '');
                    $('#lbltotal_propina').html(prop);

                    tdsc = (tdsc * 1) + (d * 1);
                    tice = (tice * 1) + (pic * 1);
                    tib = (tib * 1) + (pib * 1);

                    if (ob == '14') {
                        t12 = (t12 * 1 + vt * 1);
                        tiva = ((tice + t12) * 14 / 100);
                    }

                    if (ob == '12') {
                        t12 = (t12 * 1 + vt * 1);
                        tiva = ((tice + t12) * 12 / 100);
                    }
                    if (ob == '0') {
                        t0 = (t0 * 1 + vt * 1);
                    }
                    if (ob == 'EX') {
                        tex = (tex * 1 + vt * 1);
                    }
                    if (ob == 'NO') {
                        tno = (tno * 1 + vt * 1);
                    }

                }
                sub = t12 + t0 + tex + tno;
                //               tiva = ((tice + t12) * 12 / 100);
//                tiva = (t12 * 12 / 100);
                gtot = (sub * 1 + tiva * 1 + tice * 1 + tib * 1 + prop * 1);

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
                $('#total_descuento').val(tdsc.toFixed(dec));
                $('#lbltotal_descuento').html(tdsc.toFixed(6));
                $('#total_iva').val(tiva.toFixed(dec));
                $('#lbltotal_iva').html(tiva.toFixed(6));
                $('#total_ice').val(tice.toFixed(dec));
                $('#lbltotal_ice').html(tice.toFixed(6));
                $('#total_irbpnr').val(tib.toFixed(dec));
                $('#lbltotal_irbpnr').html(tib.toFixed(6));
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot.toFixed(6));
                pago_cantidad1.value = gtot.toFixed(dec);
                $('#lblpago_cantidad1').html(gtot.toFixed(6));
                calculo_pago_locales();
                valores_lbl();
            }
            function calculo_pago_locales() {
                j = 0;
                while (j < 5) {
                    j++;
                    if ($('#pago_forma' + j).val() == '8' && $('#id_nota_credito' + j).val() != 0 && $('#id_nota_credito' + j).val() != '') {
                        cnt_not = parseFloat($('#val_nt_cre' + j).val());
                        cnt_pag = parseFloat($('#pago_cantidad' + j).val());
                        if (cnt_pag > cnt_not) {
                            alert('Pago es mayor al del documento $: ' + parseFloat(cnt_not).toFixed(2));
                            $('#pago_cantidad' + j).val('0');
                            $('#pago_cantidad' + j).focus();
                        }
                    }
                }

                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value.replace(',', '')) - tp.toFixed(dec);
                if (flt.toFixed(dec) < 0) {
                    alert('Valor ingresado incorrecto');
                } else {
                    t_pagos.value = flt.toFixed(dec);
                }

            }
            function cancelar() {
                t = '<?php echo $_GET[txt] ?>';
                d = '<?php echo $_GET[desde] ?>';
                h = '<?php echo $_GET[hasta] ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + t + '&desde=' + d + '&hasta=' + h;//Cambiar Form_productos
            }
            function cerrar_ventana() {
                $('#con_clientes').hide();
            }

            function pago(obj) {
                n = 0;
                itm = $('.itme').length;
                if (obj.value <= 4) {
                    f = obj.value - itm;
                    while (n < f) {
                        clona_fila('#tbl_colum3');
                        n++;
                    }
                }

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

            function habilitar(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }

                if ($('#pago_forma' + s).val() == '1') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).attr('disabled', false);
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '2') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '3') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '9') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).focus();
                } else if ($('#pago_forma' + s).val() > '3') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).focus();
                } else {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', true);
                }
                calculo_pago_locales();
            }

            function caracter(e, obj, x) {

                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)
                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });
                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    v = 0;
                    load_producto(j, v);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#cantidad' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }


            }


            function load_producto(j, v) {
                if (v == 1) {
                    vl = $('#pro_descripcion' + j).val();
                } else {
                    vl = $('#pro_descripcion' + j).val();
                }
                $('.itm').each(function () {
                    pro = $('#pro_aux' + this.value).val();
                    pro2 = $('#pro_descripcion' + j).val();
                    $('#pro_descripcion' + j).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Producto ya ingresado');
                        vl = '';
                        $('#pro_descripcion' + j).focus();
                        return false;
                    }
                });
                $.post("actions_factura.php", {op: 1, id: vl, emi: emi, x: inven, ctr_inv: ctr_inv},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pro_descripcion' + j).val(dat[1]);
                        $('#pro_referencia' + j).val(dat[2]);
                        $('#iva' + j).val(dat[8]);
                        $('#pro_aux' + j).val(dat[0]);
                        $('#pro_ids' + j).val(dat[14]);
                        $('#cantidad' + j).val('');

                        if (dat[9] == '') {
                            $('#unidad' + j).val('');
                        } else {
                            $('#unidad' + j).val(dat[9]);
                        }
                        if (dat[3] == '') {
                            $('#pro_precio' + j).val(0);
                            $('#iva' + j).val('12');
                        } else {
                            $('#pro_precio' + j).val(parseFloat(dat[3]).toFixed(dec));
                        }

                        if (dat[5] == '') {
                            $('#descuento' + j).val(0);
                        } else {
                            $('#descuento' + j).val(parseFloat(dat[5]).toFixed(dec));
                        }
                        if (inven == 0) {
                            if (dat[6] == '') {
                                $('#mov_cost_unit' + j).val('0');
                            } else {
                                $('#mov_cost_unit' + j).val(parseFloat(dat[10]).toFixed(dec));
                            }
                            if (dat[6] == '') {
                                $('#inventario' + j).val(0);
                            } else {
                                $('#inventario' + j).val(parseFloat(dat[6]).toFixed(dc));
                            }
                        }

                        if (dat[11] == '') {
                            $('#ice' + j).val('0');
                            $('#ice_p' + j).val('0');
                        } else {
                            $('#ice' + j).val(0);
                            $('#ice_p' + j).val(parseFloat(dat[11]).toFixed(dc));

                        }

                        if (dat[13] == '') {
                            $('#ice_cod' + j).val('0');
                        } else {
                            $('#ice_cod' + j).val(dat[13]);
                        }

                        if (dat[12] == '') {
                            $('#irbp' + j).val(0);
                            $('#irbp_p' + j).val(0);
                        } else {
                            $('#irbp' + j).val(0);
                            $('#irbp_p' + j).val(parseFloat(dat[12]).toFixed(dc));
                        }
                        $('#cantidad' + j).focus();
                    } else {
                        $('#pro_descripcion' + j).val('');
                        $('#pro_referencia' + j).val('');
                        $('#cantidad' + j).val('');
                        $('#iva' + j).val('0');
                        $('#pro_aux' + j).val('');
                        $('#pro_ids' + j).val('');
                        $('#pro_precio' + j).val(0);
                        $('#descuento' + j).val(0);
                        if (inven == 0) {
                            $('#mov_cost_unit' + j).val('0');
                            $('#inventario' + j).val(0);
                        }
                        $('#ice' + j).val('0');
                        $('#ice_p' + j).val('0');
                        $('#ice_cod' + j).val('0');
                        $('#irbp' + j).val(0);
                        $('#irbp_p' + j).val(0);
                        $('#pro_descripcion' + j).focus();


                    }

                    calculo('1');
                });

            }

            function enter(e) {
                var char = e.which;
                if (char == 13) {
                    return false;
                }
            }

            function inventario(obj) {
                if (inven == 0) {
                    n = obj.lang;
                    if ($('#pro_ids' + n).val() != 79 && $('#pro_ids' + n).val() != 80) {
                        if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                            alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                            $(obj).val('');
                            $(obj).focus();
                            $(obj).css({borderColor: "red"});
                            calculo();
                        }
                    } else {
                        calculo();
                    }
                }
            }
            function pag_sig(obj) {
                f = obj.lang;
                s = parseInt(f) + 1;
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value) - parseFloat(tp);
                if (obj.value != 0 && (flt.toFixed(dec) > 0)) {
                    $('#pago_cantidad' + s).val(flt.toFixed(dec));
                    $('#lblpago_cantidad' + s).val(flt.toFixed(6));
                }
                valores_lbl();
            }

            function asientos(sms, d1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: d1, x: id, data: secuencial.value, emi: emi},
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function costo(obj) {
                i = obj.lang;
                can = $('#cantidad' + i).val();
                uni = $('#mov_cost_unit' + i).val() * 1;
                tot = $('#mov_cost_tot' + i).val();
                t = parseFloat(can) * parseFloat(uni);
                $('#mov_cost_tot' + i).val(t.toFixed(6));
            }

            function cambio_cmb(obj) {
                i = obj.lang;
                if ($('#pago_forma' + i).val() != 9) {
                    var op = "<option value='0'>SELECCIONE</option>" +
                            "<option value='1'>CORRIENTE</option>" +
                            "<option value='2'>3 meses</option>" +
                            "<option value='3'>6 meses</option>" +
                            "<option value='4'>9 meses</option>" +
                            "<option value='5'>12 meses</option>" +
                            "<option value='6'>18 meses</option>" +
                            "<option value='7'>36 meses</option>";
                    $('#pago_contado' + i).html(op);
                } else {
                    var op = "<option value='0'>SELECCIONE</option>" +
                            "<option value='8'>8 dias</option>" +
                            "<option value='15'>15 dias</option>" +
                            "<option value='30'>30 dias</option>" +
                            "<option value='45'>45 dias</option>" +
                            "<option value='60'>60 dias</option>" +
                            "<option value='90'>90 dias</option>" +
                            "<option value='120'>120 dias</option>";
                    $('#pago_contado' + i).html(op);
                }
            }

            function valores_lbl() {
                n = 0;
                while (n < 4) {
                    n++;
                    val = parseFloat($('#pago_cantidad' + n).val());
                    $('#lblpago_cantidad' + n).html(val.toFixed(6));

                }
            }

            function validar_email(valor)
            {
                var filter = /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
                if (filter.test(valor))
                    return true;
                else
                    return false;
            }

            function mail_validado() {
                if ($("#email_cliente").val() == '')
                {
                    alert("Ingrese un email");
                } else if (validar_email($("#email_cliente").val()))
                {

                } else
                {
                    alert("El email no es valido");
                    $('#email_cliente').css({borderColor: "red"});
                    $('#email_cliente').val('');
                    $('#email_cliente').focus();
                }
            }

            function busqueda_ntscre(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }
                nc = obj.value;
                ruc_cli = $('#identificacion').val();
                if (ruc_cli != '') {
                    if (nc == 8) {
                        $('#num_nota_credito' + s).attr('disabled', true);
                        $.post("actions_factura.php", {op: 3, id: ruc_cli, s: 0, l: s, doc: nc},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('El Cliente no tiene Documentos \n En esta opcion');
                                $('#num_nota_credito' + s).val('');
                                $('#id_nota_credito' + s).val('0');
                                $('#val_nt_cre' + s).val('');
                                $('#pago_forma' + s).val(0);
                                $('#pago_forma' + s).focus();
                                $('#pago_cantidad' + s).val('0');
                                $('#pago_cantidad' + s).attr('disabled', true);
                                $('#num_nota_credito' + s).attr('disabled', false);
                            }
                        });
                    } else {
                        $('#num_nota_credito' + s).attr('disabled', false);
                        $('#num_nota_credito' + s).val('');
                        $('#id_nota_credito' + s).val('0');
                        $('#val_nt_cre' + s).val('');
                    }
                } else {
                    alert('Debe elejir un cliente');
                    $('#pago_forma' + s).val(0);
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#identificacion').focus();
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('');
                }
            }

            function load_notas_credito(n, obj) {
                id1 = $('#id_nota_credito1').val();
                id2 = $('#id_nota_credito2').val();
                id3 = $('#id_nota_credito3').val();
                id4 = $('#id_nota_credito4').val();
                id5 = obj;
                if (id1 == id5 || id2 == id5 || id3 == id5 || id4 == id5) {
                    $('#con_clientes').hide();
                    alert('Documento ya ingresado');
                    $('#pago_forma' + n).val(0);
                    $('#num_nota_credito' + n).val('');
                    $('#id_nota_credito' + n).val('0');
                    $('#val_nt_cre' + n).val('');
                    $('#pago_cantidad' + n).val('0');
                    $('#pago_cantidad' + n).attr('disabled', true);
                    obj = '';
                    return false;
                }
                $.post("actions_factura.php", {op: 3, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('El Cliente no tiene Documentos \n En esta opcion');
                        $('#pago_forma' + n).val(0);
                        $('#pago_forma' + n).focus();
                        $('#num_nota_credito' + s).attr('disabled', false);

                    } else {
                        dat = dt.split('&');
                        $('#num_nota_credito' + n).val(dat[0]);
                        $('#pago_cantidad' + n).val(dat[1]);
                        $('#id_nota_credito' + n).val(dat[2]);
                        $('#val_nt_cre' + n).val(dat[1]);
                        $('#pago_cantidad' + n).focus();
                        $('#num_nota_credito' + s).attr('disabled', true);
                        calculo_pago_locales();
                    }
                    if (dt == 1) {
                        $('#num_nota_credito' + n).val('');
                        $('#id_nota_credito' + n).val('0');
                        $('#val_nt_cre' + n).val('');
                        $('#pago_cantidad' + n).val(0);
                        $('#pago_cantidad' + n).attr('disabled', true);
                        $('#num_nota_credito' + s).attr('disabled', false);
                        calculo_pago_locales();
                    }
                    $('#con_clientes').hide();
                }
                );
            }

            function verificar_cuenta(obj) {
                if (obj.lang != null) {
                    s = obj.lang;
                } else {
                    s = obj;
                }
                if ($('#pago_forma' + s).val() != '9') {
                    $.post("actions_factura.php", {op: 4, id: obj.value, usu: emi},
                    function (dt) {
                        if (dt == 1) {
                            alert('La Cuenta de esta forma de Pago \n Se encuentra inactiva en este momento');
                            $('#pago_forma' + s).val(0);
                            $('#pago_banco' + s).attr('disabled', true);
                            $('#pago_tarjeta' + s).attr('disabled', true);
                            $('#pago_cantidad' + s).attr('disabled', true);
                            $('#pago_contado' + s).attr('disabled', true);
                        }
                    });
                }
            }

            function limpliar_ruc() {
                $('#identificacion').val('');
                $('#identificacion').focus();
            }

            //algoritmo digito verificado CC//
            function verificar_cedula(obj) {
                
                i = obj.value.trim().length;
                c = obj.value.trim();

                var s = 0;
                if ($('#pasaporte').attr('checked') == false) {
                    if (!isNaN(c)) {
                        n = 0;
                        while (n < 9) {
                            r = n % 2;
                            if (r == 0) {
                                m = 2;
                            } else {
                                m = 1;
                            }
                            ml = (c.substr(n, 1) * 1) * m;

                            if (ml > 9) {
                                ml = (ml.toString().substr(0, 1) * 1) + (ml.toString().substr(1, 1) * 1);
                            }
                            s += ml;
                            n++;
                        }
                        d = s % 10;
                        if (d == 0) {
                            t = 0;
                        } else {
                            t = 10 - d;
                        }
                        if (t.toString() == c.substr(9, 1) && (i==10 ||i==13)) {
                            load_cliente(obj);
                        } else {
                            alert('RUC/CC incorrecto');
                            $(obj).val('');
                        }
                    } else {
                        load_cliente(obj);
                    }
                } else {
                    //           alert('ESTA INGRESANDO UN PASAPORTE, SI NO ES CORRECTO FAVOR REVISAR');
                    load_cliente(obj);
                }

            }
        </script>
        <style>
            .fila-base{ display: none; } /* fila base oculta */
            .eliminar{ cursor: pointer; color: #000; }
            thead tr td{
                font-size: 11px;
                border:solid 1px #ccc;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 11px;
                font-weight:100; 
            }
            select{
                width: 150px;
            }
            .sms{
                color: #D8000C !important;
                background-color: #FFBABA;

            }
            input{
                text-transform:uppercase; 
            }
            table{
                border-spacing: 0px;
                border-collapse: collapse;                
            }
            #tbl_dinamic input{
                text-align:right; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="proceso" >
            <!--<font id="sri_cont"><img src="../img/load_circle.gif" id="sri_load" style="width:32px" /></font>-->
<!--            <font id="mail_cont"><img src="../img/load_circle.gif" id="mail_load" style="width:32px" /></font>-->
        </div>
        <div id="cargando"></div>

        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden';
                    limpliar_ruc()">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form" >
                <thead>
                    <tr>
                        <th colspan="10" >
                            <?php echo "FORMULARIO DE FACTURACIÓN " . $bodega ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>Factura N:</td>
                                <td>
                                    <input type="text" size="20" id="secuencial"  maxlength="17" value="<?php echo $rst[fac_numero] ?>" />
                                    <input type="hidden" id="cod_punto_emision" value="<?php echo $rst[emi_cod_punto_emision] ?>" />
                                </td>
                                <td>Fecha:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_emision" readonly value="<?php echo $rst[fac_fecha_emision] ?>" />
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td><table id='tbl_colum2' >
                            <tr class="trthead">
                                <td  colspan="2" style="background:#00557F ;color:white " align='center' >
                                    <label class="tdtitulo">CLIENTE:</label>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:80px ">RUC/CC:</td>
                                <!--cambia el evento onchange de load_cliente a verificar_cedula-->
                                <td><input type="text" size="45"  maxlength="13" id="identificacion" value="<?php echo $rst[fac_identificacion] ?>" onchange="verificar_cedula(this)" onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/, '')"  />
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"/></td>
                                <td>
                                    <input type="checkbox" id="pasaporte"/> 
                                </td>
                                <td>
                                    Pasaporte 
                                </td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre"  value="<?php echo $rst[fac_nombre] ?>" onblur="this.value = this.value.toUpperCase()" /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente"  value="<?php echo $rst[fac_direccion] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente"  value="<?php echo $rst[fac_telefono] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td>
                                    <input type="email"   size="45"  id="email_cliente"  value="<?php echo $rst[fac_email] ?>" style="text-transform:lowercase " onchange="mail_validado()" />
                                </td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia"  value="<?php echo $rst[cli_parroquia] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad"  value="<?php echo $rst[cli_canton] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais"  value="<?php echo $rst[cli_pais] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr></table></td>
                    <td valign="top">
                        <table id='tbl_colum3' border="0" cellspacing="0" cellpadding="0" >
                            <td class="trthead" colspan="6" align='center' style="background:#00557F ;color:white " >
                                <label  class="tdtitulo">FORMAS DE PAGO</label>
                            </td>
                            <tr>
                                <td class="vendedor" colspan="4">Vendedor:<input type="text" id="vendedor" value="<?php echo $rst['vendedor'] ?>" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td>FORMA</td>
                                <td>DOCUMENTO</td>
                                <td>BANCO</td>
                                <td>TARJETA</td>
                                <td>PAGO</td>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CANTIDAD</td>
                            </tr>

                            <tr>
                                <td>
                                    <select id="pago_forma1" lang="1" onblur="habilitar(this), cambio_cmb(this), busqueda_ntscre(this), verificar_cuenta(this)">
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">TARJETA DE CREDITO</option>
                                        <option value="2">TARJETA DE DEBITO</option>
                                        <option value="3">CHEQUE</option>
                                        <option value="4">EFECTIVO</option>
                                        <option value="5">CERTIFICADOS</option>
                                        <option value="6">TRANSFERENCIA</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito1" lang="1" maxlength="17" onkeyup = "this.value = this.value.replace(/[^0-9-]/, '')" >
                                    <input type="hidden" size="6" id="id_nota_credito1" lang="1" value="0">
                                    <input type="hidden" size="6" id="val_nt_cre1" lang="1" value="0">
                                </td>
                                <td>
                                    <select id="pago_banco1" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">Banco Pichincha</option>
                                        <option value="2">Banco del Pacífico</option>
                                        <option value="3">Banco de Guayaquil</option>
                                        <option value="4">Produbanco</option>
                                        <option value="5">Banco Bolivariano</option>
                                        <option value="6">Banco Internacional</option>
                                        <option value="7">Banco del Austro</option>
                                        <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                        <option value="9">Banco de Machala</option>
                                        <option value="10">BGR</option>
                                        <option value="11">Citibank (Ecuador)</option>
                                        <option value="12">Banco ProCredit (Ecuador)</option>
                                        <option value="13">UniBanco</option>
                                        <option value="14">Banco Solidario</option>
                                        <option value="15">Banco de Loja</option>
                                        <option value="16">Banco Territorial</option>
                                        <option value="17">Banco Coopnacional</option>
                                        <option value="18">Banco Amazonas</option>
                                        <option value="19">Banco Capital</option>
                                        <option value="20">Banco D-MIRO</option>
                                        <option value="21">Banco Finca</option>
                                        <option value="22">Banco Comercial de Manabí</option>
                                        <option value="23">Banco COFIEC</option>
                                        <option value="24">Banco del Litoral</option>
                                        <option value="25">Banco Delbank</option>
                                        <option value="26">Banco Sudamericano</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_tarjeta1" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">VISA</option>
                                        <option value="2">MASTER CARD</option>
                                        <option value="3">AMERICAN EXPRESS</option>
                                        <option value="4">DINNERS</option>
                                        <option value="5">DISCOVER</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_contado1" disabled>
                                        <option value='0'>SELECCIONE</option>
                                        <option value='1'>CORRIENTE</option>
                                        <option value='2'>3 meses</option>
                                        <option value='3'>6 meses</option>
                                        <option value='4'>9 meses</option>
                                        <option value='5'>12 meses</option>
                                        <option value='6'>18 meses</option>
                                        <option value='7'>36 meses</option>
                                        <option value='8'>8 dias</option>
                                        <option value='15'>15 dias</option>
                                        <option value='30'>30 dias</option>
                                        <option value='45'>45 dias</option>
                                        <option value='60'>60 dias</option>
                                        <option value='90'>90 dias</option>
                                        <option value='120'>120 dias</option>
                                    </select>
                                </td>
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad1" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="1" disabled onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                    <label hidden id="lblpago_cantidad1" lang="1"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma2" lang="2" onblur="habilitar(this), cambio_cmb(this), busqueda_ntscre(this)">
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">TARJETA DE CREDITO</option>
                                        <option value="2">TARJETA DE DEBITO</option>
                                        <option value="3">CHEQUE</option>
                                        <option value="4">EFECTIVO</option>
                                        <option value="5">CERTIFICADOS</option>
                                        <option value="6">TRANFERENCIA</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito2" lang="2" maxlength="17" onkeyup = "this.value = this.value.replace(/[^0-9-]/, '')">
                                    <input type="hidden" size="6" id="id_nota_credito2" lang="2" value="0">
                                    <input type="hidden" size="6" id="val_nt_cre2" lang="2" value="0">
                                </td>
                                <td>
                                    <select id="pago_banco2" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">Banco Pichincha</option>
                                        <option value="2">Banco del Pacífico</option>
                                        <option value="3">Banco de Guayaquil</option>
                                        <option value="4">Produbanco</option>
                                        <option value="5">Banco Bolivariano</option>
                                        <option value="6">Banco Internacional</option>
                                        <option value="7">Banco del Austro</option>
                                        <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                        <option value="9">Banco de Machala</option>
                                        <option value="10">BGR</option>
                                        <option value="11">Citibank (Ecuador)</option>
                                        <option value="12">Banco ProCredit (Ecuador)</option>
                                        <option value="13">UniBanco</option>
                                        <option value="14">Banco Solidario</option>
                                        <option value="15">Banco de Loja</option>
                                        <option value="16">Banco Territorial</option>
                                        <option value="17">Banco Coopnacional</option>
                                        <option value="18">Banco Amazonas</option>
                                        <option value="19">Banco Capital</option>
                                        <option value="20">Banco D-MIRO</option>
                                        <option value="21">Banco Finca</option>
                                        <option value="22">Banco Comercial de Manabí</option>
                                        <option value="23">Banco COFIEC</option>
                                        <option value="24">Banco del Litoral</option>
                                        <option value="25">Banco Delbank</option>
                                        <option value="26">Banco Sudamericano</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_tarjeta2" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">VISA</option>
                                        <option value="2">MASTER CARD</option>
                                        <option value="3">AMERICAN EXPRESS</option>
                                        <option value="4">DINNERS</option>
                                        <option value="5">DISCOVER</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_contado2" disabled>
                                        <option value='0'>SELECCIONE</option>
                                        <option value='1'>CORRIENTE</option>
                                        <option value='2'>3 meses</option>
                                        <option value='3'>6 meses</option>
                                        <option value='4'>9 meses</option>
                                        <option value='5'>12 meses</option>
                                        <option value='6'>18 meses</option>
                                        <option value='7'>36 meses</option>
                                        <option value='8'>8 dias</option>
                                        <option value='15'>15 dias</option>
                                        <option value='30'>30 dias</option>
                                        <option value='45'>45 dias</option>
                                        <option value='60'>60 dias</option>
                                        <option value='90'>90 dias</option>
                                        <option value='120'>120 dias</option>
                                    </select>
                                </td>
                                <td align="right" ><input type="text" style="text-align:right" size="15" id="pago_cantidad2" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="2" disabled onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                    <label hidden id="lblpago_cantidad2" lang="2"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma3" lang="3" onblur="habilitar(this), cambio_cmb(this), busqueda_ntscre(this)">
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">TARJETA DE CREDITO</option>
                                        <option value="2">TARJETA DE DEBITO</option>
                                        <option value="3">CHEQUE</option>
                                        <option value="4">EFECTIVO</option>
                                        <option value="5">CERTIFICADOS</option>
                                        <option value="6">TRANFERENCIA</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito3" lang="3"  maxlength="17" onkeyup = "this.value = this.value.replace(/[^0-9-]/, '')">
                                    <input type="hidden" size="6" id="id_nota_credito3" lang="3" value="0">
                                    <input type="hidden" size="6" id="val_nt_cre3" lang="3" value="0">
                                </td>
                                <td>
                                    <select id="pago_banco3" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">Banco Pichincha</option>
                                        <option value="2">Banco del Pacífico</option>
                                        <option value="3">Banco de Guayaquil</option>
                                        <option value="4">Produbanco</option>
                                        <option value="5">Banco Bolivariano</option>
                                        <option value="6">Banco Internacional</option>
                                        <option value="7">Banco del Austro</option>
                                        <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                        <option value="9">Banco de Machala</option>
                                        <option value="10">BGR</option>
                                        <option value="11">Citibank (Ecuador)</option>
                                        <option value="12">Banco ProCredit (Ecuador)</option>
                                        <option value="13">UniBanco</option>
                                        <option value="14">Banco Solidario</option>
                                        <option value="15">Banco de Loja</option>
                                        <option value="16">Banco Territorial</option>
                                        <option value="17">Banco Coopnacional</option>
                                        <option value="18">Banco Amazonas</option>
                                        <option value="19">Banco Capital</option>
                                        <option value="20">Banco D-MIRO</option>
                                        <option value="21">Banco Finca</option>
                                        <option value="22">Banco Comercial de Manabí</option>
                                        <option value="23">Banco COFIEC</option>
                                        <option value="24">Banco del Litoral</option>
                                        <option value="25">Banco Delbank</option>
                                        <option value="26">Banco Sudamericano</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_tarjeta3" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">VISA</option>
                                        <option value="2">MASTER CARD</option>
                                        <option value="3">AMERICAN EXPRESS</option>
                                        <option value="4">DINNERS</option>
                                        <option value="5">DISCOVER</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_contado3" disabled>
                                        <option value='0'>SELECCIONE</option>
                                        <option value='1'>CORRIENTE</option>
                                        <option value='2'>3 meses</option>
                                        <option value='3'>6 meses</option>
                                        <option value='4'>9 meses</option>
                                        <option value='5'>12 meses</option>
                                        <option value='6'>18 meses</option>
                                        <option value='7'>36 meses</option>
                                        <option value='8'>8 dias</option>
                                        <option value='15'>15 dias</option>
                                        <option value='30'>30 dias</option>
                                        <option value='45'>45 dias</option>
                                        <option value='60'>60 dias</option>
                                        <option value='90'>90 dias</option>
                                        <option value='120'>120 dias</option>
                                    </select>
                                </td>
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad3" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="3" disabled onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                    <label hidden id="lblpago_cantidad3" lang="3"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma4" lang="4" onblur="habilitar(this), cambio_cmb(this), busqueda_ntscre(this)">
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">TARJETA DE CREDITO</option>
                                        <option value="2">TARJETA DE DEBITO</option>
                                        <option value="3">CHEQUE</option>
                                        <option value="4">EFECTIVO</option>
                                        <option value="5">CERTIFICADOS</option>
                                        <option value="6">TRANSFERENCIA</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito4" lang="4"  maxlength="17" onkeyup = "this.value = this.value.replace(/[^0-9-]/, '')">
                                    <input type="hidden" size="6" id="id_nota_credito4" lang="4" value="0">
                                    <input type="hidden" size="6" id="val_nt_cre4" lang="4" value="0">
                                </td>
                                <td>
                                    <select id="pago_banco4" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">Banco Pichincha</option>
                                        <option value="2">Banco del Pacífico</option>
                                        <option value="3">Banco de Guayaquil</option>
                                        <option value="4">Produbanco</option>
                                        <option value="5">Banco Bolivariano</option>
                                        <option value="6">Banco Internacional</option>
                                        <option value="7">Banco del Austro</option>
                                        <option value="8">Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                        <option value="9">Banco de Machala</option>
                                        <option value="10">BGR</option>
                                        <option value="11">Citibank (Ecuador)</option>
                                        <option value="12">Banco ProCredit (Ecuador)</option>
                                        <option value="13">UniBanco</option>
                                        <option value="14">Banco Solidario</option>
                                        <option value="15">Banco de Loja</option>
                                        <option value="16">Banco Territorial</option>
                                        <option value="17">Banco Coopnacional</option>
                                        <option value="18">Banco Amazonas</option>
                                        <option value="19">Banco Capital</option>
                                        <option value="20">Banco D-MIRO</option>
                                        <option value="21">Banco Finca</option>
                                        <option value="22">Banco Comercial de Manabí</option>
                                        <option value="23">Banco COFIEC</option>
                                        <option value="24">Banco del Litoral</option>
                                        <option value="25">Banco Delbank</option>
                                        <option value="26">Banco Sudamericano</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_tarjeta4" disabled>
                                        <option value="0">SELECCIONE</option>
                                        <option value="1">VISA</option>
                                        <option value="2">MASTER CARD</option>
                                        <option value="3">AMERICAN EXPRESS</option>
                                        <option value="4">DINNERS</option>
                                        <option value="5">DISCOVER</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="pago_contado4" disabled>
                                        <option value='0'>SELECCIONE</option>
                                        <option value='1'>CORRIENTE</option>
                                        <option value='2'>3 meses</option>
                                        <option value='3'>6 meses</option>
                                        <option value='4'>9 meses</option>
                                        <option value='5'>12 meses</option>
                                        <option value='6'>18 meses</option>
                                        <option value='7'>36 meses</option>
                                        <option value='8'>8 dias</option>
                                        <option value='15'>15 dias</option>
                                        <option value='30'>30 dias</option>
                                        <option value='45'>45 dias</option>
                                        <option value='60'>60 dias</option>
                                        <option value='90'>90 dias</option>
                                        <option value='120'>120 dias</option>
                                    </select>
                                </td>
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad4" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="4" disabled onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                    <label hidden id="lblpago_cantidad4" lang="4"></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" ></td>
                                <td align="right">Faltante</td>
                                <td align="right"><input type="text" style="text-align:right" readonly id="t_pagos" name="t_pagos" size="13" value="0"  /></td>
                            </tr>
                        </table>
                    </td>




                <tr><td colspan="2">
                        <table  id="tbl_dinamic" lang="0" border="0" cellspacing="0" cellpadding="0" >
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>CODIGO</th>
                                    <th>DESCRIPCION</th>
                                    <th>UNIDAD</th>
                                    <th <?php echo $hidden ?>>INVENTARIO</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>DESCUENTO%</th>
                                    <th>DESCUENTO $</th>
                                    <th>IVA</th>
                                    <th hidden>ICE%</th>
                                    <th hidden>ICE $</th>
                                    <th hidden>IRBPRN%</th>
                                    <th hidden>IRBPRN $</th>
                                    <th>VALOR TOTAL</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;
                                    $cns_det = $Set->lista_detalle_factura($rst[fac_id]);
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                    <tr>
                                        <td align="center"><input style="text-align:right " type ="text" size="1" class="itm" id="item1"  readonly value="1" lang="1"/></td>
                                        <td>
                                            <input style="text-align:left " type="text" size="25" id="pro_descripcion1"  value="" lang="1"   maxlength="16" onfocus="this.style.width = '300px';" onblur="this.style.width = '100px';"  list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"  />
                                        </td>
                                        <td>
                                            <input style="text-align:left " type ="text" size="40" class="refer"  id="pro_referencia1"   value="" lang="1" readonly style="width:300px;height:20px;font-size:11px;font-weight:100 "  />
                                            <input type="hidden"  id="pro_aux1"  lang="1" />
                                            <input type="hidden"  id="pro_ids1"  lang="1" />
                                            <input type="hidden"  id="mov_cost_unit1"  lang="1" />
                                            <input type="hidden"  id="mov_cost_tot1"  lang="1" />
                                        </td>
                                        <td><input type ="text" size="7"  id="unidad1"  value="" lang="1" readonly/></td>
                                        <td <?php echo $hidden ?>><input type ="text" size="7"  id="inventario1"  value="" lang="1" readonly <?php echo $hidden ?>/></td>
                                        <td><input type ="text" size="7"  id="cantidad1"  value="" lang="1" onchange="calculo(this), inventario(this), costo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                                        <td><input type ="text" size="7"  id="pro_precio1"  onchange="calculo(this)" value="" lang="1" readonly /></td>
                                        <td><input type ="text" size="7"  id="descuento1"  value="" lang="1" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                                        <td><input type ="text" size="7"  id="descuent1"  value="" lang="1" readonly  />
                                            <label hidden id="lbldescuent1" lang="1"></label>
                                        </td>
                                        <td><input type="text" id="iva1" size="5" value="" readonly /></td>
                                        <td hidden><input type="text" id="ice_p1" size="5" value="" readonly lang="1" /></td>
                                        <td hidden><input type="text" id="ice1" size="5" value="" readonly lang="1"/>
                                            <label hidden id="lblice1" lang="1"></label>
                                            <input type="hidden" id="ice_cod1" size="5" value="" readonly lang="1"/>
                                        </td>
                                        <td hidden><input type="text" id="irbp_p1" size="5" value="" readonly /></td>

                                        <td hidden><input type="text" id="irbp1" size="5" value="" readonly lang="1"/>
                                            <label hidden id="lblirbp1" lang="1"></label></td>
                                        <td>
                                            <input type ="text" size="9"  id="valor_total1"  value="" lang="1" readonly />
                                            <label hidden id="lblvalor_total1" lang="1"></label>
                                        </td>
                                        <td onclick="elimina_fila(this)" ><img class="auxBtn" width="12px" src="../img/del_reg.png" /></td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        $rst_prod = pg_fetch_array($Set->lista_un_producto_id($rst_det[pro_id]));
                                        if ($inv5 == 0) {
                                            if ($ctr_inv == 0) {
                                                $fra = '';
                                            } else {
                                                $fra = "and m.bod_id=$emisor";
                                            }
                                            $rst_inv = pg_fetch_array($Set->total_ingreso_egreso_fact($rst_det[pro_id], $fra));
                                            $inv = $rst_inv[ingreso] - $rst_inv[egreso] + $rst_det[dfc_cantidad];
                                            $rst2 = pg_fetch_array($Set->lista_un_movimiento_pro($rst_det[pro_id], $rst[fac_numero]));
                                            $rst2[mov_val_tot] = $rst2[mov_val_unit] * $rst_det[dfc_cantidad];

//                                            $rst2 = pg_fetch_array($Set->lista_costos_mov($rst_det[pro_id], $fra));
//                                            $rst2[mov_val_unit] = (round(($rst2[ingreso] - $rst2[egreso]), $dec) / round(($rst2[icnt] - $rst2[ecnt]), $dec));
//                                            $rst2[mov_val_tot] = $rst2[mov_val_unit] * $rst_det[dfc_cantidad];
                                        }
                                        ?>
                                        <tr>
                                            <td><input type ="text" size="1" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                            <td><input type="text" style="text-align:left;" size="25" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det[dfc_codigo] ?>" lang="<?PHP echo $n ?>" maxlength="16" onfocus="this.style.width = '400px';
                                                       " onblur="this.style.width = '100px';"  list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/> </td>
                                            <td>
                                                <input type ="text" style="text-align:left;" size="40"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det[dfc_descripcion] ?>" lang="<?PHP echo $n ?>" readonly/>
                                                <input type="hidden" size="7" id="pro_aux<?PHP echo $n ?>" value="<?php echo $rst_det[pro_id] ?>"/>
                                                <input type="hidden" size="7" id="mov_cost_unit<?PHP echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[mov_val_unit], $dec)) ?>" lang="<?PHP echo $n ?>"/>
                                                <input type="hidden" size="7" id="mov_cost_tot<?PHP echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[mov_val_tot], $dec)) ?>" lang="<?PHP echo $n ?>"/>
                                            </td>
                                            <td><input type ="text" size="7"  id="<?php echo 'unidad' . $n ?>"  value="<?PHP echo $rst_prod[mp_q] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                            <td <?php echo $hidden ?>><input type ="text" size="7"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, $dc)) ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det[dfc_cantidad], $dec)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this), inventario(this), costo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"  /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det[dfc_precio_unit], $dec)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" readonly /></td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det[dfc_porcentaje_descuento], $dec)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" />
                                            </td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det[dfc_val_descuento], $dec)) ?>" lang="<?PHP echo $n ?>"  readonly />
                                                <label hidden id="<?php echo 'lbldescuent' . $n ?>" lang="<?PHP echo $n ?>"><?php echo str_replace(',', '', $rst_det[dfc_val_descuento]) ?></label>
                                            </td>
                                            <td><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det[dfc_iva] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td hidden><input type="text" id="<?php echo 'ice_p' . $n ?>" size="5" value="<?php echo str_replace(',', '', number_format($rst_det[dfc_p_ice], $dec)) ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td hidden><input type="text" id="<?php echo 'ice' . $n ?>" size="5" value="<?php echo str_replace(',', '', number_format($rst_det[dfc_ice], $dec)) ?>" readonly lang="<?php echo $n ?>"/>
                                                <label hidden id="<?php echo 'lblice' . $n ?>" lang="<?php echo $n ?>"><?php echo $rst_det[dfc_ice] ?></label>
                                                <input type="hidden" id="<?php echo 'ice_cod' . $n ?>" size="5" value="<?php echo $rst_det[dfc_cod_ice] ?>" lang="<?PHP echo $n ?>"readonly />
                                            </td>
                                            <td hidden><input type="text" id="<?php echo 'irbp_p' . $n ?>" size="5" value="<?php echo str_replace(',', '', number_format($rst_det[dfc_p_irbpnr], $dec)) ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td hidden><input type="text" id="<?php echo 'irbp' . $n ?>" size="5" value="<?php echo str_replace(',', '', number_format($rst_det[dfc_irbpnr], $dec)) ?>" lang="<?PHP echo $n ?>" readonly />
                                                <label hidden id="<?php echo 'lblirbp' . $n ?>" lang="<?php echo $n ?>"><?php echo $rst_det[dfc_irbpnr] ?></label></td>

                                            <td>
                                                <input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det[dfc_precio_total], $dec)) ?>" readonly lang="<?PHP echo $n ?>"/>
                                                <label hidden id="<?php echo 'lblvalor_total' . $n ?>"  lang="<?PHP echo $n ?>"><?php echo str_replace(',', '', $rst_det[dfc_precio_total]) ?></label>
                                            </td>
                                            <td onclick="elimina_fila(this)" ><img class="auxBtn" width="12px" src="../img/del_reg.png" /></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>
                                <tr>
                                    <td>Observaciones:</td>
                                </tr>
                                <tr>

                                    <td valign="top" rowspan="11" colspan="7"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst[fac_observaciones] ?></textarea></td>    
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal 12%:</td>
                                    <td>
                                        <input style="text-align:right" type="text" size="12" id="subtotal12" value="<?php echo str_replace(',', '', number_format($rst['fac_subtotal12'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal12">  <?php echo str_replace(',', '', $rst[fac_subtotal12]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal 0%:</td>
                                    <td>
                                        <input style="text-align:right" type="text" size="12" id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst['fac_subtotal0'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal0"><?php echo str_replace(',', '', $rst[fac_subtotal0]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst['fac_subtotal_ex_iva'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotalex" ><?php echo str_replace(',', '', $rst[fac_subtotal_ex_iva]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst['fac_subtotal_no_iva'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotalno"><?php echo str_replace(',', '', $rst[fac_subtotal_no_iva]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Subtotal sin Impuestos:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal" value="<?php echo str_replace(',', '', number_format($rst['fac_subtotal'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal"><?php echo str_replace(',', '', $rst[fac_subtotal]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" value="<?php echo str_replace(',', '', number_format($rst['fac_total_descuento'], $dec)) ?>" readonly/>
                                        <label hidden id="lbltotal_descuento"><?php echo str_replace(',', '', $rst[fac_total_descuento]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Total ICE:</td>
                                    <td><input type="text" size="12" id="total_ice" value="<?php echo str_replace(',', '', number_format($rst[fac_total_ice], $dec)) ?>"  style="text-align:right" onchange="calculo()" readonly/>
                                        <label hidden id="lbltotal_ice" ><?php echo $rst[fac_total_ice] ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" value="<?php echo str_replace(',', '', number_format($rst[fac_total_iva], $dec)) ?>" readonly />
                                        <label hidden id="lbltotal_iva"><?php echo str_replace(',', '', $rst[fac_total_iva]) ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Total IRBPRN:</td>
                                    <td><input type="text" size="12" id="total_irbpnr" value="<?php echo str_replace(',', '', number_format($rst[fac_total_irbpnr], $dec)) ?>"  style="text-align:right" onchange="calculo()" readonly/>
                                        <label hidden id="lbltotal_irbpnr" ><?php echo $rst[fac_total_irbpnr] ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Propina:</td>
                                    <td><input type="text" size="15" id="total_propina" value="<?php echo str_replace(',', '', number_format($rst[fac_total_propina], $dec)) ?>"  style="text-align:right" onchange="calculo()"/>
                                        <label hidden id="lbltotal_propina" ><?php echo $rst[fac_total_propina] ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo $col ?>" align="right">Total Valor:</td>
                                    <td><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" value="<?php echo str_replace(',', '', number_format($rst[fac_total_valor], $dec)) ?>" readonly />
                                        <label hidden id="lbltotal_valor"><?php echo str_replace(',', '', $rst['fac_total_valor']) ?></label>
                                    </td>
                                </tr>
                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan="2"><button id="save" onclick="frm_save.lang = 1"  >Guardar</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<script>
    s = 0;
<?php
$cns_pagos1 = $Clase_pagos->lista_detalle_pagos($id);
while ($rts_combos = pg_fetch_array($cns_pagos1)) {
    if ($rts_combos[pag_forma] == '8') {
        $rst_nc = pg_fetch_array($Set->lista_cheques_id($rts_combos[pag_id_chq]));
        $val_nc = $rst_nc[chq_monto] - $rst_nc[chq_cobro] + $rts_combos[pag_cant];
        $disabl = 'true';
    } else {
        $val_nc = '0';
        $disabl = 'false';
    }
    ?>
        s++;
        tarjeta = '<?php echo $rts_combos[pag_tarjeta] ?>';
        $('#pago_tarjeta' + s).val(tarjeta);
        forma = '<?php echo $rts_combos[pag_forma] ?>';
        num_doc = '<?php echo $rts_combos[chq_numero] ?>';
        id_doc = '<?php echo $rts_combos[pag_id_chq] ?>';
        val_doc = '<?php echo $val_nc ?>';
        banco = '<?php echo $rts_combos[pag_banco] ?>';
        cant =<?php echo $rts_combos[pag_cant] ?>;
        con = '<?php echo $rts_combos[pag_contado] ?>';
        $('#pago_forma' + s).val(forma);
        $('#num_nota_credito' + s).val(num_doc);
        $('#id_nota_credito' + s).val(id_doc);
        $('#val_nt_cre' + s).val(val_doc);
        $('#pago_banco' + s).val(banco);
        $('#pago_cantidad' + s).val(cant.toFixed(dec));
        $('#lblpago_cantidad' + s).html(cant);
        $('#pago_contado' + s).val(con);
        $('#num_nota_credito' + s).attr('disabled', <?php echo $disabl ?>);
        //        habilitar(n);
        if ($('#pago_forma' + s).val() == '1') {
            $('#pago_banco' + s).attr('disabled', false);
            $('#pago_tarjeta' + s).attr('disabled', false);
            $('#pago_cantidad' + s).attr('disabled', false);
            $('#pago_contado' + s).attr('disabled', false);
            $('#pago_banco' + s).focus();
        } else if ($('#pago_forma' + s).val() == '2') {
            $('#pago_banco' + s).attr('disabled', false);
            $('#pago_tarjeta' + s).attr('disabled', false);
            $('#pago_cantidad' + s).attr('disabled', false);
            $('#pago_contado' + s).attr('disabled', true);
            $('#pago_banco' + s).focus();
        } else if ($('#pago_forma' + s).val() == '3') {
            $('#pago_banco' + s).attr('disabled', false);
            $('#pago_tarjeta' + s).attr('disabled', true);
            $('#pago_tarjeta' + s).val('0');
            $('#pago_contado' + s).attr('disabled', true);
            $('#pago_contado' + s).val('0');
            $('#pago_cantidad' + s).attr('disabled', false);
            $('#pago_banco' + s).focus();
        } else if ($('#pago_forma' + s).val() == '9') {
            $('#pago_banco' + s).attr('disabled', true);
            $('#pago_banco' + s).val('0');
            $('#pago_tarjeta' + s).attr('disabled', true);
            $('#pago_tarjeta' + s).val('0');
            $('#pago_contado' + s).attr('disabled', false);
            $('#pago_cantidad' + s).attr('disabled', false);
            $('#pago_contado' + s).focus();
        } else if ($('#pago_forma' + s).val() > '3') {
            $('#pago_banco' + s).attr('disabled', true);
            $('#pago_banco' + s).val('0');
            $('#pago_tarjeta' + s).attr('disabled', true);
            $('#pago_tarjeta' + s).val('0');
            $('#pago_contado' + s).attr('disabled', true);
            $('#pago_contado' + s).val('0');
            $('#pago_cantidad' + s).attr('disabled', false);
            $('#pago_cantidad' + s).focus();
        } else {
            $('#pago_banco' + s).attr('disabled', true);
            $('#pago_banco' + s).val('0');
            $('#pago_tarjeta' + s).attr('disabled', true);
            $('#pago_tarjeta' + s).val('0');
            $('#pago_contado' + s).attr('disabled', true);
            $('#pago_contado' + s).val('0');
            $('#pago_cantidad' + s).attr('disabled', true);
        }
    <?php
}
?>
    calculo_pago_locales();
</script>
<datalist id="productos">
    <?php
    if ($ctr_inv == 0) {
        $fra1 = '';
    } else {
        $fra1 = "and m.cod_punto_emision=$emisor";
    }
    $cns_pro = $Set->lista_producto_total($inv5, $fra1);
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        echo "<option value='$rst_pro[id]'> $rst_pro[mp_c] $rst_pro[mp_d]</option>";
    }
    if ($inv5 == 0) {
        $cns_ser_var = $Set->lista_pro_ser_var();
        while ($rst_pro_sv = pg_fetch_array($cns_ser_var)) {
            echo "<option value='$rst_pro_sv[id]'> $rst_pro_sv[mp_c] $rst_pro_sv[mp_d]</option>";
        }
    }
    ?>
</datalist>
