<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
//if(isset($_GET[txt])){
//    $cns_cp=$Set->lista_capacidad_de_compra(trim(strtoupper($_GET[txt])));    
//}else{
 $cns_cp=$Set->lista_capacidad_de_compra(); 
//}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Descuentos</title>
    <head>
        <script>
            $(function() {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows="*,0%";
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
                        frm.src = '../Scripts/Form_i_descuentos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_descuentos.php?id=' + id;
                        look_menu();
                        break;
                }
            }
            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 29, id: id}, function(dt) {
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
    #mn37{
         background:black;
         color:white;
         border: solid 1px white;
    }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:52%" id="tbl">
            <caption  class="tbl_head">
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
                <center class="cont_title" >Descuentos</center>
                <center class="cont_finder">                   
                    <form method="GET" id="frmSearch" hidden name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo : <input type="text" name="txt" size="19" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>

            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No.</th>
            <th>Código</th>
            <th>Capacidad de Compra</th>
            <th>Tipo de Pago</th>
            <th>Cumplimiento</th>
            <th>Descuento Total</th>                   
            </thead>
            <!--------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst_cp = pg_fetch_array($cns_cp)){
                    $cns_tp=$Set->lista_tipo_de_pago();
                while ($rst_tp = pg_fetch_array($cns_tp)){
                            $cns_cum=$Set->lista_cumplimiento();
                        while ($rst_cum = pg_fetch_array($cns_cum)){
                            $tot=($rst_cp[cap_descuento]+$rst_tp[tip_descuento]+$rst_cum[cum_descuento])/3;
                            $n++;
                        ?>
                            <tr>
                                <td><?php echo $n?></td>
                                <td align="center"><?php echo $rst_cp[cap_codigo].$rst_tp[tip_codigo].$rst_cum[cum_codigo]?></td>
                                <td align="right"><?php echo $rst_cp[cap_descuento]?></td>
                                <td align="right"><?php echo $rst_tp[tip_descuento]?></td>
                                <td align="right"><?php echo $rst_cum[cum_descuento]?></td>
                                <td align="right"><?php echo number_format($tot,2)?></td>
                            </tr>  
                        <?PHP
                        }
                   }
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

