<?php

include_once '../Clases/clsSetting.php'; //cambiar clsClase_productos
include_once '../Clases/clsClase_cliente.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Set = new Clase_cliente();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_cl.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Set = new Clase_cliente();
    $Aud = new Auditoria();
    $arch = $file;
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $pos = strpos($aux, ';');
            if ($pos == true) {
                $row = explode(";", $aux);
            } else {
                $row = explode(",", $aux);
            }
            switch ($row[0]) {
                case 'CLIENTE':
                    $tipo = 0;
                    $t = 'C';
                    break;
                case 'PROVEEDOR':
                    $tipo = 1;
                    $t = 'P';
                    break;
                case 'AMBOS':
                    $tipo = 2;
                    $t = 'CP';
                    break;
            }
            switch ($row[1]) {
                case 'NATURAL':
                    $categoria = 1;
                    $c = 'N';
                    break;
                case 'JURIDICO':
                    $tipo = 2;
                    $c = 'J';
                    break;
            }

            if ($row[4] == 'ECUADOR') {
                $nac = 0;
            } else {
                $nac = 1;
            }

            $rst = pg_fetch_array($Set->lista_secuencial_cliente($t . $c));
            if ($t == 'CP') {
                $sec = (substr($rst[cli_codigo], 3, 8) + 1);
            } else {
                $sec = (substr($rst[cli_codigo], 2, 8) + 1);
            }
            if ($sec >= 0 && $sec < 10) {
                $txt = '0000';
            } else if ($sec >= 10 && $sec < 100) {
                $txt = '000';
            } else if ($sec >= 100 && $sec < 1000) {
                $txt = '00';
            } else if ($sec >= 1000 && $sec < 10000) {
                $txt = '0';
            } else if ($sec >= 10000 && $sec < 100000) {
                $txt = '';
            }

            $codigo = $t . $c . $txt . $sec;

            $fecha = date('Y-m-d');
            $ced = $row[2];
            $raz_social = $row[3];
            $pais = $row[4];
            $provincia = $row[5];
            $canton = $row[6];
            $parroquia = $row[7];
            $direccion = $row[8];
            $telefono = $row[9];
            $email = $row[10];

            $data = array(
                $fecha,
                $tipo,
                $categoria,
                $codigo,
                0,
                $ced,
                $raz_social,
                $pais,
                $provincia,
                $canton,
                $parroquia,
                $direccion,
                $telefono,
                $email,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $nac);
            if (strlen(trim($row[0])) > 0) {
                if (!$Set->insert_varios_clientes($data)) {
                    $err;
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php'
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
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php?'
          </script> ";
}

function check_file($file) {
    $sms = 0;
    $Set = new Set();
    $Aud = new Auditoria();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $n++;
        $aux = fgets($file);
        if ($n > 1) {
            $pos = strpos($aux, ';');
            if ($pos == true) {
                $row = explode(";", $aux);
            } else {
                $row = explode(",", $aux);
            }
            $chr = array('*', '=', '/', "'", '"');
            if (strlen(trim($row[0]))) {
                foreach ($chr as $val) {
                    $pos0 = strpos($row[0], $val);
                    $pos1 = strpos($row[1], $val);
                    $pos2 = strpos($row[3], $val);
                    $pos3 = strpos($row[4], $val);
                    $pos4 = strpos($row[5], $val);
                    $pos5 = strpos($row[6], $val);
                    $pos6 = strpos($row[7], $val);

                    if ($pos0 == true || $pos1 == true || $pos2 == true || $pos3 == true || $pos4 == true || $pos5 == true || $pos6 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea  $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php'
                           </script>
                            ";
                    break;
                } else {
                    if (is_numeric(trim($row[9]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo telefono no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_i_cliente.php'
                           </script>
                            ";
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

