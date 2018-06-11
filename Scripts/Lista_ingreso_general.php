<?php
include_once '../Includes/permisos.php';
//include_once '../Clases/clsSetting.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso General</title>
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
                        frm.src = '../Scripts/Form_ingreso_general.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_ingreso_general.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function(dt) {
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
    #mn177{
         background:black;
         color:white;
         border: solid 1px white;
    }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                            <?php
                            $cns_sbm=$User->list_primer_opl(53,$_SESSION[usuid]);
                            while($rst_sbm=  pg_fetch_array($cns_sbm)){
                                ?>
                                    <font class="sbmnu" id="<?php echo "mn".$rst_sbm[opl_id]?>" onclick="window.location='<?php echo "../".$rst_sbm[opl_direccion].".php"?>'" ><?php echo $rst_sbm[opl_modulo]?></font>
                                <?php
                            }
                            ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >Ingreso General</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Usuario:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Concepto</th>
            <th>Tipo Documento</th>
            <th>No Documento</th>                                
            <th>Fecha Emision</th>
            <th>Descripcion/Razon</th>
            <th>Valor</th>
            <th>NIF</th>
            <th>Debe</th>
            <th>Haber</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[usu_person] ?></td>
                    <td><?php echo $rst[cup_xorden] ?></td>                     
                    <td><?php echo $rst[cup_mensual] ?></td>                    
                   <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                       <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[cup_id] ?>)">
                        <?php }
                        if ($Prt->edition == 0) {
                            ?>
                       <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cup_id] ?>, 0)">
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

