<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_tipo_de_pago($id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            function save(id)
            {
                if (tip_codigo.value.length == 0) {
                    alert('Codigo es campo obligatorio')
                    tip_codigo.focus();
                    tip_codigo.style.borderColor = "red";
                } else if (tip_descripcion.value.length == 0) {
                    alert('Descripción es campo obligatorio')
                    tip_descripcion.focus();
                    tip_descripcion.style.borderColor = "red";
                } else if (tip_descuento.value.length == 0) {
                    alert('Descuento es campo obligatorio')
                    tip_descuento.focus();
                    tip_descuento.style.borderColor = "red";
                } else {
                    var data = Array(
                            tip_codigo.value.toUpperCase(),
                            tip_descripcion.value.toUpperCase(),
                            tip_descuento.value)
                    $.post("actions.php", {act: 36, 'data[]': data, id: id},
                    function(dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                }

            }
            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
            }
        </script>
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >Tipo de Pago</th></tr>
            </thead>
            <tr>
                <td>Codigo:</td>
                <td><input type="text" name="tip_codigo" id="tip_codigo" size="10" value="<?php echo $rst[tip_codigo] ?>" /></td>
            </tr>
            <tr>
                <td>Descripcion:</td>
                <td><input type="text" name="tip_descripcion" id="tip_descripcion" size="45" value="<?php echo $rst[tip_descripcion] ?>" /></td>
            </tr>
            <tr>
                <td>% Descuento:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="tip_descuento" id="tip_descuento" size="10" value="<?php echo $rst[tip_descuento] ?>" /></td>
            </tr>                    
            <tr>
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>

                    <?php }
                    ?>
                    <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>
    </body>
</html>