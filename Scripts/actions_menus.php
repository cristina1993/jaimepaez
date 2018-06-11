<?php

include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$md_id = $_REQUEST[md_id];
$prc_id = $_REQUEST[prc_id];
switch ($op) {
    case 0:
        $sms = 0;
        if ($Adt->update_orden($data[3], $data[2], $id)) {
            if ($Adt->update_modulo($data[1], $md_id)) {
                if (!$Adt->update_proceso($data[0], $prc_id)) {
                    $sms = pg_last_error();
                }
            } else {
                $sms = pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if (!$Adt->eliminar_menus($id)) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
