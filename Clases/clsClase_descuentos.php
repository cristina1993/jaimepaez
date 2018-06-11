<?php

include_once 'Conn.php';

class Descuentos {

    var $con;

    function Descuentos() {
        $this->con = new Conn();
    }
    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor ORDER by cod_orden ");
        }
    }
    function lista_productos($pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos WHERE pro_codigo LIKE'%$pro%' OR pro_descripcion like '%$pro%' ORDER BY pro_codigo ");
        }
    }
    
    
}

?>
