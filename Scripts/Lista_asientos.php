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
        $txt = " where (cast (con_asiento as varchar) = '$txt' or con_concepto like '%$txt%' or con_documento like '%$txt%') and mod_id!=15";
        $fec1 = '';
        $fec2 = '';
    } else {
        $txt = " where con_fecha_emision between '$fec1' and '$fec2' and mod_id!=15 ";
    }
    $cns = $Clase_asientos->lista_buscador_asientos($txt);
} else {
    $txt = '';
//    $cns = $Clase_asientos->lista_asientos();
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        parent.document.getElementById('contenedor2').rows = "*,90%";
                        frm.src = '../Scripts/Form_asientos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,90%";
                        frm.src = '../Scripts/Form_asientos.php?id=' + id + '&x=' + x;
                        break;
                    case 2://PDF
                        parent.document.getElementById('contenedor2').rows = "*,90%";
                        frm.src = '../Scripts/frm_pdf_asientos.php?id=' + id;
                        break;
                }

            }
            function del(id, op, nom)
            {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_asientos.php", {act: 48, id: nom, op: op, nom: nom}, function (dt) {
                        if (dt == 0)
                        {

                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_asientos.php';
                        } else {

                            loading('hidden');
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
            #mn177{
                background:black;
                color:white;
                border: solid 1px white;
            }
            tbody tr{
                height:25px; 
            }
            .pendiente{
                background-color:#FF8080;         
            }
        </style>
    </head>
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
                </center>               
                <center class="cont_title" ><?PHP echo 'LISTA DE ASIENTOS CONTABLES' ?></center>
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
                    if ($rst['con_estado'] == 2) {
                        $rst['con_estado'] = 'ANULADO';
                    }
                    if ($grup != $rst['con_asiento']) {
                        $rst_tot = pg_fetch_array($Clase_asientos->suma_totales($rst['con_asiento']));
                        $debe = round($rst_tot[debe], 2);
                        $haber = round($rst_tot[haber], 2);
                        if ($debe != $haber) {
                            $style = "pendiente";
                        } else {
                            $style = "";
                        }
                        ?>
                        <tr>
                            <td class="<?php echo $style ?>"><?php echo $n ?></td>
                            <td class="<?php echo $style ?>"><?php echo $rst['con_asiento'] ?></td>    
                            <td class="<?php echo $style ?>"><?php echo $rst['con_concepto'] ?></td>    
                            <td class="<?php echo $style ?>"><?php echo $rst['con_fecha_emision']?></td>    
                            <td class="<?php echo $style ?>" align="center" ><?php echo $rst['con_estado'] ?></td>
                            <td class="<?php echo $style ?>">
                                <!--<img class="auxBtn" width="16px" src="../img/del.png" onclick="del(<?php echo $rst[con_id] ?>, 1, '<?php echo $rst[con_asiento] ?>')"/>-->
                                <?php
                                if ($rst['con_estado'] != 'COMPLETADO') {
                                    ?> 
                                    <img class="auxBtn" width="16px" src="../img/upd.png" onclick="auxWindow(1, <?php echo $rst[con_id] ?>, 0)"/>
                                    <?php
                                }
                                ?> 
                                <img class="auxBtn" width="16px" src="../img/orden.png" onclick="auxWindow(2, '<?php echo $rst[con_asiento] ?>', 0)"/>
                            </td> 
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

