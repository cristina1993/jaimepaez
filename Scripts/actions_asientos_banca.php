<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_asientos_banca.php';
$Clase_asientos = new Clase_asientos_banca();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields];
$s = $_REQUEST[s];
switch ($op) {
    case 0:
        $sms = 0;
        $n = 0;
        $v = 0;
        if (empty($id)) {
            $dat_sec = explode('&', $data[0]);
            $rst_sec = pg_fetch_array($Clase_asientos->listar_asiento_numero($dat_sec[0]));
            if (empty($rst_sec)) {
                while ($n < count($data)) {
                    $dat = explode('&', $data[$n]);

                    if ($Clase_asientos->insert_asientos($dat) == false) {
                        $sms = pg_last_error();
                    }
                    $n++;
                    if (!empty($dat[5]) && $v == 0) {
                        $doc = $dat[2];
                        $cta = $dat[5];
                        $v = 1;
                    }
                }

                if ($dat[8] == 1) {
                    $rst_obl = pg_fetch_array($Clase_asientos->lista_secuencial_obligaciones());
                    if (empty($rst_obl)) {
                        $sec = 'OP000001';
                    } else {
                        $txt = '000000';
                        $x = substr($rst_obl[obl_codigo], -6);
                        $x++;
                        $sec = 'OP' . substr($txt, 0, (6 - strlen($x))) . $x;
                    }

                    $data1 = array(
                        '0',
                        $sec,
                        $dat[10],
                        '0',
                        $dat[3],
                        'CHEQUE',
                        $dat[1],
                        '1',
                        $dat[11],
                        $dat[0],
                        $doc,
                        $cta
                    );
                    if ($Clase_asientos->inser_pago_obligaciones($data1) == false) {
                        $sms = pg_last_error();
                    }
                }
                $j = 0;
                while ($j < count($fields)) {
                    $f = $f . strtoupper($fields[$j] . '&');
                    $j++;
                }
                $modulo = 'ASIENTOS DE BANCOS Y CAJAS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
                $sms = 1;
            }
        } else {
            if ($Clase_asientos->delete_asientos($id) == false) {
                $sms = pg_last_error();
            } else {
                while ($n < count($data)) {
                    $dat = explode('&', $data[$n]);
                    if ($Clase_asientos->insert_asientos($dat) == false) {
                        $sms = pg_last_error();
                    }
                    if (!empty($dat[5]) && $v == 0) {
                        $doc = $dat[2];
                        $cta = $dat[5];
                        $v = 1;
                    }
                    $n++;
                }
                $data1 = array(
                    '0',
                    $sec,
                    $dat[10],
                    '0',
                    $dat[3],
                    'CHEQUE',
                    $dat[1],
                    '1',
                    $dat[11],
                    $dat[0],
                    $doc,
                    $cta
                );
                if ($Clase_asientos->update_pago_obligaciones($data1, $dat[0]) == false) {
                    $sms = pg_last_error();
                }
                $j = 0;
                while ($j < count($fields)) {
                    $f = $f . strtoupper($fields[$j] . '&');
                    $j++;
                }
                $modulo = 'ASIENTOS DE BANCOS Y CAJAS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_asientos->delete_asientos($id) == true) {
            $sms = 0;
            $n = 0;
            $f = $nom;
            $modulo = 'ASIENTOS DE BANCOS Y CAJAS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        if ($s == 0) {
            $cns = $Clase_asientos->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Clase_asientos->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id];
            }
            echo $sms;
        }
        break;
    case 3:
        $rst1 = pg_fetch_array($Clase_asientos->listar_un_asiento($id));
        echo $rst1[pln_descripcion];
        break;
}
?>
