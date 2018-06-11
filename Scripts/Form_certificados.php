<?php
$vl=$_GET[cr];
include_once '../Includes/permisos.php';
$rst_cred = pg_fetch_array($User->lista_configuraciones($vl));
$cred=  explode('&',$rst_cred[con_valor2]);
$alias=$cred[0];
$clave=$cred[1];
$p12=$cred[2];
$cer=$cred[3];
//echo $rst_cred[con_nombre];
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
            var cr='<?php echo $_GET[cr];?>';
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

            function save(cr) {
                var data = Array(
                        con_credencial_alias.value.toUpperCase(),
                        con_clave_certificado.value,
                        con_credencial_p12_val.value );

                if (con_credencial_alias.value.length == 0) {
                    $('#con_credencial_alias').css({'border': 'solid 1px red'});
                    $('#con_credencial_alias').focus();
                } else if (con_clave_certificado.value.length == 0) {
                    $('#con_clave_certificado').css({'border': 'solid 1px red'});
                    $('#con_clave_certificado').focus();
                } else if (con_credencial_p12_val.value.length == 0) {
                    $('#con_credencial_p12_val').css({'border': 'solid 1px red'});
                    $('#con_credencial_p12_val').focus();
                } else {
                    $.post("../FacturacionElectronica/Scripts/accions.php", {op:cr, 'data[]': data},
                    function (dt) {
                        if(dt==0){
                            alert('Registro de Credenciales Exitoso');
                            redireccionar();
                        }else{
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
                <td>Nombre de la Empresa:</td>
                <td>
                    <input type="text" size="30" id="con_credencial_alias" value="<?php echo $alias ?>" style="text-transform: uppercase" />
                    <font class="sms">(Sin espacios ni caracteres especiales)</font>
                </td>
            </tr>
            <tr>
                <td>Clave de Firma digital:</td>
                <td>
                    <input type="password" size="30" id="con_clave_certificado"  value="<?php //echo $clave ?>"  />
                </td>
            </tr>
            <tr>
                <td>Archivo de Firma (.p12):</td>
                <td>
                    <div id="con_credencial_p12">...</div>        
                    <input type="text" id="con_credencial_p12_val" size="50px" value="<?php echo $p12 ?>"  readonly style="font-size:10px;height:20px;border:none"/>
                </td>
            </tr>
            <tfoot >
                <tr>
                    <td style="padding:10px "><input  type="submit" name="save" id="save" value="Guardar" onclick="save(cr)"/></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>  

