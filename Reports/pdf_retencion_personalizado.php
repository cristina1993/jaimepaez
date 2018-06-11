<?php

include_once '../Clases/clsClase_retencion.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$id = $_GET[id];
$Set = new Clase_retencion();
$rst = pg_fetch_array($Set->lista_retencion_id($id));
$cns = $Set->lista_det_retencion($id);
$emisor = pg_fetch_array($Set->lista_emisor($rst[emi_id]));

class PDF extends FPDF {

    function retencion($rst, $cns, $emisor) {
        $x = 0;
        $y = 0;
        $ln = 0;
        if ($rst[ret_denominacion_comp] == 1) {
            $comp = 'FACTURA';
        } else {
            $comp = 'NOTA CREDITO';
        }
        $this->SetFont('Arial', 'B', 7);
        $this->Text($x + 24, $y + 40, utf8_decode($rst[ret_nombre]));
        $this->Text($x + 143, $y + 40, $rst[ret_fecha_emision]);
        $this->Text($x + 34, $y + 47, utf8_decode($rst[ret_identificacion]));
        $this->Text($x + 143, $y + 47, $comp);
        $this->Text($x + 24, $y + 54, utf8_decode($rst[ret_direccion]));
        $this->Text($x + 143, $y + 54, $rst[ret_num_comp_retiene]);

        while ($rst2 = pg_fetch_array($cns)) {
            if ($rst2[dtr_tipo_impuesto] == "IV") {
                $tp = 'IVA';
            } else if ($rst2[dtr_tipo_impuesto] == "IR") {
                $tp = 'RENTA';
            } else if ($rst2[dtr_tipo_impuesto] == "ID") {
                $tp = 'SALIDA DE DIVISAS';
            }
            $this->SetXY($x + 12, $y + 70);
            $this->Cell(31, 5, $rst2[dtr_ejercicio_fiscal], $ln, 0, 'C');
            $this->Cell(31, 5, number_format($rst2[dtr_base_imponible], 2), $ln, 0, 'C');
            $this->Cell(31, 5, $tp, $ln, 0, 'C');
            $this->Cell(31, 5, $rst2[dtr_codigo_impuesto], $ln, 0, 'C');
            $this->Cell(31, 5, $rst2[dtr_procentaje_retencion], $ln, 0, 'C');
            $this->Cell(31, 5, number_format($rst2[dtr_valor], 2), $ln, 0, 'C');
            $this->Ln();
            $y = $y + 5;
        }


        $this->Text($x + 180, 116 , number_format($rst[ret_total_valor],2));
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'dpv_ret');
$pdf->AddPage();
$pdf->retencion($rst, $cns, $emisor);
$pdf->Output();
