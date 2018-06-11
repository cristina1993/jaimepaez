<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$tbl_set = 'erp_mp_set';
$tbl = substr($tbl_set, 0, -4);
$tbl_name = 'mp';
$tp = 'mp_tipo';
$tp0 = 'mp_';
$Set = new Set();
$mod = $_GET[mod];
$rst_ti = pg_fetch_array($Set->lista_titulo($mod));
$des = strtoupper($rst_ti[mod_descripcion]);
$r_tip = pg_fetch_array($Set->lista_un_mp_mod($des));
$tipo = $r_tip[ids];
$head = pg_fetch_array($Set->lista_one_data($tbl_set, $tipo));

$tp_prod = explode('&', $head[1]);
$txt = trim(strtoupper($_GET[txt]));
$estado = $_GET[estado];
if (!empty($txt)) {
    $texto = "(mp_c like '%$txt%' or mp_d like '%$txt%') and ids=$tipo";
    $cns = $Set->lista_buscador($texto);
} else if ($estado != '') {
    $texto = "mp_i='$estado' and ids=$tipo";
    $cns = $Set->lista_buscador($texto);
} else {
//    $cns = $Set->lista_table_by_tipo($tbl, $tipo);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
    <head>
        <script>
            var des = '<?php echo $des ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
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
                        frm.src = '../Scripts/Form_' + tbl_name + '.php?tipo=' + tipo + '&des=' + des;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_' + tbl_name + '.php?id=' + id + '&tipo=' + tipo + '&x=' + x + '&des=' + des;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        if (x == 0) {
                            look_menu();
                        }
                        break;
                    case 2:
                        main.src = '../Scripts/Set_' + tbl_name + '.php?ol=<?php echo $_SESSION[ol] ?>';
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 3:
                        var imgs;
                        switch (tipo) {
                            case 0:
                                imgs = "<img src = '../img/activo.png' width='16px' class='auxBtn' onclick = 'auxWindow(3,1," + id + ",this)'>";
                                break;
                            case 1:
                                imgs = "<img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(3,0," + id + ",this)'>";
                                break;
                        }

                        $.post("actions.php", {act: 80, id: id, tbl: tipo}, function (dt) {
                            if (dt != 0) {
                                alert(dt);
                            } else {
                                var row = $(x).parents();
                                $(row[0]).html(imgs);
                            }
                        });
                        break;
                    case 4:
                        frm.src = '../Scripts/frm_pdf_etp_productos.php?id=' + id + '&tipo=' + tipo;
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        break;

                }

            }

            function del(id, tbl, dat, cod)
            {

                data = Array(dat);
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 6, tbl: tbl, id: id, 'data[]': data, cod: cod}, function (dt) {
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
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                </center>
                <center class="cont_title" >
                    <?php echo $rst_ti[mod_descripcion] ?>
                    <?php
                    if ($Prt->special == 0) {
                        ?>
                        <img class="auxBtn" src="../img/set.png" onclick="auxWindow(2)" width="16px" />
                    <?php }
                    ?>
                </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, '<?php echo $tipo ?>')" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" name="mod" id="mod" size="15" value="<?php echo $mod ?>" />
                        Codigo:<input type="text" name="txt" id="txt" size="15" value="<?php echo $txt ?>" />
                        Estado:<select name="estado" id="estado">
                            <option value="0">Activo</option>
                            <option value="1">Inactivo</option>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
            <th>No</th>
            <!--<th>Tipo</th>-->
            <?php
            $n = 2;
            while ($n <= count($head)) {
                $file = explode('&', $head[$n]);
                if (!empty($file[9]) && $file[3] == '0') {
                    if (trim($file[8]) != 'mp_i') {
                        ?>
                        <th><?php echo $file[9] ?></th>
                        <?php
                    }
                }
                $n++;
            }
            ?>
            <th>ESTADO</th>
            <th>Acciones</th>
        </thead>
        <tbody id="tbody">
            <?php
            $cn = 0;
            while ($rst = pg_fetch_array($cns)) {
                $cn++;
                ?>
                <tr>
                    <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $cn ?></td>   
                    <!--<td onclick="auxWindow(1,<?php //echo $rst[ids]  ?>,<?php //echo $rst[id]  ?>, 1)" ><?php //echo $tp_prod[9]  ?></td>-->   
                    <?php
                    $n = 2;
                    $cod = '';
                    $ref = '';
                    while ($n <= count($head)) {
                        $file = explode('&', $head[$n]);
                        if (!empty($file[9]) && $file[3] == '0') {
                            if ($file[2] == 'I') {
                                $value = $rst[$file[8]];
                                $rst[$file[8]] = "<img src='$value' width=64px />";
                            }

                            if ($file[2] == 'E') {
                                $rstEnlace = pg_fetch_array($Set->list_one_data_by_id_tip($file[6], $rst[$file[8]]));
                                $rst[$file[8]] = $rstEnlace[tps_nombre];
                            }
                            if (trim($file[8]) != 'mp_i') {
                                if ($n == 4) {
                                    $cod = $rst[$file[8]];
                                } elseif ($n == 5) {
                                    $ref = $rst[$file[8]];
                                }
                                ?>
                                <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $rst[$file[8]] ?></td>
                                <?php
                            } else {
                                $est = $rst[$file[8]];
                            }
                        }
                        $n++;
                    }
                    ?>
                    <?php
                    if ($est == 1) {
                        $rst[$file[8]] = " <img src = '../img/inactivo.png' width='16px' class='auxBtn' onclick = 'auxWindow(3, 0,$rst[id],this)'>";
                    } else {
                        $rst[$file[8]] = " <img src = '../img/activo.png' width='16px' class='auxBtn' onclick = 'auxWindow(3, 1, $rst[id],this)'>";
                    }
                    ?>
                    <td><?php echo $rst[$file[8]] ?></td>
                    <td align="center">

                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" title="Eliminar Registro" onclick="del(<?php echo $rst[id] ?>, '<?php echo $tbl ?>', '<?php echo $rst[$tp0 . 'a'] ?>', '<?php echo $rst[mp_c] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="12px" class="auxBtn" title="Editar Registro" onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 0)">
                            <?php
                        }
                        ?>
                            <img src="../img/etiqueta_prod.png" width="17px" class="auxBtn" title="Etiqueta" onclick="auxWindow(4, '<?php echo $cod ?>', '<?php echo $ref ?>', 0)">                                                    
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>
<script>
    var est = '<?php echo $estado ?>';
    $('#estado').val(est);
</script>

