<?php
session_start();
include_once '../Includes/permisos.php';
$desde = $_REQUEST[desde];
$hasta = $_REQUEST[hasta];
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
        thead{
            background:#11658d; 
            color:white; 
        }
    </style>
</head>
<body>
    <table style="width:100% ">
        <thead>
            <tr>
                <th>
                    <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>  
                </th>
            </tr>
    </thead>
</table>   
    <iframe  src="../Reports/pdf_libro_diario.php?desde=<?php echo $desde?>&hasta=<?php echo $hasta?> " width="100%"  />
</body>