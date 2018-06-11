<?php
include_once '../Includes/permisos.php';
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
                $('#desde').val('<?php echo date('Y-m-d') ?>');
                $('#hasta').val('<?php echo date('Y-m-d') ?>');
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
                } else if (a == 3) {
                    if ($('#txt').val().length == 0) {
                        v = 1;
                        sms = 'Ingrese Cedula o Ruc del cliente';
                    } else {
                        v = 0;
                    }
                } else {
                    v = 0;
                }

                if (v == 0) {
                    switch (a) {
                        case '0'://Cuentas x cobrar
                            frm.src = '../Scripts/frm_pdf_ctasxcobrar.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val();
                            break;
                        case '1'://Saldos x cuenta
                            frm.src = '../Scripts/frm_pdf_saldoxcuenta.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val();
                            break;
                        case '2'://Cartera Vencida
                            frm.src = '../Scripts/frm_pdf_cartera_ven.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val();
                            break;
                        case '3'://Estado de Cuenta
                            frm.src = '../Scripts/frm_pdf_estado_cuenta.php?txt=' + $('#txt').val() + '&d=' + $('#desde').val() + '&h=' + $('#hasta').val() + '&e=' + $('#estado').val() + '&cli=' + $('#txt').val();
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
                        case '0'://Cuentas x cobrar
                            frm.src = '../Scripts/Form_diario_general_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
                            break;
                        case '1'://Libro Mayor
                            frm.src = '../Scripts/Form_libro_mayor_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
                            break;
                        case '2'://Balance de Comprobacion
                            frm.src = '../Scripts/Form_balance_comprobacion_excel.php?desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val() + '&nivel=' + $('#nivel').val();
                            break;
                        case '3'://Balance general
                            frm.src = '../Scripts/Form_balance_general_excel.php?nivel=' + $('#nivel').val() + '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                        case '4'://Estado de Perdidas y Ganancias
                            frm.src = '../Scripts/Form_estado_pyg_excel.php?nivel=' + $('#nivel').val() + '&anio=' + $('#anio').val() + '&mes=' + $('#mes').val();
                            break;
                    }
                } else {
                    alert(sms);
                }
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
                <center class="cont_title" ><?PHP echo 'REPORTES CUENTAS POR COBRAR' ?></center>
                <center class="cont_finder">
                    <div> 
                        Reporte:    
                        <select id="reporte">
                            <option value="">Elija una Opcion</option>
                            <option value="0">Cuentas por cobrar</option>
                            <option value="1">Saldos por cuentas</option>
                            <option value="2">Cartera vencida</option>
                            <option value="3">Estado de cuenta cliente</option>
                        </select>
                    </div>
                    <div id="cont_txt">cliente:
                        <input type="text" id="txt" size="20"/>
                    </div>

                    <div id="cont_fecha">
                        Desde:<input type="text" id="desde" size="12" readonly style="text-align:right" />
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" size="12" readonly style="text-align:right" />
                        <img src="../img/calendar.png" id="im_hasta"/>
                    </div>
                    <div><input type="submit" id="save" onclick="auxWindow()" value="Generar"></div>
                    <!--<div><input type="submit" id="save" onclick="auxWindow1()" value="Generar Reporte Excel"></div>-->

                </center>
            </caption>
        </table>                    
    </body>
    <html>
