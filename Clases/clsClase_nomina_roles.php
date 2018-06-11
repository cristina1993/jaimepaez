<?php

include_once 'Conn.php';

class Clase_nomina_roles {

    var $con;

    function Clase_nomina_roles() {
        $this->con = new Conn();
    }

    function lista_buscar_nomina($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_nomina n, par_empleados e where n.nom_empleado=e.emp_id $txt");
        }
    }

    function lista_una_nomina_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_nomina n, par_empleados e where n.nom_empleado=e.emp_id and n.nom_id=$id");
        }
    }

    function lista_detalle_nomina($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_det_nomina d, erp_rubros_nomina r where d.dnm_rubro=r.rub_id and d.nom_id=$id ");
        }
    }

    function lista_emisor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_id='$id'");
        }
    }

    function lista_configuraciones_dec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_configuraciones where con_id='2'");
        }
    }

}
