<?php

include_once '../Clases/clsClase_cierres_caja_masivo.php';
include_once("../Clases/clsAuditoria.php");
$Clase_cierres_caja_masivo = new Clase_cierres_caja_masivo();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$fecha = $_REQUEST[fec];
$user = $_REQUEST[user];
$bodega = $_REQUEST[bod];
switch ($op) {
    case 0:
        $sms = 0;
        $cns1 = $Clase_cierres_caja_masivo->lista_facturas_vendedor($fecha);
        if (!$Clase_cierres_caja_masivo->delete_cierre($fecha)) {
            $sms = pg_last_error();
        } else {
            while ($rst = pg_fetch_array($cns1)) {
                $rst_sec = pg_fetch_array($Clase_cierres_caja_masivo->lista_ultimo_secuencial($rst[emi_id]));
                if ($rst[emi_id] >= 10) {
                    $ems = $rst[emi_id];
                } else {
                    $ems = '0' . $rst[emi_id];
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
                $sec1 = $ems . $txt . $sec;

                if (!$Clase_cierres_caja_masivo->delete_asiento($sec1) == FALSE) {
                    $sms = pg_last_error();
                }

                $rst_nfct = pg_fetch_array($Clase_cierres_caja_masivo->lista_fechaemi_factura($fecha, $rst[emi_id], $rst[vendedor]));
                $rst_npf = pg_fetch_array($Clase_cierres_caja_masivo->lista_cantidad_productos($fecha, $rst[emi_id], $rst[vendedor]));
                $rst_sbt = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_subtotal($fecha, $rst[emi_id], $rst[vendedor]));
                $rst_tnc = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_notacredito($fecha, $rst[emi_id], $rst[vendedor]));
                $rst_fp = pg_fetch_array($Clase_cierres_caja_masivo->lista_formas_pago($fecha, $rst[emi_id], $rst[vendedor]));
                $suma_subt = $rst_sbt[suma_subtotal] + $rst_sbt[suma_descuento];

                $data = array($sec1,
                    $fecha,
                    date('H:i'),
                    $rst[vendedor],
                    $rst[emi_id],
                    $rst_nfct[nfac],
                    str_replace(',', '', $rst_npf[suma_cantidad]),
                    str_replace(',', '', number_format($suma_subt, 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_descuento], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_iva], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_total_valor], 4)),
                    str_replace(',', '', number_format($rst_tnc[suma_total_valor_nc], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_credito], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_debito], 4)),
                    str_replace(',', '', number_format($rst_fp[cheque], 4)),
                    str_replace(',', '', number_format($rst_fp[efectivo], 4)),
                    str_replace(',', '', number_format($rst_fp[certificados], 4)),
                    str_replace(',', '', number_format($rst_fp[bonos], 4)),
                    str_replace(',', '', number_format($rst_fp[retencion], 4)),
                    str_replace(',', '', number_format($rst_fp[nota_credito], 4)),
                    str_replace(',', '', number_format($rst_fp[credito], 4))
                );

                if ($Clase_cierres_caja_masivo->insert_cierre($data) == false) {
                    $sms = pg_last_error();
                } else {
                    $modulo = 'CIERRE DE CAJA MASIVO';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $sec1) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        $cns = $Clase_cierres_caja_masivo->lista_facturas_vendedor_bodega($fecha, $bodega);
        if (!$Clase_cierres_caja_masivo->delete_cierre_bodega($fecha, $bodega)) {
            $sms = pg_last_error();
        } else {
            while ($rst = pg_fetch_array($cns)) {

                $rst_sec = pg_fetch_array($Clase_cierres_caja_masivo->lista_ultimo_secuencial($bodega));
                if ($rst[emi_id] >= 10) {
                    $ems = $rst[emi_id];
                } else {
                    $ems = '0' . $rst[emi_id];
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
                $sec1 = $ems . $txt . $sec;
                if (!$Clase_cierres_caja_masivo->delete_asiento($sec1) == FALSE) {
                    $sms = pg_last_error();
                }

                $rst_nfct = pg_fetch_array($Clase_cierres_caja_masivo->lista_fechaemi_factura_bodega($fecha, $bodega, $rst[vendedor]));
                $rst_npf = pg_fetch_array($Clase_cierres_caja_masivo->lista_cantidad_productos_bodega($fecha, $bodega, $rst[vendedor]));
                $rst_sbt = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_subtotal_bodega($fecha, $bodega, $rst[vendedor]));
                $rst_tnc = pg_fetch_array($Clase_cierres_caja_masivo->lista_total_notacredito($fecha, $bodega, $rst[vendedor]));
                $rst_fp = pg_fetch_array($Clase_cierres_caja_masivo->lista_formas_pago_bodega($fecha, $bodega, $rst[vendedor]));
                $suma_subt = $rst_sbt[suma_subtotal] + $rst_sbt[suma_descuento];

                $data = array($sec1,
                    $fecha,
                    date('H:i'),
                    $rst[vendedor],
                    $rst[emi_id],
                    $rst_nfct[nfac],
                    str_replace(',', '', $rst_npf[suma_cantidad]),
                    str_replace(',', '', number_format($suma_subt, 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_descuento], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_iva], 4)),
                    str_replace(',', '', number_format($rst_sbt[suma_total_valor], 4)),
                    str_replace(',', '', number_format($rst_tnc[suma_total_valor_nc], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_credito], 4)),
                    str_replace(',', '', number_format($rst_fp[tarjeta_debito], 4)),
                    str_replace(',', '', number_format($rst_fp[cheque], 4)),
                    str_replace(',', '', number_format($rst_fp[efectivo], 4)),
                    str_replace(',', '', number_format($rst_fp[certificados], 4)),
                    str_replace(',', '', number_format($rst_fp[bonos], 4)),
                    str_replace(',', '', number_format($rst_fp[retencion], 4)),
                    str_replace(',', '', number_format($rst_fp[nota_credito], 4)),
                    str_replace(',', '', number_format($rst_fp[credito], 4))
                );
                if ($Clase_cierres_caja_masivo->insert_cierre($data) == false) {
                    $sms = 'cierre' . pg_last_error();
                } else {
                    $modulo = 'CIERRE DE CAJA';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, '', $sec1) == false) {
                        $sms = "Auditoria" . pg_last_error();
                    }
                }
            }
        }
        echo $sms;
        break;
}
?>
