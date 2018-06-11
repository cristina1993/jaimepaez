<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cheques.php';
$Cheque = new Clase_cheques();
$id = $_GET[cli];
$ch = $_GET[ch];
$txt = $_GET[txt];
$est = $_GET[estado];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
$cli = pg_fetch_array($Cheque->lista_un_cliente_id($id));
$cns = $Cheque->lista_factras_clientes($cli[cli_ced_ruc]);
$rst = pg_fetch_array($Cheque->lista_un_cheque($ch));
$rst_cred = pg_fetch_array($Cheque->suma_credito($cli[cli_ced_ruc]));
$valor_credito = ($rst_cred[sum1] + $rst_cred[sum4]) - ($rst_cred[sum2] + $rst_cred[sum3]);
$val_chq = $rst[chq_monto] - $rst[chq_cobro]
?>
<!DOCTYPE html>

<head>
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Expires" CONTENT="-1">    
    <meta charset="utf-8">
    <title>Control de Cobros</title>
    <script>
        $(function () {
            $('#cancelar').click(function (e) {
                e.preventDefault();
                cancelar();
            });
            $('#frm_save').submit(function (e) {
                e.preventDefault();
            });
        })


        function save() {

            id =<?php echo $rst[chq_id] ?>;
            fecha = '<?php echo $rst[chq_recepcion] ?>';
            fp = '<?php echo $rst[chq_tipo_doc] ?>';
            cuenta = '<?php echo $rst[chq_cuenta] ?>';
            num_chq = '<?php echo $rst[chq_numero] ?>';
            var data = Array();
            $('.pagos').each(function () {
                fac = this.lang;
                if (parseFloat($(this).val()) > 0) {
                    data.push(
                            id + '&' +
                            fac + '&' +
                            $(this).val() + '&' +
                            fecha + '&' +
                            fp + '&' +
                            identificacion.value + '&' +
                            num_chq + '&' +
                            cuenta + '&' +
                            $('#num' + fac).html()+ '&' +
                            concepto.value.toUpperCase()
                            );
                }
            });
            var fields = Array();
            $('.pagos').each(function () {
                f = this.lang;
                numero = $('#num' + f).html();
                var elemento = this;
                if (parseFloat($(this).val()) > 0) {
                    des = numero + "=" + elemento.value;
                    fields.push(des);
                }
            });
            fields.push('');
            $.ajax({
                beforeSend: function () {
                    //Validaciones antes de enviar
                    v = 0;
                    $('.pagos').each(function () {
                        if ($(this).val().length == 0) {
                            $(this).css({borderColor: "red"});
                            return v = 1;
                        }
                    });
                    if (parseFloat($('#t_pago').html()) > parseFloat($('#val_doc').val())) {
                        alert('No se puede registrar el pago porque el total\n\del pago es mayor que el valor del documento ');
                        v = 1;
                    }
                    if (parseFloat($('#t_pago').html()) == 0 || $('#t_pago').html().length == 0) {
                        alert('Ingrese un valor');
                        v = 1;
                    }

                    if (v == 1) {
                        return false;
                    }
                    $('#guardar').attr('disabled',true);
                },
                type: 'POST',
                url: 'actions_cheques.php',
                data: {op: 5, 'data[]': data, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                success: function (dt) {
                    if (dt == 0) {
                        parent.document.getElementById('bottomFrame').src = '../Scripts/Form_control_cobros.php?cli=' + <?php echo $id ?> + '&ch=' + <?php echo $ch ?>+'&txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
                    } else {
                        $('#guardar').attr('disabled',false);
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
            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cheques.php?txt=' + '<?php echo $txt ?>' + '&estado=' + '<?php echo $est ?>' + '&fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
        }


        function loading(prop) {
            $('#cargando').css('visibility', prop);
            $('#charging').css('visibility', prop);
        }

        function total() {
            tot = 0;
            $('.pagos').each(function () {
                f = this.lang;
                saldo = $('#sd' + f).html();
                if (parseFloat(saldo) < parseFloat($(this).val())) {
                    alert('pago es mayor al saldo');
                    $(this).val('0');
                } else {
                    tot = tot + parseFloat($(this).val());
                }
            });
            $('#t_pago').html(tot.toFixed(4));
        }

    </script>
    <style>
        .fila-base{ display: none; } /* fila base oculta */
        .eliminar{ cursor: pointer; color: #000; }
        thead tr td{
            font-size: 11px;
            border:solid 0px #ccc;
        }

        *{
            font-size: 11px;
            font-weight:100; 
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
                    <th colspan="8"><?php echo 'FORMULARIO CONTROL DE COBROS' ?>
                        <font class="cerrar"  id="cerrar" onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>Nombre:</td>
                            <td><input type="text" size="25" id="nombre" readonly value="<?php echo $cli[cli_raz_social] ?>" /></td>
                            <td>Valor Doc.:</td>
                            <td><input type="text" size="10" id="val_doc" readonly value="<?php echo str_replace(',', '', number_format($val_chq, 4)) ?>" /></td>
                        </tr>
                        <tr>
                            <td>Identificacion:</td>
                            <td><input type="text" size="25" id="identificacion" readonly value="<?php echo $cli[cli_ced_ruc] ?>" /></td>
                            <td>Credito:</td>
                            <td><input type="text" size="10" id="credito" readonly value="<?php echo str_replace(',', '', number_format($valor_credito, 4)) ?>" /></td>
                        </tr>
                         <tr>
                            <td>Concepto:</td>
                            <td><input type="text" size="25" id="concepto" readonly value="<?php echo $rst[chq_concepto] ?>" /></td>
                           
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
                                <th>FECHA</th>
                                <th>FACTURA</th>
                                <th>FECHA VENCIMIENTO</th>
                                <th>VALOR TOTAL $</th>
                                <th>PAGADO</th>
                                <th>SALDO</th>
                                <th>PAGO</th>
                            </tr>
                        </thead>
                        <?php
                        $n = 0;
                        $c = '"';
                        while ($fac = pg_fetch_array($cns)) {

                            $credito = ($fac[debito] + $fac[fac_total_valor]) - $fac[monto];
                            if ($credito > 0) {
                                $n++;
                                $t_f+=$fac[fac_total_valor];
                                $ast = '';
                                $res = pg_fetch_array($Cheque->suma_pagos($fac['fac_id'], $rst['fac_numero'])); ///quitar fac_numero
                                $pagado = $res[monto];
                                $total_valor = $fac['fac_total_valor'] + $res[debito];
                                $saldo = $total_valor - $pagado;
                                if ($res[debito] != 0) {
                                    $ast = '*';
                                }
                                $fec = $rst_pag = pg_fetch_array($Cheque->lista_fecha_vence($fac[fac_id]));
                                $com = '""';
                                echo "<tr>
                                        <td>$n</td>
                                        <td>$fac[fac_fecha_emision]</td>
                                        <td id='num$fac[fac_id]'>$fac[fac_numero] $ast</td>
                                        <td>$fec[pag_fecha_v]</td>
                                        <td align='right'>" . str_replace(',', '', number_format($fac[fac_total_valor], 4)) . "</td>
                                        <td align='right'>" . str_replace(',', '', number_format($pagado, 4)) . "</td>
                                        <td align='right' id='sd$fac[fac_id]'>" . str_replace(',', '', number_format($saldo, 4)) . "</td>
                                        <td><input type='text' size='10' class='pagos' id='pago$fac[fac_id]' lang='$fac[fac_id]' value='0' onchange='total()' onkeyup='this.value = this.value.replace(/[^0-9.]/, $com)' /></td>
                                     </tr>";
                                $t_pagado+=$pagado;
                                $t_saldo+=$saldo;
                            }
                        }
                        echo "<tr>
                                        <td colspan='4'>Total</td>
                                        <td align='right'>" . str_replace(',', '', number_format($t_f, 4)) . "</td>
                                        <td align='right'>" . str_replace(',', '', number_format($t_pagado, 4)) . "</td>
                                        <td align='right'>" . str_replace(',', '', number_format($t_saldo, 4)) . "</td>
                                        <td id='t_pago' align='right'></td>
                               </tr>";
                        ?>

                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <button id="guardar" onclick="save()">Guardar</button>   
                    <button id="cancelar" >Cancelar</button>  
                </td>
            </tr>
        </table>
    </form> 
</body>
</html>
