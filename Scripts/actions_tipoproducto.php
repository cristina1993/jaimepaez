<?php

include_once '../Clases/clsClase_tipoproducto.php';
include_once("../Clases/clsAuditoria.php");
$Clase_tipoproducto = new Clase_tipoproducto();
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
//            $sig1 = pg_fetch_array($Clase_tipoproducto->Lista_tipos_siglas(strtoupper($data[2]), $data[1]));
//            if (empty($sig1)) {
                if ($Clase_tipoproducto->insert_tipoproducto($str) == FALSE) {
                    $sms = pg_last_error();
                } else {

                    $n = 0;
                    while ($n < count($fields)) {
                        $f = $f . strtoupper($fields[$n] . '&');
                        $n++;
                    }
                    $modulo = 'TIPO SET';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }
                }
//            } else {
//                $sms = 'Siglas duplicadas';
//            }
        } else {
            if ($Clase_tipoproducto->upd_tipoproducto($data, $id) == FALSE) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'TIPO SET';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
   case 1:
        if ($Clase_tipoproducto->delete_tipoproducto($id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $nom;
            $modulo = 'TIPO SET';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
}
?>
