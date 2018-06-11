<?php

include_once '../Clases/clsClase_seguimiento_pedido.php';
include_once("../Clases/clsAuditoria.php");
$Clase_seguimiento_pedido = new Clase_seguimiento_pedido();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        $rst_ped = pg_fetch_array($Clase_seguimiento_pedido->lista_un_seguimiento($id));
        $dat = Array($rst_ped[pro_id], 4, $_REQUEST[ems], $rst_ped[bod_id], $rst_ped[seg_aux_orden], $rst_ped[seg_guia], $rst_ped[seg_fecha], $data, $rst_ped[seg_transportista]);        
        if ($Clase_seguimiento_pedido->update_seguimiento($data, $id) == true) {
            if ($Clase_seguimiento_pedido->insert_movimiento($dat) == false) {
                $sms = 'Mov' . pg_last_error();
            }
        } else {
            $sms = 'Seg' . pg_last_error();
        }
        echo $sms;
        break;
}
?>
