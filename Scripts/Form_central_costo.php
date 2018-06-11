<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_central_costo.php'; // cambiar clsClase_productos
$Set = new Clase_central_costo();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Set->lista_una_central($id));
    $cns = $Set->lista_una_central($id);
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
            var id = '<?php echo $id ?>';
            var emi =<?php echo $emisor ?>;
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            $(function () {
                $('#pro_codigo1').attr('disabled', true);
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#dinamica').find("tbody tr:last");
                    a = tr.find("input").attr("lang");
                    i = parseInt(a);
//                    if () {
                    clona_fila($('#dinamica'));
//                    }
                });

                parent.document.getElementById('contenedor2').rows = "*,80%";
            });

            function save(id, x) {
                var data = Array();
                var tr = $('#dinamica').find("tbody tr:last");
                var a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                while (n < i) {
                    n++;
                    if ($('#pln_codigo' + n).val() != null) {
                        pln = $('#pln_id' + n).val();
                        des = $('#ctc_descripcion').val();
                        data.push(
                                pln + '&' +
                                des
                                );
                    }
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        if (ctc_descripcion.value.length == 0) {
                            $("#ctc_descripcion").css({borderColor: "red"});
                            $("#ctc_descripcion").focus();
                            return false;
                        }
                        var tr = $('#dinamica').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        i = parseInt(a);
                        n = 0;
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#pln_descripcion' + n).val() != null) {
                                    if ($('#pln_codigo' + n).val() == 0) {
                                        $('#pln_codigo' + n).css({borderColor: "red"});
                                        $('#pln_codigo' + n).focus();
                                        return false;
                                    }
                                    if ($('#pln_descripcion' + n).val() == 0) {
                                        $('#pln_descripcion' + n).css({borderColor: "red"});
                                        $('#pln_descripcion' + n).focus();
                                        return false;
                                    }

                                }
                            }
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_central_costo.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
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
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] != 'cantidad') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] != 'cantidad') {
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
                var tr = $('#dinamica').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if (i > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }


            function load_cuentas(obj) {
                j = obj.lang;
                vl = $('#pln_codigo' + j).val();
                $('.itm').each(function () {
                    pro = $('#pln_id' + this.value).val();
                    pro2 = $('#pln_codigo' + j).val();
                    $('#pln_codigo' + j).css({borderColor: ""});
                    if (pro2 == pro) {
                        alert('Cuenta ya ingresada');
                        vl = '';
                        $('#pln_codigo' + j).focus();
                        return false;
                    }
                });

                $.post("actions_central_costo.php", {op: 2, id: vl},
                function (dt) {
                    dat = dt.split('&&');
                    if (dt.trim().length != 0) {
                        $('#pln_id' + j).val(dat[0]);
                        $('#pln_codigo' + j).val(dat[1]);
                        $('#pln_descripcion' + j).val(dat[2]);
                    } else {
                        $('#pln_id' + j).val('0');
                        $('#pln_codigo' + j).val('');
                        $('#pln_descripcion' + j).val('');
                    }
                });

            }

            
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }

            .add td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            *{
                font-size: 10px;
            }

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>

                    <tr>
                        <th colspan="7" >
                            FORMULARIO CENTRAL
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>Descripcion:</td>
                                <td>
                                    <input type="text" size="50px" id="ctc_descripcion" value="<?php echo $rst[ctc_descripcion] ?>"  />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="dinamica">
                            <thead id="tabla">
                                <tr id="head">
                                    <th>Item</th>
                                    <th>Codigo</th>
                                    <th>Cuenta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <?php
                            if ($id == '0') {
                                ?>
                                <tr>
                                    <td><input type="text" size="10" class="itm" id="item1" name="item1" readonly value="1" lang="1"/>
                                        <input type ="hidden" size="20"  id="pln_id1"  value="" lang="1" />
                                    </td>
                                    <td><input type="text" size="60" id="pln_codigo1"  value="" lang="1" list="lista_cuentas" onchange="load_cuentas(this)"/> </td>
                                    <td><input type="text" size="60"  id="pln_descripcion1" value="" lang="1" readonly  style="font-weight: 100"/></td>
                                    <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                                </tr>
                                <?php
                            } else {
                                $n = 0;
                                while ($rst_dt = pg_fetch_array($cns)) {
                                    $n++;
                                    ?>
                                    <tr>
                                        <td><input type="text" size="10" class="itm" id="item<?php echo $n ?>" name="item<?php echo $n ?>" readonly value="<?php echo $n ?>" lang="<?php echo $n ?>"/>
                                            <input type ="hidden" id="pln_id<?php echo $n ?>"  value="<?php echo $rst_dt[pln_id] ?>" lang="<?php echo $n ?>" />
                                        </td>
                                        <td><input type="text" size="60" id="pln_codigo<?php echo $n ?>"  value="<?php echo $rst_dt[pln_codigo] ?>" lang="<?php echo $n ?>" list="lista_cuentas" onchange="load_cuentas(this)"/> </td>
                                        <td><input type="text" size="60"  id="pln_descripcion<?php echo $n ?>" value="<?php echo $rst_dt[pln_descripcion] ?>" lang="<?php echo $n ?>" readonly  style="font-weight: 100"/></td>
                                        <td onclick="elimina_fila(this)" ><img class="auxBtn" src="../img/b_delete.png" /></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tfoot>
                                <tr class="add">
                                    <td>
                                        <?PHP
                                        if ($x != 1) {
                                            ?> 
                                            <button id="add_row">+</button>
                                            <?PHP
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save('<?php echo $rst[ctc_descripcion] ?>', 0)">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>   
    </body>
</html>

<datalist id="lista_cuentas">
    <?php
    $cns_cta = $Set->lista_cuentas();
    while ($rst_cta = pg_fetch_array($cns_cta)) {
        echo "<option value='$rst_cta[pln_id]' >$rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
