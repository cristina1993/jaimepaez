<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
$cns = $Clase_cierre_caja->lista_cierres_caja();
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://PDF Cierres de Cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_cierre_caja.php?id=' + id;
                        look_menu();
                        break;
                }
            }

        </script> 
        <style>
            #mn64,
            #mn112,
            #mn117,
            #mn122,
            #mn127,
            #mn132,
            #mn137,
            #mn142,
            #mn147,
            #mn152{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
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
                <center class="cont_title" ><?php echo "CIERRES DE CAJA DE BODEGAS" . $bodega ?></center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>N DOCUEMNTO</th>
                    <th>LOCAL</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>VENDEDOR</th>
                    <th>TOTAL FACTURAS</th>
                    <th>TOTAL NOTAS CREDITO</th>
                    <th>ACCION</th>
                </tr>        
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                <td><?php echo $n ?></td>
                <td><?php echo $rst['cie_secuencial'] ?></td>
                <td><?php echo $rst['cie_punto_emision'] ?></td>
                <td><?php echo $rst['cie_fecha'] ?></td>
                <td><?php echo $rst['cie_hora'] ?></td>
                <td><?php echo $rst['cie_usuario'] ?></td>
                <td><?php echo $rst['cie_total_facturas'] ?></td>
                <td><?php echo $rst['cie_total_notas_credito'] ?></td>
                <td align="center">
                    <?php {
                        ?>
                        <img src="../img/orden.png"  class="auxBtn" onclick="auxWindow(0,<?php echo $rst[cie_punto_emision] ?>)">
                        <?php
                    }
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

