<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
include_once '../Clases/clsClase_industrial_movimientopt.php'; // cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_ingreso_industrial($id));
    $cns = $Clase_industrial_ingresopt->lista_ingreso_industrial_documento($rst['mov_documento']);
    $x = $_GET[x];
    $fila = pg_numrows($cns);
} else {
    $id = 0;
    $rst['mov_fecha_trans'] = date('Y-m-d');
    $fila = 0;
//    $secuencial = $_GET[sec];
}
$cns_pro = $Clase_industrial_ingresopt->lista_clientes_tipo(0);
$cns_trans = $Clase_industrial_movimientopt->lista_combo_transacciones();
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
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            $(function () {
                $('#pro_codigo1').attr('disabled', true);
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    des = $('#pro_descripcion' + i).val();
                    can = $('#mov_cantidad' + i).val();
                    tot = $('#mov_cost_tot' + i).val();
                    uni = $('#mov_cost_unit' + i).val();
                    if (des.length != 0 && can != '' && parseFloat(can) != 0 && parseFloat(tot) != 0 && tot.length != 0 && parseFloat(uni) != 0 && uni.length != 0) {
                        clona_fila($('#dinamica'));
                    }
                });
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
                if (id == 0) {
                    $("#factura").hide();
                    $("#guardar").show();
                    $('#mov_cantidad1').val('0');
                }
                 
                if (id == 0) {
                    seccion_auto();
                    load_transaccion(0);
                }
                parent.document.getElementById('contenedor2').rows = "*,80%";
            });

            function save(id, x) {
                var data = Array();
                n = 0;
                i = $('.itm');
                while (n < i.length) {
                    n++;
                    if ($('#pro_id' + n).val() != null) {
                        pro = $('#pro_id' + n).val();
                        can = $('#mov_cantidad' + n).val();
                        tab = $('#pro_tbl' + n).val();
                        trs_id = $('#trs_id').val().substring(1, 100);
                        uni = $('#lblmov_cost_unit' + n).html();
                        tot = $('#lblmov_cost_tot' + n).html();
                        data.push(pro + '&' +
                                trs_id + '&' +
                                cli_id.value + '&' +
                                emisor.value + '&' +
                                mov_documento.value + '&' +
                                mov_guia_transporte.value + '&' +
                                mov_fecha_trans.value + '&' +
                                can + '&' +
                                '0&' +
                                uni + '&' +
                                tot + '&' +
                                '' + '&' +
                                '0'
                                );
                    }
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        if (mov_documento.value.length == 0) {
                            $("#mov_documento").css({borderColor: "red"});
                            $("#mov_documento").focus();
                            return false;
                        } else if (mov_fecha_trans.value.length == 0) {
                            $("#mov_fecha_trans").css({borderColor: "red"});
                            $("#mov_fecha_trans").focus();
                            return false;
                        } else if (trs_id.value == 0) {
                            $("#trs_id").css({borderColor: "red"});
                            $("#trs_id").focus();
                            return false;
                        } else if (cli_nombre.value.length == 0) {
                            $("#cli_nombre").css({borderColor: "red"});
                            $("#cli_nombre").focus();
                            return false;
                        }
                        var tr = $('#dinamica').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pro_descripcion' + n).val() != null) {
                                    if ($('#pro_codigo' + n).val() == 0) {
                                        $('#pro_codigo' + n).css({borderColor: "red"});
                                        $('#pro_codigo' + n).focus();
                                        return false;
                                    }
                                    if ($('#pro_descripcion' + n).val() == 0) {
                                        $('#pro_descripcion' + n).css({borderColor: "red"});
                                        $('#pro_descripcion' + n).focus();
                                        return false;
                                    }
                                    if ($('#mov_cantidad' + n).val() == 0) {
                                        $('#mov_cantidad' + n).css({borderColor: "red"});
                                        $('#mov_cantidad' + n).focus();
                                        return false;
                                    }
                                    if ($('#mov_cost_unit' + n).val() == 0) {
                                        $('#mov_cost_unit' + n).css({borderColor: "red"});
                                        $('#mov_cost_unit' + n).focus();
                                        return false;
                                    }
                                    if ($('#mov_cost_tot' + n).val() == 0) {
                                        $('#mov_cost_tot' + n).css({borderColor: "red"});
                                        $('#mov_cost_tot' + n).focus();
                                        return false;
                                    }
                                    if ($('#trs_id').val().substring(0, 1) == 1) {
                                        if (parseFloat($('#inventario' + n).val()) < parseFloat($('#mov_cantidad' + n).val())) {
                                            alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                                            $('#mov_cantidad' + n).val('');
                                            $('#mov_cantidad' + n).focus();
                                            $('#mov_cantidad' + n).css({borderColor: "red"});
                                            total();
                                            return false;
                                        }
                                    }

                                }
                            }
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_industrial_ingresopt.php',
                    data: {op: 12, 'data[]': data, 'fields[]': fields, id: id, x: 2}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            loading('hidden');
                            imprimir();
                            cancelar();
                            // $('#mov_documento').val(dat[1]);
                            
                        } else {
                            alert(dat[0]); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
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
                main = parent.document.getElementById('mainFrame');
                            main.src = '../Scripts/Lista_industrial_movimientopt.php?fecha1=' + mov_fecha_trans.value + '&fecha2=' + mov_fecha_trans.value;//Cambiar Form_productos
                            mnu = window.parent.frames[0].document.getElementById('lock_menu');
            }

            function load_transaccion(obj) {
                $.post('actions_industrial_ingresopt.php', {id: obj, op: 6}, function (dt) {
                    $('#trs_id').val(dt);
                })
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                tr.find("label").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
                $('#pro_descripcion' + x).focus();
                $('#mov_cantidad' + x).val('0');
                total();
            }
            function elimina_fila(obj) {
                var tr = $('#dinamica').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if (i > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
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
                    }
                })
            }
            function total() {
                var tr = $('#dinamica').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sum = 0;
                su = 0;
                st = 0;
                while (n < i) {
                    n++;

                    if ($('#mov_cantidad' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#mov_cantidad' + n).val();
                    }
                    if ($('#mov_cost_unit' + n).val().length == 0) {
                        u = 0;
                    } else {
                        u = $('#mov_cost_unit' + n).val();
                    }
                    if ($('#mov_cost_tot' + n).val().length == 0) {
                        t = 0;
                    } else {
                        t = $('#mov_cost_tot' + n).val();
                    }
                    sum = sum + parseFloat(can);
                    su = su + parseFloat(u);
                    st = st + parseFloat(t);
                }

                $('#total').html(sum.toFixed(dc));
                $('#tu').html(su.toFixed(dec));
                $('#tt').html(st.toFixed(dec));
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function caracter(e, obj, x) {
                j = obj.lang;
                var ch0 = e.keyCode;
                var ch1 = e.which;
                if (ch0 == 0 && ch1 == 46 && x == 0) { //Punto (Con lector de Codigo de Barras)

                    $(obj).autocomplete({
                        minLength: 0,
                        source: ''
                    });
                } else if (ch0 == 9 && ch1 == 0 && x == 0) { //Tab (Sin lector de Codigo de Barras)
                    v = 0;
                    load_producto(j, v);
                } else if (x == 1 && obj.value.length > 8) {//Desde lote
                    $('#mov_cantidad' + j).focus();
                    v = 1;
                    load_producto(j, v);
                }
            }

            function seleccion() {
                if ($('#trs_id').val() == 0) {
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    n = 0;
                    sum = 0;
                    while (n < i) {
                        n++;
                        $('#pro_codigo' + n).attr('disabled', true);
                    }
                } else {
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
                    n = 0;
                    sum = 0;
                    while (n < i) {
                        n++;
                        $('#pro_codigo' + n).attr('disabled', false);
                        if ($('#trs_id').val().substring(0, 1) == 1) {
                            $('#mov_cost_unit' + n).attr('readonly', true);
                            $('#mov_cost_tot' + n).attr('readonly', true);
                        } else {
                            $('#mov_cost_unit' + n).attr('readonly', false);
                            $('#mov_cost_tot' + n).attr('readonly', false);
                        }
                    }
                }
            }

            function load_producto(j, v) {
                if (v == 1) {
                    vl = $('#pro_codigo' + j).val();
                    lt = 0;
                } else {
                    vl = $('#pro_codigo' + j).val();
                    lt = 0;
                }
                $('.itm').each(function () {
                    pro = $('#pro_id' + this.value).val();
                    pro2 = $('#pro_codigo' + j).val();
                    $('#pro_codigo' + j).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Producto ya ingresado');
                        vl = '';
                        $('#pro_codigo' + j).focus();
                        return false;
                    }
                });

                $.post("actions_industrial_ingresopt.php", {op: 10, id: vl, s: emi},
                function (dt) {
                    dat = dt.split('&');
                    if (dt.trim().length != 0) {
                        $('#pro_codigo' + j).val(dat[1]);
                        $('#pro_descripcion' + j).val(dat[2]);
                        $('#pro_id' + j).val(dat[0]);
                        $('#mov_cantidad' + j).val('');
                        $('#pro_tbl' + j).val(dat[7]);
                        $('#pro_uni' + j).val(dat[9]);
                        if (dat[6] == '') {
                            $('#inventario' + j).val('0');
                        } else {
                            $('#inventario' + j).val(parseFloat(dat[6]).toFixed(dc));
                        }
                        $('#mov_cantidad' + j).focus();
                        if (dat[10] == '') {
                            dat[10] = '0';
                        } else {
                            dat[10] = dat[10];
                        }
                        if ($('#trs_id').val().substring(0, 1) == 1) {
                            $('#mov_cost_unit' + j).val(parseFloat(dat[10]).toFixed(dec));
                        }
                    } else {
                        $('#pro_codigo' + j).val('');
                        $('#pro_descripcion' + j).val('');
                        $('#pro_id' + j).val('');
                        $('#mov_cantidad' + j).val(0);
                        $('#pro_tbl' + j).val('');
                        $('#inventario' + j).val('0');
                    }
                });

                total();
            }

            function inventario(obj) {
                n = obj.lang;
                if ($('#trs_id').val().substring(0, 1) == 1) {
                    if (parseFloat($('#inventario' + n).val()) < parseFloat($(obj).val())) {
                        alert('NO SE PUEDE REGISTRAR LA CANTIDAD\n ES MAYOR QUE EL INVENTARIO');
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                        total();
                    }
                }
            }
            function costo(obj, x) {
                i = obj.lang;
                can = $('#mov_cantidad' + i).val();
                uni = $('#mov_cost_unit' + i).val() * 1;
                tot = $('#mov_cost_tot' + i).val();
                if (x == 1) {
                    t = parseFloat(can) * parseFloat(uni);
                    $('#mov_cost_tot' + i).val(t.toFixed(dec));
                    $('#lblmov_cost_tot' + i).html(t.toFixed(6));
                    valores_lbls(1, t, i);
                } else {
                    if (can != 0) {
                        t = parseFloat(tot) / parseFloat(can);
                    } else {
                        t = 0;
                    }
                    $('#mov_cost_unit' + i).val(t.toFixed(dec));
                    $('#lblmov_cost_unit' + i).html(t.toFixed(6));
                    valores_lbls(2, t, i);
                }
            }

            function valores_lbls(x, v, i) {
                uni = parseFloat($('#mov_cost_unit' + i).val());
                tot = parseFloat($('#mov_cost_tot' + i).val());
                if (x == 1) {
                    $('#lblmov_cost_unit' + i).html(uni.toFixed(dec));
                    $('#lblmov_cost_tot' + i).html(v.toFixed(6));
                } else {
                    $('#lblmov_cost_unit' + i).html(v.toFixed(6));
                    $('#lblmov_cost_tot' + i).html(tot.toFixed(6));
                }
            }

             function imprimir() {
                $('#head_frm').hide();
                $('.botones').hide();
                window.print();
                $('#head_frm').show();
                $('.botones').show();
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

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>

                    <tr>
                        <th colspan="7" ><?PHP echo 'MOVIMIENTO DE PRODUCTO TERMINADO ' . $bodega ?>
                            <font class="cerrar botones"  onclick="cancelar()" title="Salir del Formulario" >&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Documento No:</td>
                                <td>
                                    <input type="text" size="20"  id="mov_documento" readonly value="<?php echo $secuencial?>"  />
                                    <input type="hidden"   id="emisor" readonly value="<?php echo $emisor ?>"  />
                                </td>
                                <td>Fecha de Ingreso:</td>
                                <td>
                                    <input type="text" size="20" name="fecha1" id="mov_fecha_trans" value="<?php echo $rst['mov_fecha_trans'] ?>"/>
                                    <img src="../img/calendar.png" id="im-mov_fecha_trans"/>
                                </td>
                                <td>Guia de Recepcion:</td>
                                <td><input type="text" size="20"  id="mov_guia_transporte" value="<?php echo $rst['mov_guia_transporte'] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>Transaccion:</td>

                                <td> <select id="trs_id" style="width:130px; " onchange="seleccion()">
                                        <option value="0">Seleccione</option>
                                        <?php
                                        while ($rst_tran = pg_fetch_array($cns_trans)) {
                                            echo "<option value=$rst_tran[trs_operacion]$rst_tran[trs_id]>$rst_tran[trs_descripcion]</option>";
                                        }
                                        ?>  
                                    </select>
                                </td>
                                <td>Proveedor:</td>
                                <td><input type="text" size="30" id="cli_nombre" onblur="cliente(this)" value="<?php echo $rst['cli_raz_social'] ?>" list="lista_proveedor"/>
                                    <input type ="text" size="20"  id="cli_id"  value="" hidden=""/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="dinamica">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Unidad</th>
                            <th>Inventario</th>
                            <th>Cantidad</th>
                            <th>Cost. Unit</th>
                            <th>Cost. Tot</th>
                            <th>Acciones</th>
                            </thead>

                </tr>
                <tr>
                    <td><input type="text" size="10" class="itm" id="item1" name="item1" readonly value="1" lang="1"/>
                        <input type ="hidden" size="20"  id="pro_id1"  value="" lang="1" />
                        <input type ="hidden" size="20"  id="pro_tbl1"  value="" lang="1" />
                    </td>
                    <td><input type="text" size="30" id="pro_codigo1"  value="" lang="1"   maxlength="13" onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos" onkeypress="caracter(event, this, 0), frm_save.lang = 2"/> </td>
                    <td><input type="text" size="50"  id="pro_descripcion1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                    <td><input type ="text" size="7"  id="pro_uni1"  value="" lang="1" readonly /></td>
                    <td><input type ="text" size="7"  id="inventario1"  value="" lang="1" readonly /></td>
                    <td><input type="text" size="10" id="mov_cantidad1"  value="" lang="1" class="cnt" onblur="inventario(this), costo(this, 1), total()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                    <td><input type="text" size="10" id="mov_cost_unit1" value="" lang="1" class="cnt" onblur="costo(this, 1), total()"onkeyup="total()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                        <label hidden id="lblmov_cost_unit1" lang="1"></label></td>
                    <td><input type="text" size="10" id="mov_cost_tot1" value="" lang="1" class="cnt" onblur="costo(this, 2), total()" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                        <label hidden id="lblmov_cost_tot1" lang="1"></label></td>
                    <td onclick="elimina_fila(this)" ><img class="auxBtn botones" src="../img/b_delete.png" /></td>
                </tr>
                <tfoot>
                    <tr >
                        <td>
                            <?PHP
                            if ($x != 1) {
                                ?> 
                                <button id="add_row" class="add botones">+</button>
                                <?PHP
                            }
                            ?>
                        </td>
                        <td colspan="4" align="right">Total:</td>
                        <td align="right" style="font-size:15px; " id="total"><?php echo number_format($total, $dec) ?></td>
                        <td align="right" style="font-size:15px; " id="tu"><?php echo number_format($tu, $dec) ?></td>
                        <td align="right" style="font-size:15px; " id="tt"><?php echo number_format($tt, $dec) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
</table>
</form>
<?PHP
if ($x != 1) {
    ?> 
    <button id="guardar" onclick="save(<?php echo $id ?>, 0)" class="botones">Guardar</button>   
    <button id="factura" onclick="save(<?php echo $id ?>, 1)" class="botones">Factura</button>
    <?PHP
}
?>
<button id="cancelar" class="botones">Cancelar</button>   
</body>
</html>

<datalist id="lista_proveedor">
    <?php
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        echo "<option value='$rst_pro[cli_id]' >$rst_pro[nombres]</option>";
    }
    ?>
</datalist>
<datalist id="productos">
    <?php
    if ($ctr_inv == 0) {
        $fra1 = '';
    } else {
        $fra1 = "and m.cod_punto_emision=$emisor";
    }
    $cns_pro = $Clase_industrial_ingresopt->lista_producto_total($inv5, $fra1);
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        ?>
        <option value="<?php echo $rst_pro[id] ?>" label="<?php echo $rst_pro[mp_c] . ' ' . $rst_pro[mp_d] ?>" />
        <?php
    }
    ?>
</datalist>

<script>
    var emp_id = '<?php echo $rst[emp_id] ?>';
    $('#emp_id').val(emp_id);
</script>