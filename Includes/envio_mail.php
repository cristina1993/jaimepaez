<?php

include_once '../Reports/fpdf/fpdf.php';
include_once '../Clases/clsMail.php';
include_once '../Clases/clsClase_factura.php';

class PDF extends FPDF {

    function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.12, $h = 14, $wide = false) {
        $this->SetFont('Arial', 'B', 7);
        $this->Text($x + 5, $y + 17, $code);
        if ($ext) {
            $code = $this->encode_code39_ext($code);
        } else {
            $code = strtoupper($code);
            if (!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
                $this->Error('Invalid barcode value: ' . $code);
        }
        if ($cks)
            $code .= $this->checksum_code39($code);
        $code = '*' . $code . '*';
        $narrow_encoding = array(
            '0' => '101001101101', '1' => '110100101011', '2' => '101100101011',
            '3' => '110110010101', '4' => '101001101011', '5' => '110100110101',
            '6' => '101100110101', '7' => '101001011011', '8' => '110100101101',
            '9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
            'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101',
            'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
            'I' => '101101001101', 'J' => '101011001101', 'K' => '110101010011',
            'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
            'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011',
            'R' => '110101011001', 'S' => '101101011001', 'T' => '101011011001',
            'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101',
            'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
            '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101',
            '*' => '100101101101', '$' => '100100100101', '/' => '100100101001',
            '+' => '100101001001', '%' => '101001001001');

        $wide_encoding = array(
            '0' => '101000111011101', '1' => '111010001010111', '2' => '101110001010111',
            '3' => '111011100010101', '4' => '101000111010111', '5' => '111010001110101',
            '6' => '101110001110101', '7' => '101000101110111', '8' => '111010001011101',
            '9' => '101110001011101', 'A' => '111010100010111', 'B' => '101110100010111',
            'C' => '111011101000101', 'D' => '101011100010111', 'E' => '111010111000101',
            'F' => '101110111000101', 'G' => '101010001110111', 'H' => '111010100011101',
            'I' => '101110100011101', 'J' => '101011100011101', 'K' => '111010101000111',
            'L' => '101110101000111', 'M' => '111011101010001', 'N' => '101011101000111',
            'O' => '111010111010001', 'P' => '101110111010001', 'Q' => '101010111000111',
            'R' => '111010101110001', 'S' => '101110101110001', 'T' => '101011101110001',
            'U' => '111000101010111', 'V' => '100011101010111', 'W' => '111000111010101',
            'X' => '100010111010111', 'Y' => '111000101110101', 'Z' => '100011101110101',
            '-' => '100010101110111', '.' => '111000101011101', ' ' => '100011101011101',
            '*' => '100010111011101', '$' => '100010001000101', '/' => '100010001010001',
            '+' => '100010100010001', '%' => '101000100010001');

        $encoding = $wide ? $wide_encoding : $narrow_encoding;

        //Inter-character spacing
        $gap = ($w > 0.29) ? '00' : '0';

        //Convert to bars
        $encode = '';
        for ($i = 0; $i < strlen($code); $i++)
            $encode .= $encoding[$code[$i]] . $gap;

        //Draw bars
        $this->draw_code39($encode, $x, $y, $w, $h);
    }

    function checksum_code39($code) {
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
            'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
        $sum = 0;
        for ($i = 0; $i < strlen($code); $i++) {
            $a = array_keys($chars, $code[$i]);
            $sum += $a[0];
        }
        $r = $sum % 43;
        return $chars[$r];
    }

    function encode_code39_ext($code) {
        $encode = array(
            chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
            chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
            chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '?K',
            chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
            chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
            chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
            chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
            chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
            chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
            chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
            chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
            chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
            chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
            chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
            chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
            chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
            chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
            chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
            chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
            chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
            chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
            chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
            chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
            chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
            chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
            chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
            chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
            chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
            chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
            chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
            chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
            chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');

        $code_ext = '';
        for ($i = 0; $i < strlen($code); $i++) {
            if (ord($code[$i]) > 127)
                $this->Error('Invalid character: ' . $code[$i]);
            $code_ext .= $encode[$code[$i]];
        }
        return $code_ext;
    }

    function draw_code39($code, $x, $y, $w, $h) {
        for ($i = 0; $i < strlen($code); $i++) {
            if ($code[$i] == '1')
                $this->Rect($x + $i * $w, $y, $w, $h, 'F');
        }
    }

    function factura($rst1, $cns, $emisor, $amb, $dec) {

           // ///////////////////////////////// ENCABEZADO IZQUIERDO ///////////////////////////////////////////////////////
        $round = $dec;
        $this->Ln($x + 4, $y + 90);
//        if ($tp == 0) {
//            $this->Image('../img/logo_dalcroze.jpg', 5, 1, 100);
//        }
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(7, 34);
        $this->Cell(100, 15, utf8_decode($emisor[emi_nombre_comercial]), 'LRT', 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 45);
        $this->Cell(100, 9, "Dir Matriz : ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 50);
        $this->Cell(100, 9, utf8_decode($emisor[emi_dir_establecimiento_matriz]), 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 55);
        $this->Cell(100, 9, "  ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 60);
        $this->Cell(100, 9, " ", 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 67);
        $this->Cell(100, 5, "Contribuyente Especial Nro :" . $emisor[emi_contribuyente_especial], 'LR');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(7, 72);
        $this->Cell(100, 5, "OBLIGADO A LLEVAR CONTABILIDAD: $emisor[emi_obligado_llevar_contabilidad] ", 'LRB', 0, 'L');
        $this->Ln();
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////// ENCABEZADO DERECHO ////////////////////////////////////////////////////////        
        $this->SetFont('helvetica', 'B', 10);
        if ($amb != 0) {
            $this->SetXY(115, 9);
            $this->Cell(88, 7, "RUC: " . $emisor[emi_identificacion], 'LRT', 0, 'L');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 12);
            $this->SetXY(115, 14);
            $this->Cell(88, 5, "FACTURA", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 19);
            $this->Cell(88, 5, "No.   " . $rst1[fac_numero], 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 24);
            $this->Cell(88, 5, "NUMERO DE AUTORIZACION", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 8);
            $this->SetXY(115, 29);
            $this->Cell(88, 5, $rst1[fac_autorizacion], 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 34);
            $this->Cell(88, 5, "FECHA Y HORA DE  ", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 39);
            $this->Cell(88, 5, "AUTORIZACION : " . $rst1[fac_fec_hora_aut], 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 44);
            if ($amb == 1) {
                $ambiente = 'PRUEBAS';
            } else {
                $ambiente = 'PRODUCCION';
            }
            $this->Cell(88, 5, "AMBIENTE : " . $ambiente, 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 49);
            $this->Cell(88, 5, "EMISION :  NORMAL", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 54);
            $this->Cell(88, 5, "CLAVE DE ACCESO", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 59);
            $this->Code39($x + 115, $y + 59, $rst1[fac_clave_acceso]);
            $this->Cell(88, 19, "", 'LRB', 0, 'L');
            $this->Ln();
        } else {
            $this->SetXY(115, 34);
            $this->Cell(88, 10, "RUC: " . $emisor[emi_identificacion], 'LRT', 0, 'L');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 12);
            $this->SetXY(115, 39);
            $this->Cell(88, 10, "FACTURA", 'LR');
            $this->Ln();
            $this->SetFont('helvetica', 'B', 10);
            $this->SetXY(115, 44);
            $this->Cell(88, 10, "No.   " . $rst1[fac_numero], 'LR');
            $this->Ln();
            $this->SetXY(115, 49);
            $this->Cell(88, 10, "", 'LR');
            $this->Ln();
            $this->SetXY(115, 59);
            $this->Cell(88, 19, "", 'LRB', 0, 'L');
            $this->Ln();
        }


        //////////////////////////////////// ENCABEZADO CENTRAL ////////////////////////////////////////////////////////        
        $this->Ln($x + 4, $y + 1);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(141, 5, "Razon Social / Nombres y Apellidos : " . $rst1[fac_nombre], 'LT', 0, 'L');
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(55, 5, "Identificacion : " . $rst1[fac_identificacion], 'RT', 'R');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(141, 5, "Fecha de Emision : " . $rst1[fac_fecha_emision], 'LB', 0, 'L');
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(55, 5, "Guia Remision : " . $rst1[fac_num_guia_remision], 'RB', 0, 'L');
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////// CUERPO ////////////////////////////////////////////////////////                        
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(22, 5, "COD.", 'LRT', 0, 'C');
        $this->Cell(21, 5, "COD.", 'LT', 0, 'C');
        $this->Cell(13, 5, "CANT.", 'LT', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(80, 5, "DESCRIPCION", 'LT', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "PRECIO", 'LT', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "DESCUENTO", 'LT', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "PRECIO", 'LTR', 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(22, 5, "PRINCIPAL", 'LR', 0, 'C');
        $this->Cell(21, 5, "AUXILIAR", 'LR', 0, 'C');
        $this->Cell(13, 5, "", '', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(80, 5, "", 'L', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "UNITARIO", 'LR', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "", '', 0, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "TOTAL", 'LBR', 0, 'C');
        $this->Ln();

        //___________________________________________________________________________
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $this->SetFont('helvetica', '', 7);
            $this->Cell(22, 4, $rst[dfc_codigo], 1, 0, 'C');
            $this->Cell(21, 4, $rst[dfc_cod_aux], 1, 0, 'C');
            $this->Cell(13, 4, $rst[dfc_cantidad], 1, 0, 'C');
            $this->Cell(80, 4, utf8_decode(substr($rst[dfc_descripcion], 0, 40)), 1, 0, 'L');
            $this->Cell(20, 4, number_format($rst[dfc_precio_unit], $round), 1, 0, 'R');
            $this->Cell(20, 4, number_format($rst[dfc_val_descuento], $round), 1, 0, 'R');
            $this->Cell(20, 4, number_format($rst[dfc_precio_total], $round), 1, 0, 'R');
            $this->Ln();
        }
        //___________________________________________________________________________
        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "SUBTOTAL 12%", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_subtotal12], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 10);
        $this->Cell(85, 5, "Informacion Adicional ", 'LRT', 0, 'L');
        $this->Cell(51, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "SUBTOTAL 0%", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_subtotal0], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(85, 5, "", 'LR', 0, 'L');
        $this->Cell(51, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "SUBTOTAL No objeto de IVA", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_subtotal_no_iva], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(85, 5, "Direccion : " . utf8_decode($rst1[fac_direccion]), 'LR', 0, 'L');
        $this->Cell(51, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "SUBTOTAL Exento de IVA", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_subtotal_ex_iva], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(85, 5, "Telefono : " . $rst1[fac_telefono], 'LR', 0, 'L');
        $this->Cell(51, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "SUBTOTAL SIN IMPUESTOS", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_subtotal], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(85, 5, "Email :  " . strtolower($rst1[fac_email]), 'LR', 0, 'L');
        $this->Cell(51, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "DESCUENTO", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_descuento], $round), 'LRTB', 0, 'R');
        $this->Ln();

        $this->SetFont('helvetica', '', 4);
        $this->SetFont('helvetica', '', 8);



        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "ICE", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_ice], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "IVA 12%", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_iva], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "IRBPNR", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_irbpnr], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "PROPINA", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_propina], $round), 'LRTB', 0, 'R');

        $this->Ln();
        $this->Cell(136, 5, "", '', 0, 'L');
        $this->SetFont('helvetica', '', 8);

        $this->Cell(40, 5, "VALOR TOTAL", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[fac_total_valor], $round), 'LRTB', 0, 'R');
        $this->Ln();

        $this->SetXY(7.2, ($this->GetY() - 25));
        $this->MultiCell(85, 5, "Observaciones :  " . strtoupper($rst1[fac_observaciones]), 'RL', 1);
        //______________________________________________________________________________________________________________
        $this->MultiCell(85, 5, "Forma de pago", 'RL', 1);
        $n = 0;
        while ($rst2 = pg_fetch_array($cns2)) {
            $n++;
            if (empty($rst2[pag_valor])) {
                $debito = $rst2[pag_cant];
            } else {
                $debito = $rst2[pag_valor];
            }
            $this->Cell(85, 5, "$rst2[pag_contado]" . "  Dias : " . "Fecha Vencimiento    " . $rst2[pag_fecha_v] . " : Valor  $" . $debito, 'RL', 1);
        }
        //______________________________________________________________________________________________________________
        $this->MultiCell(85, 5, "", 'LRB', 1);
    
    }

}

