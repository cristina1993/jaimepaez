<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_cumplimiento($id));
} else {
    $id = 0;
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
                if (cum_codigo.value.length == 0) {
                    alert('Codigo es campo obligatorio')
                    cum_codigo.focus();
                    cum_codigo.style.borderColor = "red";
                } else if (cum_descripcion.value.length == 0) {
                    alert('Descripcón es campo obligatorio')
                    cum_descripcion.focus();
                    cum_descripcion.style.borderColor = "red";
                } else if (cum_descuento.value.length == 0) {
                    alert('Descuento es campo obligatorio')
                    cum_descuento.focus();
                    cum_descuento.style.borderColor = "red";
                } else {
                    var data = Array(
                            cum_codigo.value.toUpperCase(),
                            cum_descripcion.value.toUpperCase(),
                            cum_descuento.value)
                    $.post("actions.php", {act: 34, 'data[]': data, id: id},
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
                <tr><th colspan="3" >Cumplimiento</th></tr>
            </thead>
            <tr>
                <td>Codigo:</td>
                <td><input type="text" name="cum_codigo" id="cum_codigo" size="10" value="<?php echo $rst[cum_codigo] ?>" /></td>
            </tr>
            <tr>
                <td>Descripcion:</td>
                <td><input type="text" name="cum_descripcion" id="cum_descripcion" size="45" value="<?php echo $rst[cum_descripcion] ?>" /></td>
            </tr>
            <tr>
                <td>% Descuento:</td>
                <td><input onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" type="text" name="cum_descuento" id="cum_descuento" size="10" value="<?php echo $rst[cum_descuento] ?>" /></td>
            </tr>                    
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>

                    <?php }
                    ?>
                    <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>                            
                    <button id="cancel"  onclick="cancelar()">Cancelar</button>                           
                </td>
            </tr>                    

        </table>
    </body>
</html>