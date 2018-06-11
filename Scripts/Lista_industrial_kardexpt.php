<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_kardexpt.php'; //cambiar clsClase_productos
$Clase_industrial_kardexpt = new Clase_industrial_kardexpt();
$cns_comb = $Clase_industrial_kardexpt->lista_un_mp_mod1();
if ($ctr_inv == 0) {
    $fra = '';
    $fra2 = '';
} else {
    $fra = "and m.bod_id=$emisor ";
}
if (isset($_GET[prod], $_GET[fecha1], $_GET[fecha2])) {
    $prod = trim(strtoupper($_GET[prod]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $ids = $_GET[ids];
    if ($ids == '') {
        $ids = 'no';
    }
    if (!empty($prod)) {
        $txt = "(m.mov_documento like '%$prod%' or m.mov_guia_transporte like '%$prod%' or c.cli_raz_social like '%$prod%' or t.trs_descripcion like '%$prod%' or p.mp_c like '%$prod%' or p.mp_d like '%$prod%') and m.mov_fecha_trans between '$fec1' and '$fec2' and p.mp_i='0' and p.ids=$ids $fra";
    } else {
        $txt = "m.mov_fecha_trans between '$fec1' and '$fec2' and mp_i='0' and p.ids=$ids $fra";
    }
    $cns = $Clase_industrial_kardexpt->lista_buscar_kardexpt($txt);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
if ($ids == '26') {
    $exc = 1;
} else {
    $exc = 2;
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
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
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

            function auxWindow(a) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        main.src = '../Scripts/Lista_reporte_kardexpt.php'//Cambiar Form_productos
                        break;
                }
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
            #mn198{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
            .sel{
                font-size: 11px;
                width: 100px;
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="15"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="15" align="center"><?PHP echo 'KARDEX ' . $bodega ?></th></tr>
                <tr>
                    <td colspan="15"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=<?php echo $exc ?>" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?PHP echo 'KARDEX ' . $bodega ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        TIPO:<select id="ids" name="ids" class="sel">
                            <!--<option value="">SELECCIONE</option>-->
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                $dt = explode('&', $rst_c[mp_tipo]);
                                ?>
                                <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        BUSCAR POR:<input type="text" name="prod" size="15" id="prod" />
                        <input type="hidden" name="txt" size="15" id="txt" />
                        DESDE:<input type="text" name="fecha1" size="15" id="fecha1" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        AL:<input type="text" name="fecha2" size="15" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="4">Documento</th>
                    <th colspan="2">Transacción</th>
                    <th colspan="3">Ingresos (+)</th>
                    <th colspan="3">Egresos (-)</th>
                    <th colspan="3">Saldos</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Fecha de transacción</th>
                    <th>Documento No</th>
                    <th>Guía</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                    <th>Cantidad</th>
                    <th>Costo/U</th>
                    <th>Costo/T</th>
                </tr>        
            </thead>

            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $j = 1;
                $mp = null;
                $mp_code = null;
                $tabla = null;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst[trs_operacion] == 0) {
                        $ing = $rst['mov_cantidad'];
                        $ui = $rst['mov_val_unit'];
                        $ti = $rst['mov_val_tot'];
                        $egr = '';
                        $ue = '';
                        $te = '';
                    } else {
                        $ing = '';
                        $ui = '';
                        $ti = '';
                        $egr = $rst['mov_cantidad'];
                        $ue = $rst['mov_val_unit'];
                        $te = $rst['mov_val_tot'];
                    }

                    if ($t_cnt == 0) {
                        $t_cnt = '';
                    }
                    if ($t_ui == 0) {
                        $t_ui = '';
                    }
                    if ($t_ti == 0) {
                        $t_ti = '';
                    }
                    if ($t_egr == 0) {
                        $t_egr = '';
                    }
                    if ($t_ue == 0) {
                        $t_ue = '';
                    }
                    if ($t_te == 0) {
                        $t_te = '';
                    }

                    if (($mp != $rst[pro_id] || $tabla != $rst[mov_tabla]) && $n != 1) {
                        $sal = 0;
                        $su = 0;
                        $st = 0;
                        if ($t_sal <= 0.009 && $t_sal >= -0.009) {
                            $t_su = 0;
                            $t_st = 0;
                        }
                        ?>

                        <tr>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" >Total</td>                                
                            <td class="totales" ><?php echo $mp_code ?></td>
                            <td class="totales" ><?php echo $des ?></td>
                            <td class="totales" ><?php echo $uni ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_cnt, $dc) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_ui, $dec) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_ti, $dec) ?></td>
                            <td class="totales" align="right"><?php echo number_format($t_egr, $dc) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_ue, $dec) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_te, $dec) ?></td>
                            <td class="totales" align="right"><?php echo number_format($t_sal, $dc) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_su, $dec) ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_st, $dec) ?></td>
                        </tr>

                        <?php
                        $t_cnt = 0;
                        $t_egr = 0;
                        $t_sal = 0;
                        $t_ui = 0;
                        $t_ti = 0;
                        $t_ue = 0;
                        $t_te = 0;
                        $t_su = 0;
                        $t_st = 0;
                        $tti = 0;
                        $tte = 0;
                        $egt = 0;
                        $cnt = 0;
                    }
                    if (($mp != $rst[pro_id] || $tabla != $rst[mov_tabla])) {
                        $rst_ant = pg_fetch_array($Clase_industrial_kardexpt->total_ingreso_egreso($rst['pro_id'], $emisor, $fec1, $fra));
                        $aing = $rst_ant[ingreso];
                        $aegr = $rst_ant[egreso];
                        $tia = $rst_ant[ti];
                        $tea = $rst_ant[te];
                        $ant = $aing - $aegr;
                        if ($ant <= 0.009 && $ant >= -0.009) {
                            $au = 0;
                            $at = 0;
                        }
                        $at = $tia - $tea;
                        $au = $at / $ant;
                        $uia = $tia / $aing;
                        $uea = $tea / $aegr;
                        ?>
                        <tr style="font-weight:bolder">
                            <td><?php echo $j++ ?></td>
                            <td><?php echo $rst['mp_c'] ?></td>
                            <td><?php echo $rst['mp_d'] ?></td>
                            <td><?php echo $rst['mp_q'] ?></td>
                            <td></td>
                            <td>Saldo anterior</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><?php echo number_format($ant, $dc) ?></td>
                            <td align="right"><?php echo number_format($au, $dec) ?></td>
                            <td align="right"><?php echo number_format($at, $dec) ?></td>
                        </tr>
                        <?php
                    }
                    $sal = $ant + $sal + $ing - $egr;
                    $st = $at + $st + $ti - $te;
                    $su = $st / $sal;

                    if ($ing == 0) {
                        $ing = '';
                    }
                    if ($ui == 0) {
                        $ui = '';
                    }
                    if ($ti == 0) {
                        $ti = '';
                    }
                    if ($egr == 0) {
                        $egr = '';
                    }
                    if ($ue == 0) {
                        $ue = '';
                    }
                    if ($te == 0) {
                        $te = '';
                    }
                    if ($sal <= 0.009 && $sal >= -0.009) {
                        $su = 0;
                        $st = 0;
                    }
                    ?>
                    <tr>
                        <td><?php echo $j++ ?></td>
                        <td align="center"><?php echo $rst['mov_fecha_trans'] ?></td>
                        <td><?php echo $rst['mov_documento'] ?></td>
                        <td><?php echo $rst['mov_guia_transporte'] ?></td>
                        <td><?php echo $rst['cli_raz_social'] ?></td>
                        <td><?php echo $rst['trs_descripcion'] ?></td>
                        <td align="right"><?php echo number_format($ing, $dc) ?></td>
                        <td align="right"><?php echo number_format($ui, $dec) ?></td>
                        <td align="right"><?php echo number_format($ti, $dec) ?></td>
                        <td align="right"><?php echo number_format($egr, $dc) ?></td>
                        <td align="right"><?php echo number_format($ue, $dec) ?></td>
                        <td align="right"><?php echo number_format($te, $dec) ?></td>
                        <td align="right"><?php echo number_format($sal, $dc) ?></td>
                        <td align="right"><?php echo number_format($su, $dec) ?></td>
                        <td align="right"><?php echo number_format($st, $dec) ?></td>
                    </tr>  
                    <?PHP
