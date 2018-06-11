<?php
session_start();
include_once '../Includes/permisos.php';
$desde = $_REQUEST[desde];
$hasta = $_REQUEST[hasta];
$nivel = $_REQUEST[nivel];
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
    <iframe  src="../Reports/pdf_balance_comprobacion.php?desde=<?php echo $desde ?> &hasta=<?php echo $hasta ?> &nivel=<?php echo $nivel ?> "  width="100%" /> 
</body>