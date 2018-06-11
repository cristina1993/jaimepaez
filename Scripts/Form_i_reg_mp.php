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
    $no_orden = $rst_h[orc_codigo];
    $no_trs = $rst_h[orc_documento];
    $cns_mp = $Set->lista_mp($rst_h[emp_id]);
    $cns = $Set->lista_det_orden_compra($rst_h[orc_id]);
    $tbl_hidden = "";
} else {
    $tbl_hidden = "hidden";
    $cn0 = "";
    $cn1 = "hidden";
    $rst_sec = pg_fetch_array($Set->lista_secuencial_orden(0));
    $rst_h[orc_fecha] = date("Y-m-d");
    $rst_h[orc_fecha_entrega] = date("Y-m-d");
    $rst_h[orc_estado] = 0;
    $sec = ( substr($rst_sec[orc_codigo], 2) + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "0000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "00";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "0";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "";
    }
    $no_orden = 'OC' . $tx_trs . $sec;


    $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion(0));
    $rst_h[mov_fecha_trans] = date('Y-m-d');
    $sec0 = explode('-', $rst_sec[mov_num_trans]);
    $sec = ($sec0[1] + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "000000000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "00000000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "0000000";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "000000";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "00000";
    } elseif ($sec >= 100000 && $sec < 1000000) {
        $tx_trs = "0000";
    } elseif ($sec >= 1000000 && $sec < 10000000) {
        $tx_trs = "000";
    } elseif ($sec >= 10000000 && $sec < 100000000) {
        $tx_trs = "00";
    } elseif ($sec >= 100000000 && $sec < 1000000000) {
        $tx_trs = "0";
    } elseif ($sec >= 1000000000 && $sec < 10000000000) {
        $tx_trs = "";
    }
    $no_trs = '000-' . $tx_trs . $sec;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            var sts =<?php echo $rst_h[orc_estado] ?>;
            $(function () {
                Calendar.setup({inputField: "orc_fecha", ifFormat: "%Y-%m-%d", button: "im-orc_fecha"});
                Calendar.setup({inputField: "orc_fecha_entrega", ifFormat: "%Y-%m-%d", button: "im-orc_fecha_entrega"});
                $('#emp_id').val(<?php echo $rst_h[emp_id] ?>);
                $('#cli_id').val(<?php echo $rst_h[cli_id] ?>);
                $('#orc_condicion_pago').val('<?php echo $rst_h[orc_condicion_pago] ?>');
                if (sts > 2) {
                    $('input,select').each(function () {
                        this.disabled = true;
                    });
                    $('img').each(function () {
                        this.hidden = true;
                    });
                }
               parent.document.getElementById('contenedor2').rows = "*,80%"; 

            });
            function save(id) {
                if (orc_factura.value == 0) {
                    $('#orc_factura').css({'border': 'solid 1px red'});
                    $('#orc_factura').focus();
                } else if (mp_id.value == 0) {
                    $('#mp_id').css({'border': 'solid 1px red'});
                    $('#mp_id').focus();
                } else if (orc_det_cant.value.length == 0) {
                    $('#orc_det_cant').css({'border': 'solid 1px red'});
                    $('#orc_det_cant').focus();
                } else if (orc_det_vu.value.length == 0) {
                    $('#orc_det_vu').css({'border': 'solid 1px red'});
                    $('#orc_det_vu').focus();
                } else {

                    var data = Array(
                            orc_id.value,
                            mp_id.value,
                            orc_det_cant.value,
                            orc_det_vu.value,
                            orc_det_vt.value,
                            orc_guia_recepcion.value
                            );
                    $.post("actions.php", {act: 30, 'data[]': data, id: id, s: 1},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_reg_mp.php?orc_codigo=" + orc_codigo.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
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
                window.history.go(0);
            }
            function save_head() {
                if (emp_id.value != '0' && cli_id.value != '0' && orc_fecha_entrega.value != '0' && orc_factura.value.length > 0) {
                    if(iva_aplica.checked==true){
                        iva=0;
                    }else{
                        iva=1;
                    }
                    var data = Array(
                            cli_id.value,
                            orc_fecha.value,
                            orc_codigo.value,
                            emp_id.value,
                            orc_descuento.value,
                            orc_flete.value,
                            orc_fecha_entrega.value,
                            orc_factura.value,
                            orc_condicion_pago.value,
                            orc_direccion_entrega.value,
                            orc_guia_recepcion.value,
                            orc_documento.value,
                            iva
                            )
                    $.post("actions.php", {act: 29, 'data[]': data, id: orc_codigo.value},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_reg_mp.php?orc_codigo=" + orc_codigo.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function del(id) {
                if (confirm("Desea Eliminar Este Elemento?")) {
                    $.post("actions.php", {act: 31, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_reg_mp.php?orc_codigo=" + orc_codigo.value;
                        }else{
                            alert(dt);
                        }

                    });
                }
            }
            function datos(id) {
                $.post("actions.php", {act: 26, mp: id},
                function (dt) {
                    det = dt.split('&');
                    descripcion.innerHTML = det[0];
                    unidad.innerHTML = det[2];
                });
            }
            
            function valor_total() {
                orc_det_vt.value = (orc_det_cant.value * orc_det_vu.value).toFixed(1);
            }

            function imprimir(){
                var r = confirm("Luego de Imprmir ya no se podra modificar la Orden \n Desea Continuar??");
                if (r == true) {
                    window.print();
                    $.post("actions.php", {act: 50, sts: 3, id: orc_id.value}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
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
        <link rel="stylesheet" href="../css/style_print.css" type="text/css" media="print" />        
    </head>
    <body>
        <table id="tbl_form"  >
            <thead>
                <tr>
                    <th colspan="8" >
                        INGRESO DE FACTURA DE INGRESO DE MATERIA PRIMA
                        <font class="cerrar"  onclick="cerrar()" title="Salir del Formulario">&#X00d7;</font>
                    </th>
                </tr>
            </thead>
            <tr>
                <td >Fabrica:</td>
                <td>    
                    <select id="emp_id" onchange="save_head()" style="width:200px"  >
                        <option value="0">Elija Una Fabrica</option>
                        <?php
                        $cns_emp = $Set->lista_fabricas();
                        while ($rst_emp = pg_fetch_array($cns_emp)) {
                            echo "<option $sel value='$rst_emp[emp_id]'>$rst_emp[emp_descripcion]</option>";
                        }
                        ?>
                    </select>
                </td>
                <td>Fecha orden:</td>
                <td><input type="text"  id="orc_fecha" size="12" value="<?php echo $rst_h[orc_fecha] ?>" onchange="save_head()"/>
                    <img id='im-orc_fecha' src='../img/calendar.png'  />
                </td>
                <td>Orden #:</td>
                <td colspan="3">
                    <input type="text" id="orc_codigo" size="10"  readonly value="<?php echo $no_orden ?>" />
                    <input type="hidden" id="orc_id" value="<?php echo $rst_h[orc_id] ?>" />
                </td>
            </tr>
            <tr>
                <td >Proveedor:</td>
                <td>
                    <select id="cli_id" onchange="save_head()" style="width:200px" >
                        <option value="0">Elija Una Opcion</option>
                        <?php
                        $cns_cli = $Set->lista_clientes_tipo(0);
                        while ($rst_cli = pg_fetch_array($cns_cli)) {
                            echo "<option $sel value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
                        }
                        ?>
                    </select>
                </td>
                <td>Fecha Entrega:</td>
                <td><input type="text"  id="orc_fecha_entrega" size="12" value="<?php echo $rst_h[orc_fecha_entrega] ?>" onchange="save_head()"/>
                    <img id='im-orc_fecha_entrega' src='../img/calendar.png'  />
                </td>
                <td>Documento No:</td>
                <td colspan="3">
                    <input type="text" size="25" id="orc_documento" readonly style="background:#ccc;" value="<?php echo $no_trs ?>" />
                        <?php
                        if ($rst_h[orc_estado] > 1 && $rst_h[orc_estado] < 6) {
                            ?>
                            <img class="auxBtn" src="../img/print_iconop.png" width="20px" onclick="imprimir()" >
                            <?php
                        } else {
                            ?>
                            <img class="auxBtn" src="../img/no_print.png" width="20px"  >
                            <?php
                        }
                        ?>
                </td>
            </tr>
            <tr>
                <td>Condicion_Pago:</td>
                <td>
                    <select id="orc_condicion_pago" style="width:200px" onchange="save_head()" >
                        <option value="CONTADO">CONTADO</option>
                        <option value="A 30 DIAS">A 30 DIAS</option>
                        <option value="A 60 DIAS">A 60 DIAS</option>
                        <option value="A 90 DIAS">A 90 DIAS</option>
                        <option value="A 120 DIAS">A 120 DIAS</option>
                        <option value="A 150 DIAS">A 150 DIAS</option>
                    </select>
                </td>
                <td>Direccion de Entrega:</td>
                <td colspan="5">
                    <input type="text" id="orc_direccion_entrega" style="width:100%" value="<?php echo $rst_h[orc_direccion_entrega] ?>" onchange="save_head()" />
                </td>
            </tr>
            <tr>
                <td>Factura #:</td>
                <td >
                    <input type="text" id="orc_factura"   size="20"  value="<?php echo $rst_h[orc_factura] ?>" onchange="save_head()" />
                </td>
                <td>
                    Guia de Recepcion:
                </td>
                <td colspan="5">
                    <input type="text"  id="orc_guia_recepcion" value="<?php echo $rst_h[orc_guia_recepcion] ?>" onchange="save_head()" />
                </td>
            </tr>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>
                    <th>Referencia</th>            
                    <th>Unidad</th>
                    <th>Cantidad</th>
                    <th>Valor_U</th>
                    <th>Valot_T</th>
                    <th>Acciones</th>
                </tr>
                <tr class="options" <?php echo $tbl_hidden ?> >
                    <td></td>
                    <td>
                        <select id="mp_id" style="width:200px" onchange="datos(mp_id.value)">
                            <option value="0">Elija un Opcion</option> 
                            <?php
                            while ($rst_mp = pg_fetch_array($cns_mp)) {
                                echo "<option value='$rst_mp[mp_id]'  >$rst_mp[mp_referencia]</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td align="left" id="descripcion"></td>
                    <td align="center" id="unidad"></td>
                    <td><input type="text" id="orc_det_cant" size="10" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="valor_total()" /></td>
                    <td><input type="text" id="orc_det_vu" size="10" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" onchange="valor_total()" /></td>
                    <td id="total"><input type="text" readonly id="orc_det_vt" size="10" /></td>
                    <td><button onclick="save(0)">+</button></td>
                </tr>
            </thead>
            <tbody class="tbl_frm_aux" >                 
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $stb+=$rst[orc_det_vt];
                    ?>
                    <tr>
                        <td align="right"><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>          
                        <td><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><?php echo number_format($rst[orc_det_cant], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[orc_det_vu], 1) ?></td>
                        <td align="right" class="sbtls" ><?php echo number_format($rst[orc_det_vt], 1) ?></td>
                        <td align="center">
                            <?php
                            if ($Prt->delete == 0) {
                                ?>
                                <img class="auxBtn pesos" src="../img/peso.png" title="Pesos" width="20px"  onclick="etiquetas(0,<?php echo $rst[orc_det_id] ?>, '<?php echo $rst[mp_codigo] . $no_orden ?>',<?php echo $rst[orc_det_cant] ?>)">                                
                                <img src="../img/b_delete.png" onclick="del(<?php echo $rst[orc_det_id] ?>)">
                            <?php 
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                $t_desc = $stb * $rst_h[orc_descuento] / 100;
                if($rst_h[orc_iva]==0){
                    $iva12 = ($stb - $t_desc) * 0.12;
                    $chk_iva='checked';
                }else{
                    $iva12 = 0;
                    $chk_iva='';
                }
                
                $total = ($stb - $t_desc + $iva12 + $rst_h[orc_flete]);
                ?>
                <tr>
                    <td colspan="6" align="right">Subtotal:</td>
                    <td class="sbtls" align="right"><input type="text" size="10" readonly  value="<?php echo number_format($stb, 2) ?>" /></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">Descuento:</td>
                    <td class="sbtls" align="right">
                        <input type="text" size="5" maxlength="4" id="orc_descuento" onchange="save_head()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" value="<?php echo number_format($rst_h[orc_descuento], 1) ?>" />
                        %<input type="text" size="10" readonly  value="<?php echo number_format($t_desc, 2) ?>" />                    
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">
                        <font style="float:right  ">IVA 12%:</font>
                        <input type="checkbox" id="iva_aplica" <?php echo $chk_iva?> style="float:right" onclick="save_head()"  />                        
                    </td>
                    <td class="sbtls" align="right">
                            <input type="text" size="10" readonly id="iva12"  value="<?php echo number_format($iva12, 2) ?>" />
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">Flete:</td>
                    <td class="sbtls" align="right"><input type="text" size="10" id='orc_flete' onchange="save_head()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" value="<?php echo number_format($rst_h[orc_flete], 2) ?>" /></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="6" align="right">Total:</td>
                    <td class="sbtls" align="right" style="border-bottom:solid 1px #ccc;font-size:18px; "><input type="text" size="10" readonly  value="<?php echo number_format($total, 2) ?>" /></td>
                    <td></td>
                </tr>
                <?php
                if ($y == 0) {
                    ?>
                    <tr>
                        <td colspan="8">
                            <button id="save" style="float:left" onclick="finalizar()">Guardar</button>  
                            <button id="cancel0" style="float:left" <?php echo $cn0 ?> onclick="cancelar()">Cancelar</button>
                            <button id="cancel1" style="float:left" <?php echo $cn1 ?> onclick="cerrar()">Cancelar</button>
                        </td>
                    </tr>
                    <?php
                }
                ?>

            </tbody>          
        </table>
    </body>
</html>