<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Tivka Systems</title>
        <meta name="description" content="Index">
        <meta name="author" content="Tikva Systemas">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">   
        <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>                
        <script type="text/javascript" src="js/jquery.min.js"></script> 
        <link rel="shortcut icon" type="image/x-icon" href="img/icono_tivka.ico" />
        <script>
            $(function () {
                $('#pass').attr('checked', true);
                $('#user').focus();
                cambia_forma();
            });
            function documento_electronico(e) {
                e.preventDefault();
                if (pass.checked == true) {
                    op = 0;
                } else {
                    op = 1;
                }
                cli = identificacion.value;
                
                tpd = tp_doc.value;
                ndoc = n_doc.value;
                iframe_doc.src = 'Scripts/Lista_documentos_electronicos.php?op=' + op + '&cli=' + cli + '&tpd=' + tpd + '&ndoc=' + ndoc;
            }
            function cambia_forma() {
                if (pass.checked == true) {
                    cont_tipo_doc.hidden = true;
                    txt_clave.innerHTML = 'Clave:';
                } else {
                    cont_tipo_doc.hidden = false;
                    txt_clave.innerHTML = '#_Doc:';
                }
            }
            function tipo_documento(obj) {
                switch (obj.value)
                {
                    case '1':
                        n_doc.placeholder = 'Numero de Factura';
                        break;
                    case '4':
                        n_doc.placeholder = 'Numero de Nota de Credito';
                        break;
                    case '5':
                        n_doc.placeholder = 'Numero de Nota de Debito';
                        break;
                    case '6':
                        n_doc.placeholder = 'Numero de Guia de Remision';
                        break;
                    case '7':
                        n_doc.placeholder = 'Numero de Retencion';
                        break;
                }
            }

            function save() {
                var data = Array(nombre.value.toUpperCase(),
                        empresa.value.toUpperCase(),
                        email.value,
                        comentarios.value.toUpperCase()
                        );
                fields = $('#frm_acceso').serialize();
                $.ajax({
                    beforeSend: function () {
                        nom = nombre.value.split(' ');
                        if ($('#nombre').val().length == 0 || nom[1] == null) {
                            $('#nombre').css({borderColor: "red"});
                            $('#nombre').focus();
                            return false;
                        } else if ($('#empresa').val().length == 0) {
                            $('#empresa').css({borderColor: "red"});
                            $('#empresa').focus();
                            return false;
                        } else if ($('#email').val().length == 0) {
                            $('#email').css({borderColor: "red"});
                            $('#email').focus();
                            return false;
                        } else if ($('#comentarios').val().length == 0) {
                            $('#comentarios').css({borderColor: "red"});
                            $('#comentarios').focus();
                            return false;
                        } else {
                            $('#sms').html('<img src="img/load_circle.gif" id="loading" style="width:32px" />');
                        }
                    },
                    type: 'POST',
                    url: 'Scripts/actions_acceso.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            $('#sms').html('Su solicitud ha sido enviada, pronto nos pondremos en contacto');
                            $('#nombre').val('');
                            $('#empresa').val('');
                            $('#email').val('');
                            $('#comentarios').val('');
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }

        </script>
        <style>
            select{
                margin: 1px 0 0 1px;
                padding-left: 10px;	
            }
            textarea{
                margin: 1px 0 0 1px;
                padding-left: 10px;	
            }
            #loading{
                background:brown;
                border-radius:7px; 
                padding:3px;
                border:solid 1px cadetblue; 
            }
        </style>
    </head>

    <body>
        <table align="center" style="width: auto" border="0">
            <tr><td>
                    <div id="tags" class="container"> <!-- Tags -->
                        <div class="bar title-bar">
                            <h2>Facturación Electrónica</h2>
                        </div>
                        <table border="0" id="tbl_left" style="padding-right:10px;padding-left:10px;  ">
                            <tr>
                                <td>Clave:</td>
                                <td><input type="radio" name="opcion" id="pass" onclick="cambia_forma()" />&nbsp;&nbsp;Tipo Documento:<input type="radio" name="opcion" id="doc" onclick="cambia_forma()"  /></td>
                            </tr>
                            <br/>
                            <tr>
                                <td>Cliente:</td>
                                <td><input type="text" name="identificacion" id="identificacion" placeholder="Cedula / Ruc" ></td>
                            </tr>
                            <tr id="cont_tipo_doc" disabled>
                                <td>T_Doc:</td>
                                <td>
                                    &nbsp;<select name="tp_doc" id="tp_doc" onchange="tipo_documento(this)" >
                                        <option value="1">Factura</option>
                                        <option value="4">Nota Credito</option>
                                        <option value="5">Nota Debito</option>
                                        <option value="6">Guia Remision</option>
                                        <option value="7">Retencion</option>
                                    </select>                                            
                                </td>
                            </tr>                                    
                            <tr>
                                <td id="txt_clave">Clave:</td>
                                <td><input type="text" name="n_doc" id="n_doc" placeholder="Numero de Factura" ></td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit"  class="btn" onclick="documento_electronico(event)" value="Obtener" id="obtener"  /></td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td>
                    <div id="tags" class="container" > <!-- Tags -->
                        <img style="margin-left:10px; " class="img_logo" width="250px" src="img/logo_noperti.jpg" />
                    </div>
                </td>
                <td>
                    <div id="left-container"> <!-- Left part -->
                        <div id="tags" class="container"> <!-- Tags -->
                            <div class="bar title-bar">
                                <h2>Ingreso al Sistema</h2>
                            </div>
                            <form method="post" id="frm_login" action="Validate/userValidate.php" autocomplete="off">
                                <table border="0" style="padding-left:0px;padding-top:10px  ">
                                    <tr><td>Usuario:</td><td><input type="text" name="user" id="user"></td></tr>
                                    <tr><td>Clave:</td><td><input type="password" name="pass" id="pass"></td></tr>
                                    <tr><td colspan="2">&nbsp;</td></tr>
                                    <tr><td colspan="2"></td></tr>
                                    <tr><td colspan="2"><input type="submit" class="btn" value="Entrar" id="enter" /></td></tr>
                                </table>
                            </form>                                
                        </div>
                    </div>
                </td></tr>
            <tr><td colspan="3" >
                    <div class="bar title-bar">
                        <h2 style="text-align:center ">Documentos Electrónicos</h2>
                    </div>
                </td></tr>

            <tr>
                <td colspan="3" >
                    <div id="left-container"> <!-- Left part -->
                        <div id="frm_set" >
                            <iframe id="iframe_doc"  width="100%" height="100%" frameborder="0" ></iframe>
                        </div>

                    </div>
                </td>
            </tr>
        </table>                        


    </body>
</html>
