<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_pedidospt.php';
$Clase_pedidospt = new Clase_pedidospt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        if (empty($id)) {
            $sms = 0;
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);

                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    $dt[4],
                    $dt[5]
                );

                if ($dt[0] != 'undefined') {
                    if ($Clase_pedidospt->insert_pedido($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        $fields = str_replace("&", ",", $fields[0]);
                        $modulo = 'PedidoPro';
                        $accion = 'Insert';
                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                            $sms = "Auditoria" . pg_last_error();
                        }
                    }
                }
                $n++;
            }
        } else {
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                if ($dt[4] == 1) {
                    $rst2 = pg_fetch_array($Clase_pedidospt->lista_cantidad_nop($dt[5], $dt[11]));
                } else {
                    $rst2 = pg_fetch_array($Clase_pedidospt->lista_cantidad($dt[5], $dt[11]));
                }
                if ($rst2[suma] == '') {
                    $rst2[suma] = 0;
                }
                $saldo = $dt[12] - $dt[10] - $rst2[suma];
                if ($saldo == 0) {
                    $estado = 2;
                } else {
                    $estado = 1;
                }
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    $dt[4],
                    $dt[5],
                    $dt[6],
                    $dt[7],
                    strtoupper($dt[8]),
                    strtoupper($dt[9]),
                    $dt[10],
                    $estado
                );
                
                
                
                if ($dt[0] != 'undefined') {
                    if ($Clase_pedidospt->upd_pedido($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        if ($Clase_pedidospt->insert_egreso($dat) == false) {
                            $sms = pg_last_error();
                        } else {
                            if ($Clase_pedidospt->insert_seguimiento($dat,$_REQUEST[dest]) == true) {
                                $fields = str_replace("&", ",", $fields[0]);
                                $modulo = 'EgresoPro';
                                $accion = 'Insert';
                                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                                    $sms = "Auditoria" . pg_last_error();
                                }
                            }else{
                                print_r($dat);
                                $sms=  "Seg ".pg_last_error();
                            }
                        }
                    }
                }
                $n++;
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_pedidospt->delete($id) == true) {
            $sms = 0;
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Clase_pedidospt->lista_secuencial_documento());
        $sec = (substr($rst[ped_documento], -6) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '00000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '0000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '00';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '0';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '';
        }
        $retorno = PT . $txt . $sec;
        echo $retorno;
        break;

    case 3:
        $rst_cli = pg_fetch_array($Clase_pedidospt->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id] . '&' . $rst_cli[nombres];
        echo $retorno;
        break;
    case 4:
        $grup = '';
        if ($id == 1) {
            $cns = $Clase_pedidospt->lista_producto_noperti($id);
        } else {
            $cns = $Clase_pedidospt->lista_producto($id);
        }

        while ($rst = pg_fetch_array($cns)) {

            if ($id == 1) {
                $rst['pro_codigo'] = $rst['pro_a'];
                $rst['pro_descripcion'] = $rst['pro_b'];
                $fl = explode('&', $rst['pro_tipo']);
                $fml = $fl[9] . ' ';
            } else {
                $fml = '';
            }


            if ($grup != $rst[pro_descripcion]) {
                $producto.= "<option value='$rst[pro_codigo]' >$fml$rst[pro_descripcion]</option>";
            }
            $grup = $rst[pro_codigo];
        }
        echo $producto;
        break;
    case 5:
        $n = 0;
        if ($_REQUEST[bd] == 1) {
            $rst = pg_fetch_array($Clase_pedidospt->lista_un_producto_nop($id));
            $inv = pg_fetch_array($Clase_pedidospt->lista_inv_prod_nop(1, $id));
            $pro = $rst[id];
            $codigo = $rst[pro_a];
            $desc = $rst[pro_b];
            $uni = $rst[pro_c];
            $uni2 = $rst[pro_d];
        } else {
            $rst = pg_fetch_array($Clase_pedidospt->lista_un_producto_ind($id));
            $inv = pg_fetch_array($Clase_pedidospt->lista_inv_prod_ind($_REQUEST[bd], $id));
            $pro = $rst[pro_id];
            $codigo = $rst[pro_codigo];
            $desc = $rst[pro_descripcion];
            $uni = $rst[pro_uni];
            $uni2 = '';
        }



        echo $pro . '&' . $codigo . '&' . $desc . '&' . $uni . '&' . $uni2 . '&' . $inv[total];
        break;
}
?>
