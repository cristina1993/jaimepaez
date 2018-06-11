<?php

include_once 'Conn.php';

class Timbradas {

    var $con;

    function Timbradas() {
        $this->con = new Conn();
    }

    function listaArchivosTxt() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_txt tx,par_users us WHERE tx.usu_id=us.usu_id ORDER BY tx.txt_id  DESC ");
        }
    }

    function lista_timb_tipo_search($txt, $from, $until, $tmb) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas tm,
                                         par_empleados em 
                                         where tm.trm_emp_cod=em.emp_codigo 
                                         $txt
                                         and tm.trm_date BETWEEN '$from' AND '$until'
                                         and tm.trm_timbrador=$tmb order by tm.trm_date,tm.trm_time desc");
        }
    }

    function listaTimbradasByFile($file) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas where txt_id=$file");
        }
    }

    function lista_crosstab_timbradas_t1($dia, $code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *
                                FROM crosstab(
                                 'select trm_emp_cod, trm_date, trm_time
                                  from par_timbradas
                                  where trm_date between ''$dia'' and ''$dia''
                                  and trm_emp_cod=$code
                                  order by trm_time'
                                  )
                                AS (codigo integer, tmb1 time, tmb2 time, tmb3 time, tmb4 time , tmb5 time , tmb6 time) ");
        }
    }
    function lista_crosstab_timbradas_t2($dia,$code) {
        if ($this->con->Conectar() == true) {
            $dia2 = date('Y-m-d', strtotime($dia . ' +1 day'));
            return pg_query("SELECT *
                                FROM crosstab(
                                 'SELECT trm_emp_cod, trm_date, trm_time FROM par_timbradas
                                        WHERE (trm_emp_cod=$code AND trm_date=''$dia'' AND trm_time BETWEEN ''12:00'' AND ''24:00'') 
                                        OR (trm_emp_cod=$code AND trm_date=''$dia2'' AND trm_time between ''00:00'' AND ''08:00'')    
                                        order by trm_date,trm_time '
                                  )
                                AS (codigo integer, tmb1 time, tmb2 time, tmb3 time, tmb4 time , tmb5 time , tmb6 time) ");
        }
    }
    

    function lista_timb_tipo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas tm,
                            par_empleados em 
                            where tm.trm_emp_cod=em.emp_codigo 
                            and tm.trm_tipo=$tp ");
        }
    }

    function listaTimbradasByEmpDate($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_timbradas where trm_emp_cod=$emp and trm_date='$date' order by trm_time asc");
        }
    }

    function lista_timbrada_almuerzo($emp, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_timbradas where trm_emp_cod='$emp' 
                                         and trm_date between '$from' and '$until'
                                         and trm_time between '11:00' and '14:00'");
        }
    }

    function lista_primera_timbrada($emp, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas 
                                         WHERE trm_emp_cod='$emp' 
                                         AND trm_date BETWEEN '$from' AND '$until' 
                                         ORDER BY trm_date,trm_time ASC limit 1    
                                             ");
        }
    }

    function listaTimbradasByEmpDateT1($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_timbradas where trm_emp_cod=$emp and trm_date='$date' order by trm_time asc");
        }
    }

    function listaTimbradasByEmpWeek($emp, $f1, $f2) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas WHERE trm_emp_cod='$emp' AND trm_date BETWEEN '$f1' AND '$f2'  ORDER BY trm_date,trm_time ASC");
        }
    }

    function listaTimbradasByEmpDateT2($emp, $date) {
        if ($this->con->Conectar() == true) {
            $date2 = date('d/m/Y', strtotime($date . ' +1 day'));
            return pg_query("SELECT * FROM par_timbradas
                                        WHERE (trm_emp_cod=$emp AND trm_date='$date' AND trm_time BETWEEN '12:00' AND '24:00') 
                                        OR (trm_emp_cod=$emp AND trm_date='$date2' AND trm_time between '00:00' AND '08:00')    
                                        ORDER BY trm_date ASC, trm_time ASC");
        }
    }

    function listaAllTimbradasByEmpDate($emp, $date) {
        if ($this->con->Conectar() == true) {
            $date2 = date('d/m/Y', strtotime($date . ' 1 day'));
            return pg_query("SELECT * FROM par_timbradas
                                        WHERE (trm_emp_cod=$emp AND trm_date='$date' AND trm_time BETWEEN '06:00' AND '24:00') 
                                        OR (trm_emp_cod=$emp AND trm_date='$date2' AND trm_time between '00:00' AND '08:00')    
                                        ORDER BY trm_date ASC, trm_time ASC");
        }
    }

    function listaFirstTimbradasByEmpDate($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas
                                        WHERE trm_emp_cod=$emp AND trm_date='$date'  
                                        ORDER BY  trm_time ASC limit 1");
        }
    }

    function listaLastTimbradasByEmpDate($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas
                                        WHERE trm_emp_cod=$emp AND trm_date='$date'  
                                        ORDER BY trm_time desc  limit 1 ");
        }
    }

    function listaAlmTimbradasByEmpDate($emp, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_timbradas
                                         WHERE trm_emp_cod=$emp AND trm_date='$date' and trm_time between '09:30' and '14:30' 
                                         ORDER BY trm_date ASC, trm_time ASC ");
        }
    }

    function lista_timbrada_emp_fecha($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_timbradas WHERE trm_date='$data[1]' AND trm_emp_cod=$data[2] ");
        }
    }

    function listaUnTxtByName($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_txt WHERE txt_archivo='$txt'");
        }
    }

    function upd_timbrada_validation($code, $fecha, $cor) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_timbradas SET trm_val=$cor WHERE trm_emp_cod=$code and trm_date='$fecha' ");
        }
    }

    function upd_timbrada_emp_fecha($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_timbradas SET trm_he='$data[0]' WHERE trm_date='$data[1]' AND trm_emp_cod=$data[2] ");
        }
    }

    function upd_turno_timbrada_emp_fecha($data, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_timbradas SET trm_turno=$data[0] WHERE trm_date BETWEEN '$data[1]' AND '$hasta' AND trm_emp_cod=$data[2] ");
        }
    }

    function delAllTxt() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_txt ");
        }
    }

    function delTxtByName($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_txt where txt_archivo='$txt' ");
        }
    }

    function delTxtById($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_txt where txt_id=$txt ");
        }
    }

    function delAllTimbradas() {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_timbradas ");
        }
    }

    function delTimbradasBytxt($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_timbradas WHERE txt_id=$txt");
        }
    }

    function insertaArchivoTxt($file, $user, $obs) {
        if ($this->con->Conectar() == true) {
            $date = date('d/m/Y');
            $hour = date('H:i');
            return pg_query(" INSERT INTO par_txt
                                        ( txt_archivo,
                                          usu_id,
                                          txt_date,
                                          txt_hour,
                                          txt_obs
                                        )VALUES('$file',
                                                 $user,
                                                '$date',
                                                '$hour',
                                                '$obs' )");
        }
    }

    function insertaTimbradas($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_timbradas
                                        ( trm_timbrador,
                                          trm_emp_cod,
                                          trm_time,
                                          trm_date,
                                          txt_id,
                                          trm_tipo,
                                          trm_val
                                        )VALUES( $campos[0],
                                                 $campos[1],
                                                '$campos[2]',
                                                '$campos[3]',
                                                 $campos[4],
                                                 $campos[5],
                                                 $campos[6] )");
        }
    }

    //Quincenas
    function lista_quincena_emp_date($emp, $dia) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincena where qnc_codigo='$emp' and substr(qnc_fecha,4,10)='$dia' ");
        }
    }

    function lista_quincena_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincena order by qnc_id ");
        }
    }

        
    
    function lista_novedades_quincena($qnc, $mes, $anio, $vld) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincena qc,par_empleados em, par_secciones sc 
                            where qc.qnc_codigo=em.emp_codigo
                            and em.sec_id=sc.sec_id
                            and qc.qnc_no=$qnc
                            and qc.qnc_mes=$mes
                            and qc.qnc_anio=$anio
                            and qc.qnc_vld=$vld
                            order by em.emp_codigo ");
        }
    }

    function lista_quincenas_disponibles() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT qnc_no,qnc_mes,qnc_anio FROM par_quincena GROUP BY qnc_no,qnc_mes,qnc_anio ");
        }
    }

    function lista_quincena_emp_date_total($emp, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincena where qnc_codigo='$emp' and  qnc_desde='$desde' and  qnc_hasta='$hasta' and qnc_empleado='TOTALES'   ");
        }
    }

    function lista_quincena_emp_date_org($emp, $desde, $hasta) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincenas where emp_id=$emp and qnc_desde='$desde' and qnc_hasta='$hasta' ");
        }
    }

    function lista_quincena_qnc_mes_anio($qnc, $mes, $anio) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincena qc,par_empleados em 
                            where qc.qnc_codigo=em.emp_codigo
                            and qc.qnc_no=$qnc 
                            and qc.qnc_mes=$mes 
                            and qc.qnc_anio=$anio
                            and qc.qnc_empleado='TOTALES'
                            order by em.emp_codigo  ");
        }
    }

    function lista_quincena_qnc_mes_anio_emp($qnc, $mes, $anio, $emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_quincenas qc
                            where qnc_no=$qnc 
                            and qnc_mes=$mes
                            and qnc_anio=$anio
                            and emp_id=$emp  ");
        }
    }

    function inserta_quincena($campos) {
        if ($this->con->Conectar() == true) {
            $hora=date('H:i');
            $fecha=date('Y-m-d');
            return pg_query("INSERT INTO par_quincena
                                        (   
  usu_id ,
  qnc_no ,
  qnc_mes ,
  qnc_anio ,
  qnc_desde ,
  qnc_hasta ,
  qnc_hora_reg ,
  qnc_fecha_reg ,
  qnc_codigo ,
  qnc_cedula ,
  qnc_empleado ,
  qnc_gerencia ,
  qnc_division ,
  qnc_seccion ,
  qnc_cargo ,
  qnc_horario ,
  qnc_turno ,
  qnc_alm ,
  qnc_sem ,
  qnc_dias ,
  qnc_fecha ,
  qnc_h_entrada ,
  qnc_timb1 ,
  qnc_timb2 ,
  qnc_timb3 ,
  qnc_timb4 ,
  qnc_timb5 ,
  qnc_timb6 ,
  qnc_h0 ,
  qnc_h125 ,
  qnc_h150 ,
  qnc_h200 ,
  qnc_desc ,
  qnc_novedades ,
  qnc_thoras ,
  qnc_vld  )  VALUES( 
                                                 $_SESSION[usuid],
                                                 $campos[0],
                                                 $campos[1],    
                                                 $campos[2],
                                                '$campos[3]',
                                                '$campos[4]',
                                                '$hora',
                                                '$fecha',
                                                 $campos[5],
                                                '$campos[6]',
                                                '$campos[7]',
                                                '$campos[8]',
                                                '$campos[9]',
                                                '$campos[10]',
                                                '$campos[11]',
                                                '$campos[12]',
                                                '$campos[13]',
                                                '$campos[14]',
                                                '$campos[15]',
                                                '$campos[16]',
                                                '$campos[17]',
                                                '$campos[18]',
                                                '$campos[19]',
                                                '$campos[20]',
                                                '$campos[21]',
                                                '$campos[22]',
                                                '$campos[23]',
                                                '$campos[24]',
                                                '$campos[25]',
                                                '$campos[26]',
                                                '$campos[27]',
                                                '$campos[28]',
                                                '$campos[29]',
                                                '$campos[30]',                                                    
                                                '$campos[31]',
                                                 $campos[32]   )  ");
        }
    }

    function upd_quincena($campos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_quincenas SET
                                            emp_id=$campos[0],
                                            qnc_no=$campos[1],
                                            qnc_mes=$campos[2],
                                            qnc_anio=$campos[3],
                                            qnc_desde='$campos[4]',
                                            qnc_hasta='$campos[5]',
                                            qnc_h0='$campos[6]',
                                            qnc_h125='$campos[7]',
                                            qnc_h150='$campos[8]',
                                            qnc_h200='$campos[9]',
                                            qnc_h300='$campos[10]',
                                            qnc_desc='$campos[11]',
                                            qnc_dias_laborados=$campos[12],
                                            qnc_novedades='$campos[13]'    where qnc_id=$id  ");
        }
    }
    function upd_quincena_cuadrada($campos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_quincenas SET
                                            qnc_h0='$campos[0]',
                                            qnc_h125='$campos[1]',
                                            qnc_h150='$campos[2]',
                                            qnc_h200='$campos[3]'     where qnc_id=$id  ");
        }
    }

