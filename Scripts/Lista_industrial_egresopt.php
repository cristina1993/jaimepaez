<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_egresopt.php'; //cambiar clsClase_productos
include_once '../Clases/clsClase_pedidospt.php';
$Clase_pedidospt = new Clase_pedidospt();
$Clase_industrial_egresopt = new Clase_industrial_egresopt();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    ;
    if (!empty($txt)) {
        $txt = " and p.pro_codigo like '%$txt%'";
    }
    $cns = $Clase_industrial_egresopt->lista_buscador_industrial_egresopt($txt);
} else {
    $txt = '';
    if ($emisor == 1) {
        $cns = $Clase_industrial_egresopt->lista_ped_nop($emisor);
    } else {
        $cns = $Clase_industrial_egresopt->lista($emisor);
    }
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
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
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_egresopt.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2:
                        main.src = '../Scripts/Lista_industrial_ingresopt.php';
                        break;
                    case 3:
                        main.src = '../Scripts/Lista_industrial_egresopt.php';
                        break;
                    case 4:
                        main.src = '../Scripts/Lista_industrial_movimientopt.php';
                        break;
                    case 5:
                        main.src = '../Scripts/Lista_industrial_inventariopt.php';
                        break;
                    case 6:
                        main.src = '../Scripts/Lista_industrial_kardexpt.php';
                        break;
                }
            }
        </script> 
        <style>
            #mn55,
            #mn60,
            #mn71,
            #mn76,
            #mn81,
            #mn86,
            #mn91,
            #mn96,
            #mn101,
            #mn106{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
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
                <center class="cont_title" ><?PHP echo 'EGRESO DE PRODUCTO TERMINADO ' . $bodega ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO:<input type="text" name="txt" size="15" id="txt" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Orden</th>
            <th>Cliente</th>
            <th>Tipo de Transacción</th>
            <th>Guía de Transporte</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($rst['ped_estado'] == 1) {
                    $rst['ped_estado'] = 'ENPROCESO';
                }
                if ($rst['ped_estado'] == 2) {
                    $rst['ped_estado'] = 'COMPLETADO';
                }
                if ($rst['ped_estado'] == 3) {
                    $rst['ped_estado'] = 'PENDIENTE';
                }
                ?>
    <!--                <tr id="fila" ondblclick="auxWindow(1,<?php //echo $rst[ped_id]  ?>, 1)">-->

                <?PHP
                $ev = "onclick='auxWindow(1,$rst[ped_id],1)'";

                if ($grup != $rst['ped_documento']) {
                    ?>
                <td><?php echo $n ?></td>
                <td <?php echo $ev ?> ><?php echo $rst['ped_documento'] ?></td>
                <td <?php echo $ev ?> ><?php echo trim($rst['cli_apellidos'].' '.$rst['cli_nombres'].' '.$rst['cli_raz_social']) ?></td>
                <?php
            } else {
                ?>
                <td></td>
                <td></td>
                <td></td>
                <?php
            }
            ?>


            <td <?php echo $ev ?> ><?php echo $rst['trs_descripcion'] ?></td>
            <td <?php echo $ev ?> ><?php echo $rst['ped_guia_transporte'] ?></td>
            <td <?php echo $ev ?> ><?php echo $rst['ped_estado'] ?></td>
            <td align="center">
                <?php
                if ($Prt->edition == 0) {
                    ?>
                    <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)">
                    <?php
                }
                ?>


            </td>           
        </tr>  
        <?PHP
        $grup = $rst['ped_documento'];
    }
    ?>
</tbody>


</table>            

</body>    
</html>

