<?php

include_once 'Conn.php';

class Clase_asignacion_puestos {

    var $con;

    function Clase_asignacion_puestos() {
        $this->con = new Conn();
    }

    function lista_division() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division order by div_descripcion");
        }
    }

    function lista_una_seccion_div($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_secciones where sec_area='$id' order by sec_nombre");
        }
    }

    function lista_buscardor_division($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division $txt order by div_descripcion");
        }
    }

    function lista_una_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division where div_id='$id'");
        }
    }

    function insert_division($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_division(
                        div_codigo,
                        div_descripcion,
                        ger_id,
                        div_siglas
                       )
            VALUES ('$data[0]','$data[1]','$data[2]','$data[3]')");
        }
    }

    function upd_division($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_division SET 
                    div_codigo='$data[0]',
                    div_descripcion='$data[1]',
                    ger_id='$data[2]', 
                    div_siglas='$data[3]' 
                    WHERE div_id='$id'");
        }
    }

    function delete_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_division WHERE div_id= '$id'"
            );
        }
    }

    function lista_una_gerencia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia where ger_id='$id'");
        }
    }

    function lista_gerencias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia order by ger_descripcion");
        }
    }

    function lista_contador($sc, $sem, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt,par_asg_puestos pta
                             WHERE  pt.pt_id=pta.pt_id
                             AND    pt.sec_id=$sc
                             AND    pta.asg_pt_semana=$sem
                             AND    pta.asg_pt_year=$y
                             AND    asg_indent=0");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////         
}

?>
