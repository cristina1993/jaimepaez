<?php
include_once '../Clases/clsUsers.php';
include_once '../Includes/permisos.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Menu</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function save(id,md_id,prc_id) {

                pro = $('#pro_' + id).val();
                mod = $('#mod_' + id).val();
                opl = $('#opl_' + id).val();
                ord = $('#ord_' + id).val();

                data = Array(pro, mod, opl, ord);

                $.post("actions_menus.php", {op: 0, 'data[]': data, id: id,md_id:md_id,prc_id:prc_id}, function (dt) {
                    if (dt == 0) {
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_menu.php';
                    } else {
                        alert(dt);
                    }
                });
            }
            function eliminar(id){
                $.post("actions_menus.php", {op: 1, id: id}, function (dt) {
                    if (dt == 0) {
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_menu.php';
                    } else {
                        alert(dt);
                    }
                });
            }
        </script>
        <style>
           #tbl input{
                border:none; 
            }
            #mn280{
                background:black;
                color:white;
                border: solid 1px white;
            }

        </style>


    </head>
    <body>
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
                <center class="cont_title" >
                    ADMINISTRACION DE MENUS
                </center>
            </caption>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Modulo</th>
                    <th>Menu</th>
                    <th>Sub-menu</th>
                    <th>Orden</th>
                    <th>Acciones</th>
                </tr>
            </thead>                
            <tbody>                
                <?php
                $n = 0;
                $cnsProc = $User->lista_todos_procesos();
                while ($rstProc = pg_fetch_array($cnsProc)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><input type="text" size="50" value="<?php echo $rstProc[proc_descripcion] ?>" id="<?php echo 'pro_' . $rstProc[opl_id] ?>" onchange="save('<?php echo $rstProc[opl_id] ?>','<?php echo $rstProc[mod_id] ?>','<?php echo $rstProc[proc_id] ?>')" /></td>
                        <td><input type="text" size="50" value="<?php echo $rstProc[mod_descripcion] ?>" id="<?php echo 'mod_' . $rstProc[opl_id] ?>" onchange="save('<?php echo $rstProc[opl_id] ?>','<?php echo $rstProc[mod_id] ?>','<?php echo $rstProc[proc_id] ?>')"/></td>
                        <td><input type="text" size="50" value="<?php echo $rstProc[opl_modulo] ?>" id="<?php echo 'opl_' . $rstProc[opl_id] ?>" onchange="save('<?php echo $rstProc[opl_id] ?>','<?php echo $rstProc[mod_id] ?>','<?php echo $rstProc[proc_id] ?>')"/></td>
                        <td><input type="text" size="2" value="<?php echo $rstProc[opl_orden] ?>" id="<?php echo 'ord_' . $rstProc[opl_id] ?>" onchange="save('<?php echo $rstProc[opl_id] ?>','<?php echo $rstProc[mod_id] ?>','<?php echo $rstProc[proc_id] ?>')"/></td>
                        <td align="center">
                            <img class="auxBtn" src="../img/del_reg.png" width="12px" onclick="eliminar('<?php echo $rstProc[opl_id] ?>')" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </body>
    <p id="back-top" style="display: block;">
        <a href="#" >&#9650;Inicio</a>
    </p>
</html>
