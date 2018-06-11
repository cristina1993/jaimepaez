<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php';
$Clase_guia_remision = new Clase_guia_remision();
if ($pto_emi > 99) {
    $ems = $pto_emi;
} else if ($pto_emi < 100 && $pto_emi > 9) {
    $ems = '0' . $pto_emi;
} else {
    $ems = '00' . $pto_emi;
}
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Clase_guia_remision->lista_una_guia_id($id));
    $rst['tipo_comprobante'] = 'FACTURA';
    $vnd_id = $rst[vnd_id];
    $cns = $Clase_guia_remision->lista_detalle_guia($id);
} else {
    $id = 0;
    $rst[tipo_comprobante] = 'FACTURA';
    $rst[gui_fecha_emision] = date('Y-m-d');
    $rst[gui_fecha_inicio] = date('Y-m-d');
    $rst[gui_fecha_fin] = date('Y-m-d');
    $rst[gui_fecha_comp] = date('Y-m-d');
    $rst_sec = pg_fetch_array($Clase_guia_remision->lista_secuencial_documento($emisor));
    if (empty($rst_sec)) {
        $sec = $rst_mod[emi_sec_guia_remision];
    } else {
        $dat = explode('-', $rst_sec[gui_numero]);
        $sec = $dat[2] + 1;
    }
    if ($sec >= 0 && $sec < 10) {
        $tx = '00000000';
    } else if ($sec >= 10 && $sec < 100) {
        $tx = '0000000';
    } else if ($sec >= 100 && $sec < 1000) {
        $tx = '000000';
    } else if ($sec >= 1000 && $sec < 10000) {
        $tx = '00000';
    } else if ($sec >= 10000 && $sec < 100000) {
        $tx = '0000';
    } else if ($sec >= 100000 && $sec < 1000000) {
        $tx = '000';
    } else if ($sec >= 1000000 && $sec < 10000000) {
        $tx = '00';
    } else if ($sec >= 10000000 && $sec < 100000000) {
        $tx = '0';
    } else if ($sec >= 100000000 && $sec < 1000000000) {
        $tx = '';
    }
    $rst[gui_numero] = $ems . '-001-' . $tx . $sec;
    $rst[fac_id] = '0';
    $rst_ven = pg_fetch_array($Clase_guia_remision->lista_vendedor(strtoupper($rst_user[usu_person])));
    $vnd_id = $rst_ven[vnd_id];
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
            var usu =<?php echo $emisor ?>;
            var vnd ='<?php echo $vnd_id ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    if (this.lang == 1) {
                        save(id);
                    } else if (this.lang == 0) {
                        var tr = $('#guia').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        if ($('#descripcion' + a).val().length != 0) {
                            if (this.lang == 0) {
                                clona_fila($('#guia'));
                            }
                        }
                    }
                });
                $('#con_transportistas').hide();
                $('#con_cliente').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_inicio_transporte", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                Calendar.setup({inputField: "fecha_fin_transporte", ifFormat: "%Y-%m-%d", button: "im-campo3"});
                Calendar.setup({inputField: "fecha_comp", ifFormat: "%Y-%m-%d", button: "im-campo4"});
                posicion_aux_window();
            });


            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    if (parts[1] == 'item') {
                        this.value = x;
                    }
                    if (parts[1] != 'item') {
                        this.value = '';
                    }
                    this.lang = x;
                    return parts[1] + x;
                });
                $('#guia').find("tbody tr:last").after(tr);
                $('#cod_producto' + x).focus();
            }

            function elimina_fila(obj) {
                itm = $('.item').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                } else {
                    alert('No puede eliminar todas las filas');
                }
                calculo();
            }

            function save(id) {
                var data = Array(
                        vnd,
                        usu,
                        cli_id.value,
                        num_comprobante.value,
                        fecha_emision.value,
                        fecha_inicio_transporte.value,
                        fecha_fin_transporte.value,
                        motivo_traslado.value,
                        punto_partida.value,
                        destino.value,
                        identificacion_destinario.value,
                        nombre_destinatario.value,
                        identificacion_trasportista.value,
                        documento_aduanero.value,
                        cod_establecimiento_destino.value,
                        num_comprobante_venta.value,
                        observacion.value,
                        fac_id.value,
                        tra_id.value, //tra_id,
                        '1',
                        autorizacion.value,
                        fecha_comp.value
                        );

                n = 0;
                var tr = $('#guia').find("tbody tr:last");
                var a = tr.find("input").attr("lang");
                var i = parseInt(a);
                var data2 = Array();
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() != null) {
                        cantidad = $('#cantidad' + n).val();
                        descripcion = $('#descripcion' + n).val();
                        cod_producto = $('#cod_producto' + n).val();
                        pro_id = $('#pro_id' + n).val();
                        data2.push(
                                cantidad + '&' +
                                cod_producto + '&' +
                                '' + '&' +
                                descripcion + '&' +
                                pro_id
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
                        //Validaciones antes de enviar
                        var tr = $('#guia').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        var i = parseInt(a);
                        n = 0;
                        if (num_comprobante_venta.value.length == 0) {
                            $("#num_comprobante_venta").css({borderColor: "red"});
                            $("#num_comprobante_venta").focus();
                            return false;
                        }
                        if (autorizacion.value.length != 0) {
                            if (autorizacion.value.length != 10 && autorizacion.value.length != 37 && autorizacion.value.length != 49) {
                                $("#autorizacion").css({borderColor: "red"});
                                $("#autorizacion").focus();
                                return false;
                            }
                        }
                        if (motivo_traslado.value.length == 0) {
                            $("#motivo_traslado").css({borderColor: "red"});
                            $("#motivo_traslado").focus();
                            return false;
                        }
                        else if (punto_partida.value.length == 0) {
                            $("#punto_partida").css({borderColor: "red"});
                            $("#punto_partida").focus();
                            return false;
                        }
                        else if (destino.value.length == 0) {
                            $("#destino").css({borderColor: "red"});
                            $("#destino").focus();
                            return false;
                        }

                        else if (nombre_destinatario.value.length == 0) {
                            $("#nombre_destinatario").css({borderColor: "red"});
                            $("#nombre_destinatario").focus();
                            return false;
                        }
                        else if (identificacion_destinario.value.length == 0) {
                            $("#identificacion_destinario").css({borderColor: "red"});
                            $("#identificacion_destinario").focus();
                            return false;
                        }
                        else if (cod_establecimiento_destino.value.length == 0) {
                            $("#cod_establecimiento_destino").css({borderColor: "red"});
                            $("#cod_establecimiento_destino").focus();
                            return false;
                        }
                        else if (identificacion_trasportista.value.length == 0) {
                            $("#identificacion_trasportista").css({borderColor: "red"});
                            $("#identificacion_trasportista").focus();
                            return false;
                        }
                        else if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#item' + n).val() != null) {
                                    if ($('#cantidad' + n).val().length == 0 || $('#cantidad' + n).val() == '0') {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (vnd == '') {
                            alert('El usuario no es vendedor');
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_guia_remision.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
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
                t = '<?php echo $_GET[txt] ?>';
                d = '<?php echo $_GET[fecha1] ?>';
                h = '<?php echo $_GET[fecha2] ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_guia_remision.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;
            }


            function saldo(obj) {
                n = obj.lang;
                if (fac_id.value != 0) {
                    var saldo = parseFloat($('#saldox' + n).val());
                    if (saldo < $(obj).val()) {
                        $(obj).css({borderColor: "red"});
                        $(obj).focus();
                        $(obj).val('');
                        alert('cantidad sobrepasa el saldo');
                        return false;
                    } else {
                        if ($(obj).val().length == 0 || $(obj).val() == 0) {
                            var saldo = parseFloat($('#cantidadf' + n).val()) - parseFloat($('#entregado' + n).val());
                            $('#saldo' + n).val(saldo);
                        } else {
                            var saldo = parseFloat($('#cantidadf' + n).val()) - parseFloat($(obj).val()) - parseFloat($('#entregado' + n).val());
                            $('#saldo' + n).val(saldo);
                        }
                    }
                }
            }

            function load_transportista(obj) {
                $.post("actions_guia_remision.php", {op: 5, id: obj.value, s: 0},
                function (dt) {
                    if (dt != '') {
                        $('#clientes').html(dt);
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                    } else {
                        alert('Transportista no existe \n Cree uno Nuevo??');
                        $('#identificacion_trasportista').focus();
                        $('#identificacion_trasportista').val('');
                        $('#nombre_trasportista').val('');
                        $('#placa').val('');
                        $('#tra_id').val('0');
                    }
                });
            }

            function load_transportista2(obj) {
                $.post("actions_guia_remision.php", {op: 5, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Transportista no existe');
                        $('#nombre_trasportista').focus();
                        $('#identificacion_trasportista').val('');
                        $('#nombre_trasportista').val('');
                        $('#placa').val('');
                        $('#tra_id').val('0');
                    } else {
                        dat = dt.split('&');
                        $('#identificacion_trasportista').val(dat[0]);
                        $('#nombre_trasportista').val(dat[1]);
                        $('#placa').val(dat[2]);
                        $('#tra_id').val(dat[3]);
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

            function load_factura(obj) {
                $.post("actions_guia_remision.php", {op: 8, id: obj.value, emi: usu},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#fac_id').val(dat[0]);
                        $('#identificacion_destinario').val(dat[1]);
                        $('#nombre_destinatario').val(dat[2]);
                        $('#lista').html(dat[3]);
                        $('#cli_id').val(dat[4]);
                        $('#autorizacion').val(dat[5]);
                        $('#fecha_comp').val(dat[6]);
                        $('#add_row').hide();
                    } else {
                        $('#fac_id').val('0');
                        $('#identificacion_destinario').val('');
                        $('#nombre_destinatario').val('');
                        $('#lista').html('');
                        $('#cli_id').val('0');
                        $('#add_row').show();
                        $('#autorizacion').val('');
                        a = '"';
                        var tr = "<tr>" +
                                "<td align='right'><input type ='text' size='8'  class='item' id='item1' value='1' readonly lang='1'/></td>" +
                                "<td><input type ='text' size='20'  id='cod_producto1' lang='1' value='' list='productos' onblur='this.style.width =" + a + "100px" + a + ",load_producto(this)' onfocus='this.style.width =" + a + "500px" + a + "'/>" +
                                "<input type ='hidden' size='20'  id='pro_id1' lang='1' value=''/>" +
                                "<td><input type ='text' size='60'  id='descripcion1'  lang='1' value='' readonly/></td>" +
                                "<td><input type ='text' size='15'  id='cantidadf1'  lang='1' value='0' readonly/></td>" +
                                "<td><input type ='text' size='15'  id='entregado1'  lang='1' value='0' readonly/></td>" +
                                "<td><input type ='text' size='15'  id='saldo1'  lang='1' value='0' readonly/>" +
                                "<input type ='text' size='15'  id='saldox1'  lang='1' hidden value='0'/></td>" +
                                "<td><input type ='text' size='15'  id='cantidad1' lang='1' onkeyup='this.value = this.value.replace(/[^0-9.]/," + a + a + "), saldo(this)' value='0'/></td> " +
                                "<td onclick = 'elimina_fila(this)' ><img class = 'auxBtn' width='12px' src = '../img/del_reg.png'/></td>" +
                                "</tr>";
                        $('#lista').html(tr);

                    }
                });
            }

            function load_cliente(obj) {
                $.post("actions_guia_remision.php", {op: 9, id: obj.value, s: 0},
                function (dt) {
                    if (dt != 1) {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe');
                        $('#identificacion_destinario').focus();
                        $('#identificacion_destinario').val('');
                        $('#nombre_destinatario').val('');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_guia_remision.php", {op: 9, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                    } else {
                        dat = dt.split('&');
                        $('#identificacion_destinario').val(dat[0]);
                        $('#nombre_destinatario').val(dat[1]);
                        $('#cli_id').val(dat[5]);
                    }
                    $('#con_clientes').hide();
                });

            }

            function load_producto(obj) {
                $.post("actions_guia_remision.php", {op: 10, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    $('#pro_id' + obj.lang).val(dat[0]);
                    $('#cod_producto' + obj.lang).val(dat[1]);
                    $('#descripcion' + obj.lang).val(dat[2]);
                });
            }

            function num_factura(obj) {
                nfac = obj.value;
                if (nfac.length != 17) {
                    $(obj).val('');
                    $('fac_id').val('0');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple con la estructura ejem: 000-000-000000000');
                }
            }

        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
            #observacion{
                text-transform: uppercase;
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
            .obs{
                font-weight:bolder;
                font-size: 11px;
                text-transform: uppercase;
            }

        </style>
    </head>
    <body>

        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE CONTROL <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>

                </thead>
                <tr><td>
                        <table>
                            <tr>

                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision"  value="<?php echo $rst[gui_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1" readonly/></td>
                                <td>FECHA INCIO DE TRASLADO:</td>
                                <td><input type="text" size="20"  id="fecha_inicio_transporte"  value="<?php echo $rst[gui_fecha_inicio] ?>" /><img src="../img/calendar.png" id="im-campo2"/></td>
                                <td>FECHA TERMINACION DE TRASLADO:</td>
                                <td><input type="text" size="20"  id="fecha_fin_transporte"  value="<?php echo $rst[gui_fecha_fin] ?>" /><img src="../img/calendar.png" id="im-campo3"/></td>
                            </tr>
                            <tr>
                                <td>GUIA DE REMISION NO.:</td>
                                <td><input type="text" size="20"  id="num_comprobante"  value="<?php echo $rst[gui_numero] ?>" readonly/></td>
                                <td>TIPO DOCUMENTO:</td>
                                <td><input type="text" size="20"  readonly id="tipo_comprobante" value="<?php echo $rst[tipo_comprobante] ?>"/>
                                <td>FECHA EMISION FACTURA:</td>
                                <td><input type="text" size="20"  id="fecha_comp"  value="<?php echo $rst[gui_fecha_comp] ?>" /><img src="../img/calendar.png" id="im-campo4"/></td>
                            </tr>

                            <tr>
                                <td>NO. DOCUMENTO:</td>
                                <td><input type="text" size="23"  id="num_comprobante_venta"  value="<?php echo $rst[gui_num_comprobante] ?>" onblur="num_factura(this)" maxlength="17" onchange="load_factura(this)"/>
                                    <input type="hidden" size="10"  id="fac_id"  value="<?php echo $rst[fac_id] ?>"/></td>
                                <td>NO. AUTORIZACION FACTURA:</td>
                                <td><input type="text" size="23"  id="autorizacion" value="<?php echo $rst[gui_aut_comp] ?>"/>
                                <td>NO. DECLARACION ADUANERA:</td>
                                <td><input type="text" size="23"  id="documento_aduanero" value="<?php echo $rst[gui_doc_aduanero] ?>"/>
                            </tr>
                            <tr>
                                <td>MOTIVO DEL TRASLADO:</td>
                                <td><input type="text" size="23"  id="motivo_traslado" value="<?php echo $rst[gui_motivo_traslado] ?>"/>
                                <td>PUNTO DE PARTIDA:</td>
                                <td><input type="text" size="23"  id="punto_partida"  value="<?php echo $rst[gui_punto_partida] ?>" /></td>
                                <td>DESTINO:</td>
                                <td><input type="text" size="23"  id="destino"  value="<?php echo $rst[gui_destino] ?>"/></td>
                            </tr>
                            <tr>
                                <td>CEDULA / RUC:</td>
                                <td><input type="text" size="23"  id="identificacion_destinario" maxlength="13" value="<?php echo $rst[gui_identificacion] ?>" onchange="load_cliente(this)" /></td>
                                <td>CLIENTE:</td>
                                <td><input type="text" size="23"  id="nombre_destinatario"  value="<?php echo $rst[gui_nombre] ?>"/>
                                    <input type="hidden" size="10"  id="cli_id"  value="<?php echo $rst['cli_id'] ?>"/></td>
                                <td>COD. ESTABLECIMIENTO DESTINO:</td>
                                <td><input type="text" size="23"  id="cod_establecimiento_destino"  value="<?php echo $rst[gui_cod_establecimiento] ?>"</td>
                            </tr>
                            <tr>
                                <td>CEDULA / RUC TRANSPORTISTA:</td>
                                <td><input type="text" size="23"  id="identificacion_trasportista"  value="<?php echo $rst[gui_identificacion_transp] ?>" onchange="load_transportista(this)" /></td>
                                <td>NOMBRE TRANSPORTISTA:</td>
                                <td><input type="text" size="23"  id="nombre_trasportista"  value="<?php echo $rst[tra_razon_social] ?>"  />
                                    <input type="hidden" size="23"  id="tra_id"  value="<?php echo $rst[tra_id] ?>"  /></td>
                                <td>PLACA:</td>
                                <td><input type="text" size="23"  id="placa"  value="<?php echo $rst[tra_placa] ?>"  /></td>
                            </tr>

                        </table>
                <tr><td>
                        <table id="guia">
                            <thead id="tabla">
                                <tr>
                                    <th>Item</th>
                                    <th>Codigo</th>
                                    <th>Descripcion</th>
                                    <th>Solicitado</th>
                                    <th>Entregado</th>
                                    <th>Saldo</th>
                                    <th>Cantidad</th>
                                    <th></th>
                                <tr>
                            </thead>
                            <tbody id="lista">
                                <?php
                                if (empty($cns)) {
                                    ?>
                                    <tr>                                 
                                        <td align="right"><input type ="text" size="8"  class="item" id="item1" lang="1" value="1" readonly/></td> 
                                        <td><input type ="text" size="20"  id="cod_producto1" lang="1" list="productos" onblur="this.style.width = '100px', load_producto(this)" onfocus="this.style.width = '500px'"/>
                                            <input type ="hidden" size="20"  id="pro_id1" lang="1"/></td>
                                        <td><input type ="text" size="60"  id="descripcion1"  lang="1" readonly/></td>
                                        <td><input type ="text" size="15"  id="cantidadf1"  lang="1" value="0" readonly/></td>
                                        <td><input type ="text" size="15"  id="entregado1"  lang="1" value="0" readonly/></td>
                                        <td><input type ="text" size="15"  id="saldo1"  lang="1" value="0" readonly/>
                                            <input type ="text" size="15"  id="saldox1"  lang="1" hidden/></td>
                                        <td><input type ="text" size="15"  id="cantidad1" lang="1" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), saldo(this)" value="0" /></td>
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" width="12px" src = "../img/del_reg.png"/></td>
                                    </tr>
                                    <?php
                                } else {
                                    $n = 0;
                                    $suma = 0;
                                    while ($rst1 = pg_fetch_array($cns)) {
                                        $n++;
                                        $rst_c = pg_fetch_array($Clase_guia_remision->lista_pro_factura($rst1[pro_id], $rst[fac_id]));
                                        ?>
                                        <tr id="matriz">
                                            <td align="right"><input type ="text" size="8"  class="itm" id="item1"  readonly value="<?php echo $n ?>" lang="<?php echo $n ?>"/></td>
                                            <td><input type ="text" size="20"  id="cod_producto<?php echo $n ?>"  value="<?php echo $rst1[dtg_codigo] ?>" lang="<?php echo $n ?>"/></td>
                                            <td><input type ="text" size="60"  id="descripcion<?php echo $n ?>"  value="<?php echo $rst1[dtg_descripcion] ?>" lang="<?php echo $n ?>" readonly/>
                                                <input type ="hidden" size="10"  id="pro_id<?php echo $n ?>"  value="<?php echo $rst1[pro_id] ?>" lang="<?php echo $n ?>"/></td>
                                            <td><input type ="text" size="15"  id="cantidadf<?php echo $n ?>"  value="<?php echo $rst_c[dfc_cantidad] ?>" lang="<?php echo $n ?>" readonly/></td>
                                            <?php
                                            if ($rst[fac_id] != 0) {
                                                $rst_sum = pg_fetch_array($Clase_guia_remision->suma_cantidad_entregado($rst1[pro_id], $rst[fac_id]));
                                                if ($rst_sum[suma] == '') {
                                                    $rst_sum[suma] = 0;
                                                }
                                                $entr = $rst_sum[suma] - $rst1[dtg_cantidad];
                                                $saldo = $rst_c[dfc_cantidad] - $entr;
                                            }
                                            ?>
                                            <td><input type ="text" size="15"  id="entregado<?php echo $n ?>"  value="<?php echo $entr ?>" lang="<?php echo $n ?>" readonly/></td>
                                            <td><input type ="text" size="15"  id="saldo<?php echo $n ?>"  value="<?php echo $saldo ?>" lang="<?php echo $n ?>" readonly/>
                                                <input type ="text" size="15"  id="saldox<?php echo $n ?>"  value="<?php echo $saldo ?>" lang="<?php echo $n ?>" readonly hidden/></td>
                                            <td><input type ="text" size="15"  id="cantidad<?php echo $n ?>"  value="<?php echo $rst1[dtg_cantidad] ?>" lang="<?php echo $n ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), saldo(this)"/></td>
                                            <td onclick = "elimina_fila(this)" ><img class = "auxBtn" width="12px" src = "../img/del_reg.png"/></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>  
                            </tfoot>
                        </table>
                    </td> </tr>
                <tr>
                    <td> 
                        <table>
                            <tr style="height: 20px"></tr>
                            <tr>
                                <td style="width: 700px">Observaciones: </td>
                            </tr>
                            <tr>
                                <td><textarea id="observacion" style="width:100%"><?php echo $rst[gui_observacion] ?></textarea></td>    
                            </tr>
                        </table>     
                    </td> </tr>
                <tfoot>
                    <tr><td> 
                            <button id="guardar" onclick="frm_save.lang = 1">Guardar</button>    
                            <button id="cancelar" >Cancelar</button>
                        </td> </tr>
                </tfoot>
            </table>

        </form>
    </body>
</html>
<datalist id="productos">
    <?php
    $cns_pro = $Clase_guia_remision->lista_producto_total();
    $n = 0;
    while ($rst_pro = pg_fetch_array($cns_pro)) {
        $n++;
        echo "<option value='$rst_pro[id]'/> $rst_pro[mp_c] $rst_pro[mp_d] </option>";
    }
    ?>
</datalist>


