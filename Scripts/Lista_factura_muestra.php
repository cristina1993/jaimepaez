<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
$Clase_pagos = new Clase_pagos();
$Set = new Set();
$emisor = $_GET[emisor];
$bodega = 'LOCAL 1';
if ($emisor == 10) {
    $ems = '010';
} else {
    $ems = '00' . $emisor;
}
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
    $rst_sec = pg_fetch_array($Set->lista_secuencial_documento($ems));
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
$cns_pag = $Clase_pagos->lista_detalle_pagos($fact);
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
            $(function () {
                $('input,select,textarea').each(function(){
                   this.disabled=true; 
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#pro_descripcion' + a).val().length != 0 && $('#cantidad' + a).val().length != 0) {
//                            var arr = Array();
//                            $('.refer').each(function () {
//                                arr.push(this.value);
//                            });
//                            var ar = eliminaDuplicados(arr);
//
//                            if (arr.length != ar.length) {
//                                
//                                alert('Existen items duplicados Porfavor revise');
//
//                            } else {
                            clona_fila('#tbl_form');
//                            }



                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                parent.document.getElementById('contenedor2').rows = "*,0%";
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
                        subtotal12.value.replace(',', ''), //subtotal12.value,
                        subtotal0.value.replace(',', ''), //subtotal0.value,
                        subtotalex.value.replace(',', ''), //subtotal_exento_iva.value,
                        subtotalno.value.replace(',', ''), //subtotal_no_objeto_iva.value,
                        total_descuento.value.replace(',', ''),
                        '0', //total_ice.value,
                        total_iva.value.replace(',', ''), //total_iva.value,
                        '0', //total_irbpnr.value,
                        '0', //total_propina.value,
                        total_valor.value.replace(',', ''),
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
                        observacion.value
                        )

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
                        pt = $('#valor_total' + n).val().replace(',', '');
                        dsc0 = $('#descuent' + n).val();
                        aux = $('#pro_aux' + n).val();
                        lote = $('#lote' + n).val();
                        data2.push(
                                ns[0] + ns[1] + ns[2] + '&' + //num_comprobante,
                                cod + '&' + //cod_producto,
                                aux + '&' + //cod_aux,
                                cnt.replace(',', '') + '&' + //cantidad,
                                desc + '&' + //descripcion,
                                '' + '&' + //detalle_adicional1,
                                '' + '&' + //detalle_adicional2,
                                pr.replace(',', '') + '&' + //precio_unitario,
                                dsc.replace(',', '') + '&' + //descuento,
                                pt.replace(',', '') + '&' + //precio_total,
                                iva + '& NO &' + dsc0 + '&' +
                                lote  //Iva   Ice   Descuento $
                                );
                    }

                }
                if (emi != 1 && emi != 10) {
                    var data3 = Array();
                    n = 0;
                    while (n < 4) {
                        n++;
                        pag_forma = $('#pago_forma' + n).val();
                        pag_banco = $('#pago_banco' + n).val();
                        pag_tarjeta = $('#pago_tarjeta' + n).val();
                        pag_cantidad = $('#pago_cantidad' + n).val();
                        data3.push(
                                emi + '&' +
                                pag_forma + '&' +
                                pag_banco + '&' +
                                pag_tarjeta + '&' +
                                pag_cantidad
                                );
                    }
                } else {
                    var data3 = Array();
                    i = $('.itme').length;
                    n = 0;
                    while (n < i) {
                        n++;
                        pag = $('#pag' + n).val();
                        dias = $('#dias' + n).val();
                        valor = $('#valor' + n).val();
                        fecha = $('#fecha' + n).val();
                        data3.push(
                                emi + '&' +
                                pag + '&' +
                                dias + '&' +
                                valor + '&' + //cod_producto,
                                fecha
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

                                }
                            }
                        }
                        if ($('#total_valor').val() > 20 && $('#nombre').val() == 'CONSUMIDOR FINAL') {
                            alert('PARA CONSUMIDOR FINAL EL VALOR TOTAL NO PUDE SER MAYOR $20');
                            return false;
                        }
                        if (emi == 1 || emi == 10) {
                            if ($('#opg_codigo').val().length == 0) {
                                $('#opg_codigo').css({borderColor: "red"});
                                $('#opg_codigo').focus();
                            }
                            if (pag.length != 0) {
                                while (j < pag.length) {
                                    j++;
                                    if ($('#pag' + j).val() == 0) {
                                        $('#pag' + j).css({borderColor: "red"});
                                        $('#opg_codigo').focus();
                                        return false;
                                    }
                                    else if ($('#dias' + j).val() == 0) {
                                        $('#dias' + j).css({borderColor: "red"});
                                        $('#dias' + j).focus();
                                        return false;
                                    }
                                    else if ($('#valor' + j).val().length == 0) {
                                        $('#valor' + j).css({borderColor: "red"});
                                        $('#valor' + j).focus();
                                        return false;
                                    }
                                    else if ($('#fecha' + j).val().length == 0) {
                                        $('#fecha' + j).css({borderColor: "red"});
                                        $('#fecha' + j).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        else if (emi != 1 && emi != 10) {
                            if ($('#vendedor').val().length == 0) {
                                $('#vendedor').css({borderColor: "red"});
                                $('#vendedor').focus();
                                return false;
                            }
                            sp = (parseFloat($('#pago_cantidad1').val()) * 1) + (parseFloat($('#pago_cantidad2').val()) * 1) + (parseFloat($('#pago_cantidad3').val()) * 1) + (parseFloat($('#pago_cantidad4').val()) * 1);
                            if (sp.toFixed(4) != $('#total_valor').val().replace(',', '')) {
                                alert('LA SUMA DE LOS PAGOS NO COINCIDEN CON EL TOTAL FACTURADO');
                                return false;
                            }
                        }
                        loading('visible');
                        $('#proceso').show();
                        $('#sri_cont').show();
                        $('#mail_cont').hide();
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
                        $('#descuent' + n).val((vtp * d / 100).toFixed(4));
                        $('#valor_total' + n).val(vt.toFixed(4));
                        ob = $('#iva' + n).val();
                        val = $('#valor_total' + n).val().replace(',', '');
                        d = $('#descuent' + n).val().replace(',', '');
                    }

                    tdsc = (tdsc * 1) + (d * 1);
                    if (ob == '14') {
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

                tiva = (t12 * 14 / 100);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1);
                $('#subtotal12').val(t12.toFixed(4));
                $('#subtotal0').val(t0.toFixed(4));
                $('#subtotalex').val(tex.toFixed(4));
                $('#subtotalno').val(tno.toFixed(4));
                $('#total_descuento').val(tdsc.toFixed(4));
                $('#total_iva').val(tiva.toFixed(4));
                $('#total_valor').val(gtot.toFixed(4));
                if (emi == 1 || emi == 10) {
                    calculo_pago();
                } else {
                    calculo_pago_locales();
                }

            }
            function calculo_pago_locales() {
                tp = (pago_cantidad1.value * 1) + (pago_cantidad2.value * 1) + (pago_cantidad3.value * 1) + (pago_cantidad4.value * 1);
                flt = (total_valor.value * 1) - tp;
                if ((flt * 1) < 0) {
                    alert('Valor ingresado incorrecto');
                } else {
                    t_pagos.value = flt.toFixed(4);
                }

            }
            function cancelar() {
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

            function calculo_pago() {
                itm = $('.itme').length;
                n = 0;
                while (n < itm) {
                    n++;
                    pag = $('#pag' + n).val().replace(',', '');
                    vt = $('#total_valor').val().replace(',', '');
                    tot = (pag / 100) * vt;
                    $('#valor' + n).val(tot.toFixed(4));
                    if ($('#pag' + n).val() > 100) {
                        alert('La cantidad es mayor a 100%');
                        $('#pag' + n).val(0);
                        $('#valor' + n).val(0);
                    }
                }
            }

            function calculo_fecha(obj) {
                n = obj.lang;
                var sumarDias = parseInt($('#dias' + n).val());
                var fecha = $('#fecha_emision').val();
                fecha = fecha.replace("-", "/").replace("-", "/");
                fecha = new Date(fecha);
                fecha.setDate(fecha.getDate() + sumarDias);
                var anio = fecha.getFullYear();
                var mes = fecha.getMonth() + 1;
                var dia = fecha.getDate();
                if (mes.toString().length < 2) {
                    mes = "0".concat(mes);
                }

                if (dia.toString().length < 2) {
                    dia = "0".concat(dia);
                }
                $('#fecha' + n).val(anio + "-" + mes + "-" + dia);
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
                if ($('#pago_forma' + s).val() == '1' || $('#pago_forma' + s).val() == '2') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                } else if ($('#pago_forma' + s).val() == '3') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                } else if ($('#pago_forma' + s).val() > '3') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                } else {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#pago_cantidad' + s).val('0');
                }
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
                $.post("actions.php", {act: 64, id: vl, lt: lt},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + j).val(dat[0]);
                    $('#pro_referencia' + j).val(dat[1]);
                    $('#iva' + j).val(dat[4]);
                    $('#descuent' + j).val(0);
                    $('#pro_aux' + j).val(dat[7]);
                    $('#lote' + j).val(dat[8]); ///comentar para codigo ean

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
                    calculo('1');
                });

            }


            function enter(e) {
                var char = e.which;
                if (char == 13) {
                    return false;
                }
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
                            if (sms != '') {
                                //alert('Lote incorrecto se utilizara el registro encontrado por codigo');
                                envia_sri(d1, 1);
                            }
                        } else {
                            alert(dt);
                        }

                    }
                });
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
                position:absolute;
                top:15%;
                left:30px; 
                color: #D8000C !important;
                background-color: #FFBABA;
                transform: rotate(-15deg);
                font-size: 20px !important;
            }
        </style>
    </head>
    <body>
        <div id="proceso">
            <font id="sri_cont"><img  src="../img/sri.ico" id="sri_ico" /> <img src="../img/load_circle.gif" id="sri_load" /></font>
            <font id="mail_cont"><img  src="../img/send_mail.png" id="mail_ico" /><img src="../img/load_circle.gif" id="mail_load" /></font>
        </div>
        <div id="cargando"></div>

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
                            <?php echo "FORMULARIO DE FACTURACIÓN ELECTRÓNICA " ?>
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
                                    <input type="text" size="20" id="num_secuencial" readonly value="<?php echo $rst['num_secuencial'] ?>"/>
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
                <tr><td><table id='tbl_colum2' border="0" ><tr class="trthead"><td  colspan="2" style="background:#00557F ;color:white " align='center' ><label class="tdtitulo">CLIENTE:</label></td></tr>
                            <tr>
                                <td style="width:80px ">RUC/CC:</td>
                                <td><input type="text" size="45"  id="identificacion" value="<?php echo $rst['identificacion'] ?>" onchange="load_cliente(this)"  /></td>
                            </tr>
                            <tr>
                                <td>NOMBRE:</td>
                                <td><input type="text"  size="45" id="nombre"  value="<?php echo $rst['nombre'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text"  size="45" id="direccion_cliente"  value="<?php echo $rst['direccion_cliente'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>TELEFONO:</td>
                                <td><input type="text"   size="45"  id="telefono_cliente"  value="<?php echo $rst['telefono_cliente'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>EMAIL:</td>
                                <td><input type="text"   size="45"  id="email_cliente"  value="<?php echo $rst['email_cliente'] ?>" style="text-transform:lowercase " /></td>
                            </tr>
                            <tr>
                                <td>PARROQUIA:</td>
                                <td><input type="text"  size="45"  id="cli_parroquia"  value="<?php echo $rst['cli_parroquia'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CIUDAD:</td>
                                <td><input type="text"  size="45"  id="cli_ciudad"  value="<?php echo $rst['cli_ciudad'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>PAIS:</td>
                                <td><input type="text"  size="45"  id="cli_pais"  value="<?php echo $rst['cli_pais'] ?>"  /></td>
                            </tr></table></td>
                    <td valign="top">
                        <table width='100%' id='tbl_colum3' border="0">
                            <td class="trthead" colspan="6" align='center' style="background:#00557F ;color:white " >
                                <label  class="tdtitulo">FORMAS DE PAGO</label>
                            </td>
                            <tr>
                                <td class="vendedor">Vendedor:</td>
                                <td>  
                                    <input type="text" size="" id="vendedor" value="<?php echo $rst['vendedor'] ?>" />
                                </td>
                            </tr>
                            <?php
                            if ($emisor == 1 || $emisor == 10) {
                                ?>
                                <tr>
                                    <td>#PAGOS</td>
                                    <td><input type="text"  min="1" max="36"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" id="opg_codigo" value="<?php echo $rst['opg_codigo'] ?>" onblur="pago(this)" lang="1" /></td>
                                </tr>
                                <tr>
                                    <td align="center">PROCENTAJE</td>
                                    <td align="center">DIAS</td>
                                    <td align="center">VALOR</td>
                                    <td align="center">FECHA</td>
                                </tr>
                                <?php
                                if ($num_pagos == 0) {
                                    ?>
                                    <tr id="pagos" >
                                        <td><input type="text" id="pag1" class="itme" value="<?php echo $rst['pag'] ?>" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                        <td><input type="text" id="dias1" value="<?php echo $rst['dias'] ?>" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                        <td><input type="text" id="valor1" value="<?php echo $rst['valor_pago'] ?>" lang="1" readonly/></td>
                                        <td><input type="text" id="fecha1" value="<?php echo $rst['fecha_pago'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_pag = pg_fetch_array($cns_pagos)) {
                                        $n++;
                                        ?>

                                        <tr id="pagos" >
                                            <td><input type="text" id="pag<?php echo $n ?>" class="itme" value="<?php echo $rst_pag['pag_porcentage'] ?>" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                            <td><input type="text" id="dias<?php echo $n ?>" value="<?php echo $rst_pag['pag_dias'] ?>" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                            <td><input type="text" id="valor<?php echo $n ?>" value="<?php echo $rst_pag['pag_valor'] ?>" lang="1" readonly/></td>
                                            <td><input type="text" id="fecha<?php echo $n ?>" value="<?php echo $rst_pag['pag_fecha_v'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <?php
                            } else {
                                ?>
                                <tr>
                                    <td>FORMA</td>
                                    <td>BANCO</td>
                                    <td>TARJETA</td>
                                    <td>CANTIDAD</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma1" lang="1" onblur="habilitar(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
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
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad1" value="0" onchange="calculo_pago_locales()" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma2" lang="2" onblur="habilitar(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
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
                                    <td align="right" ><input type="text" style="text-align:right" size="15" id="pago_cantidad2" value="0" onchange="calculo_pago_locales()" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma3" lang="3" onblur="habilitar(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
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
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad3" value="0" onchange="calculo_pago_locales()" disabled/></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma4" lang="4" onblur="habilitar(this)">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                            <option value="8">NOTA CREDITO</option>
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
                                    <td align="right"><input type="text" style="text-align:right" size="15" id="pago_cantidad4" value="0" onchange="calculo_pago_locales()" disabled/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" ></td>
                                    <td align="right">Faltante</td>
                                    <td align="right"><input type="text" style="text-align:right" readonly id="t_pagos" name="t_pagos" size="13" value="0"  /></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td>
                <tr><td colspan="2">
                        <table  id="tbl_dinamic" lang="0">
                            <thead>
                            <th>Item</th>
                            <th>CODIGO</th>
                            <th>DESCRIPCION</th>
                            <th>LOTE</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO</th>
                            <th>DESCUENTO%</th>
                            <th>DESCUENTO $</th>
                            <th>IVA</th>
                            <th>VALOR TOTAL</th>
                            <th>ACCIONES</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    $n = 0;

                                    if ($emisor == 1 || $emisor == 10) {
                                        $readOnly = "";
                                    } else {
                                        $readOnly = "readOnly";
                                    }

                                    $cns_det = $Set->lista_detalle_factura(str_replace('-', '', $rst['num_secuencial']));
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                    <tr>
                                        <td><input type ="text" size="4" class="itm" id="item1"  readonly value="1" lang="1"/></td>
                                        <td>
                                            <input type="text" size="25" id="pro_descripcion1"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"  />
                                        </td>
                                        <td>
                                            <input type ="text" size="40" class="refer"  id="pro_referencia1"   value="" lang="1" readonly style="width:300px;height:20px;font-size:11px;font-weight:100 "  />
                                            <input type="text"  id="pro_aux1" hidden lang="1" />
                                        </td>
                                        <td><input type ="text" size="10"  id="lote1"  value="" lang="1" maxlength="10" onkeypress="caracter(event, this, 1)" /></td>
                                        <td><input type ="text" size="7"  id="cantidad1"  value="" lang="1" onchange="calculo(this)" /></td>
                                        <td><input type ="text" size="7"  id="pro_precio1"  onchange="calculo(this)" value="" lang="1" <?php echo $readOnly ?> /></td>
                                        <td><input type ="text" size="7"  id="descuento1"  value="" lang="1" onchange="calculo(this)"  <?php echo $readOnly ?> /></td>
                                        <td><input type ="text" size="7"  id="descuent1"  value="" lang="1" readonly  /></td>
                                        <td><input type="text" id="iva1" size="5" value="" readonly /></td>
                                        <td>
                                            <input type ="text" size="9"  id="valor_total1"  value="" lang="1" readonly />
                                        </td>
                                        <td onclick="elimina_fila(this)" ><img class="auxBtn" width="16px" src="../img/b_delete.png" /></td>
                                    </tr>
                                    <?php
                                } else {
                                    while ($rst_det = pg_fetch_array($cns_det)) {
                                        $n++;
                                        ?>
                                        <tr>
                                            <td><input type ="text" size="4" class="itm" id="<?PHP echo 'item' . $n ?>"  lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/></td>
                                            <td><input type="text" size="25" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det['cod_producto'] ?>" lang="<?PHP echo $n ?>" maxlength="13" onfocus="asg_autocomplete(this)" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/></td>
                                            <td>
                                                <input type ="text" size="40"  id="<?php echo 'pro_referencia' . $n ?>"  value="<?php echo $rst_det['descripcion'] ?>" lang="<?PHP echo $n ?>" readonly/>
                                                <input type="text" size="35" id="pro_aux<?PHP echo $n ?>" hidden <?PHP echo $n ?> value="<?php echo $rst_det['cod_aux'] ?>"/>
                                            </td>
                                            <td><input type ="text" size="10"  id="<?php echo 'lote' . $n ?>"  value="<?php echo $rst_det['lote'] ?>" lang="<?PHP echo $n ?>" /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'cantidad' . $n ?>"  value="<?php echo $rst_det['cantidad'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" /></td>
                                            <td><input type ="text" size="7"  id="<?php echo 'pro_precio' . $n ?>"  value="<?php echo $rst_det['precio_unitario'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)"  /></td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuento' . $n ?>"  value="<?php echo $rst_det['descuento'] ?>" lang="<?PHP echo $n ?>" onchange="calculo(this)" />
                                            </td>
                                            <td>
                                                <input type ="text" size="7"  id="<?php echo 'descuent' . $n ?>"  value="<?php echo $rst_det['descuent'] ?>" lang="<?PHP echo $n ?>"  readonly />
                                            </td>

                                            <td><input type="text" id="<?php echo 'iva' . $n ?>" size="5" value="<?php echo $rst_det['iva'] ?>" lang="<?PHP echo $n ?>" readonly /></td>
                                            <td><input type ="text" size="9"  id="<?php echo 'valor_total' . $n ?>"  value="<?php echo number_format($rst_det['precio_total'], 4) ?>" lang="1" readonly lang="<?PHP echo $n ?>"/></td>
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

                                    <td valign="top" rowspan="7" colspan="7"><textarea id="observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst[observaciones] ?></textarea></td>    
                                    <td colspan="2" align="right">Sub Total 14%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal12" value="<?php echo number_format($rst['subtotal12'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Sub Total 0%:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotal0" value="<?php echo number_format($rst['subtotal0'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Sub Total Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalex" value="<?php echo number_format($rst['subtotal_exento_iva'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Sub Total no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="subtotalno" value="<?php echo number_format($rst['subtotal_no_objeto_iva'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_descuento" value="<?php echo number_format($rst['total_descuento'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="12" id="total_iva" value="<?php echo number_format($rst['total_iva'], 4) ?>" readonly /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total Valor:</td>
                                    <td><input style="text-align:right;font-size:15px;color:red  " type="text" size="12" id="total_valor" value="<?php echo number_format($rst['total_valor'], 4) ?>" readonly /></td>
                                </tr>

                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan=""><button id="save" onclick="return false"  >FACTURAR AUTORIZAR Y ENVIAR</button></td>
                        <td class="sms">Solicite para obtener el Módulo de Facturación Electrónica </td>
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
