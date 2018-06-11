<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_ingresopt.php'; // cambiar clsClase_productos
$Clase_industrial_ingresopt = new Clase_industrial_ingresopt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$x = $_REQUEST[x];
$s = $_REQUEST[s];
$emp = $_REQUEST[emp];
$inv5 = $_REQUEST[inv];
$ctr_inv = $_REQUEST[ctinv];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {

            $n = 0;

            while ($n < count($data)) {
                $dat = explode('&', $data[$n]);
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_ingresopt->insert_industrial_ingresopt($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'BodegaIndustrial';
                        $accion = 'INSERTAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
                $n++;
            }
        } else {
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    strtoupper($dt[4]),
                    $dt[5],
                    $dt[6],
                    $dt[7],
                    $dt[8],
                );
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_ingresopt->upd_industrial_ingreso($dat) == FALSE) {
                        $sms = pg_last_error();
                    } else {
                        $n = 0;
                        while ($n < count($fields)) {
                            $f = $f . strtoupper($fields[$n] . '&');
                            $n++;
                        }
                        $modulo = 'BodegaIndustrial';
                        $accion = 'MODIFICAR';
                        if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                            $sms = "Auditoria" . pg_last_error() . 'ok2';
                        }
                    }
                }
                $n++;
            }
        }
        echo $sms . '&' . $x;
        break;
    case 1:
        if ($Clase_industrial_ingresopt->delete_industrial_ingreso($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'BodegaIndustrial';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        if ($_REQUEST[ems] == 1) {
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod($id));
            $id = $rst[id];
            $codigo = $rst[pro_a];
            $desc = $rst[pro_b];
            $uni = $rst[pro_uni];
            echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
        } else {
            if ($emp == 1 or $emp == 2) {
                $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_noperti_cod($id));
                $id = $rst[id];
                $codigo = $rst[pro_a];
                $desc = $rst[pro_b];
                $uni = $rst[pro_uni];
                echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
            } else {
                $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_cod($id));
                $id = $rst[pro_id];
                $codigo = $rst[pro_codigo];
                $desc = $rst[pro_descripcion];
                $uni = $rst[pro_uni];
                //$uni2 = $rst[pro_uni2];
            }
            echo $id . '&' . $codigo . '&' . $desc . '&' . $uni;
        }



        break;
    case 3:
        if ($_REQUEST[ems] == 1) {
            $cns = $Clase_industrial_ingresopt->lista_productos_comercial();
            while ($rst = pg_fetch_array($cns)) {
                $tp = explode('&', $rst[pro_tipo]);
                $producto.= "<option value='$rst[id]' >$rst[pro_a] $rst[pro_ac] $rst[pro_b]</option>";
            }
        } else {
            $cns = $Clase_industrial_ingresopt->lista_producto($id);
            while ($rst = pg_fetch_array($cns)) {
                $producto.= "<option value='$rst[pro_id]' >$rst[pro_codigo] $rst[pro_descripcion]</option>";
            }
        }
        echo $producto;
        break;
    case 4:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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

        $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_siglas($id));
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:
        $rst_cli = pg_fetch_array($Clase_industrial_ingresopt->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id] . '&' . $rst_cli[nombres];
        echo $retorno;
        break;
    case 6:
        $rst_tra = pg_fetch_array($Clase_industrial_ingresopt->lista_transaccion($id));
        $retorno = $rst_tra[trs_descripcion];
        echo $retorno;
        break;
    case 7:
        $cns1 = $Clase_industrial_ingresopt->lista_prod_noperti();
        while ($rst = pg_fetch_array($cns1)) {
            $tp = explode('&', $rst[pro_tipo]);
//            $producto.= "<option value='$rst[pro_a]' >$rst[pro_a] - $rst[pro_ac] $tp[9] $rst[pro_b]</option>";
            $producto.= "<option value='$rst[id]' >$rst[pro_a] - $rst[pro_ac] $rst[pro_b]</option>";
        }
        echo $producto;
        break;
    case 8:
        $cns1 = $Clase_industrial_ingresopt->lista_prod_comercial();
        while ($rst = pg_fetch_array($cns1)) {
            $tp = explode('&', $rst[pro_tipo]);
            $producto.= "<option value='$rst[id]' >$rst[pro_a] - $rst[pro_ac] $rst[pro_b]</option>";
        }
        echo $producto;
        break;
