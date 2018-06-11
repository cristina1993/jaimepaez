<?php
//session_start();
include_once("../Clases/clsAuditoria.php");
include_once("../Clases/clsPuestoTrabajo.php");
include_once("../Clases/clsClase_empleados.php");
$op = $_REQUEST[op];
$data = $_REQUEST['data'];
$sem = $_REQUEST['sem'];
$year = $_REQUEST['y'];
$ger = $_REQUEST['ger'];
$div = $_REQUEST['div'];
$asptid = $_REQUEST['aptid'];
$emp1 = $_REQUEST['emp1'];
$emp2 = $_REQUEST['emp2'];
$d = $_REQUEST['dia'];
$fields = $_REQUEST[fields];
$Puesto = new PuestoTrabajo();
$Emp = new Clase_empleados();
$Adt = new Auditoria();
switch ($op) {
    case 0:
        $v1 = 0;
        $v2 = 0;
        $rstE1 = pg_fetch_array($Emp->lista_un_empleado_cod($data[0]));
        $rstE2 = pg_fetch_array($Emp->lista_un_empleado_cod($data[4]));

        if (empty($rstE1)) {
            $rstE1[emp_id] = 0;
        }
        if (empty($rstE2)) {
            $rstE2[emp_id] = 0;
        }


        if ($rstE1[emp_id] > 0) {
            $cnsDupl1 = $Puesto->listAsgPuestoTrabajoEmpSem0($rstE1[emp_id], $data[2], 0, $data[6]);
        } else {
            $cnsDupl1 = 0;
        }
        if ($rstE2[emp_id] > 0) {
            $cnsDupl2 = $Puesto->listAsgPuestoTrabajoEmpSem0($rstE2[emp_id], $data[2], 0, $data[6]);
        } else {
            $cnsDupl2 = 0;
        }

        if ($ger == 'T') {
            while ($rstDupl1 = pg_fetch_array($cnsDupl1)) {
                if ($rstDupl1[pt_division] != $div) {
                    $v1 = 1;
                }
            }

            while ($rstDupl2 = pg_fetch_array($cnsDupl2)) {
                if ($rstDupl2[pt_division] != $div) {
                    $v2 = 1;
                }
            }
        } else {

            while ($rstDupl1 = pg_fetch_array($cnsDupl1)) {
                if ($rstDupl1[pt_gerencia] != $ger) {
                    $v1 = 1;
                }
            }

            while ($rstDupl2 = pg_fetch_array($cnsDupl2)) {
                if ($rstDupl2[pt_gerencia] != $ger) {
                    $v2 = 1;
                }
            }
        }


        if ($v1 == 0 && $v2 == 0) {
            $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], 0, $data[6]));


            if (empty($rstAgpt)) {

                if ($Puesto->insertarAsgPuestoTrabajo($data, $rstE1[emp_id], $rstE2[emp_id]) == true) {

                    $modulo = 'ASIG. PUESTOS TRABAJO';
                    $accion = 'INSERTAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields, '') == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }

                    $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], 0, $data[6]));
                    $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rstAgpt[emp_id1]));
                    $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rstAgpt[emp_id2]));
                    echo $rstEmp1[emp_codigo];
                    echo "*";
                    echo $rstEmp1[emp_apellido_paterno] . ' ' . $rstEmp1[emp_apellido_materno] . ' ' . $rstEmp1[emp_nombres];
                    echo "*";
                    echo $rstEmp2[emp_codigo];
                    echo "*";
                    echo $rstEmp2[emp_apellido_paterno] . ' ' . $rstEmp2[emp_apellido_materno] . ' ' . $rstEmp2[emp_nombres];
                } else {
                    echo "error*" . pg_last_error();
                }
            } else {

                if ($Puesto->modificaAsgPuestoTrabajo($data, $rstAgpt[asg_pt_id], $rstE1[emp_id], $rstE2[emp_id]) == true) {

                    $modulo = 'ASIG. PUESTOS TRABAJO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields, '') == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }


                    $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], 0, $data[6]));
                    $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rstAgpt[emp_id1]));
                    $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rstAgpt[emp_id2]));
                    echo $rstEmp1[emp_codigo];
                    echo "*";
                    echo $rstEmp1[emp_apellido_paterno] . ' ' . $rstEmp1[emp_apellido_materno] . ' ' . $rstEmp1[emp_nombres];
                    echo "*";
                    echo $rstEmp2[emp_codigo];
                    echo "*";
                    echo $rstEmp2[emp_apellido_paterno] . ' ' . $rstEmp2[emp_apellido_materno] . ' ' . $rstEmp2[emp_nombres];
                } else {
                    echo "error*" . pg_last_error();
                }
            }
        } else {
            $rstPtAnt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], 0, $data[6]));
            echo "error*duplicado*" . $rstPtAnt[emp_id1] . '*' . $rstPtAnt[emp_id2];
        }
        break;


    case 'aptWeekEnd':
        $v1 = 0;
        $v2 = 0;
        $d = $data[8];
        if ($data[0] > 0) {
            $cnsDupl1 = $Puesto->listAsgPuestoTrabajoEmpSem0($data[0], $data[2], $d);
        } else {
            $cnsDupl1 = 0;
        }
        if ($data[4] > 0) {
            $cnsDupl2 = $Puesto->listAsgPuestoTrabajoEmpSem0($data[4], $data[2], $d);
        } else {
            $cnsDupl2 = 0;
        }

        if ($ger == 'T') {
            while ($rstDupl1 = pg_fetch_array($cnsDupl1)) {
                if ($rstDupl1[pt_division] != $div) {
                    $v1 = 1;
                }
            }

            while ($rstDupl2 = pg_fetch_array($cnsDupl2)) {
                if ($rstDupl2[pt_division] != $div) {
                    $v2 = 1;
                }
            }
        } else {

            while ($rstDupl1 = pg_fetch_array($cnsDupl1)) {
                if ($rstDupl1[pt_gerencia] != $ger) {
                    $v1 = 1;
                }
            }

            while ($rstDupl2 = pg_fetch_array($cnsDupl2)) {
                if ($rstDupl2[pt_gerencia] != $ger) {
                    $v2 = 1;
                }
            }
        }
        if ($v1 == 0 && $v2 == 0) {
            $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], $d));
            if (empty($rstAgpt)) {
                if ($Puesto->insertarAsgPuestoTrabajoWeekEnd($data) == true) {
                    $usu_id = $_SESSION['usuid'];
                    $adt_date = date("d/m/Y");
                    $adt_hour = date("H:m:s");
                    $adt_modulo = "Asigna Puestos de Trabajo FS ";
                    $adt_accion = "Insertar";
                    $adt_ip = $_SERVER['REMOTE_ADDR'];
                    $adt_documento = $data[0] . $data[2] . $data[4];
                    include '../Validate/auditoria.php';

                    $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], $d));
                    $rstEmp1 = pg_fetch_array($Emp->listaUnEmpleadoCodigo($rstAgpt[emp_id1]));
                    $rstEmp2 = pg_fetch_array($Emp->listaUnEmpleadoCodigo($rstAgpt[emp_id2]));
                    echo $rstAgpt[asg_cliente];
                    echo "*";
                    echo $rstAgpt[asg_horario];
                    echo "*";
                    echo $rstEmp1[emp_codigo];
                    echo "*";
                    echo $rstEmp1[emp_apellido_paterno] . ' ' . $rstEmp1[emp_apellido_materno] . ' ' . $rstEmp1[emp_nombres];
                    echo "*";
                    echo $rstEmp2[emp_codigo];
                    echo "*";
                    echo $rstEmp2[emp_apellido_paterno] . ' ' . $rstEmp2[emp_apellido_materno] . ' ' . $rstEmp2[emp_nombres];
                } else {
                    echo "error*" . pg_last_error();
                }
            } else {
                if ($Puesto->modificaAsgPuestoTrabajoWeekEnd($data, $rstAgpt[asg_pt_id]) == true) {
                    $usu_id = $_SESSION['usuid'];
                    $adt_date = date("d/m/Y");
                    $adt_hour = date("H:m:s");
                    $adt_modulo = "Asigna Puestos de Trabajo FS";
                    $adt_accion = "Insertar";
                    $adt_ip = $_SERVER['REMOTE_ADDR'];
                    $adt_documento = $data[0] . $data[2] . $data[4];
                    include '../Validate/auditoria.php';

                    $rstAgpt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], $d));
                    $rstEmp1 = pg_fetch_array($Emp->listaUnEmpleadoCodigo($rstAgpt[emp_id1]));
                    $rstEmp2 = pg_fetch_array($Emp->listaUnEmpleadoCodigo($rstAgpt[emp_id2]));
                    echo $rstAgpt[asg_cliente];
                    echo "*";
                    echo $rstAgpt[asg_horario];
                    echo "*";
                    echo $rstEmp1[emp_codigo];
                    echo "*";
                    echo $rstEmp1[emp_apellido_paterno] . ' ' . $rstEmp1[emp_apellido_materno] . ' ' . $rstEmp1[emp_nombres];
                    echo "*";
                    echo $rstEmp2[emp_codigo];
                    echo "*";
                    echo $rstEmp2[emp_apellido_paterno] . ' ' . $rstEmp2[emp_apellido_materno] . ' ' . $rstEmp2[emp_nombres];
                } else {
                    echo "error*" . pg_last_error();
                }
            }
        } else {
            $rstPtAnt = pg_fetch_array($Puesto->listAsgPuestoTrabajo($data[1], $data[2], $d));
            echo "error*duplicado*" . $rstPtAnt[emp_id1] . '*' . $rstPtAnt[emp_id2];
        }
        break;


    case 3:
        if ($ger == 'T') {
            $cnsSB = $Puesto->listaAsgPuestoTrabajoSemGerDiv($sem, $ger, $div, $year);
            $cnsSA = $Puesto->listaAsgPuestoTrabajoSemGerDiv($sem + 1, $ger, $div, $year);
        } else {
            $cnsSB = $Puesto->listaAsgPuestoTrabajoSemGer($sem, $ger, $year);
            $cnsSA = $Puesto->listaAsgPuestoTrabajoSemGer($sem + 1, $ger, $year);
        }
        if (pg_num_rows($cnsSA) > 0) {
            while ($rstSA = pg_fetch_array($cnsSA)) {
                $Puesto->eliminarAsgPuesto($rstSA[asg_pt_id]);
            }
        }
        while ($rstSB = pg_fetch_array($cnsSB)) {
            $ax = 0;
            $ptExt = pg_fetch_array($Puesto->listaUnPuestoTrabajo($rstSB[pt_id]));

//            if (substr($ptExt['pt_responsabilidad'], 0, 3) == 'EXT' && $ptExt[pt_turno2] > 0) {
//                $ax = $rstSB[emp_id1];
//                $rstSB[emp_id1] = $rstSB[emp_id2];
//                $rstSB[emp_id2] = $ax;
//            }


            if ($Puesto->insertarAsgPuestoTrabajo(array(
                        $rstSB[emp_id1]
                        , $rstSB[pt_id]
                        , $rstSB[asg_pt_semana] + 1
                        , date('d-m-Y')
                        , $rstSB[emp_id2]
                        , $rstSB[emp_id3]
                        , $rstSB[asg_pt_year]
                            ), $rstSB[emp_id1], $rstSB[emp_id2]) == false) {
                $val = 1;
            }
            $sem = $rstSB[asg_pt_semana] + 1;
            $year = $rstSB[asg_pt_year];
        }

        if ($val == 1) {
            echo 'error';
        } else {
            $modulo = 'ASIG. PUESTOS TRABAJO';
            $accion = 'INSERTAR';
            $f = 'año=' . $year . '&semana=' . $sem . '&&';
            if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
            echo 'ok';
        }
        break;
    case 4:
        $rstAsgPt = pg_fetch_array($Puesto->listaUnAsignaPuesto($asptid));
        if (!empty($rstAsgPt)) {
            $aux = $rstAsgPt[emp_id1];
            $rstAsgPt[emp_id1] = $rstAsgPt[emp_id2];
            $rstAsgPt[emp_id2] = $aux;
            if ($Puesto->cambiaTurnos($rstAsgPt[emp_id1], $rstAsgPt[emp_id2], $asptid) == true) {
                $rstAsgPt = pg_fetch_array($Puesto->listaUnAsignaPuesto($asptid));
                $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rstAsgPt[emp_id1]));
                $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rstAsgPt[emp_id2]));
                echo $rstEmp1[emp_codigo];
                echo '*';
                echo $rstEmp2[emp_codigo];
                echo '*';
                echo $rstEmp1[emp_apellido_paterno] . ' ' . $rstEmp1[emp_apellido_materno] . ' ' . $rstEmp1[emp_nombres];
                echo '*';
                echo $rstEmp2[emp_apellido_paterno] . ' ' . $rstEmp2[emp_apellido_materno] . ' ' . $rstEmp2[emp_nombres];

                $modulo = 'ASIG. PUESTOS TRABAJO';
                $accion = 'MODIFICAR';
                $f = 'año=' . $rstAsgPt[asg_pt_year] . '&semana=' . $rstAsgPt[asg_pt_semana] . '&codigo1=' . $rstEmp1[emp_codigo] . '&codigo2=' . $rstEmp2[emp_codigo] . '&&';
                if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            } else {
//                echo 'error';  
                echo pg_last_error();
            }
        }
        break;
    case 5:
        $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($emp1));
        $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($emp2));
        $rstAsgPt1 = pg_fetch_array($Puesto->listaUnPuestoTrabajoReporte($sem, $rstEmp1[emp_id], $year));
        $rstAsgPt2 = pg_fetch_array($Puesto->listaUnPuestoTrabajoReporte($sem, $rstEmp2[emp_id], $year));
        if (!empty($rstAsgPt1) && !empty($rstAsgPt2)) {
            if ($emp1 == $rstAsgPt1[emp_id1]) {
                $pt1 = 1;
            } elseif ($emp1 == $rstAsgPt1[emp_id2]) {
                $pt1 = 2;
            } elseif ($emp1 == $rstAsgPt1[emp_id3]) {
                $pt1 = 3;
            }

            if ($emp2 == $rstAsgPt2[emp_id1]) {
                $pt2 = 1;
            } elseif ($emp2 == $rstAsgPt2[emp_id2]) {
                $pt2 = 2;
            } elseif ($emp2 == $rstAsgPt2[emp_id3]) {
                $pt2 = 3;
            }


            if ($Puesto->cambiaPuestos($emp1, $pt2, $rstAsgPt2[asg_pt_id]) == true) {
                if ($Puesto->cambiaPuestos($emp2, $pt1, $rstAsgPt1[asg_pt_id]) == true) {
                    $modulo = 'ASIG. PUESTOS TRABAJO';
                    $accion = 'MODIFICAR';
                    if ($Adt->insert_audit_general($modulo, $accion, $fields, '') == false) {
                        $sms = "Auditoria" . pg_last_error() . 'ok2';
                    }

                    echo "ok";
                }
            }
        }

        break;

    case 6:
        $rstEmp = pg_fetch_array($Emp->lista_un_empleado_cod($emp1));
        $cns = $Puesto->listAsgPuestoTrabajoEmpSem($rstEmp[emp_id], $sem, 0, $year);
        ?>                   
        <table>
            <?php
            while ($rst = pg_fetch_array($cns)) {
                ?>  
                <tr>    
                    <?php
                    if ($rst[emp_id1] == $emp1) {
                        echo "<td>$rst[sec_descricpion],$rst[pt_cargo],$rst[pt_responsabilidad]</td>";
                    }
                    if ($rst[emp_id2] == $emp1) {
                        echo "<td>$rst[sec_descricpion],$rst[pt_cargo],$rst[pt_responsabilidad]</td>";
                    }
                    ?>
                </tr>
                <?php
            }
            ?>                   
        </table>
        <?php
        break;
    case 7:
        $cnsEmp = $Puesto->listaEmpleadosDuplicados($sem, $year);
        ?>   
        <tr style='font-weight: bolder'>
            <td colspan="4" align="center">LISTA DUPLICADOS</td>
        </tr>
        <tr style='font-weight: bolder'>
            <td>NUMERO</td>
            <td>SECCION</td>
            <td>CARGO</td>
            <td>RESPONSABILIDAD</td>
        </tr>
        <?php
        while ($rstDupl = pg_fetch_array($cnsEmp)) {
            $cns = $Puesto->listAsgPuestoTrabajoEmpSem($rstDupl[emp_id1], $sem, 0, $year);
            ?>                   

            <?php
            while ($rst = pg_fetch_array($cns)) {
                $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rst[emp_id1]));
                $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rst[emp_id2]));
                ?>  
                <tr>    
                    <?php
                    if ($rst[emp_id1] == $rstDupl[emp_id1]) {
                        echo "<td>$rstEmp1[emp_codigo]</td><td>$rst[sec_descricpion]</td><td>$rst[pt_cargo]</td><td>$rst[pt_responsabilidad]</td>";
                    }
                    if ($rst[emp_id2] == $rstDupl[emp_id1]) {
                        echo "<td>$rstEmp2[emp_codigo]</td><td>$rst[sec_descricpion]</td><td>$rst[pt_cargo]</td><td>$rst[pt_responsabilidad]</td>";
                    }
                    ?>
                </tr>
                <?php
            }
            ?>                   

            <?php
        }

        break;
}
?>

