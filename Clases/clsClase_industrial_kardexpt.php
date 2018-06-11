<?php

include_once 'Conn.php';

class Clase_industrial_kardexpt {

    var $con;

    function Clase_industrial_kardexpt() {
        $this->con = new Conn();
    }

    function lista_kardex($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and m.bod_id=$bod order by m.pro_id,m.mov_tabla,m.mov_fecha_trans desc");
        }
    }

    function lista_kardex_noperti($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                SELECT m.*,p.*,t.*,c.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t,erp_i_cliente c, erp_productos_set ps  where  m.pro_id=p.id and c.cli_id=m.cli_id and ps.ids=p.ids and m.trs_id=t.trs_id and m.bod_id=$bod ORDER BY mov_documento desc                
                
                    
                    ");
        }
    }

    function lista_suma_ingreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 0");
        }
    }

    function lista_suma_egreso($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and p.pro_codigo='$cod' and t.trs_operacion= 1");
        }
    }

    function lista_buscar_kardexpt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_mp p where m.pro_id=p.id and m.trs_id=t.trs_id and c.cli_id=m.cli_id and $txt order by m.pro_id,m.mov_fecha_trans,t.trs_operacion,m.mov_id");
        }
    }

    function lista_kardexpt_mov($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT  m.pro_id,p.mp_c,p.mp_d,m.mov_tabla,t.trs_id,t.trs_descripcion,t.trs_operacion,m.bod_id FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_mp p where m.pro_id=p.id and m.trs_id=t.trs_id and c.cli_id=m.cli_id and $txt group by m.pro_id,p.mp_c,p.mp_d,m.mov_tabla,t.trs_id,m.bod_id order by m.pro_id,t.trs_descripcion desc");
        }
    }

    function suma_cant_cost($trs, $pro, $fec1, $fec2, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(mov_cantidad) FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_mp p where m.pro_id=p.id and m.trs_id=t.trs_id and m.trs_id=$trs and m.pro_id=$pro and m.mov_fecha_registro between '$fec1' and '$fec2' $txt) as cant, 
                                    (select sum(mov_val_tot) FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_mp p where m.pro_id=p.id and m.trs_id=t.trs_id and m.trs_id=$trs and m.pro_id=$pro and m.mov_fecha_registro between '$fec1' and '$fec2' $txt) as cost_tot");
        }
    }

    function lista_prod_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps  where ps.ids=p.ids and id=$id");
        }
    }

    function lista_prod_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_id=$id");
        }
    }

    function lista_buscar_industriales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id' or pro_descripcion='$id'");
        }
    }

    function lista_buscar_comerciales($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT p.*,ps.pro_tipo FROM  erp_productos p,erp_productos_set ps where ps.ids=p.ids and (p.pro_a='$id' or p.pro_b='$id')");
        }
    }

    function buscar_un_movimiento($id, $tab, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and m.pro_id='$id' and m.mov_tabla='$tab' and m.bod_id=$emi ORDER BY m.pro_id,m.mov_tabla");
        }
    }

    function lista_buscador_industrial_kardex($bod, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m,erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.bod_id=$bod $txt ORDER BY m.pro_id,m.mov_tabla");
        }
    }

    function total_ingreso_egreso($id, $emi, $fec1, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=0 and mov_fecha_trans<'$fec1' $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=0 and mov_fecha_trans<'$fec1' $txt) as egreso,
                                   (SELECT SUM(m.mov_val_tot)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=0 and mov_fecha_trans<'$fec1' $txt) as ti,
                                   (SELECT SUM(m.mov_val_tot)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=0 and mov_fecha_trans<'$fec1' $txt) as te");
        }
    }
   

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set WHERE ids<>79 and ids<>80 order by split_part(mp_tipo,'&',10) asc");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
