<?php

include_once '../Clases/clsClase_factura.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_factura();
$id = $_GET[id];
$rst1 = pg_fetch_array($Set->lista_una_factura_id($id));
$cns = $Set->lista_detalle_factura($id);
$emisor = pg_fetch_array($Set->lista_emisor($rst1[emi_id]));
$rst_amb = pg_fetch_array($Set->lista_configuraciones());
$amb = $rst_amb[con_valor];
$rst_dec = pg_fetch_array($Set->lista_configuraciones_dec());
$dec = $rst_dec[con_valor];

class PDF extends FPDF {

    function factura($rst1, $cns, $emisor, $amb, $dec) {
        $x = 0;
        $y = 0;
        $ln = 0;
        $this->SetFont('Arial', 'B', 10);
        $this->SetMargins(0, 0 , 0); 
        $this->SetAutoPageBreak(true,0);
   	$this->Text($x + 156, $y + 28, $rst1[fac_numero]);
        $this->Text($x + 22, $y + 46, $rst1[fac_fecha_emision]);
        $this->Text($x + 130, $y + 46, $rst1[fac_identificacion]);
        $this->Text($x + 22, $y + 53, $rst1[fac_nombre]);
        $this->Text($x + 25, $y + 59, $rst1[fac_direccion]);
        $this->Text($x + 137, $y + 59, $rst1[fac_telefono]);

        while ($rst = pg_fetch_array($cns)) {
            $this->SetXY($x + 8, $y + 69);
            $this->Cell(11, 5, $rst[dfc_cantidad], $ln, 0, 'C');
            $this->Cell(41, 5, $rst[dfc_codigo], $ln, 0, 'C');
            $this->Cell(80, 5, utf8_decode(substr($rst[dfc_descripcion], 0, 40)), $ln, 0, 'C');
            $this->Cell(18, 5, number_format($rst[dfc_precio_unit], 2), $ln, 0, 'C');
            $this->Cell(18, 5, number_format($rst[dfc_porcentaje_descuento], 2), $ln, 0, 'C');
            $this->Cell(22, 5, number_format($rst[dfc_precio_total], 2), $ln, 0, 'C');
            $this->Ln();
            $y+=5;
        }
        $this->SetXY($x + 177, 119);$this->Cell(22, 5, number_format($rst1[fac_subtotal12],2), $ln, 0, 'C');
        $this->SetXY($x + 177, 124);$this->Cell(22, 5, number_format($rst1[fac_total_descuento],2), $ln, 0, 'C');
        $this->SetXY($x + 177, 129);$this->Cell(22, 5, number_format($rst1[fac_total_iva],2), $ln, 0, 'C');
        $this->SetXY($x + 177, 134);$this->Cell(22, 5, '0,00', $ln, 0, 'C');
        $this->SetXY($x + 177, 139);$this->Cell(22, 5, number_format($rst1[fac_total_valor],2), $ln, 0, 'C');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'dpv_fact');
$pdf->AddPage();
$pdf->factura($rst1, $cns, $emisor, $amb, $dec);
$pdf->Output();



