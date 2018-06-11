<?php

include_once '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fields = $_REQUEST[fields];
switch ($op) {
    case 0:
        $rst_cierre = pg_fetch_array($Clase_cierre_caja->lista_un_cierre_punto_fecha($emisor, date('Y-m-d')));
        $sms = 0;
        $n = 0;
        $rst1 = pg_fetch_array($Clase_cierre_caja->lista_fechaemi_factura(date('Ymd'), $emisor));
        $rst2 = pg_fetch_array($Clase_cierre_caja->lista_cantidad_productos(date('Ymd'), $emisor));
        $rst3 = pg_fetch_array($Clase_cierre_caja->lista_total_subtotal(date('Ymd'), $emisor));
        $rst4 = pg_fetch_array($Clase_cierre_caja->lista_formas_pago(date('Ymd'), $emisor));
        if ($emisor == 10) {
            $ems = '10';
        } else {
            $ems = '0' . $emisor;
        }

        $sec = (substr($rst[cie_secuencial], -4) + 1);
        if ($sec >= 0 && $sec < 10) {
            $txt = '000';
        } else if ($sec >= 10 && $sec < 100) {
            $txt = '00';
        } else if ($sec >= 100 && $sec < 1000) {
            $txt = '0';
        } else if ($sec >= 1000 && $sec < 10000) {
            $txt = '';
        }
        $secuencial = $ems . $txt . $sec;

        $rst[vendedor] = $rst_user[usu_person];
        $fac_emitidas = $rst1[facturas_emitidas];
        $suma_productos = $rst2[suma_cantidad];
        $suma_total_notcre = $rst2[suma_nota_credito];
        $subtotal = $rst3[suma_subtotal];
        $descuento = $rst3[suma_descuento];
        $iva = $rst3[suma_iva];
        $suma_total_valor = $rst3[suma_total_valor];
        $suma_tarjeta_credito = $rst4[tarjeta_credito];
        $suma_tarjeta_debito = $rst4[tarjeta_debito];
        $suma_cheque = $rst4[cheque];
        $suma_efectivo = $rst4[efectivo];
        $suma_certificados = $rst4[certificados];
        $suma_bonos = $rst4[bonos];
        $suma_retencion = $rst4[retencion];


        $dat = array(
            $secuencial,
            date('Y-m-d'),
            date('H:i:s'),
            $rst[vendedor],
            $emisor,
            $fac_emitidas,
            $suma_productos,
            $subtotal,
            $descuento,
            $iva,
            $suma_total_valor,
            $suma_total_notcre,
            str_replace(',', '', number_format($suma_tarjeta_credito, 4)),
            str_replace(',', '', number_format($suma_tarjeta_debito, 4)),
            str_replace(',', '', number_format($suma_cheque, 4)),
            str_replace(',', '', number_format($suma_efectivo, 4)),
            str_replace(',', '', number_format($suma_certificados, 4)),
            str_replace(',', '', number_format($suma_bonos, 4)),
            str_replace(',', '', number_format($suma_retencion, 4))
        );

        if (round($subtotal) == 0) {
            $sms=1;
        } else {
            if (empty($rst_cierre)) {
                if ($Clase_cierre_caja->insert_cierre($dat) == false) {
                    $sms = pg_last_error();
                }
            } else {
                if ($Clase_cierre_caja->upd_cierre_caja($dat, $rst_cierre[cie_id]) == false) {
                    $sms = pg_last_error();
                }
            }
        }



        echo '&' . $sms;
        break;
}
?>
