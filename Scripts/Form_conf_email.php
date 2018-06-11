<?php
$vl = $_GET[cr];
include_once '../Includes/permisos.php';
$rst_cred = pg_fetch_array($User->lista_configuraciones($vl));
$cred = explode('&', $rst_cred[con_valor2]);
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <link href="../FacturacionElectronica/Scripts/uploadfile.min.css" rel="stylesheet">
        <script src="../FacturacionElectronica/Scripts/jquery.min.js"></script>
        <script src="../FacturacionElectronica/Scripts/jquery.uploadfile.min.js"></script>

        <title>Formulario</title>
        <script>
            var cr = '<?php echo $_GET[cr]; ?>';
            $(function () {
                $("#con_credencial_p12").uploadFile({
                    url: '../FacturacionElectronica/Scripts/files.php',
                    fileName: "archivo",
                    autoUpload: true,
                    showDelete: false,
                    showDone: false,
                    allowedTypes: "p12",
                    dragDrop: false,
                    onSuccess: function (files, data, xhr) {
                        con_credencial_p12_val.value = data;
                    }
                });
            });

            function save() {
                var data = Array(
                        secure.value.toUpperCase(),
                        puerto.value.toUpperCase(),
                        host.value,
                        usuario.value,
                        contrasena.value,
                        emisor.value.toUpperCase(),
                        asunto.value.toUpperCase(),
                        mensaje.value.toUpperCase()
                        );

//                if (secure.value.length == 0) {
//                    $('#secure').css({'border': 'solid 1px red'});
//                    $('#secure').focus();
//                } else
                    if (puerto.value.length == 0) {
                    $('#puerto').css({'border': 'solid 1px red'});
                    $('#puerto').focus();
                } else if (host.value.length == 0) {
                    $('#host').css({'border': 'solid 1px red'});
                    $('#host').focus();
                } else if (usuario.value.length == 0) {
                    $('#usuario').css({'border': 'solid 1px red'});
                    $('#usuario').focus();
                } else if (contrasena.value.length == 0) {
                    $('#contrasena').css({'border': 'solid 1px red'});
                    $('#contrasena').focus();
                } else if (emisor.value.length == 0) {
                    $('#emisor').css({'border': 'solid 1px red'});
                    $('#emisor').focus();
                }
                else {
                    $.post("actions.php", {act: 83, 'data[]': data},
                    function (dt) {
                        if (dt == 0) {
                            alert('Registro de Credenciales Exitoso');
                            redireccionar();
                        } else {
                            alert(dt);
                        }
                    });

                }
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function redireccionar() {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_configuraciones.php';
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script>
        <style>
            .sms{
                color:#000;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="proceso" >   
        </div>
        <div id="cargando"></div>

        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table id="tbl_form">
            <thead>
                <tr><th colspan="9" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>
            <tr>
                <td>SMTPSecure:</td>
                <td>
                    <input type="text" size="30" id="secure" value="<?php echo $cred[0]?>" style="text-transform: uppercase" />
                    <font class="sms">(Sin espacios ni caracteres especiales)</font>
                </td>
            </tr>
            <tr>
                <td>Puerto:</td>
                <td>
                    <input type="text" size="30" id="puerto"  value="<?php echo $cred[1]?>" style="text-transform: uppercase" />
                </td>
            </tr>
            <tr>
                <td>Host:</td>
                <td>
                    <input type="text" size="30" id="host"  value="<?php echo $cred[2] ?>" style="text-transform: lowercase" />
                </td>
            </tr>
            <tr>
                <td>Nombre de Usuario:</td>
                <td>
                    <input type="text" size="30" id="usuario"  value="<?php echo $cred[3]  ?>"  />
                </td>
            </tr>
            <tr>
                <td>Contraseña:</td>
                <td>
                    <input type="password" size="30" id="contrasena"  value="<?php echo $cred[4]  ?>"  />
                </td>
            </tr>
            <tr>
                <td>Nombre del emisor:</td>
                <td>
                    <input type="text" size="30" id="emisor"  value="<?php echo $cred[5]?>"  style="text-transform: uppercase"/>
                </td>
            </tr>
            <tr>
                <td>Asunto:</td>
                <td>
                    <input type="text" size="30" id="asunto"  value="<?php echo $cred[6]?>" style="text-transform: uppercase" />
                </td>
            </tr>
            <tr>
                <td>Cuerpo del Mensaje:</td>
                <td valign="top" rowspan="15" ><textarea id="mensaje" style="width:250px; height:200px;  text-transform: uppercase;"><?php echo $cred[7]?></textarea></td>    
            </tr>
            <tfoot >
                <tr>
                    <td style="padding:10px "><input  type="submit" name="save" id="save" value="Guardar" onclick="save(cr)"/></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>  

