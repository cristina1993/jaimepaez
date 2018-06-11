<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_ingresopt.php'; //cambiar clsClase_productos
$Trasn = new Clase_industrial_ingresopt();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    $cns = $Trasn->lista_transferencias_fecha($fec1, $fec2, $emisor);
} else {
    $txt = '';
    $trs = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $det = 0;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var det = '<?php echo $det ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
//                if (det == 0) {
//                    seccion_auto();
//                }
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, b) {
                f1 = $('#fecha1').val();
                f2 = $('#fecha2').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_industrial_transferenciapt.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_industrial_transferenciapt.php?id=' + id + '&x=' + x + '&bod=' + b + '&fecha1=' + f1 + '&fecha2=' + f2;//Cambiar Form_productos
                        break;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn188{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[readonly]{
                background:#f8f8f8; 
            }
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
            }
            .totales{
                background:white;
                color:#00557F;
                font-weight:bolder; 
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
                <center class="cont_title" ><?PHP echo 'TRANSFERENCIAS DE PRODUCTO TERMINADO ' . $bodega ?> </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, secuencial)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="text" name="secuencial" id="secuencial"/>
                        PRODUCTO:<input type="text" name="prod" size="15" id="prod" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>"  />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>Usuario</th>
                    <th>Fecha de Transacción</th>
                    <th>Transaccion</th>
                    <th>Origen</th>
                    <th>Documento No</th>
                    <th>Guía de Recepción</th>
                    <th>Destino</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_cli = pg_fetch_array($Trasn->lista_un_proveedor($rst[cli_id]));
                    $cod = $rst[mp_c];
                    $des = $rst[mp_d];
                    $rst_bod = pg_fetch_array($Trasn->lista_un_local($rst[bod_id]));
                    $total+=number_format($rst[mov_cantidad], 2);
                    echo "<tr>
                        <td>$n</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst[mov_usuario]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst[mov_fecha_trans]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>TRANSFERENCIA</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst_bod[emi_nombre_comercial]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst[mov_documento]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst[mov_guia_transporte]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$rst_cli[nombres]</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$cod</td>
                        <td onclick='auxWindow(1,$rst[mov_id],1,$rst[bod_id])'>$des</td>
                        <td align='right'>$rst[mov_cantidad]</td>
                    </tr> ";
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                echo "<tr class='totales'>
                    <td colspan='10' align='right'>Total</td>
                    <td align='right'>" . number_format($total, 2) . "</td>
                </tr>";
                ?>
            </tfoot>
        </table>            
    </body>    
</html>

