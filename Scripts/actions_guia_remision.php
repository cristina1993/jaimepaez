<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php';
include_once("../Clases/clsAuditoria.php");
$Clase_guia_remision = new Clase_guia_remision();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$x = $_REQUEST[x];
$s = $_REQUEST[s];
switch ($op) {
    case 0:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {
            $n = 0;

            $cli_id = $data[2];
            $dat = Array(
                $data[0], //vendedor
                $data[1], //emisor
                $cli_id,
                $data[3], //numero
                $data[4], // fec_emi
                $data[5], //fec_ini
                $data[6], //fec_fin
                strtoupper($data[7]), //motivo
                strtoupper($data[8]), //pto_partida
                strtoupper($data[9]), //destino
                strtoupper($data[10]), //identificacion
                strtoupper($data[11]), //nombre
                strtoupper($data[12]), //ident. transp
                strtoupper($data[13]), //cod_aduanero
                strtoupper($data[14]), //cod_establecimiento
                $data[15], //num_comprobantes
                strtoupper($data[16]), //observacion
                $data[17], //fac_id
                $data[18], //tra_id
                $data[19], //denomi_comp
                $data[20], //autorizacion
                $data[21] //fch_comp
            );

            if ($Clase_guia_remision->insert_guia_remision($dat) == TRUE) {
                $gr = pg_fetch_array($Clase_guia_remision->lista_una_guia($data[3]));
                $gui_id = $gr[gui_id];
                $n = 0;
                while ($n < count($data2)) {
                    $dt = explode('&', $data2[$n]);
                    if ($Clase_guia_remision->insert_det_guia_remision($dt, $gui_id) == false) {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    }
                    $n++;
                }
            } else {
                $sms = 'Insert' . pg_last_error();
                $aud = 1;
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GUIA REMISION';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                    $aud = 1;
                }
            }
        } else {
            if ($Clase_guia_remision->delete_det_guia($id) == FALSE) {
                $sms = pg_last_error();
                $aud = 1;
            } else {
                $cli_id = $data[2];
                $dat = Array(
                    $data[0], //vendedor
                    $data[1], //emisor
                    $cli_id,
                    $data[3], //numero
                    $data[4], // fec_emi
                    $data[5], //fec_ini
                    $data[6], //fec_fin
                    strtoupper($data[7]), //motivo
                    strtoupper($data[8]), //pto_partida
                    strtoupper($data[9]), //destino
                    strtoupper($data[10]), //identificacion
                    strtoupper($data[11]), //nombre
                    strtoupper($data[12]), //ident. transp
                    strtoupper($data[13]), //cod_aduanero
                    strtoupper($data[14]), //cod_establecimiento
                    $data[15], //num_comprobantes
                    strtoupper($data[16]), //observacion
                    $data[17], //fac_id
                    $data[18], //tra_id
                    $data[19], //denomi_comp
                    $data[20], //autorizacion
                    $data[21] //autorizacion
                );

                if ($Clase_guia_remision->update_guia_remision($dat, $id) == TRUE) {
                    $gr = pg_fetch_array($Clase_guia_remision->lista_una_guia($data[3]));
                    $gui_id = $gr[gui_id];
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_guia_remision->insert_det_guia_remision($dt, $gui_id) == false) {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $aud = 1;
                }
            }


            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GUIA REMISION';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_guia_remision->delete_det_guia($id) == false) {
            $sms = pg_last_error();
        } else {
            if ($Clase_guia_remision->delete_guia_remision($id) == false) {
                $sms = pg_last_error();
            } else {
                $modulo = 'GUIA REMISION';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;

    case 2:
        if ($emisor == 10) {
            $ems = '010-';
        } else {
            $ems = '00' . $emisor . '-';
        }
        $rst = pg_fetch_array($Clase_guia_remision->lista_secuencial_documento($x));
        $rst1 = pg_num_rows($Clase_guia_remision->lista_secuencial_documento($x));

        if ($rst1 != 0) {
            $sec = (substr($rst[sec], -5) + 1);
            if ($sec >= 0 && $sec < 10) {
                $txt = '00000000';
            } else if ($sec >= 10 && $sec < 100) {
                $txt = '0000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $txt = '000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $txt = '00000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $txt = '0000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $txt = '000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $txt = '00';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $txt = '0';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $txt = '';
            }
        } else {
            $txt = '000000001';
        }
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:

        if ($s == 0) {
            $cns = $Clase_guia_remision->lista_buscar_transportistas(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[tra_razon_social]);
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_transportista2('$rst[tra_identificacion]')" . " /></td><td>$n</td><td>$rst[tra_identificacion]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_guia_remision->lista_un_transportista($id));
            if (!empty($rst)) {
                $sms = $rst[tra_identificacion] . '&' . trim($rst[tra_razon_social]) . '&' . $rst[tra_placa] . '&' . $rst[tra_id];
            }
            echo $sms;
        }
        break;

    case 6:
        $cns = $Clase_guia_remision->lista_guias();
        $rst_am = pg_fetch_array($Clase_guia_remision->lista_configuraciones('5'));
        $ambiente = $rst_am[con_valor];
        $codigo = "12345678";
        $tp_emison = '1';
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[gui_clave_acceso])) {
                $emis = pg_fetch_array($Clase_guia_remision->lista_emisor($rst[emi_id]));
                if ($emis[emi_cod_establecimiento_emisor] > 0 && $emis[emi_cod_establecimiento_emisor] < 10) {
                    $txem = '00';
                } elseif ($emis[emi_cod_establecimiento_emisor] >= 10 && $emis[emi_cod_establecimiento_emisor] < 100) {
                    $txem = '0';
                } else {
                    $txem = '';
                }
                if ($emis[emi_cod_punto_emision] > 0 && $emis[emi_cod_punto_emision] < 10) {
                    $txpe = '00';
                } elseif ($emis[emi_cod_punto_emision] >= 10 && $emis[emi_cod_punto_emision] < 100) {
                    $txpe = '0';
                } else {
                    $txpe = '';
                }
                $ems = $txem . $emis[emi_cod_establecimiento_emisor];
                $pt_ems = $txpe . $emis[emi_cod_punto_emision];

                $fecha = date_format(date_create($rst[gui_fecha_emision]), 'd/m/Y');

                $ndoc = explode('-', $rst[gui_numero]);
                $secuencial = $ndoc[2];
                $cod_doc = "06"; //01= factura, 02=nota de credito tabla 4
                $f2 = date_format(date_create($rst[gui_fecha_emision]), 'dmY');
                $id_comprador = $rst[gui_identificacion];
                if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
                    $tipo_id_comprador = "04"; //RUC 04 
                } else if (strlen($id_comprador) == 10) {
                    $tipo_id_comprador = "05"; //CEDULA 05 
                } else if ($id_comprador == '9999999999999') {
                    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
                } else {
                    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
                }
                $id_trans = $rst[gui_identificacion_transp];
                if (strlen($id_trans) == 13 && $id_trans != '9999999999999') {
                    $tipo_id_trans = "04"; //RUC 04 
                } else if (strlen($id_trans) == 10) {
                    $tipo_id_trans = "05"; //CEDULA 05 
                } else if ($id_trans == '9999999999999') {
                    $tipo_id_trans = "07"; //VENTA A CONSUMIDOR FINAL
                } else {
                    $tipo_id_trans = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
                }
                $round = 2;
                $clave1 = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison);
                $cla = strrev($clave1);
                $n = 0;
                $p = 1;
                $i = strlen($clave1);
                $m = 0;
                $s = 0;
                $j = 2;
                while ($n < $i) {
                    $d = substr($cla, $n, 1);
                    $m = $d * $j;
                    $s = $s + $m;
                    $j++;
                    if ($j == 8) {
                        $j = 2;
                    }
                    $n++;
                }
                $div = $s % 11;
                $digito = 11 - $div;
                if ($digito < 10) {
                    $digito = $digito;
                } else if ($digito == 10) {
                    $digito = 1;
                } else if ($digito == 11) {
                    $digito = 0;
                }
                $clave = trim($f2 . $cod_doc . $emis[emi_identificacion] . $ambiente . $ems . $pt_ems . $secuencial . $codigo . $tp_emison . $digito);
                $Clase_guia_remision->upd_guia_clave_acceso($clave, $rst[gui_id]);
