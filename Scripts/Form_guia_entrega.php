<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
$Clase_pagos = new Clase_pagos();
$Set = new Set();
$emisor = 1;
$bodega = '';
$ems = '0001';
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Set->lista_una_factura_id($id));
    $rst[num_secuencial] = $rst[num_documento];
    $f1 = substr($rst['fecha_emision'], 0, 4);
    $f2 = substr($rst['fecha_emision'], 4, 2);
    $f3 = substr($rst['fecha_emision'], -2);
    $rst['fecha_emision'] = $f1 . '-' . $f2 . '-' . $f3;
    $cns_pagos = $Clase_pagos->lista_detalle_pagos($rst[num_documento]);
    $num_pagos = pg_num_rows($Clase_pagos->lista_detalle_pagos($rst[num_documento]));
    $rst['opg_codigo'] = $num_pagos;
    $det = 1;
} else {
    $rst_sec = pg_fetch_array($Set->lista_secuencial_entrega());
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
    $id = 0;
    $rst['vendedor'] = $rst_user[usu_person];
    $num_pagos = 0;
    $det = 0;
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
            det = '<?php echo $det ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';

            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#pro_descripcion' + a).val().length != 0 && $('#cantidad' + a).val().length != 0) {
                            if (a < 9) {
                                clona_fila('#tbl_form');
                            }
                        }
                    }
                });
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
                f = fecha_emision.value.split('-');
                fch = f[0] + f[1] + f[2];
                ns = num_secuencial.value.split('-');
                sec = num_secuencial.value;
                data = Array(
                        ns[2],
                        nombre.value,
                        identificacion.value,
                        fch,
                        '0', //num_guia_remision.value,
                        '0', //cod_numerico.value,//sri
                        '01', //tipo_comprobante.value,
//                        $('#lblsubtotal12').html().replace(',', ''), //subtotal12.value,
                        '0',
                        $('#lblsubtotal0').html().replace(',', ''), //subtotal0.value,
                        '0',
                        '0',
//                        $('#lblsubtotalex').html().replace(',', ''), //subtotal_exento_iva.value,
//                        $('#lblsubtotalno').html().replace(',', ''), //subtotal_no_objeto_iva.value,
                        $('#lbltotal_descuento').html().replace(',', ''),
                        '0', //total_ice.value,
//                        $('#lbltotal_iva').html().replace(',', ''), //total_iva.value,
                        '0',
                        '0', //total_irbpnr.value,
                        '0', //total_propina.value,
                        $('#lbltotal_valor').html().replace(',', ''),
                        direccion_cliente.value,
                        email_cliente.value,
                        telefono_cliente.value,
                        cli_ciudad.value,
                        cli_pais.value,
                        '1', //cod_establecimiento_emisor,
                        cod_punto_emision.value, //cod_punto_emision
                        cli_parroquia.value,
                        sec,
                        vendedor.value,
                        observacion.value,
                        fecha_emision.value,
                        '0',
                        '0',
                        '1'
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
                        und = $('#pro_unidad' + n).val();
                        pr = $('#pro_precio' + n).val();
                        dsc = $('#descuento' + n).val();
                        iva = $('#iva' + n).val().trim();
                        pt = $('#lblvalor_total' + n).html().replace(',', '');
                        dsc0 = $('#lbldescuent' + n).html().replace(',', '');
                        pro_id = $('#pro_aux' + n).val();
                        uni = $('#mov_cost_unit' + n).val();
                        ctot = $('#mov_cost_tot' + n).val();
                        data2.push(
                                ns[0] + ns[1] + ns[2] + '&' + //num_comprobante,
                                cod + '&' + //cod_producto,
                                cnt.replace(',', '') + '&' + //cantidad,
                                desc + '&' + //descripcion,
                                '' + '&' + //detalle_adicional1,
                                '' + '&' + //detalle_adicional2,
                                pr.replace(',', '') + '&' + //precio_unitario,
                                dsc.replace(',', '') + '&' + //descuento,
                                pt.replace(',', '') + '&' + //precio_total,
                                iva + '& NO &' +
                                dsc0 + '&' +
                                pro_id + '&' +
                                uni + '&' +
                                ctot//Iva   Ice   Descuento $
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
                    data3.push(
                            emi + '&' +
                            pag_forma + '&' +
                            pag_banco + '&' +
                            pag_tarjeta + '&' +
                            pag_cantidad + '&' +
                            pag_contado
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
                        if (num_secuencial.value.length != 18) {
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
                        }
                        else if (cli_ciudad.value.length == 0) {
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
                                    }
                                    else if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                    else if ($('#descuento' + n).val().length == 0) {
                                        $('#descuento' + n).css({borderColor: "red"});
                                        $('#descuento' + n).focus();
                                        return false;
                                    }
                                    else if ($('#pro_precio' + n).val() == 0) {
                                        $('#pro_precio' + n).css({borderColor: "red"});
                                        $('#pro_precio' + n).focus();
                                        return false;
                                    }

                                }
                            }
                        }
                        if ($('#total_valor').val() > 20 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $20');
                            return false;
                        }
                        if ($('#vendedor').val() == 0) {
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
                        if (sp.toFixed(dec) != $('#total_valor').val().replace(',', '')) {
                            alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                            return false;
                        }

                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 65, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: id, 'fields[]': fields},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[2], dat[1]);
                        } else {
                            alert(dat[0]);
                        }
                    }
                })
            }
            function envia_sri(id, a) {
                $.ajax({
                    beforeSend: function () {

                    },
                    timeout: 10000,
                    type: 'POST',
                    url: '../xml/factura_xml.php',
                    data: {id: id},
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            confirm_sri(id, a);
                        }
                    },
                    success: function (dt) {
                        dat = dt.split('&');
                        cambia_estado(dat, id);
                    }
                });
            }

            function confirm_sri(id, s)
            {
                if (a != 3) {
                    var r = confirm("Error de conexion con el SRI \n Desea Enviar Nuevamente");
                    if (r == true) {
                        a = s + 1;
                        envia_sri(id, a);
                    } else {
                        window.history.go(0);
                    }
                } else {
                    alert('Error de conexion con el SRI \n Intente envio mas tarde');
                    window.history.go(0);
                }
            }

            function cambia_estado(dat, id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 67, id: id, 'data[]': dat},
                    success: function (dt) {
                        r = dt.split('&');
                        if (r[0] == 0) {
                            if (dat[4].length == 38) {
                                envi_mail(r[1], 1);
                            } else {
                                alert(dat[3]);
                                loading('hidden');
                            }

                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function envi_mail(id, a) {
                $('#proceso').show();
                $('#sri_cont').hide();
                $('#mail_cont').show();

                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Reports/pdf_factura_mail.php',
                    timeout: 10000,
                    data: {id: id},
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            confirm_email(id, a);
                        }
                    },
                    success: function (dt) {
                        rs = dt.split('&');
                        if (rs[0] == 0) {
                            cambia_status_mail(rs[1], id);
                        } else {
                            alert('No se pudo enviar ' + dt);
                            window.history.go(-1);
                        }
                    }
                });
            }

            function confirm_email(id, s)
            {
                if (a != 3) {
                    var r = confirm("Servidor e-mail inaccesible \n Desea Enviar Via e-mail Nuevamente");
                    if (r == true) {
                        a = s + 1;
                        envi_mail(id, a);
                    } else {
                        window.history.go(-1);
                    }
                } else {
                    alert('Error de conexion con el Servidor e-mail \n Intente envio mas tarde');
                    window.history.go(-1);
                }
            }

            function cambia_status_mail(id, num) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 73, id: id},
                    success: function (dt) {
                        loading('hidden');
                        if (dt == 0) {
                            alert('Documento Autorizado y Enviado Correctamente');
//                            window.history.go(0);
                            if (emi == 1 || emi == 10) {
                                auxWindow(0, num);
                            } else {
                                auxWindow(1, num);
                            }

                        } else {
                            alert(dt);
                        }

                    }
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_cliente(obj) {
                $.post("actions.php", {act: 63, id: obj.value, s: 0},
                function (dt) {
//                    dat = dt.split('%');
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Se creará uno nuevo');
                        $('#nombre').focus();
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Se creará uno nuevo');
                        $('#nombre').focus();
                    }
                    else {
                        dat = dt.split('&');
                        if (dat[10] == 1 || dat[10] == 2) {
                            alert('Cliente se encuentra Inactivo o Suspendido');
                            $('#identificacion').focus();
                        } else {
                            $('#identificacion').val(dat[0]);
                            $('#nombre').val(dat[1]);
                            $('#direccion_cliente').val(dat[2]);
                            $('#telefono_cliente').val(dat[3]);
                            $('#email_cliente').val(dat[4]);
                            $('#cli_parroquia').val(dat[5]);
                            $('#cli_ciudad').val(dat[6]);
                            $('#cli_pais').val(dat[7]);
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
                    calculo('1');
                } else {
                    alert('No puede eliminar todas las filas');
                }
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
                    } else {
                        cnt = $('#cantidad' + n).val().replace(',', '');
                        pr = $('#pro_precio' + n).val().replace(',', '');
                        d = $('#descuento' + n).val().replace(',', '');
                        vtp = cnt * pr; //Valor total parcial
                        vt = (vtp * 1) - (vtp * d / 100);
                        $('#descuent' + n).val((vtp * d / 100).toFixed(dec));
                        $('#lbldescuent' + n).html((vtp * d / 100).toFixed(6));
                        $('#valor_total' + n).val(vt.toFixed(dec));
                        $('#lblvalor_total' + n).html(vt.toFixed(6));
                        ob = $('#iva' + n).val();
                        val = $('#valor_total' + n).val().replace(',', '');
                        d = $('#descuent' + n).val().replace(',', '');
                    }

                    tdsc = (tdsc * 1) + (d * 1);
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

                tiva = (t12 * 12 / 100);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);

//                $('#subtotal12').val(t12.toFixed(dec));
//                $('#lblsubtotal12').html(t12.toFixed(6));
                $('#subtotal0').val(t0.toFixed(dec));
                $('#lblsubtotal0').html(t0.toFixed(6));
//                $('#subtotalex').val(tex.toFixed(dec));
//                $('#lblsubtotalex').html(tex.toFixed(6));
//                $('#subtotalno').val(tno.toFixed(dec));
//                $('#lblsubtotalno').html(tno.toFixed(6));
                $('#total_descuento').val(tdsc.toFixed(dec));
                $('#lbltotal_descuento').html(tdsc.toFixed(6));
//                $('#total_iva').val(tiva.toFixed(dec));
//                $('#lbltotal_iva').html(tiva.toFixed(6));
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot.toFixed(6));

                pago_cantidad1.value = gtot.toFixed(dec);
                $('#lblpago_cantidad1').html(gtot.toFixed(6));
                calculo_pago_locales();
                valores_lbl();
            }
            function calculo_pago_locales() {
                tp = parseFloat(pago_cantidad1.value) + parseFloat(pago_cantidad2.value) + parseFloat(pago_cantidad3.value) + parseFloat(pago_cantidad4.value);
                flt = parseFloat(total_valor.value.replace(',', '')) - tp.toFixed(dec);
                if (flt.toFixed(dec) < 0) {
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
                    $('#pro_descripcion' + j).val(dat[1]);
                    $('#pro_referencia' + j).val(dat[2]);
                    $('#iva' + j).val('0');
                    $('#pro_aux' + j).val(dat[0]);
                    $('#mov_cost_unit' + j).val(parseFloat(dat[10]).toFixed(dec));

                    if (dat[3] == '') {
                        $('#pro_precio' + j).val(0);
//                        $('#iva' + j).val('12');
                    } else {
                        $('#pro_precio' + j).val(parseFloat(dat[3]).toFixed(dec));
                    }

                    if (dat[5] == '') {
                        $('#descuento' + j).val(0);
                    } else {
                        $('#descuento' + j).val(parseFloat(dat[5]).toFixed(dec));
                    }
                    if (dat[6] == '') {
                        $('#inventario' + j).val(0);
                    } else {
                        $('#inventario' + j).val(parseFloat(dat[6]).toFixed(dc));
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
                n = obj.lang;
                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                    $(obj).val('');
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

            function asientos(sms, d1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: num_secuencial.value, x: det},
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
//                            if (sms != '') {
//                                envia_sri(d1, 1);
//                            }
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
                            "<option value='1'>Contado</option>" +
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
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form" >
                <thead>
                    <tr>
                        <th colspan="10" >
                            <?php echo "FORMULARIO GUIA DE ENTREGA" ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>Guia Entrega N:</td>
                                <td>
                                    <?php
                                    if ($_SESSION[usuid] == 1) {
                                        $rd_nf = "";
                                    } else {
                                        $rd_nf = "readOnly";
                                    }
                                    ?>
                                    <input type="text" <?php echo $rd_nf ?> size="20" id="num_secuencial"  value="<?php echo $rst['num_secuencial'] ?>"/>
                                    <input type="hidden" id="cod_punto_emision" value="<?php echo $emisor ?>" />
                                </td>
                                <td>Fecha:</td>
                                <td>
                                    <input type="text" size="10" id="fecha_emision" readonly value="<?php echo $rst['fecha_emision'] ?>" />
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
                                <td><input type="text" size="45"  id="identificacion" value="<?php echo $rst['identificacion'] ?>" onchange="load_cliente(this)"  /></td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre"  value="<?php echo $rst['nombre'] ?>" onblur="this.value = this.value.toUpperCase()" /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente"  value="<?php echo $rst['direccion_cliente'] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente"  value="<?php echo $rst['telefono_cliente'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td>
                                    <input type="email"   size="45"  id="email_cliente"  value="<?php echo $rst['email_cliente'] ?>" style="text-transform:lowercase " />
                                </td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia"  value="<?php echo $rst['cli_parroquia'] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad"  value="<?php echo $rst['cli_ciudad'] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais"  value="<?php echo $rst['cli_pais'] ?>"  onblur="this.value = this.value.toUpperCase()"/></td>
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
                                    <select id="pago_forma1" lang="1" onblur="habilitar(this), cambio_cmb(this)">
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
                                        <option value='1'>Contado</option>
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
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad1" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="1" disabled/>
                                    <label hidden id="lblpago_cantidad1" lang="1"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma2" lang="2" onblur="habilitar(this), cambio_cmb(this)">
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
                                        <option value='1'>Contado</option>
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
                                <td align="right" ><input type="text" style="text-align:right" size="15" id="pago_cantidad2" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="2" disabled/>
                                    <label hidden id="lblpago_cantidad2" lang="2"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma3" lang="3" onblur="habilitar(this), cambio_cmb(this)">
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
                                        <option value='1'>Contado</option>
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
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad3" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="3" disabled/>
                                    <label hidden id="lblpago_cantidad3" lang="3"></label>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <select id="pago_forma4" lang="4" onblur="habilitar(this), cambio_cmb(this)">
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
                                        <option value='1'>Contado</option>
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
                                <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad4" value="0" onchange="calculo_pago_locales(), pag_sig(this)" lang="4" disabled/>
                                    <label hidden id="lblpago_cantidad4" lang="4"></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" ></td>
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
                                    <th>INVENTARIO</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>DESCUENTO%</th>
                                    <th>DESCUENTO $</th>
                                    <th hidden>IVA</th>
                                    <th>VALOR TOTAL</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;
                                    $cns_det = $Set->lista_detalle_factura(str_replace('-', '', $rst['num_secuencial']));
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                    <tr>
                                        <td align="center"><input style="text-align:right " type ="text" size="1" class="itm" id="item1"  readonly value="1" lang="1"/></td>
                                        <td>
                                            <input style="text-align:left " type="text" size="25" id="pro_descripcion1"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';"  list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"  />
                                        </td>
                                        <td>
                                            <input style="text-align:left " type ="text" size="40" class="refer"  id="pro_referencia1"   value="" lang="1" readonly style="width:300px;height:20px;font-size:11px;font-weight:100 "  />
                                            <input type="text"  id="pro_aux1" hidden lang="1" />
                                            <input type="hidden"  id="mov_cost_unit1"  lang="1" />
                                            <input type="hidden"  id="mov_cost_tot1"  lang="1" />
                                        </td>
                                        <td><input type ="text" size="7"  id="inventario1"  value="" lang="1" readonly/></td>
                                        <td><input type ="text" size="7"  id="cantidad1"  value="" lang="1" onchange="calculo(this), inventario(this), costo(this)" /></td>
                                        <td><input type ="text" size="7"  id="pro_precio1"  onchange="calculo(this)" value="" lang="1"  /></td>
                                        <td><input type ="text" size="7"  id="descuento1"  value="" lang="1" onchange="calculo(this)" /></td>
                                        <td><input type ="text" size="7"  id="descuent1"  value="" lang="1" readonly  />
                                            <label hidden id="lbldescuent1" lang="1"></label>
                                        </td>
                                        <td hidden><input type="text" id="iva1" size="5" value="" readonly /></td>
                                        <td>
                                            <input type ="text" size="9"  id="valor_total1"  value="" lang="1" readonly />
                                            <label hidden id="lblvalor_total1" lang="1"></label>
                                        </td>
                                        <td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        $rst_pro = pg_fetch_array($Set->lista_un_producto_industrial($rst_det['cod_producto']));
                                        $pro_id = $rst_pro[id];
                                        $rst_inv = pg_fetch_array($Set->total_ingreso_egreso_fact($pro_id, '0'));
                                        $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                        $rst2 = pg_fetch_array($Set->lista_costos_mov($pro_id));
                                        ?>
                                        <tr>
                                            <td><input type ="text" size="1" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                            <td><input type="text" style="text-align:left;" size="25" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det['cod_producto'] ?>" lang="<?PHP echo $n ?>" maxlength="13" onfocus="this.style.width = '400px';
                                                       " onblur="this.style.width = '100px';"  list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/> </td>
                                            <td>
                                                <input type ="text" style="text-align:left;" size="40"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det['descripcion'] ?>" lang="<?PHP echo $n ?>" readonly/>
                                                <input type="hidden" size="7" id="pro_aux<?PHP echo $n ?>" value="<?php echo $pro_id ?>"/>
                                                <input type="hidden" size="7" id="mov_cost_unit<?PHP echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[mov_val_unit], $dec)) ?>" lang="<?PHP echo $n ?>"/>
                                                <input type="hidden" size="7" id="mov_cost_tot<?PHP echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst2[mov_val_tot], $dec)) ?>" lang="<?PHP echo $n ?>"/>
                                            </td>
                                            <td><input type ="text" size="7"  id="<?php echo 'inventario' . $n ?>"  value="<?php echo str_replace(',', '', number_format($inv, $dc)) ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det['cantidad'], $dc)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this), costo(this)" onblur="inventario(this)" /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det['precio_unitario'], $dec)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)"  /></td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det['descuento'], $dec)) ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" />
                                            </td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det['descuent'], $dec)) ?>" lang="<?PHP echo $n ?>"  readonly />
                                                <label hidden id="<?php echo 'lbldescuent' . $n ?>" lang="<?PHP echo $n ?>"><?php echo str_replace(',', '', $rst_det['descuent']) ?></label>
                                            </td>
                                            <td hidden><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det['iva'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td>
                                                <input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst_det['precio_total'], $dec)) ?>" readonly lang="<?PHP echo $n ?>"/>
                                                <label hidden id="<?php echo 'lblvalor_total' . $n ?>"  lang="<?PHP echo $n ?>"><?php echo str_replace(',', '', $rst_det['precio_total']) ?></label>
                                            </td>
                                            <td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>
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

                                    <td valign="top" rowspan="7" colspan="6"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst[observaciones] ?></textarea></td>    
