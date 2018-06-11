<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cheques.php';
$Docs = new Clase_cheques();
$rst[chq_fecha] = date('Y-m-d');
if (isset($_GET[fecha1], $_GET[fecha2])) {
    $txt = trim(strtoupper($_GET[txt]));
    $est = $_GET[estado];
    $fec1 = $_GET[fecha1];
    $fec2 = $_GET[fecha2];
    if (!empty($txt)) {
        $texto = "and (c.cli_raz_social like'%$txt%' or h.chq_nombre like'%$txt%' or h.chq_banco like'%$txt%' or h.chq_numero like'%$txt%')";
    } else if ($est != '') {
        $texto = "and h.chq_estado='$est'";
    } else {
        $texto = "and h.chq_recepcion between '$fec1' and '$fec2'";
    }
    $cns = $Docs->lista_buscador_cheques($texto);
} else {
    $fec1 = date('Y-m-d');
    $fec2 = date('Y-m-d');
    $texto = "and h.chq_recepcion between '$fec1' and '$fec2'";
    $cns = $Docs->lista_buscador_cheques($texto);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ingreso Facturas</title>
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
                Calendar.setup({inputField: "chq_fecha", ifFormat: "%Y-%m-%d", button: "im-chq_fecha"});
                posicion_accion();
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, cl) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_cheques.php?txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_cheques.php?id=' + id + '&txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                    case 2://Pagos
                        parent.document.getElementById('bottomFrame').src = '../Scripts/Form_control_cobros.php?cli=' + cl + '&ch=' + id + '&txt=' + $('#txt').val() + '&estado=' + $('#estado').val() + '&fecha1=' + $('#fecha1').val() + '&fecha2=' + $('#fecha2').val();
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
            }


            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

            function acciones(op) {
                if (op == 1) {
                    $('#dep').show();
                    $('#reb').hide();
                } else {
                    $('#dep').hide();
                    $('#reb').show();
                }
                $('#mot').show();

            }

            function cargar_datos(id, fa, e) {
                tbl_aux.style.top = e.clientY;
                tbl_aux.style.left = (e.clientX - 600);
                tbl_aux.style.display = 'block';
                cheque.value = fa;
                chq_id.value = id;
            }


            function cambiar() {
                id = chq_id.value;
                motivo = motivo.value;
                fecha = chq_fecha.value;
                if ($('#depositar').attr('checked') == true) {
                    est = $('#depositar').val();
                } else {
                    est = $('#rebotar').val();
                }

                $.ajax({
                    beforeSend: function () {
                        if ($("#depositar").attr('checked') == false && $("#rebotar").attr('checked') == false) {
                            alert('Seleccione Depositar o Rebotar');
                            return false;
                        } else if ($("#motivo").val().length == 0) {
                            $("#motivo").css({borderColor: "red"});
                            $("#motivo").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_cheques.php',
                    data: {op: 3, id: id, data: motivo, s: est, fec: fecha}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        tbl_aux.style.display = 'none';
                        if (dt == 0) {
                            window.location = 'Lista_cheques.php';
                        } else {
                            alert(dt);
                        }

                    }
                })
            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_cheques.php", {op: 1, id: id}, function (dt) {
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
            
            function asiento_registro(id) {
                parent.document.getElementById('contenedor2').rows = "*,80%";
                parent.document.getElementById('bottomFrame').src = '../Scripts/frm_pdf_contol_cobros.php?id=' + id;
            }

        </script> 
        <style>
            #mn180{
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
        <div id="cargando">Por Favor Espere...</div>
        <table id="tbl_aux" style="border: solid 2px black">
            <tr>
                <td colspan="2" style="font-weight:bolder "><img src="../img/b_delete.png" style="float:right;cursor: pointer" onclick="tbl_aux.style.display = 'none', motivo.value = ''"  /></td>
            </tr>
            <tr>
                <td># Cheque </td>
                <td><input size="30" readonly id="cheque"/>
                    <input size="10" hidden id="chq_id"/></td>
            </tr>
            <tr>
                <td>Depositar <input type="radio" name="seleccion" id="depositar" value="1" onchange="acciones(1)"/> </td>
                <td>Devuelto <input type="radio" name="seleccion" id="rebotar" value="2" onchange="acciones(2)"/> </td>
            </tr>
            <tr>
                <td>Fecha:</td>
                <td>
                    <input type="text" size="10" id="chq_fecha" readonly value="<?php echo $rst[chq_fecha] ?>" />
                    <img src="../img/calendar.png" id="im-chq_fecha" />
                </td>
            </tr>
            <tr >
                <td id="dep" hidden>Deposito</td>
                <td id="reb" hidden>Motivo</td>
                <td id="mot" hidden><input size="30" id="motivo"/></td>
            </tr>
            <tr>
                <td colspan="2"><img style="float:left" src="../img/save.png" class="auxBtn" onclick="cambiar()" /></td>
            </tr>
        </table>
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
                </center>               
                <center class="cont_title" ><?php echo "CONTROL DE COBROS" ?></center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        ESTADO:
                        <select id="estado" name="estado">
                            <option value="">SELECCIONE</option>
                            <option value="0" >CAJA</option>
                            <option value="1" >DEPOSITADO</option>
                            <option value="2" >REBOTADO</option>
                        </select>
                        DESDE:<input type="text" size="10" name="fecha1" id="fecha1" value="<?php echo $fec1 ?>" />
                        <img src="../img/calendar.png" id="im-campo1"/>
                        HASTA:<input type="text" size="10" name="fecha2" id="fecha2" value="<?php echo $fec2 ?>" />
                        <img src="../img/calendar.png" id="im-campo2"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>                                                               
                    </form>  
                </center>
            </caption>

            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>TIPO DOCUMENTO</th>
            <th>CLIENTE</th>
            <th>NOMBRE DEL CHEQUE</th>                                
            <th>BANCO</th>
            <th># CHEQUE</th>
            <th>FECHA RECEPCION</th>
            <th>FECHA CHEQUE</th>
            <th>VALOR</th>
            <th>SALDO</th>
            <th>ESTADO</th>
            <th>ACCION</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[chq_tipo_doc]) {
                    case '1':$tipo = 'CHEQUE A LA FECHA';
                        break;
                    case '2':$tipo = 'CHEQUE POSTFECHADO';
                        break;
                    case '3':$tipo = 'NOTA CREDITO';
                        break;
                    case '4':$tipo = 'NOTA DEBITO';
                        break;
                    case '5':$tipo = 'RETENCION';
                        break;
                    case '6':$tipo = 'TARJETA DE CREDITO';
                        break;
                    case '7':$tipo = 'TARJETA DE DEBITO';
                        break;
                    case '8':$tipo = 'CERTIFICADOS';
                        break;
                    case '9':$tipo = 'BONOS';
                        break;
                    case '10':$tipo = 'EFECTIVO';
                        break;
                }
                switch ($rst[chq_estado]) {
                    case '0':$estado = 'CAJA';
                        break;
                    case '1':$estado = 'DEPOSITADO';
                        break;
                    case '2':$estado = 'DEVUELTO';
                        break;
                    case '3':$estado = 'ANULADO';
                        break;
                }
                $saldo = $rst[chq_monto] - $rst[chq_cobro];
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $tipo ?></td>
                    <td><?php echo $rst[cli_raz_social] ?></td>
                    <td><?php echo $rst[chq_nombre] ?></td>
                    <td><?php echo $rst[chq_banco] ?></td>
                    <td><?php echo $rst[chq_numero] ?></td>
                    <td><?php echo $rst[chq_recepcion] ?></td>
                    <td><?php echo $rst[chq_fecha] ?></td>
                    <td align="right"><?php echo number_format($rst[chq_monto], 4) ?></td>
                    <td align="right"><?php echo number_format($saldo, 4) ?></td>
                    <?php
                    if ($estado == 'DEVUELTO' || $estado == 'ANULADO' || $saldo != $rst[chq_monto] || ($rst[chq_tipo_doc] == 3 || $rst[chq_tipo_doc] == 4 || $rst[chq_tipo_doc] == 5)) {
                        ?>
                        <td align="center" style="color:darkred;font-weight:bolder"><?php echo $estado ?></td>
                        <?php
                    } else {
                        ?>
                        <td align="center" style="color:darkred;font-weight:bolder" onclick="cargar_datos('<?PHP echo $rst[chq_id] ?>', '<?php echo $rst[chq_numero] ?>', event)"><?PHP echo $estado ?></td>
                        <?php
                    }
                    ?>
                    <td align="center">
                        <?php
                        $rst_cta = pg_fetch_array($Docs->lista_cuentasxcobrar_chq($rst[chq_id]));
                        if (!empty($rst_cta)) {
                            ?>
                            <img src='../img/orden.png' class="auxBtn" width="20px" onclick='asiento_registro(<?PHP echo $rst[chq_id] ?>)' /> 
                            <?php
                        }
                        if (($estado != 'REBOTADO' && $estado != 'ANULADO') && ($rst[chq_tipo_doc] != 3 && $rst[chq_tipo_doc] != 4 && $rst[chq_tipo_doc] != 5) && $saldo == $rst[chq_monto]) {
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/b_delete.png" width="20px"  class="auxBtn" onclick="del(<?php echo $rst[chq_id] ?>)">
                                <?php
                            }
                        }
                        if (($estado == 'CAJA' || $estado == 'DEPOSITADO') && ($rst[chq_tipo_doc] != 3 && $rst[chq_tipo_doc] != 4 && $rst[chq_tipo_doc] != 5) && $saldo == $rst[chq_monto]) {
                            if ($Prt->edition == 0) {
                                ?>
                                <img src="../img/upd.png"  class="auxBtn" width="20px" onclick="auxWindow(1, '<?php echo $rst[chq_id] ?>')">
                                <?php
                            }
                        }
                        if (($estado == 'CAJA' || $estado == 'DEPOSITADO') && ($rst[chq_tipo_doc] != 3 && $rst[chq_tipo_doc] != 4 && $rst[chq_tipo_doc] != 5) && $saldo != 0) {
                            if ($Prt->edition == 0) {
                                ?>
                                <img src="../img/table.png"  title="Pagos" class="auxBtn" width="20px" onclick="auxWindow(2, '<?php echo $rst[chq_id] ?>', '<?php echo $rst[cli_id] ?>')">
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>
<script>
    var e = '<?php echo $est ?>';
    $('#estado').val(e);
</script>
