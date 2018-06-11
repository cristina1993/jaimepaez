<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_pedidospt.php';
include_once '../Clases/clsClase_asientos.php';
$Clase_asientos = new Clase_asientos();
$Clase_pedidospt = new Clase_pedidospt();
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
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
    $cns = $Clase_asientos->lista_total_asientos_fecha($txt);
} else {
    $txt = '';
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

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_asientos.php?id=' + id;
                        break;
                    case 1://Nuevo
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_libro_diario.php?desde=' + id + "&hasta=" + x;
                        break;
                }

            }
            function del(id, op)
            {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_asientos.php", {act: 48, id: id, op: op}, function (dt) {
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
            #mn157,
            #mn159,
            #mn161,
            #mn163,
            #mn165,
            #mn167,
            #mn169,
            #mn171,
            #mn173,
            #mn175,
            #mn111{
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
                    <img class="auxBtn" style="float:right" onclick="auxWindow(1, '<?php echo $fec1 ?>', '<?php echo $fec2 ?>')" title="Imprimir Reporte"  src="../img/orden.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?PHP echo 'REPORTE LIBRO DIARIO' ?></center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" />
                        PERIODO DESDE:<input type="text" size="15" name="fecha1" id="fecha1" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>

                    <th>Asiento No</th>
                    <th>No</th>
                    <th>Fecha Emision</th>
                    <th>Codigo</th>
                    <th>Cuenta</th>
                    <th>Documento</th>
                    <th>Concepto</th>
                    <th>Debe</th>
                    <th>Haber</th>
                    <th>Estado</th>
                    <th>Acciones</th>                    
                </tr>

            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                while ($rst = pg_fetch_array($cns)) {
                    $cns_cuentas = $Clase_asientos->lista_cuentas_asientos($rst[con_asiento]);
                    $cuentas1 = Array();
                    while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
                        if (!empty($rst_cuentas[con_concepto_debe])) {
                            array_push($cuentas1, $rst_cuentas[con_concepto_debe]);
                        }

                        if (!empty($rst_cuentas[con_concepto_haber])) {
                            array_push($cuentas1, $rst_cuentas[con_concepto_haber]);
                        }
                    }

                    $cuentas = array_unique($cuentas1);

                    $n = 0;
                    $j = 1;
                    while ($n < count($cuentas)) {
                        $rst_cuentas1 = pg_fetch_array($Clase_asientos->listar_descripcion_asiento($cuentas[$n]));
                        $rst_v = pg_fetch_array($Clase_asientos->listar_debe_haber_asiento_cuenta($rst[con_asiento], $cuentas[$n]));

                        if ($rst_v[estado] == 0) {
                            $rst_v[estado] = 'PENDIENTE';
                            $r = 1;
                        }
                        if ($rst_v[estado] == 1) {
                            $rst_v[estado] = 'COMPLETADO';
                            $es = 0;
                        }
                        ?>
                        <tr>
                            <?PHP
                            if ($j == 1) {
                                ?>
                                <td><?php echo $rst[con_asiento] ?></td>
                                <?PHP
                            } else {
                                ?>
                                <td></td>
                                <?PHP
                            }
                            ?>
                            <td><?php echo $j ?></td>
                            <td td align="center" ><?php echo $rst_v[fecha] ?></td>
                            <td><?php echo $rst_cuentas1[pln_codigo] ?></td>
                            <td><?php echo $rst_cuentas1[pln_descripcion] ?></td>
                            <td><?php echo $rst_v[documento] ?></td>
                            <td><?php echo $rst_v[concepto] ?></td>
                            <td align="right" ><?php echo number_format($rst_v[debe], 2) ?></td>
                            <td align="right"  ><?php echo number_format($rst_v[haber], 2) ?></td>
                            <td><?php echo $rst_v[estado] ?></td>
                            <td>
                                <?PHP
                                if ($j == 1) {
                                    if ($r != 1) {
                                        ?>
                                        <img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(0, '<?php echo $rst[con_asiento] ?>')">
                                        <?PHP
                                    }
                                }
                                ?>
                            </td>             
                        </tr>  
                        <?PHP
                        $n++;
                        $j++;
                    }
                    $r=0;
                }
                ?>

            </tbody>
        </table>            
    </body>    
</html>

