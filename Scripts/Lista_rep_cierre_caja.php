<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
$f = date('Y-m-d');
$emisor = 2;
if (isset($_GET[txt], $_GET[desde], $_GET[hasta])) {
    $txt = trim($_GET[txt]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "where cie_secuencial like '%$txt%' or cie_usuario like '%$txt%'";
    } else {
        $texto = "where cie_fecha between '$desde' and '$hasta'";
    }
    $cns = $Clase_cierre_caja->lista_buscador_cierres_caja($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
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
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });

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
                    case 0://PDF Cierres de Cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_cierre_caja.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }
            }

            function mensaje(act, id) {

                sms = confirm("ESTA SEGURO DE GENERAR EL CIERRE DE CAJA ?");
                if (sms == true) {
                    $.post("actions_cierre_caja_n.php", {op: 0, id: id, emi: emisor.value},
                    function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                            ;
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
            #mn191,
            #mn192,
            #mn193,
            #mn194,
            #mn195,
            #mn196,
            #mn197,
            #mn198,
            #mn199,
            #mn200{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #desde,#hasta{
                background:#E0E0E0; 
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
        <img id="charging" src="../img/load_circle.gif" />    
        <div id="cargando"></div>
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
                <center class="cont_title" ><?php echo "CIERRE DE CAJA " . $bodega ?></center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Cerrar de Caja" onclick="mensaje(1,<?php echo $_SESSION[usuid] ?>)" ><?PHP echo 'Cerrar / ' . date('Y-m-d') ?></a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                        Buscar por:<input type="text" id="txt" name="txt" value="<?PHP echo $txt ?>" />
                        Desde:<input type="text" id="desde" name="desde" size="12" style="text-align:right" value="<?PHP echo $desde ?>" readonly/>
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" name="hasta" size="12"  style="text-align:right" value="<?PHP echo $hasta ?>" readonly/>
                        <img src="../img/calendar.png" id="im_hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>N DOCUMENTO</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>VENDEDOR</th>
                    <th>TOTAL FACTURAS</th>
                    <th>TOTAL NOTAS CREDITO</th>
                    <th>REPORTE</th>
                </tr>             
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $totfac +=$rst['cie_total_facturas'];
                    $totnc += $rst['cie_total_notas_credito'];
                    if ($grup != $rst['cie_fecha'] && $n != 1) {
                        ?>
                        <tr>

                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" ></td>
                            <td class="totales" >Total</td>                                
                            <td class="totales" align="right" ><?php echo number_format($t_f, 4) ?></td>
                            <td class="totales" align="right"><?php echo number_format($t_nc, 4) ?></td>
                            <td class="totales" ></td>
                        </tr>
                        <?php
                        $t_f = 0;
                        $t_nc = 0;
                    }
                    ?>

                <td><?php echo $n ?></td>
                <td><?php echo $rst['cie_secuencial'] ?></td>
                <td><?php echo $rst['cie_fecha'] ?></td>
                <td><?php echo $rst['cie_hora'] ?></td>
                <td><?php echo $rst['cie_usuario'] ?></td>
                <td align="right"><?php echo number_format($rst['cie_total_facturas'], 4)  ?></td>
                <td align="right"><?php echo number_format($rst['cie_total_notas_credito'], 4)  ?></td>
                <td align="center">
                    <?php {
                        ?>
                        <img src="../img/orden.png"  class="auxBtn" onclick="auxWindow(0,<?php echo $rst[cie_id] ?>, '<?php echo $rst['cie_fecha'] ?>')">
                        <?php
                    }
                    ?>


                </td>           
            </tr>  
            <?PHP
            $t_f+=$rst['cie_total_facturas'];   
            $t_nc+=$rst['cie_total_notas_credito'];
            $grup=$rst['cie_fecha'];
        }
        ?>
        <tr>
            <td class="totales" ></td>
            <td class="totales" ></td>
            <td class="totales" ></td>
            <td class="totales" ></td>
            <td class="totales" >Total</td>                                
            <td class="totales" align="right" ><?php echo number_format($t_f, 4) ?></td>
            <td class="totales" align="right"><?php echo number_format($t_nc, 4) ?></td>
            <td class="totales" ></td>
        </tr>
    </tbody>
<!--    <tr style="font-weight:bolder">
        <td colspan="6" align="right" >Total</td>
        <td align="right" style="font-size:14px;" id="gtotal"><?php echo number_format($totfac, 4) ?></td>
        <td align="right" style="font-size:14px;"><?php echo number_format($totnc, 4) ?></td>
        <td colspan="6"></td>
    </tr>-->
</table>            
</body>    
</html>

