<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_tipoproducto.php'; //cambiar clsClase_productos
$Clase_tipoproducto = new Clase_tipoproducto();
$Set = new Set();
$mod = $_GET['mod'];
$rst_ti = pg_fetch_array($Set->lista_titulo($mod));
$des = strtoupper($rst_ti[mod_descripcion]);
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $texto = "where  upper(tps_siglas) like '%$txt%' or upper(tps_nombre) like '%$txt%' or upper(tps_observaciones) like '%$txt%'";
    }
    $cns = $Clase_tipoproducto->lista_buscardor_tipoproducto($texto);
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
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_tipoproducto.php?txt=' + $('#txt').val();//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_tipoproducto.php?id=' + id + '&txt=' + $('#txt').val();//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_tipoproducto.php?id=' + id + '&x=' + x + '&txt=' + $('#txt').val();//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        break;
                }
            }
            function del(id, op, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_tipoproducto.php", {act: 48, id: id, op: op, nom: nom}, function (dt) {
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
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >LISTA TIPOS </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" />
                        <!--                        <button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>-->
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Nombres</th>
            <th>Relación</th>
            <th>Siglas</th>
            <th>Observaciones</th>
            <th>Acciones</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                if ($rst['tps_relacion'] == '1') {
                    $rst['tps_relacion'] = 'Familia';
                } else {
                    $rst['tps_relacion'] = 'TIPO';
                }
                $ev = "onclick='auxWindow(2,$rst[tps_id],1)'";
                $n++;
                ?>
                <tr style="height: 30px" onclick="auxWindow(1,<?php echo $rst['tps_id'] ?>, 1)">
                    <td <?php echo $ev ?>><?php echo $n ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['tps_nombre'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['tps_relacion'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['tps_siglas'] ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['tps_observaciones'] ?></td>

                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                        <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rst[tps_id] ?>, 1, '<?php echo $rst[tps_nombre] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="12px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[tps_id] ?>, 0)">
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

