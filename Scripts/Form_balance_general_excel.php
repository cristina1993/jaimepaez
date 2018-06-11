<?php

header("Content-type: application/vnd.ms-excel; name='excel'");
header("Content-Disposition: filename=" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

include_once '../Clases/clsClase_balance_general.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_balance_general();
$niv = $_GET[nivel];
$anio = $_GET[anio];
$mes = $_GET[mes];
$ultimo_dia = 28;
while (checkdate($mes, $ultimo_dia + 1, $anio)) {
    $ultimo_dia++;
}
if ($mes == 13) {
    $desde = trim($anio) . '-01' . '-01';
    $hasta = trim($anio) . '-12' . '-31';
    $periodo = "Anual del " . trim($anio);
} else {
    $desde = trim($anio) . '-' . $mes . '-01';
    $hasta = trim($anio) . '-' . $mes . '-' . $ultimo_dia;
    $periodo = "DESDE: $desde AL $hasta";
}

$emisor = pg_fetch_array($Set->lista_un_emisor('1'));
///*******Primer Enc********
echo "
    <table>
    <thead>
    <tr><th colspan='5'>BALANCE GENERAL</th></tr>
    </thead>
    <tr><td colspan='5'>" . $emisor[emi_nombre_comercial] . "</td></tr>
    <tr><td colspan='5'>RUC: " . $emisor[emi_identificacion] . "</td></tr>
    <tr><td colspan='5'>Periodo $periodo</td></tr>";
    

echo "
    <tr>
    <td>COD. CUENTA</td>
    <td>NOMBRE CUENTA</td>
    <td>PARCIAL</td>
    <td>TOTAL</td>
    <td>% REND</td>
    </tr>
    ";

$i = 0;
while ($i < 3) {
    $i++;
    $d = $i . '.';
    $cns_cuentas = $Set->listar_asiento_agrupado($d, $desde, $hasta);
    $cuentas1 = Array();
    while ($rst_cuentas = pg_fetch_array($cns_cuentas)) {
        if (!empty($rst_cuentas[con_concepto_debe])) {
            array_push($cuentas1, $rst_cuentas[con_concepto_debe]);
        }
    }
    $cuentas = array_unique($cuentas1);
    $n = 0;
    $j = 1;
    $g = 0;
    while ($n < count($cuentas)) {
        $dt = explode('.', $cuentas[$n]);
        $d1 = $dt[0];
        $d = $dt[0] . '.';
        if ($d == '2.' && $det == 0) {

            $pp = pg_fetch_array($Set->suma_pasivo_patrimonio($desde, $hasta));
            $tpp = $pp[debe] - $pp[haber];
            $rdp = ($tpp * 100) / $tpp;
            echo"
                <tr>
                <td></td>
                <td>PASIVO Y PATRIMONIO</td>
                <td></td>
                <td>" . $op . "" . number_format($tpp, 4) . "</td>
                <td>" . $op . "" . number_format($rdp, 2) . "</td>
                </tr>
              ";
        }
//        if ($d1 == '2.' || $d1 == '3.') {
//            $op = '-';
//        }

       if (($niv == 1 || $niv > 1) && ($dt[0] != null)) {
                    if ($g != $d1) {
                        $rst1 = pg_fetch_array($Set->listar_descripcion_asiento($d1, $d));
                        $sm = pg_fetch_array($Set->lista_balance_general1($d1 . '%', $desde, $hasta));
                        $tn1 = ($sm[debe1] + $sm[debe2] - $sm[debe3]) - ($sm[haber1] + $sm[haber2] - $sm[haber3]);
                        if ($d1 == '2.' || $d1 == '3.') {
                            $rd1 = ($tn1 * 100) / $tpp;
                        } else {
                            $rd1 = ($tn1 * 100) / $tn1;
                        }
                        if ($d == '1.') {
                            $activo = $tn1;
                        }
                echo"
                    <tr>
                    <td>" . $rst1[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst1[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn1, 4) . "</td>
                    <td>" . $op . "" . number_format($rd1, 2) . "<td>
                    </tr>
                    ";
            }
        }
       if (($niv == 2 || $niv > 2) && ($dt[1] != null)) {
                    $dt2 = explode('.', $cuentas[$n]);
                    $d2 = $dt2[0] . '.' . $dt2[1];
                    $ds2 = $dt2[0] . '.' . $dt2[1] . '.';
                    if ($g2 != $d2) {
                        $rst2 = pg_fetch_array($Set->listar_descripcion_asiento($d2, $ds2));
                        $sm2 = pg_fetch_array($Set->lista_balance_general1($d2 . '%', $desde, $hasta));
                        $tn2 = ($sm2[debe1] + $sm2[debe2] - $sm2[debe3]) - ($sm2[haber1] + $sm2[haber2] - $sm2[haber3]);
                        if ($d1 == '2.' || $d1 == '3.') {
                            $rd2 = ($tn2 * 100) / $tpp;
                        } else {
                            $rd2 = ($tn2 * 100) / $tn1;
                        }
                        if ($d == '1.') {
                            $activo = $tn1;
                        }
                echo"
                    <tr>
                    <td>" . $rst2[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst2[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn2, 4) . "</td>
                    <td>" . $op . "" . number_format($rd2, 2) . "<td>
                    </tr>
                    ";
            }
        }
         if (($niv == 3 || $niv > 3) && ($dt[2] != null)) {
                    $dt3 = explode('.', $cuentas[$n]);
                    $d3 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2];
                    $ds3 = $dt3[0] . '.' . $dt3[1] . '.' . $dt3[2] . '.';
                    if ($g3 != $d3) {
                        $rst3 = pg_fetch_array($Set->listar_descripcion_asiento($d3, $ds3));
                        $sm3 = pg_fetch_array($Set->lista_balance_general1($d3 . '%', $desde, $hasta));
                        $tn3 = ($sm3[debe1] + $sm3[debe2] - $sm3[debe3]) - ($sm3[haber1] + $sm3[haber2] - $sm3[haber3]);
                        if ($d1 == '2.' || $d1 == '3.') {
                            $rd3 = ($tn3 * 100) / $tpp;
                        } else {
                            $rd3 = ($tn3 * 100) / $tn1;
                        }
                        if ($d == '1.') {
                            $activo = $tn1;
                        }
                echo"
                    <tr>
                    <td>" . $rst3[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst3[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn3, 4) . "</td>
                    <td>" . $op . "" . number_format($rd3, 2) . "<td>
                    </tr>
                    ";
            }
        }
       if (($niv == 4 || $niv > 4) && ($dt[3] != null)) {
                    $dt4 = explode('.', $cuentas[$n]);
                    $d4 = $dt4[0] . '.' . $dt4[1] . '.' . $dt4[2] . '.' . $dt4[3];
                    $ds4 = $dt4[0] . '.' . $dt4[1] . '.' . $dt4[2] . '.' . $dt4[3] . '.';
                    if ($g4 != $d4) {
                        $rst4 = pg_fetch_array($Set->listar_descripcion_asiento($d4, $ds4));
                        $sm4 = pg_fetch_array($Set->lista_balance_general1($d4 . '%', $desde, $hasta));
                        $tn4 = ($sm4[debe1] + $sm4[debe2] - $sm4[debe3]) - ($sm4[haber1] + $sm4[haber2] - $sm4[haber3]);
                        if ($d1 == '2.' || $d1 == '3.') {
                            $rd4 = ($tn4 * 100) / $tpp;
                        } else {
                            $rd4 = ($tn4 * 100) / $tn1;
                        }
                        if ($d == '1.') {
                            $activo = $tn1;
                        }
                echo"
                    <tr>
                    <td>" . $rst4[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst4[pln_descripcion]), 0, 35) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($tn4, 4) . "</td>
                    <td>" . $op . "" . number_format($rd4, 2) . "<td>
                    </tr>
                    ";
            }
        }
       if (($niv == 5) && ($dt[4] != null)) {
                    $rst_cuentas1 = pg_fetch_array($Set->listar_descripcion_asiento($cuentas[$n]));
                    $rst_v = pg_fetch_array($Set->suma_cuentas($cuentas[$n], $desde, $hasta));
                    $tot = $rst_v[debe] - $rst_v[haber];
                    if ($d1 == '2.' || $d1 == '3.') {
                        $rd5 = ($tot * 100) / $tpp;
                    } else {
                        $rd5 = ($tot * 100) / $tn1;
                    }
                    if ($d == '1.') {
                        $activo = $tn1;
                    }
            echo"
                    <tr>
                    <td>" . $rst_cuentas1[pln_codigo] . "</td>
                    <td>" . substr(strtoupper($rst_cuentas1[pln_descripcion]), 0, 35) . "</td>
                    <td>" . $op . "" . number_format($tot, 4) . "</td>
                    <td></td>
                    <td>" . $op . "" . number_format($rd5, 2) . "</td>
                    </tr>
                    ";
        }
        $n++;
        $g = $d1;
        $g2 = $d2;
        $g3 = $d3;
        $g4 = $d4;
    }
}
echo"
    <tr>
    <td></td>
    <td>RESULTADO DEL PERIODO</td>
    <td></td>
    <td>" .  number_format($activo + $tpp,2) . "</td>
    <td>" . number_format(0, 2) . "</td>
    </tr>
    ";
echo "<tr><td><br><br><br></td></tr>
     <tr><td colspan='2' align='center'>APROBADO</td>
        <td colspan='2' align='center'>REVISADO</td>
        <td colspan='2' align='center'>ELABORADO</td></tr>";
echo "</table>";
?>