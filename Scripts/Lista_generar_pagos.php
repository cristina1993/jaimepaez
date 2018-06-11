<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
if (isset($_GET[txt])) {
    $cns = $Cxp->lista_pagos_aprobados();
//echo "okk";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function acciones(sts, obj) {

                if (sts == 1) {
                    img = '<img class="axb" src="../img/exito2.png" onclick="mensaje(1)" />';
                } else {
                    img = '<img class="axb" src="../img/del_reg.png" onclick="mensaje(2)" />';
                }
                id = obj.id.split('_');
                data = Array(sts, id[1]);
                $.post("actions_pago_proveedores.php", {op: 1, 'data[]': data},
                function (dt) {
                    if (dt == 0) {
                        $('#td_' + id[1]).html(img);
                    } else {
                        alert(dt);
                    }
                });
            }

            function mensaje(sms) {
                if (sms == 1) {
                    alert('Pago Aprobado');
                } else {
                    alert('Pago Rechazado');
                }
            }
            function guardar(obl, tip, n, cli) {
                fpago = $('#fpag_' + obl + n).val();//Forma de Pago [1]
                conc = $('#conc_' + obl + n).val().toUpperCase();//Concepto [2]
                doc = $('#doc_' + obl + n).val().toUpperCase();//#de Documento [3]
                cnt = $('#cnt_' + obl + n).val();//Cuenta contable [4]
                fec_pago = $('#fechapago_' + obl + n).val();//Fecha de Pago[5]
                ruc = $('#ruc_' + obl + n).html();
                nombre = $('#nombre_' + obl + n).html();
                valor = $('#cant_' + obl + n).html();               

                 var fields = Array('OBLIGACION=' + obl,
                        'RUC=' + ruc,
                        'PROVEEDOR=' + nombre,
                        'VALOR=' + valor,
                        'FORMA_PAGO=' + fpago,
                        'FECHA_PAGO=' + fec_pago,
                        'CONCEPTO=' + conc,
                        'DOCUMENTO=' + doc,
                        'CUENTA=' + cnt,
                        '');
                if (fpago == 0) {
                    alert('Seleccione una forma de pago');
                    $('#fpag_' + obl + n).focus();
                    $('#fpag_' + obl + n).css('border', 'Solid 1px brown');
                } else if (conc.length < 10) {
                    alert('Debe poner un concepto de pago valido');
                    $('#conc_' + obl + n).focus();
                    $('#conc_' + obl + n).css('border', 'Solid 1px brown');
                } else if (doc.length < 5) {
                    alert('Debe poner un documento de pago valido');
                    $('#doc_' + obl + n).focus();
                    $('#doc_' + obl + n).css('border', 'Solid 1px brown');
                } else if (cnt.length < 15) {
                    alert('Debe poner una cuenta valida');
                    $('#cnt_' + obl + n).focus();
                    $('#cnt_' + obl + n).css('border', 'Solid 1px brown');
                } else if (fec_pago.length < 10) {
                    alert('Debe poner una fecha valida');
                    $('#fechapago_' + obl + n).focus();
                    $('#fechapago_' + obl + n).css('border', 'Solid 1px brown');
                }else {
                    data = Array(obl, fpago, conc, doc, cnt,fec_pago, cli);
                    if (tip == 0) {
                        $.post("actions_pago_proveedores.php", {op: 2, 'data[]': data},
                        function (dt) {
                            var dat = JSON.parse(dt);
                            n = 0;
                            while (n < dat.length) {
                                datos = dat[n].split('&');
                                $.post("actions_ctasxpagar.php", {op: 0, 'data[]': datos, id: datos[0], 'fields[]': fields, x: 0, obl: obl},
                                function (dt) {
                                    if (dt != 0) {
                                        alert(dt);
                                    } else {
                                        if (n == dat.length) {
                                            window.history.go(0);
                                        }
                                    }
                                });
                                n++;
                            }
                        });
                    } else {
                        $.post("actions_pago_proveedores.php", {op: 3, 'data[]': data},
                        function (dt) {
                            if (dt != 0) {
                                alert(dt);
                            } else {
                                if (n == dat.length) {
                                    window.history.go(0);
                                }
                            }
                        });
                    }
                }
            }
            function generar_documentos(cod, fecha, nom, monto, op, egr) {
                parent.document.getElementById('contenedor2').rows = "*,50%";
                frm = parent.document.getElementById('bottomFrame');
                frm.src = '../Scripts/frm_pagos.php?cod=' + cod + '&fecha=' + fecha + '&nom=' + nom + '&monto=' + monto + '&op=' + op + '&egr=' + egr;

            }
            function validar_cuenta(obj) {
                ob = $(obj).val().split('-');
                ob1 = ob[0].split('.');
                if (ob[1] == undefined || ob1.length < 4) {
                    alert('Cuenta contable no existe');
                    $(obj).val('');
                }
            }

            function del(id, x, doc) {
                var r = confirm("Esta Seguro de anular este elemento?");
                if (r == true) {
                    var pass = prompt('Ingrese Codigo de Seguridad de Anulacion', '');
                    if (pass == 'tvkscpgkv') {
                        $.post("actions_pago_proveedores.php", {id: id, op: 4, x: x, doc: doc}, function (dt) {
                            if (dt == 0)
                            {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_generar_pagos.php';
                            } else {
                                alert(dt);
                            }
                        });
                    } else {
                        alert('Codigo Incorrecto');
                        return false;
                    }
                } else {
                    return false;
                }
            }

        </script> 
        <style>
            #mn316{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                /*                background:#ccc;
                                color:black;
                                font-weight:bolder; */
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
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "GENERAR PAGOS" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Proveedor  :<input type="text" name="txt" size="35" id="txt" value="" list="lista_proveedores"/>
                        <datalist id="lista_proveedores">
                            <?php
                            $cns_prov = $Cxp->lista_proveedores();
                            while ($rst_prov = pg_fetch_array($cns_prov)) {
                                echo "<option value='$rst_prov[cli_ced_ruc]'>$rst_prov[cli_raz_social]</option>";
                            }
                            ?>
                        </datalist>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>


                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="6"></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Orden Pago</th>
                    <th>Ruc</th>
                    <th>Proveedor</th>
                    <th>Valor A pagar</th>
                    <th>Formas de Pago</th>
                    <th>Concepto</th>
                    <th>#Documento</th>
                    <th>Cuenta</th>
                    <th>Estado</th>
                    <th>F.Pago</th>
                    <th style="width:100px ">Acciones</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                    if ($rst[obl_tipo] == 1) {
                        $op = 2;
                    } else {
                        $op = 0;
                    }
                    if ($rst[obl_estado_obligacion] == 3) {
                        $sts = 'Pagado';
                        $fpago = $rst[obl_fecha_pago];
                        $concepto = $rst[obl_concepto];
                        $documento = $rst[obl_doc];
                        $cuenta_contable = $rst[obl_cuenta];
                        $forma_pago = $rst[obl_forma_pago];
                        $disabled = 'disabled';
                        $disabled1 = 'disabled';
                    } elseif ($rst[obl_estado_obligacion] == 1 && $rst[obl_tipo] == 1) {
                        $sts = '';
                        $fpago = $rst[obl_fecha_pago];
                        $concepto = $rst[obl_concepto];
                        $documento = $rst[obl_doc];
                        $cuenta_contable = $rst[obl_cuenta];
                        $forma_pago = $rst[obl_forma_pago];
                        $disabled1 = 'disabled';
                        $disabled = '';
                    } else {
                        $sts = '';
                        $fpago = date('Y-m-d');
                        $concepto = '';
                        $documento = '';
                        $cuenta_contable = '';
                        $forma_pago = '';
                        $disabled = '';
                        $disabled1 = '';
                    }
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td ><?php echo $rst[obl_codigo] ?></td>
                        <td ><?php echo $rst[reg_ruc_cliente] ?></td>
                        <td ><?php echo $rst_cli[cli_raz_social] ?></td>
                        <td align="right" id="<?php echo 'cant_' . $rst[reg_ruc_cliente] ?>" ><?php echo number_format($rst[sum], 2) ?></td>
                        <td align="right" >
                            <select <?php echo $disabled1 ?> id="<?php echo 'fpag_' . $rst[obl_codigo] . $n ?>">
                                <option value="0">Seleccione</option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <option value="RETENCION">RETENCION</option>
                                <option value="DEBITO">DEBITO</option>
                                <option value="NOTA DE CREDITO">NOTA DE CREDITO</option>
                                <option value="NOTA DE DEBITO">NOTA DE DEBITO</option>
                            </select>
                            <?php echo "<script>fpag_$rst[obl_codigo]$n.value='$forma_pago'</script>"; ?>
                        </td>
                        <td width="100px"><input <?php echo $disabled1 ?> id="<?php echo 'conc_' . $rst[obl_codigo] . $n ?>" style="text-transform:uppercase" size="50px" value="<?php echo $concepto ?>" /></td>
                        <td width="100px"><input <?php echo $disabled ?> id="<?php echo 'doc_' . $rst[obl_codigo] . $n ?>" style="text-transform:uppercase" size="15px" value="<?php echo $documento ?>" /></td>
                        <td align="left" width="200px"><input <?php echo $disabled ?> type="text" list="cuentas_contables" onchange="validar_cuenta(this)" size="40" id="<?php echo 'cnt_' . $rst[obl_codigo] . $n ?>" value="<?php echo $cuenta_contable ?>" /></td>
                        <td><?php echo $sts; ?></td>
                        <td style="width:200px"><input <?php echo $disabled1 ?> id="<?php echo 'fechapago_' . $rst[obl_codigo] . $n ?>" style="text-transform:uppercase" size="10px" value="<?php echo $fpago ?>" />
                            <?php
                            if ($disabled1 == '') {
                                ?>
                                <img src="../img/calendar.png" id="im-<?php echo $n ?>"/>
                                <?php
                            }
                            ?>
                        </td>

                <script>
                    cal = "<?php echo 'fechapago_' . $rst[obl_codigo] . $n ?>";
                    im = "im-<?php echo $n ?>";
                    Calendar.setup({inputField: cal, ifFormat: "%Y-%m-%d", button: im});
                </script>
                        <td align="right" style="width: 150px">
                            <?php
                            if ($forma_pago == 'CHEQUE' && $sts == 'Pagado') {
                                ?>
                                <img class="axb" height="15px" src="../img/cheque.png" title="Imprimir Cheque" onclick="generar_documentos('<?php echo $rst[obl_codigo] ?>', '<?php echo $fpago ?>', '<?php echo $rst_cli[cli_raz_social] ?>', '<?php echo $rst[sum] ?>', 1)" />                                                        
                                <?php
                            }
                            if ($sts != 'Pagado') {
                                ?>
                                <img class="axb" src="../img/save.png" title="Guardar y generar pagos" onclick="guardar('<?php echo $rst[obl_codigo] ?>', '<?php echo $rst[obl_tipo] ?>', '<?php echo $n ?>','<?php echo $rst_cli[cli_id] ?>')" />                                
                                <?php
                            }
                            ?>
                            <img class="axb" src="../img/orden.png" title="Imprimir Asientos" onclick="generar_documentos('<?php echo $rst[obl_codigo] ?>', '<?php echo $fpago ?>', '<?php echo $rst_cli[cli_raz_social] ?>', '<?php echo $rst[sum] ?>', '<?php echo $op ?>', '<?php echo $rst[obl_num_egreso] ?>')" />
                            <?php
                            if ($Prt->delete == 0) {
                                if ($sts == 'Pagado') {
                                    ?>
                                    <img src="../img/b_delete.png" title="Anular" class="axb" onclick="del('<?php echo $rst[obl_codigo] ?>', '<?php echo $rst[obl_tipo] ?>', '<?php echo $documento ?>')">
                                    <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>            
    </body>   
</html>
<datalist id="cuentas_contables">
    <?php
    $cns_cue = $Cxp->lista_cuentas_bancos();
    while ($rst_cue = pg_fetch_array($cns_cue)) {
        echo "<option value='$rst_cue[pln_codigo]-$rst_cue[pln_descripcion]'>$rst_cue[pln_codigo]-$rst_cue[pln_descripcion]</option>";
    }
    ?>
</datalist>
