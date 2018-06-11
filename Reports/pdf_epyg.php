<?php

include_once '../Clases/clsClase_reportes.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Rep = new Reportes();
$niv = $_GET[nivel];
$anio = $_GET[anio];
$mes = $_GET[mes];
$dc = 2;

class PDF extends FPDF {

    function ultimoDia($mes, $anio) {
        $ultimo_dia = 28;
        while (checkdate($mes, $ultimo_dia + 1, $anio)) {
            $ultimo_dia++;
        }
        return $ultimo_dia;
    }

    function sms($val, $mensaje, $tnp) {
        $dc = 2;
        $rdp = ($val * 100) / $tnp;
        $this->Ln();
        $this->Cell(25, 5, '', 0, 0, 'L');
        $this->Cell(90, 5, $mensaje, 0, 0, 'L');
        $this->Cell(25, 5, '', 0, 0, 'R');
        $this->Cell(25, 5, $op . number_format($val, $dc), 0, 0, 'R');
        $this->Cell(25, 5, $op . number_format($rdp, $dc), 0, 0, 'R');
        $this->Ln();
    }

    function ingresos($Rep, $desde, $hasta) {
        $n = 0;
        /// cambia forma de extraer informacion
        $cue = "4%";
        $vin = pg_fetch_array($Rep->lista_balance_general1($cue, $desde, $hasta));
        $tvi = ($vin[debe1] + $sm[debe2] + $vin[debe3]) - ($vin[haber1] + $vin[haber2] + $vin[haber3]);
        return abs($tvi);
    }

    function ventas_netas($Rep, $desde, $hasta) {
/// cambio las ventas netas = total ingreso
        $cue = "4%";
        $vin = pg_fetch_array($Rep->lista_balance_general1($cue, $desde, $hasta));
        $tvn = ($vin[debe1] + $sm[debe2] + $vin[debe3]) - ($vin[haber1] + $vin[haber2] + $vin[haber3]);
        return abs($tvn);
    }

    function utilidad_bruta($Rep, $desde, $hasta) {
        ///// cambio utilidad_bruta= ventas_netas - 5.;
        $tvn = $this->ventas_netas($Rep, $desde, $hasta);
        $co = pg_fetch_array($Rep->lista_balance_general1('5%', $desde, $hasta));
        $tco = ($co[debe1] + $co[debe2] + $co[debe3]) - ($co[haber1] + $co[haber2] + $co[haber3]);
        $tub = abs($tvn) - abs($tco);
        return ($tub);
    }

/// utilidad_neta_ventas no se utiliza
    function utilidad_neta_ventas($Rep, $desde, $hasta) {
        $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
        $ga = pg_fetch_array($Rep->lista_balance_general1('6.01%', $desde, $hasta));
        $tga = $ga[debe1] - $ga[haber1];
        $tunv = ($tub) - ($tga);
        return ($tunv);
    }

    function utilidad_antes($Rep, $desde, $hasta) {
        // cambio utilidad_antes_ejercicio= utilidad_bruta - 6.
        $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
        $uai = pg_fetch_array($Rep->lista_balance_general1('6%', $desde, $hasta));
        $tuai = ($uai[debe1] + $uai[debe2] + $uai[debe3]) - ($uai[haber1] + $uai[haber2] + $uai[haber3]);
        $tui = ($tub) - ($tuai);
        return ($tui);
    }

    function utilidad_ejercicio($Rep, $desde, $hasta) {
        ///cambio utilidad_ejercicio= utilidad_antes_ejercicio -7.
        $tui = $this->utilidad_antes($Rep, $desde, $hasta);
        $ue = pg_fetch_array($Rep->lista_balance_general1('7%', $desde, $hasta));
        $tue = ($ue[debe1] + $ue[debe2] + $ue[debe3]) - ($ue[haber1] + $ue[haber2] + $ue[haber3]);
        $tuej = ($tui) - ($tue);
        return ($tuej);
    }

