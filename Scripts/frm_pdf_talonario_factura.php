<?php
session_start();
include_once '../Includes/permisos.php';
$id = $_REQUEST[id];
if (isset($_REQUEST[det])) {
    $det = $_REQUEST[det];
} else {
    $det = '0';
}
?>
<head>
    <script>

        function salir() {
            det =<?php echo $det ?>;
            id = '<?php echo $id ?>';
            if (det == '1') {
                confir_impresion(id);
            } else {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

        }
        function confir_impresion(id)
        {
            var r = confirm("Desea Imprimir RIDE");
            if (r == true) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                frm.src = '../Scripts/frm_pdf_factura2.php?id=' + id;
            } else {
                window.history.go(0);
            }
        }
    </script>
    <style>
        iframe{
            height:87%!important;
        }
        html,body{
            height:100%; 
            overflow:hidden;
        }
        iframe{
            height:87%!important;
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
            <font class="cerrar" onclick="salir()" title="Salir del Formulario">&#X00d7;</font>
        </th>
    </thead>

</table>   
<iframe  src='../Reports/pdf_talonario_factura.php?id=<?php echo $id ?>' width="100%" />           
</body>