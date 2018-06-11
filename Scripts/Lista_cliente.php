<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
$Clase_cliente = new Clase_cliente();
if (isset($_GET[codigo], $_GET[cli_tipo], $_GET[cli_categoria], $_GET[cli_estado])) {
    $cns = $Clase_cliente->lista_buscador_cliente(trim(strtoupper($_GET[codigo])), trim(strtoupper($_GET[cli_tipo])), trim(strtoupper($_GET[cli_categoria])), trim(strtoupper($_GET[cli_estado])));
} else {
    $cns = $Clase_cliente->lista_cliente();
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
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
                        frm.src = '../Scripts/Form_cliente.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_cliente.php?id=' + id;
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_cliente.php?id=' + id + '&x=' + x  ;
                                look_menu();
                        break;
                }

            }

            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_cliente.php", {act: 48, id: id, op: op}, function (dt) {
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
            #mn39{
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
                    <span class="subMenu"  >Lista</span>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >CLIENTE/PROVEEDOR</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO:<input type="text" name="codigo" id="codigo" size="15" />
                        TIPO:
                        <select id="cli_tipo" name="cli_tipo">
                            <option value="" >SELECCIONE</option>
                            <option value="0" >CLIENTE</option>
                            <option value="1" >PROVEEDOR</option>
                            <option value="2" >AMBOS</option>

                        </select>

                        CATEGORIA:
                        <select id="cli_categoria" name="cli_categoria">
                            <option value="" >SELECCIONE</option>
                            <option value="1" >NATURAL</option>
                            <option value="2" >JURIDICO</option>
                        </select>
                        ESTADO:
                        <select id="cli_estado" name="cli_estado">
                            <option value="" >SELECCIONE</option>
                            <option value="0" >ACTIVO</option>
                            <option value="1" >INACTIVO</option>
                            <option value="2" >SUSPENDIDO</option>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button><img src="../img/finder.png"/>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Cedula/ruc</th>
            <th>Cliente</th>
            <th>Fecha de Ingreso</th>
            <th>Pais</th>
            <th>Ciudad</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Accciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
            <tr id="fila" ondblclick="auxWindow(2,<?php echo $rst[cli_id] ?>, 1)">
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['cli_codigo'] ?></td>
                    <td><?php echo $rst['cli_tipo'] ?></td>
                    <td><?php echo $rst['cli_categoria'] ?></td>
                    <td><?php echo $rst['cli_ced_ruc'] ?></td>
                    <td><?php echo $rst['cli_apellidos'] . ' ' . $rst['cli_nombres'] . ' ' . $rst['cli_nom_comercial'] ?></td>
                    <td><?php echo $rst['cli_fecha'] ?></td>
                    <td><?php echo $rst['cli_pais'] ?></td>
                    <td><?php echo $rst['cli_parroquia'] ?></td>
                    <td><?php echo $rst['cli_calle_prin'] . ' ' . $rst['cli_numero'] . ' ' . $rst['cli_calle_sec'] ?></td>
                    <td><?php echo $rst['cli_telefono'] ?></td>
                    <td><?php echo $rst['cli_email'] ?></td>
                    <td><?php echo $rst['cli_estado'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[cli_id] ?>, 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cli_id] ?>, 0)">
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