//**********Rangos de Timbradas*************************************************
    function listaRangosTimbradas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_rango_timbradas");
        }
    }

    function listaUnRangoTimbradas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_rango_timbradas WHERE rgt_id=$id");
        }
    }

    function deleteRangoTimbradas($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_rango_timbradas WHERE rgt_id=$id");
        }
    }

    function insertRangoTimbradas($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_rango_timbradas
                    ( rgt_antes_hora_ingreso,
                      rgt_antes_hora_ingreso_aviso,
                      rgt_hora_ingreso_normal_menos,
                      rgt_hora_ingreso_normal_mas,
                      rgt_hora_ingreso_normal_aviso,
                      rgt_atrazo,
                      rgt_atrazo_aviso,
                      rgt_falta,
                      rgt_falta_aviso,
                      rgt_almuerzo_menos,
                      rgt_almuerzo_mas,
                      rgt_almuerzo_aviso,
                      rgt_hora_salida_menos,
                      rgt_hora_salida_mas,
                      rgt_hora_salida_aviso,
                      rgt_valido_desde,
                      rgt_valido_hasta)   VALUES($campos[0],
                                                '$campos[1]',
                                                 $campos[2],
                                                 $campos[3],
                                                '$campos[4]',
                                                 $campos[5],
                                                '$campos[6]',
                                                 $campos[7],
                                                '$campos[8]',
                                                 $campos[9],
                                                 $campos[10],
                                                '$campos[11]',
                                                 $campos[12],
                                                 $campos[13],
                                                '$campos[14]',
                                                '$campos[15]',
                                                '$campos[16]' ) ");
        }
    }

    function updateRangoTimbradas($campos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_rango_timbradas SET
                                          rgt_antes_hora_ingreso=$campos[0],
                                          rgt_antes_hora_ingreso_aviso='$campos[1]',
                                          rgt_hora_ingreso_normal_menos=$campos[2],
                                          rgt_hora_ingreso_normal_mas=$campos[3],
                                          rgt_hora_ingreso_normal_aviso='$campos[4]',
                                          rgt_atrazo=$campos[5],
                                          rgt_atrazo_aviso='$campos[6]',
                                          rgt_falta=$campos[7],
                                          rgt_falta_aviso='$campos[8]',
                                          rgt_almuerzo_menos=$campos[9],
                                          rgt_almuerzo_mas=$campos[10],
                                          rgt_almuerzo_aviso='$campos[11]',
                                          rgt_hora_salida_menos=$campos[12],
                                          rgt_hora_salida_mas=$campos[13],
                                          rgt_hora_salida_aviso='$campos[14]',
                                          rgt_valido_desde='$campos[15]',
                                          rgt_valido_hasta='$campos[16]' WHERE rgt_id=$id");
        }
    }

