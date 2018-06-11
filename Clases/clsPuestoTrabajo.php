<?php

include_once '../Clases/Conn.php';

class PuestoTrabajo {

    var $con;

    function PuestoTrabajo() {
        $this->con = new Conn();
    }

//****************************Lista Para Calificaciones*****************************************
    function listaPuestoTrabajoCalfGD($ger, $div, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_estado='t'
                                        AND   pt.pt_tipo_puesto=$tp ORDER BY pt.pt_nivel ");
        }
    }

    function listaPuestoTrabajoCalfGDS($ger, $div, $sec, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_estado='t'
                                        AND   sc.sec_id=$sec
                                        AND   pt.pt_tipo_puesto=$tp ORDER BY pt.pt_nivel ");
        }
    }

    function listaPuestoTrabajoCalfGDSfiltro($ger, $div, $sec, $tp, $c, $rs) {
        if ($this->con->Conectar() == true) {
            //echo "<script>alert('$rs[9]')</script>";
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_estado='t'
                                        AND   sc.sec_id=$sec
                                        AND   pt.pt_tipo_puesto=$tp
                                        AND(
                                               (pt_cargo LIKE '%$c[0]%' AND pt_responsabilidad LIKE '%$rs[0]%')
                                            OR (pt_cargo LIKE '%$c[1]%' AND pt_responsabilidad LIKE '%$rs[1]%')
                                            OR (pt_cargo LIKE '%$c[2]%' AND pt_responsabilidad LIKE '%$rs[2]%')
                                            OR (pt_cargo LIKE '%$c[3]%' AND pt_responsabilidad LIKE '%$rs[3]%')
                                            OR (pt_cargo LIKE '%$c[4]%' AND pt_responsabilidad LIKE '%$rs[4]%')
                                            OR (pt_cargo LIKE '%$c[5]%' AND pt_responsabilidad LIKE '%$rs[5]%')
                                            OR (pt_cargo LIKE '%$c[6]%' AND pt_responsabilidad LIKE '%$rs[6]%')
                                            OR (pt_cargo LIKE '%$c[7]%' AND pt_responsabilidad LIKE '%$rs[7]%')
                                            OR (pt_cargo LIKE '%$c[8]%' AND pt_responsabilidad LIKE '%$rs[8]%')
                                            OR (pt_cargo LIKE '%$c[9]%' AND pt_responsabilidad LIKE '%$rs[9]%')
                                
					   ) ORDER BY pt.pt_nivel ");
        }
    }

    function listaPuestoTrabajoCalfGDSfiltro2($ger, $div, $sec, $tp, $c) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_estado='t'
                                        AND   sc.sec_id=$sec
                                        AND   pt.pt_tipo_puesto=$tp
                                        AND(
                                           pt_responsabilidad LIKE '%$c[0]%'
					OR pt_responsabilidad LIKE '%$c[1]%'
					OR pt_responsabilidad LIKE '%$c[2]%'
					OR pt_responsabilidad LIKE '%$c[3]%'
					OR pt_responsabilidad LIKE '%$c[4]%'
					OR pt_responsabilidad LIKE '%$c[5]%'
					OR pt_responsabilidad LIKE '%$c[6]%'
					OR pt_responsabilidad LIKE '%$c[7]%'
					OR pt_responsabilidad LIKE '%$c[8]%'
					OR pt_responsabilidad LIKE '%$c[9]%'
                                        )	
                                         ORDER BY pt.pt_nivel");
        }
    }

    //************************************************************************************************* 



    function listaPuestoTrabajo($ger, $div, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_estado='t'
                                        AND   sc.sec_id=$cod
                                        AND   pt.pt_nivel<=6   
                                        ORDER BY pt.pt_codigo Asc ");
        }
    }

    function listaPuestoTrabajoGer($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_estado='t'
                                        AND   pt.pt_nivel<=2   
                                        ORDER BY pt.pt_nivel Asc ");
        }
    }

    function listaPuestoTrabajoGerDiv($ger, $div) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_gerencia='$ger'
                                        AND   pt.pt_division='$div'
                                        AND   pt.pt_estado='t'
                                        AND   pt.pt_nivel<=2   
                                        ORDER BY pt.pt_nivel Asc ");
        }
    }

    function listaPuestoTrabajoGerDivSec($ger, $div, $sec, $from, $until, $ident) {
        if ($this->con->Conectar() == true) {
            $frm = explode('/', $from);
            $date = $frm[0] . '-' . $frm[1] . '-' . $frm[2];
            $frm1 = date('d-m-Y', strtotime($date));
            $sem1 = intval(date('W', strtotime($frm1)));
            $frm = explode('/', $until);
            $date = $frm[0] . '-' . $frm[1] . '-' . $frm[2];
            $frm1 = date('d-m-Y', strtotime($date));
            $sem2 = intval(date('W', strtotime($frm1)));
            if ($ger == 'T') {

                if ($div == '0') {//Solo Gerencia
                    return pg_query("SELECT * FROM  par_puestos_trabajo pt, par_asg_puestos apt, par_secciones sc
                                            WHERE pt.pt_id=apt.pt_id
                                            AND   pt.sec_id=sc.sec_id
                                            AND   pt.pt_gerencia='$ger'
					    AND   apt.asg_pt_semana between $sem1 and $sem2
					    AND   apt.asg_indent=$ident ORDER BY pt.pt_division desc,sc.sec_nombre asc");
                } elseif ($sec == 0) {//Gerencia y Solo Division
                    return pg_query("SELECT * FROM  par_puestos_trabajo pt, par_asg_puestos apt, par_secciones sc
                                            WHERE pt.pt_id=apt.pt_id
                                            AND   pt.sec_id=sc.sec_id
                                            AND   pt.pt_gerencia='$ger'
                                            AND   pt.pt_division='$div'
					    AND   apt.asg_pt_semana between $sem1 and $sem2
					    AND   apt.asg_indent=$ident ORDER BY pt.pt_division desc,sc.sec_nombre asc");
                } else { //Gerencia y Division y Seccion
                    return pg_query("SELECT * FROM  par_puestos_trabajo pt, par_asg_puestos apt, par_secciones sc
                                            WHERE pt.pt_id=apt.pt_id
                                            AND   pt.sec_id=sc.sec_id
                                            AND   pt.sec_id=$sec
					    AND   apt.asg_pt_semana between $sem1 and $sem2
					    AND   apt.asg_indent=$ident ORDER BY pt.pt_division desc,sc.sec_nombre asc");
                }
            } else {


                if ($sec == 0) {//Solo Gerencia
                    return pg_query("SELECT * FROM  par_puestos_trabajo pt, par_asg_puestos apt, par_secciones sc
                                            WHERE pt.pt_id=apt.pt_id
                                            AND   pt.sec_id=sc.sec_id
                                            AND   pt.pt_gerencia='$ger'
					    AND   apt.asg_pt_semana between $sem1 and $sem2
					    AND   apt.asg_indent=$ident ORDER BY pt.pt_division desc,sc.sec_nombre asc");
                } else { //Por Seccion
                    return pg_query("SELECT * FROM  par_puestos_trabajo pt, par_asg_puestos apt, par_secciones sc
                                            WHERE pt.pt_id=apt.pt_id
                                            AND   pt.sec_id=sc.sec_id
                                            AND   pt.sec_id=$sec
					    AND   apt.asg_pt_semana between $sem1 and $sem2
					    AND   apt.asg_indent=$ident ORDER BY pt.pt_division desc,sc.sec_nombre asc");
                }
            }
        }
    }

    function listaAsgPuestoTrabajoSemGerDiv($sem, $ger, $div,$y) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_asg_puestos apt
                                        where pt.pt_id=apt.pt_id
                                        and pt.pt_gerencia='$ger'
                                        and pt.pt_division='$div'
                                        and apt.asg_pt_semana=$sem
                                        and apt.asg_pt_semana=$y");
        }
    }

    function listaAsgPuestoTrabajoSemGer($sem, $ger, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_asg_puestos apt
                                            where pt.pt_id=apt.pt_id
                                            and pt.pt_gerencia='$ger'
                                            and apt.asg_pt_semana=$sem
                                            and apt.asg_pt_year=$y");
        }
    }

    function listaAllPuestoTrabajo01() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         ORDER BY pt.pt_gerencia,pt.pt_division,sc.sec_nombre Asc");
        }
    }

