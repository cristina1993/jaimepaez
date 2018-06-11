<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
if (isset($_GET[txt], $_GET[desde], $_GET[hasta], $_GET[estado])) {
    $nm = trim(strtoupper($_GET[txt]));
    $est1 = strtoupper($_GET[estado]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $pagados1 = $_GET[pagados];
    $vencidos1 = $_GET[vencidos];
    $por_vencer1 = $_GET[por_vencer];
    $pagados = $_GET[pagados];
    $vencidos = $_GET[vencidos];
    $por_vencer = $_GET[por_vencer];
    if ($pagados == 'on' && $vencidos == 'on' && $por_vencer == 'on') {
        $est1 = 0;
        $pagados = 'checked';
        $vencidos = 'checked';
        $por_vencer = 'checked';
    } else if ($pagados == 'on' && $vencidos != 'on' && $por_vencer != 'on') {
        $est1 = 1;
        $pagados = 'checked';
    } else if ($pagados != 'on' && $vencidos == 'on' && $por_vencer != 'on') {
        $est1 = 3;
        $vencidos = 'checked';
    } else if ($pagados != 'on' && $vencidos != 'on' && $por_vencer == 'on') {
        $est1 = 2;
        $por_vencer = 'checked';
    } else if ($pagados != 'on' && $vencidos == 'on' && $por_vencer == 'on') {
        $est1 = 4;
        $vencidos = 'checked';
        $por_vencer = 'checked';
    } else if ($pagados == 'on' && $vencidos == 'on' && $por_vencer != 'on') {
        $est1 = 5;
        $pagados = 'checked';
        $vencidos = 'checked';
    } else if ($pagados == 'on' && $vencidos != 'on' && $por_vencer == 'on') {
        $est1 = 6;
        $pagados = 'checked';
        $por_vencer = 'checked';
    }
    if (!empty($_GET[txt])) {
        $txt = "and (cli_raz_social like '%$nm%' or reg_ruc_cliente like '%$nm%' or reg_num_documento LIKE '%$nm%' or reg_concepto like '%$nm%') and reg_estado<3";
    } else {
        if ($est1 == 0) {
            $txt = " and c.reg_femision between '$desde' and '$hasta' and c.reg_estado<3";
        } else if ($est1 == 1) {///pagados
            $txt = " and c.reg_femision between '$desde' and '$hasta' and not exists(Select from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3 and reg_total+(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago='NOTA DE DEBITO')=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO') or reg_total=(Select sum(ctp_monto) from erp_ctasxpagar ct where c.reg_id=ct.reg_id and ctp_forma_pago<>'NOTA DE DEBITO')";
        } else if ($est1 == 2) {///xvencer
            $cns = $Cxp->buscar_documentos_vencer_cp(date('Y-m-d'), $desde, $hasta);
//            $txt = " and c.reg_femision between '$desde' and '$hasta' and reg_total>(Select sum(ctp_monto)from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3";
        } else if ($est1 == 3) {//vencidos
            $cns = $Cxp->buscar_documentos_vencidos_cp(date('Y-m-d'), $desde, $hasta);
//            $txt = " and c.reg_femision between '$desde' and '$hasta' and reg_total=(Select sum(ctp_monto)from erp_ctasxpagar ct where c.reg_id=ct.reg_id) and c.reg_estado<3";
        } else if ($est1 == 4) {//Vencidos y por_vencer
            $cns = $Cxp->buscar_documentos_vencidos_xvencer(date('Y-m-d'), $desde, $hasta);
        } else if ($est1 == 5) {//Pagados y Vencidos 
            $cns = $Cxp->buscar_documentos_pagados_vencidos(date('Y-m-d'), $desde, $hasta);
        } else if ($est1 == 6) {//Pagados y por_vencer 
            $cns = $Cxp->buscar_documentos_pagados_xvencer(date('Y-m-d'), $desde, $hasta);
        }
    }
//    echo $txt;
    if ($est1 < 2) {
        $cns = $Cxp->lista_documentos_buscador($txt);
    }
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $vencidos = 'checked';
    $por_vencer = 'checked';
}
$dec = 2;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Cuentas por Pagar</title>
    <head>
        <script>

            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});

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
                        frm.src = '../Scripts/frm_pdf_estado_pagar.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&cli=' + id + '&dec=' + <?php echo $dec ?>;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 3://Reporte cartera vencida
                        frm.src = '../Scripts/frm_pdf_cartera_ven_cxp.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&dec=' + <?php echo $dec ?>;
                        break;
                    case 4://Reporte saldo
                        frm.src = '../Scripts/frm_pdf_saldoxcuenta_cxp.php?txt=' + txt + '&d=' + fec1 + '&h=' + fec2 + '&e=' + est + '&dec=' + <?php echo $dec ?>;
                        break;
                    case 5://Reporte Estado de cuenta cliente 
                        frm.src = '../Scripts/frm_pdf_std_cta_proveedor.php?id=' + id + '&dec=' + <?php echo $dec ?> + '&mod=' + <?php echo $modulo ?>;
                        break;
                    case 6://Reporte
                        frm.src = '../Scripts/frm_pdf_ctasxpagar.php?txt=' + txt + '&d=' + fec1 + '&h=' + fec2 + '&e=' + est + '&dec=' + <?php echo $dec ?>;
                        break;

                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function exportar_excel() {
                $("#datatodisplay").val("");
                $("#tbl2 tbody").html("");
                $("#tbl2 tfoot").html("");
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }

        </script> 
        <style>
            #mn182{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="11"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="11" align="center"><?PHP echo 'CUENTAS POR PAGAR' ?></th></tr>
                <tr>
                    <td colspan="11"><?php 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
                </tr>
            </thead>
        </table>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=9" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?php echo "CUENTAS POR PAGAR" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Factura/Cliente  :<input type="text" name="txt" size="25" id="txt" value="<?php echo $nm ?>"/>
                        <input type="checkbox" id="pagados" name="pagados" <?php echo $pagados ?>/>Pagado
                        <input type="checkbox" id="vencidos" name="vencidos" <?php echo $vencidos ?> />Vencido
                        <input type="checkbox" id="por_vencer" name="por_vencer" <?php echo $por_vencer ?> />Por Vencer
                        <!--Fecha Emision:--> 
                        <!--Desde  :-->
                        <input type="text" size="12" id="desde" name="desde" value="2015-01-01" hidden/>
                        <!--<img src="../img/calendar.png" id="im-desde"/>-->
                        Al  :<input type="text" size="12" id="hasta" name="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                        <font style="float: right;margin-top:7px;padding:7px;">Fecha Hoy  :<input style="float:right;;margin-top:-2px;padding:-1px;color:black " readonly type="text" size="12" id="f_act" name="f_act" value="<?php echo date('Y-m-d') ?>" /></font>                        
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Reporte Cuentas x Cobrar" onclick="auxWindow(6, 0)" >Reporte Cuentas x Pagar</a>
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Cartera Vencida" onclick="auxWindow(3, 0)" >Cartera Vencida</a>
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Saldo Por Cuentas" onclick="auxWindow(4, 0)" >Saldo Por Cuentas</a>
                        <select id="estado" name="estado" style="float: right;margin-top:12px;padding:-1px;">
                            <option value="0">Todos</option>
                            <option value="1">Pagados</option>
                            <option value="2">Por Vencer</option>
                            <option value="3">Vencidos</option>
                        </select>
                    </form>


                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
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
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $d = 0;
            $f = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $ast = '';
                $res = pg_fetch_array($Cxp->suma_pagos1($rst['reg_id']));
                $pagado = $res[monto];
                $total_valor = $rst[reg_total] + $res[debito];
                $saldo = $total_valor - $pagado;
                if ($res[debito] != 0) {
                    $ast = '*';
                }
                $cns_pag = $Cxp->lista_pagos_regfac($rst[reg_id]);
                while ($rst_pag = pg_fetch_array($cns_pag)) {
                    $cns_cta = $Cxp->listar_una_ctapagar_pagid($rst_pag[pag_id]);
                    $rst_ct = pg_fetch_array($cns_cta);
                    $f++;
                    $fp = pg_num_rows($cns_pag);
                    if ($rst_pag[pag_fecha_v] != $rst_ct[ctp_fecha] && $d == 0) {
                        $fec = $rst_pag[pag_fecha_v];
                        $d = 1;
                    }

                    if ($fp == $f) {
                        $fec = $rst_pag[pag_fecha_v];
                    }
                }
