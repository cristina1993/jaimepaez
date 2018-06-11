<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_retencion.php'; //cambiar clsClase_productos
//include_once '../Clases/clsSetting.php';
$Clase_retencion = new Clase_retencion();
if ($emisor >= 10) {
    $ems = '0' . $emisor . '-';
} else {
    $ems = '00' . $emisor . '-';
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = trim($_GET[fecha1]);
    $fec2 = trim($_GET[fecha2]);
    if (!empty($txt)) {
        $text = "(r.ret_numero like '%$txt%' or r.ret_nombre like '%$txt%' or r.ret_identificacion like '%$txt%' or r.ret_estado_aut like '%$txt%')";
    } else {
        $text = "r.ret_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_retencion->lista_buscador_retencion($text);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $cns = $Clase_retencion->lista_retencion_completo_noaut();
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
                    url: 'actions_retencion.php',
                    data: {op: 5}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == '') {
                            window.location = 'Lista_retencion_completo.php';
                        }
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
                    url: 'actions_retencion.php',
                    data: {op: 6, na: na, fh: fh, id: ca}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_retencion_completo.php';
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
                            tp = 7;
                        }
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&tp=' + tp;
                        break;

                    case 1://Genera XML
                        window.location = '../Includes/envio_mail_retencion.php?id=' + id;
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
        <div id="load_automaticos" hidden ></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2"><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none'"  /></td>
            </tr>
            <tr>
                <td>Retencion # </td>
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
                <center class="cont_title" ><?PHP echo 'RETENCION COMPLETO' ?>
                    <img src="../img/set.png" class="auxBtn" onclick="cargar_claves()" /></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>"/>
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
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
                    <th>RETENCION</th>
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
                $grup = '';
                $i;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $g_total+=$rst[ret_total_valor];
                    if (strlen(trim($rst[ret_autorizacion])) == 37) {
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

                    $estaemail = $rst[ret_estado_correo];
                    $nomemi = $rst[ret_nombre];
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
                        $estaemail = $rst[ret_estado_correo];
                        $nomemi = $rst[ret_nombre];
                    }
                    ?>
                    <tr class='<?php echo $class ?>'>
                        <td><?php echo $n ?></td>
                        <td align="center"><?php echo $rst['ret_fecha_emision'] ?></td>
                        <td align="center"><?php echo $rst['ret_numero'] ?></td>
                        <td><?php echo $rst['ret_nombre'] ?></td>
                        <td><?php echo $rst['ret_identificacion'] ?></td>
                        <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst['ret_total_valor'], 2) ?></td>
                        <td><?PHP echo $rst[ret_fec_hora_aut] ?></td>
                        <td id='<?php echo 'id' . $rst[ret_numero] ?>' ondblclick="auxWindow(5, 0, 0, '<?php echo $rst[ret_numero] ?>')"><?PHP echo $estdado ?></td>
                        <td><?PHP echo $rst['ret_clave_acceso'] ?></td>
                        <?php
                        if (strlen($rst['ret_clave_acceso']) == 37 && !empty($rst['ret_fec_hora_aut'])) {
                            ?>
                            <td><?PHP echo $rst['ret_autorizacion'] ?></td>
                            <?php
                        } else {
                            if (empty($rst['ret_autorizacion']) && empty($rst['ret_fec_hora_aut'])) {
                                $sms = 'NO SE ENCUENTRA ENVIADA';
                            } else {
                                $sms = '';
                            }
                            ?>
                            <td style="color:darkred;font-weight:bolder  " ondblclick="cargar_datos('<?php echo $rst[ret_clave_acceso] ?>', '<?php echo $rst['ret_numero'] ?>', event)" ><?PHP echo $sms . $rst['ret_autorizacion'] ?></td>
                            <?php
                        }
                        ?>
                        <td><?PHP echo $rst[ret_estado_correo] ?></td>
                        <td><img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(0, '<?php echo $rst[ret_id] ?>', '<?php echo $rst[ret_clave_acceso] ?>', '<?php echo $sms ?>')" />
                            <?php
                            if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {
                                ?>
                                <img class="auxBtn" width="12px" src="../img/mail.png" onclick="auxWindow(1, '<?php echo $rst[ret_id] ?>')">      </td>    
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
                <td colspan="7"></td>
            </tr>
        </table>            
    </body>    
</html>
