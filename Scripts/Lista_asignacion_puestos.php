<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_asignacion_puestos.php';
$Asig = new Clase_asignacion_puestos();
//$cns_sec = $Asig->lista_division();
$ger = 2;
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $emp = trim(strtoupper($_GET[emp]));
    $sec = $_GET[sec];
    $doc = trim(strtoupper($_GET[doc]));
    if (empty($doc) && empty($emp) && $sec == 0) {
        $cnsp = $Asig->list_per_vac_fecha($desde, $hasta);
    } elseif (!empty($doc)) {
        $cnsp = $Asig->list_per_vac_doc($doc);
    } elseif (!empty($emp) && empty($doc)) {
        $cnsp = $Asig->list_per_vac_emp($emp, $desde, $hasta);
    } elseif (empty($emp) && empty($doc) && $sec != 0) {
        $cnsp = $Asig->list_per_vac_sec($sec, $desde, $hasta);
    }
} else {
    $desde = date("Y-m-d");
    $hasta = date("Y-m-d");
}
?>

<head>
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


            function auxWindow(a, id, g, div, y)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_asignacion_puestos.php?id=' + id + '&gr=' + g + '&dv=' + div + '&year=' + y;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/reg_permisos_form.php?id=' + id + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
//                        look_menu();
                        break;
                    case 2://reporte
                        frm.src = '../Scripts/frm_pdf_asignacion_puestos.php?gr=' + g + '&dv=' + div + '&id=' + id + '&year=' + y;//Cambiar Form_productos
