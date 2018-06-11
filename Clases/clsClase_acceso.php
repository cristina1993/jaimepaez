<?php

include_once 'Conn.php';

class Clase_acceso {

    var $con;

    function Clase_acceso() {
        $this->con = new Conn();
    }

    function insert_acceso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_sol_acceso(
                acc_nombre,
                acc_empresa,
                acc_email,
                acc_comentarios
            )
    VALUES ('$data[0]','$data[1]','$data[2]','$data[3]')");
        }
    }

}

?>
