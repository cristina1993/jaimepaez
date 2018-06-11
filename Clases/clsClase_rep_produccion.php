<?php

include_once 'Conn.php';

class ClaseOrden {

    var $con;

    function ClaseOrden() {
        $this->con = new Conn();
    }

    function lista_orden() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion op, erp_i_empresa e, erp_i_producto p,erp_i_registro_produccion rp where p.emp_id= e.emp_id and rp.ord_id=op.ord_id ORDER BY rp.reg_id DESC");
        }
    }

    function lista_resgistro_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_producto p, erp_i_orden_produccion op,erp_i_registro_produccion rp, erp_i_det_registro_produccion drp where rp.ord_id=op.ord_id and drp.reg_id= rp.reg_id and rp.reg_id=$id");
        }
    }

    function lista_buscador_orden($fec1, $fec2, $ord) {
        if ($this->con->Conectar() == true) {
            if ($fec1 == '') {
                $fec1 = '1970-01-01';
            }
            if ($fec2 == '') {
                $fec2 = date('Y-m-d');
            }
            if ($ord == '') {
                $ord = '%';
            }
            return pg_query("SELECT * FROM erp_i_orden_produccion op, erp_i_empresa e, erp_i_producto p,erp_i_registro_produccion rp where p.emp_id= e.emp_id and rp.ord_id=op.ord_id and rp.reg_fecha between '$fec1' and '$fec2' and op.ord_num_orden like '$ord%'");
        }
    }

    function lista_una_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion where ord_id=$id");
        }
    }

    function lista_ultimo_registro() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_registro_produccion order by reg_id desc limit 1");
        }
    }

    function insert_orden($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_registro_produccion(
            ord_id,    
            reg_fecha,
            reg_operador,
            reg_maquina,
            reg_gramaje)
            VALUES ($data[0],'$data[1]','$data[2]',$data[3],'$data[4]')");
        }
    }

    function insert_detalle($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_det_registro_produccion(
            reg_id,
            pro_id,
            reg_peso,
            ord_pro_secundario,
            reg_peso_secundario,
            reg_peso_reproceso,
            reg_peso_refilado)
            VALUES ($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6])");
        }
    }

    function upd_orden($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_registro_produccion SET ord_id=$data[0], reg_fecha='$data[1]',reg_operador='$data[2]',reg_maquina=$data[3],reg_gramaje='$data[4]' WHERE reg_id=$id");
        }
    }

    function upd_detalle($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_det_registro_produccion SET reg_id=$data[0],pro_id=$data[1],reg_peso='$data[2]',ord_pro_secundario=$data[3],reg_peso_secundario='$data[4]',reg_peso_reproceso='$data[5]',reg_peso_refilado='$data[6]' WHERE reg_id=$id");
        }
    }

    function delete_detalle($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_det_registro_produccion WHERE reg_id=$id");
        }
    }

    function delete_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_registro_produccion WHERE reg_id=$id");
        }
    }

    function lista_una_orden_codigo($ord) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_producto p, erp_i_orden_produccion op where op.ord_num_orden='$ord'");
        }
    }

    function suma_pesos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(reg_peso) as pri, sum(reg_peso_secundario) as sec, sum(reg_peso_reproceso) as rep, sum(reg_peso_refilado) as refilado  from erp_i_det_registro_produccion group by reg_id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
