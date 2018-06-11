<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';
$Rep = new Reportes();
if (isset($_GET[search])) {
    $d = $_GET[desde];
    $h = $_GET[hasta];
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $vnd = $_GET[vnd];

    if ($vnd == 'on') {
        $vnd = 'checked';
    } else {
        $vnd = '';
    }
//    $val = 'where emi_cod_punto_emision<>1 and emi_cod_punto_emision<>10';
    $cns = $Rep->lista_emisores($val);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $vnd = 'checked';
    $res = 'checked';
    $cns = null;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Desgloce de Targetas de Credito</title>
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

        </script> 
        <style>
            #mn307{
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
            <tr><td colspan="33"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="33" align="center">REPORTES POR TARJETA</td></tr>
            <tr>
                <td colspan="33"><?php echo 'Desde: ' . $desde . '    Hasta: ' . $hasta ?></td>
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
                <center class="cont_title" >REPORTES POR TARJETA</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="checkbox" id="vnd" name="vnd" <?php echo $vnd ?> />Por Vendedor
                        DESDE:<input type="text" id="desde" name="desde" size="12"  value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im_desde" />
                        HASTA:<input type="text" id="hasta" name="hasta" size="12"  value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im_hasta" />
                        <input type="submit" name="search" id="search" value="Buscar"  >
                    </form>  
                    <form id="exp_excel" style="float:right " method="post" action="../Includes/export.php"  >
                        <input type="submit" value="Excel" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th  name='col_ventas' ></th>
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        $n1 = 0;
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            $n1 = $n1 + pg_num_rows($cns_pag);
                        }
                        ?>
                        <th  colspan="<?php echo $n1 ?>" name='col_desg'><?php echo $rst_bh[banco] ?></th>
                        <?php
                    }
                    ?>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    $ch = 0;
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $ch++;
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            ?>
                            <th colspan="<?php echo pg_num_rows($cns_pag) ?>" name='col_desg'><?php echo $rst_tc[targeta] ?></th>
                            <?php
                        }
                    }
                    ?>
                    <th></th>            
                </tr>
                <tr>
                    <th>Locales/Vendedores</th>
                    <?php
                    $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                    $lng = 25;
                    while ($rst_bh = pg_fetch_array($cns_bh)) {
                        $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                        while ($rst_tc = pg_fetch_array($cns_tc)) {
                            $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                            while ($rst_pg = pg_fetch_array($cns_pag)) {
                                $lng++;
                                ?>
                                <th class="titulo" lang="<?php echo $lng ?>" name='col_desg'><?php echo $rst_pg[pago] ?></th>
                                <?php
                            }
                        }
                    }
                    ?>
                    <th class="titulo" lang="24" name='col_caja'>Totales</th>                                 
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
                    $cns_vendedor = $Rep->lista_vendedores_factura($rst_locales[emi_cod_punto_emision], $d, $h, 1);
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
                        <td colspan="50"></td>
                    </tr>
                    <?php
                    while ($rst_vnd = pg_fetch_array($cns_vendedor)) {
                        $tc = pg_fetch_array($Rep->lista_tot_tipo_pago($rst_locales[emi_cod_punto_emision], $d, $h, $rst_vnd[vendedor], '1'));
                        $tc[sum] = number_format($tc[sum], $dec);
                        $vnd = pg_fetch_array($Rep->lista_vendedores($rst_vnd[vendedor]));
                        ?>
                        <tr class="vendedores">
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $vnd[vnd_nombre] ?></td>
                            <?php
                            $cns_bh = $Rep->lista_bancos_desgloce_general_tc($d, $h);
                            $itm = 25;
                            while ($rst_bh = pg_fetch_array($cns_bh)) {
                                $cns_tc = $Rep->lista_tc_general($d, $h, $rst_bh[pag_banco]);
                                while ($rst_tc = pg_fetch_array($cns_tc)) {
                                    $cns_pag = $Rep->lista_pag_general($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta]);
                                    while ($rst_pg = pg_fetch_array($cns_pag)) {
                                        $itm++;
                                        $rst_vlr = pg_fetch_array($Rep->lista_valor_targeta_contado($d, $h, $rst_bh[pag_banco], $rst_tc[pag_tarjeta], $rst_pg[pag_contado], $rst_locales[emi_cod_punto_emision], $rst_vnd[vendedor]));
                                        $rst_vlr[sum] = number_format($rst_vlr[sum], $dec);
                                        ?>
                                        <td align="right" class="<?php echo 'itm' . $itm . $rst_locales[emi_cod_punto_emision] ?>" name='col_desg' ><?php echo $rst_vlr[sum] ?></td>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            <td align="right"  class="<?php echo 'itm24' . $rst_locales[emi_cod_punto_emision] ?>" name='col_caja'><?php echo $tc[sum] ?></td>                 
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
        cn = 0;
        i++;
        $('.titulo').each(function () {
            t = 0;
            cn++;
            if (cn > 0 && cn <= 9) {
                nm = 'col_ventas';
            } else if (cn > 9 && cn <= 15) {
                nm = 'col_devoluciones';
            } else if (cn > 16 && cn <= 25) {
                nm = 'col_caja';
            } else if (cn != 16) {
                nm = 'col_desg';
            } else {
                nm = '';
            }
            $('.itm' + this.lang + i).each(function () {
                t = ((t * 1) + ($(this).html().replace(/,/g, '') * 1));
            });
                t = accounting.formatMoney(t, "", dec, ",", ".");
            $("#tot" + i).append("<td align='right'  class='t" + this.lang + "' name='" + nm + "' >" + t + "</td>").html();
        });
    });
    cn = 0;
    $('.titulo').each(function () {
        t = 0;
        cn++;
        if (cn > 0 && cn <= 9) {
            nm = 'col_ventas';
        } else if (cn > 9 && cn <= 15) {
            nm = 'col_devoluciones';
        } else if (cn > 16 && cn <= 25) {
            nm = 'col_caja';
        } else if (cn != 16) {
            nm = 'col_desg';
        } else {
            nm = '';
        }
        $('.t' + this.lang).each(function () {
            t = ((t * 1) + ($(this).html().replace(/,/g, '') * 1));
        });
        $(".totales").append("<td align='right' name='" + nm + "' >" + accounting.formatMoney(t, "", dec, ",", ".") + "</td>").html();
    });

</script>

