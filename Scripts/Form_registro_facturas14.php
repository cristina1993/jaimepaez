<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_registro_facturas.php';
$Docs = new Clase_registro_facturas();
$cns_prov = $Docs->lista_proveedores();
$cns_comb = $Docs->lista_un_mp_mod1();
$cns_tip = $Docs->lista_un_tipo('2');
$cns_prot = $Docs->lista_un_tipo('1');
if (isset($_GET[id])) {
    $id = $_GET[id];
    $std = $_GET[std];
    $x = $_GET[vst];
    $rst = pg_fetch_array($Docs->lista_un_registro($id));
    $cns_det = $Docs->lista_detalle_registro($id);
    $cns_pag = $Docs->lista_pagos_registro($id);
    $num_doc = $rst[reg_num_registro];
    $rst_cli = pg_fetch_array($Docs->lista_cliente_ruc($rst[reg_ruc_cliente]));
    $dn = explode('-', $rst[reg_num_documento]);
    if ($rst[reg_fvencimiento] == '1900-01-01') {
        $rst[reg_fvencimiento] = '';
    }
    if ($rst[reg_fautorizacion] == '1900-01-01') {
        $rst[reg_fautorizacion] = '';
    }
    if ($rst[reg_fcaducidad] == '1900-01-01') {
        $rst[reg_fcaducidad] = '';
    }
    $j = 1;

    $rst_ret = pg_fetch_array($Docs->lista_retencion($id));
    if (!empty($rst_ret)) {
        $std = 5;
    }
//    else {
//        if (empty($rst[reg_num_ingreso]) && $rst[reg_estado] != 3) {
//            $std = $rst[reg_estado] = 4;
//        } else {
//            $std = 1;
//        }
//    }
//    echo $std;
    switch ($std) {
        case 1:
            $visib = "hidden";
            $visib2 = "hidden";
            $visib3 = "";
            $read = "readonly";
            $read1 = "";
            $read2 = "readonly";
            $read3 = "";
            $disabled = "disabled";
            $disabled2 = "";
            $disabled3 = "disabled";
            $disabled4 = "";
            $sms = "Factura de Ingreso General no se pueden modificar los valores monetarios";
            break;
        case 2:
            $visib = "hidden";
            $visib2 = "";
            $visib3 = "";
            $read = "readonly";
            $read1 = "";
            $read2 = "";
            $read3 = "";
            $disabled = "disabled";
            $disabled2 = "";
            $disabled3 = "";
            $disabled4 = "";
            $sms = "";
            break;
        case 3:
            $visib = "hidden";
            $visib2 = "hidden";
            $visib3 = "";
            $read = "readonly";
            $read1 = "readonly";
            $read2 = "";
            $read3 = "";
            $disabled = "disabled";
            $disabled2 = "disabled";
            $disabled3 = "disabled";
            $disabled4 = "";
            $sms = "";
            break;
        case 4:
            $visib = "";
            $visib2 = "";
            $visib3 = "";
            $read = "";
            $read1 = "";
            $read2 = "";
            $read3 = "";
            $disabled = "";
            $disabled2 = "";
            $disabled3 = "";
            $disabled4 = "";
            $sms = "";
            break;
        case 5:
            $visib = "hidden";
            $visib2 = "hidden";
            $visib3 = "hidden";
            $read = "readonly";
            $read1 = "readonly";
            $read2 = "readonly";
            $read3 = "readonly";
            $disabled = "disabled";
            $disabled2 = "disabled";
            $disabled3 = "disabled";
            $disabled4 = "";
            $sms = "Apertura de Retencion no se pueden modificar los valores monetarios";
            break;
        case 6:
            $visib = "hidden";
            $visib2 = "hidden";
            $visib3 = "hidden";
            $read = "readonly";
            $read1 = "readonly";
            $read2 = "readonly";
            $read3 = "readonly";
            $disabled = "disabled";
            $disabled2 = "disabled";
            $disabled3 = "disabled";
            $disabled4 = "";
            $sms = "Apertura de Nota de Credito no se pueden modificar los valores monetarios";
            break;
    }
} else {
    $id = '0';
    $x = 1;
    $txt = '000000000';
    $rst = pg_fetch_array($Docs->lista_ultimo_registro());
    $num_doc = $rst[reg_num_registro];
    $num_doc = intval($num_doc + 1);
    $num_doc = substr($txt, 0, (10 - strlen($num_doc))) . $num_doc;
    $rst[reg_fautorizacion] = date('Y-m-d');
    $rst[reg_fcaducidad] = date('Y-m-d');
    $rst[reg_tipo_documento] = 0;
    $rst[reg_num_documento] = '';
    $rst[reg_sustento] = '';
    $rst[reg_num_autorizacion] = '';
    $rst[reg_tpcliente] = '';
    $rst[reg_concepto] = '';
    $rst[reg_sbt12] = 0;
    $rst[reg_sbt0] = 0;
    $rst[reg_sbt_noiva] = 0;
    $rst[reg_sbt_excento] = 0;
    $rst[reg_sbt] = 0;
    $rst[reg_tdescuento] = 0;
    $rst[reg_ice] = 0;
    $rst[reg_irbpnr] = 0;
    $rst[reg_iva12] = 0;
    $rst[reg_propina] = 0;
    $rst[reg_total] = 0;
    $rst[reg_ruc_cliente] = '';
    $rst[reg_fregistro] = date('Y-m-d');
    $rst[reg_femision] = date('Y-m-d');
    $rst[pln_id] = 0;
    $rst[reg_codigo_cta] = '';
    $rst[cli_id] = '0';
    $j = 0;
    $rst[imp_id] = 0;
    $rst[reg_importe] = '';
    $std = 0;
    $rst[reg_tipo_pago] = 0;
    $rst[reg_forma_pago] = 0;
    $rst[reg_pais_importe] = 0;
    $rst[reg_relacionado] = '';
    $visib = "";
    $visib2 = "";
    $read = "";
    $read2 = "";
    $disabled = "";
    $disabled2 = "";
    $disabled3 = "";
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            dec = '2';
            id = '<?php echo $id ?>';
            dc = '1';
            std = '<?php echo $std ?>';
            j = '<?php echo $j ?>';
            $(function () {
                $('#usu_id').val(<?php echo $rst[usu_id] ?>);
                $('#frm_detalle').submit(function (e) {
                    e.preventDefault();
                    if ($('#aumentar').attr('style') != 'display: none;' && $('#aumentar').attr('hidden') != true) {
                        clona_fila($("#tbl_detalle"), 0);
                    }
                });
                $('#frm_fpagos').submit(function (e) {
                    e.preventDefault();
                    clona_fila($("#tbl_fpagos"), 1);
                });
                $('#cancel').click(function () {
                    cancelar();
                    return false;
                });
                parent.document.getElementById('contenedor2').rows = "*,80%";
                Calendar.setup({inputField: "reg_fregistro", ifFormat: "%Y-%m-%d", button: "im-reg_fregistro"});
                Calendar.setup({inputField: "reg_femision", ifFormat: "%Y-%m-%d", button: "im-reg_femision"});
                Calendar.setup({inputField: "reg_fautorizacion", ifFormat: "%Y-%m-%d", button: "im-reg_fautorizacion"});
                Calendar.setup({inputField: "reg_fcaducidad", ifFormat: "%Y-%m-%d", button: "im-reg_fcaducidad"});
                if (id == 0) {
                    calculo_fecha();
                }
                posicion_aux_window();
                habilitar(std);
                calculo();
            });
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            function clona_fila(table, tbl) {
                $("#lst_prod").attr('id', 'lst_productos');
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input,select,img").attr("name", function () {
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
                    } else if (parts[1] == 'det_codigo_externo' || parts[1] == 'det_descuento_porcentaje' || parts[1] == 'det_descuento_moneda') {
                        this.value = 0;
                    } else {
                        this.value = '';
                    }

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
                if (tbl == 0) {
                    $('#sv' + x).attr('disabled', false);
                    $('#det_tipo' + x).attr('disabled', true);
                    $('#det_prov' + x).attr('disabled', true);
                    $('#det_tip' + x).attr('disabled', true);
                    $('#del' + x).attr('hidden', false);
                    $('#det_codigo_empresa' + x).attr('readonly', false);
                    $('#det_descripcion' + x).attr('readonly', false);
                }
            }

            function elimina_fila(obj, tbl) {
                if (tbl == 0) {
                    tb = "#tbl_fpagos .itm1";
                } else {
                    tb = "#tbl_detalle .itm";
                }
                itm = $(tb).length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo();
            }

            function save(id) {
                ///**********encabezado*****************
                ndoc = reg_num_documento0.value + '-' + reg_num_documento1.value + '-' + reg_num_documento2.value;
                fa = reg_fautorizacion.value;
                fc = reg_fcaducidad.value;
                fv = '1900-01-01';
                if (fa.length == 0) {
                    fa = '1900-01-01';
                }
                if (fc.length == 0) {
                    fc = '1900-01-01';
                }


                var data = Array(
                        reg_fregistro.value,
                        reg_femision.value,
                        fv,
                        reg_tipo_documento.value,
                        ndoc,
                        reg_num_autorizacion.value,
                        fa,
                        fc,
                        reg_tpcliente.value.toUpperCase(),
                        reg_concepto.value.toUpperCase(),
                        $('#reg_sbt12').val().replace(',', ''),
                        $('#reg_sbt0').val().replace(',', ''),
                        $('#reg_sbt_noiva').val().replace(',', ''),
                        $('#reg_sbt_excento').val().replace(',', ''),
                        $('#reg_sbt').val().replace(',', ''),
                        $('#reg_tdescuento').val().replace(',', ''),
                        $('#reg_ice').val().replace(',', ''),
                        $('#reg_irbpnr').val().replace(',', ''),
                        $('#reg_iva12').val().replace(',', ''),
                        $('#reg_propina').val().replace(',', ''),
                        $('#reg_total').val().replace(',', ''),
                        reg_ruc_cliente.value.toUpperCase(),
                        reg_num_registro.value,
                        cli_raz_social.value.toUpperCase(),
                        direccion.value.toUpperCase(),
                        telefono.value.toUpperCase(),
                        email.value,
                        reg_sustento.value,
                        cli_id.value,
                        reg_importe.value,
                        imp_id.value,
                        reg_tipo_pago.value,
                        reg_forma_pago.value,
                        reg_pais_importe.value,
                        reg_relacionado.value.toUpperCase()
                        );
///**********detalle*****************                        
                var detalle = Array();
                var sbt_mp = 0;
                ndet = 0;
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                val_dat = 0;
                while (ndet < i) {
                    ndet++;
                    if ($('#det_descripcion' + ndet).val() != null) {
                        //*************En caso de que sea Materia Prima**********************************
                        tp_prod = $('#det_tipo' + ndet).val();
                        tp_imp = $('#det_impuesto' + ndet).val();
                        if (tp_prod == '2' && tp_imp == '14') {
                            sbt_mp += ($('#det_total' + ndet).val() * 1);
                        }
                        if ($('#pln_id' + ndet).val() == '0' || $('#pln_id' + ndet).val().length == 0) {
                            val_dat = 1;
                        }
////*******************************************************************************                    
                        detalle.push($('#det_codigo_empresa' + ndet).val().toUpperCase() + '&' +
                                $('#det_descripcion' + ndet).val().toUpperCase() + '&' +
                                $('#det_cantidad' + ndet).val() + '&' +
                                $('#det_vunit' + ndet).val() + '&' +
                                $('#det_descuento_porcentaje' + ndet).val() + '&' +
                                $('#det_descuento_moneda' + ndet).val() + '&' +
                                $('#det_total' + ndet).val() + '&' +
                                $('#det_impuesto' + ndet).val() + '&' +
                                $('#det_tipo' + ndet).val() + '&' +
                                $('#det_codigo_externo' + ndet).val() + '&' +
                                $('#val' + ndet).val() + '&' +
                                $('#pro_id' + ndet).val() + '&' +
                                $('#det_tipo' + ndet).val() + '&' +
                                $('#pln_id' + ndet).val() + '&' +
                                $('#reg_codigo_cta' + ndet).val() + '&' +
                                $('#det_tip' + ndet).val() + '&' +
                                $('#det_prov' + ndet).val()
                                );
                    }

                }
///**********pagos*****************                                        
                pag = $('#tbl_fpagos .itm1');
                var pagos = Array();
                npag = 1;
                while (npag <= pag.length) {
                    pagos.push($('#pag_porcentage' + npag).val() + '&' +
                            $('#pag_dias' + npag).val() + '&' +
                            $('#pag_valor' + npag).val() + '&' +
                            $('#pag_fecha_v' + npag).val()
                            );
                    npag++;
                }

                var fields = Array();
                $("#frm_detalle").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.ajax({
                    beforeSend: function () {

                        return_v = 0;
                        if (date_validator(reg_fregistro.value) == null) {
                            $('#reg_fregistro').focus();
                            $('#reg_fregistro').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (date_validator(reg_femision.value) == null) {
                            $('#reg_femision').focus();
                            $('#reg_femision').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (date_validator(reg_fautorizacion.value) == null && reg_fautorizacion.value.length != 0) {
                            $('#reg_fautorizacion').focus();
                            $('#reg_fautorizacion').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (date_validator(reg_fcaducidad.value) == null && reg_fcaducidad.value.length != 0) {
                            $('#reg_fcaducidad').focus();
                            $('#reg_fcaducidad').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_tipo_documento.value == '0') {
                            $('#reg_tipo_documento').focus();
                            $('#reg_tipo_documento').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_sustento.value == '0') {
                            $('#reg_sustento').focus();
                            $('#reg_sustento').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_num_documento0.value.length != 3) {
                            $('#reg_num_documento0').focus();
                            $('#reg_num_documento0').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_num_documento1.value.length != 3) {
                            $('#reg_num_documento1').focus();
                            $('#reg_num_documento1').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_num_documento2.value.length != 9) {
                            $('#reg_num_documento2').focus();
                            $('#reg_num_documento2').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_num_autorizacion.value.length != 37 && reg_num_autorizacion.value.length != 10 && reg_num_autorizacion.value.length != 49) {
                            $('#reg_num_autorizacion').focus();
                            $('#reg_num_autorizacion').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_ruc_cliente.value.length == 0) {
                            $('#reg_ruc_cliente').focus();
                            $('#reg_ruc_cliente').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (cli_raz_social.value.length == 0) {
                            $('#cli_raz_social').focus();
                            $('#cli_raz_social').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_tpcliente.value.length == 0) {
                            $('#reg_tpcliente').focus();
                            $('#reg_tpcliente').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (direccion.value.length == 0) {
                            $('#direccion').focus();
                            $('#direccion').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (telefono.value.length == 0) {
                            $('#telefono').focus();
                            $('#telefono').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_relacionado.value.length == 0) {
                            $('#reg_relacionado').focus();
                            $('#reg_relacionado').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (email.value.length == 0) {
                            $('#email').focus();
                            $('#email').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (reg_concepto.value.length == 0) {
                            $('#reg_concepto').focus();
                            $('#reg_concepto').css('border', 'solid 2px red');
                            return false;
                        } else if (reg_total.value == 0 || reg_total.value.length == 0) {
                            $('#reg_total').focus();
                            $('#reg_total').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (pg_total.value != reg_total.value) {
                            $('#pg_total').focus();
                            $('#pg_total').css('border', 'solid 2px red');
                            $('#reg_total').css('border', 'solid 2px red');
                            return_v = 1;
                        } else if (pg_por.value != 100) {
                            $('#pg_por').focus();
                            $('#pg_por').css('border', 'solid 2px red');
                            return_v = 1;
                        } else {
                            $("#tbl_detalle .dt_input ").each(function () {
                                var parts = this.id.match(/(\D+)(\d+)$/);
                                j = parts[2];
                                cod_ext = 'det_codigo_externo' + j;
                                des_p = 'det_descuento_porcentaje' + j;
                                des_m = 'det_descuento_moneda' + j;
                                cntd = 'det_cantidad' + j;
                                tot1 = 'det_total' + j;
                                cod_cta = 'reg_codigo_cta' + j;
                                if (this.value == 0) {
                                    if (this.id != cod_ext && this.id != des_p && this.id != des_m && this.id != cntd && this.id != tot1) {
                                        $(this).focus();
                                        $(this).css('border', 'solid 2px red');
                                        return_v = 1;
                                    }
                                }
                                if (this.value.length == 0 && this.id != cod_cta) {
                                    $(this).focus();
                                    $(this).css('border', 'solid 2px red');
                                    return_v = 1;
                                }
                            });
                            $("#tbl_detalle .sel").each(function () {
                                if (this.value.length == 0) {
                                    $(this).focus();
                                    $(this).css('border', 'solid 2px red');
                                    return_v = 1;
                                }
                            });
                            $("#tbl_fpagos .pg_input").each(function () {
                                if (this.value.length == 0) {
                                    $(this).focus();
                                    $(this).css('border', 'solid 2px red');
                                    return_v = 1;
                                }
                            });
                        }
                        if (return_v == 1) {
                            return false;
                        } else {
                            return true;
                        }

                        loading('visible');
                    },
                    type: 'POST',
                    url: "actions_reg_docs.php",
                    data: {op: 0, 'data[]': data, 'detalle[]': detalle, 'pagos[]': pagos, sbt_mp: sbt_mp, id: id, 'fields[]': fields, estd: std, vd: val_dat},
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0 || dat[0] == 2) {
//                            retencion(dat[0], dat[1], dat[2]);
                            opcion_retenciones(dat[0], dat[1], dat[2]);
                        } else if (dat[0] == 1) {
                            alert('Una de las cuentas del Registro de Facturas esta inactiva');
                            loading('hidden');
                        } else if (dat[0] == 3) {
                            alert('Numero Secuencial del Registro ya existe \n Debe hacer otro Registro con otro Secuencial');
                            loading('hidden');
                            $('#save').attr('disabled', true);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function load_cliente(obj) {
                if (obj.value.length > 3) {
                    $.post("actions.php", {act: 63, id: obj.value, s: 0},
                    function (dt) {
                        if (dt.trim().length != 0) {
                            $('#con_clientes').css('visibility', 'visible');
                            $('#con_clientes').show();
                            $('#clientes').html(dt);
                        } else {
                            alert('Cliente no existe \n Se creará uno nuevo');
                            $('#reg_ruc_cliente').focus();
                            $('#cli_raz_social').val('');
                            $('#direccion').val('');
                            $('#telefono').val('');
                            $('#email').val('');
                            $('#cli_id').val('0');
                        }
                    });
                }
            }

            function load_cliente2(obj) {
                $.post("actions.php", {act: 63, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Se creará uno nuevo');
                        $('#cli_ced_ruc').focus();
                        $('#reg_ruc_cliente').val('');
                        $('#cli_raz_social').val('');
                        $('#direccion').val('');
                        $('#telefono').val('');
                        $('#email').val('');
                        $('#cli_id').val('0');
                        $('#reg_tpcliente').val('');
                    } else {
                        dat = dt.split('&');
                        $('#reg_ruc_cliente').val(dat[0]);
                        $('#cli_raz_social').val(dat[1]);
                        $('#direccion').val(dat[2]);
                        $('#telefono').val(dat[3]);
                        $('#email').val(dat[4]);
                        $('#cli_id').val(dat[8]);
                        if (dat[9] != 1) {
                            tp = 'LOCAL';
                        } else {
                            tp = 'EXTRANJERO';
                        }
                        $('#reg_tpcliente').val(tp);
                    }
                    $('#con_clientes').hide();
                    doc_duplicado();
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

            function date_validator(fecha) {
                var f = (fecha).match(/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/);
                var fec = fecha.split('-');
                var aho = fec[0];
                var mes = fec[1];
                var dia = fec[2];

                var fv = new Date(aho, mes - 1, dia);//mes empieza de cero Enero = 0

                if (!fv || fv.getFullYear() == aho && fv.getMonth() == mes - 1 && fv.getDate() == dia) {
                    f = f;
                } else {
                    f = null;
                }

                return f;

            }

            function calculo_fecha() {
                obj = $(".itm1");
                n = 1;
                while (n <= obj.length) {
                    if ($('#pag_dias' + n).val().length != 0) {
                        var sumarDias = parseInt($('#pag_dias' + n).val());
                        var fecha = $('#reg_femision').val();
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
                        $('#pag_fecha_v' + n).val(anio + "-" + mes + "-" + dia);
                        n++;
                    }
                }
            }

            function calculo_total_pago() {
                var t = 0;
                var tp = 0;
                obj = $(".itm1");
                total = $("#reg_total").val();
                n = 1;
                while (n <= obj.length) {
                    por = $("#pag_porcentage" + n).val();
                    vpago = (por * total / 100);
                    $("#pag_valor" + n).val(vpago.toFixed(dec));
                    t += ($("#pag_valor" + n).val() * 1);
                    tp += ($("#pag_porcentage" + n).val() * 1);
                    n++;
                }
                $("#pg_total").val(t.toFixed(dec));
                $("#pg_por").val(tp.toFixed(dec));
            }

            function calculo(obj) {
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
//                    n = obj.lang;
                    n++;
                    c = $("#det_cantidad" + n).val();
                    v = $("#det_vunit" + n).val();
                    dp = $("#det_descuento_porcentaje" + n).val();
                    tp = (c * v);
                    d = (tp * dp / 100);
                    t = (c * v) - d;
                    $("#det_descuento_moneda" + n).val(d.toFixed(dec));
                    $("#det_total" + n).val(t.toFixed(dec));
                }
                calculo_totales();
            }

            function calculo_totales() {
                var tr = $('#tbl_detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                obj = parseInt(a);
                n = 1;
                sbt12 = 0;
                sbt0 = 0;
                sbtno = 0;
                sbtex = 0;
                desc = 0;
                while (n <= obj) {
                    if ($("#det_descuento_moneda" + n).val() != null) {
                        d = ($("#det_descuento_moneda" + n).val() * 1);
                    } else {
                        d = 0;
                    }
                    desc += d;
                    switch ($("#det_impuesto" + n).val()) {
                        case '14':
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
                    n++;
                }
                ice = ($("#reg_ice").val() * 1);
                sbt = (sbt12 + sbt0 + sbtno + sbtex);
                iva = ((sbt12 + ice) * 0.14);
                gtot = (sbt + iva + ($("#reg_ice").val() * 1) + ($("#reg_irbpnr").val() * 1) + ($("#reg_propina").val() * 1));
                $("#reg_sbt12").val(sbt12.toFixed(dec));
                $("#reg_sbt0").val(sbt0.toFixed(dec));
                $("#reg_sbt_noiva").val(sbtno.toFixed(dec));
                $("#reg_sbt_excento").val(sbtex.toFixed(dec));
                $("#reg_sbt").val(sbt.toFixed(dec));
                $("#reg_tdescuento").val(desc.toFixed(dec));
                $("#reg_iva12").val(iva.toFixed(dec));
                $("#reg_total").val((gtot * 1).toFixed(dec));
                calculo_total_pago();
            }

            function load_producto(o, x) {
                v = o.value;
                n = o.lang;
                ob = v.split('-');
                val = ob[0];
                t = val.charAt(0);
                b = val.charAt(1);
                tbl = t + b;
                id = val.substring(2, (val.length));
                $('.itm').each(function () {
                    if (this.value != n) {
                        nvo = $("#sv" + this.value).attr('checked');
                        if (nvo == false) {
                            pro = $('#pro_id' + this.value).val();
                            pro2 = id;
                        } else {
                            pro = $('#det_codigo_empresa' + this.value).val().toUpperCase();
                            pro2 = v.toUpperCase();
                        }
                        if (pro2 == pro) {
                            alert('Producto ya ingresado');
                            $('#det_codigo_empresa' + n).val('');
                            $('#det_descripcion' + n).focus();
                            id = '';
                            return false;
                        }
                    }
                });
                nvo = $("#sv" + n).attr('checked');
                if (nvo == false) {
                    vf = 0;
                    id = id;
                } else {
                    vf = 1;
                    id = v.toUpperCase();
                }
                if (id != '') {
                    $.post("actions_reg_docs.php", {op: 2, id: id, tbl: tbl, doc: vf},
                    function (dt) {
                        dat = dt.split('&');
                        if (vf == 0) {
                            tab = dat[0];
                            switch (dat[0]) {
                                case '26':
                                    dat[0] = 26;
                                    break;
                                case '27':
                                    dat[0] = 27;
                                    break;
                                case '69':
                                    dat[0] = 69;
                                    break;
                                case '79':
                                    dat[0] = 79;
                                    break;
                                case '80':
                                    dat[0] = 80;
                                    break;
                                default :
                                    dat[0] = 99;
                                    break;
                            }
                            if (dat[0] == 99) {
                                nuevo = $("#sv" + n).attr('checked');
                                if (nuevo == false) {
                                    alert('Producto, Insumo, o Materia Prima No Existe , Si desea crear uno nuevo \n Porfavor activar la casilla Nuevo en el item correspondiente');
                                    $("#det_tipo" + n).val('');
                                    $("#det_codigo_empresa" + n).val('');
                                    $("#det_codigo_externo" + n).val('');
                                    $("#det_descripcion" + n).val('');
                                    $("#pro_id" + n).val('');
                                    $("#tab" + n).val(tbl);
                                    $("#det_tip" + n).val('');
                                    $("#det_prov" + n).val('');
                                }
                                $("#val" + n).val(1);
                            } else {
                                $("#val" + n).val(0);
                                $("#det_tipo" + n).val(dat[0]);
                                $("#det_codigo_empresa" + n).val(dat[1]);
                                $("#det_codigo_empresa" + n).attr('readonly', '');
                                $("#det_descripcion" + n).val(dat[2]);
                                $("#tab" + n).val(tab);
                                $("#pro_id" + n).val(dat[3]);
                                if (dat[4] != '') {
                                    $("#pln_id" + n).val(dat[4]);
                                } else {
                                    $("#pln_id" + n).val('0');
                                }
                                if (dat[5] != '') {
                                    $("#reg_codigo_cta" + n).val(dat[5]);
                                } else {
                                    $("#reg_codigo_cta" + n).val('');
                                }
                                $("#det_tip" + n).val(dat[6]);
                                $("#det_prov" + n).val(dat[7]);
                                $("#sv" + n).attr('checked', false);
                                var parent = $(obj).parents();
                                $(parent[1]).css('background-color', 'transparent');
                            }
                        } else {
                            $("#val" + n).val(1);
                            if (dat[8] == '1') {
                                $("#val" + n).val(0);
                                alert('Producto ya existe');
                                $("#det_codigo_empresa" + n).val('');
                                $("#det_codigo_externo" + n).val('');
                                $("#det_descripcion" + n).val('');
                                $("#pro_id" + n).val('');
                                $("#det_tipo" + n).val('');
                                $("#det_tip" + n).val('');
                                $("#det_prov" + n).val('');
                                $("#tab" + n).val('');
                            }
                        }
                    });
                } else {
                    $("#det_tipo" + n).val('0');
                    $("#det_codigo_empresa" + n).val('');
                    $("#det_codigo_externo" + n).val('0');
                    $("#det_descripcion" + n).val('');
                    $("#pro_id" + n).val('');
                    $("#tab" + n).val(tbl);
                    $("#pln_id" + n).val('0');
                    $("#reg_codigo_cta" + n).val('');
                    $("#det_tip" + n).val('');
                    $("#det_prov" + n).val('');
                }
            }

            function habilita_lista(obj) {
                n = obj.lang;
                var parent = $(obj).parents();
                var cod = $('#det_codigo_empresa' + n);
                if (obj.checked == true) {
                    $(parent[1]).css('background-color', 'navajowhite');
                    cod.attr('readonly', '');
                    $("#det_tipo" + n).val('');
                    $("#det_tip" + n).val('');
                    $("#det_prov" + n).val('');
                    $("#det_tipo" + n).attr('disabled', '');
                    $("#det_tip" + n).attr('disabled', '');
                    $("#det_prov" + n).attr('disabled', '');
                    $("#det_codigo_empresa" + n).val('');
                    $("#det_codigo_externo" + n).val(0);
                    $("#det_descripcion" + n).val('');
                    $("#pro_id" + n).val('');
                    $("#pln_id" + n).val('0');
                    $("#reg_codigo_cta" + n).val('');
                    $("#lst_productos").attr('id', 'lst_prod');
                } else {
                    $(parent[1]).css('background-color', 'transparent');
                    cod.attr('readonly', '');
                    $("#det_codigo_empresa" + n).val('');
                    $("#det_tipo" + n).val('');
                    $("#det_tip" + n).val('');
                    $("#det_prov" + n).val('');
                    $("#pro_id" + n).val('');
                    $("#det_tipo" + n).attr('disabled', 'disabled');
                    $("#det_tip" + n).attr('disabled', 'disabled');
                    $("#det_prov" + n).attr('disabled', 'disabled');
                    $("#pln_id" + n).val('0');
                    $("#reg_codigo_cta" + n).val('');
                    $("#lst_prod").attr('id', 'lst_productos');
                }
            }

            function load_codigo_nuevo(obj) {
                n = obj.lang;
                if (obj.value.length > 0) {
                    $.post("actions_reg_docs.php", {op: 3, id: obj.value},
                    function (dt) {
                        dat = dt.split('&');
                        $('#cod_aux' + n).val(dat[0]);
                        $('#cod_aux' + n).attr('title', dat[1]);
                        $('#tab' + n).val(obj.value);
                        if (dat[0] != '')
                        {
                            $('#det_codigo_empresa' + n).val(dat[0] + dat[1]);
                            if (n > 1) {
                                codi = dat[0];
                                cdg = dat[1];
                                codigo(n, codi, cdg);
                            }
                        }
                    });
                }
            }

            function codigo(n, codi, cdg) {
                tr = $("#detalle").find("input:hidden[value=" + codi.trim() + "]");
                if (tr.length == 1) {
                    n1 = 0;
                    sec = ((tr.eq(n1).attr('title') * 1));
                } else {
                    n1 = (tr.length - 2);
                    sec = ((tr.eq(n1).attr('title') * 1) + 1);
                }
                if (sec >= 0 && sec < 10) {
                    tx = '000';
                } else if (sec >= 10 && sec < 100) {
                    tx = '00';
                } else if (sec >= 100 && sec < 1000) {
                    tx = '0';
                } else if (sec >= 1000 && sec < 10000) {
                    tx = '';
                }
                cod = tx + '' + sec;
                $('#det_codigo_empresa' + n).val(dat[0] + cod);
                $('#cod_aux' + n).attr('title', cod);
            }

            function asg_autocomplete(obj) {
                $(obj).autocomplete({source: productos});
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function retencion(f) {
                op = $('#rop').val();
                id = $('#rid').val();
                asi = $('#rasi').val();
                if (f == 0) {
                    var_msm = 0;
                    if (std == '5') {
                        op = '2';
                    }
                    if (op == '0') {
                        url1 = '../Scripts/Form_retencion.php?reg_id=' + id;
                        frm = parent.document.getElementById('bottomFrame').src = url1;
                    } else if (op == '2') {
                        tbl_estado.style.visibility = 'hidden'
                        $('#tbl_estado').hide();
                        imp_asiento();
                    }
                } else if (f == 1) {
                    $('#imp_porcentaje').show();
                    $('#guardar').show();
                    $('#aceptar').hide();
                    $('#otros').hide();
                } else if (f == 2) {
                    tbl_estado.style.visibility = 'hidden'
                    $('#tbl_estado').hide();
                    imp_asiento();
                }
            }
            function imp_asiento() {
                asi = $('#rasi').val();
                if (asi != 0) {
                    msm = 'la Impresion del asiento contable';
                    url = '../Scripts/frm_pdf_asientos.php?id=' + asi + '&asi=1';
                    var r = confirm("Desea generar " + msm);
                    if (r == true) {
                        frm = parent.document.getElementById('bottomFrame').src = url;
                    } else {
                        cancelar();
                        window.history.go();
                    }
                } else {
                    cancelar();
                    window.history.go();
                }
            }

            function habilitar(std) {
                if (std == 2 || std == 1) {
                    cod_aux = 0;
                    load_encabezado_ant();
                }
                $('.dt_input').each(function () {
                    var pts = this.id.match(/(\D+)(\d+)$/);
                    if (pts[1] == 'det_codigo_externo') {
                        this.value = cod_aux;
                    }
                });
            }

            function doc_duplicado() {
                num_doc0 = $('#reg_num_documento0').val();
                num_doc1 = $('#reg_num_documento1').val();
                num_doc2 = $('#reg_num_documento2').val();
                num_doc = num_doc0 + '-' + num_doc1 + '-' + num_doc2;
                ruc_pro = $('#reg_ruc_cliente').val();
                tip_doc = $('#reg_tipo_documento').val();
                if (num_doc.length = 17 && ruc_pro.length > 0 && tip_doc != 0) {
                    $.post("actions_reg_docs.php", {op: 4, doc: num_doc, ruc: ruc_pro, data: tip_doc},
                    function (dt) {
                        dat = dt.split('&');
                        if (dat[0] != '') {
                            $('#con_clientes').hide();
                            $('#clientes').html(dt);
                            alert('EL numero de Documento y el Ruc del Proveedor \n Ya existen en el Registro de Facturas');
                            $('#reg_num_documento0').val('');
                            $('#reg_num_documento1').val('');
                            $('#reg_num_documento2').val('');
                            $('#reg_num_autorizacion').val('');
                            $('#reg_ruc_cliente').val('');
                            $('#cli_raz_social').val('');
                            $('#direccion').val('');
                            $('#telefono').val('');
                            $('#email').val('');
                            $('#reg_num_documento0').focus();
                            $('#reg_num_documento0').css({borderColor: "red"});
                            $('#reg_num_documento1').css({borderColor: "red"});
                            $('#reg_num_documento2').css({borderColor: "red"});
                            $('#reg_tipo_documento').val('0');
                            $('#reg_sustento').val('0');
                            $('#reg_tpcliente').val('');
                            $('#reg_concepto').val('');
                        } else {
                            load_encabezado_ant();
                        }
                    });
                }
            }

            function load_codigo(obj) {
                n = obj.lang;
                $.post("actions_reg_docs.php", {op: 6, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id' + n).val(dat[0]);
                        $('#reg_codigo_cta' + n).val(dat[1]);
                    } else {
                        $('#pln_id' + n).val('0');
                        $('#reg_codigo_cta' + n).val('');
                    }
                });
            }


            function load_encabezado_ant() {
                cli = cli_id.value;
                $.post("actions_reg_docs.php", {op: 7, id: cli},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#reg_sustento').val(dat[0]);
                    }
                    if (dat[1] != '') {
                        $('#reg_num_autorizacion').val(dat[1]);
                    }
                    if (dat[2] != '') {
                        $('#reg_fautorizacion').val(dat[2]);
                    }
                    if (dat[3] != '') {
                        $('#reg_fcaducidad').val(dat[3]);
                    }
                    if (dat[4] != '') {
                        $('#reg_tpcliente').val(dat[4]);
                    }
                    if (dat[5] != '') {
                        $('#reg_concepto').val(dat[5]);
                    }
                });
            }

            function limpliar_ruc() {
                $('#identificacion').val('');
                $('#identificacion').focus();
            }

            function validar_detalle(id) {
                if (std == 2) {
                    var tr = $('#tbl_detalle').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    obj = parseInt(a);
                    var detalle = Array();
                    ndet = 1;
                    return_v = 0;
                    while (ndet <= obj) {
                        detalle.push($('#det_codigo_empresa' + ndet).val().toUpperCase() + '&' +
                                $('#det_cantidad' + ndet).val() + '&' +
                                $('#det_vunit' + ndet).val() + '&' +
                                $('#det_tipo' + ndet).val() + '&' +
                                $('#pro_id' + ndet).val()
                                );
                        ndet++;
                    }

                    $("#tbl_detalle .dt_input ").each(function () {
                        var parts = this.id.match(/(\D+)(\d+)$/);
                        j = parts[2];
                        cod_ext = 'det_codigo_externo' + j;
                        des_p = 'det_descuento_porcentaje' + j;
                        des_m = 'det_descuento_moneda' + j;
                        cntd = 'det_cantidad' + j;
                        tot1 = 'det_total' + j;
                        cod_cta = 'reg_codigo_cta' + j;

                        if (this.value == 0) {
                            if (this.id != cod_ext && this.id != des_p && this.id != des_m && this.id != cntd && this.id != tot1) {
                                $(this).focus();
                                $(this).css('border', 'solid 2px red');
                                return_v = 1;
                            }
                        }
                        if (this.value.length == 0 && this.id != cod_cta) {
                            $(this).focus();
                            $(this).css('border', 'solid 2px red');
                            return_v = 1;
                        }
                    });
                    $("#tbl_detalle .sel").each(function () {
                        if (this.value.length == 0) {
                            $(this).focus();
                            $(this).css('border', 'solid 2px red');
                            return_v = 1;
                        }
                    });
                    if (return_v == 0) {
                        $.post("actions_reg_docs.php", {op: 8, id: id, 'data[]': detalle},
                        function (dt) {
                            if (id != 0) {
                                vd = dt.split('&');
                                if (vd[0] == 1) {
                                    var r = confirm("Los Datos del detalle fueron \n modificados esta seguro de seguir");
                                    if (r == true) {
                                        if (vd[1] != 1) {
                                            save(id);
                                        } else {
                                            alert('Los productos no tienen suficiente inventario');
                                            return false;
                                        }
                                    } else {
                                        return false;
                                    }
                                } else {
                                    save(id);
                                }
                            } else {
                                save(id);
                            }
                        });
                    }
                } else {
                    save(id);
                }
            }

            function validar_fechas(obj) {
                fecha = obj.value;
                f = (fecha).match(/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/);
                var fec = fecha.split('-');
                var aho = fec[0];
                var mes = fec[1];
                var dia = fec[2];

                var fv = new Date(aho, mes - 1, dia);//mes empieza de cero Enero = 0

                if (!fv || fv.getFullYear() == aho && fv.getMonth() == mes - 1 && fv.getDate() == dia) {
                    f = f;
                } else {
                    f = null;
                }

                if (f == null) {
                    $(obj).focus();
                    $(obj).css('border', 'solid 2px red');
                    $(obj).val('');
                } else {
                    $(obj).css('border', '');
                }
            }

            function completar_ceros(obj, v) {
                o = obj.value;
                val = parseFloat(o);
                if (v == 0) {
                    if (val == 0) {
                        alert("Numero incorrecto");
                        $(obj).val('');
                    } else if (val > 0 && val < 10) {
                        txt = '00';
                    } else if (val >= 10 && val < 100) {
                        txt = '0';
                    } else if (val >= 100 && val < 1000) {
                        txt = '';
                    }
                    $(obj).val(txt + val);
                } else {
                    if (val > 0 && val < 10) {
                        txt = '00000000';
                    } else if (val >= 10 && val < 100) {
                        txt = '0000000';
                    } else if (val >= 100 && val < 1000) {
                        txt = '000000';
                    } else if (val >= 1000 && val < 10000) {
                        txt = '00000';
                    } else if (val >= 10000 && val < 100000) {
                        txt = '0000';
                    } else if (val >= 100000 && val < 1000000) {
                        txt = '000';
                    } else if (val >= 1000000 && val < 10000000) {
                        txt = '00';
                    } else if (val >= 10000000 && val < 100000000) {
                        txt = '0';
                    } else if (val >= 100000000 && val < 1000000000) {
                        txt = '';
                    }
                    $(obj).val(txt + val);

                    if (val == 0 || o.length == 0) {
                        alert("Numero incorrecto");
                        $(obj).val('');
                        return false;
                    }
                }
                doc_duplicado()
            }




            function opcion_retenciones(op, id, asi) {
                $('#rop').val(op);
                $('#rid').val(id);
                $('#rasi').val(asi);
                if (std != '5') {
                    $('#tbl_estado').show();
                    $('#save').attr('disabled', true);
                    $("#estado").attr('checked', false);
                    lf = ($(window).width() / 2) - 200;
                    tp = ($(window).height() / 2) - 200;
                    tbl_estado.style.left = lf;
                    tbl_estado.style.top = tp;
                } else {
                    if (asi != '') {
                        imp_asiento();
                    } else {
                        cancelar();
                        window.history.go();
                    }

                }
            }

            function guardar_retencion() {
                vnd = '<?php echo $_SESSION[usuid] ?>';
                comp_ret = reg_num_documento0.value + '-' + reg_num_documento1.value + '-' + reg_num_documento2.value;
                rt = rtipo.value.split('_');
                ejer = reg_femision.value.split('-');
                ejf = ejer[1] + '/' + ejer[0];
                var data = Array(cli_id.value,
                        '1',
                        vnd,
                        '',
                        cli_raz_social.value,
                        reg_ruc_cliente.value,
                        direccion.value,
                        email.value,
                        comp_ret,
                        reg_tipo_documento.value,
                        telefono.value,
                        '0',
                        reg_femision.value,
                        rid.value
                        );
                var data2 = Array(
                        impuesto.value, //por_id
                        ejf, //ejercicio_fiscal,
                        reg_sbt.value, //base_imponible,
                        rt[1], //tipo
                        rcodigo.value, //codigo
                        0, //porcentaje_retencion,
                        0 //valor_retenido
                        );
                $.ajax({
                    beforeSend: function () {
                        if ($('#impuesto').val() == '0') {
                            $('#impuesto').css({borderColor: "red"});
                            $('#impuesto').focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_reg_docs.php',
                    data: {op: 9, 'data[]': data, 'detalle[]': data2}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            $('#tbl_estado').hide();
                            imp_asiento();
                        } else {
                            alert(dt);
                        }
                    }
                })
            }

            function load_impuesto(obj) {
                $.post("actions_retencion.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#rtipo').val(dat[3]);
                        $('#rcodigo').val(dat[2]);
                    } else {
                        $('#rtipo').val('');
                        $('#rcodigo').val('0');
                    }
                });
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
            input[type=text]{
                text-transform:uppercase !important; 
            }
            #tbl_form{
                border:solid 1px; 
            }
            *{
                font-size:11px; 
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
            .sel{
                width: 100px;
            }
            #btnsms{
                width:200px;
                height:auto important;
                z-index:999999;
                bottom:5px;
                color: #9F6000;
                background-color: #FEEFB3;
                padding:5px;

            }
            #tbl_estado {
                font-size:12px; 
                width: 500px;
                height: 100px;
                position:fixed;
                background:white;
                border: solid 1px;

            }
            #disable{
                position: absolute;
                background:#f8f8f8;
                opacity: 0.4;
                top:0;
                left:0; 
                width:100%;
                height:100%; 
                cursor:no-drop; 
                z-index:999999; 
                visibility:hidden; 
            }
        </style>
    </head>
    <body>
        <div id="disable" ></div> 
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden';
                    limpliar_ruc()">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table id="tbl_estado" cellpadding='5' hidden>
            <tr>
                <td colspan="6" style="font-size: 12px; font-weight: bolder" align="center">
                    ¿Desea generar la retencion del documento?
                    <input type="hidden" size="5" id="rid" />
                    <input type="hidden" size="5" id="rop" />
                    <input type="hidden" size="5" id="rasi" />
                </td>
            </tr>
            <tr id="imp_porcentaje" hidden >
                <td align="center">IMPUESTO:
                    <select id="impuesto" onchange="load_impuesto(this)">
                        <!-- value es el id del porcentaje cambiar segun tabla porcentage_retencion-->
                        <option value='0'>ELIJA UN IMPUESTO</option>
                        <option value='3'>332</option>
                        <option value='4'>332A</option>
                        <option value='5'>332B</option>
                        <option value='6'>332C</option>
                        <option value='7'>332D</option>
                        <option value='8'>332E</option>
                        <option value='9'>332F</option>
                        <option value='10'>332G</option>
                    </select>
                    <input type="hidden" size="5" id="rtipo" value=""/>
                    <input type="hidden" size="5" id="rcodigo" value="0"/>
                </td>

            </tr>
            <tr>
                <td align="center">
                    <button id="guardar" onclick="guardar_retencion()" hidden>Guardar</button>
                    <button id="aceptar" onclick="retencion(0)">Retencion</button>
                    <button id="otros" onclick="retencion(1)"/>Retencion 332</button>
                    <button id="cancelar" onclick="retencion(2)"/>Cancelar</button></td>
            </tr>


        </table>
        <table id="tbl_form" cellpadding="0"  border="0"  autocomplete="off" >
            <thead>
                <tr>
                    <th colspan="5" >
                        <font style="float:left"><?PHP echo "Registro No: " ?><input type="text" style="background:#ccc;" id="reg_num_registro" value="<?php echo $num_doc ?>"/></font>
                        <?PHP echo "FORMULARIO DE REGISTRO DE FACTURAS" ?>
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fecha de Registro:</td>
                    <td colspan="3">
                        <input type="text" id="reg_fregistro" size="10" value="<?php echo $rst[reg_fregistro] ?>" readonly onchange="validar_fechas(this)" maxlength="10"/>
                        <img width="16px" src="../img/calendar.png" id="im-reg_fregistro"/>
                    </td>
                    <td style="overflow:scroll;" rowspan="10" valign="top" align="left"  >
                        <form id="frm_fpagos" autocomplete="off">
                            <table id="tbl_fpagos">
                                <thead>
                                    <tr><th colspan="6">Formas de Pago</th></tr>
                                    <tr>
                                        <th>No</th>
                                        <th>%</th>
                                        <th>Días</th>
                                        <th>Valor</th>
                                        <th>Fecha</th>
                                        <th>-</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (pg_num_rows($cns_pag) == 0) {
                                        ?>
                                        <tr>
                                            <td align="right">
                                                <input type="text" size="2" class="itm1" id="item1" name="item1"  value="1" lang="1" readonly style="text-align:right"/>
                                            </td>
                                            <td><input class="pg_input" type="text" size="5" id="pag_porcentage1" name="pag_porcentage1" lang="1" maxlength="4" onkeyup=" this.value = this.value.replace(/[^0-9]/, ''), calculo_total_pago()" value="100"/></td>
                                            <td><input class="pg_input" type="text" size="7" id="pag_dias1" name="pag_dias1" lang="1" onchange="calculo_fecha(this)"  onkeyup=" this.value = this.value.replace(/[^0-9]/, '')" value="1"/></td>
                                            <td><input class="pg_input" type="text" size="7" id="pag_valor1" name="pag_valor1" lang="1" onkeyup="calculo_total_pago()" style="text-align:right" readonly />
                                            </td>
                                            <td>
                                                <input class="pg_input" type="date" size="10" id="pag_fecha_v1" name="pag_fecha_v1" lang="1"  readonly />
                                            </td>
                                            <td onclick="elimina_fila(this, 0)" align="center" ><img id="eli1" class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
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
                                                <td align="right"><input type="text" size="2" class="itm1" id="<?php echo 'item' . $npg ?>" name="<?php echo 'item' . $npg ?>"   value="<?php echo $npg ?>" lang="<?php echo $npg ?>" readonly style="text-align:right"/></td>
                                                <td><input class="pg_input" type="text" size="5" id="<?php echo 'pag_porcentage' . $npg ?>" name="<?php echo 'pag_porcentage' . $npg ?>" value="<?php echo $rst_pag[pag_porcentage] ?>" lang="<?php echo $npg ?>" maxlength="4"  onkeyup=" this.value = this.value.replace(/[^0-9]/, ''), calculo_total_pago()"/></td>
                                                <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_dias' . $npg ?>" name="<?php echo 'pag_dias' . $npg ?>" value="<?php echo $rst_pag[pag_dias] ?>" lang="<?php echo $npg ?>" onchange="calculo_fecha(this)" onkeyup=" this.value = this.value.replace(/[^0-9]/, '')"/></td>
                                                <td><input class="pg_input" type="text" size="7" id="<?php echo 'pag_valor' . $npg ?>" name="<?php echo 'pag_valor' . $npg ?>" value="<?php echo str_replace(',', '', number_format($rst_pag[pag_valor], 2)) ?>" lang="<?php echo $npg ?>" onkeyup="calculo_total_pago()" style="text-align:right" readonly/>
                                                </td>
                                                <td>
                                                    <input class="pg_input" type="date" size="10" id="<?php echo 'pag_fecha_v' . $npg ?>" name="<?php echo 'pag_fecha_v' . $npg ?>" value="<?php echo $rst_pag[pag_fecha_v] ?>" lang="<?php echo $npg ?>" readonly />
                                                </td>
                                                <td onclick="elimina_fila(this, 0)" align="center" ><img id="<?php echo 'eli' . $npg ?>" class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
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
                                    <tr>
                                        <td>Total:</td>
                                        <td><input type="text" size="7" value="<?php echo round($pag_por) ?>" id="pg_por" style="text-align:right" readonly/></td>
                                        <td></td>
                                        <td><input type="text" size="7" value="<?php echo str_replace(',', '', number_format($pag_total, 2)) ?>" id="pg_total" style="text-align:right" readonly/></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form> 
                    </td>
                </tr>
                <tr>
                    <td>Fecha de Emision Factura:</td>
                    <td>
                        <input type="text" id="reg_femision" size="10" value="<?php echo $rst[reg_femision] ?>" onchange="calculo_fecha(), validar_fechas(this)" <?php echo $read3 ?> maxlength="10"/>
                        <img width="16px" src="../img/calendar.png" id="im-reg_femision" <?php echo $visib3 ?>/>
                    </td>
                </tr>
                <tr>
                    <td>Tipo de Documento:</td>
                    <td>
                        <select id="reg_tipo_documento" style="width:270px; " onchange="doc_duplicado()" >
                            <?php
                            $cns_tdc = $Docs->lista_tipo_documentos();
                            echo "<option value='0'>Elija un Tipo</option>";
                            while ($rst_tdc = pg_fetch_array($cns_tdc)) {
                                echo "<option value='$rst_tdc[tdc_id]'>$rst_tdc[tdc_codigo] - $rst_tdc[tdc_descripcion] </option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Sustento:</td>
                    <td>
                        <select id="reg_sustento" style="width:270px; ">
                            <?php
                            $cns_sustento = $Docs->lista_sustento_documentos();
                            echo "<option value='0'>Elija un Sustento</option>";
                            while ($rst_sust = pg_fetch_array($cns_sustento)) {
                                echo "<option value='$rst_sust[sus_id]'>$rst_sust[sus_codigo] - $rst_sust[sus_descripcion] </option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td># Importacion:</td>
                    <td><input type="text" id="reg_importe" size="20" maxlength="8" value="<?php echo $rst[reg_importe] ?>" />
                        <input type="hidden" id="imp_id" size="10" value="<?php echo $rst[imp_id] ?>" /></td>

                </tr>
                <tr>
                    <td>Numero de Documento:</td>
                    <td>
                        <input type="text" id="reg_num_documento0" size="3" maxlength="3" value="<?php echo $dn[0] ?>" onkeyup=" this.value = this.value.replace(/[^0-9]/, '')" onchange="completar_ceros(this, 0)" <?php echo $disabled ?>/>
                        -<input type="text" id="reg_num_documento1" size="3" maxlength="3" value="<?php echo $dn[1] ?>" onkeyup=" this.value = this.value.replace(/[^0-9]/, '')" onchange="completar_ceros(this, 0)" <?php echo $disabled ?>/>
                        -<input type="text" id="reg_num_documento2" size="34" maxlength="9" value="<?php echo $dn[2] ?>" onkeyup=" this.value = this.value.replace(/[^0-9]/, '')" onchange="completar_ceros(this, 1)" <?php echo $disabled ?>/>
                    </td>
                    <td>Pais origen:</td>
                    <td>
                        <select id="reg_pais_importe" style="width:150px; ">
                            <?php
                            $cns_pai = $Docs->lista_paises();
                            echo "<option value='0'>Elija un Pais</option>";
                            while ($rst_pai = pg_fetch_array($cns_pai)) {
                                echo "<option value='$rst_pai[pai_id]'>$rst_pai[pai_codigo] - $rst_pai[pai_descripcion] </option>";
                            }
                            ?>
                        </select>
                    </td>

                </tr>
                <tr>
                    <td>Numero de Autorizacion:</td>
                    <td><input type="text" id="reg_num_autorizacion" size="50" maxlength="49"  min="49" value="<?php echo $rst[reg_num_autorizacion] ?>" onkeyup=" this.value = this.value.replace(/[^0-9]/, '')" <?php echo $disabled2 ?>/></td>
                    <td>Fecha de Autorizacion:</td>
                    <td>
                        <input type="text" id="reg_fautorizacion" size="20" value="<?php echo $rst[reg_fautorizacion] ?>" onchange="validar_fechas(this)" maxlength="10"/>
                        <img width="16px" src="../img/calendar.png" id="im-reg_fautorizacion"/>
                    </td>

                </tr>
                <tr>
                    <td>Proveedor RUC:</td>
                    <td><input type="text" id="reg_ruc_cliente" maxlength="13" size="50" onchange="load_cliente(this)" value="<?php echo $rst[reg_ruc_cliente] ?>" onkeyup="this.value = this.value.replace(/[^a-zA-Z0-9]/, '')" <?php echo $disabled ?>/></td>
                    <td>Fecha de Caducidad:</td>
                    <td>
                        <input type="text" id="reg_fcaducidad" size="20"  value="<?php echo $rst[reg_fcaducidad] ?>" onchange="validar_fechas(this)" maxlength="10"/>
                        <img width="16px" src="../img/calendar.png" id="im-reg_fcaducidad" />
                    </td>

                </tr>
                <tr>
                    <td>Proveedor Razon Social:</td>
                    <td><input type="text" id="cli_raz_social" size="50" value="<?php echo $rst_cli[cli_raz_social] ?>" <?php echo $disabled ?>/>
                        <input type="hidden" id="cli_id" size="10" value="<?php echo $rst[cli_id] ?>" /></td>
                    <td>Tipo pago:</td>
                    <td>
                        <select id="reg_tipo_pago" style="width:150px; ">
                            <option value='0'>Elija un Tipo</option>
                            <option value='01'>01 - Pago a Residente</option>
                            <option value='02'>02 - Pago a no Residente</option>
                        </select>
                    </td>

                </tr>

                <tr>
                    <td>Direccion:</td>
                    <td><input type="text" id="direccion" size="50" value="<?php echo $rst_cli[cli_calle_prin] ?>" <?php echo $disabled2 ?>/></td>
                    <td>Forma de Pago:</td>
                    <td>
                        <select id="reg_forma_pago" style="width:150px; ">
                            <?php
                            $cns_fpg = $Docs->lista_formas_pago();
                            echo "<option value='0'>Elija una Forma</option>";
                            while ($rst_fpg = pg_fetch_array($cns_fpg)) {
                                echo "<option value='$rst_fpg[fpg_id]'>$rst_fpg[fpg_codigo] - $rst_fpg[fpg_descripcion] </option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Telefono:</td>
                    <td><input type="text" id="telefono" size="50" value="<?php echo $rst_cli[cli_telefono] ?>" <?php echo $disabled2 ?>/></td>
                    <td>Doc. Relacionado:</td>

                    <td>
                        <select id="reg_relacionado" style="width:150px; ">
                            <option value=''>Elija un Tipo</option>
                            <option value='NO'>NO</option>
                            <option value='SI'>SI</option>
                        </select>
                    </td>  
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="email" id="email" size="50" value="<?php echo $rst_cli[cli_email] ?>" style="text-transform: lowercase" <?php echo $disabled2 ?>/></td>

                </tr>
                <tr>
                    <td>Tipo Proveedor:</td>
                    <td>
                        <select id="reg_tpcliente" style="width:270px; ">
                            <option value=''>Elija un Tipo</option>
                            <option value='LOCAL'>LOCAL</option>
                            <option value='EXTRANJERO'>EXTRANJERO</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Concepto:</td>
                    <td><input type="text" id="reg_concepto" size="50" value="<?php echo $rst[reg_concepto] ?>" <?php echo $disabled2 ?>/></td>
                </tr>
                <tr><td><br></td></tr>
                <tr>
                    <td colspan="5">
                        <form id="frm_detalle" autocomplete="off">
                            <table border="0" align="left" id="tbl_detalle" width="100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nuevo</th>
                                        <th>Productos</th>
                                        <th>Tipo</th>
                                        <th>Familia</th>
                                        <th>Cta.Contable</th>
                                        <th>Cod. Empresa</th>
                                        <th>Cod. Externo</th>
                                        <th>Descripcion</th>
                                        <th>Cantidad</th>
                                        <th>V.Unitario</th>
                                        <th>Descuento%</th>
                                        <th>Descuento$</th>
                                        <th>Total</th>
                                        <th>Impuesto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="detalle">
                                    <?php
                                    if (pg_num_rows($cns_det) == 0) {
                                        ?>
                                        <tr>
                                            <td align="right">
                                                <input type="text" size="2" class="itm" id="item1" name="item1"   value="1" lang="1" readonly style="text-align:right"/>
                                                <input type="hidden" size="2"  id="val1" name="val1" lang="1" />
                                            </td>
                                            <td align="center">
                                                <input class="check" type="checkbox" name="sv1" id="sv1" lang="1" title="Activar/Desactivar Nuevo Registro de Producto, Insumos Otro, Materia Prima" onclick="habilita_lista(this)" />
                                            </td>
                                            <td>
                                                <select  id="det_tipo1" name="det_tipo1" lang="1"  disabled class="sel" >
                                                    <option value="">SELECCIONE</option>
                                                    <?php
                                                    while ($rst_c = pg_fetch_array($cns_comb)) {
                                                        $dt = explode('&', $rst_c[mp_tipo]);
                                                        ?>
                                                        <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="det_tip1" name="det_tipo1" lang="1" disabled class="sel">
                                                    <option value="">SELECCIONE</option>
                                                    <?php
                                                    while ($rst_t = pg_fetch_array($cns_tip)) {
                                                        ?>
                                                        <option value="<?php echo $rst_t[tps_id] ?>"><?php echo $rst_t[tps_nombre] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="det_prov1" name="det_prov" lang="1" disabled class="sel">
                                                    <option value="">SELECCIONE</option>
                                                    <?php
                                                    while ($rst_p = pg_fetch_array($cns_prot)) {
                                                        ?>
                                                        <option value="<?php echo $rst_p[tps_id] ?>"><?php echo $rst_p[tps_nombre] ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input class="" type="text" id="reg_codigo_cta1" size="20"  lang="1" list="cuentas" onchange="load_codigo(this)" onfocus="this.style.width = '400px';" onblur="this.style.width = '120px';" />
                                                <input type="hidden" id="pln_id1" size="10" value="<?php echo $rst[pln_id] ?>" lang="1" />
                                            </td>
                                            <td>
                                                <input class="dt_input" type="text" size="10" id="det_codigo_empresa1" name="det_codigo_empresa1" lang="1" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" onchange="load_producto(this, 0)" onfocus="asg_autocomplete(this)" list="lst_productos" />
                                                <input type="hidden" id="cod_aux1" lang="1" />
                                                <input type="hidden" id="pro_id1" lang="1" />
                                                <input type="hidden" id="tab1" lang="1" />
                                            </td>
                                            <td><input class="dt_input" type="text" size="10" id="det_codigo_externo1" name="det_codigo_externo1" lang="1"  value="0"/></td>
                                            <td><input class="dt_input" type="text" size="50" id="det_descripcion1" name="det_descripcion1" lang="1" onchange="load_producto(this, 1)" onfocus="asg_autocomplete(this)" style="font-size:9px;height:20px;" list="lst_productos"/></td>
                                            <td><input class="dt_input" type="text" size="7" id="det_cantidad1" name="det_cantidad1" lang="1"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                                            <td><input class="dt_input" type="text" size="7" id="det_vunit1" name="det_vunit1" lang="1"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                                            <td><input class="dt_input" type="text" size="7" id="det_descuento_porcentaje1" name="det_descuento_porcentaje1" lang="1"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" value="0"/></td>
                                            <td><input class="dt_input" type="text" size="7" id="det_descuento_moneda1" name="det_descuento_moneda1" lang="1" readonly style="text-align:right"/></td>                                        
                                            <td><input class="dt_input" type="text" size="7" id="det_total1" name="det_total1" lang="1" readonly style="text-align:right" /></td>
                                            <td>
                                                <select id="det_impuesto1" name="det_impuesto1" lang="1" onchange="calculo(this)" >
 
                                                    <option value="14">IVA 14</option>
                                                    <option value="0">IVA 0</option>
                                                    <option value="NO">NO OBJETO</option>
                                                    <option value="EX">EXCENTO</option>
                                                </select>
                                            </td>
                                            <td onclick="elimina_fila(this, 1)" align="center" ><img id="del1" class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
                                        </tr>
                                        <?php
                                    } else {
                                        $ndt = 0;
                                        while ($rst_det = pg_fetch_array($cns_det)) {
                                            $ndt++;
                                            $rts_producto = pg_fetch_array($Docs->lista_producto_insumos_otros_cod($rst_det[det_codigo_empresa]));

                                            if ($std == 2) {
                                                $rst_ant = pg_fetch_array($Docs->lista_producto_ant($rts_producto[id]));
                                                if (empty($rst_ant)) {
                                                    $rst_det[pln_id] = '0';
                                                    $rst_det[reg_codigo_cta] = '';
                                                } else {
                                                    $rst_det[pln_id] = $rst_ant[pln_id];
                                                    $rst_det[reg_codigo_cta] = $rst_ant[reg_codigo_cta];
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td align="right">
                                                    <input type="text" size="2" class="itm" id="<?php echo 'item' . $ndt ?>" name="<?php echo 'item' . $ndt ?>"   value="<?php echo $ndt ?>" lang="<?php echo $ndt ?>" readonly style="text-align:right"/>
                                                    <input type="hidden" size="2" id="<?php echo 'val' . $ndt ?>" name="<?php echo 'val' . $ndt ?>"  value="0"   lang="<?php echo $ndt ?>" />
                                                </td>
                                                <td align="center">
                                                    <input class="check" type="checkbox" id="<?php echo 'sv' . $ndt ?>" name="<?php echo 'sv' . $ndt ?>" lang="<?php echo $ndt ?>"  title="Activar/Desactivar Nuevo Registro de Producto, Insumos Otro, Materia Prima" onclick="habilita_lista(this)" <?php echo $disabled ?>/>
                                                </td>
                                                <td>
                                                    <select id="<?php echo 'det_tipo' . $ndt ?>" name="<?php echo 'det_tipo' . $ndt ?>" lang="<?php echo $ndt ?>" disabled class="sel" >
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        $combo = $cns_comb . $ndt;
                                                        $combo = $Docs->lista_un_mp_mod1();
                                                        while ($rst_c = pg_fetch_array($combo)) {
                                                            $dt = explode('&', $rst_c[mp_tipo]);
                                                            ?>
                                                            <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="<?php echo 'det_tip' . $ndt ?>" name="<?php echo 'det_tip' . $ndt ?>" lang="<?php echo $ndt ?>" disabled class="sel" >
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        $cmbtip = $cns_tip . $ndt;
                                                        $cmbtip = $Docs->lista_un_tipo('2');
                                                        while ($rst_t = pg_fetch_array($cmbtip)) {
                                                            ?>
                                                            <option value="<?php echo $rst_t[tps_id] ?>"><?php echo $rst_t[tps_nombre] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id="<?php echo 'det_prov' . $ndt ?>" name="<?php echo 'det_prov' . $ndt ?>" lang="<?php echo $ndt ?>" disabled class="sel" >
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        $cprot = $cns_prot . $ndt;
                                                        $cprot = $Docs->lista_un_tipo('1');
                                                        while ($rst_p = pg_fetch_array($cprot)) {
                                                            ?>
                                                            <option value="<?php echo $rst_p[tps_id] ?>"><?php echo $rst_p[tps_nombre] ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="" type="text" id="<?php echo 'reg_codigo_cta' . $ndt ?>" size="20"  lang="<?php echo $ndt ?>" list="cuentas" onchange="load_codigo(this)" onfocus="this.style.width = '400px';" onblur="this.style.width = '120px';" value="<?php echo $rst_det[reg_codigo_cta] ?>"  <?php echo $disabled4 ?>/>
                                                    <input type="hidden" id="<?php echo 'pln_id' . $ndt ?>" size="10" value="<?php echo $rst_det[pln_id] ?>" lang="<?php echo $ndt ?>" />
                                                </td>
                                                <td>
                                                    <input class="dt_input" type="text" size="12" id="<?php echo 'det_codigo_empresa' . $ndt ?>" name="<?php echo 'det_codigo_empresa' . $ndt ?>" value="<?php echo $rst_det[det_codigo_empresa] ?>" lang="<?php echo $ndt ?>" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" onchange="load_producto(this)" onfocus="asg_autocomplete(this)" list="lst_productos" onchange="calculo(this)" <?php echo $read ?>/>
                                                    <input type="hidden" id="<?php echo 'cod_aux' . $ndt ?>" name="<?php echo 'cod_aux' . $ndt ?>" lang="<?php echo $ndt ?>" />
                                                    <input type="hidden" id="<?php echo 'pro_id' . $ndt ?>" lang="<?php echo $ndt ?>" value="<?php echo $rts_producto[id] ?>"/>
                                                    <input type="hidden" id="<?php echo 'tab' . $ndt ?>" lang="<?php echo $ndt ?>" value="<?php echo $rts_producto[ids] ?>"/>
                                                </td>
                                                <td><input class="dt_input" type="text" size="10" id="<?php echo 'det_codigo_externo' . $ndt ?>" name="<?php echo 'det_codigo_externo' . $ndt ?>" value="<?php echo $rst_det[det_codigo_externo] ?>" lang="<?php echo $ndt ?>" onchange="calculo(this)"/></td>
                                                <td><input class="dt_input" type="text" size="50" id="<?php echo 'det_descripcion' . $ndt ?>" name="<?php echo 'det_descripcion' . $ndt ?>" value="<?php echo $rst_det[det_descripcion] ?>" lang="<?php echo $ndt ?>" onchange="load_producto(this)" onfocus="asg_autocomplete(this)" style="font-size:9px;height:20px;" list="lst_productos" <?php echo $read ?>/></td>
                                                <td><input class="dt_input" type="text" size="7" id="<?php echo 'det_cantidad' . $ndt ?>" name="<?php echo 'det_cantidad' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_cantidad], 2)) ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                                <td><input class="dt_input" type="text" size="7" id="<?php echo 'det_vunit' . $ndt ?>" name="<?php echo 'det_vunit' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_vunit], 2)) ?>" lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                                <td><input class="dt_input" type="text" size="7" id="<?php echo 'det_descuento_porcentaje' . $ndt ?>" name="<?php echo 'det_descuento_porcentaje' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_descuento_porcentaje], 2)) ?>"  lang="<?php echo $ndt ?>"  onchange="calculo(this)" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                                <td><input class="dt_input" type="text" size="7" id="<?php echo 'det_descuento_moneda' . $ndt ?>" name="<?php echo 'det_descuento_moneda' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_descuento_moneda], 2)) ?>"  lang="<?php echo $ndt ?>"  readonly style="text-align:right" /></td>                                        
                                                <td><input class="dt_input" type="text" size="7" id="<?php echo 'det_total' . $ndt ?>" name="<?php echo 'det_total' . $ndt ?>" value="<?php echo str_replace(',', '', number_format($rst_det[det_total], 2)) ?>"  lang="<?php echo $ndt ?>" readonly style="text-align:right"/></td>
                                                <td>
                                                    <select id="<?php echo 'det_impuesto' . $ndt ?>" name="<?php echo 'det_impuesto' . $ndt ?>"   lang="<?php echo $ndt ?>" onchange="calculo(this)" <?php echo $disabled3 ?>>
                                                        <option value="14">14%</option>
                                                        <option value="0">0%</option>
                                                        <option value="NO">NO</option>
                                                        <option value="EX">EX</option>
                                                    </select>
                                                    <script>
                                                        idt = '<?php echo 'det_tipo' . $ndt ?>';
                                                        $('#' + idt).val('<?php echo $rst_det[det_tipo] ?>');
                                                        tp = '<?php echo 'det_tip' . $ndt ?>';
                                                        $('#' + tp).val('<?php echo $rts_producto[mp_a] ?>');
                                                        pr = '<?php echo 'det_prov' . $ndt ?>';
                                                        $('#' + pr).val('<?php echo $rts_producto[mp_b] ?>');
                                                        idt1 = '<?php echo 'det_impuesto' . $ndt ?>';
                                                        $('#' + idt1).val('<?php echo $rst_det[det_impuesto] ?>');</script>
                                                </td>
                                                <td  onclick="elimina_fila(this, 1)" align="center"><img id="del<?php echo $ndt ?>" <?php echo $visib ?> class="btn-dinamic" width="18px" src="../img/del_reg.png" /></td>
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
                                            <input type="submit" id="aumentar" value="+" <?php echo $visib2 ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Subtotal 14%:</td>
                                        <td><input type="text" size="10" id="reg_sbt12" value="<?php echo str_replace(',', '', number_format($rst[reg_sbt12], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Subtotal 0%:</td>
                                        <td><input type="text" size="10" id="reg_sbt0" value="<?php echo str_replace(',', '', number_format($rst[reg_sbt0], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Subtotal No objeto de Iva:</td>
                                        <td><input type="text" size="10" id="reg_sbt_noiva" value="<?php echo str_replace(',', '', number_format($rst[reg_sbt_noiva], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Subtotal Excento de Iva:</td>
                                        <td><input type="text" size="10" id="reg_sbt_excento" value="<?php echo str_replace(',', '', number_format($rst[reg_sbt_excento], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Subtotal:</td>
                                        <td><input type="text" size="10" id="reg_sbt" value="<?php echo str_replace(',', '', number_format($rst[reg_sbt], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Total Descuento:</td>
                                        <td><input type="text" size="10" id="reg_tdescuento" value="<?php echo str_replace(',', '', number_format($rst[reg_tdescuento], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Valor ICE:</td>
                                        <td><input type="text" size="10" id="reg_ice" value="<?php echo str_replace(',', '', number_format($rst[reg_ice], 2)) ?>"  style="text-align:right" onchange="calculo_totales()" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">IVA 14%:</td>
                                        <td><input type="text" size="10" id="reg_iva12" value="<?php echo str_replace(',', '', number_format($rst[reg_iva12], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Valor IRBPRN:</td>
                                        <td><input type="text" size="10" id="reg_irbpnr" value="<?php echo str_replace(',', '', number_format($rst[reg_irbpnr], 2)) ?>"  style="text-align:right" onchange="calculo_totales()" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Propina:</td>
                                        <td><input type="text" size="10" id="reg_propina" value="<?php echo str_replace(',', '', number_format($rst[reg_propina], 2)) ?>"  style="text-align:right" onchange="calculo_totales()" onkeyup=" this.value = this.value.replace(/[^0-9.]/, '')" <?php echo $read2 ?>/></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11"></td>
                                        <td colspan="2">Valor Total:</td>
                                        <td><input type="text" size="10" id="reg_total" value="<?php echo str_replace(',', '', number_format($rst[reg_total], 2)) ?>" readonly style="text-align:right"/></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" id='btnsms' style="font-size:12px;"><?php echo $sms ?></td>
                </tr>
                <tr>
                    <td colspan="6">
                        <?php
                        if ($x == 1) {
                            ?>
                            <button id="save" lang="<?php echo $id ?>" onclick="validar_detalle(<?php echo $id ?>)" >Guardar</button>
                            <button id="cancel" >Cancelar</button>
                            <?php
                        }
                        ?>
                    </td>
                </tr>

            </tfoot>
        </table>

    </body>
</html>
<script>
    $('#reg_tpcliente').val('<?php echo $rst[reg_tpcliente] ?>');
    $('#reg_tipo_documento').val('<?php echo $rst[reg_tipo_documento] ?>');
    $('#reg_sustento').val('<?php echo $rst[reg_sustento] ?>');
    $('#reg_pais_importe').val('<?php echo $rst[reg_pais_importe] ?>');
    $('#reg_tipo_pago').val('<?php echo $rst[reg_tipo_pago] ?>');
    $('#reg_forma_pago').val('<?php echo $rst[reg_forma_pago] ?>');
    var productos = [];
</script>
<?php
$cns = $Docs->lista_productos_insumosotros_matpri();
echo "<datalist id='lst_productos'>";
while ($rst_doc = pg_fetch_array($cns)) {
    echo "<option value='$rst_doc[tbl]$rst_doc[id]' >$rst_doc[cod] $rst_doc[dsc]</option>";
}
echo "</datalist>";
?>
<datalist id="cuentas">
    <?php
    $cns_ctas = $Docs->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
