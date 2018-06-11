<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';
$Rep = new Reportes();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $cns_data = $Rep->lista_reporte_pagos_cheques($desde, $hasta);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
$dias = array(1 => "Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado", 7 => "Domingo");
$meses = array(1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Depositos</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <script type='text/javascript' src='../js/includes.js'></script>
    <head>
        <script>
            dec = '<?php echo $dec ?>';
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
                $('#exp_excel').submit(function () {
                    $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                    $("#tbl2").append($("#tbl tbody").clone()).html();
                    $("#tbl2").append($("#tbl tfoot").clone()).html();
                    $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                });

                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cupos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cupos.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 48, id: id}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
        </script> 
        <style>
            #mn309{
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
            .familias,.familias_t{
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
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="33"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="33" align="center">Depositos</td></tr>
            <tr>
                <td colspan="33"><?php echo 'Desde: ' . $desde . '    Hasta: ' . $hasta ?></td>
            </tr>
        </table>        

        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table  id="tbl" style="width:100%">
            <caption  class="tbl_head" id="cont_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn"  style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >Reporte Depositos</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="float:left " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Desde:<input type="text" id="desde" name="desde" size="10" readonly value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" name="hasta" size="10" readonly value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im_hasta" />
                        <input type="submit" value="Buscar" name="search" id="search"  />
                    </form>  
                    <!--                    <form id="exp_excel" style="float:right " method="post" action="../Includes/export.php?tipo=5"  >
                                            <input type="submit" value="Excel" class="auxBtn" />
                                            <input type="hidden" id="datatodisplay" name="datatodisplay">
                                        </form>-->
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th width='80px'>Dia</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Concepto</th>
                    <th># Factura</th>
                    <th>Forma Pago</th>
                    <th>Fecha Pago</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody>
                <?PHP
                $n = 0;
                $fml = '';
                $fml2 = '';
                while ($rst = pg_fetch_array($cns_data)) {
                    $rst_doc = pg_fetch_array($Rep->lista_facturas($rst[com_id]));
                    $rst_cliente = pg_fetch_array($Rep->lista_clientes($rst_doc[cli_id]));
                    $f = explode('-', $rst[cta_fecha]);
                    $m = round($f[1]);
                    $fec = $f[2] . '-' . $meses[$m] . '-' . $f[0];
                    $d = date_format(date_create($rst[cta_fecha]), 'N');
                    $s = date_format(date_create($rst[cta_fecha]), 'W');
                    $dia = $dias[$d];
                    $n++;
                    if ($fml != $s) {
                        if ($n > 1) {
                            echo "<tr class='familias_t' id='$fml'   >
                                <td >Total</td>
                                <td colspan='6'></td>
                                <td align='right'>" . number_format($t_mont, 2) . "</td>
                            </tr>";
                            $t_mont = 0;
                        }
                        echo "<tr>
                                <td class='familias' lang='$s' colspan='36' >Semana: $s</td>
                        </tr>";
                    }
                    echo "<tr class='row' lang='$s' >
                            <td>$dia</td>
                            <td>$fec</td>
                            <td>$rst_cliente[cli_raz_social]</td>
                            <td>$rst[cta_concepto]</td>
                            <td>$rst_doc[fac_numero]</td>
                            <td>$rst[cta_forma_pago]</td>
                            <td>$rst[cta_fecha_pago]</td>
                            <td align='right'>" . number_format($rst[cta_monto], 2) . "</td>";
                    echo "</tr>";
                    $fml = $s;
                    $tm = $rst[cta_monto];
                    $t_mont+= $tm;
                    $tot_val+=$tm;
                }
                echo"<tr class='familias_t' id='$fml'   >
                    <td >Total:</td>
                    <td colspan='6'></td>
                    <td align='right'>" . number_format($t_mont, 2) . "</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class='totales'>
                    <td colspan='6'></td>
                    <td>Totales:</td>
                    <td align='right'>" . number_format($tot_val, 2) . "</td>
                </tr>
                </tfoot>
                </table>";
                ?>