//                        look_menu();
                        break;
                }
            }
            function del(id, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_transportista.php", {id: id, op: 1, data: doc}, function (dt) {
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
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }


        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:50%" id="tbl">
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
                <center class="cont_menu" >
                    <?php
                    $cns_div = $Asig->lista_division();
                    while ($rst_div = pg_fetch_array($cns_div)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_div[div_id] ?>" onclick="window.location = '<?php echo "Lista_asignacion_puestos.php?div_id=" . $rst_div[div_id] ?>'"><?php echo $rst_div[div_descripcion] ?></font>
                        <?php
                    }
                    ?>
                </center> 
                <?php
                if (isset($_REQUEST[div_id])) {
                    $rst_d = pg_fetch_array($Asig->lista_una_division($_REQUEST[div_id]));
                    ?>
                    <center class="cont_title" ><?php echo $rst_d[div_descripcion] ?></center>
                </caption>
                <thead>
                    <?php
                    $col = pg_num_rows($Asig->lista_una_seccion_div($_REQUEST[div_id])) + 2;
                    ?>
                    <tr>
                        <th align="Center" colspan="<?php echo $col ?>"></th>
                        <th align="Center" colspan="3">REGULAR</th>
                        <th align="Center" colspan="3">EXTRAS</th>
                    </tr>
                    <tr>

                        <th align="Center">AÑO</th>
                        <th align="Center">SEM</th>
                        <?php
                        $cns_sec = $Asig->lista_una_seccion_div($_REQUEST[div_id]);
                        while ($rstSec = pg_fetch_array($cns_sec)) {
                            echo "<th align='Center'>$rstSec[sec_nombre]</th>";
                        }
                        ?>
                        <th align="Center">TOT</th>
                        <th align="Center">REPORTE</th>
                        <th align="Center">EDIT</th>
                        <th align="Center">TOT</th>
                        <th align="Center">REPORTE</th>
                        <th align="Center">EDIT</th>
                    </tr>
                </thead>  
                <tbody>
                    <?php
                    $cn = 0;
                    $v = 0;
                    $fecha = date('Y-m-d');
                    $sem = date_format(date_create($fecha), 'W');
                    $y = explode('-', $fecha);
                    $s = $sem - 4;

                    if ($s < 0) {
                        $ya = $y[0] - 1;
                        $date = new DateTime;
                        $date->setISODate($ya, 53);
                        $date->format("W");
                        if ($date->format("W") == "53") {
                            $sa = 53;
                        } else {
                            $sa = 52;
                        }

                        $s = $sa - abs($s);
                        $a = $y[0] - 1;
                    }

                    if ($sem == 53 || $sem == 52) {
                        $act = $sem;
                        $ant = $sem - 1;
                        $post = 1;
                    } else if ($sem == 1) {
                        $act = $sem;
                        $ant = $sa;
                        $post = $sem + 1;
                    } else {
                        $act = $sem;
                        $ant = $sem - 1;
                        $post = $sem + 1;
                    }


                    while ($n < 8) {
                        $n++;
                        if ($s > $sa) {
                            $s = 1;
                            $a = $y[0];
                        }
                        if ($s == $sem) {
                            $style = "style='background-color:#FF8080;'";
                        } else {
                            $style = '';
                        }
                        $t_cEmpByPt = 0;
                        ?>
                        <tr <?php echo $style ?>>
                            <td align="right"><?php echo $a ?></td>
                            <td align="right"><?php echo number_format($s) ?></td>
                            <?php
                            $cns_sec = $Asig->lista_una_seccion_div($_REQUEST[div_id]);
                            while ($rstSec = pg_fetch_array($cns_sec)) {
                                $cns_count = $Asig->lista_contador($rstSec[sec_id], $s, $a);
                                $cEmpByPt = 0; //Contador de empleados por puestos de trabajo
                                while ($rstNumPt = pg_fetch_array($cns_count)) {
                                    if ($rstNumPt[emp_id1] > 0) {
                                        $cEmpByPt++;
                                    }
                                    if ($rstNumPt[emp_id2] > 0) {
                                        $cEmpByPt++;
                                    }
                                    if ($rstNumPt[emp_id3] > 0) {
                                        $cEmpByPt++;
                                    }
                                }
                                ?>
                                <td align="right"><?php echo $cEmpByPt ?></td>
                                <?php
                                $t_cEmpByPt+=$cEmpByPt;
                            }
                            ?>
                            <td align="right"><?php echo number_format($t_cEmpByPt) ?></td>
                            <td align="center">
                                <img src="../img/orden.png" width="16px"  class="auxBtn" onclick="auxWindow(2, <?php echo number_format($s) ?>,<?php echo $ger ?>,<?php echo $_REQUEST[div_id] ?>,<?php echo $a ?>)">
                            </td>
                            <?php
                            if ($s == $ant || $s == $act || $s == $post) {
                                ?>
                                <td  align="center" <?php echo $prt->edition ?> onclick="auxWindow(0,<?php echo number_format($s) ?>,<?php echo $ger ?>,<?php echo $_REQUEST[div_id] ?>,<?php echo $a ?>)" >
                                    <img class="auxBtn" src="../img/upd.png" width="16px" />
                                </td> 
                                <?php
                            } else {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td align="right"><?php echo number_format($s) ?></td>
                            <td align="center">
                                <img src="../img/orden.png" width="16px"  class="auxBtn" onclick="auxWindow(2, <?php echo number_format($s) ?>,<?php echo $ger ?>,<?php echo $_REQUEST[div_id] ?>,<?php echo $a ?>)">
                            </td>
                            <?php
                            if ($s == $ant || $s == $act || $s == $post) {
                                ?>
                                <td  align="center" <?php echo $prt->edition ?> onclick="auxWindow(0)" >
                                    <img class="auxBtn" src="../img/upd.png" width="16px" />
                                </td> 
                                <?php
                            } else {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                        $s++;
                    }
                    ?>
                </tbody>
            </table>
            <form method="POST" style="display:block" id="frm_excel" action="../Includes/excel_permisos.php">
                <input type="hidden" name="cns" id="cns" value="<?php echo $cnsp2 ?>" />
            </form>
        </body>
    </html>  
    <script>
        var secc = '<?php echo $sec ?>';
        $('#sec').val(secc);
    </script>
    <?php
}
?>