//    function listaAllPuestoTrabajo() {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * from par_puestos_trabajo pt, par_secciones sc 
//                                         WHERE pt.sec_id=sc.sec_id
//                                         ORDER BY  pt.pt_nivel ASC LIMIT 1 ");
//        }
//    }

    function listaAllPuestoTrabajo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         ORDER BY  pt.sec_id,pt.pt_nivel ASC ");
        }
    }

//    function listaAllPuestoTrabajoG($g) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
//                                         WHERE pt.sec_id=sc.sec_id
//                                         AND   pt.pt_gerencia='$g'
//                                         AND   pt.pt_nivel=2
//                                         ORDER BY pt.pt_division desc, pt.pt_no asc");
//        }
//    }
    function listaAllPuestoTrabajoG($g) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         ORDER BY  sc.sec_id,pt.pt_nivel ASC");
        }
    }

    function listaAllPuestoTrabajoG2($g) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                          ORDER BY  pt.pt_nivel ASC");
        }
    }

    function listaAllPuestoTrabajoGD2($g, $d) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         AND   pt.pt_division='$d'
                                         ORDER BY  sc.sec_nombre,pt.pt_nivel,pt.pt_puesto asc");
        }
    }

//    function listaAllPuestoTrabajoGD($g, $d) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
//                                         WHERE pt.sec_id=sc.sec_id
//                                         AND   pt.pt_gerencia='$g'
//                                         AND   pt.pt_division='$d'
//                                         ORDER BY  pt.pt_nivel ASC LIMIT 1");
//        }
//    }

    function listaAllPuestoTrabajoGD($g, $d) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         AND   pt.pt_division='$d'
                                         ORDER BY  pt.sec_id,pt.pt_nivel ASC");
        }
    }

