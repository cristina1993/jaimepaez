<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_cuentasxcobrar.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$CuentasCobrar = new CuentasCobrar();
$CuentasPagar = new CuentasPagar();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$x = $_REQUEST[x];
$s = $_REQUEST[s];

switch ($op) {
    case 0:
        $sms = 0;
        $rst1 = pg_fetch_array($CuentasCobrar->listar_un_asiento($data[5]));
        $monto = $data[2];

        if ($CuentasCobrar->insert_ctasxcobrar($data, $rst1[pln_id]) == false) {
            $sms = pg_last_error();
        } else {
            if ($x != 0) {
                $fd = pg_num_rows($CuentasPagar->listar_una_ctapagar_comid($data[9]));
                $fc = pg_num_rows($CuentasPagar->lista_pagos_regfac($data[9]));
                if ($fd == $fc) {
                    $rst2 = pg_fetch_array($CuentasPagar->lista_pagos_regfac($data[9], 'desc'));
                } else {
                    $rst2 = pg_fetch_array($CuentasCobrar->buscar_un_pago($data[9]));
                }
                if ($CuentasCobrar->insert_ctasxpagar($data, $rst1[pln_id], $rst2[pag_id]) == false) {
                    $sms = pg_last_error();
                }
                $monto = 0;
            }
            If ($data[3] == 'NOTA DE DEBITO') {
                $cd = $data[5];
                $ch = $data[4];
                $vd = $data[2];
                $vh = $monto;
            } else {
                $cd = $data[4];
                $ch = $data[5];
                $vd = $monto;
                $vh = $data[2];
            }
            $asiento = $CuentasCobrar->siguiente_asiento();
            $dat = Array(
                $asiento,
                $data[11],
                $data[10],
                $data[6],
                $cd,
                $ch,
                $vd,
                $vh,
                '0'
            );
            if ($CuentasCobrar->insert_asientos($dat) == false) {
                $sms = pg_last_error();
            } else {
                if (!empty($data[13])) {
                    $rst_chq = pg_fetch_array($CuentasCobrar->lista_un_cheque($data[13]));
                    $mto = $rst_chq[chq_cobro] + $data[2];
                    if ($CuentasCobrar->updte_cheque($data[13], $mto) == false) {
                        $sms = pg_last_error();
                    }
                }
            }
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'CTAS X COBRAR';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 1:
        if ($CuentasCobrar->delete_asientos($id) == true) {
            $sms = 0;
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:

        $rst1 = pg_fetch_array($CuentasCobrar->lista_cliente_ced($data));
        $rst2 = pg_fetch_array($CuentasCobrar->buscar_una_factura($id, $rst1[cli_ced_ruc]));
        $rst3 = pg_fetch_array($CuentasCobrar->suma_pagos_documentos($rst2[reg_id]));
        $saldo = $rst3[pago] - $rst3[monto];
        if ($rst3[pago] == '') {
            echo $rst2[reg_total] . '&' . $rst2[reg_id];
        } else {
            echo $saldo . '&' . $rst2[reg_id];
        }

    case 3:
        $rst1 = pg_fetch_array($CuentasCobrar->listar_un_asiento($id));
        echo $rst1[pln_descripcion];
        break;
    case 4:
        if ($id == 'CHEQUE') {
            $doc = '<=2';
        } else if ($id == 'RETENCION') {
            $doc = '=5';
        } else if ($id == 'NOTA DE CREDITO') {
            $doc = '=3';
        } else if ($id == 'NOTA DE DEBITO') {
            $doc = '=4';
        }
        if ($s == 0) {
            $cns = $CuentasCobrar->lista_docs(strtoupper($data), $doc);
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[chq_monto] - $rst[chq_cobro];
                if ($nm != 0) {
                    if ($n == 1) {
                        $cli .= "<tr><td></td><td></td><td>#Doc</td><td>Monto</td>";
                    }
                    $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_docs('$rst[chq_id]')" . " /></td><td>$n</td><td>$rst[chq_numero]</td><td>" . number_format($nm, 2) . "</td></tr>";
                }
            }
            echo $cli;
        } else {
            $sms = 0;
            $rst = pg_fetch_array($CuentasCobrar->lista_un_cheque($id));
            if (!empty($rst)) {
                $mto = $rst[chq_monto] - $rst[chq_cobro];
                $sms = $rst[chq_id] . '&' . $rst[chq_numero] . '&' . $mto;
            }
            echo $sms;
        }


        break;
}
?>
