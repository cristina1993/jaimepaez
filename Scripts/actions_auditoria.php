<?php

//include_once '../Includes/permisos.php';
include_once '../Clases/clsAuditoria.php';
$Clase_auditoria = new Auditoria();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];
switch ($op) {
    case 0:
        $rst = pg_fetch_array($Clase_auditoria->lista_Auditoria($id));
        $aud = "";
        $n = 0;
        $i = substr_count($rst[adt_campo], '&');
        $dt = explode('&', $rst[adt_campo]);
        $i = $i - 1;
        while ($n < $i) {
//            $n++;
            $aud .= "<tr ><td>$n</td><td>$dt[$n]</td></tr>";
            $n++;
        }
        echo $aud;
        break;
}
?>
