<?php

include_once '../Clases/clsClase_asientos.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$txt = " where con_fecha_emision between '$desde' and '$hasta'";
$cns = $Set->lista_total_asientos_fecha($txt);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
$dc=2;
class PDF extends FPDF {

    function encabezado($emisor, $desde, $hasta) {
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "REPORTE LIBRO DIARIO", 0, 0, 'C');
        $this->Ln();
//        $this->Cell(200, 5, "TIVKA SYSTEMS S.A.", 0, 0, 'C');
//        $this->Ln();
//        $this->Cell(200, 5, "RUC: " . $emisor[identificacion], 0, 0, 'C');
//        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "PERIODO  DESDE: " . $desde . '  AL ' . $hasta, 0, 0, 'C');
        $this->Ln();
        $this->Cell(85, 5, "MONEDA: DOLAR", 0, 0, 'L');
    }

    function encabezado_tab($as) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(85, 5, "ASIENTO No.: " . $as, 0, 0, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(10, 5, "No", 'TB', 0, 'C');
        $this->Cell(18, 5, "F. EMISION", 'TB', 0, 'C');
        $this->Cell(25, 5, "CODIGO", 'TB', 0, 'C');
        $this->Cell(45, 5, "CUENTA", 'TB', 0, 'C');
        $this->Cell(30, 5, "DOCUMENTO", 'TB', 0, 'C');
        $this->Cell(38, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(18, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(18, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
    }

    function asientos($as, $emisor, $desde, $hasta,$dc) {
        $Set = new Clase_asientos();
        $this->SetFont('helvetica', '', 8);
        $cns_cuentas = $Set->lista_cuentas_asientos($as);
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

            $rst_v = pg_fetch_array($Set->listar_debe_haber_asiento_cuenta($as, $cuentas[$n]));

            $this->Cell(10, 5, $j, 0, 0, 'L');
            $this->Cell(18, 5, $rst_v[fecha], 0, 0, 'L');
            $this->Cell(25, 5, $rst_cuentas1[pln_codigo], 0, 0, 'L');
            $this->Cell(45, 5, substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 24), 0, 0, 'L');
            $this->Cell(30, 5, $rst_v[documento], 0, 0, 'L');
            $this->Cell(38, 5, $rst_v[concepto], 0, 0, 'L');
            $this->Cell(18, 5, number_format($rst_v[debe], $dc), 0, 0, 'R');
            $this->Cell(18, 5, number_format($rst_v[haber], $dc), 0, 0, 'R');
            $this->Ln();
            $n++;
            $j++;
        }
    }

    function totales($as,$dc) {
        $Set = new Clase_asientos();
        $tot = pg_fetch_array($Set->suma_totales($as));
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(160, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(23, 5, number_format($tot[debe], $dc), '', 0, 'R');
        $this->Cell(17, 5, number_format($tot[haber], $dc), '', 0, 'R');
        $this->Ln();
    }

    function pie_pagina() {
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'PREPARADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'REVISADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'AUTORIZADO', 'T', 0, 'C');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $desde, $hasta);
$pdf->Ln();

while ($rst = pg_fetch_array($cns)) {
    $pdf->encabezado_tab($rst[con_asiento]);
    $pdf->asientos($rst[con_asiento], $emisor, $desde, $hasta,$dc);
    $pdf->Cell(202, 7, '', 'T', 0, 'L');
    $pdf->Ln();
    $pdf->totales($rst[con_asiento],$dc);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Ln();
}
$pdf->pie_pagina();
$pdf->Output();



