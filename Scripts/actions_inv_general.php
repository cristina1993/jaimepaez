<?php

set_time_limit(0);
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_industrial_inventariopt.php';
$Inv_general = new Clase_industrial_inventariopt();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$bod = $_REQUEST[bod];
$auditor = $_REQUEST[auditor];
$fields = $_REQUEST[fields]; //Datos para auditoria
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $sms = 0;

        if ($Inv_general->limpiar_movpt_total() == false) {
            $sms = pg_last_error();
        } else {
            $n = 1;
            $n_bod = pg_num_rows($Inv_general->lista_emisores());
            while ($n <= $n_bod) {
                $cns = $Inv_general->lista_inv_productos($n);
                while ($rst = pg_fetch_array($cns)) {
                    $rst_inv = pg_fetch_array($Inv_general->total_ingreso_egreso($rst[pro_id], $n, $rst[mov_tabla]));
                    $inv = $rst_inv[ingreso] - $rst_inv[egreso];
                    $inv_costo = $rst_inv[ingresoc] - $rst_inv[egresoc];
                    $data = Array($rst[pro_id],$rst[mov_tabla],$inv,date('Y-m-d'),$n,$inv_costo);
                    if ($Inv_general->insert_movpt_total($data) == false) {
                        $sms = pg_last_error();
                    }
                }
                $n++;
            }
        }
        echo $sms;
        break;
}
?>
