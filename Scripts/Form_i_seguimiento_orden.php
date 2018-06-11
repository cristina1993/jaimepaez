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
            var sts =<?php echo $rst_h[orc_estado] ?>;
            $(function() {
                $('#emp_id').val(<?php echo $rst_h[emp_id] ?>);
                $('#cli_id').val(<?php echo $rst_h[cli_id] ?>);
                $('#orc_condicion_pago').val('<?php echo $rst_h[orc_condicion_pago] ?>');
                if (sts != 2 && sts != 3 && sts != 4) {
                    $('input,select').each(function() {
                        this.disabled = true;
                    });
                    $('.auxBtn').each(function() {
                        this.hidden = true;
                    });
                    $('.pesos').each(function() {
                        this.hidden = false;
                    });
                }

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
                    function(dt) {
                        if (dt == 0) {
                            window.location = "Form_i_seguimiento_orden.php?orc_codigo=" + orc_codigo.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function etiquetas(a, id, barcode, p_entregado) {
                var boxH = $(window).height() * 0.9;
                var boxW = $(window).width() * 0.9;
                var boxHF = (boxH - 20);
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
                            onclick: function() {
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
//                if (orc_id.value.length > 0) {
//                    $.post("actions.php", {act: 32, id: orc_id.value},
//                    function(dt) {
//                        if (dt == 0)
//                        {
//                            cerrar();
//                        } else {
//                            alert(dt);
//                        }
//                    });
//                } else {
                    cerrar();
//                }

            }
            function finalizar()
            {
                cerrar();
                window.history.go(0);
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
                    <th colspan="9" >FORMULARIO ORDEN DE COMPRA
                        <font class="cerrar"  onclick="cancelar(0)" title="Salir del Formulario">&#X00d7;</font>
                    </th>
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
                    <input  type="text" readonly value="<?php echo trim($rst_cli[cli_apellidos].' '.$rst_cli[cli_nombre].' '.$rst_cli[cli_raz_social]) ?>"/>
                    <input  type="hidden" id="cli_id" value="<?php echo $rst_h[cli_id] ?>"/>
                </td>
                <td>Fecha Entrega:</td>
                <td><input type="text"  id="orc_fecha_entrega" readonly size="11" value="<?php echo $rst_h[orc_fecha_entrega] ?>" />
                </td>
                <td>Factura #:</td>
                <td>
                    <input type="text" id="orc_factura"  size="20"  value="<?php echo $rst_h[orc_factura] ?>"  />
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
                    <th>Solicitado</th>
                    <th>Entregado</th>
                    <th>Saldo</th>
                    <th>Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="tbl_frm_aux" >                 
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_mov = pg_fetch_array($Set->lista_inv_mp_doc($rst[mp_id], $no_orden, 0));
                    $saldo = $rst[orc_det_cant] - $rst_mov[peso];
                    if ($saldo > 0 && ($rst_h[orc_estado]==3 || $rst_h[orc_estado]==4 || $rst_h[orc_estado]==5)) {
                        $rdnl = '';
                    } else {
                        $rdnl = 'readOnly';
                    }
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>          
                        <td><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><?php echo number_format($rst[orc_det_cant], 1) ?></td>
                        <td align="center"><?php echo number_format($rst_mov[peso], 1) ?></td>
                        <td align="center"><input type="text" style="text-align:right" readonly size="10" id="<?php echo 'sld' . $rst[mp_id] ?>" value="<?php echo number_format($saldo, 1) ?>" /></td>
                        <td align="center"><input type="text" style="text-align:right" <?php echo $rdnl ?> size="10" id="<?php echo 'cnt' . $rst[mp_id] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                        <td align="center">
                            <?php
                            if ($Prt->delete == 0) {
                                if ($rst_mov[peso] == 0) {
                                    ?>
                                    <img class="auxBtn pesos" src="../img/peso0.png" title="Pesos" width="20px"  >                                
                                    <?php
                                } else {
                                    ?>
                                    <img class="auxBtn pesos" src="../img/peso.png" title="Pesos" width="20px"  onclick="etiquetas(0,<?php echo $rst[orc_det_id] ?>, '<?php echo $rst[mp_codigo] . $no_orden ?>',<?php echo $rst_mov[peso] ?>)">                                
                                    <?php
                                }
                                ?>                                
                                <img class="auxBtn"  src="../img/save.png" title="Guardar Registro" onclick="save(<?php echo $rst[mp_id] ?>)">                                                            
                            <?php }
                            ?>
                        </td>
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