<?php
include_once '../Clases/clsClase_impuestos.php';
include_once("../Clases/clsAuditoria.php");
$Clase_impuestos = new Clase_impuestos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_impuestos->insert_impuestos($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'IMPUESTOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Clase_impuestos->upd_impuestos($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                                
                $modulo = 'IMPUESTOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_impuestos->delete_impuestos($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'IMPUESTOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
        }
        echo $sms;
        break;
        case 2:
            $sms=0;
            $rst=  pg_fetch_array($Clase_impuestos->lista_una_cuenta_codigo($id));
            if(empty($rst)){
                $sms=1;
            }else{
                echo $rst[pln_id].'&'.$rst[pln_codigo].'&'.$rst[pln_descripcion];
            }
            
        break;
}
?>
