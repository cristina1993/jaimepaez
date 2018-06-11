<?php

include_once 'Conn.php';

class Clase_kardex_nopertipt {

    var $con;

    function Clase_kardex_nopertipt() {
        $this->con = new Conn();
    }

    function lista_kardex_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and c.cli_id=m.cli_id order by p.pro_codigo,m.mov_fecha_trans desc");
        }
    }

    function lista_buscador_kardex_noperti($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $txt");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////         
}

?>
