<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_asientos_banca.php';
$Clase_asientos = new Clase_asientos_banca();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    if (empty($rst)) {
        $rst = pg_fetch_array($Clase_asientos->lista_ingreso_asiento($id));
        $cns = $Clase_asientos->lista_ingreso_asiento($rst['con_asiento']);
    }
} else {
    $rst_sec = pg_fetch_array($Clase_asientos->lista_secuencial_asientos());
    $sec = (substr($rst_sec[con_asiento], 2, 10) + 1);
    if ($sec >= 0 && $sec < 10) {
        $txt = '000000000';
    } else if ($sec >= 10 && $sec < 100) {
        $txt = '00000000';
    } else if ($sec >= 100 && $sec < 1000) {
        $txt = '0000000';
    } else if ($sec >= 1000 && $sec < 10000) {
        $txt = '000000';
    } else if ($sec >= 10000 && $sec < 100000) {
        $txt = '00000';
    } else if ($sec >= 100000 && $sec < 1000000) {
        $txt = '0000';
    } else if ($sec >= 1000000 && $sec < 10000000) {
        $txt = '000';
    } else if ($sec >= 10000000 && $sec < 100000000) {
        $txt = '00';
    } else if ($sec >= 100000000 && $sec < 1000000000) {
        $txt = '0';
    } else if ($sec >= 1000000000 && $sec < 10000000000) {
        $txt = '';
    }
    $secuencial = 'AB' . $txt . $sec;
    $rst['con_asiento'] = $secuencial;
    $id = 0;
    $fila = 0;
    $rst['con_fecha_emision'] = date('Y-m-d');
    $rst['valor_debe'] = 0;
    $rst['valor_haber'] = 0;
    $rst[cli_id] = 0;
    $hidden = "hidden";
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
            f = new Date();
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    var i = a;
                    if ($('#con_documento' + i).val().length != 0 && ($('#concepto_debe' + i).val().length || $('#concepto_haber' + i).val().length) && ($('#valor_debe' + i).val().length || $('#valor_haber' + i).val().length)) {
                        if (this.lang == 0) {
                            clona_fila($('#detalle'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "con_fecha_emision", ifFormat: "%Y-%m-%d", button: "im-fecha_emision"});
                posicion_aux_window();

            });

            function save() {
                var data = Array();
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                var a = tr.find("input").attr("lang");
                var i = a;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() != null) {
                        asiento = $('#con_asiento').val();
                        concepto = $('#con_concepto').val().toUpperCase();
                        documento = $('#con_documento' + n).val().toUpperCase();
                        fec_emision = $('#con_fecha_emision').val();
                        con_debe = $('#concepto_debe' + n).val();
                        con_haber = $('#concepto_haber' + n).val();
                        val_debe = $('#valor_debe' + n).val();
                        val_haber = $('#valor_haber' + n).val();
                        movimiento = $('#con_movimiento').val();
                        nomb = $('#con_pago_nombre').val().toUpperCase();
                        tot = $('#total').html();
                        cli = $('#cli_id').val();
                        data.push(asiento + '&' +
                                concepto + '&' +
                                documento + '&' +
                                fec_emision + '&' +
                                con_debe + '&' +
                                con_haber + '&' +
                                val_debe + '&' +
                                val_haber + '&' +
                                movimiento + '&' +
                                nomb + '&' +
                                tot + '&' +
                                cli
                                );
                    }
                }
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($('#con_concepto').val().length == 0) {
                            $('#con_concepto').css({borderColor: "red"});
                            $('#con_concepto').focus();
                            return false;
                        }
                        else if ($('#con_concepto').val() == 1) {
                            if ($('#con_pago_nombre').val().length == 0) {
                                $('#con_pago_nombre').css({borderColor: "red"});
                                $('#con_pago_nombre').focus();
                                return false;
                            }
                        }

                        var tr = $('#detalle').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        var i = a;
                        n = 0;

                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#item' + n).val() != null) {

                                    if ($('#con_documento' + n).val() == 0) {
                                        $('#con_documento' + n).css({borderColor: "red"});
                                        $('#con_documento' + n).focus();
                                        return false;
                                    }
                                    if ($('#concepto_debe' + n).val().length == 0 && $('#concepto_haber' + n).val().length == 0) {
                                        $('#concepto_debe' + n).css({borderColor: "red"});
                                        $('#concepto_haber' + n).css({borderColor: "red"})
                                        $('#concepto_debe' + n).focus();
                                        $('#concepto_haber' + n).focus();
                                        alert('DEBE INGRESAR DEBE O HABER');
                                        return false;
                                    }
                                    if ($('#valor_debe' + n).val().length == 0 && $('#valor_haber' + n).val().length == 0) {
                                        $('#valor_debe' + n).css({borderColor: "red"});
                                        $('#valor_haber' + n).css({borderColor: "red"});
                                        $('#valor_debe' + n).focus();
                                        $('#valor_haber' + n).focus();
                                        return false;
                                    }
                                    if ($('#valor_debe' + n).val().length == 0) {
                                        $('#valor_debe' + n).css({borderColor: "red"});
                                        $('#valor_debe' + n).focus();
                                        return false;
                                    }

                                    if ($('#valor_haber' + n).val().length == 0) {
                                        $('#valor_haber' + n).css({borderColor: "red"});
                                        $('#valor_haber' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (parseFloat($('#total').html()) != parseFloat($('#total1').html())) {
                            alert('LOS TOTALES DEL DEBE Y EL HABER NO COINCIDEN');
                            return false;
                        }
                        loading('visible');

                    },
                    type: 'POST',
                    url: 'actions_asientos_banca.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            loading('hidden');
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_asientos_banca.php';
                        } else if (dt == 1) {
                            loading('hidden');
                            alert('Numero Secuencial del Asiento ya existe \n Debe hacer otro Asiento con otro Secuencial');
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_asientos_banca.php';
            }
            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input,font").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    if (parts[1] == 'documento') {
                        this.value = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'descripcion_debe') {
                        this.innerHTML = '';
                        this.lang = x;
                    }
                    if (parts[1] == 'descripcion_haber') {
                        this.innerHTML = '';
                        this.lang = x;
                    }

                    if (parts[1] != 'con_asiento') {
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
                $('#con_concepto' + x).focus();
                $('#valor_debe' + x).val(0);
                $('#valor_haber' + x).val(0);
                $('#valor_debe' + x).attr('readonly', true);
                $('#valor_haber' + x).attr('readonly', true);
                if ($('#todos').attr('checked') == true) {
                    copiar();
                }

            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                total();

            }

            function total() {
                n = 0;
                var sum = 0;
                var tr = $('#detalle').find("tbody tr:last");
                var a = tr.find("input").attr("lang");
                var i = a;
                while (n < i) {
                    n++;
                    if ($('#valor_haber' + n).val() != null) {
                        if ($('#valor_haber' + n).val().length == 0) {
                            can = 0;
                        } else {
                            can = $('#valor_haber' + n).val();
                        }
                    } else {
                        can = 0;
                    }

                    sum = sum + parseFloat(can);
                }

                m = 0;
                var sum1 = 0;
                while (m < i) {
                    m++;
                    if ($('#valor_debe' + m).val() != null) {
                        if ($('#valor_debe' + m).val().length == 0) {
                            can1 = 0;
                        } else {
                            can1 = $('#valor_debe' + m).val();
                        }
                    } else {
                        can1 = 0;
                    }
                    sum1 = sum1 + parseFloat(can1);
                }
                $('#total').html(sum1.toFixed(2));
                $('#total1').html(sum.toFixed(2));
            }

            function load_asientos(obj) {
                $.post("actions_asientos.php", {op: 3, id: obj.value},
                function (dt) {
                    if (dt.length != 0) {
                        $('#descripcion_debe' + obj.lang).html(dt.substr(0, 30));
                        $('#descripcion_debe' + obj.lang).attr('title', dt);
                        $('#valor_debe' + obj.lang).attr('readonly', false);
                        $('#valor_debe' + obj.lang).val('0');

                    } else {
                        $(obj).val('');
                        $('#descripcion_debe' + obj.lang).html('');
                        $('#descripcion_debe' + obj.lang).attr('title', '');
                        $('#valor_debe' + obj.lang).val('0');
                        $('#valor_debe' + obj.lang).attr('readonly', true);
                    }
                    total();
                });
            }

            function load_asientos1(obj) {
                $.post("actions_asientos.php", {op: 3, id: obj.value},
                function (dt) {
                    if (dt.length != 0) {
                        $('#descripcion_haber' + obj.lang).html(dt.substr(0, 30));
                        $('#descripcion_haber' + obj.lang).attr('title', dt);
                        $('#valor_haber' + obj.lang).attr('readonly', false);
                        $('#valor_haber' + obj.lang).val('0');
                    } else {
                        $(obj).val('');
                        $('#descripcion_haber' + obj.lang).html('');
                        $('#descripcion_haber' + obj.lang).attr('title', '');
                        $('#valor_haber' + obj.lang).val('0');
                        $('#valor_haber' + obj.lang).attr('readonly', true);
                    }
                    total();
                });
            }


            function cargar_combo(obj) {
                $(obj).autocomplete({
                    minLength: 0,
                    source: cuentas,
                    focus: function (event, ui) {
                        $(obj).val(ui.item.label);
                        return false;
                    }, select: function (event, ui) {
                        $(obj).val(ui.item.value);
                        return false;
                    }
                }).data("autocomplete")._renderItem = function (ul, item) {
                    return $("<li></li>")
                            .data("item.autocomplete", item)
                            .append("<a>" + item.label + "</a>")
                            .appendTo(ul);
                };
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function ocultar() {
                if ($('#con_movimiento').val() == 1) {
                    $('#nombre').show();
                    $('#con_pago_nombre').focus();
                } else {
                    $('#nombre').hide();
                    $('#con_pago_nombre').val('');
                }
            }

            function copiar() {
                if ($('#todos').attr('checked') == true) {
                    var tr = $('#detalle').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    var i = a;
                    n = 0;
                    doc = $('#con_documento1').val();
                    while (n < i) {
                        n++;
                        $('#con_documento' + n).val(doc);
                    }
                }
            }

            function load_cliente(obj) {
                $.post("actions_asientos_banca.php", {op: 2, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#con_pago_nombre').focus();
                        $('#con_pago_nombre').val('');
                        $('#cli_id').val('');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_asientos_banca.php", {op: 2, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Debe crearlo');
                        $('#con_pago_nombre').focus();
                        $('#con_pago_nombre').val('');
                        $('#cli_id').val('');
                    } else {
                        dat = dt.split('&');
                        $('#con_pago_nombre').val(dat[1]);
                        $('#cli_id').val(dat[8]);
                    }
                    $('#con_clientes').hide();
                });
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }
        </script>
        <style>
            *{font-size: 10px}
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 150px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
            }
            .debe_haber{
                font-size: 10px;
                font-weight:normal;
                text-transform:capitalize; 
            }
            .ui-autocomplete {
                max-height: 200px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
             */
            * html .ui-autocomplete {
                height: 200px;
            }

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form"  >
                <thead>
                    <tr><th colspan="15">FORMULARIOS DE ASIENTOS DE BANCOS Y CAJAS<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td>ASIENTO N°</td>
                                <td><input type ="text" id="con_asiento"  value="<?php echo $rst['con_asiento'] ?>" readonly /></td>
                                <td></td>
                                <td></td>
                                <td>FECHA:</td>
                                <td>
                                    <input type="text" size="10" id="con_fecha_emision"  value="<?php echo $rst[con_fecha_emision] ?>" />
                                    <img src="../img/calendar.png" id="im-fecha_emision" />
                                </td>
                            </tr>
                            <tr>
                                <td>CONCEPTO</td>
                                <td colspan="3"><input type ="text" id="con_concepto" size="66" value="<?php echo $rst['con_concepto'] ?>"/></td>
                                <td>MOVIMIENTO</td>
                                <td>
                                    <select id="con_movimiento" style="width: 100px" onchange="ocultar()">
                                        <option value="0">INGRESO</option>
                                        <option value="1">EGRESO</option>
                                    </select>
                                </td>
                            </tr>
                            <tr id="nombre" <?php echo $hidden ?>>
                                <td>PAGUESE A LA ORDEN DE</td>
                                <td colspan="3"><input type ="text" id="con_pago_nombre" size="66" value="<?php echo $rst['con_pago_nombre'] ?>" onchange="load_cliente(this)"/>
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="detalle">
                            <tr id="head">
                            <thead id="tabla">
                                <tr>
                                    <th>No</th>
                                    <th>Documento No <input type="checkbox" id="todos" onclick="copiar()"></th>
                                    <th width="280px">Cuenta Debe</th>
                                    <th width="280px">Cuenta Haber</th>
                                    <th>$ Debe</th>
                                    <th>$ Haber</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <?php
                            if ($fila == "0") {
                                ?>
                                <tr>
                                    <td align="right"><input type ="text" size="1"  class="itm" id="item1"  readonly value="1" lang="1"  /></td>
                                    <td><input type="text" size="20" id="con_documento1" value="" lang="1" /></td>
                                    <td>
                                        <input type ="text" size="15"  id="concepto_debe1" value="" lang="1"  onfocus="cargar_combo(this)" onkeypress="caracter(event, this)" onblur="load_asientos(this)" />
                                        <font  font id="descripcion_debe1" class="debe_haber"  >&nbsp;</font>
                                    </td>
                                    <td>
                                        <input type ="text" size="15"  id="concepto_haber1" value="" lang="1" onfocus="cargar_combo(this)" onkeypress="caracter(event, this)" onblur="load_asientos1(this)" />
                                        <font id="descripcion_haber1" class="debe_haber" >&nbsp;</font>
                                    </td>
                                    <td><input type ="text" size="10"  id="valor_debe1"  value="<?php echo $rst['valor_debe'] ?>" lang="1" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()" readonly/></td>                        
                                    <td><input type ="text" size="10"  id="valor_haber1" value="<?php echo $rst['valor_haber'] ?>" lang="1" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()" readonly/></td>
                                    <td onclick="elimina_fila(this)"><img class="auxBtn" src="../img/del.png" width="14px"/></td>     
                                </tr>
                                <tr>
                                    <td align="right"><input type ="text" size="1"  class="itm" id="item2"  readonly value="2" lang="2"  />
                                        <input type="text" id="con_id1" value="0" lang="1" hidden/></td>
                                    <td><input type="text" size="20" id="con_documento2" value="" lang="2" /></td>
                                    <td>
                                        <input type ="text" size="15"  id="concepto_debe2" value="" lang="2"  onfocus="cargar_combo(this)" onkeypress="caracter(event, this)" onblur="load_asientos(this)" />
                                        <font  font id="descripcion_debe2" class="debe_haber"  >&nbsp;</font>
                                    </td>
                                    <td>
                                        <input type ="text" size="15"  id="concepto_haber2" value="" lang="2" onfocus="cargar_combo(this)" onkeypress="caracter(event, this)" onblur="load_asientos1(this)" />
                                        <font id="descripcion_haber2" class="debe_haber" >&nbsp;</font>
                                    </td>
                                    <td><input type ="text" size="10"  id="valor_debe2"  value="<?php echo $rst['valor_debe'] ?>" lang="2" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()" readonly/></td>                        
                                    <td><input type ="text" size="10"  id="valor_haber2" value="<?php echo $rst['valor_haber'] ?>" lang="2" class="cnt" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), total()" readonly/></td>
                                    <td onclick="elimina_fila(this)"><img class="auxBtn" src="../img/del.png" width="14px"/></td>     
                                </tr>
                                <?PHP
                            } else {
                                ?>
                                <?php
                                $n = 0;
                                $suma = 0;
                                $suma1 = 0;
                                while ($rst1 = pg_fetch_array($cns)) {
                                    $n++;
                                    if ($rst1['con_estado'] == 0) {
                                        $rst1['con_estado'] = 'PENDIENTE';
                                    } else {
                                        $rst1['con_estado'] = 'COMPLETO';
                                    }
                                    $rst2 = pg_fetch_array($Clase_asientos->lista_un_plan_cuenta($rst1['con_concepto_debe']));
                                    $rst3 = pg_fetch_array($Clase_asientos->lista_un_plan_cuenta($rst1['con_concepto_haber']));
                                    ?>
                                    <tr>
                                        <td align="right">
                                            <input type ="text" size="8"  class="itm" id="<?PHP echo 'item' . $n ?>" lang="<?PHP echo $n ?>" readonly value="<?PHP echo $n ?>"/>
                                            <input type="text" id="<?php echo 'con_id' . $n ?>" value="<?php echo $rst1[con_id] ?>" lang="<?PHP echo $n ?>" hidden/>
                                        </td>
                                        <td><input type="text" size="16" id="<?php echo 'con_documento' . $n ?>" value="<?php echo $rst1['con_documento'] ?>" lang="<?PHP echo $n ?>"/></td>
                                        <td><input type ="text" size="15"  id="<?php echo 'concepto_debe' . $n ?>"  value="<?php echo $rst1['con_concepto_debe'] ?>" lang="<?PHP echo $n ?>" onfocus="cargar_combo(this)" onblur="load_asientos(this)" />                                        
                                            <font id="<?php echo 'descripcion_debe' . $n ?>" class="debe_haber" title="<?php echo $rst2['pln_descripcion'] ?>" ><?php echo substr($rst2['pln_descripcion'], 0, 30) ?></font>
                                        </td>
                                        <td><input type ="text" size="15"  id="<?php echo 'concepto_haber' . $n ?>"  value="<?php echo $rst1['con_concepto_haber'] ?>" lang="<?PHP echo $n ?>" onfocus="cargar_combo(this)" onblur="load_asientos1(this)" />
                                            <font id="<?php echo 'descripcion_haber' . $n ?>" class="debe_haber" title="<?php echo $rst3['pln_descripcion'] ?>" ><?php echo substr($rst3['pln_descripcion'], 0, 30) ?></font>
                                        </td>
                                        <td align="right"><input type ="text" size="10"  style="text-align:right " id="<?php echo 'valor_debe' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst1['con_valor_debe'], 2)) ?>" lang="<?PHP echo $n ?>" onkeyup="total()"/></td>                            
                                        <td align="right"><input type ="text" size="10"  style="text-align:right " id="<?php echo 'valor_haber' . $n ?>"  value="<?php echo str_replace(',', '', number_format($rst1['con_valor_haber'], 2)) ?>" lang="<?PHP echo $n ?>" onkeyup="total()"/></td>
                                        <td onclick="elimina_fila(this)"><img class="auxBtn" src="../img/del.png" width="14px"/></td>     
                                    </tr>
                                    <?PHP
                                    $suma = $suma + $rst1['con_valor_debe'];
                                    $suma1 = $suma1 + $rst1['con_valor_haber'];
                                }
                            }
                            ?>
                            <tfoot>
                                <tr class="totales">
                                    <td><button id="add_row">+</button></td>
                                    <td colspan="3" align="right">Total:</td>
                                    <td align="right" style="font-size:15px; " id="total"><?php echo str_replace(',', '', number_format($suma, 2)) ?></td>
                                    <td align="right" style="font-size:15px; " id="total1"><?php echo str_replace(',', '', number_format($suma1, 2)) ?></td>    
                                    <td colspan="3"></td>
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
            <button id="guardar" onclick="save()">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>    
    </body>
</html> 
<script>
    var cuentas = [];</script>
<?php
$cns_can = $Clase_asientos->lista_asientos_contables();
while ($rst_can = pg_fetch_array($cns_can)) {
    ?>
    <script>
        val = '<?php echo $rst_can[pln_codigo] ?>';
        lbl = '<?php echo $rst_can[pln_codigo] . ' ' . $rst_can[pln_descripcion] ?>';
        cuentas.push({value: val, label: lbl});
    </script>
    <?php
}
?>
<script>
    var mov = '<?php echo $rst[con_movimiento] ?>';
    $('#con_movimiento').val(mov);
    ocultar();
</script>
