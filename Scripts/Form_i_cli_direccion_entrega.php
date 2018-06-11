<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_una_direccion_entrega($id));
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
            function save(id) {
                if (cde_local.value.length == 0) {
                    alert('Local es campo obligatorio')
                    cde_local.style.borderColor = "red";
                } else if (cde_apellido.value.length == 0) {
                    alert('Apellido es campo obligatorio')
                    cde_apellido.focus();
                    cde_apellido.style.borderColor = "red";
                } else if (cde_nombre.value.length == 0) {
                    alert('Nombre es campo obligatorio')
                    cde_nombre.focus();
                    cde_nombre.style.borderColor = "red";
                } else if (cde_telefono.value.length == 0) {
                    alert('Telefono es campo obligatorio')
                    cde_telefono.focus();
                    cde_telefono.style.borderColor = "red";
                } else if (cde_pais.value.length == 0) {
                    alert('Pais es campo obligatorio')
                    cde_pais.focus();
                    cde_pais.style.borderColor = "red";
                } else if (cde_provincia.value.length == 0) {
                    alert('Provincia es campo obligatorio')
                    cde_provincia.focus();
                    cde_provincia.style.borderColor = "red";
                } else if (cde_cal_principal.value.length == 0) {
                    alert('Calle Principal es campo obligatorio')
                    cde_cal_principal.focus();
                    cde_cal_principal.style.borderColor = "red";
                } else if (cde_cal_secundaria.value.length == 0) {
                    alert('Calle Secundaria es campo obligatorio')
                    cde_cal_secundaria.focus();
                    cde_cal_secundaria.style.borderColor = "red";
                } else {
                    var data = Array(
                            cde_local.value.toUpperCase(),
                            cde_apellido.value.toUpperCase(),
                            cde_nombre.value.toUpperCase(),
                            cde_telefono.value.toUpperCase(),
                            cde_celular.value.toUpperCase(),
                            cde_pais.value.toUpperCase(),
                            cde_provincia.value.toUpperCase(),
                            cde_parroquia.value.toUpperCase(),
                            cde_canton.value.toUpperCase(),
                            cde_cal_principal.value.toUpperCase(),
                            cde_numeracion.value.toUpperCase(),
                            cde_cal_secundaria.value.toUpperCase(),
                            cde_referencia.value.toUpperCase())
                    $.post("actions.php", {act: 32, 'data[]': data, id: id},
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
                <tr><th colspan="8" ></th></tr>
            </thead>
            <tr>
                <td>Local:</td>
                <td><input type="text" name="cde_local" id="cde_local" size="60" value="<?php echo $rst[cde_local] ?>" /></td>
            </tr>                 
            <tr>
                <td>Apellido:</td>         
                <td><input type="text" name="cde_apellido" id="cde_apellido" size="35" value="<?php echo $rst[cde_apellido] ?>" /></td>           
                <td>Nombre:</td>
                <td><input type="text" name="cde_nombre" id="cde_nombre" size="35" value="<?php echo $rst[cde_nombre] ?>" /></td>           
                <td>Teléfono:</td>
                <td><input type="text" name="cde_telefono" id="cde_telefono" size="20" value="<?php echo $rst[cde_telefono] ?>" /></td>                          
                <td>Celular:</td>
                <td><input type="text" name="cde_celular" id="cde_celular" size="20" value="<?php echo $rst[cde_celular] ?>" /></td>                          
            </tr>         
            <tr>
                <td>País:</td>         
                <td><input type="text" name="cde_pais" id="cde_pais" size="35" value="<?php echo $rst[cde_pais] ?>" /></td>           
                <td>Provincia:</td>
                <td><input type="text" name="cde_provincia" id="cde_provincia" size="25" value="<?php echo $rst[cde_provincia] ?>" /></td>           
                <td>Parroquia :</td>
                <td><input type="text" name="cde_parroquia" id="cde_parroquia" size="25" value="<?php echo $rst[cde_parroquia] ?>" /></td>                          
                <td>Cantón:</td>
                <td><input type="text" name="cde_canton" id="cde_canton" size="25" value="<?php echo $rst[cde_canton] ?>" /></td>                          
            </tr>         
            <tr>
                <td>Calle Principal:</td>         
                <td><input type="text" name="cde_cal_principal" id="cde_cal_principal" size="60" value="<?php echo $rst[cde_cal_principal] ?>" /></td>           
                <td>Numeración:</td>
                <td><input type="text" name="cde_numeracion" id="cde_numeracion" size="25" value="<?php echo $rst[cde_numeracion] ?>" /></td>           
                <td>Calle Secundaria:</td>
                <td><input type="text" name="cde_cal_secundaria" id="cde_cal_secundaria" size="45" value="<?php echo $rst[cde_cal_secundaria] ?>" /></td>                          
            </tr>                                       
            <tr>                        
                <td>Referencia:</td>
                <td><input type="text" name="cde_referencia" id="cde_referencia" size="60" value="<?php echo $rst[cde_referencia] ?>" /></td>                           
            </tr>
            <tr>
                <td colspan="8">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                    <?php }
                    ?>
                    <button id="save" onclick="save(<?php echo $id ?>)">Añadir</button>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>                          
                </td>
            </tr>                    
        </table>
    </body>                

</table>
</body>  
</html>