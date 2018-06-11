<?php
//session_start();
$a = $_REQUEST[anio];
$m = $_REQUEST[mes];
$e = $_REQUEST[emi_id];
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
        html,
        body{
            height:100%; 
            overflow:hidden;
        }
        iframe{
            height:87%!important;
        }
        .cerrar{
            position:absolute; 
            top:-10px;
            right:0px; 


            width:24px;
            font-size:18px;  
            font-weight:bolder; 
            padding:5px; 
            border-radius:2px; 
            background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */
            cursor:pointer; 
            color:white;
            font-weight:bolder; 
            float:right; 
            text-align:center; 
        }
    </style>
</head>
<body>
    <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>  
    <br>
    <iframe  src='http://186.4.200.125/tivka/ats/resumen?anio=<?php echo $a?>&mes=<?php echo $m?>&emi_id=<?php echo $e?>' width="100%" />          
</body>