//                echo $clave;
            }
        }
        break;
    case 7:
        $sms = 0;
        if ($Clase_guia_remision->upd_guia_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 8:
        $rst = pg_fetch_array($Clase_guia_remision->lista_una_factura_numdoc($id));
        if ($rst[fac_id] != '') {
            $cns = $Clase_guia_remision->lista_detalle_factura($rst[fac_id]);
            while ($rst1 = pg_fetch_array($cns)) {
                $n++;
                $rst_sum = pg_fetch_array($Clase_guia_remision->suma_cantidad_entregado($rst1[pro_id], $rst[fac_id]));
                if ($rst_sum[suma] == '') {
                    $rst_sum[suma] = 0;
                }
                $saldo = $rst1[dfc_cantidad] - $rst_sum[suma];
                $a = '"';
                $lista.=" <tr id='matriz'>
                                    <td align='right'><input type ='text' size='8'  class='item' id='item$n'  readonly value='$n' lang='$n'/></td>
                                    <td><input type ='text' size='20'  id='cod_producto$n'  value='$rst1[dfc_codigo]' lang='$n' readonly/>
                                        <input type ='hidden' id='pro_id$n'  value='$rst1[pro_id]' lang='$n' readonly hidden/></td>
                                    <td><input type ='text' size='60'  id='descripcion$n'  value='$rst1[dfc_descripcion]' lang='$n' readonly/></td>
                                    <td><input type ='text' size='15'  id='cantidadf$n'  value='$rst1[dfc_cantidad]' lang='$n' readonly/></td>
                                    <td><input type ='text' size='15'  id='entregado$n'  value='$rst_sum[suma]' lang='$n' readonly/></td>
                                    <td><input type ='text' size='15'  id='saldo$n'  value='$saldo' lang='$n' readonly/>
                                        <input type ='text' size='15'  id='saldox$n'  value='$saldo' lang='$n' readonly hidden/></td>
                                    <td><input type ='text' size='15'  id='cantidad$n'  value='$saldo' lang='$n' onkeyup='this.value = this.value.replace(/[^0-9.]/, $a$a), saldo(this)'/></td>
                                    <td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>
                                </tr>";
            }
        }
        echo $rst[fac_id] . '&' . $rst[fac_identificacion] . '&' . $rst[fac_nombre] . '&' . $lista . '&' . $rst[cli_id] . '&' . $rst[fac_autorizacion] . '&' . $rst[fac_fecha_emision];

        break;
    case 9:
        if ($s == 0) {
            $cns = $Clase_guia_remision->lista_clientes_search(strtoupper($id));
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
            $rst = pg_fetch_array($Clase_guia_remision->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . trim($rst[cli_id]);
            }
            echo $sms;
        }
        break;
    case 10:
        $rst = pg_fetch_array($Clase_guia_remision->lista_un_producto_mp($id));
        if ($rst[id] != '') {
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst[mp_j] . '&' . $rst[mp_k];
        }
        break;
}
?>
