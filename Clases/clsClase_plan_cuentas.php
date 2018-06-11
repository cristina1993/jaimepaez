<?php

include_once 'Conn.php';

class Clase_plan_cuentas {

    var $con;

    function Clase_plan_cuentas() {
        $this->con = new Conn();
    }

    function lista_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }

    function lista_buscador_cuentas($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas $txt  ORDER BY pln_codigo ");
        }
    }

     function lista_una_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id=$id");
        }
    }
    function insert_cuenta($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_plan_cuentas (pln_codigo, pln_descripcion, pln_obs) values ('$data[0]','$data[1]','$data[2]')");
        }
    }

    function update_cuenta($id, $data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_plan_cuentas SET pln_codigo='$data[0]', pln_descripcion='$data[1]', pln_obs='$data[2]' WHERE pln_id=$id");
        }
    }
    
    function delete_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE  from erp_plan_cuentas WHERE pln_id=$id");
        }
    }

}

?>