//                if (round($saldo, $dec) == 0) {
//                    $estado = 'PAGADO';
//                } else if ($pagado == 0) {
//                    $estado = 'POR PAGAR';
//                } else if (round($pagado, $dec) != round($saldo, $dec)) {
//                    $estado = 'PARCIALMENTE PAGADO';
//                }
                if ($rst_pag[pag_fecha_v] < date('Y-m-d')) {
                    $estado = 'VENCIDO';
                } else {
                    $estado = 'POR VENCER';
                }
                If (round($saldo, $dec) == 0) {
                    $estado = 'PAGADO';
                }

                $fecha = $fec;
                $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                if ($pagados1 == 'on') {
                    $pag_est = "PAGADO";
                }
                if ($vencidos1 == 'on') {
                    $pag_ven = "VENCIDO";
                }
                if ($por_vencer1 == 'on') {
                    $pag_vencer = "POR VENCER";
                }
                if ($estado == $pag_est || $estado == $pag_ven || $estado == $pag_vencer) {
                    $n++;

                    if ($grup != $rst['reg_ruc_cliente'] && $n != 1) {
                        echo "<tr>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' ></td>
                        <td class='totales' >Total</td>  
                        <td class='totales' align='right' >" . number_format($tv, $dec) . "</td>
                        <td class='totales' align='right' >" . number_format($tp, $dec) . "</td>
                        <td class='totales' align='right' >" . number_format($ts, $dec) . "</td>
                        <td class='totales' ></td>
                    </tr>";
                        $tv = 0;
                        $tp = 0;
                        $ts = 0;
                    }
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <?php
                        if ($grup != $rst['reg_ruc_cliente']) {
                            ?>
                            <td onclick="auxWindow(5, '<?php echo $rst[cli_id] ?>')"><a href='#'><?php echo $rst['reg_ruc_cliente'] ?></a></td>
                            <td style='mso-number-format:"@"' onclick="auxWindow(1, '<?php echo $rst['reg_ruc_cliente'] ?>')"><a href='#'><?php echo $rst_cli['cli_raz_social'] ?></a></td>
                            <?php
                        } else {
                            ?>
                            <td></td>
                            <td style='mso-number-format:"@"' onclick="auxWindow(1, '<?php echo $rst['reg_ruc_cliente'] ?>')"></td>
                            <?php
                        }
                        ?>
                        <td onclick="auxWindow(0,<?php echo $rst[reg_id] ?>)"><a href='#'><?php echo $rst['reg_num_documento'] ?></a></td>
                        <td ><?php echo $rst['reg_concepto'] ?></td>
                        <td ><?php echo $rst['reg_femision'] ?></td>
                        <td ><?php echo $fecha ?></td>
                        <td align="right" ><?php echo $ast . number_format($total_valor, $dec) ?></td>
                        <td align="right" ><?php echo number_format($pagado, $dec) ?></td>
                        <td align="right" ><?php echo number_format($saldo, $dec) ?></td>
                        <td ><?php echo $estado ?></td>
                    </tr>
                    <?PHP
                    $d = 0;
                    $grup = $rst['reg_ruc_cliente'];
                    $tv+=round($total_valor, $dec);
                    $tp+=$pagado;
                    $ts+=$saldo;
                }
            }
            ?>
            <tr>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" >Total</td>  
                <td class="totales" align="right" ><?php echo number_format($tv, $dec) ?></td>
                <td class="totales" align="right"><?php echo number_format($tp, $dec) ?></td>
                <td class="totales" align="right"><?php echo number_format($ts, $dec) ?></td>
                <td class="totales" ></td>
            </tr>
        </tbody>
    </table>            
</body>   
</html>
<script>
    var p = '<?php echo $est1 ?>';
    $('#estado').val(p);
</script>

