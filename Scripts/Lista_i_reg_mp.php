<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $cns = $Set->lista_mov_mp_search(0, trim(strtoupper($_GET[txt])));
} else {
    $cns = $Set->lista_mov_mp(0);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Registro Materia Prima</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
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
                switch (a) {
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_mp.php';
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_reg_mp.php?id=' + id + '&x=' + x;
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        if (x == 0) {
                            look_menu();
                        }
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
            #mn24{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl(18, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <center class="cont_title" >Registro de Inventario de Materia Prima</center>
                    <center class="cont_finder">
                        <?php
                        if ($Prt->add == 0) {
                            ?>
                            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                            <?php
                        }
                        ?>
                        <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                            Codigo:<input type="text" name="txt" size="15" />
                            <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        </form>  
                    </center>

            </caption>

            <thead>
                <tr>
                    <th colspan="5">Materia Prima</th>
                    <th colspan="4">Documento</th>
                    <th colspan="4">Transaccion</th>
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
                    <th>Cantidad</th>
                    <th>Peso Unitario</th>
                    <th>Peso Total en Stock</th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]));
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>
                        <td><?php echo $rst[mp_presentacion] ?></td>
                        <td align="center" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>                        
                        <td><?php echo $rst[mov_fecha_trans] ?></td>
                        <td><?php echo $rst[mov_num_trans] ?></td>
                        <td><?php echo $rst[mov_guia_remision] ?></td>
                        <td><?php echo trim($rst_cli['cli_apellidos'] . ' ' . $rst_cli['cli_nombres'] . ' ' . $rst_cli['cli_raz_social']) ?></td>
                        <td><?php echo $rst[trs_descripcion] ?></td>
                        <td align="right"><?php echo number_format($rst[mov_cantidad], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_unit], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_total], 1) ?></td>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>

