<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_notacredito_nuevo.php';
$Clase_nota_Credito_nuevo = new Clase_nota_Credito_nuevo();
$Adt = new Auditoria();
$act = $_REQUEST[act]; //Accion
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$x = $_REQUEST[x];
$c = $_REQUEST[c];
$ctr_inv = $_REQUEST[ctr_inv];
$fields = $_REQUEST[fields];
$emi = $_REQUEST[emi];
switch ($act) {
    case 0:
        $sms = 0;
        $aud = 0;
        $rst_sec = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_notac_num($data[2]));
        $ven_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '7'));
        $cli_nac_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '8'));
        $cli_ext_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '9'));
        $iva_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '10'));
        $ice_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '73'));
        $irb_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '74'));
        $prop_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '75'));
        $desc_ctas = pg_fetch_array($Clase_nota_Credito_nuevo->lista_asientos_ctas($data[1], '84'));
        if ($cli_nac_ctas[pln_id] == '' || $cli_ext_ctas[pln_id] == '' || $ven_ctas[pln_id] == '' || $iva_ctas[pln_id] == '' || $desc_ctas[pln_id] == '' || $ice_ctas[pln_id] == '' || $irb_ctas[pln_id] == '' || $prop_ctas[pln_id] == '') {
            $sms = 2;
        } else {
            if (empty($id)) {
                if (!empty($rst_sec)) {
                    $sms = 1;
                } else {
                    if (empty($data[0])) {
                        if (strlen($data[6]) < 11) {
                            $tipo = 'CN';
                        } else {
                            $tipo = 'CJ';
                        }
                        $rst_cod = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_cliente($tipo));
                        $sec = (substr($rst_cod[cli_codigo], -5) + 1);
                        if ($sec >= 0 && $sec < 10) {
                            $txt = '0000';
                        } else if ($sec >= 10 && $sec < 100) {
                            $txt = '000';
                        } else if ($sec >= 100 && $sec < 1000) {
                            $txt = '00';
                        } else if ($sec >= 1000 && $sec < 10000) {
                            $txt = '0';
                        } else if ($sec >= 10000 && $sec < 100000) {
                            $txt = '';
                        }
                        $retorno = $tipo . $txt . $sec;
                        $da = array(
                            strtoupper($data[5]),
                            strtoupper($data[6]),
                            strtoupper($data[8]),
                            $retorno,
                            strtoupper($data[20]),
                            $data[7]
                        );
                        if ($Clase_nota_Credito_nuevo->insert_cliente($da) == false) {
                            $sms = 'Insert_cli' . pg_last_error();
                            $v = 1;
                        }
                        $rstcli = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_cliente_cedula($data[6]));
                        $cli_id = $rstcli[cli_id];
                    } else {
                        $cli_id = $data[0];
                    }

                    if ($v == 0) {
                        $dat = Array(
                            $cli_id,
                            $data[1], //emisor
                            $data[24], //vendedor
                            $data[2], //numero
                            $data[3], //motivo
                            $data[4], //fecha emision
                            strtoupper($data[5]), //nombre
                            strtoupper($data[6]), //identificacion
                            $data[7], //email
                            strtoupper($data[8]), //direccion
                            $data[9], //denominacion comp
                            $data[10], //numero_comp
                            $data[11], //fecha_comp
                            $data[12], //iva12
                            $data[13], //iva0
                            $data[14], //ivaex
                            $data[15], //ivano
                            $data[16], //desc
                            $data[17], //ice
                            $data[18], //total iva
                            $data[19], //total irbpnr
                            $data[20], //telefono
                            $data[21], //total valor
                            $data[22], //total propina
                            $data[25], //fac_id
                            $data[23], //trs_id
                            $data[26] //subtotal
                        );

                        if ($Clase_nota_Credito_nuevo->insert_nota_credito($dat) == TRUE) {
                            $nc = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_notac_num($data[2]));
                            $nrc_id = $nc[ncr_id];
                            $n = 0;
                            while ($n < count($data1)) {
                                $dt = explode('&', $data1[$n]);
                                if ($Clase_nota_Credito_nuevo->insert_det_nota_credito($dt, $nrc_id) == TRUE) {
                                    if ($x == 0) {
                                        $rst_ids = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_id($dt[0]));
                                        $p_ids = $rst_ids[ids];
                                        if ($p_ids != 79 && $p_ids != 80) {
                                            $bod = $data[1];
                                            $dat2 = Array(
                                                $dt[0],
                                                $data[23],
                                                $cli_id,
                                                $bod,
                                                $data[2],
                                                $data[4],
                                                $dt[3],
                                                '0',
                                                $dt[12],
                                                $dt[13]
                                            );
                                            if ($data[23] != '1') {
                                                if ($Clase_nota_Credito_nuevo->insert_movimiento($dat2) == FALSE) {
                                                    $sms = pg_last_error() . 'insert_mov,' . $pro;
                                                    $aud = 1;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $sms = 'Insert_det' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                        } else {
                            $sms = 'Insert' . pg_last_error();
                            $accion = 'Insertar';
                            $aud = 1;
                        }
                    }
                    if ($aud == 0) {
                        $cheques = Array($data[0],
                            'NOTA DE CREDITO',
                            '',
                            $data[2],
                            $data[4],
                            $data[4],
                            $data[21],
                            '0',
                            '',
                            '3',
                            '',
                            '0',
                            $nrc_id,
                            '0');
                        if ($Clase_nota_Credito_nuevo->insert_cheques($cheques) == false) {
                            $sms = 'Insert_cheques' . pg_last_error();
                            $aud = 1;
                        }
                    }
                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'NOTA DE CREDITO';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[2]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
            } else {
                $nrc_id = $id;
                if (empty($data[0])) {
                    if (strlen($data[6]) < 11) {
                        $tipo = 'CN';
                    } else {
                        $tipo = 'CJ';
                    }
                    $rst_cod = pg_fetch_array($Clase_nota_Credito_nuevo->lista_secuencial_cliente($tipo));
                    $sec = (substr($rst_cod[cli_codigo], -5) + 1);
                    if ($sec >= 0 && $sec < 10) {
                        $txt = '0000';
                    } else if ($sec >= 10 && $sec < 100) {
                        $txt = '000';
                    } else if ($sec >= 100 && $sec < 1000) {
                        $txt = '00';
                    } else if ($sec >= 1000 && $sec < 10000) {
                        $txt = '0';
                    } else if ($sec >= 10000 && $sec < 100000) {
                        $txt = '';
                    }
                    $retorno = $tipo . $txt . $sec;
                    $da = array(
                        strtoupper($data[5]),
                        strtoupper($data[6]),
                        strtoupper($data[8]),
                        $retorno,
                        strtoupper($data[20]),
                        $data[7]
                    );
                    if ($Clase_nota_Credito_nuevo->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $v = 1;
                    }
                    $rstcli = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_cliente_cedula($data[6]));
                    $cli_id = $rstcli[cli_id];
                } else {
                    $cli_id = $data[0];
                }


                $dat = Array(
                    $cli_id,
                    $data[1], //emisor
                    $data[24], //vendedor
                    $data[2], //numero
                    $data[3], //motivo
                    $data[4], //fecha emision
                    strtoupper($data[5]), //nombre
                    strtoupper($data[6]), //identificacion
                    $data[7], //email
                    strtoupper($data[8]), //direccion
                    $data[9], //denominacion comp
                    $data[10], //numero_comp
                    $data[11], //fecha_comp
                    $data[12], //iva12
                    $data[13], //iva0
                    $data[14], //ivaex
                    $data[15], //ivano
                    $data[16], //desc
                    $data[17], //ice
                    $data[18], //total iva
                    $data[19], //total irbpnr
                    $data[20], //telefono
                    $data[21], //total valor
                    $data[22], //total propina
                    $data[25], //fac_id
                    $data[23], //fac_id
                    $data[26] //subtotal
                );

                if ($Clase_nota_Credito_nuevo->upd_nota_credito($dat, $id) == FALSE) {
                    $sms = pg_last_error() . 'updnota_credito';
                    $aud = 1;
                } else {

                    if ($x == 0) {
                        if ($Clase_nota_Credito_nuevo->delete_movimiento($data[1], $data[2]) == FALSE) {
                            $sms = pg_last_error() . 'del_mov';
                            $aud = 1;
                        }
                    }
                    if ($Clase_nota_Credito_nuevo->delete_cobros($id) == FALSE) {
                        $sms = pg_last_error() . 'delet_detalle';
                        $aud = 1;
                    } else {
                        if ($Clase_nota_Credito_nuevo->delete_det_nota_credito($id) == FALSE) {
                            $sms = pg_last_error() . 'delet_detalle';
                            $aud = 1;
                        } else {
                            $n = 0;
                            while ($n < count($data1)) {
                                $dt = explode('&', $data1[$n]);
                                if ($Clase_nota_Credito_nuevo->insert_det_nota_credito($dt, $id) == TRUE) {
                                    if ($x == 0) {
                                        $rst_ids = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_id($dt[0]));
                                        $p_ids = $rst_ids[ids];
                                        if ($p_ids != 79 && $p_ids != 80) {
                                            $bod = $data[1];
                                            $dat2 = Array(
                                                $dt[0],
                                                $data[23],
                                                $cli_id,
                                                $bod,
                                                $data[2],
                                                $data[4],
                                                round($dt[3], 2),
                                                '0',
                                                round($dt[12], 2),
                                                (round($dt[3], 2) * round($dt[13], 2))
                                            );
                                            if ($data[23] != '1') {
                                                if ($Clase_nota_Credito_nuevo->insert_movimiento($dat2) == FALSE) {
                                                    $sms = pg_last_error() . 'insert_mov,' . $pro;
                                                    $aud = 1;
                                                }
                                            }
                                        }
                                    }
                                    $n++;
                                }
                            }

                            if ($aud == 0) {
                                $cheques = Array($data[0],
                                    'NOTA DE CREDITO',
                                    '',
                                    $data[2],
                                    $data[4],
                                    $data[4],
                                    $data[21],
                                    '0',
                                    '',
                                    '3',
                                    '',
                                    '0',
                                    $nrc_id,
                                    '0');
                                if ($Clase_nota_Credito_nuevo->insert_cheques($cheques) == false) {
                                    $sms = 'Insert_cheques' . pg_last_error();
                                    $aud = 1;
                                }
                            }
                        }
                    }
                }
                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'NOTA DE CREDITO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[2]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms . '&' . $nrc_id;
        break;
    case 1:
        if ($s == 0) {
            $cns = $Clase_nota_Credito_nuevo->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
            echo $sms = 1;
        } else {
            $sms;
            $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . trim($rst[cli_id]);
            }
            echo $sms;
        }
        break;

    case 2:
        $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_noperti_id($id));
        $rst_precio1 = pg_fetch_array($Clase_nota_Credito_nuevo->lista_precio_producto($id, '1'));
        echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad];
        break;
    case 3:
        $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_mp($id));
        if ($rst[id] != '') {
            $rst2 = pg_fetch_array($Clase_nota_Credito_nuevo->lista_costos_mov($rst[id]));
            if ($x == 0) {
                if ($ctr_inv == 0) {
                    $fra = '';
                } else {
                    $fra = "and m.bod_id=$emi";
                }
                $rst_i = pg_fetch_array($Clase_nota_Credito_nuevo->total_ingreso_egreso_fact($rst2[pro_id], $fra));
                $inv = $rst_i[ingreso] - $rst_i[egreso];
            }
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst[mp_j] . '&' . $rst[mp_k] . '&' . $rst[mp_l] . '&';
        }
        break;

    case 4:
        $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_una_factura_nfact($id));
        if (empty($data)) {
            echo $rst[fac_id] . '&' .
            $rst[fac_fecha_emision] . '&' .
            str_replace('&', '',$rst[fac_identificacion]) . '&' .
            str_replace('&', '',$rst[fac_nombre]) . '&' .
            str_replace('&', '',$rst[fac_direccion]) . '&' .
            str_replace('&', '',$rst[fac_telefono]) . '&' .
            str_replace('&', '',$rst[fac_email]). '&' .
            $rst[cli_id] . '&' .
            str_replace(',', '', number_format($rst[fac_total_propina], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal12], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal0], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal_no_iva], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal_ex_iva], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_total_descuento], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_total_ice], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_total_iva], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_total_irbpnr], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_total_valor], $s)) . '&' .
            str_replace(',', '', number_format($rst[fac_subtotal], $s));
        } else {
            if ($rst[fac_id] != '') {
                $cns = $Clase_nota_Credito_nuevo->lista_det_factura($rst[fac_id]);
                while ($rst2 = pg_fetch_array($cns)) {
                    $n++;
                    $rst_s = pg_fetch_array($Clase_nota_Credito_nuevo->suma_prod_nota_credito($rst[fac_id], $rst2[pro_id]));
                    $entr = $rst2[dfc_cantidad] - $rst_s[sum];
                    if ($x == 0) {
                        if ($ctr_inv == 0) {
                            $fra = '';
                        } else {
                            $fra = "and m.bod_id=$emi";
                        }
                        $rst_i = pg_fetch_array($Clase_nota_Credito_nuevo->total_ingreso_egreso_fact($rst2[pro_id], $fra));
                        $inv = $rst_i[ingreso] - $rst_i[egreso];
                        $hidden = '';
                    } else {
                        $hidden = 'hidden';
                    }
                    $a = '"';
                    $lista.="<tr>
                                        <td><input type='text' size='5'  id='item$n' class='itm'  lang='$n' value='$n' readonly  style='text-align:right'/></td>  
                                        <td class='td1'><input type='text' size='20' id='cod_producto$n' value='$rst2[dfc_codigo]' lang='$n' readonly/>
                                            <input hidden type='text' size='10' id='pro_id$n' value='$rst2[pro_id]' lang='$n' readonly/></td>
                                        <td><input type='text' size='35' id='descripcion$n' value='$rst2[dfc_descripcion]' lang='$n' readonly/></td>  
                                        <td class='td1'><input type='text' size='8'  id='cantidad$n'  value='" . str_replace(',', '', number_format($entr, $c)) . "' lang='$n' readonly/></td>
                                        <td $hidden><input type='text' $hidden size='8' id='inventario$n' value='" . str_replace(',', '', number_format($inv, $c)) . "' lang='$n' readonly/></td>  
                                        <td><input type='text' size='7'  id='cantidadf$n'  value='" . str_replace(',', '', number_format($rst2[dfc_cantidad], $c)) . "' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a)' onchange='calculo(),comparar(this)' style='text-align:right' lang='$n'/></td>
                                        <td><input type='text' size='7'  id='precio_unitario$n'  value='" . str_replace(',', '', number_format($rst2[dfc_precio_unit], $s)) . "' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/,$a$a), calculo()' onblur='calculo()' lang='$n' readonly/></td>                  
                                        <td><input type='text' size='7'  id='descuento$n'  value='" . str_replace(',', '', number_format($rst2[dfc_porcentaje_descuento], $s)) . "'  style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a), calculo()' onblur='calculo()' lang='$n' readonly/></td>                  
                                        <td>
                                            <input type='text' size='7'  id='descuent$n'  value='" . str_replace(',', '', number_format($rst2[dfc_val_descuento], $s)) . "' lang='$n' readonly  />
                                            <label hidden id='lbldescuent$n' lang='$n'>$rst2[dfc_val_descuento]</label>                  
                                        </td>
                                        <td><input type='text' size='7'  id='iva$n'  value='$rst2[dfc_iva]' style='text-align:right' onkeyup='calculo()' lang='$n' onblur='calculo(), this.value = this.value.toUpperCase()' readonly/></td>                  
                                        <td hidden><input type='text' size='7'  id='ice_p$n'  value='" . str_replace(',', '', number_format($rst2[dfc_p_ice], $s)) . "' style='text-align:right' lang='$n' onblur='calculo(), this.value = this.value.toUpperCase()' readonly/></td>
                                              <td hidden><input type='text' id='ice$n' size='5' value='" . str_replace(',', '', number_format($rst2[dfc_ice], $s)) . "' readonly lang='$n'/>
                                                <label hidden id='lblice$n' lang='$n'>$rst2[dfc_ice]</label>
                                                <input type='hidden' id='ice_cod$n' size='5' value='$rst2[dfc_cod_ice]' readonly lang='$n'/>
                                            </td>
                                            <td hidden><input type='text' size='7'  id='irbp_p$n'  value='$rst2[dfc_p_irbpnr]' style='text-align:right' onkeyup='this.value = this.value.replace(/[^0-9.]/,$a$a)' lang='$n' onblur='calculo()'/></td>
                                        <td hidden><input type='text' size='7'  id='irbp$n'  value='" . str_replace(',', '', number_format($rst2[dfc_irbpnr], $s)) . "' style='text-align:right' lang='$n' onblur='calculo(), this.value = this.value.toUpperCase()' readonly/>
                                            <label hidden id='lblirbp$n' lang='$n'>$rst2[dfc_irbpnr]</label></td>   
                                        <td><input type='text' size='10'  id='precio_total$n'  value='" . str_replace(',', '', number_format($rst2[dfc_precio_total], $s)) . "' style='text-align:right' lang='$n' readonly />
                                            <label hidden id='lblprecio_total$n' lang='$n'>$rst2[dfc_precio_total]</label></td>                 
                                        <td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png' /></td>
                                    </tr>";
                }
            }
            echo str_replace('&', '',$lista);
        }
        break;

    case 5:
        if ($Clase_nota_Credito_nuevo->delete_det_nota_credito($id) == false) {
            $sms = pg_last_error();
        } else {
            if ($Clase_nota_Credito_nuevo->delete_nota_credito($id) == false) {
                $sms = pg_last_error();
            } else {
                $modulo = 'NOTA DE CREDITO';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;

    case 6:

        $sms = 0;
        $aud = 0;
        $rst_ch = pg_fetch_array($Clase_nota_Credito_nuevo->lista_cheques_ctasxcob($id));
        $rst = pg_fetch_array($Clase_nota_Credito_nuevo->lista_una_nota_credito($id));
        if (empty($rst_ch)) {
            if ($Clase_nota_Credito_nuevo->update_estado_nota_credito($id) == false) {
                $sms = pg_last_error();
            } else {
                ////inserta_movimientos///
                if ($rst[trs_id] != 1) {
                    $cns_d = $Clase_nota_Credito_nuevo->lista_detalle_nota_credito($id);
                    while ($dt = pg_fetch_array($cns_d)) {
                        $rst_ids = pg_fetch_array($Clase_nota_Credito_nuevo->lista_un_producto_id($dt[pro_id]));
                        $p_ids = $rst_ids[ids];
                        $fra = "and m.bod_id= $rst[emi_id]";
                        $rst2 = pg_fetch_array($Clase_nota_Credito_nuevo->lista_costos_mov($dt[pro_id], $fra));
                        $rst2[mov_val_unit] = (($rst2[ingreso] - $rst2[egreso]) / ($rst2[icnt] - $rst2[ecnt]));
                        if ($p_ids != 79 && $p_ids != 80) {
                            $dat2 = Array(
                                $dt[pro_id],
                                '27',
                                $rst[cli_id],
                                $rst[emi_id],
                                $rst[ncr_numero],
                                date('Y-m-d'),
                                round($dt[dnc_cantidad], 2),
                                '0',
                                round($rst2[mov_val_unit], 2),
                                (round($dt[dnc_cantidad], 2) * round($rst2[mov_val_unit], 2))
                            );
                            if ($data[23] != '1') {
                                if ($Clase_nota_Credito_nuevo->insert_movimiento($dat2) == FALSE) {
                                    $sms = pg_last_error() . 'insert_mov,' . $pro;
                                    $aud = 1;
                                }
                            }
                        }
                    }
                }
                ///anula_cobros////
                if ($Clase_nota_Credito_nuevo->update_estado_cheques($id) == false) {
                    $sms = pg_last_error();
                } else {
                    ///anula asiento///
                    $cns_as = $Clase_nota_Credito_nuevo->lista_asientos_mod($id, '2');
                    $asi = $Clase_nota_Credito_nuevo->siguiente_asiento();
                    while ($rst_as = pg_fetch_array($cns_as)) {
                        $asiento = array(
                            $asi,
                            'ANULACION ' . $rst_as[con_asiento],
                            $rst_as[con_documento], //doc
                            date('Y-m-d'), //fec
                            $rst_as[con_concepto_haber], //con_debe
                            $rst_as[con_concepto_debe], //con_haber
                            $rst_as[con_valor_haber], //val_debe
                            $rst_as[con_valor_debe], // val_haber
                            '2', //estado
                            '2',
                            $id,
                            $rst_as[cli_id]
                        );
                        if ($Clase_nota_Credito_nuevo->insert_asientos($asiento) == false) {
                            $sms = 'Insert_asientos' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
            }
        } else {
            $sms = 1;
        }
        echo $sms;
        break;
}
?>



















