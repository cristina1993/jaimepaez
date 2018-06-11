<?php

include_once '../Clases/clsClase_central_costo.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_central_costo();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$id = $_GET[id];
$id = explode(',', $id);
//echo count($id);
//$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
$dc = 2;

class PDF extends FPDF {

    function encabezado($desde, $hasta, $id) {
        sort($id);
        $titulo = "";
        if (count($id) == 1) {
            $t = explode('--', $id[0]);
            $titulo = $t[0];
        } else {
            $n = 0;
            while ($n < count($id)) {
                $t = explode('--', $id[$n]);
                $titulo.=$t[1] . ' / ';
                $n++;
            }
        }
        $this->SetFont('helvetica', 'B', 12);
        $this->MultiCell(200, 5, "CENTRAL DE COSTOS $titulo", '', 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "PERIODO " . $desde . '  AL ' . $hasta, 0, 0, 'C');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->Ln();
        $this->Cell(20, 5, "CODIGO", 'TBLR', 0, 'C');
        $this->Cell(120, 5, "CUENTA", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "DEBE", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "HABER", 'TBLR', 0, 'C');
        $this->Ln();
    }

    function asientos($desde, $hasta, $id, $dc, $Set) {
        sort($id);
        $n = 0;
        while ($n < count($id)) {
            $this->SetFont('helvetica', '', 6);
            $idc = explode('--', $id[$n]);
            $cns = $Set->lista_una_central($idc[1]);
            $total_debe=0;
            $total_haber=0;
            while ($rst = pg_fetch_array($cns)) {
                $rst_suma = pg_fetch_array($Set->lista_suma_cuentas($rst[pln_codigo], $desde, $hasta));
                $this->Cell(20, 5, $rst[pln_codigo], 'LR', 0, 'L');
                $this->Cell(120, 5, strtoupper($rst[pln_descripcion]), 'LR', 0, 'L');
                $this->Cell(20, 5, number_format($rst_suma[debe], $dc), 'LR', 0, 'R');
                $this->Cell(20, 5, number_format($rst_suma[haber], $dc), 'LR', 0, 'R');
                $debe = $rst_suma[debe];
                $haber = $rst_suma[haber];
                $total = $debe - $haber;

                $total_debe = $total_debe + $debe;
                $total_haber = $total_haber + $haber;
                $this->Ln();
            }
            $this->SetFont('helvetica', 'B', 6);
            $this->SetFillColor(200, 200, 200);
            $this->Cell(140, 5, 'SUMA TOTAL ', 'TBRL', 0, 'C', true);
            $this->Cell(20, 5, number_format($total_debe, $dc), 'TBR', 0, 'R', true);
            $this->Cell(20, 5, number_format($total_haber, $dc), 'TBR', 0, 'R', true);
            $this->Ln();
            $tot_debe+=$total_debe;
            $tot_haber+=$total_haber;
            $n++;
        }
        if($n>1){
            $this->SetFont('helvetica', 'B', 8);
            $this->SetFillColor(200, 200, 200);
            $this->Cell(140, 5, 'TOTAL ', 'TBRL', 0, 'C', true);
            $this->Cell(20, 5, number_format($tot_debe, $dc), 'TBR', 0, 'R', true);
            $this->Cell(20, 5, number_format($tot_haber, $dc), 'TBR', 0, 'R', true);
            $this->Ln();
        }
    }

    function Footer() {
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(20, 5, '');
        $this->Ln();
        $this->Cell(20, 5, '');
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
$pdf->encabezado($desde, $hasta, $id);
$pdf->encabezado_tab();
$pdf->asientos($desde, $hasta, $id, $dc, $Set);
$pdf->Output();



