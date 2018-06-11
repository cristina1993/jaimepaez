<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
if (isset($_GET[orc_codigo]) || isset($_GET[id])) {
    $rst_h = pg_fetch_array($Set->lista_orden_compra_code($_GET[orc_codigo]));
    if (empty($rst_h)) {
        $rst_h = pg_fetch_array($Set->lista_una_orden_compra($_GET[id]));
        $cn0 = "hidden";
        $cn1 = "";
    } else {
        $cn0 = "";
        $cn1 = "hidden";
    }

    $rst_emp = pg_fetch_array($Set->lista_una_fabrica($rst_h[emp_id]));
    $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst_h[cli_id]));
    $no_orden = $rst_h[orc_codigo];
    $cns_mp = $Set->lista_mp($rst_h[emp_id]);
    $cns = $Set->lista_det_orden_compra($rst_h[orc_id]);
    $tbl_hidden = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                $('#emp_id').val(<?php echo $rst_h[emp_id] ?>);
                $('#cli_id').val(<?php echo $rst_h[cli_id] ?>);
                $('#orc_condicion_pago').val('<?php echo $rst_h[orc_condicion_pago] ?>');

            });
            function save(id) {
                var f = new Date();
                mp_id = id;
                mov_documento = $('#orc_codigo').val();
                mov_num_trans = '000';
                mov_fecha_trans = f.getFullYear() + '-' + (f.getMonth() + 1) + '-' + f.getDate();
                mov_cantidad = $('#cnt' + id).val();
                saldo = $('#sld' + id).val();
                mov_presentacion = 'KG';
                mov_peso_total = mov_cantidad;
                mov_proveedor = $('#cli_id').val();
                factura = $('#orc_factura').val();
                if ((saldo * 1) < (mov_cantidad * 1)) {
                    alert('El registro no puede ser mayor al Saldo');
                    $('#cnt' + id).focus();
                } else if (factura.length == 0) {
                    $('#orc_factura').css({'border': 'solid 1px red'});
                    $('#orc_factura').focus();
                } else if (mov_cantidad.length == 0) {
                    $('#cnt' + id).css({'border': 'solid 1px red'});
                    $('#cnt' + id).focus();
                } else {
                    var data = Array(
                            3, //Ingreso por inventario
                            mp_id,
                            mov_documento,
                            mov_num_trans,
                            mov_fecha_trans,
                            mov_cantidad,
                            mov_presentacion,
                            mov_peso_total,
                            mov_proveedor,
                            1, //Peso Unitario
                            '', //Transportista
                            factura
                            );
                    $.post("actions.php", {act: 22, 'data[]': data, id: id},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_seguimiento_orden.php?orc_codigo=" + orc_codigo.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function etiquetas(a, id, barcode, p_entregado) {
                var gap = 20;
                var boxH = $(window).height() - gap * 1.1;
                var boxW = $(window).width() * 0.35;
                var boxHF = (boxH - gap);
                if (a == 0) {
                    wnd = '<iframe id="frmmodal" width="' + boxW + '" height="' + boxHF + '" src="Form_i_pesos_orden.php?id=' + id + '&barcode=' + barcode + '&p_entregado=' + p_entregado + '   " frameborder="0" />';
                } else {
                    wnd = '<iframe id="frmmodal" width="' + boxW + '" height="' + boxHF + '" src="../Reports/etq_ord_compra.php?id=' + id + '   " frameborder="0" />'
                }

                $.fallr.show({
                    content: "<font id='titulo_ventana'>INGRESO DE PESOS</font><br/><br/>"
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });

            }
            function cerrar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function cancelar() {
                if (orc_id.value.length > 0) {
                    $.post("actions.php", {act: 32, id: orc_id.value},
                    function (dt) {
                        if (dt == 0)
                        {
                            cerrar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    cerrar();
                }

            }
            function finalizar()
            {
                cerrar();
            }
        </script>
        <style>
            .sbtls{
                border-left:solid 1px #ccc;
                border-right:solid 1px #ccc;        
            }
            .sbtls input{
                text-align:right; 
            }
        </style>        
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr>
                    <th colspan="9" >FORMULARIO ORDEN DE COMPRA</th>
                </tr>
            </thead>
            <tr>
                <td >Fabrica:</td>
                <td>  
                    <input  type="text" readonly value="<?php echo $rst_emp[emp_descripcion] ?>"/>
                    <input  type="hidden" id="emp_id" value="<?php echo $rst_h[emp_id] ?>"/>
                </td>
                <td>Fecha orden:</td>
                <td><input type="text" readonly id="orc_fecha" size="11" value="<?php echo $rst_h[orc_fecha] ?>" />
                </td>
                <td>Orden #:</td>
                <td>
                    <input type="text" id="orc_codigo" disabled size="10"  readonly value="<?php echo $no_orden ?>" />
                    <input type="hidden" id="orc_id" value="<?php echo $rst_h[orc_id] ?>" />
                </td>
            </tr>
            <tr>
                <td >Proveedor:</td>
                <td>
                    <input  type="text" readonly value="<?php echo $rst_cli[cli_raz_social] ?>"/>
                    <input  type="hidden" id="cli_id" value="<?php echo $rst_h[cli_id] ?>"/>
                </td>
                <td>Fecha Entrega:</td>
                <td><input type="text"  id="orc_fecha_entrega" readonly size="11" value="<?php echo $rst_h[orc_fecha_entrega] ?>" />
                </td>
                <td>Factura #:</td>
                <td>
                    <input type="text" readonly id="orc_factura"  size="20"  value="<?php echo $rst_h[orc_factura] ?>"  />
                </td>
            </tr>
            <tr>
                <td>Condicion de Pago:</td>
                <td>
                    <input  type="text" readonly id="orc_condicion_pago" value="<?php echo $rst_h[orc_condicion_pago] ?>"/>
                </td>
                <td>Direccion de Entrega:</td>
                <td colspan="8">
                    <input type="text" id="orc_direccion_entrega" readonly style="width:100%" value="<?php echo $rst_h[orc_direccion_entrega] ?>"  />
                </td>
            </tr>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>
                    <th>Referencia</th>            
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>V.Unitario</th>
                    <th>V.U.Anterior</th>
                    <th>Valor Total</th>
                </tr>
            </thead>
            <tbody class="tbl_frm_aux" >                 
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_historial_mp = pg_fetch_array($Set->lista_historial_orden_mp($rst[mp_id]));
                    $tot = $rst[orc_det_vu] * $rst[orc_det_cant];
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>          
                        <td><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><?php echo number_format($rst[orc_det_cant], 1) ?></td>
                        <td align="right" style="font-size:15px"><?php echo number_format($rst[orc_det_vu], 1) ?></td>
                        <td align="right" style="font-size:15px"><?php echo number_format($rst_historial_mp[orc_det_vu], 1) ?></td>
                        <td align="right" style="height:35px; "><?php echo number_format($tot, 1) ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="9">
                        <button id="save" onclick="finalizar()">Guardar</button>                          
                        <button id="cancel" onclick="cerrar()">Cancelar</button>                        
                    </td>
                </tr>
            </tbody>          
        </table>
    </body>
</html>