<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_retencion.php';
$Clase_retencion = new Clase_retencion();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$nom = $_REQUEST[nom];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
$s = $_REQUEST[s];
$doc = $_REQUEST[doc];
$tp_doc = $_REQUEST[tdoc];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;
        $ctaxpag = pg_fetch_array($Clase_retencion->lista_asientos_ctas('21')); ///falta ctas x pagar
        $prov = pg_fetch_array($Clase_retencion->lista_asientos_ctas('26'));
        if (empty($id)) {
            //// proveedores
            if ($ctaxpag[pln_id] == '' || $prov[pln_id] == '') {
                $sms = 2;
            } else {
                $rst = pg_fetch_array($Clase_retencion->lista_retencion_numero($data[3]));
                if (empty($rst)) {
                    $comprobante = $data[3];
                    if (empty($data[0])) {
                        if (strlen($data[5]) < 11) {
                            $tipo = 'PN';
                            $categoria = 1;
                        } else {
                            $tipo = 'PJ';
                            $categoria = 2;
                        }

                        $rst_cod = pg_fetch_array($Clase_retencion->lista_secuencial_cliente($tipo));
                        $sec = (substr($rst_cod[cli_codigo], 2, 5) + 1);

                        if ($sec >= 0 && $sec < 10) {
                            $txt = '0000';
                        } else if ($sec >= 10 && $sec < 100) {
                            $txt = '000';
                        } else if ($sec >= 100 && $sec < 1000) {
                            $txt = '00';
                        } else if ($sec >= 1000 && $sec < 10000) {
                            $txt = '0';
                        } else {
                            $txt = '';
                        }

                        $retorno = $tipo . $txt . $sec;
                        $da = array(
                            strtoupper($data[4]),
                            strtoupper($data[5]),
                            strtoupper($data[6]),
                            strtoupper($data[10]),
                            strtoupper($data[7]),
                            $retorno,
                            $categoria
                        );
                        if ($Clase_retencion->insert_cliente($da) == false) {
                            $sms = 'Insert_cli' . pg_last_error();
                            $aud = 1;
                        }
                        $rst_cl = pg_fetch_array($Clase_retencion->lista_clientes_cedula($data[5]));
                        $cli_id = $rst_cl[cli_id];
                    } else {
                        $cli_id = $data[0];
                    }

                    if ($Clase_retencion->insert_retencion($data, $cli_id, $comprobante) == false) {
                        $sms = 'Insert_retencion' . pg_last_error();
                        $aud = 1;
                    } else {
                        //////////////inserta_detalle////
                        $rst_ret = pg_fetch_array($Clase_retencion->lista_retencion_numero($comprobante));
                        $ret_id = $rst_ret[ret_id];
                        $n = 0;
                        while ($n < count($data2)) {
                            $dt = explode('&', $data2[$n]);
                            if ($Clase_retencion->insert_det_retencion($dt, $ret_id) == false) {
                                $sms = pg_last_error() . 'insert_det_retencion';
                                $aud = 1;
                            }
                            $n++;
                        }

                        /////Inserta asiento de reg_factura///
                        $iv = pg_fetch_array($Clase_retencion->lista_asientos_ctas('27'));
                        $ic = pg_fetch_array($Clase_retencion->lista_asientos_ctas('28'));
                        $irb = pg_fetch_array($Clase_retencion->lista_asientos_ctas('29'));
                        $cdesc = pg_fetch_array($Clase_retencion->lista_asientos_ctas('30'));
                        $prop = pg_fetch_array($Clase_retencion->lista_asientos_ctas('31'));

                        $rst_rete = pg_fetch_array($Clase_retencion->lista_retencion_id($ret_id));
                        $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_rete[reg_id]));
                        if (!empty($rst_reg[con_asiento])) {
                            if (strlen(trim($rst_reg[con_asiento])) != 0 || $rst_reg[con_asiento] != '') {
                                $asiento = $rst_reg[con_asiento];
                                if ($Clase_retencion->elimina_asientos_asiento($asiento) == false) {
                                    $sms = pg_last_error();
                                }
                            }
                            $cns_sum = $Clase_retencion->lista_sum_cuentas($rst_rete[reg_id]);
                            while ($rst1 = pg_fetch_array($cns_sum)) {
                                $dat_asi_det = array(
                                    $asiento,
                                    $rst_reg[reg_concepto],
                                    $rst_reg[reg_num_documento],
                                    $rst_reg[reg_femision],
                                    $rst1[reg_codigo_cta],
                                    '',
                                    $rst1[dtot] + $rst1[ddesc],
                                    '1',
                                    '1',
                                    '5',
                                    $rst_reg[reg_id],
                                    $rst_reg[cli_id]
                                );
                                if ($Clase_retencion->insert_asientos($dat_asi_det) == false) {
                                    $sms = 'asi_det_mod1' . pg_last_error();
                                    $aud = 1;
                                }
                            }
                            $dat_asi = array(
                                $rst_reg[reg_sbt],
                                $rst_reg[reg_num_documento],
                                $rst_reg[reg_femision],
                                '',
                                $rst_reg[reg_iva12],
                                ($rst_reg[reg_total] - $rst_rete[ret_total_valor]),
                                $rst_reg[reg_ice],
                                $rst_reg[reg_irbpnr],
                                $rst_reg[reg_propina],
                                $rst_reg[reg_tdescuento],
                                '0',
                                $iv[pln_codigo],
                                $prov[pln_codigo],
                                $ic[pln_codigo],
                                $irb[pln_codigo],
                                $cdesc[pln_codigo],
                                $prop[pln_codigo],
                                $rst_reg[reg_concepto],
                                '1',
                                '5',
                                $rst_reg[reg_id],
                                $rst_reg[cli_id]
                            );
                            $result = $Clase_retencion->insert_asiento_mp($dat_asi, $asiento);
                            $rst_asien = explode('&', $result);
                            if ($rst_asien[0] == false) {
                                $sms = 'Asiento1 ' . pg_last_error();
                            }

                            /////asiento de retencion//

                            $n = 0;
                            while ($n < count($data2)) {
                                $dt = explode('&', $data2[$n]);
                                $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($dt[0]));
                                $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                                $rst_regi = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                                $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                                $dt_asi = array(
                                    $dt[6],
                                    $rst_ret[ret_numero],
                                    $rst_ret[ret_fecha_emision],
                                    $rst_cta[pln_codigo],
                                    $concepto,
                                    '1',
                                    '4',
                                    $rst_ret[ret_id],
                                    $rst_ret[cli_id]
                                );
                                if ($Clase_retencion->insert_asientos_ret($dt_asi, $rst_regi[con_asiento]) == false) {
                                    $sms = 'insert_asiento_detret_reg_fac' . pg_last_error();
                                    $aud = 1;
                                }
                                $n++;
                            }
                        } else {
                            $rst_reg[con_asiento] = 0;
                        }
//                            
                        ////inserta en control de cobros///

                        $rp = pg_fetch_array($Clase_retencion->buscar_un_pago_doc($rst_ret[reg_id]));
                        if (empty($rp)) {
                            $rp2 = pg_fetch_array($Clase_retencion->buscar_un_pago_doc1($rst_ret[reg_id]));
                            $pag_id = $rp2[pag_id];
                        } else {
                            $pag_id = $rp[pag_id];
                        }
                        $cta = array(
                            $rst_ret[reg_id], //com_id
                            $rst_ret[ret_fecha_emision], //cta_fec
                            $rst_ret[ret_total_valor], //cta_monto
                            'RETENCION', //forma de pago
                            $ctaxpag[pln_codigo], //cta_banco
                            $prov[pln_id], /// pln_id
                            $rst_ret[ret_fecha_emision], //fec_pag
                            $pag_id, //pag_id
                            $rst_ret[ret_numero], //num_doc
                            'ABONO REG_FAC. ' . $rst_reg[reg_num_documento], //cta_concepto
                            '2', //asiento
                            '0', //chq_id
                            $rst_ret[ret_id] //doc_id
                        );
                        if ($Clase_retencion->insert_ctasxpagar($cta) == false) {
                            $sms = 'ctasxpagar ' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    if ($aud == 0) {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'RETENCION';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                } else {
                    $sms = 1;
                }
            }
        } else {

            $comprobante = $data[3];
            if (empty($data[0])) {
                if (strlen($data[5]) < 11) {
                    $tipo = 'PN';
                    $categoria = 1;
                } else {
                    $tipo = 'PJ';
                    $categoria = 2;
                }

                $rst_cod = pg_fetch_array($Clase_retencion->lista_secuencial_cliente($tipo));
                $sec = (substr($rst_cod[cli_codigo], 2, 5) + 1);

                if ($sec >= 0 && $sec < 10) {
                    $txt = '0000';
                } else if ($sec >= 10 && $sec < 100) {
                    $txt = '000';
                } else if ($sec >= 100 && $sec < 1000) {
                    $txt = '00';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $txt = '0';
                } else {
                    $txt = '';
                }

                $retorno = $tipo . $txt . $sec;
                $da = array(
                    strtoupper($data[4]),
                    strtoupper($data[5]),
                    strtoupper($data[6]),
                    strtoupper($data[10]),
                    strtoupper($data[7]),
                    $retorno,
                    $categoria
                );
                if ($Clase_retencion->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $aud = 1;
                }
                $rst_cl = pg_fetch_array($Clase_retencion->lista_clientes_cedula($data[5]));
                $cli_id = $rst_cl[cli_id];
            } else {
                $cli_id = $data[0];
            }
            if ($Clase_retencion->update_retencion($data, $id, $cli_id) == false) {
                $sms = 'Insert_retencion' . pg_last_error();
                $aud = 1;
            } else {
                $rst_rete = pg_fetch_array($Clase_retencion->lista_retencion_id($id));
                $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_rete[reg_id]));
                if ($Clase_retencion->delete_ctasxpagar($id) == false) {
                    $sms = 'delete_ctasxcob' . pg_last_error();
                    $aud = 1;
                } else {
                    if ($Clase_retencion->delete_det_retencion($id) == false) {
                        $sms = 'elimina_detalle_retencion' . pg_last_error();
                        $aud = 1;
                    }
                    //////////////inserta_detalle////
                    $ret_id = $id;
                    $n = 0;
                    while ($n < count($data2)) {
                        $dt = explode('&', $data2[$n]);
                        if ($Clase_retencion->insert_det_retencion($dt, $ret_id) == false) {
                            $sms = pg_last_error() . 'insert_det_retencion';
                            $aud = 1;
                        }
                        $n++;
                    }
                    /////Inserta asiento de reg_factura///
                    $iv = pg_fetch_array($Clase_retencion->lista_asientos_ctas('27'));
                    $ic = pg_fetch_array($Clase_retencion->lista_asientos_ctas('28'));
                    $irb = pg_fetch_array($Clase_retencion->lista_asientos_ctas('29'));
                    $cdesc = pg_fetch_array($Clase_retencion->lista_asientos_ctas('30'));
                    $prop = pg_fetch_array($Clase_retencion->lista_asientos_ctas('31'));


                    if (!empty($rst_reg[con_asiento])) {
                        if (strlen(trim($rst_reg[con_asiento])) != 0 || $rst_reg[con_asiento] != '') {
                            $asiento = $rst_reg[con_asiento];
                            if ($Clase_retencion->elimina_asientos_asiento($asiento) == false) {
                                $sms = pg_last_error();
                            }
                        }
                        $cns_sum = $Clase_retencion->lista_sum_cuentas($rst_rete[reg_id]);
                        while ($rst1 = pg_fetch_array($cns_sum)) {
                            $dat_asi_det = array(
                                $asiento,
                                $rst_reg[reg_concepto],
                                $rst_reg[reg_num_documento],
                                $rst_reg[reg_femision],
                                $rst1[reg_codigo_cta],
                                '',
                                $rst1[dtot] + $rst1[ddesc],
                                '1',
                                '1',
                                '5',
                                $rst_reg[reg_id],
                                $rst_reg[cli_id]
                            );
                            if ($Clase_retencion->insert_asientos($dat_asi_det) == false) {
                                $sms = 'asi_det_mod1' . pg_last_error();
                                $aud = 1;
                            }
                        }
                        $dat_asi = array(
                            $rst_reg[reg_sbt],
                            $rst_reg[reg_num_documento],
                            $rst_reg[reg_femision],
                            '',
                            $rst_reg[reg_iva12],
                            ($rst_reg[reg_total] - $rst_rete[ret_total_valor]),
                            $rst_reg[reg_ice],
                            $rst_reg[reg_irbpnr],
                            $rst_reg[reg_propina],
                            $rst_reg[reg_tdescuento],
                            '0',
                            $iv[pln_codigo],
                            $prov[pln_codigo],
                            $ic[pln_codigo],
                            $irb[pln_codigo],
                            $cdesc[pln_codigo],
                            $prop[pln_codigo],
                            $rst_reg[reg_concepto],
                            '1',
                            '5',
                            $rst_reg[reg_id],
                            $rst_reg[cli_id]
                        );
                        $result = $Clase_retencion->insert_asiento_mp($dat_asi, $asiento);
                        $rst_asien = explode('&', $result);
                        if ($rst_asien[0] == false) {
                            $sms = 'Asiento1 ' . pg_last_error();
                        }

                        /////asiento de retencion//

                        $n = 0;
                        while ($n < count($data2)) {
                            $dt = explode('&', $data2[$n]);
                            $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($dt[0]));
                            $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                            $rst_regi = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_rete[reg_id]));
                            $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                            $dt_asi = array(
                                $dt[6],
                                $rst_rete[ret_numero],
                                $rst_rete[ret_fecha_emision],
                                $rst_cta[pln_codigo],
                                $concepto,
                                '1',
                                '4',
                                $rst_rete[ret_id],
                                $rst_rete[cli_id]
                            );
                            if ($Clase_retencion->insert_asientos_ret($dt_asi, $rst_regi[con_asiento]) == false) {
                                $sms = 'insert_asiento_detret_reg_fac' . pg_last_error();
                                $aud = 1;
                            }
                            $n++;
                        }
                    } else {
                        $rst_reg[con_asiento] = 0;
                    }
