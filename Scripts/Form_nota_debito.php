<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota_debito.php';
$Clase_nota_debito = new Clase_nota_debito();
if ($pto_emi > 99) {
    $ems = $pto_emi;
} else if ($pto_emi < 100 && $pto_emi > 9) {
    $ems = '0' . $pto_emi;
} else {
    $ems = '00' . $pto_emi;
}

if (isset($_GET[id])) {
    $id = $_GET[id];
    $det = 1;
    $rst = pg_fetch_array($Clase_nota_debito->lista_una_nota_debito_id($id));
    $cns = $Clase_nota_debito->lista_detalle_nota($id);
    $vnd_id = $rst[vnd_id];
} else {
    $det = 0;
    $id = 0;
    $rst_sec = pg_fetch_array($Clase_nota_debito->lista_secuencial_nota_debito($emisor));
    if (empty($rst_sec)) {
        $sec = $rst_mod[emi_sec_notdeb];
    } else {
        $dat = explode('-', $rst_sec[ndb_numero]);
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
    $rst[fac_id] = '0';
    $rst[ndb_numero] = $ems . '-001-' . $tx . $sec;
    $rst[ndb_fecha_emision] = date('Y-m-d');
    $rst[ndb_fecha_emi_comp] = date('Y-m-d');
    $rst_ven = pg_fetch_array($Clase_nota_debito->lista_vendedor(strtoupper($rst_user[usu_person])));
    $vnd_id = $rst_ven[vnd_id];
    $rst[imp_id] = 0;
    $rst[ndb_cod_ice] = 0;
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
            var num = '<?php echo $num_not_credito ?>';
            var det = '<?php echo $det ?>';
            var vnd_id = '<?php echo $vnd_id ?>';
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#detalle').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#descripcion' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#detalle'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha_emision_comprobante", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (det != 0) {
                    calculo();
                }
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

                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                tr.find("select").attr("name", function () {
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
                    ;
                    this.lang = x;
                    return parts[1] + x;
                });
                $('#detalle').find("tbody tr:last").after(tr);
                $('#descripcion' + x).focus();
                $('#cantidad' + x).val('0');
                $('#item' + x).attr('lang', x);
            }
