<?php

include_once 'Conn.php';

class Clase_gerencia {

    var $con;

    function Clase_gerencia() {
        $this->con = new Conn();
    }

    function lista_gerencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia order by ger_descripcion");
        }
    }

    function lista_buscardor_gerencial($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia $txt order by ger_descripcion");
        }
    }

    function lista_una_gerencia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia where ger_id='$id'");
        }
    }

    function insert_gerencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_gerencia(
                        ger_codigo,
                        ger_descripcion
                       )
            VALUES ('$data[0]','$data[1]')");
        }
    }

    function upd_gerencia($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_gerencia SET ger_codigo='$data[0]',ger_descripcion='$data[1]' WHERE ger_id='$id'");
        }
    }

    function delete_gerencia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_gerencia WHERE ger_id= '$id'"
            );
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
