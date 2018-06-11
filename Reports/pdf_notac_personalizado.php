<?php

include_once "../Clases/clsClase_notacredito_nuevo.php";
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_nota_Credito_nuevo();
if (isset($_GET[ide])) {
    $nc = $_GET[ide];
    $rnc = pg_fetch_array($Set->lista_un_notac_num($nc));
    $id = $rnc[nrc_id];
} else {
    $id = $_GET[id];
}

$id;
$rst1 = pg_fetch_array($Set->lista_una_nota_credito($id));
$cns = $Set->lista_detalle_nota_credito($id);
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
        $this->SetFont('Arial', 'B', 7);
        $this->SetMargins(0, 0, 0);
        $this->SetAutoPageBreak(true, 0);
        $this->Text($x + 26, $y + 52, $rst1[ncr_fecha_emision]);
        $this->Text($x + 27, $y + 58, $rst1[ncr_nombre]);
        $this->Text($x + 154, $y + 58, $rst1[ncr_identificacion]);
        $this->Text($x + 25, $y + 64, $rst1[ncr_direccion]);
        $this->Text($x + 170, $y + 64, $rst1[nrc_telefono]);
        $this->Text($x + 58, $y + 71, $rst1[ncr_num_comp_modifica]);
        while ($rst2 = pg_fetch_array($cns)) {
            $n++;
            $this->SetXY($x + 25, $y + 84);
            $this->Cell(120, 5, $rst1[ncr_motivo], $ln, 0, 'L');
            $this->Cell(50, 5, number_format($rst2[dnc_precio_total], 2), $ln, 0, 'R');
            $this->Ln();
            $y = $y + 5;
        }
        
        $this->SetXY($x+145,113);$this->Cell(50, 5, number_format($rst1[ncr_subtotal12], 2), $ln, 0, 'R');        
        $this->SetXY($x+145,119);$this->Cell(50, 5, number_format($rst1[ncr_subtotal10]+$rst1[ncr_subtotal_no_iva]+$rst1[ncr_subtotal_ex_iva], 2), $ln, 0, 'R');        
        $this->SetXY($x+145,125);$this->Cell(50, 5, number_format($rst1[nrc_total_valor], 2), $ln, 0, 'R');        
        
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'dpv_fact');
$pdf->AddPage();
$pdf->factura($rst1, $cns, $emisor, $amb, $dec);
$pdf->Output();




