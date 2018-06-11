<?php

include_once 'Conn.php';

class Clase_central_costo {

    var $con;

    function Clase_central_costo() {
        $this->con = new Conn();
    }

    function lista_central($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ctc_descripcion FROM erp_central_costo c $txt group by c.ctc_descripcion order by c.ctc_descripcion");
        }
    }

    function lista_una_central($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_central_costo c, erp_plan_cuentas p where c.pln_id=p.pln_id and ctc_descripcion='$id' order by pln_codigo    ");
        }
    }

    function lista_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_estado=0 order by pln_codigo");
        }
    }

    function lista_una_cuenta($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_plan_cuentas where pln_id=$id");
        }
    }

    function insert_central_costo($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_central_costo (pln_id,ctc_descripcion) values('$data[0]','$data[1]')");
        }
    }

    function delete_central_costo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("delete from erp_central_costo where ctc_descripcion='$id'");
        }
    }

    function lista_suma_cuentas($cuenta, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select  sum(ac.con_valor_debe) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and trim(ac.con_concepto_debe)=trim('$cuenta')) as debe, 
                                    (select  sum(ac.con_valor_haber) FROM erp_asientos_contables ac WHERE ac.con_fecha_emision BETWEEN '$desde' AND '$hasta' and trim(ac.con_concepto_haber)=trim('$cuenta')) as haber");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
