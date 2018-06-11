<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsTimbradas.php';
$Tmb = new Timbradas();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $x = $_GET['x'];
    $rstTmb = pg_fetch_array($Tmb->listUnDiaExtraordinarios($id));
    switch ($rstTmb[dex_tipo]) {
        case 0:$feriado = 'checked';
            $regular = '';
            break;
        case 1:$feriado = '';
            $regular = 'checked';
            break;
    }
} else {
    $id = 0;
    $x = 0;
    $feriado = 'checked';
    $regular = '';
    $rstTmb[dex_fecha] = date('Y-m-d');
}
?>
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
                Calendar.setup({inputField: "dex_fecha", ifFormat: "%Y-%m-%d", button: "im-dex_fecha"});
            });

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/diasExtLista.php?txt=' + '<?php echo $txt ?>';
            }

            function save(id) {
                f = dex_fecha.value.split('-');
                if ($('#dex_tipo1').attr('checked') == true) {
                    tip = 0;
                } else {
                    tip = 1;
                }
                var data = Array(
                        f[0],
                        dex_fecha.value,
                        tip,
                        dex_obs.value.toUpperCase()
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
                        if (dex_obs.value.length == 0) {
                            $("#dex_obs").css({borderColor: "red"});
                            $("#dex_obs").focus();
                            return false;
                        }

                    },
                    type: 'POST',
                    url: 'actions_dias_ext.php',
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

        </script>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DIAS EXTRAORDINARIOS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>DIA/FECHA</td>
                    <td><input type="text" readonly name="dex_fecha" id="dex_fecha"  size="10"  value="<?php echo $rstTmb[dex_fecha] ?>"  />
                        <img src="../img/calendar.png" id="im-dex_fecha" /></td>
                </tr>
                <tr>
                    <td colspan="2">
                        Feriado:<input <?php echo $feriado ?> type="radio" readonly name="dex_tipo" id="dex_tipo1"  value="0"    />
                        Dia Regular:<input <?php echo $regular ?> type="radio" readonly name="dex_tipo" id="dex_tipo2"  value="1"    />
                    </td>
                </tr>
                <tr>
                    <td>OBS:</td>
                    <td><textarea name="dex_obs" id="dex_obs" cols="30" rows="10" style="text-transform: uppercase"><?php echo $rstTmb[dex_obs] ?></textarea>
                </tr>

                </tbody>        
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