<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_resumen_ctasxpagar.php';
$Docs = new Clase_resumen_ctasxpagar();
$rst[chq_fecha] = date('Y-m-d');
if (isset($_GET[fecha1], $_GET[fecha2])) {
     $txt = trim(strtoupper($_GET[txt]));
    $est = $_GET[estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
       $texto = "and (cl.cli_raz_social like'%$txt%' or ctp_concepto like'%$txt%' or ctp_forma_pago like'%$txt%')";
    } else {
        $texto = "and ctp_fecha_pago between '$fec1' and '$fec2'";
    }
    $cns = $Docs->lista_ctasxpagar($texto);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                posicion_accion();
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

        
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

          
          function generar_documentos(id) {
                parent.document.getElementById('contenedor2').rows = "*,80%";
                frm = parent.document.getElementById('bottomFrame');
                frm.src = '../Scripts/frm_resumen_ctasxpagar.php?id=' + id;

            }

           

        </script> 
        <style>
            #mn180{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }

        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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
                <center class="cont_title" ><?php echo "RESUMEN CUENTAS X PAGAR" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                               
                    </form>  
                </center>
            </caption>

            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>FECHA PAGO</th>
            <th>PROVEEDOR</th>
            <th>DOCUMENTO</th>                                
            <th>CONCEPTO</th>
            <th>FORMA PAGO</th>
            <th>CUENTA</th>
            <th>VALOR</th>
            <th>ACCION</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[ctp_fecha_pago] ?></td>
                    <td><?php echo $rst[cli_raz_social] ?></td>
                    <td><?php echo $rst[reg_num_documento] ?></td>
                    <td><?php echo $rst[ctp_concepto] ?></td>
                    <td><?php echo $rst[ctp_forma_pago] ?></td>
                    <td><?php echo $rst[ctp_banco] ?></td>
                    <td align="right"><?php echo number_format($rst[ctp_monto], 2) ?></td>
                    <td align="center">
                        <img src='../img/orden.png' class="auxBtn" width="20px" onclick="generar_documentos('<?PHP echo $rst[ctp_id]?>')" /> 
                    </td>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>
<script>
    var e = '<?php echo $est ?>';
    $('#estado').val(e);
</script>
