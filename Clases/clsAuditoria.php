<?php

session_start();
include_once 'Conn.php';
date_default_timezone_set('America/Guayaquil');

class Auditoria {

    var $con;

    function Auditoria() {
        $this->con = new Conn();
    }

    function listaSecuencialByPedido($ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  par_sec_etq where ord_pedido_id=$ped order by sec_etq_id desc limit 1 ");
        }
    }

    function insert_audit_general($modulo, $accion, $values,$doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria
                  ( usu_login,
                    adt_date,
                    adt_hour,
                    adt_ip,                    
                    adt_modulo,
                    adt_accion,
                    adt_campo,
                    adt_documento)
                     VALUES( '$_SESSION[User]',
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$_SERVER[REMOTE_ADDR]',                                
                            '$modulo',
                            '$accion',
                            '$values', 
                            '$doc') ");
        }
    }

    function insertAuditoria($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria
                    ( usu_login,
                      adt_date,
                      adt_hour,
                      adt_modulo,
                      adt_accion,
                      adt_ip,
                      adt_documento  )
                      VALUES( $campos[0],
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$campos[1]',
                            '$campos[2]',
                            '$campos[3]',
                            '$campos[4]')  ");
        }
    }

    function insert($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria
                    ( usu_login,
                      adt_date,
                      adt_hour,
                      adt_modulo,
                      adt_accion,
                      adt_ip,
                      adt_documento  )
                      VALUES('$_SESSION[User]',
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$campos[0]',
                            '$campos[1]',
                            '$_SERVER[REMOTE_ADDR]',
                            '$campos[2]'   ) ");
        }
    }

    function insertComplette($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_auditoria
                  ( usu_login,
                    adt_date,
                    adt_hour,
                    adt_modulo,
                    adt_accion,
                    adt_ip,
                    adt_documento,
                    adt_campo,
                    adt_vi,
                    adt_vf )
                     VALUES( '$_SESSION[User]',
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$campos[0]',
                            '$campos[1]',
                            '$_SERVER[REMOTE_ADDR]',
                            '$campos[2]',
                            '$campos[3]',
                            '$campos[4]',
                            '$campos[5]'   ) ");
        }
    }

    function insertTmpAudit($modulo, $accion, $doc, $campo, $vi, $vf) {
        if ($this->con->Conectar() == true) {
            $date = date('Y-m-d');
            $hour = date('H:i');
            $usu = '$_SESSION[User]';
            return pg_query("INSERT INTO erp_auditoria
                    ( usu_login,
                      adt_date,
                      adt_hour,
                      adt_modulo,
                      adt_accion,
                      adt_ip,
                      adt_documento,
                      adt_campo,
                      adt_vi,
                      adt_vf )
                      VALUES($usu,
                            '$date',
                            '$hour',
                            '$modulo',
                            '$accion',
                            '$_SERVER[REMOTE_ADDR]',
                            '$doc',
                            '$campo',
                            '$vi',
                            '$vf'   )  ");
        }
    }

    function editarSecuencial($id, $sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_sec_etq SET sec_secuencial=$sec where sec_etq_id=$id");
        }
    }

    function listaAuditoriaFecha($from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria 
                                WHERE adt_date BETWEEN '$from' AND '$until' ");
        }
    }

    function listaAuditoriaFechaAccion($from, $until, $accion) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria 
                                WHERE adt_accion like '%$accion%'
                                AND adt_date BETWEEN '$from' AND '$until' ");
        }
    }

    function listaAuditDoc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria 
                                WHERE adt_documento like '%$doc%' ");
        }
    }

    function listaAuditoriaFechaUsuario($from, $until, $user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria 
                                WHERE adt_date BETWEEN '$from' AND '$until'
                                AND usu_id=$user ");
        }
    }

    function listaAuditoriaFechaUsuarioAccion($from, $until, $user, $accion) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria 
                                WHERE adt_date BETWEEN '$from' AND '$until'
                                AND usu_id=$user
                                AND adt_accion like '%$accion%'
                        ");
        }
    }

    function insertEtiquetaSecuencial($pedido, $secuencial, $ultima) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_sec_etq
                                 (ord_pedido_id,
                                  sec_secuencial,
                                  sec_ultima)
                                  VALUES($pedido,
                                         $secuencial,
                                         $ultima ) ");
        }
    }

    function insert_auditoria($campos) {
        if ($this->con->Conectar() == true) {
            $n = 0;
            while ($n < count($campos[2])) {
                $values.=$campos[2][$n] . '=' . $campos[3][$n] . ',';
                $n++;
            }
            return pg_query("INSERT INTO erp_auditoria
                  ( usu_login,
                    adt_date,
                    adt_hour,
                    adt_ip,                    
                    adt_modulo,
                    adt_accion,
                    adt_vi )
                     VALUES( '$_SESSION[User]',
                            '" . date("Y-m-d") . "',
                            '" . date("H:i:s") . "',
                            '$_SERVER[REMOTE_ADDR]',                                
                            '$campos[0]',
                            '$campos[1]',
                            '$values'           ) ");
        }
    }

    function sanear_string($string) {

        $string = trim($string);

        $string = str_replace(
                array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
        );

        $string = str_replace(
                array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
        );

        $string = str_replace(
                array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
        );

        $string = str_replace(
                array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
        );

        $string = str_replace(
                array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
        );

        $string = str_replace(
                array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
        );

        $string = str_replace(
                array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "`", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            "."), '', $string
        );


        return $string;
    }

    function lista_Auditoria($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_auditoria where adt_id=$id");
        }
    }
    function lista_usuarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_users ORDER BY usu_person");
        }
    }
    
    
    /////**********MENUS*******//////////
    
    function update_orden($ord,$mod,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_option_list set opl_orden=$ord,opl_modulo='$mod' where opl_id=$id");
        }
    }

    function update_modulo($mod,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_modulos set mod_descripcion='$mod' where mod_id=$id");
        }
    }

    function update_proceso($proc,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update erp_procesos set proc_descripcion='$proc' where proc_id=$id");
        }
    }
    
    function eliminar_menus($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                            delete from erp_asg_option_list where opl_id=$id;
                            delete from erp_option_list where opl_id=$id  ");
        }
    }
    

}

?>
