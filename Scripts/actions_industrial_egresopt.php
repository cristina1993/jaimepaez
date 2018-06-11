<?php

$_SESSION[User] = 'PRUEBA';
//include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_egresopt.php'; // cambiar clsClase_productos

$Clase_industrial_egresopt = new Clase_industrial_egresopt();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields]; //Datos para auditoria
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
                    if ($Clase_industrial_egresopt->insert_industrial_ingresopt($dat) == false) {
                        $sms = pg_last_error();
                    } else {
                        $fields = str_replace("&", ",", $fields[0]);
                        $modulo = 'BodegaIndus';
                        $accion = 'Insert';
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
                if ($dt[0] != 'undefined') {
                    if ($Clase_industrial_egresopt->upd_industrial_ingreso($dat) == FALSE) {
                        $sms = pg_last_error();
                    }
                }
                $n++;
            }
        }
        echo $sms . '&' . $x;
        break;
    case 1:
        if ($Clase_industrial_egresopt->delete_industrial_ingreso($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 2:
        $rst = pg_fetch_array($Clase_industrial_egresopt->lista_un_producto($id));
        $id = $rst[pro_id];
        $codigo = $rst[pro_codigo];
        $uni = $rst[pro_uni];
        $uni2 = $rst[pro_uni2];
        echo $id . '&' . $codigo . '&' . $uni . '&' . $uni2;

        break;
    case 3:
        $cns = $Clase_industrial_egresopt->lista_producto($id);
        while ($rst = pg_fetch_array($cns)) {
            $producto.= '<option value="' . $rst[pro_descripcion] . '" >' . $rst[pro_descripccion] . '</option>';
        }
        echo $producto;
        break;
    case 4:
        $rst = pg_fetch_array($Clase_industrial_egresopt->lista_secuencial());
        $sec = (substr($rst[mov_documento], -5) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '000000000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '00000000';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '0000000';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '000000';
        } else if ($sec >= 10000 && $sec < 100000) {
            $txt = '00000';
        } else if ($sec >= 100000 && $sec < 1000000) {
            $txt = '0000';
        } else if ($sec >= 1000000 && $sec < 10000000) {
            $txt = '000';
        } else if ($sec >= 10000000 && $sec < 100000000) {
            $txt = '00';
        } else if ($sec >= 100000000 && $sec < 1000000000) {
            $txt = '0';
        } else if ($sec >= 1000000000 && $sec < 10000000000) {
            $txt = '';
        }

        $rst1 = pg_fetch_array($Clase_industrial_egresopt->lista_siglas($id));
        $retorno = $txt . $sec;
        echo $retorno;
        break;

    case 5:
        $rst_cli = pg_fetch_array($Clase_industrial_egresopt->lista_un_proveedor($id));
        $retorno = $rst_cli[cli_id];
        echo $retorno;
        break;
    case 6:
        $rst_tra = pg_fetch_array($Clase_industrial_egresopt->lista_transaccion($id));
        $retorno = $rst_tra[trs_descripcion];
        echo $retorno;
        break;
}
?>
