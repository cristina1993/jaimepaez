<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_factura.php';
$Set = new Clase_factura();
$txt = strtoupper($_GET[txt]);
if (isset($_GET[desde], $_GET[hasta])) {
    $txt = trim(strtoupper($_GET[txt]));
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "and(c.fac_nombre like '%$txt%' or c.fac_identificacion like '%$txt%' or c.fac_numero like '%$txt%')";
    } else {
        $texto = "and c.fac_fecha_emision between '$desde' and '$hasta'";
    }
    $cns = $Set->lista_buscador_factura($texto, $emisor);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
/////////*******RESPUESTAS************
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script type="text/javascript" src="../js/jquery.PrintArea.js_4.js"></script>
        <script>
            usuid = '<?php echo $_SESSION[usuid] ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $('#load_automaticos').load('../Includes/envio_sri.php');
                $('#load_automaticos').load('../Includes/envio_mail.php');
                $("#dialog").dialog({
                    autoOpen: false,
                    modal: true,
//                    buttons: {
//                        "Cerrar": function () {
//                            $(this).dialog("close");
//                        }
//                    }
                });
                $("#abrir")
                        .button()
                        .click(function () {
                            $("#dialog").dialog("option", "width", 600);
                            $("#dialog").dialog("option", "height", 300);
                            $("#dialog").dialog("open");

                        });
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, comid) {
                d = $('#desde').val();
                h = $('#hasta').val();
                t = $('#txt').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_factura.php?emisor=' + emisor.value + '&txt=' + t + '&desde=' + d + '&hasta=' + h;//Cambiar Form_productos
                        //look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_factura.php?id=' + id + '&x=' + x + '&emisor=' + emisor.value + '&txt=' + t + '&desde=' + d + '&hasta=' + h;//Cambiar Form_productos
                        break;
                    case 2://Eliminar
                        alert('Proceso en construccion');
                        break;
                    case 3://Envio al SRI
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            timeout: 10000,
                            type: 'POST',
                            url: '../xml/factura_xml.php',
                            data: {id: comid},
                            error: function (j, t, e) {
                                if (t == 'timeout') {
                                    loading('hidden');
                                    alert('Tiempo agotado sin respuesta del SRI \n Intente mas tarde');
                                    window.history.go(-1);
                                }
                            },
                            success: function (dt) {
//                                loading('hidden');
                                dat = dt.split('&');
                                $.post("actions.php", {act: 67, 'data[]': dat, dt: dt, id: comid},
                                function (dato) {
                                    dat1 = dato.split('&');
                                    if (dat1[0] == 0) {
//                                        window.history.go(0);
                                        if (dat[4].length == 38) {
                                            envia_mail(id);
                                        } else {
                                            window.history.go(0);
                                        }
                                    } else {
                                        alert(dato);
                                    }
                                });
                            }
                        });
                        break;
                    case 4://PDF
                        parent.document.getElementById('contenedor2').rows = "*,70%";
                        frm.src = '../Scripts/frm_pdf_factura.php?id=' + id;
                        break;
                    case 5:
                        $.post("actions.php", {act: 70, id: comid},
                        function (dt) {
                            if (dt.length == 0) {
                                dt = 'S/N';
                            }
                            obj = $('#aux' + comid);
                            obj.html(dt);

                        });
                        break;
                    case 6://PDF talonario
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/frm_pdf_talonario_factura.php?id=' + id;
//                        look_menu();
                        break;
                    case 7://EMAIL
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_factura_mail.php',
                            data: {id: id},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 73, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Factura Enviada Correctamente');
                                        window.history.go(0);
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        break;

                    case 8://Cierre de Caja
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'POST',
                            url: 'actions_cierre_caja.php',
                            data: {op: 0, id: id},
                            success: function (dt) {
                                d = dt.split('&');
                                if (d[1] == 1) {

                                    alert('No existe facturas realizadas en la fecha actual');

                                } else {
                                    if (d[1] == 0) {
                                        parent.document.getElementById('contenedor2').rows = "*,'95%";
                                        frm.src = '../Scripts/Form_cierre_caja.php?emisor=' + emisor;
                                    } else {
                                        alert(d[1]);
                                    }
                                }
                            }
                        });
                        break;
                    case 9://Genera XML
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&clave=' + x + '&tp=' + comid;
                        loading('hidden');
                        break;

                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function envia_mail(id) {
                $.ajax({
                    beforeSend: function () {

                    },
                    type: 'GET',
                    url: '../Includes/envio_mail.php?id=' + id,
                    data: {id: id},
                    timeout: 10000,
                    error: function (j, t, e) {
                        if (t == 'timeout') {
                            loading('hidden');
                            alert('Tiempo agotado No se pudo enviar Via e-mal');
                            window.history.go(-1);
                        }
                    },
                    success: function (dt) {

                        if (dt == 0) {
                            alert('Factura Enviada Correctamente');
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

            function cargar_datos(id, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                factura.value = fa;
                com_id.value = id;
            }

            function anular() {
                fec1 = $('#desde').val();
                fec2 = $('#hasta').val();
                if (usuid == 1 || usuid == 109 || usuid == 57) {
                    id = com_id.value;
                    codigo_muzo = 'brt8thir';
                    codigo_mejia = '75ma7gBU';
                    codigo_supadm = 'tvk36146';
                    $.ajax({
                        beforeSend: function () {
                            if (cod_anular.value == codigo_muzo && usuid == 57) {
                                return true;
                            } else if (cod_anular.value == codigo_mejia && usuid == 109) {
                                return true;
                            } else if (cod_anular.value == codigo_supadm && usuid == 1) {
                                return true;
                            } else {
                                $('#cod_anular').css('border', 'solid 1px red');
                                $('#cod_anular').val('');
                                return false;
                            }
                        },
                        type: 'POST',
                        url: 'actions_factura.php',
                        data: {op: 6, id: id}, //op sera de acuerdo a la acion que le toque
                        success: function (dt) {

                            tbl_aux.style.display = 'none';
                            if (dt == 0) {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            } else if (dt == 1) {
                                alert('No se puede anular este documento \n Existe una Retencion con este documento');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            } else if (dt == 2) {
                                alert('No se puede anular este documento \n Existe una Nota de Credito con este documento');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            } else if (dt == 3) {
                                alert('No se puede anular este documento \n Existe una Nota de Debitocon este documento');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            } else if (dt == 4) {
                                alert('No se puede anular este documento \n Existen Pagos a Credito cancelados');
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            } else {
                                alert(dt);
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_factura.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                            }
                        }
                    });
                } else {
                    alert('Ud no esta autorizado para realizar este proceso');
                }
            }

        </script>


    </script> 
    <style>
        #mn246{
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
    <div id="dialog" title="Ayuda">
        <p>Diálogo básico modal con botón de cerrado. Puede ser movido y con dimensiones personalizadas.</p>
    </div>
    
    <table id="tbl_aux" style="border: solid 2px black">
        <tr>
            <td colspan="2" style="font-weight:bolder ">Anulación de Documento<img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', cod_anular.value = ''"  /></td>
        </tr>
        <tr>
            <td>Factura # </td>
            <td>
                <input size="30" readonly id="factura"/>
                <input size="10" hidden type="text" id="com_id"/></td>
        </tr>
        <tr>
            <td>Codigo de autorizacion</td>
            <td><input size="30" id="cod_anular"/></td>
        </tr>
        <tr>
            <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="anular()" /></td>
        </tr>
    </table>
    <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
    <img id="charging" src="../img/load_circle.gif" />    
    <div id="cargando"></div>
    <div id="load_automaticos" hidden ></div>
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
                <img class="auxBtn" style="float:right" onclick="imprimir()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                <img id="abrir" style="float:right;margin-top:0px;padding:0px;" title="Ayuda"  src="../img/ayuda.jpg" width="26px" />                            
            </center>
            <center class="cont_title" ><?php echo "FACTURACIÓN " ?></center>
            <center class="cont_finder">
                <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                    <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FACTURA:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                    DESDE:<input type="text" size="15" name="desde" id="desde" value="<?php echo $desde ?>" />
                    <img src="../img/calendar.png" id="im-desde"/>
                    HASTA:<input type="text" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" />
                    <img src="../img/calendar.png" id="im-hasta"/>
                    <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                </form>  
            </center>
        </caption>
        <!--Nombres de la columna de la tabla-->
        <thead>
            <tr>
                <th>No</th>
                <th>FECHA</th>
                <th>FACTURA</th>
                <th>CLIENTE</th>
                <th>RUC</th>
                <th>VALOR TOTAL $</th>
                <th>VENDEDOR</th>
                <th>ESTADO</th>
                <th>ACCIONES</th>
            </tr>        
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($rst[fac_estado_aut] <> 'ANULADO') {
                    $g_total+=$rst[fac_total_valor];
                }
                if (strlen($rst[fac_estado_aut]) > 20) {
                    $sts = $rst[fac_estado_aut];
                    $rst[fac_estado_aut] = 'Error :' . substr($rst[fac_estado_aut], 0, 25);
                } else {
                    $sts = '';
                }
                $rst_ret = pg_fetch_array($Set->lista_ret_factura($rst[fac_id]));
                $rst_nc = pg_fetch_array($Set->lista_nc_factura($rst[fac_id]));
                $rst_nd = pg_fetch_array($Set->lista_nd_factura($rst[fac_id]));
                $cns_pg = $Set->lista_pagos_credito($rst[fac_id]);
                $cta_v = 0;
                while ($rp = pg_fetch_array($cns_pg)) {
                    $rst_cta = pg_fetch_array($Set->lista_ctasxcobrar_pagid($rp[pag_id], $rst[fac_id]));
                    if (!empty($rst_cta)) {
                        $cta_v = 1;
                    }
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[fac_fecha_emision] ?></td>
                    <td><?php echo $rst[fac_numero] ?></td>
                    <td><?php echo $rst[fac_nombre] ?></td>
                    <td><?php echo $rst[fac_identificacion] ?></td>
                    <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst[fac_total_valor], $dec) ?></td>
                    <td><?php echo $rst[vnd_nombre] ?></td>
                    <?php
                    if ($rst[fac_estado_aut] == 'ANULADO') {
                        ?>
                        <td style="color:darkred;font-weight:bolder "><?PHP echo substr($rst[fac_estado_aut], 0, 20) ?></td>
                        <?php
                    } else {
                        ?>
                        <td style="<?php echo $style ?>" title="<?php echo $rst[fac_estado_aut] ?>" ondblclick="cargar_datos('<?php echo $rst[fac_id] ?>', '<?php echo $rst[fac_numero] ?>', event)"><?PHP echo $rst[fac_estado_aut] ?></td>
                        <?php
                    }
                    ?>
                    <td style="width:170px">
                        <?php
                        if ($_SESSION[usuid] == 1 && empty($rst_ret) && empty($rst_nc) && empty($rst_nd) && $cta_v != 1 && $rst[fac_estado_aut] != 'ANULADO' && $rst[fac_estado_aut] != 'RECIBIDA AUTORIZADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/upd.png" onclick="auxWindow(1, '<?php echo $rst[fac_id] ?>')" />                    
                            <?PHP
                        }
                        ?>
                        <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(9, '<?php echo $rst[fac_id] ?>', '<?php echo $rst[fac_clave_acceso] ?>', '1')" />
                        <?php
                        if ($rst[fac_cedula] != '9999999999' && $rst[fac_cedula] != '9999999999999' && $rst[fac_estado_aut] == 'RECIBIDA AUTORIZADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/mail.png" onclick="envia_mail('<?php echo $rst[fac_id] ?>')">          
                            <?PHP
                        }
                        ?>

                        <img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(4, '<?php echo $rst[fac_id] ?>')">
                        <img class="auxBtn" width="75px" src="../img/talon.png" onclick="auxWindow(6, '<?php echo $rst[fac_id] ?>')">
                </tr>  
                <?php
            }
            ?>
        </tbody>

        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td bgcolor="#D8D8D8" colspan="16" rowspan="5"><br><br><br><br><br><br></td>
    </tr>
</table>            
</body>    
</html>

