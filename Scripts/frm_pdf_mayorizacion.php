<?php
session_start();
include_once '../Includes/permisos.php';
$desde = $_REQUEST[desde];
$hasta = $_REQUEST[hasta];
$cuenta = $_REQUEST[cuenta];
?>
<head>
    <script>
        function salir() {
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
    <iframe  src="../Reports/pdf_mayorizacion.php?desde=<?php echo $desde ?> &hasta=<?php echo $hasta ?> &cuenta=<?php echo $cuenta?> "  width="100%" />           
</body>