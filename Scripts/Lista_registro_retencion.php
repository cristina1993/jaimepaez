<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_retencion.php'; //cambiar clsClase_productos
$Reg_retencion = new Clase_reg_retencion();
if (isset($_GET[txt], $_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fec1 = trim($_GET[fecha1]);
    $fec2 = trim($_GET[fecha2]);

    if (!empty($txt)) {
        $text = "where (rgr_num_registro like '%$txt%' or rgr_nombre like '%$txt%' or rgr_identificacion like '%$txt%' or rgr_numero like '%$txt%' or rgr_num_comp_retiene like '%$txt%')";
    } else {
        $text = "where rgr_fec_registro between '$fec1' and '$fec2' ";
    }
    $cns = $Reg_retencion->lista_buscador_retencion($text);
} else {
    $txt = '';
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $text = "where rgr_fec_registro between '$fec1' and '$fec2'";
    $cns = $Reg_retencion->lista_buscador_retencion($text);
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

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x, d)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_registro_retencion.php?x=1'//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Ver
                        frm.src = '../Scripts/Form_registro_retencion.php?id=' + id + '&x=' + d;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_registro_retencion.php?id=' + id + '&est=' + x + '&x=' + d;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
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
                    $.post("actions_reg_retencion.php", {op: 1, id: id, data: num}, function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function cambiar_estado(e, id, std) {
                if (std != 3) {
                    $('#tbl_estado').show();
                    $('#mod_id').val(id);
                    $('#img_save').hide();
                    $("#estado").attr('checked', false);
                    $("#estado").val(std);
                    tbl_estado.style.left = e.clientX;
                    tbl_estado.style.top = e.clientY;

                } else {
                    alert('Este Registro de Retencion\n Ya se encuentra Anulado');
                }
            }

            function save_estado() {
                fec1 = $('#fecha1').val();
                fec2 = $('#fecha2').val();
                var r = confirm("Esta Seguro de Cambiar de Estado a este Registro?");
                if (r == true) {
                    $.post("actions_reg_retencion.php", {op: 7, md_id: $('#mod_id').val(), estado: $('input:checkbox[name=estado]:checked').val()},
                            function (dt) {
                                if (dt == 0) {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_retencion.php?txt=' + '' + '&fecha1=' + fec1 + '&fecha2=' + fec2;
                                } else if (dt == 1) {
                                    alert('No se puede anular este registro');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_retencion.php?txt=' + '' + '&fecha1=' + fec1 + '&fecha2=' + fec2;
                                } else {
                                    alert(dt);
                                }
                            });
                } else {
                    return false;
                }

            }

            function cerrar_aux() {
                $('#tbl_estado').hide();
            }

            function mostrar() {
                if (estado.checked == true) {
                    $('#img_save').show();
                } else if (estado.checked == false) {
                    $('#img_save').hide();
                }
            }
            function asiento_registro(id, x) {
                parent.document.getElementById('contenedor2').rows = "*,80%";
                parent.document.getElementById('bottomFrame').src = '../Scripts/frm_pdf_asientos.php?id=' + id + '&asi=1' + '&x=' + x;
            }

        </script> 
        <style>
            #mn68,
            #mn116,
            #mn121,
            #mn126,
            #mn131,
            #mn136,
            #mn141,
            #mn146,
            #mn151,
            #mn156{
                background:black;
                color:white;
                border: solid 1px white;
            }

            #tbl_estado {
                font-size:12px; 
                width: 150px;
                position:fixed;
                background:white;
                border: solid 1px;
            }
            #tbl_estado tr:hover{
                background:gainsboro;
                cursor:pointer; 
            }
            td.pendiente{
                background-color:#FF8080;         
            }
        </style>
    </head>
    <body>
        <table id="tbl_estado" cellpadding='5' hidden>
            <tr>
                <td colspan="2">
                    Cambiar Estado:
                    <input type="hidden" size="5" id="mod_id" />
                    <font class="cerrar" style="color:white;text-align:center "  onclick="cerrar_aux()" title="Salir del Formulario">&#X00d7;</font>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" name="estado" id="estado" value="3"  onclick="mostrar()"/></td>
                <td>Anular Registro</td>
            </tr>
            <tr><td colspan="2"><img src="../img/save.png" id="img_save" onclick="save_estado()" /></td></tr>
        </table>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" ><?php echo "REGISTRO DE RETENCIONES" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
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
            <th>No</th>
            <th>Registro No</th>
            <th>Fecha de Emision</th>
            <th>Retencion No.</th>
            <th>Tipo</th>
            <th>Documento Retenido No.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Valor $</th>
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
                $tot_ret = $rst[rgr_total_valor];
