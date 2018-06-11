<?php

include_once '../Clases/clsClase_industrial_movimientopt.php'; //cambiar clsClase_productos
include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';

$Mvpt = new Clase_industrial_movimientopt();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_inv.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file, $emisor, $id_cli) {
    $Mvpt = new Clase_industrial_movimientopt();
    $arch = $file;
    $file = fopen($file, "r") or die("Error de Archivo" . $emisor);
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
            $row = explode(",", $aux);
            $rst_prod = pg_fetch_array($Mvpt->lista_productos_cod($row[0]));
            $pro_id = $rst_prod[id];
            $tbl = 0;

            switch ($row[1]) {
                case 'INGRESO':
                    $trs_id = 2;
                    $dc0 = '001';
                    break;
                case 'EGRESO':
                    $trs_id = 21;
                    $dc0 = '001';
                    break;
            }
            $txdc = '0000000000';
            $rst_doc = pg_fetch_array($Mvpt->lista_ultimo_secuencial_tp($dc0));
            $dc = explode('-', $rst_doc[mov_documento]);
            $dc1 = ($dc[1] + 1);
            $dc2 = $dc0 . '-' . (substr($txdc, 0, (10 - strlen($dc1)))) . $dc1;

            $cli_id = $id_cli;
            $bod_id = $emisor;
            $mov_documento = $dc2;
            $mov_fecha_trans = date('Y-m-d');
            $mov_fecha_registro = date('Y-m-d');
            $mov_hora_registro = date('H:i');
            $mov_tranportista = $row[2];
            $mov_cantidad = $row[3];
            $mov_val_unit = $row[4];
            $mov_val_tot = ($row[3] * 1) * ($row[4] * 1);
            $mov_tabla = $tbl;

            $data = array(
                $pro_id,
                $trs_id,
                $cli_id,
                $bod_id,
                $mov_documento,
                $mov_fecha_trans,
                $mov_fecha_registro,
                $mov_hora_registro,
                $mov_cantidad,
                $mov_tranportista,
                $mov_tabla,
                $mov_val_unit,
                $mov_val_tot);
            if (strlen(trim($row[0])) > 0) {
                if (!$Mvpt->insert_movimiento_pt($data)) {
                    $err;
                    echo "$err Linea $n";
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
}

function check_file($file) {
    $sms = 0;
    $Mvpt = new Clase_industrial_movimientopt();
    $file = fopen($file, "r") or die("Error de Archivo");
    $n = 0;
    while (!feof($file)) {
        $aux = fgets($file);
        if ($n > 0) {
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
                    $sms = utf8_decode("No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea " . $n);
                    //<script>
//                                   alert("No se acepta caracteres tales como (*='" . '/$%&#|°´+{}-;[]¨~``\¡?"' . ") linea $n"))
//                                   parent.document.getElementById('bottomFrame').src = ''
//                                   parent.document.getElementById('contenedor2').rows = '*,0%'
//                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
//                           </script>                        
//                        ";
                    break;
                } else {
                    if ($row[1] == 'INGRESO' || $row[1] == 'EGRESO') {
                        
                    } else {
                        $sms = "Campo INGRESO/EGRESO es incorrecto  linea  " . $n;
                        //<script>
//                                   alert('Campo INGRESO/EGRESO es incorrecto linea $n ')
//                                   parent.document.getElementById('bottomFrame').src = ''
//                                   parent.document.getElementById('contenedor2').rows = '*,0%'
//                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
//                           </script>                        
//                        ";
                        break;
                    }
                    if (is_numeric($row[3])) {
                        
                    } else {
                        $sms = 'El campo cantidad no es un valor numerico linea ' . $n;
                        //<script>
//                                   alert("El campo cantidad no es un valor numerico linea $n"))
//                                   parent.document.getElementById('bottomFrame').src = ''
//                                   parent.document.getElementById('contenedor2').rows = '*,0%'
//                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
//                           </script> 
                        break;
                    }
//                    if (is_numeric($row[4])) {
//                        
//                    } else {
//                        $sms = 'El campo costo unitario no es un valor numerico linea ' . $n;
//                        //<script>
////                                   alert("El campo costo unitario no es un valor numerico linea $n"))
////                                   parent.document.getElementById('bottomFrame').src = ''
////                                   parent.document.getElementById('contenedor2').rows = '*,0%'
////                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_movimientopt.php'
////                           </script> 
//                        break;
//                    }
                }
            }
        }
        $n++;
    }
    fclose($file);
    return $sms;
}

$check = check_file('../formatos/' . $name);
if (strlen($check) == 1) {
    load_file('../formatos/' . $name, $emisor, $id_cli);
} else {
    echo $check;
}

