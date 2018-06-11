<?php

include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsPermisosVacaciones.php';
$PerVac = new VacacionesPermisos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($PerVac->insertVacacionesPermisos($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }

                $modulo = 'REG.PERMISOS/VACACIONES';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($PerVac->editVacacionesPermisos($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }

                $modulo = 'REG.PERMISOS/VACACIONES';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($PerVac->delete_transportista($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'REG.PERMISOS/VACACIONES';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $data) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 2:
        $rst = pg_fetch_array($PerVac->buscar_empleado($id));
        if (empty($rst)) {
            $sms = 1;
        } else {
            $sms = $rst[emp_id] . '&' . $rst[emp_codigo] . '&' . $rst[emp_apellido_paterno] . '&' . $rst[emp_apellido_materno] . '&' . $rst[emp_nombres];
        }
        echo $sms;
        break;
    case 3:
        $rst = pg_fetch_array($PerVac->list_sec_vac_perm());
        if (empty($rst[reg_vac_documento])) {
            $rst[reg_vac_documento] = "1000000";
        }
        echo ($rst[reg_vac_documento] + 1);
        break;
}
?>
