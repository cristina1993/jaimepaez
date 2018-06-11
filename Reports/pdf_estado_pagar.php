<?php

include_once '../Clases/clsClase_cuentasxpagar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasPagar();
$fec1 = $_GET[d];
$fec2 = $_GET[h];
$cli = $_GET[cli];
$estado = $_GET[e];
$dec = $_GET[dec];

//$txt = "WHERE c.reg_identificacion ='$cli' and exists (select * from erp_pagos_factura p where p.com_id=cast(c.reg_id as varchar))";
$cns = $Set->lista_estado_cuenta_cliente($cli, $fec1, $fec2);
$emisor = pg_fetch_array($Set->lista_emisor_id('1'));
$cliente = pg_fetch_array($Set->lista_cliente_ced($cli));

//$res = pg_fetch_array($Set->saldo_anterior($cli, $fec1));
//$ant = ($res[debito] + $res[debito1]) - $res[credito];

class PDF extends FPDF {

    function encabezado($emisor, $fec1, $fec2, $cl) {
        $Set = new CuentasPagar();
        $cod = '';
        $rst_cta = pg_fetch_array($Set->lista_codigo_cuenta($cl[cli_ced_ruc]));
        if ($rst_cta[pln_id] != '') {
            $rst_pln = pg_fetch_array($Set->listar_una_cuenta_id($rst_cta[pln_id]));
            $cod = $rst_pln[pln_codigo];
        }
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(270, 5, "CUENTAS POR PAGAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, "ESTADO DE CUENTAS CLIENTE", 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, utf8_decode($emisor[emi_nombre_comercial]), 0, 0, 'C');
        $this->Ln();
        $this->Cell(270, 5, "RUC: " . $emisor[emi_identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(270, 5, "PERIODO  DE: " . $fec1 . " AL " . $fec2, 0, 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->Cell(150, 5, "CODIGO CLIENTE: " . $cl[cli_codigo], 0, 0, 'L');
        $this->Cell(150, 5, "RUC: " .utf8_decode($cl[cli_ced_ruc]), 0, 0, 'L');
        $this->Ln();
        $this->Cell(150, 5, "CLIENTE: " . utf8_decode($cl[cli_raz_social]), 0, 0, 'L');
        $this->Cell(150, 5, "DIRECCION: " . utf8_decode($cl[cli_calle_prin]), 0, 0, 'L');
        $this->Ln();
        $this->Cell(150, 5, "CODIGO CONTABLE: " . $cod, 0, 0, 'L');
        $this->Cell(150, 5, "TELEFONO: " . $cl[cli_telefono], 0, 0, 'L');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(25, 5, "FECHA", 'TB', 0, 'L');
        $this->Cell(30, 5, "DOCUMENTO", 'TB', 0, 'C');
        $this->Cell(60, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(30, 5, "F.PAGO", 'TB', 0, 'C');
        $this->Cell(33, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(33, 5, "HABER", 'TB', 0, 'C');
        $this->Cell(33, 5, "SALDO", 'TB', 0, 'C');
        $this->Cell(35, 5, "SALDO VENCIDO", 'TB', 0, 'C');
    }

    function body($cns, $dec) {
        $Set = new CuentasPagar();
        $this->SetFont('Arial', '', 8);
        $saldo1 = 0;
        $n = 0;

        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $debito1 = round($rst[total_valor],$dec);
            $credito1 = round($rst[haber],$dec);
            if ($rst[forma] == 'NOTA DE DEBITO') {
                $debito1 = round($rst[haber],$dec);
                $credito1 = '0';
            }
            $saldo1 = round($saldo1,$dec) + $debito1 - $credito1;
//            if ($rst[concepto] == 'FACTURACION COMPRA') {
                $Set = new CuentasPagar();
                $res = pg_fetch_array($Set->suma_pagos1($rst[reg_id]));
                $mto = round($res[monto],$dec); // suma pagos cta
                $pgo = round($res[pago],$dec) + round($res[debito],$dec);
                $rst_pag1 = pg_fetch_array($Set->lista_ultimo_pago($rst[reg_id]));
                $fvencimiento = $rst_pag1[pag_fecha_v];
                if ($fvencimiento < date('Y-m-d')) {
                    $vencido1 = round($pgo,$dec) - round($mto,$dec);
                } else {
                    $vencido1 = 0;
                }
//            } else {
//                $vencido1 = 0;
//            }
            $this->SetFont('Arial', '', 8);
            $this->Cell(25, 5, $rst[reg_femision], 0, 0, 'L');
            $this->Cell(30, 5, $rst[reg_num_documento], 0, 0, 'L');
            $this->Cell(60, 5, substr($rst[concepto], 0, 30), 0, 0, 'L');
            $this->Cell(30, 5, substr($rst[forma], 0, 30), 0, 0, 'L');
            $this->Cell(30, 5, number_format($debito1,  $dec), 0, 0, 'R');
            $this->Cell(30, 5, number_format($credito1,  $dec), 0, 0, 'R');
            $this->Cell(30, 5, number_format($saldo1,  $dec), 0, 0, 'R');
            $this->Cell(35, 5, number_format($vencido1,  $dec), 0, 0, 'R');
            $this->Ln();
            $tdeb+=round($debito1,$dec);
            $tcre+=round($credito1,$dec);
            $tvc+=round($vencido1,$dec);
        }
        $tsal = $tdeb - $tcre;


        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(110, 5, '', '', 0, 'R');
        $this->Cell(35, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(30, 5, number_format($tdeb,  $dec), 'T', 0, 'R');
        $this->Cell(30, 5, number_format($tcre,  $dec), 'T', 0, 'R');
        $this->Cell(30, 5, number_format($tsal,  $dec), 'T', 0, 'R');
        $this->Cell(35, 5, number_format($tvc,  $dec), 'T', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'L', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec1, $fec2, $cliente);
$pdf->encabezado_tab();
$pdf->Ln();
$grup = '';
$pdf->body($cns, $dec);
$pdf->Output();



