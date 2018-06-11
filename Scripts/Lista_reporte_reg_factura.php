<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_registro_facturas.php';
$Set = new Set();
$Fac = new Clase_registro_facturas();
if (isset($_GET[txt])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($_GET[txt])) {
        $texto = "and (f.reg_num_registro like '%$txt%' or f.reg_num_documento like '%$txt%' or f.reg_ruc_cliente like '%$txt%') and reg_estado=1";
    } else {
        $texto = "and f.reg_femision between '$desde' and '$hasta' and reg_estado=1";
    }
    $cns = $Fac->lista_registros_factura($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $texto = "and f.reg_femision between '$desde' and '$hasta'  and reg_estado=1";
    $cns = $Fac->lista_registros_factura($texto);
}
/////////*******RESPUESTAS************
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            
            function exportar_excel() {
                $("#datatodisplay").val("");
                $("#tbl2 tbody").html("");
                $("#tbl2 tfoot").html("");
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }
        </script>  
        <style>
            #tbl_aux{
                position: fixed;                                     
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                display:none; 
                border-bottom:solid 1px #ccc  ;
            }
            #mensaje{
                position:fixed;
                top:50px;
                right:20px; 
            }
            .incorrecto{
                font-family:Arial, Helvetica, sans-serif; 
                border: 1px solid;
                margin: 10px 0px;
                padding:15px 10px 15px 50px;
                background-repeat: no-repeat;
                background-position: 10px center;
                color: #D8000C;
                background-color: #FFBABA !important;
            }
            #mn315{
                background:black;
                color:white;
                border: solid 1px white;
            }

        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="26"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr>
                    <td colspan="26"><?php echo 'Desde: ' . $desde . ' Hasta: ' . $hasta ?></td>
                </tr>
            </thead>
        </table>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <div id="mensaje" ondblclick="this.hidden = true"></div>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=11" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                        FACTURA:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" />
                        DESDE:<input type="text" size="15" name="desde" id="desde" value="<?php echo $desde ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="text" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="12">FACTURA COMPRA</th>
                    <th colspan="8">NOTA DE CREDITO COMPRA</th>
                    <th colspan="8">RETENCION</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th># Doc</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>ESTADO</th>
                    <th>SUB_T</th>
                    <th>DESCUENTO</th>
                    <th>SUBT0%</th>
                    <th>SUBT12%</th>
                    <th>IVA</th>
                    <th>TOTAL$</th>
                    <th># Doc</th>
                    <th>SUB_T</th>
                    <th>DESCUENTO</th>
                    <th>SUBT0%</th>
                    <th>SUBT12%</th>
                    <th>IVA</th>
                    <th>TOTAL$</th>
                    <th>(FACTURA-NC)</th>
                    <th># Doc</th>
                    <th>COD.IVA</th>
                    <th>IVA %</th>
                    <th>IVA $</th>
                    <th>COD.RENTA</th>
                    <th>RENTA %</th>
                    <th>RENTA $</th>
                    <th>VALOR</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                //FACTURA
                $fsubt = 0;
                $fdesc = 0;
                $fiva0 = 0;
                $fiva12 = 0;
                $ftotal = 0;
                $tiva = 0;
                //NC
                $ncsubt = 0;
                $ncdesc = 0;
                $nciva0 = 0;
                $nciva12 = 0;
                $nciva = 0;
                $nctotal = 0;
                $fcnctotal = 0;
                //Retencion
                $retiva = 0;
                $retrent = 0;
                $retval = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst[reg_estado] == 1) {
                        $estado = 'REGISTRADO';
                    } else if ($rst[reg_estado] == 2) {
                        $estado = 'PENDIENTE';
                    } else if ($rst[reg_estado] == 3) {
                        $estado = 'ANULADO';
                    }
                    $iva0 = ($rst[reg_sbt0] + $rst[reg_sbt_excento] + $rst[reg_sbt_noiva]);
                    $rst_nc = pg_fetch_array($Fac->lista_notcre_factura($rst[reg_id])); //Notas de credito
                    $rst_ret = pg_fetch_array($Fac->lista_retencion_factura($rst[reg_id])); //Retenciones
                    $det_ret = pg_fetch_array($Fac->lista_det_ret($rst_ret[ret_id]));
                    
                    $ret_iva = round($det_ret[iva],2);
                    $ret_ren = round($det_ret[renta],2);

                    $nc_iva0 = number_format($rst_nc[rnc_subtotal0] + $rst_nc[rnc_subtotal_ex_iva] + $rst_nc[rnc_subtotal_no_iva], 2);
                    $nc_tot = ($rst[reg_total] - $rst_nc[rnc_total_valor]);

                    $fsubt+=$rst[reg_sbt];
                    $fdesc+=$rst[reg_tdescuento];
                    $fiva0+=$iva0;
                    $fiva12+=$rst[reg_sbt12];
                    $ftotal+=$rst[reg_total];
                    $tiva += $rst[reg_iva12];
                    //NC
                    $ncsubt+=$rst_nc[rnc_subtotal];
                    $ncdesc+=$rst_nc[rnc_total_descuento];
                    $nciva0+=$nc_iva0;
                    $nciva12+=$rst_nc[rnc_subtotal12];
                    $nciva+=$rst_nc[rnc_total_iva];
                    $nctotal+=$rst_nc[rnc_total_valor];
                    $fcnctotal+=($rst[reg_total] - $rst_nc[rnc_total_valor]);
                    //Retencion
                    $retiva+=$ret_iva;
                    $retrent+=$ret_ren;
                    $retval+=$rst_ret[ret_total_valor];
                    $style='mso-number-format:"@"';
                    echo "<tr >
                        <td> $n</td>
                        <td>$rst[reg_femision]</td>
                        <td>$rst[reg_num_documento]</td>
                        <td>$rst[cli_raz_social]</td>
                        <td style='$style'>$rst[reg_ruc_cliente]</td>
                        <td>$estado</td>
                        <td align=right >" . number_format($rst[reg_sbt], 2) . "</td>
                        <td align=right >" . number_format($rst[reg_tdescuento], 2) . "</td>                            
                        <td align=right >" . number_format($iva0, 2) . "</td>                                
                        <td align=right >" . number_format($rst[reg_sbt12], 2) . "</td>
                        <td align=right >" . number_format($rst[reg_iva12], 2) . "</td>    
                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[reg_total], 2) . "</td>
                        <td>$rst_nc[rnc_numero]</td>
                        <td align='right' >" . number_format($rst_nc[rnc_subtotal], 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[rnc_total_descuento], 2) . "</td>    
                        <td align='right' >" . number_format($nc_iva0, 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[rnc_subtotal12], 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[rnc_total_iva], 2) . "</td>    
                        <td align='right' >" . number_format($rst_nc[rnc_total_valor], 2) . "</td>
                        <td align='right' >" . number_format($nc_tot, 2) . "</td>    
                        <td >$rst_ret[ret_numero]</td>
                        <td align='right' >" . $det_ret[cod_iva] . "</td>
                        <td align='right' >" . number_format($det_ret[p_iva], 2) . "</td>
                        <td align='right' >" . number_format($ret_iva, 2) . "</td>
                        <td align='right' >" . $det_ret[cod_renta] . "</td>    
                        <td align='right' >" . number_format($det_ret[p_renta], 2) . "</td>    
                        <td align='right' >" . number_format($ret_ren, 2) . "</td>    
                        <td align='right' >" . number_format($rst_ret[ret_total_valor], 2) . "</td>        
                       </tr>";
                }
                echo"</tbody>
            <tr style='font-weight:bolder'>
                <td colspan='6' align='right'>Total</td>
                <td align='right' style='font-size:14px;'>" . number_format($fsubt, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($fdesc, 2) . "</td>    
                <td align='right' style='font-size:14px;'>" . number_format($fiva0, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($fiva12, 2) . "</td>    
                <td align='right' style='font-size:14px;'>" . number_format($tiva, 2) . "</td>                        
                <td align='right' style='font-size:14px;'>" . number_format($ftotal, 2) . "</td>
                <td align='right' style='font-size:14px;'></td>    
                <td align='right' style='font-size:14px;'>" . number_format($ncsubt, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($ncdesc, 2) . "</td>    
                <td align='right' style='font-size:14px;'>" . number_format($nciva0, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($nciva12, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($nciva, 2) . "</td>    
                <td align='right' style='font-size:14px;'>" . number_format($nctotal, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($fcnctotal, 2) . "</td>    
                <td align='right' style='font-size:14px;'></td>
                <td align='right' style='font-size:14px;'></td>
                <td align='right' style='font-size:14px;'></td>
                <td align='right' style='font-size:14px;'>" . number_format($retiva, 2) . "</td>  
                <td align='right' style='font-size:14px;'></td>
                <td align='right' style='font-size:14px;'></td>
                <td align='right' style='font-size:14px;'>" . number_format($retrent, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($retval, 2) . "</td>    
            </tr>";
                ?>
        </table>            
    </body>    
</html>

