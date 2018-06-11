<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_inventariopt.php'; //cambiar clsClase_productos
$Clase_industrial_inventariopt = new Clase_industrial_inventariopt();
$cns_comb = $Clase_industrial_inventariopt->lista_un_mp_mod1();
if ($ctr_inv == 0) {
    $fra = '';
} else {
    $fra = "and m.bod_id=$emisor";
}
if (isset($_GET[txt], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec2 = $_GET[fecha2];
    $ids = $_GET[ids];
    if ($ids == '') {
        $ids = 'no';
    }
    if (!empty($txt)) {
        $text = " where m.pro_id=p.id $fra and (p.mp_c like '%$txt%' or p.mp_d like '%$txt%') and p.mp_i='0' and p.ids='$ids'";
    } else {
        $text = " where m.pro_id=p.id $fra and m.mov_fecha_trans between '2000-01-01' and '$fec2' and p.mp_i='0' and p.ids='$ids'";
    }
    $cns = $Clase_industrial_inventariopt->lista_buscar_inventariopt($text);
} else {
    $txt = '';
    $fec1 = '2000-01-01';
    $fec2 = date("Y-m-d");
}
if ($ids == '26') {
    $exc = 4;
} else {
    $exc = 5;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var ids = '<?php echo $ids ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (ids == 'no') {
                    alert('Elija tipo');
                }
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function imprimir() {
                $('.cont_finder').hide();
                $('.cont_menu').hide();
                window.print();
                $('.cont_finder').show();
                $('.cont_menu').show();
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
            #mn197{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .sel{
                font-size: 11px;
                width: 100px;
            }
            .totales{
                height: 20px;
                background: #7198ab !important;
                color:white !important;
                font-size:12px !important;
                font-weight:bolder; 
            }
            .subtotal{
                height: 20px;
                background: #9bb6c4 !important;
                color:white !important;
                font-size:12px !important;
                font-weight:bolder; 
            }
            .total_fin{
                height: 20px;
                background: #616975 !important;
                color:white !important;
                font-size:12px !important;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="5"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="5" align="center"><?PHP echo 'INVENTARIO ' . $bodega ?></th></tr>
                <tr>
                    <td colspan="5"><?php echo 'Hasta: ' . $fec2 ?></td>
                </tr>
            </thead>
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
                    <img class="auxBtn" style="float:right" onclick="imprimir()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=<?php echo $exc ?>" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?PHP echo 'INVENTARIO ' . $bodega ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        TIPO:<select id="ids" name="ids" class="sel">
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                $dt = explode('&', $rst_c[mp_tipo]);
                                ?>
                                <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        CODIGO:<input type="text" name="txt" size="15" id="txt"/>
                        AL:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2; ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="4"></th>
                    <th>Totales</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Codigo</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $i = 0;
                $gr_a = 0;
                $gr_b = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $i++;
                    $cod = $rst[mp_c];
                    $des = $rst[mp_d];
                    $uni = $rst[mp_q];
                    $rst_inv = pg_fetch_array($Clase_industrial_inventariopt->total_ingreso_egreso_fac($rst[pro_id], $fec2, $fra));
                    $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                    $tot_inv+=$inv;
                    ?>
                    <?PHP
                    if (($gr_a != $rst[mp_a] || $gr_b != $rst[mp_b]) && $i != 1) {
                        $rst_b = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_b));
                        $rst_ab = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_a));
                        ?>
                        <tr style="height: 20px;" class="subtotal" id="fila">
                            <td colspan="3"><?php echo $rst_ab[tps_nombre] . '  ' . $rst_b[tps_nombre] ?></td>
                            <td><?php echo 'TOTAL ' . $rst_ab[tps_siglas] . '.' . $rst_b[tps_siglas] . '.  ' ?></td>
                            <td align="right"><?php echo number_format($v_b, $dc) ?></td>
                        </tr>  
                        <?php
                        $v_b = 0;
                    }
                    if ($gr_a != $rst[mp_a] && $i != 1) {
                        $rst_a = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_a));
                        ?>
                        <tr style="height: 20px;" class="totales" id="fila" >
                            <td style="font-size: 14px;" colspan="3"><?php echo $rst_a[tps_nombre] ?></td>
                            <td style="font-size: 14px;"><?php echo 'TOTAL ' . $rst_a[tps_siglas] . '.  ' ?></td>
                            <td style="font-size: 14px;" align="right"><?php echo number_format($v_a, $dc) ?></td>
                        </tr>  
                        <?PHP
                        $v_a = 0;
                    }
                    ?>
                    <tr style="height: 20px" id="fila">
                        <td><?php echo $i ?></td>
                        <td><?php echo $cod ?></td>
                        <td><?php echo $des ?></td>
                        <td><?php echo $uni ?></td>
                        <td align="right"><?php echo number_format($inv, $dc) ?></td>
                    </tr>  
                    <?php
                    $gr_a = $rst[mp_a];
                    $gr_b = $rst[mp_b];
                    if ($gr_a == $rst[mp_a]) {
                        $v_a+=$inv;
                    }
                    if ($gr_b == $rst[mp_b]) {
                        $v_b+=$inv;
                    }
                }

                if (($gr_a != $rst[mp_a] || $gr_b != $rst[mp_b])) {
                    $rst_b = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_b));
                    $rst_ab = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_a));
                    ?>
                    <tr style="height: 20px;" class="subtotal" id="fila">
                        <td colspan="3"><?php echo $rst_ab[tps_nombre] . '  ' . $rst_b[tps_nombre] ?></td>
                        <td><?php echo 'TOTAL ' . $rst_ab[tps_siglas] . '.' . $rst_b[tps_siglas] . '.  ' ?></td>
                        <td align="right"><?php echo number_format($v_b, $dc) ?></td>
                    </tr>  
                    <?php
                    $v_b = 0;
                }
                if ($gr_a != $rst[mp_a]) {
                    $rst_a = pg_fetch_array($Clase_industrial_inventariopt->lista_tipos($gr_a));
                    ?>
                    <tr style="height: 20px;" class="totales" id="fila">
                        <td style="font-size: 14px;" colspan="3"><?php echo $rst_ab[tps_nombre] ?></td>
                        <td style="font-size: 14px;"><?php echo 'TOTAL ' . $rst_a[tps_siglas] . '.  ' ?></td>
                        <td style="font-size: 14px;" align="right"><?php echo number_format($v_a, $dc) ?></td>
                    </tr>  
                    <?PHP
                    $v_a = 0;
                }
                ?>
                <tr style="height: 20px;" class="total_fin" id="fila">
                    <td style="font-size: 15px;" colspan="3"></td>
                    <td style="font-size: 15px;"><?php echo 'TOTAL' ?></td>
                    <td style="font-size: 15px;" align="right"><?php echo number_format($tot_inv, $dc) ?></td>
                </tr> 
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var ids = '<?php echo $ids ?>';
    $('#ids').val(ids);
</script>



