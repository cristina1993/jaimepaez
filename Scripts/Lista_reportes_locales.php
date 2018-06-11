<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';
$Rep = new Reportes();
if (isset($_GET[search])) {
    $d = str_replace('-', '', $_GET[desde]);
    $h = str_replace('-', '', $_GET[hasta]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $vnd = $_GET[vnd];
    $res = $_GET[res];
    $vnt = $_GET[vnt];
    $dev = $_GET[dev];
    $caja = $_GET[caja];
    $desg = $_GET[desg];

    if ($vnd == 'on') {
        $vnd = 'checked';
    } else {
        $vnd = '';
    }
    if ($res == 'on') {
        $res = 'checked';
    } else {
        $res = '';
    }
    if ($vnt == 'on') {
        $vnt = 'checked';
    } else {
        $vnt = '';
    }
    if ($dev == 'on') {
        $dev = 'checked';
    } else {
        $dev = '';
    }
    if ($caja == 'on') {
        $caja = 'checked';
    } else {
        $caja = '';
    }
    //$val="where emi_cod_punto_emision=1 or emi_cod_punto_emision=11";
    $cns = $Rep->lista_emisores($val);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $vnd = 'checked';
    $res = 'checked';
    $vnt = 'checked';
    $dev = 'checked';
    $caja = 'checked';
    $desg = 'checked';
    $cns = null;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Tipo de pago</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <head>
        <script>
            dec = '<?php echo $dec ?>';
            $(function () {

                $("#tbl").tablesorter({
                    headers: {
                        0: {sorter: false},
                        1: {sorter: false},
                        2: {sorter: false},
                        3: {sorter: false},
                        4: {sorter: false},
                        5: {sorter: false},
                        6: {sorter: false},
                        7: {sorter: false},
                        8: {sorter: false},
                        9: {sorter: false},
                        10: {sorter: false},
                        11: {sorter: false},
                        12: {sorter: false},
                        14: {sorter: false},
                        15: {sorter: false},
                        16: {sorter: false},
                        17: {sorter: false},
                        18: {sorter: false},
                        19: {sorter: false},
                        20: {sorter: false},
                        21: {sorter: false},
                        22: {sorter: false},
                        23: {sorter: false},
                        24: {sorter: false},
                        25: {sorter: false},
                        26: {sorter: false},
                        27: {sorter: false},
                        28: {sorter: false},
                        29: {sorter: false},
                        30: {sorter: false},
                        31: {sorter: false},
                        32: {sorter: false},
                        33: {sorter: false},
                        34: {sorter: false},
                        35: {sorter: false},
                        36: {sorter: false},
                        37: {sorter: false},
                        38: {sorter: false},
                        39: {sorter: false},
                        40: {sorter: false},
                        41: {sorter: false},
                        42: {sorter: false},
                        43: {sorter: false},
                        44: {sorter: false}
                    },
                    widgets: ['stickyHeaders'],
                    highlightClass: 'highlight',
                    widthFixed: false
                });
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                var vnd = '<?php echo $vnd ?>';
                if (vnd != 'checked') {
                    $('.vendedores').hide();
                    $('.enc').hide();
                    $('.tit0').hide();
                    $('.tit1').show();
                } else {
                    $('.vendedores').show();
                    $('.enc').show();
                    $('.tit0').show();
                    $('.tit1').hide();
                }

                $('#vnd').click(function () {
                    if (this.checked == true) {
                        $('.vendedores').show();
                        $('.enc').show();
                        $('.tit0').show();
                        $('.tit1').hide();
                    } else {
                        $('.vendedores').hide();
                        $('.enc').hide();
                        $('.tit0').hide();
                        $('.tit1').show();
                    }
                });

                var res = '<?php echo $res ?>';
                if (res != 'checked') {
                    $('th[name="resumen"],td[name="resumen"]').hide();
                } else {
                    $('th[name="resumen"],td[name="resumen"]').show();
                }
                $('#res').click(function () {
                    if (this.checked == true) {
                        $('th[name="resumen"],td[name="resumen"]').show();
                    } else {
                        $('th[name="resumen"],td[name="resumen"]').hide();
                    }
                });


                var vnt = '<?php echo $vnt ?>';
                if (vnt != 'checked') {
                    $('th[name="col_ventas"],td[name="col_ventas"]').hide();
                } else {
                    $('th[name="col_ventas"],td[name="col_ventas"]').show();
                }
                $('#vnt').click(function () {
                    if (this.checked == true) {
                        $('th[name="col_ventas"],td[name="col_ventas"]').show();
                    } else {
                        $('th[name="col_ventas"],td[name="col_ventas"]').hide();
                    }
                });

                var dev = '<?php echo $dev ?>';
                if (dev != 'checked') {
                    $('th[name="col_devoluciones"],td[name="col_devoluciones"]').hide();
                } else {
                    $('th[name="col_devoluciones"],td[name="col_devoluciones"]').show();
                }
                $('#dev').click(function () {
                    if (this.checked == true) {
                        $('th[name="col_devoluciones"],td[name="col_devoluciones"]').show();
                    } else {
                        $('th[name="col_devoluciones"],td[name="col_devoluciones"]').hide();
                    }
                });

                var caja = '<?php echo $caja ?>';
                if (caja != 'checked') {
                    $('th[name="col_caja"],td[name="col_caja"]').hide();
                } else {
                    $('th[name="col_caja"],td[name="col_caja"]').show();
                }
                $('#caja').click(function () {
                    if (this.checked == true) {
                        $('th[name="col_caja"],td[name="col_caja"]').show();
                    } else {
                        $('th[name="col_caja"],td[name="col_caja"]').hide();
                    }
                });

                $('#exp_excel').submit(function () {
                    $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                    $("#tbl2").append($("#tbl tbody").clone()).html();
                    $("#tbl2").append($("#tbl tfoot").clone()).html();
                    $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                })


            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cupos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cupos.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
        </script> 
        <style>
            #mn261{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #tbody tr td{
                background-color:#f8f8f8;
            }            
            .enc{
                font-size:12px !important; 
                font-weight:bolder;                 
            }
            .enc_t > td{
                background:#F0F5FF  !important;
                cursor:pointer !important; 
                font-weight:bolder; 
            }
            .totales td{
                padding:5px;         
                font-size:13px; 
                font-weight:bolder; 
                color: #00529B;
                background-color: #BDE5F8;

            }
            #tbody tr:hover > td{
                background:#BDE5F8;
                cursor:pointer; 
            }            
        </style>
    </head>
    <body>

        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="26"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="26" align="center">REPORTES POR PUNTO DE FACTURACION</td></tr>
            <tr>
                <td colspan="26"><?php echo 'Desde: ' . $desde . '    Hasta: ' . $hasta ?></td>
            </tr>
        </table>        

        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >REPORTES POR PUNTO DE FACTURACION</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="checkbox" id="vnd" name="vnd" <?php echo $vnd ?> />Por Vendedor
                        <input type="checkbox" id="res" name="res" <?php echo $res ?> />Resumen
                        <input type="checkbox" id="vnt" name="vnt" <?php echo $vnt ?> />Ventas
                        <input type="checkbox" id="dev" name="dev" <?php echo $dev ?> />Devolucion
                        <input type="checkbox" id="caja" name="caja" <?php echo $caja ?> />Caja
                        <input type="text" id="desde" name="desde" size="12"  value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im_desde" />
                        <input type="text" id="hasta" name="hasta" size="12"  value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im_hasta" />
                        <input type="submit" name="search" id="search" value="Buscar"  >
                    </form>  
                    <form id="exp_excel" style="float:right " method="post" action="../Includes/export.php"  >
                        <input type="submit" value="Excel" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <colgroup span="1" />
            <colgroup span="9" style="background:#AAFFAA;visibility:hidden " id="cols_vent" />
            <thead>
                <tr>
                    <th></th>
                    <th colspan="9" name='col_ventas' >Ventas</th>
                    <th colspan="6" name='col_devoluciones'>Devoluciones</th>
                    <th>TOTAL</th>
                    <th colspan="10" name='col_caja'>Cierre de Caja</th>
                </tr>
                <tr>
                    <th id="locales">Locales/Vendedores</th>
                    <th class="titulo" lang="01" name='col_ventas'>#Facturas</th>
                    <th class="titulo" lang="02" name='col_ventas'>Total_Ventas</th>                                
                    <th class="titulo" lang="03" name='col_ventas'>Descuento</th>
                    <th class="titulo" lang="04" name='col_ventas'>Sbt_con_IVA</th>
                    <th class="titulo" lang="05" name='col_ventas'>Sbt_sin_IVA</th>
                    <th class="titulo" lang="06" name='col_ventas'>Sbt_Neto</th>
                    <th class="titulo" lang="07" name='col_ventas'>ICE</th>
                    <th class="titulo" lang="08" name='col_ventas'>IVA</th>
                    <th class="titulo" lang="09" name='resumen'>Total_Ventas</th>

                    <th class="titulo" lang="10" name='col_devoluciones'>#NC</th>
                    <th class="titulo" lang="11" name='col_devoluciones'>Sbt_con_IVA</th>
                    <th class="titulo" lang="12" name='col_devoluciones'>Sbt_sin_IVA</th>
                    <th class="titulo" lang="13" name='col_devoluciones'>Sbt_Neto</th>
                    <th class="titulo" lang="14" name='col_devoluciones'>IVA</th>
                    <th class="titulo " lang="15" name='resumen'>Tot_Dev</th>

                    <th class="titulo" lang="16" name='resumen' >Ventas_Netas</th>

                    <th class="titulo" lang="17" name='col_caja'>Cheque</th>
                    <th class="titulo" lang="18" name='col_caja'>Efectivo</th>
                    <th class="titulo" lang="19" name='col_caja'>Retencion</th>
                    <th class="titulo" lang="20" name='col_caja'>NC</th>
                    <th class="titulo" lang="21" name='col_caja'>Certificados</th>
                    <th class="titulo" lang="22" name='col_caja'>Bonos</th>
                    <th class="titulo" lang="23" name='col_caja'>TD</th>
                    <th class="titulo" lang="24" name='col_caja'>TC</th> 
                    <th class="titulo" lang="25" name='col_caja'>Credito</th> 
                    <th class="titulo " lang="26" name='resumen'>Total_Caja</th> 
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $pte = 0;
                $txt_pte = '';
                while ($rst_locales = pg_fetch_array($cns)) {
                    $n++;
                    $cns_vendedor = $Rep->lista_vendedores_fac_not($rst_locales[emi_cod_punto_emision], $desde, $hasta, 1);
                    if ($n > 1) {
                        ?>
                        <tr class="enc_t" id="<?php echo 'tot' . $pte ?>" >
                            <td>
                                <font class="tit0"><?php echo 'Total : ' ?></font>
                                <font class="tit1" ><?php echo $txt_pte ?></font>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="enc" lang="<?php echo $pte ?>" ><?php echo $rst_locales[emi_nombre_comercial] ?></td>
                        <td colspan="26"></td>
                    </tr>
                    <?php
                    while ($rst_vnd = pg_fetch_array($cns_vendedor)) {
                        $rst_ventas = pg_fetch_array($Rep->lista_ventas_devoluciones_vendedor($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor]));
                        $rst_dev = pg_fetch_array($Rep->lista_devoluciones_vendedor($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor]));
                        $vendedor = pg_fetch_array($Rep->lista_vendedores($rst_vnd[vendedor]));
//                        if ($rst_locales[emi_cod_punto_emision] != 1 && $rst_locales[emi_cod_punto_emision] != 10) {
                        $tc = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '1'));
                        $td = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '2'));
                        $ch = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '3'));
                        $ef = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '4'));
                        $cr = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '5'));
                        $bs = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '6'));
                        $rt = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '7'));
                        $nc = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '8'));
                        $crd = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $desde, $hasta, $rst_vnd[vendedor], '9'));
                        $total_caja = number_format($ch[sum] + $ef[sum] + $rt[sum] + $nc[sum] + $cr[sum] + $bs[sum] + $td[sum] + $tc[sum] + $crd[sum], 2);
                        $ch[sum] = number_format($ch[sum], 2);
                        $ef[sum] = number_format($ef[sum], 2);
                        $rt[sum] = number_format($rt[sum], 2);
                        $nc[sum] = number_format($nc[sum], 2);
                        $cr[sum] = number_format($cr[sum], 2);
                        $bs[sum] = number_format($bs[sum], 2);
                        $td[sum] = number_format($td[sum], 2);
                        $tc[sum] = number_format($tc[sum], 2);
                        $crd[sum] = number_format($crd[sum], 2);


                        //*******desgloce de targeta de credito*************///
                        $cns_banc = $Rep->lista_bancos_desgloce_tc($rst_locales[emi_cod_punto_emision], $d, $h, $rst_vnd[vendedor]);
