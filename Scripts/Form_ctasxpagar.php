<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$CuentasPagar = new CuentasPagar();
$fec1 = $_GET[fec1];
$fec2 = $_GET[fec2];
$est = $_GET[estado];
$txt = $_GET[nm];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($CuentasPagar->lista_documentos_id($id));
    $rst_cli = pg_fetch_array($CuentasPagar->lista_cliente_ced($rst[reg_ruc_cliente]));
    $rst_ctas = pg_fetch_array($CuentasPagar->listar_una_ctapagar_comid($id));
    $cns_pag = $CuentasPagar->lista_pagos_regfac($id);
    $rst_cue = pg_fetch_array($CuentasPagar->listar_una_cuenta_id($rst_ctas[pln_id]));
    if ($rst_ctas[asiento] != '') {
        $x = 1;
    } else {
        $x = 0;
    }
    $cns_ctas = $CuentasPagar->listar_una_ctapagar_comid($id);
    $rst[ctp_fecha_pago] = date('Y-m-d');
} else {
    $id = 0;
    $rst[ctp_fecha_pago] = date('Y-m-d');
    $rst['mov_cantidad1'] = 0;
    $fila = 0;
    $x = 0;
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
            fec1 = '<?php echo $fec1 ?>';
            fec2 = '<?php echo $fec2 ?>';
            est = '<?php echo $est ?>';
            txt = '<?php echo $txt ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                total();
                o(1);
                bloquear();
                Calendar.setup({inputField: "ctp_fecha_pago", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
            });
            function save(id, x) {
                f = fecha.value;
                fp = ctp_fecha_pago.value;
                fac = '<?php echo $rst[reg_num_documento] ?>';
                saldo = $('#tsaldo').html();
                cli = '<?php echo $rst_cli[cli_id] ?>';
                if (x == 0) {
                    m = ctp_monto.value;
                    forma = ctp_forma_pago.value;
                    banco = ctp_banco.value;
                    doc = num_documento.value;
                    com_id = 0;
                    concepto = ctp_concepto.value;
                } else {
                    m = monto.value;
                    forma = 'CRUCE DE CUENTAS';
                    banco = '';
                    doc = factura.value;
                    com_id = $('#facid').val();
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
                        fac,
                        com_id,
                        concepto,
                        asiento.value,
                        cli
                        );
                var fields = Array();
                fields.push('documento='+$('#doc').html());
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
                            if (ctp_monto.value.length == 0) {
                                $("#ctp_monto").css({borderColor: "red"});
                                $("#ctp_monto").focus();
                                return false;
                            }
                            else if (ctp_concepto.value.length == 0) {
                                $("#ctp_concepto").css({borderColor: "red"});
                                $("#ctp_concepto").focus();
                                return false;
                            }
                            else if (ctp_forma_pago.value.length == 0) {
                                $("#ctp_forma_pago").css({borderColor: "red"});
                                $("#ctp_forma_pago").focus();
                                return false;
                            }
                            else if (ctp_banco.value.length == 0) {
                                $("#ctp_banco").css({borderColor: "red"});
                                $("#ctp_banco").focus();
                                return false;
                            }
                            else if (parseFloat(saldo) < parseFloat(m)) {
                                alert('NO SE PUEDE REGISTRAR EL PAGO PORQUE \nEL MONTO ES MAYOR QUE EL VALOR DEL SALDO');
                                return false;
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
                            else if (parseFloat(saldo) < parseFloat(m) || parseFloat(facmonto.value) < parseFloat(m)) {
                                alert('NO SE PUEDE REALIZAR EL CRUCE PORQUE EL MONTO \nES MAYOR QUE EL VALOR DE LA FACTURA O EL SALDO');
                                return false;
                            }
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_ctasxpagar.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields, x: x}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('bottomFrame').src = '../Scripts/Form_ctasxpagar.php?id=' + id + '&fec1=' + fec1 + '&fec2=' + fec2 + '&estado=' + est + '&nm=' + txt;
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cuentasxpagar.php?desde=' + fec1 + '&hasta=' + fec2 + '&estado=' + est + '&txt=' + txt;
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
                    sv = $('#vencido' + n).html().replace(',', '');
                    tsv = tsv + parseFloat(sv);

                }
                $('#tdeb').html(td.toFixed(4));
                $('#tcre').html(tc.toFixed(4));
                $('#tsaldo').html(ts.toFixed(4));
                $('#tsv').html(tsv.toFixed(4));
            }
            function load_asientos(obj) {
                $.post("actions_ctasxpagar.php", {op: 3, id: obj.value},
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

            function o(op) {
                if (op == 1) {
                    $('.ocultar').hide();
                    $('#factura').val('');
                    $('#monto').val('');
                    $('#facmonto').val('');
                    $('#cruce').attr('checked', false);
                } else {
                    $('.ocultar').show();
                }
            }

            function buscar_fac(obj) {
                data = $('#codigo').val();
                $.post("actions_ctasxpagar.php", {op: 2, id: obj.value, data: data},
                function (dt) {
                    dat = dt.split('&')
                    if (dat[0] != '') {
                        $('#facmonto').val(dat[0]);
                        $('#facid').val(dat[1]);
                    } else {
                        alert('NO EXISTE FACTURA');
                    }
                });
            }

            function aceptar(id, x) {
                save(id, x);
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
            function auxWindow(a, id, r)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/frm_pdf_cheque.php?id=' + id + '&nombre=' + $('#cliente').val() + '&reg=' + r;
                        parent.document.getElementById('contenedor2').rows = "*,70%";
                        look_menu();
                        break;
                }
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
            #tbl_prioridades {
                font-size:12px; 
                width: 150px;
                position:fixed;
                background:white;
                border: solid 1px;
            }
            #tbl_prioridades tr:hover{
                background:gainsboro;
                cursor:pointer; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO CUENTAS POR PAGAR<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>CODIGO CLIENTE:</td>
                                <td><input type="text" size="40"  id="codigo" value="<?php echo $rst_cli[cli_codigo] ?>" onblur="this.value = this.value.toUpperCase()" readonly/></td>
                                <td>CRUZAR CUENTA:</td>
                                <td><input type="checkbox"  id="cruce" onclick="o(0)"/></td>
                            </tr>
                            <tr>
                                <td>CLIENTE:</td>
                                <td><input type="text" size="40"  id="cliente" value="<?php echo $rst_cli[cli_raz_social] ?>" onblur="this.value = this.value.toUpperCase()" readonly/></td>
                                <td class="ocultar">FACTURA</td>
                                <td class="ocultar"><input type="text" size="30" maxlength="17"  id="factura" onblur="buscar_fac(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/></td>
                            </tr>
                            <tr>
                                <td>CODIGO CONTABLE:</td>
                                <td><input type="text" size="47"  id="pln_codigo" value="<?php echo $rst_cue[pln_codigo]; ?>" onfocus="cargar_combo(this)" onblur="load_asientos(this)"/></td>
                                <td class="ocultar">VALOR FACTURA</td>
                                <td class="ocultar"><input type="text" size="26"  id="facmonto" readonly/>
                                    <input type="text" id="facid" hidden/></td>
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
                                    <th>F.PAGO</th>
                                    <th>DEBITO</th>
                                    <th>CREDITO</th>
                                    <th>SALDO</th>
                                    <th>SALDO VENCIDO</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <?php
                            $n = 0;
                            $de = 0;
                            $s = 0;
                            $v = 0;
                            $cns_pag1 = $CuentasPagar->lista_pagos_regfac($id);
                            while ($rst_pag1 = pg_fetch_array($cns_pag1)) {
                                $fvencimiento = $rst_pag1[pag_fecha_v];
                            }
                            while ($rst_pag = pg_fetch_array($cns_pag)) {
                                $n++;
                                $pag_fecha_v = $rst_pag[pag_fecha_v];
                                $concepto = 'FACTURACION EN VENTAS';
                                $forma = '';
                                $debito = $rst_pag[pag_valor];
                                $saldo = $saldo + $debito - $credito;
                                $res = pg_fetch_array($CuentasPagar->suma_pagos1($rst[reg_id]));
                                $mto = $res[monto]; // suma pagos cta
                                $pgo = $res[pago]+ $res[debito]; //suma pagos
//                                $rst_pag[pag_fecha_v] = '2015-02-01';
                                if ($fvencimiento < date('Y-m-d')) {
                                    if ($n == '1') {
                                        $vencido = $pgo - $mto;
                                    } else {
                                        $vencido = 0;
                                    }
                                }
                                $rp = pg_fetch_array($CuentasPagar->buscar_un_pago_doc($rst_pag[reg_id]));
                                $pag_id = $rp[pag_id];
                                $pag_fecha_v = $rp[pag_fecha_v];
                                if ($pag_id == '') {
                                    $pag_id = $rst_pag[pag_id];
                                    $pag_fecha_v = $rst_pag[pag_fecha_v];
                                }
                                ?>
                                <tr style="height: 22px">
                                    <td style="font-size: 12px;"><?php echo $rst_pag[pag_fecha_v] ?></td>
                                    <td id="doc" style="font-size: 12px;"><?php echo $rst[reg_num_documento] ?></td>
                                    <td style="font-size: 12px;"><?php echo $concepto ?></td>
                                    <td style="font-size: 12px;"><?php echo $forma ?></td>
                                    <td style="font-size: 12px;" align="right" id="debito<?php echo $n ?>" class="debito"><?php echo str_replace(',', '', number_format($debito, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="credito<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($credito, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="saldo<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($saldo, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="vencido<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($vencido, 4)) ?></td>
                                    <td></td>
                                </tr>
                                <?php
                            }

                            $debito1 = 0;
                            $credito1 = 0;
                            $saldo1 = 0;
                            $vencido1 = 0;
                            $cnscon = $CuentasPagar->listar_una_ctapagar_comid($rst[reg_id]);
                            while ($rst_ctp = pg_fetch_array($cnscon)) {
                                $j++;
                                $n++;
                                if ($j == 1) {
                                    $saldo1 = $saldo;
                                }
                                $debito1 = '0';
                                $credito1 = $rst_ctp[ctp_monto];
                                if ($rst_ctp[ctp_forma_pago] == 'NOTA DE DEBITO') {
                                    $debito1 = $rst_ctp[ctp_monto];
                                    $credito1 = 0;
                                }
                                $saldo1 = $saldo1 + $debito1 - $credito1;
                                ?>
                                <tr style="height: 22px">
                                    <td style="font-size: 12px;"><?php echo $rst_ctp[ctp_fecha_pago] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst[reg_num_documento] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst_ctp[ctp_concepto] ?></td>
                                    <td style="font-size: 12px;"><?php echo $rst_ctp[ctp_forma_pago] ?></td>
                                    <td style="font-size: 12px;" align="right" id="debito<?php echo $n ?>" class="debito"><?php echo str_replace(',', '', number_format($debito1, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="credito<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($credito1, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="saldo<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($saldo1, 4)) ?></td>
                                    <td style="font-size: 12px;" align="right" id="vencido<?php echo $n ?>"> <?php echo str_replace(',', '', number_format($vencido1, 4)) ?></td>
                                    <?php
                                    if ($rst_ctp[ctp_forma_pago] == 'CHEQUE' && $rst_ctp[ctp_aprobacion] == 1) {
                                        ?>
                                        <td><img src="../img/print_iconop.png" class="auxBtn" width="12px" id="img_print" onclick="auxWindow(0,<?php echo $rst_ctp[ctp_id] ?>,<?php echo $rst_ctp[reg_id] ?>)" /></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td></td>
                                        <?php
                                    }
                                    ?>
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
                                    <td></td>

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
                                    <th colspan="6">REGISTRAR PAGO</th> 
                                </tr>
                                <tr>
                                    <th>MONTO</th>
                                    <th>CONCEPTO</th>
                                    <th>FORMA DE PAGO</th>
                                    <th>No.DOCUMENTO</th>
                                    <th>CUENTA</th>
                                    <th>FECHA</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>
                                    <input type="text" size="20"  id="ctp_monto" value="<?php echo $rst[ctp_monto] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                    <input type="text" id="pag_id" value="<?php echo $pag_id ?>" hidden/>
                                    <input type="text" id="fecha" value="<?php echo $pag_fecha_v ?>" hidden/>
                                </td>
                                <td><input type="text" size="33"  id="ctp_concepto" value="<?php echo $rst[ctp_concepto] ?>" onblur="this.value = this.value.toUpperCase()" /></td>
                                <td><select id="ctp_forma_pago">
                                        <option value="CHEQUE">CHEQUE</option>
                                        <option value="EFECTIVO">EFECTIVO</option>
                                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                        <option value="RETENCION">RETENCION</option>
                                        <option value="NOTA DE CREDITO">NOTA DE CREDITO</option>
                                        <option value="NOTA DE DEBITO">NOTA DE DEBITO</option>
                                    </select>
                                </td>
                                <td><input type="text" size="30"  id="num_documento" value="<?php echo $rst[num_documento] ?>" /></td>
                                <td><input type="text" size="30"  id="ctp_banco" value="<?php echo $rst[ctp_banco] ?>"  onfocus="cargar_combo2(this)" /></td>
                                <td>
                                    <input type="text" size="10" id="ctp_fecha_pago" value="<?php echo $rst[ctp_fecha_pago] ?>" maxlength="10" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                        </table> 
                    </td>   
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
                            <button id="cancelar" >Cancelar</button>
                            <!--<button id="imprimir" onclick="lista_pagos(event, '<?php echo $rst[mod_id] ?>', '<?php echo $rst[mod_prioridad] ?>')">IMPRIMIR CHEQUES</button>-->    
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
$cns_can = $CuentasPagar->lista_asientos_contables();
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
$cns_cue = $CuentasPagar->lista_cuentas_bancos();
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