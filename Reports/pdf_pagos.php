<?php

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
include_once '../Clases/clsClase_cuentasxpagar.php';
require_once 'fpdf/fpdf.php';
$Cxp = new CuentasPagar();
$cod = trim($_REQUEST[id]);
$fecha = $_REQUEST[fecha];
$op = $_REQUEST[op];
$egr = $_REQUEST[egr];
$rst_enc = pg_fetch_array($Cxp->lista_obligacion_pago_datos_egr($cod, $fecha, $egr));
$cns = $Cxp->lista_asientos_obligacion_pago_egr($cod, $egr);

class PDF extends FPDF {

    function reporte($cns, $rst_enc, $egr) {
        $Cxp = new CuentasPagar();
        $this->SetFont('Arial', 'B', 8);
        $x = 0;
        $y = 0;
//        $file = '../img/logo_noperti.jpg';
//        $this->Image($file, $x + 10, $y + 5, 70);
        $this->Text($x + 7, $y + 40, "FECHA DE PAGO:");
        $this->Text($x + 50, $y + 40, $rst_enc[obl_fecha_pago]);
        $this->Text($x + 7, $y + 45, "CLIENTE/PROVEEDOR:");
        $this->Text($x + 7, $y + 50, "TOTAL A PAGAR:");
        $this->Text($x + 7, $y + 55, "CONCEPTO DE PAGO:");
        $this->Text($x + 50, $y + 55, utf8_decode($rst_enc[obl_concepto]));
        $this->Text($x + 7, $y + 60, "FORMA DE PAGO:");
        $this->Text($x + 50, $y + 60, $rst_enc[obl_forma_pago]);
        $this->Text($x + 7, $y + 65, "BANCO:");
        $this->Text($x + 90, $y + 65, "CHEQUE:");
        $this->SetXY($x + 7, $y + 70);
        $this->Cell(25, 5, "COD. CONTABLE", 'TB', 0, 'C');
        $this->Cell(50, 5, "CUENTA CONTABLE", 'TB', 0, 'C');
        $this->Cell(60, 5, "NUM FACTURA", 'TB', 0, 'C');
        $this->Cell(15, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(15, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
        $tdebe = 0;
        $rst_obl = pg_fetch_array($Cxp->lista_obligaciones_reg_egr($rst_enc[obl_codigo], $egr));
        $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst_obl[reg_ruc_cliente]));
        while ($rst = pg_fetch_array($cns)) {
            $rst_asiento = pg_fetch_array($Cxp->lista_asiento_codigo($rst[con_asiento]));
            $rst_debe = pg_fetch_array($Cxp->lista_debe_factura_egr($rst[reg_id], $rst_enc[obl_codigo], $egr));
            $this->Cell(25, 5, utf8_decode($rst_asiento[con_concepto_haber]), 0, 0, 'L');
            $this->Cell(50, 5, substr(utf8_decode($rst_asiento[pln_descripcion]), 0, 45), 0, 0, 'L');
            $this->Cell(60, 5, $rst[reg_num_documento], 0, 0, 'C');
            $this->Cell(15, 5, number_format($rst_debe[sum], 2), 0, 0, 'R');
            $this->Cell(15, 5, '', 0, 0, 'R');
            $this->Ln();
            $tdebe+=$rst_debe[sum];
        }
        $rst_cuenta = pg_fetch_array($Cxp->lista_una_cuenta_bancos($rst_obl[obl_cuenta]));

        $this->Text($x + 50, $y + 45, utf8_decode($rst_cli[cli_raz_social]));
        $this->Text($x + 50, $y + 50, number_format($tdebe, 2));
        $this->Text($x + 20, $y + 65, utf8_decode($rst_cuenta[byc_referencia]));
        $this->Text($x + 110, $y + 65, $rst_obl[obl_doc]);
        $this->Cell(25, 5, $rst_obl[obl_cuenta], 0, 0, 'L');
        $this->Cell(50, 5, utf8_decode($rst_cuenta[pln_descripcion]), 0, 0, 'L');
        $this->Cell(60, 5, '', 0, 0, 'C');
        $this->Cell(15, 5, '', 0, 0, 'R');
        $this->Cell(15, 5, number_format($rst_obl[sum], 2), 0, 0, 'R');
        $this->Ln();
        $this->Cell(155, 3, '', 'B', 0, 'L');
        $this->Ln();
        $this->Cell(135, 5, '', 0, 0, 'L');
        $this->Cell(15, 5, number_format($tdebe, 2), 0, 0, 'R');
        $this->Cell(15, 5, number_format($rst_obl[sum], 2), 0, 0, 'R');
        $this->Ln(20);
        $this->Cell(40, 7, 'PREPARADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'REVISADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'RECIBIDO', 'T', 0, 'L');

        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(204, 15, 45);
        $this->Text($x + 90, $y + 30, 'EGRESO No:');
        $this->Text($x + 115, $y + 30, $egr);
    }

