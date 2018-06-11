<?php

include_once 'Conn.php';

class Clase_inventario_nopertipt {

    var $con;

    function Clase_inventario_nopertipt() {
        $this->con = new Conn();
    }

    function lista_inventario_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id order by p.pro_codigo, p.pro_descripcion");
        }
    }

    function suma_ingreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(m.mov_cantidad) as suma FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 0");
        }
    }

    function suma_egreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(m.mov_cantidad) as suma FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 1");
        }
    }

    function lista_buscador_inventario($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id $txt");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////         
}

?>
