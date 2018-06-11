<?php

//include_once '../Clases/clsClase_factura.php';
include_once '../Clases/clsClase_nota.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_nota_Credito();
$id = $_GET[id];
$g = str_replace('-', '', trim($id));
$rst1 = pg_fetch_array($Set->lista_un_notac_factura($g));
$sec = str_replace('-', '', trim($rst1[num_documento]));
$cns = $Set->lista_detalle_nota_credito($sec);
$emisor = pg_fetch_array($Set->lista_emisor($rst1[cod_punto_emision]));

class PDF extends FPDF {

    function factura($rst, $cns, $emisor, $rst1, $id) {
        $x = -10;
        $y = 0;
        $this->SetFont('helvetica', 'B', 10);
        $this->Text($x + 10, $y + 5, $g . utf8_decode($emisor[nombre_comercial]));

        $this->SetFont('helvetica', 'B', 7);
        $this->Text($x + 11, $y + 10, utf8_decode($emisor[dir_establecimiento_emisor]));
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 41, $y + 16, "MATRIZ");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 33, $y + 19, "NOPERTI CIA. LTDA.");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 19, $y + 22, utf8_decode($emisor[dir_establecimiento_matriz]));
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 34, $y + 25, "QUITO - ECUADOR");
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 33, $y + 28, "RUC:" . $emisor[identificacion]);

        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(7, 30);
        $this->Cell(45, 5, "DESCRIPCION", '', 0, 'C');
        $this->Cell(10, 5, "CANT.", '', 0, 'C');
        $this->Cell(10, 5, "PRE.UNI", '', 0, 'C');

        $this->Ln();
//        $this->SetXY(1, 35);
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {

            $n++;

            $this->SetFont('helvetica', '', 8);
            $this->Cell(60, 5, substr($rst[descripcion], 0, 35), '', 0, 'L');
            $this->Ln();
            $this->SetFont('helvetica', '', 8);
            $this->Cell(45, 5, '', 0, 'C');
            $this->Cell(10, 5, $rst[cantidad], '', 0, 'R');
            $this->Cell(10, 5, $rst[precio_unitario], '', 0, 'R');
            $this->Ln();
//             $this->SetXY(1, 35);
        }
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "SUBTOTAL 12%", '', 0, 'L');
        $this->Cell(10, 5, $rst1[subtotal12], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "SUBTOTAL 0%", '', 0, 'L');
        $this->Cell(10, 5, $rst1[subtotal0], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "SUBTOTAL Exento de IVA", '', 0, 'L');
        $this->Cell(10, 5, $rst1[subtotal_exento_iva], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "SUBTOTAL No objeto de IVA", '', 0, 'L');
        $this->Cell(10, 5, $rst1[subtotal_no_objeto_iva], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 2, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "TOTAL DESCUENTO", '', 0, 'L');
        $this->Cell(10, 5, $rst1[total_descuento], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "IVA", '', 0, 'L');
        $this->Cell(10, 5, $rst1[total_iva], '', 0, 'R');
        $this->Ln();
        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(54, 5, "VALOR TOTAL", '', 0, 'L');
        $this->Cell(10, 5, $rst1[total_valor], '', 0, 'R');
        $this->Ln();
//        $this->Cell(1, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "CLIENTE:" . $rst1[nombre], 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "CED/RUC:" . $rst1[identificacion], 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "EMAIL:" . $rst1[email_cliente], 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "FACTURA No.:" . $id, 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "NOTA CREDITO No.:" . $rst1[num_documento], 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "GRACIAS POR PREFERIRNOS", '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "Comprobante sin validez tributaria ", '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "Revise su comprobante en su mail o ingrese a:", '', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "www.gruponoperti.com", '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(14, 5, "Usuario  " . $rst1[identificacion], 0, 'L');
        $this->Ln();
        if ($rst1[cod_punto_emision] < 10) {
            $pto_em = '0' . $rst1[cod_punto_emision];
        } else {
            $pto_em = $rst1[cod_punto_emision];
        }
        $g = str_replace('-', '', trim($rst1[num_documento]));
        $sec = intval(substr($g, 6, 9));
        $this->Cell(14, 5, "Clave  04" . $pto_em . $sec, 0, 'L');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'talonario');
$pdf->AddPage();
$pdf->factura($rst, $cns, $emisor, $rst1, $id);
$pdf->SetDisplayMode(100);
$pdf->Output();



