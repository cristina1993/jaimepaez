<?php
include_once '../Includes/permisos.php';
include_once("../Clases/clsPuestoTrabajo.php");
$Puestos = new PuestoTrabajo();

if (isset($_GET['search'])) {
    if ($_GET['ger'] == '0' || $_GET['ger'] == '') {
        $cnsPuesto = $Puestos->listaAllPuestoTrabajo();
    } elseif ($_GET['pt_division'] == '0' || $_GET['pt_division'] == '') {
        $cnsPuesto = $Puestos->listaAllPuestoTrabajoG($_GET['ger']);
    } elseif ($_GET['sec_id'] == '0' || $_GET['sec_id'] == '') {
        $cnsPuesto = $Puestos->listaAllPuestoTrabajoGD($_GET['ger'], $_GET['pt_division']);
    } else {
        $cnsPuesto = $Puestos->listaAllPuestoTrabajoGDS($_GET['ger'], $_GET['pt_division'], $_GET['sec_id']);
    }
}
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Lista</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/postFormulario.php?ger=' + '<?php echo $_GET['ger'] ?>&pt_division=' + '<?php echo $_GET['pt_division'] ?>&sec_id=' + '<?php echo $_GET['sec_id'] ?>';//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/postFormulario.php?id=' + id + '&txt=' + '<?php echo $txt ?>&ger=' + '<?php echo $_GET['ger'] ?>&pt_division=' + '<?php echo $_GET['pt_division'] ?>&sec_id=' + '<?php echo $_GET['sec_id'] ?>';//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/postFormulario.php?id=' + id + '&x=' + x + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                        look_menu();
                        break;
                }
            }


            function loadDivision(g) {
                $.post("actions_puestos_trabajo.php", {op: 0, id: g},
                function (data) {
                    $("#pt_division").html(data);
                });
            }

            function loadSec(d)
            {
                $.post("actions_puestos_trabajo.php", {op: 1, id: d},
                function (data) {
                    $("#sec_id").html(data);
                });
            }
            function load()
            {
                loadDivision(ger.value);
            }

            function del(id, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_puestos_trabajo.php", {id: id, op: 6, data: doc}, function (dt) {
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
            .disabled{color:#5b74a8}
            input[type=text]{
                text-transform: uppercase;
            }
        </style> 
    </head>

    <!--    <body onload="load();
            pt.style.background = '#95BCE2'">-->
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
                <center class="cont_title" >LISTA PUESTOS DE TRABAJO</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <select id="ger" name="ger" onchange="loadDivision(this.value)" style="width: 160px" >
                            <!--                            <option value="">Seleccione</option>-->
                            <?php
                            $cns_g = $Puestos->lista_gerencias();
                            while ($rst_g = pg_fetch_array($cns_g)) {
                                echo "<option value='$rst_g[ger_id]'>$rst_g[ger_descripcion]</option>";
                            }
                            ?>
                        </select>
                        <select id="pt_division" name="pt_division" onchange="loadSec(this.value)" style="width: 160px">
                            <option value="0">Todos</option>
                            <?php
                            $cns_g = $Puestos->lista_divisiones_ger('2');
                            while ($rst_g = pg_fetch_array($cns_g)) {
                                echo "<option value='$rst_g[div_id]'>$rst_g[div_descripcion]</option>";
                            }
                            ?>

                        </select>
                        <select id="sec_id" name="sec_id" style="width: 160px">
                            <option value="0">Todos</option>
                            <?php
                            $cns_s = $Puestos->lista_secciones_div($_GET[pt_division]);
                            while ($rst_s = pg_fetch_array($cns_s)) {
                                echo "<option value='$rst_s[sec_id]'>$rst_s[sec_descricpion]</option>";
                            }
                            ?>
                        </select>
                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th align="Center">No</th>
                    <th align="Center">Gerencia</th>
                    <th align="Center">Division</th>
                    <th align="Center">Seccion</th>
                    <th align="Center">Cargo</th>
                    <th align="Center">Responsabilidad</th>
                    <th align="Center">Marca</th>
                    <th align="Center">Turno</th>
                    <th align="Center">Puesto</th>                                                
                    <th align="Center"></th>                                                
                    <th align="Center"></th>                                                
                    <th align="Center">Alm</th>                                                
                    <th align="Center">Codigo Horario1</th>                        
                    <th align="Center">Codigo Horario2</th>                                                
                    <th align="Center">Codigo Horario3</th>                                                
                    <th <?php echo $prt->edition ?> align="Center">Editar</th>                        
                </tr>     
            </thead>
            <?php
            $g = 0;
            $no = 0;


            while ($rst = pg_fetch_array($cnsPuesto)) {
                $no++;
                $grn = 'PRUEBA';

                switch ($rst[pt_division]) {
                    case '4':
                        $dv = 'MANTENIMIENTO';
                        break;
                    case '1':
                        $dv = 'GENERAL';
                        break;
                    case '3':
                        $dv = 'POLIETILENO';
                        break;
                    case '5':
                        $dv = 'POLIURETANO';
                        break;
                }

                if ($rst['pt_almuerzo'] == 't') {
                    $alm = 'SI';
                } else {
                    $alm = 'NO';
                }
                ?>
                <tr>
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $no ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $grn ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $dv ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['sec_descricpion'] ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_cargo'] ?></td>                    
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_responsabilidad'] ?></td>                    
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_marca'] ?></td>                    
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_turno1'] . "," . $rst['pt_turno2'] . "," . $rst['pt_turno3'] ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_puesto'] ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_puesto_superior'] ?></td>                    
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $rst['pt_nivel'] ?></td>          
                    <td style="<?php echo $border ?>" align="center"><?PHP echo $alm ?></td>          
                    <?php
                    if ($rst['pt_turno1'] == 1 || $rst['pt_turno1'] == 2 || $rst['pt_turno1'] == 3 || $rst['pt_turno1'] == 5 || $rst['pt_turno1'] == 8) {
                        $cod1 = $rst['pt_codigo'];
                    } else {
                        $cod1 = '...';
                    }

                    if ($rst['pt_turno2'] == 4 || $rst['pt_turno2'] == 6) {
                        $cod2 = $rst['pt_codigo2'];
                    } else {
                        $cod2 = '...';
                    }
                    if ($rst['pt_turno3'] == 7) {
                        $cod3 = $rst['pt_codigo3'];
                    } else {
                        $cod3 = '...';
                    }
                    ?>          
                    <td style="<?php echo $border ?>" align="left"><a class="<?php echo $prt->view ?>" href="javascript:editar(id=<?php echo $rst['pt_id'] ?>,x=1)"><?PHP echo $cod1 ?></a></td>                                          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $cod2 ?></td>          
                    <td style="<?php echo $border ?>" align="left"><?PHP echo $cod3 ?></td>  
                    <td style="<?php echo $border ?>" align="left" >
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rst['pt_id'] ?>, '<?php echo $cod1 ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="12px" class="auxBtn"  onclick="auxWindow(1,<?php echo $rst['pt_id'] ?>, 0)"></img>                      
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </body>
</html>
<script>
    var d = '<?php echo $_GET[pt_division] ?>';
    var s = '<?php echo $_GET[sec_id] ?>';

    $('#pt_division').val(d);
    $('#sec_id').val(s);

</script>