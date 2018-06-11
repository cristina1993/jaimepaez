<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_registro_facturas.php';
$Docs = new Clase_registro_facturas();
//$cns = $Docs->lista_registros_completo();
if (isset($_GET[txt], $_GET[desde], $_GET[hasta])) {
    $txt = strtoupper($_GET[txt]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "and (d.reg_num_documento like '%$txt%' or d.reg_tpcliente like '%$txt%' or d. reg_concepto like '%$txt%' or d.reg_num_registro like '%$txt%' or c.cli_raz_social like '%$txt%')";
    } else {
        $texto = "and reg_femision between '$desde' and '$hasta'";
    }
    $cns = $Docs->lista_buscador_reg_fac($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_registro_facturas.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_registro_facturas.php?id=' + id;
                        look_menu();
                        break;
                }
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_reg_docs.php", {op: 1, id: id}, function (dt) {
                        if (dt == 0) {
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
            #mn180{
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
$cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
while ($rst_sbm = pg_fetch_array($cns_sbm)) {
    ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >REGISTRO DE FACTURAS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" value="<?php echo $txt ?>" />
                        DESDE:<input type="date" size="15" name="desde" id="desde" value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="date" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>No Registro</th>
            <th>Tipo Documento</th>
            <th>No Documento</th>                                
            <th>Fecha Emision</th>
            <th>Proveedor</th>
            <th>Concepto</th>
            <th>Valor Total</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
<?PHP
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $n++;
    $g_total+=$rst[reg_total];

    switch ($rst[reg_tipo_documento]) {
        case '01':$tipo = 'Factura';
            break;
        case '04':$tipo = 'Nota de Credito';
            break;
        case '05':$tipo = 'Nota de Debito';
            break;
        case '06':$tipo = 'Guia Remision';
            break;
        case '07':$tipo = 'Retencion';
            break;
    }
    $rst_cli = pg_fetch_array($Docs->lista_cliente_ruc($rst[reg_ruc_cliente]));
    ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[reg_num_registro] ?></td>
                    <td><?php echo $tipo ?></td>
                    <td><?php echo $rst[reg_num_documento] ?></td>
                    <td><?php echo $rst[reg_femision] ?></td>
                    <td><?php echo $rst_cli[cli_raz_social] ?></td>
                    <td><?php echo $rst[reg_concepto] ?></td>
                    <td align="right"><?php echo number_format($rst[reg_total], $dec) ?></td>
                    <td align="center">
    <?php
    if ($Prt->delete == 0) {
        ?>
                            <img src="../img/del_reg.png" width="16px"  class="auxBtn" onclick="del(<?php echo $rst[reg_id] ?>)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[reg_id] ?>, 0)">
                            <?php
                        }
                        ?>
                    </td>
                </tr>  
    <?PHP
}
?>
        </tbody>
        <tr style="font-weight:bolder">
            <td colspan="7" align="right">Total</td>
            <td align="right" style="font-size:14px;"><?php echo number_format($g_total, $dec) ?></td>
            <td colspan="6"></td>
        </tr>

    </table>            

</body>    
</html>

