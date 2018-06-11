<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
$Ing = new Clase_industrial_ingresopt();
$cns_pro = $Ing->lista_producto_total();
$cns_prov = $Ing->lista_clientes_tipo(0);
//$secuencial = $_GET[sec];
$id = 0;
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario Industrial Ingreso General</title>
        <script>
            emi = '<?php echo $emisor ?>';
            cli = '<?php echo $id_cli ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            id = '<?php echo $id ?>';

            $(function () {
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (pro_descripcion.value != '' && (mov_cantidad.value != '' && parseFloat(mov_cantidad.value) != 0) && (mov_cost_unit.value != '' && parseFloat(mov_cost_unit.value) != 0) && (mov_cost_tot.value != '' && parseFloat(mov_cost_tot.value) != 0)) {
                        clonar();
                    }
                });
                if (id == '0') {
                    seccion_auto();
                }
            });

            function seccion_auto() {
                $.post("actions_industrial_ingresopt.php", {op: 4}, function (dt) {
                    mov_documento.value = '001-' + dt;
                });
            }
            function save(e) {
                var data = Array();
                n = 0;
                j = $('.itm').length;
                while (n < j) {
                    n++;
                    pro_id = $('#pro_id' + n).html();
                    mov_cantidad = $('#mov_cantidad' + n).html();
                    pro_tbl = $('#pro_tbl' + n).html();
                    uni = $('#lblmov_cost_unit' + n).html();
                    tot = $('#lblmov_cost_tot' + n).html();
                    data.push(
                            pro_id + '&' +
                            '8' + '&' +
                            cli_id.value + '&' +
                            e + '&' +
                            mov_documento.value + '&' +
                            '' + '&' +
                            mov_fecha_trans.value + '&' +
                            mov_cantidad + '&' +
                            pro_tbl + '&' +
                            uni + '&' +
                            tot + '&' +
                            n_doc_ntrg.value + '&' +
                            tip_doc.value);
                }
                var fields = Array();
                $('#encabezado').find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });

                $('#lista').find('td').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + $(elemento).html();
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        if ($('#tip_doc').val() == 0) {
                            alert('Seleccione un Tipo de Documento');
                            $('#tip_doc').css({borderColor: "red"});
                            $('#tip_doc').focus();
                            return false;
                        } else if ($('#cli_nombre').val().length == 0) {
                            $("#cli_nombre").css({borderColor: "red"});
                            $("#cli_nombre").focus();
                            return false;
                        } else if ($('#n_doc_ntrg').val().length == 0) {
                            $("#n_doc_ntrg").css({borderColor: "red"});
                            $("#n_doc_ntrg").focus();
                            return false;
                        }
                        if ($('#pro_id1').html() == null) {
                            alert('INGRESE POR LO MENOS UN REGISTRO');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 12, 'data[]': data, 'fields[]': fields, x: 1}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#mov_documento').val(dat[1]);
                            loading('hidden');
                            imprimir();
                            cancelar();
                        } else {
                            alert(dat[0]);
                        }
                    }
                });
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            }
            function caracter(e, obj, x) {
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)
                    $('#pro_lote').focus();
                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    load_producto(0)
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#mov_cantidad').focus();
                    load_producto(1)
                }
            }

            function load_producto(v) {

                if (v == 1) {
                    vl = $('#pro_codigo').val();
                } else {
                    vl = $('#pro_codigo').val();
                    lt = 0;
                    $('#pro_descripcion').focus();
                }
                $.post("actions_industrial_ingresopt.php", {op: 10, id: vl, s: emi},
                        function (dt) {
                            dat = dt.split('&');
                            $('#pro_codigo').val(dat[1]);
                            $('#pro_descripcion').val(dat[2]);
                            $('#pro_id').val(dat[0]);
                            $('#pro_tbl').val(dat[7]);
                            $('#pro_uni').val(dat[9]);
                            $('#mov_cantidad').focus();
                        });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function del(doc) {
                $.post("actions_industrial_ingresopt.php", {op: 1, id: doc}, function (dt) {
                    if (dt == 0)
                    {
                        cancelar();
                    }
                })
            }
            function clonar() {
                d = 0;
                n = 0;
                j = $('.itm').length;
                if (j > 0) {
                    while (n < j) {
                        n++;
                        if ($('#pro_id' + n).html() == pro_id.value) {
                            d = 1;
                            cant = parseFloat($('#mov_cantidad' + n).html()) + parseFloat(mov_cantidad.value);
                            $('#mov_cantidad' + n).html(cant.toFixed(dc));
                            val_tot = parseFloat($('#mov_cost_tot' + n).html()) + parseFloat(mov_cost_tot.value);
                            $('#mov_cost_tot' + n).html(val_tot.toFixed(dec));
                            val_uni = parseFloat($('#mov_cost_tot' + n).html()) / parseFloat($('#mov_cantidad' + n).html());
                            $('#mov_cost_unit' + n).html(val_uni.toFixed(dec));
                            var cunit = '<label  hidden id="lblmov_cost_unit' + n + '">' + val_uni.toFixed(6) + '</label>';
                            $('#mov_cost_unit' + n).append(cunit);
                            var ctot = '<label hidden id="lblmov_cost_tot' + n + '">' + val_tot.toFixed(6) + '</label>';
                            $('#mov_cost_tot' + n).append(ctot);
                        }
                    }
                }
                if (d == 0) {
                    i = j + 1;
                    can = parseFloat(mov_cantidad.value);
                    uni = parseFloat(mov_cost_unit.value);
                    tot = parseFloat(mov_cost_tot.value);
                    var fila = '<tr class="itm">' +
                            '<td>' + i + '</td>' +
                            '<td id="pro_codigo' + i + '">' + pro_codigo.value + '</td>' +
                            '<td  hidden id="pro_id' + i + '">' + pro_id.value + '</td>' +
                            '<td hidden id="pro_tbl' + i + '">' + pro_tbl.value + '</td>' +
                            '<td id="pro_descripcion' + i + '">' + pro_descripcion.value + '</td>' +
                            '<td id="pro_uni' + i + '">' + pro_uni.value + '</td>' +
                            '<td id="mov_cantidad' + i + '" align="right">' + can.toFixed(dc) + '</td>' +
                            '<td id="mov_cost_unit' + i + '" align="right">' + uni.toFixed(dec) +
                            '<label hidden id="lblmov_cost_unit' + i + '">' + uni.toFixed(6) + '</label>' + '</td>' +
                            '<td id="mov_cost_tot' + i + '" align="right">' + tot.toFixed(dec) +
                            '<label hidden id="lblmov_cost_tot' + i + '">' + tot.toFixed(6) + '</label>' + '</td>' +
                            '<td></td>' +
                            '</tr>';
                    $('#lista').append(fila);
                }
                pro_codigo.value = '';
                pro_id.value = '';
                pro_tbl.value = '';
                pro_descripcion.value = '';
                pro_uni.value = '';
                mov_cantidad.value = '';
                mov_cost_unit.value = '';
                mov_cost_tot.value = '';
                $('#pro_codigo').focus();
                $('#lblmov_cost_tot').html('');
                $('#lblmov_cost_unit').html('');
                total();

            }
            function total() {
                doc = document.getElementsByClassName('itm');
                n = 0;
                sum = 0;
                su = 0;
                st = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#mov_cantidad' + n).html().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).html();
                    }
                    if ($('#mov_cost_unit' + n).html().length == 0) {
                        u = 0;
                    } else {
                        u = $('#mov_cost_unit' + n).html();
                    }
                    if ($('#mov_cost_tot' + n).html().length == 0) {
                        t = 0;
                    } else {
                        t = $('#mov_cost_tot' + n).html();
                    }

                    sum = sum + parseFloat(can);
                    su = su + parseFloat(u);
                    st = st + parseFloat(t);
                }

                $('#total').html(sum.toFixed(dc));
                $('#t_uni').html(su.toFixed(dec));
                $('#t_tot').html(st.toFixed(dec));
            }

            function costo(x) {
                can = $('#mov_cantidad').val();
                uni = $('#mov_cost_unit').val();
                tot = $('#mov_cost_tot').val();
                if (can.length == 0) {
                    can = 0;
                }
                if (uni.length == 0) {
                    uni = 0;
                }
                if (tot.length == 0) {
                    tot = 0;
                }
                if (x == 1) {
                    t = parseFloat(can) * parseFloat(uni);
                    $('#mov_cost_tot').val(t.toFixed(dec));
                    $('#lblmov_cost_tot').html(t.toFixed(6));
                    valores_lbls(1, t);
                } else {
                    if (parseFloat(can) != 0) {
                        t = parseFloat(tot) / parseFloat(can);
                    } else {
                        t = 0;
                    }
                    $('#mov_cost_unit').val(t.toFixed(dec));
                    $('#lblmov_cost_unit').html(t.toFixed(6));
                    valores_lbls(2, t);
                }
            }

            function valores_lbls(x, v) {
                uni = parseFloat($('#mov_cost_unit').val());
                tot = parseFloat($('#mov_cost_tot').val());
                if (x == 1) {
                    $('#lblmov_cost_unit').html(uni.toFixed(6));
                    $('#lblmov_cost_tot').html(v.toFixed(6));
                } else {
                    $('#lblmov_cost_unit').html(v.toFixed(6));
                    $('#lblmov_cost_tot').html(tot.toFixed(6));
                }
            }

            function validar(obj) {
                num = obj.value;
                if (num.length != 17) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple con la estructura ejem: 000-000-000000000');
                }
                doc_duplicado();
            }

            function cliente(obj) {
                $.post("actions_industrial_ingresopt.php", {op: 5, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        cli_id.value = dat[0];
                        cli_nombre.value = dat[1];
                        doc_duplicado();
                    }
                });
            }

            function imprimir() {
                $('#head_frm').hide();
                $('#botones').hide();
                $('#add').hide();
                $('.cerrar').hide();
                window.print();
                $('#head_frm').show();
                $('#botones').show();
                $('#add').show();
                $('.cerrar').show();
            }

            function doc_duplicado() {
                num_doc = $('#n_doc_ntrg').val();
                id_cli = $('#cli_id').val();
                tip = $('#tip_doc').val();

                if (num_doc.length > 0 && id_cli.length > 0 && num_doc != '0') {
                    $.post("actions_industrial_ingresopt.php", {op: 17, doc: num_doc, id: id_cli, tdoc: tip},
                            function (dt) {
                                dat = dt.split('&');
                                if (dat[0].trim().length > 0) {
                                    alert('EL numero de Factura de este cliente  \n Ya se registraron');
                                    $('#n_doc_ntrg').focus();
                                    $('#n_doc_ntrg').css({borderColor: "red"});
                                    $('#n_doc_ntrg').val('');
                                }
                            });
                }
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
            #head td{
                background:#00529B;
                color:white !important;
                font-weight:bolder; 
                text-align:center; 
            }
            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form">
                <thead>
                    <tr>
                        <th colspan="8" ><?PHP echo 'INGRESO GENERAL' ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>
                <tbody id="encabezado">
                    <tr>
                        <td colspan="2">
                            Documento No:
                            <input type="text" size="14"  id="mov_documento" readonly value="<?php echo $secuencial ?>" />
                            <input type="hidden" id="emisor" readonly value="<?php echo $emisor ?>"  />
                        </td>
                        <td colspan="1">
                            Fecha de Ingreso:
                            <input type="text" size="12" name="fecha1" id="mov_fecha_trans" value="<?php echo date('Y-m-d') ?>" readonly/>
                        </td>
                        <td colspan="2">
                            Transaccion:
                            <input type="text" size="6"  id="trs_id" readonly value="<?php echo 'Ingresos' ?>" /> 
                        </td>
                        <td colspan="2">
                            Destino:
                            <input type="text" size="10" value="<?php echo $bodega ?>" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            Tipo Documento:
                            <select id="tip_doc" style="width:150px;" onchange="doc_duplicado()">
                                <?php
                                $cns_tdc = $Ing->lista_tipo_documentos();
                                echo "<option value='0'>Elija un Tipo</option>";
                                while ($rst_tdc = pg_fetch_array($cns_tdc)) {
                                    echo "<option value='$rst_tdc[tdc_id]'>$rst_tdc[tdc_codigo] - $rst_tdc[tdc_descripcion] </option>";
                                }
                                ?>
                                <!--                                <option value="0">Elija un Tipo</option>
                                                                <option value="1">Factura</option>
                                                                <option value="2">Guia de Remision</option>
                                                                <option value="3">Notas de Venta RISE</option>
                                                                <option value="4">Liquidacion de Compra de bienes y prestacion de servicios</option>
                                                                <option value="5">Tiquetes emitidos por maquinas registradoras y boletos o entradas a espectaculos publicos</option>
                                                                <option value="6">Otros documentos autorizados</option>-->
                            </select>
                        </td>
                        <td colspan="2">
                            Proveedor:
                            <input type="text" size="30" id="cli_nombre" onchange="cliente(this)" value="<?php echo $rst['cli_raz_social'] ?>" list="lista_proveedor"/>
                            <input type ="hidden" size="20"  id="cli_id"  value="" />
                        </td>
                        <td colspan="4">
                            No Documento Entrega:<input type="text" id="n_doc_ntrg" maxlength="17" onchange="validar(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')"/>
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Cost. Unit</th>
                        <th>Cost. Tot</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="tbl_frm_aux" id="head_frm">   
                    <tr>
                        <td></td>
                        <td>
                            <input type="text" size="10" id="pro_codigo" list="productos" onkeypress="caracter(event, this, 0)" maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" />
                            <input type="hidden" size="10" id="pro_id" />
                            <input type="hidden" size="10" id="pro_tbl" />
                        </td>
                        <td><input type="text" size="30" readonly id="pro_descripcion"  /></td>
                        <td><input type="text" size="5" readonly id="pro_uni"  /></td>
                        <td><input type="text" size="8" id="mov_cantidad" onchange="costo(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                        <td><input type="text" size="8" id="mov_cost_unit" onchange="costo(1)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                            <label hidden id="lblmov_cost_unit"></label></td>
                        <td><input type="text" size="8" id="mov_cost_tot" onchange="costo(2)" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                            <label hidden id="lblmov_cost_tot"></label></td>
                        <td><button id="add"/>+</button></td>
                    </tr>

                </tbody>
                <tbody class="tbl_frm_aux" id="lista" >   
                </tbody>
                <tr class="add">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" >TOTAL</td>
                    <td align="right" style="font-size:14px; " id="total">0</td>
                    <td align="right" style="font-size:14px; " id="t_uni">0</td>
                    <td align="right" style="font-size:14px; " id="t_tot">0</td>
                    <td></td>
                </tr>
                <tr>
            </table>
        </form>

        <table id="botones">
            <td colspan="3">
                <?php
                if ($Prt->add == 0 || $Prt->edition == 0) {
                    ?>
                    <button id="save" onclick="save('<?php echo $emisor ?>', '<?php echo $id_cli ?>')">Guardar</button>
                <?php }
                ?>
                <button id="cancel" onclick="cancelar()">Cancelar</button>
            </td>
        </tr> 
    </table>
    <datalist id="productos">
        <?php
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[id]' >$rst_pro[mp_c] $rst_pro[mp_d] </option>";
        }
        ?>
    </datalist>
    <datalist id="lista_proveedor">
        <?php
        while ($rst_prov = pg_fetch_array($cns_prov)) {
            echo "<option value='$rst_prov[cli_id]' >$rst_prov[nombres]</option>";
        }
        ?>
    </datalist>
