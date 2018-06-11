<?php

include_once 'Conn.php';

class reporte_movimientos {

    var $con;

    function reporte_movimientos() {
        $this->con = new Conn();
    }

    function lista_cuentas_byc() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT byc_cuenta_contable FROM erp_bancos_y_cajas ");
        }
    }

    function lista_un_cyb($cta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_bancos_y_cajas WHERE byc_cuenta_contable='$cta'");
        }
    }

    function lista_cuentas_detalle_byc($d, $h, $cta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 'Debe' as tipo, ac.* FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$d' AND '$h' and ac.con_concepto_debe='$cta'
                             union
                             SELECT 'Haber' as tipo, ac.* FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$d' AND '$h' and ac.con_concepto_haber='$cta'order by con_fecha_emision");
        }
    }

    function lista_cuentas_byc_tipo($tip) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT byc_cuenta_contable, byc_tipo FROM erp_bancos_y_cajas WHERE  byc_tipo='$tip'");
        }
    }

    function lista_cuentas_byc_refe($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT byc_cuenta_contable, byc_referencia FROM erp_bancos_y_cajas WHERE byc_referencia='$txt'");
        }
    }

}

?>
