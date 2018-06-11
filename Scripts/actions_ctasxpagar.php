<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_cuentasxpagar.php';
include_once '../Clases/clsClase_cuentasxcobrar.php';
$CuentasPagar = new CuentasPagar();
$CuentasCobrar = new CuentasCobrar();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$x = $_REQUEST[x];
switch ($op) {
    case 0:
        $sms = 0;
        $rst1 = pg_fetch_array($CuentasPagar->listar_un_asiento(trim($data[5])));
        $monto = $data[2];
        $est = '1';
        if ($CuentasPagar->insert_ctasxpagar($data, $rst1[pln_id]) == false) {
            $sms = 'ctaxpagar' . pg_last_error();
        } else {

            if ($x != 0) {
                $fd = pg_num_rows($CuentasCobrar->listar_una_cta_comid($data[10]));
                $fc = pg_num_rows($CuentasCobrar->lista_pagos($data[10]));
                if ($fd == $fc || $fc == 1) {
                    $rst2 = pg_fetch_array($CuentasCobrar->lista_pagos($data[10], 'desc'));
                } else {
                    $rst2 = pg_fetch_array($CuentasPagar->buscar_un_pago($data[10]));
                }
                if ($CuentasPagar->insert_ctasxcobrar($data, $rst1[pln_id], $rst2[pag_id]) == false) {
                    $sms = 'ctasxcobrar' . pg_last_error();
                }
                $monto = '0';
                $est = '0';
            }

            $asiento = $CuentasPagar->siguiente_asientop();

            if (isset($_REQUEST[obl])) {
                $dat = Array(
                    $asiento,
                    'CUENTAS X PAGAR',
                    $data[9],
                    $data[6],
                    $data[5],
                    $data[4],
                    $data[2],
                    $monto,
                    $est,
                    $data[7],
                    $data[13]
                );
                if ($CuentasPagar->insert_asientosp2($dat) == false) {
                    $sms = 'asiento' . pg_last_error();
                }
                $rst_last_egr = pg_fetch_array($CuentasPagar->lista_ultimo_num_egreso());
                $txt = "0000000000";
                $num = strlen(round($rst_last_egr[obl_num_egreso] + 1));
                $n_egr = substr($txt, 0, (10 - $num)) . ($rst_last_egr[obl_num_egreso] + 1);
                $CuentasPagar->cambia_estado_obligacion_pago(3, $_REQUEST[obl], $data[6], $data[3], $data[11], $data[8], trim($data[4]), $n_egr);
            } else {
                ///asiento modificado para ingresar cliente
                $dat = Array(
                    $asiento,
                    'CXP '.$data[11],
                    $data[9],
                    $data[6],
                    $data[5],
                    $data[4],
                    $data[2],
                    $monto,
                    $est,
                    '11', //mod
                    $data[0], //doc_id
                    $data[13] //cli_id
                );
                if ($CuentasPagar->insert_asientosp($dat) == false) {
                    $sms = 'asiento' . pg_last_error();
                }
            }


            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'CTAS X PAGAR';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 1:
        if ($CuentasPagar->delete_asientos($id) == true) {
            $sms = 0;
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst1 = pg_fetch_array($CuentasPagar->lista_cliente_ced($data));
        $rst2 = pg_fetch_array($CuentasPagar->buscar_un_documento($id, $rst1[cli_ced_ruc]));
        $rst3 = pg_fetch_array($CuentasCobrar->suma_pagos($rst2[fac_id], $id));
        $saldo = $rst3[pago] - $rst3[monto];
        if ($rst3[pago] == '') {
            echo $rst2[fac_total_valor] . '&' . $rst2[fac_id];
        } else {
            echo $saldo . '&' . $rst2[fac_id];
        }

    case 3:
        $rst1 = pg_fetch_array($CuentasPagar->listar_un_asiento($id));
        echo $rst1[pln_descripcion];
        break;
}
?>
