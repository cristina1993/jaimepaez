<?php

include_once 'Conn.php';

class User {

    var $con;

    function User() {
        $this->con = new Conn();
    }

    function listUser($pass, $user) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_pass='$pass' and usu_login='$user' ");
        }
    }

    function listAllUser() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users order by usu_person ");
        }
    }

    function lista_usuarios_estado($sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_status='$sts' order by usu_person ");
        }
    }

    function listUnUsuario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users where usu_id=$id ");
        }
    }

    function listaMenuNoAsignados($user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM
erp_procesos pr,
erp_modulos md,
erp_option_list ol
WHERE pr.proc_id=md.proc_id
and   ol.mod_id=md.mod_id
and   not exists(select * from erp_asg_option_list aol where aol.opl_id=ol.opl_id and aol.usu_id=$user) 
order by pr.proc_orden,md.mod_descripcion,ol.opl_modulo ");
        }
    }

    function listAllPermits($user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM
erp_procesos pr,
erp_modulos md,
erp_option_list ol,
erp_asg_option_list aol
WHERE pr.proc_id=md.proc_id
and   ol.mod_id=md.mod_id
and  aol.opl_id=ol.opl_id
and  aol.usu_id=$user
order by pr.proc_orden,md.mod_descripcion,ol.opl_modulo
    ");
        }
    }

    function list_proceos($user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_procesos pr 
where exists(
select * from erp_modulos md, erp_option_list ol, erp_asg_option_list aol
where md.proc_id=pr.proc_id
and   ol.mod_id=md.mod_id
and   ol.opl_id=aol.opl_id
and   aol.usu_id=$user) order by pr.proc_orden");
        }
    }

    function list_modulos($proc, $user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_modulos md,
erp_procesos pr
where pr.proc_id=md.proc_id
and pr.proc_id=$proc
and exists(
select * from erp_option_list ol, erp_asg_option_list aol
where md.mod_id=ol.mod_id
and ol.opl_id=aol.opl_id
and aol.usu_id=$user )  order by md.mod_orden ");
        }
    }

    function list_primer_opl($mod, $user, $limit) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_modulos md,erp_option_list ol
where md.mod_id=ol.mod_id
and  md.mod_id=$mod
and exists(
select * from erp_asg_option_list aol
where ol.opl_id=aol.opl_id
and aol.usu_id=$user )
order by ol.opl_orden asc  $limit ");
        }
    }

    function lista_todos_procesos() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_procesos pr,erp_modulos md, erp_option_list op
where pr.proc_id=md.proc_id
and   md.mod_id=op.mod_id
order by pr.proc_descripcion, md.mod_descripcion, op.opl_orden ");
        }
    }

    function lista_modulosby_proceso($proc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_modulos where proc_id=$proc order by mod_id ");
        }
    }

    function lista_oplby_modulo($mod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_option_list where mod_id=$mod order by opl_id ");
        }
    }

    function insertaUsuarios($campos) {
        if ($this->con->Conectar() == true) {
            $date = date('Y-m-d');
            return pg_query("INSERT INTO erp_users 
                                                                (   
  usu_login,
  usu_pass,
  usu_status,
  usu_date,
  usu_person,usu_pc                                                     )
                                                                VALUES ('$campos[0]',
                                                                        '" . md5($campos[1]) . "',
                                                                        't',
                                                                        '$date',
                                                                        '$campos[2]',
                                                                         $campos[3]    ) ");
        }
    }

    function modificaEstado($data, $usuid) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_users set usu_status='$data[0]' where usu_id=$usuid");
        }
    }

    function cambia_clave($pass, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_users set usu_pass='$pass' where usu_id=$id");
        }
    }

    function modificaUsuario($campos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_users SET 
                                          usu_login='$campos[0]',
                                          usu_pass='" . md5($campos[1]) . "',
                                          usu_person='$campos[2]',
                                          usu_pc=$campos[3]  where usu_id=$id");
        }
    }

    function delete_permisos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asg_option_list WHERE aol_id=$id   ");
        }
    }

    function lista_permiso_por_modulo($user, $ol) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_asg_option_list where usu_id=$user and opl_id=$ol");
        }
    }

    function lista_un_opl($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_option_list o, erp_emisor e where o.emi_id=e.emi_id and opl_id=$id");
        }
    }

    function insertPermisos($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_asg_option_list(
                                                    usu_id,
                                                    opl_id,
                                                    asg_opl_level_0,
                                                    asg_opl_level_1,
                                                    asg_opl_level_2, 
                                                    asg_opl_level_3,
                                                    asg_opl_level_4,
                                                    asg_opl_level_5,
                                                    asg_opl_level_6)VALUES (
                                                     $campos[0],
                                                     $campos[1],
                                                    '$campos[2]',    
                                                    '$campos[3]',
                                                    '$campos[4]',
                                                    '$campos[5]',
                                                    '$campos[6]',
                                                    '$campos[7]',
                                                    '$campos[8]'  ) ");
        }
    }

    function lista_decimales() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=1");
        }
    }

    function lista_configuraciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones where con_id=$id");
        }
    }

    function lista_configuraciones1() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_configuraciones order by con_id");
        }
    }

    function lista_cuentas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas ORDER BY pln_codigo");
        }
    }

    function lista_cuentas_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_id='$id'");
        }
    }

    function lista_una_opcion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_procesos pr, erp_modulos md, erp_option_list ol WHERE pr.proc_id=md.proc_id and  ol.mod_id=md.mod_id and  ol.opl_id=$id");
        }
    }

}

?>
