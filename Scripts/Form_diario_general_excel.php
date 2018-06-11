<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d'). ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_asientos.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$txt = " where con_fecha_emision between '$desde' and '$hasta'";
$cns = $Set->lista_total_asientos_fecha($txt);
$emisor = pg_fetch_array($Set->lista_emisor('1'));
$dc=2;
///*******Primer Enc********
echo "
    <table border='1'>
    <thead>
    <tr><th colspan='8'>REPORTE LIBRO DIARIO</th></tr>
    </thead>
    <tr><td colspan='8'>PERIODO  DESDE: " . $desde . "  HASTA: " . $hasta . "</td></tr>
    <tr><td colspan='8'>RUC: " . $emisor[emi_identificacion] . "</td></tr>
    <tr><td colspan='8'>RAZON SOCIAL:" . $emisor[emi_nombre_comercial] . " </td></tr>
    <tr><td colspan='8'>MONEDA: DOLAR</td></tr>
    <tr><td colspan='8'></td></tr>
    ";

while ($rst = pg_fetch_array($cns)) {
    echo "
        <tr><td colspan='8'>ASIENTO No.: " . $rst[con_asiento] . "</td></tr>    
        <tr>
        <td>No</td>
        <td>F. EMISION</td>
        <td>CODIGO</td>
        <td>CUENTA</td>
        <td>DOCUMENTO</td>
        <td>CONCEPTO</td>
        <td>DEBE</td>
        <td>HABER</td>
        </tr>        
    ";

    $cns_cuentas = $Set->lista_cuentas_asientos($rst[con_asiento]);
    $cuentas1 = Array();
    while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
        if (!empty($rst_cuentas[con_concepto_debe])) {
            array_push($cuentas1, $rst_cuentas[con_concepto_debe]);
        }

        if (!empty($rst_cuentas[con_concepto_haber])) {
            array_push($cuentas1, $rst_cuentas[con_concepto_haber]);
        }
    }
    $cuentas = array_unique($cuentas1);
    //Eliminar Duplicados del Array
    $n = 0;
    $j = 1;
    while ($n < count($cuentas)) {
        $rst_cuentas1 = pg_fetch_array($Set->listar_descripcion_asiento($cuentas[$n]));
        $rst_v = pg_fetch_array($Set->listar_debe_haber_asiento_cuenta($rst[con_asiento], $cuentas[$n]));
        echo"
            <tr>
            <td>" . $j . "</td>
            <td>" . $rst_v[fecha] . "</td>
            <td>" . $rst_cuentas1[pln_codigo] . "</td>
            <td>" . substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 24) . "</td>
            <td>" . $rst_v[documento] . "</td>
            <td>" . $rst_v[concepto] . "</td>
            <td>" . number_format($rst_v[debe], $dc) . "</td>
            <td>" . number_format($rst_v[haber], $dc) . "</td>
            </tr>
         ";
        $n++;
        $j++;
    }

    $tot = pg_fetch_array($Set->suma_totales($rst[con_asiento]));

    echo"
        <tr>
        <td colspan='5'></td>
        <td>TOTAL</td>
        <td>" . number_format($tot[debe], $dc) . "</td>
        <td>" . number_format($tot[haber], $dc) . "</td>
        </tr>
        <tr>
        <td colspan='8'></td>
        </tr>
           
         ";
}
echo "</table>";
?>