    function reporte2($cns, $rst_enc) {
        $Cxp = new CuentasPagar();
        $this->SetFont('Arial', 'B', 8);
        $x = 0;
        $y = 0;
//        $file = '../img/logo_noperti.jpg';
//        $this->Image($file, $x + 10, $y + 5, 70);
        $this->Text($x + 7, $y + 40, "FECHA DE PAGO:");
        $this->Text($x + 50, $y + 40, $rst_enc[obl_fecha_pago]);
        $this->Text($x + 7, $y + 45, "CLIENTE/PROVEEDOR:");
        $this->Text($x + 7, $y + 50, "TOTAL A PAGAR:");
        $this->Text($x + 7, $y + 55, "CONCEPTO DE PAGO:");
        $this->Text($x + 50, $y + 55, utf8_decode($rst_enc[obl_concepto]));
        $this->Text($x + 7, $y + 60, "FORMA DE PAGO:");
        $this->Text($x + 50, $y + 60, utf8_decode($rst_enc[obl_forma_pago]));
        $this->Text($x + 7, $y + 65, "BANCO:");
        $this->Text($x + 90, $y + 65, "CHEQUE:");
        $this->SetXY($x + 7, $y + 70);
        $this->Cell(25, 5, "COD. CONTABLE", 'TB', 0, 'C');
        $this->Cell(50, 5, "CUENTA CONTABLE", 'TB', 0, 'C');
        $this->Cell(50, 5, "NUM DOCUMENTO", 'TB', 0, 'C');
        $this->Cell(15, 5, "DEBE", 'TB', 0, 'C');
        $this->Cell(15, 5, "HABER", 'TB', 0, 'C');
        $this->Ln();
        $tdebe = 0;
        $rst_obl = pg_fetch_array($Cxp->lista_obligacion_cod($rst_enc[obl_codigo]));
        $rst_cli = pg_fetch_array($Cxp->lista_un_cliente_id($rst_obl[cli_id]));
        $cns1 = $Cxp->lista_un_asiento($rst_obl[con_asiento]);
        while ($rst = pg_fetch_array($cns1)) {
            $n++;
            if ($rst[tipo] == 0) {
                $debe = $rst[valor];
                $haber = 0;
            } else {
                $debe = 0;
                $haber = $rst[valor];
            }
            $rst_cta = pg_fetch_array($Cxp->lista_un_plan_cod($rst[concepto]));
            $this->Cell(25, 5, utf8_decode($rst[concepto]), 0, 0, 'L');
            $this->Cell(50, 5, substr(utf8_decode($rst_cta[pln_descripcion]), 0, 45), 0, 0, 'L');
            $this->Cell(60, 5, $rst[con_documento], 0, 0, 'C');
            $this->Cell(15, 5, number_format($debe, 2), 0, 0, 'R');
            $this->Cell(15, 5, number_format($haber, 2), 0, 0, 'R');
            $this->Ln();
            $tdebe+=$debe;
            $thaber+=$haber;
        }
        $rst_cuenta = pg_fetch_array($Cxp->lista_una_cuenta_bancos($rst_obl[obl_cuenta]));

        $this->Text($x + 50, $y + 45, utf8_decode($rst_cli[cli_raz_social]));
        $this->Text($x + 50, $y + 50, number_format($tdebe, 2));
        $this->Text($x + 20, $y + 65, utf8_decode($rst_cuenta[byc_referencia]));
        $this->Text($x + 110, $y + 65, $rst_obl[obl_doc]);
        $this->Cell(135, 3, '', 'B', 0, 'L');
        $this->Ln();
        $this->Cell(105, 5, '', 0, 0, 'L');
        $this->Cell(15, 5, number_format($tdebe, 2), 0, 0, 'R');
        $this->Cell(15, 5, number_format($thaber, 2), 0, 0, 'R');
        $this->Ln(20);
        $this->Cell(40, 7, 'PREPARADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'REVISADO', 'T', 0, 'L');
        $this->Cell(7, 7, '', 0, 0, 'C');
        $this->Cell(40, 7, 'RECIBIDO', 'T', 0, 'L');

        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(204, 15, 45);
        $this->Text($x + 90, $y + 30, 'EGRESO No:');
        $this->Text($x + 115, $y + 30, $rst_obl[obl_num_egreso]);
    }

    function numtoletras($xcifra) {
        $xarray = array(0 => "Cero",
            1 => "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";


        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)

        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);

            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el lÃ­mite a 6 dÃ­gitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegÃ³ al lÃ­mite mÃ¡ximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dÃ­gitos)

                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dÃ­gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            } else {
                                $key = (int) substr($xaux, 0, 3);

                                if (TRUE == array_key_exists($key, $xarray)) {  // busco si la centena es nÃºmero redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (MillÃ³n, Millones, Mil o nada)

                                    if (substr($xaux, 0, 3) == 100) {
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    } else {
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    }

                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                } else { // entra aquÃ­ si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lÃ³gica que las centenas)
                            $xsub = $this->subfijo($xaux);
                            if (substr($xaux, 1, 2) < 10) {
                                
                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE == array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    if (substr($xaux, 1, 2) == 20) {
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    } else {
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    }
                                    $xy = 3;
                                } else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10) {
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    } else {
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                    }
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)

                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
