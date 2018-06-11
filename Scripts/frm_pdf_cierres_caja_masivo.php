<?php
session_start();
include_once '../Includes/permisos.php';
$emisor = $_GET[emi];
$user = $_GET[user];
$fecha = $_GET[fec];
?>
<head>
    <script>
        function salir() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
    
    </script>
<style>
   html,body{
        height:100%; 
        overflow:hidden;
    }
    iframe{
        height:87%!important;
    }
    form{
        margin-top:-10px;  
    }
    .cerrar{
        color:white; 
        cursor:pointer; 
    }
</style>
</head>
<body>
    <table style="width:100% ">
    <thead>
    <th>
        <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>
    </th>
    </thead>
    
</table>   
    <iframe  src='../Reports/pdf_cierres_caja_masivo.php?emisor=<?php echo $emisor ?> &user=<?php echo $user ?> &fecha=<?php echo $fecha ?>' width="100%" />           
</body>