<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
$Fac = new Clase_factura();

if (isset($_GET[search])) {
    $date1 = $_GET[desde];
    $date2 = $_GET[hasta];
//    $datetime1 = new DateTime($date1);
//    $datetime2 = new DateTime($date2);
//    $interval = $datetime1->diff($datetime2);
//    echo $dia = $interval->format('%R%a');
    $fec = strtotime($date2) - strtotime($date1);
    $dias = floor($fec / 86400);
    $emi = $_GET[emisor];
} else {
    $date1 = date('Y-m-d');
    $date2 = date('Y-m-d');
}

function return_dia($fecha) {
    $day = '';
    $dia = date('w', strtotime("$fecha"));
    switch ($dia) {
        case 1:
            $day = 'Lunes';
            break;
        case 2:
            $day = 'Martes';
            break;
        case 3:
            $day = 'Miercoles';
            break;
        case 4:
            $day = 'Jueves';
            break;
        case 5:
            $day = 'Viernes';
            break;
        case 6:
            $day = 'Sabado';
            break;
        case 0:
            $day = 'Domingo';
            break;
        default :
            $day = $dia;
            break;
    }
    return $day;
}
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
            #mn325{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .tot_sem{
                background: #d4e0e4 !important;;
                color:brown; 
                font-weight:bolder;
            }
            .gtot{
                color: #00529B !important;
                background-color: #BDE5F8 !important;
                font-weight:bolder;
            }
            .gtot td{
                font-size:13px !important; 
            }

            #tbl_total thead tr th{
                background:#BDE5F8;
                color:brown; 
                padding: 5px;
                border:solid 1px #cccccc; 
            }
            #tbl_total tbody tr td{
                background:#f0f5ff;
                font-size:10px;
                padding: 5px;
                border:solid 1px #cccccc; 
            }
            #tbl_total{
                border:solid 2px  #00529B !important; 
            }
            .separador{
                color: #00529B !important;
                background-color: #BDE5F8 !important;
                font-weight:bolder;
            }
        </style>
    </head>
    <body>

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
                </center>  
                <center class="cont_title" >REPORTE POR FORMAS DE PAGO DIARIO</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        LOCAL:<select name="emisor" id="emisor">
                            <option value="0">Todos</option>
                            <?php
                            $cns_loc = $Fac->lista_locales();
                            while ($rst_loc = pg_fetch_array($cns_loc)) {
                                echo "<option value='$rst_loc[emi_id]'>$rst_loc[emi_nombre_comercial]</option>";
                            }
                            ?>
                        </select>
                        DESDE:<input type="date" size="15" name="desde" id="desde" style="text-align:right" value="<?php echo $date1 ?>" />
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="date" size="15" name="hasta" id="hasta" style="text-align:right" value="<?php echo $date2 ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="13">CIERRE DE CAJA</th>
                </tr>
                <tr>
                    <th>SEMANA</th>
                    <th>FECHA</th>
                    <th>DIA</th>                    
                    <th>TC</th>
                    <th>TD</th>
                    <th>CHEQUE</th>
                    <th>EFECTIVO</th>
                    <th>CERTIFICADOS</th>
                    <th>BONOS</th>
                    <th>RETENCION</th>
                    <th>NOTA DE CREDITO</th>
                    <th>CREDITO</th>
                    <th>TOTAL CAJA</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?php
                if ($emi > 0) {
                    $cns_emi = $Fac->lista_emisor($emi);
                } else {
                    $cns_emi = $Fac->lista_locales();
                }

                while ($rst_emi = pg_fetch_array($cns_emi)) {
                    $emi_id = $rst_emi[emi_id];
                    $emi_nombre = $rst_emi[emi_nombre_comercial];
                    $n = 0;
                    $wek = '';
                    $t_s_tc = 0;
                    $t_s_td = 0;
                    $t_s_ch = 0;
                    $t_s_ef = 0;
                    $t_s_cer = 0;
                    $t_s_bon = 0;
                    $t_s_ret = 0;
                    $t_s_nc = 0;
                    $t_s_cre = 0;
                    $t_s_tcaja = 0;
                    $gt_s_tc = 0;
                    $gt_s_td = 0;
                    $gt_s_ch = 0;
                    $gt_s_ef = 0;
                    $gt_s_cer = 0;
                    $gt_s_bon = 0;
                    $gt_s_ret = 0;
                    $gt_s_nc = 0;
                    $gt_s_cre = 0;
                    $gt_s_tcaja = 0;
                    echo "<tr><th colspan='13' align=center class='separador'>$emi_nombre</th></tr>";
                    while ($n <= $dias) {
                        $fecha = date('Y-m-d', strtotime("$date1 + $n day"));
                        $dia = return_dia($fecha);
                        $week = date("W", strtotime($fecha));
                        $rst_pagos = pg_fetch_array($Fac->lista_pagos_fecha_emisor($fecha, $emi_id));
                        $tcaja = ($rst_pagos[tc] + $rst_pagos[td] + $rst_pagos[ch] + $rst_pagos[ef] + $rst_pagos[cer] + $rst_pagos[bon] + $rst_pagos[ret] + $rst_pagos[nc] + $rst_pagos[cre]);
                        $n++;

                        if ($wek != $week && $n > 1) {
                            echo "<tr class='tot_sem'>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td align=right >" . number_format($t_s_tc, 2) . "</td>
                            <td align=right >" . number_format($t_s_td, 2) . "</td>    
                            <td align=right >" . number_format($t_s_ch, 2) . "</td>
                            <td align=right >" . number_format($t_s_ef, 2) . "</td>    
                            <td align=right >" . number_format($t_s_cer, 2) . "</td>
                            <td align=right >" . number_format($t_s_bon, 2) . "</td>    
                            <td align=right >" . number_format($t_s_ret, 2) . "</td>
                            <td align=right >" . number_format($t_s_nc, 2) . "</td>    
                            <td align=right >" . number_format($t_s_cre, 2) . "</td>
                            <td align=right >" . number_format($t_s_tcaja, 2) . "</td>    
                            </tr>";
                            $gt_s_tc += $t_s_tc;
                            $gt_s_td += $t_s_td;
                            $gt_s_ch += $t_s_ch;
                            $gt_s_ef += $t_s_ef;
                            $gt_s_cer += $t_s_cer;
                            $gt_s_bon += $t_s_bon;
                            $gt_s_ret += $t_s_ret;
                            $gt_s_nc += $t_s_nc;
                            $gt_s_cre += $t_s_cre;
                            $gt_s_tcaja += $t_s_tcaja;

                            $t_s_tc = 0;
                            $t_s_td = 0;
                            $t_s_ch = 0;
                            $t_s_ef = 0;
                            $t_s_cer = 0;
                            $t_s_bon = 0;
                            $t_s_ret = 0;
                            $t_s_nc = 0;
                            $t_s_cre = 0;
                            $t_s_tcaja = 0;
                        }
                        $t_s_tc+=$rst_pagos[tc];
                        $t_s_td+=$rst_pagos[td];
                        $t_s_ch+=$rst_pagos[ch];
                        $t_s_ef+=$rst_pagos[ef];
                        $t_s_cer+=$rst_pagos[cer];
                        $t_s_bon+=$rst_pagos[bon];
                        $t_s_ret+=$rst_pagos[ret];
                        $t_s_nc+=$rst_pagos[nc];
                        $t_s_cre+=$rst_pagos[cre];
                        $t_s_tcaja+=$tcaja;
                        echo " <tr>
                        <td align=center >$week</td>
                        <td align=center >$fecha</td>                            
                        <td>$dia</td>
                        <td align=right >" . number_format($rst_pagos[tc], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[td], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[ch], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[ef], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[cer], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[bon], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[ret], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[nc], 2) . "</td>
                        <td align=right >" . number_format($rst_pagos[cre], 2) . "</td>
                        <td align=right >" . number_format($tcaja, 2) . "</td>
                    </tr>";
                        $wek = $week;
                    }
                    echo "<tr class='tot_sem'>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td align=right >" . number_format($t_s_tc, 2) . "</td>
                            <td align=right >" . number_format($t_s_td, 2) . "</td>    
                            <td align=right >" . number_format($t_s_ch, 2) . "</td>
                            <td align=right >" . number_format($t_s_ef, 2) . "</td>    
                            <td align=right >" . number_format($t_s_cer, 2) . "</td>
                            <td align=right >" . number_format($t_s_bon, 2) . "</td>    
                            <td align=right >" . number_format($t_s_ret, 2) . "</td>
                            <td align=right >" . number_format($t_s_nc, 2) . "</td>    
                            <td align=right >" . number_format($t_s_cre, 2) . "</td>
                            <td align=right >" . number_format($t_s_tcaja, 2) . "</td>    
                            </tr>";
                    $gt_s_tc += $t_s_tc;
                    $gt_s_td += $t_s_td;
                    $gt_s_ch += $t_s_ch;
                    $gt_s_ef += $t_s_ef;
                    $gt_s_cer += $t_s_cer;
                    $gt_s_bon += $t_s_bon;
                    $gt_s_ret += $t_s_ret;
                    $gt_s_nc += $t_s_nc;
                    $gt_s_cre += $t_s_cre;
                    $gt_s_tcaja += $t_s_tcaja;
                    echo "<tr class='gtot'>
                            <td>Total</td>
                            <td></td>
                            <td></td>
                            <td align=right >" . number_format($gt_s_tc, 2) . "</td>
                            <td align=right >" . number_format($gt_s_td, 2) . "</td>    
                            <td align=right >" . number_format($gt_s_ch, 2) . "</td>
                            <td align=right >" . number_format($gt_s_ef, 2) . "</td>    
                            <td align=right >" . number_format($gt_s_cer, 2) . "</td>
                            <td align=right >" . number_format($gt_s_bon, 2) . "</td>    
                            <td align=right >" . number_format($gt_s_ret, 2) . "</td>
                            <td align=right >" . number_format($gt_s_nc, 2) . "</td>    
                            <td align=right >" . number_format($gt_s_cre, 2) . "</td>
                            <td align=right >" . number_format($gt_s_tcaja, 2) . "</td>    
                            </tr>";
                    ?>
                    <tr>
                        <td colspan="13" align="center" style="background:whitesmoke">
                            <table id="tbl_total" >
                                <thead>
                                    <tr>
                                        <th colspan="2"><?php echo "Resumen " . $emi_nombre ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>TC</td>
                                        <td align="right"><?php echo number_format($gt_s_tc, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>TD</td>
                                        <td align="right"><?php echo number_format($gt_s_td, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>CHEQUE</td>
                                        <td align="right"><?php echo number_format($gt_s_ch, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>EFECTIVO</td>
                                        <td align="right"><?php echo number_format($gt_s_ef, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>CERTIFICADOS</td>
                                        <td align="right"><?php echo number_format($gt_s_cer, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>BONOS</td>
                                        <td align="right"><?php echo number_format($gt_s_bon, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>RETENCION</td>
                                        <td align="right"><?php echo number_format($gt_s_ret, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>NOTA DE CREDITO</td>
                                        <td align="right"><?php echo number_format($gt_s_nc, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>CREDITO</td>
                                        <td align="right"><?php echo number_format($gt_s_cre, 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL CAJA</td>
                                        <td align="right"><?php echo number_format($gt_s_tcaja, 2) ?></td>
                                    </tr>                
                                </tbody>

                            </table>                        
                        </td>
                    </tr>
                </tbody>   
                <?php
            }
            ?>
        </table>

    </body>    
</html>