//                                RETURN $xseek ;
                                if (trim($xsub) == 'MIL' && trim($xseek) == 'UNO') {
                                    $xcadena = " " . $xcadena . " " . $xsub;
                                } else {
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                }
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO
            if (substr(trim($xcadena), -5, 5) == "ILLON") { // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";
            }
            if (substr(trim($xcadena), -7, 7) == "ILLONES") {// si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";
            }
//            // ----------- esta lÃ­nea la puedes cambiar de acuerdo a tus necesidades o a tu paÃ­s -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO CON $xdecimales/100";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN CON $xdecimales/100";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= "CON $xdecimales/100"; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
//            // ------------------      en este caso, para MÃ©xico se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UNO UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

    function subfijo($xx) { // esta funciÃ³n regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3) {
            $xsub = "";
        }
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6) {
            $xsub = "MIL ";
        }
        //
        return $xsub;
    }

    function encabezado($nom, $pago) {
        $this->SetXY(20, 19);
        $this->SetFont('helvetica', 'B', 11);
        if (strlen($nom) < 38) {
            $nom = $nom;
        } else {
            $nm = explode(' ', $nom);
            $n = $nm[0] . ' ' . $nm[1] . ' ' . $nm[2] . ' ' . $nm[3];
            if (strlen($n) > 38) {
                $nom = $nm[0] . ' ' . $nm[1] . ' ' . $nm[2];
            } else {
                $nom = $n;
            }
        }


        $this->Cell(110, 5, strtoupper(utf8_decode($nom)), 0, 0, 'L');
        $this->Cell(30, 5, number_format($pago, 2), 0, 0, 'L');
        $this->Ln();
        $this->SetXY(20, 26);
        $txt = $this->numtoletras(str_replace(',', '', number_format($pago, 2)));
        if (strlen($txt) < 58) {
            $txt = $txt;
        } else {
            $tx = explode(' ', $txt);
            $t = count($tx);
            $div = $t / 2;
            $i = 0;
            $d = number_format($div);
            while ($i < $d) {
                $cad = $cad . ' ' . $tx[$i];
                $i++;
            }
            $j = $i;
            while ($j < $t) {
                $txt2 = $txt2 . ' ' . $tx[$j];
                $j++;
            }
            $txt = $cad;
        }

        $this->Cell(200, 5, $txt, 0, 0, 'L');
        $this->Ln();
        $this->SetXY(20, 32);
        $this->Cell(200, 5, $txt2, 0, 0, 'L');
        $this->Ln();
        $this->SetXY(5, 39);
        $this->Cell(200, 5, "QUITO, " . date('Y-m-d'), 0, 0, 'L');
    }

}

$pdf = new PDF();
$pdf->AddPage();
if ($op == 0) {
    $pdf->reporte($cns, $rst_enc, $egr);
} else if ($op == 1 && $rst_enc[obl_forma_pago] == 'CHEQUE') {
    $pdf->encabezado($_REQUEST[nom], $_REQUEST[monto]);
} else if ($op == 1) {
    $pdf->encabezado($_REQUEST[nom], $_REQUEST[monto]);
} else if ($op == 2) {
    $pdf->reporte2($cns, $rst_enc);
}
$pdf->Output();
