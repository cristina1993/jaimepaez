<?php

include_once '../Clases/clsAuditoria.php';
include_once '../Clases/clsClase_empresas.php';
$Emp = new Empresas();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];

switch ($op) {
    case 0:
        $sms = 0;
        if ($id == 0) {
            if (!$Emp->insert($data)) {
                $sms = pg_last_error();
            }
        } else {
            if (!$Emp->update($data,$id)) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
}
?>