date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_factura();
$Obj = new Mail();
$cns_docs = $Set->lista_no_enviados();
$rst_amb = pg_fetch_array($Set->lista_configuraciones());
$amb = $rst_amb[con_valor];
$rst_dec = pg_fetch_array($Set->lista_configuraciones_dec());
$emp = pg_fetch_array($Set->lista_configuraciones_empresa());
$dec = $rst_dec[con_valor];
$id = $_GET[id];
if (empty($id)) {
    $cns_docs = $Set->lista_no_enviados();
} else {
    $cns_docs = $Set->lista_una_factura_id($id);
}
if ($amb > 0) {
    while ($rst_docs = pg_fetch_array($cns_docs)) {
        $id = $rst_docs[fac_id];
        $rst1 = pg_fetch_array($Set->lista_una_factura_id($id));
        $cns = $Set->lista_detalle_factura($id);
        $emisor = pg_fetch_array($Set->lista_emisor($rst1[emi_id]));
        $cns2 = $Set->lista_pagos_factura($id); 
        
        $pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
        $pdf->AddPage();
        $pdf->factura($rst1, $cns, $emisor, $amb, $dec,$cns2);
        $pdf->SetDisplayMode(100);
        $pdf->Output('../mail/' . 'FAC' . $rst1[fac_numero] . strtoupper($emp[con_valor2]) . '.pdf', 'F');
        $xml_autorizado = "
                    <autorizacion>
                        <estado>AUTORIZADO</estado>
                        <numeroAutorizacion>" . $rst1[fac_autorizacion] . "</numeroAutorizacion>
                        <fechaAutorizacion>" . $rst1[fac_fec_hora_aut] . "</fechaAutorizacion>
                        <ambiente>" . $amb . "</ambiente>
                        <comprobante><![CDATA[
                        " . trim($rst1[fac_xml_doc]) . "
                        ]]></comprobante>
                        <mensajes/>
                    </autorizacion>
        ";
        $xml = fopen("../xml_docs/" . $rst1[fac_clave_acceso] . ".xml", "w+o");
        fwrite($xml, $xml_autorizado);
        fclose($xml);
        $files = array('../mail/' . 'FAC' . $rst1[fac_numero] . strtoupper($emp[con_valor2]) . '.pdf', '../xml_docs/' . $rst1[fac_clave_acceso] . '.xml');
        $sms = $Obj->envia_correo(strtolower($rst1[fac_email]), strtoupper($rst1[fac_nombre]), $files, $rst1[fac_numero], 1);
        if ($sms == '0') {
            $Set->update_status_mail($id, 'ENVIADO');
            $fnm = '../mail/' . $id . '.pdf';
        } else {
            echo $sms;
        }
    }
}
