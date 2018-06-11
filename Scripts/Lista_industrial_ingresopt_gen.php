<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; //cambiar clsClase_productos
$Ing = new Clase_industrial_ingresopt();
$cns_comb = $Ing->lista_un_mp_mod1();
if ($ctr_inv == 0) {
    $fra = '';
} else {
    $fra = "and m.bod_id=$emisor";
}
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $ingr = trim(strtoupper($_GET[txt_ing]));
    $doc = trim(strtoupper($_GET[txt_doc]));
    $prod = trim(strtoupper($_GET[txt_prod]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $ids = $_GET[ids];
    if ($ids == '') {
        $ids = 'no';
    }

    if (!empty($ingr)) {
        $text = " $fra and (m.mov_documento like '%$ingr%' or c.cli_raz_social like '%$ingr%') and p.mp_i='0' and p.ids=$ids";
        $fec1 = '';
        $fec2 = '';
    } else if (!empty($doc)) {
        $text = " $fra and (m.mov_num_fac_entrega like '%$doc%') and p.mp_i='0' and p.ids=$ids";
    } else if (!empty($prod)) {
        $text = " $fra and (p.mp_c like '%$prod%' or p.mp_d like '%$prod%' ) and p.mp_i='0' and p.ids=$ids";
    } 
    else {
        $text = " $fra and m.mov_fecha_trans between '$fec1' and '$fec2' and p.mp_i='0' and p.ids=$ids";
    }
    $n_prod = pg_num_rows($Ing->lista_num_productos($text, '8'));
    $cns = $Ing->lista_buscador_industrial_ingresopt($text, '8');
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
//    $prod = trim(strtoupper($_GET[prod]));
    $nm = trim(strtoupper($_GET[txt]));
} else {
    $txt = '';
    $trs = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Industrial Ingreso General</title>
    <head>
        <script>
            var ids = '<?php echo $ids ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                if (ids == 'no') {
                    alert('Elija tipo');
                }
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_industrial_ingresopt_gen.php';//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_ingresopt_gen.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }
        </script> 
        <style>
            #mn54,
            #mn59,
            #mn70,
            #mn75,
            #mn80,
            #mn85,
            #mn90,
            #mn95,
            #mn100,
            #mn105{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
            .sel{
                font-size: 11px;
                width: 100px;
            }

        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
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
                <center class="cont_title" ><?PHP echo 'INGRESO GENERAL ' . $bodega ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        TIPO:<select id="ids" name="ids" class="sel">
                            <!--<option value="">SELECCIONE</option>-->
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                $dt = explode('&', $rst_c[mp_tipo]);
                                ?>
                                <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        DOCUMENTO:<input type="text" name="txt_ing" size="12" id="txt_ing" value="<?php echo $ingr ?>"/>
                        DOC.ENTREGA:<input type="text" name="txt_doc" size="12" id="txt_doc" value="<?php echo $doc ?>"/>
                        PRODUCTO:<input type="text" name="txt_prod" size="12" id="txt_prod" value="<?php echo $prod ?>"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="4">Documento</th>
                    <th colspan="3">Producto</th>
                    <th colspan="4">Transaccción</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Fecha de Transacción</th>
                    <th>Documento No</th>
                    <th>Doc. Entrega No</th>
                    <th>Proveedor</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Costo Unitario</th>
                    <th>Costo Total</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                $grup = '';

                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $t_cnt+=$rst['mov_cantidad'];
                    $t_u+=$rst['mov_val_unit'];
                    $t_t+=$rst['mov_val_tot'];
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <?php
//                        if ($grup != $rst['mov_documento']) {
                        ?>
                        <td align="center"><?php echo $rst['mov_fecha_trans'] ?></td>
                        <td><?php echo $rst['mov_documento'] ?></td>
                        <td><?php echo $rst['mov_num_fac_entrega'] ?></td>
                        <td><?php echo $rst['cli_raz_social'] ?></td>
                        <?php
//                        } else {
                        ?>
    <!--                            <td></td>
                        <td></td>
                        <td></td>-->
                        <?php
//                        }
                        ?>
                        <td><?php echo $rst['mp_c'] ?></td>                    
                        <td><?php echo $rst['mp_d'] ?></td>
                        <td><?php echo $rst['mp_q'] ?></td>
                        <td><?php echo $rst['trs_descripcion'] ?></td>
                        <td align="right"><?php echo number_format($rst['mov_cantidad'], $dc) ?></td>
                        <td align="right"><?php echo number_format($rst['mov_val_unit'], $dec) ?></td>
                        <td align="right"><?php echo number_format($rst['mov_val_tot'], $dec) ?></td>
                    </tr>  
                    <?PHP
//                    $grup = $rst['mov_documento'];
                }
                ?>
            </tbody>
            <tr>
                <!--<td class="totales" ></td>-->
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" ></td>
                <td class="totales" >Total</td>                                
                <td class="totales" >Items <?php echo number_format($n, $dc) ?></td>
                <td class="totales" >PRODUCTOS <?php echo number_format($n_prod, 0) ?></td>
                <td class="totales" align="right" ><?php echo number_format($t_cnt, $dec) ?></td>
                <td class="totales" align="right" ><?php echo number_format($t_u, $dec) ?></td>
                <td class="totales" align="right" ><?php echo number_format($t_t, $dec) ?></td>
            </tr>
        </table>            
    </body>    
</html>
<script>
    var ids = '<?php echo $ids ?>';
    $('#ids').val(ids);
</script>

