<?php

include_once 'Conn.php';

class Clase_criterios_permiso {

    var $con;

    function Clase_criterios_permiso() {
        $this->con = new Conn();
    }

    function lista_criterios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_criterios_permiso order by crp_descripcion");
        }
    }

    function lista_buscardor_criterios($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_criterios_permiso $txt order by crp_descripcion");
        }
    }

function lista_un_criterio($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_criterios_permiso where crp_id='$id'");
        }
    }

    function insert_criterio($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_criterios_permiso(
                        crp_codigo,
                        crp_descripcion
                       )
            VALUES ('$data[0]','$data[1]')");
        }
    }

    function upd_criterio($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_criterios_permiso SET crp_codigo='$data[0]',crp_descripcion='$data[1]' WHERE crp_id='$id'");
        }
    }

    function delete_criterio($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_criterios_permiso WHERE crp_id= '$id'"
            );
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