///Transferencias        
    case 9:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_local($id));
        echo $rst[emi_cod_punto_emision] . '&' . $rst[emi_cod_cli] . '&' . $rst[emi_nombre_comercial];
        break;
    case 10:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_un_producto_id($id));
        if ($rst[id] != '') {
            if ($x == 0) {
//                if ($ctr_inv == 0) {
//                    $fra = '';
//                } else {
                $fra = "and m.bod_id=$s";
//                }
                $rst1 = pg_fetch_array($Clase_industrial_ingresopt->total_ingreso_egreso_fact($rst[id], $fra));
                $rst2 = pg_fetch_array($Clase_industrial_ingresopt->lista_costos_mov($rst[id], $fra));
                $rst2[mov_val_unit] = (($rst2[ingreso] - $rst2[egreso]) / ($rst2[icnt] - $rst2[ecnt]));
                $inv = $rst1[ingreso] - $rst1[egreso];
            }
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst_precio[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst2[mov_val_unit] . '&' . $rst[mp_j] . '&' . $rst[mp_k] . '&' . $rst[mp_l];
        }
        break;
    case 11:
        $sms = 0;
        $data1 = $_REQUEST[data1];
        $data2 = $_REQUEST[data2];
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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
        $n = 0;
        while ($n < count($data1)) {
            $dat1 = explode('&', $data1[$n]);
            $dat2 = explode('&', $data2[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat1, $secuencial)) {
                $sms = pg_last_error();
                break;
            } else {
                if (!$Clase_industrial_ingresopt->insert_transferencia($dat2, $secuencial)) {
                    break;
                }
            }
            $n++;
        }
        echo $sms . '&' . $secuencial;
        break;

    case 12:
        $sms = 0;
        $n = 0;
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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

        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat, $secuencial)) {
                $sms = pg_last_error();
            } else {
                $j = 0;
                while ($j < count($fields)) {
                    $f = $f . strtoupper($fields[$j] . '&');
                    $j++;
                }
                if ($x == 0) {
                    $modulo = 'INGRESO PRODUCTO TERMINADO';
                } else if ($x == 1) {
                    $modulo = 'INGRESO PRODUCTO GENERAL';
                } else {
                    $modulo = 'MOVIMIENTO PRODUCTO';
                }
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[4]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
            $n++;
        }
        if ($x == 1) {
            $txt = '000000000';
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_ultimo_sec_reg_facturas());
            $num_doc = $rst[reg_num_registro];
            $num_doc = intval($num_doc + 1);
            $num_doc = substr($txt, 0, (10 - strlen($num_doc))) . $num_doc;
            $rst_rucpro = pg_fetch_array($Clase_industrial_ingresopt->lista_ruc_proveedor($dat[2]));
            $data1 = array(
                $dat[6],
                $dat[12],
                $dat[11],
                $rst_rucpro[cli_ced_ruc],
                $num_doc,
                2,
                $dat[2],
                $dat[4]
            );
            if ($Clase_industrial_ingresopt->insert_reg_facturas($data1, $secuencial) == true) {
                $n = 0;
                while ($n < count($data)) {
                    $dt = explode('&', $data[$n]);
                    $rst_reg = pg_fetch_array($Clase_industrial_ingresopt->lista_id_reg_facturas($num_doc));
                    $rst_proid = pg_fetch_array($Clase_industrial_ingresopt->lista_producto_ing_general($dt[0]));
                    $data2 = array($rst_reg[reg_id],
                        $rst_proid[mp_c],
                        $rst_proid[mp_d],
                        $dt[7],
                        $rst_proid[ids],
                        2,
                        $dt[9]);
                    if ($Clase_industrial_ingresopt->insert_det_reg_facturas($data2) == false) {
                        $sms = 'Insert_det_facturas' . pg_last_error();
                    }
                    $n++;
                }
                $n = 0;
                while ($n < count($data)) {
                    $dt = explode('&', $data[$n]);
                    if ($Clase_industrial_ingresopt->update_costo_uni($dt[0], $dt[9]) == false) {
                        $sms = 'update costo' . pg_last_error();
                    }
                    $n++;
                }
            } else {
                $sms = 'Insert_enc_facturas' . pg_last_error();
            }
        }
        echo $sms . '&' . $secuencial;
        break;

    case 13:
        if ($ctr_inv == 0) {
            $fra1 = '';
        } else {
            $fra1 = "and m.cod_punto_emision=$s";
        }
        $cns_pro = $Clase_industrial_ingresopt->lista_producto_total_bod($fra1);
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            echo "<option value='$rst_pro[id]'> $rst_pro[mp_c] $rst_pro[mp_d]</option>";
        }
        break;

    case 14:
        $sms = 0;
        $data1 = $_REQUEST[data1];
        $data2 = $_REQUEST[data2];
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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
        $n = 0;
        while ($n < count($data1)) {
            $dat1 = explode('&', $data1[$n]);
            $dat2 = explode('&', $data2[$n]);
            if (!$Clase_industrial_ingresopt->insert_transferencia($dat1, $secuencial)) {
                $sms = 'tr_bod1' . pg_last_error();
                break;
            } else {
                if (!$Clase_industrial_ingresopt->insert_transferencia($dat2, $secuencial)) {
                    $sms = 'tr_bod1' . pg_last_error();
                    break;
                }
            }
            $n++;
            $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_id_pedido($dat2[5]));
            $rst1 = pg_fetch_array($Clase_industrial_ingresopt->lista_det_pedido($dat2[5], $rst[ped_id]));
            if ($rst1[transferencia] == $rst1[pedido]) {
                $std = 8;
            } else {
                $std = 7;
            }
            if (!$Clase_industrial_ingresopt->upd_pedido($rst[ped_id], $std)) {
                $sms = 'upd' . pg_last_error();
            }
        }
        echo $sms . '&' . $secuencial;
        break;

    case 15:
        $rst = pg_fetch_array($Clase_industrial_ingresopt->lista_secuencial_transferencia());
        $sectra = trim($rst[sec_transferencias]);
        $sec = (substr($sectra, -10) + 1);
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
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 16:
        $sms = 0;
        $sec = $_REQUEST[sec];
        if ($Clase_industrial_ingresopt->insert_sec_transferencia($sec) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 17:
        $ndoc = $_REQUEST[doc];
        $tdoc = $_REQUEST[tdoc];
        $rst_ruc_pro = pg_fetch_array($Clase_industrial_ingresopt->lista_ruc_proveedor($id));
        $rst_reg_fac = pg_fetch_array($Clase_industrial_ingresopt->lista_un_reg_factura_tip($ndoc, $rst_ruc_pro[cli_ced_ruc], $tdoc));
        echo $rst_reg_fac[reg_num_documento] . '&' . $rst_reg_fac[reg_ruc_cliente];
        break;
}
?>








