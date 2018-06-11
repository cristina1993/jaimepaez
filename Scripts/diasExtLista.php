<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsTimbradas.php';
$Dex = new Timbradas();
$cnsDex = $Dex->listDiasExtraordinarios();
?>
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
                    frm.src = '../Scripts/diasExtFormulario.php?txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                    look_menu();
                    break;
                case 1://Editar
                    parent.document.getElementById('contenedor2').rows = "*,80%";
                    frm.src = '../Scripts/diasExtFormulario.php?id=' + id + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                    look_menu();
                    break;
                case 2://Editar
                    parent.document.getElementById('contenedor2').rows = "*,80%";
                    frm.src = '../Scripts/diasExtFormulario.php?id=' + id + '&x=' + x + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                    look_menu();
                    break;
            }
        }

        function del(id, doc)
        {
            var r = confirm("Esta Seguro de eliminar este elemento?");
            if (r == true) {
                $.post("actions_dias_ext.php", {id: id, op: 1, data: doc}, function (dt) {
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
</head>
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
        <center class="cont_title" >DIAS EXTRAORDINARIOS</center>
        <center class="cont_finder">
            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
            <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
            </form>  
        </center>
    </caption>
    <thead>
        <tr>
            <th>No</th>
            <th>A&ntilde;o</th>
            <th>Sem</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Obs</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $n = 0;
        while ($rstDex = pg_fetch_array($cnsDex)) {
            $n++;
            switch ($rstDex[dex_tipo]) {
                case 0:$type = 'FERIADO';
                    break;
                case 1:$type = 'REGULAR';
                    break;
            }
            ?>
            <tr>
                <td align="center" ><?php echo $n ?></td>
                <td align="center" ><?php echo $rstDex[dex_anio] ?></td>
                <td align="center" ><?php echo date("W", strtotime($rstDex[dex_fecha])) ?></td>
                <td align="center" ><?php echo date("d-M-Y", strtotime($rstDex[dex_fecha])) ?></td>
                <td align="center" ><?php echo $type ?></td>
                <td align="center" ><?php echo $rstDex[dex_obs] ?></td>
                <td align="center" >
                    <?php
                    if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rstDex[dex_id] ?>, '<?php echo $rstDex[dex_obs] ?>')">
                            <?php
                        }
                    if ($Prt->edition == 0) {
                        ?>
                        <img src="../img/upd.png" width="12px" class="auxBtn" onclick="auxWindow(1,<?php echo $rstDex[dex_id] ?>, 0)">
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>    

</body>