<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_una_capacidad_de_compra($id));
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
                if (cap_codigo.value.length == 0) {
                    alert('Codigo es campo obligatorio')
                    cap_codigo.focus();
                    cap_codigo.style.borderColor = "red";
                } else if (cap_monto_maximo.value.length == 0) {
                    alert('El valor del Monto Maximo es campo obligatorio')
                    cap_monto_maximo.focus();
                    cap_monto_maximo.style.borderColor = "red";
                } else if (cap_monto_minimo.value.length == 0) {
                    alert('El valor del Monto Minimo es campo obligatorio')
                    cap_monto_minimo.focus();
                    cap_monto_minimo.style.borderColor = "red";
                } else if (cap_descuento.value.length == 0) {
                    alert('Descuento es campo obligatorio')
                    cap_descuento.focus();
                    cap_descuento.style.borderColor = "red";
                } else {
                    var data = Array(
                            cap_codigo.value.toUpperCase(),
                            cap_monto_maximo.value,
                            cap_monto_minimo.value,
                            cap_descuento.value)
                    $.post("actions.php", {act: 38, 'data[]': data, id: id},
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
                <tr><th colspan="3" >Capacidad de Compra</th></tr>
            </thead>                 
            <tr>
                <td>Codigo:</td>
                <td><input type="text" name="cap_codigo" id="cap_codigo" size="25" value="<?php echo $rst[cap_codigo] ?>" /></td>
            </tr>
            <tr>
                <td>Monto Maximo:</td>
                <td><input type="text" name="cap_monto_maximo" id="cap_monto_maximo" size="45" value="<?php echo $rst[cap_monto_maximo] ?>" /></td>
            </tr>
            <tr>
                <td>Monto Minimo:</td>
                <td><input type="text" name="cap_monto_minimo" id="cap_monto_minimo" size="45" value="<?php echo $rst[cap_monto_minimo] ?>" /></td>
            </tr>
            <tr>
                <td>% Descuento:</td>
                <td><input type="text" name="cap_descuento" id="cap_descuento" size="45" value="<?php echo $rst[cap_descuento] ?>" /></td>
            </tr>
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