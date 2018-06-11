<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_tptp($id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                parent.document.getElementById('contenedor2').rows = "*,80%";
                tpr='<?php echo $rst[tpt_relacion]?>';
                tp='<?php echo $rst[tpt_tipo]?>';
                
                if (tpr == 0) {
                    tpt_relacion0.checked = true;
                } else {
                    tpt_relacion1.checked = true;
                }

                if (tp==0) {
                    tpt_tipo0.checked = true
                } else if (tp==1) {
                    tpt_tipo1.checked = true
                } else {
                    tpt_tipo2.checked = true
                }                
            });

            function save(id) {

                if (tpt_relacion0.checked == true) {
                    tpr = 0;
                } else {
                    tpr = 1;
                }

                if (tpt_tipo0.checked == true) {
                    tp = 0;
                } else if (tpt_tipo1.checked == true) {
                    tp = 1;
                } else {
                    tp = 2;
                }

                var data = Array(
                        mpt_siglas.value.toUpperCase(),
                        mpt_nombre.value.toUpperCase(),
                        mpt_obs.value.toUpperCase(),
                        tp,
                        tpr)

                $.post("actions.php", {act: 78, 'data[]': data, id: id},
                function (dt) {
                    if (dt == 0) {
                        window.history.go(0);
                    } else {
                        alert(dt);
                    }
                });
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
        </script>
        <style>
            .mensajes{
                color:#04678F;
                text-shadow:1px 2px 1px #fff; 
            }
            input[type=text]{
                text-transform:uppercase;  
            }
            #tbl_form{
                border-collapse:separate; 
            }
        </style>
    </head>
    <body>
        <table id="tbl_form"  border="0">
            <thead>
                <tr><th colspan="3" >TIPOS DE PRODUCTOS
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th></tr>
            </thead>                    
            <tr>
                <td colspan="2" style="height:35px; " >
                    Materia Prima:<input type="radio" name="tpt_tipo" id="tpt_tipo0" />
                    &nbsp;&nbsp;Producto Terminado:<input type="radio" name="tpt_tipo" id="tpt_tipo1" />
                    &nbsp;&nbsp;Otros:<input type="radio" name="tpt_tipo" id="tpt_tipo2" />
                </td>
            </tr>
            <tr style="height:35px; ">
                <td>Relacion:</td>                
                <td colspan="">
                    Familia:<input type="radio" name="tpt_relacion" id="tpt_relacion0" />
                    &nbsp;&nbsp;Tipo:<input type="radio" name="tpt_relacion" id="tpt_relacion1" />
                </td>
            </tr>

            <tr>
                <td>Siglas:</td>
                <td><input type="text" name="mpt_siglas" id="mpt_siglas" size="8" maxlength="4" value="<?php echo $rst[tpt_siglas] ?>" /><font class="mensajes">Maximo 4 Caracteres</font></td>
            </tr>
            <tr>
                <td>Nombre:</td>
                <td><input type="text" name="mpt_nombre" id="mpt_nombre" size="27" value="<?php echo $rst[tpt_nombre] ?>" /></td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td>
                    <textarea name="mpt_obs" id="mpt_obs" cols="20" rows="2" ><?php echo $rst[tpt_obs] ?></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save(<?php echo $id ?>)">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>
    </body>
</html>
<script>


</script>