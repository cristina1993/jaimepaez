<?php

include_once '../Clases/clsClase_reportes.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Rep = new Reportes();
$niv = $_GET[nivel];
$anio = $_GET[anio];
$mes = $_GET[mes];

//$cns = $Rep->lista_cuentas_fecha($desde,$hasta);
//$cuentas0= pg_fetch_all_columns($cns);
//$cuentas=array_unique($cuentas0);

class PDF extends FPDF {

    function ultimoDia($mes, $anio) {
        $ultimo_dia = 28;
        while (checkdate($mes, $ultimo_dia + 1, $anio)) {
            $ultimo_dia++;
        }
        return $ultimo_dia;
    }

    function sms($val, $mensaje, $tnp) {
        $rdp = ($val * 100) / $tnp;
        $this->Ln();
        $this->Cell(25, 5, '', 0, 0, 'L');
        $this->Cell(90, 5, $mensaje, 0, 0, 'L');
        $this->Cell(25, 5, '', 0, 0, 'R');
        $this->Cell(25, 5, $op . number_format($val, 4), 0, 0, 'R');
        $this->Cell(25, 5, $op . number_format($rdp, 2), 0, 0, 'R');
        $this->Ln();
    }

    function ingresos($Rep, $desde, $hasta) {
        $n = 0;
        while ($n < 10) {
            $n++;
            $cue = "4.01.0$n.";
            $vin = pg_fetch_array($Rep->lista_balance_general($cue, $desde, $hasta));
            $tvi = $vin[debe3] - $vin[haber3];
            $tvin+=abs($tvi);
        }
        return abs($tvin);
    }

    function ventas_netas($Rep, $desde, $hasta) {
        $n = 0;
        while ($n < 10) {
            $n++;
            $cue = "4.01.0$n.";
            $vin = pg_fetch_array($Rep->lista_balance_general($cue, $desde, $hasta));
            $tvi = $vin[debe3] - $vin[haber3];
            $tvin+=abs($tvi);
        }
        $dsv = pg_fetch_array($Rep->lista_balance_general('4.01.10.', $desde, $hasta));
        $tdsv = $dsv[debe3] - $dsv[haber3];
        $dv = pg_fetch_array($Rep->lista_balance_general('4.01.11.', $desde, $hasta));
        $tdv = $dv[debe3] - $dv[haber3];
        $tvn = abs($tvin) - abs($tdsv) - abs($tdv);
        return abs($tvn);
    }

    function utilidad_bruta($Rep, $desde, $hasta) {
        $tvn = $this->ventas_netas($Rep, $desde, $hasta);
        $bp = pg_fetch_array($Rep->lista_balance_general('4.01.12.', $desde, $hasta));
        $tbp = $bp[debe3] - $bp[haber3];
        $rc = pg_fetch_array($Rep->lista_balance_general('4.01.13.', $desde, $hasta));
        $trc = $rc[debe3] - $rc[haber3];
        $uv = pg_fetch_array($Rep->lista_balance_general('4.01.18.', $desde, $hasta));
        $tuv = $uv[debe3] - $uv[haber3];
        $ir = pg_fetch_array($Rep->lista_balance_general('4.01.19.', $desde, $hasta));
        $tir = $ir[debe3] - $ir[haber3];
        $oi = pg_fetch_array($Rep->lista_balance_general('4.03.', $desde, $hasta));
        $toi = $oi[debe2] - $oi[haber2];
        $co = pg_fetch_array($Rep->lista_balance_general('5.', $desde, $hasta));
        $tco = $co[debe1] - $co[haber1];
        $tub = abs($tvn) - abs($tbp) - abs($trc) + abs($tuv) + abs($tir )+ abs($toi) - abs($tco);
        return abs($tub);
    }

    function utilidad_neta_ventas($Rep, $desde, $hasta) {
        $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
        $ga = pg_fetch_array($Rep->lista_balance_general('6.01.', $desde, $hasta));
        $tga = $ga[debe2] - $ga[haber2];
        $tunv = abs($tub) - abs($tga);
        return abs($tunv);
    }

    function utilidad_antes($Rep, $desde, $hasta) {
        $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
        $uai = pg_fetch_array($Rep->lista_balance_general('6.', $desde, $hasta));
        $tuai = $uai[debe1] - $uai[haber1];
        $tui = abs($tub) - abs($tuai);
        return abs($tui);
    }

    function utilidad_ejercicio($Rep, $desde, $hasta) {
        $tui = $this->utilidad_antes($Rep, $desde, $hasta);
        $ue = pg_fetch_array($Rep->lista_balance_general('7.', $desde, $hasta));
        $tue = $ue[debe1] - $ue[haber1];
        $tuej = abs($tui) - abs($tue);
        return abs($tuej);
    }

