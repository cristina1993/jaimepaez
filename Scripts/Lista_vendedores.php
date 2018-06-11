<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_vendedores.php'; //cambiar clsClase_productos
$Clase_vendedores = new Clase_vendedores();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $text = "where  CAST(vnd_local AS VARCHAR) like '%$txt%' or vnd_codigo like '%$txt%' or vnd_nombre like '%$txt%' ";
    }
    $cns = $Clase_vendedores->lista_buscardor_vendedores($text);
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_vendedores.php?txt=' + '<?php echo $txt ?>'//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_vendedores.php?id=' + id + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_vendedores.php?id=' + id + '&x=' + x + '&txt=' + '<?php echo $txt ?>';//Cambiar Form_productos
                        break;
                }
            }
            function del(id, op, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_vendedores.php", {act: 48, id: id, op: op, nom: nom}, function (dt) {
                        if (dt == 0)
                        {
//                            window.history.go(0);
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_vendedores.php';

                        } else {
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
                <center class="cont_title" >LISTA DE VENDEDORES</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt?>" />
                        <button class="btn" title="Buscar" id='search' name='search' onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Código</th>
            <th>Usuario</th>
            <th>Vendedor</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $num = $rst['vnd_local'];
                $rst1 = pg_fetch_array($Clase_vendedores->lista_un_vend($num));
                $ev = "onclick='auxWindow(2,$rst[identificacion],1)'";
                $n++;
                ?>
                <tr style="height: 30px" onclick="auxWindow(1,<?php echo $rst['mov_id'] ?>, 1)">
                    <td <?php echo $ev ?>><?php echo $n ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['vnd_codigo'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst1['usu_person'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['vnd_nombre'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rst[vnd_id] ?>, 1, '<?php echo $rst[vnd_codigo] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  width="12px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[vnd_id] ?>, 0)">
                            <?php
                        }
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

