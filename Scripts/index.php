<?php
session_start();
$_SESSION['User'] = null;
$_SESSION['usuid'] = null;
$_SESSION['usuario'] = null;
session_destroy();
session_unset();
header("location:http://www.tikvasystems.com");
?>

