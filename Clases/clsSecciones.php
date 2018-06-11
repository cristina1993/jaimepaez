<?php

include_once 'Conn.php';

class Secciones {

    var $con;

    function Secciones() {
        $this->con = new Conn();
    }

    function listaSecciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc,sec_area,sec_descricpion Asc ");
        }
    }

    function listaSeccionesDesc($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_nombre='$desc'");
        }
    }

    function listaSeccionesRecicladoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_id=12 or sec_id=1 or sec_id=7 order by sec_descricpion Asc ");
        }
    }

    function listaUnaSecciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_id=$id");
        }
    }

    function listaSeccionesPolietileno() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones
                                         where sec_gerencia='T' and sec_area='P' 
                                         and sec_id<>13 and sec_id<>15 and sec_id<>4 order by sec_descricpion  ");
        }
    }

    function listaSeccionGerenciasDivision($ger, $div) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' and sec_area='$div' order by sec_descricpion Asc ");
        }
    }

    function lista_divisiones_gerencia($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sec_area from par_secciones where sec_gerencia='$ger' group by sec_area ");
        }
    }

    function listaSecGerDivCaract($ger, $div, $crt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones 
                                         where sec_gerencia='$ger' 
                                         and sec_area='$div' 
                                         and sec_$crt='t'
                                         order by sec_codigo Asc ");
        }
    }

    function listaSeccionGerenciasDivisionExtrusoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select sc.sec_id,sc.sec_descricpion from par_secciones sc, par_extrusoras ex 
where sc.sec_id=ex.sec_id
and sc.sec_gerencia='T' and sc.sec_area='P' 
and sc.sec_id<>13 
group by sc.sec_descricpion,sc.sec_id order by sc.sec_descricpion ");
        }
    }

    function listaAllSeccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc, sec_area desc  ");
        }
    }

    function listaSecByCaracteristica($sec, $crt, $crt1) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones 
                                        where sec_id<>$sec
                                        and (sec_$crt='t'
                                        or sec_$crt1='t') ");
        }
    }

    function listaSeccionGerencias($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' order by sec_codigo,sec_nombre  Asc ");
        }
    }

    function insertSec($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_secciones(sec_descricpion,
                                                                  sec_codigo,
                                                                  sec_area,
                                                                  sec_gerencia, 
                                                                  sec_nombre,
                                                                  sec_ext,
                                                                  sec_imp,
                                                                  sec_sel,
                                                                  sec_alm,
                                                                  sec_adm,
                                                                  sec_opr)
                                                        VALUES ('$data[0]',
                                                                '$data[1]',
                                                                '$data[2]',
                                                                '$data[3]', 
                                                                '$data[4]',
                                                                '$data[5]',
                                                                '$data[6]',
                                                                '$data[7]',
                                                                '$data[8]',
                                                                '$data[9]',
                                                                '$data[10]') ");
        }
    }

    function updateSec($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query(" update par_secciones set sec_descricpion='$data[0]',
                                                                  sec_codigo='$data[1]',
                                                                  sec_area='$data[2]',
                                                                  sec_gerencia='$data[3]', 
                                                                  sec_nombre='$data[4]',
                                                                  sec_ext='$data[5]',
                                                                  sec_imp='$data[6]',
                                                                  sec_sel='$data[7]',
                                                                  sec_alm='$data[8]',
                                                                  sec_adm='$data[9]',
                                                                  sec_opr='$data[10]' WHERE sec_id=$id ");
        }
    }

    function deleteSec() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones order by sec_gerencia desc, sec_area desc  ");
        }
    }
    
    function lista_divisiones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division");
        }
    }
    
    function lista_una_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_division WHERE div_id=$id");
        }
    }
    
    function lista_una_gerencia($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_gerencia WHERE ger_id=$id");
        }
    }

}

?>
