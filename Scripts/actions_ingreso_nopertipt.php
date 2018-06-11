<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_ingreso_nopertipt.php';
include_once('../Clases/clsAuditoria.php');
$Clase_ingreso_nopertipt = new Clase_ingreso_nopertipt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$x = $_REQUEST[x];
switch ($op) {
    case 0:
        $sms = 0;
        if (empty($id)) {
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    strtoupper($dt[3]),
                    $dt[4],
                    $dt[5]
                );
                if ($dt[0] != 'undefined') {
                    if ($Clase_ingreso_nopertipt->insert_ingreso_noperti($dat) == FALSE) {
                        $sms = pg_last_error();
                    } else {
                        $fields = str_replace("&", ",", $fields[0]);
                        $modulo = 'BODEGA COMERCIAL';
                        $accion = 'INSERT';
                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                            $sms = "Auditoria" . pg_last_error();
                        }
                    }
                }
                $n++;
            }
        } else {
            $n = 0;
            while ($n < count($data)) {
                $dt = explode('&', $data[$n]);
                $dat = Array($dt[0],
                    $dt[1],
                    $dt[2],
                    $dt[3],
                    strtoupper($dt[4]),
                    $dt[5],
                    $dt[6],
                    $dt[7],
                    $dt[8],
                );
                if ($Clase_ingreso_nopertipt->upd_ingreso_noperti($dat) == false) {
                    $sms = pg_last_error();
                }else {
                        $fields = str_replace("&", ",", $fields[0]);
                        $modulo = 'BODEGA COMERCIAL';
                        $accion = 'UPDATE';
                        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
                            $sms = "Auditoria" . pg_last_error();
                        }
                    }
                $n++;
            }
        }
        echo $sms . '&' . $x;
        break;
    case 1:
        if ($Clase_ingreso_nopertipt->delete_ingreso_noperti($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Clase_ingreso_nopertipt->lista_un_producto($id));
        $id = $rst[pro_id];
        $cod = $rst[pro_codigo];
        $uni = $rst[pro_uni];
        $uni2 = $rst[pro_unidad2];
        echo $id . '&' . $cod . '&' . $uni . '&' . $uni2;

        break;
    case 3:
        $cns = $Clase_ingreso_nopertipt->lista_producto($id);
        while ($rst = pg_fetch_array($cns)) {
            $matriz.= '<option value="' . $rst[pro_descripcion] . '" >' . $rst[pro_descripccion] . '</option>';
        }
        echo $matriz;
        break;

    case 4:
        $rst = pg_fetch_array($Clase_ingreso_nopertipt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
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

        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:
        $rst_cl = pg_fetch_array($Clase_ingreso_nopertipt->lista_un_cliente($id));
        $retorno = $rst_cl[cli_id].'&'.trim($rst_cl['cli_apellidos'] . ' ' . $rst_cl['cli_nombres'] . ' ' . $rst_cl['cli_raz_social']);;
        echo $retorno;
        break;
}
?>
