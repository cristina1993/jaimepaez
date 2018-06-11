<?php

//$_SESSION[User]='PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once("../Clases/clsClase_inv_fisico.php");
$Clase_inv_fisico = new Clase_inv_fisico();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
//$emisor = $_GET[emisor];
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;
        $n = 0;
        while ($n < count($data)) {
            $dat = explode('&', $data[$n]);
            $rst_inv = pg_fetch_array($Clase_inv_fisico->total_ingreso_egreso_fac($dat[0], $dat[2], $dat[7]));
            $inv = $rst_inv[ingreso] - $rst_inv[egreso];
            if ($Clase_inv_fisico->insert_inv_fisico($dat, $inv) == false) {
                $sms = pg_last_error();
            }
            $n++;
            $doc = $dat[1];
        }
        if ($sms == 0) {
            $j = 0;
            while ($j < count($fields)) {
                $f = $f . strtoupper($fields[$j] . '&');
                $j++;
            }
            $modulo = 'INVENTARIO FISICO';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $doc) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 1:
        if ($Prod->delete($id) == true) {
            $sms = 0;
            if ($Clases_preciospt->del_pre($id, '0') == false) {
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
        if ($id >= 10) {
            $emi = '0'. $id;
        } else {
            $emi = '00' . $id;
        }
        $rst = pg_fetch_array($Clase_inv_fisico->lista_secuencial_inventario($id));
        $sec = (substr($rst[inv_num_documento], -5) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '00000000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '0000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '00000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '0000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $txt = '00';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $txt = '0';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $txt = '';
        }
        echo $emi . '-' . $txt . $sec;
        break;
    case 3:


        $rst = pg_fetch_array($Clase_inv_fisico->lista_un_producto_id($id));
        if ($rst[id] != '') {
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&0';
        }


        break;
    case 4:
        $rst = pg_fetch_array($Clase_inv_fisico->lista_un_producto_codigo($id));
        if ($rst[id] != '') {
            echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&0';
        }
        break;
}
?>
