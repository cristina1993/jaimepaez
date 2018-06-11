<?php

include_once 'Conn.php';

class Clase_industrial_inventariopt {

    var $con;

    function Clase_industrial_inventariopt() {
        $this->con = new Conn();
    }

    function lista_ingreso_inventariopt($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.bod_id=$bod order by p.pro_codigo, p.pro_descripcion");
        }
    }

    function lista_inventariopt_noperti($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                            SELECT m.*,p.*,t.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t, erp_productos_set ps  where  m.pro_id=p.id and ps.ids=p.ids and m.trs_id=t.trs_id and m.bod_id=$bod ORDER BY mov_documento desc               
                    ");
        }
    }

    function lista_inventariopt($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.pro_codigo,p.pro_descripcion, m.mov_tabla FROM  erp_i_mov_inv_pt m, erp_i_productos p  where m.pro_id=p.pro_id and m.bod_id=$bod group by m.pro_id, p.pro_codigo,p.pro_descripcion, m.mov_tabla ORDER BY m.pro_id,mov_tabla");
        }
    }

    function lista_buscar_inventariopt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.pro_id, p.mp_c,p.mp_d,mp_q, m.mov_tabla,p.mp_a,p.mp_b FROM  erp_i_mov_inv_pt m, erp_mp p $txt group by m.pro_id, p.mp_c,p.mp_d,p.mp_q, m.mov_tabla,p.mp_a,p.mp_b ORDER BY p.mp_a,p.mp_b");
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

    function total_ingreso_egreso_fac($id,$fec1,$txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and mov_fecha_trans<='$fec1' $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and mov_fecha_trans<='$fec1' $txt) as egreso");
        }
    }
    
     function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set WHERE ids<>79 and ids<>80 order by split_part(mp_tipo,'&',10) ");
        }
    }
    
    function  lista_inventario_negativo($txt, $fml){
        if($this->con->Conectar() == true){
            return pg_query("select split_part(prod,'&',1) as familia,
                                    split_part(prod,'&',4) as codigo,
                                    split_part(prod,'&',5) as descripcion,
                                    split_part(prod,'&',7) as ids,
                                    to_char(cast(loc1 as double precision),'99,999,990') as loc1,
                                    to_char(cast(loc2 as double precision),'99,999,990') as loc2,
                                    to_char(cast(loc3 as double precision),'99,999,990') as loc3,
                                    to_char(cast(loc4 as double precision),'99,999,990') as loc4,
                                    to_char(cast(loc5 as double precision),'99,999,990') as loc5,
                                    to_char(cast(loc6 as double precision),'99,999,990') as loc6,
                                    to_char(cast(loc7 as double precision),'99,999,990') as loc7,
                                    to_char(cast(loc8 as double precision),'99,999,990') as loc8,
                                    to_char(cast(loc9 as double precision),'99,999,990') as loc9,
                                    to_char(cast(loc10 as double precision),'99,999,990') as loc10,
                                    to_char(cast(loc11 as double precision),'99,999,990') as loc11,
                                    to_char(cast(loc12 as double precision),'99,999,990') as loc12,
                                    to_char(cast(loc13 as double precision),'99,999,990') as loc13,
                                    to_char(cast(loc14 as double precision),'99,999,990') as loc14
                                from inventario_negativo where prod is not null   
                                $txt
                                $fml ");
        }
    }
    
    function lista_familias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT split_part(ps.mp_tipo, '&', 10) AS protipo ,ps.* FROM erp_mp_set ps order by protipo");
        }
    }
    
    function lista_emisores() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_emisor order by emi_cod_orden");
        }
    }

       function lista_repote_inventario_productos_buscador($txt, $fml) {
        if ($this->con->Conectar() == true) {
            return pg_query("select split_part(prod,'&',1) as id,
                                    split_part(prod,'&',2) as cod,
                                    split_part(prod,'&',3) as descr,
                                    split_part(prod,'&',4) as val,
                                    split_part(prod,'&',5) as ids,
                                    to_char(cast(loc1 as double precision),'99,999,990.99') as loc1,
                                    to_char(cast(loc2 as double precision),'99,999,990.99') as loc2,
                                    to_char(cast(loc3 as double precision),'99,999,990.99') as loc3,
                                    to_char(cast(loc4 as double precision),'99,999,990.99') as loc4,
                                    to_char(cast(loc5 as double precision),'99,999,990.99') as loc5,
                                    to_char(cast(loc6 as double precision),'99,999,990.99') as loc6,
                                    to_char(cast(loc7 as double precision),'99,999,990.99') as loc7,
                                    to_char(cast(loc8 as double precision),'99,999,990.99') as loc8,
                                    to_char(cast(loc9 as double precision),'99,999,990.99') as loc9,
                                    to_char(cast(loc10 as double precision),'99,999,990.99') as loc10,
                                    to_char(cast(loc11 as double precision),'99,999,990.99') as loc11,
                                    to_char(cast(loc12 as double precision),'99,999,990.99') as loc12,
                                    to_char(cast(loc13 as double precision),'99,999,990.99') as loc13,
                                    to_char(cast(loc14 as double precision),'99,999,990.99') as loc14
                                from inventario_general where prod is not null   
                                $txt
                                $fml ");
        }
    }

    function limpiar_movpt_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("delete  from erp_i_movpt_total");
        }
    }

    function lista_inv_productos($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id,mov_tabla FROM erp_i_mov_inv_pt where bod_id=$bod group by pro_id,mov_tabla");
        }
    }

    function total_ingreso_egreso($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad) as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad) as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso,
                                   (SELECT SUM(m.mov_val_tot) as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingresoc,
                                   (SELECT SUM(m.mov_val_tot) as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egresoc");
        }
    }

    function insert_movpt_total($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_movpt_total (
                pro_id,
                pro_tbl,
                mvt_cant, 
                mvt_fecha, 
                cod_punto_emision,mvt_costo)VALUES(
                $data[0],
                $data[1],
                '$data[2]',
                '$data[3]',
                $data[4],
                $data[5])");
        }
    }

      function lista_tipos($id){
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_tipos where tps_id=$id");
        }
    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
