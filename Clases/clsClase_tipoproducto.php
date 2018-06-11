<?php

include_once 'Conn.php';

class Clase_tipoproducto {

    var $con;

    function Clase_tipoproducto() {
        $this->con = new Conn();
    }

    function lista_tipoproducto() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_tipos order by tps_id");
        }
    }

    function lista_buscardor_tipoproducto($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_tipos $txt order by tps_nombre");
        }
    }
    function lista_un_tipoproducto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_tipos where tps_id='$id'");
        }
    }

    function insert_tipoproducto($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_tipos(
                        tps_tipo,
                        tps_relacion,
                        tps_siglas,
                        tps_nombre,
                        tps_observaciones
                                               
                        )
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]','$data[4]')");
            
        }
    }

    function upd_tipoproducto($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_tipos SET tps_tipo='$data[0]',tps_relacion='$data[1]' ,tps_siglas='$data[2]', tps_nombre='$data[3]',tps_observaciones='$data[4]' WHERE tps_id='$id'");
        }
    }

    function delete_tipoproducto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_tipos WHERE tps_id = '$id'");
        }
    }

    function delete_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_registro_produccion WHERE reg_id = $id");
        }
    }

    function delete_registro_movimientos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_num_trans='$id'");
        }
    }

    function lista_una_orden_codigo(
    $ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos p, $ord");
        }
    }

    function lista_secuencial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_registro_produccion ORDER BY reg_id DESC LIMIT 1");
        }
    }

    function lista_movimientos_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

      function lista_tipos_siglas($txt,$num) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_tipos where tps_siglas='$txt' and tps_relacion='$num'");
        }
    }
    
    
//    function lista_movimientos_numtrans($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM erp_i_mov_inv_pt WHERE mov_num_trans='$id'");
//        }
//    }
///////////////////////////////////////////////////////////////////////////////////////         
}

?>