    function rpt($anio, $mes, $niv) {
        $hasta = $this->ultimoDia($mes, $anio);
//        $desde = '01-' . $mes . '-' . $anio;
//        $hasta = $hasta . '-' . $mes . '-' . $anio;
        if ($mes < 10) {
            $mes = '0' . $mes;
        } else {
            $mes = $mes;
        }
        $desde = trim($anio) . '-' . $mes . '-01';
        $hasta = trim($anio) . '-' . $mes . '-' . $hasta;

        $Rep = new Reportes();
        switch ($mes) {
            case '01':$mes = 'Enero';
                break;
            case '02':$mes = 'Febrero';
                break;
            case '03':$mes = 'Marzo';
                break;
            case '04':$mes = 'Abril';
                break;
            case '05':$mes = 'Mayo';
                break;
            case '06':$mes = 'Junio';
                break;
            case '07':$mes = 'Julio';
                break;
            case '08':$mes = 'Agosto';
                break;
            case '09':$mes = 'Septiembre';
                break;
            case '10':$mes = 'Octubre';
                break;
            case '11':$mes = 'Noviembre';
                break;
            case '12':$mes = 'Diciembre';
                break;
        }
        $array0 = Array();
        $cns_cuentas = $Rep->lista_asientos_epyg($desde, $hasta);
        while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
            if (!empty($rst_cuentas[con_concepto_debe])) {
                array_push($array0, $rst_cuentas[con_concepto_debe]);
            }
        }
        $array1 = array_unique($array0);

        $periodo = $mes . ' del ' . $anio;
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, "ESTADO DE RESULTADOS", 0, 0, 'C');
        $this->Ln();
