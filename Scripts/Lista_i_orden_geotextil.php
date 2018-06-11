<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ordenes_geotextil.php';
$Clase = new Clase();
if (isset($_GET[txt1],$_GET[txt2],$_GET[txt3],$_GET[txt4])) {
    $cns = $Clase->lista_buscador(trim(strtoupper($_GET[txt1])),trim(strtoupper($_GET[txt2])),trim(strtoupper($_GET[txt3])),trim(strtoupper($_GET[txt4])));
} else {
    $cns = $Clase->lista();
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
                Calendar.setup({inputField: "txt1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "txt2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
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
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_ordenes_geotextil.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_ordenes_geotextil.php?id=' + id;
                        look_menu();
                        break;
                    case 2://PDF
                        frm.src = '../Scripts/frm_pdf_geotextil.php?id=' + id;
                        look_menu();
                        break;
                    case 3://doble click en la lista  aparece el formulario
                        frm.src = '../Scripts/Form_i_ordenes_geotextil.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }

            }

            function del(id, op)
            {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_ordenes_geotextil.php", {act: 48, id: id, op: op}, function (dt) {
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
            #mn52{
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
                    $cns_sbm = $User->list_primer_opl(29, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>

                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >ORDEN DE PRODUCCION GEOTEXTIL</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text" id="txt1" name="txt1" size="15"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" id="txt2" name="txt2" size="15" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        ORDEN:<input type="text" name="txt3" size="15" />
                        STATUS:<input type="text" name="txt4" size="15"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th colspan="5"></th>
            <th colspan="2">Solicitado</th>
            <th colspan="2">Producción</th>
            <th colspan="2">Despacho</th>
            <th colspan="2">Faltante</th>
            <th colspan="2"></th>

        </thead>
            <thead>
            <th>No.</th>
            <th>Pedido</th>
            <th>Fecha Pedido</th>
            <th>Cliente</th>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Kg.</th>
            <th>Cant.</th>
            <th>Kg.</th>
            <th>Cant.</th>
            <th>Kg.</th>
            <th>Cant.</th>
            <th>Kg.</th>
            <th>Status</th>
            <th>Acciones</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
            <tr id="linea" ondblclick="auxWindow(3,<?php echo $rst[opg_id] ?>, 1)">
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['opg_codigo'] ?></td>
                    <td><?php echo $rst['opg_fec_pedido'] ?></td>
                    <td><?php echo $rst['cli_nombre'] ?></td>
                    <td><?php echo $rst['pro_descripcion'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_kg1'] ?></td>
                    <td><?php echo $rst['opg_status'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[opg_id] ?>, 1)">
                            <?php
                        }
                         if ($Prt->pdf == 0) {
                            ?>
                            <img src="../img/orden.png"  class="auxBtn" onclick="auxWindow(2,<?php echo $rst[opg_id] ?>, 0)">
                            <?php
                         }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[opg_id] ?>, 0)">
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

