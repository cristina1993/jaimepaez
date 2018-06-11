<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_movimientopt.php'; //cambiar clsClase_productos
$Clase_industrial_movimientopt = new Clase_industrial_movimientopt();
$cns_comb = $Clase_industrial_movimientopt->lista_un_mp_mod1();
if ($ctr_inv == 0) {
    $fra = '';
} else {
    $fra = "and m.bod_id=$emisor";
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $ids = $_GET[ids];
    if ($ids == '') {
        $ids = 'no';
    }
    if (!empty($txt)) {
        $text = " $fra and (m.mov_documento like '%$txt%' or m.mov_guia_transporte like '%$txt%' or c.cli_raz_social like '%$txt%' or t.trs_descripcion like '%$txt%' or p.mp_c like '%$txt%' or p.mp_d like '%$txt%') and p.mp_i='0' and p.ids=$ids";
        $fec1 = '';
        $fec2 = '';
    } else {
        $text = "$fra and m.mov_fecha_trans between '$fec1' and '$fec2' and p.mp_i='0' and p.ids=$ids";
    }
    $cns = $Clase_industrial_movimientopt->lista_buscador_industrial_ingresopt($text);
    $nm = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
if ($ids == '26') {
    $exc = 6;
} else {
    $exc = 7;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
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
                        frm.src = '../Scripts/Form_industrial_movimientopt.php'
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_movimientopt.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }
            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=inv_mp.csv';
            }

            function load_file() {
                $('#frm_file').submit();
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
            #mn56,
            #mn61,
            #mn72,
            #mn77,
            #mn82,
            #mn87,
            #mn92,
            #mn97,
            #mn102,
            #mn107{
                background:black;
                color:white;
                border: solid 1px white;
            }
            div.upload {
                padding:5px; 
                width: 14px;
                height: 20px;
                background-color: #568da7;        
                background-image:-moz-linear-gradient(
                    top,
                    rgba(255,255,255,0.4) 0%,
                    rgba(255,255,255,0.2) 60%);
                color:#FFFFFF; 
                overflow: hidden;
                border-radius: 4px 4px 4px 4px; 
                cursor:pointer; 
                border:solid 1px #ccc; 
            }
            div.upload:hover{
                background-color:#7198ab;        
            }
            div.upload input {
                margin-top:-20; 
                margin-left:-5; 
                display: block !important;
                width: 40px !important;
                height: 40px !important;
                opacity: 0 !important;
                overflow: hidden !important;
                cursor:pointer; 
            }    
            #txt_load{
                margin-right:5px; 
                margin-top:13px; 
            }
            .sel{
                font-size: 11px;
                width: 100px;
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="12"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="12" align="center"><?PHP echo 'MOVIMIENTOS ' . $bodega ?></th></tr>
                <tr>
                    <td colspan="12"><?php echo 'Desde: ' . $fec1 . ' Hasta: ' . $fec2 ?></td>
                </tr>
            </thead>
        </table>
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=<?php echo $exc ?>" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" ><?PHP echo 'MOVIMIENTOS ' . $bodega ?> </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_inv_mp.php" method="POST" enctype="multipart/form-data">
                        <div class="upload">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>

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
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $nm ?>" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
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
                    <th>Guía de Recepción</th>
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
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <?php
//                        if ($grup != $rst['mov_documento']) {
                        ?>
                        <td align="center"><?php echo $rst['mov_fecha_trans'] ?></td>
                        <td><?php echo $rst['mov_documento'] ?></td>
                        <td><?php echo $rst['mov_guia_transporte'] ?></td>
                        <td><?php echo $rst['cli_raz_social'] ?></td>
                        <?php
//                        } else {
                        ?>
    <!--                            <td></td>
                        <td></td>
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
        </table>            
    </body>    
</html>
<script>
    var ids = '<?php echo $ids ?>';
    $('#ids').val(ids);
</script>