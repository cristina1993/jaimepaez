<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }

    function lista_facturas() {
        if ($this->con() == true) {
            return pg_query("select * from erp_retencion where ret_estado_aut='ANULADO'");
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

    function lista_detalle_retencion($ret) {
        if ($this->con() == true) {
            return pg_query("select * from erp_det_retencion where ret_id=$ret");
        }
    }

    function lista_id_cuenta($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM porcentages_retencion WHERE por_id=$id");
        }
    }

    function lista_cuenta_contable($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_id=$id");
        }
    }

    function lista_asientos_ret($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_asientos_contables where doc_id='$id' and mod_id='4' and con_estado=1");
        }
    }

}

$Obj = new Objeto();
$sms = 0;

$cns = $Obj->lista_facturas();
while ($rst = pg_fetch_array($cns)) {
    $asiento = $Obj->siguiente_asiento();
    $fec = $rst[ret_fecha_emision];
    $num_doc = $rst[ret_numero];
    $id = $rst[ret_id];
    $emi = 1;
    $ctas = pg_fetch_array($Obj->lista_asientos_ctas($emi, '26'));

    /////proveedores/////
    $dat5 = Array($asiento,
        'RETENCION',
        $num_doc,
        $fec,
        $ctas[pln_codigo],
        '',
        round($rst[ret_total_valor], 2),
        '0',
        '1',
        '4',
        $id,
        $rst[cli_id]
    );
    if ($Obj->insert_asientos($dat5) == false) {
        $sms = pg_last_error();
    }


    $cns_dt_ret = $Obj->lista_detalle_retencion($id);
    while ($rst_drt = pg_fetch_array($cns_dt_ret)) {
        $rst_idcta = pg_fetch_array($Obj->lista_id_cuenta($rst_drt[por_id]));
        $rst_cta = pg_fetch_array($Obj->lista_cuenta_contable($rst_idcta[cta_id]));
        $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
        $dt_asi = array(
            $asiento,
            $concepto,
            $rst[ret_numero],
            $fec,
            '',
            $rst_cta[pln_codigo],
            '0',
            round($rst_drt[dtr_valor], 2),
            '1',
            '4',
            $rst[ret_id],
            $rst[cli_id]
        );
        if ($Obj->insert_asientos($dt_asi) == false) {
            $sms = 'insert_asiento_enc' . pg_last_error();
            $aud = 1;
        }
    }
}


/////////anulacion ///////
$cns2 = $Obj->lista_facturas();
while ($rst2 = pg_fetch_array($cns2)) {
    $cns_as = $Obj->lista_asientos_ret($rst2[ret_id]);
    $asiento2 = $Obj->siguiente_asiento();
    while ($rst_as = pg_fetch_array($cns_as)) {
        $dt_a = array(
            $asiento2,
            'ANULACION ' . $rst_as[con_asiento],
            $rst_as[con_documento],
            $rst_as[con_fecha_emision],
            $rst_as[con_concepto_haber],
            $rst_as[con_concepto_debe],
            $rst_as[con_valor_haber],
            $rst_as[con_valor_debe],
            '2',
            '4',
            $rst_as[doc_id],
            $rst_as[cli_id]
        );
        if ($Obj->insert_asientos($dt_a) == false) {
            $sms = 'insert_asiento_det' . pg_last_error();
            $aud = 1;
        }
    }
}
echo $sms;
?>
