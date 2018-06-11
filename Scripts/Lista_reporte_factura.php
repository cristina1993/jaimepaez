<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_factura.php';
$Set = new Set();
$Fac = new Clase_factura();
if (isset($_GET[txt])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($_GET[txt])) {
        $texto = "where (fac_numero like '%$txt%' or fac_nombre like '%$txt%' or fac_identificacion like '%$txt%') and (fac_estado_aut<>'ANULADO' OR  fac_estado_aut is null)";
    } else {
        $texto = "where fac_fecha_emision between '$desde' and '$hasta' and (fac_estado_aut<>'ANULADO' OR  fac_estado_aut is null)";
    }
    $cns = $Fac->lista_factura_completo($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $texto = "where fac_fecha_emision between '$desde' and '$hasta' and (fac_estado_aut<>'ANULADO' OR  fac_estado_aut is null)";
    $cns = $Fac->lista_factura_completo($texto);
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


            function cargar_claves() {
                $.ajax({
                    beforeSend: function () {
                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 71}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        window.location = 'Lista_factura_completo.php';
                    }
                })
            }
            function insert_na() {
                ca = clave_acceso.value;
                na = num_auto.value;
                fh = fh_auto.value;
                $.ajax({
                    beforeSend: function () {
                        if (ca.length == 0) {
                            $("#clave_acceso").css({borderColor: "red"});
                            $("#clave_acceso").focus();
                            return false;
                        } else if (na.length != 37) {
                            $("#num_auto").css({borderColor: "red"});
                            $("#num_auto").focus();
                            return false;
                        } else if (fh.length == 0) {
                            $("#fh_auto").css({borderColor: "red"});
                            $("#fh_auto").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_factura_nuevo.php',
                    data: {op: 3, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_reporte_factura.php';
                        } else {
                            alert(dt);
                        }

                    }
                });
            }
            function cargar_datos(ca, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                clave_acceso.value = ca;
                factura.value = fa;
            }
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
                <tr><th colspan="23"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr>
                    <td colspan="23"><?php echo 'Desde: ' . $desde . ' Hasta: ' . $hasta ?></td>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=14" onsubmit="return exportar_excel()"  >
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
                    <th colspan="11">FACTURA</th>
                    <th colspan="8">NOTA DE CREDITO</th>
                    <th colspan="4">RETENCION</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th># Doc</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
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
                    <th>IVA</th>
                    <th>RENTA</th>
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
//CONTROL DE ERRORES ***********************
                    if (strlen(trim($rst[fac_autorizacion])) == 37) {
                        $estdado = 'RECIBIDA AUTORIZADA';
                        $class = '';
                    } else {
                        $class = 'incorrecto';
                        $err1 = strpos($rst[fac_estado_aut], 'SIN CONEXION');
                        if ($err1 == true) {
                            $estdado = 'SIN CONEXION';
                        } else {
                            $estdado = substr($rst[fac_estado_aut], 0, 30);
                        }
                    }

                    if (strlen($rst[fac_xml_doc]) < 100) {
                        $xml = '';
                    } else {
                        $xml = 'Si';
                    }
                    $iva0 = (round($rst[fac_subtotal0], 2) + round($rst[fac_subtotal_ex_iva], 2) + round($rst[fac_subtotal_no_iva], 2));
                    $rst_nc = pg_fetch_array($Fac->lista_notcre_factura($rst[fac_numero], 1)); //Notas de credito
                    $rst_ret = pg_fetch_array($Fac->lista_retencion_factura($rst[fac_numero], 1)); //Retenciones
                    $det_ret = pg_fetch_array($Fac->lista_det_ret($rst_ret[rgr_id]));

                    $ret_iva = round($det_ret[iva], 2);
                    $ret_ren = round($det_ret[renta], 2);

                    $nc_iva0 = number_format(round($rst_nc[ncr_subtotal0], 2) + round($rst_nc[ncr_subtotal_ex_iva], 2) + round($rst_nc[ncr_subtotal_no_iva], 2), 2);
                    $nc_tot = (round($rst[fac_total_valor], 2) - round($rst_nc[nrc_total_valor], 2));

                    $fsubt+=round($rst[fac_subtotal], 2);
                    $fdesc+=round($rst[fac_total_descuento], 2);
                    $fiva0+=round($iva0, 2);
                    $fiva12+=round($rst[fac_subtotal12], 2);
                    $ftotal+=round($rst[fac_total_valor], 2);
                    $tiva += round($rst[fac_total_iva], 2);
                    //NC
                    $ncsubt+=round($rst_nc[ncr_subtotal], 2);
                    $ncdesc+=round($rst_nc[ncr_total_descuento], 2);
                    $nciva0+=round($nc_iva0, 2);
                    $nciva12+=round($rst_nc[ncr_subtotal12], 2);
                    $nciva+=round($rst_nc[ncr_total_iva], 2);
                    $nctotal+=round($rst_nc[nrc_total_valor], 2);
                    $fcnctotal+=(round($rst[fac_total_valor], 2) - round($rst_nc[nrc_total_valor], 2));
                    //Retencion
                    $retiva+=round($ret_iva, 2);
                    $retrent+=round($ret_ren, 2);
                    $retval+=round($rst_ret[rgr_total_valor], 2);
                    $style = 'mso-number-format:"@"';
                    echo "<tr >
                        <td> $n $rst_ret[ret_id]</td>
                        <td>$rst[fac_fecha_emision]</td>
                        <td>$rst[fac_numero]</td>
                        <td>$rst[fac_nombre]</td>
                        <td  style='$style'>$rst[fac_identificacion]</td>
                        <td align=right >" . number_format($rst[fac_subtotal], 2) . "</td>
                        <td align=right >" . number_format($rst[fac_total_descuento], 2) . "</td>                            
                        <td align=right >" . number_format($iva0, 2) . "</td>                                
                        <td align=right >" . number_format($rst[fac_subtotal12], 2) . "</td>
                        <td align=right >" . number_format($rst[fac_total_iva], 2) . "</td>    
                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[fac_total_valor], 2) . "</td>
                        <td>$rst_nc[ncr_numero]</td>
                        <td align='right' >" . number_format($rst_nc[ncr_subtotal], 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[ncr_total_descuento], 2) . "</td>    
                        <td align='right' >" . number_format($nc_iva0, 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[ncr_subtotal12], 2) . "</td>
                        <td align='right' >" . number_format($rst_nc[ncr_total_iva], 2) . "</td>    
                        <td align='right' >" . number_format($rst_nc[nrc_total_valor], 2) . "</td>
                        <td align='right' >" . number_format($nc_tot, 2) . "</td>    
                        <td >$rst_ret[rgr_numero]</td>
                        <td align='right' >" . number_format($ret_iva, 2) . "</td>
                        <td align='right' >" . number_format($ret_ren, 2) . "</td>    
                        <td align='right' >" . number_format($rst_ret[rgr_total_valor], 2) . "</td>        
                       </tr>";
                }
                echo"</tbody>
            <tr style='font-weight:bolder'>
                <td colspan='5' align='right'>Total</td>
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
                <td align='right' style='font-size:14px;'>" . number_format($retiva, 2) . "</td>    
                <td align='right' style='font-size:14px;'>" . number_format($retrent, 2) . "</td>
                <td align='right' style='font-size:14px;'>" . number_format($retval, 2) . "</td>    
            </tr>";
                ?>
        </table>            
    </body>    
</html>

