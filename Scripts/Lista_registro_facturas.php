<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_registro_facturas.php';
$Docs = new Clase_registro_facturas();
if (isset($_GET[txt], $_GET[desde], $_GET[hasta])) {
    $txt = strtoupper($_GET[txt]);
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    if (!empty($_GET[txt])) {
        $texto = "and (d.reg_num_documento like '%$txt%' or d.reg_tpcliente like '%$txt%' or d. reg_concepto like '%$txt%' or d.reg_num_registro like '%$txt%' or c.cli_raz_social like '%$txt%')";
    } else {
        $texto = "and reg_femision between '$desde' and '$hasta'";
    }
    $cns = $Docs->lista_buscador_reg_fac($texto);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Registro de Facturas</title>
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
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, std, vst) {

                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";

                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_registro_facturas.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_registro_facturas.php?id=' + id + '&std=' + std + '&vst=' + vst;
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_registro_facturas14.php?id=' + id + '&std=' + std + '&vst=' + vst;
                        look_menu();
                        break;
                }
            }

            function del(id, nom)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_reg_docs.php", {op: 1, id: id, nom: nom}, function (dt) {
                        if (dt == 0) {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php';
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

            function cambiar_estado(e, id, std) {
                if (std != 3) {
                    $('#tbl_estado').show();
                    $('#mod_id').val(id);
                    $('#img_save').hide();
                    $("#estado").attr('checked', false);
                    tbl_estado.style.left = e.clientX;
                    tbl_estado.style.top = e.clientY;

                } else {
                    alert('Este Registro de Factura \n Ya se encuentra Anulado');
                }
            }

            function save_estado() {
                fec1 = $('#desde').val();
                fec2 = $('#hasta').val();
                var r = confirm("Esta Seguro de Cambiar de Estado a este Registro?");
                if (r == true) {
                    $.post("actions_reg_docs.php", {op: 5, md_id: $('#mod_id').val(), estado: $('input:checkbox[name=estado]:checked').val()},
                            function (dt) {
                                if (dt == 0) {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                                } else if (dt == 1) {
                                    alert('No se puede anular este documento \n Existe una Nota de Credito con este documento');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                                } else if (dt == 2) {
                                    alert('Una de las cuentas del Registro de Facturas esta inactiva');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                                } else if (dt == 3) {
                                    alert('No se puede anular el documento \nel producto no tiene suficiente inventario');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                                } else if (dt == 4) {
                                    alert('No se puede anular este documento \n Existe una Nota de Debito con este documento');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
                                } else if (dt == 5) {
                                    alert('No se puede anular este documento \n Existe una Retencion con este documento');
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_facturas.php?txt=' + '' + '&desde=' + fec1 + '&hasta=' + fec2;
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
            
            function exportar_excel() {
                $("#datatodisplay").val("");
                $("#tbl2 tbody").html("");
                $("#tbl2 tfoot").html("");
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }
        </script> 
        <style>
            #mn180{
                background:black;
                color:white;
                border: solid 1px white;
            }
            td.pendiente{
                background-color:#FF8080;         
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
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <thead>
                <tr><th colspan="15"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></th></tr>
                <tr><th colspan="15" align="center"><?PHP echo 'REGISTRO DE FACTURAS' ?></th></tr>
                <tr>
                    <td colspan="15"><?php echo 'Desde: ' . $desde . ' Hasta: ' . $hasta ?></td>
                </tr>
            </thead>
        </table>
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
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
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
                    <form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="../Includes/export.php?tipo=13" onsubmit="return exportar_excel()"  >
                        <input style="color: #FFFFEE;" type="submit" value="EXCEL" class="auxBtn" />
                        <input type="hidden" id="datatodisplay" name="datatodisplay">
                    </form>
                </center>               
                <center class="cont_title" >REGISTRO DE FACTURAS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" value="<?php echo $txt ?>" />
                        DESDE:<input type="text" size="15" name="desde" id="desde" value="<?php echo $desde ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="text" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" maxlength="10"/>
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>No Registro</th>
            <th>Tipo Documento</th>
            <th>No Documento</th> 
            <th>No Ingreso</th>
            <th>Fecha Emision</th>
            <th>Proveedor</th>
            <th>Concepto</th>
            <th>Subt14</th>
            <th>Subt0</th>
            <th>Iva</th>
            <th>Valor Total</th>
            <th>Retencion</th>
            <th>Estado</th>
            <th style="width: 100px">Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if ($rst[reg_estado] == 1) {
                    $g_tot_sub12+=round($rst[reg_sbt12], 2);
                    $g_tot_sub0+=(round($rst[reg_sbt0], 2) + round($rst[reg_sbt_noiva], 2) + round($rst[reg_sbt_excento], 2));
                    $g_tot_iva+=round($rst[reg_iva12], 2);
                    $g_total+=round($rst[reg_total], 2);
                }
                $tip_doc = pg_fetch_array($Docs->lista_un_tipo_documentos($rst[reg_tipo_documento]));
                switch ($rst[reg_estado]) {
                    case 1:$estado = 'Registrado';
                        break;
                    case 2:$estado = 'Pendiente';
                        $class_p = 'pendiente';
                        break;
                    case 3:$estado = 'Anulado';
                        break;
                    case 4:$estado = 'Registrado/Modificado';
                        break;
                }


                $rst_cli = pg_fetch_array($Docs->lista_cliente_ruc($rst[reg_ruc_cliente]));
                $rst_ret = pg_fetch_array($Docs->lista_retencion($rst[reg_id]));
                $rst_nc = pg_fetch_array($Docs->lista_una_nota_cred($rst[reg_id]));
                if (empty($rst[reg_num_ingreso]) && $rst[reg_estado] != 3) {
                    $rst[reg_estado] = 4;
                }
                if (!empty($rst_ret[reg_id])) {
                    $ret = 'SI';
                    $rst[reg_estado] = 5;
                } else {
                    $ret = 'NO';
                    if (!empty($rst_nc[reg_id])) {
                        $rst[reg_estado] = 6;
                    }
                }
                $style='mso-number-format:"@"';
                echo "<tr>";
                if ($estado == 'Pendiente') {
                    echo"<td class='$class_p' >$n</td>
                        <td style='$style' class='$class_p'>$rst[reg_num_registro]</td>
                        <td class='$class_p'>$tip_doc[tdc_descripcion]</td>
                        <td class='$class_p'>$rst[reg_num_documento]</td>
                        <td class='$class_p'>$rst[reg_num_ingreso]</td>
                        <td class='$class_p'>$rst[reg_femision]</td>
                        <td class='$class_p'>$rst_cli[cli_raz_social]</td>
                        <td class='$class_p'>$rst[reg_concepto]</td>
                        <td class='$class_p' align='right'>" . number_format($rst[reg_sbt12], $dec) . "</td>
                        <td class='$class_p' align='right'>" . number_format($rst[reg_sbt0], $dec) . "</td>
                        <td class='$class_p' align='right'>" . number_format($rst[reg_iva12], $dec) . "</td>
                        <td class='$class_p' align='right'>" . number_format($rst[reg_total], $dec) . "</td>
                        <td class='$class_p' align='center'>" . $ret . "</td>
                        <td class='$class_p' id='estados' align='center' title='Cambiar Estado' onclick='cambiar_estado(event, $rst[reg_id], $rst[reg_estado])' >$estado</td>";
                } else {
                    $ev = "onclick='auxWindow(1,$rst[reg_id], 3, 0)'";
                                       
                    echo"<td>$n</td>
                        <td style='$style' $ev>$rst[reg_num_registro]</td>
                        <td $ev>$tip_doc[tdc_descripcion]</td>
                        <td $ev>$rst[reg_num_documento]</td>
                        <td $ev>$rst[reg_num_ingreso]</td>
                        <td $ev>$rst[reg_femision]</td>
                        <td $ev>$rst_cli[cli_raz_social]</td>
                        <td $ev>$rst[reg_concepto]</td>
                        <td $ev align='right'>" . number_format($rst[reg_sbt12], $dec) . "</td>
                        <td $ev align='right'>" . number_format($rst[reg_sbt0], $dec) . "</td>
                        <td $ev align='right'>" . number_format($rst[reg_iva12], $dec) . "</td>
                        <td $ev align='right'>" . number_format($rst[reg_total], $dec) . "</td>
                        <td $ev align='center'>" . $ret . "</td>
                        <td align='center' title='Cambiar Estado' onclick='cambiar_estado(event, $rst[reg_id], $rst[reg_estado])' >$estado</td>";
                    
                }


                echo"<td align='center'>";
                if (($estado == 'Registrado/Modificado' || $estado == 'Registrado') && !empty($rst[con_asiento])) {
                    ?>
                <img src='../img/orden.png' width='20px' class='auxBtn' title="Comprobante Diario" onclick="asiento_registro('<?php echo $rst['con_asiento'] ?>', '0')" /> 
                <?php
            } else if ($estado == 'Anulado' && !empty($rst[reg_asi_anulacion])) {
                ?>
                <img src='../img/orden.png' width='20px' class='auxBtn' title="Anulacion Comprobante Diario" onclick="asiento_registro('<?php echo $rst['reg_asi_anulacion'] ?>', '1')" /> 
                <?php
            }
            if ($Prt->edition == 0 && $rst[reg_estado] != 3) {
                echo"<img src='../img/upd12.jpg' width='20px' class='auxBtn' title='Editar' onclick='auxWindow(1, $rst[reg_id], $rst[reg_estado], 1)'>";
                echo"<img src='../img/upd14.jpg' width='20px' class='auxBtn' title='Editar' onclick='auxWindow(2, $rst[reg_id], $rst[reg_estado], 1)'>";
            }
            echo"</td>
            </tr> ";
        }
        ?>
    </tbody>
    <tr style="font-weight:bolder">
        <td colspan="8" align="right">Total</td>
        <td align="right" style="font-size:14px;"><?php echo number_format($g_tot_sub12, 2) ?></td>
        <td align="right" style="font-size:14px;"><?php echo number_format($g_tot_sub0, 2) ?></td>
        <td align="right" style="font-size:14px;"><?php echo number_format($g_tot_iva, 2) ?></td>
        <td align="right" style="font-size:14px;"><?php echo number_format($g_total, 2) ?></td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td bgcolor="#D8D8D8" colspan="15" rowspan="5"><br><br><br></td>
    </tr>
</table>            
</body>    
</html>




