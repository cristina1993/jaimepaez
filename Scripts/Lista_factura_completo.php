<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura_completo.php';
$Set = new Clase_factura_completo();
if (isset($_GET[txt])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $cns = $Set->lista_una_factura_aut($_GET[txt], $emisor);
    } else {
        $cns = $Set->lista_factura_por_fechas($desde, $hasta, $emisor);
    }
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $cns = $Set->lista_factura_completo_noaut();
}
/////////*******RESPUESTAS************
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Facturas Autorizadas y no Autorizadas</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $('#load_automaticos').load('../Includes/envio_sri_factura.php');
                $('#load_automaticos').load('../Includes/envio_mail.php');
                setInterval('contador()', 25000);
            });


            function contador() {
                window.location = 'Lista_factura_completo.php';
            }


            function cargar_claves() {
                $.ajax({
                    beforeSend: function () {
                    },
                    type: 'POST',
                    url: 'actions_factura.php',
                    data: {op: 5}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        window.location = 'Lista_factura_completo.php';
                    }
                })
            }
            function insert_na() {
                ca = clave_acceso.value;
                na = num_auto.value;
                fh = fh_auto.value;
                $.ajax({
                    beforeSend: function () {
                        if (ca.length == 0) {
                            $("#clave_acceso").css({borderColor: "red"});
                            $("#clave_acceso").focus();
                            return false;
                        } else if (na.length != 37) {
                            $("#num_auto").css({borderColor: "red"});
                            $("#num_auto").focus();
                            return false;
                        } else if (fh.length == 0) {
                            $("#fh_auto").css({borderColor: "red"});
                            $("#fh_auto").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 72, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_factura_completo.php';
                        } else {
                            alert(dt);
                        }

                    }
                })
            }
            function cargar_datos(ca, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                clave_acceso.value = ca;
                factura.value = fa;
            }

            function auxWindow(a, id, x, sms)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Genera XML
                        if (sms != 'NO SE ENCUENTRA ENVIADA') {
                            id = x;
                            tp = 0;
                        } else {
                            id = id;
                            tp = 1;
                        }
//                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        loading('hidden');
                        break;

                    case 1://ENVIO MAIL
                        window.location = '../Includes/envio_mail_factura.php?id=' + id;
                        break;

                }
            }

        </script>  
        <style>
            #tbl_aux{
                position: fixed;                                     
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }
            .incorrecto{
                font-family:Arial, Helvetica, sans-serif; 
                border: 1px solid;
                margin: 10px 0px;
                padding:15px 10px 15px 50px;
                background-repeat: no-repeat;
                background-position: 10px center;
                color: #D8000C;
                background-color: #FFBABA !important;
            }
        </style>
    </head>
    <body>

        <div id="mensaje" ondblclick="this.hidden = true"></div>

        <div id="load_automaticos" <?php
        if ($_SESSION[usuid] == 1) {
            echo 'hidden';
        } else {
            echo 'hidden';
        }
        ?> >x</div>

        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2"><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none'"  /></td>
            </tr>
            <tr>
                <td>Factura # </td>
                <td><input size="60" readonly id="factura"/></td>
            </tr>
            <tr>
                <td>Clave_Acceso</td>
                <td><input size="60" readonly id="clave_acceso"/></td>
            </tr>
            <tr>
                <td>Num_Autorizacion</td>
                <td><input size="50" id="num_auto"/></td>
            </tr>
            <tr>
                <td>Fecha_Hora_Auto</td>
                <td><input size="50" id="fh_auto"/></td>
            </tr>
            <tr>
                <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="insert_na()" /></td>
            </tr>
        </table>
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
                <center class="cont_title" >FACTURACION COMPLETA <img src="../img/set.png" class="auxBtn" onclick="cargar_claves()" /></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                        FACTURA:<input type="text" name="txt" size="15" id="txt" />
                        DESDE:<input type="date" size="15" name="desde" id="desde" value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="date" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th colspan="6">DOCUMENTO</th>
                    <th colspan="6">SERVICIO DE RENTAS INTERNAS</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>FECHA</th>
                    <th>FACTURA</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>VALOR TOTAL_$</th>
                    <th>FECHA/HORA/AUTO</th>
                    <th>ESTADO</th>
                    <th>CLAVE DE ACCESO</th>
                    <th>NUM AUTORIZACION SRI</th>
                    <th>E-Mail</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $g_total+=$rst[fac_total_valor];
