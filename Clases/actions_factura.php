<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
include_once '../Clases/clsClase_pagos.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Clase_pagos = new Clase_pagos();
$Set = new Clase_factura();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$data4 = $_REQUEST[data3];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$x = $_REQUEST[x];
$emi = $_REQUEST[emi];
$ctr_inv = $_REQUEST[ctr_inv];
switch ($op) {
    case 0:
        if ($s == 0) {
            $cns = $Set->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[cli_tipo_cliente] . '&' . $rst[cli_estado];
            }
            echo $sms;
        }

        break;

    case 1:
        $v = $_REQUEST[v];
        if ($v == 1) {
            $rst = pg_fetch_array($Set->lista_un_producto_codigo($id));
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto_id($id));
        }
        
        if ($rst[id] != '') {
//            $rst_precio = $Set->lista_precio_producto($id, $emi);
            if ($x == 0) {
                if ($ctr_inv == 0) {
                    $fra = '';
                } else {
                    $fra = "and m.bod_id=$emi";
                }
                $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fact($rst[id], $fra));
                $inv = $rst1[ingreso] - $rst1[egreso];
                $rst2 = pg_fetch_array($Set->lista_costos_mov($rst[id]));
            }
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst_precio[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst2[mov_val_unit] . '&' . $rst[mp_j] . '&' . $rst[mp_k]. '&' . $rst[mp_l];
        }

        break;

    case 2:
        $sms = 0;
        $aud = 0;
        if (empty($id)) {// Insertar
            if (!empty($data[1])) {
                $data3 = array(
                    strtoupper($data[9]),
                    strtoupper($data[8]),
                    strtoupper($data[20]),
                    strtoupper($data[22]),
                    strtoupper($data[23]),
                    strtoupper($data[24])
                );
                if ($Set->upd_email_cliente($data3, $data[7]) == false) {
                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                }
                $cli = $data[1];
            } else {
                if (strlen($data[7]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
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

                $da = array(
                    strtoupper($data[6]),
                    strtoupper($data[7]),
                    strtoupper($data[9]),
                    strtoupper($data[20]),
                    strtoupper($data[8]),
                    strtoupper($data[23]),
                    strtoupper($data[25]),
                    $retorno,
                    strtoupper($data[24])
                );
                if ($Set->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $aud = 1;
                }
                $rst_cl = pg_fetch_array($Set->lista_clientes_codigo($data[7]));
                $cli = $rst_cl[cli_id];
            }
////pedido venta cambio de estado
//            if ($data[3] == 0) {
//                $estp = '4';
//            } else {
//                $estp = '3';
//            }
//            if ($Set->lista_cambia_status($data[30], $estp) == false) {
//                $sms = pg_last_error();
//            }


            if ($Set->insert_factura($data, $cli) == false) {
                $sms = 'Insert' . pg_last_error();
                $accion = 'Insertar';
                $aud = 1;
            } else {
                $rst_fac = pg_fetch_array($Set->lista_una_factura_num($data[5]));
                $fac_id = $rst_fac[fac_id];

                $dt1 = explode('&', $data4[0]);
                $m = 0;
                $h = count($data4);
                while ($m < $h) {
                    $dt1 = explode('&', $data4[$m]);
                    $pg = 0;
                    if ($dt1[1] == '9') {
                        $nf = strtotime("+$dt1[5] day", strtotime($data[4]));
                        $fec = date('Y-m-j', $nf);
                    } else {
                        $fec = $data[4];
                    }
                    $data5 = array(
                        $fac_id, //com_id
                        $pg,
                        0,
                        0,
                        0,
                        $fec,
                        $dt1[1],
                        $dt1[2],
                        $dt1[3],
                        $dt1[4],
                        $dt1[5]
                    );
                    if ($dt1[1] != 0) {
                        if ($Clase_pagos->insert_pagos($data5) == false) {
                            $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                            $aud = 1;
                        }
                    }
                    $m++;
                }

                $n = 0;
                $i = count($data2);
                while ($n < $i) {
                    $dt = explode('&', $data2[$n]);
                    if ($Set->insert_detalle_factura($dt, $fac_id) == false) {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    } else {
                        if ($x == 0) {
                            $bod = $data[0];
                            $dat = array(
                                $dt[0],
                                25,
                                $cli,
                                $bod, ///BODEGA
                                $data[5],
                                '0',
                                '0',
                                date('Y-m-d'),
                                date('Y-m-d'),
                                date('H:i:s'),
                                $dt[3], //cantidad
                                '',
                                date('Y-m-d'),
                                $data[5],
                                '',
                                '',
                                $dt[11],
                                0,
                                0,
                                0,
                                0,
                                $dt[12]
                            );

                            if ($Set->insert_movimiento_pt($dat) == false) {
                                $sms = 'Insert_mov' . pg_last_error();
                                $aud = 1;
                            }
                        }
                    }
                    $n++;
                }
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            if ($x == 0) {
                if ($Set->elimina_movpt_documento($data[5]) == false) {
                    $sms = 'del' . pg_last_error();
                    $aud = 1;
                }
            }

            if ($Set->elimina_detalle_factura($id) == true) {
                if ($Clase_pagos->delete_pagos($id) == false) {
                    $sms = 'Delete_pagos1' . pg_last_error();
                    $aud = 1;
                } else {
                    if ($Set->elimina_factura($id) == false) {
                        $sms = 'del' . pg_last_error();
                        $aud = 1;
                    }
                }
            } else {
                $sms = 'del_det' . pg_last_error();
                $aud = 1;
            }


            if (!empty($data[1])) {
                $data3 = array(
                    strtoupper($data[9]),
                    strtoupper($data[8]),
                    strtoupper($data[20]),
                    strtoupper($data[22]),
                    strtoupper($data[23]),
                    strtoupper($data[24])
                );
                if ($Set->upd_email_cliente($data3, $data[7]) == false) {
                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                }
                $cli = $data[1];
            } else {
                if (strlen($data[7]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Set->lista_secuencial_cliente($tipo));
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

                $da = array(
                    strtoupper($data[6]),
                    strtoupper($data[7]),
                    strtoupper($data[9]),
                    strtoupper($data[20]),
                    strtoupper($data[8]),
                    strtoupper($data[23]),
                    strtoupper($data[25]),
                    $retorno,
                    strtoupper($data[24])
                );
                if ($Set->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $aud = 1;
                }
                $rst_cl = pg_fetch_array($Set->lista_clientes_codigo($data[7]));
                $cli = $rst_cl[cli_id];
            }

            if ($Set->insert_factura($data, $cli) == false) {
                $sms = 'Insert' . pg_last_error();
                $accion = 'Insertar';
                $aud = 1;
            } else {
                $rst_fac = pg_fetch_array($Set->lista_una_factura_num($data[5]));
                $fac_id = $rst_fac[fac_id];

                $dt1 = explode('&', $data4[0]);
                $m = 0;
                $h = count($data4);
                while ($m < $h) {
                    $dt1 = explode('&', $data4[$m]);
                    $pg = 0;
                    if ($dt1[1] == '9') {
                        $nf = strtotime("+$dt1[5] day", strtotime($data[4]));
                        $fec = date('Y-m-j', $nf);
                    } else {
                        $fec = $data[4];
                    }
                    $data5 = array(
                        $fac_id, //com_id
                        $pg,
                        0,
                        0,
                        0,
                        $fec,
                        $dt1[1],
                        $dt1[2],
                        $dt1[3],
                        $dt1[4],
                        $dt1[5]
                    );
                    if ($dt1[1] != 0) {
                        if ($Clase_pagos->insert_pagos($data5) == false) {
                            $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                            $aud = 1;
                        }
                    }
                    $m++;
                }

                $n = 0;
                $i = count($data2);
                while ($n < $i) {
                    $dt = explode('&', $data2[$n]);
                    if ($Set->insert_detalle_factura($dt, $fac_id) == false) {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    } else {
                        $bod = $data[0];
                        $dat = array(
                            $dt[0],
                            25,
                            $cli,
                            $bod, ///BODEGA
                            $data[5],
                            '0',
                            '0',
                            date('Y-m-d'),
                            date('Y-m-d'),
                            date('H:i:s'),
                            $dt[3], //cantidad
                            '',
                            date('Y-m-d'),
                            $data[5],
                            '',
                            '',
                            $dt[11],
                            0,
                            0,
                            0,
                            0,
                            $dt[12]
                        );

                        if ($Set->insert_movimiento_pt($dat) == false) {
                            $sms = 'Insert_mov' . pg_last_error();
                            $aud = 1;
                        }
                    }
                    $n++;
                }
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[5]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        $rst_com = pg_fetch_array($Set->lista_una_factura_id($fac_id));
        echo $sms . '&' . $rst_com[fac_id] . '&' . $mesaje;
        break;










//    case 0:
//        if (empty($id)) {
//            if ($Clase_factura->insert($data) == true) {
//                $sms = 0;
//            } else {
//                $sms = pg_last_error();
//            }
//        } else {
//            if ($Clase_factura->upd($data, $id) == true) {
//                $sms = 0;
//            } else {
//                $sms = pg_last_error();
//            }
//        }
//        echo $sms;
//        break;
//    case 1:
//        if ($Clase_factura->delete($id) == true) {
//            $sms = 0;
//        } else {
//            $sms = pg_last_error();
//        }
//        echo $sms;
//        break;
//    case 2:
//        $rst=  pg_fetch_array($Clase_factura->lista_secuencial($id));
//        $sec=  (substr($rst[pro_codigo],-5)+1);
//        if($sec>=0 && $sec<10){
//            $txt='0000';
//        }else if($sec>=10 && $sec<100){
//            $txt='000';
//        }else if($sec>=100 && $sec<1000){
//            $txt='00';
//        }else if($sec>=1000 && $sec<10000){
//            $txt='0';
//        }else if($sec>=10000 && $sec<100000){
//            $txt='';
//        }
//        
//        $rst1=  pg_fetch_array($Clase_factura->lista_siglas($id));
//        $retorno=$rst1[emp_sigla].$txt.$sec;
//        
//        $cns=$Clase_factura->lista_combomp($id);
//        $combo="<option value='0'>Seleccione</option>";
//        while($rst=  pg_fetch_array($cns)){
//            $combo.="<option value='$rst[mpt_id]'>$rst[mpt_nombre]</option>";
//        }
//                
//        echo $retorno.'&'.$combo;
//        break;
}
?>
