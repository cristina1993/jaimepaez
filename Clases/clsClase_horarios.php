<?php

include_once 'Conn.php';

class Clase_horarios {

    var $con;

    function Clase_horarios() {
        $this->con = new Conn();
    }

    function insert_horarios($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_horarios(
            hor_descripcion,
            hor_h_entrada,
            hor_h_salida,
            hor_si_no,
            hor_h_inicio,
            hor_h_final,
            hor_h_total
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',
             $data[3],   
            '$data[4]',
            '$data[5]',
            '$data[6]')");
        }
    }
    
    function update_horarios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_horarios SET hor_descripcion='$data[0]',
                                                     hor_h_entrada='$data[1]',
                                                     hor_h_salida='$data[2]',
                                                     hor_si_no=$data[3],
                                                     hor_h_inicio='$data[4]',
                                                     hor_h_final='$data[5]',
                                                     hor_h_total='$data[6]'
                                                 WHERE hor_id=$id ");
        }
    }

    function lista_horarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_horarios ORDER BY hor_id ASC");
        }
    }

    function insert_periodos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_periodos(per_descripcion)VALUES('$data[0]')");
        }
    }
    
    function update_periodos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_periodos SET per_descripcion='$data[0]' WHERE per_id=$id");
        }
    }

    function lista_periodos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_periodos ORDER BY per_id ASC");
        }
    }

    function insert_grup_horarios($data, $dat) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_grupos_horarios(
                gru_horarios,
                gru_id_horarios,
                gru_hrs_semana,
                gru_lunes,
                gru_martes,
                gru_miercoles,
                gru_jueves,
                gru_viernes,
                gru_sabado,   
                gru_domingo
            )
    VALUES ('$dat[0]',
            '$data[0]', 
             $dat[1],
             $dat[2],
             $dat[3],
             $dat[4],
             $dat[5],
             $dat[6],
             $dat[7],
             $dat[8])");
        }
    }
    
    function update_grup_horarios($data, $dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_grupos_horarios SET gru_horarios='$dat[0]',
                                                            gru_id_horarios='$data[0]', 
                                                            gru_hrs_semana=$dat[1],
                                                            gru_lunes=$dat[2],
                                                            gru_martes=$dat[3],
                                                            gru_miercoles=$dat[4],
                                                            gru_jueves=$dat[5],
                                                            gru_viernes=$dat[6],
                                                            gru_sabado=$dat[7],  
                                                            gru_domingo=$dat[8] 
                                                        WHERE gru_id=$id");
        }
    }
    
    function lista_grupo_horarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_grupos_horarios");
        }
    }
    
    function lista_un_grupo_horario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_grupos_horarios WHERE gru_id=$id");
        }
    }
    
    function delete_grupo_horario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_grupos_horarios WHERE gru_id=$id");
        }
    }
    
    function delete_horario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_horarios WHERE hor_id=$id");
        }
    }
    
    function lista_un_horario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_horarios WHERE hor_id=$id");
        }
    }
    
    function lista_un_periodo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_periodos WHERE per_id=$id");
        }
    }

}
