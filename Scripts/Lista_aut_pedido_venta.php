<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();

if (isset($_GET[ord], $_GET[cli], $_GET[ruc], $_GET[ven])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $estado = $_GET[ped_estado];
    $ven = trim(strtoupper($_GET[ven]));
    if (!empty($ord)) {
        $ord = "where ped_num_registro='$ord'";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($ord);
    } else if (!empty($cli)) {
        $cli = "where ped_nom_cliente='$cli'";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($cli);
    } else if (!empty($ruc)) {
        $ruc = "where ped_ruc_cc_cliente='$ruc'";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($ruc);
    } else if ($estado != 'x') {
        $estado = "where ped_estado='$estado' and tipo_cliente=0";
        $cns = $Docs->lista_buscador_orden($estado);
    } else {
        $ven = "where ped_vendedor='$ven' ";
        $cns = $Docs->lista_buscador_orden($ven);
    }
} else {
    $cns = $Docs->lista_registros_completo_pedidos_pendiente();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
    <head>
        <script>
            emi = '<?php echo $emisor ?>';
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

            function auxWindow(a, id, x) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 2://Editar
                        frm.src = '../Scripts/Form_ord_pedido_venta.php?id=' + id + '&x=' + x;
                        look_menu();
                        break;
                }
            }

//            function aprobar(act, id, ped, emi) {
//                if (act == 1) {
//                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar?");
//                    sts = 1;
//                } else if (act == 0) {
//                    sms = confirm("No se aprobara el pedido " + ped + " \n Desea Continuar?");
//                    sts = 2;
//                }
//                if (sms == true) {
//                    $.post("actions_ord_pedido_venta.php", {op: 3, id: id, sts: sts, emi: emi}, function (dt) {
//                        dat = dt.split('&');
//                        alert(dat[0]);
//                        if (dat[0] == 0) {
//                            asientos(dat[1]);
//                        } else {
//                            alert(dt);
//                        }
//                    });
//                } else {
//                    return false;
//                }
//            }

            function cambiar_estado(act, id, ped, emi) {
                main = parent.document.getElementById('mainFrame');
                if (act == 1) {
                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 1;
                } else if (act == 0) {
                    sms = confirm("No se aprobara el pedido " + ped + " \n Desea Continuar?");
                    sts = 2;
                }
                if (sms == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 4, id: id, sts: sts, emi: emi}, function (dt) {
                        if (dt == 0) {
                            main.src = '../Scripts/Lista_aut_pedido_venta.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

//            function asientos(d1) {
//
//                $.ajax({
//                    beforeSend: function () {
//
//                    },
//                    type: 'POST',
//                    url: 'actions_asientos_automaticos.php',
//                    data: {op: 0, id: d1, x: 1},
//                    success: function (dt) {
//                        if (dt[0] == 0) {
//                            window.history.go(0);
//                        } else {
//                            alert(dt);
//                        }
//
//                    }
//                });
//            }
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
                <center class="cont_title" >AUTORIZACIÓN DE PEDIDO DE VENTA</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli"/>
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc"/>
                        Vendedor:<input type="text" name="ven" size="15" id="ven"/>
                        ESTADO:
                        <select id="ped_estado" name="ped_estado">
                            <option value="x" >SELECCIONE</option>
                            <option value="0" >PENDIENTE</option>
                            <option value="1" >APROBADO</option>
                            <option value="2" >RECHAZADO</option>
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                                
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Orden de Venta</th>
            <th>Ruc/Cedula</th>
            <th>Cliente</th>                                
            <th>Local</th>
            <th>Vendedor</th>
            <th>Total Valor</th>
            <th>Descuento%</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                
                $rst_sumd = pg_fetch_array($Docs->lista_suma_descuento($rst[ped_id]));
                $rst_sumt = pg_fetch_array($Docs->lista_suma_total($rst[ped_id]));
                $descuento = number_format(($rst_sumd[suma_descuento]/($rst_sumd[suma_descuento] + $rst_sumt[suma_total])) * 100, 2);
                $rst_loc=  pg_fetch_array($Docs->lista_emisor($rst[ped_local]));
                
                switch ($rst[ped_estado]) {
                    case '0':$estado = 'Pendiente';
                        break;
                    case '1':$estado = 'Aprobado';
                        break;
                    case '2':$estado = 'Rechazado';
                        break;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_num_registro] ?></td>
                    <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_ruc_cc_cliente] ?></td>
                    <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_nom_cliente] ?></td>
                    <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst_loc[emi_nombre_comercial] ?></td>
                    <td onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_vendedor] ?></td>
                    <td align="right" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $rst[ped_total] ?></td>
                    <td align="right" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $descuento . '%' ?></td>
                    <td align="center" onclick="auxWindow(2,<?php echo $rst[ped_id] ?>, 1)"><?php echo $estado ?></td>
                    <td align="center">
                        <?php
                        if($rst[ped_estado] == 0 || $rst[ped_estado] == 2){
                        ?>
                        <img class="auxBtn" width="16px" src="../img/error.png" onclick="cambiar_estado(0,<?php echo $rst[ped_id] ?>, '<?php echo $rst[ped_num_registro] ?>')" title="Rechazar Pedido de Venta"/>
                        <img class="auxBtn" width="16px" src="../img/exito.png" onclick="cambiar_estado(1,<?php echo $rst[ped_id] ?>, '<?php echo $rst[ped_num_registro] ?>', '<?php echo $rst[ped_local] ?>')" title="Aprobar Pedido de Venta"/>
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

