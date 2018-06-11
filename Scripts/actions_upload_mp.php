<?php

include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
$set = new Set();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_mp.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Set = new Set();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $row = explode(",", $aux);
            $rst_tip = pg_fetch_array($Set->lista_un_tpmp_nom($row[0]));
            $rst_code = pg_fetch_array($Set->lista_codigo_mp($rst_tip[mpt_id]));
            $rst_tp = pg_fetch_array($Set->lista_un_tpmp($rst_tip[mpt_id]));
            $cod = substr($rst_code[mp_codigo], -4);
            $code = ($cod + 1);
            if ($code >= 0 && $code < 10) {
                $txt = '000';
            } elseif ($code >= 10 && $code < 100) {
                $txt = '00';
            } elseif ($code >= 100 && $code < 1000) {
                $txt = '0';
            } elseif ($code >= 1000 && $code < 10000) {
                $txt = '';
            }

            $cod = $rst_tp[mpt_siglas] . $txt . $code;
            $emp_id = 2;
            $mpt_id = $rst_tip[mpt_id];
            $mp_referencia = trim($row[1]);
            $mp_pro1 = '';
            $mp_pro2 = '';
            $mp_pro3 = '';
            $mp_pro4 = '';
            $mp_obs = '';
            $mp_unidad = trim($row[2]);
            $mp_presentacion = trim($row[3]);
            $data = array(
                $emp_id,
                $mpt_id,
                $cod,
                $mp_referencia,
                $mp_pro1,
                $mp_pro2,
                $mp_pro3,
                $mp_pro4,
                $mp_obs,
                $mp_unidad,
                $mp_presentacion);
            if (strlen(trim($row[0])) > 0) {
                if (!$Set->insert_mp($data)) {
                    echo '&' . pg_last_error() . ' Linea ' . $n;
                    print_r($data);
                    break;
                }
            }
        }
        $n++;
    }
    fclose($file);
}

function check_file($file) {
    $sms = 0;
    $Set = new Set();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $n++;
        $aux = fgets($file);
        if ($n > 1) {
            $row = explode(",", $aux);
            $chr = array('*', '=', '/', "'", '"');
            if (strlen(trim($row[0]))) {
                foreach ($chr as $val) {
                    $pos0 = strpos($row[0], $val);
                    $pos1 = strpos($row[1], $val);
                    $pos1 = strpos($row[2], $val);
                    $pos4 = strpos($row[3], $val);
                    if ($pos0 == true || $pos1 == true || $pos2 == true || $pos3 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = utf8_decode("No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea " . $n);
                    break;
                } else {
                    $rst_tip = pg_fetch_array($Set->lista_un_tpmp_nom($row[0]));
                    if (empty($rst_tip)) {
                        $sms = 'TIPO '. $row[0] . ' no existe favor registrarlo  linea  ' . $n;
                        break;
                    }
                }
            }
        }
    }
    fclose($file);
    return $sms;
}

$check = check_file('../formatos/' . $name);
if (strlen($check) == 1) {
    load_file('../formatos/' . $name);
} else {
    echo $check;
}