//*********************DIAS EXTRAORDINARIOs*************************************************       
    function listDiasExtraordinarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_dias_extraordinarios ORDER BY dex_fecha ASC");
        }
    }

    function listUnDiaExtraordinarios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_dias_extraordinarios WHERE dex_id=$id");
        }
    }

    function listUnDiaExtraordinarioFecha($date, $year) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_dias_extraordinarios WHERE 	dex_fecha='$date' and dex_anio=$year");
        }
    }

    function insertDiasExtraordinarios($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_dias_extraordinarios
                                        (dex_anio,dex_fecha,dex_tipo,dex_obs) VALUES('$data[0]','$data[1]','$data[2]','$data[3]') ");
        }
    }

    function updateDiasExtraordinarios($data,$id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE par_dias_extraordinarios SET 
                                         dex_anio='$data[0]',dex_fecha='$data[1]',dex_tipo='$data[2]',dex_obs='$data[3]' WHERE dex_id=$id ");
        }
    }

    function deleteUnDiaExtraordinario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_dias_extraordinarios WHERE dex_id=$id");
        }
    }

    function deleteAsgPuestoByDiaExt($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_asg_puestos WHERE asg_indent=$id");
        }
    }

//*********************VACACIONES*************************************************       
    function listVacaciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_vacaciones ORDER BY vac_id ASC");
        }
    }

    function listUnaVacacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_vacaciones WHERE vac_id=$id");
        }
    }

    function insertVacacion($campos) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_vacaciones 
                            ( vac_desde,
                              vac_hasta,
                              vac_dias_vacacion,
                              vac_incrementar,
                              vac_hasta_max )VALUES($campos[0],
                                                    $campos[1],
                                                    $campos[2],
                                                    $campos[3],
                                                    $campos[4])  ");
        }
    }

    function updateVacacion($campos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query(" UPDATE par_vacaciones SET
                                                  vac_desde=$campos[0],
                                                  vac_hasta=$campos[1],
                                                  vac_dias_vacacion=$campos[2],
                                                  vac_incrementar=$campos[3],
                                                  vac_hasta_max=$campos[4] WHERE vac_id=$id ");
        }
    }

    function deleteUnaVacacion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_vacaciones WHERE vac_id=$id");
        }
    }

