<?php

include_once '../Clases/clsClase_central_costo.php';
include_once("../Clases/clsAuditoria.php");
$Set = new Clase_central_costo();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
$nom = $_REQUEST[nom];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $tipo) {
                $str[$n] = strtoupper($tipo);
                $n++;
            }

            $m = 0;
            while ($m < count($str)) {
                $dt = explode('&', $str[$m]);
                if ($Set->insert_central_costo($dt) == FALSE) {
                    $sms.= pg_last_error();
                }
                $m++;
            }
            if ($sms = 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CENTRAL COSTOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Set->delete_central_costo($id) == false) {
                $sms = pg_last_error();
            } else {
                $str = $data;
                $n = 0;
                foreach ($str as $row => $tipo) {
                    $str[$n] = strtoupper($tipo);
                    $n++;
                }


                $m = 0;
                while ($m < count($str)) {
                    $dt = explode('&', $str[$m]);
                    if ($Set->insert_central_costo($dt) == FALSE) {
                        $sms.= pg_last_error();
                    }
                    $m++;
                }
            }
            if ($sms = 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'CENTRAL COSTOS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Set->delete_central_costo($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'CENTRAL COSTOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Set->lista_una_cuenta($id));
        echo $rst[pln_id] . '&&' . $rst[pln_codigo] . '&&' . $rst[pln_descripcion];
        break;
}
?>
