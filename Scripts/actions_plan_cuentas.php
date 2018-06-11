<?php

include_once '../Clases/clsClase_plan_cuentas.php';
include_once("../Clases/clsAuditoria.php");
$Clase_plan_cuentas = new Clase_plan_cuentas();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$cod = $_REQUEST[cod];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_plan_cuentas->insert_cuenta($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PLAN DE CUENTAS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        } else {
            if ($Clase_plan_cuentas->update_cuenta($id, $data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'PLAN DE CUENTAS';
                $accion = 'EDITAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_plan_cuentas->delete_cuenta($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $cod;
            $modulo = 'PLAN DE CUENTAS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
}
?>
