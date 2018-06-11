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
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {
            if ($data[2] >= 10) {
                $ems = '0' . $data[2] . '-';
            } else {
                $ems = '00' . $data[2] . '-';
            }
            $rst_sec = pg_fetch_array($Clase_nota_debito->lista_secuencial_nota_debito($data[2]));
            if (empty($rst_sec)) {
                $sec = 1;
            } else {
                $se = explode('-', $rst_sec[ndb_numero]);
                $sec = ($se[2] + 1);
            }
            if ($sec >= 0 && $sec < 10) {
                $tx = '00000000';
            } else if ($sec >= 10 && $sec < 100) {
                $tx = '0000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $tx = '000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $tx = '00000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $tx = '0000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $tx = '000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $tx = '00';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $tx = '0';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $tx = '';
            }
            $comprobante = $ems . '001-' . $tx . $sec;
            $rst_ulti_sec = pg_fetch_array($Clase_nota_debito->lista_secuencial_locales($data[2]));
            if ($data[3] == $rst_ulti_sec[secuencial]) {
                $sms = 1;
                $aud = 1;
            } else {
                switch ($data[2]) {
                    case 1:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('217'));
                        $cli_ext_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('218'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('219'));
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('220'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('305'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('337'));
                        break;
                    case 2:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('221'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('222'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('223'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('307'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('338'));
                        break;
                    case 3:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('224'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('225'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('226'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('309'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('339'));
                        break;
                    case 4:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('227'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('228'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('229'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('311'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('340'));
                        break;
                    case 5:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('230'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('231'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('232'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('313'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('341'));
                        break;
                    case 6:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('233'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('234'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('235'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('315'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('342'));
                        break;
                    case 7:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('236'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('237'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('238'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('317'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('343'));
                        break;
                    case 8:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('239'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('240'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('241'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('319'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('344'));
                        break;
                    case 9:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('242'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('243'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('244'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('321'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('345'));
                        break;
                    case 10:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('246'));
                        $cli_ext_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('247'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('245'));
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('248'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('323'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('346'));
                        break;
                    case 11:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('249'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('250'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('251'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('325'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('347'));
                        break;
                    case 12:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('252'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('253'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('254'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('327'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('348'));
                        break;
                    case 13:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('255'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('256'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('257'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('329'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('349'));
                        break;
                    case 14:
                        $cli_nac_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('258'));
                        $cli_ext_ctas[pln_id] = '0';
                        $iva_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('259'));
                        $ven_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('260'));
                        $fle_ctas = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('331'));
                        $ctas_cob = pg_fetch_array($Clase_nota_debito->lista_asientos_ctas('350'));
                        break;
                }

                if ($cli_nac_ctas[pln_id] == '' || $cli_ext_ctas[pln_id] == '' || $ven_ctas[pln_id] == '' || $iva_ctas[pln_id] == '' || $fle_ctas[pln_id] == '' || $ctas_cob[pln_id] == '') {
                    $sms = 2;
                } else {

                    if ($Clase_nota_debito->insert_nota_debito($data, $data[3]) == TRUE) {
                        $nd = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito($comprobante));
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
                            'ND',
                            $data[3],
                            $data[5], // fecha
                            $data[5],
                            $data[20], //total
                            '4', // tipo documento
                            $data[20]
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
                            $tf12);
                        if ($Clase_nota_debito->insert_asiento_nd($dat_asi, $asient) == false) {
                            $sms = 'asientos_1' . pg_last_error();
                        } else {
                            $chq4 = pg_fetch_array($Clase_nota_debito->lista_cheques_numero($data[3], $data[0]));
                            $fd = pg_num_rows($Clase_nota_debito->listar_una_cta_comid($data[19]));
                            $fc = pg_num_rows($Clase_nota_debito->lista_pagos($data[19]));
                            if ($fd == $fc || $fc == 1 ) {
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
                                'PAGO FACTURACION',
                                '0',
                                $chq4[chq_id]
                            );
                            if ($Clase_nota_debito->insert_ctaxcobrar($cuenta1) == false) {
                                $sms = 'insert_ctasxcobrar' . pg_last_error();
                            } else {
                                $asi = $Clase_nota_debito->siguiente_asiento();
                                $asiento = array(
                                    $asi,
                                    'CUENTAS X COBRAR',
                                    $data[11], //doc
                                    $data[5], //fec
                                    $ctas_cob[pln_codigo], //con_debe
                                    $cli_nac_ctas[pln_codigo], //con_haber
                                    $data[20], // val_debe
                                    $data[20], //val_haber
                                    '0' //estado
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
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    if ($n == 0) {
                        $f = $f . "NUM_COMPROBANTE=$comprobante&";
                    } else {
                        $f = $f . strtoupper($fields[$n] . '&');
                    }
                    $n++;
                }
                $modulo = 'NOTA DE DEBITO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $comprobante) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {/////////editar
            if ($Clase_nota_debito->upd_nota_debito($data, $id) == TRUE) {
                if ($Clase_nota_debito->delete_det_nota($id) == false) {
                    $sms = 'delete' . pg_last_error();
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
                }
            } else {
                $sms = 'Insert_nota' . pg_last_error();
                $aud = 1;
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
        echo $sms;
        break;
    case 1:
        $cns = $Clase_nota_debito->lista_nota_debito_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "05"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                if ($rst[cod_punto_emision] >= 10) {
                    $ems = '0' . $rst[cod_punto_emision];
                } else {
                    $ems = '00' . $rst[cod_punto_emision];
                }

                $sec = $rst[num_secuencial];
                if ($sec >= 0 && $sec < 10) {
                    $tx = "00000000";
                } else if ($sec >= 10 && $sec < 100) {
                    $tx = "0000000";
                } else if ($sec >= 100 && $sec < 1000) {
                    $tx = "000000";
                } else if ($sec >= 1000 && $sec < 10000) {
                    $tx = "00000";
                } else if ($sec >= 10000 && $sec < 100000) {
                    $tx = "0000";
                } else if ($sec >= 100000 && $sec < 1000000) {
                    $tx = "000";
                } else if ($sec >= 1000000 && $sec < 10000000) {
                    $tx = "00";
                } else if ($sec >= 10000000 && $sec < 100000000) {
                    $tx = "0";
                } else if ($sec >= 100000000 && $sec < 1000000000) {
                    $tx = "";
                }
                $secuencial = $tx . $sec;

                $codigo = "12345678"; //Del ejemplo del SRI                    
                $tp_emison = "1"; //Emision Normal                    
                $clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison);
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
                $clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison . $digito);
                if (strlen($clave) != 49) {
                    $clave = '';
                }
                $Clase_nota_debito->upd_notdeb_clave_acceso($clave, $rst[com_id]);
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
        $sec = str_replace('-', '', trim($id));
        if ($Clase_nota_debito->delete_nota_debito($id) == FALSE) {
            $sms = pg_last_error() . 'delete1';
        }
        if ($Clase_nota_debito->delete_det_nota($sec) == FALSE) {
            $sms = pg_last_error() . 'delete2';
        }
        $modulo = 'NOTA DE DEBITO';
        $accion = 'ELIMINAR';
        if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
            $sms = "Auditoria" . pg_last_error();
        }
        break;
    case 4:
        $sms = 0;
        if ($Clase_nota_debito->upd_estado_notdeb($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
