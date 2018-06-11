<?php
include_once '../Includes/permisos.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                $('#usu_id').val(<?php echo $rst[usu_id] ?>);

                $('#frm_detalle').submit(function (e) {
                    e.preventDefault();
                    clona_fila($("#tbl_detalle"));
                });
//    $(this).keypress(function(e){
//        if(e.keyCode ==13){
//            save(this.lang);
//            return false;
//        }
//    })
//    $('#save').click(function(){
//           save(this.lang);
//           return false;
//      })
                $('#cancel').click(function () {
                    cancelar();
                    return false;
                });
            })

            function save(id) {
                var dat = Array(
                        usu_id.value,
                        cup_mensual.value,
                        cup_xorden.value);

                $.ajax({
                    type: 'POST',
                    url: "actions.php",
                    data: {act: 47, 'data[]': dat, id: id},
                    beforeSend: function () {
                        if (usu_id.value == '0') {
                            $("#usu_id").css({borderColor: "red"});
                            $("#usu_id").focus();
                            return false;
                        } else if (cup_mensual.value.length == 0) {
                            $("#cup_mensual").css({borderColor: "red"});
                            $("#cup_mensual").focus();
                            return false;
                        } else if (cup_xorden.value.length == 0) {
                            $("#cup_xorden").css({borderColor: "red"});
                            $("#cup_xorden").focus();
                            return false;
                        }

                    },
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
                            dat = dt.split(' ');
                            if (dat[2] == 'llave') {
                                alert('El usuario ya tiene asignado un cupo');
                            } else {
                                alert(dt);
                            }
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

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'inv_dest') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    ;
                    return parts[1] + x;
                });
                $(table).find("tbody tr:last").after(tr);
            }
            function elimina_fila(obj) {
                itm=$('.itm').length;
                if(itm>1){
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                }else{
                    alert('No puede eliminar todas las filas');
                }
            }
            
        </script>
        <style>
            .btn-dinamic{
             cursor:pointer;
             background:#752201;
             padding:2px; 
             border-radius:15px;
            }
            .btn-dinamic:hover{
             background:saddlebrown;
            }
        </style>
    </head>
    <body>
        <table id="tbl_form" cellpadding="20"  border="0" >
            <thead>
                <tr><th colspan="4" >FORMULARIO DE INGRESO</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Concepto:</td>
                    <td colspan="3"><input type="text" id="" size="70" /></td>
                </tr>
                <tr>
                    <td>No Documento:</td>
                    <td colspan="3"><input type="text" id="" size="30" /></td>
                </tr>
                <tr>
                    <td>Fecha Emision:</td>
                    <td colspan="3"><input type="text" id="" size="30" /></td>
                </tr>
                <tr>
                    <td>NIF:</td>
                    <td><input type="text" id="" size="30" /></td>
                    <td>Cruce:</td>
                    <td><input type="text" id="" size="30" /></td>
                </tr>
                <tr>
                    <td>Debe/Haber:</td>
                    <td colspan="3">
                        Debe:<input type="radio" name="movimiento" id="debe" />
                        Haber:<input type="radio" name="movimiento" id="haber" />
                    </td>
                </tr>
                <tr><td><br></td></tr>
                <tr>
                    <td colspan="4">
                        <form id="frm_detalle">
                            <table border="0" align="left" id="tbl_detalle">
                                <tr>
                                    <td>No</td>
                                    <td>Descripcion/Razon</td>
                                    <td>Valor</td>
                                </tr>
                                <tbody>
                                    <tr>
                                        <td align="right"><input type="text" size="2" class="itm" id="item1" name="item1"   value="1" lang="1" readonly style="text-align:right"/></td>
                                        <td><input type="text" size="15" id="inv_cantidad1" name="inv_cantidad1" value="" lang="1" /></td>
                                        <td><input type="text" size="30" id="inv_dest1" name="inv_dest1" list="dest_proc" lang="1"  /></td>
                                        <td onclick="elimina_fila(this)" ><img class="btn-dinamic" width="22px" src="../Img/del_reg.png" /></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <input type="submit" id="" value="+" />
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </form>
                    </td>
                </tr>

            </tbody>
            <tfoot>
                <tr><td><br><br><br></td></tr>
                <tr>
                    <td colspan="4">
                        <button id="save" lang="<?php echo $id ?>" >Guardar</button>
                        <button id="cancel" >Cancelar</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>