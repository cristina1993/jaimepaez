<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_reg_nota_credito.php'; //cambiar clsClase_productos
$Reg_nota_credito = new Clase_reg_nota_credito();
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $fecha1 = $_GET[fecha1];
    $fecha2 = $_GET[fecha2];

    if (!empty($txt)) {
        $texto = "where (rnc_identificacion like '%$txt%' or rnc_nombre like '%$txt%' or rnc_numero like '%$txt%' or rnc_num_comp_modifica like '%$txt%')";
    } else {
        $texto = "where rnc_fecha_emision between '$fecha1' and '$fecha2'";
    }
    $cns = $Reg_nota_credito->lista_buscador_nota_credito($texto);
} else {
    $txt = '';
    $fecha1 = date('Y-m-d');
    $fecha2 = date('Y-m-d');
    $texto = "where rnc_fecha_emision between '$fecha1' and '$fecha2'";
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
                        frm.src = '../Scripts/Form_registro_nota_credito.php?txt=' + '' + '&fecha1=' + d + '&fecha2=' + h;
                        ;//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_registro_nota_credito.php?x=' + x + '&id=' + id + '&txt=' + '' + '&fecha1=' + d + '&fecha2=' + h;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2://Reporte
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_i_pdf_nota_credito.php?id=' + id + '&x=' + x;
//                        look_menu();
                        break;
                }
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function del(id, num, comp)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_reg_nota_credito.php", {act: 5, id: id, data: num, data1: comp}, function (dt) {
                        if (dt == 0)
                        {
                            window.history.go();
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
                    tbl_estado.style.left = e.clientX;
                    tbl_estado.style.top = e.clientY;

                } else {
                    alert('Este Registro de Nota de Credito\n Ya se encuentra Anulado');
                }
            }

            function save_estado() {
                fec1 = $('#fecha1').val();
                fec2 = $('#fecha2').val();
                var r = confirm("Esta Seguro de Cambiar de Estado a este Registro?");
                if (r == true) {
                    $.post("actions_reg_nota_credito.php", {act: 6, md_id: $('#mod_id').val(), estado: $('input:checkbox[name=estado]:checked').val()},
                            function (dt) {
                                if (dt == 0) {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_registro_nota_credito.php?txt=' + '' + '&fecha1=' + fec1 + '&fecha2=' + fec2;
                                } else if (dt == 1) {
                                    alert('Una de las cuentas de la Anulacion del Registro de Nota de Credito esta inactiva');
                                    loading('hidden');
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


        </script> 
        <style>
            #mn264{
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
                <center class="cont_title" ><?php echo "REGISTRO DE NOTAS DE CREDITO" ?></center>

                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="25" id="txt" value="<?php echo $txt ?>"  />
                        DESDE:<input type="text" size="15" name="fecha1" id="fecha1" value="<?php echo $fecha1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="15" name="fecha2" id="fecha2" value="<?php echo $fecha2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>No Registro</th>
            <th>Fecha de Emision</th>
            <th>Nota Credito No.</th>
            <th>Tipo</th>
            <th>Factura No.</th>
            <th>Identificacion</th>
            <th>Cliente</th>
            <th>Total Nota Cred. $</th>
            <th>Estado</th>
            <th width='100px'>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            $grup = '';
            while ($rst = pg_fetch_array($cns)) {
                switch ($rst[rnc_estado]) {
                    case 0:$estado = 'Pendiente de cobro';
                        break;
                    case 1:$estado = 'Registrado';
                        break;
                    case 3:$estado = 'Anulado';
                        break;
                    case 4:$estado = 'Pendiente de cobro S / A';
                        break;
                    case 5:$estado = 'Registrado S / A';
                        break;
                }
                $n++;
                $ev = "onclick='auxWindow(1,$rst[rnc_id],1)'";
                echo "<tr>
                    <td $ev>$n </td>
                    <td $ev align='center'>$rst[rnc_num_registro]</td>
                    <td $ev align='center'>$rst[rnc_fecha_emision]</td>
                    <td $ev align='center'>$rst[rnc_numero]</td>
                    <td $ev>FACTURA</td>
                    <td $ev>$rst[rnc_num_comp_modifica]</td>
                    <td $ev>$rst[rnc_identificacion]</td>
                    <td $ev>$rst[rnc_nombre]</td>
                    <td $ev align='right' style='font-size:14px;font-weight:bolder'>" . number_format($rst[rnc_total_valor], $dec) . "</td>
                    <td align='left' title='Cambiar Estado' onclick='cambiar_estado(event, $rst[rnc_id], $rst[rnc_estado])' >$estado</td>
                    <td>";
                if ($Prt->edition == 0 && $rst[rnc_estado] != 3) {
                    echo "<img src='../img/upd.png' width='20px' class='auxBtn' title='Editar' onclick='auxWindow(1, $rst[rnc_id],2)'>";
                }
                echo "</td>
               
                </tr>";

                $n_total+=$rst[rnc_total_valor];
            }

            echo "</tbody>
                <tr style='font-weight:bolder'>
                    <td colspan='8' align='right'>Total</td>
                    <td align='right' style='font-size:14px;'>" . number_format($n_total, $dec) . "</td>
                    <td colspan='6'></td>
                </tr>";
            ?>
            <tr>
                <td bgcolor="#D8D8D8" colspan="15" rowspan="5"><br><br><br></td>
            </tr>
    </table>            
</body>   
</html>

