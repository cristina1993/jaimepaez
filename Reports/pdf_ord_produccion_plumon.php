<?php
include_once '../Clases/clsSetting.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Set();
$cns = $Set->lista_una_orden_produccion_plumon($_GET[id]);

class PDF extends FPDF {
function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.25, $h = 14, $wide = false) {
	$this->SetFont('Arial', '', 10);
        $this->Text($x+5, $y+17, $code);
	if($ext) {
		$code = $this->encode_code39_ext($code);
	}else {
		$code = strtoupper($code);
		if(!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
			$this->Error('Invalid barcode value: '.$code);
	}
	if ($cks)
		$code .= $this->checksum_code39($code);
	$code = '*'.$code.'*';
	$narrow_encoding = array (
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
		'+' => '100101001001', '%' => '101001001001' );

	$wide_encoding = array (
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
	for ($i = 0; $i< strlen($code); $i++)
		$encode .= $encoding[$code[$i]].$gap;

	//Draw bars
	$this->draw_code39($encode, $x, $y, $w, $h);
}
function checksum_code39($code) {
	$chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
							'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
							'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
							'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
	$sum = 0;
	for ($i=0 ; $i<strlen($code); $i++) {
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
	for ($i = 0 ; $i<strlen($code); $i++) {
		if (ord($code[$i]) > 127)
			$this->Error('Invalid character: '.$code[$i]);
		$code_ext .= $encode[$code[$i]];
	}
	return $code_ext;
}
function draw_code39($code, $x, $y, $w, $h) {
	for($i=0; $i<strlen($code); $i++) {
		if($code[$i] == '1')
			$this->Rect($x+$i*$w, $y, $w, $h, 'F');
	}
}

    function etq($rst, $rst_pro, $rst_cli, $rst_mp1, $rst_mp2, $rst_mp3, $rst_mp4) {
        $x = 0;
        $y = 0;        
        $this->Code39($x+5, $y+5,$rst[orp_num_pedido]);
        $this->SetFont('helvetica', 'B', 18);
        $this->Text($x + 55, $y + 28, "ORDEN PRODUCCION - PLUMON");
        $this->SetFont('helvetica', 'B', 8);
        $this->Line($x + 1, $y + 32, $x + 209, $y + 32);
        $this->Text($x + 1, $y + 35, "DATOS GENERALES");
        $this->Text($x + 56, $y + 35, "DETALLE PRODUCTOS");
        $this->Text($x + 105, $y + 35, "MATERIAS PRIMAS");
        $this->Line($x + 1, $y + 36, $x + 209, $y + 36);
        $this->Text($x + 2, $y + 40, "PEDIDO : " . $rst[orp_num_pedido]);
        $this->Text($x + 57, $y + 40, "ANCHO : " . $rst[orp_pro_ancho]);
        $this->Text($x + 106, $y + 40, "MP-1 :  " . $rst_mp1[mp_referencia]);
        $this->Text($x + 155, $y + 40, "" . $rst[orp_mf1] . " %");
        $this->Text($x + 170, $y + 40, "" . $rst[orp_kg1] . " Kg");
        $this->Text($x + 2, $y + 45, "CLIENTE : " . $rst_cli[cli_nombre]);
        $this->Text($x + 57, $y + 45, "LARGO : " . $rst[orp_pro_largo]);
        $this->Text($x + 106, $y + 45, "MP-2 :  " . $rst_mp2[mp_referencia]);
        $this->Text($x + 155, $y + 45, "" . $rst[orp_mf2] . " %");
        $this->Text($x + 170, $y + 45, "" . $rst[orp_kg2] . " Kg");
        $this->Text($x + 2, $y + 50, "PRODUCTO : " . $rst_pro[pro_descripcion]);
        $this->Text($x + 57, $y + 50, "PESO : " . $rst[orp_pro_peso]);
        $this->Text($x + 106, $y + 50, "MP-3 :  " . $rst_mp3[mp_referencia]);
        $this->Text($x + 155, $y + 50, "" . $rst[orp_mf3] . " %");
        $this->Text($x + 170, $y + 50, "" . $rst[orp_kg3] . " Kg");
        $this->Text($x + 2, $y + 55, "CANTIDAD : " . $rst[orp_cantidad]);
        $this->Text($x + 57, $y + 55, "GRAMAJE : " . $rst[orp_pro_gramaje]);
        $this->Text($x + 106, $y + 55, "MP-4 :  " . $rst_mp4[mp_referencia]);
        $this->Text($x + 155, $y + 55, "" . $rst[orp_mf4] . " %");
        $this->Text($x + 170, $y + 55, "" . $rst[orp_kg4] . " Kg");
        $this->Text($x + 2, $y + 60, "FECHA PEDIDO : " . $rst[orp_fec_pedido]);
        $this->Text($x + 57, $y + 60, "CAPA : " . $rst[orp_capa]);
        $this->Text($x + 106, $y + 60, "TOTAL");
        $this->Text($x + 155, $y + 60, "" . $rst[orp_mftotal] . " %");
        $this->Text($x + 170, $y + 60, "" . $rst[orp_kgtotal] . " Kg");
        $this->Text($x + 2, $y + 65, "FECHA ENTREGA : " . $rst[orp_fec_entrega]);
        $this->Text($x + 57, $y + 65, "ESPESOR : " . $rst[orp_espesor]);
        $this->Text($x + 57, $y + 70, "MEDIDOR DE VUELTAS : " . $rst[orp_med_vueltas]);
        $this->Text($x + 57, $y + 75, "PAQUETES : " . $rst[orp_paquetes]);
        $this->Line($x + 1, $y + 77, $x + 209, $y + 77);
        $this->Text($x + 90, $y + 80, "CONDICION DE MAQUINA");
        $this->Line($x + 1, $y + 81, $x + 209, $y + 81);
        $this->SetFont('helvetica', 'B', 8);
        $this->Text($x + 1, $y + 85, "TEMPERATURA: " . $rst[orp_temperatura] . "C");
        $this->Text($x + 46, $y + 85, "AGUA: " . $rst[orp_agua] . " Lt");
        $this->Text($x + 76, $y + 85, "RESINA: " . $rst[orp_resina] . " Lt");
        $this->Line($x + 1, $y + 87, $x + 209, $y + 87);
        $this->Text($x + 1, $y + 92, "OBSERVACIONES : " . $rst[orp_observaciones]);
        $this->Line($x + 1, $y + 102, $x + 209, $y + 102);
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
while ($rst = pg_fetch_array($cns)) {
    $rst_pro = pg_fetch_array($Set->lista_un_producto($rst[pro_id]));
    $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[cli_id]));
    $rst_mp1 = pg_fetch_array($Set->lista_un_mp($rst[orp_mp1]));
    $rst_mp2 = pg_fetch_array($Set->lista_un_mp($rst[orp_mp2]));
    $rst_mp3 = pg_fetch_array($Set->lista_un_mp($rst[orp_mp3]));
    $rst_mp4 = pg_fetch_array($Set->lista_un_mp($rst[orp_mp4]));
    $pdf->AddPage();
    $pdf->etq($rst, $rst_pro,$rst_cli, $rst_mp1, $rst_mp2, $rst_mp3, $rst_mp4);
}
$pdf->SetDisplayMode(100);
$pdf->Output();



