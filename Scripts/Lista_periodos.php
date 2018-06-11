<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_horarios.php';
$Hor = new Clase_horarios;
$cns = $Hor->lista_periodos();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Periodos</title>
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

            function save() {
                id = $('#per_id').val();
                var data = Array(per_descripcion.value.toUpperCase());
                if ($('#per_descripcion').val() == '') {
                    $('#per_descripcion').focus();
                    $('#per_descripcion').css('border', 'Solid 1px brown');
                } else {
                    $.post("actions_horarios.php", {op: 1, 'data[]': data, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_periodos.php';
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function update_periodo(id) {
                $.post("actions_horarios.php", {op: 6, id: id},
                function (dt) {
                    dat = dt.split('&');
                    $('#per_id').val(dat[0]);
                    $('#per_descripcion').val(dat[1]);
                });
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
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:25%" id="tbl">
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
                <center class="cont_title" >LISTA PERIODOS</center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>    
            <th>Descripcion</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody>
            <tr>
                <td align="center">1</td>
                <td align="center">
                    <input type="hidden" size="2" id="per_id" value="0">
                    <input type="text" size="25" id="per_descripcion">
                </td>
                <td align="center">
                    <img class="axb" src="../img/save.png" title="Guardar" onclick="save()" />
                </td>
            </tr>
        <thead>
        <th colspan="3"></th>
    </thead>
    <?PHP
    $n = 0;
    while ($rst = pg_fetch_array($cns)) {
        $n++;
        ?>
        <tr style="height: 30px">
            <td align="center"><?php echo $n ?></td>
            <td align="center"><?php echo $rst['per_descripcion'] ?></td>
            <td align="center">
                <?php
                if ($Prt->edition == 0) {
                    ?>
                    <img src="../img/upd.png" width="12px" class="auxBtn" onclick="update_periodo(<?php echo $rst[per_id] ?>)">
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

