<?php

$tip = $_GET[tipo];
switch ($tip) {
    case 1:
        $nm = 'kardex_pt' . date('Ymd') . '.xls';
        break;
    case 2:
        $nm = 'kardex_mp' . date('Ymd') . '.xls';
        break;
    case 3:
        $nm = 'prec_pt' . date('Ymd') . '.xls';
        break;
    case 4:
        $nm = 'inventario_pt' . date('Ymd') . '.xls';
        break;
    case 5:
        $nm = 'inventario_mp' . date('Ymd') . '.xls';
        break;
    case 6:
        $nm = 'movimiento_pt' . date('Ymd') . '.xls';
        break;
    case 7:
        $nm = 'movimiento_mp' . date('Ymd') . '.xls';
        break;
    case 8:
        $nm = 'central_cobranza' . date('Ymd') . '.xls';
        break;
    case 9:
        $nm = 'ctasxpagar' . date('Ymd') . '.xls';
        break;
    case 10:
        $nm = 'pedidos_venta' . date('Ymd') . '.xls';
        break;
    case 11:
        $nm = 'rep_factura_compra' . date('Ymd') . '.xls';
        break;
    case 12:
        $nm = 'rep_retencion' . date('Ymd') . '.xls';
        break;
    case 13:
        $nm = 'registro_factura' . date('Ymd') . '.xls';
        break;
    case 14:
        $nm = 'rep_factura_venta' . date('Ymd') . '.xls';
        break;
    default :
        $nm = 'ventas' . date('Ymd') . '.xls';
        break;
}
header('Content-Type: application/force-download');
header('Content-disposition: attachment; filename=' . $nm);
// Fix for crappy IE bug in download.  
header("Pragma: ");
header("Cache-Control: ");
echo utf8_decode($_REQUEST['datatodisplay']);
?>