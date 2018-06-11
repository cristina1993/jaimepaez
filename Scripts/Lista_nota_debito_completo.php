<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota_debito.php'; //cambiar clsClase_productos
//include_once '../Clases/clsSetting.php';
$Clase_nota_debito = new Clase_nota_debito();
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];

    if (!empty($txt)) {
        $text = "and (ndb_identificacion like '%$txt%' or ndb_nombre like '%$txt%' or ndb_numero like '%$txt%' or ndb_num_comp_modifica like '%$txt%' or ndb_estado_aut like'%$txt%')";
    } else {
        $text = "and ndb_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_nota_debito->lista_buscador_nota_debito($text);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $cns = $Clase_nota_debito->lista_nota_debito_completo_noaut();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
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
            });

            function cargar_claves() {
                $.ajax({
                    beforeSend: function () {
                    },
                    type: 'POST',
                    url: 'actions_nota_debito.php',
                    data: {op: 1}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        alert(dt);
                        window.location = 'Lista_nota_debito_completo.php';
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
                    url: 'actions_nota_debito.php',
                    data: {op: 2, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_nota_debito_completo.php';
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
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
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
                            tp = 5;
                        }
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        break;

                    case 1://Genera XML
                        window.location = '../Includes/envio_mail_nota_debito.php?id=' + id;
                        break;

                }
            }
        </script> 
        <style>
            #tbl_aux{
                position:fixed; 
                display:none; 
                background:white; 
            }
            #tbl_aux tr{
                border-bottom:solid 1px #ccc  ;
            }
            #mensaje{
                position:fixed;
                top:50px;
                right:20px; 
                color:white;
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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="mensaje" ondblclick="this.hidden = true"></div>

        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2"><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none'"  /></td>
            </tr>
            <tr>
                <td>Nota Debito # </td>
                <td><input size="60" readonly id="factura"/></td>
            </tr>
            <tr>
                <td>Clave_Acceso</td>
                <td><input size="60" readonly id="clave_acceso"/></td>
            </tr>
            <tr>
                <td>Num_Autorizacion</td>
                <td><input size="50" id="num_auto" maxlength="37"/></td>
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
                <center class="cont_title" ><?PHP echo 'NOTA DE DEBITO COMPLETA' ?>
                    <img src="../img/set.png" class="auxBtn" onclick="cargar_claves()" /></center>

                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>"/>
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>"/>
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button><img src="../img/finder.png"/>
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
                    <th>NOTA DEBITO</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>VALOR TOTAL_$</th>
                    <th>FECHA/HORA/AUTO</th>
                    <th>ESTADO</th>
                    <th>CLAVE DE ACCESO</th>
                    <th>NUM AUTORIZACION SRI</th>   
                    <th>e-mail</th>   
                    <th>ACCIONES</th>   
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;

                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $g_total+=$rst[ndb_total_valor];
                    if (empty($rst[ndb_autorizacion])) {

                        $tx_err1 = "No se puede conectar";
                        $tx_err2 = "NO AUTORIZADO CLAVE ACCESO REGISTRADA";
                        $tx_err3 = "NO AUTORIZADO ERROR SECUENCIAL REGISTRADO";
                        $tx_err4 = "javalangNullPointerException";
                        $estdado = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[ndb_estado_aut]);
                        $obs = preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $rst[ndb_observacion]);
                        $err1 = strpos($obs, $tx_err1);
                        $err2 = strpos($obs, $tx_err2);
                        $err3 = strpos($obs, $tx_err3);
                        $err4 = strpos($obs, $tx_err4);
                        if ($err1 == true) {
                            $estdado = 'SIN CONECCION';
                        }
                        if ($err2 == true || $err3 == true) {
                            $estdado = 'DEVUELTA';
                        }
                        if ($err4 == true && strlen($obs) < 50) {
                            $estdado = 'RECIBIDA AUTORIZADO';
                            $rst[ndb_utorizacion] = 'NO SE RECUPERÓ NA';
                        }
                        if ($err4 == true && strlen($obs) > 50) {
                            $estdado = 'RECIBIDA';
                            $rst[ndb_autorizacion] = 'ERROR DE ACCESO AL SRI';
                        }
                        $class = 'incorrecto';
                    } else {
                        $estdado = $rst[ndb_estado_aut];
                        $class = '';
                    }
                    $estaemail = $rst[ndb_estado_correo];
                    $nomemi = $rst[ndb_nombre];
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
                        $estaemail = $rst[ndb_estado_correo];
                        $nomemi = $rst[ndb_nombre];
                    }
                    $cla1 = '"' . $rst[ndb_clave_acceso] . '"';
                    $doc1 = '"' . $rst[ndb_numero] . '"';

                    echo "<tr class='$class'>
                        <td>$n</td>
                        <td align='center'>$rst[ndb_fecha_emision]</td>
                        <td align='center'>$rst[ndb_numero]</td>
                        <td>$rst[ndb_nombre]</td>
                        <td>$rst[ndb_identificacion]</td>
                        <td align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[ndb_total_valor], 2) . "</td>
                        <td>$rst[ndb_fec_hora_aut]</td>
                        <td id='id$rst[ndb_id]' ondblclick='auxWindow(5, 0, 0, $rst[ndb_id])'>$estdado</td>
                        <td>$rst[ndb_clave_acceso]</td>";
                    if (strlen($rst[ndb_autorizacion]) == 37 && !empty($rst[ndb_fec_hora_aut])) {
                        echo "<td>$rst[ndb_autorizacion]</td>";
                    } else {
                        if (empty($rst[ndb_autorizacion]) && empty($rst[ndb_fec_hora_aut])) {
                            $sms = 'NO SE ENCUENTRA ENVIADA';
                        } else {
                            $sms = '';
                        }
                        echo "<td style='color:darkred;font-weight:bolder' ondblclick='cargar_datos($cla1, $doc1, event)' >$sms $rst[ndb_autorizacion]</td>";
                    }
                    $sms1 = '"' . $sms . '"';
                    echo "<td>$rst[ndb_estado_correo]</td>
                        <td><img class='auxBtn' width='12px' src='../img/xml.png' onclick='auxWindow(0, $rst[ndb_id], $cla1, $sms1)'/>";

                    if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {

                        echo "<img class='auxBtn' width='12px' src='../img/mail.png' onclick='auxWindow(1, $rst[ndb_id])'></td>";
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
            <tr style="font-weight:bolder">
                <td colspan="5" align="right">Total</td>
                <td align="right" style="font-size:14px;"><?php echo number_format($g_total, 2) ?></td>
                <td colspan="7"></td>
            </tr>
        </table>            
    </body>    
</html>
