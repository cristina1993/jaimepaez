<?php
session_start();
include_once '../Clases/clsClase_multimedia.php';
$Mltm = new Multimedia();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Mltm->lista_un_multimedia($id));
    $code = $rst[mlt_code];
} else {
    $rst[mlt_fecha] = date('Y-m-d');
    $rst[mlt_user] = $_SESSION[usuario];
    $rst_sec = pg_fetch_array($Mltm->lista_sec_codigo());
    $sec = ($rst_sec[mlt_cod] + 1);
    $tx = '000000000';
    $code = substr($tx, 0, (10 - strlen($sec))) . $sec;
    $id = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Multimedia</title>
        <meta charset="UTF-8" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/ajaxupload-min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script>
            $(function () {
                $('#uploader_div').ajaxupload({
                    url: 'upload.php',
                    remotePath: 'Archivos/',
                    maxFileSize: '10G',
                    hideUploadButton: true,
                    maxFiles: 1,
                    autoStart: true,
                    checkFileExists: true,
                    allowExt: ['pdf'],
                    validateFile: function (name, extension, size) {
                        if (extension !== 'pdf') {
                            alert('Solo puede subir archivos PDF');
                        }
                    },
                    onInit: function () {
                        this.removeFiles.hide();
                        this.uploadFiles.hide();
                    }
                });
                parent.document.getElementById('contenedor2').rows = "*,80%";
            });

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_multimedia_adm.php';
            }

            function save() {
                dat = Array();
                $('.ax-file-name').each(function () {
                    dat.push(this.innerHTML);
                });
                dat.push(mlt_cod.value);
                dat.push(mlt_fecha.value);
                dat.push(mlt_user.value);
                dat.push(mlt_informacion.value);
                $.ajax({
                    type: 'POST',
                    url: "../Scripts/actions_multimedia.php",
                    data: {op: 0, 'data[]': dat, id: 0},
                    beforeSend: function () {
                        if (dat.length == 0) {
                            alert('Debe subir almenos un archivo');
                            $('#mlt_informacion').focus();
                        } else if (mlt_informacion.value.length == 0) {
                            alert('Informacion es campo obligatorio')
                            $('#mlt_informacion').focus();
                            return false;
                        }
                    },
                    success: function (dt) {
                        if(dt==0){
                            cancelar();
                        }else{
                            alert(dt)
                        }
                    }
                });

            }

        </script>    
        <style>
            .cerrar{
                width:24px;
                padding:3px 10px;; 
                border-radius:2px; 
                color:white !important;                
                font-weight:bolder !important; 
                font-size:18px !important; 
                background: linear-gradient(to bottom, #f0b7a1 0%,#8c3310 50%,#752201 51%,#bf6e4e 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0b7a1', endColorstr='#bf6e4e',GradientType=0 ); /* IE6-9 */
                cursor: pointer;
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
            .ax-toolbar{
                display:none; 
            }
        </style>
    </head>
    <body>
        <div>
            <div class="line"></div>
            <table class="options" border="1">
                <thead>
                    <tr>
                        <th align="right">
                            <input type="text" readonly size="20" style="float:left" id="mlt_cod" value="<?php echo $code ?>" />
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <div id="uploader_div">
                        </div>
                    </td>
                </tr>
                <?php
                if ($id != 0) {
                    ?>
                    <tr>
                        <td>
                            <div>
                                <img src="../img/orden.png" />
                                <?php echo $rst[mlt_archivo] ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>
                        <textarea id="mlt_informacion" style="width:99%" rows="4"><?php echo $rst[mlt_informacion] ?></textarea><br>
                    </td>
                    <td style="display:none">
                        <input id="mlt_fecha" size="10" readonly value="<?php echo $rst[mlt_fecha] ?>" />
                        <input id="mlt_user" size="" value="<?php echo $rst[mlt_user] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="button" value="Guardar" onclick="save()" />
                        <input style="float:right " type="button" value="Cancelar" />
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>	