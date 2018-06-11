<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }

    function lista_retenciones() {
        if ($this->con() == true) {
            return pg_query("select * from erp_registro_retencion where rgr_estado!=2 order by rgr_num_registro");
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

    function lista_det_retencion($num) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM  erp_det_reg_retencion where rgr_id='$num'");
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

    function update_asiento_ret($id, $as) {
        if ($this->con() == true) {
            return pg_query("UPDATE erp_registro_retencion set con_asiento='$as' WHERE rgr_id=$id");
        }
    }

}

$Obj = new Objeto();

$sms = 0;
$fle12 = 0;
$fle0 = 0;
$tf0 = 0;

$cns = $Obj->lista_retenciones();
while ($rst = pg_fetch_array($cns)) {
    $asiento = $Obj->siguiente_asiento();
    $rst_cli = pg_fetch_array($Obj->lista_cliente_id($rst[cli_id]));
    if ($rst_cli[cli_tipo_cliente] == 0) {
        $ord = 1;
    } else {
        $ord = 2;
    }
    $rst_cliente = pg_fetch_array($Obj->lista_asientos_ctas('1', $ord));
    $cns_det = $Obj->lista_det_retencion($rst[rgr_id]);

    while ($dt = pg_fetch_array($cns_det)) {
        $rst_idcta = pg_fetch_array($Obj->lista_id_cuenta($dt[por_id]));
        $rst_cta = pg_fetch_array($Obj->lista_cuenta_contable($rst_idcta[cta_id]));
        $data1 = Array($asiento,
            'REGISTRO DE RETENCION',
            $rst[rgr_numero],
            $rst[rgr_fecha_emision],
            $rst_cta[pln_codigo],
            '',
            str_replace(',','',number_format($dt[drr_valor], 2)),
            '0',
            '1',
            '8',
            $rst[rgr_id],
            $rst[cli_id]
        );
        if ($Obj->insert_asientos($data1) == false) {
            $sms = 'Insert_asientos_det' . pg_last_error();
        }
    }

    $dat0 = Array($asiento,
        'REGISTRO DE RETENCION',
        $rst[rgr_numero],
        $rst[rgr_fecha_emision],
        '',
        $rst_cliente[pln_codigo],
        '0',
        str_replace(',','',number_format($rst[rgr_total_valor], 2)),
        '1',
        '8',
        $rst[rgr_id],
        $rst[cli_id]
    );


    if ($Obj->insert_asientos($dat0) == false) {
        $sms = 'Insert_asientos1' . pg_last_error();
        $aud = 1;
    } else {
        if ($Obj->update_asiento_ret($rst[rgr_id], $asiento) == false) {
            $sms = 'Insert_asientos1' . pg_last_error();
            $aud = 1;
        }
    }
}

echo $sms;
?>
