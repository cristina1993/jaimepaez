<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_division.php';
$Clase_division = new Clase_division();
$txt = $_GET[txt];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_division->lista_una_division($id));
} else {
    $id = 0;
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
                });
            });
            function save(id) {
                var data = Array(
                        codigo.value.toUpperCase(),
                        descripcion.value.toUpperCase(),
                        gerencia.value
                        );
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (codigo.value.length == 0) {
                            $("#codigo").css({borderColor: "red"});
                            $("#codigo").focus();
                            return false;
                        }
                        else if (descripcion.value.length == 0) {
                            $("#descripcion").css({borderColor: "red"});
                            $("#descripcion").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_division.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
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
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_division.php?txt=' + '<?php echo $txt ?>';
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE DIVISION<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                
                <tr>
                    <td>CODIGO:</td>
                    <td><input type="text" size="40"  id="codigo" value="<?php echo $rst[div_codigo] ?>"/></td>
                </tr>
                <tr>
                    <td>GERENCIA:</td>
                    <td><select  id="gerencia" style="width: 300px">
                            <option value="0">NINGUNA</option>
                            <?PHP
                            $cns_ger = $Clase_division->lista_gerencias();
                            while ($rst_ge = pg_fetch_array($cns_ger)) {
                                echo "<option value='$rst_ge[ger_id]'>$rst_ge[ger_codigo] - $rst_ge[ger_descripcion]</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>DESCRIPCION:</td>
                    <td><input type="text" size="40"  id="descripcion" value="<?php echo $rst[div_descripcion] ?>"/>
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
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

