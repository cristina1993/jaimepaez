<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reportes.php';

$Rep = new Reportes();
$cns_comb = $Rep->lista_un_mp_mod1();
if (isset($_GET[search])) {
    $txt = strtoupper($_GET[txt]);
    $hasta = date('Y-m-d');
    $ids = $_GET[ids];
    if ($ids == '') {
        $ids = 'no';
    }
    //MODIFICACION DEL SQL 
    $txt = " and p.prod like '%$txt%' and split_part(p.prod,'&',5)='$ids'";
    $cns_data = $Rep->lista_reporte_costos_productos_buscador($txt);
} else {
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Reporte por Productos</title>
    <script type='text/javascript' src='../js/accounting.js'></script>
    <script type='text/javascript' src='../js/includes.js'></script>
    <head>
        <script>
            dec = '<?php echo $dec ?>';
            dc = '<?php echo $dc ?>';
            var ids = '<?php echo $ids ?>';
            $(function () {
                if (ids == 'no') {
                    alert('Elija tipo');
                }
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
            #mn311{
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
            <tr><td colspan="33" align="center">REPORTE DE COSTOS POR PRODUCTO</td></tr>
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
                <center class="cont_title" >INVENTARIO AL COSTO</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" style="float:left " action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        TIPO:<select id="ids" name="ids" class="sel">
                            <option value="">SELECCIONE</option>
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                $dt = explode('&', $rst_c[mp_tipo]);
                                ?>
                                <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <input type="text" name="txt" id="txt" size="35" placeholder="Codigo/Referencia" style="text-transform:uppercase" />                        
                        &nbsp;&nbsp;&nbsp;&nbsp;AL:&nbsp;<input type="text" id="hasta" name="hasta" size="10" readonly value="<?php echo $hasta ?>" />
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
                    <th width='150px' colspan="3">Productos</th>
                    <th></th>
                    <?php
                    $cns = $Rep->lista_emisores($val);
                    while ($rst_locales = pg_fetch_array($cns)) {
                        echo "<th colspan='2' class='locales' lang='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</th>";
                        ?>

                        <?php
                    }
                    ?>
                    <th colspan="2" class="locales" lang="">TOTAL</th>     
                </tr>
                <tr>
                    <th width='150px'>Codigo</th>
                    <th width='150px'>Codigo Auxiliar</th>
                    <th>Descripcion</th>
                    <th>Precio</th>
                    <?php
                    $cns = $Rep->lista_emisores($val);
                    while ($rst_locales = pg_fetch_array($cns)) {
                        echo "
                        <th>Cant</th>
                        <th>Valor</th>
                        ";
                    }
                    ?>
                    <th>Cant</th>
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
                    $n++;

                    echo "<tr class='row' lang='$rst[ids]' >
                            <td>$rst[cod]</td>
                            <td>$rst[cod_aux]</td>
                            <td class='desc' >$rst[descr]</td>
                            <td class='desc precio' align='right'>$rst[precio]</td>";
                    $l = 0;
                    $cns1 = $Rep->lista_emisores();
                    $to = 0;
                    $cantidad = 0;
                    while ($rst_locales = pg_fetch_array($cns1)) {
                        $l++;
                        $cnt = $rst[loc . $l];
                        //MODIFICACACION DE variable antes $rst[v.$] ahora $rst[c.$] 
                        $v = $rst[c.$l];
                        $to = $to + $v;
                        $cantidad = $cantidad + $cnt;
                        echo "<td align='right'  name='cn$rst[id]$l' class='cnt$l'  >$cnt</td>    
                              <td align='right'  name='vl$rst[id]$l' class='vlr$l'  >".number_format($v,2)."</td>";
                        
                    }
                     echo "<td align='right'  name='cn$l' class='cnt'  >$cantidad</td>    
                              <td align='right'  name='vl$l' class='vlr'  >".number_format($to,2)."</td>";
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
                    <td></td>
                    <td>Totales:</td>";
                $l = 0;
                $cns1 = $Rep->lista_emisores();
                while ($rst_locales = pg_fetch_array($cns1)) {
                    $l++;
                    echo" <td align='right' id='t_cnt$l' >c</td>
                          <td align='right' id='t_vlr$l' >v</td>";
                }
                echo "<td align='right' id='t_cnt'>c</td>
                    <td align='right' id='t_vlr'>v</td>
                </tr>
            </tfoot>
        </table>";
                $local = pg_num_rows($Rep->lista_emisores());
                ?>
            <script>
//                var l = '<?php // echo $local ?>';
//                $('.row').each(function () {
//                    pr = $(this).find('.precio').html().replace(/,/g, '');
//                    var t_v = 0;
//                    j = 0;
//                    while (j < l) {
//                        j++;
//                        cnt = '.cnt' + j;
//                        t = $(this).find(cnt).html().replace(/,/g, '');
//                        t_v = parseFloat(t_v + (t * 1));
//                    }
//                    $(this).append("<td align='right' name='cn" + this.lang + "' class='cnt' >" + accounting.formatMoney(t_v, "", dec, ",", ".") + "</td><td align='right' name='vl" + this.lang + "' class='vlr' >" + accounting.formatMoney(pr * t_v, "", dec, ",", ".") + "</td>");
//                });
            </script>
            <script>
                $('.locales').each(function () {
                    cnt = 'cnt' + this.lang;
                    tcnt = 0;
                    $('.' + cnt).each(function () {
                        tcnt = (tcnt * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + cnt).html(accounting.formatMoney(tcnt, "", dc, ",", "."));
                    vlr = 'vlr' + this.lang;
                    tvlr = 0;
                    $('.' + vlr).each(function () {
                        tvlr = (tvlr * 1) + ($(this).html().replace(/,/g, '') * 1);
                    });
                    $('#t_' + vlr).html(accounting.formatMoney(tvlr, "", dec, ",", "."));
                });
            </script>
            <script>
                var ids = '<?php echo $ids ?>';
                $('#ids').val(ids);
            </script>