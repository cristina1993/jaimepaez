<?php
session_start();
include_once '../Includes/permisos.php';
$dec = $_REQUEST['dec'];
$id = $_REQUEST['id'];
$mod = $_REQUEST['mod'];
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
    <?php
    if ($mod == '292') {
        ?>
        <table style="width:100% ">
            <thead>
            <th>
                <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>  
            </th>
        </thead>
    </table>
    <?php
}
?>
<iframe  src='../Reports/pdf_std_cta_cliente.php?id=<?php echo $id ?>&dec=<?php echo $dec ?>' width="100%" />           
</body>