//    function listaAllPuestoTrabajoGDS($g, $d, $s) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
//                                         WHERE pt.sec_id=sc.sec_id
//                                         AND   pt.pt_gerencia='$g'
//                                         AND   pt.pt_division='$d'
//                                         AND   pt.sec_id=$s
//                                         ORDER BY  pt.pt_nivel ASC LIMIT 1");
//        }
//    }
    function listaAllPuestoTrabajoGDS($g, $d, $s) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         AND   pt.pt_division='$d'
                                         AND   pt.sec_id=$s
                                         ORDER BY  pt.sec_id,pt.pt_nivel ASC");
        }
    }

    function listaAllPuestoTrabajoGDgeneral($g, $d, $s) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         AND   pt.pt_division='$d'
                                         AND   pt.sec_id=$s
                                         ORDER BY pt.pt_nivel asc ");
        }
    }

    function listaAllPuestoTrabajoGDgeneral2() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   (pt.pt_id=71 OR pt.pt_id=141 OR pt.pt_id=177 OR pt.pt_id=333 OR pt.pt_id=219) ");
        }
    }

    function listaAllPuestoTrabajoGDS2($g, $d, $s) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_gerencia='$g'
                                         AND   pt.pt_division='$d'
                                         AND   pt.sec_id=$s
					 ORDER BY  pt.pt_nivel ASC LIMIT 1 ");
        }
    }

    function listaAllPuestoTrabajo0() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND (pt.pt_nivel=0 )
					     ORDER BY pt.pt_gerencia,pt.pt_division,sc.sec_nombre  ASC  ");
        }
    }

    function listaAllPuestoSubordinados($pt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_puesto_superior=$pt
                                         AND   pt.pt_nivel<>2
                                         ORDER BY pt.pt_codigo ASC");
        }
    }

    function listaAllPuestoSubordinadosSup($pt, $var) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_puesto_superior=$pt
                                         AND   pt.pt_nivel<>2
                                         AND   SUBSTR(pt.pt_responsabilidad,1,3)='$var'
                                         ORDER BY pt.pt_codigo ASC");
        }
    }

