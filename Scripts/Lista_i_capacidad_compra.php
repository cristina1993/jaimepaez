<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if(isset($_GET[txt])){
    $cns=$Set->lista_capacidad_compra_search(trim(strtoupper($_GET[txt])));    
}else{
            $cns = $Set->lista_capacidad_de_compra(); // FUNCION clsSetting ( Clases>clsSetting )
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Capacidad de Compra</title>
    <head>
        <script>
            $(function() {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows="*,50%";
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
                    case 0://NUevo
                        frm.src = '../Scripts/Form_i_capacidad_compra.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_capacidad_compra.php?id='+id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 39, id: id}, function(dt) {
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
    #mn34{
         background:black;
         color:white;
         border: solid 1px white;
    }
        </style>
    </head>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                            <?php
                            $cns_sbm=$User->list_primer_opl(19,$_SESSION[usuid]);
                            while($rst_sbm=  pg_fetch_array($cns_sbm))
                            {
                                ?>
                                    <font class="sbmnu" id="<?php echo "mn".$rst_sbm[opl_id]?>" onclick="window.location='<?php echo "../".$rst_sbm[opl_direccion].".php"?>'" ><?php echo $rst_sbm[opl_modulo]?></font>
                                <?php
                            }
                            ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >Capacidad de Compra</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Monto Maximo</th>
            <th>Monto Minimo</th>
            <th>% Descuento</th>
            <th>Acciones</th>
        </thead>                
        <!--------------------------------->

        <tbody id="tbody">
            <?PHP

            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[cap_codigo] ?></td>
                    <td><?php echo $rst[cap_monto_maximo] ?></td>
                    <td><?php echo $rst[cap_monto_minimo] ?></td>
                    <td><?php echo $rst[cap_descuento] ?></td>
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
                        <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cap_id] ?>, 0)">
                        <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[cap_id] ?>)">
                    </td>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>


    </table>            

</body>    
</html>

