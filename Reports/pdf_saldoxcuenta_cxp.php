<?php

include_once '../Clases/clsClase_cuentasxpagar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasPagar();
$fec1 = $_GET[d];
$fec2 = $_GET[h];
$dec = $_GET[dec];

$nm = trim(strtoupper($_GET[txt]));
$estado = $_GET[e];

$txt = "and (f.reg_num_documento LIKE '%$nm%' or cl.cli_raz_social like '%$nm%' or f.reg_ruc_cliente like '%$nm%') and f.reg_femision between '$fec1' and '$fec2' and exists (select * from erp_pagos_documentos p where p.reg_id=f.reg_id)";
$cns = $Set->lista_documentos_ctas($txt);
$emisor = pg_fetch_array($Set->lista_emisor_id('1'));

class PDF extends FPDF {

    function encabezado($emisor, $fec2, $fec1) {
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "CUENTAS POR PAGAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "SALDO POR CUENTAS", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, utf8_decode($emisor[emi_nombre_comercial]), 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "RUC: " . $emisor[emi_identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, " AL " . $fec2, 0, 0, 'C');
        $this->Ln();
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(10, 5, "", '', 0, 'C');
        $this->Cell(30, 5, "COD. CONTABLE", 'TB', 0, 'C');
        $this->Cell(70, 5, "CLIENTE", 'TB', 0, 'L');
        $this->Cell(25, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(25, 5, "HABER", 'TB', 0, 'C');
        $this->Cell(25, 5, "SALDO", 'TB', 0, 'C');
        $this->Ln();
    }

    function doc($cns, $dec) {
        $Set = new CuentasPagar();
        $debito = 0;
        $credito = 0;
        $deudor = 0;
        $acreedor = 0;
        $cant = 0;
        $deb = 0;
        $cre = 0;
        $n = 0;
        while ($rst_doc = pg_fetch_array($cns)) {
            if (!empty($rst_doc[pln_id])) {
                $rst_pln = pg_fetch_array($Set->listar_una_cuenta_id($rst_doc[pln_id]));
                $cod = $rst_pln[pln_codigo];
            }
            if ($grup != $rst_doc[reg_ruc_cliente]) {
                $rst_cli = pg_fetch_array($Set->lista_cliente_ced($rst_doc[reg_ruc_cliente]));
                $cns_r = $Set->lista_pagos_ctas($rst_cli[cli_id]);
                while ($resp = pg_fetch_array($cns_r)) {
                    $s = ($resp[pag_cant] + $resp[debito]) - $resp[credito];
                    if ($s != 0) {
                        $cant+=$resp[pag_cant];
                        $deb+=$resp[debito];
                        $cre+=$resp[credito];
                    }
                }
//                $res = pg_fetch_array($Set->suma_documentos_cliente($rst_doc[reg_ruc_cliente]));
                $haber = $cre; // suma pagos cta
                $debe = $cant + $deb;
                $saldo = $debe - $haber;
                if ($saldo != 0) {
                    if ($debe > $haber) {
                        $deudor = $debe - $haber;
                    } else {
                        $acreedor = $debe - $haber;
                    }
                    if (round($debe, 2) != 0) {
                        $this->SetFont('Arial', '', 8);
                        $this->Cell(10, 5, "", '', 0, 'C');
                        $this->Cell(30, 5, $cod, 0, 0, 'L');
                        $this->Cell(70, 5, utf8_decode(substr($rst_doc[cli_raz_social], 0, 45)), 0, 0, 'L');
                        $this->Cell(25, 5, number_format($debe, $dec), 0, 0, 'R');
                        $this->Cell(25, 5, number_format($haber, $dec), 0, 0, 'R');
                        $this->Cell(25, 5, number_format($deudor, $dec), 0, 0, 'R');
                        $this->Ln();
                    }
                    $tdeb+=$debe;
                    $thab+=$haber;
                    $tdeu+=$deudor;
                    $tacr+=$acreedor;
                    $debe = 0;
                    $haber = 0;
                    $deudor = 0;
                    $acreedor = 0;
                    $cant = 0;
                    $deb = 0;
                    $cre = 0;
                    $grup = $rst_doc[reg_ruc_cliente];
                    $grup2 = $cod;
                }
            }
            $cod = '';
            $rst_doc[pln_id] = '';
        }
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(10, 5, "", '', 0, 'C');
        $this->Cell(60, 5, '', '', 0, 'R');
        $this->Cell(40, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(25, 5, number_format($tdeb, $dec), 'T', 0, 'R');
        $this->Cell(25, 5, number_format($thab, $dec), 'T', 0, 'R');
        $this->Cell(25, 5, number_format($tdeu, $dec), 'T', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec2, $fec1);
$pdf->encabezado_tab();
$pdf->doc($cns, $dec);
$pdf->Ln();
$pdf->Output();



