<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();
if (isset($_GET[ord], $_GET[cli], $_GET[ruc], $_GET[fecha1], $_GET[fecha2])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $est = $_GET[ped_estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($ord)) {
        $txt4 = "where ped_num_registro='$ord' and ped_local=$emisor and  ped_estado='1'";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($txt4);
    } else if (!empty($cli)) {
        $txt1 = "where ped_nom_cliente='$cli' and ped_local=$emisor and  ped_estado='1'";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($txt1);
    } else if (!empty($ruc)) {
        $txt2 = "where ped_ruc_cc_cliente='$ruc' and ped_local=$emisor and  ped_estado='1'";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($txt2);
    } else if ($est != 'x') {
        $txt = "where ped_estado='$est' and ped_local=$emisor";
        $cns = $Docs->lista_buscador_orden($txt);
    } else {
        $txt3 = "where ped_femision between '$fec1' and '$fec2' and ped_local=$emisor and  ped_estado='1'";
        $cns = $Docs->lista_buscador_orden($txt3);
    }
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
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
                posicion_aux_window();
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
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_ord_pedido_venta.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_factura_pedventa.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
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
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedventa_local.php';
                        } else {
                            alert(dt);
                        }

                    }
                });
            }

//            function del(id)
//            {
//                var r = confirm("Esta Seguro de eliminar este elemento?");
//                if (r == true) {
//                    $.post("actions_ord_pedido_venta.php", {op: 1, id: id}, function (dt) {
//                        if (dt == 0) {
//                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedventa_local.php';
//                        } else {
//                            alert(dt);
//                        }
//                    });
//                } else {
//                    return false;
//                }
//            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 200) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 520) / 2);
            }

            function load_campo(id) {
                $.post("actions_ord_pedido_venta.php", {op: 5, id: id},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#clientes').html(dt);
                        $('#con_clientes').show();
                    }
                });
            }
            
            function exportar_excel() {
                $("#datatodisplay").val("");
                $("#tbl2 tbody").html("");
                $("#tbl2 tfoot").html("");
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }

        </script> 
        <style>
            #mn180{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #clientes{
                font-size: 14px;
            }

        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="10"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="10" align="center"><?PHP echo 'ORDENES DE PEDIDO DE VENTA ' . $bodega ?></th></tr>
                <tr>
                    <td colspan="10"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
                </tr>
            </thead>
        </table>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center" >
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>

        <div id="accion" align="center" >
            <table id="confirm" border="0" align="center"> 
                <tr>
                    <td colspan="2">Elija la acción a realizar</td>
                    <td><input type="hidden"  id="texto"/> </td>
                </tr>
                <tr id="hab">
                    <td>Habilitar</td>
                    <td><input type="radio" name="seleccion" id="habilitar" value="habilitar"/> </td>
                </tr>
                <tr id="sus">
                    <td>Suspender</td>
                    <td><input type="radio" name="seleccion" id="suspender" value="5"/> </td>
                </tr>
                <tr>
                    <td>Anular</td>
                    <td><input type="radio" name="seleccion" id="anular" value="6"/></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <button onclick="camb_estado(1)">Aceptar</button>
                        <button onclick="accion.style.visibility = 'hidden'">Cancelar</button>
                    </td>
                </tr>
            </table>
        </div>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=10" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?php echo "ORDENES DE PEDIDO DE VENTA " . $bodega ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord" value="<?php echo $ord ?>"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli" value="<?php echo $cli ?>"  />
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc" value="<?php echo $ruc ?>" />
                        ESTADO:
                        <select id="ped_estado" name="ped_estado">
                            <option value="x" >SELECCIONE</option>
                            <option value="1" >APROBADO</option>
                            <option value="3" >SEMI-FACTURADO</option>
                            <option value="4" >FACTURADO</option>
                            <option value="5" >SUSPENDIDO</option>
                            <option value="6" >CADUCADO</option>
                        </select>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" maxlength="10"/>
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
                    case '1':$estado = 'Aprobado';
                        break;
                    case '3':$estado = 'Semi-Facturado';
                        break;
                    case '4':$estado = 'Facturado';
                        break;
                    case '5':$estado = 'Suspendido';
                        break;
                    case '6':$estado = 'Caducado';
                        break;
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td style='mso-number-format:"@"'><?php echo $rst[ped_num_registro] ?></td>
                    <td style='mso-number-format:"@"'><?php echo $rst[ped_ruc_cc_cliente] ?></td>
                    <td><?php echo $rst[ped_nom_cliente] ?></td>
                    <td><?php echo $rst_loc[emi_nombre_comercial] ?></td>
                    <td><?php echo $rst[ped_vendedor] ?></td>
                    <td align="right"><?php echo number_format($rst[ped_total],2) ?></td>
                    <?php
                    if ($estado == 'Aprobado') {
                        ?>

                        <td align="center" style="color:darkred;font-weight:bolder"><?php echo $estado ?></td>
                        <?php
                    } else {
                        ?>
                        <td align="center" style="color:darkred;font-weight:bolder" onclick="load_campo(<?PHP echo $rst[ped_id] ?>)"><?PHP echo $estado ?></td>
                        <?php
                    }
                    ?>
                    <td align="center"><?php echo $rst[ped_fecha_hora] ?></td>
                    <td align="center">
                        <?php
                        if ($estado == 'Semi-Facturado') {
                            if ($Prt->edition == 0) {
                                ?>
                                <img class="auxBtn" width="16px" src="../img/facturar.png" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)" title="Facturar"/>
                                <?php
                            }
                        }
                        if ($estado == 'Aprobado') {
                            ?>
                            <img class="auxBtn" width="16px" src="../img/facturar.png" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)" title="Facturar"/>
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
    var e = '<?php echo $est ?>';
    $('#ped_estado').val(e);
</script>
