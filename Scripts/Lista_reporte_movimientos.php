<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reporte_movimientos.php';
$Rep = new reporte_movimientos();
if (isset($_GET[search])) {
    $d = $_GET[desde];
    $h = $_GET[hasta];
    $tip = $_GET[tipo];
    $text = strtoupper($_GET[text]);
    if ($text == '' && $tip != 'x') {
        $cns_ctas = $Rep->lista_cuentas_byc_tipo($tip);
    } else if ($text != '' && $tip == 'x') {
        $cns_ctas = $Rep->lista_cuentas_byc_refe($text);
    } else if ($text == '' && $tip == 'x') {
        $cns_ctas = $Rep->lista_cuentas_byc();
    }
    $cuentas0 = pg_fetch_all_columns($cns_ctas);
    $cuentas = array_unique($cuentas0);
} else {
    $d = date('Y-m-d');
    $h = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN">
<html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte Movimientos</title>
        <script>
            $(function () {
                $("#tbl").tablesorter({
                    headers: {
                        0: {sorter: false},
                        1: {sorter: false},
                        2: {sorter: false},
                        3: {sorter: false},
                        4: {sorter: false},
                        5: {sorter: false},
                        6: {sorter: false},
                        7: {sorter: false},
                        8: {sorter: false},
                        9: {sorter: false},
                        10: {sorter: false},
                        11: {sorter: false},
                        12: {sorter: false},
                        14: {sorter: false},
                        15: {sorter: false},
                        16: {sorter: false},
                        17: {sorter: false},
                        18: {sorter: false},
                        19: {sorter: false},
                        20: {sorter: false},
                        21: {sorter: false},
                        22: {sorter: false},
                        23: {sorter: false},
                        24: {sorter: false},
                        25: {sorter: false},
                        26: {sorter: false},
                        27: {sorter: false},
                        28: {sorter: false},
                        29: {sorter: false},
                        30: {sorter: false},
                        31: {sorter: false},
                        32: {sorter: false},
                        33: {sorter: false},
                        34: {sorter: false},
                        35: {sorter: false},
                        36: {sorter: false},
                        37: {sorter: false},
                        38: {sorter: false},
                        39: {sorter: false},
                        40: {sorter: false},
                        41: {sorter: false},
                        42: {sorter: false},
                        43: {sorter: false},
                        44: {sorter: false}
                    },
                    widgets: ['stickyHeaders'],
                    highlightClass: 'highlight',
                    widthFixed: false
                });
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                $('#exp_excel').submit(function () {
                    $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                    $("#tbl2").append($("#tbl tbody").clone()).html();
                    $("#tbl2").append($("#tbl tfoot").clone()).html();
                    $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                })
            });
        </script>
        <style>
            #mn262{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input{
                background:#f8f8f8 !important; 
            }
            body{
                background:#f8f8f8;
            }
            .desc{
                font-size:9px !important; 
                letter-spacing:-0.35px !important;
            }
            .totales{
                color: #9F6000;
                font-size:12px; 
                background-color: #FEEFB3;
                font-weight:bolder; 
            }
            .cuentas,.cuentas_t{
                color: #D8000C;
                font-weight:bolder; 
                background-color: #FFBABA;
            }
            thead tr th{
                font-size:11px !important; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" id="cont_head">
                <center class="cont_menu">
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                </center>
                <center class="cont_title">REPORTE MOVIMIENTOS</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="float: left" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                        REFERENCIA:
                        <input type="text" id="text" name="text" value="<?php echo $text ?>"/>
                        TIPO:
                        <select id="tipo" name="tipo">
                            <option value="x">SELECCIONE</option>
                            <option value="1">BANCO</option>
                            <option value="2">CAJA</option>
                            <option value="3">CAJA CHICA</option>
                        </select>
                        DESDE:<input type="text" id="desde" name="desde" size="10" value="<?php echo $d ?>" />
                        <img src="../img/calendar.png" id="im_desde">
                        HASTA:<input type="text" id="hasta" name="hasta" size="10" value="<?php echo $h ?>" />
                        <img src="../img/calendar.png" id="im_hasta">
                        <button class="btn" title="Buscar" name="search" id="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo Documento</th>
                    <th>No. Documento</th>
                    <th>Concepto</th>
                    <th>Operacion</th>
                    <th>Credito</th>
                    <th>Debito</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <!----------------------------->
            <tbody>
                <?PHP
                $n = 0;
                while ($n < count($cuentas0)) {
                    $rst_cta = pg_fetch_array($Rep->lista_un_cyb($cuentas[$n]));
                    switch ($rst_cta[byc_tipo]) {
                        case 1:
                            $tipo = 'BANCO';
                            break;
                        case 2:
                            $tipo = 'CAJA';
                            break;
                        case 3:
                            $tipo = 'CAJA CHICA';
                            break;
                    }
                    echo "<tr>
                          <td class='cuentas' colspan='8'>$tipo : $rst_cta[byc_referencia]</td>
                          </tr>
                          <tr>
                          <td class='cuentas' colspan='8'>CUENTA : " . $cuentas0[$n] . "</td>
                          </tr>
                          <tr class='cuentas_t'>
                                <td colspan='3'></td>
                                <td>SALDO INICIAL</td>
                                <td colspan='3'></td>
                                <td>$rst_cta[byc_saldo]</td>
                              </tr>";
                    $cns_as = $Rep->lista_cuentas_detalle_byc($d, $h, $cuentas0[$n]);
                    while ($rst = pg_fetch_array($cns_as)) {
                        echo "<tr>
                                <td colspan='7' hidden></td>
                                <td hidden>$rst_cta[byc_saldo]</td>
                              </tr>";
                        if ($rst[con_concepto] == 'FACTURACION VENTA') {
                            $tp_doc = 'FACTURA';
                        } else if ($rst[con_concepto] == 'CIERRE CAJA') {
                            $tp_doc = 'CIERRE CAJA';
                        } else if ($rst[con_concepto] == 'DEVOLUCION VENTA') {
                            $tp_doc = 'NOTA CREDITO';
                        } else {
                            $tp_doc = 'OTROS';
                        }
                        if ($rst[con_concepto] == 'CIERRE CAJA') {
                            $rst[con_concepto] = 'CIERRE CAJA LOCAL';
                        }
                        if ($rst[tipo] == 'Debe') {
                            $debe = $rst[con_valor_debe];
                            $haber = '';
                        } else {
                            $debe = '';
                            $haber = $rst[con_valor_haber];
                        }
                        $sal = $rst_cta[byc_saldo] + $sal - $haber + $debe;
                        echo "<tr>
                          <td>$rst[con_fecha_emision]</td>
                          <td>$tp_doc</td>    
                          <td>$rst[con_documento]</td>
                          <td>$rst[con_concepto]</td>
                          <td></td>
                          <td>$debe</td>
                          <td>$haber</td>
                          <td>$sal</td>
                          </tr>";
                        $rst_cta[byc_saldo] = 0;
                    }
                    $sal = 0;
                    $n++;
                }
                ?>
            </tbody>
        </table>
    </body>
</html>
<script>
    var t = '<?php echo $tip ?>';
    $('#tipo').val(t);
</script>
