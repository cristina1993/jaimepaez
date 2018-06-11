<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_multimedia.php';
$Mltm = new Multimedia();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if ($id == 0) {
            if (!$Mltm->insert_multimedia($data)) {
                $sms = pg_last_error();
            }
        } else {
            if (!$Mltm->update_multimedia($data, $id)) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Mltm->delete_mult($id)) {
            unlink('../Multimedia/Archivos/' . $_REQUEST[file]);
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        if ($Mltm->delete_sms_mult($id)) {
            if (!$Mltm->insert_sms_multimedia($id)) {
                $sms = pg_last_error();
            }
        }else{
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
