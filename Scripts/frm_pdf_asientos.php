<?php
session_start();
include_once '../Includes/permisos.php';
$id = $_REQUEST['id'];
$asi = $_REQUEST['asi'];
$x = $_REQUEST['x'];
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
            window.history.go();
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
<?php
if (empty($asi)) {
    ?>
    <iframe  src='../Reports/pdf_asientos.php?id=<?php echo $id ?>' width="100%" />    
    <?php
} else {
    if (empty($x)) {
        ?>
        <iframe  src='../Reports/pdf_asientos_fac.php?id=<?php echo $id ?>' width="100%" />    
        <?php
    } else if ($x == 1) {
        ?>
        <iframe  src='../Reports/pdf_asientos_fac_anulada.php?id=<?php echo $id ?>' width="100%" />    
        <?php
    } else if ($x == 2) {
        ?>
        <iframe  src='../Reports/pdf_asientos_ret.php?id=<?php echo $id ?>' width="100%" />    
        <?php
    }
}
?>
</body>