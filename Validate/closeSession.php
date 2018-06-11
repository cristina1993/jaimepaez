<?php

try {
    session_start();
    $_SESSION['User'] = null;
    $_SESSION['usuid'] = null;
    $_SESSION['usuario'] = null;
    session_destroy();
    session_unset();
//    header("location:http://www.tikvasystems.com");
        header("location:../index.php");
} catch (Exception $e) {
    echo 'Error:', $e->getMessage();
}
?>
