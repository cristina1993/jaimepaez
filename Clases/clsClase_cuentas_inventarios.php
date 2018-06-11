<?php

include_once 'Conn.php';

class Clase_cuentas_inventarios {

    var $con;

    function Clase_cuentas_inventarios() {
        $this->con = new Conn();
    }

    function lista_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("Select  * from erp_transacciones t,erp_cuentas_inventarios c, erp_emisor e where t.trs_id=c.trs_id and e.emi_id=c.emi_id  
                             order by c.emi_id,trs_operacion,trs_descripcion");
        }
    }

    function lista_plan_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_estado='0'ORDER BY pln_codigo ");
        }
    }

    function lista_plan_cuentas_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id=$id");
        }
    }

    function update_cuentas($id, $d, $h) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_cuentas_inventarios SET cin_debe=$d,cin_haber=$h where cin_id=$id");
        }
    }

}

?>