//                    $t_cnt+=$ing + $aing;
                    $cnt+=$ing + $aing;
                    $t_cnt+=$ing;
//                    $t_egr+=$egr + $aegr;
                    $egt+=$egr + $aegr;
                    $t_egr+=$egr;
                    $t_sal = $cnt - $egt;
                    $tti += $ti + $tia;
                    $t_ti += $ti;
                    $t_ui = $t_ti / $t_cnt;
                    $tte += $te + $tea;
                    $t_te += $te;
                    $t_ue = $t_te / $t_egr;
                    $t_su = $st / $sal;
                    $t_st = $tti - $tte;
                    $mp = $rst[pro_id];
                    $des = $rst[mp_d];
                    $uni = $rst[mp_q];
                    $mp_code = $rst[mp_c];
                    $tabla = $rst[mov_tabla];
                    $ant = 0;
                    $aing = 0;
                    $aegr = 0;
                    $uia = 0;
                    $tia = 0;
                    $uea = 0;
                    $tea = 0;
                    $au = 0;
                    $at = 0;
                }

                if ($t_cnt == 0) {
                    $t_cnt = '';
                }
                if ($t_ui == 0) {
                    $t_ui = '';
                }
                if ($t_ti == 0) {
                    $t_ti = '';
                }
                if ($t_egr == 0) {
                    $t_egr = '';
                }
                if ($t_ue == 0) {
                    $t_ue = '';
                }
                if ($t_te == 0) {
                    $t_te = '';
                }
                if ($t_sal <= 0.009 && $t_sal >= -0.009) {
                    $t_su = 0;
                    $t_st = 0;
                }
                ?>
                <tr>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" >Total</td>  
                    <td class="totales" ><?php echo $mp_code ?></td>
                    <td class="totales" ><?php echo $des ?></td>
                    <td class="totales" ><?php echo $uni ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_cnt, $dc) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_ui, $dec) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_ti, $dec) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_egr, $dc) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_ue, $dec) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_te, $dec) ?></td>
                    <td class="totales" align="right" ><?php echo number_format($t_sal, $dc) ?></td>
                    <td class="totales" align="right" ><?php echo number_format($t_su, $dec) ?></td>
                    <td class="totales" align="right" ><?php echo number_format($t_st, $dec) ?></td>
                </tr>
            </tbody>
        </table>            
    </body>    
</html>
<script>
    var ids = '<?php echo $ids ?>';
    $('#ids').val(ids);
</script>


