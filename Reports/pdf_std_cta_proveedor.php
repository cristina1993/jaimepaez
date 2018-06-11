<?php

include_once '../Clases/clsClase_cuentasxpagar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasPagar();
$id = $_GET[id];
$dec = $_GET[dec];
$fec_act = date("Y-m-d");
$emisor = pg_fetch_array($Set->lista_emisor_id('1'));
$cliente = pg_fetch_array($Set->lista_cliente_id($id));

class PDF extends FPDF {

    function encabezado($emisor, $fec, $cliente) {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(135, 5, "ESTADO DE CUENTA PROVEEDOR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(135, 5, utf8_decode($emisor[emi_nombre_comercial]), 0, 0, 'C');
        $this->Ln();
        $this->Cell(135, 5, "RUC: " . $emisor[emi_identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(135, 5, "AL: " . $fec, 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 8);
        $this->Cell(50, 5, "CODIGO CLIENTE: " . $cliente[cli_codigo], 0, 0, 'L');
        $this->Cell(62, 5, "CLIENTE: " . utf8_decode($cliente[cli_raz_social]), 0, 0, 'L');
        $this->Ln();
        $this->Cell(50, 5, "RUC: " . $cliente[cli_ced_ruc], 0, 0, 'L');
        $this->Cell(62, 5, "TELEFONO: " . $cliente[cli_telefono], 0, 0, 'L');
        $this->Ln();
        $this->Cell(62, 5, "DIRECCION: " . utf8_decode($cliente[cli_calle_prin]), 0, 0, 'L');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(135, 5, "VENCIDOS", 'T', 0, 'C');
        $this->Ln();
        $this->Cell(35, 5, "FACTURA", 'TB', 0, 'C');
        $this->Cell(30, 5, "FECHA", 'TB', 0, 'C');
        $this->Cell(20, 5, "VALOR", 'TB', 0, 'C');
        $this->Cell(25, 5, "PLAZO", 'TB', 0, 'C');
        $this->Cell(25, 5, "VENCIMIENTO", 'TB', 0, 'C');
        $this->Ln();
    }

    function doc($dec, $id) {
        $Set = new CuentasPagar();
        $fec_act = date("Y-m-d");
        $dias = 30;
        $fec_ant_30 = date("Y-m-d", strtotime("$fec_act -$dias day")); // vencido 30
        $vencido_30 = $Set->lista_pagos_vencidos_rep($id, $fec_ant_30, $fec_act);

        $fec_ant_60 = date("Y-m-d", strtotime("$fec_ant_30 -$dias day")); // vencido 60
        $vencido_60 = $Set->lista_pagos_vencidos_rep($id, $fec_ant_60, $fec_ant_30);

        $fec_ant_90 = date("Y-m-d", strtotime("$fec_ant_60 -$dias day")); // vencido 90
        $vencido_90 = $Set->lista_pagos_vencidos_rep($id, $fec_ant_90, $fec_ant_60);

        $fec_ant_120 = date("Y-m-d", strtotime("$fec_ant_90 -$dias day")); // vencido 120
        $vencido_120 = $Set->lista_pagos_vencidos_rep($id, $fec_ant_120, $fec_ant_90);

        $vencido_m120 = $Set->lista_pagos_vencidos_m120($id, $fec_ant_120);

        $por_vencer = $Set->lista_pag_porvencer($id, $fec_act); // por vencer

        $n = 0;
        while ($rst_doc = pg_fetch_array($vencido_30)) {
       
$n++;
            $tot_ven_30 = ($rst_doc[pag_cant] + $rst_doc[debito]) - $rst_doc[credito];
        
if ($tot_ven_30 != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc[reg_num_documento]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_ven_30, $dec), 0, 0, 'R');
                $this->Cell(25, 5, (strtotime($rst_doc[pag_fecha_v]) - strtotime($rst_doc[reg_femision])) / 86400, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot1+=$tot_ven_30;
                $tot_ven_30 = 0;
            }
        }
        while ($rst_doc1 = pg_fetch_array($vencido_60)) {
            $n++;
            $tot_ven_60 = ($rst_doc1[pag_cant] + $rst_doc1[debito]) - $rst_doc1[credito];
            if ($tot_ven_60 != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc1[reg_num_documento]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc1[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_ven_60, $dec), 0, 0, 'R');
                $this->Cell(25, 5, (strtotime($rst_doc1[pag_fecha_v]) - strtotime($rst_doc1[reg_femision])) / 86400, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc1[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot2+=$tot_ven_60;
                $tot_ven_60 = 0;
            }
        }
        while ($rst_doc2 = pg_fetch_array($vencido_90)) {
            $n++;
            $tot_ven_90 = ($rst_doc2[pag_cant] + $rst_doc2[debito]) - $rst_doc2[credito];
            if ($tot_ven_90 != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc2[reg_num_documento]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc2[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_ven_90, $dec), 0, 0, 'R');
                $this->Cell(25, 5, (strtotime($rst_doc2[pag_fecha_v]) - strtotime($rst_doc2[reg_femision])) / 86400, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc2[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot3+=$tot_ven_90;
                $tot_ven_90 = 0;
            }
        }
        while ($rst_doc3 = pg_fetch_array($vencido_120)) {
            $n++;
            $tot_ven_120 = ($rst_doc3[pag_cant] + $rst_doc3[debito]) - $rst_doc3[credito];
            if ($tot_ven_120 != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc3[reg_num_documento]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc3[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_ven_120, $dec), 0, 0, 'R');
                $this->Cell(25, 5, (strtotime($rst_doc3[pag_fecha_v]) - strtotime($rst_doc3[reg_femision])) / 86400, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc3[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot4+=$tot_ven_120;
                $tot_ven_120 = 0;
            }
        }
        while ($rst_doc4 = pg_fetch_array($vencido_m120)) {
            $n++;
            $tot_ven_m120 = ($rst_doc4[pag_cant] + $rst_doc4[debito]) - $rst_doc4[credito];
            if ($tot_ven_m120 != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc4[reg_femision]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc4[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_ven_m120, $dec), 0, 0, 'R');
                $this->Cell(25, 5, (strtotime($rst_doc4[pag_fecha_v]) - strtotime($rst_doc4[reg_femision])) / 86400, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc4[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot5+=$tot_ven_m120;
                $tot_ven_m120 = 0;
            }
        }
        $total = $tot1 + $tot2 + $tot3 + $tot4 + $tot5;
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(35, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(20, 5, number_format($total, $dec), 'T', 0, 'R');
        $this->Cell(25, 5, '', 'T', 0, 'R');
        $this->Cell(25, 5, '', 'T', 0, 'R');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(135, 5, "CORRIENTE", 'T', 0, 'C');
        $this->Ln();
        $this->Cell(35, 5, "FACTURA", 'TB', 0, 'C');
        $this->Cell(30, 5, "FECHA", 'TB', 0, 'C');
        $this->Cell(20, 5, "VALOR", 'TB', 0, 'C');
        $this->Cell(25, 5, "PLAZO", 'TB', 0, 'C');
        $this->Cell(25, 5, "VENCIMIENTO", 'TB', 0, 'C');
        $this->Ln();
        while ($rst_doc5 = pg_fetch_array($por_vencer)) {
            $n++;
            $tot_por_vencert = ($rst_doc5[pag_cant] + $rst_doc5[debito]) - $rst_doc5[credito];
            $d_plazo = (strtotime($rst_doc5[pag_fecha_v]) - strtotime($rst_doc5[reg_femision])) / 86400;
            $d_plazo = abs($d_plazo);
            $d_plazo = floor($d_plazo);
            if ($d_plazo > 120) {
                $d_plazo = 'mas de 120';
            }
            if ($tot_por_vencert != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(35, 5, utf8_decode($rst_doc5[reg_num_documento]), 0, 0, 'C');
                $this->Cell(30, 5, $rst_doc5[reg_femision], 0, 0, 'C');
                $this->Cell(20, 5, number_format($tot_por_vencert, $dec), 0, 0, 'R');
                $this->Cell(25, 5, $d_plazo, 0, 0, 'C');
                $this->Cell(25, 5, $rst_doc5[pag_fecha_v], 0, 0, 'C');
                $this->Ln();
                $tot6+=$tot_por_vencert;
                $tot_por_vencert = 0;
            }
        }
        $totalc = $tot6;
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(35, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(20, 5, number_format($totalc, $dec), 'T', 0, 'R');
        $this->Cell(25, 5, '', 'T', 0, 'R');
        $this->Cell(25, 5, '', 'T', 0, 'R');
        $this->Ln();
        $this->Ln();
        $fec_ant_30t = date("Y-m-d", strtotime("$fec_act -$dias day")); // vencido 30
        $vencido_30t = pg_fetch_array($Set->lista_pagos_vencidost($id, $fec_ant_30t, $fec_act));
        $tot_ven_30t = ($vencido_30t[cantidad] + $vencido_30t[debito]) - $vencido_30t[credito];
        $fec_ant_60t = date("Y-m-d", strtotime("$fec_ant_30t -$dias day")); // vencido 60
        $vencido_60t = pg_fetch_array($Set->lista_pagos_vencidost($id, $fec_ant_60t, $fec_ant_30t));
        $tot_ven_60t = ($vencido_60t[cantidad] + $vencido_60t[debito]) - $vencido_60t[credito];
        $fec_ant_90t = date("Y-m-d", strtotime("$fec_ant_60t -$dias day")); // vencido 90
        $vencido_90t = pg_fetch_array($Set->lista_pagos_vencidost($id, $fec_ant_90t, $fec_ant_60t));
        $tot_ven_90t = ($vencido_90t[cantidad] + $vencido_90t[debito]) - $vencido_90t[credito];
        $fec_ant_120t = date("Y-m-d", strtotime("$fec_ant_90t -$dias day")); // vencido 120
        $vencido_120t = pg_fetch_array($Set->lista_pagos_vencidost($id, $fec_ant_120t, $fec_ant_90t));
        $tot_ven_120t = ($vencido_120t[cantidad] + $vencido_120t[debito]) - $vencido_120t[credito];
        $vencido_m120t = pg_fetch_array($Set->lista_pagos_vencidos_m120t($id, $fec_ant_120t));
        $tot_ven_m120t = ($vencido_m120t[cantidad] + $vencido_m120t[debito]) - $vencido_m120t[credito];
        $por_vencert = pg_fetch_array($Set->lista_pag_porvencert($id, $fec_act));
        $tot_por_vencert = ($por_vencert[cantidad] + $por_vencert[debito]) - $por_vencert[credito];

        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'CORRIENTE', 'LT', 0, 'L');
        $this->Cell(25, 5, number_format($tot_por_vencert, $dec), 'TR', 0, 'R');
        $this->Ln();
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'VENCIDO 1 A 30 DIAS', 'L', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_30t, $dec), 'R', 0, 'R');
        $this->Ln();
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'VENCIDO 31 A 60 DIAS', 'L', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_60t, $dec), 'R', 0, 'R');
        $this->Ln();
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'VENCIDO 61 A 90 DIAS', 'L', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_90t, $dec), 'R', 0, 'R');
        $this->Ln();
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'VENCIDO 91 A 120 DIAS', 'L', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_120t, $dec), 'R', 0, 'R');
        $this->Ln();
        $this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, 'MAS DE 120 DIAS', 'LB', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_m120t, $dec), 'BR', 0, 'R');
       	$this->Ln();     
   	$this->Cell(80, 5, '', '', 0, 'R');
        $this->Cell(30, 5, ' TOTAL', 'BL', 0, 'L');
        $this->Cell(25, 5, number_format($tot_ven_30t+$tot_ven_60t+ $tot_ven_90t+$tot_ven_120t+$tot_por_vencert+$tot_ven_m120t,  $dec), 'BR', 0, 'R');
 
        ////////////////////////////////////////////////////////////////////////CHEQUES
