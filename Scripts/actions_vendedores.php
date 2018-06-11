<?php

include_once '../Clases/clsClase_vendedores.php';
include_once("../Clases/clsAuditoria.php");
$Clase_vendedores = new Clase_vendedores();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$nom = $_REQUEST[nom];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            if ($Clase_vendedores->insert_vendedores($data) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'VENDEDORES';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($Clase_vendedores->upd_vendedores($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'VENDEDORES';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Clase_vendedores->delete_vendedores($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'VENDEDORES';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 2:
        $rst_usu = pg_fetch_array($Clase_vendedores->lista_usuario($id));
        echo strtoupper($rst_usu[usu_person]);
        break;
}
?>
