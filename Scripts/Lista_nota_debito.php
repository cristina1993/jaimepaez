<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nota_debito.php'; //cambiar clsClase_productos
include_once '../Clases/clsSetting.php';
$Clase_nota_debito = new Clase_nota_debito();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $text = "and n.emi_id=$emisor and (n.ndb_identificacion like '%$txt%' or n.ndb_nombre like '%$txt%' or n.ndb_numero like '%$txt%' or n.ndb_num_comp_modifica like '%$txt%')";
    } else {
        $text = "and n.emi_id=$emisor and n.ndb_fecha_emision between '$fec1' and '$fec2'";
    }
    $cns = $Clase_nota_debito->lista_buscador_nota_debito($text);
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
            usuid = '<?php echo $_SESSION[usuid] ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "fecha1", ifFormat: "%Y-%m-%d", button: "im-campo1"});
                Calendar.setup({inputField: "fecha2", ifFormat: "%Y-%m-%d", button: "im-campo2"});
                $('#load_automaticos').load('../Includes/envio_sri_nota_debito.php');
                $('#load_automaticos').load('../Includes/envio_mail.php');
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, e)
            {
                d = $('#fecha1').val();
                h = $('#fecha2').val();
                t = $('#txt').val();
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_nota_debito.php?txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_nota_debito.php?id=' + id + '&txt=' + t + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://Reporte
                        frm.src = '../Scripts/Form_i_pdf_nota_debito.php?id=' + id + '&x=' + x;
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                     case 4://xml
                        loading('visible');
                        window.location = '../Reports/descargar_xml.php?id=' + id + '&clave=' + x + '&tp=' + e;
                        loading('hidden');
                        break;
   
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function del(id, num) {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_nota_debito.php", {op: 3, id: id, data: num}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php?txt=' + '<?php echo $txt ?>' + '&fecha1=' + '<?php echo $fecha1 ?>' + '&fecha2=' + '<?php echo $fecha2 ?>';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }
            function cargar_datos(id, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                factura.value = fa;
                com_id.value = id;
            }

            function anular() {
                fec1 = $('#fecha1').val();
                fec2 = $('#fecha1').val();
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
                        url: 'actions_nota_debito.php',
                        data: {op: 4, id: id}, //op sera de acuerdo a la acion que le toque
                        success: function (dt) {
                            tbl_aux.style.display = 'none';
                            if (dt == 0) {
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php?txt=' + '' + '&fecha1=' + fec1 + '&fecha2=' + fec2;
                            } else {
                                alert(dt);
                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nota_debito.php?txt=' + '' + '&fecha1=' + fec1 + '&fecha2=' + fec2;
                            }
                        }
                    });
                } else {
                    alert('Ud no esta autorizado para realizar este proceso');
                }
            }
            
            function envia_mail(id) {
                $.ajax({
                    beforeSend: function () {
                        loading('visible');
                    },
                    type: 'GET',
                    url: '../Includes/envio_mail_nota_debito.php?id=' + id,
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
                            alert('Nota de Debito Enviada Correctamente');
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
        </script> 
        <style>
            #mn267{
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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <div id="load_automaticos" hidden ></div>        
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2" style="font-weight:bolder ">Anulación de Documento<img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', cod_anular.value = ''"  /></td>
            </tr>
            <tr>
                <td>Nota Debito # </td>
                <td><input size="30" readonly id="factura"/>
                    <input size="10" hidden id="com_id"/></td>
            </tr>
            <tr>
                <td>Codigo de autorizacion</td>
                <td><input size="30" id="cod_anular"/></td>
            </tr>
            <tr>
                <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="anular()" /></td>
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
                <center class="cont_title" ><?php echo "NOTAS DE DEBITO" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>" />
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
            <th>Nota debito No.</th>            
            <th>Tipo</th>
            <th>Factura No.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Nota Deb. $</th>
            <th>Total Factura $</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_fac = pg_fetch_array($Clase_nota_debito->lista_una_factura_id($rst[fac_id]));
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td align="center"><?php echo $rst[ndb_fecha_emision] ?></td>
                    <td align="center"><?php echo $rst[ndb_numero] ?></td>
                    <td>FACTURA</td>
                    <td><?php echo $rst[ndb_num_comp_modifica] ?></td>
                    <td><?php echo $rst[ndb_identificacion] ?></td>
                    <td><?php echo $rst[ndb_nombre] ?></td>
                    <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst[ndb_total_valor], $dec) ?></td>
                    <td align="right" style="font-size:14px;font-weight:bolder"><?php echo number_format($rst_fac[fac_total_valor], $dec) ?></td>
                    <td><?PHP echo $rst[vnd_nombre] ?></td>
                    <?php
                    if ($rst[ndb_estado_aut] == 'ANULADO') {
                        ?>
                        <td style="color:darkred;font-weight:bolder "><?PHP echo substr($rst[ndb_estado_aut], 0, 20) ?></td>
                        <?php
                    } else {
                        ?>
                        <td style="<?php echo $style ?>" title="<?php echo $rst[ndb_estado_aut] ?>" ondblclick="cargar_datos('<?php echo $rst[ndb_id] ?>', '<?php echo $rst[ndb_numero] ?>', event)"><?PHP echo $rst[ndb_estado_aut] ?></td>
                        <?php
                    }
                    ?>
                    <td align="center">
                        <?php
                        if ($_SESSION[usuid] == 1 && $rst[ndb_estado_aut] != 'ANULADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/upd.png" onclick="auxWindow(1, '<?php echo $rst[ndb_id] ?>')" />                    
                            <?PHP
                        }
                        ?>
                        <img class="auxBtn" width="12px" src="../img/xml.png" onclick="auxWindow(4, '<?php echo $rst[ndb_id] ?>', '<?php echo $rst[ndb_clave_acceso] ?>', '5')" />
                        <?php
                        if ($rst[ndb_cedula] != '9999999999' && $rst[ndb_cedula] != '9999999999999' && $rst[ndb_estado_aut] == 'RECIBIDA AUTORIZADO') {
                            ?>
                            <img class="auxBtn" width="12px" src="../img/mail.png" title="Envio de Correo" onclick="envia_mail('<?php echo $rst[ndb_id] ?>')">          
                            <?php
                        }
                        ?>

                        <img src="../img/orden.png"  class="auxBtn" width="12px" onclick="auxWindow(2, '<?php echo $rst[ndb_id] ?>')"> 
                    </td> 
                </tr>  
                <?PHP
                $f_total+=$rst_fac[fac_total_valor];
                $n_total+=$rst[ndb_total_valor];
            }
            ?>
        </tbody>
        <tr style="font-weight:bolder">
            <td colspan="7" align="right">Total</td>
            <td align="right" style="font-size:14px;"><?php echo number_format($n_total, $dec) ?></td>
            <td align="right" style="font-size:14px;"><?php echo number_format($f_total, $dec) ?></td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td bgcolor="#D8D8D8" colspan="15" rowspan="5"><br><br><br><br><br><br></td>
        </tr>
    </table>            
</body>   
</html>

