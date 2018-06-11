<?php

//$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_asientos.php';
$Clase_asientos = new Clase_asientos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields]; 
switch ($op) {
    case 0:
        $sms = 0;
        $n = 0;
        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);

            if (empty($dat[8])) {
                if ($Clase_asientos->insert_asientos($dat) == false) {
                    $sms = pg_last_error();
                } else {

                    $j = 0;
                    while ($j < count($fields)) {
                        $f = $f . strtoupper($fields[$j] . '&');
                        $j++;
                    }
                    $modulo = 'ASIENTOS';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[0]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            } else {
                if ($Clase_asientos->upd_asientos($dat) == false) {
                    $sms = pg_last_error();
                } else {
                    $j = 0;
                    while ($j < count($fields)) {
                        $f = $f . strtoupper($fields[$j] . '&');
                        $j++;
                    }
                    $modulo = 'ASIENTOS';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $dat[0]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
            }
            $n++;
        }
        echo $sms;
        break;
    case 100:
        $sms = 0;
        echo $id;
        if (empty($id)) {
            //print_r($data);
//            $n = 0;
//            while ($n < count($data)) {
//                $dt = explode('&', $data[$n]);
//                $dat = Array($dt[0],
//                    strtoupper($dt[1]),
//                    $dt[2],
//                    $dt[3],
//                    $dt[4],
//                    $dt[5],
//                    $dt[6],
//                    $dt[7],
//                    $id
//                );
//                if ($dt[0] != 'undefined') {
//                    if ($Clase_asientos->insert_asientos($dat) == false) {
//                        $sms = pg_last_error();
//                    } else {
//                        $fields = str_replace("&", ",", $fields[0]);
//                        $modulo = 'PedidoPro';
//                        $accion = 'Insert';
//                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                            $sms = "Auditoria" . pg_last_error();
//                        }
//                    }
//                }
//                $n++;
//            }
        } else {
            $n = 0;
            //print_r($data);
//            while ($n < count($data)) {
//                $dt = explode('&', $data[$n]);
//                if ($Clase_asientos->upd_asientos($dt) == false) {
//                    $sms = pg_last_error();
//                } else {
//                    $fields = str_replace("&", ",", $fields[0]);
//                    $modulo = 'EgresoPro';
//                    $accion = 'Insert';
//                    if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//                        $sms = "Auditoria" . pg_last_error();
//                    }
//                }
//                $n++;
//            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_asientos->delete_asientos($id) == true) {
            $sms = 0;
            $n = 0;
            $f = $nom;
            $modulo = 'ASIENTOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        $rst_sts = pg_fetch_array($Clase_asientos->lista_un_asiento($id));
        if ($rst_sts[con_estado] == 0) {
            $sts = 1;
        } else {
            $sts = 0;
        }
        if ($Clase_asientos->update_asientos($sts, $id) == true) {
            $sms = 'Mov' . pg_last_error();
        }
        break;
    case 3:
        $rst1 = pg_fetch_array($Clase_asientos->listar_un_asiento($id));
        echo $rst1[pln_descripcion];
        break;
}
?>
