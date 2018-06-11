<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_retencion.php';
$Clase_retencion = new Clase_retencion();
if ($emisor == '') {
    $emisor = 1;
}
$ems = '001';
$id = $_GET[id];
$reg_id = $_GET[reg_id];
$txt = $_GET[txt];
$fec1 = $_GET[fecha1];
$fec2 = $_GET[fecha2];
if (empty($reg_id)) {
    if (isset($_GET[id])) {
        $id = $_GET[id];
        $x = $_GET[x];
        $det = '1';
        $rst = pg_fetch_array($Clase_retencion->lista_retencion_id($id));
        $cns = $Clase_retencion->lista_det_retencion($id);
        $vnd_id = $rst[vnd_id];
        $reg_id = $rst[reg_id];
        $fac = pg_fetch_array($Clase_retencion->lista_reg_factura($rst[reg_id]));
        $iva = $fac[reg_iva12];
        $base = $fac[reg_sbt];
    } else {
        $id = '0';
        $x = '0';
        $rst[cli_id] = '0';
        $det = '0';
        $reg_id = '0';
        $rst[ret_fecha_emision] = date('Y-m-d');
        $rst2[dtr_ejercicio_fiscal] = date('m/Y');
        $rst2[dtr_base_imponible] = '0';
        $rst2[dtr_procentaje_retencion] = '0';
        $rst_ven = pg_fetch_array($Clase_retencion->lista_vendedor(strtoupper($rst_user[usu_person])));
        $vnd_id = $rst_ven[vnd_id];
        $rst_sec = pg_fetch_array($Clase_retencion->lista_secuencial_retencion($emisor));
        if (empty($rst_sec)) {
            $sec = 1;
        } else {
            $dat = explode('-', $rst_sec[ret_numero]);
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
        $rst[ret_numero] = $ems . '-001-' . $tx . $sec;
    }
} else {
    $id = '0';
    $x = '0';
    $det = '0';
    $read = 'readonly';
    $disabled = 'disabled';

    $rst_sec = pg_fetch_array($Clase_retencion->lista_secuencial_retencion($emisor));
    if (empty($rst_sec)) {
        $sec = 1;
    } else {
        $dat = explode('-', $rst_sec[ret_numero]);
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


    $rst = pg_fetch_array($Clase_retencion->lista_reg_factura($reg_id));
    $rst[ret_numero] = $ems . '-001-' . $tx . $sec;
    $rst[ret_num_comp_retiene] = $rst[reg_num_documento];
    $rst[ret_fecha_emision] = $rst[reg_femision];
    $rst[ret_tipo_comprobante] = $rst[reg_tipo_documento];
    $rst[ret_nombre] = $rst[cli_raz_social];
    $rst[ret_identificacion] = $rst[cli_ced_ruc];
    $rst[ret_direccion] = $rst[cli_calle_prin];
    $rst[ret_email] = $rst[cli_email];
    $rst[ret_telefono] = $rst[cli_telefono];
    $rst[cli_id] = $rst[cli_id];
    $iva = $rst[reg_iva12];
    $base = $rst[reg_sbt];
    $ej = explode('-', $rst[reg_femision]);
    $rst2[dtr_ejercicio_fiscal] = $ej[1] . '/' . $ej[0];
    $rst_ven = pg_fetch_array($Clase_retencion->lista_vendedor(strtoupper($rst_user[usu_person])));
    $vnd_id = $rst_ven[vnd_id];
}
if ($_SESSION[usuid] != 1) {
    $rs = 'readonly';
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
            var reg_id =<?php echo $reg_id ?>;
            var x =<?php echo $x ?>;
            var comp = '<?php echo $rst[ret_numero] ?>';
            var usu = '<?php echo $emisor ?>';
            var det = '<?php echo $det ?>';
//            var vnd = '<?php echo $vnd_id ?>';
            var vnd = '<?php echo $_SESSION[usuid] ?>';
            $(function () {

                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    var tr = $('#tbl_form').find("tbody tr:last");
                    var a = tr.find("input").attr("lang");
                    if ($('#ejercicio_fiscal' + a).val().length != 0) {
                        if (this.lang == 0) {
                            clona_fila($('#tbl_form'));
                        } else {
                            this.lang = 0;
                        }
                    }
                });
                $('#con_clientes').hide();
                Calendar.setup({inputField: "fecha_emision", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                total();
                posicion_aux_window();
            });

            function save(x) {
                var data = Array(cli_id.value,
                        usu,
                        vnd,
                        num_comprobante.value,
                        nombre.value,
                        identificacion.value,
                        direccion.value,
                        email.value,
                        num_comp_retenido.value,
                        tipo_comprobante.value,
                        telefono.value,
                        $('#total').html(),
                        fecha_emision.value,
                        $('#id_reg_fac').val()
                        );
                var data2 = Array();
                doc = document.getElementsByClassName('itm');
                n = 0;
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                while (n < i) {
                    n++;
                    if ($('#ejercicio_fiscal' + n).val() != null) {
                        imp = $('#por_id' + n).val().split('_');
                        por_tipo = imp[1];
                        por_id = imp[0];
                        ejercicio_fiscal = $('#ejercicio_fiscal' + n).val();
                        base_imponible = $('#base_imponible' + n).val();
                        codigo = $('#codigo' + n).val();
                        porcentaje_retencion = $('#porcentaje_retencion' + n).val();
                        valor_retenido = $('#valor_retenido' + n).val();
                        data2.push(
                                por_id + '&' +
                                ejercicio_fiscal + '&' +
                                base_imponible + '&' +
                                por_tipo + '&' +
                                codigo + '&' +
                                porcentaje_retencion + '&' +
                                valor_retenido
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
                        } else if ($('#num_comp_retenido').val().length == 0) {
                            $('#num_comp_retenido').css({borderColor: "red"});
                            $('#num_comp_retenido').focus();
                            return false;
                        } else if ($('#identificacion').val().length == 0) {
                            $('#identificacion').css({borderColor: "red"});
                            $('#identificacion').focus();
                            return false;
                        } else if ($('#telefono').val().length == 0) {
                            $('#telefono').css({borderColor: "red"});
                            $('#telefono').focus();
                            return false;
                        } else if ($('#nombre').val().length == 0) {
                            $('#nombre').css({borderColor: "red"});
                            $('#nombre').focus();
                            return false;
                        } else if ($('#direccion').val().length == 0) {
                            $('#direccion').css({borderColor: "red"});
                            $('#direccion').focus();
                            return false;
                        } else if ($('#email').val().length == 0) {
                            $('#email').css({borderColor: "red"});
                            $('#email').focus();
                            return false;
                        }
                        var tr = $('#tbl_form').find("tbody tr:last");
                        var a = tr.find("input").attr("lang");
                        n = 0;
                        if (i != 0) {
                            while (n < doc.length) {
                                n++;
                                if ($('#ejercicio_fiscal' + n).val() != null) {
                                    if ($('#ejercicio_fiscal' + n).val().length == 0) {
                                        $('#ejercicio_fiscal' + n).css({borderColor: "red"});
                                        $('#ejercicio_fiscal' + n).focus();
                                        return false;
                                    } else if ($('#descripcion_impuesto' + n).val().length == 0) {
                                        $('#descripcion_impuesto' + n).css({borderColor: "red"});
                                        $('#descripcion_impuesto' + n).focus();
                                        return false;
                                    } else if ($('#base_imponible' + n).val().length == 0) {
                                        $('#base_imponible' + n).css({borderColor: "red"});
                                        $('#base_imponible' + n).focus();
                                        return false;
                                    } else if ($('#codigo' + n).val().length == 0) {
                                        $('#codigo' + n).css({borderColor: "red"});
                                        $('#codigo' + n).focus();
                                        return false;
                                    } else if ($('#porcentaje_retencion' + n).val().length == 0) {
                                        $('#porcentaje_retencion' + n).css({borderColor: "red"});
                                        $('#porcentaje_retencion' + n).focus();
                                        return false;
                                    }
                                }
                            }
                        }
                        if (id != 0) {
                            if (comp != $('#num_comprobante').val()) {
                                alert('No se puede modificar numero de Retencion');
                                $('#num_comprobante').css({borderColor: "red"});
                                $('#num_comprobante').focus();
                                return false;
                            }
                        }
//                        if (vnd == '') {
//                            alert('El usuario no es vendedor');
//                            return false;
//                        }

                        loading('visible');
                    },
                    type: 'POST',
                    url: 'actions_retencion.php',
                    data: {op: 0, 'data[]': data, 'data2[]': data2, 'fields[]': fields, id: id, x: x}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
//                        alert(dt);
                        loading('hidden');
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            imprimir_asiento(dat[1]);
                        } else if (dat[0] == 1) {
                            alert('Numero Secuencial de la Retencion ya existe \n Debe hacer otra Retencion con otro Secuencial');
                        } else if (dat[0] == 2) {
                            alert('Una de las cuentas del Registro de Facturas esta inactiva');
                            loading('hidden');
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }

            function cancelar() {
                d = '<?php echo $fec1 ?>';
                h = '<?php echo $fec2 ?>';
                t = '<?php echo $txt ?>';
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                if (reg_id == 0 || x >= 1) {
                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_retencion.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos//Cambiar Form_productos
                } else {
                    parent.document.getElementById('mainFrame').src = "../Scripts/Lista_registro_facturas.php?txt=&desde=<?php echo date('Y-m-d') ?>&hasta=<?php echo date('Y-m-d') ?>";

                }
            }

            function clona_fila(table) {
                var tr = $(table).find("tbody tr:last").clone();
                tr.find("input").attr("name", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    return parts[1] + ++parts[2];
                }).attr("id", function () {
                    var parts = this.id.match(/(\D+)(\d+)$/);
                    x = ++parts[2];
                    this.lang = x;
                    if (parts[1] == 'cantidad') {
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
                $('#ejercicio_fiscal' + x).focus();
                $('#base_imponible' + x).val('0');
                $('#valor_retenido' + x).val('0');
            }

            function elimina_fila(obj) {
                itm = $('.itm').length;
                if (itm > 1) {
                    var parent = $(obj).parents();
                    $(parent[0]).remove();
                    total();
                } else {
                    alert('No puede eliminar todas las filas');
                }
            }

            function descripcion(obj) {
                $('#frm_save').attr('lang', 1);
                d = obj.value;
                n = obj.lang;
                $.post('actions_retencion.php', {op: 2, id: d}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] == 0) {
                        $(obj).val('');
                        $(obj).focus();
                        $(obj).css({borderColor: "red"});
                    } else {
                        if (dat[5] == 0) {
                            alert('El impuesto no tiene una Cuenta Contable asignada');
                            $('#descripcion_impuesto' + n).val('');
                            $('#descripcion_impuesto' + n).focus();
                        } else if (dat[6] == 1) {
                            alert('La Cuenta Contable del Impuesto \n Esta Inactiva');
                            $('#descripcion_impuesto' + n).val('');
                            $('#descripcion_impuesto' + n).focus();
                        } else {
                            $('#descripcion_impuesto' + n).val(dat[0]);
                            $('#porcentaje_retencion' + n).val(dat[1]);
                            $('#codigo' + n).val(dat[2]);
                            $('#por_id' + n).val(dat[3]);
                            $('#num_comp_retenido' + n).attr('readonly', true);
                            var iva = parseFloat($('#iva').val());
                            var base = parseFloat($('#base').val());
                            if (dat[4] == 'IV') {
                                $('#base_imponible' + n).val(parseFloat(iva).toFixed(2));
                            } else if (dat[4] == 'IR') {
                                $('#base_imponible' + n).val(parseFloat(base).toFixed(2));
                            }
                            s(obj);
                            total();
                        }
                    }
                })
            }

            function s(obj) {
                n = obj.lang;
                var vt = (parseFloat($('#base_imponible' + n).val()) * parseFloat($('#porcentaje_retencion' + n).val())) / 100;
                $('#valor_retenido' + n).val(vt.toFixed(2));
            }

            function total() {
                var tr = $('#tbl_form').find("tbody tr:last");
                a = tr.find("input").attr("lang");
                i = parseInt(a);
                n = 0;
                sum = 0;
                while (n < i) {
                    n++;
                    if ($('#item' + n).val() != null) {
                        if ($('#valor_retenido' + n).val().length == 0) {
                            can = 0;
                        } else {
                            can = $('#valor_retenido' + n).val()
                        }
                        sum = sum + parseFloat(can);
                    }
                }
                $('#total').html(sum.toFixed(2));
            }

            function load_cliente(obj) {
                $.post("actions_retencion.php", {op: 4, id: obj.value, s: 0},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#nombre').focus();
                            }
                        });
            }

            function load_cliente2(obj) {
                $.post("actions_retencion.php", {op: 4, id: obj, s: 1},
                        function (dt) {
                            if (dt == 0) {
                                alert('Cliente no existe \n Cree uno Nuevo??');
                                $('#nombre').focus();
                            } else {
                                dat = dt.split('&');
                                $('#identificacion').val(dat[0]);
                                $('#nombre').val(dat[1]);
                                $('#direccion').val(dat[2]);
                                $('#telefono').val(dat[3]);
                                $('#email').val(dat[4]);
                                $('#cli_id').val(dat[7]);
                            }
                            $('#con_clientes').hide();
                        });
            }

            function num_comp(obj) {
                comp = obj.value;
                dt = comp.split('-');
                if (comp.length != 17 || dt[0].length != 3 || dt[1].length != 3 || dt[2].length != 9) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple la estructura ejem: 000-000-000000000');
                }
            }

            function ejercio(obj) {
                ejer = obj.value;
                dt = ejer.split('/');
                if (ejer.length != 7 || dt[0].length != 2 || dt[1].length != 4) {
                    $(obj).val('');
                    $(obj).focus();
                    $(obj).css({borderColor: "red"});
                    alert('No cumple la estructura ejem: 01/2015');
                }
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 600) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 590) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_documento(obj) {
                tp_doc = $('#tipo_comprobante').val();
                $.post("actions_retencion.php", {op: 8, doc: obj.value, tdoc: tp_doc, s: 0},
                        function (dt) {
                            if (dt != '') {
                                $('#con_clientes').css('visibility', 'visible');
                                $('#con_clientes').show();
                                $('#clientes').html(dt);
                            } else {
                                alert('No existe Numero de Documento');
                                $('#tipo_comprobante').attr('disabled', false);
                                $('#num_comp_retenido').focus();
                                $('#num_comp_retenido').val('');
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#direccion').val('');
                                $('#telefono').val('');
                                $('#email').val('');
                                $('#base').val('0');
                                $('#iva').val('0');
                            }
                        }
                );
            }

            function load_documento2(obj, doc) {
                $.post("actions_retencion.php", {op: 8, id: obj, doc: doc, s: 1},
                        function (dt) {
                            if (dt == 0) {
                                alert('No existe Numero de Documento');
                                $('#num_comp_retenido').focus();
                                $('#num_comp_retenido').val('');
                                $('#identificacion').val('');
                                $('#nombre').val('');
                                $('#direccion').val('');
                                $('#telefono').val('');
                                $('#email').val('');
                                $('#base').val('');
                                $('#iva').val('');
                            } else {
                                dat = dt.split('&');
                                $('#identificacion').val(dat[0]);
                                $('#nombre').val(dat[1]);
                                $('#direccion').val(dat[2]);
                                $('#telefono').val(dat[3]);
                                $('#email').val(dat[4]);
                                $('#id_reg_fac').val(dat[7]);
                                $('#base').val(dat[8]);
                                $('#iva').val(dat[9]);
                            }
                            $('#con_clientes').hide();
                        });
            }

            function imprimir_asiento(asi) {
                if (asi != 0) {
                    var r = confirm("Desea generar la Impresion del asiento contable");
                    if (r == true) {
                        frm = parent.document.getElementById('bottomFrame').src = '../Scripts/frm_pdf_asientos.php?id=' + asi + '&asi=1';
                    } else {
                        cancelar();
                    }
                } else {
                    cancelar();
                }
            }

            function validar_fechas(obj) {
                fecha = obj.value;
                f = (fecha).match(/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/);
                var fec = fecha.split('-');
                var aho = fec[0];
                var mes = fec[1];
                var dia = fec[2];

                var fv = new Date(aho, mes - 1, dia);//mes empieza de cero Enero = 0

                if (!fv || fv.getFullYear() == aho && fv.getMonth() == mes - 1 && fv.getDate() == dia) {
                    f = f;
                } else {
                    f = null;
                }

                if (f == null) {
                    $(obj).focus();
                    $(obj).css('border', 'solid 2px red');
                    $(obj).val('');
                } else {
                    $(obj).css('border', '');
                }
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
                width: 150px;
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
            <table id="tbl_form"  >
                <thead>
                    <tr><th colspan="9" >RETENCION<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
                <tr>
                    <td>RETENCION NO.:</td>
                    <td><input type="text" size="20"  id="num_comprobante" value="<?php echo $rst[ret_numero] ?>"  maxlength="17" onchange="num_comp(this)"/></td>
                    <td>FECHA DE EMISION:</td>
                    <td><input type="text" size="12"  id="fecha_emision"  value="<?php echo $rst[ret_fecha_emision] ?>" maxlength="10" onchange="validar_fechas(this)" />
                        <img width="16px" src="../img/calendar.png" id="im-campo1"/>
                    </td>
                    <td>TIPO COMPROBANTE:</td>
                    <td> <select id="tipo_comprobante" <?php echo $disabled ?> >
                            <option value="1">FACTURA</option>
                            <option value="4">NOTA CREDITO</option>
                        </select>
                    </td>
                    <td>COMPROBANTE RETENIDO #:</td>
                    <td><input type="text" size="20"  id="num_comp_retenido" maxlength="17" value="<?php echo $rst[ret_num_comp_retiene] ?>" onblur="num_comp(this)" onkeyup="this.value = this.value.replace(/[^0-9-]/, '')" onchange="load_documento(this)" <?php echo $read ?> />
                        <input type="hidden" size="20"  id="id_reg_fac"  value="<?php echo $rst[reg_id] ?>"/></td>
                </tr>
                <tr>
                    <td>CLIENTE:</td>
                    <td colspan="3"><input type="text" size="60"  id="nombre" value="<?php echo $rst[ret_nombre] ?>" onchange="load_cliente(this)" readonly/>
                        <input type="hidden" size="10"  id="cli_id" value="<?php echo $rst[cli_id] ?>"/></td>
                    <td>RUC:</td>
                    <td><input type="text" size="17"  id="identificacion" value="<?php echo $rst[ret_identificacion] ?>" readonly /></td>

                    <td>TELEFONO:</td>
                    <td><input type="text" size="17"  id="telefono" value="<?php echo $rst[ret_telefono] ?>" readonly /></td>
                </tr>
                <tr>
                    <td>DIRECCION:</td>
                    <td colspan="3"><input type="text" size="60"  id="direccion" value="<?php echo $rst[ret_direccion] ?>" readonly /></td>
                    <td>EMAIL:</td>
                    <td><input type="email" size="17"  id="email" value="<?php echo $rst[ret_email] ?>" style="text-transform: lowercase" readonly/>
                        <input type="hidden" id="base" size="15" value="<?php echo $base ?>"/>
                        <input type="hidden" id="iva" size="15" value="<?php echo $iva ?>"/></td>
                </tr>
                <tr>
                    <?php
//                    if ($amb == 0) {
                        ?>
<!--                        <td>Autorizacion SRI:</td>
                        <td colspan="9">
                            <input type="text" size="8" id="autorizacion_sri" value="<?php echo $rst_mod[emi_num_aut_ret] ?>" readonly/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            Valido Desde:
                            <input type="text" size="8" id="fec_val_desde" value="<?php echo $rst_mod[emi_fec_vald_ret_desde] ?>" readonly/>
                            Hasta:
                            <input type="text" size="8" id="fec_val_hasta" value="<?php echo $rst_mod[emi_fec_vald_ret_hasta] ?>" readonly/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            N Retencion Del:
                            <input type="text" size="7" id="num_val_desde" value="<?php echo $rst_mod[emi_sec_vald_ret_desde] ?>" readonly/>
                            Al:
                            <input type="text" size="7" id="num_val_hasta" value="<?php echo $rst_mod[emi_sec_vald_ret_hasta] ?>" readonly/>
                            <input type="hidden" id="ambiente" value="<?php echo $amb ?>"/>
                        </td>-->
                        <?php
//                    }
                    ?>
                </tr>
                <tr id="head">
                <thead id="tabla">
                <th>ITEM</th>
                <th>EJERCICIO FISCAL</th>
                <th colspan="2">IMPUESTO</th>
                <th>BASE IMP. DE RETENCION</th>
                <th>CODIGO DEL IMPUESTO</th>
                <th>% DE RETENCION</th>
                <th>VALOR RETENIDO</th>
                <th>Acciones</th>
                </thead>
                <?php
                if ($det == '0') {
                    ?>
                    <tr>
                        <td align = "right"><input type = "text" size = "8" class = "itm" id = "item1" readonly value = "1" lang="1"/>
                        <td><input type = "text" size = "20" id = "ejercicio_fiscal1" value = "<?php echo $rst2[dtr_ejercicio_fiscal] ?>" lang = "1" onblur="ejercio(this)" onkeyup = "this.value = this.value.replace(/[^0-9/]/, '')" maxlength="7"/></td>
                        <td colspan = "2"><input type = "text" size = "50" id = "descripcion_impuesto1" lang = "1" list = "lista_descripcion" onchange = "descripcion(this)" /></td>
                        <td><input type = "text" size = "20" id = "base_imponible1" value = "<?php echo $rst2[dtr_base_imponible] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, ''), total()" lang = "1" /></td>
                        <td><input type = "text" size = "20" id = "codigo1" value = "<?php echo $rst2[dtr_codigo_impuesto] ?>" list = "lista_porcentaje" lang = "1"/>
                            <input type = "text" size = "20" id = "por_id1" value = "" hidden /></td>
                        <td><input type = "text" size = "20" id = "porcentaje_retencion1" value = "<?php echo $rst2[dtr_procentaje_retencion] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, ''), total()" lang = "1" /></td>
                        <td><input type = "text" size = "20" id = "valor_retenido1" value = "" lang = "1" class = "cnt" readonly /></td>
                        <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                    </tr>
                    <?php
                } else if ($det == '1') {
                    $n = 0;
                    $cns2 = $Clase_retencion->lista_det_retencion($id);
                    while ($rst2 = pg_fetch_array($cns2)) {
                        $n++;
                        ?>
                        <tr>
                            <td align = "right"><input type = "text" size = "8" class = "itm" id = "item<?php echo $n ?>" readonly value = "<?php echo $n ?>" lang="<?php echo $n ?>"/>
                            <td><input type = "text" size = "20" id = "ejercicio_fiscal<?php echo $n ?>" value = "<?php echo $rst2[dtr_ejercicio_fiscal] ?>" lang = "<?php echo $n ?>" onblur="ejercio(this)" onkeyup = "this.value = this.value.replace(/[^0-9/]/, '')" maxlength="7"/></td>
                            <td colspan = "2"><input type = "text" size = "50" id = "descripcion_impuesto<?php echo $n ?>" value = "<?php echo $rst2[por_descripcion] ?>"  lang = "<?php echo $n ?>" list = "lista_descripcion" onchange = "descripcion(this)"/></td>
                            <td><input type = "text" size = "30" id = "base_imponible<?php echo $n ?>" value = "<?php echo $rst2[dtr_base_imponible] ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, ''), total()" lang = "<?php echo $n ?>" /></td>
                            <td><input type = "text" size = "20" id = "codigo<?php echo $n ?>" value = "<?php echo $rst2[dtr_codigo_impuesto] ?>" list = "lista_porcentaje" lang = "<?php echo $n ?>" readonly/>
                                <input type = "text" size = "20" id = "por_id<?php echo $n ?>" value = "<?php echo $rst2[por_id] . '_' . $rst2[dtr_tipo_impuesto] ?>" hidden/></td>
                            <td><input type = "text" size = "20" id = "porcentaje_retencion<?php echo $n ?>" value = "<?php echo $rst2[dtr_procentaje_retencion] ?>" lang = "<?php echo $n ?>" onkeyup = "s(this), this.value = this.value.replace(/[^0-9.]/, ''), total()"/></td>
                            <td><input type = "text" size = "20" id = "valor_retenido<?php echo $n ?>" value = "<?php echo $rst2[dtr_valor] ?>" lang = "<?php echo $n ?>" class = "cnt" readonly /></td>
                            <td onclick = "elimina_fila(this)" ><img class = "auxBtn" src = "../img/b_delete.png" /></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tfoot>
                    <tr class = "totales">
                        <td><button id = "add_row">+</button></td>
                        <td></td>
                        <td colspan = "5" align = "right">Total:</td>
                        <?PHP
                        if ($fila != "0") {
                            ?>
                            <td align="right" style="font-size:15px; " id="total"><?php echo $suma ?></td>
                            <?PHP
                        } else {
                            ?>
                            <td align="right" style="font-size:15px; " id="total">0</td>
                            <?PHP
                        }
                        ?>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </form>
        <?PHP
        if ($x != 1) {
            ?> 
            <button id="guardar" onclick="save('<?php echo $det ?>')">Guardar</button>   
            <?PHP
        }
        ?>
        <button id="cancelar" >Cancelar</button>    
    </body>
</html>    
<datalist id="lista_porcentaje">
    <?php
    while ($rst_por = pg_fetch_array($cns_por)) {
        echo "<option value='$rst_por[por_codigo]' >$rst_por[por_codigo]</option>";
    }
    ?>
</datalist>
<datalist id="lista_descripcion">
    <?php
    $cns_des = $Clase_retencion->lista_porcentaje();
    while ($rst_des = pg_fetch_array($cns_des)) {
        echo "<option value='$rst_des[por_id]' >$rst_des[por_codigo]  $rst_des[por_descripcion]</option>";
    }
    ?>
</datalist>

<script>
    var mot = '<?php echo $rst[tipo_comprobante] ?>';
    $('#tipo_comprobante').val(mot);

</script>