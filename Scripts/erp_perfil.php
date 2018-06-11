<?php
include_once("../Includes/permisos.php");
include_once("../Clases/clsUsers.php");
$User = new User();
$id = $_SESSION['usuid'];
$rstUser = pg_fetch_array($User->listUnUsuario($id));
?>
<html>
    <head>
        <script>
            $(function () {
                id =<?php echo $id ?>;
                pass = '<?php echo $rstUser[usu_pass] ?>';
                $('#frm').submit(function (e) {
                    e.preventDefault();
                    save(id, pass);
                })
            })

            function save(id, pass) {
                var dat = Array(usu_clave2.value, usu_clave_ant.value, pass);
                $.ajax({
                    type: 'POST',
                    url: "actions.php",
                    data: {act: 62, 'data[]': dat, id: id},
                    beforeSend: function () {
                        if (usu_clave.value != usu_clave2.value) {
                            $("#usu_clave2").css({borderColor: "red"});
                            $("#usu_clave2").focus();
                            return false;
                        } else {
                            return true;
                        }
                    },
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(-1);
                            //parent.document.getElementById('bottomFrame').src = '';
                        } else {
                            alert(dt);
                        }

                    }
                })
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
            }
        </script>
    </head>
    <body>
        <form action=""  id='frm' autocomplete="off" enctype="multipart/form-data"  method="GET"  >
            <table style="border:solid 3px #005580" cellpadding="0" cellspacing="0">
                <tbody>
                    <tr>
                        <td colspan="2" align="center" style="background:#005580;color:white;font-weight:bolder;"><?php echo 'Perfil de: ' . $rstUser['usu_person'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:5 5 5 5;background:#005580;color:white;" >
                            <?php echo 'Usuario: ' . $rstUser['usu_login'] ?>
                            <input type="hidden" name="usu_login"  value="<?php echo $rstUser['usu_login'] ?>" />
                        </td> 
                    </tr>
                    <tr>
                        <td align="right" >Clave Anterior:*</td>
                        <td>
                            <input type="password"  size="30" maxlength="50" required  id="usu_clave_ant" />
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right" >Nueva Clave:*</td>
                        <td><input type="password"   size="30" maxlength="50" required  id="usu_clave" /></td>
                    </tr>
                    <tr>
                        <td class="" align="right" >Confirme Nueva Clave:*</td>
                        <td><input type="password"  size="30" maxlength="50" required  id="usu_clave2" /></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" ><input type="submit" onClick="" value="Guardar" id="save"></td>
                    </tr>
                </tbody>
            </table>
        </form>
            <div class="alerta" style="text-transform:capitalize;width:30% ">
                <li>La clave que Ud decida utilizar debe ser una que pueda recordar facilmente</li>
                <li>Recuerde que Usted es el administrador y responsable de su cuenta</li>
                <li>Los movimientos que se realizan son guardadas en la Auditoria del Sistema</li>
                <li>Esta Informacion es revisada diariamente por el administrador del sistema</li>    
            </div>
    </body>
</html>
