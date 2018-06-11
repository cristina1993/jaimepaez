<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_nota.php';
$Clase_nota_Credito = new Clase_nota_Credito();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $aud = 0;
        $sms = 0;
//        echo $data[3];
        if (empty($x)) {
            $num = str_replace('-', '', trim($data[0]));
            $comp = substr($num, 7, 9);
            $data[1] = str_replace('-', '', trim($data[1]));
            $data[4] = str_replace('-', '', trim($data[4]));
            $data[16] = str_replace('-', '', trim($data[16]));
            $cod_punto_emision = substr($num, 0, 3);
            $dat = Array(
                $comp,
                $data[1],
                strtoupper($data[2]),
                $data[3],
                $data[4],
                $data[5],
                $data[6],
                $data[7],
                $data[8],
                $data[9],
                $data[10],
                $data[11],
                $data[12],
                $data[13], //iva
                $data[14],
                $data[15],
                $data[16],
                strtoupper($data[17]),
                strtoupper($data[18]),
                $data[19],
                $cod_punto_emision,
                $data[0]);
            if ($Clase_nota_Credito->insert_nota_credito($dat) == FALSE) {
                $sms = pg_last_error() . 'ok1';
                $aud = 1;
            } else {

                $n = 0;
                while ($n < count($data1)) {
                    $comprobante = str_replace('-', '', trim($data[0]));
                    $dt = explode('&', $data1[$n]);
                    $dat1 = Array(
                        $comprobante,
                        strtoupper($dt[0]),
                        $dt[1],
                        strtoupper($dt[2]),
                        $dt[3],
                        $dt[4],
                        $dt[5],
                        $dt[6],
                        strtoupper($dt[7]),
                        $data[6],
                        strtoupper($data[22])
                    );
                    if ($Clase_nota_Credito->insert_det_nota_credito($dat1) == FALSE) {
                        $sms = pg_last_error();
                        $aud = 1;
                    } else {
                        $rst_proi = pg_fetch_array($Clase_nota_Credito->lista_i_producto_cod($dt[0]));

                        $pro = $rst_proi[id];
                        $tab = 0;
                        $bod = $data[21];
                        $rst_cli = pg_fetch_array($Clase_nota_Credito->lista_un_cliente($data[3]));
                        $cli = $rst_cli[cli_id];
                        $dat2 = Array(
                            $pro,
                            $data[20],
                            $cli,
                            $bod,
                            $comprobante,
                            $data[4],
                            $dt[1],
                            $tab,
                            $dt[8],
                            $dt[9]
                        );
                        if ($data[20] != '1') {
                            if ($Clase_nota_Credito->insert_movimiento($dat2) == FALSE) {
                                $sms = pg_last_error() . 'ok4,' . $pro;
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
                $modulo = 'NOTA DE CREDITO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $num = str_replace('-', '', trim($data[0]));
            $comp = substr($num, 7, 9);
            $data[1] = str_replace('-', '', trim($data[1]));
            $data[4] = str_replace('-', '', trim($data[4]));
            $data[16] = str_replace('-', '', trim($data[16]));
            $cod_punto_emision = substr($num, 0, 3);
            $dat = Array(
                $data[4],
                $data[6],
                $data[7],
                $data[8],
                $data[9],
                $data[10],
                $data[11],
                $data[13],
                $data[12],
                $data[15]
            );

            if ($Clase_nota_Credito->upd_nota_credito($dat, $comp, $cod_punto_emision) == FALSE) {
                $sms = pg_last_error() . 'oky1';
                $aud = 1;
            } else {
                if ($Clase_nota_Credito->delete_movimiento($cod_punto_emision, $num) == FALSE) {
                    $sms = pg_last_error() . 'oky2';
                    $aud = 1;
                } else {
                    if ($Clase_nota_Credito->delete_det_nota_credito($num) == FALSE) {
                        $sms = pg_last_error() . 'oky3';
                        $aud = 1;
                    } else {
                        $n = 0;
                        while ($n < count($data1)) {
                            $comprobante = str_replace('-', '', trim($data[0]));
                            $dt = explode('&', $data1[$n]);
                            $dat1 = Array(
                                $comprobante,
                                strtoupper($dt[0]),
                                $dt[1],
                                strtoupper($dt[2]),
                                $dt[3],
                                $dt[4],
                                $dt[5],
                                $dt[6],
                                strtoupper($dt[7]),
                                $data[6],
                                strtoupper($data[22])
                            );
                            if ($Clase_nota_Credito->insert_det_nota_credito($dat1) == FALSE) {
                                $sms = pg_last_error() . 'oky3';
                                $aud = 1;
                            } else {
                                $rst_proi = pg_fetch_array($Clase_nota_Credito->lista_i_producto_cod($dt[0]));
                                $pro = $rst_proi[id];
                                $tab = 0;
                                $bod = $data[21];
                                $rst_cli = pg_fetch_array($Clase_nota_Credito->lista_un_cliente($data[3]));
                                $cli = $rst_cli[cli_id];
                                $dat2 = Array(
                                    $pro,
                                    $data[20],
                                    $cli,
                                    $bod,
                                    $comprobante,
                                    $data[4],
                                    $dt[1],
                                    $tab,
                                    $dt[8],
                                    $dt[9]
                                );
                                if ($data[20] != '1') {
                                    if ($Clase_nota_Credito->insert_movimiento($dat2) == FALSE) {
                                        $sms = pg_last_error() . 'oky4';
                                        $aud = 1;
                                    }
                                }
                            }
                            $n++;
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
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $cns = $Clase_nota_Credito->lista_nota_credito_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "04"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                if ($rst[cod_punto_emision] == 10) {
                    $ems = '010';
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
                $Clase_nota_Credito->upd_notcre_clave_acceso($clave, $rst[com_id]);
            }
        }
        break;
    case 2:
        $sms = 0;
        if ($Clase_nota_Credito->upd_notcre_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 3:
        $sms = 0;
        $sec = str_replace('-', '', trim($id));
        if ($Clase_nota_Credito->delete_comprobante_notacredito($id) == false) {
            $sms = pg_last_error() . 'delete1';
        }
        if ($Clase_nota_Credito->delete_det_notacredito($sec) == FALSE) {
            $sms = pg_last_error() . 'delete2';
            $aud = 1;
        }
        if ($Clase_nota_Credito->delete_asientos_notacredito($id) == FALSE) {
            $sms = pg_last_error() . 'delete3';
            $aud = 1;
        }
        if ($Clase_nota_Credito->delete_movimiento_notacredito($sec) == FALSE) {
            $sms = pg_last_error() . 'delete4';
            $aud = 1;
        }
        if ($aud != 1) {
            $modulo = 'Nota de Credito';
            $accion = 'Eliminar';
            if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>



















