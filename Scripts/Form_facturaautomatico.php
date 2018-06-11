<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_pagos.php';
$Clase_pagos = new Clase_pagos();
$Set = new Set();
$emisor = $_GET[emisor];
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
            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else {
                        clona_fila('#tbl_form');
                    }

                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                if (emi == 1 || emi == 10) {
                    $('.vendedor').attr('hidden', true);
                    $('#vendedor').attr('hidden', true);
                    $('#vendedor').val('');
                } else {
                    $('.vendedor').attr('hidden', false);
                    $('#vendedor').attr('hidden', false);
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
                $(table).find("tbody tr:last").after(tr);

                //                $('#pro_descripcion' + x).focus();
                //                $('#mov_cantidad' + x).val('0');
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
                        vendedor.value
                        )

                var data2 = Array();
//                i = $('.itm').length;
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
                                iva + '& NO &' + dsc0 + '&' + lote  //Iva   Ice   Descuento $
                                );
                    }

                }
                if (emi != 1 && emi != 10) {
                    var data3 = Array();
                    n = 0;
                    while(n < 4){
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
                $.ajax({
                    beforeSend: function () {
                        loading('visible');
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
                        if($('#total_valor').val() > 20 && $('#nombre').val() == 'CONSUMIDOR FINAL'){
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
//                             if($('#total_valor').val() > 20){
//                                alert('noooo');
//                                return false;
//                            }
//                            else if (tipo_pago1.checked == false && tipo_pago2.checked == false && tipo_pago3.checked == false && tipo_pago4.checked == false && tipo_pago5.checked == false && tipo_pago6.checked == false && tipo_pago7.checked == false && tipo_pago8.checked == false) {
//                                alert('Escoja un Tipo de Pago');
//                                $('#tipo_pago1').focus();
//                                return false;
//                                $('#total_valor').val()
//                            }
                        }

                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 65, 'data[]': data, 'data2[]': data2, 'data3[]': data3, id: id},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            resp = envia_sri(dat[1]);
                        } else {
                            alert(dat[0]);
                        }


                    }
                })
            }

            function envia_sri(id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    timeout: 5000,
                    type: 'POST',
                    url: '../xml/factura_xml.php',
                    data: {id: id},
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            alert('Tiempo de Espera Agotado \n Sin Respuesta Del SRI \n Documento no Aurotizado');
                            window.history.go(0);
                        }
                    },
                    success: function (dt) {
                        dat = dt.split('&');
                        cambia_estado(dat, id);
                    }
                });


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
                                envi_mail(r[1]);
                            } else {
                                alert('Error en la conexion con el SRI \n Favor Revisar e intentar mas tarde');
                                window.history.go(0);
                            }

                        } else {
                            alert(dt);
                        }
                    }
                });




            }


            function envi_mail(id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Reports/pdf_factura_mail.php',
                    timeout: 5000,
                    data: {id: id},
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            alert('Tiempo de Espera Agotado 5s \n Autorizado Correctamente \n No se pudo enviar el documento via e-mail');
                            window.history.go(0);
                        }
                    },
                    success: function (dt) {
                        rs = dt.split('&');
                        if (rs[0] == 0) {
                            cambia_status_mail(rs[1]);
                        } else {
                            alert('No se pudo enviar ' + dt);
                            window.history.go(0);
                        }
                    }
                });


            }

            function cambia_status_mail(id) {
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
                        } else {
                            alert(dt);
                        }
                        window.history.go(0);
                    }
                });



