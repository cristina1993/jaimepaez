<?php

include_once '../Clases/clsClase_cierres_caja_masivo.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$emisor = $_GET[emisor];
$user = trim($_GET[user]);
$fec = trim($_GET[fecha]);

class PDF extends FPDF {

    function cierre_caja($emisor, $user, $fec) {
        $Set = new Clase_cierres_caja_masivo();
        $rst = pg_fetch_array($Set->lista_cierrres($fec, $user, $emisor));
        $rst1 = pg_fetch_array($Set->lista_bodega($emisor));
        /////////////////////////////////// ENCABEZADO ///////////////////////////////////////////////////////
        $this->SetFont('helvetica', 'B', 10);
        $this->SetXY(50, 20);
        $this->Cell(80, 5, "", 'LT');
        $this->Cell(13, 5, $rst[cie_secuencial], 'T');
        $this->Cell(7, 5, "", 'TR');
        $this->Ln();
        $this->SetXY(50, 25);
        $this->Cell(58, 5, "", 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(35, 5, "FECHA : " . $rst[cie_fecha], '');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 30);
        $this->Cell(58, 5, "", 'L');
        $this->Cell(35, 5, "HORA   : " . $rst[cie_hora], '');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 35);
        $this->Cell(100, 5, "", 'LR');
        $this->Ln();
        $this->SetXY(50, 40);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(25, 5, "USUARIO", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(55, 5, $rst[vnd_nombre], 'LTRB');
        $this->Cell(13, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 45);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(25, 5, "ALMACEN", '');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(55, 5, $rst1[emi_nombre_comercial], 'LTRB');
        $this->Cell(13, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 50);
        $this->Cell(100, 10, "", 'LR');
        $this->Ln();
        $this->SetXY(50, 60);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(58, 5, "FACTURAS EMITIDAS", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, $rst[cie_fac_emitidas], 'LTRB');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 65);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(58, 5, "PRODUCTOS FACTURADOS", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, $rst[cie_productos_facturados], 'LTRB');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 70);
        $this->Cell(100, 5, "", 'LR');
        $this->Ln();
        $this->SetXY(50, 75);
        $this->Cell(33, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(32, 5, "SUBTOTAL", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($rst[cie_subtotal], 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 80);
        $this->Cell(33, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(32, 5, "DESCUENTO", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($rst[cie_descuento], 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 85);
        $this->Cell(33, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(32, 5, "IVA", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($rst[cie_iva], 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $sub = $rst[cie_subtotal];
        $desc = $rst[cie_descuento];
        $iva = $rst[cie_iva];
        $resp = 0;
        $resp = ($sub - $desc) + $iva;
        $this->SetXY(50, 90);
        $this->Cell(33, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(32, 5, "TOTAL", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($resp, 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 95);
        $this->Cell(100, 5, "", 'LR');
        $this->Ln();
        $this->SetXY(50, 100);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(58, 5, "TOTAL FACTURAS", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($rst[cie_total_facturas], 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 105);
        $this->Cell(7, 5, "", 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(58, 5, "TOTAL NOTAS DE CREDITO", '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($rst[cie_total_notas_credito], 2), 'LTRB', 0, 'R');
        $this->Cell(7, 5, "", 'R');
        $this->Ln();
        $this->SetXY(50, 110);
        $this->Cell(100, 5, "", 'LR');
        $this->Ln();
        $this->SetXY(50, 115);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(7, 5, "", 'LTB', 0, 'L', false);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(58, 5, "TOTAL EN CAJA", 'TRB', 0, 'L', false);
        $tot_facturas = $rst[cie_total_facturas];
        $tot_nc = $rst[cie_total_notas_credito];
        $tot_caja = $tot_facturas - $tot_nc;
        $this->SetFont('helvetica', '', 10);
        $this->Cell(28, 5, number_format($tot_caja, 2), 'LTB', 0, 'R', false);
        $this->Cell(7, 5, "", 'TRB', 0, 'L', false);
        $this->Ln();
        $this->SetXY(50, 120);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(100, 5, "FORMAS DE PAGO EN CAJA", 'LTRB', 0, 'C', false);
        $this->Ln();
        $this->SetXY(50, 125);
        $this->Cell(7, 5, "", 'LB');
        $this->Cell(45, 5, "TIPO DE PAGO", 'TRB');
        $this->Cell(32, 5, "VALOR", 'LTB', 0, 'R');
        $this->Cell(16, 5, "", 'TRB');
        $this->Ln();
        $this->SetXY(50, 130);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(7, 5, "1", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "TARJETA DE CREDITO", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_tarjeta_credito]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_nc]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 135);
        $this->Cell(7, 5, "2", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "TARJETA DE DEBITO", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_tarjeta_debito]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_tc]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 140);
        $this->Cell(7, 5, "3", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "CHEQUE", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_cheque]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_cheque]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 145);
        $this->Cell(7, 5, "4", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "EFECTIVO", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_efectivo]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_efectivo]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 150);
        $this->Cell(7, 5, "5", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "CERTICADOS", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_certificados]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_certif]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 155);
        $this->Cell(7, 5, "6", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "BONOS", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_bonos]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_bonos]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 160);
        $this->Cell(7, 5, "7", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "RETENCION", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_retencion]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_ret]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 165);
        $this->Cell(7, 5, "8", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "NOTA CREDITO", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_not_credito]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_not_cre]", 'TRB', 0, 'R');
        $this->Ln();
        $this->SetXY(50, 170);
        $this->Cell(7, 5, "9", 'LTRB', 0, 'R');
        $this->Cell(45, 5, "CREDITO", 'LTRB');
        $this->Cell(24, 5, "$rst[cie_total_credito]", 'LTBR', 0, 'R');
        $this->Cell(24, 5, "$rst[cie_camb_credito]", 'TRB', 0, 'R');
        $this->Ln();
        $ntc = $rst[cie_total_tarjeta_credito];
        $ndb = $rst[cie_total_tarjeta_debito];
        $che = $rst[cie_total_cheque];
        $efec = $rst[cie_total_efectivo];
        $cert = $rst[cie_total_certificados];
        $bono = $rst[cie_total_bonos];
        $rete = $rst[cie_total_retencion];
        $nc = $rst[cie_total_not_credito];
        $cre = $rst[cie_total_credito];
        $resul = 0;
        $resul = $ntc + $ndb + $che + $efec + $cert + $bono + $rete + $nc + $cre;
        $this->SetXY(50, 175);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(52, 5, "TOTAL EN CAJA", 'LTRB', 0, 'C', false);
        $this->SetFont('helvetica', '', 10);
        $this->Cell(24, 5, number_format($resul, 2), 'LTBR', 0, 'R', false);
        $this->Cell(24, 5, number_format($resul, 2), 'LTRB', 0, 'R', false);

        if (number_format($resul, 2) != number_format($resp, 2)) {
            $this->SetFont('Arial', 'B', 25);
            $this->SetTextColor(255, 192, 203);
            $this->Text(35,190, 'FORMAS DE PAGO INCORRECTOS');
            //$this->RotatedText(35, 190,'FORMAS DE PAGO INCORRECTO', 45);
        }
    }

}

$pdf = new PDF();
$pdf->AddPage();
$pdf->cierre_caja($emisor, $user, $fec);
$pdf->Output();