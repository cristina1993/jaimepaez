<?php

session_start();
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
require '../Validate/sessionValidate.php';
include_once("../Clases/clsPermisos.php");
include_once '../Includes/library.php';
include_once '../Clases/clsUsers.php';
$User = new User();
$Prt = new Permisos();
if (!empty($_REQUEST[ol])) {
    $modulo = $_REQUEST[ol];
    $_SESSION[ol] = $_REQUEST[ol];
} else {
    $modulo = $_SESSION[ol];
}
$rst_mod = pg_fetch_array($User->lista_un_opl($modulo));
$mod_id = $rst_mod[mod_id];
$emisor = $rst_mod[emi_id];
$bodega = $rst_mod[emi_nombre_comercial];
$id_cli = $rst_mod[emi_cod_cli];
$pto_emi = $rst_mod[emi_cod_punto_emision];
$rst_dec = pg_fetch_array($User->lista_configuraciones('2'));
$dec = $rst_dec[con_valor];
$rst_cnt = pg_fetch_array($User->lista_configuraciones('1'));
$dc = $rst_cnt[con_valor];
$rst_amb = pg_fetch_array($User->lista_configuraciones('5'));
$amb = $rst_amb[con_valor];
$rst_inv5 = pg_fetch_array($User->lista_configuraciones('3'));
$inv5 = $rst_inv5[con_valor];
$rst_asi = pg_fetch_array($User->lista_configuraciones('4'));
$asi = $rst_asi[con_valor];
$rst_ci = pg_fetch_array($User->lista_configuraciones('6'));
$ctr_inv = $rst_ci[con_valor];



//$cod_cli='CPJ00001';

$Prt->Permit($_SESSION[usuid], $modulo);
$rst_user = pg_fetch_array($User->listUnUsuario($_SESSION[usuid]));
?>