<!--                                    <td colspan="2" align="right">Sub Total 12%:</td>
                                    <td>
                                        <input style="text-align:right" type="text" size="12" id="subtotal12" value="<?php echo str_replace(',', '', number_format($rst['subtotal12'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal12">  <?php echo str_replace(',', '', $rst['subtotal12']) ?></label>
                                    </td>
                                </tr>
                                <tr>-->
                                    <td colspan="2" align="right">Sub Total:</td>
                                    <td>
                                        <input style="text-align:right" type="text" size="12" id="subtotal0" value="<?php echo str_replace(',', '', number_format($rst['subtotal0'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotal0"><?php echo str_replace(',', '', $rst['subtotal0']) ?></label>
                                    </td>
                                </tr>
<!--                                <tr>
                                    <td colspan="2" align="right">Sub Total Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" value="<?php echo str_replace(',', '', number_format($rst['subtotal_exento_iva'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotalex" ><?php echo str_replace(',', '', $rst['subtotal_exento_iva']) ?></label>
                                    </td>
                                </tr>-->
<!--                                <tr>
                                    <td colspan="2" align="right">Sub Total no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" value="<?php echo str_replace(',', '', number_format($rst['subtotal_no_objeto_iva'], $dec)) ?>" readonly/>
                                        <label hidden id="lblsubtotalno"><?php echo str_replace(',', '', $rst['subtotal_no_objeto_iva']) ?></label>
                                    </td>
                                </tr>-->
                                <tr>
                                    <td colspan="2" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" value="<?php echo str_replace(',', '', number_format($rst['total_descuento'], $dec)) ?>" readonly/>
                                        <label hidden id="lbltotal_descuento"><?php echo str_replace(',', '', $rst['total_descuento']) ?></label>
                                    </td>
                                </tr>
<!--                                <tr>
                                    <td colspan="2" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" value="<?php echo str_replace(',', '', number_format($rst['total_iva'], $dec)) ?>" readonly />
                                        <label hidden id="lbltotal_iva"><?php echo str_replace(',', '', $rst['total_iva']) ?></label>
                                    </td>
                                </tr>-->
                                <tr>
                                    <td colspan="2" align="right">Total Valor:</td>
                                    <td><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" value="<?php echo str_replace(',', '', number_format($rst['total_valor'], $dec)) ?>" readonly />
                                        <label hidden id="lbltotal_valor"><?php echo str_replace(',', '', $rst['total_valor']) ?></label>
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
    n = 0;
<?php
$cns_pagos1 = $Clase_pagos->lista_detalle_pagos($rst[num_documento]);
while ($rts_combos = pg_fetch_array($cns_pagos1)) {
    ?>
        n++;
        tarjeta = '<?php echo $rts_combos[pag_tarjeta] ?>';
        $('#pago_tarjeta' + n).val(tarjeta);
        forma = '<?php echo $rts_combos[pag_forma] ?>';
        banco = '<?php echo $rts_combos[pag_banco] ?>';
        cant =<?php echo $rts_combos[pag_cant] ?>;
        con = '<?php echo $rts_combos[pag_contado] ?>';
        $('#pago_forma' + n).val(forma);
        $('#pago_banco' + n).val(banco);
        $('#pago_cantidad' + n).val(cant.toFixed(dec));
        $('#lblpago_cantidad' + n).html(cant);
        $('#pago_contado' + n).val(con);
        habilitar(n);
    <?php
}
?>
</script>
<datalist id="productos">
    <?php
    $cns_pro = $Set->lista_producto_total();
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[id] ?>" label="<?php echo $rst_pro[mp_c] . ' ' . $rst_pro[mp_d] ?>" />
        <?php
    }
    ?>
</datalist>
