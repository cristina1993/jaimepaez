<?php

set_time_limit(0);

class Objeto {

    function con() {
        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=SuremandaS495');
//        return pg_connect('host=localhost port=5432 dbname=erp2.5_server user=postgres password=1234');
    }

    function lista_facturas() {
        if ($this->con() == true) {
            return pg_query("select * from erp_reg_documentos where reg_estado<>2");
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
            return pg_query("SELECT * FROM  erp_reg_det_documentos where reg_id='$num'");
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
            return pg_query("SELECT (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='0') as desc0,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='12') as desc12,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='EX') as descex,
                                    (SELECT sum(dfc_val_descuento) FROM  erp_det_factura where fac_id=$doc and dfc_iva='NO') as descno");
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
            return pg_query("select * from erp_reg_documentos where reg_id=$id");
        }
    }

    function lista_sum_cuentas($id) {
        if ($this->con() == true) {
            return pg_query("select pln_id,reg_codigo_cta, sum(det_total) as dtot, sum(det_descuento_moneda) as ddesc  from erp_reg_det_documentos where reg_id=$id group by pln_id,reg_codigo_cta");
        }
    }

    function lista_retencion($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM  erp_retencion where reg_id='$id' and (ret_estado_aut is null or ret_estado_aut<>'ANULADO') ");
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

    function verifica_ctas_det($id) {
        if ($this->con() == true) {
            return pg_query("select f.reg_estado,f.reg_num_registro,d.* from erp_reg_det_documentos d, erp_reg_documentos f where d.reg_id=f.reg_id and f.reg_estado<>2  and pln_id=0 and f.reg_id=$id");
        }
    }

    function lista_cuenta_contable($id) {
        if ($this->con() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas WHERE pln_id=$id");
        }
    }

    function update_asiento($as, $id) {
        if ($this->con() == true) {
            return pg_query("UPDATE erp_reg_documentos SET con_asiento='$as' WHERE reg_id=$id");
        }
    }

}

$Obj = new Objeto();
$sms = 0;

$cns = $Obj->lista_facturas();
while ($rst = pg_fetch_array($cns)) {
    $rst_cta_det = pg_fetch_array($Obj->verifica_ctas_det($rst[reg_id]));
    if (empty($rst_cta_det)) {
        $asiento = $Obj->siguiente_asiento();
        $fec = $rst[reg_femision];
        $num_doc = $rst[reg_num_documento];
        $id = $rst[reg_id];
        $cns_det = $Obj->lista_det_fac($id);
        $emi = 1;
        $ctas = pg_fetch_array($Obj->lista_asientos_ctas($emi, '26'));
        $iv = pg_fetch_array($Obj->lista_asientos_ctas($emi, '27'));
        $ic = pg_fetch_array($Obj->lista_asientos_ctas($emi, '28'));
        $irb = pg_fetch_array($Obj->lista_asientos_ctas($emi, '29'));
        $cdesc = pg_fetch_array($Obj->lista_asientos_ctas($emi, '30'));
        $prop = pg_fetch_array($Obj->lista_asientos_ctas($emi, '31'));

        $id = $rst[reg_id];
        $cns_sum = $Obj->lista_sum_cuentas($rst[reg_id]);
        while ($rst1 = pg_fetch_array($cns_sum)) {
            $dat_asi_det = array(
                $asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                $rst1[reg_codigo_cta],
                '',
                round($rst1[dtot] + $rst1[ddesc], 2),
                0,
                '1',
                '5',
                $id,
                $rst[cli_id]
            );

            if ($Obj->insert_asientos($dat_asi_det) == false) {
                $sms = 'asi_factura' . pg_last_error();
                $aud = 1;
            }
        }

/////descuento/////
        if ($rst[reg_tdescuento] > 0) {
            $dat0 = Array($asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                '',
                $cdesc[pln_codigo],
                '0',
                round($rst[reg_tdescuento], 2),
                '1',
                '5',
                $id,
                $rst[cli_id]
            );
        }



        /////iva/////
        if ($rst[reg_iva12] > 0) {
            $dat1 = Array($asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                $iv[pln_codigo],
                '',
                round($rst[reg_iva12], 2),
                '0',
                '1',
                '5',
                $id,
                $rst[cli_id]
            );
        }


        /////ice/////
        if ($rst[reg_ice] > 0) {
            $dat2 = Array($asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                $ic[pln_codigo],
                '',
                round($rst[reg_ice], 2),
                '0',
                '1',
                '5',
                $id,
                $rst[cli_id]
            );
        }


        /////irbp/////
        if ($rst[reg_irbpnr] > 0) {
            $dat3 = Array($asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                $irb[pln_codigo],
                '',
                round($rst[reg_irbpnr], 2),
                '0',
                '1',
                '5',
                $id,
                $rst[cli_id]
            );
        }



        /////propina/////
        if ($rst[reg_propina] > 0) {
            $dat4 = Array($asiento,
                $rst[reg_concepto],
                $num_doc,
                $fec,
                $prop[pln_codigo],
                '',
                round($rst[reg_propina], 2),
                '0',
                '1',
                '5',
                $id,
                $rst[cli_id]
            );
        }


        /////proveedores/////
        $rst_rete = pg_fetch_array($Obj->lista_retencion($id));
        if (!empty($rst_rete)) {
            $total = round($rst[reg_total], 2) - round($rst_rete[ret_total_valor], 2);
        } else {
            $total = round($rst[reg_total], 2);
        }
        $dat5 = Array($asiento,
            $rst[reg_concepto],
            $num_doc,
            $fec,
            '',
            $ctas[pln_codigo],
            '0',
            round($total, 2),
            '1',
            '5',
            $id,
            $rst[cli_id]
        );


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



        ////si existe retencion ////
        if (!empty($rst_rete)) {
            $cns_dt_ret = $Obj->lista_detalle_retencion($rst_rete[ret_id]);
            while ($rst_drt = pg_fetch_array($cns_dt_ret)) {
                $rst_idcta = pg_fetch_array($Obj->lista_id_cuenta($rst_drt[por_id]));
                $rst_cta = pg_fetch_array($Obj->lista_cuenta_contable($rst_idcta[cta_id]));
                $concepto = $rst_idcta[por_descripcion] . ' ' . $rst_idcta[por_codigo];
                $dt_asi = array(
                    $asiento,
                    $concepto,
                    $rst_rete[ret_numero],
                    $fec,
                    '',
                    $rst_cta[pln_codigo],
                    '0',
                    round($rst_drt[dtr_valor], 2),
                    '1',
                    '4',
                    $rst_rete[ret_id],
                    $rst[cli_id]
                );
                if ($Obj->insert_asientos($dt_asi) == false) {
                    $sms = 'insert_asiento_detret_reg_fac' . pg_last_error();
                    $aud = 1;
                }
            }
        }

        if ($Obj->update_asiento($asiento, $id) == false) {
            $sms = pg_last_error();
        }
    }
}

echo $sms;
?>
