<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_registro_costos.php';
$Docs = new Clase_registro_costos();
$dec = 4;
$dc = 2;
if (isset($_GET[txt])) {
    $txt = $_GET[txt];
    if (strlen(trim($txt)) != 0) {
        $cns = $Docs->lista_productos_documentos($txt);
        $cns_prod = $Docs->lista_productos_consventa($txt);
        $cns_por = $Docs->lista_productos_consventa($txt);
        $cns_por2 = $Docs->lista_productos_consventa($txt);
        $cns_por3 = $Docs->lista_productos_consventa($txt);
        $cns_fac = $Docs->lista_factura($txt);
        $t_fac = pg_fetch_array($Docs->lista_suma_factura_imp($txt));
    } else {
        $t_fac[sum] = 0;
    }
} else {
    $t_fac[sum] = 0;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var dec =<?php echo $dec ?>;
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function update(id, costo, pro) {
                var r = confirm("Esta Seguro de realizar los cambios?");
                if (r == true) {
                    fecha = '<?php echo date('Y-m-d') ?>';
                    importacion = '<?php echo $txt ?>';
                    var data = Array(costo,
                            '0',
                            fecha,
                            importacion);
                    var fields = Array('producto=' + pro,
                            'costo=' + costo,
                            'fecha=' + fecha,
                            'importacion=' + importacion,
                            '');
                    $.post("actions_preciosmp.php", {op: 8, 'data[]': data, 'fields[]': fields, id: id},
                    function (dt) {
                        dat = dt.split('&')
                        if (dat[0] == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_costos.php?txt=<?php echo $txt ?>';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function suma_factura() {
                n = 0;
                var vt = 0;
                var vt2 = 0;
                var gr = '';
                $('.check').each(function () {
                    n++;
                    id = this.id;

                    id2 = id.split('-');
                    vl = '#' + n + 'val' + id2[1];
                    chk = '#' + n + 'chk' + id2[1];
                    rub = '#' + n + 'rub' + id2[1];
                    if (id2[1] != gr) {
                        rb = parseFloat($(rub).val());
                    } else {
                        rb = 0;
                    }
                    if ($(chk).attr('checked') == true) {
                        val = parseFloat($(vl).html()) + rb;
                        val2 = parseFloat($(vl).html());
                    } else if ($(chk).attr('checked') == false) {
                        val = 0 + rb;
                        val2 = 0;
                    } else {
                        val = parseFloat($(vl).html()) + rb;
                        val2 = 0;
                    }
                    vt = vt + val;
                    vt2 = vt2 + val2;
                    gr = id2[1];
                });
                $('#tot_fac_imp').html(vt.toFixed(dec));
                $('#tot_imp').html(vt2.toFixed(dec));
                l = 0;
                $('.por_aporte').each(function () {
                    l++
                    porcentaje = parseFloat($('#por2_' + l).html());
                    if (vt > 0) {
                        importacion = (porcentaje * vt) / 100;
                        costo = importacion / parseFloat($('#cnt' + l).html());
                        incr = costo / parseFloat($('#p_uni' + l).html());
                        $('#import' + l).html(importacion.toFixed(dec));
                        $('#costo_uni' + l).html(costo.toFixed(dec));
                        $('#incremento' + l).html(incr.toFixed(dec));
                    } else {
                        $('#import' + l).html(parseFloat(0).toFixed(dec));
                        $('#costo_uni' + l).html(parseFloat(0).toFixed(dec));
                        $('#incremento' + l).html(parseFloat(0).toFixed(dec));
                    }

                });
            }

        </script> 
        <style>
            #mn180{
                background:black;
                color:white;
                border: solid 1px white;
            }


        </style>
    </head>
    <body>

        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:50%" id="tbl">
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
                <center class="cont_title" ><?php echo "COSTOS IMPORTACION" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" >
                        # IMPORTACION:<input type="text" name="txt" size="20" id="txt" value="<?php echo $txt ?>" list="lista_importacion"/>
                        <datalist id="lista_importacion">
                            <?php
                            $cns_exp = $Docs->lista_importacion();
                            while ($rst_imp = pg_fetch_array($cns_exp)) {
                                echo "<option value='$rst_imp[reg_importe]'>$rst_imp[reg_importe]</option>";
                            }
                            ?>
                        </datalist>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                             
                    </form>  
                </center>
            </caption>
            <tr>
                <td>
                    <table style="width: 100%">
                        <!--Nombres de la columna de la tabla-->
                        <thead>
                        <th>No</th>
                        <th>DOCUMENTO</th>
                        <th>CANTIDAD</th>                                
                        <th>DESCRIPCION</th>                                
                        <th>V.UNIT</th>
                        <th>V.TOTAL</th>
                        <th>APLICA COSTOS</th>
                        </thead>
                        <!------------------------------------->

                        <?PHP
                        $n = 0;
                        $g_style = 'background-color:#FF8080;';
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;
                            $rst_st = pg_fetch_array($Docs->lista_un_producto($rst[pro_id]));
                            $rst_fc = pg_fetch_array($Docs->lista_una_factura($rst[reg_id]));
//                            $rub = $rst_fc[reg_iva12] + $rst_fc[reg_irbpnr] + $rst_fc[reg_ice] + $rst_fc[reg_propina];
                            $rub = 0;
                            if (!empty($rst_st)) {
                                $style = 'background-color:#FF8080;';
                                $chk = '';
                                $vrc+= round($rst[det_cantidad], $dc);
                                $vru+= round($rst[det_vunit], $dec);
                                $vrt+= round($rst[det_total], $dec);
                                $x++;
                            } else {
                                $style = '';
                                $chk = "<input type='checkbox'  checked id='$n" . 'chk' . "$rst[reg_id]' onclick='suma_factura($rub)'>";
                            }
                            ?>
                            <?PHP
                            if ($style != $g_style && $x>1) {
                                ?>
                                <tr style="font-weight: bolder">
                                    <td colspan="2" align="right">Total </td>
                                    <td align="right"><?php echo str_replace(',', '', number_format($vrc, $dc)) ?></td>
                                    <td align="right"> </td>
                                    <td align="right"></td>
                                    <td align="right"><?php echo str_replace(',', '', number_format($vrt, $dec)) ?></td>
                                    <td colspan="2" align="right"></td>
                                </tr>
                                <?PHP
                            }
                            $g_style = $style;
                        
                        ?>
                        <tr style="<?php echo $style ?>">
                            <td><?php echo $n ?></td>
                            <td><?php echo $rst[reg_num_documento] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[det_cantidad], $dc)) ?></td>
                            <td><?php echo $rst[det_descripcion] ?></td>
                            <td align="right"><?php echo str_replace(',', '', number_format($rst[det_vunit], $dec)) ?></td>
                            <td align="right" id="<?php echo $n . 'val' . $rst[reg_id] ?>"><?php echo str_replace(',', '', number_format($rst[det_total], $dec)) ?></td>
                            <td align="center" class='check' id='<?php echo $n . '-' . $rst[reg_id] ?>'><?php echo $chk ?></td>
                            <td hidden><input type="hidden" id="<?php echo $n . 'rub' . $rst[reg_id] ?>" value="<?php echo $rub ?>"></td>
                        </tr>  

                        <?PHP
                        }
                        
                        ?>
                        <tr style="font-weight: bolder">
                            <td colspan="5" align="right">Total  de Facturas</td>
                            <td align="right" id="tot_imp"><?php echo str_replace(',', '', number_format($t_fac[sum]-$vrt, $dec)) ?></td>
                            <td colspan="2" align="right"></td>
                        </tr>
                        <tr style="font-weight: bolder">
                            <td colspan="5" align="right">Total General de Facturas</td>
                            <td align="right" id="tot_fac_imp"><?php echo str_replace(',', '', number_format($t_fac[sum], $dec)) ?></td>
                            <td colspan="2" align="right"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%">
                        <thead>
                            <tr>
                                <th >No</th>
                                <th >CANTIDAD</th>
                                <th >DESCRIPCION</th>
                                <th >V.UNIT</th>
                                <th >V.TOT</th>
                                <th >% APORTE</th>
                                <th >V.TOT.IMPORTACION </th>
                                <th >NUEVO V.UNIT</th>
                                <th >INCREMENTO</th>
                                <th colspan="2">ACCION</th>
                            </tr>
                        </thead>
                        <?PHP
                        $sum = pg_fetch_array($Docs->suma_total_importacion($txt));
                        $total = $sum[total];
                        $n = 0;
                        while ($rst_pro = pg_fetch_array($cns_por2)) {
                            $n++;
                            $porcentaje = round(($rst_pro[total] / $total) * 100, $dec);
                            $importacion = ($porcentaje * $t_fac[sum]) / 100;
                            $costo = $importacion / $rst_pro[cant];
                            $incremento = ($costo / $rst_pro[vunit]);
                            $rst_cost = pg_fetch_array($Docs->lista_un_producto($rst_pro[pro_id]));
                            if (!empty($rst_cost[mp_y]) && !empty($rst_cost[mp_z])) {
                                $sms = 'Ultima modificacion ' . $rst_cost[mp_y];
                            } else {
                                $sms = '';
                            }
                            ?>
                            <tr>
                                <td class="por_aporte"><?php echo $n ?></td>
                                <td align="right" id='cnt<?php echo $n ?>'><?php echo str_replace(',', '', number_format($rst_pro[cant], $dc)) ?></td>
                                <td ><?php echo $rst_pro[det_descripcion] ?></td>
                                <td align="right" id='p_uni<?php echo $n ?>'><?php echo str_replace(',', '', number_format($rst_pro[vunit], $dec)) ?></td>
                                <td align="right" id='p_tot<?php echo $n ?>'><?php echo str_replace(',', '', number_format($rst_pro[total], $dec)) ?></td>
                                <td align="right" id='por2_<?php echo $n ?>'><?php echo str_replace(',', '', number_format($porcentaje, $dec)) ?></td>
                                <td align="right" id='import<?php echo $n ?>'><?php echo str_replace(',', '', number_format($importacion, $dec)) ?></td>
                                <td align="right" id='costo_uni<?php echo $n ?>'><?php echo str_replace(',', '', number_format($costo, $dec)) ?></td>
                                <td align="right" id='incremento<?php echo $n ?>'><?php echo str_replace(',', '', number_format($incremento, $dec)) ?></td>
                                <td colspan="2" style="width: 400px"><input type="button" value="APROBAR" onclick="update(<?php echo $rst_pro[pro_id] ?>, $('#costo_uni<?php echo $n ?>').html(), '<?php echo $rst_pro[det_descripcion] ?>')"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $sms ?></td>
                            </tr>  
                            <?PHP
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>

