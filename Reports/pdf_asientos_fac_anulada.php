<?php

include_once '../Clases/clsClase_asientos.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_asientos();

$id = $_GET[id];
$cns = $Set->listar_asiento_agrupado($id);
$rst1 = pg_fetch_array($Set->listar_asiento_numero($id));
$emisor = pg_fetch_array($Set->lista_emisor('1'));

class PDF extends FPDF {

    function encabezado($emisor, $id) {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(200, 5, "COMPROBANTE DIARIO", 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RAZON SOCIAL:". utf8_decode($emisor[emi_nombre_comercial]), 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "RUC: " . utf8_decode($emisor[emi_identificacion]), 0, 0, 'L');
        $this->Ln();
        $this->Cell(85, 5, "MONEDA: DOLAR", 0, 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->Ln();
    }

    function encabezado_tab($as) {
        $Set = new Clase_asientos();
        $rst1 = pg_fetch_array($Set->listar_asiento_numero($as));
        $rst_fac = pg_fetch_array($Set->listar_factura_asiento_anulado($as));
        $rst_ret = pg_fetch_array($Set->listar_ultima_retencion_regid($rst_fac[reg_id]));
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 5, "FECHA: " . $rst1[con_fecha_emision], 'T', 0, 'L');
        $this->Cell(100, 5, "ASIENTO: " . $as, 'T', 0, 'L');
        $this->Ln();
        $this->Cell(100, 5, "CLIENTE: " . utf8_decode($rst_fac[cli_raz_social]), 0, 0, 'L');
        $this->Cell(100, 5, "REGISTRO: " . $rst_fac[con_asiento], 0, 0, 'L');
        $this->Ln();
        $this->Cell(100, 5, "FACTURA: " . $rst_fac[reg_num_documento], 'B', 0, 'L');
        $this->Cell(100, 5, "RETENCION: " . $rst_ret[ret_numero], 'B', 0, 'L');
        $this->Ln();
        $this->Cell(10, 5, "No", 'TB', 0, 'C');
        $this->Cell(30, 5, "CODIGO", 'TB', 0, 'C');
        $this->Cell(55, 5, "CUENTA", 'TB', 0, 'C');
        $this->Cell(55, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(25, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(25, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
    }

    function asientos($as, $emisor, $desde, $hasta) {
        $Set = new Clase_asientos();
        $this->SetFont('helvetica', '', 8);
        $cns_cuentas = $Set->lista_cuentas_asientos_anulado($as);

        $n = 0;
        $m = 0;
        $j = 1;
        $grup = '';

        while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
            $rst_cuentas1 = pg_fetch_array($Set->listar_descripcion_asiento($rst_cuentas[concepto]));
            $rst_v = pg_fetch_array($Set->listar_debe_haber_asiento_cuenta_fac1($rst_cuentas[con_asiento], $rst_cuentas[concepto]));
            if ($rst_cuentas[tipo] == 0) {
                if (!empty($rst_v[debe])) {
                    $debe = $rst_v[debe] - $rst_v[haber];
                } else {
                    $debe = $rst_cuentas[valor];
                }
                $haber = 0;
            } else {
                $debe = 0;
                $haber = $rst_cuentas[valor];
            }
            if ($grup != $rst_cuentas1[pln_codigo]) {
                $this->Cell(10, 5, $j, 0, 0, 'L');
                $this->Cell(30, 5, $rst_cuentas1[pln_codigo], 0, 0, 'L');
                $this->Cell(50, 5, utf8_decode(substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 24)), 0, 0, 'L');
                $this->Cell(50, 5, utf8_decode(substr($rst_cuentas[con_concepto], 0, 24)), 0, 0, 'L');
                $this->Cell(30, 5, number_format($debe, 2), 0, 0, 'R');
                $this->Cell(30, 5, number_format($haber, 2), 0, 0, 'R');
                $this->Ln();
                $j++;
                $t_debe+=$debe;
                $t_haber+=$haber;
            }
            $grup = $rst_cuentas1[pln_codigo];
            $n++;
        }
        $this->SetFont('helvetica', '', 8);
        $this->Cell(140, 5, 'TOTAL ', 'T', 0, 'R');
        $this->Cell(30, 5, number_format($t_debe, 2), 'T', 0, 'R');
        $this->Cell(30, 5, number_format($t_haber, 2), 'T', 0, 'R');
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Cell(60, 5, 'AUTORIZADO ', 'T', 0, 'L');
        $this->Cell(5, 5, '', '', 0, 'L');
        $this->Cell(60, 5, 'CONTADOR ', 'T', 0, 'L');
        $this->Cell(5, 5, '', '', 0, 'L');
        $this->Cell(60, 5, 'CONTABILIZADO ', 'T', 0, 'L');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $id);
$pdf->Ln();

while ($rst = pg_fetch_array($cns)) {
    $pdf->encabezado_tab($rst[con_asiento]);
    $pdf->asientos($rst[con_asiento], $emisor, $desde, $hasta);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Ln();
}
$pdf->Output();
