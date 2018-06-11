<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_inventariopt.php'; //cambiar clsClase_productos
$Clase_industrial_inventariopt = new Clase_industrial_inventariopt();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
//    $txt.= trim(strtoupper($_GET[linea]));
//    $txt.= trim(strtoupper($_GET[talla]));
    $fml = $_GET[fml];
    $hasta = $_GET[hasta];
    if ($fml == 'x') {
        $txt = "and prod like '%$txt%'";
        $fml = " ";
        $cns_data = $Clase_industrial_inventariopt->lista_inventario_negativo($txt, $fml);
    } else {
        $txt = "";
        $fml = "and split_part(prod,'&',7)='$fml'";
        $cns_data = $Clase_industrial_inventariopt->lista_inventario_negativo($txt, $fml);
    }
} else {
    $vl = 0;
    $hasta = date('Y-m-d');
    $cnop = 'checked';
    $cind = 'checked';
    $cloc = 'checked';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Inventario General</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <script type='text/javascript' src='../js/includes.js'></script>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a) {
                    case 0:
                        main.src = '../Scripts/Lista_industrial_ingresopt.php';
                        break;
                    case 1:
                        main.src = '../Scripts/Lista_industrial_egresopt.php';
                        break;
                    case 2:
                        main.src = '../Scripts/Lista_industrial_movimientopt.php';
                        break;
                    case 3:
                        main.src = '../Scripts/Lista_industrial_inventariopt.php';
                        break;
                    case 4:
                        main.src = '../Scripts/Lista_industrial_kardexpt.php';
                        break;
                }
            }


            function actualizar() {
                var r = confirm("Esta accion tardará varios minutos \nSe eliminaran los datos actuales \nEsta Seguro de seguir con esta acccón?");
                if (r == true) {
                    loading('visible');
                    $.post("actions_inv_general.php", {op: 0}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_inventariopt.php';
                        }
                        else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }

            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());

                return true;
            }

        </script> 
        <style>
            #mn269{
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
            <tr><td colspan="19"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="19" align="center">INVENTARIO GENERAL</td></tr>
            <tr>
                <td colspan="19"><?php echo 'Fecha Actual: ' . $hasta ?></td>
            </tr>
        </table>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_circle.gif" />    
        <div id="cargando"></div>
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
                <center class="cont_title" ><?PHP echo 'INVENTARIO GENERAL DE PRODUCTO TERMINADO(-) ' . $bodega ?></center>
                <center class="cont_finder">
                    <?php
                    if ($_SESSION[usuid] == 1) {
                        ?>
                        <!--<a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Actualizar" onclick="actualizar()" >Actualizar</a>-->
                        <?php
                    }
                    ?>
                    <form method="GET" id="frmSearch" name="frm1" style="float:left" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="text" name="txt" id="txt" size="35" placeholder="Codigo/Referencia" style="text-transform:uppercase" />
                        <!--<input type="text" name="linea" id="linea" size="15" placeholder="Linea" style="text-transform:uppercase" />-->                                                
                        <!--<input type="text" name="talla" id="talla" size="15" placeholder="Talla" style="text-transform:uppercase" />-->
                        <select name="fml" id="fml" >
                            <option value="x">Familia</option>
                            <?php
                            $cns_fml = $Clase_industrial_inventariopt->lista_familias();
                            while ($rst_fml = pg_fetch_array($cns_fml)) {
                                if ($faml == $rst_fml[ids]) {
                                    $sel = 'selected';
                                } else {
                                    $sel = '';
                                }
                                echo "<option $sel value=$rst_fml[ids]>$rst_fml[protipo]</option>";
                            }
                            if ($faml == '0') {
                                $sel = 'selected';
                            }
                            ?>
                        </select>
                        &nbsp;&nbsp;&nbsp;&nbsp;ACTUAL:&nbsp;<input type="text" id="hasta" name="hasta" size="10" value="<?php echo $hasta ?>" readonly/>
                        &nbsp;&nbsp;&nbsp;
                        <button class="btn" title="Buscar" name="search" id="search" onclick="frmSearch.submit()" >Buscar</button>
                    </form>
                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php" onsubmit="return exportar_excel()"  >
                        <input type="submit" value="Excel" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <?php
                    $cns_locales = $Clase_industrial_inventariopt->lista_emisores();
                    while ($rst_locales = pg_fetch_array($cns_locales)) {
                        echo "<th class='locales' lang='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</th>";
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
                    $n++;
                    echo "<tr class='row' lang='$rst[ids]' >
                            <td>$n</td>
                            <td>$rst[codigo]</td>
                            <td class='desc' >$rst[descripcion]</td>";
                    $l = 0;
                    $cns1 = $Clase_industrial_inventariopt->lista_emisores();
                    while ($rst_locales = pg_fetch_array($cns1)) {
                        $l++;
                        $cnt = $rst[loc . $l];
                        echo"<td align='right'  name='cn$rst[ids]$l' class='cnt$l'  >$cnt</td>";
                    }
                    echo "</tr>";
                    $fml2 = $rst[ids];
                    $fml = $rst[familia];
                }
                echo"
            </tbody>
            <tfoot>
                <tr class='totales'>
                    <td></td>
                    <td></td>
                    <td>Totales:</td>";
                $l = 0;
                $cns1 = $Clase_industrial_inventariopt->lista_emisores();
                while ($rst_locales = pg_fetch_array($cns1)) {
                    $l++;
                    echo "<td align='right' id='t_cnt$l' >c</td>";
                }
                echo "<td align='right' id='t_cnt' >c</td>
                </tr>
            </tfoot>
        </table>";
                $local = pg_num_rows($Clase_industrial_inventariopt->lista_emisores());
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
                        t_v = parseFloat(t_v + (t * 1));
                    }
                    $(this).append("<td align='right' name='cn" + this.lang + "' class='cnt'>" + accounting.formatMoney(t_v, "", 0, ",", ".") + "</td>");
                });
            </script>
            <script>
                $('.locales').each(function () {
                    cnt = 'cnt' + this.lang;
                    tcnt = 0;
                    $('.' + cnt).each(function () {
                        tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + cnt).html(accounting.formatMoney(tcnt, "", 0, ",", "."));

                });
            </script>





