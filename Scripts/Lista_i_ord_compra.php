<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $_GET[txt] = strtoupper(trim($_GET[txt]));
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (empty($_GET[txt])) {
        $txt = null;
        $date = "  AND oc.orc_fecha BETWEEN '$desde' and '$hasta'  ";
    } else {
        $txt = "  AND oc.orc_codigo like '%$_GET[txt]%'  ";
        $date = null;
    }
    $cns = $Set->lista_ordenes_compra_search($txt, $date);
} else {
    //$cns=$Set->lista_ordenes_compra();    
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
    $date = "  AND oc.orc_fecha BETWEEN '$desde' and '$hasta'  ";
    $txt = null;
    $cns = $Set->lista_ordenes_compra_search($txt, $date);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Orden de Compra de Prima</title>
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_i_orden_compra.php?y=0';
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_orden_compra.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2:
                        frm.src = '../Scripts/Form_i_etq_orden_compra.php?id=' + id;
                        look_menu();
                        break;
                    case 3:
                        frm.src = '../Scripts/Form_i_orden_compra.php?id=' + id + '&x=1';
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        //look_menu();
                        break;

                }
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 32, id: id}, function (dt) {
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
            #mn31{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="" id="tbl" width='100%'>
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(96, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >ORDEN DE COMPRA DE MATERIA PRIMA</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>

                    <form method="GET" id="frmSearch" name="frm1" style="margin-top:5px; " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        DESDE:<input type="text"   name="desde" value="<?php echo $desde ?>"  id="desde" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-desde" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../img/calendar.png" width="16"  id="im-hasta" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>

            </caption>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Orden No</th>
                    <th>Fecha</th>
                    <th>Proveedor</th>
                    <th>Sub.T %</th>
                    <th>Desc %</th>
                    <th>Desc $</th>
                    <th>IVA 12%</th>
                    <th>Flete $</th>
                    <th>Total $</th>
                    <th>Acciones</th>
                </tr>  
            </thead>
            <tbody>
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $sbt = pg_fetch_array($Set->lista_subt($rst[orc_id]));
                    $t_desc = $sbt[sum] * $rst[orc_descuento] / 100;
                    $iva12 = ($sbt[sum] - $t_desc) * 0.12;
                    $total = ($sbt[sum] - $t_desc + $iva12 + $rst[orc_flete]);
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[orc_codigo] ?></td>                        
                        <td><?php echo $rst[orc_fecha] ?></td>
                        <td><?php echo trim($rst[cli_apellidos] . ' ' . $rst[cli_nombres] . ' ' . $rst[cli_raz_social]) ?></td>
                        <td align='right'><?php echo number_format($sbt[sum], 2) ?></td>
                        <td align='right'><?php echo number_format($rst[orc_descuento], 2) ?></td>
                        <td align='right'><?php echo number_format($t_desc, 2) ?></td>
                        <td align='right'><?php echo number_format($iva12, 2) ?></td>
                        <td align='right'><?php echo number_format($rst[orc_flete], 2) ?></td>                        
                        <td align='right'><?php echo number_format($total, 2) ?></td>
                        <td align="center">
    <?php
    if ($Prt->delete == 0) {
        ?>
                                <img class="auxBtn" width="20px" src="../img/del_reg.png" onclick="del(<?php echo $rst[orc_id] ?>)">
                                <?php
                            }
                            if ($Prt->edition == 0) {
                                ?>
                                <img class="auxBtn" width="20px" src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[orc_id] ?>)">     
                                <?php }
                            ?>
                        </td>
                    </tr>  
    <?PHP
}
?>    
            </tbody>


        </table>            

    </body>    
</html>

