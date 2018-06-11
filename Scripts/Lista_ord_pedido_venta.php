<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ord_pedido_venta.php';
$Docs = new Clase_ord_pedido_venta();

if (isset($_GET[fecha1], $_GET[fecha2])) {
    $ord = trim(strtoupper($_GET[ord]));
    $cli = trim(strtoupper($_GET[cli]));
    $ruc = trim(strtoupper($_GET[ruc]));
    $est = $_GET[ped_estado];
    $fecha1 = $_GET[fecha1];
    $fecha2 = $_GET[fecha2];
    if (!empty($ord)) {
        $ord1 = "where ped_num_registro='$ord'";
        $cli = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($ord1);
    } else if (!empty($cli)) {
        $cli1 = "where ped_nom_cliente like'%$cli%'";
        $ord = '';
        $ruc = '';
        $cns = $Docs->lista_buscador_orden($cli1);
    } else if (!empty($ruc)) {
        $ruc1 = "where ped_ruc_cc_cliente='$ruc'";
        $ord = '';
        $cli = '';
        $cns = $Docs->lista_buscador_orden($ruc1);
    } else if ($est != 'x') {
        $estado1 = "where ped_estado='$est'";
        $cns = $Docs->lista_buscador_orden($estado1);
    } else {
        $ord1 = "where ped_femision between '$fecha1' and '$fecha2' ";
        $cns = $Docs->lista_buscador_orden($ord1);
    }
} else {
//    $cns = $Docs->lista_registros_completo_pedidos();
    $fecha1 = date('Y-m-d');
    $fecha2 = date('Y-m-d');
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
                posicion_accion();
                $('#accion').hide();
                posicion_confirmar();
                $('#autorizar').hide();
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                ord = $('#ord').val();
                cli = $('#cli').val();
                ruc = $('#ruc').val();
                f1 = $('#fecha1').val();
                f2 = $('#fecha2').val();
                e = $('#ped_estado').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_ord_pedido_venta.php?ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_ord_pedido_venta.php?id=' + id + '&ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                        look_menu();
                        break;
                    case 2://PDF
                        frm.src = '../Scripts/frm_pdf_proforma.php?id=' + id;
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
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedido_venta.php?ord=' + ord + '&cli=' + cli + '&ruc=' + ruc + '&fecha1=' + f1 + '&fecha2=' + f2 + '&ped_estado=' + e;
                        } else {
                            alert(dt);
                        }

                    }
                });
            }

            function del(id, doc)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_ord_pedido_venta.php", {op: 1, id: id, data: doc}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedido_venta.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function estado(id, e, t) {
                if (e == 5) {
                    $('#hab').show();
                    $('#sus').hide();
                } else {
                    $('#hab').hide();
                    $('#sus').show();
                }
                if (e != 4 && e != 6 && e != 8) {
                    $('#accion').css('visibility', 'visible');
                    $('#accion').show();
                }

                $('#texto').val(id);
                $('#estado').val(e);
                $('#tipo_cliente').val(t);
            }

            function estados(sts) {
                $('#autorizar').css('visibility', 'visible');
                $('#autorizar').show();
                $('#n_estado').val(sts);
            }

            function posicion_accion() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#accion");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 200) / 2);
            }

            function posicion_confirmar() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#autorizar");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 200) / 2);
            }

            function camb_estado(r) {
                if ($('#habilitar').attr('checked') == true) {
                    sts = $('#estado').val();
                    tcli = $('#tipo_cliente').val();
                    if (sts == 5 || tcli == 0) {
                        sts = 3;
                    }
                    if (sts == 5 || tcli == 1) {
                        sts = 7;
                    }
                    estados(sts);
                } else if ($('#suspender').attr('checked') == true) {
                    sts = $('#suspender').val();
                    estados(sts);
                } else {
                    sts = $('#anular').val();
                    estados(sts);
                }
            }

            function envio_estado() {
                cdg = 7777;
                if (codigo.value != '') {
                    if (cdg == codigo.value) {
                        $.post("actions_ord_pedido_venta.php", {op: 7, id: texto.value, sts: n_estado.value}, function (dt) {
                            if (dt == 0) {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_ord_pedido_venta.php?ord=' + $('#ord').val() + '&cli=' + $('#cli').val() + '&ruc=' + $('#ruc').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val() + '&ped_estado=' + $('#ped_estado').val();
                            } else {
                                alert(dt);
                            }
                        });
                    } else {
                        alert('Codigo Incorrecto');
                        codigo.value = '';
                    }
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="9"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="9" align="center"><?PHP echo 'ORDENES DE PEDIDO DE VENTA' ?></th></tr>
                <tr>
                    <td colspan="9"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
                </tr>
            </thead>
        </table>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>  
        <div id="accion" align="center" >
            <table id="confirm" border="0" align="center"> 
                <tr>
                    <td colspan="2">Elija la acción a realizar</td>
                    <td><input type="hidden"  id="texto"/> </td>
                    <td><input type="hidden"  id="estado"/> </td>
                    <td><input type="hidden"  id="tipo_cliente"/> </td>
                </tr>
                <tr id="hab">
                    <td>Habilitar</td>
                    <td><input type="radio" name="seleccion" id="habilitar"/> </td>
                </tr>
                <tr id="sus">
                    <td>Suspender</td>
                    <td><input type="radio" name="seleccion" id="suspender" value="5"/> </td>
                </tr>
                <tr>
                    <td>Caducado</td>
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
        <div id="autorizar" align="center">
            <table id="confirm" border="0" align="center">
                <tr>
                    <td>INGRESE CODIGO</td>
                </tr>
                <tr>
                    <td><input type="text" id="codigo" value=""></td>
                    <td><input type="hidden" id="n_estado"></td>
                </tr>
                <tr>
                    <td>
                        <button onclick="envio_estado()">Aceptar</button>
                        <button onclick="autorizar.style.visibility = 'hidden'">Cancelar</button>
                    </td>
                </tr>
            </table>
        </div>
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
                <center class="cont_title" >ORDENES DE PEDIDO DE VENTA</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        ORDEN:<input type="text" name="ord" size="15" id="ord" value="<?php echo $ord ?>"/>
                        CLIENTE:<input type="text" name="cli" size="15" id="cli" value="<?php echo $cli ?>"/>
                        RUC/Cedula:<input type="text" name="ruc" size="15" id="ruc" value="<?php echo $ruc ?>"/>
                        ESTADO:
                        <select id="ped_estado" name="ped_estado">
                            <option value="x" >SELECCIONE</option>
                            <option value="0" >Pendiente</option>
                            <option value="1" >Aprobado</option>
                            <option value="2" >Rechazado</option>
                            <option value="3" >Semi-Facturado</option>
                            <option value="4" >Facturado</option>
                            <option value="5" >Suspendido</option>
                            <option value="6" >Caducado</option>
                            <option value="7" >Semi-Transferido</option>
                            <option value="8" >Transferido</option>
                        </select>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fecha1 ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fecha2 ?>" maxlength="10"/>
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
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_loc = pg_fetch_array($Docs->lista_emisor($rst[ped_local]));

                switch ($rst[ped_estado]) {
                    case '0':$estado = 'Pendiente';
                        break;
                    case '1':$estado = 'Aprobado';
                        break;
                    case '2':$estado = 'Rechazado';
                        break;
                    case '3':$estado = 'Semi-Facturado';
                        break;
                    case '4':$estado = 'Facturado';
                        break;
                    case '5':$estado = 'Suspendido';
                        break;
                    case '6':$estado = 'Caducado';
                        break;
                    case '7':$estado = 'Semi-Transferido';
                        break;
                    case '8':$estado = 'Transferido';
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
                    <td  align="right"><?php echo number_format($rst[ped_total],2) ?></td>
                    <td align="center" onclick="estado(<?php echo $rst[ped_id] ?>,<?php echo $rst[ped_estado] ?>,<?php echo $rst[tipo_cliente] ?>)"><?php echo $estado ?></td>
                    <td align="center">
                        <?php
                        if ($estado != 'Aprobado') {
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/b_delete.png" width="20px"  class="auxBtn" onclick="del(<?php echo $rst[ped_id] ?>, '<?php echo $rst[ped_num_registro] ?>')">
                                <?php
                            }
                        }
                        if ($estado == 'Rechazado') {
                            if ($Prt->edition == 0) {
                                ?>
                                <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[ped_id] ?>, 0)">
                                <?php
                            }
                        }
                        ?>
                        <img class="auxBtn" width="18px" src="../img/orden.png" onclick="auxWindow(2, '<?php echo $rst[ped_id] ?>')">
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

