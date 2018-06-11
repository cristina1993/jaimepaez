<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierres.php';
$Clase_cierre_caja = new Clase_cierres();
if (isset($_GET[desde], $_GET[hasta])) {
    $bodega = $_GET[bodega];
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if ($bodega != 0) {
        $txt = "and arq_punto_emision=$bodega";
        $cns = $Clase_cierre_caja->lista_buscador_arqueos($desde, $hasta, $txt);
    } else {
        $cns = $Clase_cierre_caja->lista_buscador_arqueos($desde, $hasta);
    }
} else {
    $actual = date('Y-m-d');
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
$cns1 = $Clase_cierre_caja->lista_locales();
//$cns2 = $Clase_cierre_caja->lista_locales();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <head>
        <meta charset=utf-8 />
        <title>Lista Cierres</title>
        <script>
            user = '<?php echo $user ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
//                Calendar.setup({inputField: actual, ifFormat: '%Y-%m-%d', button: im_actual});
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_cierres.php?id=' + id+'&bodega='+'<?php echo $bodega?>'+'&desde='+'<?php echo $desde?>'+'&hasta='+'<?php echo $hasta?>';//Cambiar Form_productos
                        break;
                }

            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script> 
        <style>
            #mn178{
                background:black;
                color:white;
                border: solid 1px white;
            }
            select{
                border:none !important; 
            }
            select:hover{
                border:none !important; 
            }
            select{
                width: 170px;
            }

            .cont_finder{
                height:40px; 
            }            
            .cont_finder div{
                margin-top:10px; 
                float:left; 
                margin-left:10px; 
            }
            #desde,#hasta,#actual{
                background:#E0E0E0; 
            }

            #mn309{
                background:black;
                color:white;
                border: solid 1px white;
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
                </center>
                <center class="cont_title" ><?PHP echo 'AUTORIZACION DE CIERRES DE CAJA' ?></center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:0px;padding:7px">   
<!--                        <select id="bodegas" name="bodegas">
                            <option value="0">Todas las Bodegas</option>
                        <?php
//                            while ($rst_locales = pg_fetch_array($cns2)) {
//                                echo "<option value='$rst_locales[cod_punto_emision]'>$rst_locales[nombre_comercial]</option>";
//                            }
                        ?>
                        </select>-->
<!--                        Fecha:<input type="text" name="actual" id="actual"  readonly size="9" style="text-align:right"  value="<?php echo $actual ?>"/>
                        <img src="../img/calendar.png" id="im_actual" />-->
                        <!--<input type="submit" id="save" onclick="auxWindow(0, desde.value, hasta.value, bodegas.value)" value="Generar Arqueo">-->
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <!--                        Vendedor:
                                                <input type="text" name="vendedor" id="vendedor" size="20">-->
                        Bodega:<select id="bodega" name="bodega">
                            <option value="0">Todas las Bodegas</option>
                            <?php
                            while ($rst_locales = pg_fetch_array($cns1)) {
                                echo "<option value='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</option>";
                            }
                            ?>
                        </select>
                        Desde:<input type="text" name="desde" id="desde"  readonly size="9" style="text-align:left"  value="<?php echo $desde ?>"/>
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" name="hasta" id="hasta"  readonly size="9" style="text-align:left"  value="<?php echo $hasta ?>"/>
                        <img src="../img/calendar.png" id="im_hasta" />
                        <input type="submit" onclick="frmSearch.submit()" value="Buscar">
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th>No</th>
                    <th>LOCAL</th>
                    <th>N DOCUMENTO</th>
                    <th>FECHA</th>
                    <th>FACTURAS DESDE</th>
                    <th>FACTURAS HASTA</th>
                    <th>ACCIONES</th>
                </tr>             
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    switch ($rst[arq_punto_emision]) {
                        case '1':$local = 'BODEGA 1';
                            break;
                        case '2':$local = 'BODEGA 2';
                            break;
                        case '3':$local = 'BODEGA 3';
                            break;
                        case '4':$local = 'BODEGA 4';
                            break;
                        case '5':$local = 'BODEGA 5';
                            break;
                        case '6':$local = 'BODEGA 6';
                            break;
                        case '7':$local = 'BODEGA 7';
                            break;
                        case '8':$local = 'BODEGA 8';
                            break;
                        case '9':$local = 'BODEGA 9';
                            break;
                        case '10':$local = 'BODEGA 10';
                            break;
                        case '11':$local = 'BODEGA 11';
                            break;
                        case '12':$local = 'BODEGA 12';
                            break;
                    }
//                    $rst_fac = pg_fetch_array($Clase_cierre_caja->lista_fac_desde_hasta($rst[cie_fecha], $rst[cie_fecha], $rst[cie_punto_emision], $rst[cie_usuario]))
                    ?>
                <td><?php echo $n ?></td>
                <td><?php echo $local ?></td>
                <td align="right"><?php echo $rst[aqr_num_documento] ?></td>
                <td align="right"><?php echo $rst[arq_fecha_emision] ?></td>
                <td align="right"><?php echo $rst[aqr_fac_desde] ?></td>
                <td align="right"><?php echo $rst[aqr_fac_hasta] ?></td>
                <td align="center">
                    <?php {
                        ?>
                        <img src="../img/upd.png"  class="auxBtn" onclick="auxWindow(0, '<?php echo $rst[arq_id] ?>')">
                        <?php
                    }
                    ?>


                </td>           
            </tr>  
            <?PHP
        }
        ?>
    </tbody>
</table>            
</body>    
</html>