//        function listaAllPuestoSubordinadosSup($pt,$var){
//		if($this->con->Conectar()==true){
//			return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
//                                         WHERE pt.sec_id=sc.sec_id
//                                         AND   pt.pt_puesto_superior=$pt
//                                         AND   pt.pt_nivel<>2
//                                         AND   pt.pt_puesto='$var'
//                                         ORDER BY pt.pt_codigo ASC");
//                                        }
//                               }


    function listaAllPuestoSubordinadosSupOtros($pt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_puesto_superior=$pt
                                         AND   pt.pt_nivel<>2
                                         AND   SUBSTR(pt.pt_responsabilidad,1,3)<>'EXT'
                                         AND   SUBSTR(pt.pt_responsabilidad,1,3)<>'IMP'
                                         AND   SUBSTR(pt.pt_responsabilidad,1,3)<>'SEL'
                                         ORDER BY pt.pt_codigo ASC");
        }
    }

    function listaAllPuestoSubordinadosGerencia($pt, $ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                         AND   pt.pt_puesto_superior=$pt
                                         AND   pt.pt_gerencia='$ger'
                                         ORDER BY pt.pt_codigo ASC");
        }
    }

    function listaUnPuestoTrabajo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo where pt_id=$id");
        }
    }

    function listaUnAsignaPuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_asg_puestos where asg_pt_id=$id");
        }
    }

    function listaUnPuestoTrabajoReporte($sem, $emp, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_asg_puestos apt, par_puestos_trabajo pt 
                                        WHERE apt.pt_id=pt.pt_id
                                        AND asg_pt_semana=$sem 
                                        AND asg_pt_year=$y
                                        AND(emp_id1=$emp OR emp_id2=$emp OR emp_id3=$emp)");
        }
    }

    function listaUltimoCodigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo 
                                        where SUBSTR(pt_codigo,1,7)='$id'
                                        order by pt_codigo desc limit 1 ");
        }
    }

    function listaPuestosFinder() {
        if ($this->con->Conectar() == true) {
            return pg_query("select pt_puesto from par_puestos_trabajo group by pt_puesto order by pt_puesto asc");
        }
    }

    function insertarPuestoTrabajo($datos) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into par_puestos_trabajo
                             (pt_gerencia,
                              pt_division,
                              sec_id,
                              pt_puesto,
                              pt_no,
                              pt_cargo,
                              pt_codigo,
                              pt_codigo2,
                              pt_codigo3,
                              pt_turno1,
                              pt_turno2,
                              pt_turno3,
                              pt_responsabilidad,
                              pt_tipo_puesto,
                              pt_puesto_superior,
                              pt_nivel,
                              pt_estado,
                              pt_almuerzo,
                              pt_marca
                              )
                       values('" . $datos[0] . "',
                              '" . $datos[1] . "',
                               " . $datos[2] . ",
                              '" . $datos[3] . "',
                               " . $datos[4] . ",
                              '" . $datos[5] . "',    
                              '" . $datos[6] . "',
                              '" . $datos[7] . "',
                              '" . $datos[8] . "',
                               " . $datos[9] . ",
                               " . $datos[10] . ",              
                               " . $datos[11] . ",
                              '" . $datos[12] . "',
                               " . $datos[13] . ",              
                               " . $datos[14] . ",
                               " . $datos[15] . ",
                              '" . $datos[16] . "',
                              '" . $datos[17] . "',
                              '" . $datos[18] . "'    )");
        }
    }

    function editarPuestosTrabajo($datos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_puestos_trabajo set
                              pt_gerencia='$datos[0]',
                              pt_division='$datos[1]',
                              sec_id='$datos[2]',
                              pt_puesto='$datos[3]',
                              pt_no='$datos[4]',
                              pt_cargo='$datos[5]',
                              pt_codigo='$datos[6]',
                              pt_codigo2='$datos[7]',
                              pt_codigo3='$datos[8]',
                              pt_turno1='$datos[9]',
                              pt_turno2='$datos[10]',
                              pt_turno3='$datos[11]',
                              pt_responsabilidad='$datos[12]',
                              pt_tipo_puesto='$datos[13]',
                              pt_puesto_superior='$datos[14]',
                              pt_nivel='$datos[15]',
                              pt_estado='$datos[16]',
                              pt_almuerzo='$datos[17]',
                              pt_marca='$datos[18]'
                              where pt_id=$id");
        }
    }

    function eliminarPuestosTrabajo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_puestos_trabajo WHERE  pt_id=" . $id);
        }
    }

    function insertarAsgPuestoTrabajo($datos, $e1, $e2) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into par_asg_puestos
         (emp_id1,
          pt_id,
          asg_pt_semana,
          asg_pt_fecha,
          emp_id2,
          emp_id3,
          asg_pt_year)
   values( " . $e1 . ",
           " . $datos[1] . ",
          '" . $datos[2] . "',
          '" . $datos[3] . "',
           " . $e2 . ",
           " . $datos[5] . ",  
           " . $datos[6] . " )");
        }
    }

    function insertarAsgPuestoTrabajoWeekEnd($datos) {
        if ($this->con->Conectar() == true) {
            return pg_query("insert into par_asg_puestos
         (
  emp_id1
  ,pt_id
  ,asg_pt_semana
  ,asg_pt_fecha
  ,emp_id2
  ,emp_id3
  ,asg_cliente
  ,asg_horario
  ,asg_indent
  ,asg_weekend_lunch
)
   values( " . $datos[0] . ",
           " . $datos[1] . ",
          '" . $datos[2] . "',
          '" . $datos[3] . "',
           " . $datos[4] . ",
           " . $datos[5] . ",
          '" . $datos[6] . "',
          '" . $datos[7] . "',
           " . $datos[8] . ",
          '" . $datos[9] . "'    )");
        }
    }

    function modificaAsgPuestoTrabajo($datos, $id, $e1, $e2) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_asg_puestos   set emp_id1=$e1
                                                                      ,pt_id=$datos[1]
                                                                      ,asg_pt_semana=$datos[2]
                                                                      ,asg_pt_fecha='$datos[3]'
                                                                      ,emp_id2=$e2
                                                                      ,emp_id3=$datos[5]
                                                                      ,asg_pt_year=$datos[6] where asg_pt_id=$id");
        }
    }

    function modificaAsgPuestoTrabajoWeekEnd($datos, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_asg_puestos SET emp_id1=$datos[0]
                                                                      ,pt_id=$datos[1]
                                                                      ,asg_pt_semana=$datos[2]
                                                                      ,asg_pt_fecha='$datos[3]'
                                                                      ,emp_id2=$datos[4]
                                                                      ,emp_id3=$datos[5]
                                                                      ,asg_cliente='$datos[6]'
                                                                      ,asg_horario='$datos[7]'
                                                                      ,asg_indent=$datos[8]
                                                                      ,asg_weekend_lunch=$datos[9]   where asg_pt_id=$id");
        }
    }

    function cambiaTurnos($emp1, $emp2, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_asg_puestos   set  emp_id1=$emp1
                                                                      ,emp_id2=$emp2
                                                                       where asg_pt_id=$id");
        }
    }

    function cambiaPuestos($emp, $pt, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update par_asg_puestos   set  emp_id$pt=$emp  where asg_pt_id=$id");
        }
    }

    function listAsgPT() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos");
        }
    }

    function delAsgPT() {
        if ($this->con->Conectar() == true) {
            return pg_query("delete FROM par_asg_puestos");
        }
    }

    function listAsgPuestoTrabajo($id, $sem, $ident, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos where pt_id=$id and asg_pt_semana=$sem and asg_indent=$ident and asg_pt_year=$y");
        }
    }

    function listUltimoPuestoAsignadoByEmpleado($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos apt,par_puestos_trabajo pt, par_secciones sc  
                                            where pt.pt_id=apt.pt_id 
                                            and   pt.sec_id=sc.sec_id
                                            and (emp_id1=$emp or emp_id2=$emp or emp_id3=$emp)
                                            order by asg_pt_semana desc limit 1");
        }
    }

    function listAsgPuestoTrabajoEmpSem($emp, $sem, $ident, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos apt ,par_puestos_trabajo pt, par_secciones sc  
                                                        where apt.pt_id=pt.pt_id 
                                                        and sc.sec_id=pt.sec_id 
                                                        and asg_pt_semana=$sem
                                                        and apt.asg_indent=$ident
                                                        and asg_pt_year=$y
                                                        and( emp_id1=$emp or emp_id2=$emp or emp_id3=$emp) ");
        }
    }

    function listAsgPuestoTrabajoEmpSem2($emp, $sem, $ident) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos apt ,par_puestos_trabajo pt, par_secciones sc  
                                                        where apt.pt_id=pt.pt_id 
                                                        and sc.sec_id=pt.sec_id 
                                                        and asg_pt_semana=$sem
                                                        and apt.asg_indent=$ident
                                                        and( emp_id1=$emp or emp_id2=$emp or emp_id3=$emp) ");
        }
    }

    function listAsgPuestoTrabajoEmpSem0($emp, $sem, $ger, $ident, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_asg_puestos apt ,par_puestos_trabajo pt, par_secciones sc  
                                                        where apt.pt_id=pt.pt_id 
                                                        and sc.sec_id=pt.sec_id 
                                                        and asg_pt_semana=$sem
                                                        and asg_pt_year=$y
                                                        and asg_indent=$ident    
                                                        and( emp_id1=$emp or emp_id2=$emp or emp_id3=$emp) ");
        }
    }

    function listAsgPuestoTrabajoBySeccion($sec, $sem, $ident) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt,par_asg_puestos pta
