<?php

include_once '../Clases/clsClase_cheques.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_cheques();

$id = $_GET[id];
$cns = $Set->lista_cuentasxcobrar_chq($id);
$emisor = pg_fetch_array($Set->lista_emisor('1790007871001'));

class PDF extends FPDF {


    function encabezado_tab($as) {
        $Set = new Clase_cheques();
        $rst = pg_fetch_array($Set->lista_un_cheque($as));
        $rst_sum = pg_fetch_array($Set->suma_cheque_cuentas($as));
        $rst_cta = pg_fetch_array($Set->lista_plan_cuentas_id($rst[pln_id]));
          switch ($rst[chq_tipo_doc]) {
                    case '1':$tipo = 'CHEQUE A LA FECHA';
                        break;
                    case '2':$tipo = 'CHEQUE POSTFECHADO';
                        break;
                    case '3':$tipo = 'NOTA CREDITO';
                        break;
                    case '4':$tipo = 'NOTA DEBITO';
                        break;
                    case '5':$tipo = 'RETENCION';
                        break;
                    case '6':$tipo = 'TARJETA DE CREDITO';
                        break;
                    case '7':$tipo = 'TARJETA DE DEBITO';
                        break;
                    case '8':$tipo = 'CERTIFICADOS';
                        break;
                    case '9':$tipo = 'BONOS';
                        break;
                    case '10':$tipo = 'EFECTIVO';
                        break;
                }
        
        $this->SetFont('Arial', 'B', 8);
        $this->Ln();
        $this->Cell(200, 5, "FECHA DE PAGO: " . $rst[chq_fecha], '0', 0, 'L');
        $this->Ln();
        $this->Cell(200, 5, "CLIENTE/PROVEEDOR: " . $rst[cli_raz_social], 0, 0, 'L');
        $this->Ln();
        $this->Cell(200, 5, "TOTAL PAGADO: " . number_format($rst_sum[sum],2), '0', 0, 'L');
        $this->Ln();
        $this->Cell(200, 5, "CONCEPTO: " . $rst[chq_concepto], '0', 0, 'L');
        $this->Ln();
        $this->Cell(200, 5, "FORMA DE PAGO: " . $tipo, '0', 0, 'L');
        $this->Ln();
        $this->Cell(60, 5, "BANCO: " . $rst[chq_banco], '0', 0, 'L');
        $this->Cell(60, 5, "CUENTA: " . $rst_cta[byc_num_cuenta], '0', 0, 'L');
        $this->Cell(60, 5, "DOC/CHEQUE: " . $rst[chq_numero], 'B', 0, 'L');
        $this->Ln();
        $this->Cell(10, 5, "No", 'TB', 0, 'C');
        $this->Cell(30, 5, "CODIGO", 'TB', 0, 'C');
        $this->Cell(55, 5, "CUENTA", 'TB', 0, 'C');
        $this->Cell(55, 5, "NUM FACTURA", 'TB', 0, 'C');
        $this->Cell(25, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(25, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
    }

    function asientos($id, $cns) {
        $Set = new Clase_cheques();
        $this->SetFont('helvetica', '', 8);
        while ($rst = pg_fetch_array($cns)) {
            $j++;
            $debe = "";
            $haber = $rst[cta_monto];
            $rst_cta = pg_fetch_array($Set->lista_una_cuenta_id($rst[pln_id]));
            $this->Cell(10, 5, $j, 0, 0, 'L');
            $this->Cell(30, 5, $rst_cta[pln_codigo], 0, 0, 'L');
            $this->Cell(50, 5, substr(strtoupper($rst_cta[pln_descripcion]), 0, 24), 0, 0, 'L');
            $this->Cell(50, 5, substr($rst[fac_numero], 0, 24), 0, 0, 'L');
            $this->Cell(30, 5, number_format($debe, 2), 0, 0, 'R');
            $this->Cell(30, 5, number_format($haber, 2), 0, 0, 'R');
            $this->Ln();
            $t_haber+=$haber;
            $banco=$rst[cta_banco];
        }
        $j++;
        $rst_cta1 = pg_fetch_array($Set->lista_una_cuenta($banco));
        $this->Cell(10, 5, $j, 0, 0, 'L');
        $this->Cell(30, 5, $rst_cta1[pln_codigo], 0, 0, 'L');
        $this->Cell(50, 5, substr(strtoupper($rst_cta1[pln_descripcion]), 0, 24), 0, 0, 'L');
        $this->Cell(50, 5, $rst_cta1[byc_num_cuenta], 0, 0, 'L');
        $this->Cell(30, 5, number_format($t_haber, 2), 0, 0, 'R');
        $this->Cell(30, 5, number_format(0, 2), 0, 0, 'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 8);
        $this->Cell(140, 5, 'TOTAL ', 'T', 0, 'R');
        $this->Cell(30, 5, number_format($t_haber, 2), 'T', 0, 'R');
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
$pdf->Ln();
$pdf->encabezado_tab($id);
$pdf->asientos($id, $cns);
$pdf->Output();
