<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_descuentos.php';
$Desc = new Descuentos();
if (isset($_GET[search])) {
    $txt = $_GET[txt];
    $cns = $Desc->lista_productos($txt);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Tipo de pago</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            widthFixed: true
                        });
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
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
                        frm.src = '../Scripts/Form_i_cupos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cupos.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
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


            function seleccionar(p, obj) {
                
                //obj2=$('#tx'+obj.id);
                
                //alert(obj.id);
                
                if (p == 0) {
                    
                    
                    
                    if ($(obj).attr('checked')) {
                        $("input:checkbox[x=" + obj.id + "]").attr('checked', true);
                    } else {
                        $("input:checkbox[x=" + obj.id + "]").attr('checked', false);
                    }

                } else {
                    
                    if ($(obj).attr('checked')) {
                        $("input:checkbox[y=" + obj.id + "]").attr('checked', true);
                    } else {
                        $("input:checkbox[y=" + obj.id + "]").attr('checked', false);
                    }

                }


            }



        </script> 
        <style>
            #mn196{
                background:black;
                color:white;
                border: solid 1px white;
            }
            thead tr th{

            }
/*            *{
                font-size:10px !important; 
            }*/
            .aplicarh{
                background-color: #BDE5F8;
                border-bottom:solid 2px #ccc !important; 
                border-right:solid 1px #ccc !important;  
            }
            .aplicarv{
                background-color: #BDE5F8;
                border-right:solid 2px #ccc !important; 
                border-bottom:solid 1px #ccc !important;  
            }

            .ttl{
                color: #D8000C;
                background-color: #FFBABA;
                text-align:center;
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >Descuentos</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Producto:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" name="search">Buscar</button>
                    </form>  
                </center>
            </caption>
            <tr>
                <td class="ttl"><input type="button" style="width:100%; height: 30px " value="Aplicar Descuento" /></td>
                <td class='aplicarh'></td>
                <td class='aplicarh'></td>
                <td class='aplicarh'></td>
                <?php
                $cns_head = $Desc->lista_locales();
                $row1 = pg_num_rows($cns_head);
                $n = 0;
                while ($n < $row1) {
                    $n++;
                    echo "<td align='center' class='aplicarh' >
                            <input type='text' id='txy$n' name='' size='3' maxlenght='3' />
                            <input type='checkbox' id='y$n' name='' onclick='seleccionar(1,this)' />
                            </td>";
                }
                ?>
            </tr>
            <thead>                                                        
                <tr>
                    <th width='170px'></th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Precio</th>
                    <?php
                    while ($rst_head = pg_fetch_array($cns_head)) {
                        echo "<th>$rst_head[nombre_comercial]</th>";
                    }
                    ?>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $x = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $x++;
                    ?>
                    <tr>
                        <td class='aplicarv' align='center'>
                            <input type="text" size="3" id="<?php echo 'txx' . $x ?>" />
                            <input type="checkbox" id="<?php echo 'x' . $x ?>" onclick="seleccionar(0, this)"/>
                        </td>
                        <td><?php echo $rst[pro_codigo] ?></td>
                        <td><?php echo $rst[pro_descripcion] ?></td>                     
                        <td align="right"><?php echo number_format($precio, 2) ?></td>                    
                        <?php
                        $cns_head = $Desc->lista_locales();
                        $row1 = pg_num_rows($cns_head);

                        $y = 0;

                        while ($y < $row1) {
                            $y++;
                            echo "<td align='center'>
                                    <input type='text' id='' name='' size='3'  value='' />
                                    <input type='checkbox'  id='' name='' x='x$x' y='y$y' />                                    
                                    </td>";
                        }
                        ?>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>

