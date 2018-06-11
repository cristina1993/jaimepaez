<?php

include_once '../Clases/clsClase_asientos.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();
$desde = $_GET[desde];
$hasta = $_GET[hasta];
$niv = $_GET[nivel];
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));
$dc = 2;

class PDF extends FPDF {

    function encabezado($emisor, $desde, $hasta) {
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "BALANCE DE COMPROBACION", 0, 0, 'C');
        $this->Ln();
//        $this->SetFont('helvetica', 'B', 12);
//        $this->Cell(200, 5, "TIVKA SYSTEMS S.A.", 0, 0, 'C');
//        $this->Ln();
//        $this->SetFont('helvetica', 'B', 12);
//        $this->Cell(200, 5, $emisor[identificacion], 0, 0, 'C');
//        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "PERIODO " . $desde . '  AL ' . $hasta, 0, 0, 'C');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->Ln();
        $this->Cell(162, 5, "");
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(40, 5, "SALDO", 'TBLR', 0, 'C');
        $this->Ln();
        $this->Cell(16, 5, "CODIGO", 'TBLR', 0, 'C');
        $this->Cell(106, 5, "CUENTA", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "DEBE", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "HABER", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "DEUDOR", 'TBLR', 0, 'C');
        $this->Cell(20, 5, "ACREEDOR", 'TBR', 0, 'C');
        $this->Ln();
    }

    function asientos($emisor, $desde, $hasta, $niv, $dc) {
        $Set = new Clase_asientos();
        $this->SetFont('helvetica', '', 6);

        $cns = $Set->lista_cuentas_fecha($desde, $hasta);
        $cuentas0 = pg_fetch_all_columns($cns);
        $cuentas = array_unique($cuentas0);

        $n = 0;
        $g = 0;
        while ($n < count($cuentas)) {
//            $d1 = substr($cuentas[$n], 0, 2);
            $d = explode('.', $cuentas[$n]);
            $d1 = $d[0] . '.';
            $d11 = $d[0];
            if ($niv == 1) {
                if ($g != $d1) {
                    $rst1 = pg_fetch_array($Set->listar_descripcion_asiento($d1, $d11));
                    $sm = pg_fetch_array($Set->lista_balance_general1($d11, $desde, $hasta));
                    $this->Cell(16, 5, $rst1[pln_codigo], 'LR', 0, 'L');
//                    $this->Cell(16, 5, $d1, 'LR', 0, 'L');
                    $this->Cell(106, 5, $rst1[pln_descripcion], 'LR', 0, 'L');
                    $this->Cell(20, 5, number_format($sm[debe], $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($sm[haber], $dc), 'LR', 0, 'R');
                    $debe = $sm[debe];
                    $haber = $sm[haber];
                    $total = $debe - $haber;
                    if ($total > 0) {
                        $deudor = $total;
                    }
                    if ($total < 0) {
                        $acreedor = $total;
                    }
                    $this->Cell(20, 5, $deudor, 'LR', 0, 'R');
                    $this->Cell(20, 5, $acreedor, 'LR', 0, 'R');
                    $total_debe = $total_debe + $debe;
                    $total_haber = $total_haber + $haber;
                    $total_deudor = $total_deudor + $deudor;
                    $total_acreedor = $total_acreedor + $acreedor;
                    $this->Ln();
                    $deudor = '';
                    $acreedor = '';
                }
            }
            if ($niv == 2) {
//                $d2 = substr($cuentas[$n], 0, 5);
                $d2 = $d[0] . '.' . $d[1] . '.';
                $d21 = $d[0] . '.' . $d[1];
                if ($g2 != $d2) {
                    $rst2 = pg_fetch_array($Set->listar_descripcion_asiento($d2, $d21));
                    $sm2 = pg_fetch_array($Set->lista_balance_general1($d21, $desde, $hasta));
                    $this->Cell(16, 5, $rst2[pln_codigo], 'LR', 0, 'L');
                    $this->Cell(106, 5, $rst2[pln_descripcion], 'LR', 0, 'L');
                    $this->Cell(20, 5, number_format($sm2[debe], $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($sm2[haber], $dc), 'LR', 0, 'R');
                    $debe = $sm2[debe];
                    $haber = $sm2[haber];
                    $total = $debe - $haber;
                    if ($total > 0) {
                        $deudor = $total;
                    }
                    if ($total < 0) {
                        $acreedor = $total;
                    }
                    $this->Cell(20, 5, number_format($deudor, $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($acreedor, $dc), 'LR', 0, 'R');
                    $total_debe = $total_debe + $debe;
                    $total_haber = $total_haber + $haber;
                    $total_deudor = $total_deudor + $deudor;
                    $total_acreedor = $total_acreedor + $acreedor;
                    $this->Ln();
                    $deudor = '';
                    $acreedor = '';
                }
            }
            if ($niv == 3) {
//                $d3 = substr($cuentas[$n], 0, 8);
                $d3 = $d[0] . '.' . $d[1] . '.' . $d[2] . '.';
                $d31 = $d[0] . '.' . $d[1] . '.' . $d[2];
                if ($g3 != $d3) {
                    $rst3 = pg_fetch_array($Set->listar_descripcion_asiento($d3, $d31));
                    $sm3 = pg_fetch_array($Set->lista_balance_general1($d31, $desde, $hasta));
                    $this->Cell(16, 5, $rst3[pln_codigo], 'LR', 0, 'L');
                    $this->Cell(106, 5, $rst3[pln_descripcion], 'LR', 0, 'L');
                    $this->Cell(20, 5, number_format($sm3[debe], $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($sm3[haber], $dc), 'LR', 0, 'R');
                    $debe = $sm3[debe];
                    $haber = $sm3[haber];
                    $total = $debe - $haber;
                    if ($total > 0) {
                        $deudor = $total;
                    }
                    if ($total < 0) {
                        $acreedor = $total;
                    }
                    $this->Cell(20, 5, number_format($deudor, $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($acreedor, $dc), 'LR', 0, 'R');
                    $total_debe = $total_debe + $debe;
                    $total_haber = $total_haber + $haber;
                    $total_deudor = $total_deudor + $deudor;
                    $total_acreedor = $total_acreedor + $acreedor;
                    $this->Ln();
                    $deudor = '';
                    $acreedor = '';
                }
            }
            if ($niv == 4) {
//                $d4 = substr($cuentas[$n], 0, 11);
                $d4 = $d[0] . '.' . $d[1] . '.' . $d[2] . '.' . $d[3] . '.';
                $d41 = $d[0] . '.' . $d[1] . '.' . $d[2] . '.' . $d[3];
                if ($g4 != $d4) {
                    $rst4 = pg_fetch_array($Set->listar_descripcion_asiento($d4,$d41));
                    $sm4 = pg_fetch_array($Set->lista_balance_general1($d4, $desde, $hasta));
                    $this->Cell(16, 5, $rst4[pln_codigo], 'LR', 0, 'L');
                    $this->Cell(106, 5, $rst4[pln_descripcion], 'LR', 0, 'L');
                    $this->Cell(20, 5, number_format($sm4[debe], $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($sm4[haber], $dc), 'LR', 0, 'R');
                    $debe = $sm4[debe];
                    $haber = $sm4[haber];
                    $total = $debe - $haber;
                    if ($total > 0) {
                        $deudor = $total;
                    }
                    if ($total < 0) {
                        $acreedor = $total;
                    }
                    $this->Cell(20, 5, number_format($deudor, $dc), 'LR', 0, 'R');
                    $this->Cell(20, 5, number_format($acreedor, $dc), 'LR', 0, 'R');
                    $total_debe = $total_debe + $debe;
                    $total_haber = $total_haber + $haber;
                    $total_deudor = $total_deudor + $deudor;
                    $total_acreedor = $total_acreedor + $acreedor;
                    $this->Ln();
                    $deudor = '';
                    $acreedor = '';
                }
            }
            if ($niv == 5) {
                $rst_suma = pg_fetch_array($Set->lista_suma_cuentas($cuentas[$n], $desde, $hasta));
                $rst_cue = pg_fetch_array($Set->listar_descripcion_asiento($cuentas[$n]));
                $this->Cell(16, 5, $cuentas[$n], 'LR', 0, 'L');
                $this->Cell(106, 5, strtoupper($rst_cue[pln_descripcion]), 'LR', 0, 'L');
                $this->Cell(20, 5, number_format($rst_suma[debe], $dc), 'LR', 0, 'R');
                $this->Cell(20, 5, number_format($rst_suma[haber], $dc), 'LR', 0, 'R');
                $debe = $rst_suma[debe];
                $haber = $rst_suma[haber];
                $total = $debe - $haber;
                if ($total > 0) {
                    $deudor = $total;
                }
                if ($total < 0) {
                    $acreedor = $total;
                }
                $this->Cell(20, 5, number_format($deudor, $dc), 'LR', 0, 'R');
                $this->Cell(20, 5, number_format($acreedor, $dc), 'LR', 0, 'R');
                $total_debe = $total_debe + $debe;
                $total_haber = $total_haber + $haber;
                $total_deudor = $total_deudor + $deudor;
                $total_acreedor = $total_acreedor + $acreedor;
                $this->Ln();
            }
            $n++;
            $g = $d1;
            $g2 = $d2;
            $g3 = $d3;
            $g4 = $d4;
            $total = 0;
            $deudor = '';
            $acreedor = '';
        }
        $this->SetFont('helvetica', 'B', 6);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(25, 5, '', 'TBL', 0, 'R', true);
        $this->Cell(97, 5, 'SUMA TOTAL ', 'TBR', 0, 'C', true);
        if ($niv == 1 || $niv == 2 || $niv == 3 || $niv == 4 || $niv == 5) {
            $this->Cell(20, 5, number_format($total_debe, $dc), 'TBR', 0, 'R', true);
            $this->Cell(20, 5, number_format($total_haber, $dc), 'TBR', 0, 'R', true);
            $this->Cell(20, 5, number_format($total_deudor, $dc), 'TBR', 0, 'R', true);
            $this->Cell(20, 5, number_format($total_acreedor, $dc), 'TBR', 0, 'R', true);
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
$pdf->encabezado($emisor, $desde, $hasta);
$pdf->encabezado_tab();
$pdf->asientos($emisor, $desde, $hasta, $niv, $dc);
$pdf->Output();



