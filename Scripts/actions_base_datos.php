<?php

include_once '../Clases/clsClase_base_datos.php';
include_once("../Clases/clsAuditoria.php");
$Set = new Clase_base_datos();
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
$s = $_REQUEST[s];
$fec = $_REQUEST[fec];
$fields = $_REQUEST[fields];
$num = $_REQUEST[num];
switch ($op) {
    case 0:
        if ($Set->delete_det_guia() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_guia() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_nota_credito() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_nota_credito() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_nota_debito() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_nota_debito() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_retencion() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_retencion() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_factura() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_pagos_factura() == false) {
                $sms = pg_last_error();
            } else {
                if ($Set->delete_factura() == false) {
                    $sms = pg_last_error();
                }
            }
        }

        if ($Set->delete_transportista() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->insert_transportita() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_productos() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->insert_productos() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_clientes() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->insert_cliente() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_vendedor() == false) {
            $sms = pg_last_error();
        }

        if ($Set->delete_emisor() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->update_emsior() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_asi_users() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_users() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_credenciales() == false) {
            $sms = pg_last_error();
        }

        if ($Set->delete_auditoria() == false) {
            $sms = pg_last_error();
        }

        $modulo = 'BASE DE DATOS';
        $accion = 'LIMPIAR';
        if ($Adt->insert_audit_general($modulo, $accion, '', '') == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }

        echo $sms;
        break;

    case 1:
        if ($Set->delete_det_guia() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_guia() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_nota_credito() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_nota_credito() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_nota_debito() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_nota_debito() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_retencion() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_retencion() == false) {
                $sms = pg_last_error();
            }
        }

        if ($Set->delete_det_factura() == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->delete_pagos_factura() == false) {
                $sms = pg_last_error();
            } else {
                if ($Set->delete_factura() == false) {
                    $sms = pg_last_error();
                }
            }
        }
        
        $modulo = 'DOCUMENTOS ELECTRONICOS';
        $accion = 'LIMPIAR';
        if ($Adt->insert_audit_general($modulo, $accion, '', '') == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }

        echo $sms;
        break;
}
?>
