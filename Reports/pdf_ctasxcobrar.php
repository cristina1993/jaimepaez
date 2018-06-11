<?php

include_once '../Clases/clsClase_cuentasxcobrar.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new CuentasCobrar();
$fec1 = $_GET[d];
$fec2 = $_GET[h];
$dec = $_GET[dec];

$nm = trim(strtoupper($_GET[txt]));
$estado = $_GET[e];

if (!empty($_GET[txt])) {
    $txt = "WHERE (fac_numero LIKE '%$nm%' or fac_nombre like '%$nm%' or fac_identificacion like '%$nm%') and fac_fecha_emision between '$fec1' and '$fec2' and exists (select * from erp_pagos_factura p where p.com_id=c.fac_id)";
    $est1 = 1;
} else {
    if ($estado == 0) {
        $txt = " WHERE fac_fecha_emision between '$fec1' and '$fec2' and exists (select * from erp_pagos_factura p where p.com_id=c.fac_id)";
    } else if ($estado == 1) {
        $txt = $txt = " WHERE fac_fecha_emision between '$fec1' and '$fec2' and fac_total_valor+(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago='NOTA DE DEBITO')=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO') or fac_total_valor=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO') and exists (select * from erp_pagos_factura p where p.com_id=c.fac_id)";
    } else if ($estado == 2) {
        $cns = $Set->buscar_documentos_vencer(date('Y-m-d'), $fec1, $fec2);
    } else if ($estado == 3) {//Vencidos
        $cns = $Set->buscar_documentos_vencidos(date('Y-m-d'), $fec1, $fec2);
    }
}
if ($estado < 2) {
    $cns = $Set->lista_documentos_buscador($txt);
}
$emisor = pg_fetch_array($Set->lista_emisor_id('1'));

class PDF extends FPDF {

    function encabezado($emisor, $fec1, $fec2) {
//        $this->Image('../img/logo_noperti.jpg', 1, 5, 50);
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(300, 5, "REPORTE CUENTAS POR COBRAR", 0, 0, 'C');
        $this->Ln();
        $this->Cell(300, 5, utf8_decode($emisor[emi_nombre_comercial]), 0, 0, 'C');
        $this->Ln();
        $this->Cell(300, 5, "RUC: " . $emisor[emi_identificacion], 0, 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', '', 10);
        $this->Cell(300, 5, "AL " . $fec2, 0, 0, 'C');
        $this->Ln();
    }

    function encabezado_tab() {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 5, "COD. CLIENTE", 'TB', 0, 'C');
        $this->Cell(65, 5, "CLIENTE", 'TB', 0, 'C');
//        $this->Cell(35, 5, "TIPO", 'TB', 0, 'L');
        $this->Cell(20, 5, "F.EMISION", 'TB', 0, 'L');
        $this->Cell(20, 5, "F.VENC.", 'TB', 0, 'L');
        $this->Cell(30, 5, "DOCUMENTO", 'TB', 0, 'C');
//        $this->Cell(35, 5, "CONCEPTO", 'TB', 0, 'C');
        $this->Cell(27, 5, "DEBITO", 'TB', 0, 'C');
        $this->Cell(27, 5, "CREDITO", 'TB', 0, 'C');
        $this->Cell(27, 5, "SALDO", 'TB', 0, 'C');
//        $this->Ln();
    }

    function doc($com, $id, $debito, $fec, $dec, $tot) {
        $Set = new CuentasCobrar();
        if ($debito != $tot) {
            $n = 0;
            $credito = 0;
            $tip = '';
            $rst_doc = pg_fetch_array($Set->lista_documentos_id($id));
            $rst_cli = pg_fetch_array($Set->lista_cliente_ced($rst_doc[fac_identificacion]));
            $rst_pag = pg_fetch_array($Set->lista_pagos_factu($id));
//        $emi = $rst_doc[fecha_emision];
            $saldo = ($rst_pag[pag_cant] + $rst_pag[debito]) - $rst_pag[credito];
            if ($saldo != 0) {
                $num = pg_num_rows($cns_pag);
                $this->SetFont('Arial', '', 8);
                $this->Cell(20, 5, $rst_cli[cli_codigo], 0, 0, 'L');
                $this->Cell(65, 5, utf8_decode(substr($rst_doc[fac_nombre], 0, 45)), 0, 0, 'L');
//        $this->Cell(35, 5, substr($tip, 0, 20), 0, 0, 'L');
                $this->Cell(20, 5, $rst_doc[fac_fecha_emision], 0, 0, 'L');
                $this->Cell(20, 5, $fec, 0, 0, 'L');
                $this->Cell(30, 5, $rst_doc[fac_numero], 0, 0, 'L');
//        $this->Cell(35, 5, substr('FACTURACION EN VENTAS', 0, 30), 0, 0, 'L');
                $this->Cell(27, 5, number_format($debito, $dec), 0, 0, 'R');
                $this->Cell(27, 5, number_format($rst_pag[credito], $dec), 0, 0, 'R');
                $this->Cell(27, 5, number_format($saldo, $dec), 0, 0, 'R');
                $this->Ln();
            }
        }
    }

