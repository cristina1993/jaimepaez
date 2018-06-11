<?php

include_once 'Conn.php';

class Clase_transportista {

    var $con;

    function Clase_transportista() {
        $this->con = new Conn();
    }

    function lista_transportista() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transportista order by tra_razon_social");
        }
    }

    function lista_buscardor_transportista($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transportista $txt order by tra_razon_social");
        }
    }

    function lista_un_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transportista where tra_id='$id'");
        }
    }

    function insert_transportista($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_transportista(
                        tra_identificacion,
                        tra_razon_social,
                        tra_email,
                        tra_placa,
                        tra_telefono,
                        tra_direccion)
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]')");
        }
    }

    function upd_transportista($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_transportista SET tra_razon_social='$data[1]',tra_email='$data[2]',tra_placa='$data[3]',tra_telefono='$data[4]',tra_direccion='$data[5]' WHERE tra_id='$id'");
        }
    }

    function delete_transportista($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_transportista WHERE tra_id= '$id'"
            );
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
