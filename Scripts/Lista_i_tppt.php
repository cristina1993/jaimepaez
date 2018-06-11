<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {

    $cns = $Set->lista_tppt_search(trim(strtoupper($_GET[txt])));
} else {
    $cns = $Set->lista_tppt();
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
                    case 0:
                        frm.src = '../Scripts/Form_i_tppt.php';
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_tppt.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 18, id: id}, function (dt) {
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
            #mn226{
                background:black;
                color:white;
                border: solid 1px white;
            }

        </style>
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >TIPOS DE PRODUCTOS</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;   " title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>

            <thead>
            <th width="50px">No</th>
            <th width="150px">Tipo</th>
            <th width="150px">Relacion</th>
            <th width="100px">Siglas</th>
            <th width="400px">Nombre</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </thead>
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[tpt_tipo]) {
                    case 0:$tp = 'Materia Prima';
                        break;
                    case 1:$tp = 'Producto Terminado';
                        break;
                    case 2:$tp = 'Otros';
                        break;
                    default :
                        $tp = 'NA';
                        break;
                }
                switch ($rst[tpt_relacion]) {
                    case 0:$tpr = 'Familia';
                        break;
                    case 1:$tpr = 'Tipo';
                        break;
                    default :
                        $tpr = 'NA';
                        break;
                }
                
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $tp?></td>
                    <td><?php echo $tpr ?></td>
                    <td><?php echo $rst[tpt_siglas] ?></td>
                    <td><?php echo $rst[tpt_nombre] ?></td>
                    <td><?php echo $rst[tpt_obs] ?></td>

                    <td align="center">
    <?php
    if ($Prt->delete == 0) {
        ?>
                            <img src="../img/del_reg.png" class="auxBtn" width="16px" onclick="del(<?php echo $rst[tpt_id] ?>)">

                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" class="auxBtn" width="16px" onclick="auxWindow(1,<?php echo $rst[tpt_id] ?>, 0)">
                <?php }
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

