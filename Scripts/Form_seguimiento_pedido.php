<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_seguimiento_pedido.php';
$Clase_seguimiento_pedido = new Clase_seguimiento_pedido();
$id = $_GET[id];
if (isset($_GET[id])) {
    $cns = $Clase_seguimiento_pedido->lista_seguimiento_pedido_orden($id);
    $cns1 = $Clase_seguimiento_pedido->lista_seguimiento_pedido_orden($id);
    $rst_head = pg_fetch_array($cns);
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
            idp = '<?php echo $id ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
            });

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
            }

            function acc(a, id) {
                obj = document.getElementById('ing' + id);
                if (a == 1) {
                    obj.readOnly = false;
                } else {
                    $.ajax({
                        beforeSend: function () {
                            loading('visible');
                        },
                        type: 'POST',
                        url: 'actions_seguimiento.php',
                        data: {op: 0, data: obj.value, id: id,ems:emisor.value},
                        success: function (dt) {
                            loading('hidden');
                            if (dt == 0) {
                                window.location = 'Form_seguimiento_pedido.php?id=' + idp;
                            } else {
                                alert(dt);
                            }
                        }
                    })

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
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr>
                        <th colspan="12" ><?PHP echo 'SEGUIMIENTO DE PEDIDO BODEGA ' . $bodega ?>
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="2">ORDEN No:</td>
                    <td colspan="2">
                        <input type="text" size="20"  id="mov_documento" readonly value="<?php echo $rst_head[seg_orden] ?>"  />
                        <input type="hidden"   id="emisor" readonly value="<?php echo $id_cli ?>"  />
                    </td>
                    <td>GUIA DE TRANSPORTE:</td>
                    <td colspan="2"><input type="text" size="20"  id="mov_guia_transporte" value="<?php echo $rst_head[seg_guia] ?>" readonly /></td>
                    <td>FECHA ENTREGA:</td>
                    <td colspan="2">
                        <input type="text" size="20" name="fecha1" id="mov_fecha_trans" value="<?php echo $rst_head[seg_fecha] ?>" readonly/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">TRASNPORTISTA:</td>
                    <td colspan="10"><input type="text" size="42"  id="ped_transportista" value="<?php echo $rst_head[seg_transportista] ?>" readonly /></td>
                </tr>
                <thead>
                    <tr>
                        <th colspan="5">Pedido del Producto</th>
                        <th>Solicitado</th>
                        <th>Entregado</th>
                        <th>Saldo</th>
                        <th>Ingreso</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Item</th>                        
                        <th>Orden</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 0;
                    while ($rst = pg_fetch_array($cns1)) {
                        $n++;
                        if ($rst[pro_tipo] == 0) {
                            $rst_prod = pg_fetch_array($Clase_seguimiento_pedido->lista_producto_comercial($rst[pro_id]));
                            $codigo = $rst_prod[pro_a];
                            $descripcion = $rst_prod[pro_b];
                            $unidad = $rst_prod[pro_c];
                        } else {
                            $rst_prod = pg_fetch_array($Clase_seguimiento_pedido->lista_producto_industrial($rst[pro_id]));
                            $codigo = $rst_prod[pro_codigo];
                            $descripcion = $rst_prod[pro_descripcion];
                            $unidad = $rst_prod[pro_uni];
                        }
                        $ent = $rst[seg_cantidad_recibida];
                        $sal = $rst[seg_cantidad] - $ent;
                        ?>
                        <tr>
                            <td align="right"><?php echo $n ?></td>
                            <td><?php echo $rst[seg_aux_orden] ?></td>
                            <td><?php echo $codigo ?></td>
                            <td><?php echo $descripcion ?></td>
                            <td><?php echo $unidad ?></td>
                            <td><input id="<?php echo 'sol' . $rst[seg_id] ?>" type="text" size="10" value="<?php echo $rst[seg_cantidad] ?>" style="text-align:right" readonly /></td>
                            <td><input id="<?php echo 'ent' . $rst[seg_id] ?>" type="text" size="10" value="<?php echo $ent ?>" style="text-align:right" readonly /></td>
                            <td><input id="<?php echo 'sal' . $rst[seg_id] ?>" type="text" size="10" value="<?php echo $sal ?>" style="text-align:right" readonly /></td>
                            <td><input id="<?php echo 'ing' . $rst[seg_id] ?>" type="text" size="10" value="<?php echo $sal ?>" style="text-align:right" readonly /></td>
                            <td>
                                <img src="../img/del.png" width="16px"  class="auxBtn" onclick="acc(1,<?php echo $rst[seg_id] ?>)" />
                                <img src="../img/chek.png" width="16px" class="auxBtn" onclick="acc(0,<?php echo $rst[seg_id] ?>)" />                                
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </form>
        <button id="guardar">Aceptar</button>   
        <button id="cancelar" >Cancelar</button>   
    </body>
</html>
