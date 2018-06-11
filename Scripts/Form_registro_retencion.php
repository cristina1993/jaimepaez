<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_retencion.php';
$Reg_retencion = new Clase_reg_retencion();
$emisor = 1;
$id = $_GET[id];
$x = $_GET[x];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Reg_retencion->lista_una_retencion($id));
    $fac = pg_fetch_array($Reg_retencion->lista_una_factura_id($rst[fac_id]));
    $rst_sal = pg_fetch_array($Reg_retencion->suma_pagos_factura($rst[fac_id]));
    if ($_GET[est] != 2) {
        $saldo = (round($rst_sal[cantidad], 2) + round($rst_sal[debito], 2)) - (round($rst_sal[credito], 2) - round($rst[rgr_total_valor], 2));
    } else {
        $saldo = (round($rst_sal[cantidad], 2) + round($rst_sal[debito], 2)) - round($rst_sal[credito], 2);
    }

    if ($_GET[est] != 2) {
        $det = 1;
        $est = 0;
        $read = '';
        $pg_ret = '0';
    } else {
        $det = 0;
        $est = $_GET[est];
        $read = 'readonly';
        $pg_ret = $rst[rgr_total_valor];
    }

    if ($rst[rgr_fec_autorizacion] == '1900-01-01') {
        $rst[rgr_fec_autorizacion] = '';
    }
    if ($rst[rgr_fec_caducidad] == '1900-01-01') {
        $rst[rgr_fec_caducidad] = '';
    }
    $rst2[drr_ejercicio_fiscal] = date('m/Y');
    $rst2[drr_base_imponible] = '0';
    $rst2[drr_procentaje_retencion] = '0';
    $rst2[drr_valor] = '0';
    if ($rst[rgr_denominacion_comp] == 1) {
        $iva = $fac[fac_total_iva];
        $base = $fac[fac_subtotal];
    } else {
        $dis_c='disabled';
        $iva = 0;
        $base = 0;
    }
    
} else {
    $id = '0';
    $det = '0';
    $saldo = '0';
    $est = 0;
    $rst = pg_fetch_array($Reg_retencion->lista_secuencial_retencion());
    if (!empty($rst)) {
        $sec = ($rst[sec] + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '000000000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '00000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '0000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '000000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '00000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '0000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $txt = '000';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $txt = '00';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $txt = '0';
        } else if ($sec >= 1000000000 && $sec < 10000000000) {
            $txt = '';
        }
    } else {
        $txt = '0000000001';
    }
    $rst[rgr_num_registro] = $txt . $sec;
    $rst[rgr_fecha_emision] = date('Y-m-d');
    $rst[rgr_fec_registro] = date('Y-m-d');
    $rst2[drr_ejercicio_fiscal] = date('m/Y');
    $rst2[drr_base_imponible] = '0';
    $rst2[drr_procentaje_retencion] = '0';
    $rst2[drr_valor] = '0';
    $rst[cli_id] = '0';
    $iva = 0;
    $base = 0;
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
            var usu =<?php echo $emisor ?>;
            var det =<?php echo $det ?>;
            var std =<?php echo $est ?>;
            var dec = '2';
            var dc = '1';
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#ejercicio_fiscal' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_registro", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_aut", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                Calendar.setup({inputField: "fecha_cad", ifFormat: "%Y-%m-%d", button: "im-campo3"});
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo4"});
                posicion_aux_window();
                if (det == '0') {
                    sec_automatico();
                }
                s();

            });

            function save(id) {
                if (fecha_aut.value == '') {
                    fec_aut = '1990-01-01';
                } else {
                    fec_aut = fecha_aut.value;
                }

                if (fecha_cad.value == '') {
                    fec_cad = '1990-01-01';
                } else {
                    fec_cad = fecha_cad.value;
                }
                var data = Array(
                        cli_id.value,
                        num_comprobante.value,
                        nombre.value,
                        identificacion.value,
                        num_comp_retenido.value,
                        autorizacion.value,
                        tipo_comprobante.value,
                        $('#total').html(),
                        fecha_emision.value,
                        num_registro.value,
                        fec_aut,
                        fecha_registro.value,
                        fec_cad,
                        $('#fac_id').val(),
                        '1'///estado
                        );

                var data2 = Array();
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                while (n < i) {
                    n++;
                    if ($('#ejercicio_fiscal' + n).val() != null) {
                        imp = $('#por_id' + n).val().split('_');
                        por_id = imp[0];
                        ejercicio_fiscal = $('#ejercicio_fiscal' + n).val();
                        base_imponible = $('#base_imponible' + n).val();
                        codigo = $('#codigo' + n).val();
                        porcentaje_retencion = $('#porcentaje_retencion' + n).val();
                        valor_retenido = $('#lblvalor_retenido' + n).html();
                        tipo_impuesto = imp[1];
                        data2.push(
                                por_id + '&' +
                                ejercicio_fiscal + '&' +
                                base_imponible + '&' +
                                codigo + '&' +
                                porcentaje_retencion + '&' +
                                valor_retenido + '&' +
                                tipo_impuesto
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
                        //Validaciones antes de enviar
                        if ($('#tipo_comprobante').val() == 1) { // valida si el tipo de documento es factura y no otros documentos 
                            if ($('#num_comp_retenido').val().length == 0) {
                                $('#num_comp_retenido').css({borderColor: "red"});
                                $('#num_comp_retenido').focus();
                                return false;
                            }
                        }
                        if ($('#num_comprobante').val().length == 0) {
                            $('#num_comprobante').css({borderColor: "red"});
                            $('#num_comprobante').focus();
                            return false;
                        } else if ($('#autorizacion').val().trim().length == 0 || ($('#autorizacion').val().trim().length != 10 && $('#autorizacion').val().trim().length != 37)) {
                            $('#autorizacion').css({borderColor: "red"});
                            $('#autorizacion').focus();
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
                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        while (n < i) {
                            n++;
                            if ($('#ejercicio_fiscal' + n).val() != null) {
                                if ($('#ejercicio_fiscal' + n).val().length == 0) {
                                    $('#ejercicio_fiscal' + n).css({borderColor: "red"});
                                    $('#ejercicio_fiscal' + n).focus();
                                    return false;
                                } else if ($('#descripcion_impuesto' + n).val().length == 0) {
                                    $('#descripcion_impuesto' + n).css({borderColor: "red"});
                                    $('#descripcion_impuesto' + n).focus();
                                    return false;
                                } else if ($('#base_imponible' + n).val().length == 0) {
                                    $('#base_imponible' + n).css({borderColor: "red"});
                                    $('#base_imponible' + n).focus();
                                    return false;
                                } else if ($('#porcentaje_retencion' + n).val().length == 0) {
                                    $('#porcentaje_retencion' + n).css({borderColor: "red"});
                                    $('#porcentaje_retencion' + n).focus();
                                    return false;
                                }
                            }
                        }
                        if ($('#tipo_comprobante').val() == 1) {
                            if (parseFloat($('#total').html()) > parseFloat($('#saldo').val())) {
                                alert('El saldo de la factura $' + parseFloat($('#saldo').val()).toFixed(2) + ' es menor \nal total de la retencion $' + parseFloat($('#total').html()).toFixed(2));
                                return false;
                            }
                        }
                    }
                    ,
                    type: 'POST',
                    url: 'actions_reg_retencion.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else if (dt == 1) {
                            alert('Una de las cuentas de la Retencion esta inactiva');
                        } else if (dt == 3) {
                            alert('Numero Secuencial del Registro ya existe \n Debe hacer otro Registro con otro Secuencial');
                            $('#guardar').attr('disabled', true);
                        } else if (dt == 4) {
                            alert('No se puede registrar esta retencion porque la \n factura tiene un saldo menor al valor de la retencion');
                        } else if (dt == 5) {
                            alert('EL numero de Documento y el Ruc del Proveedor \n Ya existen en el Registro de Retencionesno');
                            $('#num_comprobante').val('');
                            $('#num_comprobante').focus();
                            $('#num_comprobante').css({borderColor: "red"});
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
            }
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    if (parts[1] == 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }

                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                tr.find("label").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    if (parts[1] == 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }

                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#ejercicio_fiscal' + x).focus();
                $('#base_imponible' + x).val('0');
                $('#valor_retenido' + x).val('0');
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    s();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function descripcion(obj) {
                $('#frm_save').attr('lang', 1);
                id = obj.value;
                n = obj.lang;
                $.post('actions_reg_retencion.php', {op: 2, id: id}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $('#base_imponible' + n).val('0');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        if (dat[5] == 0) {
                            alert('El impuesto no tiene una Cuenta Contable asignada');
                            $('#descripcion_impuesto' + n).val('');
                            $('#descripcion_impuesto' + n).focus();
                        } else if (dat[6] == 1) {
                            alert('La Cuenta Contable del Impuesto \n Esta Inactiva');
                            $('#descripcion_impuesto' + n).val('');
                            $('#descripcion_impuesto' + n).focus();
                        } else {
                            $('#descripcion_impuesto' + n).val(dat[0]);
                            $('#porcentaje_retencion' + n).val(dat[1]);
                            $('#codigo' + n).val(dat[2]);
                            $('#por_id' + n).val(dat[3]);
                            $('#num_comp_retenido' + n).attr('readonly', true);
                            var iva = parseFloat($('#iva').val());
                            var base = parseFloat($('#base').val());

                            if (dat[4] == 'IV') {
                                $('#base_imponible' + n).val(iva.toFixed(dec));
                            } else if (dat[4] == 'IR') {
                                $('#base_imponible' + n).val(base.toFixed(dec));
                            }
                            s();
                        }
                    }

                })
            }


            function s() {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sum = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() != null) {
                        var vt = (parseFloat($('#base_imponible' + n).val()) * parseFloat($('#porcentaje_retencion' + n).val())) / 100;
                        $('#valor_retenido' + n).val(vt.toFixed(dec));
                        $('#lblvalor_retenido' + n).html(vt.toFixed(6));
                        if ($('#valor_retenido' + n).val().length == 0) {
                            can = 0;
                        } else {
                            can = $('#valor_retenido' + n).val()
                        }

                        sum = sum + parseFloat(can);
                    }
                }
                $('#total').html(sum.toFixed(dec));
            }


            function load_cliente(obj) {
                $.post("actions_reg_retencion.php", {op: 4, id: obj.value, s: 0},
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
                            doc_duplicado();
                        });
            }
            function num_comp(obj) {
                comp = obj.value;
                dt = comp.split('-');
                if (comp.length != 17 || dt[0].length != 3 || dt[1].length != 3 || dt[2].length != 9) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple la estructura ejem: 000-000-000000000');
                }
            }

            function ejercio(obj) {
                ejer = obj.value;
                dt = ejer.split('/');
                if (ejer.length != 7 || dt[0].length != 2 || dt[1].length != 4) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple la estructura ejem: 01/2015');
                }
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

            function load_factura(obj) {
                if (obj.length == 17) {
                    $.post("actions_reg_retencion.php", {op: 5, id: obj, x: 0},
                            function (dt) {
                                dat = dt.split('&');
                                if (dat[0] != '') {
                                    $('#fac_id').val(dat[0]);
                                    $('#fecha_emi_fac').val(dat[1]);
                                    $('#identificacion').val(dat[2]);
                                    $('#nombre').val(dat[3]);
                                    $('#cli_id').val(dat[4]);
                                    $('#num_comp_retenido').val(dat[5]);
                                    $('#iva').val(dat[6]);
                                    $('#base').val(dat[7]);
                                } else {
                                    alert('Factura no existe porfavor registrela para continuar');
                                    $('#fac_id').val('0');
                                    $('#fecha_emi_fac').val('');
                                    $('#identificacion').val('');
                                    $('#nombre').val('');
                                    $('#cli_id').val('');
                                    $('#num_secuencial').val('');
                                    $('#fecha_emi_fac').val('');
                                    $('#num_comp_retenido').val('');
                                    $('#iva').val('0');
                                    $('#base').val('0');
                                }
                                doc_duplicado();
                            });
                }
            }


            function doc_duplicado() {
                num_doc = $('#num_comprobante').val();
                ruc_pro = $('#identificacion').val();
                fac_id = $('#fac_id').val();
                if (num_doc.length = 17 && ruc_pro.length > 0) {
                    $.post("actions_reg_retencion.php", {op: 6, id: num_doc, data: ruc_pro, data2: fac_id},
                            function (dt) {
                                dat = dt.split('&');
                                if (dat[0] != '') {
                                    alert('EL numero de Documento y el Ruc del Proveedor \n Ya existen en el Registro de Retenciones');
                                    $('#num_secuencial').val('');
                                    $('#num_comprobante').val('');
                                    $('#autorizacion').val('');
                                    if (std != 2) {
                                        $('#fecha_emi_fac').val('');
                                        $('#identificacion').val('');
                                        $('#nombre').val('');
                                        $('#cli_id').val('');
                                        $('#num_comp_retenido').val('');
                                        $('#fac_id').val('0');
                                        $('#iva').val('0');
                                        $('#base').val('0');
                                    }
                                } else if (dat[2] != '') {
                                    if (std != 2) {
 if (op != 2) {
                                        alert('El numero de comprobante retenido ya existe');
                                        $('#num_secuencial').val('');
                                        $('#num_comprobante');
                                        $('#autorizacion').val('');
                                        $('#fecha_emi_fac').val('');
                                        $('#identificacion').val('');
                                        $('#nombre').val('');
                                        $('#cli_id').val('');
                                        $('#num_comp_retenido').val('');
                                        $('#fac_id').val('0');
                                        $('#iva').val('0');
                                        $('#base').val('0');
                                    }}
                                } else {
                                    if ($('#tipo_comprobante').val() == '1') {
                                        if (dat[3] == 0) {
                                            alert('No se puede emitir la retencion porque la \nfactura se encuentra cancelada en su totalidad');
                                            if (std != 2) {
                                                $('#num_secuencial').val('');
                                                $('#num_comprobante').val('');
                                                $('#autorizacion').val('');
                                                $('#fecha_emi_fac').val('');
                                                $('#identificacion').val('');
                                                $('#nombre').val('');
                                                $('#cli_id').val('');
                                                $('#num_comp_retenido').val('');
                                                $('#fac_id').val('0');
                                                $('#iva').val('0');
                                                $('#base').val('0');
                                                $('#saldo').val('0');
                                            }
                                        } else {
                                            $('#saldo').val(parseFloat(dat[3]).toFixed(2));
                                        }
                                    }
                                }
                            });
                }
            }

            function val_comp_ret() { //validacion para registro ret documentos bancarios
                op = tipo_comprobante.value;
                if (op == 2) {
                    $('#num_comp_retenido').attr('disabled', true);
                    $('#num_comp_retenido').val('');
                    $('#fac_id').val(0);
                    $('#identificacion').attr('readonly', false);
                    $('#identificacion').val('');
                    $('#cli_id').val(0);
                    $('#nombre').val('');
                    $('#base').val(0);
                    $('#iva').val(0);
		$std = 0
                } else {
                    $('#num_comp_retenido').attr('disabled', false);
                    $('#identificacion').attr('readonly', true);
                    $('#num_comp_retenido').val('');
                    $('#fac_id').val(0);
                    $('#identificacion').val('');
                    $('#cli_id').val(0);
                    $('#nombre').val('');
                    $('#base').val(0);
                    $('#iva').val(0);
                }
            }

            function limpiar_dat() { //limpia datos del cliente al momento de cerrar la tabla de informacion del cliente
                $('#cli_id').val(0);
                $('#identificacion').val('');
                $('#nombre').val('');
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
                width: 150px;
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
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden';
                    limpiar_dat()">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div> 
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form"  >
                <thead>
                    <tr><th colspan="9" >REGISTRO DE RETENCION<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>NO REGISTRO:</td>
                    <td><input type="text" size="17"  id="num_registro" value="<?php echo $rst[rgr_num_registro] ?>" readonly /></td>
                    <td>FECHA REGISTRO:</td>
                    <td><input type="text" size="17"  id="fecha_registro"  value="<?php echo $rst[rgr_fec_registro] ?>" readonly/><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                    <td>FECHA AUTORIZACION:</td>
                    <td><input type="text" size="17"  id="fecha_aut"  value="<?php echo $rst[rgr_fec_autorizacion] ?>" readonly/><img src="../img/calendar.png" id="im-campo2" readonly/></td>
                    <td>FECHA CADUCIDAD:</td>
                    <td><input type="text" size="17"  id="fecha_cad"  value="<?php echo $rst[rgr_fec_caducidad] ?>" readonly/><img src="../img/calendar.png" id="im-campo3" readonly/></td>

                </tr>
                <tr>
                    <td>FECHA EMISION:</td>
                    <td><input type="text" size="17"  id="fecha_emision"  value="<?php echo $rst[rgr_fecha_emision] ?>" readonly/><img src="../img/calendar.png" id="im-campo4" readonly/></td>
                    <td>RETENCION NO.:</td>
                    <td><input type="text" size="20"  id="num_comprobante" maxlength="17" value="<?php echo $rst[rgr_numero] ?>" onkeyup ="this.value = this.value.replace(/[^0-9-]/, '')" onblur="num_comp(this)" onchange="doc_duplicado()" onchange="doc_duplicado()" /></td>
                    <td>NUMERO DE AUTORIZACION:</td>
                    <td><input type="text" size="20"  id="autorizacion" value="<?php echo $rst[rgr_autorizacion] ?>"  maxlength="37" onkeyup ="this.value = this.value.replace(/[^0-9]/, '')"/></td>
                    <td>TIPO COMPROBANTE:</td>
                    <td><select id="tipo_comprobante" onchange="val_comp_ret(this)">
                            <option value="1">FACTURA</option>
                            <option value="2">OTROS DOCUMENTOS</option>
                            <!--                            <option value="5">NOTA DEBITO</option>
                                                        <option value="8">Notas de Venta RISE</option>
                                                        <option value="9">Liquidacion de Compra de bienes y prestacion de servicios</option>
                                                        <option value="10">Tiquetes emitidos por maquinas registradoras y boletos o entradas a espectaculos publicos</option>
                                                        <option value="11">Otros documentos autorizados</option>-->
                        </select>
                    </td>
                </tr>
                <tr> 
                    <td>COMPROB. RETENIDO #:</td>
                    <td><input type="text" size="20"  id="num_comp_retenido" <?php echo $dis_c?> value="<?php echo $rst[rgr_num_comp_retiene] ?>" maxlength="17" onkeyup ="this.value = this.value.replace(/[^0-9-]/, '')" onblur="num_comp(this)" onchange="load_factura(this.value)" <?php echo $read ?>/>
                        <input type="hidden" size="10"  id="fac_id" value="<?php echo $rst[fac_id] ?>"/>
                        <input type="hidden" size="10"  id="saldo" value="<?php echo $saldo ?>"/></td>
                    <td>RUC CLIENTE:</td>
                    <td><input type="text" size="17" readonly id="identificacion"  value="<?php echo $rst[rgr_identificacion] ?>" onchange="load_cliente(this)"  />
                        <input type="text" size="10"  id="cli_id" hidden value="<?php echo $rst[cli_id] ?>"/></td>
                    <td>NOMBRE CLIENTE:</td>
                    <td colspan="3"><input type="text" readonly size="62"  id="nombre" value="<?php echo $rst[rgr_nombre] ?>" />
                        <input type="hidden" id="base" size="15" value="<?php echo $base ?>"/>
                        <input type="hidden" id="iva" size="15" value="<?php echo $iva ?>"/></td>
                </tr>

                <tr id="head">
                <thead id="tabla">
                <th>ITEM</th>
                <th>EJERCICIO FISCAL</th>
                <th colspan="2">IMPUESTO</th>
                <th>BASE IMP. DE RETENCION</th>
                <th>CODIGO DEL IMPUESTO</th>
                <th>% DE RETENCION</th>
                <th>VALOR RETENIDO</th>
                <th>Acciones</th>
                </thead>
                <?php
                if ($det == '0') {
                    ?>
                    <tr>
                        <td align = "right"><input type = "text" size = "8" class = "itm" id = "item1" readonly value = "1" lang="1"/>
                        <td><input type = "text" size = "20" id = "ejercicio_fiscal1" value = "<?php echo $rst2[drr_ejercicio_fiscal] ?>" lang = "1" onblur="ejercio(this)" maxlength="7" onkeyup = "this.value = this.value.replace(/[^0-9/]/, '')"/></td>
                        <td colspan = "2"><input type = "text" size = "40" id = "descripcion_impuesto1" lang = "1" list = "lista_descripcion" onchange = "descripcion(this)" /></td>
                        <td><input type = "text" size = "17" id = "base_imponible1" value = "<?php echo $rst2[drr_base_imponible] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, '')" lang = "1" /></td>
                        <td><input type = "text" size = "20" id = "codigo1" value = "<?php echo $rst2[drr_codigo_impuesto] ?>" list = "lista_porcentaje" lang = "1"/>
                            <input type = "text" size = "20" id = "por_id1" value = "" hidden /></td>
                        <td><input type = "text" size = "17" id = "porcentaje_retencion1" value = "<?php echo $rst2[drr_procentaje_retencion] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, '')" lang = "1" /></td>
                        <td><input type = "text" size = "17" id = "valor_retenido1" value = "<?php echo $rst2[drr_valor] ?>" lang = "1" class = "cnt" readonly style="text-align:right;"/>
                            <label hidden id="lblvalor_retenido1" lang = "1" ></label>
                        </td>
                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                    </tr>
                    <?php
                } else if ($det == '1') {
                    $n = 0;
                    $cns2 = $Reg_retencion->lista_det_retencion($id);
                    while ($rst2 = pg_fetch_array($cns2)) {
                        $n++;
                        ?>
                        <tr>
                            <td align = "right"><input type = "text" size = "8" class = "itm" id = "item<?php echo $n ?>" readonly value = "<?php echo $n ?>" lang="<?php echo $n ?>"/>
                            <td><input type = "text" size = "20" id = "ejercicio_fiscal<?php echo $n ?>" value = "<?php echo $rst2[drr_ejercicio_fiscal] ?>" lang = "<?php echo $n ?>" onblur="ejercio(this)"/></td>
                            <td colspan = "2"><input type = "text" size = "50" id = "descripcion_impuesto<?php echo $n ?>" value = "<?php echo $rst2['por_descripcion'] ?>"  lang = "<?php echo $n ?>" list = "lista_descripcion" onchange = "descripcion(this)"/></td>
                            <td><input type = "text" size = "30" id = "base_imponible<?php echo $n ?>" value = "<?php echo $rst2[drr_base_imponible] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, '')" lang = "<?php echo $n ?>" /></td>
                            <td><input type = "text" size = "20" id = "codigo<?php echo $n ?>" value = "<?php echo $rst2[drr_codigo_impuesto] ?>" list = "lista_porcentaje" lang = "<?php echo $n ?>" readonly/>
                                <input type = "text" size = "20" id = "por_id<?php echo $n ?>" value = "<?php echo $rst2[por_id] . '_' . $rst_cod[drr_tipo_impuesto] ?>" hidden/></td>
                            <td><input type = "text" size = "20" id = "porcentaje_retencion<?php echo $n ?>" value = "<?php echo $rst2[drr_procentaje_retencion] ?>" lang = "<?php echo $n ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                            <td><input type = "text" size = "20" id = "valor_retenido<?php echo $n ?>" value = "<?php echo str_replace(',', '', number_format($rst2[drr_valor], $dec)) ?>" lang = "<?php echo $n ?>" class = "cnt" readonly style="text-align:right;"/>
                                <label hidden id="lblvalor_retenido<?php echo $n ?>" lang = "<?php echo $n ?>" ><?php echo str_replace(',', '', $rst2[drr_valor]) ?></label>
                            </td>
                            <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tfoot>
                    <tr class = "totales">
                        <td><button id = "add_row">+</button></td>
                        <td></td>
                        <td colspan = "5" align = "right">Total:</td>
                        <?PHP
                        if ($fila != "0") {
                            ?>
                            <td align="right" style="font-size:15px; " id="total"><?php echo str_replace(',', '', number_format($suma, $dec)) ?></td>
                            <?PHP
                        } else {
                            ?>
                            <td align="right" style="font-size:15px; " id="total">0</td>
                            <?PHP
                        }
                        ?>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <?PHP
        if ($x == 1) {
            ?> 
            <button id="guardar" onclick="save('<?php echo $id ?>')">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>    
    </body>
</html> 

<datalist id="lista_porcentaje">
    <?php
    while ($rst_por = pg_fetch_array($cns_por)) {
        echo "<option value='$rst_por[por_codigo]' >$rst_por[por_codigo]</option>";
    }
    ?>
</datalist>
<datalist id="lista_descripcion">
    <?php
    $cns_des = $Reg_retencion->lista_porcentaje();
    while ($rst_des = pg_fetch_array($cns_des)) {
        echo "<option value='$rst_des[por_id]' >$rst_des[por_codigo]  $rst_des[por_descripcion]</option>";
    }
    ?>
</datalist>

<script>
    var mot = '<?php echo $rst[rgr_denominacion_comp] ?>';
    $('#tipo_comprobante').val(parseInt(mot));
    
</script>
