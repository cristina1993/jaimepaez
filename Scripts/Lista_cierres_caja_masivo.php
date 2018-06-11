<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
if (isset($_GET[vendedor])) {
    $vend = strtoupper(trim($_GET[vendedor]));
    $bodega = $_GET[bodega];
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if ($vend != '') {
        if ($bodega != 0) {
            $dat = "WHERE  c.cie_usuario=cast(v.vnd_id as varchar) and v.vnd_nombre='$vend' AND c.cie_fecha between '$desde' and '$hasta' and c.cie_punto_emision=$bodega ";
            $cns = $Clase_cierre_caja->lista_cierres_vendedor($dat);
        } else {
            $dat = "WHERE  c.cie_usuario=cast(v.vnd_id as varchar) and  v.vnd_nombre='$vend' AND c.cie_fecha between '$desde' and '$hasta'";
            $cns = $Clase_cierre_caja->lista_cierres_vendedor($dat);
        }
    } else if ($bodega == 0) {
        $vend = '';
        $cns = $Clase_cierre_caja->lista_cierres_caja_todas($desde, $hasta);
    } else {
        $vend = '';
        $cns = $Clase_cierre_caja->lista_cierres_caja($desde, $hasta, $bodega);
    }
} else {
    $actual = date('Y-m-d');
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $user = strtoupper($rst_user[usu_person]);
    $cns = $Clase_cierre_caja->lista_cierres_caja_masivo($desde, $hasta);
}
$actual = date('Y-m-d');
$cns1 = $Clase_cierre_caja->lista_locales();
$cns2 = $Clase_cierre_caja->lista_locales();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <head>
        <meta charset=utf-8 />
        <title>Lista Cierres Caja Masivo</title>
        <script>
            user = '<?php echo $user ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: actual, ifFormat: '%Y-%m-%d', button: im_actual});
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(user, fec) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                frm.src = '';
                var bod = bodegas.value;
                switch (bod) {
                    case '0'://Cierre Caja todas las Bodegas
                        $.post("actions_cierres_caja_masivo.php", {op: 0, user: user, fec: fec},
                        function (dt) {
                            if (dt == '') {
                                main.src = '../Scripts/Lista_cierres_caja_masivo.php';
                            } else {
                                if (dt == 0) {
                                    alert('No existen datos en la fecha indicada');
                                } else {
                                    alert(dt);
                                }
                            }
                        });
                        break;
                    case '1'://Condado
                        $.post("actions_cierres_caja_masivo.php", {op: 1, user: user, fec: fec, bod: bod},
                        function (dt) {
                            if (dt == '') {
                                main.src = '../Scripts/Lista_cierres_caja_masivo.php';
                            } else {
                                if (dt == 0) {
                                    alert('No existen datos en la fecha indicada');
                                } else {
                                    alert(dt);
                                }
                            }
                        });
                        break;
                    case '2'://Quicentro Sur
                        $.post("actions_cierres_caja_masivo.php", {op: 1, user: user, fec: fec, bod: bod},
                        function (dt) {
                            if (dt == '') {
                                main.src = '../Scripts/Lista_cierres_caja_masivo.php';
                            } else {
                                if (dt == 0) {
                                    alert('No existen datos en la fecha indicada');
                                } else {
                                    alert(dt);
                                }
                            }
                        });
                        break;
                    case '3'://Mall del Sol
                        $.post("actions_cierres_caja_masivo.php", {op: 1, user: user, fec: fec, bod: bod},
                        function (dt) {
                            if (dt == '') {
                                main.src = '../Scripts/Lista_cierres_caja_masivo.php';
                            } else {
                                if (dt == 0) {
                                    alert('No existen datos en la fecha indicada');
                                } else {
                                    alert(dt);
                                }
                            }
                        });
                        break;
                }
            }

            function auxWindow1(a, user, fec, emi)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://PDF Cierres de Cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_cierres_caja_masivo.php?user=' + user + '&fec=' + fec + '&emi=' + emi;
                        look_menu();
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
            #mn191,
            #mn192,
            #mn193,
            #mn194,
            #mn195,
            #mn196,
            #mn197,
            #mn198,
            #mn199,
            #mn200{
                background:black;
                color:white;
                border: solid 1px white;
            }
            ._err{
                background: red !important;
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
                <center class="cont_title" ><?PHP echo 'CIERRES DE CAJA BODEGAS' ?></center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:0px;padding:7px">   
                        <select id="bodegas" name="bodegas">
                            <option value="0">Todas las Bodegas</option>
                            <?php
                            while ($rst_locales = pg_fetch_array($cns2)) {
                                echo "<option value='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</option>";
                            }
                            ?>
                        </select>
                        Fecha:<input type="text" name="actual" id="actual"  readonly size="9" style="text-align:right"  value="<?php echo $actual ?>"/>
                        <img src="../img/calendar.png" id="im_actual" />
                        <input type="submit" id="save" onclick="auxWindow('<?php echo $user ?>', actual.value)" value="Cerrar">
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Vendedor:
                        <input type="text" name="vendedor" id="vendedor" size="20">
                        <select id="bodega" name="bodega">
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
                    <th>N DOCUMENTO</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>LOCAL</th>
                    <th>VENDEDOR</th>
                    <th>TOTAL FACTURAS</th>
                    <th>TOTAL NOTAS CREDITO</th>
                    <th>REPORTE</th>
                </tr>             
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    switch ($rst[cie_punto_emision]) {
                        case '1':$local = 'Matriz';
                            break;
                        case '2':$local = 'Colon';
                            break;
                        case '3':$local = 'CCNU';
                            break;
                    }
                    $totfac +=$rst['cie_total_facturas'];
                    $totnc += $rst['cie_total_notas_credito'];


                    $sub = $rst[cie_subtotal];
                    $desc = $rst[cie_descuento];
                    $iva = $rst[cie_iva];
                    $resp = (round($sub,2) - round($desc,2)) + round($iva,2);

                    $ntc = $rst[cie_total_tarjeta_credito];
                    $ndb = $rst[cie_total_tarjeta_debito];
                    $che = $rst[cie_total_cheque];
                    $efec = $rst[cie_total_efectivo];
                    $cert = $rst[cie_total_certificados];
                    $bono = $rst[cie_total_bonos];
                    $rete = $rst[cie_total_retencion];
                    $nc = $rst[cie_total_not_credito];
                    $cre = $rst[cie_total_credito];
                    $resul = round($ntc,2) + round($ndb,2) + round($che,2) + round($efec,2) + round($cert,2) + round($bono,2) + round($rete,2) + round($nc,2) + round($cre,2);

                    if (round($resp,2) != round($resul, 2)) {
                        $error = '_err';
                    } else {
                        $error = '';
                    }
                    ?>
                    <tr class="<?php echo $error ?>">
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['cie_secuencial']?></td>
                        <td><?php echo $rst['cie_fecha'] ?></td>
                        <td><?php echo $rst['cie_hora'] ?></td>
                        <td><?php echo $local ?></td>
                        <td><?php echo $rst['vnd_nombre'] ?></td>
                        <td align="right"><?php echo number_format($rst['cie_total_facturas'],2) ?></td>
                        <td align="right"><?php echo number_format($rst['cie_total_notas_credito'],2) ?></td>
                        <td align="center">
                            <?php {
                                ?>
                                <img src="../img/orden.png"  class="auxBtn" width="10px" onclick="auxWindow1(0, '<?php echo $rst['cie_usuario'] ?>', '<?php echo $rst['cie_fecha'] ?>', '<?php echo $rst['cie_punto_emision'] ?>')">
                                <?php
                            }
                            ?>


                        </td>           
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>
            <tr style="font-weight:bolder">
                <td colspan="6" align="right" >Total</td>
                <td align="right" style="font-size:14px;" id="gtotal"><?php echo number_format($totfac, 2) ?></td>
                <td align="right" style="font-size:14px;"><?php echo number_format($totnc, 2) ?></td>
                <td colspan="6"></td>
            </tr>
        </table>            
    </body>    
</html>

