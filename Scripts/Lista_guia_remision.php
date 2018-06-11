<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_guia_remision.php'; //cambiar clsClase_productos
$Clase_guia_remision = new Clase_guia_remision();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];

    if (!empty($txt)) {
        $text = "and g.emi_id=$emisor and (g.gui_identificacion like '%$txt%' or g.gui_nombre like '%$txt%' or g.numero like '%$txt%')";
    } else {
        $text = "and g.emi_id=$emisor and g.gui_fecha_emision between '$fec1' and '$fec2' ";
    }
    $cns = $Clase_guia_remision->lista_buscador_guias($text);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
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
                $('#load_automaticos').load('../Includes/envio_sri_guia_remision.php');
                $('#load_automaticos').load('../Includes/envio_mail.php');
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x,comid)
            {
                d = $('#fecha1').val();
                h = $('#fecha2').val();
                t = $('#txt').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_guia_remision.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_guia_remision.php?id=' + id + '&txt=' + t + '&fecha1=' + d + '&fecha2=' + h;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 3://PDF
                        frm.src = '../Scripts/frm_pdf_guia_remision.php?id=' + id + '&txt=' + t + '&fecha1=' + d + '&fecha2=' + h;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                     case 4://xml
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

            function del(id, num)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_guia_remision.php", {id: id, op: 1, data: num}, function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_guia_remision.php?txt=' + '<?php echo $txt ?>' + 'fecha1=' + '<?php echo $fec1 ?>' + '&fecha2=' + '<?php echo $fec2 ?>';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            
            function envia_mail(id) {
                $.ajax({
                    beforeSend: function () {
                        loading('visible');
                    },
                    type: 'GET',
                    url: '../Includes/envio_mail_guia.php?id=' + id,
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
                            alert('Guia de Remision Enviada Correctamente');
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }

        </script> 
        <style>
            #mn266{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }

        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>   
        <img id="charging" src="../img/load_bar.gif" />    
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "GUIAS DE REMISION BODEGA " . $bodega ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt"  value="<?php echo $txt ?>" />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>"/>
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
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
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                //CONTROL DE ERRRORES CORREO************************
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
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td align="center" ><?php echo $rst[gui_fecha_emision] ?></td>
                    <td><?php echo $rst[gui_numero] ?></td>
                    <td><?php echo $rst[gui_num_comprobante] ?></td>
                    <td><?php echo $rst[gui_nombre] ?></td>
                    <td><?php echo $rst[gui_fecha_inicio] ?></td>
                    <td><?php echo $rst[gui_fecha_fin] ?></td>
                    <td><?php echo $rst[gui_motivo_traslado] ?></td>
                    <td><?php echo $rst[gui_punto_partida] ?></td>
                    <td><?php echo $rst[gui_destino] ?></td>
                    <td><?php echo $rst[gui_estado_aut] ?></td>
                    <td align="center">
                        <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(4, '<?php echo $rst[gui_id] ?>', '<?php echo $rst[gui_clave_acceso] ?>', '6')" />
                        <?php
                        if ($rst[gui_cedula] != '9999999999' && $rst[gui_cedula] != '9999999999999' && $rst[gui_estado_aut] == 'RECIBIDA AUTORIZADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/mail.png" title="Envio de Correo" onclick="envia_mail('<?php echo $rst[gui_id] ?>')">          
                            <?php
                        }
                        ?>

                        <img src="../img/orden.png" width="12px"  class="auxBtn" onclick="auxWindow(3, '<?php echo $rst[gui_id] ?>', 0)">
                    </td>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

