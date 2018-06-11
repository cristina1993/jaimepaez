<?php

include_once '../Clases/clsClase_registro_facturas.php';
include_once("../Clases/clsAuditoria.php");
$Reg = new Clase_registro_facturas();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$detalle = $_REQUEST[detalle];
$pagos = $_REQUEST[pagos];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$doc = $_REQUEST[doc];
$ruc = $_REQUEST[ruc];
$Adt = new Auditoria();
switch ($op) {
    case 100:
        if ($Reg->insert_asiento_mp($_REQUEST[sbt_mp]) == false) {
            $sms = 'Asiento ' . pg_last_error();
        }
        break;
    case 0:
        $sms = 0;
        $num = $data[4];
        if ($id == 0) { //Insertar
            $rst_sec = pg_fetch_array($Reg->lista_secuencial($data[22]));
            if (!empty($rst_sec)) {
                $sms = 3;
            } else {
                $ctas = pg_fetch_array($Reg->lista_asientos_ctas('26'));
                $iv = pg_fetch_array($Reg->lista_asientos_ctas('27'));
                $ic = pg_fetch_array($Reg->lista_asientos_ctas('28'));
                $irb = pg_fetch_array($Reg->lista_asientos_ctas('29'));
                $cdesc = pg_fetch_array($Reg->lista_asientos_ctas('30'));
                $prop = pg_fetch_array($Reg->lista_asientos_ctas('31'));
                if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $cdesc[pln_id] == '' || $prop[pln_id] == '') {
                    $sms = 1;
                } else {
                    $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                    if (empty($rst_cli)) {
                        if (strlen($data[21]) < 11) {
                            $tipo = 'PN';
                        } else {
                            $tipo = 'PJ';
                        }
                        $rst_cod = pg_fetch_array($Reg->lista_secuencial_cliente($tipo));
                        $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

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
                        if (trim($data[8]) == 'EXTRANJERO') {
                            $tp_clientes = 1;
                        } else {
                            $tp_clientes = 0;
                        }
                        $da = array(
                            strtoupper($data[23]),
                            strtoupper($data[21]),
                            strtoupper($data[24]),
                            strtoupper($data[25]),
                            strtolower($data[26]),
                            strtoupper($retorno),
                            $tp_clientes
                        );
                        if ($Reg->insert_cliente($da) == false) {
                            $sms = 'Insert_cli' . pg_last_error();
                        }
                    } else {
                        $da3 = array(
                            strtoupper($data[24]),
                            strtolower($data[26]),
                            strtoupper($data[25])
                        );
                        if ($Reg->upd_email_cliente($da3, $data[21]) == false) {
                            $sms = 'Update_email' . pg_last_error();
                        }
                    }
                    if ($data[28] != 0) {
                        $cli = $data[28];
                    } else {
                        $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                        $cli = $rst_cli[cli_id];
                    }
                    if ($Reg->insert_registro($data, $cli) == true) {
                        $rst_reg = pg_fetch_array($Reg->lista_registro_numero($data[22]));
                        $id = $rst_reg[reg_id];
                        $concepto_reg = $data[9];
                        foreach ($detalle as $row => $data) {
                            $det = explode('&', $data);
                            if ($det[10] == 1) {
                                $dt = array($det[8],
                                    $det[0],
                                    $det[1],
                                    $det[15],
                                    $det[16]);
                                if ($Reg->insert_producto_insumo_matpri($dt) == false) {
                                    $sms = 'Materia_Prima' . pg_last_error();
                                }
                                $rst_pro = pg_fetch_array($Reg->lista_producto_insumosotros_matpri_cod($det[12], $det[0]));
                                $pro_id = $rst_pro[id];
                            } else {
                                $pro_id = $det[11];
                            }
                            array_push($det, $pro_id);
                            array_push($det, $rst_reg[reg_id]);

                            if ($Reg->insert_detalle_registro($det) == false) {
                                $sms = 'Detalle ' . pg_last_error();
                            }
                        }
                        foreach ($pagos as $row => $data) {
                            $pag = explode('&', $data);
                            array_push($pag, $rst_reg[reg_id]);
                            if ($Reg->insert_pagos_registro($pag) == false) {
                                $sms = 'Pagos ' . pg_last_error();
                            }
                        }

                        ///// si no existen todos los datos no se realizan asientos////
                        if ($_REQUEST[vd] != 1) {
                            $asiento = $Reg->siguiente_asiento();
                            $cns_sum = $Reg->lista_sum_cuentas($rst_reg[reg_id]);
                            while ($rst1 = pg_fetch_array($cns_sum)) {
                                $dat_asi_det = array(
                                    $asiento,
                                    $concepto_reg,
                                    $rst_reg[reg_num_documento],
                                    $rst_reg[reg_femision],
                                    $rst1[reg_codigo_cta],
                                    $rst1[dtot] + $rst1[ddesc],
                                    '1',
                                    '5',
                                    $rst_reg[reg_id],
                                    $cli
                                );
                                if ($Reg->insert_asiento_det($dat_asi_det) == false) {
                                    $sms = 'asi_det' . pg_last_error();
                                    $aud = 1;
                                }
                            }

                            $dat_asi = array(
                                $rst_reg[reg_sbt],
                                $rst_reg[reg_num_documento],
                                $rst_reg[reg_femision],
                                '',
                                $rst_reg[reg_iva12],
                                $rst_reg[reg_total],
                                $rst_reg[reg_ice],
                                $rst_reg[reg_irbpnr],
                                $rst_reg[reg_propina],
                                $rst_reg[reg_tdescuento],
                                '0',
                                $iv[pln_codigo],
                                $ctas[pln_codigo],
                                $ic[pln_codigo],
                                $irb[pln_codigo],
                                $cdesc[pln_codigo],
                                $prop[pln_codigo],
                                $concepto_reg,
                                '1',
                                '5',
                                $rst_reg[reg_id],
                                $cli
                            );
                            $result = $Reg->insert_asiento_mp($dat_asi, $asiento);
                            $rst_asien = explode('&', $result);

                            if ($rst_asien[0] == false) {
                                $sms = 'Asiento ' . pg_last_error();
                            } else {
                                if ($Reg->upd_num_asiento($rst_asien[1], $rst_reg[reg_id]) == false) {
                                    $sms = pg_last_error();
                                }
                            }
                        } else {
                            $rst_asien[1] = 0;
                        }

                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'REGISTRO DOCUMENTOS';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    } else {
                        $sms = "Reg " . pg_last_error();
                    }

                    $brt = pg_fetch_array($Reg->buscar_retencion($rst_reg[reg_num_documento], $rst_reg[reg_ruc_cliente]));
                    if (!empty($brt)) {
                        $sms = '2';
                    }
                }
            }
        } else { //Modificar
            $n_doc = $data[4];
            $t_doc = $data[3];
            $concepto_reg = $data[9];
            $ctas = pg_fetch_array($Reg->lista_asientos_ctas('26'));
            $iv = pg_fetch_array($Reg->lista_asientos_ctas('27'));
            $ic = pg_fetch_array($Reg->lista_asientos_ctas('28'));
            $irb = pg_fetch_array($Reg->lista_asientos_ctas('29'));
            $cdesc = pg_fetch_array($Reg->lista_asientos_ctas('30'));
            $prop = pg_fetch_array($Reg->lista_asientos_ctas('31'));
            if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $cdesc[pln_id] == '' || $prop[pln_id] == '') {
                $sms = 1;
            } else {
                $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                if (empty($rst_cli)) {
                    if (strlen($data[21]) < 11) {
                        $tipo = 'PN';
                    } else {
                        $tipo = 'PJ';
                    }
                    $rst_cod = pg_fetch_array($Reg->lista_secuencial_cliente($tipo));
                    $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

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
                    if (trim($data[8]) == 'EXTRANJERO') {
                        $tp_clientes = 1;
                    } else {
                        $tp_clientes = 0;
                    }
                    $da = array(
                        strtoupper($data[23]),
                        strtoupper($data[21]),
                        strtoupper($data[24]),
                        strtoupper($data[25]),
                        strtolower($data[26]),
                        strtoupper($retorno),
                        $tp_clientes
                    );
                    if ($Reg->insert_cliente($da) == false) {
                        $sms = 'Insert_cli' . pg_last_error();
                    }
                } else {
                    $da3 = array(
                        strtoupper($data[24]),
                        strtolower($data[26]),
                        strtoupper($data[25])
                    );
                    if ($Reg->upd_email_cliente($da3, $data[21]) == false) {
                        $sms = 'Update_email' . pg_last_error();
                    }
                }
                if ($data[28] != 0) {
                    $cli = $data[28];
                } else {
                    $rst_cli = pg_fetch_array($Reg->lista_cliente_ruc($data[21]));
                    $cli = $rst_cli[cli_id];
                }
                if ($Reg->upd_registro($data, $id, $cli) == true) {
                    if ($Reg->elimina_detalle_pagos($id) == true) {
                        foreach ($detalle as $row => $data) {
                            $det = explode('&', $data);
                            if ($det[10] == 1) {
                                $dt = array($det[8],
                                    $det[0],
                                    $det[1],
                                    $det[15],
                                    $det[16]);
                                if ($Reg->insert_producto_insumo_matpri($dt) == false) {
                                    $sms = 'Upd_Materia_Prima' . pg_last_error();
                                }
                                $rst_pro = pg_fetch_array($Reg->lista_producto_insumosotros_matpri_cod($det[12], $det[0]));
                                $pro_id = $rst_pro[id];
                            } else {
                                $pro_id = $det[11];
                            }
                            array_push($det, $pro_id);
                            array_push($det, $id);
                            if ($Reg->insert_detalle_registro($det) == false) {
                                $sms = 'Detalle1 ' . pg_last_error();
                            }
                        }
                        foreach ($pagos as $row => $data) {
                            $pag = explode('&', $data);
                            array_push($pag, $id);
                            if ($Reg->insert_pagos_registro($pag) == false) {
                                $sms = 'Pagos1 ' . pg_last_error();
                            }
                        }
                    }

                    ///////SOLO SI LOS DATOS ESTAN COMPLETOS////
                    if ($_REQUEST[vd] != 1) {
                        $rst_as = pg_fetch_array($Reg->lista_un_registro($id));
                        if (strlen(trim($rst_as[con_asiento])) != 0 || $rst_as[con_asiento] != '') {
                            $asiento = $rst_as[con_asiento];
                            if ($Reg->elimina_asientos_asiento($asiento) == false) {
                                $sms = pg_last_error();
                            }
                        } else {
                            $asiento = $Reg->siguiente_asiento();
                            if ($Reg->upd_num_asiento($asiento, $id) == false) {
                                $sms = pg_last_error();
                            }
                        }

                        if ($_REQUEST[vd] != 1) {
                            $cns_sum = $Reg->lista_sum_cuentas($rst_as[reg_id]);
                            while ($rst1 = pg_fetch_array($cns_sum)) {
                                $dat_asi_det = array(
                                    $asiento,
                                    $concepto_reg,
                                    $rst_as[reg_num_documento],
                                    $rst_as[reg_femision],
                                    $rst1[reg_codigo_cta],
                                    $rst1[dtot] + $rst1[ddesc],
                                    '1',
                                    '5',
                                    $rst_as[reg_id],
                                    $cli
                                );
                                if ($Reg->insert_asiento_det($dat_asi_det) == false) {
                                    $sms = 'asi_det_mod1' . pg_last_error();
                                }
                            }
                            if ($_REQUEST[estd] == 5) {
                                $rst_rete = pg_fetch_array($Reg->lista_retencion($id));
                                $total = $rst_as[reg_total] - $rst_rete[ret_total_valor];
                            } else {
                                $total = $rst_as[reg_total];
                            }

                            $dat_asi = array(
                                $rst_as[reg_sbt],
                                $rst_as[reg_num_documento],
                                $rst_as[reg_femision],
                                '',
                                $rst_as[reg_iva12],
                                $total,
                                $rst_as[reg_ice],
                                $rst_as[reg_irbpnr],
                                $rst_as[reg_propina],
                                $rst_as[reg_tdescuento],
                                '0',
                                $iv[pln_codigo],
                                $ctas[pln_codigo],
                                $ic[pln_codigo],
                                $irb[pln_codigo],
                                $cdesc[pln_codigo],
                                $prop[pln_codigo],
                                $concepto_reg,
                                '1',
                                '5',
                                $rst_as[reg_id],
                                $cli
                            );
                            $result = $Reg->insert_asiento_mp($dat_asi, $asiento);
                            $rst_asien = explode('&', $result);
                            if ($rst_asien[0] == false) {
                                $sms = 'Asiento1 ' . pg_last_error();
                            }
                            /////inserta asiento de retencion en Reg. factura cuando existe retencion///
                            if ($_REQUEST[estd] == 5) {
                                $rst_rete = pg_fetch_array($Reg->lista_retencion($id));
                                $cns_dt_ret = $Reg->lista_detalle_retencion($rst_rete[ret_id]);
                                while ($rst_drt = pg_fetch_array($cns_dt_ret)) {
                                    $rst_idcta = pg_fetch_array($Reg->lista_id_cuenta($rst_drt[por_id]));
                                    $rst_cta = pg_fetch_array($Reg->lista_cuenta_contable($rst_idcta[cta_id]));
                                    $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                                    $dt_asi = array(
                                        $rst_drt[dtr_valor],
                                        $rst_rete[ret_numero],
                                        $rst_rete[ret_fecha_emision],
                                        $rst_cta[pln_codigo],
                                        $concepto,
                                        '1',
                                        '4',
                                        $rst_rete[ret_id],
                                        $cli
                                    );
                                    if ($Reg->insert_asientos_ret($dt_asi, $asiento) == false) {
                                        $sms = 'insert_asiento_detret_reg_fac' . pg_last_error();
                                        $aud = 1;
                                    }
                                }
                            }
                        } else {
                            $rst_asien[1] = $asiento;
                        }
                    } else {
                        $rst_asien[1] = 0;
                    }
                    ///////Movimiento de ajustes/////
                    if ($_REQUEST[estd] == 2) {
                        $rst_fac = pg_fetch_array($Reg->lista_un_registro($id));
                        $rst = pg_fetch_array($Reg->lista_secuencial_mov());
                        if (empty($rst)) {
                            $sec = 1;
                        } else {
                            $sc = explode('-', $rst[mov_documento]);
                            $sec = ($sc[1] + 1);
                        }
                        if ($sec >= 0 && $sec < 10) {
                            $txt = '000000000';
                        } else if ($sec >= 10 && $sec < 100) {
                            $txt = '00000000';
                        } else if ($sec >= 100 && $sec < 1000) {
                            $txt = '0000000';
                        } else if ($sec >= 1000 && $sec < 10000) {
                            $txt = '000000';
                        } else if ($sec >= 10000 && $sec < 100000) {
                            $txt = '00000';
                        } else if ($sec >= 100000 && $sec < 1000000) {
                            $txt = '0000';
                        } else if ($sec >= 1000000 && $sec < 10000000) {
                            $txt = '000';
                        } else if ($sec >= 10000000 && $sec < 100000000) {
                            $txt = '00';
                        } else if ($sec >= 100000000 && $sec < 1000000000) {
                            $txt = '0';
                        } else if ($sec >= 1000000000 && $sec < 10000000000) {
                            $txt = '';
                        }

                        $secuencial = '001-' . $txt . $sec;
                        $det_mv = array();
                        $det_mv2 = array();
                        $v = 0;
                        foreach ($detalle as $row => $deta) {
                            $det = explode('&', $deta);
                            $rst_pro = pg_fetch_array($Reg->lista_producto_cod($det[0]));
                            $rst_mov = pg_fetch_array($Reg->lista_un_movimiento_fac_pro($rst_fac[reg_num_documento], $rst_fac[reg_num_ingreso], $rst_pro[id]));
                            if ($rst_pro[tbl] != 79 && $rst_pro[tbl] != 80) {
                                if (empty($rst_mov)) {
                                    $v = 1;
                                    $dat_mov = (
                                            $rst_pro[id] . '&' .
                                            '14' . '&' .
                                            $cli . '&' .
                                            '1' . '&' .
                                            $secuencial . '&' .
                                            'MODIFICA REG.FACTURA ' . $n_doc . '&' .
                                            date('Y-m-d') . '&' .
                                            $det[2] . '&' .
                                            '0' . '&' .
                                            $det[3] . '&' .
                                            ($det[2] * $det[3]) . '&' .
                                            $n_doc . '&' .
                                            $t_doc
                                            );
                                    $dat_mov2 = '';
                                } else if (!empty($rst_mov) && (round($rst_mov[mov_cantidad], 2) != round($det[2], 2) || round($rst_mov[mov_val_unit], 2) != round($det[3], 2))) {

                                    $v = 1;
                                    $dat_mov = (
                                            $rst_pro[id] . '&' .
                                            '14' . '&' .
                                            $cli . '&' .
                                            '1' . '&' .
                                            $secuencial . '&' .
                                            'MODIFICA REG.FACTURA ' . $n_doc . '&' .
                                            date('Y-m-d') . '&' .
                                            $det[2] . '&' .
                                            '0' . '&' .
                                            $det[3] . '&' .
                                            ($det[2] * $det[3]) . '&' .
                                            $n_doc . '&' .
                                            $t_doc
                                            );

                                    $dat_mov2 = (
                                            $rst_mov[pro_id] . '&' .
                                            '21' . '&' .
                                            $rst_mov[cli_id] . '&' .
                                            '1' . '&' .
                                            $secuencial . '&' .
                                            'MODIFICA REG.FACTURA ' . $n_doc . '&' .
                                            date('Y-m-d') . '&' .
                                            $rst_mov[mov_cantidad] . '&' .
                                            '0' . '&' .
                                            $rst_mov[mov_val_unit] . '&' .
                                            $rst_mov[mov_val_tot] . '&' .
                                            $n_doc . '&' .
                                            $t_doc
                                            );
                                } else {
                                    $dat_mov = '';
                                    $dat_mov2 = '';
                                }
                            } else {
                                $dat_mov = '';
                                $dat_mov2 = '';
                            }
                            array_push($det_mv, $dat_mov);
                            array_push($det_mv2, $dat_mov2);
                        }
                        if ($v == 1) {
                            $k = 0;
                            while ($k < count($det_mv2)) {
                                $dt_n = explode('&', $det_mv2[$k]);
                                if ($dt_n[0] != null) {
                                    if (!$Reg->insert_transferencia($dt_n)) {
                                        $sms = pg_last_error();
                                    }
                                }
                                $k++;
                            }
                            $j = 0;
                            while ($j < count($det_mv)) {
                                $dt_m = explode('&', $det_mv[$j]);
                                if ($dt_m[0] != null) {
                                    if (!$Reg->insert_transferencia($dt_m)) {
                                        $sms = pg_last_error();
                                    }
                                }
                                $j++;
                            }
                        }
                    }


                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'REGISTRO DOCUMENTOS';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $num) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                } else {
                    $sms = pg_last_error();
                }
            }
        }
        echo $sms . '&' . $id . '&' . $rst_asien[1];
        break;
    case 1:
        $sms = 0;
        if ($Reg->elimina_registro_detalle_pagos_id($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'REGISTRO DOCUMENTOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        if ($doc == 1) {
            $rst_cod = pg_fetch_array($Reg->lista_producto_cod($id));
            if (empty($rst_cod)) {
                $ext = 0;
            } else {
                $ext = 1;
            }
        }
        $rst = pg_fetch_array($Reg->lista_producto_insumosotros_matpri_id($_REQUEST[tbl], $id));
        $rst_ant = pg_fetch_array($Reg->lista_producto_ant($id));
        if (empty($rst_ant)) {
            $rst_ant[pln_id] = '0';
        }
        echo $rst[tbl] . '&' . $rst[cod] . '&' . $rst[dsc] . '&' . $id . '&' . $rst_ant[pln_id] . '&' . $rst_ant[reg_codigo_cta] . '&' . $rst[mp_a] . '&' . $rst[mp_b] . '&' . $ext;
        break;
    case 3:
        $rst = pg_fetch_array($Reg->lista_ultimo_codigo_pro($id));

        if (empty($rst)) {
            switch ($id) {
                case '27':
                    $prf = 'IO';
                    break;
                case '26':
                    $prf = 'V';
                    break;
                case '69':
                    $prf = 'MP';
                    break;
                default :
                    $prf = '';
                    break;
            }
            $prf = $prf;
            $secuencial = '0001';
        } else {
            if ($id == 26) {
                $prf = substr($rst[cod], 0, 1);
                $secu = substr($rst[cod], -4);
                $secuen = ltrim($secu, '0');
                $sec = $secuen + 1;
            } else {
                $prf = substr($rst[cod], 0, 2);
                $secu = substr($rst[cod], -4);
                $secuen = ltrim($secu, '0');
                $sec = $secuen + 1;
            }
            if ($sec < 10) {
                $txt = '000';
            } else if ($sec >= 10 & $sec < 100) {
                $txt = '00';
            } else if ($sec >= 100 & $sec < 1000) {
                $txt = '0';
            } else {
                $txt = '';
            }
            $secuencial = $txt . $sec;
        }
        echo $prf . '&' . $secuencial;
        break;

    case 4:
        $rst = pg_fetch_array($Reg->lista_un_registro_factura($doc, $ruc, $data));
        echo $rst[reg_num_documento] . '&' . $rst[reg_ruc_cliente] . '&' . $rst[reg_estado];
        break;
    case 5:
        $sms = 0;
        $ctas = pg_fetch_array($Reg->lista_asientos_ctas('32'));
        $iv = pg_fetch_array($Reg->lista_asientos_ctas('33'));
        $ic = pg_fetch_array($Reg->lista_asientos_ctas('34'));
        $irb = pg_fetch_array($Reg->lista_asientos_ctas('35'));
        $desc = pg_fetch_array($Reg->lista_asientos_ctas('36'));
        $prop = pg_fetch_array($Reg->lista_asientos_ctas('37'));
        if ($iv[pln_id] == '' || $ctas[pln_id] == '' || $ic[pln_id] == '' || $irb[pln_id] == '' || $desc[pln_id] == '' || $prop[pln_id] == '') {
            $sms = 2;
        } else {
            $rst_fc = pg_fetch_array($Reg->lista_un_registro($_REQUEST[md_id]));
            $rst_nc = pg_fetch_array($Reg->lista_una_nota_cred($_REQUEST[md_id]));
            $rst_nd = pg_fetch_array($Reg->lista_una_nota_deb($_REQUEST[md_id]));
            $rst_rt = pg_fetch_array($Reg->lista_retencion($_REQUEST[md_id]));
            if (empty($rst_nc) && empty($rst_nd) && empty($rst_rt)) {
                if (empty($rst_fc[reg_femision])) {
                    $rst_fc[reg_femision] = date('Y-m-d');
                }
                $v = 0;
                $rst_mov = pg_fetch_array($Reg->lista_un_movimiento_fac($rst_fc[reg_num_documento], $rst_fc[reg_num_ingreso]));
                if (!empty($rst_mov)) {
                    $cns_rg = $Reg->lista_todo_registro($_REQUEST[md_id]);
                    while ($rst_rgt = pg_fetch_array($cns_rg)) {
                        if ($rst_rgt[ids] != 79 && $rst_rgt[ids] != 80) {
                            $fra = "and m.bod_id=$rst_mov[bod_id]";
                            $rst1 = pg_fetch_array($Reg->total_ingreso_egreso_fact($rst_rgt[id], $fra));
                            $inv = $rst1[ingreso] - $rst1[egreso];
                            $vin = $inv - $rst_rgt[det_cantidad];
                            if ($vin < 0) {
                                $v = 1;
                            }
                        }
                    }
                }

                if ($v == 0) {
                    if ($Reg->update_estado_reg_factura($_REQUEST[md_id], $_REQUEST[estado], $rst_fc[reg_femision]) == true) {
                        if ($Reg->update_estado_det_factura($_REQUEST[md_id], $_REQUEST[estado]) == false) {
                            $sms = 'Update_reg_encab_regfac' . pg_last_error();
                        } else {
                            $rst_mov = pg_fetch_array($Reg->lista_un_movimiento_fac($rst_fc[reg_num_documento], $rst_fc[reg_num_ingreso]));
                            if (!empty($rst_mov)) {
                                $cns = $Reg->lista_todo_registro($_REQUEST[md_id]);
                                while ($rst_reg = pg_fetch_array($cns)) {
                                    if ($rst_reg[ids] != 79 && $rst_reg[ids] != 80) {
                                        $rst_c = pg_fetch_array($Reg->ultimo_costo($rst_reg[id], "and m.bod_id=$rst_mov[bod_id]"));
                                        $rst_c[mov_val_unit] = (($rst_c[ingreso] - $rst_c[egreso]) / ($rst_c[icnt] - $rst_c[ecnt]));
                                        $mov = array(
                                            $rst_reg[id],
                                            '27',
                                            $rst_reg[cli_id],
                                            $rst_mov[bod_id],
                                            $rst_reg[reg_num_registro],
                                            date('Y-m-d'),
                                            $rst_reg[det_cantidad],
                                            '0',
                                            round($rst_c[mov_val_unit], 4),
                                            round($rst_c[mov_val_unit] * $rst_reg[det_cantidad], 4)
                                        );
                                        if ($Reg->insert_movimiento($mov) == false) {
                                            $sms = 'Movimientos ' . pg_last_error();
                                            $aud = 1;
                                        }
                                    }
                                }
                            }
                            /////asiento de anulacion /////
                            if (!empty($rst_fc[con_asiento])) {
                                $asiento = $Reg->siguiente_asiento();
                                $cns_asiento = $Reg->lista_un_asiento($rst_fc[con_asiento]);

                                while ($rst_an = pg_fetch_array($cns_asiento)) {
                                    $dat_asi = array(
                                        $asiento,
                                        'ANULACION ' . $rst_an[con_asiento],
                                        $rst_an[con_documento],
                                        date('Y-m-d'),
                                        $rst_an[con_concepto_haber],
                                        $rst_an[con_concepto_debe],
                                        $rst_an[con_valor_haber],
                                        $rst_an[con_valor_debe],
                                        '2',
                                        '5',
                                        $rst_fc[reg_id],
                                        $rst_fc[cli_id]
                                    );
                                    $result = $Reg->insert_asiento_anulacion($dat_asi, $asiento);
                                }
                                if ($Reg->update_asiento_anulacion($asiento, $rst_fc[reg_id]) == false) {
                                    $sms = 'Update_asi_anulacion' . pg_last_error();
                                }
                            }
                        }
                    } else {
                        $sms = 'Update_reg_encab_regfac' . pg_last_error();
                    }
                } else {
                    $sms = '3';
                }
            } else {
                if (!empty($rst_nc)) {
                    $sms = '1';
                } else if (!empty($rst_nd)) {
                    $sms = '4';
                } else if (!empty($rst_rt)) {
                    $sms = '5';
                }
            }
        }
        echo $sms;
        break;

    case 6:
        $cta = pg_fetch_array($Reg->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;

    case 7:
        $rst = pg_fetch_array($Reg->lista_encabezdo_ant($id));
        if ($rst[reg_fcaducidad] == '1900-01-01') {
            $rst[reg_fcaducidad] = '';
        }
        if ($rst[reg_fautorizacion] == '1900-01-01') {
            $rst[reg_fautorizacion] = '';
        }

        echo $rst[reg_sustento] . '&' . $rst[reg_num_autorizacion] . '&' . $rst[reg_fautorizacion] . '&' . $rst[reg_fcaducidad] . '&' . $rst[reg_tpcliente] . '&' . $rst[reg_concepto];
        break;

    case 8:
///// validar detalle contra ingreso de inventario
        $rst = pg_fetch_array($Reg->lista_un_registro($id));
        $v = 0;
        $inventario = 0;
        foreach ($data as $row => $detalle) {
            $det = explode('&', $detalle);
            $rst_pro = pg_fetch_array($Reg->lista_producto_cod($det[0]));
            $rst_mov = pg_fetch_array($Reg->lista_un_movimiento_fac_pro($rst[reg_num_documento], $rst[reg_num_ingreso], $rst_pro[id]));
            $fra = "and m.bod_id=$rst_mov[bod_id]";
            $rst_inv = pg_fetch_array($Reg->total_ingreso_egreso_fact($rst_pro[id], $fra));
            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
            if (!empty($rst_mov) && (round($rst_mov[mov_cantidad], 2) != round($det[1], 2) || round($rst_mov[mov_val_unit], 2) != round($det[2], 2))) {
                $val = $rst_mov[mov_cantidad];
                if ((round($rst_mov[mov_cantidad], 2) != round($det[1], 2))) {////aumeto de linea para no validar inventario
                    if ($val > $inv && $rst_pro[ids] != 79 && $rst_pro[ids] != 80) {
                        $inventario = 1;
                    }
                }
            }
            if (empty($rst_mov) || round($rst_mov[mov_cantidad], 2) != round($det[1], 2) || $rst_mov[pro_id] != $rst_pro[id] || round($rst_mov[mov_val_unit], 2) != round($det[2], 2)) {
                $v = 1;
            }
        }
        echo $v . '&' . $inventario;
        break;
    case 9:
        $reg = pg_fetch_array($Reg->lista_un_registro($data[13]));
        if ($Reg->insert_retencion($data, $reg[cli_id]) == false) {
            $sms = 'Insert_retencion' . pg_last_error();
            $aud = 1;
        } else {
            $ret = pg_fetch_array($Reg->lista_retencion_factura($data[13]));
            if ($Reg->insert_det_retencion($detalle, $ret[ret_id]) == false) {
                $sms = 'Insert_det_retencion' . pg_last_error();
                $aud = 1;
            }
        }
        echo $sms;
        break;
   
}
?>
