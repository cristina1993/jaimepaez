<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_vendedores.php';

$Clase_vendedores = new Clase_vendedores();
$cns_com = $Clase_vendedores->lista_locales();
$txt = $_GET[txt];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_vendedores->lista_un_vendedor($id));
    $num = $rst[vnd_local];
    $rst1 = pg_fetch_array($Clase_vendedores->lista_usuario($num));
    $num1 = $rst1[usu_person];
} else {
    $id = 0;
    $rst['reg_fecha'] = date('Y-m-d');
    $rst['mov_cantidad1'] = 0;
    $fila = 0;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
            });
            function save(id) {

                var data = Array(
                        vnd_codigo.value,
                        vnd_nombre.value,
                        vnd_local.value
                        );
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (vnd_codigo.value.length == 0) {
                            $("#vnd_codigo").css({borderColor: "red"});
                            $("#vnd_codigo").focus();
                            return false;
                        }
                        else if (vnd_nombre.value.length == 0) {
                            $("#vnd_nombre").css({borderColor: "red"});
                            $("#vnd_nombre").focus();
                            return false;
                        }
                        else if (vnd_local.value.length == 0) {
                            $("#vnd_local").css({borderColor: "red"});
                            $("#vnd_local").focus();
                            return false;
                        }

                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_vendedores.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                            loading('hidden');
                        }
                    }
                })
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_vendedores.php?txt=' + '<?php echo $txt ?>';
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function vendedor(obj) {
                usu = obj.value;
                $.post("actions_vendedores.php", {op: 2, id: usu},
                function (dt) {
                    dat = dt.split('&');
                    $('#vnd_nombre').val(dat[0]);
                });
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>CODIGO:</td>
                    <td><input type="text" size="28"  id="vnd_codigo" value="<?php echo $rst[vnd_codigo] ?>" onblur="this.value = this.value.toUpperCase()"/></td>
                </tr>
                <tr>
                    <td>USUARIO:</td>
                    <td> 
                        <select id="vnd_local" class="select" onblur="vendedor(this)">
                            <option value="">SELECCIONAR</option>
                            <?PHP
                            $cns_com = $Clase_vendedores->lista_usuarios();
                            while ($rst_tp = pg_fetch_array($cns_com)) {
                                ?>
                                <option value='<?php echo $rst_tp[usu_id] ?>'><?php echo $rst_tp[usu_person] ?></option>
                                <?PHP
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>VENDEDOR:</td>
                    <td><input type="text" size="28"  id="vnd_nombre" value="<?php echo $rst[vnd_nombre] ?>" onblur="this.value = this.value.toUpperCase()"/>
                </tr>
                <tfoot>
                    <tr><td colspan = "2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar"> Guardar</button>    
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>
<script>
    $('#vnd_local').val("<?php echo $num1 ?>");
</script>


