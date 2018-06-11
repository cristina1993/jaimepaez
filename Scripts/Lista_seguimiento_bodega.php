<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($ord)) {
        $ord = "where ped_num_registro='$ord' and ped_estado<>'1' and ped_estado<>'2' and ped_estado<>'3' and tipo_cliente=1 and ped_local=$emisor ";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($ord);
        $f1 = $fec1;
        $f2 = $fec2;
    } else if (!empty($cli)) {
        $cli = "where ped_nom_cliente='$cli' and ped_estado<>'1' and ped_estado<>'2' and ped_estado<>'3' and tipo_cliente=1 and ped_local=$emisor ";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($cli);
        $f1 = $fec1;
        $f2 = $fec2;
    } else if (!empty($ruc)) {
        $ruc = "where ped_ruc_cc_cliente='$ruc' and ped_estado<>'1' and ped_estado<>'2' and ped_estado<>'3' and tipo_cliente=1 and ped_local=$emisor ";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($ruc);
        $f1 = $fec1;
        $f2 = $fec2;
    } else {
        $ord = "where ped_femision between '$fec1' and '$fec2' and ped_estado<>'1' and ped_estado<>'2' and ped_estado<>'3' and tipo_cliente=1 and ped_local=$emisor ";
        $cns = $Docs->lista_buscador_orden($ord);
        $f1 = $fec1;
        $f2 = $fec2;
    }
} else {
    $f1 = date('Y-m-d');
    $f2 = date('Y-m-d');
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
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x) {
                f1 = $('#fecha1').val();
                f2 = $('#fecha2').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_ord_pedido_venta.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_ord_pedido_venta.php?id=' + id + '&x=' + x + '&fecha1=' + f1 + '&fecha2=' + f2;
                        look_menu();
                        break;
                    case 2:
                        
//                        $.post("actions_industrial_ingresopt.php", {op: 15}, function (dt) {
//                            secuencial = '001-' + dt;
//                            frm.src = '../Scripts/Form_pedido_transferencia.php?id=' + id + '&ped=' + ped_arr.value + '&sec=' + secuencial;//Cambiar Form_productos
//                            if (secuencial != 0) {
//                                $.post("actions_industrial_ingresopt.php", {op: 16, sec: secuencial}, function (dt) {
//                                    if (dt != 0) {
//                                        alert(dt);
//                                    }
//                                });
//                            }
//                        });
                        frm.src = '../Scripts/Form_pedido_transferencia.php?id=' + id + '&ped=' + ped_arr.value;
//                        look_menu();
                        break;
                }
            }

            function facturar(act, id, ped, emi) {
                if (act == 1) {
                    sms = confirm("Se Realizara una Factura del pedido " + ped + " \n Desea Continuar?");
                    sts = 1;
                }
                if (sms == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 3, id: id, sts: sts, emi: emi}, function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            asientos(dat[1]);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function asientos(d1) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'POST',
                    url: 'actions_asientos_automaticos.php',
                    data: {op: 0, id: d1, x: 1},
                    success: function (dt) {
                        if (dt[0] == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }

                    }
                });
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 1, id: id}, function (dt) {
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

            function pedidos(obj) {
                if (obj.checked == true) {
                    ped = obj.value;
                    if (ped_arr.value.length == 0) {
                        ped_arr.value = ped + ';';
                    } else {
                        x = ped_arr.value.split(';')
                        $.post("actions_ord_pedido_venta.php", {op: 6, ped1: x[0], ped2: ped}, function (dt) {
                            if (dt == 0) {
                                ped_arr.value += ped + ';';
                            } else {
                                alert('No puede seleccionar este pedido \n Ya tiene otro seleccionado');
                                obj.checked = false;
                            }
                        });
                    }
                } else {
                    ped_arr.value = "";
                    var objCBarray = document.getElementsByName('trans_pedidos');
                    for (i = 0; i < objCBarray.length; i++) {
                        if (objCBarray[i].checked) {
                            ped_arr.value += objCBarray[i].value + ';';
                        }
                    }
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
        <input type="hidden" id="ped_arr" size="80"/>
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
                <center class="cont_title" ><?php echo "ORDENES DE PEDIDO DE VENTA " . $bodega ?></center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:0px;padding:7px;">
                        <?php
//                        if ($emisor == 1) {
                            ?>
                            <button class="btn" title="Guardar" onclick="auxWindow(2)">Transferencia</button>
                            <?php
//                        }
                        ?>
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli"/>
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value='<?php echo $f1 ?>' />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value='<?php echo $f2 ?>' />
                        <img src="../img/calendar.png" id="im-campo2"/>
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
            <th>Estado</th>
            <th>Registro Fecha</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
             $rst_loc=  pg_fetch_array($Docs->lista_emisor($rst[ped_local]));
                switch ($rst[ped_estado]) {
                    case '0':$estado = 'Pendiente';
                        break;
                    case '7':$estado = 'Semi-Transferido';
                        break;
                    case '8':$estado = 'Transferido';
                        break;
                    case '5':$estado = 'Suspendido';
                        break;
                    case '6':$estado = 'Caducado';
                        break;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_num_registro] ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_ruc_cc_cliente] ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_nom_cliente] ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst_loc[emi_nombre_comercial] ?></td>
                    <td onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_vendedor] ?></td>
                    <td align="right" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_total] ?></td>
                    <td align="center" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $estado ?></td>
                    <td align="center" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 3)"><?php echo $rst[ped_fecha_hora] ?></td>
                    <td align="center">
                        <?php
                        if ($emisor == $rst[ped_local]) {
                            if ($rst[ped_estado] == 6 || $rst[ped_estado] == 8) {
                                ?>
                                <input type="checkbox" style="float:center;margin-top:10px;padding:7px;" class="pedido" id="<?php echo 'pedidos' . $n ?>" name="trans_pedidos" value="<?php echo $rst[ped_num_registro] ?>" lang="<?php echo $rst[ped_nom_cliente] ?>" onclick="pedidos(this)" disabled />
                                <?php
                            } else {
                                if ($rst[ped_estado] == 0 || $rst[ped_estado] == 7) {
                                    ?>
                                    <input type="checkbox" style="float:center;margin-top:10px;padding:7px;" class="pedido" id="<?php echo 'pedidos' . $n ?>" name="trans_pedidos" value="<?php echo $rst[ped_num_registro] ?>" lang="<?php echo $rst[ped_nom_cliente] ?>" onclick="pedidos(this)" />
                                    <?php
                                } else {
                                    ?>
                                    <input type="checkbox" style="float:center;margin-top:10px;padding:7px;" class="pedido" id="<?php echo 'pedidos' . $n ?>" name="trans_pedidos" value="<?php echo $rst[ped_num_registro] ?>" lang="<?php echo $rst[ped_nom_cliente] ?>" onclick="pedidos(this)" disabled />
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>  
                    <?PHP
                }
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

