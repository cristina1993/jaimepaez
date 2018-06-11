<?php

include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_productos.php'; // cambiar clsClase_productos
$set = new Set();
$Prod = new Clase_Productos();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_pt.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Set = new Set();
    $Prod = new Clase_Productos();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $row = explode(",", $aux);
            $rst_tip = pg_fetch_array($Set->lista_un_tppt_nombre($row[2]));

            $cod = $row[0];
            $emp_id = 3;
            $pro_familia = 1;
            $pro_descripcion = trim($row[1]);
            $pro_uni = trim($row[3]);
            $pro_largo = $row[5];
            $pro_ancho = $row[4];
            $pro_capa = 0;
            $pro_espesor = 0;
            $pro_gramaje = $row[6];
            $pro_peso = $row[7];
            $pro_medvul = 0;
            $pro_mp1 = 0;
            $pro_mp2 = 0;
            $pro_mp3 = 0;
            $pro_mp4 = 0;
            $pro_mp5 = 0;
            $pro_mp6 = 0;
            $pro_mf1 = 0;
            $pro_mf2 = 0;
            $pro_mf3 = 0;
            $pro_mf4 = 0;
            $pro_mf5 = 0;
            $pro_mf6 = 0;
            $tpt_id = $rst_tip[tpt_id];

            $data = array(
                $emp_id,
                $cod,
                $pro_familia,
                $pro_descripcion,
                $pro_uni,
                $pro_largo,
                $pro_ancho,
                $pro_capa,
                $pro_espesor,
                $pro_gramaje,
                $pro_peso,
                $pro_medvul,
                $pro_mp1,
                $pro_mp2,
                $pro_mp3,
                $pro_mp4,
                $pro_mp5,
                $pro_mp6,
                $pro_mf1,
                $pro_mf2,
                $pro_mf3,
                $pro_mf4,
                $pro_mf5,
                $pro_mf6,
                $tpt_id);
            if (strlen(trim($row[0])) > 0) {
                if (!$Prod->insert($data)) {
                    echo '&' . pg_last_error() . ' Linea ' . $n;
                    print_r($data);
                    break;
                } else {
                    $rst_pro = pg_fetch_array($Prod->lista_codigo($cod));
                    $id = $rst_pro[pro_id];
                    $pre = $row[8];
                    $desc = $row[9];
                    $iva = trim($row[10]);
                    $data1 = array(
                        $id,
                        $pre,
                        $desc,
                        $iva
                    );

                    if (!$Prod->insert_precios($data1)) {
                        echo '&' . pg_last_error() . ' Linea ' . $n;
                        print_r($data1);
                        break;
                    }
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
                    $pos2 = strpos($row[2], $val);
                    $pos3 = strpos($row[3], $val);
                    if ($pos0 == true || $pos1 == true || $pos2 == true || $pos3 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = utf8_decode("No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea " . $n);
                    break;
                } else {
                    $rst_tip = pg_fetch_array($Set->lista_un_tppt_nombre($row[2]));
                    if (empty($rst_tip)) {
                        $sms = 'TIPO ' . $row[2] . ' no existe favor registrarlo  linea  ' . $n;
                        break;
                    }

                    if (is_numeric(round($row[4], 2))) {
                        
                    } else {
                        $sms = 'El campo ancho no es un valor numerico linea ' . $n;
                        break;
                    }

                    if (is_numeric(round($row[5], 2))) {
                        
                    } else {
                        $sms = 'El campo largo no es un valor numerico linea ' . $n;
                        break;
                    }

                    if (is_numeric(round($row[6], 2))) {
                        
                    } else {
                        $sms = 'El campo gramaje no es un valor numerico linea ' . $n;
                        break;
                    }

                    if (is_numeric(round($row[7], 2))) {
                        
                    } else {
                        $sms = 'El campo peso no es un valor numerico linea ' . $n;
                        break;
                    }
                    if (is_numeric(round($row[8], 2))) {
                        
                    } else {
                        $sms = 'El campo precio no es un valor numerico linea ' . $n;
                        break;
                    }
                    if (is_numeric(round($row[9], 2))) {
                        
                    } else {
                        $sms = 'El campo descuento no es un valor numerico linea ' . $n;
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