//                                        <img src='../img/orden.png' width='12px'  class='auxBtn' onclick='auxWindow(2, $rst[rgr_id])'>
                switch ($rst[rgr_denominacion_comp]) {
                    case '1':
                        $tip = 'Factura';
                        break;
                    case '2':
                        $tip = 'Otros Documentos';
                        break;
                    case '4':$tipo = 'Nota de Credito';
                        break;
                    case '5':$tipo = 'Nota de Debito';
                        break;
                    case '8':$tipo = 'Notas de Venta RISE';
                        break;
                    case '9':$tipo = 'Liquidacion de Compra de bienes y prestacion de servicios';
                        break;
                    case '10':$tipo = 'Tiquetes emitidos por maquinas registradoras y boletos o entradas a espectaculos publicos';
                        break;
                    case '11':$tipo = 'Otros documentos autorizados';
                        break;
                }

                switch ($rst[rgr_estado]) {
                    case 1:$estado = 'Registrado';
                        $ev = "onclick='auxWindow(1,$rst[rgr_id])'";
                        $class_p = '';
                        break;
                    case 2:$estado = 'Pendiente';
                        $ev = "";
                        $class_p = 'pendiente';
                        break;
                    case 3:$estado = 'Anulado';
                        $ev = "onclick='auxWindow(1,$rst[rgr_id],0,2)'";
                        $class_p = '';
                        break;
                }
                $c = '"';

                echo "<tr>
                    <td class='$class_p' $ev>$n</td>
                    <td class='$class_p' $ev>$rst[rgr_num_registro]</td>
                    <td class='$class_p' $ev align='center'>$rst[rgr_fecha_emision]</td>
                    <td class='$class_p' $ev>$rst[rgr_numero]</td>
                    <td class='$class_p' $ev>$tip</td>
                    <td class='$class_p' $ev>$rst[rgr_num_comp_retiene]</td>
                    <td class='$class_p' $ev>$rst[rgr_identificacion]</td>
                    <td class='$class_p' $ev>$rst[rgr_nombre]</td>
                    <td class='$class_p' align='right' style='font-size:14px;font-weight:bolder'>" . number_format($tot_ret, 2) . "</td>
                    <td class='$class_p' align='left' title='Cambiar Estado' onclick='cambiar_estado(event, $rst[rgr_id], $rst[rgr_estado])' >$estado</td>
                    <td align='right'>";
                if ($rst[rgr_estado] != 2) {
                    echo "<img src='../img/orden.png'  width='20px' class='auxBtn' title='Comprobante Diario' onclick='asiento_registro($c$rst[con_asiento]$c, 2)' />";
                }
                if ($Prt->edition == 0 && $rst[rgr_estado] != 3) {
                    echo "<img src='../img/upd.png' width='20px' class='auxBtn' title='Editar' onclick='auxWindow(2, $rst[rgr_id], $rst[rgr_estado],1)'>";
                }
                echo "</td> 
                   </tr>";

                $r_total+=$tot_ret;
            }
            echo "</tbody>
                        <tr style='font-weight:bolder'>
                        <td colspan='8' align='right'>Total</td>
                        <td align='right' style='font-size:14px;'>" . number_format($r_total, 2) . "</td>
                        <td colspan='7'></td>
                        </tr>
                        ";
            ?>
            <tr>
                <td bgcolor="#D8D8D8" colspan="15" rowspan="5"><br><br><br></td>
            </tr>
    </table>       
</body>    
</html>


