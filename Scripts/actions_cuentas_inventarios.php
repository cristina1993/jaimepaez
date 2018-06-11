<?php

include_once '../Clases/clsAuditoria.php';
include_once '../Clases/clsClase_cuentas_inventarios.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$Set = new Clase_cuentas_inventarios();

$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];

switch ($op) {
    case 0:
        $sms = 0;
        $n = 0;
        $aud = 0;
        $i = count($data);
        while ($n < $i) {
            $dt = explode('&', $data[$n]);
            if ($Set->update_cuentas($dt[0], $dt[1], $dt[2]) == false) {
                $sms = pg_last_error();
                $aud = 1;
            }
            $n++;
        }
        if ($aud == 0) {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'CONFIGURACION INVENTARIOS';
            $accion = 'MODIFICAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }

        echo $sms;
        break;

    case 1:
        $cta = pg_fetch_array($Set->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion] . '&' . $cta[pln_estado];
        break;
}
?>
