<?php
include_once '../Clases/clsClase_gerencia.php';
include_once("../Clases/clsAuditoria.php");
$Clase_gerencia = new Clase_gerencia();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_gerencia->insert_gerencia($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'GERENCIA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Clase_gerencia->upd_gerencia($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'GERENCIA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_gerencia->delete_gerencia($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'GERENCIA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
        }
        echo $sms;
        break;
}
?>
