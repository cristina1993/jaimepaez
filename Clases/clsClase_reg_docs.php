<?php
include_once 'Conn.php';
class Clase_reg_docs {

    var $con;

    function Clase_reg_docs() {
        $this->con = new Conn();
    }

    function lista_reg_($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  detalle_fact_notdeb_notcre where num_camprobante='$id' and tipo_comprobante=1");
        }
    }

    
}

?>
