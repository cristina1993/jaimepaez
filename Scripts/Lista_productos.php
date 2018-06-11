<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciospt.php';
$tbl_set = 'erp_productos_set';
$tbl = substr($tbl_set, 0, -4);
$tbl_name = 'productos';
$tp = 'pro_tipo';
$tp0 = 'pro_';
$Set = new Set();
$Clase_preciospt = new Clase_preciospt();
$tipo = $_GET[tipo];

if (isset($_GET[tipo])) {
    $head = pg_fetch_array($Set->lista_one_data($tbl_set, $tipo));
    $cns = $Set->lista_table_by_tipo($tbl, $tipo);
    $tp_prod = explode('&', $head[1]);
} else if (isset($_GET[txt])) {
    if ($_GET[txt] != '') {
        $prod = strtoupper($_GET[txt]);
        $rst0 = pg_fetch_array($Set->lista_one_table_code_finder($prod));
        $cns = $Set->lista_one_table_code_finder($prod, $tipo);
        $head = pg_fetch_array($Set->lista_one_data($tbl_set, $rst0[ids]));
        $tp_prod = explode('&', $head[1]);
        $tipo = $_GET[tipo] = $rst0[ids];
    } else {
        $tipo = $_GET[tipo] = '0';
        $cns = $Set->lista_productos();
        $head = pg_fetch_array($Set->lista_data($tbl_set));
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
            });
            var tbl_name = '<?php echo $tbl_name ?>';
            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, tipo, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Form_' + tbl_name + '.php?tipo=' + tipo;
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_' + tbl_name + '.php?id=' + id + '&tipo=' + tipo + '&x=' + x;
                        if (x == 0)
                        {
                            look_menu();
                        }
                        break;
                    case 2:
                        main.src = '../Scripts/Set_' + tbl_name + '.php?ol=<?php echo $_SESSION[ol] ?>';
                        break;

                    case 3:
//                        alert(x);
                        $.post("actions_preciospt.php", {op: 1, id: id, tab: x}, function (dt) {
                            if (dt == 0)
                            {
                                window.history.go(0);
                            } else {
                                alert(dt);
                            }
                        });
                        break;

                }

            }

            function del(id, tbl, dat)
            {
                data = Array(dat);
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 6, tbl: tbl, id: id, 'data[]': data}, function (dt) {
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

            function loadData(id)
            {
                window.location = 'Lista_' + tbl_name + '.php?tipo=' + id;
            }

        </script>    
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <select style="float:left;margin-top:5px;margin-left:5px;" onchange="loadData(this.value, '<?php echo $tbl_set ?>')">
                        <option value="0">Seleccione Tipo</option>
                        <?php
                        $cnsTipos = $Set->lista_by_tipo($tbl_set);
                        while ($rst = pg_fetch_array($cnsTipos)) {
                            if ($_GET[tipo] == $rst[ids]) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            $val = explode('&', $rst[$tp]);
                            echo "<option $selected value=$rst[ids]>$val[9]</option>";
                        }
                        ?>
                    </select>   
                    <font size="2" style="margin-left:30% "></font>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <?php
                    if ($Prt->special == 0) {
                        ?>
                        <img class="auxBtn" src="../img/set.png" onclick="auxWindow(2)" width="16px" />
                    <?php }
                    ?>
                </center>
                <center class="cont_title" ><?php echo $tbl_name ?></center>
                <center class="cont_finder">
                    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="frmSearch" name="frm1" style="margin:0px 5px 10px 0px;">
                        <?php
                        if ($Prt->add == 0) {
                            ?>
                            <a href="#" class="btn" title="Nuevo Registro" onclick="auxWindow(0,<?php echo $_GET[tipo] ?>)" >Nuevo</a>
                            <?php
                        }
                        ?>
                        <input type="hidden" name="tp" size="15" value="<?php echo $tipo ?>" />
                        Codigo:<input type="text" size="15" id="txt" name="txt" />
                        <a href="#" style="position:absolute " class="btn" title="Buscar" onclick="frmSearch.submit()" ><img src="../img/finder.png" /></a>
                    </form>  
                </center>
            </caption>
            <thead>
            <th>No</th>
            <th>Tipo</th>
            <?php
            $n = 2;
            while ($n <= count($head)) {
                $file = explode('&', $head[$n]);
                if (!empty($file[9]) && $file[3] == '0') {
                    ?>
                    <th><?php echo $file[9] ?></th>
                    <?php
                }
                $n++;
            }
            ?>
            <th>Precios</th>
            <th>Acciones</th>
        </thead>
        <tbody id="tbody">
            <?php
            $cn = 0;
            while ($rst = pg_fetch_array($cns)) {
                $cn++;
                $enc = pg_fetch_array($Set->lista_one_data($tbl_set, $rst[ids]));
                $tp_prod = explode('&', $enc[1]);
                ?>
                <tr>
                    <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $cn ?></td>   
                    <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $tp_prod[9] ?></td>   
                    <?php
                    $n = 2;
                    while ($n <= count($head)) {
                        $file = explode('&', $head[$n]);
                        if (!empty($file[9]) && $file[3] == '0') {
                            if ($file[2] == 'I') {
                                $value = $rst[$file[8]];
                                $rst[$file[8]] = "<img src='$value' width=64px />";
                            }
                            if ($file[2] == 'E') {
                                $rstEnlace = pg_fetch_array($Set->list_one_data_by_id($file[6], $rst[$file[8]]));
                                $rst[$file[8]] = $rstEnlace[ins_a];
                            }
                            ?>
                            <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $rst[$file[8]] ?></td>
                            <?php
                        }
                        $n++;
                    }
                    ?>
                    <td align="center">
                        <?php
                        $num_precio = pg_num_rows($Clase_preciospt->lista_precios_proid_tabla($rst[id], 1));
                        if ($num_precio != 1) {
                            ?>  
                            <img src = "../img/b_delete.png" onclick = "auxWindow(3,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)">

                            <?php
                        } else {
                            ?>
                            <img src="../img/exito.png">
                            <?php
                        }
                        ?> 
                    </td>
                    <td align="center">
                        <?php
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 0)">
                            <?php
                        }
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png" onclick="del(<?php echo $rst[id] ?>, '<?php echo $tbl ?>', '<?php echo $rst[$tp0 . 'a'] ?>')">
                        <?php }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>


    </table>            

</body>    
</html>

