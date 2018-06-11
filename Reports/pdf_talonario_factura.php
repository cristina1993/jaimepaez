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
        $this->SetFont('Arial', 'B', 7);
        $this->SetMargins(0, 0 , 0); 
        $this->SetAutoPageBreak(true,0);
   	$this->Text($x + 33, $y + 21,'NRO FAC: '.$rst1[fac_numero]);
        $this->Text($x + 3, $y + 21, 'FECHA: '.$rst1[fac_fecha_emision]);
        $this->Text($x + 3, $y + 36, 'RUC/CC: '.$rst1[fac_identificacion]);
        $this->Text($x + 3, $y + 24, 'CLIENTE: '.$rst1[fac_nombre]);
        $this->Text($x + 33, $y + 36, 'TELEFONO: '.$rst1[fac_telefono]);
        $this->SetXY($x + 2, 25);$this->Cell(40, 5, 'DIR:'.substr($rst1[fac_direccion],0,40), 0,0, 'L');
        $this->SetXY($x + 2, 29);$this->Cell(40, 5, substr($rst1[fac_direccion],40), 0,0, 'L');
        
        $this->SetFont('Arial', 'B', 6);
         $this->Text( 6, $y + 40,'Descripcion');
            $this->Text( 30, $y + 45, 'Cant');
            $this->Text( 7, $y + 45,'Codigo');
            $this->Text( 46, $y + 45,'P. uni');
            $this->Text( 58, $y + 45,'P. total');
           
        while ($rst = pg_fetch_array($cns)) {
//            $this->SetXY($x + 8, $y + 74);
//            $this->Cell(11, 5, $rst[dfc_cantidad], $ln, 0, 'C');
//            $this->Cell(31, 13, utf8_decode(substr($rst[dfc_descripcion].' '. $rst[dfc_codigo], 0, 35)));
//            $this->Cell(31, 5, , $ln, 0, 'C');
            $this->Text( 4, $y + 53,utf8_decode($rst[dfc_descripcion]));
            $this->Text( 33, $y + 56, $rst[dfc_cantidad]);
            $this->Text( 5, $y + 56,utf8_decode($rst[dfc_codigo]));
            $this->Text( 46, $y + 56,$rst[dfc_precio_unit]);
            $this->Text( 58, $y + 56,number_format($rst[dfc_precio_total], 2));
//            $this->Cell(48, 5,number_format($rst[dfc_precio_unit], 2), $ln, 0, 'C');
//            $this->Cell(18, 5, number_format($rst[dfc_porcentaje_descuento], 2), $ln, 0, 'C');
//            $this->Cell(42, 5, number_format($rst[dfc_precio_total], 2), $ln, 0, 'C');
//            $this->Ln();
            $y+=9;
        }
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($x + 32, 144);$this->Cell(22, 5, 'SUBTOTAL 12%: ', $ln, 0, 'R');
        $this->SetXY($x + 44, 144);$this->Cell(22, 5, number_format($rst1[fac_subtotal12],2), $ln, 0, 'R');
        
        if($rst1[fac_total_descuento] == 0){
             $this->SetXY($x + 32, 147);$this->Cell(22, 5, 'IVA 12%: ', $ln, 0, 'R');
            $this->SetXY($x + 44, 147);$this->Cell(22, 5, number_format($rst1[fac_total_iva],2), $ln, 0, 'R');
    //        $this->SetXY($x + 95, 134);$this->Cell(22, 5, '0,00', $ln, 0, 'C');
            $this->SetXY($x + 32, 150);$this->Cell(22, 5, 'TOTAL: ', $ln, 0, 'R');
            $this->SetXY($x + 44, 150);$this->Cell(22, 5, number_format($rst1[fac_total_valor],2), $ln, 0, 'R');
           
        }
        else{
        $this->SetFont('Arial', 'B', 9);
            $this->SetXY($x + 32, 147);$this->Cell(22, 5, 'DESC: ', $ln, 0, 'R');
             $this->SetXY($x + 44, 147);$this->Cell(22, 5, number_format($rst1[fac_total_descuento],2), $ln, 0, 'R');
    //        $this->SetXY($x + 41, 169);$this->Cell(22, 5, 'desc: '.number_format($rst1[fac_total_descuento],2), $ln, 0, 'C');
            $this->SetXY($x + 32, 150);$this->Cell(22, 5, 'IVA 12%: ', $ln, 0, 'R');
            $this->SetXY($x + 44, 150);$this->Cell(22, 5, number_format($rst1[fac_total_iva],2), $ln, 0, 'R');
    //        $this->SetXY($x + 95, 134);$this->Cell(22, 5, '0,00', $ln, 0, 'C');
            $this->SetXY($x + 32, 153);$this->Cell(22, 5, 'TOTAL: ', $ln, 0, 'R');
            $this->SetXY($x + 44, 153);$this->Cell(22, 5, number_format($rst1[fac_total_valor],2), $ln, 0, 'R');
        }
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'personal');
$pdf->AddPage();
$pdf->factura($rst1, $cns, $emisor, $amb, $dec);
$pdf->Line(2,37,70,37);
$pdf->Line(2,46,70,46);

//$pdf->Line(42,100,150,100);
$pdf->Output();



