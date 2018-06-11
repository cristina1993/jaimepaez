<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php'; //cambiar clsClase_productos
$Clase_guia_remision = new Clase_guia_remision();
$id = $_GET[id];
$emisor = 1;
$bodega = '';
if (isset($_GET[id])) {
    $x = str_replace('-', '', trim($id));
    $cns = $Clase_guia_remision->lista_guias_factura($x);
}
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = str_replace('-', '', trim(strtoupper($_GET[txt])));
    $fec1 = str_replace('-', '', trim($_GET[fecha1]));
    $fec2 = str_replace('-', '', trim($_GET[fecha2]));

    if (!empty($txt)) {
        $txt = "where num_comprobante like '%$txt%' or num_comprobante_venta like '%$txt%' or identificacion_trasportista like '%$txt%' or identificacion_destinario like '%$txt%' or nombre_destinatario like '%$txt%' or punto_partida like '%$txt%' or destino like '%$txt%' or documento_aduanero like '%$txt%' or descripcion_producto like '%$txt%' or cod_producto like '%$txt%' or identificacion_remitente like '%$txt%'";
        $fec1 = '';
        $fec2 = '';
    } else {
        $txt = "where fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_guia_remision->lista_buscador_guias($txt);
} else {
    $txt = '';
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

            }

            function auxWindow(a, id, x, y)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_guia_remision.php?x=' + id;
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&x=' + x;
                        break;
                    case 2://Listar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&x=' + x + '&y=' + y;
                        break;
                    case 3://PDF
                        frm.src = '../Scripts/frm_pdf_guia_remision.php?id=' + id;
                        break;
                    case 4://XML
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {
                            },
                            type: 'POST',
                            url: '../xml/guia_remision_xml.php',
                            data: {id: id},

                            success: function (dt) {
                                dat = dt.split('&');
                                $.post("actions.php", {act: 69, 'data[]': dat, id: id},
                                function (dato) {
                                    loading('hidden');
                                    if (dato == 0) {
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
                    case 5://PDF
                        loading('visible');
                        $.ajax({
                            beforeSend: function () {

                            },
                            type: 'GET',
                            url: '../Reports/pdf_guia_remision.php',
                            data: {id: id, x: 1},
                            success: function (dt) {
                                loading('hidden');
                                $.post("actions.php", {act: 74, dt: dt, id: id},
                                function (dt) {
                                    if (dt == 0) {
                                        alert('Guia Enviada Correctamente');
                                        window.history.go(0);
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                        break;

                }
            }
            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_guia_remision.php", {id: id, op: 1}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
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
                    url: '../Reports/pdf_guia_remision.php',
                    data: {id: id, x: 1},
                    success: function (dt) {
                        loading('hidden');
                        $.post("actions.php", {act: 74, dt: dt, id: id},
                        function (dt) {
                            if (dt == 0) {
                                alert('Guia Enviada Correctamente');
                                window.history.go(0);
                            } else {
                                alert(dt);
                            }
                        });
                    }
                });
            }

        </script> 
        <style>
            input[type=text]{
                text-transform: uppercase;
            }

        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_title" ><?php echo 'GUIAS DE REMISION DE LA FACTURA ' . $id ?><font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0, '<?php echo $id; ?>')" >Nuevo </a>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha de Emision</th>
            <th>No. Guia Remision</th>
            <th>Documento No.</th>
            <th>Cliente</th>
            <th>Fecha Inicio Trans.</th>
            <th>Fecha Fin Trans.</th>
            <th>Motivo Traslado</th>
            <th>Punto Partida</th>
            <th>Destino</th>
<!--            <th>ESTADO</th>
            <th>AUTORIZACION</th>-->
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';

            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $d = substr($rst[fecha_inicio_transporte], 6, 2);
                $m = substr($rst[fecha_inicio_transporte], 4, -2);
                $a = substr($rst[fecha_inicio_transporte], 0, -4);
                $rst['fecha_inicio_transporte'] = $a . '-' . $m . '-' . $d;
                $d1 = substr($rst[fecha_fin_transporte], 6, 2);
                $m1 = substr($rst[fecha_fin_transporte], 4, -2);
                $a1 = substr($rst[fecha_fin_transporte], 0, -4);
                $rst['fecha_fin_transporte'] = $a1 . '-' . $m1 . '-' . $d1;
                $d2 = substr($rst[fecha_emision], 6, 2);
                $m2 = substr($rst[fecha_emision], 4, -2);
                $a2 = substr($rst[fecha_emision], 0, -4);
                $rst['fecha_emision'] = $a2 . '-' . $m2 . '-' . $d2;
                $c1 = substr($rst[num_comprobante], 0, 3);
                $c2 = substr($rst[num_comprobante], 3, 3);
                $c3 = substr($rst[num_comprobante], 6, 9);
                $num_comprobante = $c1 . '-' . $c2 . '-' . $c3;
                $v1 = substr($rst[num_comprobante_venta], 0, 3);
                $v2 = substr($rst[num_comprobante_venta], 3, 3);
                $v3 = substr($rst[num_comprobante_venta], 6, 9);
                $num_comprobante_venta = $v1 . '-' . $v2 . '-' . $v3;
                //CONTROL DE ERRRORES CORREO************************
                $estaemail = $rst[guia_estado_correo];
                $nomemi = $rst[nombre_destinatario];
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
                    $estaemail = $rst[guia_estado_correo];
                    $nomemi = $rst[nombre_destinatario];
                }
                ?>
                <tr>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $n ?></td>
                    <td align="center" onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['fecha_emision'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $num_comprobante ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $num_comprobante_venta ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['nombre_destinatario'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['fecha_inicio_transporte'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['fecha_fin_transporte'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['motivo_traslado'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['punto_partida'] ?></td>
                    <td onclick="auxWindow(2, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>', 1)"><?php echo $rst['destino'] ?></td>
<!--                    <td><?PHP echo $rst[com_estado] ?></td>
                    <td><?PHP echo $rst[com_autorizacion] ?></td>-->
                    <td align="center">
                        <?php
                        ?>
                        <img class="auxBtn" width="12px" src="../img/del_reg.png" onclick="del('<?php echo $rst[num_comprobante] ?>')">
                        <?php
                        if ($rst[com_estado] != 'RECIBIDA AUTORIZADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/upd.png" onclick="auxWindow(1, '<?php echo $rst[num_comprobante] ?>', '<?php echo $rst[num_comprobante_venta] ?>')" />                    
                            <!--<img class="auxBtn" width="12px" src="../img/sri.ico" onclick="auxWindow(4, '<?php echo $rst[num_comprobante] ?>', 0, 0)" />-->

                            <?PHP
                        }
                        ?>

                        <?php
//                        if ($estaemail != 'ENVIADO' && $nomemi != 'CONSUMIDOR FINAL') {
                            ?>
                            <!--<img src="../img/mail.png" width="12px"  class="auxBtn" onclick="auxWindow(5, '<?php echo $rst[num_comprobante] ?>', 0)">-->
                            <?PHP
//                        }
                        ?>
                        <img src="../img/orden.png" width="12px"  class="auxBtn" onclick="auxWindow(3, '<?php echo $rst[num_comprobante] ?>', 0)">
                    </td>
                    <?php
                    if ($_SESSION[usuid] == 1) {
                        ?>
                        <td><?PHP echo $rst_ret[com_observacion] ?></td>
                        <?php
                    }
                    ?>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

