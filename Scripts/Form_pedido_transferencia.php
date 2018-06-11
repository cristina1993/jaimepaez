<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $secuencial = $_GET[sec];
    $pedido = trim($_REQUEST[ped], ';');
    $pedi = explode(';', $pedido);
    $ped = array_unique($pedi);
    $m = 0;
    while ($m < count($ped)) {
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_encab_pedidos_venta($ped[$m]));
        $m++;
    }
    $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_un_local($rst[ped_local]));
    $rst2 = pg_fetch_array($Clase_industrial_ingresopt->lista_locales_codcli($rst[cli_id]));
    $rst['ped_factual'] = date('Y-m-d');
}
$cns_loc = $Clase_industrial_ingresopt->lista_locales();
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id ?>;
            var emi =<?php echo $emisor ?>;
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
                if (id != 0) {
                    seccion_auto();
                    calculo();
                }
                parent.document.getElementById('contenedor2').rows = "*,80%";
            });

            function save(id, x) {
                var data1 = Array();
                var data2 = Array();
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    trs_egr = 20;
                    trs_ing = 4;
                    bod_org = $('#org_bod').val();
                    cli_org = $('#org_cli').val();
                    bod_des = $('#des_bod').val();
                    cli_des = $('#des_cli').val();
                    doc = $('#mov_documento').val();
                    gui = $('#pedido' + this.value).val();
                    fch = $('#mov_fecha_actual').val();
                    cnt = $('#ingreso' + this.value).val();
                    tbl = '0';
                    cunit = $('#cunit' + this.value).val();
                    ctot = $('#ctot' + this.value).val();
                    if (cnt > 0) {
                        cnt;
                        data1.push(pro + '&' + trs_egr + '&' + cli_des + '&' + bod_org + '&' + doc + '&' + gui + '&' + fch + '&' + cnt + '&' + tbl + '&' + cunit + '&' + ctot + '&' + fch + '&0');
                        data2.push(pro + '&' + trs_ing + '&' + cli_org + '&' + bod_des + '&' + doc + '&' + gui + '&' + fch + '&' + cnt + '&' + tbl + '&' + cunit + '&' + ctot + '&' + fch + '&0');
                    }
                });
                $.ajax({
                    beforeSend: function () {
//                        var v = 0;
//                            $('.itm').each(function () {
//                                cod = $('#ingreso' + this.value).val();
//                                if (cod == 0) {
//                                    $('#ingreso' + this.value).css({borderColor: "red"});
//                                    $('#ingreso' + this.value).focus();
//                                    v = 1;
//                                }
//                            });
//                            if (v == 1) {
//                                return false;
//                            } else {
//                                return true;
//                                loading('visible');
//                            }
                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 14, 'data1[]': data1, 'data2[]': data2, id: id, x: x},
                    success: function (dt) {
                        loading('hidden');
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#mov_documento').val(dat[1]);
                            window.history.go(0);
                        } else {
                            alert(dat[0]);
                        }
                    }
                })

            }

            function seccion_auto() {
                $.post("actions_industrial_ingresopt.php", {op: 4}, function (dt) {
                    mov_documento.value = '001-' + dt;
                });
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
//                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_seguimiento_bodega.php';
            }

            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

