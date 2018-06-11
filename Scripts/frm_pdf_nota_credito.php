<?php
session_start();
include_once '../Includes/permisos.php';
$id = $_REQUEST['id'];
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
    </style>
</head>
<body>
    <table>
        <thead>
        <th align="left">
            <button id="salir" onclick="salir()">SALIR</button> 
        </th>
    </thead>

</table>   
<iframe  src='../Reports/pdf_nota_credito.php?id=<?php echo $id ?>' width="100%" />           
</body>