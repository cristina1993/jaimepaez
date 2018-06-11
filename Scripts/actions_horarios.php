<?php

session_start();
include_once '../Clases/clsClase_horarios.php';
include_once("../Clases/clsAuditoria.php");
$Adt = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$data1 = $_REQUEST[data1];
$fields = $_REQUEST[fields];
$id = $_REQUEST[id];
$Hor = new Clase_horarios;
switch ($op) {
    case 0:
        $sms = 0;
        if ($id == 0) {
            if ($Hor->insert_horarios($data) == FALSE) {
                $sms = 'Insert_horarios' . pg_last_error();
            }
        } else {
            if ($Hor->update_horarios($data, $id) == FALSE) {
                $sms = 'Update_horarios' . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($id == 0) {
            if ($Hor->insert_periodos($data) == FALSE) {
                $sms = 'Insert_periodos' . pg_last_error();
            }
        } else {
            if ($Hor->update_periodos($data, $id) == FALSE) {
                $sms = 'Update_periodos' . pg_last_error();
            }
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        $aud = 0;
        if ($id == 0) {
            $n = 0;
            while ($n < count($data)) {
                if ($n == count($data) - 1) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $datos.="$data[$n]" . $coma;
                $n++;
                $dat = array($datos);
            }
            if ($Hor->insert_grup_horarios($dat, $data1) == FALSE) {
                $sms = 'Insert_grup_hrs' . pg_last_error();
                $aud = 1;
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GRUPOS HORARIOS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data1[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $n = 0;
            while ($n < count($data)) {
                if ($n == count($data) - 1) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $datos.="$data[$n]" . $coma;
                $n++;
                $dat = array($datos);
            }
            if ($Hor->update_grup_horarios($dat, $data1, $id) == FALSE) {
                $sms = 'Update_grup_hrs' . pg_last_error();
                $aud = 1;
            }
            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'GRUPOS HORARIOS';
                $accion = 'EDITAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data1[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 3:
        $sms = 0;
        if ($Hor->delete_grupo_horario($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'GRUPOS HORARIOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 4:
        $sms = 0;
        if ($Hor->delete_horario($id) == false) {
            $sms = pg_last_error();
        } else {
            $modulo = 'HORARIOS';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $id) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 5:
        $rst_horario = pg_fetch_array($Hor->lista_un_horario($id));
        $horario = $rst_horario[hor_id] . '&' . $rst_horario[hor_descripcion] . '&' . $rst_horario[hor_h_entrada] . '&' . $rst_horario[hor_h_salida] . '&' . $rst_horario[hor_si_no] . '&' . $rst_horario[hor_h_inicio] . '&' . $rst_horario[hor_h_final] . '&' . $rst_horario[hor_h_total];
        echo $horario;
        break;
    case 6:
        $rst_periodo = pg_fetch_array($Hor->lista_un_periodo($id));
        $periodo = $rst_periodo[per_id] . '&' . $rst_periodo[per_descripcion];
        echo $periodo;
        break;
}