//====================================================================================================================================================

            function save(id) {
                var data = Array(
                        cli_id.value,
                        vnd_id,
                        usu,
                        num_comprobante.value,
                        motivo.value.toUpperCase(),
                        fecha_emision.value,
                        nombre.value,
                        identificacion.value.toUpperCase(),
                        email_cliente.value,
                        direccion_cliente.value,
                        '1',
                        num_secuencial.value,
                        fecha_emision_comprobante.value,
                        $('#lblsubtotal12').html().replace(',', ''),
                        $('#lblsubtotal0').html().replace(',', ''),
                        $('#lblsubtotalex').html().replace(',', ''),
                        $('#lblsubtotalno').html().replace(',', ''),
                        $('#lbltotal_ice').html().replace(',', ''),
                        $('#lbltotal_iva').html().replace(',', ''),
                        fac_id.value,
                        $('#lbltotal_valor').html().replace(',', ''),
                        telefono_cliente.value,
                        cod_ice.value,
                        imp_id.value,
                        $('#lblsubtotal').html().replace(',', '')
                        );

                var data1 = Array();
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                if (st1.checked == true) {
                    iva = '12';
                } else if (st2.checked == true) {
                    iva = '0';
                } else if (st3.checked == true) {
                    iva = 'NO';
                } else if (st4.checked == true) {
                    iva = 'EX';
                }
                while (n < i) {
                    n++;
                    if ($('#cantidad' + n).val() != null) {

                        cantidad = $("#cantidad" + n).val().replace(',', '');
                        descripcion = $("#descripcion" + n).val().toUpperCase();
                        data1.push(
                                '0&' +
                                descripcion + '&' +
                                cantidad + '&' +
                                '0&' +
                                '0&' +
                                '0&' +
                                '0'
                                );
                    }
                }
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                fields.push('');
                $.ajax({
                    beforeSend: function () {
//                        Validaciones antes de enviar
                        n = 0;
                        amb = $("#ambiente").val();
                        if (amb == 0) {
                            secu = $("#num_comprobante").val();
                            sec = secu.split('-');
                            ns = sec[2];
                            nsec = parseInt(ns);

                            if (fecha_emision.value < fec_val_desde.value || fecha_emision.value > fec_val_hasta.value) {
                                alert('Revisar configuracion autorizacion SRI');
                                return false;
                            } else if (nsec < num_val_desde.value || nsec > num_val_hasta.value) {
                                alert('Revisar configuracion autorizacion SRI');
                                return false;
                            }
                        }
                        if (i != 0) {
                            while (n < i) {
                                n++;
                                if ($('#cantidad' + n).val() != null) {
                                    if ($('#descripcion' + n).val() == 0) {
                                        $('#descripcion' + n).css({borderColor: "red"});
                                        $('#descripcion' + n).focus();
                                        return false;
                                    }
                                    if ($('#cantidad' + n).val() == 0) {
                                        $('#cantidad' + n).css({borderColor: "red"});
                                        $('#cantidad' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (vnd_id == '') {
                            alert('El usuario no es vendedor');
                            return false;
                        }
                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_nota_debito.php',
                    data: {op: 0, 'data[]': data, 'data1[]': data1, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//====================================================================================================================================================

            function cancelar() {
                t = '<?php echo $_GET[txt] ?>';
                d = '<?php echo $_GET[fecha1] ?>';
                h = '<?php echo $_GET[fecha2] ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos';
            }
            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    calculo();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function calculo() {
                var tr = $('#detalle').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                var t12 = 0;
                var t0 = 0;
                var tex = 0;
                var tno = 0;
                var tiva = 0;
                var ice = 0;
                var gtot = 0;
                var st = 0;
                if (st1.checked == true) {
                    ob = '12';
                } else if (st2.checked == true) {
                    ob = '0';
                } else if (st3.checked == true) {
                    ob = 'NO';
                } else if (st4.checked == true) {
                    ob = 'EX';
                }

                while (n < i) {
                    n++;
                    if ($('#item' + n).val() == null) {
                        val = 0;
                    } else {
                        val = $('#cantidad' + n).val().replace(',', '');
                    }
                    st += parseFloat(val);
                }

                if ($('#cod_ice').val() == '') {
                    ic = 0;
                } else {
                    ic = parseFloat($('#cod_ice').val());
                }

                ice = (ic * st) / 100;

                if (ob == '12') {
                    t12 = st;
                }
                if (ob == '0') {
                    t0 = st;
                    tiva = 0;
                }
                if (ob == 'EX') {
                    tex = st;
                    tiva = 0;
                }
                if (ob == 'NO') {
                    tno = st;
                    tiva = 0;
                }


                tiva = ((t12 + ice) * 12 / 100);
                gtot = (t12 * 1 + t0 * 1 + tex * 1 + tno * 1 + tiva * 1 + ice * 1);
                $('#subtotal12').val(t12.toFixed(dec));
                $('#lblsubtotal12').html(t12.toFixed(6));
                $('#subtotal0').val(t0.toFixed(dec));
                $('#lblsubtotal0').html(t0.toFixed(6));
                $('#subtotalex').val(tex.toFixed(dec));
                $('#lblsubtotalex').html(tex.toFixed(6));
                $('#subtotalno').val(tno.toFixed(dec));
                $('#lblsubtotalno').html(tno.toFixed(6));
                $('#subtotal').val(st.toFixed(dec));
                $('#lblsubtotal').html(st.toFixed(6));
                $('#total_ice').val(ice.toFixed(dec));
                $('#lbltotal_ice').html(ice.toFixed(6));
                $('#total_iva').val(tiva.toFixed(dec));
                $('#lbltotal_iva').html(tiva.toFixed(6));
                $('#total_valor').val(gtot.toFixed(dec));
                $('#lbltotal_valor').html(gtot.toFixed(6));
            }
            function load_factura(obj) {
                $.post("actions_nota_debito.php", {op: 6, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#fac_id').val(dat[0]);
                        $('#fecha_emision_comprobante').val(dat[1]);
                        $('#identificacion').val(dat[2]);
                        $('#nombre').val(dat[3]);
                        $('#direccion_cliente').val(dat[4]);
                        $('#telefono_cliente').val(dat[5]);
                        $('#email_cliente').val(dat[6]);
                        $('#cli_id').val(dat[7]);
                    } else {
                        $('#fac_id').val('0');
                    }
                }
                );
            }

            function load_cliente(obj) {
                $.post("actions_nota_debito.php", {op: 5, id: obj.value, s: 0},
                function (dt) {
                    if (dt != 1) {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dt);
                    } else {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_id').val('0');
                    }
                });
            }

            function load_cliente2(obj) {
                $.post("actions_nota_debito.php", {op: 5, id: obj, s: 1},
                function (dt) {
                    if (dt == 0) {
                        alert('Cliente no existe \n Cree uno Nuevo??');
                        $('#nombre').focus();
                        $('#identificacion').val('');
                        $('#nombre').val('');
                        $('#direccion_cliente').val('');
                        $('#telefono_cliente').val('');
                        $('#email_cliente').val('');
                        $('#cli_id').val('0');
                    } else {
                        dat = dt.split('&');
                        $('#identificacion').val(dat[0]);
                        $('#nombre').val(dat[1]);
                        $('#direccion_cliente').val(dat[2]);
                        $('#telefono_cliente').val(dat[3]);
                        $('#email_cliente').val(dat[4]);
                        $('#cli_id').val(dat[5]);
                    }
                    $('#con_clientes').hide();
                });
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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

            function cerrar_ventana() {
                $('#con_clientes').hide();
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

            function load_impuesto(obj, op) {
                i = obj.lang;
                $.post("actions_nota_debito.php", {op: 7, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#cod_ice').val(parseFloat(dat[2]).toFixed(dec));
                        $('#imp_id').val(dat[0]);
                        calculo();
//                        $('#total_ice').val(parseFloat(dat[2]).toFixed(dec));
                    } else {
                        $('#cod_ice').val('0');
                        $('#imp_id').val('0');
                        calculo();

                    }
                });
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            select{
                width: 80px;
            }
            .totales td{
                color: #00529B;
                background-color: #BDE5F8;
                font-weight:bolder;
                font-size: 11px;
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
            <table id="tbl_form" >

                <thead>
                    <tr><th colspan="12" >NOTA DE DEBITO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>   
                <tr><td><table>
                            <tr>
                                <td width="108">NOTA DE DEBITO NO:</td>                    
                                <td><input type="text" size="25"  id="num_comprobante" readonly value="<?php echo $rst[ndb_numero] ?>"  /></td> 
                                <td>FECHA DE EMISION:</td>
                                <td><input type="text" size="20"  id="fecha_emision"  value="<?php echo $rst[ndb_fecha_emision] ?>" /><img src="../img/calendar.png" id="im-campo1"/></td>
                            </tr>         
                            <tr>
                                <td width="108">FACTURA NO:</td>                    
                                <td><input type="text" size="30"  maxlength="17" id="num_secuencial" value="<?php echo $rst[ndb_num_comp_modifica] ?>" onchange="load_factura(this)" onblur="num_factura(this)"/>
                                    <input type="hidden" size="10"  id="fac_id" value="<?php echo $rst[fac_id] ?>"</td> 
                                <td>FECHA EMISION FACT.:</td>
                                <td><input type="text" size="20"  id="fecha_emision_comprobante" value="<?php echo $rst[ndb_fecha_emi_comp] ?>" /><img src="../img/calendar.png" id="im-campo2"/></td>
                            </tr>         
                            <tr>
                                <td>CI/RUC:</td>
                                <td><input type="text" size="30"  id="identificacion" maxlength="13" value="<?php echo $rst[ndb_identificacion] ?>" onchange="load_cliente(this)" /></td>
                                <td>CLIENTE:</td>
                                <td><input type="text" size="30"  id="nombre" value="<?php echo $rst[ndb_nombre] ?>"/>
                                    <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"/></td>
                            </tr>
                            <tr>
                                <td>DIRECCION:</td>
                                <td><input type="text" size="30"  id="direccion_cliente" value="<?php echo $rst[ndb_direccion] ?>"  /></td>
                                <td>TELÉFONO:</td>
                                <td><input type="text" size="30"  id="telefono_cliente" value="<?php echo $rst[ndb_telefono] ?>"  /></td>
                            </tr>
                            <tr>
                                <td>CORREO:</td>
                                <td><input type="email" size="30"  id="email_cliente" style="text-transform:lowercase " value="<?php echo $rst[ndb_email] ?>"  /></td>
                                <td>MOTIVO:</td>
                                <td><input type="text" size="30"  id="motivo" value="<?php echo $rst[ndb_motivo] ?>"  /></td>
                            </tr>
                            <tr>
                                <?php
//                                if ($amb == 0) {
                                    ?>
<!--                                    <td colspan="6">
                                        Valido Desde:
                                        <input type="text" size="7" id="fec_val_desde" value="<?php echo $rst_mod[emi_fec_vald_ntd_desde] ?>" readonly/>
                                        Hasta:
                                        <input type="text" size="7" id="fec_val_hasta" value="<?php echo $rst_mod[emi_fec_vald_ntd_hasta] ?>" readonly/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        N Factura Del:
                                        <input type="text" size="7" id="num_val_desde" value="<?php echo $rst_mod[emi_sec_vald_ntd_desde] ?>" readonly/>
                                        Al:
                                        <input type="text" size="7" id="num_val_hasta" value="<?php echo $rst_mod[emi_sec_vald_ntd_hasta] ?>" readonly/>
                                        <input type="hidden" id="ambiente" value="<?php echo $amb ?>"/>
                                    </td>-->
                                    <?php
//                                }
                                ?>
                            </tr>
                            <tr>
                                <?php
//                                if ($amb == 0) {
                                    ?>
<!--                                    <td>Autorizacion SRI:</td>
                                    <td colspan="6">
                                        <input type="text" size="8" id="autorizacion_sri" value="<?php echo $rst_mod[emi_num_aut_ntd] ?>" readonly/>
                                    </td>-->
                                    <?php
//                                }
                                ?>
                            </tr>
                        </table>
                    </td> 
                </tr>
                <tr>
                    <td>
                        <table id="detalle">
                            <tr id="head">
                            <thead id="tabla">
                            <th>Item</th>
                            <th>Razon de la Modificacion</th>
                            <th>Val. Modificacion</th> 
                            <th>Accion</th>
                            </thead>
                            <?php
                            if ($det == '0') {
                                ?>
                                <tr>
                                    <td><input type="text" size="8" id="item1" readonly class="itm" lang="1" value="1" style="text-align:right" /></td>
                                    <td><input type="text" size="50" id="descripcion1" value="" onblur="this.value = this.value.toUpperCase()"/></td>
                                    <td><input type="text" size="17" id="cantidad1" value=""style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="1"/></td>
                                    <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" width="12px"/></td>
                                </tr>
                                <?php
                            } else {
                                $n = 0;
                                while ($rst_det = pg_fetch_array($cns)) {
                                    $n++;
                                    ?>
                                    <tr>
                                        <td><input type="text" size="8" id="item<?php echo $n ?>" readonly class="itm" lang="<?php echo $n ?>" value="<?php echo $n ?>"   accept=""style="text-align:right" /></td>
                                        <td><input type="text" size="50" id="descripcion<?php echo $n ?>" value="<?php echo $rst_det[dnd_descripcion] ?>" lang="<?php echo $n ?>" onblur="this.value = this.value.toUpperCase()"/></td>
                                        <td><input type="text" size="17" id="cantidad<?php echo $n ?>" value="<?php echo str_replace(',', '', number_format($rst_det[dnd_precio_total], $dec)) ?>"style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, ''), calculo()" lang="<?php echo $n ?>"/></td>
                                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" width="12px" /></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tfoot>
                                <tr>
                                    <td><button id="add_row" onclick="frm_save.lang = 0" >+</button></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal" readonly  value="<?php echo number_format(0, $dec) ?>" style="text-align:right"/>
                                        <label hidden id="lblsubtotal"></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="subtotal12" readonly  value="<?php echo number_format(0, $dec) ?>"style="text-align:right" />
                                        <label hidden id="lblsubtotal12"></label>
                                        <input type="radio" id="st1" name="st" onclick="calculo()"/></td>
                                    <td> </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal 0%:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotal0" value="<?php echo number_format(0, $dec) ?>" style="text-align:right" />
                                        <label hidden id="lblsubtotal0"></label>
                                        <input type="radio" id="st2" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Subtotal No Objeto Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalno" value="<?php echo number_format(0, $dec) ?>" style="text-align:right"/>
                                        <label hidden id="lblsubtotalno"></label>
                                        <input type="radio" id="st3" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total Excento Iva:</td>
                                    <td class="sbtls" ><input type="text" size="15" readonly  id="subtotalex" value="<?php echo number_format(0, $dec) ?>" style="text-align:right"/>
                                        <label hidden id="lblsubtotalex"></label>
                                        <input type="radio" id="st4" name="st" onclick="calculo()"/></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td colspan="2" align="right">ICE %:</td>
                                    <td class="sbtls" ><input type="text" size="18"  id="cod_ice"  value="<?php echo str_replace(',', '', number_format($rst[ndb_cod_ice], $dec)) ?>" list="lista_ice" onfocus="this.style.width = '400px';" onblur="this.style.width = '125px';" onchange="load_impuesto(this, 1)" style="text-align:right"/>
                                        <input type="hidden" size="18" id="imp_id"  value="<?php echo $rst[imp_id] ?>" />
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">ICE $:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="total_ice" readonly onchange="calculo()" value="<?php echo str_replace(',', '', number_format($rst[ndb_total_ice], $dec)) ?>" style="text-align:right" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/>
                                        <label hidden id="lbltotal_ice"></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">IVA 12%:</td>
                                    <td class="sbtls" ><input type="text" size="15" id="total_iva" readonly  value="<?php echo number_format(0, $dec) ?>" style="text-align:right"/>
                                        <label hidden id="lbltotal_iva"></label></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">Total:</td>
                                    <td class="sbtls"><input type="text" size="15" id="total_valor" readonly  value="<?php echo number_format(0, $dec) ?>"  style="text-align:right"/>
                                        <label hidden id="lbltotal_valor"></label></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <!--</tfoot>-->
                <!------------------------------------->
            </table>
        </form>

        <button id="guardar" onclick="save(<?php echo $id ?>)">Guardar</button>   
        <button id="cancelar" >Cancelar</button> 
    </body>
</html>    
<script>
    var iva12 = '<?php echo $rst[ndb_subtotal12] ?>';
    var iva0 = '<?php echo $rst[ndb_subtotal0] ?>';
    var ivaex = '<?php echo $rst[ndb_subtotal_ex_iva] ?>';
    var ivano = '<?php echo $rst[ndb_subtotal_no_iva] ?>';
    if (parseFloat(iva12) > 0) {
        $('#st1').attr('checked', true);
    }
    if (parseFloat(iva0) > 0) {
        $('#st2').attr('checked', true);
    }
    if (parseFloat(ivano) > 0) {
        $('#st3').attr('checked', true);
    }
    if (parseFloat(ivaex) > 0) {
        $('#st4').attr('checked', true);
    }

</script>

<datalist id="lista_ice">
    <?php
    $cns_des = $Clase_nota_debito->lista_impuesto('IC');
    while ($rst_des = pg_fetch_array($cns_des)) {
        echo "<option value='$rst_des[por_id]' >$rst_des[por_porcentage]%  $rst_des[por_descripcion]</option>";
    }
    ?>
</datalist>