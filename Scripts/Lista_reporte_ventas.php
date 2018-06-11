<?php
include_once '../Includes/permisos.php';
$fec1 = date('Y-m-d');
$fec2 = date('Y-m-d');
?>
<!doctype html>
<html lang='es'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu Reporte Ventas</title>
        <script>
            $(function () {
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function auxWindow() {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                frm.src = '../Scripts/frm_pdf_reporte_ventas.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
            }
            function fechas(op) {
                switch (op)
                {
                    case 1:
                        $('#desde').val('<?php echo $fec1 ?>');
                        $('#hasta').val('<?php echo $fec2 ?>');
                        break;
                    case 2:
                        var fecha = '<?php echo $fec1 ?>';
                        fecha = fecha.replace("-", "/").replace("-", "/");
                        fecha = new Date(fecha);
                        fecha.setDate(fecha.getDate() - 1);
                        var anio = fecha.getFullYear();
                        var mes = fecha.getMonth() + 1;
                        var dia = fecha.getDate();
                        if (mes.toString().length < 2) {
                            mes = "0".concat(mes);
                        }

                        if (dia.toString().length < 2) {
                            dia = "0".concat(dia);
                        }
                        $('#desde').val(anio + "-" + mes + "-" + dia);
                        $('#hasta').val(anio + "-" + mes + "-" + dia);
                        break;
                    case 3:
                        var fecha = '<?php echo $fec1 ?>';
                        fecha = new Date(fecha);
                        var anio = fecha.getFullYear();
                        var mes = fecha.getMonth() + 1;
                        var dia = fecha.getMonthDays();
                        if (mes.toString().length < 2) {
                            mes = "0".concat(mes);
                        }
                        $('#desde').val(anio + '-' + mes + '-01');
                        $('#hasta').val(anio + '-' + mes + '-' + dia);
                        break;
                }
                auxWindow();
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }


        </script>
        <style>
            input,select{
                border:solid 1px #ccc;
            }
            #mn185{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #desde,#hasta{
                background:#E0E0E0; 
            }
        </style>
    </head>
    <body>

        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head" >
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
                <center class="cont_title" ><?PHP echo 'REPORTE GENERAL DE VENTAS' ?></center>
                <center class="cont_finder">
                    <div> 
                        Desde:<input type="text" id="desde" size="12" readonly style="text-align:right" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" size="12" readonly style="text-align:right" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im_hasta"/>
                        <input type="submit" id="save" onclick="fechas(1)" value="Hoy">
                        <input type="submit" id="save" onclick="fechas(2)" value="Ayer">
                        <input type="submit" id="save" onclick="fechas(3)" value="Mes">
                        <input type="submit" id="save" onclick="auxWindow()" value="Generar">

                    </div>
                </center>
            </caption>
        </table>                            
    </body>
    <html>
