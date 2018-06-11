<?php

require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
include_once("../Clases/clsPuestoTrabajo.php");
include_once("../Clases/clsClase_empleados.php");
$Emp = new Clase_empleados();
$ger = $_REQUEST[ger];
$div = $_REQUEST[div];
$sem = $_REQUEST[id];
$yr = $_REQUEST[year];
$Puestos = new PuestoTrabajo();
$cnsPuesto = $Puestos->listaAllPuestoTrabajoGD($ger, $div);

class PDF extends FPDF {

    function rptPuestosTrabajo($ger, $div, $yr, $sem, $cns) {
        $Puestos = new PuestoTrabajo();
        $Emp = new Clase_empleados();
        $ptid = 0;
        $nm1 = 1000;
        $nm2 = 2000;
        $cn = 0;
        $sec = 0;
        while ($rst = pg_fetch_array($cns)) {
            $rst_d=pg_fetch_array($Emp->lista_una_division($div));
            if ($sec != $rst[sec_id]) {
                $this->SetFont('Arial', 'B', 8);
                $this->Ln();
                $this->Cell(0, 5, 'REPORTES DE TURNO DE TRABAJO', 0, 1, 'C');
                $this->Cell(0, 5, $rst_d[div_descripcion] . ' / ' . $rst['sec_descricpion'], 0, 1, 'C');
                $this->Cell(0, 5, 'Semana:' . $sem, 0, 1, 'L');
                $this->Cell(0, 5, 'Fecha Impresion: ' . date('d-m-Y H:i'), 0, 1, 'R');
                $this->Ln();
                $this->SetFont('Arial', 'B', 7);
                $header = array('CARGO',
                    'RESPONSABILIDAD',
                    'MARCA',
                    'NUMERO',
                    'NOMBRE');
                $w = array(37, 42, 24, 12, 36);
                $this->Cell($w[0] + $w[1] + $w[2], 5, 'DETALLE ', 1, 0, 'C');
                $this->Cell($w[3] + $w[4], 5, 'TURNO 1', 1, 0, 'C');
                $this->Cell($w[3] + $w[4], 5, 'TURNO 2', 1, 0, 'C');
                $this->Ln();
                $this->SetFont('Arial', 'B', 7);
                $this->Cell($w[0], 5, $header[0], 1, 0, 'C');
                $this->Cell($w[1], 5, $header[1], 1, 0, 'C');
                $this->Cell($w[2], 5, $header[2], 1, 0, 'C');
                $this->Cell($w[3], 5, $header[3], 1, 0, 'C');
                $this->Cell($w[4], 5, $header[4], 1, 0, 'C');
                $this->Cell($w[3], 5, $header[3], 1, 0, 'C');
                $this->Cell($w[4], 5, $header[4], 1, 0, 'C');
                $this->Ln();

                $this->SetFont('Arial', '', 7);
                $sec = $rst[sec_id];
            }


            $nm1++;
            $nm2++;
            $ptid++;

            if ($rst['pt_turno1'] == 1 || $rst['pt_turno1'] == 2 || $rst['pt_turno1'] == 3 || $rst['pt_turno1'] == 5 || $rst['pt_turno1'] == 8) {
                $cod1 = $rst['pt_codigo'];
            } else {
                $cod1 = '';
            }
            if ($rst['pt_turno2'] == 4 || $rst['pt_turno2'] == 6) {
                $cod2 = $rst['pt_codigo2'];
                $readonly = "";
            } else {
                $cod2 = '';
                $readonly = "readonly hidden";
            }
            if ($rst['pt_turno3'] == 7) {
                $cod3 = $rst['pt_codigo3'];
            } else {
                $cod3 = '';
            }
            $rstAsgpt = pg_fetch_array($Puestos->listAsgPuestoTrabajo($rst[pt_id], $sem, 0, $yr));
            if ($rstAsgpt[asg_pt_id] != '') {
                $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rstAsgpt[emp_id1]));
                $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rstAsgpt[emp_id2]));
                $empcod1 = $rstEmp1[emp_codigo];
                $empcod2 = $rstEmp2[emp_codigo];
                if ($rstEmp1 == '') {
                    $empName1 = '';
                } else {
                    $empName1 = $rstEmp1[emp_apellido_paterno] . " " . $rstEmp1[emp_apellido_materno] . " " . $rstEmp1[emp_nombres];
                }

                if ($rstEmp2 == '') {
                    $empName2 = '';
                } else {
                    $empName2 = $rstEmp2[emp_apellido_paterno] . " " . $rstEmp2[emp_apellido_materno] . " " . $rstEmp2[emp_nombres];
                }
            } else {
                $empcod1 = '';
                $empcod2 = '';
                $empName1 = '';
                $empName2 = '';
            }

            if ($rst[pt_estado] == 't') {
                $cn++;
                $this->Cell($w[0], 5, strtoupper(utf8_decode(substr($rst['pt_cargo'], 0, 21))), 1, 0, 'L');
                $this->Cell($w[1], 5, substr($rst['pt_responsabilidad'], 0, 30), 1, 0, 'L');
                $this->Cell($w[2], 5, strtoupper($rst['pt_marca']), 1, 0, 'L');
                $this->Cell($w[3], 5, $empcod1, 1, 0, 'C');
                $this->Cell($w[4], 5, strtoupper(utf8_decode(substr($empName1, 0, 21))), 1, 0, 'L');
                $this->Cell($w[3], 5, $empcod2, 1, 0, 'C');
                $this->Cell($w[4], 5, strtoupper(utf8_decode(substr($empName2, 0, 21))), 1, 0, 'L');
                $this->Ln();
            }
        }//Fin Nivel 1
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->rptPuestosTrabajo($ger, $div, $yr, $sem, $cnsPuesto);
$pdf->Ln();
$pdf->Output();
?>

