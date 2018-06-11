<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_asientos_automaticos.php';
$Clase_asientos_automaticos = new Clase_asientos_automaticos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = trim($_REQUEST[id]);
$x = $_REQUEST[x];
$fields = $_REQUEST[fields];
$emi = $_REQUEST[emi];
switch ($op) {
    case 0:
        $sms = 0;
        $fle12 = 0;
        $fle0 = 0;
        $tf0 = 0;
        if (isset($id)) {
            $cns = $Clase_asientos_automaticos->lista_facturas_documento($id);
            if ($x != 0) {
                if ($Clase_asientos_automaticos->delete_asientos($data, 'FACTURA VENTA') == FALSE) {
                    $sms = pg_last_error();
                }
            }
        } else {
            $cns = $Clase_asientos_automaticos->lista_facturas();
        }
        while ($rst = pg_fetch_array($cns)) {
            $rst_as = pg_fetch_array($Clase_asientos_automaticos->lista_asientos($rst[fac_numero], 'FACTURACION VENTA'));
            if (empty($rst_as)) {
                $asiento = $Clase_asientos_automaticos->siguiente_asiento();
                $fec = $rst[fac_fecha_emision];
                $num_doc = $rst[fac_numero];
                $cns_det = $Clase_asientos_automaticos->lista_det_fac($id);

                $rst_emi = pg_fetch_array($Clase_asientos_automaticos->lista_emisor_id($emi));
                $ventas = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '3'));
                $cliente = pg_fetch_array($Clase_asientos_automaticos->lista_cliente_id($rst[cli_id]));
                if ($cliente[cli_tipo_cliente] == 0) {
                    $cli = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '1')); ///
                } else {
                    $cli = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '2')); ///
                }
                $descuento = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '5')); ///
                $flete = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '6')); ///
                $iva = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '4')); ///
                $ice = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '70')); ///
                $irbprn = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '71')); ///
                $propina = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '72')); ///

                $des = pg_fetch_array($Clase_asientos_automaticos->lista_suma_descuentos_factura($id));
                $sub0 = $rst[fac_subtotal0] + $des[desc0] + $rst[fac_subtotal_ex_iva] + $des[descex] + $rst[fac_subtotal_no_iva] + $des[descno];
                $subtotal = $rst[fac_subtotal12] + $des[desc12] + $sub0;
                $dat0 = Array($asiento,
                    'FACTURA VENTA',
                    $num_doc,
                    $fec,
                    $cli[pln_codigo],
                    $ventas[pln_codigo],
                    round($rst[fac_total_valor], 2),
                    round($subtotal, 2),
                    '1',
                    $rst[fac_id],
                    $rst[cli_id]
                );

                $fle = pg_fetch_array($Clase_asientos_automaticos->lista_suma_fletes_factura($id));
                if (!empty($fle)) {
                    $dat1 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $flete[pln_codigo],
                        '0.00',
                        round($fle[tot] + $fle[desc], 2),
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[fac_subtotal12] != 0) {
                    $dat1 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $iva[pln_codigo],
                        '0.00',
                        round($rst[fac_total_iva], 2),
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[fac_total_descuento] != 0) {
                    $dat3 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        $descuento[pln_codigo],
                        '',
                        round($rst[fac_total_descuento], 2),
                        '0.00',
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[fac_total_ice] != 0) {
                    $dat4 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $ice[pln_codigo],
                        '0.00',
                        round($rst[fac_total_ice], 2),
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[fac_total_irbpnr] != 0) {
                    $dat5 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $irbprn[pln_codigo],
                        '0.00',
                        round($rst[fac_total_irbpnr], 2),
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[fac_total_propina] != 0) {
                    $dat2 = Array($asiento,
                        'FACTURA VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $propina[pln_codigo],
                        '0.00',
                        round($rst[fac_total_propina], 2),
                        '1',
                        $rst[fac_id],
                        $rst[cli_id]
                    );
                }

                $array = array($dat0, $dat1, $dat2, $dat3, $dat4, $dat5);
                $j = 0;
                while ($j <= count($array)) {
                    if (!empty($array[$j])) {
                        if ($Clase_asientos_automaticos->insert_asientos($array[$j]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                    $j++;
                }
                $dat0 = array();
                $dat1 = array();
                $dat2 = array();
                $dat3 = array();
                $dat4 = array();
                $dat5 = array();
                $fle12 = 0;
                $fle0 = 0;
            }
        }
        echo $sms;
        break;

    case 1:
        $sms = 0;
        if (isset($id)) {
            $cns = $Clase_asientos_automaticos->lista_notacre_documento($id);
            if ($x != 0) {
                if ($Clase_asientos_automaticos->delete_asientos($data, 'DEVOLUCION VENTA') == FALSE) {
                    $sms = pg_last_error();
                }
            }
        } else {
            $cns = $Clase_asientos_automaticos->lista_notas_credito();
        }

        while ($rst = pg_fetch_array($cns)) {
            $rst_as = pg_fetch_array($Clase_asientos_automaticos->lista_asientos($rst[ncr_numero], 'DEVOLUCION VENTA'));
            if (empty($rst_as)) {
                $asiento = $Clase_asientos_automaticos->siguiente_asiento();
                $fec = $rst[ncr_fecha_emision];
                $num_doc = $rst[ncr_numero];

                $rst_emi = pg_fetch_array($Clase_asientos_automaticos->lista_emisor_id($emi));
                $ventas = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '7'));
                $cliente = pg_fetch_array($Clase_asientos_automaticos->lista_cliente_id($rst[cli_id]));
                if ($cliente[cli_tipo_cliente] == 0) {
                    $cli = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '8')); ///
                } else {
                    $cli = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '9')); ///
                }

                $descuento = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '84'));
                $iva = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '10'));
                $ice = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '73'));
                $irbprn = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '74')); ///
                $propina = pg_fetch_array($Clase_asientos_automaticos->lista_asientos_ctas($emi, '75'));

                $des = pg_fetch_array($Clase_asientos_automaticos->lista_suma_descuentos_nota_cred($id));
                $sub0 = $rst[ncr_subtotal0] + $des[desc0] + $rst[ncr_subtotal_ex_iva] + $des[descex] + $rst[ncr_subtotal_no_iva] + $des[descno];
                $subtotal = $rst[ncr_subtotal12] + $des[desc12] + $sub0;
                $dat0 = Array($asiento,
                    'DEVOLUCION VENTA',
                    $num_doc,
                    $fec,
                    $ventas[pln_codigo],
                    $cli[pln_codigo],
                    round($subtotal, 2),
                    round($rst[nrc_total_valor], 2),
                    '2',
                    $rst[ncr_id],
                    $rst[cli_id]
                );

                if ($rst[ncr_subtotal12] != 0) {
                    $dat1 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        $iva[pln_codigo],
                        '',
                        round($rst[ncr_total_iva], 2),
                        '0.00',
                        '2',
                        $rst[ncr_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[ncr_total_descuento] != 0) {
                    $dat3 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        '',
                        $descuento[pln_codigo],
                        '0.00',
                        round($rst[ncr_total_descuento], 2),
                        '2',
                        $rst[ncr_id],
                        $rst[cli_id]
                    );
                }


                if ($rst[ncr_total_ice] != 0) {
                    $dat4 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        $ice[pln_codigo],
                        '',
                        round($rst[ncr_total_ice], 2),
                        '0.00',
                        '2',
                        $rst[ncr_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[ncr_irbpnr] != 0) {
                    $dat5 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        $irbprn[pln_codigo],
                        '',
                        round($rst[ncr_irbpnr], 2),
                        '0.00',
                        '2',
                        $rst[ncr_id],
                        $rst[cli_id]
                    );
                }

                if ($rst[ncr_total_propina] != 0) {
                    $dat2 = Array($asiento,
                        'DEVOLUCION VENTA',
                        $num_doc,
                        $fec,
                        $propina[pln_codigo],
                        '',
                        round($rst[ncr_total_propina], 2),
                        '0.00',
                        '2',
                        $rst[ncr_id],
                        $rst[cli_id]
                    );
                }
                $array = array($dat0, $dat1, $dat2, $dat3, $dat4, $dat5);
                $j = 0;
                while ($j <= count($array)) {
                    if (!empty($array[$j])) {
                        if ($Clase_asientos_automaticos->insert_asientos($array[$j]) == false) {
                            $sms = pg_last_error();
                        }
                    }
                    $j++;
                }
                $dat0 = array();
                $dat1 = array();
                $dat2 = array();
                $dat3 = array();
                $dat4 = array();
                $dat5 = array();
            }
        }
        echo $sms = 0;
        break;

    case 2:
        $sms = 0;
        $cns = $Clase_asientos_automaticos->lista_pagos();
        while ($rst = pg_fetch_array($cns)) {
            $fecha = substr($rst[fecha_emision], 0, 4) . '-' . substr($rst[fecha_emision], 4, 2) . '-' . substr($rst[fecha_emision], 6, 2);
            $d = $rst[pag_contado];
            $nf = strtotime("+$d day", strtotime($fecha));
            $fec = date('Y-m-d', $nf);
            if ($Clase_asientos_automaticos->update_pagos($rst[pag_id], $rst[num_documento], $fec) == false) {
                $sms = pg_last_error();
            }
            echo $sms;
        }


        break;
}
?>
