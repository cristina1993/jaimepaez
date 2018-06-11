<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_rep_produccion.php';
$ClaseOrden = new ClaseOrden();
if (isset($_GET[fecha1], $_GET[fecha2], $_GET[orden])) {
    $cns = $ClaseOrden->lista_buscador_orden(trim(strtoupper($_GET[fecha1])), trim(strtoupper($_GET[fecha2])), trim(strtoupper($_GET[orden])));
} else {
    $cns = $ClaseOrden->lista_orden();
    $cns1 = $ClaseOrden->suma_pesos();
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

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/FormOrden.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/FormOrden.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id, op)
            {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actionsOrden.php", {act: 48, id: id, op: op}, function (dt) {
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
            #mn39{
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
                    <span class="subMenu"  >Lista</span>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >REPORTE DE PRODUCCION</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Desde:<input type="text" size="12" name="fecha1" id="fecha1" /><img src="../Img/calendar.png" id="im-campo1" />
                        Hasta:<input type="text" size="12" name="fecha2" id="fecha2" /><img src="../Img/calendar.png" id="im-campo2" />

                        Orden:<input type="text" name="orden" id="orden" size="15" id="txt3" onkeyup="this.value = this.value.toUpperCase()"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">BUSCAR</button><img src="../img/finder.png"/>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha</th>
            <th>Fabrica</th>
            <th>Orden</th>
            <th>Rollo</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Largo</th>
            <th>Ancho</th>
            <th>Peso</th>
            <th>Gramaje</th>
            <th>Operador</th>
            <th>Acciones</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['reg_fecha'] ?></td>
                    <td><?php echo $rst['emp_descripcion'] ?></td>
                    <td><?php echo $rst['ord_num_orden'] ?></td>
                    <td><?php echo $rst['ord_num_rollos'] ?></td>
                    <td><?php echo $rst['cli_id'] ?></td>
                    <td><?php echo $rst['pro_descripcion'] ?></td>
                    <td><?php echo $rst['pro_largo'] ?></td>
                    <td><?php echo $rst['pro_ancho'] ?></td>
                    <td><?php echo $rst['pro_peso'] ?></td>
                    <td><?php echo $rst['reg_gramaje'] ?></td>
                    <td><?php echo $rst['reg_operador'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[reg_id] ?>, 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[reg_id] ?>, 0)">
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

