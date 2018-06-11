<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxcobrar.php';
$Cxc = new CuentasCobrar();
set_time_limit(0);
if (isset($_GET[search])) {
    $nm = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[desde];
    $fec2 = $_GET[hasta];
    $texto = $_GET[txt];
    $pagados = $_GET[pagados];
    $vencidos = $_GET[vencidos];
    $por_vencer = $_GET[por_vencer];
    $pagados1 = $_GET[pagados];
    $vencidos1 = $_GET[vencidos];
    $por_vencer1 = $_GET[por_vencer];
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
        $txt = "WHERE (fac_numero LIKE '%$nm%' or fac_nombre like '%$nm%' or fac_identificacion like '%$nm%') and fac_fecha_emision between '$fec1' and '$fec2'  and (trim(fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(fac_estado_aut))=0 or fac_estado_aut isnull)";
//        $est1 = 1;
    } else {
        if ($est1 == 0) {//todos
            $txt = " WHERE fac_fecha_emision between '$fec1' and '$fec2' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull) ";
        } else if ($est1 == 1) {//pagados
            $txt = " WHERE fac_fecha_emision between '$fec1' and '$fec2' and (trim(c.fac_estado_aut)='RECIBIDA AUTORIZADO' or char_length(trim(c.fac_estado_aut))=0 or c.fac_estado_aut isnull)  and fac_total_valor+(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago='NOTA DE DEBITO')=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO') or fac_total_valor=(Select sum(cta_monto) from erp_ctasxcobrar ct where c.fac_id=ct.com_id and cta_forma_pago<>'NOTA DE DEBITO')";
        } else if ($est1 == 2) {//xvencer
            $cns = $Cxc->buscar_documentos_vencer(date('Y-m-d'), $fec1, $fec2);
        } else if ($est1 == 3) {//Vencidos
            $cns = $Cxc->buscar_documentos_vencidos(date('Y-m-d'), $fec1, $fec2);
        } else if ($est1 == 4) {//Vencidos y por_vencer
            $cns = $Cxc->buscar_documentos_vencidos_xvencer(date('Y-m-d'), $fec1, $fec2);
        } else if ($est1 == 5) {//Pagados y Vencidos 
            $cns = $Cxc->buscar_documentos_pagados_vencidos(date('Y-m-d'), $fec1, $fec2);
        } else if ($est1 == 6) {//Pagados y por_vencer 
            $cns = $Cxc->buscar_documentos_pagados_xvencer(date('Y-m-d'), $fec1, $fec2);
        }
    }
    if ($est1 < 2) {
        $cns = $Cxc->lista_documentos_buscador($txt);
    }
} else {
    $fec2 = date('Y-m-d');
    $pagados = '';
    $vencidos = 'checked';
    $por_vencer = 'checked';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Central de Cobranza</title>
    <head>
        <script>

            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
//                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
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
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Editar
                        frm.src = '../Scripts/Form_ctasxcobrar.php?id=' + id + '&fec1=' + fec1 + '&fec2=' + fec2 + '&estado=' + est + '&nm=' + txt; //Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Reporte
                        frm.src = '../Scripts/frm_pdf_ctasxcobrar.php?txt=' + txt + '&d=' + fec1 + '&h=' + fec2 + '&e=' + est + '&dec=' + <?php echo $dec ?>;
                        break;
                    case 2://Reporte estado cuenta
                        frm.src = '../Scripts/frm_pdf_estado_cuenta.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&cli=' + id + '&dec=' + <?php echo $dec ?>;
                        ;
                        break;
                    case 3://Reporte cartera vencida
                        frm.src = '../Scripts/frm_pdf_cartera_ven.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&dec=' + <?php echo $dec ?>;
                        ;
                        break;
                    case 4://Reporte saldo
                        frm.src = '../Scripts/frm_pdf_saldoxcuenta.php?txt=' + txt + '&d=' + fec1 + '&h=' + fec2 + '&e=' + est + '&dec=' + <?php echo $dec ?>;
                        ;
                        break;
                    case 5://Reporte Estado de cuenta cliente 
                        frm.src = '../Scripts/frm_pdf_std_cta_cliente.php?id=' + id + '&dec=' + <?php echo $dec ?> + '&mod=' + <?php echo $modulo ?>;
                        ;
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
                <tr><th colspan="10"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="10" align="center"><?PHP echo 'CENTRAL DE COBRANZA'?></th></tr>
                <tr>
                    <td colspan="10"><?php 'Al: ' . $fec2 ?></td>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=8" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?php echo "CENTRAL DE COBRANZA" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Factura/Cliente  :<input type="text" name="txt" size="25" id="txt" value="<?php echo $texto ?>"/>
                        <input type="checkbox" id="pagados" name="pagados" <?php echo $pagados ?>/>Pagado
                        <input type="checkbox" id="vencidos" name="vencidos" <?php echo $vencidos ?> />Vencido
                        <input type="checkbox" id="por_vencer" name="por_vencer" <?php echo $por_vencer ?> />Por Vencer
                        <input type="hidden" size="12" id="desde" name="desde" value="2015-01-01"/>
                        Al:<input type="text" size="12" id="hasta" name="hasta" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                        <font style="float: right;margin-top:7px;padding:7px;">Fecha Hoy  :<input style="float:right;;margin-top:-2px;padding:-1px;color:black " readonly type="text" size="12" id="f_act" name="f_act" value="<?php echo date('Y-m-d') ?>" /></font>                        
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Reporte Cuentas x Cobrar" onclick="auxWindow(1, 0)" >Reporte Cuentas x Cobrar</a>
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Cartera Vencida" onclick="auxWindow(3, 0)" >Cartera Vencida</a>
                        <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Saldo Por Cuentas" onclick="auxWindow(4, 0)" >Saldo Por Cuentas</a>
                        <select id="estado" name="estado" style="float: right;margin-top:12px;padding:-1px;">
                            <option value="0">Todos</option>
                            <option value="1">Pagados</option>
                            <option value="2">Por Vencer</option>
                            <option value="3">Vencidos</option>
                        </select>
<!--                        <img src="../img/orden.png" width="20px" class="auxBtn" title="Imprimir Reporte" onclick="auxWindow(1, 0)">
                        <img src="../img/orden.png" width="20px" class="auxBtn" title="cartera vencida" onclick="auxWindow(3, 0)">
                        <img src="../img/orden.png" width="20px" class="auxBtn" title="saldo por cuenta" onclick="auxWindow(4, 0)">-->
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Ruc</th>
            <th>Cliente</th>
            <th>Documento</th>
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
            $c = '"';
            while ($rst = pg_fetch_array($cns)) {
                $ast = '';
                $res = pg_fetch_array($Cxc->suma_pagos1($rst['fac_id'])); ///quitar fac_numero
                $pagado = $res[monto];
                $total_valor = $rst['fac_total_valor'] + $res[debito];
                $saldo = $total_valor - $pagado;
                if ($res[debito] != 0) {
                    $ast = '*';
                }
                $rst_pag = pg_fetch_array($Cxc->lista_ultimo_pago($rst[fac_id]));
                $com_id = $rst_pag[com_id];
                if ($rst_pag[pag_fecha_v] < date('Y-m-d')) {
                    $estado = 'VENCIDO';
                } else {
                    $estado = 'POR VENCER';
                }
                If (round($saldo, $dec) == 0) {
                    $estado = 'PAGADO';
                }

                $vencer = $saldo;

                $fecha = $rst_pag[pag_fecha_v];
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
                    if (!empty($com_id)) {
                        $n++;
                        if ($grup != $rst[fac_identificacion] && $n != 1) {

                            echo "<tr>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' ></td>
                            <td class='totales' >Total</td>  
                            <td class='totales' align='right'>" . number_format($tv, $dec) . "</td>
                            <td class='totales' align='right'>" . number_format($tp, $dec) . "</td>
                            <td class='totales' align='right'>" . number_format($ts, $dec) . "</td>
                            <td class='totales' ></td>
                        </tr>";
                            $tv = 0;
                            $tp = 0;
                            $ts = 0;
                        }
                        $style='mso-number-format:"@"';

                        echo "<tr>
                        <td>$n</td>";

                        if ($grup != $rst[fac_identificacion]) {

                            echo "<td style='$style' onclick='auxWindow(5, $c$rst[cli_id]$c)'><a href='#'>$rst[fac_identificacion]</a></td>
                            <td onclick='auxWindow(2, $c$rst[fac_identificacion]$c)'><a href='#'>$rst[fac_nombre]</a></td>";
                        } else {
                            echo "<td></td>
                            <td onclick='auxWindow(2, $c$rst[fac_identificacion]$c)'></td>";
                        }
                        echo "<td onclick='auxWindow(0,$rst[fac_id])'><a href='#'>$rst[fac_numero]</a></td>
                        <td >$rst[fac_fecha_emision]</td>
                        <td >$fecha</td>
                        <td align='right' >" . $ast . number_format($total_valor, $dec) . "</td>
                        <td align='right' >" . number_format($pagado, $dec) . "</td>
                        <td align='right' >" . str_replace('-', '', number_format($saldo, $dec)) . "</td>
                        <td align='right' >$estado</td>
                    </tr>";
                        $d = 0;
                        $grup = $rst[fac_identificacion];
                        $tv+=$total_valor;
                        $tp+=$pagado;
                        $ts+=$saldo;
                    }
                    $com_id = 0;
                    $estado = 0;
                }
            }

            echo "<tr>
                <td class='totales' ></td>
                <td class='totales' ></td>
                <td class='totales' ></td>
                <td class='totales' ></td>
                <td class='totales' ></td>
                <td class='totales' >Total</td>  
                <td class='totales' align='right' >" . number_format($tv, $dec) . "</td>
                <td class='totales' align='right'>" . number_format($tp, $dec) . "</td>
                <td class='totales' align='right'>" . number_format($ts, $dec) . "</td>
                <td class='totales' ></td>
            </tr>";
            ?>
        </tbody>
    </table>            
</body>   
</html>
<script>
    var p = '<?php echo $est1 ?>';
    $('#estado').val(p);
</script>

