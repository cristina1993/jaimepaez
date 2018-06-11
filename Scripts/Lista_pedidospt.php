<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
if (isset($_GET[ord], $_GET[cli], $_GET[ruc], $_GET[fecha1], $_GET[fecha2])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $estado = $_GET[ped_estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($ord)) {
        $ord = "where ped_num_registro='$ord'";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($ord);
    } else if (!empty($cli)) {
        $cli = "where ped_nom_cliente='$cli'";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($cli);
    } else if (!empty($ruc)) {
        $ruc = "where ped_ruc_cc_cliente='$ruc'";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($ruc);
    } else if ($estado != 'x') {
        $estado = "where ped_estado='$estado'";
        $cns = $Docs->lista_buscador_orden($estado);
    } else {
        $ord = "where ped_femision between '$fec1' and '$fec2' ";
        $cns = $Docs->lista_buscador_orden($ord);
    }
}else{
    $cns = $Docs->lista_registros_completo_pedidos();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                $('#fecha1').val('<?php echo date('Y-m-d'); ?>');
                $('#fecha2').val('<?php echo date('Y-m-d'); ?>');
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
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_ord_pedido_venta.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_ord_pedido_venta.php?id=' + id;
                        look_menu();
                        break;
                }
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 1, id: id}, function (dt) {
                        if (dt == 0) {
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
            #mn180{
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
                <center class="cont_title" >ORDENES DE PEDIDO DE VENTA</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli"/>
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc"/>
                        ESTADO:
                        <select id="ped_estado" name="ped_estado">
                            <option value="x" >SELECCIONE</option>
                            <option value="0" >PENDIENTE</option>
                            <option value="1" >APROBADO</option>
                            <option value="2" >RECHAZADO</option>
                        </select>
                        DESDE:<input type="text" size="16" name="fecha1" id="fecha1" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="16" name="fecha2" id="fecha2" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Orden de Venta</th>
            <th>Ruc/Cedula</th>
            <th>Cliente</th>                                
            <th>Local</th>
            <th>Vendedor</th>
            <th>Total Valor</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[ped_local]) {
                    case '1':$local = 'BODEGA1';
                        break;
                    case '10':$local = 'BODEGA1';
                        break;
                    case '2':$local = 'Bodega1';
                        break;
                    case '3':$local = 'Quicentro Sur Shopping';
                        break;
                    case '4':$local = 'Mall del Sol';
                        break;
                    case '5':$local = 'Shopping Machala';
                        break;
                    case '6':$local = 'Riocentro Norte';
                        break;
                    case '7':$local = 'San Marino Shopping';
                        break;
                    case '8':$local = 'City Mall';
                        break;
                    case '9':$local = 'Quicentro Shopping';
                        break;
                }
                switch ($rst[ped_estado]) {
                    case '0':$estado = 'Pendiente';
                        break;
                    case '1':$estado = 'Aprobado';
                        break;
                    case '2':$estado = 'Rechazado';
                        break;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[ped_num_registro] ?></td>
                    <td><?php echo $rst[ped_ruc_cc_cliente] ?></td>
                    <td><?php echo $rst[ped_nom_cliente] ?></td>
                    <td><?php echo $local ?></td>
                    <td><?php echo $rst[ped_vendedor] ?></td>
                    <td align="right"><?php echo $rst[ped_total] ?></td>
                    <td align="center"><?php echo $estado ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                        <img src="../img/del_reg.png" width="20px"  class="auxBtn" onclick="del(<?php echo $rst[ped_id] ?>)">
                            <?php
                        }
                        if ($estado == 'Rechazado') {
                            if ($Prt->edition == 0) {
                                ?>
                                <img src="../img/upd.png" width="20px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)">
                                <?php
                            }
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

