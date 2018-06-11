<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
$Clase_pagos = new Clase_pagos();
$Set = new Set();
if ($emisor >= 10) {
    $ems = '0' . $emisor;
} else {
    $ems = '00' . $emisor;
}
if (isset($_GET[det])) {
    $dtr = $_GET[det];
} else {
    $dtr = 0;
}
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst_sec = pg_fetch_array($Docs->lista_secuencial_documento($emisor));
    $sec = ($rst_sec[secuencial] + 1);
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
    $rst['num_secuencial'] = $ems . '-001-' . $tx . $sec;
    $rst['fecha_emision'] = date('Y-m-d');
    $rst['vendedor'] = $rst_user[usu_person];
    $rst_enc = pg_fetch_array($Docs->lista_un_registro($id));
    $cns_det = $Docs->lista_detalle_registro_pedido($id);
    $cns_pag = $Docs->lista_pagos_registro_pedido($id);
    $num_doc = $rst_enc[ped_num_registro];
    $det = 1;
    $rst[opg_codigo] = pg_num_rows($Docs->lista_pagos_registro_pedido($id));
}
$rst_enc[ped_vendedor];
$rst_vend = pg_fetch_array($Docs->lista_vendedor($rst_enc[ped_vendedor]));
$id_vnd = $rst_vend[vnd_id];
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
            det = '<?php echo $det ?>';
            dtr = '<?php echo $dtr ?>';
            vnd = '<?php echo $id_vnd ?>';
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
                            clona_fila('#tbl_form');
                        }
                    }
                });
                calculo();
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                posicion_aux_window();
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
            function clona_fila(table, a) {
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
                if (a == 1) {
                    tr.find("td").attr("id", function () {
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
                }
                $(table).find("tbody tr:last").after(tr);
                if (a != 1) {
                    obj = $(table).find(".itm");
                    idt = obj[(obj.length - 1)].lang;
                    $('#pro_descripcion' + idt).focus();
                }
            }
            ;
            function save(id) {

                var data = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                f = fecha_emision.value.split('-');
                fch = f[0] + f[1] + f[2];
                ns = num_secuencial.value.split('-');
                sec = num_secuencial.value;
                cli_id = '<?php echo $rst_enc[cli_id] ?>';
                if (dtr == 1) {
                    estado = 0;
                } else {
                    estado = cli_estado.value;
                }

                data = Array(
                        emi,
                        cli_id,
                        vnd,
                        id, //ped_id,
                        fecha_emision.value,
                        num_secuencial.value,
                        nombre.value.toUpperCase(),
                        identificacion.value.toUpperCase(),
                        email_cliente.value.toUpperCase(),
                        direccion_cliente.value.toUpperCase(),
                        $('#lblsubtotal12').html().replace(',', ''),
                        $('#lblsubtotal0').html().replace(',', ''),
                        $('#lblsubtotalex').html().replace(',', ''),
                        $('#lblsubtotalno').html().replace(',', ''),
                        $('#lbltotal_descuento').html().replace(',', ''),
                        $('#lbltotal_ice').html().replace(',', ''), //total_ice.value,
                        $('#lbltotal_iva').html().replace(',', ''),
                        $('#lbltotal_irbp').html().replace(',', ''), //total_irbpnr.value,
                        $('#propina').val().replace(',', ''), //total_propina.value,
                        $('#lbltotal_valor').html().replace(',', ''),
                        telefono_cliente.value,
                        observacion.value.toUpperCase(),
                        cli_ciudad.value.toUpperCase(),
                        cli_pais.value.toUpperCase(),
                        cli_parroquia.value.toUpperCase(),
                        $('#lblsubtotal').html().replace(',', ''),
                        vendedor.value,
                        estado
                        );
                var data2 = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#pro_descripcion' + n).val() != null && parseFloat($('#cantidad' + n).val()) != 0) {
                        cod = $('#pro_descripcion' + n).val().toUpperCase();
                        desc = $('#pro_referencia' + n).val().toUpperCase();
                        cnt = $('#cantidad' + n).val();
                        und = $('#pro_unidad' + n).val();
                        pr = $('#pro_precio' + n).val();
                        dsc = $('#descuento' + n).val();
                        iva = $('#iva' + n).val().trim();
                        pt = $('#valor_total' + n).val().replace(',', '');
                        dsc0 = $('#descuent' + n).val();
                        pro_id = $('#pro_id' + n).val();
                        uni = $('#mov_cost_unit' + n).val();
                        ctot = $('#mov_cost_tot' + n).val();
                        irbrp = $('#lblirbp_val' + n).html();
                        irbp_p = $('#irbp_val' + n).val();
                        ic_p = $('#ice_p' + n).val();
                        ice = $('#lblice_val' + n).val();
                        ic_cod = $('#ice_cod' + n).val();
                        data2.push(
                                pro_id + '&' +
                                cod + '&' + //cod_producto,
                                cod + '&' + //cod_aux,
                                cnt + '&' + //cantidad,
                                desc + '&' + //descripcion,
                                pr + '&' +
                                dsc + '&' +
                                dsc0 + '&' +
                                pt + '&' +
                                iva + '&' +
                                ice + '&' + //ice
                                uni + '&' + //cost unitario
                                ctot + '&' + //cost total
                                irbrp + '&' + //irbrp  
                                ic_p + '&' + //ic_p   
                                ic_cod + '&' + //ic_cod   
                                irbp_p//irbp_p   
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
                        if (num_secuencial.value.length == 0) {
                            $("#num_secuencial").css({borderColor: "red"});
                            $("#num_secuencial").focus();
                            return false;
                        } else if (cod_punto_emision.value.length == 0) {
                            $("#cod_punto_emision").css({borderColor: "red"});
                            $("#cod_punto_emision").focus();
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
                                    } else if ($('#cantidad' + n).val().length == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    } else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    }

                                    if (parseFloat($('#cantidad' + n).val()) > 0) {
                                        if ($('#pro_ids' + n).val() != 79 && $('#pro_ids' + n).val() != 80) {
                                            if (parseFloat($('#inventario' + n).val()) < parseFloat($('#cantidad' + n).val())) {
                                                alert('LA CANTIDAD ES MAYOR QUE EL INVENTARIO');
                                                $('#cantidad' + n).css({borderColor: "red"});
                                                $('#cantidad' + n).focus();
                                                return false;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($('#total_valor').val() > 20 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $20');
                            return false;
                        }


                        if ($('#vendedor').val().length == 0) {
                            $('#vendedor').css({borderColor: "red"});
                            $('#vendedor').focus();
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
                        if (sp.toFixed(dec) != parseFloat($('#total_valor').val().replace(',', '')).toFixed(dec)) {
                            alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                            $('#save').attr('disabled', false);
                            return false;
                        }
                        if (parseFloat($('#total_valor').val()) == 0) {
                            alert('NO SE PUEDE GUARDAR UNA FACTURA CON VALOR CERO');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_factura.php',
                    data: {op: 2, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: 0, 'fields[]': fields, x: inven},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[2], dat[1]);
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Factura ya existe \n Debe hacer otra factura con otro Secuencial');
                            loading('hidden');
                        } else if (dat[0] == 2) {
                            alert('Una de las cuentas de la factura esta inactiva');
                            loading('hidden');
                        } else {
                            alert(dt);
                            loading('hidden');
                        }
                    }
                })
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#proceso').css('visibility', prop);
            }

            function load_cliente(obj) {
                $.post("actions.php", {act: 63, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                    } else {
                        dat = dt.split('&');
                        $('#identificacion').val(dat[0]);
                        $('#nombre').val(dat[1]);
                        $('#direccion_cliente').val(dat[2]);
                        $('#telefono_cliente').val(dat[3]);
                        $('#email_cliente').val(dat[4]);
                        $('#cli_parroquia').val(dat[5]);
                        $('#cli_ciudad').val(dat[6]);
                        $('#cli_pais').val(dat[7]);
                    }
                    $('#con_clientes').hide();
                });
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
                var stot = 0;
                var gtot = 0;
                var sal = 0;
                var ent = 0;
                var sol = 0;
                var tsal = 0;
                var tic = 0;
                var tib = 0;

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        d = 0;
                        cnt = 0;
                        pr = 0;
                        d = 0;
                        vtp = 0;
                        vt = 0;
                        entr = 0;
                        soli = 0;
                        ic = 0;
                        ib = 0;
                    } else {
                        cnt = ($('#cantidad' + n).val().replace(',', '') * 1);
                        entr = ($('#entregado' + n).val().replace(',', '') * 1);
                        soli = ($('#solicitado' + n).val().replace(',', '') * 1);
                        pr = ($('#pro_precio' + n).val().replace(',', '') * 1);
                        d = ($('#descuento' + n).val().replace(',', '') * 1);
                        pic = ($('#ice_p' + n).val().replace(',', '') * 1);
                        pib = ($('#irbp_val' + n).val().replace(',', '') * 1);
                        vtp = cnt * pr; //Valor total parcial
                        vt = (vtp * 1) - (vtp * d / 100);
                        vic = (pic * cnt) / 100;
                        vib = (pib * cnt);
                        $('#ice_val' + n).val(vic.toFixed(dec));
                        $('#lblice_val' + n).html(vic);
                        $('#irbp' + n).val(vib.toFixed(dec));
                        $('#lblirbp_val' + n).html(vib);
                        $('#descuent' + n).val((vtp * d / 100).toFixed(dec));
                        $('#lbldescuent' + n).html((vtp * d / 100).toFixed(6));
                        $('#valor_total' + n).val(vt.toFixed(dec));
                        $('#lblvalor_total' + n).html(vt);
                        ob = $('#iva' + n).val();
                        val = $('#valor_total' + n).val().replace(',', '');
                        d = $('#descuent' + n).val().replace(',', '');
                        ic = ($('#ice_val' + n).val().replace(',', '') * 1);
                        ib = ($('#lblirbp_val' + n).html().replace(',', '') * 1);
                    }
                    tdsc = (tdsc * 1) + (d * 1);
                    tic = (tic * 1) + (ic * 1);
                    tib = (tib * 1) + (ib * 1);
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
                    sol = sol + parseFloat(soli);
                    ent = ent + parseFloat(entr);
                    sal = sal + parseFloat(cnt);
                }
                prp = $('#propina').val().replace(',', '');
                tsal = sol - ((ent * 1) + (sal * 1));
                tiva = ((t12 + tic) * 12 / 100);
                stot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1 + tic * 1 + tib * 1 + prp * 1);
                $('#subtotal').val(stot.toFixed(dec));
                $('#lblsubtotal').html(stot);
                $('#subtotal12').val(t12.toFixed(dec));
                $('#lblsubtotal12').html(t12);
                $('#subtotal0').val(t0.toFixed(dec));
                $('#lblsubtotal0').html(t0);
                $('#subtotalex').val(tex.toFixed(dec));
                $('#lblsubtotalex').html(tex);
                $('#subtotalno').val(tno.toFixed(dec));
                $('#lblsubtotalno').html(tno);
                $('#total_descuento').val(tdsc.toFixed(dec));
                $('#lbltotal_descuento').html(tdsc);
                $('#total_ice').val(tic.toFixed(dec));
                $('#lbltotal_ice').html(tic);
                $('#total_iva').val(tiva.toFixed(dec));
                $('#lbltotal_iva').html(tiva);
                $('#total_irbp').val(tib.toFixed(dec));
                $('#lbltotal_irbp').html(tib);
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot);
                $('#cli_estado').val(tsal);
                pago_cantidad1.value = gtot.toFixed(dec);
                calculo_pago_locales();
                valores_lbl();
            }
            function calculo_pago_locales() {
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value.replace(',', '')) - tp.toFixed(dec);
                if (flt.toFixed(4) < 0) {
                    alert('Valor ingresado incorrecto');
                } else {
                    t_pagos.value = flt.toFixed(dec);
                }
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
//                parent.document.getElementById('mainFrame').src = '../';
            }
            function cerrar_ventana() {
                $('#con_clientes').hide();
            }

            function pago(obj) {
                n = 0;
                itm = $('.itme').length;
                if (obj.value <= 4) {
                    f = obj.value - itm;
                    if (f > 0) {
                        while (n < f) {
                            clona_fila('#tbl_colum3', 1);
                            n++;
                        }
                    } else {
                        f = Math.abs(f);
                        n = 0;
                        while (n < f) {
                            itm = $('.itme').length;
                            e = '#p' + itm;
                            elimina_fila(e, 1);
                            n++;
                        }
                    }
                }
            }

            function calculo_pago() {
                itm = $('.itme').length;
                n = 0;
                while (n < itm) {
                    n++;
                    pag = $('#pag' + n).val().replace(',', '');
                    vt = $('#total_valor').val().replace(',', '');
                    tot = (pag / 100) * vt;
                    $('#valor' + n).val(tot.toFixed(dec));
                    if ($('#pag' + n).val() > 100) {
                        alert('La cantidad es mayor a 100%');
                        $('#pag' + n).val(0);
                        $('#valor' + n).val(0);
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

                    $('#lote' + j).focus();

                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });


                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    $('#lote' + j).focus();
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
                    lt = $('#lote' + j).val();
                } else {
                    vl = $('#pro_descripcion' + j).val();
                    lt = 0;
                }
                $.post("actions.php", {act: 64, id: vl, lt: lt, s: emi},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + j).val(dat[0]);
                    $('#pro_referencia' + j).val(dat[1]);
                    $('#iva' + j).val(dat[4]);
                    $('#descuent' + j).val(0);
                    $('#codaux' + j).val(dat[7]);
                    $('#lote' + j).val(dat[8]); ///comentar para codigo ean
                    $('#pro_id' + j).val(dat[10] + dat[9]);
                    $('#cantidad' + j).val('');


                    if (dat[3] == '') {
                        $('#pro_precio' + j).val(0);
                        $('#iva' + j).val('12');
                    } else {
                        $('#pro_precio' + j).val(dat[3]);
                    }

                    if (dat[5] == '') {
                        $('#descuento' + j).val(0);
                    } else {
                        $('#descuento' + j).val(dat[5]);
                    }

                    if (dat[11] == '') {
                        $('#inventario' + j).val('0');
                    } else {
                        $('#inventario' + j).val(dat[11]);
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

            function asientos(sms, id1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: id1, x: 0, data: num_secuencial.value, emi: emi},
                    success: function (dt) {
                        loading('hidden');
                        if (dt == 0) {
                            auxWindow(0, id1);
                        } else {
                            alert(dt);
                        }

                    }
                });
            }




            function inventario(obj) {
                n = obj.lang;
                if ($('#pro_ids' + n).val() != 79 && $('#pro_ids' + n).val() != 80) {
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        calculo();
                    }
                } else {
                    calculo();
                }
            }

            function solicitado(obj) {
                n = obj.lang;
                if (parseFloat($('#solicitado' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE LO SOLICTADO');
                    $(obj).val('0');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    calculo();
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
                            }
                        });
                    } else {
                        $('#num_nota_credito' + s).val('');
                        $('#id_nota_credito' + s).val('0');
                        $('#val_nt_cre' + s).val('');
                    }
                } else {
                    alert('Debe elejir un cliente');
                    $('#pago_forma' + s).val(0);
                    $('#pago_cantidad' + s).attr('disabled', true);
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
                    } else {
                        dat = dt.split('&');
                        $('#num_nota_credito' + n).val(dat[0]);
                        $('#pago_cantidad' + n).val(dat[1]);
                        $('#id_nota_credito' + n).val(dat[2]);
                        $('#val_nt_cre' + n).val(dat[1]);
                        $('#pago_cantidad' + n).focus();
                        calculo_pago_locales();
                    }
                    if (dt == 1) {
                        $('#num_nota_credito' + n).val('');
                        $('#id_nota_credito' + n).val('0');
                        $('#val_nt_cre' + n).val('');
                        $('#pago_cantidad' + n).val(0);
                        $('#pago_cantidad' + n).attr('disabled', true);
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
                            "<option value='90'>90 dias</option>";
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
                text-transform: uppercase;
            }
            select{
                width: 150px;
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" hidden></div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form" border="1">
                <thead>
                    <tr>
                        <th colspan="10" >
                            <?php echo "FORMULARIO DE FACTURA " . $bodega ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="5">
                        <table>
                            <tr>
                                <td>Factura N:</td>
                                <td>
                                    <input type="text" size="20" id="num_secuencial" readonly value="<?php echo $rst['num_secuencial'] ?>"/>
                                    <input type="hidden" id="cod_punto_emision" value="<?php echo $emisor ?>" />
                                </td>
                                <td>Fecha Pedido:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_pedido" readonly value="<?php echo $rst_enc['ped_femision'] ?>" />
                                </td>
                                <td>Fecha Emision:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_emision" readonly value="<?php echo $rst['fecha_emision'] ?>" />
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr><td><table id='tbl_colum2' border="0" ><tr class="trthead"><td  colspan="2" style="background:#00557F ;color:white " align='center' ><label class="tdtitulo">CLIENTE:</label></td></tr>
                            <tr>
                                <td style="width:80px ">RUC/CC:</td>
                                <td><input type="text" size="45"  id="identificacion" readonly value="<?php echo $rst_enc[ped_ruc_cc_cliente] ?>" onchange="load_cliente(this)"  /></td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre" readonly value="<?php echo $rst_enc[ped_nom_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente" readonly value="<?php echo $rst_enc[ped_dir_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente"  value="<?php echo $rst_enc[ped_tel_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td><input type="text"   size="45"  id="email_cliente"  value="<?php echo $rst_enc[ped_email_cliente] ?>" style="text-transform:lowercase " /></td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia"  value="<?php echo $rst_enc[ped_parroquia_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad"  value="<?php echo $rst_enc[ped_ciu_cliente] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais"  value="<?php echo $rst_enc[ped_pais_cliente] ?>"  /></td>
                                <td><input type="hidden"  size="5"  id="cli_estado" readonly value="" /></td>
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
                                        <option value="6">BONOS</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito1" lang="1" disabled >
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
                                        <option value="6">BONOS</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito2" lang="2" disabled >
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
                                        <option value="6">BONOS</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito3" lang="3" disabled >
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
                                        <option value="6">BONOS</option>
                                        <option value="7">RETENCION</option>
                                        <option value="8">NOTA CREDITO</option>
                                        <option value="9">CREDITO</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="num_nota_credito4" lang="4" disabled >
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
                        <table  id="tbl_dinamic" lang="0">
                            <thead>
                            <th>Item</th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th>INVENTARIO</th>
                            <th>SOLICITADO</th>
                            <th>ENTREGADO</th>
                            <th>SALDO</th>
                            <th>PRECIO</th>
                            <th>DESCUENTO%</th>
                            <th>DESCUENTO $</th>
                            <th>IVA</th>
                            <th>ICE%</th>
                            <th>ICE $</th>
                            <th>IRBPRN $</th>
                            <th>VALOR TOTAL</th>
                            <!--<th>ACCIONES</th>-->
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;

                                        $rst_ent = pg_fetch_array($Docs->sum_entregado($id, $rst_det[pro_id], $rst_det[det_tab]));
                                        if ($rst_ent[suma] == '') {
                                            $rst_ent[suma] = 0;
                                        }
                                        $saldo = $rst_det['det_cantidad'] - $rst_ent[suma];
                                        if ($inv5 == 0) {
                                            if ($ctr_inv == 0) {
                                                $fra = '';
                                            } else {
                                                $fra = "and m.bod_id=$emisor ";
                                            }
                                            $rst_inv = pg_fetch_array($Set->total_ingreso_egreso_fact($rst_det[pro_id], $fra));
                                            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                            $rst2 = pg_fetch_array($Set->lista_costos_mov($rst_det[pro_id], $fra));
                                            $rst2[mov_val_unit] = (($rst2[ingreso] - $rst2[egreso]) / ($rst2[icnt] - $rst2[ecnt]));
                                            $rst2[mov_val_tot] = $rst2[mov_val_unit] * $saldo;
                                            $rst_ids = pg_fetch_array($Set->lista_un_producto_id($rst_det[pro_id]));
                                        }
                                        ?>
                                    <tr>
                                        <td><input type ="text" size="4" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                        <td><input type="text" size="20" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det['det_cod_producto'] ?>" lang="<?PHP echo $n ?>" maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" readonly /></td>
                                        <td>
                                            <input type ="text" size="35"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det['det_descripcion'] ?>" lang="<?PHP echo $n ?>" readonly/>
                                            <input type ="text" size="15"  id="<?php echo 'pro_id' . $n ?>"  value="<?php echo $rst_det[pro_id] ?>" lang="1" hidden/>
                                            <input type ="text" size="15"  id="<?php echo 'pro_ids' . $n ?>"  value="<?php echo $rst_ids[ids] ?>" lang="1" hidden />
                                            <input type="hidden" size="7" id="<?PHP echo 'mov_cost_unit' . $n ?>" value="<?php echo $rst2[mov_val_unit] ?>" lang="<?PHP echo $n ?>"/>
                                            <input type="hidden" size="7" id="<?PHP echo 'mov_cost_tot' . $n ?>" value="<?php echo $rst2[mov_val_tot] ?>" lang="<?PHP echo $n ?>"/>
                                        </td>
                                        <td><input type ="text" size="7"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo $inv ?>" lang="1" readonly/></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'solicitado' . $n ?>"  value="<?php echo $rst_det['det_cantidad'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'entregado' . $n ?>"  value="<?php echo $rst_ent[suma] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo $saldo ?>" lang="<?PHP echo $n ?>" onblur=" inventario(this), solicitado(this), calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                        <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo $rst_det['det_vunit'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" readonly /></td>
                                        <td>
                                            <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo $rst_det['det_descuento_porcentaje'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" readonly />
                                        </td>
                                        <td>
                                            <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  lang="<?PHP echo $n ?>"  readonly />
                                            <label hidden id="<?php echo 'lbldescuent' . $n ?>" lang="<?PHP echo $n ?>"><?php echo $rst_det['det_descuento_moneda'] ?></label>
                                        </td>

                                        <td><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det['det_impuesto'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type="text" id="<?php echo 'ice_p' . $n ?>" size="5" value="<?php echo $rst_det['det_p_ice'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type="text" id="<?php echo 'ice_val' . $n ?>" size="5" lang="<?PHP echo $n ?>" readonly />
                                            <label hidden id="<?php echo 'lblice_val' . $n ?>" lang="<?PHP echo $n ?>"></label>
                                            <input type="hidden" id="<?php echo 'ice_cod' . $n ?>" size="5" value="<?php echo $rst_det['det_cod_ice'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                        <td><input type="text" id="<?php echo 'irbp' . $n ?>" size="5"  lang="<?PHP echo $n ?>" readonly />
                                            <input type="hidden" id="<?php echo 'irbp_val' . $n ?>" size="5" value="<?php echo $rst_det['det_irbp'] ?>" lang="<?PHP echo $n ?>" readonly />
                                            <label hidden id="<?php echo 'lblirbp_val' . $n ?>" lang="<?PHP echo $n ?>"></label></td>
                                        <td><input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>" readonly lang="<?PHP echo $n ?>"/>
                                            <label hidden id="<?php echo 'lblvalor_total' . $n ?>" lang="<?PHP echo $n ?>"></label></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Observaciones:</td>
                                </tr>
                                <tr>
                                    <td valign="top" rowspan="11" colspan="8"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst[observaciones] ?></textarea></td>    
                                    <td colspan="6" align="right">Subtotal 12%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal12" readonly/>
                                        <label hidden id="lblsubtotal12"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Subtotal 0%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal0" readonly/>
                                        <label hidden id="lblsubtotal0"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Subtotal Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" readonly/>
                                        <label hidden id="lblsubtotalex"><?php echo $rst_enc['ped_sbt_excento'] ?></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Subtotal no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" readonly/>
                                        <label hidden id="lblsubtotalno"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Subtotal sin impuestos:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal" readonly/>
                                        <label hidden id="lblsubtotal"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" readonly/>
                                        <label hidden id="lbltotal_descuento"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Total ICE:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_ice" readonly/>
                                        <label hidden id="lbltotal_ice"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" readonly />
                                        <label hidden id="lbltotal_iva"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Total IRBPRN:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_irbp" readonly/>
                                        <label hidden id="lbltotal_irbp"></label></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Propina:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="propina" value="<?php echo str_replace(',', '', number_format($rst_enc['ped_propina'], $dec)) ?>"  onchange="calculo()"/></td>
                                </tr>
                                <tr>
                                    <td colspan="6" align="right">Total Valor:</td>
                                    <td colspan="2"><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" readonly />
                                        <label hidden id="lbltotal_valor"></label></td>
                                </tr>

                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan="2"><button id="save" onclick="frm_save.lang = 1" >FACTURAR</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<script>
    n = 0;
<?php
while ($rts_combos = pg_fetch_array($cns_pagos)) {
    ?>
        tarjeta =<?php echo $rts_combos[pag_tarjeta] ?>;
        forma =<?php echo $rts_combos[pag_forma] ?>;
        banco =<?php echo $rts_combos[pag_banco] ?>;
        cant =<?php echo $rts_combos[pag_cant] ?>;
        n++;
        $('#pago_tarjeta' + n).val(tarjeta);
        $('#pago_forma' + n).val(forma);
        $('#pago_banco' + n).val(banco);
        $('#pago_cantidad' + n).val(cant);
        habilitar(n);
    <?php
}
?>
</script>
<datalist id="productos">
    <?php
    $cns_pro = $Set->lista_producto_total($emisor);
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[tbl] . $rst_pro[id] ?>" label="<?php echo $rst_pro[lote] . ' ' . $rst_pro[codigo] . ' ' . $rst_pro[descripcion] ?>" />
        <?php
    }
    ?>
</datalist>
