<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set=new Set();
$id=$_GET[id];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title></title>
<head>
       <script>
$(function(){
    $("#tbl_form").attr("width","50%") 
    $("#tbl_form").attr("height","100%") 
    $("#frm_etq").attr("width","99%") 
    $("#frm_etq").attr("height","90%") 
});
           
function cerrar(){
    mnu=window.parent.frames[0].document.getElementById('lock_menu');  
    mnu.style.visibility="hidden";
    grid=window.parent.frames[1].document.getElementById('grid');  
    grid.style.visibility="hidden";
    parent.document.getElementById('bottomFrame').src='';
}
        </script>
        <style>
            #frm_etq{
             margin-top:-25px; 
             margin-left:0px; 
            }
            .auxBtn{
              width:20px; 
            }
        </style>        
</head>
<body>
    <table id="tbl_form" cellpadding="0" >
        <thead>
            <tr>
                <th colspan="3" style="height:30px;">
                    ETIQUETA DE CONTROL DE  MATERIA PRIMA
                    <img class="auxBtn" src="../img/error.png" onclick="cerrar()" />
                </th>
            </tr>
        </thead>
        <tr>
            <td>
                <iframe id="frm_etq" src="../Reports/etq_ord_compra.php?id=<?php echo $id?>" ></iframe>
            </td>
        </tr>
    </table>
</body>
</html>