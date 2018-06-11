<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClaseOrden.php';
$ClaseOrden = new ClaseOrden();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $n = 0;
            if ($ClaseOrden->insert_orden($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $rst_ord = pg_fetch_array($ClaseOrden->lista_una_orden($data[0]));
                $rst_reg = pg_fetch_array($ClaseOrden->lista_ultimo_registro());
                while ($n < count($data1)) {
                    $dt = explode('&', $data1[$n]);
                    if ($dt[0] == 'undefined') {
                        $dt[0] = 0;
                    }
                    if ($dt[1] == 'undefined') {
                        $dt[1] = 0;
                    }
                    if ($dt[2] == 'undefined') {
                        $dt[2] = 0;
                    }
                    if ($dt[3] == 'undefined') {
                        $dt[3] = 0;
                    }

                    $dat = Array($rst_reg[reg_id],
                        $rst_ord[pro_id],
                        $dt[0],
                        $rst_ord[ord_pro_secundario],
                        $dt[1],
                        $dt[2],
                        $dt[3]);

                    if ($ClaseOrden->insert_detalle($dat) == FALSE) {
                        $sms = pg_last_error();
                    }

                    $n++;
                }
            }
        } else {
            if ($ClaseOrden->upd_orden($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                if ($ClaseOrden->delete_detalle($id) == false) {
                    $sms = pg_last_error();
                } else {
                    $rst_ord = pg_fetch_array($ClaseOrden->lista_una_orden($data[0]));
                    $n = 0;
                    while ($n < count($data1)) {
                        $dt = explode('&', $data1[$n]);
                        if ($dt[0] == 'undefined') {
                            $dt[0] = 0;
                        }
                        if ($dt[1] == 'undefined') {
                            $dt[1] = 0;
                        }
                        if ($dt[2] == 'undefined') {
                            $dt[2] = 0;
                        }
                        if ($dt[3] == 'undefined') {
                            $dt[3] = 0;
                        }

                        $dat = Array($id,
                            $rst_ord[pro_id],
                            $dt[0],
                            $rst_ord[ord_pro_secundario],
                            $dt[1],
                            $dt[2],
                            $dt[3]);

                        if ($ClaseOrden->insert_detalle($dat) == FALSE) {
                            $sms = pg_last_error();
                        }
                        $n++;
                    }
                }
            }
        }
        echo $sms;
        break;

    case 1:
        $sms = 0;
        if ($ClaseOrden->delete_detalle($id) == false) {
            $sms = pg_last_error();
        } else {
            if ($ClaseOrden->delete_orden($id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($ClaseOrden->lista_una_orden_codigo($id));
        $filas = pg_fetch_row($ClaseOrden->lista_una_orden_codigo($id));
        if ($filas == 0) {
            echo '0';
        } else {
            $retorno = $rst[pro_descripcion];
            $ancho = $rst[ord_rep_ancho];
            $carril1 = $rst[ord_pri_carril];
            $carril2 = $rst[ord_sec_carril];
            if ($carril1 > $carril2) {
                $ncarril = $carril1;
            } else {
                $ncarril = $carril2;
            }

            if ($ncarril != 0) {
                $i = 0;
                while ($i < $ncarril) {
                    $matriz.='<tr>
                             <td class="input">PESO</td>';
                    $res1 = $ncarril - $carril1;
                    $esp="''";
                    if ($res1 != 0) {
                        if ($i < $carril1) {
                            $matriz.='<td><input  type="text" class="text" size="12"   id="reg_peso' . $i . '" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
                        } else {
                            $matriz.='<td></td>';
                        }
                    } else {
                        $matriz.='<td><input type="text" class="text" size="12"  id="reg_peso' . $i . '" value="0" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
                    }
                    if ($i < $carril2) {
                        $matriz.='<td><input type="text" class="text" size="12" id="reg_peso_secundario' . $i . '" value="0" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
                    }

                    if ($i == 0) {
                        $matriz.='<td><input type="text" class="text" size="12"  id="reg_peso_reproceso' . $i . '" value="0" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>
                    <td><input type="text" class="text" size="12"  id="reg_peso_refilado' . $i . '" value="0" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
                    }
                    $matriz.='</tr>';
                    $i++;
                }
            }
            echo $retorno . '&' . $ancho . '&' . $matriz . '&' . $rst[ord_id];
        }
        
        break;
    case 3:
        $cns = $ClaseOrden->lista_resgistro_id($id);
        $i = 0;
        $esp="''";
        while ($rst = pg_fetch_array($cns)) {
            $p = $rst[reg_peso];
            $ps = $rst[reg_peso_secundario];
            $pr = $rst[reg_peso_reproceso];
            $pf = $rst[reg_peso_refilado];
            $matriz.='<tr>
                              <td class="input">PESO</td>';
            if ($p != 0.00) {
                $matriz.='
                              <td><input  type="text" class="text" size="12" id="reg_peso' . $i . '" value="' . $p . '" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
            } else {
                $matriz.='<td></td>';
            }
            if ($ps != 0.00) {
                $matriz.='
            
                              <td><input  type="text" class="text" size="12" id="reg_peso_secundario' . $i . '" value="' . $ps . '" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
            } else {
                $matriz.='<td></td > ';
            }
            if ($pr != 0.00) {
                $matriz.='
            <td><input type = "text" class = "text" size = "12" id="reg_peso_reproceso' . $i . '" value = "' . $pr . '" value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');"/>KG</td>';
            } else {
                $matriz.='<td></td > ';
            }
            if ($pf != 0.00) {
                $matriz.='
            <td><input type = "text" class = "text" size = "12" id="reg_peso_refilado' . $i . '" value = "' . $pf . '"/ value="0" onkeyup="this.value=this.value.replace(/[^0-9.]/,'.$esp.');">KG</td>';
            } else {
                $matriz.='<td></td > ';
            }
            $matriz.='</tr>';
            $i++;
        }

        echo $matriz;
        break;
}
?>
