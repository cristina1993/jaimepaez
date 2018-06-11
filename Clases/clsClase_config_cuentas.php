<?php

include_once 'Conn.php';

class Clase_config_cuentas {

    var $con;

    function Clase_config_cuentas() {
        $this->con = new Conn();
    }

    function lista_cuentas1() {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from erp_ctas_asientos a, erp_plan_cuentas c where a.pln_id=c.pln_id and a.cas_estado=0 ORDER BY a.cas_tipo_doc,a.cas_orden,a.cas_id");
        }
    }
    
    function lista_cuentas($emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * from erp_ctas_asientos where cas_estado=0 and emi_id=$emi ORDER BY cas_tipo_doc,cas_orden,cas_id");
        }
    }

    function lista_plan_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id=$id");
        }
    }

    function update_conf_ctas($id, $pln) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_ctas_asientos SET pln_id=$pln where cas_id=$id");
        }
    }
    
    function lista_bodegas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor ORDER BY emi_cod_punto_emision");
        }
    }

}

?>
