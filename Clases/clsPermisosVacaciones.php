<?php

include_once 'Conn.php';

class VacacionesPermisos {

    var $con;

    function VacacionesPermisos() {
        $this->con = new Conn();
    }

    //Filtros
    function list_per_vac_fecha($from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
par_empleados em,
erp_criterios_permiso cp,
par_secciones sc
where rv.emp_id=em.emp_id
and em.sec_id=sc.sec_id
and cp.crp_id=rv.con_id
and rv.reg_vac_finicio between '$from' and '$until' 
ORDER BY em.emp_codigo    
                                ");
        }
    }

    function list_per_vac_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
par_empleados em,
erp_criterios_permiso cp,
par_secciones sc
where rv.emp_id=em.emp_id
and em.sec_id=sc.sec_id
and cp.crp_id=rv.con_id
and rv.reg_vac_documento like '%$doc%'
ORDER BY em.emp_codigo ");
        }
    }

    function list_per_vac_emp($emp, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
par_empleados em,
erp_criterios_permiso cp,
par_secciones sc
where rv.emp_id=em.emp_id
and em.sec_id=sc.sec_id
and cp.crp_id=rv.con_id
and (em.emp_codigo like '%$emp%' or em.emp_apellido_paterno like '%$emp%' or em.emp_apellido_materno like '%$emp%' or em.emp_nombres like '%$emp%' )   
and rv.reg_vac_finicio between '$from' and '$until'  
ORDER BY em.emp_codigo    ");
        }
    }

    function list_per_vac_sec($sec, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
par_empleados em,
erp_criterios_permiso cp,
par_secciones sc
where rv.emp_id=em.emp_id
and em.sec_id=sc.sec_id
and cp.crp_id=rv.con_id
and em.sec_id=$sec
and rv.reg_vac_finicio between '$from' and '$until'  
ORDER BY em.emp_codigo    ");
        }
    }

    //Funciones
    function listVacacionesPermisos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
                                            par_empleados em,
                                            par_concepto_permisos cp
                                            where rv.emp_id=em.emp_id
                                            and cp.con_id=rv.con_id ");
        }
    }

    function list_sec_vac_perm() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones where length(reg_vac_documento)>6 and substr(reg_vac_documento,1,1)<>'V'
                                         ORDER BY reg_vac_documento DESC limit 1");
        }
    }

    function listUnaVacacionesPermisos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
                                            par_empleados em,
                                            erp_criterios_permiso cp
                                            where rv.emp_id=em.emp_id
                                            and cp.crp_id=rv.con_id
                                            and reg_vac_id=$id ");
        }
    }

    function list_un_permiso_by_emp_date($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_reg_premisos_vacaciones rv,
                                                  par_empleados em,
                                                  par_concepto_permisos cp
                                                  where rv.emp_id=em.emp_id
                                                  and cp.con_id=rv.con_id
                                                  and em.emp_id=$emp
                                                  and '$date' BETWEEN rv.reg_vac_finicio and rv.reg_vac_ffinal
						  and EXTRACT(DOW from TIMESTAMP '$date')<>0		
						  and EXTRACT(DOW from TIMESTAMP '$date')<>6	");
        }
    }

    function insertVacacionesPermisos($data) {
        if ($this->con->Conectar() == true) {
            $freg = date('Y-m-d');
            return pg_query("INSERT INTO par_reg_premisos_vacaciones (
                                          emp_id,
                                          con_id,
                                          reg_vac_finicio,
                                          reg_vac_ffinal,
                                          reg_vac_descripcion,
                                          reg_vac_obs,
                                          reg_vac_documento,
                                          reg_vac_freg,
                                          reg_vac_hinicio,
                                          reg_vac_hfinal,
                                          reg_vac_recargo ) 
                                VALUES ($data[0],
                                        $data[1],
                                       '$data[2]',
                                       '$data[3]',
                                       '$data[4]',
                                       '$data[5]',
                                       '$data[6]',
                                        '$freg',
                                       '$data[7]',
                                       '$data[8]',
                                        $data[9]    
                                            ) ");
        }
    }

    function editVacacionesPermisos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_reg_premisos_vacaciones SET
                                          emp_id=$data[0],
                                          con_id=$data[1],
                                          reg_vac_finicio='$data[2]',
                                          reg_vac_ffinal='$data[3]',
                                          reg_vac_descripcion='$data[4]',
                                          reg_vac_obs='$data[5]',
                                          reg_vac_documento='$data[6]',
                                          reg_vac_hinicio='$data[7]',
                                          reg_vac_hfinal='$data[8]',
                                          reg_vac_recargo=$data[9]    
                                          WHERE  reg_vac_id=$id    ");
        }
    }

    function deleteVacacionesPermisos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_reg_premisos_vacaciones WHERE  reg_vac_id=$id   ");
        }
    }

    function listConseptoPermisos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_criterios_permiso order by crp_descripcion");
        }
    }

    function buscar_empleado($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_empleados where emp_codigo='$id'");
        }
    }

    function listSecciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones s, erp_division d, erp_gerencia g where d.ger_id=g.ger_id and cast(s.sec_area as integer)=d.div_id order by sec_descricpion");
        }
    }

    function lista_una_division($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_division where div_id= $id");
        }
    }

}

?>
