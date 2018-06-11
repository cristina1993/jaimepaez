<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//          return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }

    function lista_facturas() {
        if ($this->con() == true) {
            return pg_query("select * from erp_factura where fac_estado_aut!='ANULADO' or fac_estado_aut is null");
        }
    }

    function ultimo_asiento() {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_asientos_contables ORDER BY con_asiento DESC LIMIT 1");
        }
    }

    function siguiente_asiento() {
        if ($this->con() == true) {
            $rst = pg_fetch_array($this->ultimo_asiento());
            if (!empty($rst)) {
                $sec = (substr($rst[con_asiento], -10) + 1);
                $n_sec = substr($rst[con_asiento], 0, (12 - strlen($sec))) . $sec;
            } else {
                $n_sec = 'AS0000000001';
            }
            return $n_sec;
        }
    }

    function lista_det_fac($num) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM  erp_det_factura where fac_id='$num'");
        }
    }

    function lista_emisor_id($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_emisor where emi_id=$id");
        }
    }

    function lista_asientos_ctas($id, $ord) {
        if ($this->con() == true) {
            return pg_query("SELECT a.pln_id, c.pln_codigo FROM  erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.emi_id='$id' and a.cas_orden_emi='$ord' and c.pln_estado=0");
        }
    }

    function lista_cliente_id($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM  erp_i_cliente where cli_id='$id'");
        }
    }

////AUMENTA DESCUENTO 14%
    function lista_suma_descuentos_factura($doc) {
        if ($this->con() == true) {
            return pg_query("SELECT (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='0') as desc0,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='12') as desc12,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='14') as desc14,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='EX') as descex,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='NO') as descno");
        }
    }

    function lista_suma_fletes_factura($doc) {
        if ($this->con() == true) {
            return pg_query("SELECT sum(dfc_val_descuento) as desc, sum(dfc_precio_total) as tot FROM  erp_det_factura where fac_id=$doc and dfc_descripcion like '%FLETE%')");
        }
    }

    function insert_asientos($data) {
        if ($this->con() == true) {
            return pg_query("INSERT INTO erp_asientos_contables(
                con_asiento,
                con_concepto,
                con_documento,
                con_fecha_emision,
                con_concepto_debe,
                con_concepto_haber,
                con_valor_debe,
                con_valor_haber,
                con_estado,
                mod_id,
                doc_id,
                cli_id
            )
            VALUES (
                    '$data[0]',
                    '$data[1]',
                    '$data[2]',
                    '$data[3]',
                    '$data[4]',
                    '$data[5]',
                    '$data[6]',
                    '$data[7]',
                    '$data[8]',
                    '$data[9]',
                    '$data[10]',
                    '$data[11]'
                    )");
        }
    }

    function lista_ctasxcobrar_pagos() {
        if ($this->con() == true) {
            return pg_query("select * from erp_pagos_factura where pag_forma<>'9' and pag_forma<>'7' order by com_id");
        }
    }

    function lista_factura_id($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_factura where fac_id=$id");
        }
    }

}

$Obj = new Objeto();

$sms = 0;
$fle12 = 0;
$fle0 = 0;
$tf0 = 0;

