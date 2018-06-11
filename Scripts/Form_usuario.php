<?php
include_once '../Includes/permisos.php';
include_once("../Clases/clsUsers.php");
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($User->listUnUsuario($id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Usuarios</title>
    <head>
        <script>
            $(function () {
                $('#usu_pc').val(<?php echo $rst[usu_pc] ?>);
                parent.document.getElementById('contenedor2').rows = "*,75%";
            })

            function save(id)
            {
                if (usu_pass.value !== usu_pass2.value || usu_pass.value.length == 0 || usu_pass2.value.length == 0)
                {
                    alert('Claves Incorrectas \n Favor Revisar¡');

                } else if (usu_login.value.length == 0) {
                    alert('Login es campo Requerido');
                } else if (usu_person.value.length == 0) {
                    alert('Persona es campo Requerido');
                } else {
                    data = Array(usu_login.value,
                            usu_pass.value,
                            usu_person.value,
                            usu_pc.value);
                    var fields = Array();
                    $("#tbl_form").find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                    $.post("actions.php", {act: 8, 'data[]': data, id: id, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                            parent.document.getElementById('bottomFrame').src = '';
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function cancelar()
            {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
        </script>
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >FORMULARIO DE USUARIO</th></tr>
            </thead>
            <tr>
                <td>Login:</td>
                <td><input type="text" id="usu_login"  value="<?php echo $rst[usu_login] ?>" /></td>
            </tr>
            <tr>
                <td>Clave:</td>
                <td><input type="password" id="usu_pass" required /></td>
            </tr>
            <tr>
                <td>Repita Clave:</td>
                <td>
                    <input type="password" id="usu_pass2" required />
                </td>
            </tr>
            <tr>
                <td>Persona:</td>
                <td><input type="text" id="usu_person"  value="<?php echo $rst[usu_person] ?>" /></td>
            </tr>
            <tr><td colspan="2" style="background:#015b85;height:20px;  color:white;font-weight:bolder;text-align:center">Modulos Especiales</td></tr>
            <tr>
                <td>Clientes/Proveedores</td>
                <td><select id="usu_pc"><option value="2">Ambos</option><option value="0">Clientes</option><option value="1">Proveedores</option></select></td>
            </tr>
            <tr>
                <td colspan="3">
                    <button onclick="save(<?php echo $_GET[id] ?>)">Guardar</button>
                    <button onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    
        </table>
    </body>
</html>