//        $this->Ln();
//        $this->Ln();
//        $this->SetFont('helvetica', 'B', 6);
//        $this->Cell(168, 5, 'CHEQUES', 'LTR', 0, 'C');
//        $this->Ln();
//        $this->Cell(37, 5, 'CONCEPTO', 'LTRB', 0, 'C');
//        $this->Cell(26, 5, 'NOMBRE DEL CHEQUE', 'TRB', 0, 'C');
//        $this->Cell(18, 5, 'BANCO', 'TRB', 0, 'C');
//        $this->Cell(16, 5, '# CHEQUE', 'TRB', 0, 'C');
//        $this->Cell(21, 5, 'FECHA RECEPCION', 'TRB', 0, 'C');
//        $this->Cell(18, 5, 'FECHA CHEQUE', 'TRB', 0, 'C');
//        $this->Cell(16, 5, 'VALOR', 'TRB', 0, 'C');
//        $this->Cell(16, 5, 'SALDO', 'TRB', 0, 'C');
//        $this->Ln();
//        $cns_chq = $Set->lista_cheques_cliente($id);
//        $c = 0;
//        while ($rst_chq = pg_fetch_array($cns_chq)) {
//            $saldo = $rst_chq[chq_monto] - $rst_chq[chq_cobro];
//            if ($saldo != 0) {
//                $c++;
//                $this->Cell(37, 5, $rst_chq[chq_concepto], 'LTRB', 0, 'L');
//                $this->Cell(26, 5, $rst_chq[chq_nombre], 'TRB', 0, 'C');
//                $this->Cell(18, 5, $rst_chq[chq_banco], 'TRB', 0, 'C');
//                $this->Cell(16, 5, $rst_chq[chq_numero], 'TRB', 0, 'C');
//                $this->Cell(21, 5, $rst_chq[chq_recepcion], 'TRB', 0, 'C');
//                $this->Cell(18, 5, $rst_chq[chq_fecha], 'TRB', 0, 'C');
//                $this->Cell(16, 5, number_format($rst_chq[chq_monto], 2), 'TRB', 0, 'R');
//                $this->Cell(16, 5, number_format($saldo, 2), 'TRB', 0, 'R');
//                $this->Ln();
//                $tm = $rst_chq[chq_monto];
//                $t_mont+= $tm;
//                $ts+= $saldo;
//            }
//        }
//        $totalm = $t_mont;
//        $totals = $ts;
//        $this->SetFont('helvetica', 'B', 6);
//        $this->Cell(118, 5, '', '', 0, 'R');
//        $this->Cell(18, 5, 'TOTAL ', 'RBL', 0, 'R');
//        $this->Cell(16, 5, number_format($totalm, 2), 'RBL', 0, 'R');
//        $this->Cell(16, 5, number_format($totals, 2), 'RBL', 0, 'R');
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec_act, $cliente);
$pdf->encabezado_tab();
$pdf->doc($dec, $id);
$pdf->Ln();
$pdf->Output();



