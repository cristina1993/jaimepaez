<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }

    function lista_cuentas() {
        if ($this->con() == true) {
            return pg_query("select * from erp_ctasxpagar where ctp_forma_pago!='RETENCION'");
        }
    }

    function lista_una_factura($id) {
        if ($this->con() == true) {
            return pg_query("select * from erp_reg_documentos where reg_id=$id");
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

}

$Obj = new Objeto();
$sms = 0;

$cns = $Obj->lista_cuentas();

while ($rst = pg_fetch_array($cns)) {

    $asiento = $Obj->siguiente_asiento();
    $fec = $rst[ctp_fecha];
    $id = $rst[ctp_id];
    $rst_fac = pg_fetch_array($Obj->lista_una_factura($rst[reg_id]));
    $emi = 1;
    $ctas = pg_fetch_array($Obj->lista_asientos_ctas($emi, '26'));

    $dat5 = Array($asiento,
        $rst[ctp_concepto],
        $rst[num_documento],
        $fec,
        $ctas[pln_codigo],
        $rst[ctp_banco],
        round($rst[ctp_monto], 2),
        round($rst[ctp_monto], 2),
        '1',
        '11',
        $id,
        $rst_fac[cli_id]
    );
//        print_r($dat5);
    if ($Obj->insert_asientos($dat5) == false) {
        $sms = pg_last_error();
    }
}
echo $sms;
?>
