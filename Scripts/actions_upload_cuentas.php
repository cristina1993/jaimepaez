<?php

include_once '../Clases/clsClase_plan_cuentas.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Set = new Clase_plan_cuentas();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_ctas.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Set = new Clase_plan_cuentas();
     $Aud = new Auditoria();
      $arch = $file;
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $row = explode(",", $aux);
            $cod = $row[0];
            $des = $row[1];
            $obs = $row[2];
            $data = array(
                $cod,
                $des,
                $obs);
            if (strlen(trim($row[0])) > 0) {
                if (!$Set->insert_cuenta($data)) {
                     $err;
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_plan_cuentas.php'
                           </script>                        
                        ";
                    break;
                }
            }
        }
        $n++;
    }
    $campos = Array('Subir Archvo', 'Subir', $arch);
    if (!$Aud->insert($campos)) {
        $err = pg_last_error();
        echo "<script>alert($err)</script>";
    }
    fclose($file);
    echo "<script>
           parent.document.getElementById('bottomFrame').src = ''
           parent.document.getElementById('contenedor2').rows = '*,0%'
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_plan_cuentas.php'
          </script> ";
}

function check_file($file) {
    $sms = 0;
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
                    if ($pos0 == true || $pos1 == true || $pos2 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea  $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_plan_cuentas.php'
                           </script>
                            ";
                    break;
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