    function body($com, $id, $dec, $deb) {
        $Set = new CuentasCobrar();
        $n = 0;
        $credito = 0;
        $debito = 0;
        $cns_pag = $Set->listar_una_cta_comid($id);
        $rst_doc = pg_fetch_array($Set->lista_documentos_id($id));
        $rst_cli = pg_fetch_array($Set->lista_cliente_ced($rst_doc[fac_identificacion]));

//        $emi = $rst_doc[fecha_emision];
        $num = pg_num_rows($cns_pag);

        while ($rst = pg_fetch_array($cns_pag)) {
            $n++;
            $rst_tot = pg_fetch_array($Set->lista_totales_pagos($rst[com_id]));
            if ($deb != $rst_tot[total]) {

                $debito = 0;
                if ($rst[asiento] == 0) {
                    $tip = 'FC.PENCION PRIMARIA';
                } else if ($rst[asiento] == 1) {
                    $tip = 'FP.FACTURAS POS';
                } else if ($rst[asiento] == 2) {
                    $tip = 'CA. CANCELACION FACTURAS';
                } else if ($rst[asiento] == 3) {
                    $tip = 'AB. ABONO';
                } else if ($rst[asiento] == 4) {
                    $tip = 'RC. RECIBO';
                } else if ($rst[asiento] == 5) {
                    $tip = 'ND. NOTA DEBITO';
                } else if ($rst[asiento] == 6) {
                    $tip = 'NC. NOTA CREDITO';
                } else if ($rst[asiento] == 7) {
                    $tip = 'DD. AJUSTE DIF. CAMBIO DB';
                } else if ($rst[asiento] == 8) {
                    $tip = 'Dc. AJUSTE DIF. CAMBIO CR';
                } else if ($rst[asiento] == 9) {
                    $tip = 'RF.RETENCION FUENTE';
                }

                if ($rst[cta_forma_pago] == 'NOTA DE DEBITO') {
                    $debito = $rst[cta_monto];
                    $rst[cta_monto] = 0;
                }
//                if ($debito != 0 && $rst[cta_monto] != 0) {
                $this->SetFont('Arial', '', 8);
                $this->Cell(20, 5, $rst_cli[cli_codigo], 0, 0, 'L');
                $this->Cell(65, 5, utf8_decode(substr($rst_doc[fac_nombre], 0, 45)), 0, 0, 'L');
//                $this->Cell(35, 5, substr($tip, 0, 20), 0, 0, 'L');
                $this->Cell(20, 5, $rst_doc[fac_fecha_emision], 0, 0, 'L');
                $this->Cell(20, 5, $rst[cta_fecha], 0, 0, 'L');
                $this->Cell(30, 5, $rst_doc[fac_numero], 0, 0, 'L');
//                $this->Cell(35, 5, substr($rst[cta_concepto], 0, 30), 0, 0, 'L');
                $this->Cell(27, 5, number_format($debito, $dec), 0, 0, 'R');
                $this->Cell(27, 5, number_format($rst[cta_monto], $dec), 0, 0, 'R');
                $this->Ln();
//                }
            }
        }
    }

    function totales($debito, $credito, $dec, $c) {
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(127, 5, '', '', 0, 'R');
        $this->Cell(35, 5, 'TOTAL ', '', 0, 'R');
        $this->Cell(27, 5, number_format($debito, $dec), 'T', 0, 'R');
        $this->Cell(27, 5, number_format($c, $dec), 'T', 0, 'R');
        $this->Ln();
    }

}

$pdf = new PDF($orientation = 'L', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->encabezado($emisor, $fec1, $fec2);
$pdf->encabezado_tab();
$pdf->Ln();
$grup = '';
while ($rst = pg_fetch_array($cns)) {
    $cns_pag = $Set->lista_pagos($rst[fac_id]);
    while ($rst1 = pg_fetch_array($cns_pag)) {
        $fec = $rst1[pag_fecha_v];
        $num_c = $rst1[com_id];
    }
    $rst_tf = pg_fetch_array($Set->lista_totales_pagos($rst[fac_id]));
    if (!empty($num_c)) {
        $pdf->doc($num_c, $rst[fac_id], $rst[fac_total_valor], $fec, $dec, $rst_tf[total]);
//        $pdf->body($num_c, $rst[fac_id], $dec, $rst[fac_total_valor]);
        $cns_cta = $Set->listar_una_cta_comid($rst[fac_id]);
        while ($rst2 = pg_fetch_array($cns_cta)) {
            if ($rst2[cta_forma_pago] == 'NOTA DE DEBITO') {
                $valors+= $rst2[cta_monto];
                $rst2[cta_monto] = 0;
            }
            $cred+=$rst2[cta_monto];
        }
        $deb+=$rst[fac_total_valor] + $valors;
        $valors = 0;
        $rst_pag = pg_fetch_array($Set->lista_pagos_factu($rst[fac_id]));
        $saldo = ($rst_pag[pag_cant] + $rst_pag[debito]) - $rst_pag[credito];
        if($saldo != 0){
            $cre+=$rst_pag[credito];
            $totd = $rst_pag[pag_cant] + $rst_pag[debito];
            $dtot+=$totd;
        }
    }
    $num_c = '';
    $f = '';
}
$pdf->totales($dtot, $cred, $dec, $cre);

$pdf->Output();



