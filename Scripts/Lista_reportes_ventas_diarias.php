<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';

$Rep = new Reportes();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $cns_data = $Rep->lista_reporte_ventas_diarias_buscador($desde, $hasta);
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
    <title>Reporte por Ventas Diarias</title>
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
            <tr><td colspan="33" align="center">REPORTE DE VENTAS DIARIAS</td></tr>
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
                <center class="cont_title" >REPORTE DE VENTAS DIARIAS</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="float:left " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Desde:<input type="text" id="desde" name="desde" size="10" readonly value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" name="hasta" size="10" readonly value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im_hasta" />
                        <input type="submit" value="Buscar" name="search" id="search"  />
                    </form>  
                    <form id="exp_excel" style="float:right " method="post" action="../Includes/export.php?tipo=5"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th width='150px'>Dia</th>
                    <th>Fecha</th>
                    <?php
                    $cns = $Rep->lista_emisores($val);
                    while ($rst_locales = pg_fetch_array($cns)) {
                        echo "<th class='locales' lang='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</th>";
                        ?>

                        <?php
                    }
                    ?>
                    <th class="locales" lang="">TOTAL</th>     
                </tr>
            </thead>
            <!------------------------------------->
            <tbody>
                <?PHP
                $n = 0;
                $fml = '';
                $fml2 = '';
                while ($rst = pg_fetch_array($cns_data)) {
                    $f = explode('-', $rst[fecha]);
                    $m = round($f[1]);
                    $fec = $f[2] . '-' . $meses[$m];
                    $d = date_format(date_create($rst[fecha]), 'N');
                    $s = date_format(date_create($rst[fecha]), 'W');
                    $dia = $dias[$d];
                    $n++;
                    if ($fml != $s.$f[0]) {
                        if ($n > 1) {
                            echo "<tr class='familias_t' id='$fml'   >
                                <td >Total</td>
                                <td ></td>
                            </tr>";
                        }
                        echo "<tr>
                                <td class='familias' lang='$s$f[0]' colspan='36' >Semana: $s</td>
                        </tr>";
                    }
                    echo "<tr class='row' lang='$s$f[0]' >
                            <td>$dia</td>
                            <td>$fec</td>";
                    $cns1 = $Rep->lista_emisores($val);
                    $l = 0;
                    while ($rst_locales = pg_fetch_array($cns1)) {
                        $l++;
                        $cnt = $rst[loc . $l];
                        echo "<td align='right'  name='cn" . $s.$f[0] . $l . "' class='cnt$l'>$cnt</td>";
                    }
                    echo "</tr>";
                    $fml = $s.$f[0];
                }

                echo"<tr class='familias_t' id='$fml'   >
                    <td >Total:</td>
                    <td></td>
                </tr>
                
            </tbody>
            <tfoot>
                <tr class='totales'>
                    <td></td>
                    <td>Totales:</td>";
                $l = 0;
                $cns1 = $Rep->lista_emisores($val);
                while ($rst_locales = pg_fetch_array($cns1)) {
                    $l++;
                    echo "<td align='right' id='t_cnt$l' >c</td>";
                }
                echo "<td align='right' id='t_cnt' >c</td>
                </tr>
                </tfoot>
                </table>";
               $local = pg_num_rows($Rep->lista_emisores());
                
                ?>
            <script>
                var l = '<?php echo $local ?>';
                $('.row').each(function () {
                    var t_v = 0;
                    j = 0;
                    while (j < l) {
                        j++;
                        cnt = '.cnt' + j;
                        t = $(this).find(cnt).html().replace(/,/g, '');
                        t_v = t_v + (t * 1);
                        
                    }

                    $(this).append("<td align = 'right' name = 'cn" + this.lang + "' class = 'cnt' >" + accounting.formatMoney(t_v, "", dec, ",", ".") + "</td>");
                });
            </script>

            <script>
                $('.familias').each(function () {
                    fml = this.lang;
                    $('.locales').each(function () {
                        tcn = 0;
                        cn_nm = 'cn' + fml + this.lang;
                        $("td[name = " + cn_nm + "]").each(function () {
                            tcn = (tcn * 1) + ($(this).html().replace(/,/g, '') * 1);
                        });
                        $("#" + fml).append("<td align='right'>" + accounting.formatMoney(tcn, "", dec, ",", ".") + "</td>").html();
                    });
                });

            </script>
            <script>
                tcnt = 0;
                $('.cnt_tot').each(function () {
                    tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                   
                });
                $('#tcnt_tot').html(accounting.formatMoney(tcnt, "", dec, ",", "."));
                tcnt = 0;
            </script>
            <script>
                $('.locales').each(function () {
                    cnt = 'cnt' + this.lang;
                    tcnt = 0;
                    $('.' + cnt).each(function () {
                        tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + cnt).html(accounting.formatMoney(tcnt, "", dec, ",", "."));
                });
            </script>
