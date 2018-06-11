<?php

include_once '../Clases/clsClase_ord_pedido_venta.php';
require_once 'fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_ord_pedido_venta();
$id = $_GET[id];
$rst1 = pg_fetch_array($Set->lista_un_registro($id));
$cns = $Set->lista_un_detalle_pedido_venta($id);
$emisor = pg_fetch_array($Set->lista_emisor($rst1[ped_local]));
$rst_dec = pg_fetch_array($Set->lista_configuraciones('2'));
$rst_dc = pg_fetch_array($Set->lista_configuraciones('1'));
$dec = $rst_dec[con_valor];
$dc = $rst_dec[con_valor];

class PDF extends FPDF {

    function factura($rst1, $cns, $emisor, $dec, $dc) {

        // ///////////////////////////////// ENCABEZADO IZQUIERDO ///////////////////////////////////////////////////////
        $round = $dec;
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(200, 5, utf8_decode($emisor[emi_nombre]), '', 0, 'C');
        $this->Ln();
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(200, 5, utf8_decode($emisor[emi_dir_establecimiento_matriz]), '', 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, utf8_decode($emisor[emi_ciudad]) . ' - ' . utf8_decode($emisor[emi_pais]), '', 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "TELF.:$emisor[emi_telefono]", '', 0, 'C');
        $this->Ln();
        $this->Cell(200, 5, "R.U.C.: " . $emisor[emi_identificacion], '', 0, 'C');
        $this->Ln();
        $this->Cell(190, 5, "Proforma No.: " . $rst1[ped_num_registro], '', 0, 'R');
        $this->Ln();

        //////////////////////////////////// ENCABEZADO CENTRAL ////////////////////////////////////////////////////////        
        $this->Ln($x + 4, $y + 1);
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(125, 5, "Cliente: " . utf8_decode(strtoupper($rst1[ped_nom_cliente])), 'LT', 0, 'L');
        $this->Cell(75, 5, "Fecha: " . $rst1[ped_femision], 'TR', 0, 'L');
        $this->Ln();
        $this->Cell(125, 5, "Direccion : " . utf8_decode(strtoupper($rst1[ped_dir_cliente])), 'L', 'L');
        $this->Cell(75, 5, "Vendedor : " . utf8_decode(strtoupper($rst1[ped_vendedor])), 'R', 'L');
        $this->Ln();
        $this->Cell(200, 5, "R.U.C.: " . utf8_decode(strtoupper($rst1[ped_ruc_cc_cliente])), 'LR', 'L');
        $this->Ln();
        $this->Cell(200, 5, "Telefono: " . $rst1[ped_tel_cliente], 'LRB', 'L');
        $this->Ln();

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////// CUERPO ////////////////////////////////////////////////////////                        
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(40, 5, 'CODIGO', 'LRT', 0, 'C');
        $this->Cell(70, 5, "DESCRIPCION", 'LT', 0, 'C');
        $this->Cell(20, 5, "CANTIDAD", 'LT', 0, 'C');
        $this->Cell(20, 5, "VALOR", 'LT', 0, 'C');
        $this->Cell(15, 5, "%", 'LT', 0, 'C');
        $this->Cell(15, 5, "%", 'LT', 0, 'C');
        $this->Cell(20, 5, "VALOR", 'LTR', 0, 'C');
        $this->Ln();
        $this->Cell(40, 5, "", 'LRB', 0, 'C');
        $this->Cell(70, 5, "", 'LRB', 0, 'C');
        $this->Cell(20, 5, "", 'LRB', 0, 'C');
        $this->Cell(20, 5, "UNITARIO", 'LRB', 0, 'C');
        $this->Cell(15, 5, "DES", 'LRB', 0, 'C');
        $this->Cell(15, 5, "IVA", 'LRB', 0, 'C');
        $this->Cell(20, 5, "TOTAL", 'LBR', 0, 'C');
        $this->Ln();

        //___________________________________________________________________________
        $n = 0;
        while ($rst = pg_fetch_array($cns)) {
            $n++;
            $this->SetFont('helvetica', '', 7);
            $this->Cell(40, 4, $rst[det_cod_producto], 'LR', 0, 'C');
            $this->Cell(70, 4, utf8_decode(substr($rst[det_descripcion], 0, 40)), 'LR', 0, 'L');
            $this->Cell(20, 4, number_format($rst[det_cantidad], $dc), 'LR', 0, 'C');
            $this->Cell(20, 4, number_format($rst[det_vunit], $round), 'LR', 0, 'R');
            $this->Cell(15, 4, number_format($rst[det_descuento_porcentaje], $round), 'LR', 0, 'R');
            $this->Cell(15, 4, number_format($rst[det_impuesto], $round), 'LR', 0, 'R');
            $this->Cell(20, 4, number_format($rst[det_total], $round), 'LR', 0, 'R');
            $this->Ln();
        }
        if ($n < 10) {
            $a = 10 - $n;
            $j=0;
            while ($j < $a) {
                $this->Cell(40, 4, '', 'LR', 0, 'C');
                $this->Cell(70, 4, '', 'LR', 0, 'L');
                $this->Cell(20, 4, '', 'LR', 0, 'C');
                $this->Cell(20, 4, '', 'LR', 0, 'R');
                $this->Cell(15, 4, '', 'LR', 0, 'R');
                $this->Cell(15, 4, '', 'LR', 0, 'R');
                $this->Cell(20, 4, '', 'LR', 0, 'R');
                $this->Ln();
                $j++;
            }
        }
        //___________________________________________________________________________
        $this->SetFont('helvetica', '', 8);
        $this->Cell(40, 5, "ELABORADO", 'LRTB', 0, 'L');
        $this->Cell(50, 5, "VTO. BUENO", 'LRTB', 0, 'L');
        $this->Cell(50, 5, "CLIENTE", 'LRTB', 0, 'L');
        $this->Cell(40, 5, "DESCTO", 'LRTB', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[ped_tdescuento], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(40, 5, "", 'LR', 0, 'LB');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(40, 5, "TARIFA 0%", 'LRBT', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[ped_sbt0] + $rst1[ped_sbt_excento] + $rst1[ped_sbt_noiva], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(40, 5, utf8_decode(substr($rst1[ped_vendedor], 0, 30)), 'LR', 0, 'L');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(40, 5, "TARIFA 12%", 'LRBT', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[ped_sbt12], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(40, 5, "", 'LR', 0, 'LB');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(50, 5, "", 'LR', 0, 'L');
        $this->Cell(40, 5, "I.V.A. 12%", 'LRBT', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[ped_iva12], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->Cell(40, 5, "", 'LRB', 0, 'LB');
        $this->Cell(50, 5, "", 'LRB', 0, 'L');
        $this->Cell(50, 5, "", 'LRB', 0, 'L');
        $this->Cell(40, 5, "TOTAL", 'LRBT', 0, 'L');
        $this->Cell(20, 5, number_format($rst1[ped_total], $round), 'LRTB', 0, 'R');
        $this->Ln();
        $this->MultiCell(140, 5, "Observaciones :  " . strtoupper($rst1[ped_observacion]), 'LRBT', 1);
    }

}

$pdf = new PDF($orientation = 'P', $unit = 'mm', $size = 'A4');
$pdf->AddPage();
$pdf->factura($rst1, $cns, $emisor, $dec, $dc);
$pdf->SetDisplayMode(75);
$pdf->Output();




