<?php

include_once '../Clases/clsAuditoria.php';
include_once '../Clases/clsClase_config_cuentas.php';
$Set = new Clase_config_cuentas();
$op = $_REQUEST[op];
$data = $_REQUEST[data];
$id = $_REQUEST[id];

switch ($op) {
    case 0:
        $sms = 0;
        $n = 0;
        $i = count($data);
        while ($n < $i) {
            $dt = explode('&', $data[$n]);
            if ($Set->update_conf_ctas($dt[0], $dt[1])==false) {
                $sms = pg_last_error();
            }
            $n++;
        }
        echo $sms;
        break;

    case 1:
        $cta = pg_fetch_array($Set->lista_plan_cuentas_id($id));
        echo $cta[pln_id] . '&' . $cta[pln_codigo] . '&' . $cta[pln_descripcion] . '&' . $cta[pln_estado] ;
        break;
}
?>
