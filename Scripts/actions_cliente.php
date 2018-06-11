<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cliente = new Clase_cliente();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$id = $_REQUEST[id];
$l1 = $_REQUEST[l1];
$l2 = $_REQUEST[l2];
$fields = $_REQUEST[fields];
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }
            if ($Clase_cliente->insert_cliente($str) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }

                $modulo = 'Clientes';
                $accion = 'Insertar';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $str[3]) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $cliente) {
                $str[$n] = strtoupper($cliente);
                $n++;
            }

            if ($Clase_cliente->upd_cliente($str, $id) == false) {
                $sms = 'upd_cli' . pg_last_error();
            } else {

                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'Clientes';
                $accion = 'Editar';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $str[3]) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Clase_cliente->delete_cliente($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $data;
            $modulo = 'CLIENTES';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Clase_cliente->lista_secuencial_cliente($l1 . $l2));
        if ($l1 == 'CP') {
            $sec = (substr($rst[cli_codigo], 3, 8) + 1);
        } else {
            $sec = (substr($rst[cli_codigo], 2, 8) + 1);
        }
        if ($sec >= 0 && $sec < 10) {
            $txt = '0000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '00';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '0';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '';
        }
        if ($l1 == '0') {
            $retorno = '';
        } else {
            $retorno = $l1 . $l2 . $txt . $sec;
        }

        echo $retorno;
//        echo $sec;
        break;
    case 3:
        $sms = 0;
        $rst = pg_fetch_array($Clase_cliente->lista_una_ced_ruc($id));
        $ced_ruc = $rst[cli_ced_ruc];
        if (!empty($ced_ruc)) {
            $sms = 1;
        }
        echo $sms;
        break;
}
?>