//*********************EXCEPCIONES TIMBRADAS*************************************************       
    function listExcepciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_excepciones_timbradas ORDER BY exc_id ASC");
        }
    }

    function listUnaExcepciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_excepciones_timbradas WHERE exc_id=$id");
        }
    }

    function listUnaExcepcionesEmp($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_excepciones_timbradas WHERE emp_id=$emp");
        }
    }

    function insertExcepciones($emp_id, $obs) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_excepciones_timbradas 
                                            (emp_id,exc_obs)VALUES($emp_id,'$obs')");
        }
    }

    function updateExcepciones($emp_id, $obs, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query(" UPDATE par_excepciones_timbradas SET
                                                 emp_id=$emp_id,exc_obs='$obs' WHERE exc_id=$id ");
        }
    }

    function deleteUnaExcepciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_excepciones_timbradas WHERE exc_id=$id");
        }
    }

//*********************HORARIOS*************************************************       
    function listHorarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_horario ORDER BY hor_id ASC");
        }
    }

    function list_grupo_horarios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_grupo_horarios ORDER BY grp_codigo ASC");
        }
    }

    function listUnHorario($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_horario WHERE hor_id=$id");
        }
    }

    function insertHorarios($selector, $inicio, $fin, $poliut, $poliur, $mant, $gen, $quito, $guay) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_horario 
                                         (hor_selector,
                                          hor_inicio,
                                          hor_fin,
                                          hor_polietileno,
                                          hor_poliuretano,
                                          hor_mantenimiento,
                                          hor_general,
                                          hor_quito,
                                          hor_guayaquil )VALUES ($selector,
                                                                '$inicio',
                                                                '$fin',
                                                                '$poliut',
                                                                '$poliur',
                                                                '$mant',
                                                                '$gen',
                                                                '$quito',
                                                                '$guay')");
        }
    }

    function inserta_horarios($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO par_horario 
                                         (hor_inicio,
                                          hor_fin,
                                          hor_alms,
                                          hor_alme,
                                          hor_alm )VALUES ('$data[0]',
                                                           '$data[1]',
                                                           '$data[2]',
                                                           '$data[3]',
                                                            $data[4])");
        }
    }

    function updateHorarios($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query(" UPDATE par_horario SET
                                                  hor_inicio='$data[0]',
                                                  hor_alms='$data[1]',
                                                  hor_alme='$data[2]',
                                                  hor_fin='$data[3]',
                                                  hor_alm=$data[4]    
                                                        WHERE hor_id=$id ");
        }
    }

    function deleteUnaHorarios($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_horario WHERE hor_id=$id");
        }
    }

//****************************************************************************************                     
}

?>
