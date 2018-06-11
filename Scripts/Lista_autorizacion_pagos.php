<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cuentasxpagar.php';
$Cxp = new CuentasPagar();
if (isset($_GET[txt])) {
    $cns = $Cxp->lista_obligaciones_pago($_GET[txt]);
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
            });
            function acciones(sts, obj, tip) {
                id = obj.id.split('_');
                cnt = parseFloat($('#cobl_' + id[1]).val().replace(',', ''));
                ant = parseFloat($('#sld_' + id[1]).html().replace(',', ''));
                if (cnt > ant) {
                    alert('La cantidad a pagar es mayor al saldo de la deuda');
                } else {
                    if (tip == 1 && sts == 1) {
                        sts = 3;
                    }
                    data = Array(sts, id[1], cnt);
                    $.post("actions_pago_proveedores.php", {op: 1, 'data[]': data},
                    function (dt) {
                        if (dt == 0) {
                            window.location = 'Lista_autorizacion_pagos.php?txt=' + txt.value;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }

            function mensaje(sms) {
                if (sms == 1) {
                    alert('Pago Aprobado');
                } else {
                    alert('Pago Rechazado');
                }
            }

            function validar_saldo(cod) {
                valor = parseFloat($('#cobl_' + cod).val().replace(',', ''));
                saldo = parseFloat($('#sld_' + cod).html().replace(',', ''));
                if (valor > saldo) {
                    alert('El valor a pagar no puede ser mayor al saldo');
                    $('#cobl_' + cod).val(saldo);
                }
            }

        </script> 
        <style>
            #mn315{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                /*                background:#ccc;
                                color:black;
                                font-weight:bolder; */
            }
            .axb{
                padding:2px;
                width: 20px;
                background:#616975;
                border:solid 1px #00529B; 
                margin-left:5px;
                border-radius:5px; 
                cursor:pointer; 
            }
            .axb:hover{
                background:#7198ab; 
            }
            .registrado{
                background:white !important;  
            }
            .aprobado{
                background:green !important;  
            }
            .rechazado{
                background:brown !important;  
            }
            .pagado{
                background:teal !important
            }
            .con_info .registrado, .con_info .aprobado, .con_info .rechazado, .con_info .pagado{
                height:17px;
                margin-left:1px; 
                float:left;
                padding-left:10px; 
                padding-right:10px; 
                padding-top:2px;  
                box-shadow:1px 1px 5px #616975; 
            }
            .con_info{
                text-align:center; 
                font-size:9px; 
                color:white; 
                float:right;
                padding:5px;
                margin-top:-40px; 
                text-shadow:2px 1px 0px black; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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
                <center class="cont_title" ><?php echo "AUTORIZACION DE PAGOS PENDIENTES" ?></center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                        Proveedor  :<input type="text" name="txt" size="35" id="txt" value="<?php echo $_GET[txt] ?>" list="lista_proveedores"/>
                        <datalist id="lista_proveedores">
                            <?php
                            $cns_prov = $Cxp->lista_proveedores();
                            while ($rst_prov = pg_fetch_array($cns_prov)) {
                                echo "<option value='$rst_prov[cli_ced_ruc]'>$rst_prov[cli_ced_ruc] $rst_prov[cli_raz_social]</option>";
                            }
                            ?>
                        </datalist>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()">Buscar</button>
                    </form>
                    <div class="con_info">
                        <div class="registrado">Pendiente</div>
                        <div class="aprobado">Aprobado</div>
                        <div class="rechazado">Rechazado</div>
                        <div class="pagado">Pagado</div>
                    </div>

                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>O.Pago</th>
            <th>Ruc</th>
            <th>Proveedor</th>
            <th>Documento</th>
            <th>Concepto</th>
            <th>Fecha Emision</th>
            <th>Fecha Vencimiento</th>
            <th>Total $</th>
            <th>Pagado $</th>
            <th>Saldo $</th>
            <th>A pagar $</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst_cli = pg_fetch_array($Cxp->lista_cliente_ruc($rst[reg_ruc_cliente]));
                $rst_pagos = pg_fetch_array($Cxp->lista_pagos_documento($rst['reg_id']));
                $rst_pagos_ndebito = pg_fetch_array($Cxp->lista_pagos_ndebito($rst['reg_id']));
                switch ($rst[obl_estado]) {
                    case 0:
                        $estado = 'POR VENCER';
                        break;
                    case 1:
                        $estado = 'VENCIDO';
                        break;
                }
                switch ($rst[obl_estado_obligacion]) {
                    case 0://Registrada
                        $class = 'registrado';
                        $title = 'Registrada';
                        $disabled = '';
                        break;
                    case 1://Aprobada
                        $class = 'aprobado';
                        $title = 'Aprobada';
                        $disabled = 'disabled';
                        break;
                    case 2://Rechazada
                        $class = 'rechazado';
                        $title = 'Rechazada';
                        $disabled = 'disabled';
                        break;
                    case 3://Pagado
                        $class = 'pagado';
                        $title = 'Pagada';
                        $disabled = 'disabled';
                        break;
                }
                $total = $rst[pag_valor] + $rst_pagos_ndebito[debito];
                $pagado = $rst_pagos[sum];
                $saldo = $total - $pagado;
                $apagar = $rst[obl_cantidad];
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td ><?php echo $rst[obl_codigo] ?></td>
                    <td ><?php echo $rst[reg_ruc_cliente] ?></td>
                    <td ><?php echo $rst_cli[cli_raz_social] ?></td>
                    <td ><?php echo $rst[reg_num_documento] ?></td>
                    <td ><?php echo $rst[reg_concepto] ?></td>
                    <td ><?php echo $rst[reg_femision] ?></td>
                    <td ><?php echo $rst[pag_fecha_v] ?></td>
                    <td align="right" id="<?php echo "tobl_" . $rst[obl_id] ?>" ><?php echo number_format($total, 2) ?></td>
                    <td align="right" ><?php echo number_format($pagado, 2) ?></td>
                    <td align="right" id="<?php echo "sld_" . $rst[obl_id] ?>" ><?php echo number_format($saldo, 2) ?></td>
                    <td width="150px" align="right" >
                        <input type="text" size="10px" style="text-align:right" <?php echo $disabled ?> id="<?php echo "cobl_" . $rst[obl_id] ?>" value="<?php echo number_format($apagar, 2) ?>" onchange="validar_saldo(<?php echo $rst[obl_id] ?>)" />
                    </td>
                    <td align="center" ><?php echo $estado ?></td>
                    <td align="center" title="<?php echo $title ?>" class="<?php echo $class ?>" id="<?php echo 'td_' . $rst[obl_id] ?>" >
                        <?php
                        switch ($rst[obl_estado_obligacion]) {
                            case 0:
                                ?>
                                <img class="axb" src="../img/exito2.png" title="Aprobar Pago" id="<?php echo 'obl1_' . $rst[obl_id] ?> " onclick="acciones(1, this, '<?php echo $rst[obl_tipo] ?>')" />
                                <img class="axb" src="../img/del_reg.png" title="Rechazar Pago" id="<?php echo 'obl2_' . $rst[obl_id] ?>" onclick="acciones(2, this)" />                        
                                <?php
                                break;
                            case 1:
                                ?>
                                <img class="axb" src="../img/del_reg.png" title="Rechazar Pago" id="<?php echo 'obl2_' . $rst[obl_id] ?>" onclick="acciones(2, this)" />                        
                                <?php
                                break;
                            default :
                                break;
                        }
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
<script>
    var p = '<?php echo $est1 ?>';
    $('#estado').val(p);
</script>