//CONTROL DE ERRORES ***********************
                    if (strlen(trim($rst[fac_autorizacion])) == 37) {
                        $estdado = 'RECIBIDA AUTORIZADA';
                        $class = '';
                    } else {
                        $class = 'incorrecto';
                        $err1 = strpos($rst[fac_estado_aut], 'SIN CONEXION');
                        if ($err1 == true) {
                            $estdado = 'SIN CONEXION';
                        } else {
                            $estdado = substr($rst[fac_estado_aut], 0, 30);
                        }
                    }

                    $estaemail = $rst[fac_estado_correo];
                    $nomemi = $rst[fac_nombre];
                    if ($nomemi == 'CONSUMIDOR FINAL') {
                        $nomemi = 'CONSUMIDOR FINAL';
                    }
                    if ($estaemail == 'ERROR AL ENVIAR') {
                        $estaemail = 'ERROR AL ENVIAR';
                    }
                    if ($estaemail == 'PENDIENTE DE ENVIAR') {
                        $estaemail = 'PENDIENTE DE ENVIAR';
                    }
                    if ($estaemail == 'ENVIADO') {
                        $estaemail = 'ENVIADO';
                    } else {
                        $estaemail = $rst[fac_estado_correo];
                        $nomemi = $rst[fac_nombre];
                    }

//                   if (empty($rst['fac_estado_aut'])) {
//
//                        $tx_err1 = "No se puede conectar";
//                        $tx_err2 = "NO AUTORIZADO CLAVE ACCESO REGISTRADA";
//                        $tx_err3 = "NO AUTORIZADO ERROR SECUENCIAL REGISTRADO";
//                        $tx_err4 = "javalangNullPointerException";
//                        $estdado = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[fac_estado_aut]);
//                        $obs = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[fac_observaciones]);
//                        $err1 = strpos($obs, $tx_err1);
//                        $err2 = strpos($obs, $tx_err2);
//                        $err3 = strpos($obs, $tx_err3);
//                        $err4 = strpos($obs, $tx_err4);
//                        if ($err1 == true) {
//                            $estdado = 'SIN CONECCION';
//                        }
//                        if ($err2 == true || $err3 == true) {
//                            $estdado = 'DEVUELTA';
//                        }
//                        if ($err4 == true && strlen($obs) < 50) {
//                            $estdado = 'RECIBIDA AUTORIZADO';
//                            $rst['ret_estado_aut'] = 'NO SE RECUPERÓ NA';
//                        }
//                        if ($err4 == true && strlen($obs) > 50) {
//                            $estdado = 'RECIBIDA';
//                            $rst['ret_estado_aut'] = 'ERROR DE ACCESO AL SRI';
//                        }
//                    } else {
//                        $estdado = $rst[fac_estado_aut];
//                    }
                    ?>
                    <tr class='<?php echo $class ?>'>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[fac_fecha_emision] ?></td>
                        <td><?php echo $rst[fac_numero] ?></td>
                        <td><?php echo $rst[fac_nombre] ?></td>
                        <td><?php echo $rst[fac_identificacion] ?></td>
                        <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst[fac_total_valor], 2) ?></td>
                        <td><?PHP echo $rst[fac_fec_hora_aut] ?></td>
                        <td id='<?php echo 'id' . $rst[fac_numero] ?>' ondblclick="auxWindow(5, 0, 0, '<?php echo $rst[fac_numero] ?>')"><?PHP echo $estdado ?></td>
                        <td><?PHP echo $rst[fac_clave_acceso] ?></td>
                        <?php
                        if (strlen($rst[fac_autorizacion]) == 37 && !empty($rst[fac_fec_hora_aut])) {
                            ?>
                            <td><?PHP echo $rst[fac_autorizacion] ?></td>
                            <?php
                        } else {
                            if (empty($rst[fac_autorizacion]) && empty($rst[fac_fec_hora_aut])) {
                                $sms = 'NO SE ENCUENTRA ENVIADA';
                            } else {
                                $sms = '';
                            }
                            ?>
                            <td style="color:darkred;font-weight:bolder  " ondblclick="cargar_datos('<?php echo $rst[fac_clave_acceso] ?>', '<?php echo $rst[fac_numero] ?>', event)" ><?PHP echo $sms . $rst[fac_autorizacion] ?></td>
                            <?php
                        }
                        ?>
                        <td><?PHP echo $rst[fac_estado_correo] ?></td>
                        <td><img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(0, '<?php echo $rst[fac_id] ?>', '<?php echo $rst[fac_clave_acceso] ?>', '<?php echo $sms ?>')" />
                            <?php
                            if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {
                                ?>
                                <img class="auxBtn" width="12px" src="../img/mail.png" onclick="auxWindow(1, '<?php echo $rst[fac_id] ?>')"></td>    
                            <?PHP
                        }
                        ?>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>
            <tr style="font-weight:bolder">
                <td colspan="5" align="right">Total</td>
                <td align="right" style="font-size:14px;"><?php echo number_format($g_total, 2) ?></td>
                <td colspan="6"></td>
            </tr>
        </table>            
    </body>    
</html>