WHERE  pt.pt_id=pta.pt_id
AND    pt.sec_id=$sec
AND    pta.asg_pt_semana=$sem
AND    asg_indent=$ident");
        }
    }

    function listAsgPuestoTrabajoBySeccionWeekEnd($sec, $sem) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM par_puestos_trabajo pt,par_asg_puestos pta
                                            WHERE  pt.pt_id=pta.pt_id
                                            AND    pt.sec_id=$sec
                                            AND    pta.asg_pt_semana=$sem
                                            AND    asg_indent<>0   ");
        }
    }

    function eliminarAsgPuestoTrabajoBySeccion($sem, $ptId) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_asg_puestos WHERE asg_pt_semana=$sem and  pt_id=$ptId");
        }
    }

    function eliminarAsgPuesto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM par_asg_puestos WHERE  asg_pt_id=$id");
        }
    }

    //// nuevas consultas ////

    function lista_gerencias() {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM erp_gerencia order by ger_descripcion");
        }
    }

    function lista_divisiones_ger($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM erp_division where ger_id=$id  order by div_descripcion");
        }
    }

    function lista_secciones_div($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM par_secciones where sec_area='$id' order by sec_descricpion");
        }
    }

    function lista_una_seccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Select * FROM par_secciones s, erp_gerencia g, erp_division d where d.ger_id=g.ger_id and cast (s.sec_area as integer)=d.div_id and s.sec_id='$id'");
        }
    }

    function listaSeccionGerenciasDivision($ger, $div) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' and sec_area='$div' order by sec_descricpion Asc ");
        }
    }

    function listaSeccionGerencias($ger) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_secciones where sec_gerencia='$ger' order by sec_codigo,sec_nombre  Asc ");
        }
    }

    function listaExtrusoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_extrusoras e, par_secciones s WHERE e.sec_id=s.sec_id and e.ext_id<>96 ORDER BY e.ext_descripcion ASC");
        }
    }

    function listaImpresoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_impresoras i, par_secciones s WHERE i.sec_id=s.sec_id AND i.imp_foranea='f' ORDER BY i.imp_descripcion ASC");
        }
    }

    function listaSelladoras() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_selladoras sl, 
                                         par_secciones s WHERE sl.sec_id=s.sec_id 
                                         ORDER BY sl.sell_descripcion ASC");
        }
    }

    function listaPuestoTrabajo_sec($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                        WHERE pt.sec_id=sc.sec_id
                                        AND   pt.pt_estado='t'
                                        AND   sc.sec_id=$cod
                                        AND   pt.pt_nivel<=6   
                                        ORDER BY pt.pt_codigo Asc ");
        }
    }

    function listaBuscadorPuestoTrabajo() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from par_puestos_trabajo pt, par_secciones sc 
                                         WHERE pt.sec_id=sc.sec_id
                                        ORDER BY pt.pt_division desc,pt.pt_no asc");
        }
    }

    function listaEmpleadosDuplicados($sem, $y) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT emp_id1, COUNT(*) AS contador 
                                         FROM par_asg_puestos 
                                         WHERE asg_pt_semana=$sem and  asg_pt_year=$y and emp_id1<>0 
                                         GROUP BY emp_id1 HAVING COUNT(*) > 1  ");
        }
    }

}

?>
