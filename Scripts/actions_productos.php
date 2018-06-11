<?php

//$_SESSION[User]='PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once("../Clases/clsClase_preciospt.php");
include_once '../Clases/clsClase_productos.php'; // cambiar clsClase_productos

$Prod = new Clase_Productos();
$Clases_preciospt= new Clase_preciospt();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $producto) {
                $str[$n] = strtoupper($producto);
                $n++;
            }
            
            if ($Prod->insert($str) == false) {
                $sms = pg_last_error();
            } else {
                $fields = str_replace("&", ",", $fields[0]);
                $modulo = 'Productos';
                $accion = 'Insert';
                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $producto) {
                $str[$n] = strtoupper($producto);
                $n++;
            }
            if ($Prod->upd($str, $id) == true) {
                $sms = 0;
                $fields = str_replace("&", ",", $fields[0]);
                $modulo = 'Productos';
                $accion = 'Editar';
                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            } else {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Prod->delete($id) == true) {
            $sms = 0;
            if ($Clases_preciospt->del_pre($id,'0') == false) {
                $sms = pg_last_error();
            }
            $fields = str_replace("&", ",", $fields[0]);
            $modulo = 'Productos';
            $accion = 'Eliminar';
            if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                $sms = "Auditoria" . pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $cns = $Prod->lista_combomp($id);
        $combo = "<option value='0'>Seleccione</option>";
        while ($rst = pg_fetch_array($cns)) {
            $combo.="<option value='$rst[mp_id]'>$rst[mp_referencia]</option>";
        }
        echo $retorno . '&' . $combo;
        break;
}
?>
