<?php

include_once '../Clases/clsSetting.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Set();
$cns = $Set->lista_etq_orden($_GET[id]);
$rst_total = pg_fetch_array($Set->lista_etq_orden_total($_GET[id]));
if ($rst_total[peso] == 0) {
    $cns = $Set->lista_etq_orden_mov($_GET[id]);
    $rst_total = pg_fetch_array($Set->lista_etq_orden_total_mov($_GET[id]));
}

class PDF extends FPDF {

    function Code39($x, $y, $code, $w = 0.46, $h = 28, $ext = false, $cks = false, $wide = false) {
        $this->SetFont('Arial', '', 10);
        $this->Text($x + 30, $y + $h + 4, $code);
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
        $gap = ($w > 0.29) ? '00' : '0';
        $encode = '';
        for ($i = 0; $i < strlen($code); $i++)
            $encode .= $encoding[$code[$i]] . $gap;
        $this->draw_code39($encode, $x, $y, $w, $h);
    }

    function etq($rst) {
        $x = 0;
        $y = 0;
        $this->SetFont('helvetica', 'B', 16);
        $this->Text($x + 30, $y + 5, "NOPERTI CIA LTDA");
        $this->Line($x + 1, $y + 6, $x + 110, $y + 6);
        
        $cx = strlen($rst_total[etq_bar_code]);
        if ($cx == 15) {
            $x1 = 2;
        } else {
            $x1 = 8;
        }


        $this->Code39($x1, 7, $rst[etq_bar_code]);
        $this->Line($x + 1, $y + 40, $x + 110, $y + 40);
        $this->SetFont('helvetica', 'B', 10);
        $this->Text($x + 3, $y + 45, "REFERENCIA:  " . $rst[mp_referencia]);
        $this->Text($x + 3, $y + 60, "PESO: " . $rst[etq_peso] . " KG");
        $this->Text($x + 70, $y + 60, "FECHA: " . date("Y-m-d"));
    }

    function etq_total($rst_total) {
        $x = 0;
        $y = 0;
        $this->SetFont('helvetica', 'B', 16);
        $this->Text($x + 30, $y + 5, "NOPERTI CIA LTDA");
        $this->Line($x + 1, $y + 6, $x + 110, $y + 6);

        $cx = strlen($rst_total[etq_bar_code]);
        if ($cx == 15) {
            $x1 = 2;
        } else {
            $x1 = 8;
        }

        $this->Code39($x1, 7, $rst_total[etq_bar_code]);
        $this->Line($x + 1, $y + 40, $x + 110, $y + 40);
        $this->SetFont('helvetica', 'B', 10);
        $this->Text($x + 3, $y + 45, "REFERENCIA:  " . $rst_total[mp_referencia]);
        $this->Text($x + 3, $y + 60, "PESO TOTAL: " . $rst_total[peso] . " KG");
        $this->Text($x + 70, $y + 60, "FECHA: " . date("Y-m-d"));
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'etq_nop');
$pdf->AddPage();
$pdf->etq_total($rst_total);
while ($rst = pg_fetch_array($cns)) {
    $pdf->AddPage();
    $pdf->etq($rst);
}

$pdf->SetDisplayMode(100);
$pdf->Output();