//                                loading('hidden');
//                                $.post("actions.php", {act: 73,id: id},
//                                function (dt) {
//                                    if (dt == 0) {
//                                        alert('Factura Enviada Correctamente');
//                                        window.history.go(0);
//                                    } else {
//                                        alert(dt);
//                                    }
//                                });
            }





            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function load_cliente(obj) {
                $.post("actions.php", {act: 63, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
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


            function load_producto(obj) {

                $.post("actions.php", {act: 64, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + obj.lang).val(dat[0]);
                    $('#pro_referencia' + obj.lang).val(dat[1]);
                    $('#iva' + obj.lang).val(dat[4]);
                    $('#descuento' + obj.lang).val(dat[5]);
                    $('#descuent' + obj.lang).val(0);
                    $('#pro_aux' + obj.lang).val(dat[7]);
                    $('#lote' + obj.lang).val(dat[8]);

                    if (dat[3] == '') {
                        $('#pro_precio' + obj.lang).val(0);
                        $('#iva' + obj.lang).val('12');
                    } else {
                        $('#pro_precio' + obj.lang).val(dat[3]);
                    }
                    $('#lote' + obj.lang).focus();
                     calculo('1');
                });
            }

            function load_producto_noperti(obj) {

                $.post("actions.php", {act: 66, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_descripcion' + obj.lang).val(dat[0]);
                    $('#pro_referencia' + obj.lang).val(dat[1]);
                    $('#pro_aux' + obj.lang).val(dat[7]);
                    $('#lote' + obj.lang).val(dat[8]);
                    $('#iva' + obj.lang).val(dat[4]);
                    $('#descuento' + obj.lang).val(dat[5]);
                    $('#descuent' + obj.lang).val(0);
//                    
                    if (dat[3] == '') {
                        $('#pro_precio' + obj.lang).val(0);
                        $('#iva' + obj.lang).val('12');
                    } else {
                        $('#pro_precio' + obj.lang).val(dat[3]);
                    }
                    $('#lote' + obj.lang).focus();
                     calculo('1');
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
                if (obj == '1') {
                    obj = i;
                } else {
                    obj = obj.lang;
                }

                cnt = $('#cantidad' + obj).val().replace(',', '');
                pr = $('#pro_precio' + obj).val().replace(',', '');
                d = $('#descuento' + obj).val().replace(',', '');
                vtp = cnt * pr;//Valor total parcial
                vt = (vtp * 1) - (vtp * d / 100);
                $('#descuent' + obj).val((vtp * d / 100).toFixed(4));
                $('#valor_total' + obj).val(vt.toFixed(4));
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
//                    alert($('#item' + n).val());
                    if ($('#item' + n).val() == null) {
                        ob = 0;
                        val = 0;
                        d = 0;
                    } else {
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

                $('#subtotal12').val(t12.toFixed(4));
                $('#subtotal0').val(t0.toFixed(4));
                $('#subtotalex').val(tex.toFixed(4));
                $('#subtotalno').val(tno.toFixed(4));
                $('#total_descuento').val(tdsc.toFixed(4));
                $('#total_iva').val(tiva.toFixed(4));
                $('#total_valor').val(gtot.toFixed(4));
                if (emi == 1 || emi == 10) {
                    calculo_pago();
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
            #clientes{
                background: white;
                font-size: 10px;
                border:solid 2px;

            }
            #con_clientes{
                background:transparent;
                position:absolute;
                margin-left:30%;
                margin-top:0px;
                max-height:100% !important;
                /*width:100% !important;*/
                overflow:auto;
            }
            #clientes tr:hover{
                background:#BDE5F8;
                cursor:pointer;
            }
            #clientes input{
                font-size:10px;
            }
            *{
                font-size: 11px;
                font-weight:100; 
            }
        </style>

    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <div id="con_clientes" align="center">
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
                                    <input type="text" size="10" id="fecha_emision" value="<?php echo $rst['fecha_emision'] ?>" />
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
                                <td class="vendedor">Vendedor:
                                    <input type="text" size="" id="vendedor" hidden value="<?php echo $rst['vendedor'] ?>" />
                                </td>
                            </tr>
                            <?php
                            if ($emisor == 1 || $emisor == 10) {
                                ?>
                                <tr>
                                    <td>#PAGOS</td>
                                    <td><input type="text" style="width:70px " min="1" max="36"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" id="opg_codigo" value="<?php echo $rst['opg_codigo'] ?>" onblur="pago(this)" lang="1" /></td>
                                </tr>
                                <tr>
                                    <td align="center">PROCENTAJE</td>
                                    <td align="center">DIAS</td>
                                    <td align="center">VALOR</td>
                                    <td align="center">FECHA</td>
                                </tr>
                                <?php
                                //
                                //                                $n = 0;
                                //                                while ($rst2 = pg_fetch_array($cns_pag)) {
                                //
                            ?>
                                <tr id="pagos" >
                                    <!--<td colspan="2"></td>-->
                                    <td><input type="text" id="pag1" class="itme" value="<?php echo $rst['pag'] ?>" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_pago(this)"/>%</td>
                                    <td><input type="text" id="dias1" value="<?php echo $rst['dias'] ?>" lang="1"  onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onchange="calculo_fecha(this)"/></td>
                                    <td><input type="text" id="valor1" value="<?php echo $rst['valor_pago'] ?>" lang="1" readonly/></td>
                                    <td><input type="text" id="fecha1" value="<?php echo $rst['fecha_pago'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                                </tr>
                                <?php
                                //
                                //                                }
                                //
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
                                        <select id="pago_forma1">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_banco1">
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
                                        <select id="pago_tarjeta1">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td><input type="text" size="15" id="pago_cantidad1" value="<?php echo $rst['pago_cantidad'] ?>"</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma2">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_banco2">
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
                                        <select id="pago_tarjeta2">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td><input type="text" size="15" id="pago_cantidad2" value="<?php echo $rst['pago_cantidad'] ?>"</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma3">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_banco3">
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
                                        <select id="pago_tarjeta3">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td><input type="text" size="15" id="pago_cantidad3" value="<?php echo $rst['pago_cantidad'] ?>"</td>
                                </tr>
                                <tr>
                                    <td>
                                        <select id="pago_forma4">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">TARJETA DE CREDITO</option>
                                            <option value="2">TARJETA DE DEBITO</option>
                                            <option value="3">CHEQUE</option>
                                            <option value="4">EFECTIVO</option>
                                            <option value="5">CERTIFICADOS</option>
                                            <option value="6">BONOS</option>
                                            <option value="7">RETENCION</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="pago_banco4">
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
                                        <select id="pago_tarjeta4">
                                            <option value="0">SELECCIONE</option>
                                            <option value="1">VISA</option>
                                            <option value="2">MASTER CARD</option>
                                            <option value="3">AMERICAN EXPRESS</option>
                                            <option value="4">DINNERS</option>
                                            <option value="5">DISCOVER</option>
                                        </select>
                                    </td>
                                    <td><input type="text" size="15" id="pago_cantidad4" value="<?php echo $rst['pago_cantidad'] ?>"</td>
                                </tr>
                                <?php
                            }
                            ?>

                        </table>
                    </td>
                </tr>


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
                                        <?php
                                        if ($emisor == 1) {
                                            ?>
                                        <td><input type="text" size="30" id="pro_descripcion1" onchange="calculo(this)" value="" lang="1" list="productos"
                                                       onblur="load_producto(this), this.style.width = '100px'" onfocus="this.style.width = '400px'"   /></td>
                                                <?php
                                            } else if ($emisor != 1 || $emisor != 10) {
                                                ?>
                                            <td><input type="text" size="30" id="pro_descripcion1" onchange="calculo(this)" value="" lang="1" list="productos"
                                                       onblur="load_producto(this), this.style.width = '100px'" onfocus="this.style.width = '400px'" /></td>
                                                <?php
                                            }
                                            ?>
                                        <td>
                                            <input type ="text" size="40"  id="pro_referencia1"   value="" lang="1" readonly style="width:250px;height:20px;font-size:11px;font-weight:100 "  />
                                            <input type="text"  id="pro_aux1" hidden lang="1" />
                                        </td>
                                        <td><input type ="text" size="10"  id="lote1"  value="" lang="1" /></td>
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
                                            <td><input type="text" size="70" id="<?php echo 'pro_descripcion' . $n ?>" value="<?php echo $rst_det['cod_producto'] ?>" lang="<?PHP echo $n ?>" list="productos" onchange="load_producto(this), load_producto_noperti(this)"/></td>
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
                                    <td colspan="9" align="right">Sub Total 12%:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="subtotal12" value="<?php echo number_format($rst['subtotal12'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Sub Total 0%:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="subtotal0" value="<?php echo number_format($rst['subtotal0'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Sub Total Excento de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="subtotalex" value="<?php echo number_format($rst['subtotal_exento_iva'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Sub Total no objeto de Iva:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="subtotalno" value="<?php echo number_format($rst['subtotal_no_objeto_iva'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Total Descuento:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="total_descuento" value="<?php echo number_format($rst['total_descuento'], 4) ?>" readonly/></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Total IVA:</td>
                                    <td><input style="text-align:right" type="text" size="9" id="total_iva" value="<?php echo number_format($rst['total_iva'], 4) ?>" readonly /></td>
                                </tr>
                                <tr>
                                    <td colspan="9" align="right">Total Valor:</td>
                                    <td><input style="text-align:right;font-size:16px;color:red  " type="text" size="9" id="total_valor" value="<?php echo number_format($rst['total_valor'], 4) ?>" readonly /></td>
                                </tr>

                            </tfoot>
                        </table></td></tr>
                <tfoot>
                    <tr>
                        <td colspan="2"><button id="save" onclick="frm_save.lang = 1" >Guardar</button></td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<!--<datalist id="clientes">
<?php
//    $cns_cli = $Set->lista_clientes_tipo_codigo(1);
//    while ($rst_cli = pg_fetch_array($cns_cli)) {
//        $nm = $rst_cli[cli_ced_ruc] . ' ' . string($rst_cli[nombres]);
//        echo "<option value='$rst_cli[cli_ced_ruc]'>$nm</option>";
//    }
?>
</datalist>-->
<datalist id="productos" >
    <?php
    if ($emisor == 1) {
        $cns_pro1 = $Set->lista_productos_noperti();
        while ($rst_pro1 = pg_fetch_array($cns_pro1)) {
            echo "<option value='$rst_pro1[id]'>$rst_pro1[pro_a]  $rst_pro1[pro_b]</option>";
        }
        $cns_pro = $Set->lista_productos_industrial_like();
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[pro_codigo]'>$rst_pro[pro_codigo] $rst_pro[pro_descripcion]</option>";
        }
    } else if ($emisor == 10) {
        $cns_pro = $Set->lista_productos_industrial();
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[pro_codigo]'>$rst_pro[pro_codigo]  $rst_pro[pro_descripcion]</option>";
        }
    } else {

        //    if ($emisor != 10 || $emisor != 1) {
        $cns_pro1 = $Set->lista_productos_noperti();
        while ($rst_pro1 = pg_fetch_array($cns_pro1)) {
            echo "<option value='$rst_pro1[id]'>$rst_pro1[pro_a]  $rst_pro1[pro_b]</option>";
        }
        $cns_pro = $Set->lista_productos_industrial();
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[pro_codigo]'>$rst_pro[pro_codigo] $rst_pro[pro_descripcion]</option>";
        }
    }
    ?>
</datalist>
