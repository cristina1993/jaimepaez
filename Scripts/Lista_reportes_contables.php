<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_asientos.php';
$Set = new Clase_asientos();
?>
<!doctype html>
<html lang='es'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Menu</title>
        <script>
            $(function () {
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
                parent.document.getElementById('contenedor2').rows = "*,0%";
                $('#cont_nivel').hide();
                $('#cont_periodo').hide();
                $('#cont_fecha').hide();
                $('#cont_cta').hide();
                $('#cuenta').val('');
            });
            function auxWindow() {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                frm.src = '';
                var a = reporte.value;
                if (a.length == 0) {
                    v = 1;
                    sms = 'Elija un Reporte';
                } else if ((a == '0' || a == '1') && (desde.value.length == 0 || hasta.value.length == 0)) {
                    v = 1;
                    sms = 'Rango de Fechas Incorrectas';
                } else if (desde.value > hasta.value) {
                    v = 1;
                    sms = 'Rango de Fechas Incorrectas';
                } else {
                    v = 0;
                }

                if (v == 0) {
                    switch (a) {
                        case '0'://Diario General
                            frm.src = '../Scripts/frm_pdf_libro_diario.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
                            break;
                        case '1'://Libro Mayor
                            frm.src = '../Scripts/frm_pdf_mayorizacion.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val() + '&cuenta=' + $('#cuenta').val();
                            break;
                        case '2'://Balance de Comprobacion
                            frm.src = '../Scripts/frm_pdf_balance_comprobacion.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val() + '&nivel=' + $('#nivel').val();
                            break;
                        case '3'://Balance general
                            frm.src = '../Scripts/frm_pdf_balance_general.php?nivel=' + $('#nivel').val() + '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                        case '4'://Estado de Perdidas y Ganancias
                            frm.src = '../Scripts/frm_pdf_epyg.php?nivel=' + $('#nivel').val()+ '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                    }
                } else {
                    alert(sms);
                }
            }

            function auxWindow1() {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                frm.src = '';
                var a = reporte.value;
                if (a.length == 0) {
                    v = 1;
                    sms = 'Elija un Reporte';
                } else if ((a == '0' || a == '1') && (desde.value.length == 0 || hasta.value.length == 0)) {
                    v = 1;
                    sms = 'Rango de Fechas Incorrectas';
                } else if (desde.value > hasta.value) {
                    v = 1;
                    sms = 'Rango de Fechas Incorrectas';
                } else {
                    v = 0;
                }

                if (v == 0) {
                    switch (a) {
                        case '0'://Diario General
                            frm.src = '../Scripts/Form_diario_general_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
                            break;
                        case '1'://Libro Mayor
                            frm.src = '../Scripts/Form_libro_mayor_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val()+'&cuenta='+$('#cuenta').val();
                            break;
                        case '2'://Balance de Comprobacion
                            frm.src = '../Scripts/Form_balance_comprobacion_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val() + '&nivel=' + $('#nivel').val();
                            break;
                        case '3'://Balance general
                            frm.src = '../Scripts/Form_balance_general_excel.php?nivel=' + $('#nivel').val() + '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                        case '4'://Estado de Perdidas y Ganancias
                            frm.src = '../Scripts/Form_estado_pyg_excel.php?nivel=' + $('#nivel').val()+ '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                    }
                } else {
                    alert(sms);
                }
            }

            function mostrar_ocultar(obj) {
                $('#desde').val('<?php echo date('Y-m-d') ?>');
                $('#hasta').val('<?php echo date('Y-m-d') ?>');
                switch (obj.value) {
                    case '0':
                        $('#cont_nivel').hide();
                        $('#cont_periodo').hide();
                        $('#cont_fecha').show();
                        $('#cont_cta').hide();
                        $('#cuenta').val('');
                        break;
                    case '1':
                        $('#cont_nivel').hide();
                        $('#cont_periodo').hide();
                        $('#cont_fecha').show();
                        $('#cont_cta').show();
                        break;
                    case '2':
                        $('#cont_nivel').show();
                        $('#cont_periodo').show();
                        $('#cont_fecha').show();
                        $('#cont_cta').hide();
                        $('#cuenta').val('');

                        break;
                    case '3':
                        $('#cont_nivel').show();
                        $('#cont_periodo').show();
                        $('#cont_fecha').show();
                        $('#cont_cta').hide();
                        $('#cuenta').val('');

                        break;
                    case '4':
                        $('#cont_nivel').show();
                        $('#cont_periodo').show();
                        $('#cont_fecha').show();
                        $('#cont_cta').hide();
                        $('#cuenta').val('');

                        break;
                    default :
                        $('#cont_nivel').hide();
                        $('#cont_periodo').show();
                        $('#cont_fecha').show();
                        $('#cont_cta').hide();
                        $('#cuenta').val('');

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
                <center class="cont_title" ><?PHP echo 'REPORTES CONTABLES' ?></center>
                <center class="cont_finder">
                    <div> 
                        Reporte:    
                        <select id="reporte" onchange="mostrar_ocultar(this)">
                            <option value="">Elija una Opcion</option>
                            <option value="0">Diario General</option>
                            <option value="1">Libro Mayor</option>
                            <option value="2">Balance de Comprobacion</option>
                            <option value="3">Balance General</option>
                            <option value="4">Estado de Perdidas y Ganancias</option>
                        </select>
                    </div>
                    <div id="cont_nivel">Nivel:
                        <select id="nivel">
                            <option value="1">Nivel 1</option>
                            <option value="2">Nivel 2</option>
                            <option value="3">Nivel 3</option>
                            <option value="4">Nivel 4</option>
                            <option value="5">Nivel 5</option>
                        </select>
                    </div>
                    <div id="cont_periodo">
                        Perdiodo: 
                        Año:
                        <select id="anio">
                            <option value="2015">2015</option>
                            <option value="2016">2016</option>
                            <option value="2017">2017</option>
                            <option value="2018">2018</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                        </select>
                        Mes:
                        <select id="mes">
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                            <option value="13">Anual</option>

                        </select>
                    </div>
                    <div id="cont_cta">
                        Cuenta:<input type="text" id="cuenta" size="20" list="cuentas" onfocus="this.style.width = '400px';" onblur="this.style.width = '120px';"/>
                    </div>
                    <div id="cont_fecha">
                        Desde:<input type="text" id="desde" size="12" readonly style="text-align:right" />
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" size="12" readonly style="text-align:right" />
                        <img src="../img/calendar.png" id="im_hasta"/>
                    </div>
                    <div><input type="submit" id="save" onclick="auxWindow()" value="Generar"></div>
                    <div><input type="submit" id="save" onclick="auxWindow1()" value="Generar Reporte Excel"></div>

                </center>
            </caption>
        </table>                    
    </body>
    <html>
        <datalist id="cuentas">
            <?php
            $cns_ctas = $Set->lista_plan_cuentas();
            while ($rst_cta = pg_fetch_array($cns_ctas)) {
                echo "<option value='$rst_cta[pln_codigo]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
            }
            ?>
        </datalist>
