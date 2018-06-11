<?php

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
include_once '../Clases/clsClase_nomina_roles.php';
require_once 'fpdf/fpdf.php';
$Set = new Clase_nomina_roles();

$prd = trim(strtoupper($_GET[periodo]));
$a = $_GET[anio];
$cns = $Set->lista_nomina($prd, $a);

class PDF extends FPDF {

    function rol($rst1, $cns1, $emisor, $dec, $x, $y) {

        // ///////////////////////////////// ENCABEZADO///////////////////////////////////////////////////////
        $round = $dec;
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(190, 9, "ROL DE PAGO", 'LRT', 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 10);
        $this->Cell(50, 5, '', 'L', 0, 'C');
        $this->Cell(80, 5, utf8_decode($emisor[emi_nombre_comercial]), '', 0, 'C');
        $this->Cell(60, 5, utf8_decode($rst1[nom_periodo]), 'R', 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Empleado : ", 'L', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(155, 5, utf8_decode($rst1[emp_apellido_paterno] . ' ' . $rst1[emp_apellido_materno] . ' ' . $rst1[emp_nombres]), 'R', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Cedula : ", 'L', 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, $rst1[emp_documento], '', 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Periodo : ", '', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, $rst1[nom_fp_desde] . ' al ' . $rst1[nom_fp_hasta], 'R', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Salario Base: ", 'L', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, $rst1[nom_sueldo_base], '', 0, 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Fecha : ", '', 0, '');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, $rst1[nom_fec_registro], 'R', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Forma de Pago : ", 'L', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, utf8_decode($rst1[nom_forma_pago]), '', 0, 'L');
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(35, 5, "Dias Laborados : ", '', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(60, 5, $rst1[nom_dias_trabajados], 'R', 0, 'L');
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////// CUERPO ////////////////////////////////////////////////////////                        
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(190, 5, "DETALLE DE SUELDO", 'LRTB', 0, 'C');
        $this->Ln();
        $this->Cell(20, 5, "CANT", 'LRTB', 0, 'C');
        $this->Cell(85, 5, "INGRESOS", 'LRTB', 0, 'C');
        $this->Cell(85, 5, "EGRESOS", 'LRTB', 0, 'C');
        $this->Ln();

        //___________________________________________________________________________
        $ingreso = Array();
        $egreso = Array();
        $tot_ing = 0;
        $tot_eg = 0;
        while ($rst_cuentas = pg_fetch_array($cns1)) {
            if ($rst_cuentas[rub_tipo] == 1) {
                $tot_ing = $tot_ing + $rst_cuentas[dnm_valor];
                array_push($ingreso, $rst_cuentas[dnm_cantidad] . '&' . $rst_cuentas[rub_descripcion] . '&' . $rst_cuentas[dnm_valor]);
            }

            if ($rst_cuentas[rub_tipo] == 2) {
                $tot_eg = $tot_eg + $rst_cuentas[dnm_valor];
                array_push($egreso, $rst_cuentas[rub_descripcion] . '&' . $rst_cuentas[dnm_valor]);
            }
        }

        $n = 0;
        while ($n < count($ingreso)) {            
            $dat = explode('&', $ingreso[$n]);
            $n++;
            $this->SetFont('helvetica', '', 5);
            $this->Cell(20, 3, $dat[0], 'LR', 0, 'C');
            $this->Cell(65, 3, utf8_decode(substr($dat[1], 0, 40)), 0, 0, 'L');
            $this->Cell(20, 3, number_format($dat[2], $round), 'R', 0, 'R');
            $this->Ln();
        }

        while ($n < 13) {
            $n++;
            $this->Cell(20, 3, '', 'LR', 0, 'C');
            $this->Cell(65, 3, '', 0, 0, 'L');
            $this->Cell(20, 3, '', 'R', 0, 'R');
            $this->Ln();
        }
        $x = $x + 112;
        $y = $y + 51;
        $m = 0;
        while ($m < count($egreso)) {
            $this->SetXY($x, $y);
            $dt = explode('&', $egreso[$m]);
            $m++;
            $this->Cell(65, 3, utf8_decode(substr($dt[0], 0, 40)), '', 0, 'L');
            $this->Cell(20, 3, number_format($dt[1], $round), 'R', 0, 'R');
            $y = $y + 3;
        }
        while ($m < 13) {
            $this->SetXY($x, $y);
            $m++;
            $this->Cell(65, 3, '', '', 0, 'L');
            $this->Cell(20, 3, '', 'R', 0, 'R');
            $y = $y + 3;
        }

        $this->SetXY(7, $y);
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(20, 5, "", 'LRTB', 0, 'L');
        $this->Cell(65, 5, "TOTAL INGRESOS", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($tot_ing, $round), 'LRTB', 0, 'R');
        $this->Cell(65, 5, "TOTAL EGRESOS", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($tot_eg, $round), 'LRTB', 0, 'R');
        $this->Ln();
        $tot = $tot_ing - $tot_eg;
        $this->SetFont('helvetica', 'IBU', 10);
        $this->Cell(85, 15, "TOTAL A RECIBIR ", 'L', 0, 'L');
        $this->Cell(20, 15, number_format($tot, $round), '', 0, 'R');
        $this->Cell(85, 5, "", 'R', 0, 'L');
        $this->Ln();
        $this->Cell(190, 15, "", 'LR', 0, 'L');
        $this->SetFont('helvetica', '', 10);
        $this->Ln();
        $this->Cell(105, 5, "", 'L', 0, '');
        $this->Cell(85, 5, "____________________________", 'R', 0, 'C');
        $this->Ln();
        $this->Cell(105, 5, "", 'L', 0, '');
        $this->Cell(85, 5, utf8_decode("Recibí Confrome"), 'R', 0, 'C');
        $this->Ln();
        $this->Cell(105, 5, "", 'L', 0, 'C');
        $this->Cell(85, 5, utf8_decode($rst1[emp_apellido_paterno] . ' ' . $rst1[emp_apellido_materno] . ' ' . $rst1[emp_nombres]), 'R', 0, 'C');
        $this->Ln();
        $this->Cell(190, 5, " ", 'LRB', 0, 'C');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
while ($rst = pg_fetch_array($cns)) {
    $rst1 = pg_fetch_array($Set->lista_una_nomina_id($rst[nom_id]));
    $cns1 = $Set->lista_detalle_nomina($rst[nom_id]);
    $cns2 = $Set->lista_detalle_nomina($rst[nom_id]);
    $emisor = pg_fetch_array($Set->lista_emisor('1'));
    $rst_dec = pg_fetch_array($Set->lista_configuraciones_dec());
    $dec = $rst_dec[con_valor];
    $i = 0;
    $x = 0;
    $y = 0;
    while ($i < 2) {
        $i++;
        if ($i == 1) {
            $cn = $cns1;
        } else {
            $cn = $cns2;
        }
        $pdf->rol($rst1, $cn, $emisor, $dec, $x, $y);
        $y = $y + 139;
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
    }
}

$pdf->Output();



