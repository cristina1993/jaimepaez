<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();

$ord = $_GET[ord];
$cli = $_GET[cli];
$ruc = $_GET[ruc];
$f1 = $_GET[fecha1];
$f2 = $_GET[fecha2];
$e = $_GET[ped_estado];

if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst_enc = pg_fetch_array($Docs->lista_un_registro($id));
    $cns_det = $Docs->lista_detalle_registro_pedido($id);
    $cns_pag = $Docs->lista_pagos_registro_pedido($id);
    $num_doc = $rst_enc[ped_num_registro];
    $rst_emi = pg_fetch_array($Docs->lista_emisor($rst_enc[ped_local]));
    $dis = 'disabled';
    $id_cli = $rst_emi[emi_cod_cli];
} else {
    $id = 0;
    $id_cli = 0;
    $txt = '000000000';
    $rst = pg_fetch_array($Docs->lista_ultimo_registro_sec());
    $num_doc = $rst[ped_num_registro];
    $num_doc = intval($num_doc + 1);
    $num_doc = substr($txt, 0, (10 - strlen($num_doc))) . $num_doc;
    $rst[det_cantidad] = 0;
    $rst[ped_sbt12] = 0;
    $rst[ped_sbt0] = 0;
    $rst[ped_sbt_noiva] = 0;
    $rst[ped_sbt_excento] = 0;
    $rst[ped_sbt] = 0;
    $rst[ped_tdescuento] = 0;
    $rst[ped_ice] = 0;
    $rst[ped_irbpnr] = 0;
    $rst[ped_iva12] = 0;
    $rst[ped_propina] = 0;
    $rst[ped_total] = 0;
    $rst_enc[ped_desc_asolicitar] = 0;
    $rst_enc[ped_vendedor] = $rst_user[usu_person];
    $rst_enc[ped_femision] = date('Y-m-d');
    $rst_enc['ped_pais_cliente'] = 'ECUADOR';
    $dis = '';
//    $rst_pag[pag_fecha_v] = date('Y-m-d');
}
if ($x == 1) {
    $read = 'readonly';
    $disabled = 'disabled';
} else {
    $read = '';
    $disabled = '';
}
$cns = $Docs->lista_bodegas();
$cns_vnd = $Docs->lista_vendedores();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            mod = '<?php echo $mod_id ?>';
            id = '<?php echo $id ?>';
            inven = '<?php echo $inv5 ?>';
            ctr_inv = '<?php echo $ctr_inv ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            $(function () {
                $('#usu_id').val(<?php echo $rst[usu_id] ?>);

                $('#frm_detalle').submit(function (e) {
                    e.preventDefault();
                    clona_fila($("#tbl_detalle"));
                });
                $('#frm_fpagos').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_fpagos').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#pag_porcentage' + a).val() != 0 && $('#pag_dias' + a).val() != 0) {
                        clona_fila($("#tbl_fpagos"));
                    }
                });

                $('#cancel').click(function () {
                    cancelar(0);
                    return false;
                });
                $('#con_clientes').hide();
                parent.document.getElementById('contenedor2').rows = "*,90%";
                Calendar.setup({inputField: "ped_femision", ifFormat: "%Y-%m-%d", button: "im-reg_femision"});
                posicion_aux_window();
                load_id_cli();
            });

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a) {
                    case 1://PDF
                        frm.src = '../Scripts/frm_pdf_proforma.php?id=' + id;
                        break;
                }
            }

            function cancelar(a, mod) {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (a == 0) {
                    sec_ord = $('#ped_num_registro').val();
                    $.post("actions_ord_pedido_venta.php", {op: 11, sec: sec_ord}, function (dt) {
                        if (dt != 0) {
                            alert(dt);
                        }
                    });
                }
                if (mod == 42) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedido_venta.php?ord=' + '<?php echo $ord ?>' + '&cli=' + '<?php echo $cli ?>' + '&ruc=' + '<?php echo $ruc ?>' + '&fecha1=' + '<?php echo $f1 ?>' + '&fecha2=' + '<?php echo $f2 ?>' + '&ped_estado=' + '<?php echo $e ?>';
                } else if (mod == 64) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_aut_pedido_venta.php';
                } else if (mod == 31) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_seguimiento_bodega.php?fecha1=' + '<?php echo $f1 ?>' + '&fecha2=' + '<?php echo $f2 ?>';
                }
            }

            function cerrar_ventana() {
                $('#con_clientes').hide();
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input,select,label").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    this.checked = false;
                    var parent = $(this).parents();
                    $(parent[1]).css('background-color', 'transparent');
                    if (parts[1] == 'item') {
                        this.value = x;
                    } else {
                        this.value = '';
                    }
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#det_cod_producto' + x).focus();
            }

            function elimina_fila(obj, tbl) {
                if (tbl == 0) {
                    tb = "#tbl_fpagos";
                } else {
                    tb = "#tbl_detalle";
                }
                itm = $(tb + ' .itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo();
                calculo_total_pago();

            }
            function save(id) {
                ///**********encabezado*****************

                id_cli = $('#cli_id').val();
                var data = Array(
                        ped_femision.value,
                        ped_num_registro.value,
                        ped_local.value,
                        ped_vendedor.value,
                        ped_ruc_cc_cliente.value.toUpperCase(),
                        ped_nom_cliente.value.toUpperCase(),
                        ped_dir_cliente.value.toUpperCase(),
                        ped_tel_cliente.value.toUpperCase(),
                        ped_email_cliente.value.toLowerCase(),
                        ped_parroquia_cliente.value.toUpperCase(),
                        ped_ciu_cliente.value.toUpperCase(),
                        ped_pais_cliente.value.toUpperCase(),
                        $('#lblped_sbt12').html(),
                        $('#lblped_sbt0').html(),
                        $('#lblped_sbt_noiva').html(),
                        $('#lblped_sbt_excento').html(),
                        $('#lblped_sbt').html(),
                        $('#lblped_tdescuento').html(),
                        $('#lblped_ice').html(),
                        $('#lblped_irbpnr').html(),
                        $('#lblped_iva12').html(),
                        ped_propina.value,
                        $('#lblped_total').html(),
                        ped_observacion.value.toUpperCase(),
                        id_cli,
                        tipo_cliente.value);
///**********detalle*****************        
                var detalle = Array();
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                ndet = 0;
                while (ndet < i) {
                    ndet++;
                    if ($('#det_cod_producto' + ndet).val() != null) {
                        codigo = $('#det_cod_producto' + ndet).val();
                        descripcion = $('#det_descripcion' + ndet).val();
                        cantidad = $('#det_cantidad' + ndet).val();
                        vunit = $('#det_vunit' + ndet).val();
                        descuento_por = $('#det_descuento_porcentaje' + ndet).val();
                        descuento_mone = $('#lbldet_descuento_moneda' + ndet).html();
                        val_ice = $('#lbldet_val_ice' + ndet).html();
                        ice = $('#det_ice' + ndet).val();
                        cod_ice = $('#det_cod_ice' + ndet).val();
                        irbp = $('#det_val_irbp' + ndet).val();
                        val_irbp = $('#lbldet_irbp' + ndet).html();
                        total = $('#lbldet_total' + ndet).html();
                        impuesto = $('#det_impuesto' + ndet).val();
                        pro_id = $('#pro_id' + ndet).val();
                        unidad = $('#det_unidad' + ndet).val();
                        detalle.push(codigo + '&' +
                                codigo + '&' +
                                descripcion + '&' +
                                cantidad + '&' +
                                vunit + '&' +
                                descuento_por + '&' +
                                descuento_mone + '&' +
                                total + '&' +
                                impuesto + '&' +
                                pro_id + '&' +
                                '0&' +
                                unidad + '&' +
                                val_ice + '&' +
                                cod_ice + '&' +
                                irbp + '&' +
                                val_irbp + '&' +
                                ice
                                );
                    }
                }
//**********pagos*****************                                        
                pag = $('#tbl_fpagos .itm');
                var pagos = Array();
                npag = 1;
                while (npag <= pag.length) {
                    pag_valor = 0
                    pagos.push($('#pag_porcentage' + npag).val() + '&' +
                            $('#pag_dias' + npag).val() + '&' +
                            '0' + '&' +
                            '0'
                            );
                    npag++;
                }
                $.ajax({
                    beforeSend: function () {

                        var tr = $('#tbl_form').find("tbody tr:last");
                        a = tr.find("input").attr("lang");
                        i = parseInt(a);

                        var tr = $('#tbl_fpagos').find("tbody tr:last");
                        b = tr.find("input").attr("lang");
                        pag = parseInt(b);

                        n = 0;
                        j = 0;
                        return_v = 0;
                        if (cli_id.value == cliente.value) {
                            alert('Elija un cliente diferente al Local seleccionado');
                            $('#ped_ruc_cc_cliente').val('');
                            $('#ped_nom_cliente').val('');
                            $('#ped_dir_cliente').val('');
                            $('#ped_tel_cliente').val('');
                            $('#ped_email_cliente').val('');
                            $('#ped_parroquia_cliente').val('');
                            $('#ped_ciu_cliente').val('');
                            $('#ped_pais_cliente').val('');
                            return_v = 1;
                        } else if (ped_local.value == '0') {
                            $('#ped_local').focus();
                            $('#ped_local').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (ped_vendedor.value == '0') {
                            $('#ped_vendedor').focus();
                            $('#ped_vendedor').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (tipo_cliente.value == '0') {
                            if (ped_ruc_cc_cliente.value.length == 0) {
                                $('#ped_ruc_cc_cliente').focus();
                                $('#ped_ruc_cc_cliente').css('border', 'solid 2px red');
                                return_v = 1;
                            } else if (ped_nom_cliente.value.length == 0) {
                                $('#ped_nom_cliente').focus();
                                $('#ped_nom_cliente').css('border', 'solid 2px red');
                                return_v = 1;
                            } else if (ped_dir_cliente.value.length == 0) {
                                $('#ped_dir_cliente').focus();
                                $('#ped_dir_cliente').css('border', 'solid 2px red');
                                return_v = 1;
                            }
//                            else if (ped_tel_cliente.value.length == 0) {
//                                $('#ped_tel_cliente').focus();
//                                $('#ped_tel_cliente').css('border', 'solid 2px red');
//                                return_v = 1;
//                            } 
//                            else if (ped_email_cliente.value.length == 0) {
//                                $('#ped_email_cliente').focus();
//                                $('#ped_email_cliente').css('border', 'solid 2px red');
//                                return_v = 1;
//                            } else if (ped_parroquia_cliente.value.length == 0) {
//                                $('#ped_parroquia_cliente').focus();
//                                $('#ped_parroquia_cliente').css('border', 'solid 2px red');
//                                return_v = 1;
//                            } else if (ped_ciu_cliente.value.length == 0) {
//                                $('#ped_ciu_cliente').focus();
//                                $('#ped_ciu_cliente').css('border', 'solid 2px red');
//                                return_v = 1;
//                            } else if (ped_pais_cliente.value.length == 0) {
//                                $('#ped_pais_cliente').focus();
//                                $('#ped_pais_cliente').css('border', 'solid 2px red');
//                                return_v = 1;
//                            } 
                            else if (i != 0) {
                                while (n < i) {
                                    n++;
                                    if ($('#det_cod_producto' + n).val() != null) {
                                        if ($('#det_cod_producto' + n).val() == 0) {
                                            $('#det_cod_producto' + n).css({borderColor: "red"});
                                            $('#det_cod_producto' + n).focus();
                                            return false;
                                        } else if ($('#det_descripcion' + n).val() == '') {
                                            alert('El Producton no existe \n Seleccione uno que exista');
                                            $('#det_cod_producto' + n).focus();
                                            return false;
                                        } else if ($('#det_cantidad' + n).val() == 0) {
                                            $('#det_cantidad' + n).css({borderColor: "red"});
                                            $('#det_cantidad' + n).focus();
                                            return false;
                                        }
                                    }
                                }
                            }
                        } else if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#det_cod_producto' + n).val() != null) {
                                    if ($('#det_cod_producto' + n).val() == 0) {
                                        $('#det_cod_producto' + n).css({borderColor: "red"});
                                        $('#det_cod_producto' + n).focus();
                                        return false;
                                    } else if ($('#det_descripcion' + n).val() == '') {
                                        alert('El Producton no existe \n Seleccione uno que exista');
                                        $('#det_cod_producto' + n).focus();
                                        return false;
                                    } else if ($('#det_cantidad' + n).val() == 0) {
                                        $('#det_cantidad' + n).css({borderColor: "red"});
                                        $('#det_cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        m = 0;
                        while (m < pag) {
                            m++;
                            if ($('#pag_porcentage' + m).val() != null) {
                                if ($('#pag_porcentage' + m).val().length == 0) {
                                    $('#pag_porcentage' + m).css({borderColor: "red"});
                                    $('#pag_porcentage' + m).focus();
                                    return_v = 1;
                                } else if ($('#pag_dias' + m).val().length == 0) {
                                    $('#pag_dias' + m).css({borderColor: "red"});
                                    $('#pag_dias' + m).focus();
                                    return_v = 1;
                                }
                            }
                        }

                        if (return_v == 1) {
                            return false;
                        } else {
                            return true;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: "actions_ord_pedido_venta.php",
                    data: {op: 0, 'data[]': data, 'detalle[]': detalle, 'pagos[]': pagos, id: id},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            loading('hidden');
                            var r = confirm('¿Desea imprimir la proforma?');
                            if (r == true) {
                                auxWindow(1, dat[1]);
                            } else {
                                cancelar(0, mod);
                            }
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial del Pedido ya existe \n Debe hacer otro Pedido con otro Secuencial');
                            loading('hidden');
                        } else {
                            alert(dat[0]);
                        }


                    }
                });
            }
            function load_cliente(obj) {
                $.post("actions_ord_pedido_venta.php", {op: 8, id: obj.value, s: 0},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#ped_nom_cliente').focus();
                                load_id_cli();
                            }
                        });
            }

            function load_cliente2(obj) {
                $.post("actions_ord_pedido_venta.php", {op: 8, id: obj, s: 1},
                        function (dt) {
                            if (dt == 0) {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#ped_nom_cliente').focus();
                                load_id_cli();
                            } else {
                                dat = dt.split('&');
                                if (dat[10] == 0 || dat[10] == 2) {
                                    $('#ped_ruc_cc_cliente').val(dat[0]);
                                    $('#ped_nom_cliente').val(dat[1]);
                                    $('#ped_dir_cliente').val(dat[2]);
                                    $('#ped_tel_cliente').val(dat[3]);
                                    $('#ped_email_cliente').val(dat[4]);
                                    $('#ped_parroquia_cliente').val(dat[5]);
                                    $('#ped_ciu_cliente').val(dat[6]);
                                    $('#ped_pais_cliente').val(dat[7]);
                                    $('#cli_id').val(dat[8]);
                                    $('#tipo_cliente').val(dat[9]);
                                } else {
                                    alert('El Cliente esta Inactivo');
                                    $('#ped_ruc_cc_cliente').focus();
                                    $('#ped_ruc_cc_cliente').val('');
                                }
                            }
                            $('#con_clientes').hide();
                            load_id_cli();
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


            function calculo_total_pago() {
                var t = 0;
                var tp = 0;
                obj = $("#tbl_fpagos .itm ");
                total = $("#ped_total").val();
                n = 1;
                while (n <= obj.length) {
                    por = $("#pag_porcentage" + n).val();
                    n++;
                }
                $("#pg_total").val(t.toFixed(2));
                $("#pg_por").val(tp.toFixed(2));
            }

            function calculo(obj) {
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        c = 0;
                        v = 0;
                        dp = 0;
                        tp = 0;
                        d = 0;
                        t = 0;
                        ic = 0;
                        ib = 0;
                        $("#det_descuento_moneda" + n).val(d.toFixed(dec));
                        $("#lbldet_descuento_moneda" + n).html(d);
                        $("#det_val_ice" + n).val(ic.toFixed(dec));
                        $("#lbldet_val_ice" + n).html(ic);
                        $("#det_irbp" + n).val(ib.toFixed(dec));
                        $("#lbldet_irbp" + n).html(ib);
                        $("#det_total" + n).val(t.toFixed(dec));
                        $("#lbldet_total" + n).html(t);
                    } else {
                        c = ($("#det_cantidad" + n).val() * 1);
                        v = ($("#det_vunit" + n).val() * 1);
                        ice = ($("#det_ice" + n).val() * 1);
                        irb = ($("#det_val_irbp" + n).val() * 1);
                        dp = $("#det_descuento_porcentaje" + n).val();
                        if (dp > 100) {
                            de = 0;
                            alert('El descuento no puede ser mayor a 100%');
                            $('#det_descuento_porcentaje' + n).css({borderColor: "red"});
                            $('#det_descuento_porcentaje' + n).val(0);
                            $("#det_descuento_moneda" + n).val(de.toFixed(dec));
                            $('#det_descuento_porcentaje' + n).focus();
                            calculo();
                        } else {
                            tp = (c * v);
                            d = (tp * dp / 100);
                            t = (c * v) - d;
                            ic = (c * ice) / 100;
                            ib = c * irb;
                            $("#det_descuento_moneda" + n).val(d.toFixed(dec));
                            $("#lbldet_descuento_moneda" + n).html(d);
                            $("#det_val_ice" + n).val(ic.toFixed(dec));
                            $("#lbldet_val_ice" + n).html(ic);
                            $("#det_irbp" + n).val(ib.toFixed(dec));
                            $("#lbldet_irbp" + n).html(ib);
                            $("#det_total" + n).val(t.toFixed(dec));
                            $("#lbldet_total" + n).html(t);
                        }
                    }
                }
                calculo_totales();
            }
            function calculo_totales() {
                obj = $("#tbl_detalle .itm ");
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sbt12 = 0;
                sbt0 = 0;
                sbtno = 0;
                sbtex = 0;
                desc = 0;
                ice = 0;
                irbp = 0;
                while (n < i) {
                    n++;

                    desc += ($("#lbldet_descuento_moneda" + n).html() * 1)
                    ice += ($("#lbldet_val_ice" + n).html() * 1);
                    irbp += ($("#lbldet_irbp" + n).html() * 1);
                    switch ($("#det_impuesto" + n).val()) {
                        case '14':
                            sbt12 += ($("#det_total" + n).val() * 1);
                            break;
                        case '12':
                            sbt12 += ($("#det_total" + n).val() * 1);
                            break;
                        case '0':
                            sbt0 += ($("#det_total" + n).val() * 1);
                            break;
                        case 'NO':
                            sbtno += ($("#det_total" + n).val() * 1);
                            break;
                        case 'EX':
                            sbtex += ($("#det_total" + n).val() * 1);
                            break;
                    }
                }
                sbt = (sbt12 + sbt0 + sbtno + sbtex);
                iva = ((sbt12 + ice) * 0.14);
                gtot = (sbt + ice + iva + irbp + ($("#ped_propina").val() * 1));
                $("#ped_sbt12").val(sbt12.toFixed(dec));
                $("#lblped_sbt12").html(sbt12.toFixed(6));
                $("#ped_sbt0").val(sbt0.toFixed(dec));
                $("#lblped_sbt0").html(sbt0.toFixed(6));
                $("#ped_sbt_noiva").val(sbtno.toFixed(dec));
                $("#lblped_sbt_noiva").html(sbtno.toFixed(6));
                $("#ped_sbt_excento").val(sbtex.toFixed(dec));
                $("#lblped_sbt_excento").html(sbtex.toFixed(6));
                $("#ped_sbt").val(sbt.toFixed(dec));
                $("#lblped_sbt").html(sbt.toFixed(6));
                $("#ped_tdescuento").val(desc.toFixed(dec));
                $("#lblped_tdescuento").html(desc.toFixed(6));
                $("#ped_ice").val(ice.toFixed(dec));
                $("#lblped_ice").html(ice.toFixed(6));
                $("#ped_irbpnr").val(irbp.toFixed(dec));
                $("#lblped_irbpnr").html(irbp.toFixed(6));
                $("#ped_iva12").val(iva.toFixed(dec));
                $("#lblped_iva12").html(iva.toFixed(6));
                $("#ped_total").val((gtot * 1).toFixed(dec));
                $("#lblped_total").html((gtot * 1).toFixed(6));
                calculo_total_pago();
            }

            function asg_autocomplete(obj) {
                $(obj).autocomplete({source: productos});

            }

            function list_productos(obj) {
                id = $('#ped_local').val();
                if (id == 1 || id == 10) {
                    rdn = false;
                } else {
                    rdn = true;
                }

                $('.pg_input').each(function () {
                    var pts = this.id.match(/(\D+)(\d+)$/);
                    if (pts[1] == 'pag_porcentage') {
                        this.readOnly = rdn;
                    }
                    if (pts[1] == 'pag_dias') {
                        this.readOnly = rdn;
                    }
                })

                $('.dt_input').each(function () {
                    var pts = this.id.match(/(\D+)(\d+)$/);
                    if (pts[1] == 'det_vunit') {
                        this.readOnly = rdn;
                    }
                })
                $.post('actions_ord_pedido_venta.php', {op: 2, id: id, inv: inven, ctinv: ctr_inv, emi: ped_local.value}, function (dt) {
                    dat = dt.split('&&');
                    $('#productos').html(dat[0]);
                    $('#cliente').val(dat[1]);
                })
            }

            function caracter(e, obj, x) {
                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)

                    $('#det_descripcion' + j).focus();

                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });


                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    $('#det_descripcion' + j).focus();
                    v = 0;
                    load_producto(j, v);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#det_cantidad' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }
            }

            function load_producto(j, v) {
                vl = $('#det_cod_producto' + j).val();
                lt = 0;

                sig = 0;
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    pro2 = $('#det_cod_producto' + j).val();
                    $('#det_cod_producto' + j).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Producto ya ingresado');
                        vl = '';
                        $('#det_cod_producto' + j).val('');
                        $('#det_descripcion' + j).val('');
                        $('#det_unidad' + j).val('');
                        $('#det_impuesto' + j).val('');
                        $('#det_descuento_moneda' + j).val(0);
                        $('#det_cantidad' + j).val(0);
                        $('#pro_id' + j).val('');
                        $('#det_ice' + j).val(0);
                        $('#det_val_ice' + j).val(0);
                        $('#det_val_irbp' + j).val(0);
                        $('#det_irbp' + j).val(0);
                        $('#det_vunit' + j).val(0);
                        $('#det_descuento_porcentaje' + j).val(0);
                        $('#det_cod_ice' + j).val(0);
                        $('#inventario' + j).val(0);
                        $('#det_cod_producto' + j).focus();
                        sig = 1;
                    }
                });
                if (sig == 0) {

                    $.post("actions_ord_pedido_venta.php", {op: 12, id: vl, emi: ped_local.value, inv: inven, ctinv: ctr_inv},
                            function (dt) {
                                dat = dt.split('&');
                                $('#det_cod_producto' + j).val(dat[1]);
                                $('#det_descripcion' + j).val(dat[2]);
                                $('#det_impuesto' + j).val(dat[8]);
                                $('#det_unidad' + j).val(dat[9]);
                                $('#det_descuento_moneda' + j).val(0);
                                $('#det_cantidad' + j).val(0);
                                $('#pro_id' + j).val(dat[0]);
                                $('#det_ice' + j).val(dat[11]);
                                $('#det_val_ice' + j).val(0);
                                $('#det_val_irbp' + j).val(dat[12]);
                                $('#det_irbp' + j).val(0);


                                if (dat[3] == '') {
                                    $('#det_vunit' + j).val(0);
                                } else {
                                    $('#det_vunit' + j).val(parseFloat(dat[3]).toFixed(dec));
                                }

                                if (dat[5] == '') {
                                    $('#det_descuento_porcentaje' + j).val(0);
                                } else {
                                    $('#det_descuento_porcentaje' + j).val(dat[5]);
                                }

                                if (dat[6] == '') {
                                    $('#inventario' + j).val('0');
                                } else {
                                    $('#inventario' + j).val(parseFloat(dat[6]).toFixed(dec));
                                }

                                if (dat[13] == '') {
                                    $('#det_cod_ice' + j).val(0);
                                } else {
                                    $('#det_cod_ice' + j).val(dat[13]);
                                }
                                calculo();
                            });
                    calculo();
                }
            }

            function aprobar(act, id, ped) {
                main = parent.document.getElementById('mainFrame');
                if (act == 1) {
                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 1;
                } else if (act == 0) {
                    sms = confirm("No se aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 2;
                }
                if (sms == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 4, id: id, sts: sts}, function (dt) {
                        if (dt == 0) {
//                            main.src = '../Scripts/Lista_aut_pedido_venta.php';
                            cancelar(1, 64);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }



//            function inventario(obj) {
//                n = obj.lang;
//                bdgs = $('#ped_ruc_cc_cliente').val();
//                emi = $('#ped_local').val();
//                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
//                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
//                    $(obj).val('');
//                    $(obj).focus();
//                    $(obj).css({borderColor: "red"});
//                    calculo();
//                }
//            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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
                if ($("#ped_email_cliente").val() == '')
                {
                    alert("Ingrese un email");
                } else if (validar_email($("#ped_email_cliente").val()))
                {

                } else
                {
                    alert("El email no es valido");
                    $('#ped_email_cliente').css({borderColor: "red"});
                    $('#ped_email_cliente').val('');
                    $('#ped_email_cliente').focus();
                }
            }

            function confirmar_dat(op) {
                if (op == 1) {
                    sms = confirm("Esta seguro que los datos son correctos? \n Los datos ya no podran ser modificados");
                }
                if (sms == true) {
                    loc = $('#ped_local').val();
                    vend = $('#ped_vendedor').val();
                    ruc = $('#ped_ruc_cc_cliente').val();
                    nom = $('#ped_nom_cliente').val();
                    dir = $('#ped_dir_cliente').val();
                    tel = $('#ped_tel_cliente').val();
                    email = $('#ped_email_cliente').val();
                    parr = $('#ped_parroquia_cliente').val();
                    ciu = $('#ped_ciu_cliente').val();
                    pais = $('#ped_pais_cliente').val();
                    cli_id = $('#cli_id').val();
                    cli = $('#cliente').val();
                    tipo = $('#tipo_cliente').val();

                    if (tipo != 0) {
                        if (cli_id == cli) {
                            alert('Elija un cliente diferente al Local seleccionado');
                            $('#ped_ruc_cc_cliente').val('');
                            $('#ped_nom_cliente').val('');
                            $('#ped_dir_cliente').val('');
                            $('#ped_tel_cliente').val('');
                            $('#ped_email_cliente').val('');
                            $('#ped_parroquia_cliente').val('');
                            $('#ped_ciu_cliente').val('');
                            $('#ped_pais_cliente').val('');
                            $('#cli_id').val('0');
                            $('#tipo_cliente').val('0');
                            rdn = true;
                        } else if (loc != 0 && vend != 0 && ruc != '' && nom != '') {
                            $('#ped_femision').attr('disabled', true);
                            $('#im-reg_femision').hide();
                            $('#ped_local').attr('disabled', true);
                            $('#ped_ruc_cc_cliente').attr('disabled', true);
                            $('#ped_nom_cliente').attr('disabled', true);
                            $('#ped_dir_cliente').attr('disabled', true);
                            $('#ped_tel_cliente').attr('disabled', true);
                            $('#ped_email_cliente').attr('disabled', true);
                            $('#ped_parroquia_cliente').attr('disabled', true);
                            $('#ped_ciu_cliente').attr('disabled', true);
                            $('#ped_pais_cliente').attr('disabled', true);
                            $('#confirmar').attr('disabled', true);
                            rdn = false;
                        } else {
                            alert('Falta de ingresar un campos del encabezado \n del Pedido de Venta');
                            rdn = true;
                        }
                    } else {
                        if (cli_id == cli) {
                            alert('Elija un cliente diferente al Local seleccionado');
                            $('#ped_ruc_cc_cliente').val('');
                            $('#ped_nom_cliente').val('');
                            $('#ped_dir_cliente').val('');
                            $('#ped_tel_cliente').val('');
                            $('#ped_email_cliente').val('');
                            $('#ped_parroquia_cliente').val('');
                            $('#ped_ciu_cliente').val('');
                            $('#ped_pais_cliente').val('');
                            $('#cli_id').val('0');
                            $('#tipo_cliente').val('0');
                            rdn = true;
                        } else if (loc != 0 && vend != 0 && ruc != '' && nom != '' && dir != '') {
                            $('#ped_femision').attr('disabled', true);
                            $('#im-reg_femision').hide();
                            $('#ped_local').attr('disabled', true);
                            $('#ped_ruc_cc_cliente').attr('disabled', true);
                            $('#ped_nom_cliente').attr('disabled', true);
                            $('#ped_dir_cliente').attr('disabled', true);
                            $('#ped_tel_cliente').attr('disabled', true);
                            $('#ped_email_cliente').attr('disabled', true);
                            $('#ped_parroquia_cliente').attr('disabled', true);
                            $('#ped_ciu_cliente').attr('disabled', true);
                            $('#ped_pais_cliente').attr('disabled', true);
                            $('#confirmar').attr('disabled', true);
                            rdn = false;
                        } else {
                            alert('Falta de ingresar un campo del encabezado \n del Pedido de Venta');
                            rdn = true;
                        }
                    }

                    $('.dt_input').each(function () {
                        var pts = this.id.match(/(\D+)(\d+)$/);
                        if (pts[1] == 'det_cod_producto') {
                            this.readOnly = rdn;
                        }
                        if (pts[1] == 'det_lote') {
                            this.readOnly = rdn;
                        }
                        if (pts[1] == 'det_cod_auxiliar') {
                            this.readOnly = rdn;
                        }
                        if (pts[1] == 'det_cantidad') {
                            this.readOnly = rdn;
                        }
                        if (pts[1] == 'det_descuento_porcentaje') {
                            this.readOnly = rdn;
                        }
                    });
                } else {
                    return false;
                }
                if (id != 0) {
                    $('#save').attr('disabled', false);
                    list_productos();

                }
            }

            function limpliar_ruc() {
                $('#ped_ruc_cc_cliente').val('');
                $('#ped_ruc_cc_cliente').focus();
            }

            function load_id_cli() {
                dec = '<?php echo $dec ?>';
                id_cli = $('#cli_id').val();
                $.post("actions_ord_pedido_venta.php", {op: 13, id: id_cli, dec: dec},
                        function (dt) {
                            if (dt != '') {
                                dat = dt.split('&');
                                $('#ven_treinta').val(dat[0]);
                                $('#ven_sesenta').val(dat[1]);
                                $('#ven_noventa').val(dat[2]);
                                $('#ven_cien_veinte').val(dat[3]);
                                $('#ven_mas_civein').val(dat[4]);
                                $('#corriente').val(dat[5]);
                            } else {
                                $('#ven_treinta').val(0);
                                $('#ven_sesenta').val(0);
                                $('#ven_noventa').val(0);
                                $('#ven_cien_veinte').val(0);
                                $('#ven_mas_civein').val(0);
                                $('#corriente').val(0);
                            }
                        });
            }

            function std_cta_cli(dec) {
                id_cli = $('#cli_id').val();
                ruc_cli = $('#ped_ruc_cc_cliente').val();
                var boxH = $(window).height() * 1.0;
                var boxW = $(window).width() * 0.9;
                var boxHF = (boxH - 0.);
                if (ruc_cli == '') {
                    alert('Debe ingresar un cliente \n Para generar el Reporte');
                } else if (id_cli == '') {
                    alert('El cliente no se encuentra registrado \n No se puede generar el Reporte');
                } else {
                    wnd = '<iframe id="frmmodal" width="' + boxW + '" height="' + boxHF + '" src="frm_pdf_std_cta_cliente.php?id=' + id_cli + '&dec=' + dec + ' " frameborder="0" />';
                }
                wind = $.fallr.show({
                    content: '<center><H1>ESTADO DE CUENTA CLIENTE</H1></center>'
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 0,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });
            }
            
             //algoritmo digito verificado CC//
            function verificar_cedula(obj) {
                i = obj.value.trim().length;
                c = obj.value.trim();

                var s = 0;

                if (i == 10 || i == 13) {
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
                        if(d==0){
                            t=0;
                        }else{
                        t = 10 - d;
                    }
                        if (t.toString() == c.substr(9, 1)) {
                            load_cliente(obj);
                        } else {
                            alert('RUC/CC incorrecto');
                            $(obj).val('');
                        }
                    } else {
                        load_cliente(obj);
                    }
                } else {
                    load_cliente(obj);
                }
            }


        </script>
        <style>
            .btn-dinamic{
                cursor:pointer;
                padding:3px; 
                border-radius:2px;  
                background:#ccc;
            }
            .btn-dinamic:hover{
                background:linen;
                background:navajowhite 
            }
            /*            input[type=text]{
                            text-transform:uppercase !important; 
                        }*/
            #tbl_form{
                border:solid 1px; 
            }
            *{
                font-size:11px; 
                text-transform: uppercase;
            }
            #frm_detalle thead th,#frm_detalle tbody td{
                padding:1px !important; 
            }
            #reg_total{
                color: #900000;
                font-weight:bolder;
                font-size: 14px;
                border:solid 2px brown; 
            }

            .ui-autocomplete {
                max-height: 200px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }
            * html .ui-autocomplete {
                height: 200px;
            }

        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden';
                    limpliar_ruc()">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table id="tbl_form" cellpadding="0"  border="0"   >
            <thead>
                <tr>
                    <th colspan="5" >   
                        <?PHP echo "FORMULARIO ORDENES DE PEDIDO DE VENTA" ?>
                        <font class="cerrar"  onclick="cancelar(0, mod)" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">
                        <form id="frm_fencabezado" autocomplete="off">
                            <table id="tbl_fencabezado"  border="0">
                                <tbody>
                                    <tr>
                                        <td>Orden de Venta</td>
                                        <td><input type="text" size="12" id="ped_num_registro" value="<?php echo $num_doc ?>" readonly /></td>
                                        <td>Fecha</td>
                                        <td>
                                            <input type="text" size="10" id="ped_femision" value="<?php echo $rst_enc[ped_femision] ?>" <?php echo $read ?> />
                                            <img width="16px" src="../img/calendar.png" id="im-reg_femision"/>
                                        </td>
                                        <td>Local</td>
                                        <td>
                                            <select id="ped_local" onchange="list_productos(this)" <?php echo $disabled ?> <?php echo $dis ?>> 
                                                <option value="0">Seleccione</option>
                                                <?php
                                                while ($rst_locales = pg_fetch_array($cns)) {
                                                    echo "<option value='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" id="cliente" value="<?php echo $id_cli ?>">
                                        </td>
                                        <td>Vendedor</td>
                                        <!--<td><input type="text" size="30" id="ped_vendedor" value="<?php echo $rst_enc[ped_vendedor] ?>" /></td>-->
                                        <td><select id="ped_vendedor" <?php echo $disabled ?>>
                                                <option value="0">SELECCIONE</option>
                                                <?php
                                                while ($rst_vnd = pg_fetch_array($cns_vnd)) {
                                                    echo "<option value='$rst_vnd[vnd_nombre]'>$rst_vnd[vnd_nombre]</option>";
                                                }
                                                ?>
                                            </select></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form> 
                    </td>
                </tr>
                <tr>
                    <td style="overflow:scroll;" rowspan="0" valign="top" align="left"  >
                        <form id="frm_fcliente" autocomplete="off">
                            <table id="tbl_fcliente"  border="0">
                                <thead>
                                    <tr><th colspan="2">Cliente</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>RUC/CC:</td>
                                        <!--cambia el evento onchange de load_cliente a verificar_cedula-->
                                        <td><input type="text" id="ped_ruc_cc_cliente" size="50" onchange="verificar_cedula(this)" value="<?php echo $rst_enc[ped_ruc_cc_cliente] ?>" maxlength="13" placeholder="maximo 13 caracteres" <?php echo $read ?> /></td>
                                    </tr>
                                    <tr>
                                        <td>NOMBRE:</td>
                                        <td><input type="text" id="ped_nom_cliente" size="50" value="<?php echo $rst_enc[ped_nom_cliente] ?>" placeholder="NOMBRES Y APELLIDOS" <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>DIRECCION:</td>
                                        <td><input type="text" id="ped_dir_cliente" size="50" value="<?php echo $rst_enc[ped_dir_cliente] ?>" <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>TELEFONO:</td>
                                        <td><input type="text" id="ped_tel_cliente" size="50" value="<?php echo $rst_enc[ped_tel_cliente] ?>" <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>EMAIL:</td>
                                        <td><input type="text" id="ped_email_cliente" size="50" value="<?php echo $rst_enc[ped_email_cliente] ?>" onchange="mail_validado()" style="text-transform:lowercase " <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>PARROQUIA:</td>
                                        <td><input type="text" id="ped_parroquia_cliente" size="50" value="<?php echo $rst_enc[ped_parroquia_cliente] ?>" <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>CIUDAD:</td>
                                        <td><input type="text" id="ped_ciu_cliente" size="50" value="<?php echo $rst_enc[ped_ciu_cliente] ?>" <?php echo $read ?>/></td>
                                    </tr>
                                    <tr>
                                        <td>PAIS:</td>
                                        <td><input type="text" id="ped_pais_cliente" size="50" value="<?php echo $rst_enc[ped_pais_cliente] ?>" <?php echo $read ?>/>
                                            <input type="button" id="confirmar" onclick="confirmar_dat(1)" value="CONFIRMAR" <?php echo $disabled ?>/></td>
                                    </tr>
                                    <tr>
                                        <td><input type="hidden" id="cli_id" size="5" value="<?php echo $rst_enc[cli_id] ?>"  />
                                            <input type="hidden" id="tipo_cliente" size="5" value="<?php echo $rst_enc[tipo_cliente] ?>" />
                                    </tr>
                                </tbody>
                            </table>
                        </form> 
                    </td>
                    <td style="overflow:scroll;" rowspan="0" valign="top" align="left"  >
                        <form id="frm_fpagos" autocomplete="off">
                            <table id="tbl_fpagos"  border="0">
                                <thead>
                                    <tr><th colspan="6">Formas de Pago</th></tr>
                                    <tr>
                                        <th>No</th>
                                        <th>%</th>
                                        <th>Días</th>
                                        <th hidden>Valor</th>
                                        <th hidden>Fecha</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (pg_num_rows($cns_pag) == 0) {
                                        ?>
                                        <tr>
                                            <td align="right">
                                                <input type="text" size="2" class="itm" id="item1" name="item1"  value="1" lang="1" readonly style="text-align:right"/>
                                            </td>
                                            <td><input class="pg_input" type="text" size="5" id="pag_porcentage1" name="pag_porcentage1" lang="1" value="0" maxlength="4" onkeyup="calculo_total_pago()"/></td>
                                            <td><input class="pg_input" type="text" size="7" id="pag_dias1" name="pag_dias1" lang="1" value="0" onchange="calculo_fecha(this)" /></td>
                                            <td hidden><input class="pg_input" type="text" size="7" id="pag_valor1" name="pag_valor1" lang="1" value="0" onkeyup="calculo_total_pago()" style="text-align:right" readonly /></td>
                                            <td hidden>
                                                <input class="pg_input" type="date" size="10" id="pag_fecha_v1" name="pag_fecha_v1" lang="1"  value="<?php echo $rst_pag[pag_fecha_v] ?>" readonly />
                                            </td>
                                            <td onclick="elimina_fila(this, 0)" align="center" ><img class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
                                        </tr>
                                        <?php
                                    } else {
                                        $npg = 0;
                                        while ($rst_pag = pg_fetch_array($cns_pag)) {
                                            $pag_total+=$rst_pag[pag_valor];
                                            $pag_por+=$rst_pag[pag_porcentage];
                                            $npg++;
                                            ?>
                                            <tr>
                                                <td align="right"><input type="text" size="2" class="itm" id="<?php echo 'item' . $npg ?>" name="<?php echo 'item' . $npg ?>"   value="<?php echo $npg ?>" lang="<?php echo $npg ?>" readonly style="text-align:right"/></td>
                                                <?php
                                                if ($rst_pag[ped_local] == 1 || $rst_pag[ped_local] == 10) {
                                                    ?>
                                                    <td><input class="pg_input" type="text" size="5" id="<?php echo 'pag_porcentage' . $npg ?>" name="<?php echo 'pag_porcentage' . $npg ?>" value="<?php echo $rst_pag[pag_porcentage] ?>" lang="<?php echo $npg ?>" maxlength="4" onkeyup="calculo_total_pago()" <?php echo $read ?>/></td>
                                                    <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_dias' . $npg ?>" name="<?php echo 'pag_dias' . $npg ?>" value="<?php echo $rst_pag[pag_dias] ?>" lang="<?php echo $npg ?>" onchange="calculo_fecha(this)" <?php echo $read ?>/></td>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <td><input class="pg_input" type="text" size="5" id="<?php echo 'pag_porcentage' . $npg ?>" name="<?php echo 'pag_porcentage' . $npg ?>" value="<?php echo $rst_pag[pag_porcentage] ?>" lang="<?php echo $npg ?>" maxlength="4" onkeyup="calculo_total_pago()" readonly/></td>
                                                    <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_dias' . $npg ?>" name="<?php echo 'pag_dias' . $npg ?>" value="<?php echo $rst_pag[pag_dias] ?>" lang="<?php echo $npg ?>" onchange="calculo_fecha(this)" readonly/></td>
                                                    <?php
                                                }
                                                ?>
                                                <td hidden><input class="pg_input" type="text" size="7" id="<?php echo 'pag_valor' . $npg ?>" name="<?php echo 'pag_valor' . $npg ?>" value="<?php echo $rst_pag[pag_valor] ?>" lang="<?php echo $npg ?>" onkeyup="calculo_total_pago()" style="text-align:right" readonly/></td>
                                                <td hidden>
                                                    <input class="pg_input" type="date" size="10" id="<?php echo 'pag_fecha_v' . $npg ?>" name="<?php echo 'pag_fecha_v' . $npg ?>" value="<?php echo $rst_pag[pag_fecha_v] ?>" lang="<?php echo $npg ?>" readonly />
                                                </td>
                                                <td onclick="elimina_fila(this, 0)" align="center" ><img class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><input type="submit" id="" value="+" /></td>
                                        <td colspan="7"></td>
                                    </tr>
                                    <tr hidden>
                                        <td>Total:</td>
                                        <td><input type="text" size="7" value="<?php echo str_replace(',', '', number_format($pag_por, $dec)) ?>" id="pg_por" style="text-align:right" readonly/></td>
                                        <td></td>
                                        <td><input type="text" size="7" value="<?php echo str_replace(',', '', number_format($pag_total, $dec)) ?>" id="pg_total" style="text-align:right" readonly/></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form> 
                    </td>
                </tr>
                <tr><td><br></td></tr>
                <tr>
                    <td colspan="2">
                        <form id="frm_detalle" autocomplete="off">
                            <table border="0" align="left" id="tbl_detalle" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>Inventario</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th>V.Unitario</th>
                                        <th>Descuento%</th>
                                        <th>Descuento $</th>
                                        <th>IVA</th>
                                        <th hidden>ICE%</th>
                                        <th hidden>ICE $</th>
                                        <th hidden>IRBPRN $</th>
                                        <th>Total</th>

                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                        <tr>
                                            <td align="right">
                                                <input type="text" size="2" class="itm" id="item1" name="item1"   value="1" lang="1" readonly style="text-align:right"/>
                                            </td>
                                            <td>
                                                <input class="dt_input" type="text" size="25" id="det_cod_producto1" name="det_cod_producto"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2" readonly />
                                            </td>
                                            <td>
                                                <input class="dt_input" type="text" size="50" id="det_descripcion1" name="det_descripcion1" value="" lang="1"  style="font-size:9px;height:20px; " readonly />
                                                <input class="dt_input" type="hidden" size="15" id="pro_id1"  value="" lang="1"/>
                                            </td>
                                            <td><input class="dt_input" type ="text" size="7"  id="inventario1"  value="" lang="1" readonly /></td>
                                            <td><input class="dt_input" type="text" size="10" id="det_cantidad1" name="det_cantidad1" value="" lang="1"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" readonly /></td>
                                            <td>
                                                <input class="dt_input" type="text" size="7" id="det_unidad1" name="det_unidad1" lang="1" readonly />
                                            </td>
                                            <td><input class="dt_input" type="text" size="10" id="det_vunit1" name="det_vunit1" value="" lang="1"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" readonly /></td>
                                            <td><input class="dt_input" type="text" size="10" id="det_descuento_porcentaje1" name="det_descuento_porcentaje1" value="" lang="1"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" readonly /></td>
                                            <td><input class="dt_input" type="text" size="10" id="det_descuento_moneda1" name="det_descuento_moneda1" value="" lang="1" readonly style="text-align:right"/>
                                                <label hidden id="lbldet_descuento_moneda1" lang="1"></label></td>                                        
                                            <td>
                                                <select id="det_impuesto1" name="det_impuesto1" lang="1" onchange="calculo(this)" >
                                                    <option value="14">IVA 14</option>
                                                    <option value="12">IVA 12</option>
                                                    <option value="0">IVA 0</option>
                                                    <option value="NO">NO OBJETO</option>
                                                    <option value="EX">EXCENTO</option>
                                                </select>
                                            </td>
                                            <td hidden><input class="dt_input" type="text" size="7" id="det_ice1" name="det_ice1" value="" lang="1" readonly style="text-align:right" /></td>
                                            <td hidden><input class="dt_input" type="text" size="10" id="det_val_ice1" name="det_val_ice1" value="" lang="1" readonly style="text-align:right" />
                                                <label hidden id="lbldet_val_ice1" lang="1"></label>
                                                <input class="dt_input" type="hidden" size="10" id="det_cod_ice1" name="det_cod_ice1" value="" lang="1" readonly style="text-align:right" /></td>
                                            <td hidden><input class="dt_input" type="text" size="10" id="det_irbp1" name="det_irbp1" value="" lang="1" readonly style="text-align:right" />
                                                <input type="hidden" id="det_val_irbp1" value="" />
                                                <label hidden id="lbldet_irbp1" lang="1"></label></td>
                                            <td><input class="dt_input" type="text" size="10" id="det_total1" name="det_total1" value="" lang="1" readonly style="text-align:right" />
                                                <label hidden id="lbldet_total1" lang="1"></label></td>

                                            <td onclick="elimina_fila(this, 1)" align="center" ><img class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
                                        </tr>
                                        <?php
                                    } else {
                                        $ndt = 0;
                                        while ($rst_det = pg_fetch_array($cns_det)) {
                                            $ndt++;
                                            $rst_bod = pg_fetch_array($Docs->lista_una_bodega($rst_det[ped_id]));
                                            $rst_inv = pg_fetch_array($Docs->total_ingreso_egreso_fac($rst_det[pro_id], $rst_bod[ped_local], $rst_det[det_tab]));
                                            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                                            ?>
                                            <tr>
                                                <td align="right">
                                                    <input type="text" size="2" class="itm" id="<?php echo 'item' . $ndt ?>" name="<?php echo 'item' . $ndt ?>"   value="<?php echo $ndt ?>" lang="<?php echo $ndt ?>" readonly style="text-align:right"/>
                                                </td>
                                                <td><input class="dt_input" type="text" size="25" id="<?php echo 'det_cod_producto' . $ndt ?>" name="<?php echo 'det_cod_producto' . $ndt ?>" value="<?php echo $rst_det[det_cod_producto] ?>" lang="<?php echo $ndt ?>" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2" <?php echo $read ?>/></td>
                                                <td>
                                                    <input class="dt_input" type="text" size="50" id="<?php echo 'det_descripcion' . $ndt ?>" name="<?php echo 'det_descripcion' . $ndt ?>" value="<?php echo $rst_det[det_descripcion] ?>" lang="<?php echo $ndt ?>"    style="font-size:9px" readonly />
                                                    <input class="dt_input" type="text" size="15" id="<?php echo 'pro_id' . $ndt ?>"  value="<?php echo $rst_det[pro_id] ?>" lang="1" hidden/>
                                                </td>                                                
                                                <td><input class="dt_input" type="text" size="7"  id="<?php echo 'inventario' . $ndt ?>"  value="<?php echo $inv ?>" lang="1" readonly/></td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_cantidad' . $ndt ?>" name="<?php echo 'det_cantidad' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_cantidad], $dc)) ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read ?>/></td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_unidad' . $ndt ?>" name="<?php echo 'det_unidad' . $ndt ?>" value="<?php echo $rst_det[det_unidad] ?>" lang="<?php echo $ndt ?>"   readonly/></td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_vunit' . $ndt ?>" name="<?php echo 'det_vunit' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_vunit], $dec)) ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read ?>/></td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_descuento_porcentaje' . $ndt ?>" name="<?php echo 'det_descuento_porcentaje' . $ndt ?>" value="<?php echo $rst_det[det_descuento_porcentaje] ?>"  lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read ?>/></td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_descuento_moneda' . $ndt ?>" name="<?php echo 'det_descuento_moneda' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_descuento_moneda], $dec)) ?>"  lang="<?php echo $ndt ?>"  readonly style="text-align:right"/>
                                                    <label hidden id="<?php echo 'lbldet_descuento_moneda' . $ndt ?>" lang="<?PHP echo $ndt ?>"><?php echo $rst_det[det_descuento_moneda] ?></label></td>                                        
                                                <td>
                                                    <select id="<?php echo 'det_impuesto' . $ndt ?>" name="<?php echo 'det_impuesto' . $ndt ?>"   lang="<?php echo $ndt ?>" onchange="calculo(this)" <?php echo $disabled ?>>
                                                        <option value="14">14%</option>
                                                        <option value="12">12%</option>
                                                        <option value="0">0%</option>
                                                        <option value="NO">NO</option>
                                                        <option value="EX">EX</option>
                                                    </select>
                                                    <script>
                                                        idt1 = '<?php echo 'det_impuesto' . $ndt ?>';
                                                        $('#' + idt1).val('<?php echo $rst_det[det_impuesto] ?>');
                                                    </script>
                                                </td>
                                                <td hidden><input class="dt_input" type="text" size="10" id="<?php echo 'det_ice' . $ndt ?>" name="<?php echo 'det_ice' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_p_ice], $dec)) ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" /></td>
                                                <td hidden><input class="dt_input" type="text" size="10" id="<?php echo 'det_val_ice' . $ndt ?>" name="<?php echo 'det_val_ice' . $ndt ?>" value="<?php echo $rst_det[det_val_ice] ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" />
                                                    <label hidden id="<?php echo 'lbldet_val_ice' . $ndt ?>" lang="<?PHP echo $ndt ?>"><?php echo str_replace(',', '', number_format($rst_det[det_val_ice], $dec)) ?></label></td>                                        
                                        <input class="dt_input" type="hidden" size="10" id="<?php echo 'det_cod_ice' . $ndt ?>" name="<?php echo 'det_cod_ice' . $ndt ?>" value="<?php echo $rst_det[det_cod_ice] ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" /></td>
                                        <td hidden><input class="dt_input" type="text" size="10" id="<?php echo 'det_irbp' . $ndt ?>" name="<?php echo 'det_irbp' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_irbp], $dec)) ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" />
                                            <input type="hidden" id="<?php echo 'det_val_irbp' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_val_irbp], $dec)) ?>" />
                                            <label hidden id="<?php echo 'lbldet_irbp' . $ndt ?>" lang="<?PHP echo $ndt ?>"><?php echo $rst_det[det_irbp] ?></label></td>                                        
                                        <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_total' . $ndt ?>" name="<?php echo 'det_total' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_total], $dec)) ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right" />
                                            <label hidden id="<?php echo 'lbldet_total' . $ndt ?>" lang="<?PHP echo $ndt ?>"><?php echo $rst_det[det_total] ?></label></td>                                     
                                        <td onclick="elimina_fila(this, 1)" align="center" ><img class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td align="center">
                                            <input type="submit" id="" value="+" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="6"></td>
                                        <td colspan="2">Subtotal 14%:</td>
                                        <td><input type="text" size="10" id="ped_sbt12" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_sbt12], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_sbt12">  <?php echo str_replace(',', '', $rst_enc[ped_sbt12]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Observaciones:</td>
                                        <td colspan="6"></td>
                                        <td colspan="2">Subtotal 0%:</td>
                                        <td><input type="text" size="10" id="ped_sbt0" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_sbt0], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_sbt0">  <?php echo str_replace(',', '', $rst_enc[ped_sbt0]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td valign="top" rowspan="3" colspan="8"><textarea id="ped_observacion" style="width:100%; text-transform: uppercase;" onkeydown="return enter(event)" <?php echo $disabled ?>><?php echo $rst_enc[ped_observacion] ?></textarea></td>
                                        <td colspan="2">Subtotal No objeto de Iva:</td>
                                        <td><input type="text" size="10" id="ped_sbt_noiva" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_sbt_noiva], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_sbt_noiva">  <?php echo str_replace(',', '', $rst_enc[ped_sbt_noiva]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Subtotal Excento de Iva:</td>
                                        <td><input type="text" size="10" id="ped_sbt_excento" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_sbt_excento], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_sbt_excento">  <?php echo str_replace(',', '', $rst_enc[ped_sbt_excento]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Subtotal:</td>
                                        <td><input type="text" size="10" id="ped_sbt" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_sbt], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_sbt">  <?php echo str_replace(',', '', $rst_enc[ped_sbt]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Corriente</td>
                                        <td><input type="text" size="11" id="corriente" style="text-align: right" readonly /></td>
                                        <td colspan="5"></td>
                                        <td colspan="2">Total Descuento:</td>
                                        <td><input type="text" size="10" id="ped_tdescuento" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_tdescuento], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_tdescuento">  <?php echo str_replace(',', '', $rst_enc[ped_tdescuento]) ?></label></td>
                                    </tr>
                                    <tr hidden>
                                        <td colspan="2">Valor ICE:</td>
                                        <td><input type="text" size="10" id="ped_ice" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_ice], $dec)) ?>"  style="text-align:right" onchange="calculo_totales()" readonly/>
                                            <label hidden id="lblped_ice">  <?php echo str_replace(',', '', $rst_enc[ped_ice]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Vencido 1 a 30 Dias</td>
                                        <td><input type="text" size="11" id="ven_treinta" style="text-align: right" readonly /></td>
                                        <td colspan="5"></td>
                                        <td colspan="2">IVA 14%:</td>
                                        <td><input type="text" size="10" id="ped_iva12" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_iva12], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_iva12">  <?php echo str_replace(',', '', $rst_enc[ped_iva12]) ?></label></td>
                                    </tr>
                                    <tr hidden>
                                        <td colspan="2">Valor IRBPRN:</td>
                                        <td><input type="text" size="10" id="ped_irbpnr" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_irbpnr], $dec)) ?>"  style="text-align:right" onchange="calculo_totales()" readonly/>
                                            <label hidden id="lblped_irbpnr">  <?php echo str_replace(',', '', $rst_enc[ped_irbpnr]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Vencido 31 a 60 Dias</td>
                                        <td><input type="text" size="11" id="ven_sesenta" style="text-align: right" readonly /></td>
                                        <td colspan="5"></td>
                                        <td colspan="2">Propina:</td>
                                        <td><input type="text" size="10" id="ped_propina" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_propina], $dec)) ?>"  style="text-align:right" onchange="calculo_totales()" <?php echo $read ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Vencido 61 a 90 Dias</td>
                                        <td><input type="text" size="11" id="ven_noventa" style="text-align: right" readonly /></td>
                                        <td colspan="5"></td>
                                        <td colspan="2">Valor Total:</td>
                                        <td><input type="text" size="10" id="ped_total" value="<?php echo str_replace(',', '', number_format($rst_enc[ped_total], $dec)) ?>" readonly style="text-align:right"/>
                                            <label hidden id="lblped_total">  <?php echo str_replace(',', '', $rst_enc[ped_total]) ?></label></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Vencido 91 a 120 Dias</td>
                                        <td><input type="text" size="11" id="ven_cien_veinte" style="text-align: right" readonly /></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Mas de 120 Dias</td>
                                        <td>
                                            <input type="text" size="11" id="ven_mas_civein" style="text-align: right" readonly />
                                            <input type="button" value="REPORTE" onclick="std_cta_cli(<?php echo $dec ?>)">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td colspan="6"><br><br></td></tr>
                <tr>
                    <td colspan="6">
                        <?php
                        if ($x != 3) {
                            if ($x != 1) {
                                ?>
                                <button id="save" lang="<?php echo $id ?>" <?php echo $dis ?> onclick="save(<?php echo $id ?>)" >Guardar</button>
                                <button id="cancel" >Cancelar</button>
                                <?php
                            } else {
                                ?>
                                <button id="aprobar" lang="<?php echo $id ?>" onclick="aprobar(1,<?php echo $rst_enc[ped_id] ?>, '<?php echo $num_doc ?>')" >Aprobar</button>
                                <button id="rechazar" lang="<?php echo $id ?>" onclick="aprobar(0,<?php echo $rst_enc[ped_id] ?>, '<?php echo $num_doc ?>')">Rechazar</button>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>
<script>
    $('#ped_local').val('<?php echo $rst_enc[ped_local] ?>');
    $('#ped_vendedor').val('<?php echo $rst_enc[ped_vendedor] ?>');
</script>
<datalist id="productos">
</datalist>
