<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_pedidospt.php';
include_once '../Clases/clsClase_asientos.php';
$Clase_asientos = new Clase_asientos();
$Clase_pedidospt = new Clase_pedidospt();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $txt = " where cast (con_asiento as varchar) = '$txt' or con_concepto like '%$txt%' or con_documento like '%$txt%'";
        $fec1 = '';
        $fec2 = '';
    } else {
        $txt = " where con_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_asientos->lista_buscador_asientos($txt);
} else {
    $txt = '';
    $cns = $Clase_asientos->lista_asientos();
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
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
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
            });

            function auxWindow(a,id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Asientos
                        parent.document.getElementById('contenedor2').rows = "*,90%";
                        frm.src = '../Scripts/frm_pdf_asientos.php?id=' + id;
                        break;
                }

            }
        </script> 
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="min-width:50%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_title" ><?PHP echo 'ASIENTOS CONTABLES' ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" />
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
                    <th>Asiento N</th>
                    <th>Concepto</th>
                    <th>F. Emision</th>
                    <th>Estado</th>
                    <th>Acciones</th>                    
                </tr>

            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    if ($rst['con_estado'] == 0) {
                        $rst['con_estado'] = 'PENDIENTE';
                    }
                    if ($rst['con_estado'] == 1) {
                        $rst['con_estado'] = 'COMPLETADO';
                    }
                    $orden = '';
                    
                    if($grup != $rst['con_asiento']){
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['con_asiento'] ?></td>    
                        <td><?php echo $rst['con_concepto'] ?></td>    
                        <td><?php echo $rst['con_fecha_emision'] ?></td>    
                        <td align="center" ><?php echo $rst['con_estado'] ?></td>
                        <td><img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(0,'<?php echo $rst[con_asiento]?>')"></td>    
                    </tr>  
                    <?PHP
                    }
                    $grup = $rst['con_asiento'];
                }
                ?>
            </tbody>
        </table>            
    </body>    
</html>