//                            
                    ////inserta en control de cobros///

                    $rp = pg_fetch_array($Clase_retencion->buscar_un_pago_doc($rst_rete[reg_id]));
                    if (empty($rp)) {
                        $rp2 = pg_fetch_array($Clase_retencion->buscar_un_pago_doc1($rst_rete[reg_id]));
                        $pag_id = $rp2[pag_id];
                    } else {
                        $pag_id = $rp[pag_id];
                    }
                    $cta = array(
                        $rst_rete[reg_id], //com_id
                        $rst_rete[ret_fecha_emision], //cta_fec
                        $rst_rete[ret_total_valor], //cta_monto
                        'RETENCION', //forma de pago
                        $ctaxpag[pln_codigo], //cta_banco
                        $prov[pln_id], /// pln_id
                        $rst_rete[ret_fecha_emision], //fec_pag
                        $pag_id, //pag_id
                        $rst_rete[ret_numero], //num_doc
                        'ABONO REG_FAC. ' . $rst_reg[reg_num_documento], //cta_concepto
                        '2', //asiento
                        '0', //chq_id
                        $rst_rete[ret_id] //doc_id
                    );
                    if ($Clase_retencion->insert_ctasxpagar($cta) == false) {
                        $sms = 'ctasxpagar ' . pg_last_error();
                        $aud = 1;
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
            $modulo = 'RETENCION';
            $accion = 'MODIFICAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[3]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms . '&' . $rst_reg[con_asiento];
        break;
    case 1:
        $sec = str_replace('-', '', trim($id));
        if ($Clase_retencion->delete_retencion($sec) == true) {
            $sms = 0;
            $n = 0;
            $f = $nom;
            $modulo = 'RETENCION';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($Clase_retencion->lista_datos_porcentaje($id));
        $rst_cuenta = pg_fetch_array($Clase_retencion->lista_cuentas_act_inac($rst[cta_id]));
        $descripcion = $rst[por_descripcion];
        $porcentaje = $rst[por_porcentage];
        $cod = $rst[por_codigo];
        $por_id = $rst[por_id] . '_' . $rst[por_siglas];
        echo $descripcion . '&' . $porcentaje . '&' . $cod . '&' . $por_id . '&' . $rst[por_siglas] . '&' . $rst[cta_id] . '&' . $rst_cuenta[pln_estado];
        break;

    case 3:
        $rst = pg_fetch_array($Clase_retencion->lista_secuencial_retencion($x));
        $rst1 = pg_num_rows($Clase_retencion->lista_secuencial_retencion($x));
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

    case 4:
        if ($s == 0) {
            $cns = $Clase_retencion->lista_buscar_clientes(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = trim($rst[cli_apellidos] . ' ' . $rst[cli_nombres] . ' ' . $rst[cli_raz_social]);
                //$ruc=string($rst[cli_ced_ruc]);
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id];
            }
            echo $sms;
        }

        break;
    case 5:
        $rst_am = pg_fetch_array($Clase_retencion->lista_configuraciones('5'));
        $ambiente = $rst_am[con_valor];
        $cns = $Clase_retencion->lista_retencion_completo();
        $codigo = "12345678";
        $tp_emison = '1';
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[ret_clave_acceso])) {
                $emis = pg_fetch_array($Clase_retencion->lista_emisor($rst[emi_id]));
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
                $fecha = date_format(date_create($rst[ret_fecha_emision]), 'd/m/Y');
                $ndoc = explode('-', $rst[ret_numero]);
                $secuencial = $ndoc[2];
                $cod_doc = "07"; //01= factura, 02=nota de credito tabla 4

                $f2 = date_format(date_create($rst[ret_fecha_emision]), 'dmY');
                $contabilidad = $emis[emi_obligado_llevar_contabilidad];
                $id_comprador = $rst_enc[ret_identificacion];
                if (strlen($id_comprador) == 13 && $id_comprador != '9999999999999') {
                    $tipo_id_comprador = "04"; //RUC 04 
                } else if (strlen($id_comprador) == 10) {
                    $tipo_id_comprador = "05"; //CEDULA 05 
                } else if ($id_comprador == '9999999999999') {
                    $tipo_id_comprador = "07"; //VENTA A CONSUMIDOR FINAL
                } else {
                    $tipo_id_comprador = "06"; // PASAPORTE 06 O IDENTIFICACION DELEXTERIOR* 08 PLACA 09            
                }
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
                $Clase_retencion->upd_retencion_clave_acceso($clave, $rst[ret_id]);
            }
        }

        break;
    case 6:
        $sms = 0;
        if ($Clase_retencion->upd_retencion_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {

            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 7:
        $sms = 0;
        $ctaxpag = pg_fetch_array($Clase_retencion->lista_asientos_ctas('25')); ///falta ctas x pagar
        $prov = pg_fetch_array($Clase_retencion->lista_asientos_ctas('26'));
        if ($ctaxpag[pln_id] == '' || $prov[pln_id] == '') {
            $sms = 2;
        } else {
            if ($Clase_retencion->upd_estado_retencion($id) == FALSE) {
                $sms = 'update_estado_retencion'.pg_last_error();
                $aud = 1;
            } else {
//                $asiento = $Clase_retencion->siguiente_asiento();
                $rst_ret = pg_fetch_array($Clase_retencion->lista_retencion_id($id));
                $rst_reg = pg_fetch_array($Clase_retencion->lista_reg_factura_id($rst_ret[reg_id]));
                $cns_det = $Clase_retencion->lista_detalle_retencion($id);

                ///// insercion asiento_factura///
                $asiento_fac = $rst_reg[con_asiento];
                if ($Clase_retencion->elimina_asientos_asiento($asiento_fac) == true) {
                    $cns_sum = $Clase_retencion->lista_sum_cuentas($rst_ret[reg_id]);
                    while ($rst1 = pg_fetch_array($cns_sum)) {
                        $dat_asi_det = array(
                            $asiento_fac,
                            $rst_reg[reg_concepto],
                            $rst_reg[reg_num_documento],
                            $rst_reg[reg_femision],
                            $rst1[reg_codigo_cta],
                            '',
                            $rst1[dtot] + $rst1[ddesc],
                            0,
                            '1',
                            '5',
                            $rst_reg[reg_id],
                            $rst_reg[cli_id]
                        );
                        if ($Clase_retencion->insert_asientos($dat_asi_det) == false) {
                            $sms = 'asi_factura' . pg_last_error();
                            $aud = 1;
                        }
                    }

                    $iv = pg_fetch_array($Clase_retencion->lista_asientos_ctas('27'));
                    $ic = pg_fetch_array($Clase_retencion->lista_asientos_ctas('28'));
                    $irb = pg_fetch_array($Clase_retencion->lista_asientos_ctas('29'));
                    $cdesc = pg_fetch_array($Clase_retencion->lista_asientos_ctas('30'));
                    $prop = pg_fetch_array($Clase_retencion->lista_asientos_ctas('31'));

                    $dat_asi = array(
                        $rst_reg[reg_sbt],
                        $rst_reg[reg_num_documento],
                        $rst_reg[reg_femision],
                        '',
                        $rst_reg[reg_iva12],
                        $rst_reg[reg_total],
                        $rst_as[reg_ice],
                        $rst_reg[reg_irbpnr],
                        $rst_reg[reg_propina],
                        $rst_reg[reg_tdescuento],
                        '0',
                        $iv[pln_codigo],
                        $prov[pln_codigo],
                        $ic[pln_codigo],
                        $irb[pln_codigo],
                        $cdesc[pln_codigo],
                        $prop[pln_codigo],
                        $rst_reg[reg_concepto],
                        '1',
                        '5',
                        $rst_reg[reg_id],
                        $rst_reg[cli_id]
                    );
                    $result = $Clase_retencion->insert_asiento_mp($dat_asi, $asiento_fac);
                    $rst_asien = explode('&', $result);
                    if ($rst_asien[0] == false) {
                        $sms = 'asiento_det_factura ' . pg_last_error();
                        $aud = 1;
                    }
                }

                /////insercion de asiento de la retencion////
                $asiento_ant = $Clase_retencion->siguiente_asiento();
                $dat_asi = array(
                    $rst_ret[ret_total_valor],
                    $rst_ret[ret_numero],
                    $rst_ret[ret_fecha_emision],
                    $prov[pln_codigo],
                    '1',
                    '4',
                    $rst_ret[ret_id],
                    $rst_ret[cli_id]
                );
                if ($Clase_retencion->insert_asiento($dat_asi, $asiento_ant) == false) {
                    $sms = 'insert_asiento_retencion' . pg_last_error();
                    $aud = 1;
                }
                $a = 0;
                while ($rst_dan = pg_fetch_array($cns_det)) {
                    $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($rst_dan[por_id]));
                    $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
                    $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                    $dt_asi = array(
                        $rst_dan[dtr_valor],
                        $rst_ret[ret_numero],
                        $rst_ret[ret_fecha_emision],
                        $rst_cta[pln_codigo],
                        $concepto,
                        '1',
                        '4',
                        $rst_ret[ret_id],
                        $rst_ret[cli_id]                        
                    );
                    if ($Clase_retencion->insert_asientos_ret($dt_asi, $asiento_ant) == false) {
                        $sms = 'insert_asiento_det_retencion' . pg_last_error();
                        $aud = 1;
                    }
                    $a++;
                }
//                ////anulacion de retencion////
                $asiento = $Clase_retencion->siguiente_asiento();
                $rst_art = pg_fetch_array($Clase_retencion->lista_asiento_doc('4', $rst_ret[ret_id], '1'));
                $con_anulacion = 'ANULACION ' . $rst_art[con_asiento];
                $dt_asi = array(
                    $rst_ret[ret_total_valor],
                    $rst_ret[ret_numero],
                    $rst_ret[ret_fecha_emision],
                    $prov[pln_codigo],
                    $con_anulacion,
                    '2',
                    '4',
                    $rst_ret[ret_id],
                    $rst_ret[cli_id]
                );
                if ($Clase_retencion->insert_asientos_ret($dt_asi, $asiento) == false) {
                    $sms = 'insert_asiento_anulacion_retencion' . pg_last_error();
                    $aud = 1;
                }
                $cns_det = $Clase_retencion->lista_detalle_retencion($id);
                while ($rst_det = pg_fetch_array($cns_det)) {
                    $rst_idcta = pg_fetch_array($Clase_retencion->lista_id_cuenta($rst_det[por_id]));
                    $rst_cta = pg_fetch_array($Clase_retencion->lista_cuenta_contable($rst_idcta[cta_id]));
//                    $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                    $dt_asi1 = array(
                        $rst_det[dtr_valor],
                        $rst_ret[ret_numero],
                        $rst_ret[ret_fecha_emision],
                        $rst_cta[pln_codigo],
                        $con_anulacion,
                        '2',
                        '4',
                        $rst_ret[ret_id],
                        $rst_ret[cli_id]
                    );
                    if ($Clase_retencion->insert_asientos_ret_anulacion($dt_asi1, $asiento) == false) {
                        $sms = 'insert_asiento_det_anulacion_retencion' . pg_last_error();
                        $aud = 1;
                    }
                }

                ////anulacion de pagos///
                if ($aud != 1) {
                    $ctp = pg_fetch_array($Clase_retencion->lista_ctasxpagar1($id));
                    if (!empty($ctp)) {
                        if ($Clase_retencion->update_ctasxpagar($ctp[ctp_id]) == false) {
                            $sms = 'update_ctasxpagar' . pg_last_error();
                            $aud = 1;
                        }
                    }
                }
                
                 if ($aud != 1) {
                    $f = 'NUMERO='.$rst_ret[ret_numero] . '&'. 'NOMBRE='.$rst_ret[ret_nombre] . '&' . 'IDENTIFICACION='.$rst_ret[ret_identificacion] . '&' . 'TOTAL='. $rst_ret[ret_total_valor] . '& &';
                    $modulo = 'RETENCION';
                    $accion = 'ANULACION';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $rst_ret[ret_numero]) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        echo $sms;
        break;
    case 8:
        if ($s == 0) {
            $cns = $Clase_retencion->lista_reg_facturas($tp_doc, $doc);
            $docu = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_cli = pg_fetch_array($Clase_retencion->lista_proveedores($rst[reg_ruc_cliente]));
                $nm = $rst_cli[cli_raz_social];
                $docu .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_documento2('$rst[reg_ruc_cliente]','$doc')" . " /></td><td>$n</td><td>$rst[reg_num_documento]</td><td>$nm</td></tr>";
            }
            echo $docu;
        } else if ($s == 1) {
            $sms;
            $rst = pg_fetch_array($Clase_retencion->lista_clientes_cedula($id));
            if (!empty($rst)) {
                $rst_id_doc = pg_fetch_array($Clase_retencion->lista_id_reg_factura($doc, $id));
                $sms = $rst[cli_ced_ruc] . '&' . trim($rst[cli_raz_social]) . '&' . $rst[cli_calle_prin] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst_id_doc[reg_id] . '&' . $rst_id_doc[reg_sbt] . '&' . $rst_id_doc[reg_iva12];
            }
            echo $sms;
        }
        break;
}
?>
