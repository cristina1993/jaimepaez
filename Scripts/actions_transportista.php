<?php
include_once '../Clases/clsClase_transportista.php';
include_once("../Clases/clsAuditoria.php");
$Clase_transportista = new Clase_transportista();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_transportista->insert_transportista($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'TRANSPORTISTA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Clase_transportista->upd_transportista($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'TRANSPORTISTA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_transportista->delete_transportista($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'TRANSPORTISTA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
        }
        echo $sms;
        break;
}
?>
