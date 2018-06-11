<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClaseOrden.php';
$ClaseOrden = new ClaseOrden();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($ClaseOrden->lista_resgistro_id($id));
} else {
    $id = 0;
    $rst['reg_fecha'] = date('Y-m-d');
    $rst['reg_operador'] = 0;
    $rst['reg_maquina'] = 0;
    $rst['reg_gramaje'] = 0;
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
                Calendar.setup({inputField: "reg_fecha", ifFormat: "%Y-%m-%d", button: "im-campo1"});
            });
            function load_datos(obj) {
                $.post('actionsOrden.php', {id: obj.value, op: 2}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0]==0){
                        alert ('CODIGO NO EXISTE');
                        $("#ord_num_orden").css({borderColor: "red"});
                            $("#ord_num_orden").focus();
                            return false;
                    }else{
                    $('#pro_descripcion1,#pro_descripcion2').val(dat[0]);
                    $('#ord_rep_ancho').val(dat[1]);
                    $('#matriz').html(dat[2]);
                    $('#ord_id').val(dat[3]);
                }
                })
            }
            if (id != 0) {
                $.post('actionsOrden.php', {id: id, op: 3}, function (dt) {
                    $('#matriz').html(dt);
                })
            }
            function save(id) {
                var data = Array(
                        ord_id.value,
                        reg_fecha.value,
                        reg_operador.value,
                        reg_maquina.value,
                        reg_gramaje.value
                        );
                var data1 = Array();
                doc = document.getElementsByClassName('input');
                n = 0;
                while (n < doc.length) {
                    rp = $('#reg_peso' + n).val();
                    rs = $('#reg_peso_secundario' + n).val();
                    rr = $('#reg_peso_reproceso' + n).val();
                    rf = $('#reg_peso_refilado' + n).val();
                    data1.push(rp + '&' +
                            rs + '&' +
                            rr + '&' +
                            rf
                            );
                    n++;
                }

                var i = doc.length;
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        doc = document.getElementsByClassName('input');
                        doc = document.getElementsByClassName('input');
                        var n = 0;

                        if (reg_fecha.value.length == 0) {
                            $("#reg_fecha").css({borderColor: "red"});
                            $("#reg_fecha").focus();
                            return false;
                        }
                        else if ($("#reg_operador").val().length == 0) {
                            $("#reg_operador").css({borderColor: "red"});
                            $("#reg_operador").focus();
                            return false;
                        }
                        else if (reg_maquina.value.length == 0) {
                            $("#reg_maquina").css({borderColor: "red"});
                            $("#reg_maquina").focus();
                            return false;
                        }
                        else if (reg_gramaje.value.length == 0) {
                            $("#reg_gramaje").css({borderColor: "red"});
                            $("#reg_gramaje").focus();
                            return false;
                        } else if (doc.length != 0) {
                            while (n < doc.length) {
                                if ($('#reg_peso' + n).val() == 0) {
                                    $('#reg_peso' + n).css({borderColor: "red"});
                                    $('#reg_peso' + n).focus();
                                    return false;
                                }
                                else if ($('#reg_peso_secundario' + n).val() == 0) {
                                    $('#reg_peso_secundario' + n).css({borderColor: "red"});
                                    $('#reg_peso_secundario' + n).focus();
                                    return false;
                                }
                                else if ($('#reg_peso_reproceso' + n).val() == 0) {
                                    $('#reg_peso_reproceso' + n).css({borderColor: "red"});
                                    $('#reg_peso_reproceso' + n).focus();
                                    return false;
                                }
                                else if ($('#reg_peso_refilado' + n).val() == 0) {
                                    $('#reg_peso_refilado' + n).css({borderColor: "red"});
                                    $('#reg_peso_refilado' + n).focus();
                                    return false;
                                }
                                n++;
                            }
                        }
                    },
                    type: 'POST',
                    url: 'actionsOrden.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, i: i}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
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
            }

        </script>

    </head>
    <body>
        <form  autocomplete="off" id="frm_save" name="frm_save">
            <table id="tbl_form" >
                <thead>
                    <tr><th colspan="5" >REPORTE DE PRODUCCIÓN </th></tr>
                </thead>
                <tr>
                    <td>NO. DE ORDEN:</td>
                    <td><input type="text" size="12"  id="ord_num_orden" onchange="load_datos(this)" value="<?php echo $rst['ord_num_orden'] ?>" onkeyup="this.value=this.value.toUpperCase()"/>
                        <input type="hidden" id="ord_id" value="<?php echo $rst['ord_id'] ?>"/></td>
                </tr>

                <tr>
                    <td>FECHA:</td>
                    <td><input type="text" size="12"  id="reg_fecha"  value="<?php echo $rst['reg_fecha'] ?>" /><img src="../Img/calendar.png" id="im-campo1"/></td>
                </tr>
                <tr>
                    <td>OPERADOR:</td>
                    <td><input type="text" size="12"  id="reg_operador"  value="<?php echo $rst['reg_operador'] ?>" onkeyup="this.value=this.value.replace (/[^0-9.]/,'');" /></td>
                </tr>
                <tr>
                    <td>MAQUINARIA </td>
                    <td><input type="text" size="12"  id="reg_maquina"  value="<?php echo $rst['reg_maquina'] ?>" onkeyup="this.value=this.value.replace (/[^0-9.]/,'');"/></td>

                </tr>
                <tr>
                    <td>GRAMAJE:</td>
                    <td><input type="text" size="12"  id="reg_gramaje"  value="<?php echo $rst['reg_gramaje'] ?>" onkeyup="this.value=this.value.replace (/[^0-9.]/,'');"/>GR/M</td>
                </tr>
                <tr>
                    <td></td>
                    <td>PRODUCTO PRINCIPAL</td>
                    <td>PRODUCTO SECUNDARIO</td>
                    <td>REPROCESO</td>
                    <td>REFILADO</td>
                </tr>
                <tr>
                    <td>PRODUCTO:</td>
                    <td><input type="text" size="12"  id="pro_descripcion1"  readonly value="<?php echo $rst['pro_descripcion'] ?>" /></td>
                    <td><input type="text" size="12"  id="pro_descripcion2"  readonly value="<?php echo $rst['pro_descripcion'] ?>"/></td>
                    <td><input type="text" size="12"  id="ord_rep_ancho"  readonly value="<?php echo $rst['ord_rep_ancho'] ?>"/></td>
                    <td><input type="text" size="12"  id="ord_refilado"  readonly value="<?php echo $rst['ord_refilado'] ?>"/></td>
                    </td>
                </tr>
                <tr></tr>
                <tbody id="matriz"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <button id="guardar">Guardar</button>    
                            <button id="cancelar" >Cancelar</button>    
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>  
