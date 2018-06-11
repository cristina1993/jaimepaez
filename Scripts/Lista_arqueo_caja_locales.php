<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
if (isset($_GET[desde])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $actual = date('Y-m-d');
    $emisor;
    $cns = $Clase_cierre_caja->lista_buscador_arq_caja($desde, $hasta, $emisor);
} else {
    $actual = date('Y-m-d');
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Arqueo de Caja</title>
    <head>
        <script>
            user = '<?php echo $usu ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "actual", ifFormat: "%Y-%m-%d", button: "im-actual"});
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, actual) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Formulario arqueo de caja
                        $.post("actions_cierre_caja_n.php", {op: 3, fec: actual, emi: emisor.value},
                        function (dt) {
                            if (dt == 1) {
                                alert('Ya existe un Arqueo de Caja en esta fecha');
                            } else if (dt == 2) {
                                alert('No existe un Cierre de Caja en esta Fecha');
                            } else {
                                if (dt == 0) {
                                    parent.document.getElementById('contenedor2').rows = "*,80%";
                                    frm.src = '../Scripts/Form_arqueo_caja_locales.php?actual=' + actual;
                                } else {
                                    alert(dt);
                                }
                            }
                        });
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
        </style>
    </head>
    <body>

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
                <center class="cont_title" ><?php echo "ARQUEO DE CAJA BODEGA " . $bodega ?></center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:0px;padding:7px">
                        FECHA:<input type="date" size="10" name="actual" id="actual" readonly value="<?php echo $actual ?>" />
                        <img src="../img/calendar.png" id="im-actual"/>
                        <input type="submit" onclick="auxWindow(0, actual.value)" value="Generar Arqueo de Caja">
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <div>
                            <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                            DESDE:<input type="date" size="10" name="desde" id="desde" readonly value="<?php echo $desde ?>" />
                            <img src="../img/calendar.png" id="im-desde"/>
                            HASTA:<input type="date" size="10" name="hasta" id="hasta" readonly value="<?php echo $hasta ?>" />
                            <img src="../img/calendar.png" id="im-hasta"/>
                        </div>
                        <div><input type="submit" onclick="frmSearch.submit()" value="Buscar"></div>
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>LOCAL</th>
                    <th>No DOCUMENTO</th>
                    <th>FECHA</th>
                    <th>FACTURAS DESDE</th>
                    <th>FACTURAS HASTA</th>
                </tr>             
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                <td><?php echo $n ?></td>
                <td><?php echo $bodega ?></td>
                <td><?php echo $rst['aqr_num_documento'] ?></td>
                <td><?php echo $rst['arq_fecha_emision'] ?></td>
                <td><?php echo $rst['aqr_fac_desde'] ?></td>
                <td><?php echo $rst['aqr_fac_hasta'] ?></td>          
            </tr>  
            <?PHP
        }
        ?>
    </tbody>
</table>            
</body>    
</html>

