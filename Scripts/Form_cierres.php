<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierres.php'; // Cambiar clase cierre caja
$Clase_cierre_caja = new Clase_cierres();
$id = $_GET[id];
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$bodega = $_GET[bodega];
$rst = pg_fetch_array($Clase_cierre_caja->lista_un_arqueo($id));
$cns = $Clase_cierre_caja->lista_tarjetas_de_credito($rst[arq_fecha_emision], $rst[arq_fecha_emision], $rst[arq_punto_emision]);
switch ($rst[arq_punto_emision]) {
    case '1':$local = 'BODEGA 1';
        break;
    case '2':$local = 'BODEGA 2';
        break;
    case '3':$local = 'BODEGA 3';
        break;
    case '4':$local = 'BODEGA 4';
        break;
    case '5':$local = 'BODEGA 5';
        break;
    case '6':$local = 'BODEGA 6';
        break;
    case '7':$local = 'BODEGA 7';
        break;
    case '8':$local = 'BODEGA 8';
        break;
    case '9':$local = 'BODEGA 9';
        break;
    case '10':$local = 'BODEGA 10';
        break;
    case '11':$local = 'BODEGA 11';
        break;
    case '12':$local = 'BODEGA 12';
        break;
}
$tc = 0;
$td = 0;
$tch = 0;
$te = 0;
$tct = 0;
$tb = 0;
$tr = 0;
$tnc = 0;
$cre = 0;
$ci = 0;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Arqueo de Caja</title>
        <script>
            var id_arq =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("id");
                    var i = a.substring(4, 5);
                    if ($('#tc_m_docho' + i).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                posicion_aux_window();
            });

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input,font").attr("name", function () {
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
                $('#tc_bancos' + x).focus();
            }

            function save(n) {
                fac_id = n.substr(1);
                var data = Array($('#pag_id' + n).val(),
                        $('#num_nota_credito' + n).val(),
                        $('#id_nota_credito' + n).val(),
                        $('#pago_forma' + n).val(),
                        $('#pago_banco' + n).val(),
                        $('#pago_tarjeta' + n).val(),
                        $('#pago_contado' + n).val(),
                        $('#pago_cantidad' + n).val(),
                        fac_id,
                        $('#fec_actual').val()
                        );
                var fields = Array('factura=' + $('#fac_num' + fac_id).html(),
                        'pago_forma=' + $('#pago_forma' + n).val(),
                        'num_nota_credito=' + $('#num_nota_credito' + n).val(),
                        'pago_banco=' + $('#pago_banco' + n).val(),
                        'pago_tarjeta=' + $('#pago_tarjeta' + n).val(),
                        'pago_contado=' + $('#pago_contado' + n).val(),
                        'pago_cantidad=' + $('#pago_cantidad' + n).val(),
                        ''
                        );

                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($('#pago_forma' + n).val() == 0) {
                            $('#pago_forma' + n).css({borderColor: "red"});
                            $('#pago_forma' + n).focus();
                            return false;
                        }
                        if ($('#pago_banco' + n).val() == 0 && $('#pago_banco' + n).attr('disabled') == false) {
                            $('#pago_banco' + n).css({borderColor: "red"});
                            $('#pago_banco' + n).focus();
                            return false;
                        }
                        if ($('#pago_tarjeta' + n).val() == 0 && $('#pago_tarjeta' + n).attr('disabled') == false) {
                            $('#pago_tarjeta' + n).css({borderColor: "red"});
                            $('#pago_tarjeta' + n).focus();
                            return false;
                        }
                        if ($('#pago_contado' + n).val() == 0 && $('#pago_contado' + n).attr('disabled') == false) {
                            $('#pago_contado' + n).css({borderColor: "red"});
                            $('#pago_contado' + n).focus();
                            return false;
                        }
                        if ($('#pago_cantidad' + n).val().length == 0 || parseFloat($('#pago_cantidad' + n).val()) == 0) {
                            $('#pago_cantidad' + n).css({borderColor: "red"});
                            $('#pago_cantidad' + n).focus();
                            return false;
                        }
                        j = n.substr(1);
                        sum = parseFloat($('#pago_cantidada' + j).val()) + parseFloat($('#pago_cantidadb' + j).val()) + parseFloat($('#pago_cantidadc' + j).val()) + parseFloat($('#pago_cantidadd' + j).val());
                        if (sum > parseFloat($('#val' + j).html())) {
                            alert('Valor ingresado incorrecto');
                            $('#pago_cantidad' + n).css({borderColor: "red"});
                            $('#pago_cantidad' + n).focus();
                            return false;
                        }

                    },
                    type: 'POST',
                    url: 'actions_cierres.php', //cambiar actions_productos
                    data: {op: 1, 'data[]': data, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('bottomFrame').src = '../Scripts/Form_cierres.php?id=' +<?php echo $id ?> + '&bodega=' + '<?php echo $bodega ?>' + '&desde=' + '<?php echo $desde ?>' + '&hasta=' + '<?php echo $hasta ?>';
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }


            function update() {
                var data = Array(arq_secuencial.value,
                        fec_actual.value,
                        arq_emisor.value,
                        fac_desde.value,
                        fac_hasta.value,
                        credito_modf.value,
                        debito_modf.value,
                        cheque_modf.value,
                        deposito.value,
                        efectivo_modf.value,
                        certi_modf.value,
                        bonos_modf.value,
                        rete_modf.value,
                        nc_modf.value,
                        cierre_modf.value,
                        observacion.value,
                        codigo_cta.value,
                        pln_id.value,
                        cre_modf.value
                        );
                var data2 = Array();
                j = $('.notcre').length;
                n = 0;
                while (n < j) {
                    n++;
                    data2.push($('#num_nc' + n).val() + ';' +
                            $('#valor_nc' + n).val() + ';' +
                            $('#num_fac' + n).val()
                            );

                }
                var fields = Array();
                $("#encabezado,#tarjetas,#cierre,#obsv").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                fields.push('');
                $.ajax({
                    beforeSend: function () {
                        if ($('#cierre_modf').val() != 0) {
                            if (parseFloat($("#total_cierre").val()) != parseFloat(cierre_modf.value)) {
                                alert('Total Cierres no coinciden');
                                $("#total_cierre").css({borderColor: "red"});
                                $("#total_cierre").focus();
                                $("#cierre_modf").css({borderColor: "red"});
                                $("#cierre_modf").focus();
                                return false;
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actions_cierres.php', //cambiar actions_productos
                    data: {op: 3, 'data[]': data, 'data2[]': data2, 'fields[]': fields, id: id_arq}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cierres.php?bodega=' + '<?php echo $bodega ?>' + '&desde=' + '<?php echo $desde ?>' + '&hasta=' + '<?php echo $hasta ?>';
            }


            function total_cierre() {
                tc = parseFloat($('#tarjeta_credito').val() * 1);
                td = parseFloat($('#tarjeta_debito').val() * 1);
                ch = parseFloat($('#cheque').val() * 1);
                ef = parseFloat($('#efectivo').val() * 1);
                ce = parseFloat($('#certificados').val() * 1);
                bo = parseFloat($('#bonos').val() * 1);
                re = parseFloat($('#retencion').val() * 1);
                nc = parseFloat($('#nota_credito').val() * 1);
                tot = tc + td + ch + ef + ce + bo + re + nc;
                $('#total_cierre').val(tot.toFixed(3));
            }

            function tab(e, op) {
                var ch0 = e.keyCode;
                if (ch0 == 9) {
                    e.preventDefault();
                    switch (op)
                    {
                        case 0:
                            $('#debito_modf').focus();
                            break;
                        case 1:
                            $('#cheque_modf').focus();
                            break;
                        case 2:
                            $('#deposito').focus();
                            break;
                        case 3:
                            $('#efectivo_modf').focus();
                            break;
                        case 4:
                            $('#certi_modf').focus();
                            break;
                        case 5:
                            $('#bonos_modf').focus();
                            break;
                        case 6:
                            $('#rete_modf').focus();
                            break;
                        case 7:
                            $('#nc_modf').focus();
                            break;
                        case 8:
                            $('#cre_modf').focus();
                            break;
                        case 9:
                            $('#cierre_modf').focus();
                            break;
                    }
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function habilitar(n) {
                $('#pago_forma' + n).attr('disabled', false);
                $('#num_nota_credito' + n).attr('disabled', false);
                $('#pago_cantidad' + n).attr('disabled', false);
                $('#pago_forma' + n).focus();
            }

            function habilitar1(obj) {
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
                    $('#num_nota_credito' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('0');
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '2') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('0');
                    $('#pago_contado' + s).val('0');
                    $('#pago_banco' + s).focus();
                } else if ($('#pago_forma' + s).val() == '3') {
                    $('#pago_banco' + s).attr('disabled', false);
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_banco' + s).focus();
                    $('#num_nota_credito' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('0');
                } else if ($('#pago_forma' + s).val() == '9') { 
                    alert('No se puede ralizar el cambio de pago a CREDITO');
                    $('#pago_forma' + s).val('0');
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#pago_contado' + s).focus();
                    $('#num_nota_credito' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('0');
                } else if ($('#pago_forma' + s).val() > '3' && $('#pago_forma' + s).val() < '8') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).focus();
                    $('#num_nota_credito' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).val('');
                    $('#id_nota_credito' + s).val('0');
                    $('#val_nt_cre' + s).val('0');
                } else if ($('#pago_forma' + s).val() == '8') {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', false);
                    $('#pago_cantidad' + s).focus();
                    $('#num_nota_credito' + s).attr('disabled', false);

                } else {
                    $('#pago_banco' + s).attr('disabled', true);
                    $('#pago_banco' + s).val('0');
                    $('#pago_tarjeta' + s).attr('disabled', true);
                    $('#pago_tarjeta' + s).val('0');
                    $('#pago_contado' + s).attr('disabled', true);
                    $('#pago_contado' + s).val('0');
                    $('#pago_cantidad' + s).attr('disabled', true);
                    $('#num_nota_credito' + s).attr('disabled', false);
                }
                busqueda_ntscre(obj)
            }
            function busqueda_ntscre(obj) {
                if (obj.lang != null) {
                    s = obj.lang;

                } else {
                    s = obj;
                }
                nc = obj.value;
                j = s.substr(1);
                ruc_cli = $('#ced' + j).html();
                if (nc == 8) {
                    $.post("actions_cierres.php", {op: 0, id: ruc_cli, s: 0, l: s, doc: nc},
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

            }

            function load_notas_credito(n, obj) {
                j = n.substr(1);
                id1 = $('#id_nota_creditoa' + j).val();
                id2 = $('#id_nota_creditob' + j).val();
                id3 = $('#id_nota_creditoc' + j).val();
                id4 = $('#id_nota_creditod' + j).val();
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
                $.post("actions_cierres.php", {op: 0, id: obj, s: 1},
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
                    }
                    if (dt == 1) {
                        $('#num_nota_credito' + n).val('');
                        $('#id_nota_credito' + n).val('0');
                        $('#val_nt_cre' + n).val('');
                        $('#pago_cantidad' + n).val(0);
                        $('#pago_cantidad' + n).attr('disabled', true);
                    }
                    $('#con_clientes').hide();
                })
                calculo_pago(n);
            }

            function load_codigo(obj) {
                n = obj.lang;
                $.post("actions_cierres.php", {op: 2, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id' + n).val(dat[0]);
                        $('#codigo_cta' + n).val(dat[1]);
                    } else {
                        alert('Cuenta no existe o esta inactiva');
                        $('#pln_id' + n).val('');
                        $('#codigo_cta' + n).val('');
                    }
                });
            }


            function calculo_pago(obj) {
                if (obj.lang != null) {
                    j = obj.lang.substr(1);
                } else {
                    j = obj.substr(1);
                }
                sum = parseFloat($('#pago_cantidada' + j).val()) + parseFloat($('#pago_cantidadb' + j).val()) + parseFloat($('#pago_cantidadc' + j).val()) + parseFloat($('#pago_cantidadd' + j).val());
                if (sum > parseFloat($('#val' + j).html())) {
                    alert('Valor ingresado incorrecto');
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
            
            function calculo() {
                tcre = $('#credito_modf').val();
                tdeb = $('#debito_modf').val();
                cheq = $('#cheque_modf').val();
                efec = $('#efectivo_modf').val();
                cert = $('#certi_modf').val();
                bono = $('#bonos_modf').val();
                rete = $('#rete_modf').val();
                ncre = $('#nc_modf').val();
                cred = $('#cre_modf').val();
                tot = tcre * 1 + tdeb * 1 + cheq * 1 + efec * 1 + cert * 1 + bono * 1 + rete * 1 + ncre * 1 + cred * 1;
                $('#cierre_modf').val(tot.toFixed(4));
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

        </script>
        <style>
            .fila-base{ display: none; } /* fila base oculta */
            .eliminar{ cursor: pointer; color: #000; }
            thead tr td{
                font-size: 11px;
                border:solid 0px #ccc;
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
            #usr{
                float:right; 
                margin-right:20px; 
                text-transform:uppercase;
                display:none; 
            }
            input {
                border:solid 0px #ccc;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" border='0'>
                <thead>
                    <tr>
                        <th colspan="8"><?php echo 'REPORTE CIERRE DE CAJA' ?>
                            <font class="cerrar"  id="cerrar" onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                            <font id="usr"><?php echo $_SESSION[usuario] ?></font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="8">
                        <table id='encabezado'>
                            <tr>
                                <td>NO</td>
                                <td><input type="text" id="arq_secuencial" value="<?php echo $rst[aqr_num_documento] ?>" readonly /></td>
                                <td>CIERRE DE CAJA LOCAL</td>
                                <td><input type="text" id="arq_local" value="<?php echo $local ?>" readonly /></td>
                                <td><input type="hidden" id="arq_emisor" value="<?php echo $rst[arq_punto_emision] ?>"></td>
                            </tr>
                            <tr>
                                <td>FECHA</td>
                                <td><input type="text" id="fec_actual" value="<?php echo $rst[arq_fecha_emision] ?>" readonly /></td>
                                <td>FACTURAS DESDE</td>
                                <td><input type="text" id="fac_desde" value="<?php echo $rst[aqr_fac_desde] ?>" readonly /></td>
                                <td>HASTA</td>
                                <td><input type="text" id="fac_hasta" value="<?php echo $rst[aqr_fac_hasta] ?>" readonly /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr id="lista_fac">
                    <td>
                        <table id="tbl">
                            <thead >
                                <tr>
                                    <th>No</th>
                                    <th>FACTURA</th>
                                    <th>CLIENTE</th>
                                    <th>RUC</th>
                                    <th>VALOR TOTAL $</th>
                                    <th>FORMA</th>
                                    <th>NUM DOC pAGO</th>
                                    <th>BANCO</th>
                                    <th>TARJETA</th>
                                    <th>PAGO</th>
                                    <th>VALOR</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <?php
                            $cns_fac = $Clase_cierre_caja->lista_factura($rst[arq_fecha_emision], $rst[arq_punto_emision]);
                            $n = 0;
                            $c = '"';
//                            $grup = '';
                            while ($fac = pg_fetch_array($cns_fac)) {
                                $n++;
                                $t_f+=$fac[fac_total_valor];

                                echo "<tr>
                                        <td>$n</td>
                                        <td id='fac_num$fac[fac_id]'>$fac[fac_numero]</td>
                                        <td>$fac[fac_nombre]</td>
                                        <td id='ced$fac[fac_id]'>$fac[fac_identificacion]</td>
                                        <td id='val$fac[fac_id]' align='right'>$fac[fac_total_valor]</td>
                                        <td>
                                        <select id='pago_formaa$fac[fac_id]' lang='a$fac[fac_id]' onblur='cambio_cmb(this)' onchange='habilitar1(this)' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>TARJETA DE CREDITO</option>
                                            <option value='2'>TARJETA DE DEBITO</option>
                                            <option value='3'>CHEQUE</option>
                                            <option value='4'>EFECTIVO</option>
                                            <option value='5'>CERTIFICADOS</option>
                                            <option value='6'>BONOS</option>
                                            <option value='7'>RETENCION</option>
                                            <option value='8'>NOTA CREDITO</option>
                                            <option value='9'>CREDITO</option>
                                        </select>
                                        </td>
                                        <td>
                                            <input type='text' id='num_nota_creditoa$fac[fac_id]' lang='a$fac[fac_id]' maxlength='17' disabled>
                                            <input type='hidden' size='6' id='id_nota_creditoa$fac[fac_id]' lang='a$fac[fac_id]''>
                                            <input type='hidden' size='6' id='val_nt_crea$fac[fac_id]' lang='a$fac[fac_id]''>
                                            <input type='hidden' size='6' id='pag_ida$fac[fac_id]' lang='a$fac[fac_id]''>
                                        </td>
                                        <td>
                                        <select id='pago_bancoa$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>Banco Pichincha</option>
                                            <option value='2'>Banco del Pacífico</option>
                                            <option value='3'>Banco de Guayaquil</option>
                                            <option value='4'>Produbanco</option>
                                            <option value='5'>Banco Bolivariano</option>
                                            <option value='6'>Banco Internacional</option>
                                            <option value='7'>Banco del Austro</option>
                                            <option value='8'>Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value='9'>Banco de Machala</option>
                                            <option value='10'>BGR</option>
                                            <option value='11'>Citibank (Ecuador)</option>
                                            <option value='12'>Banco ProCredit (Ecuador)</option>
                                            <option value='13'>UniBanco</option>
                                            <option value='14'>Banco Solidario</option>
                                            <option value='15'>Banco de Loja</option>
                                            <option value='16'>Banco Territorial</option>
                                            <option value='17'>Banco Coopnacional</option>
                                            <option value='18'>Banco Amazonas</option>
                                            <option value='19'>Banco Capital</option>
                                            <option value='20'>Banco D-MIRO</option>
                                            <option value='21'>Banco Finca</option>
                                            <option value='22'>Banco Comercial de Manabí</option>
                                            <option value='23'>Banco COFIEC</option>
                                            <option value='24'>Banco del Litoral</option>
                                            <option value='25'>Banco Delbank</option>
                                            <option value='26'>Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_tarjetaa$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>VISA</option>
                                            <option value='2'>MASTER CARD</option>
                                            <option value='3'>AMERICAN EXPRESS</option>
                                            <option value='4'>DINNERS</option>
                                            <option value='5'>DISCOVER</option>
                                            <option value='6'>CUOTAFACIL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_contadoa$fac[fac_id]' disabled>
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
                                        <option value='120'>120 dias</option>
                                        </select>
                                    </td>
                                    <td><input type='text' style='text-align:right' size='15' id='pago_cantidada$fac[fac_id]' value='0' onchange='calculo_pago(this)' lang='a$fac[fac_id]'' disabled/></td>
                                    <td><img src='../img/upd.png' class='auxBtn' width='12px' onclick='habilitar($c" . "a$fac[fac_id]$c)'>
                                        <img src='../img/save.png' class='auxBtn' width='12px' onclick='save($c" . "a$fac[fac_id]$c)'></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan='5'></td>
                                        <td><select id='pago_formab$fac[fac_id]' lang='b$fac[fac_id]' onblur='cambio_cmb(this)' onchange='habilitar1(this)' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>TARJETA DE CREDITO</option>
                                            <option value='2'>TARJETA DE DEBITO</option>
                                            <option value='3'>CHEQUE</option>
                                            <option value='4'>EFECTIVO</option>
                                            <option value='5'>CERTIFICADOS</option>
                                            <option value='6'>BONOS</option>
                                            <option value='7'>RETENCION</option>
                                            <option value='8'>NOTA CREDITO</option>
                                            <option value='9'>CREDITO</option>
                                        </select></td>
                                        <td>
                                            <input type='text' id='num_nota_creditob$fac[fac_id]' lang='b$fac[fac_id]' maxlength='17' disabled>
                                            <input type='hidden' size='6' id='id_nota_creditob$fac[fac_id]' lang='b$fac[fac_id]'>
                                            <input type='hidden' size='6' id='val_nt_creb$fac[fac_id]' lang='b$fac[fac_id]'>
                                            <input type='hidden' size='6' id='pag_idb$fac[fac_id]' lang='b$fac[fac_id]''>    
                                        </td>
                                        <td>
                                        <select id='pago_bancob$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>Banco Pichincha</option>
                                            <option value='2'>Banco del Pacífico</option>
                                            <option value='3'>Banco de Guayaquil</option>
                                            <option value='4'>Produbanco</option>
                                            <option value='5'>Banco Bolivariano</option>
                                            <option value='6'>Banco Internacional</option>
                                            <option value='7'>Banco del Austro</option>
                                            <option value='8'>Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value='9'>Banco de Machala</option>
                                            <option value='10'>BGR</option>
                                            <option value='11'>Citibank (Ecuador)</option>
                                            <option value='12'>Banco ProCredit (Ecuador)</option>
                                            <option value='13'>UniBanco</option>
                                            <option value='14'>Banco Solidario</option>
                                            <option value='15'>Banco de Loja</option>
                                            <option value='16'>Banco Territorial</option>
                                            <option value='17'>Banco Coopnacional</option>
                                            <option value='18'>Banco Amazonas</option>
                                            <option value='19'>Banco Capital</option>
                                            <option value='20'>Banco D-MIRO</option>
                                            <option value='21'>Banco Finca</option>
                                            <option value='22'>Banco Comercial de Manabí</option>
                                            <option value='23'>Banco COFIEC</option>
                                            <option value='24'>Banco del Litoral</option>
                                            <option value='25'>Banco Delbank</option>
                                            <option value='26'>Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_tarjetab$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>VISA</option>
                                            <option value='2'>MASTER CARD</option>
                                            <option value='3'>AMERICAN EXPRESS</option>
                                            <option value='4'>DINNERS</option>
                                            <option value='5'>DISCOVER</option>
                                            <option value='6'>CUOTAFACIL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_contadob$fac[fac_id]' disabled>
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
                                        <option value='120'>120 dias</option>
                                        </select>
                                    </td>
                                    <td><input type='text' style='text-align:right' size='15' id='pago_cantidadb$fac[fac_id]' value='0' onchange='calculo_pago(this)' lang='b$fac[fac_id]' disabled/></td>
                                    <td><img src='../img/upd.png' class='auxBtn' width='12px' onclick='habilitar($c" . "b$fac[fac_id]$c)'>
                                        <img src='../img/save.png' class='auxBtn' width='12px' onclick='save($c" . "b$fac[fac_id]$c)'></td>
                                </tr>
                                
                                <tr>
                                        <td colspan='5'></td>
                                        <td><select id='pago_formac$fac[fac_id]' lang='c$fac[fac_id]' onblur='cambio_cmb(this)' onchange='habilitar1(this)' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>TARJETA DE CREDITO</option>
                                            <option value='2'>TARJETA DE DEBITO</option>
                                            <option value='3'>CHEQUE</option>
                                            <option value='4'>EFECTIVO</option>
                                            <option value='5'>CERTIFICADOS</option>
                                            <option value='6'>BONOS</option>
                                            <option value='7'>RETENCION</option>
                                            <option value='8'>NOTA CREDITO</option>
                                            <option value='9'>CREDITO</option>
                                        </select></td>
                                        <td>
                                            <input type='text' id='num_nota_creditoc$fac[fac_id]' lang='c$fac[fac_id]' maxlength='17' disabled>
                                            <input type='hidden' size='6' id='id_nota_creditoc$fac[fac_id]' lang='c$fac[fac_id]'>
                                            <input type='hidden' size='6' id='val_nt_crec$fac[fac_id]' lang='c$fac[fac_id]'>
                                            <input type='hidden' size='6' id='pag_idc$fac[fac_id]' lang='c$fac[fac_id]'>
                                        </td>
                                        <td>
                                        <select id='pago_bancoc$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>Banco Pichincha</option>
                                            <option value='2'>Banco del Pacífico</option>
                                            <option value='3'>Banco de Guayaquil</option>
                                            <option value='4'>Produbanco</option>
                                            <option value='5'>Banco Bolivariano</option>
                                            <option value='6'>Banco Internacional</option>
                                            <option value='7'>Banco del Austro</option>
                                            <option value='8'>Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value='9'>Banco de Machala</option>
                                            <option value='10'>BGR</option>
                                            <option value='11'>Citibank (Ecuador)</option>
                                            <option value='12'>Banco ProCredit (Ecuador)</option>
                                            <option value='13'>UniBanco</option>
                                            <option value='14'>Banco Solidario</option>
                                            <option value='15'>Banco de Loja</option>
                                            <option value='16'>Banco Territorial</option>
                                            <option value='17'>Banco Coopnacional</option>
                                            <option value='18'>Banco Amazonas</option>
                                            <option value='19'>Banco Capital</option>
                                            <option value='20'>Banco D-MIRO</option>
                                            <option value='21'>Banco Finca</option>
                                            <option value='22'>Banco Comercial de Manabí</option>
                                            <option value='23'>Banco COFIEC</option>
                                            <option value='24'>Banco del Litoral</option>
                                            <option value='25'>Banco Delbank</option>
                                            <option value='26'>Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_tarjetac$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>VISA</option>
                                            <option value='2'>MASTER CARD</option>
                                            <option value='3'>AMERICAN EXPRESS</option>
                                            <option value='4'>DINNERS</option>
                                            <option value='5'>DISCOVER</option>
                                            <option value='6'>CUOTAFACIL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_contadoc$fac[fac_id]' disabled>
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
                                        <option value='120'>120 dias</option>
                                        </select>
                                    </td>
                                    <td><input type='text' style='text-align:right' size='15' id='pago_cantidadc$fac[fac_id]' value='0' onchange='calculo_pago(this)' lang='c$fac[fac_id]' disabled/></td>
                                    <td><img src='../img/upd.png' class='auxBtn' width='12px' onclick='habilitar($c" . "c$fac[fac_id]$c)'>
                                        <img src='../img/save.png' class='auxBtn' width='12px' onclick='save($c" . "c$fac[fac_id]$c)'></td>
                                </tr>
                                
                                <tr>
                                        <td colspan='5'></td>
                                        <td><select id='pago_formad$fac[fac_id]' lang='d$fac[fac_id]' onblur='cambio_cmb(this)' onchange='habilitar1(this)'  disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>TARJETA DE CREDITO</option>
                                            <option value='2'>TARJETA DE DEBITO</option>
                                            <option value='3'>CHEQUE</option>
                                            <option value='4'>EFECTIVO</option>
                                            <option value='5'>CERTIFICADOS</option>
                                            <option value='6'>BONOS</option>
                                            <option value='7'>RETENCION</option>
                                            <option value='8'>NOTA CREDITO</option>
                                            <option value='9'>CREDITO</option>
                                        </select></td>
                                        <td>
                                            <input type='text' id='num_nota_creditod$fac[fac_id]' lang='d$fac[fac_id]' disabled maxlength='17'>
                                            <input type='hidden' size='6' id='id_nota_creditod$fac[fac_id]' lang='d$fac[fac_id]'>
                                            <input type='hidden' size='6' id='val_nt_cred$fac[fac_id]' lang='d$fac[fac_id]'>
                                            <input type='hidden' size='6' id='pag_idd$fac[fac_id]' lang='d$fac[fac_id]'>
                                        </td>
                                        <td>
                                        <select id='pago_bancod$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>Banco Pichincha</option>
                                            <option value='2'>Banco del Pacífico</option>
                                            <option value='3'>Banco de Guayaquil</option>
                                            <option value='4'>Produbanco</option>
                                            <option value='5'>Banco Bolivariano</option>
                                            <option value='6'>Banco Internacional</option>
                                            <option value='7'>Banco del Austro</option>
                                            <option value='8'>Banco Promerica (Ecuador) - Antes: Banco MM Jaramillo Arteaga</option>
                                            <option value='9'>Banco de Machala</option>
                                            <option value='10'>BGR</option>
                                            <option value='11'>Citibank (Ecuador)</option>
                                            <option value='12'>Banco ProCredit (Ecuador)</option>
                                            <option value='13'>UniBanco</option>
                                            <option value='14'>Banco Solidario</option>
                                            <option value='15'>Banco de Loja</option>
                                            <option value='16'>Banco Territorial</option>
                                            <option value='17'>Banco Coopnacional</option>
                                            <option value='18'>Banco Amazonas</option>
                                            <option value='19'>Banco Capital</option>
                                            <option value='20'>Banco D-MIRO</option>
                                            <option value='21'>Banco Finca</option>
                                            <option value='22'>Banco Comercial de Manabí</option>
                                            <option value='23'>Banco COFIEC</option>
                                            <option value='24'>Banco del Litoral</option>
                                            <option value='25'>Banco Delbank</option>
                                            <option value='26'>Banco Sudamericano</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_tarjetad$fac[fac_id]' disabled>
                                            <option value='0'>SELECCIONE</option>
                                            <option value='1'>VISA</option>
                                            <option value='2'>MASTER CARD</option>
                                            <option value='3'>AMERICAN EXPRESS</option>
                                            <option value='4'>DINNERS</option>
                                            <option value='5'>DISCOVER</option>
                                            <option value='6'>CUOTAFACIL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id='pago_contadod$fac[fac_id]' disabled>
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
                                            <option value='120'>120 dias</option>
                                        </select>
                                    </td>
                                    <td><input type='text' style='text-align:right' size='15' id='pago_cantidadd$fac[fac_id]' value='0' onchange='calculo_pago(this)' lang='d$fac[fac_id]' disabled/></td>
                                    <td><img src='../img/upd.png' class='auxBtn' width='12px' onclick='habilitar($c" . "d$fac[fac_id]$c)'>
                                        <img src='../img/save.png' class='auxBtn' width='12px' onclick='save($c" . "d$fac[fac_id]$c)'></td>
                                    </tr>";
                                ?>
                                <script>
                                    l = 96;
                                </script>
                                <?php
                                $cns_pagos = $Clase_cierre_caja->lista_pagos_fac($fac[fac_id]);
                                while ($rts_combos = pg_fetch_array($cns_pagos)) {
                                    if ($rts_combos[pag_forma] == 8) {
                                        $rst_chq = pg_fetch_array($Clase_cierre_caja->lista_cheques_id($rts_combos[pag_id_chq]));
                                        $val = $rst_chq[chq_cobro] + $rts_combos[pag_cant];
                                    } else {
                                        $rst_chq = pg_fetch_array($Clase_cierre_caja->lista_cheques_pagid($rts_combos[pag_id]));
                                    }
                                    ?>
                                    <script>
                                        l++;
                                        id = '<?php echo $fac[fac_id] ?>';
                                        s = String.fromCharCode(l) + id;
                                        pag = '<?php echo $rts_combos[pag_id] ?>';
                                        forma = '<?php echo $rts_combos[pag_forma] ?>';
                                        tarjeta = '<?php echo $rts_combos[pag_tarjeta] ?>';
                                        banco = '<?php echo $rts_combos[pag_banco] ?>';
                                        cant = '<?php echo $rts_combos[pag_cant] ?>';
                                        cont = '<?php echo $rts_combos[pag_contado] ?>';
                                        nnc = '<?php echo $rts_combos[chq_numero] ?>';
                                        tch = '<?php echo $rst_chq[chq_tipo_doc] ?>';
                                        if (forma == 8) {
                                            idnc = '<?php echo $rst_chq[chq_id] ?>';
                                            vnc = '<?php echo $val ?>';
                                        } else {
                                            idnc = 0;
                                            vnc = 0;
                                        }
                                        $('#pag_id' + s).val(pag);
                                        $('#pago_forma' + s).val(forma);
                                        $('#num_nota_credito' + s).val(nnc);
                                        $('#id_nota_credito' + s).val(idnc);
                                        $('#val_nt_cre' + s).val(vnc);
                                        $('#pago_tarjeta' + s).val(tarjeta);
                                        $('#pago_banco' + s).val(banco);
                                        $('#pago_cantidad' + s).val(cant);
                                        $('#pago_contado' + s).val(cont);
                                    </script>  
                                    <?php
                                    switch ($rts_combos[pag_forma]) {
                                        case 1:
                                            $tc = $tc + $rts_combos[pag_cant];
                                            break;
                                        case 2:
                                            $td = $td + $rts_combos[pag_cant];
                                            break;
                                        case 3:
                                            $tch = $tch + $rts_combos[pag_cant];
                                            break;
                                        case 4:
                                            $te = $te + $rts_combos[pag_cant];
                                            break;
                                        case 5:
                                            $tct = $tct + $rts_combos[pag_cant];
                                            break;
                                        case 6:
                                            $tb = $tb + $rts_combos[pag_cant];
                                            break;
                                        case 7:
                                            $tr = $tr + $rts_combos[pag_cant];
                                            break;
                                        case 8:
                                            $tnc = $tnc + $rts_combos[pag_cant];
                                            break;
                                        case 9:
                                            $cre = $cre + $rts_combos[pag_cant];
                                            break;
                                    }
                                    $tci = $tc + $td + $tch + $te + $tct + $tb + $tr + $tnc + $cre;
                                }
                            }
                            echo "<tr>
                                        <td colspan='4'>Total</td>
                                        <td align='right'>$t_f</td>
                                        <td colspan='7'></td>
                                        </tr>";
                            ?>

                        </table>
                    </td>
                </tr>
                <tr id="head">
                    <td>
                        <table id='tarjetas'>
                            <thead id="tabla">
                                <tr>
                                    <th>No</th>
                                    <th>TARJETA DE CREDITO</th>
                                    <th>CORRIENTE</th>
                                    <th>3 MESES</th>
                                    <th>6 MESES</th>
                                    <th>9 MESES</th>
                                    <th>12 MESES</th>
                                    <th>18 MESES</th>
                                </tr>
                            </thead>
                            <?php
                            $n = 0;
                            $suma = 0;
                            $suma1 = 0;
                            $suma2 = 0;
                            $suma3 = 0;
                            $suma4 = 0;
                            $suma5 = 0;
                            while ($rst1 = pg_fetch_array($cns)) {
                                $n++;
                                $rst_pag = pg_fetch_array($Clase_cierre_caja->lista_totales_tarjetas($rst1[pag_banco], $rst1[pag_tarjeta], $rst[arq_fecha_emision], $rst[arq_fecha_emision], $rst[arq_punto_emision]));
                                echo "<tr>
                        <td><input type ='text' size='1'  class='itm' id='item1'  style='text-align:right' readonly value='$n' /></td>
                        <td>
                        <input type='text' size='18' id='tc_banco1' value='$rst1[banco]' lang='1' readonly />
                        <input type='text' size='20' id='tc_tarjeta1' value='$rst1[tarjeta]' lang='1' readonly />
                        </td>
                        <td><input type='text' size='15' id='tc_corriente1' style='text-align:right' value='$rst_pag[contado]'lang='1' readonly /></td>
                        <td><input type='text' size='15' id='tc_m_tres1' style='text-align:right' value='$rst_pag[tres_meses]' lang='1' readonly /></td>
                        <td><input type='text' size='15' id='tc_m_seis1' style='text-align:right' value='$rst_pag[seis_meses]' lang='1' readonly /></td>
                        <td><input type='text' size='15' id='tc_m_nueve1' style='text-align:right' value='$rst_pag[nueve_meses]' lang='1' readonly /></td>
                        <td><input type='text' size='15' id='tc_m_toce1' style='text-align:right' value='$rst_pag[doce_meses]' lang='1' readonly /></td>
                        <td><input type='text' size='15' id='tc_m_docho1' style='text-align:right' value='$rst_pag[docho_meses]' lang='1' readonly /></td>
                    </tr>";
                                $suma = $suma + $rst_pag[contado];
                                $suma1 = $suma1 + $rst_pag[tres_meses];
                                $suma2 = $suma2 + $rst_pag[seis_meses];
                                $suma3 = $suma3 + $rst_pag[nueve_meses];
                                $suma4 = $suma4 + $rst_pag[doce_meses];
                                $suma5 = $suma5 + $rst_pag[docho_meses];
                            }
                            ?>
                            <tfoot>
                                <tr class="totales">
                                    <td></td>
                                    <td align="right">Totales:</td>
                                    <td align="right" style="font-size:12px; " id="total"><?php echo number_format($suma, 4) ?></td>
                                    <td align="right" style="font-size:12px; " id="total1"><?php echo number_format($suma1, 4) ?></td>
                                    <td align="right" style="font-size:12px; " id="total2"><?php echo number_format($suma2, 4) ?></td>
                                    <td align="right" style="font-size:12px; " id="total3"><?php echo number_format($suma3, 4) ?></td>  
                                    <td align="right" style="font-size:12px; " id="total4"><?php echo number_format($suma4, 4) ?></td>
                                    <td align="right" style="font-size:12px; " id="total5"><?php echo number_format($suma5, 4) ?></td> 
                                <tr>
                                    <td id="espacio" hidden>&nbsp;</td>
                                </tr>    
                                <tr>
                                    <td colspan="8" style="border:1px solid;padding:0px 0px;" hidden id="linea"></td>
                                </tr>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <thead>
                    <tr><th colspan="8" >CIERRES DE CAJA</th></tr>
                </thead>
                <tr>
                    <td colspan="8">
                        <table id='cierre'>
                            <tr>
                                <td>TARJETA DE CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="tarjeta_credito" style="text-align:right" value="<?php echo $tc ?>" readonly />
                                    <input type="text" size="12"  id="credito_modf" style="text-align:right" value="<?php echo $rst[arq_tot_tcredito] ?>" onkeypress="tab(event, 0)" />
                                </td>
                            </tr>
                            <tr>
                                <td>TARJETA DE DEBITO</td>
                                <td>
                                    <input type="text" size="12"  id="tarjeta_debito" style="text-align:right" value="<?php echo $td ?>" readonly />
                                    <input type="text" size="12"  id="debito_modf" style="text-align:right" value="<?php echo $rst[arq_tot_tdebito] ?>" onkeypress="tab(event, 1)" />
                                </td>
                            </tr>
                            <tr>
                                <td>CHEQUE</td>
                                <td>
                                    <input type="text" size="12"  id="cheque" style="text-align:right" value="<?php echo $tch ?>" readonly />
                                    <input type="text" size="12"  id="cheque_modf" style="text-align:right" value="<?php echo $rst[arq_tot_cheque] ?>" onkeypress="tab(event, 2)" />
                                </td>
                                <td>DEPOSITO</td>
                                <td>
                                    <input type="text" size="30" id="deposito" style="text-align:right" value="<?php echo $rst[arq_deposito] ?>" onkeypress="tab(event, 3)" />
                                </td>
                            </tr>
                            <tr>
                                <td>EFECTIVO</td>
                                <td>
                                    <input type ="text" size="12" id="efectivo" style="text-align:right" value="<?php echo $te ?>" readonly />
                                    <input type="text" size="12"  id="efectivo_modf" style="text-align:right" value="<?php echo $rst[arq_tot_efectivo] ?>" onkeypress="tab(event, 4)" />
                                </td>
                                <td>CUENTA</td>
                                <td><input type="text" id="codigo_cta" size="30"  list="cuentas" onchange="load_codigo(this)"  value="<?php echo $rst[arq_cuenta] ?>"/>
                                    <input type="hidden" id="pln_id" size="10" value="<?php echo $rst[pln_id] ?>" </td>

                            </tr>
                            <tr>
                                <td>CERTIFICADOS</td>
                                <td>
                                    <input type="text" size="12" id="certificados" style="text-align:right" value="<?php echo $tct ?>" readonly />
                                    <input type="text" size="12"  id="certi_modf" style="text-align:right" value="<?php echo $rst[arq_tot_certificados] ?>" onkeypress="tab(event, 5)"/>
                                </td>
                            </tr>   
                            <tr>
                                <td>BONOS</td>
                                <td>
                                    <input type="text" size="12"  id="bonos" style="text-align:right" value="<?php echo $tb ?>" readonly />
                                    <input type="text" size="12"  id="bonos_modf" style="text-align:right" value="<?php echo $rst[arq_tot_bonos] ?>" onkeypress="tab(event, 6)" />
                                </td>
                            </tr>
                            <tr>
                                <td>RETENCION</td>
                                <td>
                                    <input type="text" size="12"  id="retencion" style="text-align:right" value="<?php echo $tr ?>" readonly />
                                    <input type="text" size="12"  id="rete_modf" style="text-align:right" value="<?php echo $rst[arq_tot_retencion] ?>" onkeypress="tab(event, 7)" />
                                </td>
                            </tr>
                            <tr>
                                <td>NOTA CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="nota_credito" style="text-align:right" value="<?php echo $tnc ?>" readonly />
                                    <input type="text" size="12"  id="nc_modf" style="text-align:right" value="<?php echo $rst[arq_tot_notcredito] ?>" onkeypress="tab(event, 8)" />
                                </td>
                            </tr>
                            <tr>
                                <td>CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="credito" style="text-align:right" value="<?php echo $cre ?>" readonly />
                                    <input type="text" size="12"  id="cre_modf" style="text-align:right" value="<?php echo $rst[arq_tot_credito] ?>" onkeypress="tab(event, 9)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">TOTAL CIERRE:</td>
                                <td>
                                    <input type="text" size="12"  id="total_cierre" style="text-align:right" readonly value="<?php echo $tci ?>" />
                                    <input type="text" size="12"  id="cierre_modf" style="text-align:right" value="<?php echo $rst[arq_tot_cierre] ?>"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td id="espacio1" hidden>&nbsp;</td>
                </tr>    
                <tr>
                    <td colspan="8" style="border:1px solid;padding:0px 0px;" hidden id="linea1"></td>
                </tr>
                <thead>
                    <tr><th colspan="8" >NOTAS DE CREDITO</th></tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <table border="0" id="tbl_nc">
                                        <thead>
                                            <tr>
                                                <td><br></td>
                                            </tr>
                                            <tr>
                                                <th>NOTA CREDITO No</th>
                                                <th>VALOR</th>
                                                <th>No FACTURA</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $cns_nc = $Clase_cierre_caja->lista_notas_credito($rst[arq_fecha_emision], $rst[arq_punto_emision]);
                                        $n = 0;
                                        while ($rst_nc = pg_fetch_array($cns_nc)) {
                                            $n++;
                                            echo "<tr>
                                <td><input class='notcre' type='text' id='num_nc$n' value='$rst_nc[ncr_numero]'/></td>
                                <td><input type='text' id='valor_nc$n' style='text-align:right' value='$rst_nc[nrc_total_valor]'/></td>
                                <td><input type='text' id='num_fac$n' value='$rst_nc[ncr_num_comp_modifica]'/></td>
                            </tr>";
                                        }
                                        ?>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>

                                <td>
                                    <table id='obsv'>
                                        <tr>
                                            <td>Observaciones:</td>
                                            <td valign="top" rowspan="7" ><textarea id="observacion" style="width:300%; text-transform: uppercase;"><?php echo $rst[arq_observaciones] ?></textarea></td>    
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" id="esp1" hidden>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" id="esp2" hidden>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" id="esp3" hidden>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" id="esp4" hidden>_______________________</td>
                </tr>
                <tr>
                    <td align="center" id="firma" hidden>FIRMA RESPONSABLE</td>
                </tr>
            </table>
        </form> 
        <button id="guardar" onclick="update()">Guardar</button>   
        <button id="cancelar" >Cancelar</button>  
    </body>
</html>    

<datalist id="cuentas">
    <?php
    $cns_ctas = $Clase_cierre_caja->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>