//                        } else {
//                            $ch[sum] = '';
//                            $ef[sum] = '';
//                            $rt[sum] = '';
//                            $nc[sum] = '';
//                            $cr[sum] = '';
//                            $bs[sum] = '';
//                            $td[sum] = '';
//                            $tc[sum] = '';
//                            $total_caja = '';
//                        }
                        ?>
                        <tr class="vendedores">
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vendedor[vnd_nombre] ?></td>
                            <td align="right"  class="<?php echo 'itm01' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[nfact]) ?></td>
                            <td align="right"  class="<?php echo 'itm02' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[tventa], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm03' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[desc], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm04' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[con_iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm05' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[sin_iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm06' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[sbt_neto], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm07' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[ice], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm08' . $rst_locales[emi_cod_punto_emision] ?>" name='col_ventas'><?php echo number_format($rst_ventas[iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm09' . $rst_locales[emi_cod_punto_emision] ?>  " name='resumen'><?php echo number_format($rst_ventas[tventas], 2) ?></td>
                            <!--Devoluciones-->
                            <td align="right"  class="<?php echo 'itm10' . $rst_locales[emi_cod_punto_emision] ?>" name='col_devoluciones'><?php echo $rst_dev[nfact] ?></td>
                            <td align="right"  class="<?php echo 'itm11' . $rst_locales[emi_cod_punto_emision] ?>" name='col_devoluciones'><?php echo number_format($rst_dev[con_iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm12' . $rst_locales[emi_cod_punto_emision] ?>" name='col_devoluciones'><?php echo number_format($rst_dev[sin_iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm13' . $rst_locales[emi_cod_punto_emision] ?>" name='col_devoluciones'><?php echo number_format($rst_dev[sbt_neto], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm14' . $rst_locales[emi_cod_punto_emision] ?>" name='col_devoluciones'><?php echo number_format($rst_dev[iva], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm15' . $rst_locales[emi_cod_punto_emision] ?>  " name='resumen'><?php echo number_format($rst_dev[tventas], 2) ?></td>

                            <td align="right"  class="<?php echo 'itm16' . $rst_locales[emi_cod_punto_emision] ?>  " name='resumen'><?php echo number_format($rst_ventas[tventas] - $rst_dev[tventas], 2) ?></td>
                            <td align="right"  class="<?php echo 'itm17' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $ch[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm18' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $ef[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm19' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $rt[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm20' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $nc[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm21' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $cr[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm22' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $bs[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm23' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $td[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm24' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $tc[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm25' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $crd[sum] ?></td>
                            <td align="right"  class="<?php echo 'itm26' . $rst_locales[emi_cod_punto_emision] ?>  " name='resumen'><?php echo $total_caja ?></td>
                        </tr>
                        <?php
                    }
                    $txt_pte = $rst_locales[emi_nombre_comercial];
                    $pte = $rst_locales[emi_cod_punto_emision];
                }
                ?>
                <tr class="enc_t" id="<?php echo 'tot' . $pte ?>" >
                    <td>
                        <font class="tit0"><?php echo 'Total : ' ?></font>
                        <font class="tit1" ><?php echo $txt_pte ?></font>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="totales" >
                    <td>Totales:</td>
                </tr>
            </tfoot>
        </table>            
    </body>    
</html>
<script>
    i = 0;
    $('.enc').each(function () {
        en = this.lang;
        i++;
        cn = 0;
        $('.titulo').each(function () {
            t = 0;
            cn++;
            if (cn > 0 && cn <= 9) {
                nm = 'col_ventas';
            } else if (cn > 9 && cn <= 16) {
                nm = 'col_devoluciones';
            } else if (cn > 16 && cn <= 26) {
                nm = 'col_caja';
            } else {
                nm = '';
            }

            $('.itm' + this.lang + i).each(function () {
                t = ((t * 1) + ($(this).html().replace(/,/g, '') * 1));
            });
            t = parseFloat(t);
            if (cn == 9 || cn == 15 || cn == 16 || cn == 26) {
                nm = 'resumen';
            }
            $("#tot" + i).append("<td align='right'  class='t" + this.lang + " ' name='" + nm + "' >" + accounting.formatMoney(t, "", dec, ",", ".")  + "</td>").html();
            t = 0;
        });
    });

    cn = 0;
    $('.titulo').each(function () {
        t = 0;
        cn++;
        if (cn > 0 && cn <= 9) {
            nm = 'col_ventas';
        } else if (cn > 9 && cn <= 16) {
            nm = 'col_devoluciones';
        } else if (cn > 16 && cn <= 25) {
            nm = 'col_caja';
        } else {
            nm = '';
        }
        $('.t' + this.lang).each(function () {
            t = ((t * 1) + ($(this).html().replace(/,/g, '') * 1));
        });

        if (cn == 9 || cn == 15 || cn == 16 || cn == 26) {
            nm = 'resumen';
        }

        $(".totales").append("<td align='right' name='" + nm + "' >" + accounting.formatMoney(t, "", dec, ",", ".") + "</td>").html();
        t = 0;
    });

</script>

