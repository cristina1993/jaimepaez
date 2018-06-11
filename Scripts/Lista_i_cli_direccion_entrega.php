<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Cliente Direccion Entrega</title>
    <head>
        <script>
            $(function() {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,50%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cli_direccion_entrega.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cli_direccion_entrega?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elementossss?");
                if (r == true) {
                    $.post("actions.php", {act: 31, id: id}, function(dt) {
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
            #mncm{
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
                        </center>
                        <center class="cont_finder">
                            <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>                           
                            <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >                                                                                       
                            </form>  
                        </center>
                    </caption>
                    <!--Nombres de la columna de la tabla-->
                    <thead>
                    <th>No.</th>
                    <th>Local</th>
                    <th>Calle Principal</th>
                    <th>Numeración</th>
                    <th>Calle Secundaria</th>                    
                    <th>Telefono</th>           
                    <th>Acciones</th>                
                    </thead>
                    <tbody id="tbody">
                        <?PHP
                        $cns = $Set->lista_direccion_entrega();
                        $n = 0;
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;
                            ?>
                            <tr>
                                <td><?php echo $n ?></td>
                                <td><?php echo $rst[cde_local] ?></td>
                                <td><?php echo $rst[cde_cal_principal] ?></td>
                                <td><?php echo $rst[cde_numeracion] ?></td>
                                <td><?php echo $rst[cde_cal_secundaria] ?></td>                                
                                <td><?php echo $rst[cde_telefono] ?></td>                                
                                <td align="center">
                                    <?php
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <?php
                                    }
                                    if ($Prt->delete == 0) {
                                        ?>
                                    <?php }
                                    ?>
                                    <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cde_id] ?>, 0)">
                                    <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[cde_id] ?>)">
                                </td>
                            </tr>  
                            <?PHP
                        }
                        ?>
                    </tbody>
                </table>            
            </body>                          
    </table>            

</body>    
</html>

