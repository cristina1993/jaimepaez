<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_acceso.php';
include_once '../Clases/clsMail_registro.php';

$Clase_acceso = new Clase_acceso();
$Adt = new Auditoria();
$Obj = new Mail();

$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if ($Clase_acceso->insert_acceso($data) == false) {
            $sms = pg_last_error();
        } else {
            $sms = $Obj->envia_correo_reg(strtolower($data[2]),strtoupper($data[0]), strtoupper($data[1]), strtoupper($data[3]));

            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'SOLICITUD ACCESO';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }

        echo $sms;
        break;
}
?>
