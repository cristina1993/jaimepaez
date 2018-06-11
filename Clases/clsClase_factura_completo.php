<?php

include_once 'Conn.php';

class Clase_factura_completo {

    var $con;

    function Clase_factura_completo() {
        $this->con = new Conn;
    }
    
    function lista_factura_completo_noaut() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura WHERE  fac_estado_aut is null or length(fac_estado_aut) = 0 ORDER BY fac_numero");
        }
    }
    
    function lista_una_factura_aut($sec, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_factura WHERE fac_numero='$sec' and emi_id=$cod");
        }
    }
    
     function lista_factura_por_fechas($from, $until, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_factura where fac_fecha_emision >='$from' and fac_fecha_emision <='$until'  and emi_id=$cod ORDER BY fac_fecha_emision");
        }
    }

}
