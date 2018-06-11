<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_rubros.php';
$Clase_nomina_rubros = new Clase_nomina_rubros();
if (isset($_GET[search])) {
    $lista = $_GET[lst_roles];
    $anio = $_GET[anio];
    $stdo = $_GET[estado];
    if ($lista != 'x' && $anio != 'x' && $stdo != 'x') {
        $text = " nom_periodo='$lista' and nom_anio='$anio' and nom_estado=$stdo";
    } else if ($lista == 'x' && $anio != 'x' && $stdo != 'x') {
        $text = " nom_anio='$anio' and nom_estado=$stdo";
    } else if ($lista == 'x' && $anio == 'x' && $stdo != 'x') {
        $text = " nom_estado=$stdo";
    } else if ($lista != 'x' && $anio == 'x' && $stdo == 'x') {
        $text = " nom_periodo='$lista'";
    } else if ($lista != 'x' && $anio != 'x' && $stdo == 'x') {
        $text = " nom_periodo='$lista' and nom_anio='$anio'";
    } else if ($lista == 'x' && $anio != 'x' && $stdo == 'x') {
        $text = " nom_anio='$anio'";
    }
    $txt = " WHERE $text ";
    $cns = $Clase_nomina_rubros->lista_buscardor_pagos_a_roles($txt);
} else {
    $fecha = date('Y-m-d');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lista Generar Pagos a Roles</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function update(id)
            {
                if ($('#concept' + id).val() == 0) {
                    $('#concept' + id).css({borderColor: "red"});
                    $('#concept' + id).focus();
                    return false;
                } else if ($('#n_doc_ini' + id).val() == 0) {
                    $('#n_doc_ini' + id).css({borderColor: "red"});
                    $('#n_doc_ini' + id).focus();
                    return false;
                } else if ($('#cta_cont' + id).val() == 0) {
                    $('#cta_cont' + id).css({borderColor: "red"});
                    $('#cta_cont' + id).focus();
                    return false;
                } else {
                    f_pago = $('#form_pago' + id).val();
                    concepto = $('#concept' + id).val().toUpperCase();
                    doc_ini = $('#n_doc_ini' + id).val();
                    cta = $('#cta_cont' + id).val();
                    estado = '1';

                    var fields = Array();
                    $("#form_save").find(':input').each(function () {
                        var elemento = this;
                        des = elemento.id + "=" + elemento.value;
                        fields.push(des);
                    });
                }
                data = Array(id, f_pago, concepto, doc_ini, cta, estado);
                $.post("actions_nomina_rubros.php", {op: 6, 'data[]': data, 'fields[]': fields},
                function (dt) {
                    if (dt == 0)
                    {
                        parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_pagos_a_roles.php';
                    } else {
                        alert(dt);
                    }
                });
            }

            function cambiar_estado(std, id) {
                if (std == 0) {
                    est = 1;
                } else {
                    est = 0;
                }
                var r = confirm("Esta Seguro de Cambiar de Estado a este Registro?");
                if (r == true) {
                    $.post("actions_nomina_rubros.php", {op: 8, std: est, id: id},
                    function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_pagos_a_roles.php';
            }

            function seleccionar_todo(obj) {
                n = 0;
                if ($(obj).attr('checked') == true) {
                    $('.todos').each(function () {
                        $(this).attr('checked', true);
                        n++;
                    })
                } else {
                    $('.todos').each(function () {
                        $(this).attr('checked', false);
                        n++;
                    })
                }
            }

            function aplicar() {
                fpag = $('#f_pago').val();
                $('.f_pago').each(function () {
                    $(this).val(fpag);
                });
                con = $('#concepto_p').val();
                $('.concepto').each(function () {
                    $(this).val(con);

                });
                doc = $('#doc_ini').val();
                $('.documento').each(function () {
                    $(this).val(doc);
                    doc++;
                });
                cta = $('#cta_contable').val();
                $('.cuenta_cont').each(function () {
                    $(this).val(cta);

                });
                idc = $('#pln_id').val();
                $('.id').each(function () {
                    $(this).val(idc);
                });
                desc = $('#cta_descrip').val();
                $('.descripcion').each(function () {
                    $(this).html(desc);
                });
            }

            function load_cuenta_principal(obj) {
                $.post("actions_nomina_rubros.php", {op: 1, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#pln_id').val(dat[0]);
                        $('#cta_contable').val(dat[1]);
                        $('#cta_descrip').val(dat[2].substr(0, 30));
                    } else {
                        $('#pln_id').val('');
                        $('#cta_contable').val('');
                        $('#cta_descrip').val('');
                    }
                });
            }

            function load_lista_cuenta(obj) {
                n = obj.lang;
                $.post("actions_nomina_rubros.php", {op: 1, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        $('#cta_id' + n).val(dat[0]);
                        $('#cta_cont' + n).val(dat[1]);
                        $('#descrip' + n).html(dat[2].substr(0, 30));
                    } else {
                        $('#cta_id' + n).val('');
                        $('#cta_cont' + n).val('');
                        $('#descrip' + n).html('');
                    }
                });
            }

            function save() {
                if (todos_c.checked == true) {
                    var data = Array();
                    v = 0;
                    $("#form_save").find('.nom_id').each(function () {
                        n = this.lang;
                        var chbox = document.getElementById('seleccion' + n).checked;
                        if (chbox == true) {
                            dat = $('#id_nomina' + n).val() + '&' +
                                    $('#form_pago' + n).val() + '&' +
                                    $('#concept' + n).val() + '&' +
                                    $('#n_doc_ini' + n).val() + '&' +
                                    $('#cta_cont' + n).val() + '&' +
                                    $('#fec_pago' + n).html() + '&' +
                                    '1';
                            data.push(dat);
                        }
                        if ($('#concept' + n).val() == '') {
                            $('#concept' + n).focus();
                            $('#concept' + n).css({borderColor: "red"});
                            return v = 1;
                        } else if ($('#n_doc_ini' + n).val() == '') {
                            $('#n_doc_ini' + n).focus();
                            $('#n_doc_ini' + n).css({borderColor: "red"});
                            return v = 1;
                        } else if ($('#cta_cont' + n).val() == '') {
                            $('#cta_cont' + n).focus();
                            $('#cta_cont' + n).css({borderColor: "red"});
                            return v = 1;
                        } 
                    });
                    if (v != 0) {
                        return false;
                    }
                    $.post("actions_nomina_rubros.php", {op: 5, 'data[]': data},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_pagos_a_roles.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    var data = Array();
                    v = 0;
                    $("#form_save").find('.nom_id').each(function () {
                        n = this.lang;
                        var chbox = document.getElementById('seleccion' + n).checked;
                        if (chbox == true) {
                            dat = $('#id_nomina' + n).val() + '&' +
                                    $('#form_pago' + n).val() + '&' +
                                    $('#concept' + n).val() + '&' +
                                    $('#n_doc_ini' + n).val() + '&' +
                                    $('#cta_cont' + n).val() + '&' +
                                    $('#fec_pago' + n).html() + '&' +
                                    '1';
                            data.push(dat);
                        }
                        if ($('#concept' + n).val() == '') {
                            $('#concept' + n).focus();
                            $('#concept' + n).css({borderColor: "red"});
                            return v = 1;
                        } else if ($('#n_doc_ini' + n).val() == '') {
                            $('#n_doc_ini' + n).focus();
                            $('#n_doc_ini' + n).css({borderColor: "red"});
                            return v = 1;
                        } else if ($('#cta_cont' + n).val() == '') {
                            $('#cta_cont' + n).focus();
                            $('#cta_cont' + n).css({borderColor: "red"});
                            return v = 1;
                        } 
                    });
                    if (v != 0) {
                        return false;
                    }
                    $.post("actions_nomina_rubros.php", {op: 5, 'data[]': data},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_pagos_a_roles.php';
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function generar_documentos(cod, op) {
                parent.document.getElementById('contenedor2').rows = "*,50%";
                frm = parent.document.getElementById('bottomFrame');
                frm.src = '../Scripts/frm_pagos_a_roles.php?cod=' + cod + '&op=' + op;
            }

            function imprimir_doc() {
                if (todos_c.checked == true) {
                    var data = Array();
                    $("#form_save").find('.nom_id').each(function () {
                        n = this.lang;
                        var chbox = document.getElementById('seleccion' + n).checked;
                        if (chbox == true) {
                            dat = $('#id_nomina' + n).val() + '&' +
                                    $('#op' + n).val();
                            data.push(dat);
                        }
                    });
                    $.post("actions_nomina_rubros.php", {op: 7, 'data[]': data},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('contenedor2').rows = "*,70%";
                            frm = parent.document.getElementById('bottomFrame');
                            frm.src = '../Scripts/frm_pagos_a_roles_masivo.php';
                        } else {
                            alert("Verifique que todos los Roles seleccionados \n Se encuentren con el estado PAGADO");
                        }
                    });
                } else {
                    alert('Debe Marcar la opcion \n Seleccionar Todos');
                }
            }

        </script>
        <style>
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
            }
            .axb{
                padding:2px;
                width: 20px;
                background:#616975;
                border:solid 1px #00529B; 
                margin-left:5px;
                border-radius:5px; 
                cursor:pointer; 
            }
            .axb:hover{
                background:#7198ab; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert('¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
        <table style="width: 100%" id="tbl">
            <caption class="tbl_head">
                <center class="cont_menu">
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float: right" onclick="window.print()" title="Imprimir Documento" src="../img/print_iconop.png" width="16px">
                </center>
                <center class="cont_title">GENERAR PAGOS A ROLES</center>
                <center class="cont_finder">

                    <div style="float:left;margin-top:5px;padding:8px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" id="todos_c" onclick="seleccionar_todo(this)" />SELECCIONAR TODOS  
                    </div>
                    <div style="float:right;margin-top:5px;padding:8px;">
                        <button class="btn" title="Guardar" onclick="aplicar()">Aplicar</button>
                        <button class="btn" title="Guardar" onclick="save()">Guardar</button>
                        <button class="btn" title="Guardar" onclick="imprimir_doc()">Imprimir Doc</button>
                    </div>
                    <div style="float:right;margin-top:5px;padding:8px;">
                        <table>
                            <thead>
                            <th style="text-align: center">FORMA DE PAGO</th>
                            <th style="text-align: center">CONCEPTO</th>
                            <th style="text-align: center"># DOCUMENTO INICIAL</th>
                            <th style="text-align: center">CUENTA CONTABLE</th>
                            </thead>
                            <tr>
                                <td>
                                    <select id="f_pago">
                                        <option value='1'>Transferencia</option>
                                        <option value='2'>Cheque</option>
                                    </select>
                                </td>
                                <td><input type="text" id="concepto_p" /></td>
                                <td><input type="text" id="doc_ini" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')" /></td>
                                <td>
                                    <input type="text" id="cta_contable" onfocus="this.style.width = '300px';" onblur="this.style.width = '100px';" list="ctas_contables" onchange="load_cuenta_principal(this)" />
                                    <input type="hidden" id="pln_id" />
                                    <input type="hidden" id="cta_descrip" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="float:left;margin-top:5px;padding:0px;">
                        &nbsp;&nbsp;&nbsp;
                        <select id="lst_roles" name="lst_roles">
                            <option value='x'>SELECCIONE</option>
                            <?php
                            $cns_lst = $Clase_nomina_rubros->lista_periodo_pago();
                            while ($rst_lst = pg_fetch_array($cns_lst)) {
                                echo "<option value='$rst_lst[nom_periodo]'>$rst_lst[nom_periodo]</option>";
                            }
                            ?>
                        </select>
                        AÑO:
                        <select id="anio" name="anio">
                            <option value='x'>SELECCIONE</option>
                            <?php
                            $cns_anio = $Clase_nomina_rubros->lista_anio_pagos();
                            while ($rst_anio = pg_fetch_array($cns_anio)) {
                                echo "<option value='$rst_anio[nom_anio]'>$rst_anio[nom_anio]</option>";
                            }
                            ?>
                        </select>
                        ESTADO:
                        <select id="estado" name="estado">
                            <option value='x'>SELECCIONE</option>
                            <option value='0'>Pendiente</option>
                            <option value='1'>Pagado</option>
                        </select>
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th></th>
            <th>Seccion</th>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Cedula</th>
            <th>Periodo</th>
            <th>Cuenta Banco</th>
            <th>Total a Pagar</th>
            <th>Forma de Pago</th>
            <th>Concepto</th>
            <th>#Documento</th>
            <th>Cuenta Banco</th>
            <th>Banco</th>
            <th>Fecha Pago</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="form_save">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_emp = pg_fetch_array($Clase_nomina_rubros->lista_empleados_id($rst[nom_empleado]));
                $rst_sec = pg_fetch_array($Clase_nomina_rubros->lista_emp_seccion($rst_emp[sec_id]));
                $rst_in_eg = pg_fetch_array($Clase_nomina_rubros->lista_tot_ingresos_egresos($rst[nom_id]));
                $total = $rst_in_eg[ingreso] - $rst_in_eg[egreso];
                $id_cta = pg_fetch_array($Clase_nomina_rubros->lista_id_cta($rst[nom_cta_contable]));
                switch ($rst[nom_estado]) {
                    case 0:
                        $estado = 'PENDIENTE';
                        break;
                    case 1:
                        $estado = 'PAGADO';
                        break;
                }
                if ($rst[nom_fec_pago] == '') {
                    $fecha = date('Y-m-d');
                } else {
                    $fecha = $rst[nom_fec_pago];
                }
                if ($rst[nom_forma_pago_rol] == '') {
                    $val = 1;
                    $val1 = 2;
                    $fpag = 'Transferencia';
                    $fpag1 = 'Cheque';
                } else {
                    switch ($rst[nom_forma_pago_rol]) {
                        case 1:
                            $val = $rst[nom_forma_pago_rol];
                            $fpag = 'Transferencia';
                            $val1 = 2;
                            $fpag1 = 'Cheque';
                            $op = 1;
                            break;
                        case 2:
                            $val = $rst[nom_forma_pago_rol];
                            $fpag = 'Cheque';
                            $val1 = 1;
                            $fpag1 = 'Transferencia';
                            $op = 2;
                            break;
                    }
                }
                $v = "ondblclick='cambiar_estado($rst[nom_estado], $rst[nom_id])'";
                echo "<tr>
                            <td>$n</td>
                            <td style='text-align: center'>
                                <input type='checkbox' id='seleccion$rst[nom_id]' class='todos' />
                                <input type='hidden' size='1' id='id_nomina$rst[nom_id]' value='$rst[nom_id]' class='nom_id' lang='$rst[nom_id]' >
                            </td>
                            <td>$rst_sec[sec_descricpion]</td>                            
                            <td>$rst_emp[emp_codigo]</td>
                            <td>$rst_emp[emp_nombres] $rst_emp[emp_apellido_paterno] $rst_emp[emp_apellido_materno]</td> 
                            <td>$rst_emp[emp_documento]</td>
                            <td>$rst[nom_periodo] $rst[nom_anio]</td>
                            <td>$rst_emp[emp_cta_bancaria]</td>
                            <td style='text-align: right'>$total</td>
                            <td style='text-align: center'>
                                <select id='form_pago$rst[nom_id]' class='f_pago'>
                                    <option value='$val'>$fpag</option>
                                    <option value='$val1'>$fpag1</option>
                                </select>
                            </td>
                            <td style='text-align: center'><input type='text' id='concept$rst[nom_id]' class='concepto' value='$rst[nom_concepto]'/></td>
                            <td style='text-align: center'><input type='text' id='n_doc_ini$rst[nom_id]' class='documento' value='$rst[nom_num_documento]' /></td>
                            <td style='text-align: center'>";
                ?>
            <input type="text" id="<?php echo 'cta_cont' . $rst[nom_id] ?>" lang="<?php echo $rst[nom_id] ?>" class="cuenta_cont" value="<?php echo $rst[nom_cta_contable] ?>" list="ctas_contables" onchange="load_lista_cuenta(this)" onfocus="this.style.width = '300px';" onblur="this.style.width = '142px';" />
            <?php
            echo "<input type='hidden' id='cta_id$rst[nom_id]' class='id' />
                            </td>
                            <td style='width: 10%' id='descrip$rst[nom_id]' class='descripcion' >$id_cta[pln_descripcion]</td>
                            <td id='fec_pago$rst[nom_id]'>$fecha</td>
                            <td $v>$estado</td>
                            <td align='center'>";
            if ($rst[nom_estado] == 0) {
                echo"<img class='axb' height='18px' src='../img/upd.png' title='Editar' onclick='update($rst[nom_id])' />";
            }
            if ($rst[nom_forma_pago_rol] == 2) {
                echo "<img class='axb' height='18px' src='../img/cheque.png' title='Imprimir Cheque' onclick='generar_documentos($rst[nom_id], 1)' />";
            } else if ($rst[nom_forma_pago_rol] == 1) {
                echo "<img class='axb' height='18px' src='../img/orden.png' title='Transferencia Bancaria' onclick='generar_documentos($rst[nom_id], 0)' />";
            }
            echo"<input type='hidden' size='1' id='op$rst[nom_id]' value='$op'></td>
                            </tr>";
        }
        ?>
    </tbody>
</table>
</body>
</html>
<datalist id="ctas_contables">
    <?php
    $cns_cts = $Clase_nomina_rubros->lista_cuentas_bancos();
    while ($rst_cts = pg_fetch_array($cns_cts)) {
        echo "<option value='$rst_cts[pln_id]'>$rst_cts[pln_codigo] $rst_cts[pln_descripcion]</option>";
    }
    ?>
</datalist>
<script>
    var lsta = '<?php echo $lista ?>';
    $('#lst_roles').val(lsta);
    var ani = '<?php echo $anio ?>';
    $('#anio').val(ani);
    var est = '<?php echo $stdo ?>';
    $('#estado').val(est);
</script>