//            function seccion_auto() {
//                $.post("actions_industrial_ingresopt.php", {op: 4}, function (dt) {
//                    mov_documento.value = '001-' + dt;
//                })
//            }

            function inventario(obj) {
                if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                    alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    total();
                }
            }

            function total(obj) {
                n = obj.lang;
                entre = parseInt($("#entregados" + n).val());
                ingre = $("#ingreso" + n).val();
                ntrg = parseInt(ingre) + parseInt(entre);
                $("#entregado" + n).val(ntrg);
                ntg = $("#entregado" + n).val();
                soli = $('#solicitado' + n).val().replace(',', '');
                sald = soli - ntg;
                $('#saldo' + n).val(sald);
                ct = parseFloat($('#cunit' + n).val()) * parseFloat($('#ingreso' + n).val());
                $('#ctot' + n).val(ct);

            }

            function calculo() {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        soli = 0;
                        entreg = 0;
                        sald = 0;
                    } else {
                        soli = $('#solicitado' + n).val().replace(',', '');
                        entreg = $('#entregado' + n).val().replace(',', '');
                        sald = soli - entreg;
                        $('#saldo' + n).val(sald);
                        if (sald == 0) {
                            $('#ingreso' + n).attr('readonly', true);
                        } else {
                            $('#ingreso' + n).attr('readonly', false);
                        }

                    }
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }

            #descripcion{
                width: 150px;
            }
            #emp_id{
                width: 140px;
            }
            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 10px;
            }

            #txt_salir{
                width:24px;
                font-size:18px;  
                font-weight:bolder; 
                padding:3px; 
                border-radius:2px; 
                background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */
                position:fixed; 
                cursor:pointer; 
                color:white;
                font-weight:bolder; 
            }
            #txt_salir:hover{
                color:#D8000C; 

            }            
            .auxBtn{
                width:14px; 
                padding:2px; 
            }
            table{
                border-collapse:collapse; 
            }
            #usr{
                float:right; 
                margin-right:20px; 
                text-transform:uppercase;
                display:none; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />
        <div id="cargando"></div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form" border="0" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th colspan="10" ><?PHP echo 'TRANSFERENCIA DE PRODUCTO TERMINADO BODEGA ' . $bodega ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                            <font id="usr"><?php echo $_SESSION[usuario] ?></font>                            
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="6" align="left" >Guia :&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" size="25"  id="mov_documento" readonly value="<?php echo $secuencial ?>"/>
                        <input type="hidden"   id="emisor" readonly value="<?php echo $emisor ?>"  />
                        &nbsp;&nbsp;Fecha Pedido:
                        &nbsp;<input type="text" size="20" name="fecha1" id="mov_fecha_trans" readonly value="<?php echo $rst['ped_femision'] ?>"/>
                        Fecha Actual:
                        &nbsp;<input type="text" size="20" name="fecha2" id="mov_fecha_actual" readonly value="<?php echo $rst['ped_factual'] ?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="left">
                        Origen :
                        <input type="text" size="25" id="org" onchange="cliente(this)" readonly value="<?php echo $rst1['emi_nombre_comercial'] ?>"/>
                        <input type ="hidden" size="20" id="org_cli"  value="<?php echo $rst1['emi_cod_cli'] ?>" />
                        <input type ="hidden" size="20" id="org_bod"  value="<?php echo $rst['ped_local'] ?>" />
                        &nbsp;&nbsp;Destino :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="text" size="20" id="des" onchange="cliente(this)" value="<?php echo $rst['ped_nom_cliente'] ?>" readonly/>
                        <input type ="hidden" size="20"  id="des_cli"  value="<?php echo $rst['cli_id'] ?>" />
                        <input type ="hidden" size="20"  id="des_bod"  value="<?php echo $rst2['emi_cod_punto_emision'] ?>" />
                        Transaccion :&nbsp;
                        <select id="trs_id" style="width:160px;" disabled>
                            <option value="20">EGRESO TRANSFERENCIA</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <?php
                $m = 0;
                while ($m < count($ped)) {
                    $rst_ped = pg_fetch_array($Clase_industrial_ingresopt->lista_pedidos_venta(trim($ped[$m])));
                    $m++;
                    $cns_det = $Clase_industrial_ingresopt->lista_pedido_venta($rst_ped[ped_id], $rst_ped[ped_num_registro]);
                    ?>
                    <tr>
                        <td colspan="6" align="left">Pedido :
                            <input type="text" size="25"  id="<?php echo 'mov_guia_transporte' . $m ?>" readonly value="<?php echo $rst_ped[ped_num_registro] ?>"/>
                        </td>
                    </tr>
                    <thead id="tabla">
                        <tr>
                            <th>Item</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Inventario O.</th>
                            <th>Inventario D.</th>
                            <th>Solicitado</th>
                            <th>Entregado</th>
                            <th>Saldo</th>
                            <th>Ingreso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $n = 0;
                        while ($rst_det = pg_fetch_array($cns_det)) {
                            $n++;
                            if ($ctr_inv == 0) {
                                $fra1 = '';
                            } else {
                                $fra = "and m.bod_id=$rst1[emi_cod_punto_emision]";
                            }
                            $rst_inv = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fact($rst_det[pro_id], $fra));
                            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                            $rst_inv_dest = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fac_destino($rst_det[pro_id], $rst2[emi_cod_punto_emision], '0'));
                            $inv_dest = $rst_inv_dest[ingreso] - $rst_inv_dest[egreso];
                            $rst_cost = pg_fetch_array($Clase_industrial_ingresopt->lista_costos_mov($rst_det[pro_id], $fra));
                            $rst_cost['mov_val_unit'] = (($rst_cost[ingreso] - $rst_cost[egreso]) / ($rst_cost[icnt] - $rst_cost[ecnt]));
                            $rst3 = pg_fetch_array($Clase_industrial_ingresopt->lista_inventario($rst_det['ped_num_registro'], $rst_det[pro_id]));
                            if ($rst3[suma] == '') {
                                $rst3[suma] = 0;
                            } else {
                                $rst3[suma];
                            }
                            ?>
                            <tr>
                                <td>
                                    <input type="text" size="5" class="itm" id="<?php echo 'item' . $n . $m ?>" name="<?php echo 'item' . $n . $m ?>" readonly value="<?php echo $n . $m ?>" lang="<?php echo $n . $m ?>" />
                                    <input type ="hidden" size="20" id="<?php echo 'pro_id' . $n . $m ?>" name="<?php echo 'pro_id' . $n . $m ?>" value="<?php echo $rst_det[pro_id] ?>" lang="<?php echo $n . $m ?>"/>
                                    <input type ="hidden" size="20" id="<?php echo 'pedido' . $n . $m ?>" name="<?php echo 'pedido' . $n . $m ?>" value="<?php echo $rst_ped[ped_num_registro] ?>" lang="<?php echo $n . $m ?>"/>
                                </td>
                                <td><input type="text" size="15" id="<?php echo 'pro_codigo' . $n . $m ?>" name="<?php echo 'pro_codigo' . $n . $m ?>" readonly value="<?php echo $rst_det['det_cod_producto'] ?>" lang="<?php echo $n . $m ?>" />
                                    <input type="hidden" size="15" id="<?php echo 'cunit' . $n . $m ?>" name="<?php echo 'cunit' . $n . $m ?>"  value="<?php echo $rst_cost['mov_val_unit'] ?>" lang="<?php echo $n . $m ?>" />
                                    <input type="hidden" size="15" id="<?php echo 'ctot' . $n . $m ?>" name="<?php echo 'ctot' . $n . $m ?>"  lang="<?php echo $n . $m ?>" />
                                </td>
                                <td><input type="text" size="60" id="<?php echo 'pro_descripcion' . $n . $m ?>" name="<?php echo 'pro_descripcion' . $n . $m ?>" readonly value="<?php echo $rst_det['det_descripcion'] ?>" lang="<?php echo $n . $m ?>" style="font-weight: 100"/></td>
                                <td><input type="text" size="8" id="<?php echo 'inventario' . $n . $m ?>" name="<?php echo 'inventario' . $n . $m ?>" readonly value="<?php echo $inv ?>" lang="<?php echo $n . $m ?>" /></td>
                                <td><input type="text" size="8" id="<?php echo 'inventario_dest' . $n . $m ?>" name="<?php echo 'inventario_dest' . $n . $m ?>" readonly value="<?php echo $inv_dest ?>" lang="<?php echo $n . $m ?>" /></td>
                                <td><input type="text" size="5" id="<?php echo 'solicitado' . $n . $m ?>" name="<?php echo 'solicitado' . $n . $m ?>" readonly value="<?php echo $rst_det['det_cantidad'] ?>" lang="<?php echo $n . $m ?>" onchange="calculo(this)"/></td>
                                <td>
                                    <input type="text" size="5" id="<?php echo 'entregado' . $n . $m ?>" name="<?php echo 'entregado' . $n . $m ?>" readonly value="<?php echo $rst3[suma] ?>" lang="<?php echo $n . $m ?>" />
                                    <input type="hidden" size="5" id="<?php echo 'entregados' . $n . $m ?>" name="<?php echo 'entregados' . $n . $m ?>" value="<?php echo $rst3[suma] ?>">
                                </td>
                                <td><input type="text" size="5" id="<?php echo 'saldo' . $n . $m ?>" name="<?php echo 'saldo' . $n . $m ?>" readonly value="" lang="<?php echo $n . $m ?>"  onchange="estado(this)"/></td>
                                <td><input type="text" size="5" id="<?php echo 'ingreso' . $n . $m ?>" name="<?php echo 'ingreso' . $n . $m ?>" value="0"  lang="<?php echo $n . $m ?>" onchange="total(this)" onblur="inventario(this)"></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <button id="guardar" onclick="save(0, 0)">Guardar</button>   
        <button id="cancelar" >Cancelar</button>   
    </body>
</html>
