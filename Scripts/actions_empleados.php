<?php

include_once '../Clases/clsClase_empleados.php';
include_once("../Clases/clsAuditoria.php");
$Clase_empleados = new Clase_empleados();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_empleados->insert_empleado($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $fields = str_replace("&", ",", $fields[0]);
                $modulo = 'EMPLEADOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        } else {
            if ($Clase_empleados->update_empleado($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $fields = str_replace("&", ",", $fields[0]);
                $modulo = 'EMPLEADOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_empleados->delete_transportista($id) == false) {
            $sms = pg_last_error();
        } else {
            $fields = str_replace("&", ",", $fields[0]);
            $modulo = 'EMPLEADOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>
