<?php

include_once '../Clases/clsClase_industrial_ingresopt.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Set = new Clase_industrial_ingresopt();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . 'inv_mp.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
     $Aud = new Auditoria();
    $Set = new Clase_industrial_ingresopt();
    $arch = $file;
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $pos = strpos($aux, ';');
            if ($pos == true) {
                $row = explode(";", $aux);
            } else {
                $row = explode(",", $aux);
            }
            $rst1 = pg_fetch_array($Set->lista_un_producto_industrial($row[0]));

            switch ($row[1]) {
                case 'INGRESO':
                    $trs_id = 8;
                    $trs_operacion = 0;
                    break;
                case 'EGRESO':
                    $trs_id = 16;
                    $trs_operacion = 1;
                    break;
            }

            $rst = pg_fetch_array($Set->lista_secuencial());
            $sec = (substr($rst[mov_documento], -5) + 1);
            if ($sec >= 0 && $sec < 10) {
                $txt = '000000000';
            } else if ($sec >= 10 && $sec < 100) {
                $txt = '00000000';
            } else if ($sec >= 100 && $sec < 1000) {
                $txt = '0000000';
            } else if ($sec >= 1000 && $sec < 10000) {
                $txt = '000000';
            } else if ($sec >= 10000 && $sec < 100000) {
                $txt = '00000';
            } else if ($sec >= 100000 && $sec < 1000000) {
                $txt = '0000';
            } else if ($sec >= 1000000 && $sec < 10000000) {
                $txt = '000';
            } else if ($sec >= 10000000 && $sec < 100000000) {
                $txt = '00';
            } else if ($sec >= 100000000 && $sec < 1000000000) {
                $txt = '0';
            } else if ($sec >= 1000000000 && $sec < 10000000000) {
                $txt = '';
            }
            
            $mov_documento = '001-'.$txt . $sec;
            $mp_id = $rst1[id];
            $mov_fecha_trans = date('Y-m-d');
            $mov_cantidad = $row[2];
            $mov_peso_total = $row[4];
            $mov_peso_unit = $row[3];

            $data = array(
                $mp_id,
                $trs_id,
                '1',
                '1',
                $mov_documento,
                '',
                $mov_fecha_trans,
                $mov_cantidad,
                '0',
                $mov_peso_unit,
                $mov_peso_total,
                '',
                '0'
            );
            if (strlen(trim($row[0])) > 0) {
                if (!$Set->insert_transferencia($data)) {
                    $err = 'Insert_Mov' . pg_last_error();
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
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
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
          </script> ";
}

function check_file($file) {
    $sms = 0;
    $d = date('Y-m-d');
    $h = date('Y-m-d');
    $Set = new Clase_industrial_ingresopt();
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
                    $pos2 = strpos($row[2], $val);
                    if ($pos0 == true || $pos1 == true || $pos2 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
                           </script>";
                    break;
                } else {
                    $rst = pg_fetch_array($Set->lista_un_producto_industrial($row[0]));
                    if (empty($rst)) {
                        $sms = "<script>
                                   alert('$row[0] no existe favor registrarlo  linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
                           </script>";

                        break;
                    }
                    if (is_numeric(trim($row[2]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Cantidad no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
                           </script>";
                        break;
                    }
                    
                    if (is_numeric(trim($row[3]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Costo/U no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
                           </script>";
                        break;
                    }
                    
                    if (is_numeric(trim($row[4]))) {
                        
                    } else {
                        $sms = "<script>
                                   alert('El campo Costo/T no es un valor numerico linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
                           </script>";
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

