<?php
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_start();
include_once '../Validate/sessionValidate.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo 'DPVWORLD'.' / '.$_SESSION[usuario]?></title>
        <script type="text/javascript" src="../js/jquery-1.6.2.min.js"></script>                
        <script type="text/javascript" src="../js/jquery.min.js"></script>             
        <script>
            $(function () {
//                $('#contenedor').hide();
//                if ($.browser.device = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()))) {
//                    window.location='menu_movil.php';
//                }else{
//                    $('#contenedor').show();
//                }
            });
        </script>        
        <style>
            *{
                font-family: Constantia, Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif;
                color: #416c80;
                overflow-y: scroll;
                overflow-x: hidden;
                background:#ccc; 
            }
        </style>
    </head>
    <frameset id="contenedor" cols="125,*"  frameborder="No" border="0" framespacing="0">
            <frame src="menu.php" name="leftFrame" scrolling="Yes" noresize="noresize" id="leftFrame" title="leftFrame" />
            <frameset id="contenedor2" rows="*,50%" cols="*"  frameborder="YES" border="2"  >
                <frame src="" name="mainFrame" id="mainFrame" title="mainFrame" />
                <frame src="" name="bottomFrame" scrolling="Yes"  id="bottomFrame" />
            </frameset>
        </frameset>
    <body>
    </body>
</html>
