<?php

include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciosmp.php';
include_once '../Clases/clsAuditoria.php';
$Prod = new Clase_preciosmp();
$file = $_FILES[file];
$file[tmp_name];
$name = date('Ymd') . '_prec.csv';
move_uploaded_file($file[tmp_name], '../formatos/' . $name);
$emisor = 2;

function load_file($file) {
    $Prod = new Clase_preciosmp();
    $Aud = new Auditoria();
    $arch = $file;
    $file = fopen($file, "r") or die("Error de Archivo" . $emisor);
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
            $rst_prod = pg_fetch_array($Prod->lista_productos_cod(trim($row[0])));
            $pro_id = $rst_prod[id];
            $pre_precio = $row[1];
            $pre_descuento = $row[2];
            $pre_iva = trim($row[3]);
            $pre_precio2 = '0';
            $data = array(
                $pre_precio,
                $pre_precio2,
                $pre_descuento,
                $pre_iva,
            );
            if (strlen(trim($row[0])) > 0) {
                if (!$Prod->upd_precios($data, $pro_id)) {
                    $err;
                    echo "<script>
                                   alert('$err Linea $n')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php?'
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
           parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php?'
          </script> ";
}

function check_file($file) {
    $sms = 0;
    $Prod = new Clase_preciosmp();
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
            $chr = array('*', '=', '/', "'", '"');
            if (strlen(trim($row[0]))) {
                foreach ($chr as $val) {
                    $pos0 = strpos($row[3], $val);
                    if ($pos0 == true) {
                        $vl = 1;
                    }
                }
                if ($vl == 1) {
                    $sms = "<script>
                                   alert('No se acepta caracteres especiales linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp'
                           </script>";
                    break;
                } else {
                    $rst_prod = pg_fetch_array($Prod->lista_productos_cod(trim($row[0])));
                    if (empty($rst_prod)) {
                        $sms = "<script>
                            alert('No existe codigo linea $n ');
                            parent.document.getElementById('bottomFrame').src = '';
                            parent.document.getElementById('contenedor2').rows = '*,0%';
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php';
                            </script>";
                        break;
                    }

                    if (is_numeric($row[1])) {
                        
                    } else {
                        $sms ="
                        <script>
                                   alert('El campo precio no es un valor numerico linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php'
                                </script>";
                        break;
                    }
                    if (is_numeric($row[2])) {
                        
                    } else {
                        $sms = "
                        <script>
                                   alert('El campo descuento no es un valor numerico linea $n ')
                                   parent.document.getElementById('bottomFrame').src = ''
                                   parent.document.getElementById('contenedor2').rows = '*,0%'
                                   parent.document.getElementById('mainFrame').src = '../Scripts/Lista_precios_mp.php'
                                </script>";
                        break;
                    }
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
    load_file('../formatos/' . $name);
} else {
    echo $check;
}

    