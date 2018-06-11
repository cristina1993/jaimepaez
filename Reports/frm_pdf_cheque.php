<?php
session_start();
include_once '../Includes/permisos.php';
$id=$_REQUEST['id'];
$nom=$_REQUEST['nombre'];
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
    <iframe  src='../Reports/pdf_cheque.php?id=<?php echo $id?>&nombre=<?php echo $nom?>' width="100%" />           
</body>