<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
if (isset($_GET[txt])) {
//    $nm = trim(strtoupper($_GET[txt]));
//    if (!empty($_GET[txt])) {
//        echo $txt = "WHERE reg_ruc_cliente like '%$nm%' or reg_num_documento LIKE '%$nm%' or reg_concepto LIKE '%$nm%'";
//    } else {
//        
//    }
    //$txt = "WHERE reg_ruc_cliente like '%1724396807%'";
    //$cns = $Cxp->lista_documentos_pago_pendiente($txt);
    $ruc = $_GET[txt];
}
$today = date('Y-m-d');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Pago Proveedores</title>
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

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                fec1 = $('#desde').val();
                fec2 = $('#hasta').val();
                txt = $('#txt').val();
                est = $('#estado').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Editar
                        frm.src = '../Scripts/Form_ctasxpagar.php?id=' + id + '&fec1=' + fec1 + '&fec2=' + fec2 + '&estado=' + est + '&nm=' + txt;//Cambiar Form_productos
                        look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,70%";
                        break;
                    case 1://Reporte estado cuenta
                        frm.src = '../Scripts/frm_pdf_estado_pagar.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&cli=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;

                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function load_valor(obj, pf) {
                tot = 0;
                $('.' + pf + ':checked').each(function () {
                    ob = this.id.split('_');
                    ob2 = $('#' + pf + 'vreg_' + ob[1]).val().replace(',', '');
                    tot = parseFloat(tot) + parseFloat(ob2);
                });

                $('#' + pf + 'tot_general').val(tot);

                vpv = parseFloat($('#pvtot_general').val());
                vv = parseFloat($('#vtot_general').val());

                $('#tot_general').val((vpv * 1) + (vv * 1));

            }

            function validar_saldo(obj, pf) {
                id = obj.id.split('_');
                vant = parseFloat($('#' + pf + 'vreal_' + id[1]).val());
                vact = parseFloat($(obj).val());
                if (vact > vant) {
                    alert('El valor a pagar no puede ser mayor al saldo');
                    $(obj).val(vant);
                } else {
                    load_valor(this, pf);
                }

            }

            function generar_obligacion_pago() {
                tot = 0;
                data = Array();
                $('input:checked').each(function () {
                    ob = this.id.split('_');
                    sts = this.lang;
                    pf = this.className;
                    ob2 = parseFloat($('#' + pf + 'vreg_' + ob[1]).val().replace(',', ''));
                    tot = parseFloat(tot) + parseFloat(ob2);
                    data.push(ob[1] + '&' + ob2 + '&' + sts);
                });
                $.post("actions_pago_proveedores.php", {op: 0, 'data[]': data},
                function (dt) {
                    if (dt != 0) {
                        alert(dt);
                    } else {
                        window.history.go(0);
                    }
                });


            }
        </script> 
        <style>
            #mn314{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
            #cont_btn{
                background:white !important;
            }
            #cont_btn:hover{
                background:white !important; 
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
                <center class="cont_title" ><?php echo "PAGO A PROVEEDORES" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Proveedor  :<input type="text" name="txt" size="35" id="txt" value="" list="lista_proveedores"/>
                        <datalist id="lista_proveedores">
                            <?php
                            $cns_prov = $Cxp->lista_proveedores();
                            while ($rst_prov = pg_fetch_array($cns_prov)) {
                                echo "<option value='$rst_prov[cli_ced_ruc]'>$rst_prov[cli_ced_ruc] $rst_prov[cli_raz_social]</option>";
                            }
                            ?>
                        </datalist>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>


                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="12">PAGOS POR VENCER</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Ruc</th>
                    <th>Proveedor</th>
                    <th>Documento</th>
                    <th>Concepto</th>
                    <th>Fecha Emision</th>
                    <th>Fecha Vencimiento</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $cns = $Cxp->lista_pagos_por_vencer($today, $ruc);
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                    $rst_pagos = pg_fetch_array($Cxp->lista_pagos_documento($rst['reg_id']));
                    $rst_pagos_ndebito = pg_fetch_array($Cxp->lista_pagos_ndebito($rst['reg_id']));
                    //$rst_obl = pg_fetch_array($Cxp->lista_estado_obligacion_pago($rst[pag_id]));                    
                    $cns_obl = $Cxp->lista_estado_obligacion_pago($rst[pag_id]);
                    $x = 0;
                    while ($rst_obl = pg_fetch_array($cns_obl)) {
                        if ($rst_obl[obl_estado_obligacion] <> 3) {
                            $x = 1;
                        }
                    }
                    $pagado = $rst_pagos[sum];
                    $val_pagar = $rst['reg_total'] + $rst_pagos_ndebito[debito];
                    $saldo = $val_pagar - $pagado;
                    if ($pagado == 0) {
                        $estado = 'Por pagar';
                    } else if (round($saldo, 2) == 0) {
                        $estado = 'Pagado';
                    } else {
                        $estado = 'Semi Pagado';
                    }

                    if ($x == 1 || round($saldo, 2) == 0) {
                        $obl = 'disabled';
                    } else {
                        $obl = '';
                    }

                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['reg_ruc_cliente'] ?></td>
                        <td><?php echo $rst_cli['cli_raz_social'] ?></td>
                        <td><?php echo $rst['reg_num_documento'] ?></td>
                        <td><?php echo $rst['reg_concepto'] ?></td>
                        <td><?php echo $rst['reg_femision'] ?></td>
                        <td><?php echo $rst['pag_fecha_v'] ?></td>
                        <td align="right"><?php echo number_format($val_pagar, 2) ?></td>
                        <td align="right"><?php echo number_format($pagado, 2) ?></td>
                        <td align="right"><?php echo number_format($saldo, 2) ?></td>
                        <td align="center"><?php echo $estado ?></td>
                        <td>
                            <input <?php echo $obl ?> class="pv" type="checkbox" id="<?php echo 'pvchreg_' . $rst['pag_id'] ?>" lang="0" onchange="load_valor(this, 'pv')" />
                            <input type="hidden" size="10" id="<?php echo 'pvvreal_' . $rst['pag_id'] ?>" lang="<?php echo $sts ?>"  value="<?php echo number_format($saldo, 2) ?>"  />
                            <input <?php echo $obl ?> type="text" size="10" id="<?php echo 'pvvreg_' . $rst['pag_id'] ?>" lang="<?php echo $sts ?>" style="text-align:right"  value="<?php echo number_format($saldo, 2) ?>" onchange="validar_saldo(this, 'pv')" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr style="height:30px ">
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td align="right" >Total Por Vencer:</td>
                    <td>
                        <input size="10" readonly id="pvtot_general" value="0" />

                    </td>
                </tr>            

            </tbody>
            <!--////VENCIDOS-->
            <thead>
                <tr>
                    <th colspan="12">VENCIDOS</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Ruc</th>
                    <th>Proveedor</th>
                    <th>Documento</th>
                    <th>Concepto</th>
                    <th>Fecha Emision</th>
                    <th>Fecha Vencimiento</th>
                    <th>Total</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $cns = $Cxp->lista_pagos_vencidos($today, $ruc);
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                    $rst_obl = pg_fetch_array($Cxp->lista_estado_obligacion_pago($rst[pag_id]));
                    $rst_pagos = pg_fetch_array($Cxp->lista_pagos_documento($rst['reg_id']));
                    $rst_pagos_ndebito = pg_fetch_array($Cxp->lista_pagos_ndebito($rst['reg_id']));
                    $obl = '';

                    $pagado = $rst_pagos[sum];
                    $val_pagar = $rst['reg_total'] + $rst_pagos_ndebito[debito];
                    $saldo = $val_pagar - $pagado;
                    if ($pagado == 0) {
                        $estado = 'Por pagar';
                    } else if (round($saldo, 2) == 0) {
                        $estado = 'Pagado';
                    } else {
                        $estado = 'Semi Pagado';
                    }
                    if (!empty($rst_obl) && round($saldo, 2) == 0) {
                        $obl = 'disabled';
                    }
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['reg_ruc_cliente'] ?></td>
                        <td><?php echo $rst_cli['cli_raz_social'] ?></td>
                        <td><?php echo $rst['reg_num_documento'] ?></td>
                        <td><?php echo $rst['reg_concepto'] ?></td>
                        <td><?php echo $rst['reg_femision'] ?></td>
                        <td><?php echo $rst['pag_fecha_v'] ?></td>
                        <td align="right"><?php echo number_format($val_pagar, 2) ?></td>
                        <td align="right"><?php echo number_format($pagado, 2) ?></td>
                        <td align="right"><?php echo number_format($saldo, 2) ?></td>
                        <td align="center"><?php echo $estado ?></td>
                        <td>
                            <input <?php echo $obl ?> class="v" type="checkbox" id="<?php echo 'vchreg_' . $rst['pag_id'] ?>" lang="1" onchange="load_valor(this, 'v')" />
                            <input type="hidden" size="10" id="<?php echo 'vvreal_' . $rst['pag_id'] ?>" lang="<?php echo $sts ?>"  value="<?php echo number_format($saldo, 2) ?>"  />
                            <input <?php echo $obl ?> type="text" size="10" id="<?php echo 'vvreg_' . $rst['pag_id'] ?>" lang="<?php echo $sts ?>" style="text-align:right"  value="<?php echo number_format($saldo, 2) ?>" onchange="validar_saldo(this, 'v')" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr style="height:30px ">
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td></td>
                    <td></td>                    
                    <td align="right" >Total Vencidos:</td>
                    <td><input size="10" readonly id="vtot_general" value="0" /></td>
                </tr>            
            </tbody>
            <tr id="cont_btn">
                <td colspan="10" align="right">Total General</td>                
                <td></td>
                <td>
                    <input size="10" id="tot_general" readonly />
                    <button id="btn_obligacion_pago" onclick="generar_obligacion_pago()">Generar Obligacion de Pago</button>
                </td>                
            </tr>
        </table>            
    </body>   
</html>

