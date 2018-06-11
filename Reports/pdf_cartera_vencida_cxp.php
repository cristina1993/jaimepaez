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
$txt = " and (c.reg_num_documento LIKE '%$nm%' or cl.cli_raz_social like '%$nm%' or c.reg_ruc_cliente like '%$nm%')";
$cns = $Set->buscar_documentos_vencidos_cp(date('Y-m-d'), $fec1, $fec2, $txt);
$emisor = pg_fetch_array($Set->lista_emisor_id('1'));

class PDF extends FPDF {

    function encabezado($emisor, $fec1, $fec2) {
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "CUENTAS POR PAGAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "VENCIMIENTOS", 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, $emisor[emi_nombre_comercial], 0, 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "RUC: " . $emisor[emi_identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(200, 5, "AL " . $fec2, 0, 0, 'C');
        $this->Ln();
        $this->Ln();
    }

    function body($cns, $dec) {
        $Set = new CuentasPagar();
        $tip = '';
        $grup = '';
        $n = 0;
        while ($rst2 = pg_fetch_array($cns)) {
            $ast = '';
            $n++;
            $rst_cta = pg_fetch_array($Set->listar_una_cta_comid($rst2[reg_id]));
            if ($rst_cta[asiento] != '') {
                if ($rst_cta[asiento] == 0) {
                    $tip = 'FC.PENCION PRIMARIA';
                } else if ($rst_cta[asiento] == 1) {
                    $tip = 'FP.FACTURAS POS';
                } else if ($rst_cta[asiento] == 2) {
                    $tip = 'CA. CANCELACION FACTURAS';
                } else if ($rst_cta[asiento] == 3) {
                    $tip = 'AB. ABONO';
                } else if ($rst_cta[asiento] == 4) {
                    $tip = 'RC. RECIBO';
                } else if ($rst_cta[asiento] == 5) {
                    $tip = 'ND. NOTA DEBITO';
                } else if ($rst_cta[asiento] == 6) {
                    $tip = 'NC. NOTA CREDITO';
                } else if ($rst_cta[asiento] == 7) {
                    $tip = 'DD. AJUSTE DIF. CAMBIO DB';
                } else if ($rst_cta[asiento] == 8) {
                    $tip = 'DC. AJUSTE DIF. CAMBIO CR';
                } else if ($rst_cta[asiento] == 9) {
                    $tip = 'RF.RETENCION FUENTE';
                }
            }

            $rst = pg_fetch_array($Set->lista_ultimo_pago($rst2[reg_id]));
            $pag_fecha_v = $rst[pag_fecha_v];
            $res = pg_fetch_array($Set->suma_pagos1($rst2[reg_id]));
            $mto = $res[monto]; // suma pagos cta
            $pgo = $res[pago] + $res[debito]; //suma pagos
            $saldo = round(($pgo - $mto), $dec); // saldo

            if ($res[debito] != 0) {
                $ast = '*';
            }
            if ($rst[pag_fecha_v] < date('Y-m-d')) {
                $vencido = $saldo;
            } else {
                $vencido = 0;
            }
            if (!empty($vencido)) {
                if ($n > 1 && $grup != $rst2[reg_ruc_cliente] && !empty($tot)) {
                    $this->SetFont('helvetica', 'B', 8);
                    $this->Cell(75, 5, '', '', 0, 'R');
                    $this->Cell(45, 5, 'TOTAL ', '', 0, 'C');
                    $this->Cell(30, 5, number_format($tot, $dec), 'T', 0, 'R');
                    $this->Ln();
                    $this->Ln();
                    $tot = 0;
                }
                if ($grup != $rst2[reg_ruc_cliente]) {
                    $this->SetFont('Arial', 'B', 8);
                    $rst_cli = pg_fetch_array($Set->lista_cliente_ced($rst2[reg_ruc_cliente]));
                    $this->Cell(200, 5, "COD. CLIENTE: " . $rst_cli[cli_codigo], 0, 0, 'L');
                    $this->Ln();
                    $this->Cell(200, 5, "CLIENTE: " . utf8_decode($rst2[cli_raz_social]), 0, 0, 'L');
                    $this->Ln();
//                    $this->Cell(50, 5, "TIPO", 'TB', 0, 'L');
                    $this->Cell(35, 5, "FACTURA", 'TB', 0, 'L');
                    $this->Cell(30, 5, "F.EMISION", 'TB', 0, 'L');
                    $this->Cell(30, 5, "F.VENCIMIENTO", 'TB', 0, 'L');
                    $this->Cell(25, 5, "DIAS VENCIDOS", 'TB', 0, 'L');
                    $this->Cell(30, 5, "VALOR", 'TB', 0, 'C');
                    $this->Ln();
                }
                $fec = strtotime(date('Y-m-d')) - strtotime($rst[pag_fecha_v]);
                $dias = floor($fec / 86400);
                $this->SetFont('Arial', '', 8);
//                $this->Cell(50, 5, $tip, 0, 0, 'L');
                $this->Cell(35, 5, $rst2[reg_num_documento] . $ast, 0, 0, 'L');
                $this->Cell(30, 5, $rst2[reg_femision], 0, 0, 'L');
                $this->Cell(30, 5, $rst[pag_fecha_v], 0, 0, 'L');
                $this->Cell(25, 5, $dias, 0, 0, 'C');
                $this->Cell(30, 5, number_format($vencido, $dec), 0, 0, 'R');
                $this->Ln();
                $grup = $rst2[reg_ruc_cliente];
                $tot+=$vencido;
                $ts+=$vencido;
            }
        }

        $tip = '';
            $this->SetFont('helvetica', 'B', 8);
            $this->Cell(75, 5, '', '', 0, 'R');
            $this->Cell(45, 5, 'TOTAL ', '', 0, 'C');
            $this->Cell(30, 5, number_format($tot, $dec), 'T', 0, 'R');
            $this->Ln();
            $this->Ln();

            $this->SetFont('helvetica', 'B', 8);
            $this->Cell(25, 5, '', '', 0, 'R');
            $this->Cell(45, 5, 'TOTALES ', '', 0, 'C');
            $this->Cell(80, 5, number_format($ts, $dec), 'T', 0, 'R');
            $this->Ln();
            $this->Ln();
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec1, $fec2, $dec);
$pdf->body($cns, $dec);
$pdf->Ln();
$pdf->Output();



