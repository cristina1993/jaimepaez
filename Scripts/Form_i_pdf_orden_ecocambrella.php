<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                $("#tbl_form").attr("width", "75%")
                $("#tbl_form").attr("height", "100%")
                $("#frm_etq").attr("width", "99%")
                $("#frm_etq").attr("height", "90%")
            });

            function cerrar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

        </script>
        <style>
            #frm_etq{
                margin: 0px !important;
                height:100% !important; 
                width:100% !important; 
            }
            .auxBtn{
                width:20px; 
            }
        </style>        
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" cellspacing="0" border="0" >
            <tr>
                <th style="height:10px;font-size:14px;padding-top:5px;background:#005580 ;color:white;font-weight:bolder">
                    ORDEN PRODUCCION ECOCAMBRELLA
                </th>
                <th style="background:#005580">
                    <img class="auxBtn" src="../img/error.png" onclick="cerrar()" />
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <iframe id="frm_etq" src="../Reports/pdf_ord_produccion_ecocambrela.php?id=<?php echo $id ?>" ></iframe>
                </td>
            </tr>
        </table>
    </body>
</html>