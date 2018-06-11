<?php
include_once '../Clases/clsClase_multimedia.php';
include_once '../Includes/permisos.php';
$Mlt = new Multimedia();
if (isset($_GET[search])) {
    $txt = strtoupper($_GET[txt]);
    $cns = $Mlt->lista_multimedia_search($txt);
} else {
    $cns = $Mlt->lista_multimedia();
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

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Multimedia/Form_multimedia.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Multimedia/Form_multimedia.php?id=' + id;
                        look_menu();
                        break;
                    case 2://Ver PDF
                        frm.src = '../Multimedia/Form_pdf_view.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id, f)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_multimedia.php", {op: 1, id: id, file: f}, function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function cancelar() {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_multimedia.php';
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>
            #ayuda{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .pdfs{
                font-weight:bolder;
            }
            .pdfs:hover{
                text-decoration:underline; 
                text-decoration-color:#0000CC;
                color:#0000CC;
            }
            input[readonly]{
                background:#f8f8f8;
                color:black; 
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
                <center class="cont_title" >MULTIMEDIA</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Tema:<input type="text" name="txt"style="text-transform: uppercase" size="30" id="txt"  />
                        <input type="submit" id="search" name="search" value="Buscar" class="auxBtn" style="float:none;color:white;font-weight:bolder   " />
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th width="50px">No</th>
                    <th width="200px">Archivo</th>
                    <th width="200px">Fecha/Reg</th>
                    <th>Informacion</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td onclick="auxWindow(2, '<?php echo $rst[mlt_archivo] ?>')" class="pdfs"  ><img src="../img/orden.png" width="16px"/><?php echo '  ' . $rst[mlt_archivo] ?></td>
                        <td><?php echo $rst[mlt_fecha] ?></td>
                        <td><?php echo $rst[mlt_informacion] ?></td>
                    </tr>
                    <?php
                }
                ?>                    
            </tbody>


        </table>            

    </body>    
</html>


