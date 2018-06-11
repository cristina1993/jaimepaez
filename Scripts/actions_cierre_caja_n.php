<?php

include_once '../Clases/clsClase_cierre_caja.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cierre_caja = new Clase_cierre_caja();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data2 = $_REQUEST[data2];
$user = strtoupper($_REQUEST[user]);
$emisor = $_REQUEST[emi];
$fec = $_REQUEST[fec];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $sms = 0;
        $usu = pg_fetch_array($Clase_cierre_caja->lista_vendedores($user));
        $user = $usu[vnd_id];
        $cns = pg_fetch_array($Clase_cierre_caja->lista_num_facturas($fec, $user, $emisor));
        if (!$Clase_cierre_caja->delete_cierre_bodega($fec, $user, $emisor)) {
            $sms = pg_last_error();
        } else {
            $rst_cierre = pg_fetch_array($Clase_cierre_caja->lista_un_cierre_punto_fecha($emisor, $fec, $user));
            $rst_nfct = pg_fetch_array($Clase_cierre_caja->lista_fechaemi_factura($fec, $emisor, $user));
            $rst_npf = pg_fetch_array($Clase_cierre_caja->lista_cantidad_productos($fec, $emisor, $user));
            $rst_sbt = pg_fetch_array($Clase_cierre_caja->lista_total_subtotal($fec, $emisor, $user));
            $rst_tnc = pg_fetch_array($Clase_cierre_caja->lista_total_notacredito($fec, $emisor, $user));
            $rst_fp = pg_fetch_array($Clase_cierre_caja->lista_formas_pago($fec, $emisor, $user));
            $suma_subt = $rst_sbt[suma_subtotal] + $rst_sbt[suma_descuento];

            $rst_sec = pg_fetch_array($Clase_cierre_caja->lista_ultimo_secuencial($emisor));
            if ($emisor >= 10) {
                $ems = $emisor;
            } else {
                $ems = '0' . $emisor;
            }
            $sec = (substr($rst_sec[cie_secuencial], -4) + 1);
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

            if ($Clase_cierre_caja->delete_asiento($secuencial) == FALSE) {
                $sms = pg_last_error();
            }

            $fac_emitidas = $rst_nfct[nfac];
            $suma_productos = $rst_npf[suma_cantidad];
            $suma_total_notcre += $rst_tnc[suma_total_valor_nc];
            $subtotal = $suma_subt;
            $descuento = $rst_sbt[suma_descuento];
            $iva = $rst_sbt[suma_iva];
            $suma_total_valor = $rst_sbt[suma_total_valor];
            $suma_tarjeta_credito = $rst_fp[tarjeta_credito];
            $suma_tarjeta_debito = $rst_fp[tarjeta_debito];
            $suma_cheque = $rst_fp[cheque];
            $suma_efectivo = $rst_fp[efectivo];
            $suma_certificados = $rst_fp[certificados];
            $suma_bonos = $rst_fp[bonos];
            $suma_retencion = $rst_fp[retencion];
            $suma_not_cre = $rst_fp[nota_credito];
            $suma_cre = $rst_fp[credito];


            $dat = array(
                $secuencial,
                $fec,
                date('H:i:s'),
                $user,
                $emisor,
                $fac_emitidas,
                $suma_productos,
                str_replace(',', '', number_format($subtotal, 4)),
                str_replace(',', '', number_format($descuento, 4)),
                str_replace(',', '', number_format($iva, 4)),
                str_replace(',', '', number_format($suma_total_valor, 4)),
                str_replace(',', '', number_format($suma_total_notcre, 4)),
                str_replace(',', '', number_format($suma_tarjeta_credito, 4)),
                str_replace(',', '', number_format($suma_tarjeta_debito, 4)),
                str_replace(',', '', number_format($suma_cheque, 4)),
                str_replace(',', '', number_format($suma_efectivo, 4)),
                str_replace(',', '', number_format($suma_certificados, 4)),
                str_replace(',', '', number_format($suma_bonos, 4)),
                str_replace(',', '', number_format($suma_retencion, 4)),
                str_replace(',', '', number_format($suma_not_cre, 4)),
                str_replace(',', '', number_format($suma_cre, 4)),
            );
            if (empty($cns)) {
                $sms = 1;
            } else {
                if ($Clase_cierre_caja->insert_cierre($dat) == false) {
                    $sms = pg_last_error();
                    $d = 1;
                }
            }

            if ($d == 0) {
                $modulo = 'CIERRE DE CAJA LOCALES';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, '', $secuencial) == false) {
                    $sms = "Auditoria" . pg_last_error();
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($Clase_cierre_caja->upd_totales_cierres($data, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 2:
        $sms = 0;
        if (empty($id)) {
            $n = 0;
            while ($n < count($data2)) {
                $ncr .=$data2[$n] . '&';
                $n++;
            }
            if ($Clase_cierre_caja->insert_arqueo_caja($data, $ncr) == false) {
                $sms = 'Insert_arq_caja' . pg_last_error();
            }
        }
        if ($sms == 0) {
            $n = 0;
            while ($n < count($fields)) {
                $f = $f . strtoupper($fields[$n] . '&');
                $n++;
            }
            $modulo = 'ARQUEO DE CAJA';
            $accion = 'INSERTAR';
            if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;

    case 3:
        $sms = 0;
        $cns = pg_fetch_array($Clase_cierre_caja->lista_arqueo_caja($fec, $emisor));
        if (empty($cns)) {
            $sms = 0;
        } else {
            $sms = 1;
        }
        $rst_cie = pg_fetch_array($Clase_cierre_caja->lista_cierra_fecha_emision($fec, $emisor));
        if (empty($rst_cie)) {
            $sms = 2;
        }
        echo $sms;
        break;
}
?>
