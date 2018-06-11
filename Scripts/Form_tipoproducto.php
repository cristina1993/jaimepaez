<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_tipoproducto.php';
$Clase_tipoproducto = new Clase_tipoproducto();
$txt = $_GET[txt];
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Clase_tipoproducto->lista_un_tipoproducto($id));
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
                if (id == 0) {
                    $("#tps_relacion1").attr('checked', true);
                }
            });
            function save(id) {

                if ($("#tps_tipo1").attr('checked') == true) {
                    a = 1;
                } else {
                    a = 0;
                }
                if ($("#tps_tipo2").attr('checked') == true) {
                    b = 1;
                } else {
                    b = 0;
                }
                if ($("#tps_tipo3").attr('checked') == true) {
                    c = 1;
                } else {
                    c = 0;
                }
                tps_tipo = a + '&' + b + '&' + c;

                if (tps_relacion1.checked == true) {
                    rel = 1;
                } else if (tps_relacion2.checked == true) {
                    rel = 2;
                }
                var data = Array(
                        tps_tipo,
                        rel,
                        tps_siglas.value.toUpperCase(),
                        tps_nombre.value.toUpperCase(),
                        tps_observaciones.value.toUpperCase()
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
                        if ($("#tps_tipo1").attr('checked') == false && $("#tps_tipo2").attr('checked') == false && $("#tps_tipo3").attr('checked') == false) {
                            alert('Seleccione un tipo');
                            $("#tps_tipo1").css({borderColor: "red"});
                            $("#tps_tipo1").focus();
                            return false;
                        }
                        if (tps_siglas.value.length == 0) {
                            $("#tps_siglas").css({borderColor: "red"});
                            $("#tps_siglas").focus();
                            return false;
                        }
                        if (tps_nombre.value.length == 0) {
                            $("#tps_nombre").css({borderColor: "red"});
                            $("#tps_nombre").focus();
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_tipoproducto.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_tiposproductos.php?search=1&txt=<?php echo $txt ?>';
            }
            function natural(a) {
                switch (a)
                {
                    case 0:
                        if (($("#tps_tipo1").attr('checked') == true) && ($("#tps_tipo2").attr('checked') == true) && ($("#tps_tipo3").attr('checked') == true)) {
                            $("#tps_tipo1").attr('checked', false);
                            $("#tps_tipo2").attr('checked', false);
                        } else if (($("#tps_tipo1").attr('checked') == false) && ($("#tps_tipo2").attr('checked') == true) && ($("#tps_tipo3").attr('checked') == true)) {
                            $("#tps_tipo2").attr('checked', false);
                        }
                        else if (($("#tps_tipo2").attr('checked') == false) && ($("#tps_tipo1").attr('checked') == true) && ($("#tps_tipo3").attr('checked') == true)) {
                            $("#tps_tipo1").attr('checked', false);
                        }
                        break;
                    case 1:
                        if (($("#tps_tipo2").attr('checked') == true) && ($("#tps_tipo1").attr('checked') == false) && ($("#tps_tipo3").attr('checked') == true)) {
                            $("#tps_tipo3").attr('checked', false);
                        }
                        break;
                    case 2://Editar
                        if (($("#tps_tipo1").attr('checked') == true) && ($("#tps_tipo2").attr('checked') == false) && ($("#tps_tipo3").attr('checked') == true)) {
                            $("#tps_tipo3").attr('checked', false);
                        }
                        break;
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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
        <div id="cargando"></div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>TIPO:</td>
                    <td><input type="checkbox" size="20"  id="tps_tipo1" onchange="natural(2)"value="<?php echo $rst[tps_tipo] ?>"/>Para Consumo</td>
                    <td><input type="checkbox" size="20"  id="tps_tipo2" onchange="natural(1)" value="<?php echo $rst[tps_tipo] ?>"/>Para Venta</td>
                    <td><input type="checkbox" size="20"  id="tps_tipo3" onchange="natural(0)" value="<?php echo $rst[tps_tipo] ?>"/>Otros</td>
                </tr>
                <tr>
                    <td>RELACION:</td>
                    <td><input type="radio" size="20"  id="tps_relacion1"  name="tps_relacion"  value="<?php echo $rst[tps_relacion] ?>" />Familia/Proveedor</td>
                    <td><input type="radio" size="20"  id="tps_relacion2" name="tps_relacion" value="<?php echo $rst[tps_relacion] ?>" />Tipo</td>
                </tr>
                <tr>
                    <td>SIGLAS:</td>
                    <td><input type="text" size="5"  id="tps_siglas" maxlength="4" value="<?php echo $rst[tps_siglas] ?>" />max 4 Caracteres</td>
                </tr>
                <tr>
                    <td>NOMBRE:</td>
                    <td><input type="text" size="30"  id="tps_nombre" maxlength="25" value="<?php echo $rst[tps_nombre] ?>" />
                </tr>
                <tr>
                    <td>OBSERVACIÓN:</td>

                    <td valign="top" rowspan="7" colspan="6"><textarea id="tps_observaciones" style="width:52%; text-transform: uppercase;" onkeydown="return enter(event)"><?php echo $rst[tps_observaciones] ?></textarea></td>    
                </tr>

                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar" >Guardar</button>    
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
    var rel =<?php echo $rst[tps_relacion] ?>;
    if (rel == 1) {
        $('#tps_relacion1').attr('checked', true);
        natural();

    } else if (rel == 2) {
        $('#tps_relacion2').attr('checked', true);
        natural();
    }

    var tip = '<?php echo $rst[tps_tipo] ?>';
    dat = tip.split('&');

    if (dat[0] == 1) {
        $('#tps_tipo1').attr('checked', true);
    }
    if (dat[1] == 1) {
        $('#tps_tipo2').attr('checked', true);
    }
    if (dat[2] == 1) {
        $('#tps_tipo3').attr('checked', true);
    }
</script>