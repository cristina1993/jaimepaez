<?php

include_once 'Conn.php';

class Clase_subseccion{

    var $con;

    function Clase_subseccion() {
        $this->con = new Conn();
    }

    function lista_subseccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_subseccion order by sbs_descripcion");
        }
    }

    function lista_buscardor_subseccion($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_subseccion $txt order by sbs_descripcion");
        }
    }

    function lista_una_subseccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_subseccion where sbs_id='$id'");
        }
    }

    function insert_subseccion($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_subseccion(
                        sbs_codigo,
                        sbs_descripcion
                       )
            VALUES ('$data[0]','$data[1]')");
        }
    }

    function upd_subseccion($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_subseccion SET sbs_codigo='$data[0]',sbs_descripcion='$data[1]' WHERE sbs_id='$id'");
        }
    }

    function delete_subseccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_subseccion WHERE sbs_id= '$id'"
            );
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