$cns = $Obj->lista_facturas();
while ($rst = pg_fetch_array($cns)) {
    $asiento = $Obj->siguiente_asiento();
    $fec = $rst[fac_fecha_emision];
    $num_doc = $rst[fac_numero];
    $id = $rst[fac_id];
    $cns_det = $Obj->lista_det_fac($id);
    $emi = $rst[emi_id];
    $rst_emi = pg_fetch_array($Obj->lista_emisor_id($emi));
    $ventas = pg_fetch_array($Obj->lista_asientos_ctas($emi, '3'));
    $cliente = pg_fetch_array($Obj->lista_cliente_id($rst[cli_id]));
    if ($cliente[cli_tipo_cliente] == 0) {
        $cli = pg_fetch_array($Obj->lista_asientos_ctas($emi, '1')); ///
    } else {
        $cli = pg_fetch_array($Obj->lista_asientos_ctas($emi, '2')); ///
    }
    $descuento = pg_fetch_array($Obj->lista_asientos_ctas($emi, '5')); ///
    $flete = pg_fetch_array($Obj->lista_asientos_ctas($emi, '6')); ///
    $iva = pg_fetch_array($Obj->lista_asientos_ctas($emi, '4')); ///
    $ice = pg_fetch_array($Obj->lista_asientos_ctas($emi, '70')); ///
    $irbprn = pg_fetch_array($Obj->lista_asientos_ctas($emi, '71')); ///
    $propina = pg_fetch_array($Obj->lista_asientos_ctas($emi, '72')); ///

    $des = pg_fetch_array($Obj->lista_suma_descuentos_factura($id));
    $sub0 = str_replace(',','',number_format($rst[fac_subtotal0],2)) + str_replace(',','',number_format($des[desc0],2)) + str_replace(',','',number_format($rst[fac_subtotal_ex_iva],2)) + str_replace(',','',number_format($des[descex],2)) + str_replace(',','',number_format($rst[fac_subtotal_no_iva],2)) + str_replace(',','',number_format($des[descno],2));
    $subtotal = str_replace(',','',number_format($rst[fac_subtotal12],2)) + str_replace(',','',number_format($des[desc12],2)) + str_replace(',','',number_format($sub0,2)) + str_replace(',','',number_format($des[desc14],2));
    $dat0 = Array($asiento,
        'FACTURA VENTA',
        $num_doc,
        $fec,
        $cli[pln_codigo],
        $ventas[pln_codigo],
        str_replace(',','',number_format($rst[fac_total_valor] ,2)),
        str_replace(',','',number_format($subtotal ,2)),
        '1',
        '1',
        $rst[fac_id],
        $rst[cli_id]
    );

    $fle = pg_fetch_array($Obj->lista_suma_fletes_factura($id));
    if (!empty($fle)) {
        $dat1 = Array($asiento,
            'FACTURA VENTA',
            $num_doc,
            $fec,
            '',
            $flete[pln_codigo],
            '0.00',
            str_replace(',','',number_format($fle[tot],2)) + str_replace(',','',number_format($fle[desc] ,2)),
            '1',
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
            str_replace(',','',number_format($rst[fac_total_iva] ,2)),
            '1',
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
            str_replace(',','',number_format($rst[fac_total_descuento] ,2)),
            '0.00',
            '1',
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
            str_replace(',','',number_format($rst[fac_total_ice] ,2)),
            '1',
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
            str_replace(',','',number_format($rst[fac_total_irbpnr] ,2)),
            '1',
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
            str_replace(',','',number_format($rst[fac_total_propina] ,2)),
            '1',
            '1',
            $rst[fac_id],
            $rst[cli_id]
        );
    }

    $array = array($dat0, $dat1, $dat2, $dat3, $dat4, $dat5);
    $j = 0;
    while ($j <= count($array)) {
        if (!empty($array[$j])) {
            if ($Obj->insert_asientos($array[$j]) == false) {
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

$cns_pagos = $Obj->lista_ctasxcobrar_pagos();
while ($r_p = pg_fetch_array($cns_pagos)) {
    $rst_fac = pg_fetch_array($Obj->lista_factura_id($r_p[com_id]));
    $tc = 76;
    $td = 77;
    $ch = 78;
    $ef = 79;
    $rt = 80;
    $nc = 81;
    $ct = 82;
    $bn = 83;
    if ($r_p[pag_forma] != 0) {
        if ($r_p[pag_forma] != 9 && $r_p[pag_forma] != 7) {
            switch ($r_p[pag_forma]) {
                case 1:
                    $form = 'TARJETA DE CREDITO';
                    $cts = $tc;
                    $tip = 6;
                    break;
                case 2:
                    $form = 'TARJETA DE DEBITO';
                    $cts = $td;
                    $tip = 7;
                    break;
                case 3:
                    $form = 'CHEQUE';
                    $cts = $ch;
                    $tip = 1;
                    break;
                case 4:
                    $form = 'EFECTIVO';
                    $cts = $ef;
                    $tip = 10;
                    break;
                case 5:
                    $form = 'CERTIFICADOS';
                    $cts = $ct;
                    $tip = 8;
                    break;
                case 6:
                    $form = 'BONOS';
                    $cts = $bn;
                    $tip = 9;
                    break;
                case 7:
                    $form = 'RETENCION';
                    $cts = $rt;
                    $tip = 5;
                    break;
                case 8:
                    $form = 'NOTA DE CREDITO';
                    $cts = $nc;
                    break;
            }
            $cliente = pg_fetch_array($Obj->lista_cliente_id($rst[cli_id]));
            if ($cliente[cli_tipo_cliente] == 0) {
                $cli_as = '1';
            } else {
                $cli_as = '2';
            }

            $rst_cliente = pg_fetch_array($Obj->lista_asientos_ctas($rst_fac[emi_id], $cli_as));
            $rst_cta = pg_fetch_array($Obj->lista_asientos_ctas($rst_fac[emi_id], $cts));
            $asi_c = $Obj->siguiente_asiento();
            $asiento_c = array(
                $asi_c,
                'ABONO FAC. ' . $rst_fac[fac_numero],
                $r_p[chq_numero], //doc
                $rst_fac[fac_fecha_emision], //fec
                $rst_cta[pln_codigo], //con_debe
                $rst_cliente[pln_codigo], //con_haber
                str_replace(',','',number_format($r_p[pag_cant] ,2)), //val_debe
                str_replace(',','',number_format($r_p[pag_cant] ,2)), // val_haber
                '1', //estado
                '9',
                $r_p[pag_id],
                $rst_fac[cli_id]
            );
            if ($Obj->insert_asientos($asiento_c) == false) {
                $sms = 'Insert_asientos_ctasxcob' . pg_last_error();
                print_r($asiento_c);
                echo "<br>";
            }
        }
    }
}



echo $sms;
?>
