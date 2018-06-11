<?php

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
include_once '../Clases/clsClase_resumen_ctasxpagar.php';
require_once 'fpdf/fpdf.php';
$Cxp = new Clase_resumen_ctasxpagar();
$id = trim($_REQUEST[id]);
$rst_enc = pg_fetch_array($Cxp->lista_ctasxpagar_id($id));

class PDF extends FPDF {

    function reporte($rst) {
        $Cxp = new Clase_resumen_ctasxpagar();
        $this->SetFont('Arial', 'B', 8);
        $x = 0;
        $y = 0;
        //     $file = '../img/logo_noperti.jpg';
//        $this->Image($file, $x + 10, $y + 5, 70);
        $this->Text($x + 7, $y + 40, "FECHA DE PAGO:");
        $this->Text($x + 50, $y + 40, $rst[ctp_fecha_pago]);
        $this->Text($x + 7, $y + 45, "CLIENTE/PROVEEDOR:");
        $this->Text($x + 7, $y + 50, "TOTAL A PAGAR:");
        $this->Text($x + 7, $y + 55, "CONCEPTO DE PAGO:");
        $this->Text($x + 50, $y + 55, utf8_decode($rst[ctp_concepto]));
        $this->Text($x + 7, $y + 60, "FORMA DE PAGO:");
        $this->Text($x + 50, $y + 60, $rst[ctp_forma_pago]);
        $this->Text($x + 7, $y + 65, "BANCO:");
        $this->Text($x + 125, $y + 65, "CHEQUE:");
        $this->SetXY($x + 7, $y + 70);
        $this->Cell(25, 5, "COD. CONTABLE", 'TB', 0, 'C');
        $this->Cell(50, 5, "CUENTA CONTABLE", 'TB', 0, 'C');
        $this->Cell(30, 5, "NUM FACTURA", 'TB', 0, 'C');
        $this->Cell(15, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(15, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
        $tdebe = 0;

//        while ($rst = pg_fetch_array($cns)) {
//            $rst_asiento = pg_fetch_array($Cxp->lista_asiento_codigo($rst[con_asiento]));
//            $rst_debe = pg_fetch_array($Cxp->lista_debe_factura_egr($rst[reg_id], $rst_enc[obl_codigo], $egr));
//            $this->Cell(25, 5, utf8_decode($rst_asiento[con_concepto_haber]), 0, 0, 'L');
//            $this->Cell(50, 5, substr(utf8_decode($rst_asiento[pln_descripcion]), 0, 28), 0, 0, 'L');
//            $this->Cell(30, 5, $rst[reg_num_documento], 0, 0, 'L');
//            $this->Cell(15, 5, number_format($rst_debe[sum], 2), 0, 0, 'R');
//            $this->Cell(15, 5, '', 0, 0, 'R');
//            $this->Ln();
//            $tdebe+=$rst_debe[sum];
//        }
        $rst_cuenta = pg_fetch_array($Cxp->lista_una_cuenta(trim($rst[ctp_banco])));
        $rst_cuentah = pg_fetch_array($Cxp->lista_una_cuentaid($rst[pln_id]));

        $this->Text($x + 50, $y + 45, utf8_decode($rst[cli_raz_social]));
        $this->Text($x + 50, $y + 50, number_format($rst[ctp_monto], 2));
        $this->Text($x + 20, $y + 65, utf8_decode($rst_cuenta[pln_descripcion]));
        $this->Text($x + 140, $y + 65, $rst[num_documento]); //n° cheque
        $this->Cell(25, 5, $rst[ctp_banco], 0, 0, 'L');
        $this->Cell(50, 5, substr(utf8_decode($rst_cuenta[pln_descripcion]),0,25), 0, 0, 'L');
        $this->Cell(30, 5, $rst[reg_num_documento], 0, 0, 'L');
        $this->Cell(15, 5, '', 0, 0, 'R');
        $this->Cell(15, 5, number_format($rst[ctp_monto], 2), 0, 0, 'R');
        $this->Ln();
        $this->Cell(25, 5, utf8_decode($rst_cuentah[pln_codigo]), 0, 0, 'L');
        $this->Cell(50, 5, substr(utf8_decode($rst_cuentah[pln_descripcion]),0,25), 0, 0, 'L');
        $this->Cell(30, 5, $rst[reg_num_documento], 0, 0, 'L');
        $this->Cell(15, 5, number_format($rst[ctp_monto], 2), 0, 0, 'R');
        $this->Cell(15, 5, '', 0, 0, 'R');
        $this->Ln();
        $this->Cell(135, 3, '', 'B', 0, 'L');
        $this->Ln();
        $this->Cell(105, 5, '', 0, 0, 'L');
        $this->Cell(15, 5, number_format($rst[ctp_monto], 2), 0, 0, 'R');
        $this->Cell(15, 5, number_format($rst[ctp_monto], 2), 0, 0, 'R');
        $this->Ln(20);
        $this->Cell(40, 7, 'PREPARADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'REVISADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'RECIBIDO', 'T', 0, 'L');

        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(204, 15, 45);
        $this->Text($x + 90, $y + 30, 'EGRESO No:');
        $this->Text($x + 115, $y + 30, $rst[ctp_secuencial]);
    }

}

$pdf = new PDF();
$pdf->AddPage();
$pdf->reporte($rst_enc);

$pdf->Output();
