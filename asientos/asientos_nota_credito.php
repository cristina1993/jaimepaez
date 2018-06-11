<?php

set_time_limit(0);

class Objeto {

  function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//          return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }


    function lista_facturas() {
        if ($this->con() == true) {
            return pg_query("select * from erp_nota_credito");
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
            return pg_query("SELECT * FROM  erp_det_nota_credito where ncr_id='$num'");
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

    function lista_suma_descuentos_factura($doc) {
        if ($this->con() == true) {
            return pg_query("SELECT (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='0') as desc0,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='12') as desc12,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='EX') as descex,
                                    (SELECT sum(dnc_val_descuento) FROM  erp_det_nota_credito where ncr_id='$doc' and dnc_iva='NO') as descno");
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

    function lista_factura_id($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_nota_credito where ncr_id=$id");
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
    $fec = $rst[ncr_fecha_emision];
    $num_doc = $rst[ncr_numero];
    $id = $rst[ncr_id];
    $cns_det = $Obj->lista_det_fac($id);
    $emi = $rst[emi_id];
    $rst_emi = pg_fetch_array($Obj->lista_emisor_id($emi));
    $ventas = pg_fetch_array($Obj->lista_asientos_ctas($emi, '7'));
    $cliente = pg_fetch_array($Obj->lista_cliente_id($rst[cli_id]));
    if ($cliente[cli_tipo_cliente] == 0) {
        $cli = pg_fetch_array($Obj->lista_asientos_ctas($emi, '8')); ///
    } else {
        $cli = pg_fetch_array($Obj->lista_asientos_ctas($emi, '9')); ///
    }

    $descuento = pg_fetch_array($Obj->lista_asientos_ctas($emi, '84'));
    $iva = pg_fetch_array($Obj->lista_asientos_ctas($emi, '10'));
    $ice = pg_fetch_array($Obj->lista_asientos_ctas($emi, '73'));
    $irbprn = pg_fetch_array($Obj->lista_asientos_ctas($emi, '74')); ///
    $propina = pg_fetch_array($Obj->lista_asientos_ctas($emi, '75'));

    $des = pg_fetch_array($Obj->lista_suma_descuentos_factura($id));
    $sub0 = $rst[ncr_subtotal0] + $des[desc0] + $rst[ncr_subtotal_ex_iva] + $des[descex] + $rst[ncr_subtotal_no_iva] + $des[descno];
    $subtotal = $rst[ncr_subtotal12] + $des[desc12] + $sub0;
    $dat0 = Array($asiento,
        'DEVOLUCION VENTA',
        $num_doc,
        $fec,
        $ventas[pln_codigo],
        $cli[pln_codigo],
        $subtotal,
        $rst[nrc_total_valor],
        '1',
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
            '1',
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
            '1',
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
            '1',
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
            '1',
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
            '1',
            '2',
            $rst[ncr_id],
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

echo $sms;
?>
