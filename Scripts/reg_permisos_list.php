<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsPermisosVacaciones.php';
$PerVac = new VacacionesPermisos();
$cns_sec = $PerVac->listSecciones();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $emp = trim(strtoupper($_GET[emp]));
    $sec = $_GET[sec];
    $doc = trim(strtoupper($_GET[doc]));
    if (empty($doc) && empty($emp) && $sec == 0) {
        $cnsp = $PerVac->list_per_vac_fecha($desde, $hasta);
    } elseif (!empty($doc)) {
        $cnsp = $PerVac->list_per_vac_doc($doc);
    } elseif (!empty($emp) && empty($doc)) {
        $cnsp = $PerVac->list_per_vac_emp($emp, $desde, $hasta);
    } elseif (empty($emp) && empty($doc) && $sec != 0) {
        $cnsp = $PerVac->list_per_vac_sec($sec, $desde, $hasta);
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


            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/reg_permisos_form.php?txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/reg_permisos_form.php?id=' + id + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
//                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/reg_permisos_form.php?id=' + id + '&x=' + x + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                        look_menu();
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
                <center class="cont_title" >REGISTRO DE PERMISOS Y VACACIONES</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        DESDE:<input type="text"   name="desde" value="<?php echo $desde ?>"  id="desde" size="10" />
                        <img src="../Img/calendar.png" width="16"  id="im-desde" />
                        HASTA:<input type="text"   name="hasta" value="<?php echo $hasta ?>"  id="hasta" size="10" />
                        <img src="../Img/calendar.png" width="16"  id="im-hasta" />
                        <select name="sec" id="sec" style="width: 200px">
                            <option value=0 >Seccion</option>
                            <?php
                            while ($rstSec = pg_fetch_array($cns_sec)) {
                                echo "<option value='$rstSec[sec_id]'>$rstSec[ger_descripcion] - $rstSec[div_descripcion] - $rstSec[sec_descricpion]</option>";
                            }
                            ?>
                        </select>
                        Empleado:<input type="text" name="emp" size="20" value="<?php echo $emp ?>"/>
                        Documento:<input type="text" name="doc" size="20" value="<?php echo $doc ?>"/>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th align="Center">No</th>
                    <th align="Center">Codigo</th>
                    <th align="Center">Empleado</th>
                    <th align="Center">Division</th>
                    <th align="Center">Seccion</th>
                    <th align="Center">Documento</th>                        
                    <th align="Center">Fecha Inicio</th>
                    <th align="Center">Fecha Fin</th>
                    <th align="Center">Hora Inicio </th>
                    <th align="Center">Hora Fin</th>
                    <th align="Center">Motivo</th>                        
                    <th align="Center">Descripcion</th>
                    <th align="Center">Observaciones</th>
                    <th align="Center">Acciones</th>
                </tr>
            </thead>  
            <tbody>
                <?php
                $cn = 0;
                while ($rstp = pg_fetch_array($cnsp)) {
                    $cn++;
                    $div = pg_fetch_array($PerVac->lista_una_division($rstp[sec_area]));

                    if ($rstp[reg_vac_hinicio] == 0) {
                        $rstp[reg_vac_hinicio] = '00:00';
                    }
                    if ($rstp[reg_vac_hfinal] == 0) {
                        $rstp[reg_vac_hfinal] = '00:00';
                    }
                    ?>
                    <tr>
                        <td align="right"><?php echo $cn ?></td>
                        <td align="right"><?php echo $rstp[emp_codigo] ?></td>
                        <td><?php echo $rstp[emp_apellido_paterno] . ' ' . $rstp[emp_apellido_materno] . ' ' . $rstp[emp_nombres] ?></td>
                        <td><?php echo $div[div_descripcion] ?></td>
                        <td><?php echo $rstp[sec_descricpion] ?></td>
                        <td align="left"><?php echo $rstp[reg_vac_documento] ?></td>                              
                        <td><?php echo $rstp[reg_vac_finicio] ?></td>
                        <td><?php echo $rstp[reg_vac_ffinal] ?></td>
                        <td align="right"><?php echo $rstp[reg_vac_hinicio] ?></td>
                        <td align="right"><?php echo $rstp[reg_vac_hfinal] ?></td>
                        <td><?php echo $rstp[crp_descripcion] ?></td>                              
                        <td align="left"><?php echo $rstp[reg_vac_descripcion] ?></td>
                        <td><?php echo $rstp[reg_vac_obs] ?></td>
                        <td  align="center" <?php echo $prt->edition ?> onclick="auxWindow(1,<?PHP echo $rstp[reg_vac_id] ?>)" >
                            <img class="auxBtn" src="../img/upd.png" width="20px" />
                        </td>          
                    </tr>
                    <?php
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