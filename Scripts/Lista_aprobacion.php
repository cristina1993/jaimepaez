<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$tbl_set = 'erp_pedidos_set';
$tbl = substr($tbl_set, 0, -4);
$tbl_name = 'pedidos';
$tp = 'ped_tipo';
$Set = new Set();
if (isset($_GET[search])) {
    $ref = strtoupper(trim($_GET[txt]));
    $ped = strtoupper(trim($_GET[txt]));
    $from = $_GET[from];
    $until = $_GET[until];
    $sts = 0;
    $cns = $Set->list_aprobation_varios_registros($ref, $ped, $from, $until, $sts);
} else {
    $from = date('Y-m-d');
    $until = date('Y-m-d');
    $sts = 0;
    $cns = $Set->list_aprobation($sts);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
            });
            var tbl_name = '<?php echo $tbl_name ?>';

            function auxWindow(a, tipo, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 1:

                        frm.src = '../Scripts/Form_' + tbl_name + '.php?id=' + id + '&tipo=' + tipo + '&x=' + x;
                        break;
                }
            }


            function loadData(id, dr)
            {
                if (dr == 1)
                {
                    window.location = 'Lista_aprobacion.php';
                } else if (dr == 2) {
                    window.location = 'Lista_egreso_bodega_mp.php';
                } else {
                    window.location = 'Lista_' + tbl_name + '.php?tipo=' + id;
                }

            }
            function aprobar(act, id, ped)
            {

                if (act == 0)
                {
                    sms = confirm("Se Aprobara el pedido " + ped + " \n Desea Continuar? ");
                    sts = 1;
                } else if (act == 1) {

                    sms = confirm("No se aprobara el pedido " + ped + " \n Desea Continuar? ")
                    sts = 6;
                }

                if (sms == true)
                {
                    $.post("actions.php", {act: 11, id: id, sts: sts},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });

                }

            }
        </script>    
        <style>
            .sbmnu{
                position:static;
                background:white;
                color:black;
                font-weight:bolder;
                padding:5 15; 
                float:left; 
                border: solid 1px black;
                border-radius:5px 5px 5px 5px; 
                cursor:pointer; 
                margin-left:1px; 
                display:inline-block; 
            }
            .sbmnu:hover{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert('¡Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <font class="sbmnu" onclick="loadData(0, 1)" >Lista de Aprobacion</font>                                            
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >PEDIDOS EN LISTA DE APROBACION</center>
                <center class="cont_finder">
                    <form style="margin-left:0%;margin-top:5px;" action="" method="GET" id="frmSearch" name="frm1" >
                        <input type="hidden" size="10" value="<?PHP echo $tp_reg ?>" name="tp_reg" id="tp_reg" />
                        Desde:<input type="text" size="10" value="<?PHP echo $from ?>" name="from" id="from"  />
                        <img id="im_from" src='../img/calendar.png' />
                        Hasta:<input type="text" size="10" value="<?PHP echo $until ?>" name="until" id="until"  />
                        <img id="im_until" src='../img/calendar.png' />
                        Pedido:<input type="text" size="20" value="" name="txt" id="txt"  />                            
                        <a href="#" style="position:absolute " class="act_btn" title="Buscar" onclick="frmSearch.submit()" ><img src="../img/finder.png" /></a>
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="5"></th>
                    <th colspan="5">SOLICITADO</th>
                    <th colspan="2"></th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Fecha de Entrega</th>
                    <th>Referencia</th>
                    <th>1.5</th>
                    <th>2</th>
                    <th>2.5</th>
                    <th>3</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>                
            </thead>
            <tbody id="tbody">
                <?php
                $cn = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $cn++;


                    switch ($rst[ped_f]) {
                        case 0:$sts = "REGISTRADO";
                            $clr = "";
                            break;
                        case 1:$sts = "APROBADO";
                            $clr = "#FFE862";
                            break;
                        case 2:$sts = "PRODUCCION";
                            $clr = "#00cd66";
                            break;
                        case 3:$sts = "TERMINADO";
                            $clr = '#00b2ee';
                            break;
                        case 4:$sts = "ANULADO";
                            $clr = "";
                            break;
                        case 5:$sts = "SUSPENDIDO";
                            $clr = "";
                            break;
                        case 6:$sts = "NO-APROBADO";
                            $clr = "";
                            break;
                    }
                    $sol = $rst[ped_e1] + $rst[ped_e2] + $rst[ped_e3] + $rst[ped_e4];
                    $rstRef = pg_fetch_array($Set->list_one_data_by_id("erp_productos", $rst[ped_d]));
                    ?>
                    <tr>
                        <td><?php echo $cn ?></td>   
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" ><?php echo $rst[ped_a] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)"><?php echo $rst[ped_b] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)"><?php echo $rst[ped_c] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)"><?php echo $rstRef[2] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" align="right" width="50px"><?php echo $rst[ped_e1] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" align="right" width="50px"><?php echo $rst[ped_e2] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" align="right" width="50px"><?php echo $rst[ped_e3] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" align="right" width="50px"><?php echo $rst[ped_e4] ?></td>
                        <td onclick="auxWindow(1,<?php echo $rst[ids] ?>,<?php echo $rst[id] ?>, 1)" align="right" width="50px"><?php echo $sol ?></td>
                        <td align="center"  style=" text-align:center;font-weight:bolder;color:<?php echo $clr; ?>"><?php echo $sts ?></td>
                        <td align="center"  >
                    <?php
                    if ($Prt->edition == 0) {
                        ?>
                                <img src="../img/aprobado.png" onclick="aprobar(0,<?php echo $rst[id] ?>, '<?php echo $rst[ped_a] ?>')" width="36px" title="Aprobar Pedido" />
                                <img src="../img/no-aprobado.png" onclick="aprobar(1,<?php echo $rst[id] ?>, '<?php echo $rst[ped_a] ?>')" width="36px" title="No aprobar Pedido" />

    <?php }
    ?>
                        </td>
                    </tr>
    <?php
}
?>
            </tbody>
        </table>            
    </body>    
</html>

