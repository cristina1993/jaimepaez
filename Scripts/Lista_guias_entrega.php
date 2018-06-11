<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
$emisor = 1;
$bodega = '';
$ems = '0001-';
$txt = strtoupper($_GET[txt]);
if (isset($_GET[txt])) {
    $des = str_replace('-', '', $_GET[desde]);
    $has = str_replace('-', '', $_GET[hasta]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $cns = $Set->lista_buscador_factura($txt,'1');
    } else {

        $cns = $Set->lista_factura_fecha($des, $has, $emisor,'1');
    }
} else {
    $des = date('Ymd');
    $has = date('Ymd');
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
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, comid) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_guia_entrega.php?emisor=' + emisor.value;//Cambiar Form_productos
                        //look_menu();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_guia_entrega.php?id=' + id + '&x=' + x + '&emisor=' + emisor.value;//Cambiar Form_productos
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
                        //frm.src = '../Scripts/print_factura.php?id=' + id;
                        frm.src = '../Scripts/frm_pdf_factura.php?id=' + id;
                        
                        //look_menu();
                        
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
                        $.ajax({
                            beforeSend: function () {
                            },
//                            timeout: 6000,
                            type: 'POST',
                            url: '../xml/factura_xml.php',
                            data: {id: comid, gnr: 1},
                            success: function (dt) {
                                loading('hidden');
                                window.location = '../Reports/descargar_xml.php?id=' + dt;
                            }
                        });
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
                    url: '../Reports/pdf_factura_mail.php',
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
            }

            function mensaje() {
                alert('Esta Opción se Habilitará con el Modulo de Facturación');
//                var x;
//                if (confirm("ESTA SEGURO DE HACER EL CIERRE DE CAJA") == true) {
//                    auxWindow(8);
//                } else {
//                    x = "";
//                }
            }

            function imprimir() {
//                jQuery.downloadReporte = function (url, data) {
//                    url = "../Reports/" + url;
//                    $.ajax({
//                        url: url,
//                        data: data,
//                        type: 'post',
//                        success: function (datar) {
//                            var randomDivImpresion = Math.floor(Math.random()); //Numero aleatorio
//                            var nombreDivImpresion = 'recibeImpresion' + randomDivImpresion; //Div temporal con numero aleatorio en el nombre
//                            var div_impresion = '<div id="' + nombreDivImpresion + '"></div>'; //codigo html del div
//                            $(div_impresion).appendTo('body'); //se agrega al elemento body, para hacerlo funcional.
//                            $("#" + nombreDivImpresion).html(datar); //se asigna la pagina que viene desde el servidor.
//                            $("#" + nombreDivImpresion).printArea(); //se invoca la impresion.
//                            $("#" + nombreDivImpresion).remove(); //se remueve el div temporal despues de la impresion.
//                        }
//                    });
//                }
            }

        </script> 
        <style>
            #mn203{
                background:black;
                color:white;
                border: solid 1px white;
            }

        </style>
    </head>
    <body>
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
                    <img class="auxBtn" style="float:right" onclick="imprimir()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" ><?php echo "GUIAS DE ENTREGA "?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" value="<?php echo $emisor ?>" id="emisor" />
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GUIA:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
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
                    <th>No</th>
                    <th>FECHA</th>
                    <th>FACTURA</th>
                    <th>CLIENTE</th>
                    <th>RUC</th>
                    <th>VALOR TOTAL $</th>
                    <th>VENDEDOR</th>
                    <th>ACCIONES</th>
                </tr>        
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $g_total+=$rst['total_valor'];
//                    $rst_v = pg_fetch_array($Set->lista_vendedores("where vnd_id=$rst[vendedor]"));
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst['fecha_emision'] ?></td>
                        <td><?php echo $rst['num_documento'] ?></td>
                        <td><?php echo $rst['nombre'] ?></td>
                        <td><?php echo $rst['identificacion'] ?></td>
                        <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst['total_valor'], $dec) ?></td>
                        <td><?php echo $rst[vendedor] ?></td>
                        <td style="width:170px">
                            <?php
                            if (strlen($rst['com_autorizacion']) != 37 || $_SESSION[usuid] == 1) {
                                ?>
                                <img class="auxBtn" width="12px" src="../img/upd.png" onclick="auxWindow(1, '<?php echo $rst[com_id] ?>')" />                    
                                <?PHP
                            }
                            ?>
                            <img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(4, '<?php echo $rst['num_documento'] ?>')">
                    </tr>  
                    <?php
                }
                ?>
            </tbody>
            <tr style="font-weight:bolder">
                <td colspan="5" align="right">Total</td>
                <td align="right" style="font-size:14px;"><?php echo number_format($g_total, $dec) ?></td>
                <td></td>
                <td></td>
            </tr>
        </table>            
    </body>    
</html>

