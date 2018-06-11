<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $_GET[txt] = strtoupper(trim($_GET[txt]));
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $cns = $Set->lista_inv_kardex("AND (mp.mp_codigo like '%$_GET[txt]%' OR mp.mp_referencia like '%$_GET[txt]%'  )",$desde, $hasta);
} else {
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
    //$cns=$Set->lista_inv_kardex('',$hasta);    
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Movimiento de Materia Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            );

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_movmp.php';
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 20, id: id}, function (dt) {
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
            #mn192{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="" id="tbl" width='100%'>
            <caption class="tbl_head" >
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Exporta Lista"  src="../img/xls.png" width="16px" />                            
                </center>
                <center class="cont_title" >KARDEX DE MATERIA PRIMA</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        DESDE:<input type="text"   name="desde" value="<?php echo $desde ?>"  id="desde" size="10" />
                        <img src="../img/calendar.png" width="16"   id="im-desde" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"   id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                
                    </form>  
                </center>
            </caption>

            <thead>
                <tr>
                    <th colspan="5">Materia Prima</th>
                    <th colspan="4">Documento</th>
                    <th >Transaccion</th>
                    <th colspan="3">Cantidad</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Referencia</th>
                    <th>Descripcion</th>
                    <th>Presentacion</th>
                    <th>Unidad</th>
                    <th>Fecha Transaccion</th>
                    <th>Documento No</th>
                    <th>Guia de Recepcion</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
                    <th>Entrada </th>
                    <th>Salida </th>
                    <th>Saldo </th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $mp = null;
                $mp_code = null;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $j++;
                    if ($rst[trs_operacion] == 0) {
                        $operador = null;
                        $can1 = $rst[mov_peso_total];
                    } else {
                        $operador = "-";
                        $can2 = $operador . $rst[mov_peso_total];
                    }
                    $rst_prov = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]));
                    if ($mp != $rst[mp_id] && $n != 1) {
                        $sal = 0;
                        ?>
                        <tr>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" >Total</td>                                
                            <td class="totales" ><?php echo $mp_code ?></td>
                            <td class="totales" align="right" ><?php echo number_format($t_c1, 1) ?></td>
                            <td class="totales" align="right"><?php echo number_format($t_c2, 1) ?></td>
                            <td class="totales" align="right"><?php echo number_format($t_tot, 1) ?></td>
                        </tr>
                        <?php
                        $t_c1 = 0;
                        $t_c2 = 0;
                        $t_tot = 0;
                    }
                    if ($mp != $rst[mp_id]) {
                        $rst_ant = pg_fetch_array($Set->total_ingreso_egreso($rst['mp_id'], $desde));
                        $aing = $rst_ant[ingreso];
                        $aegr = $rst_ant[egreso];
                        if ($aegr != 0) {
                            $aegr = "-" . $rst_ant[egreso];
                        }
                        $ant = $aing + $aegr;
                        ?>
                        <tr style="font-weight:bolder">
                            <td><?php echo $j++ ?></td>
                            <td><?php echo $rst[mp_codigo] ?></td>
                            <td><?php echo $rst[mp_referencia] ?></td>
                            <td><?php echo $rst[mp_presentacion] ?></td>
                            <td align="center" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>    
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>                                
                            <td>Saldo anterior</td>
                            <td align="right"><?php echo number_format($aing, 1) ?></td>
                            <td align="right"><?php echo number_format($aegr, 1) ?></td>
                            <td align="right"><?php echo number_format($ant, 1) ?></td>
                        </tr>
                        <?php
                    }
                    $sal = $ant + $sal + $can1 + $can2;
                    ?>

                    <tr>
                        <td><?php echo $j ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>
                        <td><?php echo $rst[mp_presentacion] ?></td>
                        <td align="center" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>                        
                        <td><?php echo $rst[mov_fecha_trans] ?></td>
                        <td><?php echo $rst[mov_num_trans] ?></td>
                        <td><?php echo $rst[mov_documento] ?></td>
                        <td><?php echo trim($rst_prov['cli_apellidos'] . ' ' . $rst_prov['cli_nombres'] . ' ' . $rst_prov['cli_raz_social']) ?></td>
                        <td><?php echo $rst[trs_descripcion] ?></td>
                        <td align="right"><?php echo number_format($can1, 1) ?></td>
                        <td align="right"><?php echo number_format($can2, 1) ?></td>
                        <td align="right"><?php echo number_format($sal, 1) ?></td>
                    </tr>  
                    <?PHP
                    $t_c1+=$can1 + $aing;
                    $t_c2+=$can2 + $aegr;
                    $t_tot = $t_c1 + $t_c2;
                    $mp = $rst[mp_id];
                    $mp_code = $rst[mp_codigo];
                    $can1 = 0;
                    $can2 = 0;
                    $ant = 0;
                    $aing = 0;
                    $aegr = 0;
                }
                ?>
                <tr>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" ></td>
                    <td class="totales" >Total</td>                                
                    <td class="totales" ><?php echo $mp_code ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_c1, 1) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_c2, 1) ?></td>
                    <td class="totales" align="right"><?php echo number_format($t_tot, 1) ?></td>
                </tr>
            </tbody>


        </table>            

    </body>    
</html>

