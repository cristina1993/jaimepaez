<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_inv_fisico.php';
$Clase_inv_fisico = new Clase_inv_fisico();
$cns_loc = $Clase_inv_fisico->lista_bodegas();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $fila = 1;
    $cns = $Clase_inv_fisico->lista_inv_fisico_doc($id);
    $rst1 = pg_fetch_array($Clase_inv_fisico->lista_inv_fisico_doc($id));
    $disabled = 'disabled';
} else {
    $id = 0;
    $fila = 0;
    $cns_bodegas = $Clase_inv_fisico->lista_bodegas();
    $rst1['inv_fec_emison'] = date('Y-m-d');
    $rst1['auditor'] = strtoupper($rst_user[usu_person]);
    $disabled = '';
}
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
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();

                    inv = $('#inv_codigo').val().trim();
                    pro = $('#pro_id').val().trim();
                    can = $('#inv_cantidad').val().trim();
                    vald = $('#validador').val().trim();
                    if (vald == 2) {
                        d = 2;
                    } else {
                        d = 1;
                    }
                    if (inv != '' && pro != '' && can != '' && d == 1) {
                        clonar();
                    }
                });
            });
            function save() {
                var data = Array();
                n = 0;
                i = $('.itm');
                while (n < i.length) {
                    n++;
                    pro_id = $('#pro_id' + n).html();
                    codigo = $('#inv_codigo' + n).html();
                    cantidad = $('#inv_cantidad' + n).html();
                    tbl = $('#pro_tbl' + n).html();
                    data.push(
                            pro_id + '&' +
                            inv_documento.value + '& ' +
                            inv_bodegas.value + '& ' +
                            inv_fecha.value + '& ' +
                            cantidad + '&' +
                            codigo + '&' +
                            inv_auditor.value + '&' +
                            tbl
                            );
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
                        //Validaciones antes de enviar
                        if (inv_bodegas.value == 0) {
                            $('#inv_bodegas').css({borderColor: "red"});
                            $('#inv_bodegas').focus();
                            return false;
                        }
                        if ($('#pro_id1').html() == null) {
                            alert('INGRESE POR LO MENOS UN REGISTRO');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_inv_fisico.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');

                            cancelar();
                        } else {
                            loading('hidden');
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_inv_fisico.php';

            }
            function load_sec_bodegas(obj) {
                $.post('actions_inv_fisico.php', {id: obj.value, op: 2}, function (dt) { // cambiar a actions_productos
                    dat = dt.split('&');
                    $('#inv_documento').val(dat[0]);
                });
            }

//            function caracter(e, obj, x) {
//                j = obj.lang;
//                if (x == 0) {
//                    can = $('#inv_codigo').val();
//                } else {
//                    can = $('#inv_cantidad').val();
//                }
//
//                var ch0 = e.keyCode;
//                var ch1 = e.which;
//                if (can == 'MANUAL') {
//                    $('#validador').val(0);
//                    $('#nom_val').val('MANUAL');
//                    $('#inv_codigo').focus();
//                    $('#inv_cantidad').val('');
//                    $('#pro_tbl').val('');
//                    $('#pro_id').val('');
//                }
//                if (can == 'UNIDAD') {  //Punto (Con lector de Codigo de Barras)
//                    $('#validador').val(1);
//                    $('#nom_val').val('UNIDAD');
//                    $('#inv_codigo').focus();
//                    $('#inv_cantidad').val('1');
//                    $('#pro_tbl').val('');
//                    $('#pro_id').val('');
//                }
//                if (can == 'PAQUETE') {  //Punto (Con lector de Codigo de Barras)
//                    $('#validador').val(2);
//                    $('#nom_val').val('PAQUETE');
//                    $('#inv_cantidad').val('');
//                    $('#pro_tbl').val('');
//                    $('#pro_id').val('');
//                }
//                if (x == 1) {
//                    if ($('#validador').val() == 2 && (can == 'PAQUETE' || can == 'CANCELAR')) {
//                        $('#inv_cantidad').val('');
//                        $('#pro_tbl').val('');
//                        $('#pro_id').val('');
//                        $('#inv_codigo').focus();
//                    } else if ($('#validador').val() == 2) {
//                        a = can.replace('-', '');
//                        $('#inv_cantidad').val(a);
//                        sep = can.split(' ');
//                        if (sep[1] == 'ACEPTAR') {
//                            $('#inv_cantidad').val(sep[0]);
//                            clonar();
//                        } else if (sep[1] == 'CANCELAR') {
//                            $('#inv_cantidad').val('');
//                            $('#inv_cantidad').focus();
//                        }
//                    }
//                }
//
//            }



            function load_id_producto(obj) {
                if (validador.value == 0) {
                    if ($('#inv_codigo').val() == 'MANUAL') {
                        $('#inv_codigo').val('');
                    }
                    v = 0;
                    if (v == 1) {
                        vl = $('#inv_codigo').val();
                        lt = $('#pro_id').val();
                    } else {
                        vl = $('#inv_codigo').val();
                        lt = 0;
                    }
                    $.post("actions_inv_fisico.php", {op: 3, id: vl, lt: lt},
                    function (dt) {
                        if (dt != '') {
                            dat = dt.split('&');
                            $('#pro_id').val(dat[0]);
                            $('#inv_codigo').val(dat[1]);
                            $('#inv_descripcion').val(dat[2]);
                            $('#pro_tbl').val(dat[3]);
                            if (dat[0] != '') {
                                $('#inv_cantidad').focus();
                            }
                        } else {
                            alert('No existe el producto');
                            $('#inv_codigo').val('');
                        }
                    });


                } else if (validador.value == 1) {
                    cod0 = obj.value;
                    cod = cod0.replace("'", "-");
                    $.post("actions_inv_fisico.php", {op: 4, id: cod},
                    function (dt) {
                        dat = dt.split('&');
                        $('#pro_id').val(dat[0]);
                        $('#inv_codigo').val(dat[1]);
                        $('#inv_descripcion').val(dat[2]);
                        $('#pro_tbl').val(dat[3]);
                        $('#inv_cantidad').val(1);
                        total();
                        if (dat[0] != '') {
                            clonar();
                        } else {
                            $('#inv_codigo').val('');
                            $('#inv_descripcion').val('');
                            $('#pro_tbl').val('');
                            $('#pro_id').val('');
                        }


                    });
                }
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
                        if ($('#pro_id' + n).html() == pro_id.value && $('#pro_tbl' + n).html() == pro_tbl.value) {
                            d = 1;
                            cant = parseFloat($('#inv_cantidad' + n).html()) + parseFloat(inv_cantidad.value);
                            $('#inv_cantidad' + n).html(cant)
                        }
                    }
                }
                if (d == 0) {
                    i = j + 1;
                    var fila = '<tr class="itm"><td>' + i + '</td><td id="inv_codigo' + i + '">' + inv_codigo.value + '</td><td  id="pro_id' + i + '" hidden>' + pro_id.value + '</td><td id="pro_tbl' + i + '" hidden>' + pro_tbl.value + '<td id="inv_descripcion' + i + '">' + inv_descripcion.value + '</td><td id="inv_cantidad' + i + '" align="right">' + inv_cantidad.value + '</td><td></td></tr>';
                    $('#lista').append(fila);
                }
                inv_codigo.value = '';
                pro_id.value = '';
                pro_tbl.value = '';
                inv_descripcion.value = '';
                inv_cantidad.value = '';
                $('#inv_codigo').focus();
                total();
            }
            function total() {
                doc = document.getElementsByClassName('itm');
                n = 0;
                sum = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#inv_cantidad' + n).html().length == 0) {
                        can = 0;
                    } else {
                        can = $('#inv_cantidad' + n).html();

                    }
                    sum = sum + parseFloat(can);
                }

                $('#total').html(sum);

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
        <div id="cargando">Por Favor Espere...</div>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <form id="frm_save" lang="0" autocomplete="off" >
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="15">FORMULARIO TOMA DE INVENTARIO FÍSICO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tbody id="encabezado">

                    <tr>
                        <td>BODEGA</td>
                        <td colspan="2">
                            <select id="inv_bodegas" onchange="load_sec_bodegas(this)" <?php echo $disabled ?>> 
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_locales = pg_fetch_array($cns_loc)) {
                                    echo "<option value='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Documento No</td>
                        <td colspan="2"><input type="text" size="15" id="inv_documento" value="<?php echo $rst1['inv_num_documento'] ?>" readonly /></td>
                        <?php
                        if ($fila == 0) {
                            ?>
                            <td>Tipo de Ingreso</td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td>Fecha</td>
                        <td colspan="2"><input type="text" size="15" id="inv_fecha" value="<?php echo $rst1['inv_fec_emison'] ?>" readonly/></td>
                        <?php
                        if ($fila == 0) {
                            ?>
                            <td>
                                <select id="validador">
                                    <option value="0">MANUAL</option>
                                    <option value="1">AUTOMATICO</option>
                                </select>
                            </td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <td><input  type="text" size="12" id="inv_auditor" value="<?php echo $rst1['auditor'] ?>" hidden/></td>
                    </tr>
                </tbody>

                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <?php
                        if ($fila == 1) {
                            ?>
                            <th>Inventario</th>
                            <th>Diferencia</th>
                            <?php
                        } else {
                            ?>
                            <th></th>
                            <?php
                        }
                        ?>
                    </tr>
                </thead>
                <?php
                if ($fila == 0) {
                    ?>
                    <tbody class="tbl_frm_aux" >   
                        <tr>
                            <td></td>
                            <td>
                                <input type ="text" size="25"  id="inv_codigo" value="" lang="1"  onfocus="this.style.width = '400px';" onblur="this.style.width = '100px';" list="productos"  onchange="load_id_producto(this)" onkeypress="caracter(event, this, 0)" />
                                <input type="text" size="10" id="pro_id" hidden/>
                                <input type="text" size="10" id="pro_tbl" hidden/>
                            </td>
                            <td><input type="text" size="35" readonly id="inv_descripcion" readonly /></td>
                            <td><input type="text" size="10" id="inv_cantidad"  onkeypress="caracter(event, this, 1)"/></td>
                            <td><button />+</button></td>
                        </tr>

                    </tbody>
                    <tbody class="tbl_frm_aux" id="lista" >   
                    </tbody>
                    <?php
                } else {
                    ?> 
                    <tbody class="tbl_frm_aux" id="lista" >   
                        <?php
                        $n = 0;
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;
                            $dif = $rst[inv_cantidad] - abs($rst[inv_cant_inventario]);
                            $sum_dif+=$dif;
                            $sum+=$rst[inv_cant_inventario];
                            $sum_cant+=$rst[inv_cantidad];
                            ?> 
                            <tr class="itm">
                                <td><?php echo $n ?></td>
                                <td id="inv_codigo<?php echo $n ?>"><?php echo $rst[mp_c] ?></td>
                                <td  id="pro_id<?php echo $n ?>" hidden><?php echo $rst[pro_id] ?></td>
                                <td id="pro_tbl<?php echo $n ?>" hidden><?php echo '0' ?></td>
                                <td id="inv_descripcion<?php echo $n ?>"><?php echo $rst[mp_d] ?></td>
                                <td id="inv_cantidad<?php echo $n ?>" align="right"><?php echo $rst[inv_cantidad] ?></td>
                                <td id="inv_inventario<?php echo $n ?>" align="right"><?php echo $rst[inv_cant_inventario] ?></td>
                                <td id="diferencia<?php echo $n ?>" align="right"><?php echo $dif ?></td>
                            </tr>
                            <?php
                        }
                        ?> 
                    </tbody> 
                    <?php
                }
                ?>
                <tr class="add">
                    <td></td>
                    <td></td>
                    <td align="right" >TOTAL</td>
                    <td align="right" style="font-size:14px; " id="total"><?php echo $sum_cant ?></td>

                    <?php
                    if ($fila == 1) {
                        ?>
                        <td align="right" style="font-size:14px; " id="total2"><?php echo $sum ?></td>
                        <td align="right" style="font-size:14px; " id="tdif"><?php echo $sum_dif ?></td>
                        <?php
                    } else {
                        ?>
                        <td></td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
            </table>
        </form>

        <table>
            <td colspan="3">
                <?php
                if ($Prt->add == 0 || $Prt->edition == 0) {
                    if ($fila == 0) {
                        ?>
                        <button id="save" onclick="save('<?php echo $emisor ?>', '<?php echo $id_cli ?>')">Guardar</button>
                        <?php
                    }
                }
                ?>
                <button id="cancel" onclick="cancelar()">Cancelar</button>
            </td>
        </tr> 
    </table>
    <script>
        bod =<?php echo $rst1[inv_bodega] ?>;
        $('#inv_bodegas').val(bod);
    </script>
    <datalist id="productos">
        <?php
        $cns_pro = $Clase_inv_fisico->lista_productos();
        $n = 0;
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            $n++;
            ?>
            <option value="<?php echo $rst_pro[id] ?>" label="<?php echo $rst_pro[mp_c] . ' ' . $rst_pro[mp_d] ?>" />
            <?php
        }
        ?>
    </datalist>
