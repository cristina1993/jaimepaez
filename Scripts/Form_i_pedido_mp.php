<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id_edita = $_GET[id];
$btn = "";
$btn0 = "hidden";
if (isset($_GET[orden])) {
    $code = $_GET[orden];
    $fecha = $_GET[fecha];
    $clie = $_GET[clie];
    $cns = $Set->lista_pedido_codgo($code);
    $cns_tp = $Set->lista_mp($clie);
} elseif (isset($_GET[x])) {
    $rst = pg_fetch_array($Set->lista_ped_id($_GET[id]));
    $code = $rst[ped_orden];
    $fecha = $rst[ped_fecha];
    $clie = $rst[emp_id];
    $mp_id = $rst[mp_id];
    $desc = $rst[mp_codigo];
    $pres = $rst[mp_presentacion];
    $cant = $rst[ped_det_cant];
    $unidad = $rst[mp_unidad];
    $peso = $rst[ped_det_peso];
    $rst_inv_ing = pg_fetch_array($Set->lista_inv_mp($mp_id, 0));
    $rst_inv_egr = pg_fetch_array($Set->lista_inv_mp($mp_id, 1));
    $invp = number_format($rst_inv_ing[peso] - $rst_inv_egr[peso], 1);
    $invu = number_format($rst_inv_ing[unidad] - $rst_inv_egr[unidad], 1);
    $invpu = ($rst_inv_ing[peso] - $rst_inv_egr[peso]) / ($rst_inv_ing[unidad] - $rst_inv_egr[unidad]);
    $btn0 = "";
    $btn = "hidden";
    $cns_tp = $Set->lista_mp($clie);
} else {
    $fecha = date('Y-m-d');
    $rst_sec = pg_fetch_array($Set->lista_ped_sec());
    $sec = ($rst_sec[ped_orden] + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "00000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "0000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "000";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "00";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "0";
    } elseif ($sec >= 100000 && $sec < 1000000) {
        $tx_trs = "";
    }
    $code = $tx_trs . $sec;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "pmp_fecha", ifFormat: "%Y-%m-%d", button: "im-pmp_fecha"});
            });

            function save(id) {
                p1=(pmp_peso.value * 1);
                p2=(invp.innerHTML * 1);
                if (p1 > p2)
                {
                    alert('El peso del pedido excede el del inventario');
                    invp.style.background = "firebrick";
                } else {
                    invp.style.background = "none";
                    var data = Array(
                            pmp_orden.value,
                            pmp_fecha.value,
                            pmp_clie.value,
                            pmp_ref.value,
                            pmp_cant.value,
                            pmp_peso.value)
                    $.post("actions.php", {act: 25, 'data[]': data, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_pedido_mp.php?fecha=" + pmp_fecha.value + "&orden=" + pmp_orden.value + "&clie=" + pmp_clie.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function edita(id_edita){
                
                if ((pmp_peso.value * 1) > (invp.innerHTML * 1))
                {
                    alert('El peso del pedido excede el del inventario');
                    invp.style.background = "firebrick";
                } else {
                    invp.style.background = "none";
                    var data = Array(
                            pmp_orden.value,
                            pmp_fecha.value,
                            pmp_clie.value,
                            pmp_ref.value,
                            pmp_cant.value,
                            pmp_peso.value)
                    $.post("actions.php", {act: 25, 'data[]': data, id: id_edita},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                }

            }
            function cancelar(){
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,50%";
            }
            
            function mp_ref(mp)
            {
                $.post("actions.php", {act: 26, mp: mp},
                function (dt) {
                    det = dt.split('&');
                    
                    if(det[5].length==0 || det[5]==0){
                       det[5]=1; 
                    }
                    
                    pmp_desc.innerHTML = det[0];
                    invp.innerHTML = det[1];
                    invu.innerHTML = det[4];
                    invpu.value = det[5];
                    mp_unidad.innerHTML = det[2];
                    pmp_pres.innerHTML = det[3];
                    pmp_cant.focus();
                });

            }
            function combo()
            {
                if (pmp_fecha.value.length == 0) {
                    alert('Fecha es campo obligatorio');
                } else if (pmp_orden.value.length == 0) {
                    alert('Orden es campo obligatorio');
                } else if (pmp_clie.value == 0) {
                    alert('Elija un Cliente');
                } else {
                    window.location = "Form_i_pedido_mp.php?fecha=" + pmp_fecha.value + "&orden=" + pmp_orden.value + "&clie=" + pmp_clie.value;
                }

            }
            function calculo(cnt, pu) {
                pmp_peso.value = (cnt * pu).toFixed(1);
            }
        </script>
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="10" >
                        PEDIDO DE MATERIA PRIMA
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>                            
                    </th></tr>
            </thead>                    
            <tr>
                <td>Orden:</td>
                <td><input type="text" name="pmp_orden" id="pmp_orden" readonly style="background:#ccc;" size="" value="<?php echo $code; ?>" /></td>
                <td>Fecha:</td>
                <td colspan="2">
                    <input readonly type="text" name="pmp_fecha" id="pmp_fecha" size="8" value="<?php echo $fecha; ?>">
                    <img id='im-pmp_fecha' src='../img/calendar.png'  />
                </td>
                <td>Cliente:</td>
                <td colspan="4">
                    <select name="pmp_clie" id="pmp_clie" onchange="combo()" >
                        <option value="0">Seleccione</option>
                        <?php
                        $cns_fbc = $Set->lista_fabricas();
                        while ($rst_fbc = pg_fetch_array($cns_fbc)) {
                            if ($rst_fbc[emp_id] == $clie) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }
                            echo "<option $sel value='$rst_fbc[emp_id]'>$rst_fbc[emp_descripcion]</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="4">Materia Prima</th>
                    <th colspan="2">Inventario</th>
                    <th colspan="2">Solicitado</th>
                    <th></th>
                </tr>
                <tr>
                    <th>Ítem</th>
                    <th>Descripción</th>
                    <th>Referencia</th>                        
                    <th>Presentacion</th>
                    <th>Unidad</th>                                                                        
                    <th width="80px">Cantidad</th>                        
                    <th width="80px">Peso</th>                        
                    <th width="80px">Cantidad</th>                        
                    <th width="80px">Peso</th>
                    <th>Acciones</th>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <select name="pmp_ref" id="pmp_ref" style="width:200px" onchange="mp_ref(this.value)">
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_tp = pg_fetch_array($cns_tp)) {
                                if ($rst_tp[mp_id] == $mp_id) {
                                    $slc = "selected";
                                } else {
                                    $slc = "";
                                }
                                echo "<option $slc value='$rst_tp[mp_id]'>$rst_tp[mp_referencia]</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td id='pmp_desc'><?php echo $desc; ?></td>
                    <td id='pmp_pres'><?php echo $pres; ?></td>
                    <td id="mp_unidad"><?php echo $unidad; ?></td>                                                                                                
                    <td id='invu' style="font-size:14px;font-weight:bolder " align="right"><?php echo $invu; ?></td>                        
                    <td id='invp' style="font-size:14px;font-weight:bolder " align="right"><?php echo $invp; ?></td>
                    <td align="center"><input type="text" name="pmp_cant" id="pmp_cant" size="5" value="<?php echo $cant; ?>" onchange="calculo(pmp_cant.value, invpu.value)"/></td>
                    <td align="center">
                        <input type="hidden"  id="invpu" size="5" value="<?php echo $invpu; ?>" />
                        <input type="text"  readonly style="background:#ccc;text-align:right" id="pmp_peso" size="5" value="<?php echo $peso; ?>" />
                    </td>
                    <td>
                        <button id="save" <?php echo $btn ?> onclick="save(<?php echo $id ?>)">+</button>
                    </td>
                </tr>
            </thead>                    
            <tbody class="tbl_frm_aux" >                 
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>    
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_presentacion] ?></td>                            
                        <td><?php echo $rst[mp_unidad] ?></td>                            
                        <td></td>
                        <td></td>
                        <td align="right" ><?php echo number_format($rst[ped_det_cant], 1) ?></td>
                        <td align="right" ><?php echo number_format($rst[ped_det_peso], 1) ?></td>
                        <td></td>
                    </tr>                    
                    <?php
                }
                ?>
                <tr>
                    <td colspan="10" align="left">
                        <button id="save0" <?php echo $btn0 ?> onclick="edita(<?php echo $id_edita ?>)">Guardar</button>
                        <button id="cancel" <?php echo $btn ?> onclick="cancelar()">Guardar</button>
                        <button id="cancel" onclick="cancelar()">Cancelar</button>
                    </td>
                </tr>                    
            </tbody>                                           
        </table>
    </body>
</html>
