<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php'; // Cambiar clase cierre caja
$Clase_cierre_caja = new Clase_cierre_caja();
if ($emisor >= 10) {
    $emi = '0' . $emisor;
} else {
    $emi = '00' . $emisor;
}
$id = 0;
$des = $_GET[actual];
$has = $_GET[actual];
$rst = pg_fetch_array($Clase_cierre_caja->lista_totales_forma_pago($des, $has, $emisor));
$rst_fac = pg_fetch_array($Clase_cierre_caja->lista_fac_desde_hasta($des, $has, $emisor));
$cns = $Clase_cierre_caja->lista_tarjetas_de_credito($des, $has, $emisor);
$rst_sec = pg_fetch_array($Clase_cierre_caja->lista_secuencial_arqueo($emi));
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
$rst[secuencial] = $emi . '-' . $tx . $sec;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Arqueo de Caja</title>
        <script>
            var id =<?php echo $id ?>;
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
                if (id == 0) {
                    total_cierre();
                }
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

            function save() {
                tcre = $('#credito_modf').val();
                tdeb = $('#debito_modf').val();
                cheq = $('#cheque_modf').val();
                efec = $('#efectivo_modf').val();
                cert = $('#certi_modf').val();
                bono = $('#bonos_modf').val();
                rete = $('#rete_modf').val();
                ncre = $('#nc_modf').val();
                cred = $('#cre_modf').val();
                depo = $('#deposito').val();
                tciem = $('#cierre_modf').val();
                if (tcre == '') {
                    tcre = 0;
                } else {
                    tcre = tcre;
                }
                if (tdeb == '') {
                    tdeb = 0;
                } else {
                    tdeb = tdeb;
                }
                if (cheq == '') {
                    cheq = 0;
                } else {
                    cheq = cheq;
                }
                if (efec == '') {
                    efec = 0;
                } else {
                    efec = efec;
                }
                if (cert == '') {
                    cert = 0;
                } else {
                    cert = cert;
                }
                if (bono == '') {
                    bono = 0;
                } else {
                    bono = bono;
                }
                if (rete == '') {
                    rete = 0;
                } else {
                    rete = rete;
                }
                if (ncre == '') {
                    ncre = 0;
                } else {
                    ncre = ncre;
                }
                if (cred == '') {
                    cred = 0;
                } else {
                    cred = cred;
                }
                if (depo == '') {
                    depo = 0;
                } else {
                    depo = depo;
                }
                if (tciem == '') {
                    tciem = 0;
                } else {
                    tciem = tciem;
                }

                var data = Array(arq_secuencial.value,
                        fec_actual.value,
                        arq_emisor.value,
                        fac_desde.value,
                        fac_hasta.value,
                        tcre,
                        tdeb,
                        cheq,
                        depo,
                        efec,
                        cert,
                        bono,
                        rete,
                        ncre,
                        tciem,
                        cred
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
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($('#cierre_modf').val() != 0) {
                            if ($('#total_cierre').val() != $('#cierre_modf').val()) {
                                alert('LAS SUMAS DE TOTAL CIERRE NO COINCIDEN');
                                $('#total_cierre').css({borderColor: "red"});
                                $('#cierre_modf').css({borderColor: "red"});
                                return false;
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actions_cierre_caja_n.php', //cambiar actions_productos
                    data: {op: 2, 'data[]': data, 'data2[]': data2, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            imprimir();
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_arqueo_caja_locales.php';
            }

            function imprimir() {
                $('#usr').show();
                $('#guardar').hide();
                $('#cancelar').hide();
                $('#cerrar').hide();
                $('#espacio').show();
                $('#linea').show();
                $('#espacio1').show();
                $('#linea1').show();
                $('#tbl_form').css({borderColor: "white"});
                if ($('#num_nc_dos').val() == '' && $('#valor_nc_dos').val() == 0 && $('#num_fac_dos').val() == '') {
                    $('#num_nc_dos').hide();
                    $('#valor_nc_dos').hide();
                    $('#num_fac_dos').hide();
                }
                if ($('#num_nc_tres').val() == '' && $('#valor_nc_tres').val() == 0 && $('#num_fac_tres').val() == '') {
                    $('#num_nc_tres').hide();
                    $('#valor_nc_tres').hide();
                    $('#num_fac_tres').hide();
                }
                if ($('#num_nc_cuatro').val() == '' && $('#valor_nc_cuatro').val() == 0 && $('#num_fac_cuatro').val() == '') {
                    $('#num_nc_cuatro').hide();
                    $('#valor_nc_cuatro').hide();
                    $('#num_fac_cuatro').hide();
                }
                $('#esp1').show();
                $('#esp2').show();
                $('#esp3').show();
                $('#esp4').show();
                $('#firma').show();
                window.print();
                $('#usr').hide();
                cancelar();
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
                cre = parseFloat($('#credito').val() * 1);
                tot = tc + td + ch + ef + ce + bo + re + nc + cre;
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
                $('#cierre_modf').val(tot.toFixed(3));
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
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr>
                        <th colspan="8"><?php echo 'ARQUEO DE CAJA' ?>
                            <font class="cerrar"  id="cerrar" onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                            <font id="usr"><?php echo $_SESSION[usuario] ?></font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="8">
                        <table>
                            <tr>
                                <td>NO</td>
                                <td><input type="text" id="arq_secuencial" value="<?php echo $rst[secuencial] ?>" readonly /></td>
                                <td>CIERRE DE CAJA LOCAL</td>
                                <td><input type="text" id="arq_local" value="<?php echo $bodega ?>" readonly /></td>
                                <td><input type="hidden" id="arq_emisor" value="<?php echo $emisor ?>"></td>
                            </tr>
                            <tr>
                                <td>FECHA</td>
                                <td><input type="text" id="fec_actual" value="<?php echo $des ?>" readonly /></td>
                                <td>FACTURAS DESDE</td>
                                <td><input type="text" id="fac_desde" value="<?php echo $rst_fac[fac_desde] ?>" readonly /></td>
                                <td>HASTA</td>
                                <td><input type="text" id="fac_hasta" value="<?php echo $rst_fac[fac_hasta] ?>" readonly /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr id="head">
                    <td>
                        <table>
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
                                $rst_pag = pg_fetch_array($Clase_cierre_caja->lista_totales_tarjetas($rst1[pag_banco], $rst1[pag_tarjeta], $des, $has, $emisor));
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
                        <table>
                            <tr>
                                <td>TARJETA DE CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="tarjeta_credito" style="text-align:right" value="<?php echo $rst[tarjeta_credito] ?>" readonly />
                                    <input type="text" size="12"  id="credito_modf" style="text-align:right" value="<?php echo $tc ?>" onkeypress="tab(event, 0)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>TARJETA DE DEBITO</td>
                                <td>
                                    <input type="text" size="12"  id="tarjeta_debito" style="text-align:right" value="<?php echo $rst[tarjeta_debito] ?>" readonly />
                                    <input type="text" size="12"  id="debito_modf" style="text-align:right" value="<?php echo $td ?>" onkeypress="tab(event, 1)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>CHEQUE</td>
                                <td>
                                    <input type="text" size="12"  id="cheque" style="text-align:right" value="<?php echo $rst[cheque] ?>" readonly />
                                    <input type="text" size="12"  id="cheque_modf" style="text-align:right" value="<?php echo $ch ?>" onkeypress="tab(event, 2)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                                <td>DEPOSITO</td>
                                <td>
                                    <input type="text" size="12" id="deposito" style="text-align:right" value="<?php echo $de ?>" onkeypress="tab(event, 3)" />
                                </td>
                            </tr>
                            <tr>
                                <td>EFECTIVO</td>
                                <td>
                                    <input type ="text" size="12" id="efectivo" style="text-align:right" value="<?php echo $rst[efectivo] ?>" readonly />
                                    <input type="text" size="12"  id="efectivo_modf" style="text-align:right" value="<?php echo $ef ?>" onkeypress="tab(event, 4)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>CERTIFICADOS</td>
                                <td>
                                    <input type="text" size="12" id="certificados" style="text-align:right" value="<?php echo $rst[certificados] ?>" readonly />
                                    <input type="text" size="12"  id="certi_modf" style="text-align:right" value="<?php echo $ce ?>" onkeypress="tab(event, 5)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>   
                            <tr>
                                <td>BONOS</td>
                                <td>
                                    <input type="text" size="12"  id="bonos" style="text-align:right" value="<?php echo $rst[bonos] ?>" readonly />
                                    <input type="text" size="12"  id="bonos_modf" style="text-align:right" value="<?php echo $bo ?>" onkeypress="tab(event, 6)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>RETENCION</td>
                                <td>
                                    <input type="text" size="12"  id="retencion" style="text-align:right" value="<?php echo $rst[retencion] ?>" readonly />
                                    <input type="text" size="12"  id="rete_modf" style="text-align:right" value="<?php echo $re ?>" onkeypress="tab(event, 7)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>NOTA CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="nota_credito" style="text-align:right" value="<?php echo $rst[nota_credito] ?>" readonly />
                                    <input type="text" size="12"  id="nc_modf" style="text-align:right" value="<?php echo $nc ?>" onkeypress="tab(event, 8)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td>CREDITO</td>
                                <td>
                                    <input type="text" size="12"  id="credito" style="text-align:right" value="<?php echo $rst[credito] ?>" readonly />
                                    <input type="text" size="12"  id="cre_modf" style="text-align:right" onkeypress="tab(event, 9)" onchange="calculo(this)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">TOTAL CIERRE:</td>
                                <td>
                                    <input type="text" size="12"  id="total_cierre" style="text-align:right" readonly />
                                    <input type="text" size="9"  id="cierre_modf" style="text-align:right" value="<?php echo $ci ?>" readonly/>
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
                    <tr><th colspan="10" >NOTAS DE CREDITO</th></tr>
                </thead>
                <tr>
                    <td colspan="10">
                        <table border="0" id="tbl_nc">
                            <thead>
                                <tr>
                                    <td colspan="3"><br></td>
                                </tr>
                            <th>NOTA CREDITO No</th>
                            <th>VALOR</th>
                            <th>No FACTURA</th>
                            </thead>
<!--                            <tr>
                                <td><input type="text" id="num_nc_uno" /></td>
                                <td><input type="text" id="valor_nc_uno" style="text-align:right" value="<?php echo $v1 ?>"/></td>
                                <td><input type="text" id="num_fac_uno" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" id="num_nc_dos" /></td>
                                <td><input type="text" id="valor_nc_dos" style="text-align:right" value="<?php echo $v2 ?>" /></td>
                                <td><input type="text" id="num_fac_dos" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" id="num_nc_tres" /></td>
                                <td><input type="text" id="valor_nc_tres" style="text-align:right" value="<?php echo $v3 ?>" /></td>
                                <td><input type="text" id="num_fac_tres" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" id="num_nc_cuatro" /></td>
                                <td><input type="text" id="valor_nc_cuatro" style="text-align:right" value="<?php echo $v4 ?>" /></td>
                                <td><input type="text" id="num_fac_cuatro"/></td>
                            </tr>-->

                            <?php
                            $cns_nc = $Clase_cierre_caja->lista_notas_credito($des, $emisor);
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
        <button id="guardar" onclick="save()">Guardar</button>   
        <button id="cancelar" >Cancelar</button>  
    </body>
</html>    