    function rpt($anio, $mes, $niv, $dc) {
        $hasta = $this->ultimoDia($mes, $anio);
        if ($mes < 10) {
            $mes = '0' . $mes;
        } else {
            $mes = $mes;
        }
        if ($mes == 13) {
            $desde = trim($anio) . '-01' . '-01';
            $hasta = trim($anio) . '-12' . '-31';
        } else {
            $desde = trim($anio) . '-' . $mes . '-01';
            $hasta = trim($anio) . '-' . $mes . '-' . $hasta;
        }
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
            case '13':$mes = 'ANUAL';
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
            ///cambia forma de substrig por separador;
            $dt = explode('.', $array1[$n]);
            $d = $dt[0];
            $d1 = $dt[0].'.';
            if ($d > '4') {
                /// txds= operador para la busqueda con like
                $txs = $d . '%';
                $sm = pg_fetch_array($Rep->lista_balance_general1($txs, $desde, $hasta));
                $tnp = ($sm[debe1] + $sm[debe2] + $sm[debe3]) - ($sm[haber1] + $sm[haber2] + $sm[haber3]);
                $ing = pg_fetch_array($Rep->lista_balance_general1($txs, $desde, $hasta));
                $tn1 = ($ing[debe1] + $ing[debe2] + $ing[debe3]) - ($ing[haber1] + $ing[haber2] + $ing[haber3]);
                $rd1 = (abs($tn1) * 100) / abs($tnp);
            } else {
                $tvin = $this->ingresos($Rep, $desde, $hasta);
                $tn1 = ($tvin);
                $rd1 = (abs($tn1) * 100) / abs($tn1);
            }



            if (($niv == 1 || $niv > 1)&& ($dt[0]!=null)) {
                if ($g != $d1) {
                    if (($d1 == '5.' || $d1 == '6.' || $d1 == '7.') && $det == 1) {
                        $tvn = $this->ventas_netas($Rep, $desde, $hasta);
                        $this->sms($tvn, 'VENTAS NETAS', $tvin);
                        $det = 0;
                    }
                    if ($d1 == '6.' && $ut == 1) {
                        $tub = $this->utilidad_bruta($Rep, $desde, $hasta, $tvn);
                        $this->sms($tub, 'UTILIDAD BRUTA EN VENTAS', $tvin);
                        $ut = 0;
                    }
                    if ($d1 == '7.' && $ua == 1) {
                        $tui = $this->utilidad_antes($Rep, $desde, $hasta);
                        $this->sms($tui, 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', $tvin);
                        $ua = 0;
                    }

                    $this->Ln();
                    $rst1 = pg_fetch_array($Rep->listar_descripcion_asiento1($d, $d1));
                    $this->Cell(25, 5, $rst1[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst1[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, $op . number_format(($tn1), $dc), 0, 0, 'R');
                    $this->Cell(25, 5, $op . number_format(($rd1), $dc), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if (($niv == 2 || $niv > 2) && ($dt[1]!=null)) {
                //cambia forma de substrig por separador;
                $dt2 = explode('.', $array1[$n]);
                $d2 = $dt2[0] . '.' . $dt2[1];
                $ds2 = $dt2[0] . '.' . $dt2[1] . '.';
                if ($g2 != $d2) {
                    $this->Ln();
                    $rst2 = pg_fetch_array($Rep->listar_descripcion_asiento1($d2, $ds2));
                    $sm2 = pg_fetch_array($Rep->lista_balance_general1($d2 . '%', $desde, $hasta));
                    $tn2 = ($sm2[debe1] + $sm2[debe2] + $sm2[debe3]) - ($sm2[haber1] + $sm2[haber2] + $sm2[haber3]);
                    if ($d > '4') {
                        $rd2 = (abs($tn2) * 100) / abs($tnp);
                    } else {
                        $rd2 = (abs($tn2) * 100) / abs($tn1);
                    }
                    $this->Cell(25, 5, $rst2[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst2[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn2), $dc), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd2), $dc), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if (($niv == 3 || $niv > 3 ) && ($dt[2]!=null)) {
                //cambia forma de substrig por separador;
                $dt3 = explode('.', $array1[$n]);
                $d3 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2];
                $ds3 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2] . '.';
                if ($g3 != $d3) {
                     $this->Ln();
                    $rst3 = pg_fetch_array($Rep->listar_descripcion_asiento1($d3, $ds3));
                    $sm3 = pg_fetch_array($Rep->lista_balance_general1($d3 . '%', $desde, $hasta));
                    $tn3 = ($sm3[debe1] + $sm3[debe2] + $sm3[debe3]) - ($sm3[haber1] + $sm3[haber2] + $sm3[haber3]);
                    if ($d > '4') {
                        $rd3 = (abs($tn3) * 100) / abs($tnp);
                    } else {
                        $rd3 = (abs($tn3) * 100) / $tn1;
                    }
                    $this->Cell(25, 5, $rst3[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst3[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn3), $dc), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd3), $dc), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if (($niv == 4 || $niv > 4) && ($dt[3]!=null)) {
                $dt4 = explode('.', $array1[$n]);
                $d4 = $dt4[0] . '.' . $dt4[1] . '.' . $dt4[2] . '.' . $dt4[3];
                $ds4 = $dt4[0] . '.' . $dt4[1] . '.' . $dt4[2] . '.' . $dt4[3] . '.';
                if ($g4 != $d4) {
                    $this->Ln();
                    $rst4 = pg_fetch_array($Rep->listar_descripcion_asiento1($d4, $ds4));
                    $sm4 = pg_fetch_array($Rep->lista_balance_general1($d4 . '%', $desde, $hasta));
                    $tn4 = ($sm4[debe1] + $sm4[debe2] + $sm4[debe3]) - ($sm4[haber1] + $sm4[haber2] + $sm4[haber3]);
                    if ($d > '4') {
                        $rd4 = (abs($tn4) * 100) / abs($tnp);
                    } else {
                        $rd4 = (abs($tn4) * 100) / abs($tn1);
                    }
                    $this->Cell(25, 5, $rst4[pln_codigo], 0, 0, 'L');
                    $this->Cell(90, 5, substr(strtoupper($rst4[pln_descripcion]), 0, 35), 0, 0, 'L');
                    $this->Cell(25, 5, '', 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($tn4), $dc), 0, 0, 'R');
                    $this->Cell(25, 5, number_format(abs($rd4), $dc), 0, 0, 'R');
                    $this->Ln();
                }
            }
            if (($niv == 5) && ($dt[4]!=null)) {
                $d5 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2]. '.' . $dt3[3]. '.' . $dt3[4];
                $ds5 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2] . '.' . $dt3[3]. '.' . $dt3[4]. '.';
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
                $this->Cell(25, 5, number_format(abs($tot), $dc), 0, 0, 'R');
                $this->Cell(25, 5, '', 0, 0, 'R');
                $this->Cell(25, 5, number_format(abs($rd5), $dc), 0, 0, 'R');
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
            $this->sms(abs($tvn), 'VENTAS NETAS', abs($tvin));
            $det = 0;
        }
        if ($ut == 1) {
            $tub = $this->utilidad_bruta($Rep, $desde, $hasta);
            $this->sms(abs($tub), 'UTILIDAD BRUTA EN VENTAS', ($tvin));
            $ut = 0;
        }

//        if ($dv == 1) {
//            $tunv = $this->utilidad_neta_ventas($Rep, $desde, $hasta);
//            $this->sms(($tunv), 'UTILIDAD NETA EN VENTAS', ($tnp));
//            $dv = 0;
//        }
        if ($ua == 1) {
            $tui = $this->utilidad_antes($Rep, $desde, $hasta);
            $this->sms(($tui), 'UTILIDAD ANTES DE IMPUESTOS Y PARTICIPACIONES', ($tvin));
            $ua = 0;
        }
        if ($uej == 1) {
            $tuej = $this->utilidad_ejercicio($Rep, $desde, $hasta);
            $this->sms(($tuej), 'UTILIDAD NETA DEL EJERCICIO', ($tvin));
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
$pdf->rpt($anio, $mes, $niv, $dc);
$pdf->Output();



