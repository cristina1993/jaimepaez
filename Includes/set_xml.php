<?php

session_start();
set_time_limit(0);
date_default_timezone_set('America/Guayaquil');
include_once '../Clases/clsUsers.php';
$User = new User();
$rst_am = pg_fetch_array($User->lista_configuraciones('5'));
$xml_generator = 1; //Genera XML 0  Envia XML 1
$ambiente = $rst_am[con_valor];//Pruebas 1    Produccion 2
$codigo = "12345678"; //Del ejemplo del SRI
$tp_emison = "1"; //Emision Normal
$parametros = "<parametros>" .
        "<keyStore>/usr/lib/jvm/jre/lib/security/cacerts</keyStore>" .
        "<keyStorePassword>changeit</keyStorePassword>" .
        "<ambiente>" . $ambiente . "</ambiente>" .
        "<pathFirma>/var/www/FacturacionElectronica/usr006.p12</pathFirma>" .
        "<passFirma>Noperti1952</passFirma>" .
        "</parametros>";

function string($string) {

    $string = trim($string);

    $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
    );

    $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
    );

    $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
    );

    $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
    );

    $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
    );

    $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
            array("\\", "¨", "º", "-", "~",
        "#", "@", "|", "!", "\"",
        "·", "$", "%", "&", "/",
        "(", ")", "?", "'", "¡",
        "¿", "[", "^", "`", "]",
        "+", "}", "{", "¨", "´",
        ">", "< ", ";", ",", ":",
        "."), '', $string
    );


    return $string;
}

if (!empty($_REQUEST[ol])) {
    $modulo = $_REQUEST[ol];
    $_SESSION[ol] = $_REQUEST[ol];
} else {
    $modulo = $_SESSION[ol];
}
//$rst_mod = pg_fetch_array($User->lista_un_opl($modulo));
//$mod_id = $rst_mod[mod_id];
$rst_mod = pg_fetch_array($User->lista_un_opl($modulo));
$mod_id = $rst_mod[mod_id];
$emisor = $rst_mod[emi_id];
$bodega = $rst_mod[emi_nombre_comercial];
$id_cli = $rst_mod[emi_cod_cli];
$pto_emi = $rst_mod[emi_cod_punto_emision];
//switch ($mod_id) {
//    case 31:$emisor = 1;
//        $bodega = 'NOPERTI';
//        $cod_cli = 'CPJ00001';
//        $id_cli = 3;
//        break;
//    case 32:$emisor = 1;
//        $bodega = 'NOPERTI';
//        $cod_cli = 'CPJ00001';
//        $id_cli = 3;
//        break;
//    case 33:$emisor = 10;
//        $bodega = 'INDUSTRIAL';
//        $cod_cli = 'CPJ00002';
//        $id_cli = 2;
//        break;
//    case 34:$emisor = 10;
//        $bodega = 'INDUSTRIAL';
//        $cod_cli = 'CPJ00002';
//        $id_cli = 2;
//        break;
//    case 35:$emisor = 2;
//        $bodega = 'CONDADO';
//        $cod_cli = 'CPJ00010';
//        $id_cli = 49;
//        break;
//    case 36:$emisor = 2;
//        $bodega = 'CONDADO';
//        $cod_cli = 'CPJ00010';
//        $id_cli = 49;
//        break;
//    case 37:$emisor = 3;
//        $bodega = 'QUICENTRO SUR';
//        $cod_cli = 'CPJ00003';
//        $id_cli = 41;
//        break;
//    case 38:$emisor = 3;
//        $bodega = 'QUICENTRO SUR';
//        $cod_cli = 'CPJ00003';
//        $id_cli = 41;
//        break;
//    case 39:$emisor = 4;
//        $bodega = 'MALL DEL SOL';
//        $cod_cli = 'CPJ00004';
//        $id_cli = 43;
//        break;
//    case 40:$emisor = 4;
//        $bodega = 'MALL DEL SOL';
//        $cod_cli = 'CPJ00004';
//        $id_cli = 43;
//        break;
//    case 43:$emisor = 5;
//        $bodega = 'SHOPPING MACHALA';
//        $cod_cli = 'CPJ00005';
//        $id_cli = 44;
//        break;
//    case 44:$emisor = 5;
//        $bodega = 'SHOPPING MACHALA';
//        $cod_cli = 'CPJ00005';
//        $id_cli = 44;
//        break;
//    case 45:$emisor = 6;
//        $bodega = 'RIOCENTRO NORTE';
//        $cod_cli = 'CPJ00006';
//        $id_cli = 45;
//        break;
//    case 46:$emisor = 6;
//        $bodega = 'RIOCENTRO NORTE';
//        $cod_cli = 'CPJ00006';
//        $id_cli = 45;
//        break;
//    case 47:$emisor = 7;
//        $bodega = 'SAN MARINO SHOPPING';
//        $cod_cli = 'CPJ00007';
//        $id_cli = 46;
//        break;
//    case 48:$emisor = 7;
//        $bodega = 'SAN MARINO SHOPPING';
//        $cod_cli = 'CPJ00007';
//        $id_cli = 46;
//        break;
//    case 49:$emisor = 8;
//        $bodega = 'CITY MALL';
//        $cod_cli = 'CPJ00008';
//        $id_cli = 47;
//        break;
//    case 50:$emisor = 8;
//        $bodega = 'CITY MALL';
//        $cod_cli = 'CPJ00008';
//        $id_cli = 47;
//        break;
//    case 51:$emisor = 9;
//        $bodega = 'QUICENTRO SHOPPING';
//        $cod_cli = 'CPJ00009';
//        $id_cli = 48;
//        break;
//    case 52:$emisor = 9;
//        $bodega = 'QUICENTRO SHOPPING';
//        $cod_cli = 'CPJ00009';
//        $id_cli = 48;
//        break;
//    
//    case 67:$emisor = 11;
//        $bodega = 'TOP TENIS';
//        $cod_cli = 'CPJ00011';
//        $id_cli = 3217;
//        break;
//    case 68:$emisor = 11;
//        $bodega = 'TOP TENIS';
//        $cod_cli = 'CPJ00011';
//        $id_cli = 3217;
//        break;
//    case 69:$emisor = 12;
//        $bodega = 'RECREO';
//        $cod_cli = 'CPJ00015';
//        $id_cli = 3218;
//        break;
//    case 70:$emisor = 12;
//        $bodega = 'RECREO';
//        $cod_cli = 'CPJ00015';
//        $id_cli = 3218;
//        break;
//    case 71:$emisor = 13;
//        $bodega = 'CCNU';
//        $cod_cli = 'CPJ00016';
//        $id_cli = 3219;
//        break;
//    case 72:$emisor = 13;
//        $bodega = 'CCNU';
//        $cod_cli = 'CPJ00016';
//        $id_cli = 3219;
//        break;
//    case 73:$emisor = 14;
//        $bodega = 'ATAHUALPA';
//        $cod_cli = 'CPJ00017';
//        $id_cli = 3220;
//        break;
//    case 74:$emisor = 14;
//        $bodega = 'ATAHUALPA';
//        $cod_cli = 'CPJ00017';
//        $id_cli = 3220;
//        break;
//    default :
//        $emisor=0;
//        break;
//}
?>
