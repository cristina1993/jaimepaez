<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_nota_debito.php';
$Clase_nota_debito = new Clase_nota_debito();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        $aud = 0;
        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '11'));
        $cli_ext_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '12'));
        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '13'));
        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '14'));
        $ice_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '15'));
        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '16'));
        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas($data[2], '17'));
        if ($cli_nac_ctas[pln_id] == '' || $cli_ext_ctas[pln_id] == '' || $ven_ctas[pln_id] == '' || $iva_ctas[pln_id] == '' || $fle_ctas[pln_id] == '' || $ctas_cob[pln_id] == '' || $ice_ctas[pln_id] == '') {
            $sms = 2;
        } else {

            if (empty($id)) {
                if (empty($data[0])) {
                    if (strlen($data[7]) < 11) {
                        $tipo = 'CN';
                    } else {
                        $tipo = 'CJ';
                    }
                    $rst_cod = pg_fetch_array($Clase_nota_debito->lista_secuencial_cliente($tipo));
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
                        strtoupper($data[6]),
                        strtoupper($data[7]),
                        strtoupper($data[9]),
                        $retorno,
                        strtoupper($data[21]),
                        $data[8]
                    );
                    if ($Clase_nota_debito->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $v = 1;
                    }
                    $rstcli = pg_fetch_array($Clase_nota_debito->lista_un_cliente_cedula($data[7]));
                    $cli_id = $rstcli[cli_id];
                } else {
                    $cli_id = $data[0];
                }

                if ($v == 0) {
                    if ($Clase_nota_debito->insert_nota_debito($data, $cli_id) == TRUE) {
                        $nd = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito($data[3]));
                        $ndb_id = $nd[ndb_id];
                        $n = 0;
                        while ($n < count($data1)) {
                            $dt = explode('&', $data1[$n]);
                            if ($Clase_nota_debito->insert_det_nota_debito($dt, $ndb_id) == false) {
                                $sms = 'Insert_det' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                        $rst_cli = pg_fetch_array($Clase_nota_debito->lista_un_cliente($data[7]));
                        $cli = $rst_cli[cli_id];
                        /////inser cobros nota debito 
                        $cheque = Array(
                            $cli,
                            'NOTA DE DEBITO',
                            'NOTA DE DEBITO',
                            $data[3],
                            $data[5], // fecha
                            $data[5],
                            $data[20], //total
                            '4', // tipo documento
                            $data[20],
                            $ndb_id
                        );
                        if ($Clase_nota_debito->insert_cheque($cheque) == FALSE) {
                            $sms = pg_last_error() . 'error cheque ND';
                        }

                        $rst_idnt = pg_fetch_array($Clase_nota_debito->lista_id_nota_debito($data[2], $data[11]));
                        $cns_det = $Clase_nota_debito->lista_det_ntd($rst_idnt[ndb_id]);

                        while ($rst_det = pg_fetch_array($cns_det)) {
                            if (strtoupper($rst_det[dnd_descripcion]) == 'FLETE 1') {
                                $fle0 = 1;
                                $tf0 = $rst_det[dnd_precio_total] + $tf0;
                            }

                            if (strtoupper($rst_det[dnd_descripcion]) == 'FLETE 2') {
                                $fle12 = 1;
                                $tf12 = $rst_det[dnd_precio_total] + $tf12;
                            }
                        }
                        $tot_sub = $data[24] - $tf0 - $tf12;


                        $asient = $Clase_nota_debito->siguiente_asiento();
                        $dat_asi = array($tot_sub,
                            $data[3],
                            $data[5],
                            '',
                            $data[18],
                            $data[20],
                            '0',
                            $cli_nac_ctas[pln_codigo],
                            $ven_ctas[pln_codigo], // cta subtotal
                            $iva_ctas[pln_codigo], // cta iva
                            $fle_ctas[pln_codigo], // flete
                            $tf0,
                            $tf12,
                            $ice_ctas[pln_codigo],
                            $data[17],
                            '3',
                            $nd[ndb_id],
                            $nd[cli_id]
                        );
                        if ($Clase_nota_debito->insert_asiento_nd($dat_asi, $asient) == false) {
                            $sms = 'asientos_1' . pg_last_error();
                        } else {
                            $chq4 = pg_fetch_array($Clase_nota_debito->lista_cheques_numero($data[3], $data[0]));
                            $fd = pg_num_rows($Clase_nota_debito->listar_una_cta_comid($data[19]));
                            $fc = pg_num_rows($Clase_nota_debito->lista_pagos($data[19]));
                            if ($fd == $fc || $fc == 1) {
                                $rst_pag = pg_fetch_array($Clase_nota_debito->lista_pagos($data[19], 'desc'));
                            } else {
                                $rst_pag = pg_fetch_array($Clase_nota_debito->buscar_un_pago($data[19]));
                            }
                            $cuenta1 = Array(
                                $data[19],
                                $data[5],
                                $data[20],
                                'NOTA DE DEBITO',
                                $ctas_cob[pln_codigo], //cta_banco
                                $cli_nac_ctas[pln_id],
                                $rst_pag[pag_id], //// FALTA
                                $data[5],
                                $data[3],
                                'NOTA DE DEBITO',
                                '0',
                                $chq4[chq_id]
                            );
                            if ($Clase_nota_debito->insert_ctaxcobrar($cuenta1) == false) {
                                $sms = 'insert_ctasxcobrar' . pg_last_error();
                            } else {
                                $asi = $Clase_nota_debito->siguiente_asiento();
                                $asiento = array(
                                    $asi,
                                    'CXC ND FAC. ' . $data[11],
                                    $data[3], //doc
                                    $data[5], //fec
                                    $ctas_cob[pln_codigo], //con_debe
                                    $cli_nac_ctas[pln_codigo], //con_haber
                                    $data[20], // val_debe
                                    $data[20], //val_haber
                                    '1', //estado
                                    '12',
                                    $nd[ndb_id],
                                    $nd[cli_id]
                                );
                                if ($Clase_nota_debito->insert_asientos($asiento) == false) {
                                    $sms = 'Insert_asientos' . pg_last_error();
                                    $aud = 1;
                                }
                            }
                        }
                    } else {
                        $sms = 'Insert_nota' . pg_last_error();
                        $aud = 1;
                    }
                }
                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'NOTA DE DEBITO';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            } else {////Modificar
                $nd = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito_id($id));
                if (empty($data[0])) {
                    if (strlen($data[7]) < 11) {
                        $tipo = 'CN';
                    } else {
                        $tipo = 'CJ';
                    }
                    $rst_cod = pg_fetch_array($Clase_nota_debito->lista_secuencial_cliente($tipo));
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
                        strtoupper($data[6]),
                        strtoupper($data[7]),
                        strtoupper($data[9]),
                        $retorno,
                        strtoupper($data[21]),
                        $data[8]
                    );
                    if ($Clase_nota_debito->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                        $v = 1;
                    }
                    $rstcli = pg_fetch_array($Clase_nota_debito->lista_un_cliente_cedula($data[6]));
                    $cli_id = $rstcli[cli_id];
                } else {
                    $cli_id = $data[0];
                }

                if ($v == 0) {

                    if ($Clase_nota_debito->upd_nota_debito($data, $id, $cli_id) == TRUE) {
                        $rst_ch = pg_fetch_array($Clase_nota_debito->lista_cheques($id));
                        if ($Clase_nota_debito->delete_ctasxcobrar($rst_ch[chq_id]) == false) {
                            $sms = 'delete_ctasxcobrar' . pg_last_error();
                            $aud = 1;
                        } else {
                            if ($Clase_nota_debito->delete_cheques($id) == false) {
                                $sms = 'delete_cheques' . pg_last_error();
                                $aud = 1;
                            } else {
                                $rst_as = pg_fetch_array($Clase_nota_debito->lista_asientos_mod($id, '12'));
                                if ($Clase_nota_debito->delete_asientos($rst_as[con_asiento]) == false) {
                                    $sms = 'delete_asiento_ctasxcobrar' . pg_last_error();
                                    $aud = 1;
                                }
                            }
                        }
                        $rst_asn = pg_fetch_array($Clase_nota_debito->lista_asientos_mod($id, '3'));
                        if ($Clase_nota_debito->delete_asientos($rst_asn[con_asiento]) == false) {
                            $sms = 'delete_asiento_nota' . pg_last_error();
                            $aud = 1;
                        }
                        if ($Clase_nota_debito->delete_det_nota($id) == false) {
                            $sms = 'delete_det_nota' . pg_last_error();
                            $aud = 1;
                        } else {
                            $ndb_id = $id;
                            $n = 0;
                            while ($n < count($data1)) {
                                $dt = explode('&', $data1[$n]);
                                if ($Clase_nota_debito->insert_det_nota_debito($dt, $ndb_id) == false) {
                                    $sms = 'Insert_det' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                            $rst_cli = pg_fetch_array($Clase_nota_debito->lista_un_cliente($data[7]));
                            $cli = $rst_cli[cli_id];
                            /////inser cobros nota debito 
                            $cheque = Array(
                                $cli,
                                'NOTA DE DEBITO',
                                'NOTA DE DEBITO',
                                $data[3],
                                $data[5], // fecha
                                $data[5],
                                $data[20], //total
                                '4', // tipo documento
                                $data[20],
                                $ndb_id
                            );
                            if ($Clase_nota_debito->insert_cheque($cheque) == FALSE) {
                                $sms = pg_last_error() . 'error cheque ND';
                            }

                            $rst_idnt = pg_fetch_array($Clase_nota_debito->lista_id_nota_debito($data[2], $data[11]));
                            $cns_det = $Clase_nota_debito->lista_det_ntd($rst_idnt[ndb_id]);

                            while ($rst_det = pg_fetch_array($cns_det)) {
                                if (strtoupper($rst_det[dnd_descripcion]) == 'FLETE 1') {
                                    $fle0 = 1;
                                    $tf0 = $rst_det[dnd_precio_total] + $tf0;
                                }

                                if (strtoupper($rst_det[dnd_descripcion]) == 'FLETE 2') {
                                    $fle12 = 1;
                                    $tf12 = $rst_det[dnd_precio_total] + $tf12;
                                }
                            }
                            $tot_sub = $data[24] - $tf0 - $tf12;


                            $asient = $Clase_nota_debito->siguiente_asiento();
                            $dat_asi = array($tot_sub,
                                $data[3],
                                $data[5],
                                '',
                                $data[18],
                                $data[20],
                                '0',
                                $cli_nac_ctas[pln_codigo],
                                $ven_ctas[pln_codigo], // cta subtotal
                                $iva_ctas[pln_codigo], // cta iva
                                $fle_ctas[pln_codigo], // flete
                                $tf0,
                                $tf12,
                                $ice_ctas[pln_codigo],
                                $data[17],
                                '3',
                                $nd[ndb_id],
                                $nd[cli_id]
                            );
                            if ($Clase_nota_debito->insert_asiento_nd($dat_asi, $asient) == false) {
                                $sms = 'asientos_1' . pg_last_error();
                            } else {
                                $chq4 = pg_fetch_array($Clase_nota_debito->lista_cheques_numero($data[3], $data[0]));
                                $rst_pag = pg_fetch_array($Clase_nota_debito->lista_pagos($data[19], 'desc'));
                                $cuenta1 = Array(
                                    $data[19],
                                    $data[5],
                                    $data[20],
                                    'NOTA DE DEBITO',
                                    $ctas_cob[pln_codigo], //cta_banco
                                    $cli_nac_ctas[pln_id],
                                    $rst_pag[pag_id], //// FALTA
                                    $data[5],
                                    $data[3],
                                    'NOTA DE DEBITO',
                                    '0',
                                    $chq4[chq_id]
                                );
                                if ($Clase_nota_debito->insert_ctaxcobrar($cuenta1) == false) {
                                    $sms = 'insert_ctasxcobrar' . pg_last_error();
                                } else {
                                    $asi = $Clase_nota_debito->siguiente_asiento();
                                    $asiento = array(
                                        $asi,
                                        'CXC ND FAC. ' . $data[11],
                                        $data[3], //doc
                                        $data[5], //fec
                                        $ctas_cob[pln_codigo], //con_debe
                                        $cli_nac_ctas[pln_codigo], //con_haber
                                        $data[20], // val_debe
                                        $data[20], //val_haber
                                        '1', //estado
                                        '12',
                                        $nd[ndb_id],
                                        $nd[cli_id]
                                    );
                                    if ($Clase_nota_debito->insert_asientos($asiento) == false) {
                                        $sms = 'Insert_asientos' . pg_last_error();
                                        $aud = 1;
                                    }
                                }
                            }
                        }
                    } else {
                        $sms = 'Insert_nota' . pg_last_error();
                        $aud = 1;
                    }
                }
                if ($aud == 0) {
                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'NOTA DE DEBITO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $cns = $Clase_nota_debito->lista_nota_debito_completo();
        $rst_am = pg_fetch_array($Clase_nota_debito->lista_configuraciones());
        $ambiente = $rst_am[con_valor];
        $codigo = "12345678";
        $tp_emison = '1';
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[ndb_clave_acceso])) {
                $emis = pg_fetch_array($Clase_nota_debito->lista_emisor($rst[emi_id]));
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

                $fecha = date_format(date_create($rst[ndb_fecha_emision]), 'd/m/Y');
                $f2 = date_format(date_create($rst[ndb_fecha_emision]), 'dmY');
                $ndoc = explode('-', $rst[ndb_numero]);
                $nfact = str_replace('-', '', $rst[ndb_numero]);
                $secuencial = $ndoc[2];
                $cod_doc = "05"; //01= factura, 02=nota de credito tabla 4
                $id_comprador = $rst[ndb_identificacion];
                if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
                    $tipo_id_comprador = "04"; //RUC 04 
                } else if (strlen($id_comprador) == 10) {
                    $tipo_id_comprador = "05"; //CEDULA 05 
                } else if ($id_comprador == '9999999999999') {
                    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
                } else {
                    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
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

                $Clase_nota_debito->upd_notdeb_clave_acceso($clave, $rst[ndb_id]);
            }
        }
        break;
    case 2:
        $sms = 0;
        if ($Clase_nota_debito->upd_notdeb_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 3:
        $sms = 0;
        if ($Clase_nota_debito->delete_det_nota($id) == FALSE) {
            $sms = pg_last_error() . 'delete2';
        } else {
            if ($Clase_nota_debito->delete_nota_debito($id) == FALSE) {
                $sms = pg_last_error() . 'delete1';
            } else {
                $modulo = 'NOTA DE DEBITO';
                $accion = 'ELIMINAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }

        break;
    case 4:
        $sms = 0;
        if ($Clase_nota_debito->upd_estado_notdeb($id) == FALSE) {
            $sms = 'update_nota' . pg_last_error();
        } else {
            $rst_ch = pg_fetch_array($Clase_nota_debito->lista_cheques($id));
            if ($Clase_nota_debito->upd_estado_ctasxcobrar($rst_ch[chq_id], '1') == FALSE) {
                $sms = 'update_cheques' . pg_last_error();
            } else {
                if ($Clase_nota_debito->upd_estado_cheques($id, '3') == FALSE) {
                    $sms = 'update_cheques' . pg_last_error();
                } else {
                    $cns_asi = $Clase_nota_debito->lista_asientos_mod($id, '12');
                    $asi = $Clase_nota_debito->siguiente_asiento();
                    while ($rst_asi = pg_fetch_array($cns_asi)) {
                        $asiento = array(
                            $asi,
                            'ANULACION ' . $rst_asi[con_asiento],
                            $rst_asi[con_documento], //doc
                            date('Y-m-d'), //fec
                            $rst_asi[con_concepto_haber], //con_debe
                            $rst_asi[con_concepto_debe], //con_haber
                            $rst_asi[con_valor_haber], //val_debe
                            $rst_asi[con_valor_debe], // val_haber
                            '2', //estado
                            '12',
                            $id,
                            $rst_asi[cli_id]
                        );
                        if ($Clase_nota_debito->insert_asientos($asiento) == false) {
                            $sms = 'Insert_asientos_ctasxcobrar' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    $cns_as = $Clase_nota_debito->lista_asientos_mod($id, '3');
                    $as = $Clase_nota_debito->siguiente_asiento();
                    while ($rst_as = pg_fetch_array($cns_as)) {
                        $as_nota = array(
                            $as,
                            'ANULACION ' . $rst_as[con_asiento],
                            $rst_as[con_documento], //doc
                            date('Y-m-d'), //fec
                            $rst_as[con_concepto_haber], //con_debe
                            $rst_as[con_concepto_debe], //con_haber
                            $rst_as[con_valor_haber], //val_debe
                            $rst_as[con_valor_debe], // val_haber
                            '2', //estado
                            '3',
                            $id,
                            $rst_as[cli_id]
                        );
                        if ($Clase_nota_debito->insert_asientos($as_nota) == false) {
                            $sms = 'Insert_asiento_nota' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
            }
        }
        echo $sms;
        break;

    case 5:
        if ($s == 0) {
            $cns = $Clase_nota_debito->lista_clientes_search(strtoupper($id));
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
            $rst = pg_fetch_array($Clase_nota_debito->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . trim($rst[cli_id]);
            }
            echo $sms;
        }
        break;

    case 6:
        $rst = pg_fetch_array($Clase_nota_debito->lista_una_factura_numdoc($id));
        echo $rst[fac_id] . '&' . $rst[fac_fecha_emision] . '&' . $rst[fac_identificacion] . '&' . $rst[fac_nombre] . '&' . $rst[fac_direccion] . '&' . $rst[fac_telefono] . '&' . $rst[fac_email] . '&' . $rst[cli_id];
        break;

    case 7:
        $sms = 0;
        $imp = pg_fetch_array($Clase_nota_debito->lista_un_impuesto($id));
        $cnt = $imp[por_porcentage];
        echo $imp[por_id] . '&' . $imp[por_codigo] . '&' . $cnt;
        break;
}
?>
