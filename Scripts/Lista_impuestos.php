<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_impuestos.php'; //cambiar clsClase_productos
$Clase_impuestos = new Clase_impuestos();
if (isset($_GET[tipo])) {
    $txt = trim(strtoupper($_GET[txt]));
    $tip = $_GET[tipo];
    if (!empty($txt)) {
        $text = "where upper(por_codigo) like '%$txt%' or upper(por_descripcion) like '%$txt%'";
    } else {
        if ($tip == 'x') {
            $text = '';
        } else {
            $text = "where por_siglas ='$tip'";
        }
    }
    $cns = $Clase_impuestos->lista_buscardor_impuestos($text);
} else {
    $txt = '';
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
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_impuestos.php';//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_impuestos.php?id=' + id;//Cambiar Form_productos
                        look_menu();
                        break;
                }
            }
            function del(id, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_impuestos.php", {id: id, op: 1, data: doc}, function (dt) {
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
                <center class="cont_title" >LISTA DE IMPUESTOS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        TIPO:
                        <select id="tipo" name="tipo">
                            <option value="x">Seleccione</option>
                            <option value="IR">Renta</option>
                            <option value="IV">Iva</option>
                            <option value="ID">Salida de Divisas</option>
                            <option value="IC">Ice</option>
                            <option value="IRB">Irbpnr</option>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Tipo</th>
            <th>Codigo</th>
            <th>Codigo ATS</th>
            <th>Cuentas Contable</th>
            <th>Descripcion</th>
            <th>Porcentaje</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_cts = pg_fetch_array($Clase_impuestos->lista_una_cuenta_id($rst['cta_id']));
                switch ($rst[por_siglas]) {
                    case IR://Nuevo
                        $tipo = 'RENTA';
                        break;
                    case IV://Editar
                        $tipo = 'IVA';
                        break;
                    case IC://Editar
                        $tipo = 'ICE';
                        break;
                    case IRB://Editar
                        $tipo = 'IRBPNR';
                        break;
                    case ID://Editar
                        $tipo = 'SALIDA DE DIVISAS';
                        break;
                }
                ?>
                <tr style="height: 30px">
                    <td><?php echo $n ?></td>
                    <td><?php echo $tipo ?></td>
                    <td><?php echo $rst['por_codigo'] ?></td>
                    <td><?php echo $rst['por_cod_ats'] ?></td>
                    <td><?php echo $rst_cts['pln_codigo'].' '.$rst_cts['pln_descripcion'] ?></td>
                    <td><?php echo $rst['por_descripcion'] ?></td>
                    <td align="right"><?php echo $rst['por_porcentage'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/b_delete.png"  class="auxBtn" onclick="del(<?php echo $rst[por_id] ?>, '<?php echo $rst[por_codigo] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[por_id] ?>, 0)">
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
<script>
    var tip = '<?php echo $tip ?>';
    $('#tipo').val(tip);
</script>
