<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_pedidospt.php';
$Clase_pedidospt = new Clase_pedidospt();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_pedidospt->lista_un_pedido_ind($id));
    $cns = $Clase_pedidospt->lista_ingreso_pedido_documento($rst['ped_documento']);
    if (empty($rst)) {
        $rst = pg_fetch_array($Clase_pedidospt->lista_un_pedido_nop($id));
        $cns = $Clase_pedidospt->lista_ingreso_pedido_documento_nop($rst['ped_documento']);
    }
    $fila = pg_numrows($cns);
    $cliente=trim($rst['cli_apellidos'] . ' ' . $rst['cli_nombres'] . ' ' . $rst['cli_raz_social']);
    $cli_id=$rst[cli_id];
} else {
    $id = 0;
    $rst['mov_fecha_trans'] = date('Y-m-d');
    $fila = 0;
    $rst_cli=  pg_fetch_array($Clase_pedidospt->lista_un_proveedor_codigo($cod_cli));
    $cliente=$rst_cli[nombres];
$cli_id=$rst_cli[cli_id];
}
//$cns_pro = $Clase_pedidospt->lista_proveedor(0);
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
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("id");
                    var i = a.substring(4, 5);
                    if ($('#pro_descripcion' + i).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });

                Calendar.setup({inputField: "ped_fecha_registro", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                $('#ped_fecha_registro').val('<?php echo date('Y-m-d'); ?>');
                if (id == 0) {
                    sec_automatico();
                }
            });

            function save(id) {
                var data = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                var a = tr.find("input").attr("id");
                var i = a.substring(4, 5);
                while (n < i) {
                    n++;
                    pro_id = $('#pro_id' + n).val();
                    can = $('#ped_cantidad' + n).val();
                    bod_id = $('#bod_id').val();
                    data.push(pro_id + '&' +
                            cli_id.value + '&' +
                            bod_id + '&' +
                            ped_documento.value + '&' +
                            ped_fecha_registro.value + '&' +
                            can
                            );
                }

                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        doc = document.getElementsByClassName('itm');

                        n = 0;
                        if (ped_fecha_registro.value.length == 0) {
                            $("#ped_fecha_registro").css({borderColor: "red"});
                            $("#ped_fecha_registro").focus();
                            return false;
                        }
                        else if (cli_nombre.value.length == 0) {
                            $("#cli_nombre").css({borderColor: "red"});
                            $("#cli_nombre").focus();
                            return false;
                        }
                        else if (doc.length != 0) {
                            while (n < doc.length) {
                                n++;
                                if ($('#pro_descripcion' + n).val() == 0) {
                                    $('#pro_descripcion' + n).css({borderColor: "red"});
                                    $('#pro_descripcion' + n).focus();
                                    return false;
                                }
                                if ($('#ped_cantidad' + n).val() == 0) {
                                    $('#ped_cantidad' + n).css({borderColor: "red"});
                                    $('#ped_cantidad' + n).focus();
                                    return false;
                                }
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actions_pedidospt.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
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
            }
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    //this.lang = x;
                    if (parts[1] == 'cantidad') {
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
            function sec_automatico() {
                $.post("actions_pedidospt.php", {op: 2}, function (dt) {
                    ped_documento.value = dt;
                })
            }
            function cliente(obj) {
                $.post("actions_pedidospt.php", {op: 3, id: obj.value}, function (dt) {
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
            function lista_prod(obj) {
                id = obj.value;
                $.post('actions_pedidospt.php', {op: 4, id: id}, function (dt) {
                    $('#lista_prod').html(dt);
                })
            }
            function datos(obj) {
                $('#frm_save').attr('lang', 1);
                id = obj.value;
                n = obj.lang;
                $("#pro_codigo" + n).val(id);
                var p = obj.id.match(/(\D+)(\d+)$/);
                $('#ped_cantidad' + p[2]).focus();
                $.post('actions_pedidospt.php', {op: 5, id: id, bd: $('#bod_id').val()}, function (dt) {
                    dat = dt.split('&');
                    if (dat[1] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        $('#pro_id' + n).val(dat[0]);
                        $('#pro_codigo' + n).val(dat[1]);
                        $('#pro_descripcion' + n).val(dat[2]);
                        $('#pro_uni' + n).val(dat[3]);
                        $('#pro_uni2' + n).val(dat[4]);
                        $('#mov_cantidad' + n).val(dat[5]);

                    }
                })
            }

            function comparar(obj) {
                f = obj.lang;
                if ($("#ped_cantidad" + f).val() * 1 > $("#mov_cantidad" + f).val() * 1) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});

                    alert('La Cantidad es mayor al Inventario');
                }
            }

            function desactivar() {
                if ($("#bod_id").val() != 0) {
                    $("#bod_id").attr('disabled', true)
                }
            }
            function total() {
                doc = document.getElementsByClassName('itm');
                n = 0;
                sum = 0;
                while (n < doc.length) {
                    n++;
                    if ($('#ped_cantidad' + n).val().length == 0) {
                        can = 0;
                    } else {
                        can = $('#ped_cantidad' + n).val()
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
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 150px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form"  >
                <thead>
                    <tr><th colspan="7" >PEDIDO DE PRODUCTO TERMINADO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <!--<tbody>-->
                <tr>
                    <td>Orden:</td>
                    <td>
                        <input type="text" size="20"  id="ped_documento" readonly value="<?php echo $rst['ped_documento'] ?>"  />
                    </td>
                    <td align="right">Bodega:</td>
                    <td>
                        <select id="bod_id" onchange="lista_prod(this)" onblur="desactivar()">
                            <option value="0">Seleccione</option>
                            <option value="1">Noperti</option>
                            <option value="10">Industrial</option>
                            <option value="2">Condado</option>
                            <option value="3">Quicentro Sur</option>
                            <option value="4">Mall del Sol</option>
                            <option value="5">Shopping Machala</option>
                            <option value="6">Riocentro Norte</option>
                            <option value="7">San Marino Shopping</option>
                            <option value="8">City Mall</option>
                            <option value="9">Quicentro Shopping</option>
                        </select>
                    </td>
                    <td align="right">Fecha de Ingreso:</td>
                    <td><input type="text" size="20" name="ped_fecha_registro" id="ped_fecha_registro" value="<?php echo $rst['ped_fecha_registro'] ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/></td>
                </tr>
                <tr>
                    <td>Cliente:</td>
                    <td  colspan="2">
                        <?php
                        if($emisor >= 1){
                        ?>
                        <input type="text" size="40" id="cli_nombre" onblur="cliente(this)" readonly value="<?php echo $cliente ?>" list="lista_cliente"/>
                        <input type ="hidden" size="20"  id="cli_id"  value="<?php echo $cli_id?>"  />
                        <?php
                        }else{
                        ?>
                         <input type="text" size="40" id="cli_nombre" onblur="cliente(this)" value="<?php echo $cliente ?>" list="lista_cliente"/>
                        <input type ="hidden" size="20"  id="cli_id"  value="<?php echo $cli_id?>"  />
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr id="head">
                <thead id="tabla">
                <th>Item</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Unidad</th>
                <th>Inventario</th>
                <th>Cantidad</th>
                <th>Acciones</th>
                </thead>
                <?php
                if ($fila == "0") {
                    ?>
                    <tr>
                        <td align="right"><input type ="text" size="8"  class="itm" id="item1"  readonly value="1"/>
                            <input type ="text" size="20"  id="pro_id1"  value="" lang="1" hidden/>
                        </td>
                        <td><input type ="text" size="20"  id="pro_codigo1"  value="<?php echo $rst1['pro_codigo'] ?>" lang="1" readonly/></td>
                        <td><input type="text" size="30" id="pro_descripcion1" value="<?php echo $rst1['pro_descripcion'] ?>" lang="1" onchange="datos(this)" list="lista_prod"/></td>
                        <td><input type ="text" size="10"  id="pro_uni1"  value="<?php echo $rst1['pro_uni'] ?>" lang="1" readonly/></td>
                        <td style="display:none"><input type ="text" size="20"  id="pro_uni21"  value="<?php echo $rst1['pro_uni2'] ?>" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="mov_cantidad1"  value="" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="ped_cantidad1"  onblur="comparar(this)" value="" lang="1" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()"/></td>
                        <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                    </tr>
                    <?PHP
                } else {
                    $n = 0;
                    $suma = 0;
                    while ($rst1 = pg_fetch_array($cns)) {
                        $n++;
                        if ($rst[bod_id] == 1) {
                            $rst1['pro_id'] = $rst1['id'];
                            $rst1['pro_codigo'] = $rst1['pro_a'];
                            $rst1['pro_descripcion'] = $rst1['pro_b'];
                            $fl = explode('&', $rst1['pro_tipo']);
                            $fml = $fl[9];
                        }
                        ?>
                        <tr>
                            <td align="right">
                                <input type ="text" size="8"  id="item1"  readonly value="<?PHP echo $n ?>"/>
                                <input type ="text" size="20"  id="pro_id1"  value="<?php echo $rst1['pro_id'] ?>" lang="1" />
                            </td>
                            <td><input type ="text" size="20"  id="pro_codigo1"  value="<?php echo $rst1['pro_codigo'] ?>" lang="1" readonly/></td>
                            <td><input type="text" size="30" id="pro_descripcion1" value="<?php echo $rst1['pro_descripcion'] ?>" lang="1"/></td>
                            <td><input type ="text" size="10"  id="pro_uni1"  value="<?php echo $rst1['pro_uni'] ?>" lang="1" readonly/></td>
                            <td style="display:none"><input type ="text" size="20"  id="pro_uni2"  value="<?php echo $rst1['pro_uni2'] ?>" lang="1" readonly/></td>
                            <td><input type ="text" size="10"  id="mov_cantidad1"  value="<?php echo $rst1['pro_uni2'] ?>" lang="1" readonly/></td>
                            <td align="right"><input type ="text" size="10"  style="text-align:right " id="ped_cantidad1"  value="<?php echo $rst1['ped_cantidad'] ?>" lang="1" onblur="total()"/></td>
                        </tr>
                        <?PHP
                        $suma = $suma + $rst1['ped_cantidad'];
                    }
                }
                ?>
                <tfoot>
                    <tr class="totales">
                        <td><button id="add_row">+</button></td>
                        <td></td>
                        <td colspan="3" align="right">Total:</td>
                        <td align="right" style="font-size:15px; " id="total"><?php echo number_format($suma, 2) ?></td>    
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>    
    </body>
</html>    

<datalist id="lista_prod">
</datalist>
<datalist id="lista_cliente">
    <?php
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        echo "<option value='$rst_pro[cli_id]' >$rst_pro[cli_codigo]  $rst_pro[nombres]</option>";
    }
    ?>
</datalist>
<script>
    var bod_id = '<?php echo $rst[bod_id] ?>';
    $('#bod_id').val(bod_id);
</script>