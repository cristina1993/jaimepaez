<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_pedidospt.php';
include_once '../Clases/clsClase_inv_fisico.php';
$Clase_inv_fisico = new Clase_inv_fisico();
$Clase_pedidospt = new Clase_pedidospt();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $txt = " where inv_num_documento like '%$txt%' or inv_auditor like '%$txt%'";
        $fec1 = '';
        $fec2 = '';
    } else {
        $txt = " where inv_fec_emison between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_inv_fisico->lista_buscador_inv_fisico_agrup($txt);
    $nm = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $txt = " where inv_fec_emison between '$fec1' and '$fec2' ";
    $cns = $Clase_inv_fisico->lista_buscador_inv_fisico_agrup($txt);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lista Toma de Inventario Fisico</title>
    </head>
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
                    parent.document.getElementById('contenedor2').rows = "*,90%";
                    frm.src = '../Scripts/Form_inv_fisico.php';
                    look_menu();
                    break;
                case 1://
                    parent.document.getElementById('contenedor2').rows = "*,80%";
                    frm.src = '../Scripts/Form_inv_fisico.php?id=' + id;
                    break;
                case 2://PDF
                    parent.document.getElementById('contenedor2').rows = "*,90%";
                    frm.src = '../Scripts/frm_pdf_inv_fisico.php?id=' + id;
                    break;
            }

        }
        function del(id, op)
        {

            var r = confirm("Esta Seguro de eliminar este elemento?");
            if (r == true) {
                $.post("actions_asientos.php", {id: id, op: op}, function (dt) {
                    if (dt == 0)
                    {
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_inv_fisico.php';
                    } else {
                        alert(dt);
                    }
                });
            } else {
                return false;
            }
        }
        function loading(prop) {
            $('#cargando').css('visibility', prop);
            $('#charging').css('visibility', prop);
        }
    </script>
    <style>
        #mn199{
            background:black;
            color:white;
            border: solid 1px white;
        }
        tbody tr{
            height:25px; 
        }
        *{
            text-transform: uppercase;
        }

    </style>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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

                    <!--<img class="auxBtn" width="16px" src="../img/orden.png" onclick="auxWindow(2)" title="Imprimir Cartilla" />-->
                </center>               
                <center class="cont_title" ><?PHP echo 'TOMA DE INVENTARIO FÍSICO' ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>Fecha</th>
                    <th>Documento No</th>
                    <th>Auditor</th>
                    <th>Bodega</th>
                    <th>Cantidad</th>
                </tr>

            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $bod = pg_fetch_array($Clase_inv_fisico->lista_un_emisor($rst[inv_bodega]));
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td onclick="auxWindow(1, '<?php echo $rst['inv_num_documento'] ?>')"><?php echo $rst['inv_fec_emison'] ?></td>    
                        <td onclick="auxWindow(1, '<?php echo $rst['inv_num_documento'] ?>')"><?php echo $rst['inv_num_documento'] ?></td> 
                        <td onclick="auxWindow(1, '<?php echo $rst['inv_num_documento'] ?>')"><?php echo $rst['inv_auditor'] ?></td>
                        <td onclick="auxWindow(1, '<?php echo $rst['inv_num_documento'] ?>')"><?php echo $bod[emi_nombre_comercial] ?></td> 
                        <td onclick="auxWindow(1, '<?php echo $rst['inv_num_documento'] ?>')" align="right"><?php echo $rst[sum] ?></td>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>
        </table>            
    </body> 
</html>
