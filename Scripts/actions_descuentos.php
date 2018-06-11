<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
$Clase_preciospt = new Clase_preciospt();
$op = $_REQUEST[op];
$x = $_REQUEST[x];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$tab = $_REQUEST[tab];
switch ($op) {
    case 0:
        if (empty($x)) {
            $sms = 0;
            if ($Clase_preciospt->insert_descuento($data, $id) == false) {
                $sms = pg_last_error();
            }
//            else {
//                $fields = str_replace("&", ",", $fields[0]);
//                $modulo = 'PRECIOS PT';
//                $accion = 'UPDATE';
//                if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                    $sms = "Auditoria" . pg_last_error();
//                }
//            }
        } else {
            $sms = 0;
            if ($Clase_preciospt->update_descuentos($data, $id, $x) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms . '&' . $data[2];
        break;

    case 1:
        $sms = 0;
        $rst = pg_fetch_array($Clase_preciospt->lista_un_descuento_fecha($id, $tab, $x));
        if (!empty($rst)) {
            if ($rst[pro_tabla == 0]) {
                $rst1 = pg_fetch_array($Clase_preciospt->lista_i_productos($rst[pro_id]));
                $pro = $rst1[pro_descripcion];
            } else {
                $rst1 = pg_fetch_array($Clase_preciospt->lista_productos($rst[pro_id]));
                $pro = $rst1[pro_b];
            }
            echo $pro . '&' . $rst[des_fec_inicio] . '&' . $rst[des_fec_fin] . '&' . $rst[des_estado];
        } else {
            echo $sms;
        }
        break;
    case 2:
        $sms = 0;
        if ($Clase_preciospt->delete_descuentos($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;

        break;
}
?>
