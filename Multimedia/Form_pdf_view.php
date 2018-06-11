<?php 
$id=$_GET[id];
?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/ajaxupload-min.js"></script>
<link rel="stylesheet" type="text/css" href="css/style.css" />

<script>
    $(function () {
        parent.document.getElementById('contenedor2').rows = "*,80%";
    });
    function cancelar() {
        mnu = window.parent.frames[0].document.getElementById('lock_menu');
        mnu.style.visibility = "hidden";
        grid = window.parent.frames[1].document.getElementById('grid');
        grid.style.visibility = "hidden";
        parent.document.getElementById('bottomFrame').src = '';
        parent.document.getElementById('contenedor2').rows = "*,0%";
    }

</script>
<style>
    .cerrar{
        margin-top:-10px; 
        padding:3px 8px; 
        border-radius:2px; 
        color:white !important;                
        font-weight:bolder !important; 
        font-size:18px !important; 
        background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */
        cursor: pointer;
        float:right; 
    }
    .cerrar:hover{
        background: linear-gradient(to bottom, #f0b7a1 10%,#8c3310 45%,#752201 51%,#bf6e4e 90%); /* W3C */
    }
    table thead th{
        padding: 3px 10px;  
        background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #63b8ff), color-stop(1, #00529B) );
        background:-moz-linear-gradient( center top, #63b8ff 5%, #00529B 100% );
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#63b8ff', endColorstr='#00529B');
        color:#FFFFFF; 
        font-size: 12px; 
        font-weight: bold; 
        border-left: 1px solid #f8f8f8;
        border-collapse: collapse;
        cursor:pointer; 
        height:25px; 
    }
    iframe{
        width:100%  !important;
        height:90% !important; 
    }
</style>
<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
<iframe src='<?php echo 'Archivos/'.$id?>' />
    
    

