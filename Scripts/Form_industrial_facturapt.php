<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php';
$id = 1;
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
$rst = pg_fetch_array($Clase_industrial_ingresopt->lista_ultimo_ingreso_industrial());
$cns = $Clase_industrial_ingresopt->lista_ingreso_industrial_documento($rst['mov_documento']);
    $rst['mov_fecha_entrega'] = date('Y-m-d');
    $rst['mov_val_unit'] = 0;
    $rst['mov_val_total'] = 0;
    $rst['subtotal'] = 0;
    $rst['descuento'] = 0;
    $rst['descuentop'] = 0;
    $rst['iva'] = 0;
    $rst['subtotal'] = 0;
    $rst['mov_flete'] = 0;
    $rst['total'] = 0;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id; ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                Calendar.setup({inputField: "mov_fecha_entrega", ifFormat: "%Y-%m-%d", button: "im-campo2"});
            });
            function cancelar() {
                var doc = $('#mov_documento').val();
                $.post('actions_industrial_ingresopt.php', {op: 1, id: doc}, function (dt) {
                    if (dt == 0)
                    {
                        mnu = window.parent.frames[0].document.getElementById('lock_menu');
                        mnu.style.visibility = "hidden";
                        grid = window.parent.frames[1].document.getElementById('grid');
                        grid.style.visibility = "hidden";
                        parent.document.getElementById('bottomFrame').src = '';
                    } else {
                        alert(dt);
                    }
                })
            }
            function s(obj) {
                n = obj.lang;
                var vt = parseFloat($(obj).val()) * parseFloat($('#mov_cantidad' + n).val());
                $('#mov_val_total' + n).val(vt);
                doc = document.getElementsByClassName('tot');
                n = 0;
                sum = 0;
                while (n < doc.length) {
                    n++;
                    sum = sum + parseFloat($('#mov_val_total' + n).val());
                }
                $('#subtotal').val(sum);
                t();
            }

            function d() {
                var d = parseFloat($('#subtotal').val() * 1) * parseFloat($('#descuentop').val() * 1) / 100;
                d = d.toFixed(2);
                $('#descuento').val(d);
            }
            function i() {
                if (ivap.checked == true) {
                    var i = (parseFloat($('#subtotal').val() * 1) - parseFloat($('#descuento').val() * 1)) * (0.12);
                    i = i.toFixed(2);
                    $('#iva').val(i);
                } else {
                    $('#iva').val(0);
                }
            }
            function t() {
                var t = parseFloat($('#subtotal').val() * 1) - parseFloat($('#descuento').val() * 1) + parseFloat($('#iva').val() * 1) + parseFloat($('#mov_flete').val() * 1);
                t = t.toFixed(2);
                $('#total').val(t);
            }

            function save(id) {
                var data = Array();
                doc = document.getElementsByClassName('tot');
                n = 0;
                if (ivap.checked == true) {
                    iva = 1;
                } else {
                    iva = 0;
                }
                while (n < doc.length) {
                    n++;
                    mov_id = $('#mov_id' + n).val();
                    mov_val_unit = $('#mov_val_unit' + n).val();
                    data.push(mov_id + '&' +
                            mov_fecha_entrega.value + '&' +
                            mov_num_factura.value + '&' +
                            mov_pago.value + '&' +
                            mov_direccion.value + '&' +
                            mov_val_unit + '&' +
                            descuentop.value + '&' +
                            iva + '&' +
                            mov_flete.value
                            );
                }
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        doc = document.getElementsByClassName('tot');
                        n = 0;
                        if (mov_num_factura.value == 0) {
                            $("#mov_num_factura").css({borderColor: "red"});
                            $("#mov_num_factura").focus();
                            return false;
                        }
                        else if (mov_pago.value.length == 0) {
                            $("#mov_pago").css({borderColor: "red"});
                            $("#mov_pago").focus();
                            return false;
                        }
                        else if (mov_direccion.value.length == 0) {
                            $("#mov_direccion").css({borderColor: "red"});
                            $("#mov_direccion").focus();
                            return false;
                        }
                        else if (doc.length != 0) {
                            while (n < doc.length) {
                                n++;
                                if ($('#mov_val_unit' + n).val() == 0) {
                                    $('#mov_val_unit' + n).css({borderColor: "red"});
                                    $('#mov_val_unit' + n).focus();
                                    return false;
                                }
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 0, 'data[]': data, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            window.history.go(0);
                        } else {
                            alert(dat[0]); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }


        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            #pago{
                width: 150px;
            }
            #mov_pago{
                width: 160px; 
            }

        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save">
            <table id="tbl_form" >
                <thead>
                    <tr><th colspan="8" ><?PHP echo 'INGRESO DE FACTURA PRODUCTO TERMINADO '.$bodega?></th></tr>
                </thead>
                <tr>
                    <td>Fabrica:</td>
                    <td><input type="text" size="20"  id="fabrica" value="<?php echo $rst[emp_descripcion] ?>" readonly /></td>
                    <td>Fecha de Orden:</td>
                    <td><input type="text" size="17" id="mov_fecha_trans" value="<?php echo $rst[mov_fecha_trans] ?>" readonly/>
                    <td>Fecha de Entrega:</td>
                    <td><input type="text" size="12" name="mov_fecha_entrega" id="mov_fecha_entrega" value="<?php echo $rst[mov_fecha_entrega] ?>"/><img src="../Img/calendar.png" id="im-campo2"/></td>
                </tr>
                <tr>
                    <td>Orden #:</td>
                    <td><input type="text" size="20"  id="mov_documento" value="<?php echo $rst[mov_documento] ?>" readonly /></td>
                    <td>Factura #:</td>
                    <td><input type="text" size="20"  id="mov_num_factura" value="<?php echo $rst[mov_num_factura] ?>"  /></td>
                    <td>Proveedor #:</td>
                    <td colspan="2"><input type="text" size="25"  id="cli_nombre" value="<?php echo $rst[cli_raz_social] ?>" readonly />
                    </td>
                </tr>
                <tr>
                    <td>Condicion de Pago:</td>
                    <td><select id="mov_pago" onchange="load_datos(this)">
                            <option value="0">Seleccione</option>
                            <option value="1">Contado</option>
                            <option value="2">30 dias</option>
                            <option value="3">60 dias</option>
                            <option value="4">90 dias</option>
                            <option value="5">120 dias</option>
                            <option value="6">150 dias</option>

                        </select>
                    </td>
                    <td>Direccion de Entrega:</td>
                    <td><input type="text" size="20"  id="mov_direccion" value="<?php echo $mov_direccion ?>"/></td>
                </tr>
                <thead id="tabla">
                    <tr class="head">
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Val. Unit.</th>
                        <th>Val. Total</th>
                    </tr>
                </thead>
                <?PHP
                $n = 0;
                while ($rst1 = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><input type ="text" size="10"  id="mov_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[mov_id] ?>" lang="1" hidden/>
                            <input type ="text" size="2"  id="item"  readonly value="<?PHP echo $n ?>"/></td>
                        <td><input type ="text" size="10"  id="pro_codigo<?PHP echo $n ?>"  value="<?php echo $rst1['pro_codigo'] ?>" lang="1" readonly/></td>
                        <td><input type="text" size="30" id="pro_descripcion<?PHP echo $n ?>" value="<?php echo $rst1['pro_descripcion'] ?>" lang="1" readonly/></td>                            
                        <td><input type ="text" size="10"  id="pro_uni<?PHP echo $n ?>"  value="<?php echo $rst1['pro_uni'] ?>" lang="1" readonly/></td>
                        <td style="display:none"><input type ="text" size="20"  id="pro_uni2<?PHP echo $n ?>"  value="<?php echo $rst1['pro_uni2'] ?>" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="mov_cantidad<?PHP echo $n ?>"  value="<?php echo $rst1['mov_cantidad'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                        <td><input type = "text" size = "10" id = "mov_val_unit<?PHP echo $n ?>" value = "<?php echo $rst['mov_val_unit'] ?>" onblur="s(this)" lang="<?PHP echo $n ?>"/></td>
                        <td><input type = "text" size = "10" class="tot" id = "mov_val_total<?PHP echo $n ?>" value = "<?php echo $rst['mov_val_total'] ?>" readonly/></td>
                    </tr>
                    <?PHP
                }
                ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Subtotal</td>
                    <td><input type = "text" size = "17" id = "subtotal" onblur = "t()" value = "<?php echo $rst['subtotal'] ?>" readonly/></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Descuento <input type = "text" size = "3" id = "descuentop" onblur = "d(), t()" value = "<?php echo $rst['descuentop'] ?>" onblur="d()"/>%</td>
                    <td><input type = "text" size = "17" readonly id = "descuento" value = "<?php echo $rst['descuento'] ?>" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type = "checkbox" id = "ivap" onclick="i(), t()"/>Iva</td>
                    <td><input type = "text" size = "17" readonly id = "iva" value = "<?php echo $rst['iva'] ?>" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Flete</td>
                    <td><input type = "text" size = "20" id = "mov_flete" onblur = "t()" value = "<?php echo $rst['mov_flete'] ?>" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td><input type = "text" size = "17" readonly id = "total" value = "<?php echo $rst['total'] ?>"/></td>
                </tr>
                <tfoot>
                    <tr>
                        <td colspan = "2">
                            <button id = "guardar">Guardar</button>
                            <button id = "cancelar">Cancelar</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
