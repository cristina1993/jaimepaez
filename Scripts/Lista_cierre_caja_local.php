<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cierre_caja.php';
$Clase_cierre_caja = new Clase_cierre_caja();
if (isset($_GET[desde])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $emisor;
    $cns = $Clase_cierre_caja->lista_cierres_caja($desde, $hasta, $emisor);
} else {
    $actual = date('Y-m-d');
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $cns = $Clase_cierre_caja->lista_cierres_caja($desde, $hasta, $emisor);
    $user = $rst_user[usu_person];
    $rst_vend = pg_fetch_array($Clase_cierre_caja->lista_vendedor($user));
    if ($rst_vend[usu_person] == $user) {
        $usu = $rst_vend[usu_person];
    }
}
$actual = date('Y-m-d');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Cierre de Caja</title>
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

            function cierre(user, fec) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                sms = confirm("ESTA SEGURO DE GENERAR EL CIERRE DE CAJA ?");
                if (sms == true) {
                    $.post("actions_cierre_caja_n.php", {op: 0, user: user, fec: fec, emi: emisor.value},
                    function (dt) {
                        if (dt == 1) {
                            alert('No existe facturas realizadas en la fecha actual');
                        } else {
                            if (dt == 0) {
                                parent.document.getElementById('contenedor2').rows = "*,'95%";
                                frm.src = '../Scripts/Form_cierre_caja.php?emisor=' + emisor.value + '&fec=' + fec;
                            } else {
                                alert(dt);
                            }
                        }
                    });
                } else {
                    return false;
                }
            }

            function auxWindow(a, user, fec)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://PDF Cierres de Cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_cierre_caja.php?user=' + user + '&fec=' + fec;
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
                <center class="cont_title" ><?php echo "CIERRE DE CAJA BODEGA " . $bodega ?></center>
                <center class="cont_finder">
                    <div style="float:right;margin-top:0px;padding:7px">
                        FECHA:<input type="date" size="10" name="actual" id="actual" readonly value="<?php echo $actual ?>" />
                        <img src="../img/calendar.png" id="im-actual"/>
                        <input type="submit" onclick="cierre(user, actual.value)" value="Cerrar Caja">
                    </div>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <div>
                            <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                            DESDE:<input type="date" size="10" name="desde" id="desde" readonly value="<?php echo $desde ?>" />
                            <img src="../img/calendar.png" id="im-desde"/>
                            HASTA:<input type="date" size="10" name="hasta" id="hasta" readonly value="<?php echo $hasta ?>" />
                            <img src="../img/calendar.png" id="im-hasta"/>
                        </div>
                        <div><input type="submit" onclick="frmSearch.submit()" value="Buscar Cierre Caja"></div>
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th>No</th>
                    <th>N DOCUMENTO</th>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>VENDEDOR</th>
                    <th>TOTAL FACTURAS</th>
                    <th>TOTAL NOTAS CREDITO</th>
                    <th>REPORTE</th>
                </tr>             
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $totfac +=$rst['cie_total_facturas'];
                    $totnc += $rst['cie_total_notas_credito'];
                    ?>
                <td><?php echo $n ?></td>
                <td><?php echo $rst['cie_secuencial'] ?></td>
                <td><?php echo $rst['cie_fecha'] ?></td>
                <td><?php echo $rst['cie_hora'] ?></td>
                <td><?php echo $rst['vnd_nombre'] ?></td>
                <td align="right"><?php echo $rst['cie_total_facturas'] ?></td>
                <td align="right"><?php echo $rst['cie_total_notas_credito'] ?></td>
                <td align="center">
                    <?php {
                        ?>
                        <img src="../img/orden.png"  class="auxBtn" onclick="auxWindow(0, '<?php echo $rst['cie_usuario'] ?>', '<?php echo $rst['cie_fecha'] ?>')">
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
        <td colspan="5" align="right" >Total</td>
        <td align="right" style="font-size:14px;" id="gtotal"><?php echo number_format($totfac, 4) ?></td>
        <td align="right" style="font-size:14px;"><?php echo number_format($totnc, 4) ?></td>
        <td colspan="5"></td>
    </tr>
</table>            
</body>    
</html>