//        $this->Cell(200, 5, "NOPERTI CIA. LTDA." . count($array1), 0, 0, 'C');
//        $this->Ln();
//        $this->Cell(200, 5, "RUC: 1790007871001", 0, 0, 'C');
//        $this->Ln();
        $this->SetFont('helvetica', '', 9);
        $this->Cell(200, 5, "Periodo: " . $periodo, 0, 0, 'C');
        $this->Ln(10);
        //Cuentas
        $this->Cell(25, 5, "Codigo", 'B', 0, 'C');
        $this->Cell(90, 5, "Cuenta", 'B', 0, 'C');
        $this->Cell(25, 5, "Parcial", 'B', 0, 'C');
        $this->Cell(25, 5, "Total", 'B', 0, 'C');
        $this->Cell(25, 5, "% Rendimiento", 'B', 0, 'C');
        $this->Ln();

        $n = 0;
        $j = 1;
        $g = 0;
        $det = 1;
        $ut = 1;
        $ua = 1;
        $uej = 1;
        $dv = 1;
        while ($n < count($array1)) {
            $d = substr($array1[$n], 0, 1);
            $d1 = substr($array1[$n], 0, 2);

            if ($d > '4') {
                $sm = pg_fetch_array($Rep->lista_balance_general('4.', $desde, $hasta));
                $tnp = $sm[debe1] - $sm[haber1];
                $ing = pg_fetch_array($Rep->lista_balance_general($d1, $desde, $hasta));
                $tn1 = $ing[debe1] - $ing[haber1];
                $rd1 = (abs($tn1) * 100) / abs($tnp);
            } else {
                $tvin = $this->ingresos($Rep, $desde, $hasta);
                $tn1 = abs($tvin);
                $rd1 = (abs($tn1) * 100) / abs($tn1);
            }



            if ($niv == 1 || $niv > 1) {
                if ($g != $d1) {

                    if ($d1 == '5.' && $det == 1) {
                        $tvn = $this->ventas_netas($Rep, $desde, $hasta);
                        $this->sms($tvn, 'VENTAS NETAS', $tnp);
                        $det = 0;
                    }
                    if ($d1 == '6.' && $ut == 1) {
                        $tub = $this->utilidad_bruta($Rep, $desde, $hasta, $tvn);
                        $this->sms($tub, 'UTILIDAD BRUTA EN VENTAS', $tnp);
                        $ut = 0;
                    }
                    if ($d1 == '7.' && $ua == 1) {
                        $tui = $this->utilidad_antes($Rep, $desde, $hasta);
                        $this->sms($tui, 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', $tnp);
                        $ua = 0;
                    }

                    $this->Ln();
                    $rst1 = pg_fetch_array($Rep->listar_descripcion_asiento($d1));
                    $this->Cell(25, 5, $rst1[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst1[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, $op . number_format(abs($tn1), 4), 0, 0, 'R');
                    $this->Cell(25, 5, $op . number_format(abs($rd1), 2), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if ($niv == 2 || $niv > 2) {
                $d2 = substr($array1[$n], 0, 5);
                if ($g2 != $d2) {
                    if (($d2 == '6.02.' || $d2 == '6.03.' || $d2 == '6.04.' || $d2 == '6.05.' || $d2 == '6.06.' || $d2 == '6.07.' || $d2 == '6.08.' || $d2 == '6.09.' || $d3 == '6.10.') && $dv == 1) {
                        $tunv = $this->utilidad_neta_ventas($Rep, $desde, $hasta);
                        $this->sms($tunv, 'UTILIDAD NETA EN VENTAS', abs($tnp));
                        $dv = 0;
                    }
                    $this->Ln();
                    $rst2 = pg_fetch_array($Rep->listar_descripcion_asiento($d2));
                    $sm2 = pg_fetch_array($Rep->lista_balance_general($d2, $desde, $hasta));
                    if ($d2 == '4.01.') {
                        $tvn = $this->ingresos($Rep, $desde, $hasta);
                        $tn2 = abs($tvn);
                    } else {
                        $tn2 = $sm2[debe2] - $sm2[haber2];
                    }
                    if ($d > '4') {
                        $rd2 = (abs($tn2) * 100) / abs($tnp);
                    } else {
                        $rd2 = (abs($tn2) * 100) / abs($tn1);
                    }
                    $this->Cell(25, 5, $rst2[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst2[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn2), 4), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd2), 2), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if ($niv == 3 || $niv > 3) {
                $d3 = substr($array1[$n], 0, 8);
                if ($g3 != $d3) {
                    if (($d3 == '4.01.12.' || $d3 == '4.01.13.' || $d3 == '4.01.18.') && $det == 1) {
                        $tvn = $this->ventas_netas($Rep, $desde, $hasta);
                        $this->sms($tvn, 'VENTAS NETAS', abs($tnp));
                        $det = 0;
                    }
                    $this->Ln();
                    $rst3 = pg_fetch_array($Rep->listar_descripcion_asiento($d3));
                    $sm3 = pg_fetch_array($Rep->lista_balance_general($d3, $desde, $hasta));
                    $tn3 = $sm3[debe3] - $sm3[haber3];
                    if ($d > '4') {
                        $rd3 = (abs($tn3) * 100) / abs($tnp);
                    } else {
                        $rd3 = (abs($tn3) * 100) / $tn1;
                    }
                    $this->Cell(25, 5, $rst3[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst3[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn3), 4), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd3), 2), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if ($niv == 4 || $niv > 4) {
                $d4 = substr($array1[$n], 0, 11);
                if ($g4 != $d4) {

                    $this->Ln();
                    $rst4 = pg_fetch_array($Rep->listar_descripcion_asiento($d4));
                    $sm4 = pg_fetch_array($Rep->lista_balance_general($d4, $desde, $hasta));
                    $tn4 = $sm4[debe4] - $sm4[haber4];
                    if ($d > '4') {
                        $rd4 = (abs($tn4) * 100) / abs($tnp);
                    } else {
                        $rd4 = (abs($tn4) * 100) / abs($tn1);
                    }
                    $this->Cell(25, 5, $rst4[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst4[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn4), 4), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd4), 2), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if ($niv == 5) {
                $rst_cuentas1 = pg_fetch_array($Rep->listar_descripcion_asiento($array1[$n]));
                $rst_v = pg_fetch_array($Rep->suma_cuentas($array1[$n], $desde, $hasta));
                $tot = $rst_v[debe] - $rst_v[haber];
                if ($d > '4') {
                    $rd5 = (abs($tot) * 100) / abs($tnp);
                } else {
                    $rd5 = (abs($tot) * 100) / abs($tn1);
                }

                $this->Cell(25, 5, $rst_cuentas1[pln_codigo], 0, 0, 'L');
                $this->Cell(90, 5, substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 35), 0, 0, 'L');
                $this->Cell(25, 5, number_format(abs($tot), 4), 0, 0, 'R');
                $this->Cell(25, 5, '', 0, 0, 'R');
                $this->Cell(25, 5, number_format(abs($rd5), 2), 0, 0, 'R');
                $this->Ln();
            }

            $n++;
            $g = $d1;
            $g2 = $d2;
            $g3 = $d3;
            $g4 = $d4;
        }
        if ($det == 1) {
            $tvn = $this->ventas_netas($Rep, $desde, $hasta);
            $this->sms(abs($tvn), 'VENTAS NETAS', abs($tnp));
            $det = 0;
        }
        if ($ut == 1) {
            $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
            $this->sms(abs($tub), 'UTILIDAD BRUTA EN VENTAS', abs($tnp));
            $ut = 0;
        }

        if ($dv == 1) {
            $tunv = $this->utilidad_neta_ventas($Rep, $desde, $hasta);
            $this->sms(abs($tunv), 'UTILIDAD NETA EN VENTAS', abs($tnp));
            $dv = 0;
        }
        if ($ua == 1) {
            $tui = $this->utilidad_antes($Rep, $desde, $hasta);
            $this->sms(abs($tui), 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', abs($tnp));
            $ua = 0;
        }
        if ($uej == 1) {
            $tuej = $this->utilidad_ejercicio($Rep, $desde, $hasta);
            $this->sms(abs($tuej), 'UTILIDAD NETA DEL EJERCICIO', abs($tnp));
            $ut = 0;
        }
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'PREPARADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'REVISADO', 'T', 0, 'C');
        $this->Cell(20, 5, '', '');
        $this->Cell(40, 5, 'AUTORIZADO', 'T', 0, 'C');
    }

}

$pdf = new PDF();
$pdf->AddPage();
$pdf->rpt($anio, $mes, $niv);
$pdf->Output();



