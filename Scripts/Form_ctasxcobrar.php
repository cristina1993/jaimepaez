<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxcobrar.php';
$CuentasCobrar = new CuentasCobrar();
$fec1 = $_GET[fec1];
$fec2 = $_GET[fec2];
$est = $_GET[estado];
$txt = $_GET[nm];

if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($CuentasCobrar->lista_documentos_id($id));
    $rst_cli = pg_fetch_array($CuentasCobrar->lista_cliente_ced($rst[fac_identificacion]));
    $rst_ctas = pg_fetch_array($CuentasCobrar->listar_una_cta_comid($id));
    $cns_pag = $CuentasCobrar->lista_pagos($rst[fac_id]);
    $rst_cue = pg_fetch_array($CuentasCobrar->listar_una_cuenta_id($rst_ctas[pln_id]));
    if ($rst_ctas[asiento] != '') {
        $x = 1;
    } else {
        $x = 0;
    }
} else {
    $id = 0;
    $rst['reg_fecha'] = date('Y-m-d');
    $rst['mov_cantidad1'] = 0;
    $fila = 0;
    $rst_ctas[asiento] = 0;
    $x = '0';
}
$rst_cred = pg_fetch_array($CuentasCobrar->suma_credito($rst[fac_identificacion]));
$valor_credito = ($rst_cred[sum1] + $rst_cred[sum4]) - ($rst_cred[sum2] + $rst_cred[sum3]);
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            fec1 = '<?php echo $fec1 ?>';
            fec2 = '<?php echo $fec2 ?>';
            est = '<?php echo $est ?>';
            txt = '<?php echo $txt ?>';
            dec = '<?php echo $dec?>';
            dc = '<?php echo $dc?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                o(1);
                total();
                bloquear();
                posicion_aux_window();

            });

            function save(id, x) {
                f = fecha.value;
                fp = '<?php echo date('Y-m-d') ?>';
                fac = '<?php echo $rst[fac_numero] ?>';
                saldo = $('#tsaldo').html();

                if (x == 0) {
                    m = cta_monto.value;
                    forma = cta_forma_pago.value;
                    banco = cta_banco.value;
                    doc = 0;
                    reg = 0;
                    concepto = cta_concepto.value;
                } else {
                    m = monto.value;
                    forma = 'CRUCE DE CUENTAS';
                    banco = '';
                    doc = factura.value;
                    reg = $('#facid').val();
                    concepto = forma;
                }
                var data = Array(
                        id,
                        f,
                        m,
                        forma,
                        banco,
                        pln_codigo.value,
                        fp,
                        pag_id.value,
                        doc,
                        reg,
                        fac,
                        concepto,
                        asiento.value,
                        chq_id.value

                        );
                var fields = Array();
                fields.push('documento=' + $('#doc').html());
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (pln_codigo.value.length == 0) {
                            $("#pln_codigo").css({borderColor: "red"});
                            $("#pln_codigo").focus();
                            return false;
                        }
                        else if (asiento.value.length == 0) {
                            $("#asiento").css({borderColor: "red"});
                            $("#asiento").focus();
                            return false;
                        }

                        if (x == 0) {
                            if (cta_monto.value.length == 0) {
                                $("#cta_monto").css({borderColor: "red"});
                                $("#cta_monto").focus();
                                return false;
                            }
                            else if (cta_concepto.value.length == 0) {
                                $("#cta_concepto").css({borderColor: "red"});
                                $("#cta_concepto").focus();
                                return false;
                            }
                            else if (cta_forma_pago.value.length == 0) {
                                $("#cta_forma_pago").css({borderColor: "red"});
                                $("#cta_forma_pago").focus();
                                return false;
                            }
                            else if (cta_banco.value.length == 0) {
                                $("#cta_banco").css({borderColor: "red"});
                                $("#cta_banco").focus();
                                return false;
                            }
                            if (cta_forma_pago.value != 'NOTA DE DEBITO') {
                                if (parseFloat(saldo) < parseFloat(m)) {
                                    alert('NO SE PUEDE REGISTRAR EL PAGO PORQUE EL MONTO \nES MAYOR QUE EL VALOR DEL SALDO');
                                    return false;
                                }
                            }

                        } else {
                            if (factura.value.length == 0) {
                                $("#factura").css({borderColor: "red"});
                                $("#factura").focus();
                                return false;
                            }
                            else if (monto.value.length == 0) {
                                $("#monto").css({borderColor: "red"});
                                $("#monto").focus();
                                return false;
                            }
                            else if (parseFloat(saldo) < parseFloat(m) || parseFloat(montofac.value) < parseFloat(m)) {
                                alert('NO SE PUEDE REALIZAR EL CRUCE PORQUE EL MONTO \nES MAYOR QUE EL VALOR DE LA FACTURA O EL SALDO');
                                return false;
                            }
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_ctasxcobrar.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields, x: x}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('bottomFrame').src = '../Scripts/Form_ctasxcobrar.php?id=' + id + '&fec1=' + fec1 + '&fec2=' + fec2 + '&estado=' + est + '&nm=' + txt;
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cuentasxcobrar.php?desde=' + fec1 + '&hasta=' + fec2 + '&estado=' + est + '&txt=' + txt;
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            function total() {
                i = $('.debito').length;
                n = 0;
                td = 0;
                tc = 0;
                ts = 0;
                tsv = 0;
                tvn = 0;
                while (n < i) {
                    n++;
                    deb = $('#debito' + n).html().replace(',', '');
                    td = td + parseFloat(deb);
                    cre = $('#credito' + n).html().replace(',', '');
                    tc = tc + parseFloat(cre);
                    ts = td - tc;
                    ven = $('#vencido' + n).html().replace(',', '');
                    tsv = tsv + parseFloat(ven);
                }
                $('#tdeb').html(td.toFixed(dec));
                $('#tcre').html(tc.toFixed(dec));
                $('#tsaldo').html(ts.toFixed(dec));
                $('#tsv').html(tsv.toFixed(dec));

            }

            function o(op) {
                if (op == 1) {
                    $('.ocultar').hide();
                    $('#factura').val('');
                    $('#montofac').val('');
                    $('#monto').val('');
                    $('#cruce').attr('checked', false);
                } else {
                    $('.ocultar').show();
                }
            }

            function buscar_fac(obj) {
                data = $('#codigo').val();
                $.post("actions_ctasxcobrar.php", {op: 2, id: obj.value, data: data},
                function (dt) {
                    dat = dt.split('&')
                    if (dat[0] != '') {
                        $('#montofac').val(dat[0]);
                        $('#facid').val(dat[1]);
                    } else {
                        alert('NO EXISTE FACTURA');
                    }
                });
            }

            function load_asientos(obj) {
                $.post("actions_ctasxcobrar.php", {op: 3, id: obj.value},
                function (dt) {
                    $('#pln_descripcion').val(dt.substr(0, 30));
                });
            }

            function cargar_combo(obj) {
                $(obj).autocomplete({
                    minLength: 0,
                    source: cuentas,
                    focus: function (event, ui) {
                        $(obj).val(ui.item.label);
                        return false;
                    }, select: function (event, ui) {
                        $(obj).val(ui.item.value);
                        return false;
                    }
                }).data("autocomplete")._renderItem = function (ul, item) {
                    return $("<li></li>")
                            .data("item.autocomplete", item)
                            .append("<a>" + item.label + "</a>")
                            .appendTo(ul);
                };
            }

            function cargar_combo2(obj) {
                $(obj).autocomplete({
                    minLength: 0,
                    source: cuentas1,
                    focus: function (event, ui) {
                        $(obj).val(ui.item.label);
                        return false;
                    }, select: function (event, ui) {
                        $(obj).val(ui.item.value);
                        return false;
                    }
                }).data("autocomplete")._renderItem = function (ul, item) {
                    return $("<li></li>")
                            .data("item.autocomplete", item)
                            .append("<a>" + item.label + "</a>")
                            .appendTo(ul);
                };
            }

            function bloquear() {
                x =<?php echo $x ?>;
                if (x == 0) {
                    $('#pln_codigo').attr('readonly', false);
                    $('#asiento').attr('disabled', false);
                } else {
                    $('#pln_codigo').attr('readonly', true);
                    $('#asiento').attr('disabled', true);
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function comparar() {
                if ($('#cta_forma_pago').val() != 'EFECTIVO' && $('#cta_forma_pago').val() != 'TRANSFERENCIA' && $('#cta_forma_pago').val() != '') {
                    if (parseFloat(chq_monto.value) < parseFloat(cta_monto.value)) {
                        alert('El monto pago supera el monto del documento: ' + chq_monto.value);
                        $("#cta_monto").css({borderColor: "red"});
                        $("#cta_monto").focus();
                    }
                }
            }

            function cargar_datos(obj) {
                if (obj.value != 'EFECTIVO' && obj.value != 'TRANSFERENCIA' && obj.value != '') {
                    ced = '<?php echo $rst[fac_identificacion] ?>';
                    $.post("actions_ctasxcobrar.php", {op: 4, id: obj.value, data: ced, s: 0},
                    function (dt) {
                        if (dt != '') {
                            $('#tbl_aux').css('visibility', 'visible');
                            $('#tbl_aux').show();
                            $('#txb').html(dt);
                        } else {
                            alert('No tiene credito con ' + obj.value);
                            $('#cta_forma_pago').val('0');
                        }
                    });
                    tbl_aux.style.top = e.clientY;
                    tbl_aux.style.left = (e.clientX - 600);
                    tbl_aux.style.display = 'block';
                } else {
                    $('#chq_id').val('0');
                    $('#chq_numero').val('');
                    $('#chq_monto').val('');
                }
            }

            function load_docs(obj) {
                $.post("actions_ctasxcobrar.php", {op: 4, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('No tiene ingresado un documento');
                        $('#cta_forma_pago').focus();
                    } else {
                        dat = dt.split('&');
                        $('#chq_id').val(dat[0]);
                        $('#chq_numero').val(dat[1]);
                        $('#cta_monto').val(dat[2]);
                        $('#chq_monto').val(dat[2]);
                    }
                    $('#tbl_aux').hide();
                });
            }
            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#tbl_aux");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 80) / 2);
                obj.css('left', (wndW - 200) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }



        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            .totales{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 16px;
            }

            .debe_haber{
                font-size: 10px;
                font-weight:normal;
                text-transform:capitalize; 
            }
            .ui-autocomplete {
                max-height: 200px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
             */
            * html .ui-autocomplete {
                height: 200px;
            }

            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_aux" border='1'>
            <tr><td></td><td></td><td colspan='3' style='font-weight:bolder'><img src='../img/b_delete.png' style='float:right;cursor: pointer' onclick='tbl_aux.style.display = "none"'/></td></tr>
            <tbody id='txb'></tbody>
        </table>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO CUENTAS POR COBRAR<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>CODIGO CLIENTE:</td>
                                <td><input type="text" size="40"  id="codigo" value="<?php echo $rst_cli[cli_codigo] ?>" onblur="this.value = this.value.toUpperCase()" readonly/></td>
                                <td>CRUZAR CUENTA:</td>
                                <td><input type="checkbox"  id="cruce" onclick="o(0)"/>
                                    CREDITO:
                                    <input type="text" size="15"  id="credito" readonly value="<?php echo number_format($valor_credito, 2) ?>" /></td>

                            </tr>
                            <tr>
                                <td>CLIENTE:</td>
                                <td><input type="text" size="40"  id="cliente" value="<?php echo $rst[fac_nombre] ?>" onblur="this.value = this.value.toUpperCase()" readonly/></td>
                                <td class="ocultar">FACTURA</td>
                                <td class="ocultar"><input type="text" size="30"  id="factura" onblur="buscar_fac(this)"/></td>

                            </tr>
                            <tr>
                                <td>CODIGO CONTABLE:</td>
                                <td><input type="text" size="40"  id="pln_codigo" value="<?php echo $rst_cue[pln_codigo]; ?>" onfocus="cargar_combo(this)" onblur="load_asientos(this)"/></td>
                                <td class="ocultar">Valor Factura</td>
                                <td class="ocultar"><input type="text" size="26"  id="montofac" readonly/>
                                    <input type="text" id="facid"  hidden/></td>

                            </tr>
                            <tr>
                                <td>CUENTA:</td>
                                <td><input type="text" size="40"  id="pln_descripcion" value="<?php echo $rst_cue[pln_descripcion] ?>" onblur="this.value = this.value.toUpperCase()" readonly/></td>
                                <td class="ocultar">MONTO</td>
                                <td class="ocultar"><input type="text" size="30"  id="monto" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>

                            </tr>
                            <tr>
                                <td>TIPO ASIENTO:</td>
                                <td><select id="asiento">
                                        <option value="0">FC.PENSION PRIMARIA</option>
                                        <option value="1">FP.FACTURAS POS</option>
                                        <option value="2">CA.CANCELACION FACTURAS</option>
                                        <option value="3">AB.ABONO</option>
                                        <option value="4">RC.RECIBO</option>
                                        <option value="5">ND.NOTA DE DEBITO</option>
                                        <option value="6">NC.NOTA DE CREDITO</option>
                                        <option value="7">DD.AJUSTE DIF. CAMBIO DB</option>
                                        <option value="8">DC.AJUSTE DIF. CAMBIO CR</option>
                                        <option value="9">RF.RETENCION FUENTE</option>
                                    </select>
                                </td>
                                <td class="ocultar"></td>
                                <td class="ocultar"><button id="aceptar" onclick="save(<?php echo $id ?>, 1)">ACEPTAR</button> <button id="ocultar" onclick="o(1)">CANCELAR</button>  </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="tbl"> 
                            <thead>
                                <tr>
                                    <th>FECHA</th>
                                    <th>DOCUMENTO</th>
                                    <th>CONCEPTO</th>
                                    <th>FORMA DE PAGO</th>
                                    <th>DEBITO</th>
                                    <th>CREDITO</th>
                                    <th>SALDO</th>
                                    <th>SALDO VENCIDO</th>
                                </tr>
                            </thead>
                            <?php
                            $n = 0;
                            $debito = 0;
                            $credito = 0;
                            $saldo = 0;
                            $vencido = 0;
                            $d = 0;
                            $rst_pag1 = pg_fetch_array($CuentasCobrar->lista_ultimo_pago($rst[fac_id]));
                            $fvencimiento = $rst_pag1[pag_fecha_v];
                            while ($rst_pag = pg_fetch_array($cns_pag)) {
                                $n++;
                                $pag_fecha_v = $rst_pag[pag_fecha_v];
                                $concepto = 'FACTURACION EN VENTAS';
                                $debito = $rst_pag[pag_cant];
                                $saldo = $saldo + $debito - $credito;
                                $res = pg_fetch_array($CuentasCobrar->suma_pagos1($rst[fac_id]));
                                $mto = $res[monto]; // suma pagos cta
                                $pgo = $res[pago] + $res[debito]; //suma pagos
                                if ($fvencimiento < date('Y-m-d')) {
                                    if ($n == '1') {
                                        $vencido = $pgo - $mto;
                                    } else {
                                        $vencido = 0;
                                    }
                                } else {
                                    $vencido = 0;
                                }
                                $rp = pg_fetch_array($CuentasCobrar->buscar_un_pago_fac($rst_pag[com_id]));
                                $pag_id = $rp[pag_id];
                                $pag_fecha_v = $rp[pag_fecha_v];
                                if ($pag_id == '') {
                                    $pag_id = $rst_pag[pag_id];
                                    $pag_fecha_v = $rst_pag[pag_fecha_v];
                                }
                                ?>
                                <tr style="height: 22px">
                                    <td style="font-size: 12px;"><?php echo $rst_pag[pag_fecha_v] ?></td>
                                    <td id="doc" style="font-size: 12px;"><?php echo $rst[fac_numero] ?></td>
                                    <td style="font-size: 12px;"><?php echo $concepto ?></td>
                                    <td style="font-size: 12px;"><?php echo $pago ?></td>
                                    <td style="font-size: 12px;" align="right" id="debito<?php echo $n ?>" class="debito"><?php echo str_replace(',', '', number_format($debito, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="credito<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($credito, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="saldo<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($saldo, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="vencido<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($vencido, $dec)) ?></td>
                                </tr>
                                <?php
                            }
                            $debito1 = 0;
                            $credito1 = 0;
                            $saldo1 = 0;
                            $vencido1 = 0;
                            $cnscon = $CuentasCobrar->listar_una_cta_comid($rst[fac_id]);
                            while ($rst_cta = pg_fetch_array($cnscon)) {
                                $j++;
                                $n++;
                                if ($j == 1) {
                                    $saldo1 = $saldo;
                                }
                                $debito1 = '0';
                                $credito1 = $rst_cta[cta_monto];
                                if ($rst_cta[cta_forma_pago] == 'NOTA DE DEBITO') {
                                    $debito1 = $rst_cta[cta_monto];
                                    $credito1 = 0;
                                }
                                $saldo1 = $saldo1 + $debito1 - $credito1;
                                ?>
                                <tr style="height: 22px">
                                    <td style="font-size: 12px;"><?php echo $rst_cta[cta_fecha_pago] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst[fac_numero] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst_cta[cta_concepto] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst_cta[cta_forma_pago] ?></td>
                                    <td style="font-size: 12px;" align="right" id="debito<?php echo $n ?>" class="debito"><?php echo str_replace(',', '', number_format($debito1, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="credito<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($credito1, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="saldo<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($saldo1, $dec)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="vencido<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($vencido1, $dec)) ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tfoot>
                                <tr class="totales" style="height: 22px">
                                    <td colspan="4" align="right">Total:</td>
                                    <td style="font-size: 12px;" align="right"  id="tdeb"></td>    
                                    <td style="font-size: 12px;" align="right"  id="tcre"></td>    
                                    <td style="font-size: 12px;" align="right"  id="tsaldo"></td>    
                                    <td style="font-size: 12px;" align="right"  id="tsv"></td>    
                                </tr>
                            </tfoot>
                        </table> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <table> 
                            <thead>
                                <tr>
                                    <th colspan="5">REGISTRAR PAGO</th> 
                                </tr>
                                <tr>
                                    <th>MONTO</th>
                                    <th>CONCEPTO</th>
                                    <th>FORMA DE PAGO</th>
                                    <th># DOCUMENTO</th>
                                    <th>CUENTA</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>
                                    <input type="text" size="20"  id="cta_monto" value="<?php echo $rst[cta_monto] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" onblur="comparar()"/>
                                    <input type="text" id="pag_id" value="<?php echo $pag_id ?>" hidden/>
                                    <input type="text" id="fecha" value="<?php echo $pag_fecha_v ?>" hidden/>
                                </td>
                                <td><input type="text" size="35"  id="cta_concepto" value="<?php echo $rst[cta_concepto] ?>" onblur="this.value = this.value.toUpperCase()" /></td>

                                <td><select id="cta_forma_pago" onchange="cargar_datos(this)">
                                        <option value="">SELECCIONE</option>
                                        <option value="CHEQUE">CHEQUE</option>
                                        <option value="EFECTIVO">EFECTIVO</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                        <option value="RETENCION">RETENCION</option>
                                        <option value="NOTA DE CREDITO">NOTA DE CREDITO</option>
                                        <option value="NOTA DE DEBITO">NOTA DE DEBITO</option>
                                    </select>
                                </td>
                                <td><input type="text" size="15"  id="chq_numero" readonly/>
                                    <input type="hidden" size="15"  id="chq_id" readonly/>
                                    <input type="hidden" size="15"  id="chq_monto" readonly/></td>
                                <td><input type="text" size="20"  id="cta_banco" value="<?php echo $rst[cta_banco] ?>"  onfocus="cargar_combo2(this)" /></td>
                            </tr>
                        </table> 
                    </td>   
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>  

<script>
    var cuentas = [];
</script>
<?php
$cns_can = $CuentasCobrar->lista_asientos_contables();
while ($rst_can = pg_fetch_array($cns_can)) {
    ?>
    <script>
        val = '<?php echo $rst_can[pln_codigo] ?>';
        lbl = '<?php echo $rst_can[pln_codigo] . ' ' . $rst_can[pln_descripcion] ?>';
        cuentas.push({value: val, label: lbl});
    </script>
    <?php
}
?>

<script>
    var cuentas1 = [];
</script>
<?php
$cns_cue = $CuentasCobrar->lista_cuentas_bancos();
while ($rst_cue = pg_fetch_array($cns_cue)) {
    ?>
    <script>
        val = '<?php echo $rst_cue[pln_codigo] ?>';
        lbl = '<?php echo $rst_cue[pln_codigo] . ' ' . $rst_cue[pln_descripcion] ?>';
        cuentas1.push({value: val, label: lbl});
    </script>
    <?php
}
?>
<script>
    var as =<?php echo $rst_ctas[asiento] ?>;
    $('#asiento').val(as);
</script>