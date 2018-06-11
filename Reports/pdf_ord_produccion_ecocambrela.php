<?php

include_once '../Clases/clsSetting.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Set();
$cns = $Set->lista_una_orden_produccion($_GET[id]);

class PDF extends FPDF {

    function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.25, $h = 14, $wide = false) {
        $this->SetFont('Arial', '', 10);
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

    function etq($rst, $rst_pro, $rst_cli, $rst_mp1, $rst_mp2, $rst_mp3, $rst_mp4) {
        $x = 0;
        $y = 0;
        $this->Code39($x + 5, $y + 5, $rst[ord_num_orden]);
        $this->SetFont('helvetica', 'B', 18);
        $this->Text($x + 50, $y + 28, "ORDEN PRODUCCION - ECOCAMBRELLA");
        $this->SetFont('helvetica', 'B', 8);
        $this->Line($x + 1, $y + 32, $x + 209, $y + 32);
        $this->Text($x + 1, $y + 35, "DATOS GENERALES");
        $this->Text($x + 60, $y + 35, "MATERIAS PRIMAS");
        $this->Line($x + 1, $y + 36, $x + 209, $y + 36);
        $this->Text($x + 2, $y + 40, "PEDIDO : " . $rst[ord_num_orden]);
        $this->Text($x + 60, $y + 40, "MP-1 :  " . $rst_mp1[mp_referencia]);
        $this->Text($x + 109, $y + 40, "" . $rst[ord_mf1] . " %");
        $this->Text($x + 124, $y + 40, "" . $rst[ord_kg1] . " Kg");
        $this->Text($x + 2, $y + 45, "CLIENTE : " . $rst_cli[cli_nombre]);
        $this->Text($x + 60, $y + 45, "MP-2 :  " . $rst_mp2[mp_referencia]);
        $this->Text($x + 109, $y + 45, "" . $rst[ord_mf2] . " %");
        $this->Text($x + 124, $y + 45, "" . $rst[ord_kg2] . " Kg");
        $this->Text($x + 2, $y + 50, "PRODUCTO : " . $rst_pro[pro_descripcion]);
        $this->Text($x + 60, $y + 50, "MP-3 :  " . $rst_mp3[mp_referencia]);
        $this->Text($x + 109, $y + 50, "" . $rst[ord_mf3] . " %");
        $this->Text($x + 124, $y + 50, "" . $rst[ord_kg3] . " Kg");
        $this->Text($x + 2, $y + 55, "# DE ROLLOS : " . $rst[ord_num_rollos]);
        $this->Text($x + 60, $y + 55, "MP-4 :  " . $rst_mp4[mp_referencia]);
        $this->Text($x + 109, $y + 55, "" . $rst[ord_mf4] . " %");
        $this->Text($x + 124, $y + 55, "" . $rst[ord_kg4] . " Kg");
        $this->Text($x + 2, $y + 60, "PESO TOTAL A PRODUCIR : " . $rst[ord_mftotal] . " Kg");
        
        
        $this->Text($x + 60, $y + 60, "MP-5 :  " . $rst_mp5[mp_referencia]);
        $this->Text($x + 109, $y + 60, "" . $rst[ord_mf5] . " %");
        $this->Text($x + 124, $y + 60, "" . $rst[ord_kg5] . " Kg");

        
        
        $this->Text($x + 60, $y + 70, "TOTAL");
        $this->Text($x + 124, $y + 70, "" . $rst[ord_kgtotal] . " Kg");
        
        
        
        $this->Text($x + 2, $y + 65, "FECHA PEDIDO : " . $rst[ord_fec_pedido]);
        
        $this->Text($x + 60, $y + 65, "MP-6 :  " . $rst_mp6[mp_referencia]);
        $this->Text($x + 109, $y + 65, "" . $rst[ord_mf6] . " %");
        $this->Text($x + 124, $y + 65, "" . $rst[ord_kg6] . " Kg");
        
        
        
        $this->Text($x + 2, $y + 70, "FECHA ENTREGA : " . $rst[ord_fec_entrega]);
        $this->Line($x + 1, $y + 73, $x + 209, $y + 73);
        $this->Text($x + 90, $y + 76, "DETALLE DE PRODUCTO");
        $this->Line($x + 1, $y + 77, $x + 209, $y + 77);
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 1, $y + 81, "ANCHO TOTAL: " . $rst[ord_anc_total] . " M");
        $this->Text($x + 46, $y + 81, "REFILADO: " . $rst[ord_refilado] . " M");
        $this->Text($x + 1, $y + 86, "PRODUCTO PRINCIPAL: " . $rst[ord_resina]);
        $this->Text($x + 90, $y + 86, "ANCHO: " . $rst[ord_pri_ancho] . " M");
        $this->Text($x + 115, $y + 86, "CARRILES: " . $rst[ord_pri_carril]);
        $this->Text($x + 135, $y + 86, "FALTANTE: " . $rst[ord_pri_faltante] . " M");
        $this->Text($x + 1, $y + 91, "PRODUCTO SECUNDARIO: " . $rst[ord_pro_secundario]);
        $this->Text($x + 90, $y + 91, "ANCHO: " . $rst[ord_sec_ancho] . " M");
        $this->Text($x + 115, $y + 91, "CARRILES: " . $rst[ord_sec_carril]);
        $this->Text($x + 1, $y + 96, "REPROCESO: ");
        $this->Text($x + 90, $y + 96, "ANCHO: " . $rst[ord_rep_ancho] . " M");
        $this->Text($x + 115, $y + 96, "CARRILES: " . $rst[ord_rep_carril]);
        $this->Text($x + 1, $y + 101, "LARGO: " . $rst[ord_largo] . " M");
        $this->Text($x + 46, $y + 101, "GRAMAJE: " . $rst[ord_gramaje] . " M/GR2");
        $this->Line($x + 1, $y + 103, $x + 209, $y + 103);
        $this->Text($x + 90, $y + 106, "SET MAQUINAS");
        $this->Line($x + 1, $y + 107, $x + 209, $y + 107);
        $this->Text($x + 1, $y + 110, "TEMPERATURA");
        $this->Text($x + 1, $y + 115, "ZONA 1: ");
        $this->Text($x + 36, $y + 115, "ZONA 2: ");
        $this->Text($x + 72, $y + 115, "ZONA 3: ");
        $this->Text($x + 108, $y + 115, "ZONA 4: ");
        $this->Text($x + 144, $y + 115, "ZONA 5: ");
        $this->Text($x + 180, $y + 115, "ZONA 6: ");
        $this->Text($x + 1, $y + 120, "" . $rst[ord_zo1]);
        $this->Text($x + 36, $y + 120, "" . $rst[ord_zo2]);
        $this->Text($x + 72, $y + 120, "" . $rst[ord_zo3]);
        $this->Text($x + 108, $y + 120, "" . $rst[ord_zo4]);
        $this->Text($x + 144, $y + 120, "" . $rst[ord_zo5]);
        $this->Text($x + 180, $y + 120, "" . $rst[ord_zo6]);
        $this->Text($x + 1, $y + 125, "CONDICIONES DE TABLA");
        $this->Text($x + 1, $y + 130, "SPINNETER TEMP : " . $rst[ord_spi_temp]);
        $this->Text($x + 90, $y + 130, "UPPER ROLLER HEATING ON/ OFF : " . $rst[ord_upp_rol_heating]);
        $this->Text($x + 1, $y + 135, "UPPER ROLLER TEMP CONTROLLER : " . $rst[ord_upp_rol_tem_controller]);
        $this->Text($x + 90, $y + 135, "UPPER ROLLER OIL PUMP : " . $rst[ord_upp_rol_oil_pump]);
        $this->Text($x + 1, $y + 140, "DOWN ROLLER TEMP CONTROLLER : " . $rst[ord_dow_rol_tem_controller]);
        $this->Text($x + 90, $y + 140, "DOWN ROLLER HEATING ON/ OFF : " . $rst[ord_dow_rol_heating]);
        $this->Text($x + 1, $y + 145, "SPINNETER TEMP CONTROLLER : " . $rst[ord_spi_tem_controller]);
        $this->Text($x + 90, $y + 145, "DOWN ROLLER OIL PUMP : " . $rst[ord_dow_rol_oil_pump]);
        $this->Text($x + 1, $y + 150, "COOL AIR TEMP: " . $rst[ord_coo_air_temp]);
        $this->Text($x + 90, $y + 150, "SPINNETER ROLLER HEATING ON/ OFF : " . $rst[ord_spi_rol_heating ]);
        $this->Text($x + 90, $y + 155, "SPINNETER ROLLER OIL PUMP : " . $rst[ord_spi_rol_oil_pump]);
        $this->Text($x + 1, $y + 160, "MATERING PUMP : " . $rst[ord_mat_pump]);
        $this->Text($x + 1, $y + 165, "SPINNETER BLOWER : " . $rst[ord_spi_blower]);
        $this->Text($x + 90, $y + 165, "GSM SETTING : " . $rst[ord_gsm_setting]);
        $this->Text($x + 1, $y + 170, "SIDE BLOWER : " . $rst[ord_sid_blower]);
        $this->Text($x + 90, $y + 170, "AUTO SPEED ADJUST: " . $rst[ord_aut_spe_adjust]);
        $this->Text($x + 1, $y + 175, "DRAFFTING BLOWER : " . $rst[ord_dra_blower]);
        $this->Text($x + 90, $y + 175, "SPEED MODE AUTO : " . $rst[ord_spe_mod_auto]);
        $this->Text($x + 1, $y + 180, "LAPPER SPEED: " . $rst[ord_lap_speed]);
        $this->Text($x + 1, $y + 185, "MANUAL SPEED SETTING : " . $rst[ord_man_spe_setting]);
        $this->Text($x + 1, $y + 190, "ROLLING MILL : " . $rst[ord_rol_mill]);
        $this->Text($x + 1, $y + 195, "WINDING  TENSILITY: " . $rst[ord_win_tensility]);
        $this->Text($x + 1, $y + 200, "MASTERBRANCH AUTOSETTING: " . $rst[ord_mas_bra_autosetting]);
        $this->Text($x + 1, $y + 205, "ROLLING MILL UP/DOWN: " . $rst[ord_rol_mil_up_down]);
        $this->Line($x + 1, $y + 208, $x + 209, $y + 208);
        $this->Text($x + 1, $y + 211, "OBSERVACIONES : " . $rst[ord_observaciones]);
        $this->Line($x + 1, $y + 221, $x + 209, $y + 221);
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
while ($rst = pg_fetch_array($cns)) {
    $rst_pro = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
    $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[cli_id]));
    $rst_mp1 = pg_fetch_array($Set->lista_un_mp($rst[ord_mp1]));
    $rst_mp2 = pg_fetch_array($Set->lista_un_mp($rst[ord_mp2]));
    $rst_mp3 = pg_fetch_array($Set->lista_un_mp($rst[ord_mp3]));
    $rst_mp4 = pg_fetch_array($Set->lista_un_mp($rst[ord_mp4]));
    $pdf->AddPage();
    $pdf->etq($rst, $rst_pro, $rst_cli, $rst_mp1, $rst_mp2, $rst_mp3, $rst_mp4);
}
$pdf->SetDisplayMode(100);
$pdf->Output();



