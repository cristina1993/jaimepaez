<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
$emisor;
$fecha = $_GET[fec];
$fec = date('Y-m-d');
$user = strtoupper($rst_user[usu_person]);
$usu = pg_fetch_array($Clase_cierre_caja->lista_vendedores($user));
$rst = pg_fetch_array($Clase_cierre_caja->lista_cierrres($fecha, $usu[vnd_id], $emisor));
$t_fac = number_format($rst[cie_total_facturas], 4);
$tot_fac = str_replace(',', '', $t_fac);
$det = '1';
$id = '0';
$det = '0';
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario Cierre Caja</title>
        <script>

            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            function save(id, user, fec) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');

                var data = Array(camb_tnc.value,
                        camb_tnd.value,
                        camb_tcheque.value,
                        camb_tefec.value,
                        camb_tcerti.value,
                        camb_tbonos.value,
                        camb_trete.value,
                        camb_not_cre.value,
                        camb_credito.value);

                $.ajax({
                    beforeSend: function () {
                        tp = total_forpagos2.value;
                        tf = total_facturas.value;
                        tfp = tp - tf;
                        var flotante = parseFloat(tfp);
                        var resultado = Math.round(flotante * 100) / 100;
                        if(resultado != '0' && resultado != '0.01' && resultado != '0.02'){
                            alert('CANTIDADES DE TOTAL EN CAJA NO COINCIDEN');
                            return false;
                        }
//                        if (total_forpagos2.value != total_facturas.value) {
//                            alert('CANTIDADES DE TOTAL EN CAJA NO COINCIDEN');
//                            return false;
//                        }
                    },
                    type: 'POST',
                    url: 'actions_cierre_caja_n.php',
                    data: {op: 1, 'data[]': data, id: id},
                    success: function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('contenedor2').rows = "*,95%";
                            frm.src = '../Scripts/frm_pdf_cierre_caja.php?user=' + user + '&fec=' + fec;
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function calculo() {
                nc = $('#camb_tnc').val().replace(',', '');
                nd = $('#camb_tnd').val().replace(',', '');
                che = $('#camb_tcheque').val().replace(',', '');
                efec = $('#camb_tefec').val().replace(',', '');
                cert = $('#camb_tcerti').val().replace(',', '');
                bon = $('#camb_tbonos').val().replace(',', '');
                ret = $('#camb_trete').val().replace(',', '');
                notc = $('#camb_not_cre').val().replace(',', '');
                cre = $('#camb_credito').val().replace(',', '');
                sumatot = 0;
                sumatot = nc * 1 + nd * 1 + che * 1 + efec * 1 + cert * 1 + bon * 1 + ret * 1 + notc * 1 + cre * 1;

                $('#total_forpagos2').val(sumatot.toFixed(2)).replace(',', '');
            }

            function activar(form) {
                if (form.tc.checked == true) {
                    form.camb_tnc.disabled = false;
                } else {
                    form.camb_tnc.disabled = true;
                }
                if (form.td.checked == true) {
                    form.camb_tnd.disabled = false;
                } else {
                    form.camb_tnd.disabled = true;
                }
                if (form.cheque.checked == true) {
                    form.camb_tcheque.disabled = false;
                } else {
                    form.camb_tcheque.disabled = true;
                }
                if (form.efec.checked == true) {
                    form.camb_tefec.disabled = false;
                } else {
                    form.camb_tefec.disabled = true;
                }
                if (form.cert.checked == true) {
                    form.camb_tcerti.disabled = false;
                } else {
                    form.camb_tcerti.disabled = true;
                }
                if (form.bono.checked == true) {
                    form.camb_tbonos.disabled = false;
                } else {
                    form.camb_tbonos.disabled = true;
                }
                if (form.ret.checked == true) {
                    form.camb_trete.disabled = false;
                } else {
                    form.camb_trete.disabled = true;
                }
                if (form.nt_cre.checked == true) {
                    form.camb_not_cre.disabled = false;
                } else {
                    form.camb_not_cre.disabled = true;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

//            function auxWindow(a, usu) {
//                frm = parent.document.getElementById('bottomFrame');
//                main = parent.document.getElementById('mainFrame');
//                switch (a)
//                {
//                    case 0://Cierre de Caja
//                        emisor = '<?php echo $emisor ?>';
//                        $.ajax({
//                            beforeSend: function () {
//
//                            },
//                            type: 'POST',
//                            url: 'actions_cierre_caja.php',
//                            data: {op: 0, id: id},
//                            success: function (dt) {
//                                d = dt.split('&');
//                                if (d[1] == 1) {
//                                    parent.document.getElementById('contenedor2').rows = "*,95%";
//                                    frm.src = '../Scripts/frm_pdf_cierre_caja.php?emisor=' + emisor + '&usu=' + usu;
//                                } else {
//                                    alert(d[1]);
//                                }
//                            }
//                        });
//                        break;
//                }
//            }


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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form"  >
                <thead>
                    <tr><th colspan="9" ><?php echo 'CIERRE DE CAJA ' . $bodega ?><font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>FECHA</td>
                    <td><input type="text" id="fecha" readonly value="<?php echo $rst[cie_fecha] ?>">
                        HORA
                        <input type="text"  size="6" id="hora" readonly value="<?php echo $rst[cie_hora] ?>"></td>
                </tr>
                <tr>
                    <td>USUARIO</td>
                    <td><input type="text" id="usuario" readonly value="<?php echo $rst[vnd_nombre] ?>" style="width:160px;height:20px;font-size:11px;font-weight:50"></td>
                </tr>   
                <tr>
                    <td>ALMACEN</td>
                    <td><input type="text" id="almacen" readonly value="<?php echo $bodega ?>" style="width:160px;height:20px;font-size:11px;font-weight:50"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>FACTURAS EMITIDAS</td>
                    <td><input type="text" id="facturas_emitidas" readonly value="<?php echo $rst[cie_fac_emitidas] ?>"></td>
                </tr>
                <tr>
                    <td>PRODUCTOS FACTURADOS</td>
                    <td><input type="text" id="productos_facturados" readonly value="<?php echo $rst[cie_productos_facturados] ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>SUBTOTAL</td>
                    <td><input type="text" id="subtotal" readonly value="<?php echo number_format($rst[cie_subtotal], 2) ?>"></td>
                </tr>
                <tr>
                    <td>DESCUENTO</td>
                    <td><input type="text" id="descuento" readonly value="<?php echo number_format($rst[cie_descuento], 2) ?>"></td>
                </tr>
                <tr>
                    <td>IVA</td>
                    <td><input type="text" id="iva" readonly value="<?php echo number_format($rst[cie_iva], 2) ?>"></td>
                </tr>
                <?php
                $sub = $rst[cie_subtotal];
                $des = $rst[cie_descuento];
                $iva = $rst[cie_iva];
                $total = ($sub - $des) + $iva;
                ?>
                <tr>
                    <td>TOTAL</td>
                    <td><input type="text" id="total" readonly value="<?php echo number_format($total, 2) ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>TOTAL FACTURAS</td>
                    <td><input type="text" id="total_facturas" readonly value="<?php echo str_replace(',', '', number_format($tot_fac, 2)) ?>"></td>
                </tr>
                <tr>
                    <td>TOTAL NOTAS DE CREDTO</td>
                    <td><input type="text" id="total_nc" readonly value="<?php echo number_format($rst[cie_total_notas_credito], 2) ?>"></td>
                </tr>
                <?php
                $tot_fac = $rst[cie_total_facturas];
                $tot_nc = $rst[cie_total_notas_credito];
                $total_caja = $tot_fac - $tot_nc;
                ?>
                <tr>
                    <td>TOTAL EN CAJA</td>
                    <td><input type="text" id="total_caja" readonly value="<?php echo number_format($total_caja, 2) ?>"></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="4">
                            <?php echo "FORMAS DE PAGO" ?>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td>TIPO PAGO</td>
                    <td>VALOR&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONFIRMAR</td>
                </tr>
                <tr>
                    <td>1 TARJETA DE CREDITO</td>
                    <td><input type="text" id="total_tnc"  readonly value="<?php echo $rst[cie_total_tarjeta_credito] ?>">
                        <input type="checkbox" id="tc" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tnc" value="<?php echo $rst[cie_total_tarjeta_credito] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>2 TARJETA DE DEBITO</td>
                    <td><input type="text" id="total_nd" readonly value="<?php echo $rst[cie_total_tarjeta_debito] ?>" >
                        <input type="checkbox" id="td" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tnd"  value="<?php echo $rst[cie_total_tarjeta_debito] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>3 CHEQUE</td>
                    <td><input type="text" id="total_cheque" readonly value="<?php echo $rst[cie_total_cheque] ?>">
                        <input type="checkbox" id="cheque" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tcheque"  value="<?php echo $rst[cie_total_cheque] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>4 EFECTIVO</td>
                    <td><input type="text" id="total_efectivo" readonly value="<?php echo $rst[cie_total_efectivo] ?>">
                        <input type="checkbox" id="efec" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tefec"  value="<?php echo $rst[cie_total_efectivo] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>5 CERTIFICADOS</td>
                    <td><input type="text" id="total_certificados" readonly value="<?php echo $rst[cie_total_certificados] ?>">
                        <input type="checkbox" id="cert" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tcerti" value="<?php echo $rst[cie_total_certificados] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>6 BONOS</td>
                    <td><input type="text" id="total_bonos" readonly value="<?php echo $rst[cie_total_bonos] ?>">
                        <input type="checkbox" id="bono" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_tbonos" value="<?php echo $rst[cie_total_bonos] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>7 RETENCION</td>
                    <td><input type="text" id="total_retencion" readonly value="<?php echo $rst[cie_total_retencion] ?>">
                        <input type="checkbox" id="ret" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_trete" value="<?php echo $rst[cie_total_retencion] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>8 NOTA CREDITO</td>
                    <td><input type="text" id="cie_total_not_credito" readonly value="<?php echo $rst[cie_total_not_credito] ?>">
                        <input type="checkbox" id="nt_cre" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_not_cre" value="<?php echo $rst[cie_total_not_credito] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <tr>
                    <td>9 CREDITO</td>
                    <td><input type="text" id="cie_total_credito" readonly value="<?php echo $rst[cie_total_credito] ?>">
                        <input type="checkbox" id="cred" onclick="activar(this.form)">
                        <input type="text" size="8" id="camb_credito" value="<?php echo $rst[cie_total_credito] ?>" onkeyup="calculo()" disabled></td>
                </tr>
                <?php
                $tot_tc = $rst[cie_total_tarjeta_credito];
                $tot_td = $rst[cie_total_tarjeta_debito];
                $tot_cheque = $rst[cie_total_cheque];
                $tot_efectivo = $rst[cie_total_efectivo];
                $tot_certif = $rst[cie_total_certificados];
                $tot_bonos = $rst[cie_total_bonos];
                $tot_retencion = $rst[cie_total_retencion];
                $tot_notcre = $rst[cie_total_not_credito];
                $tot_credito = $rst[cie_total_credito];
                $total_forpagos = $tot_tc + $tot_td + $tot_cheque + $tot_efectivo + $tot_certif + $tot_bonos + $tot_retencion + $tot_notcre + $tot_credito;
                $t_pag = str_replace(',', '', number_format($total_forpagos, 2));
                ?>
                <thead>
                    <tr><th colspan="9" ></th></tr>
                </thead>
                <tr>
                    <td>TOTAL EN CAJA</td>
                    <td>
                        <input type="text" id="total_forpagos" readonly value="<?php echo number_format($total_forpagos, 2) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" size="7" id="total_forpagos2" readonly value="<?php echo $t_pag ?>">
                    </td>
                </tr>
            </table>
        </form> 
        <button id="guardar" onclick="save('<?php echo $rst[cie_id] ?>', '<?php echo $rst[cie_usuario] ?>', '<?php echo $fecha ?>')">Guardar</button> 
        <button id="cancelar" >Cancelar</button>    
    </body>
</html>    
