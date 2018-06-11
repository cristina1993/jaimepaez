<?php

include_once '../Clases/clsClase_bancos_y_cajas.php';
include_once("../Clases/clsAuditoria.php");
$Clase_bancos_y_cajas = new Clase_bancos_y_cajas();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if ($id == 0) {
            if ($Clase_bancos_y_cajas->insert_bancos_cajas($data) == FALSE) {
                $sms = 'Insert Bancos Cajas' . pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'BANCOS Y CAJAS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok1';
                }
            }
        } else {
            if ($Clase_bancos_y_cajas->update_bancos_cajas($data, $id) == FALSE) {
                $sms = 'Update Bancos Cajas' . pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'BANCOS Y CAJAS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $cta = pg_fetch_array($Clase_bancos_y_cajas->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion];
        break;
    case 2:
        $sms = 0;
        $std = $_REQUEST[std];
        if ($Clase_bancos_y_cajas->upd_estado_plan_cuentas($std, $id) == false) {
            $sms = 'Update estado plan cuentas' . pg_last_error();
        }
        echo $sms;
        break;
}
?>
