<?php
session_start();
//$url=  explode('/',$_SERVER['REQUEST_URI']);
//if(empty($_SESSION['User']) or empty($_SESSION['usuid'])  or ($_SESSION['company']!=$url[1] )  ){
//    $_SESSION['User']='';    
//    $_SESSION['usuid']='';
//    $_SESSION['usuario']='';
//    session_destroy();
//    header("location:../index.php");
//}

if(empty($_SESSION['User']) or empty($_SESSION['usuid'])){
    $_SESSION['User']='';    
    $_SESSION['usuid']='';
    $_SESSION['usuario']='';
    session_destroy();
    header("location:../index.php");
}
?>
