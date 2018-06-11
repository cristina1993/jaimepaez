<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_bancos_y_cajas.php';
$Clase_bancos_y_cajas = new Clase_bancos_y_cajas();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $texto = "WHERE byc_cuenta_contable='$txt'";
    }
    $cns = $Clase_bancos_y_cajas->lista_buscardor_un_reg_bancos_cajas($texto);
} else {
    $cns = $Clase_bancos_y_cajas->lista_buscardor_bancos_cajas();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN">
<html>
    <head>
        <meta charset="UTF-8">
        <title>Bancos y Cajas</title>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_bancos_y_cajas.php';//Cambiar Form_bancos_y_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_bancos_y_cajas.php?id=' + id;//Cambiar Form_bancos_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 2://Mostrar Formulario Bancos y Cajas
                        frm.src = '../Scripts/Form_bancos_y_cajas.php?id=' + id + '&x=' + x;//Cambiar Form_bancos_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                }
            }

            function cambiar_estado(std, id) {
                $.post("actions_bancos_y_cajas.php", {op: 2, std: std, id: id},
                function (dt) {
                    if (dt == 0) {
                        cancelar();
                    } else {
                        alert(dt);
                    }
                });
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_bancos_y_cajas.php';
            }
        </script>
        <style>
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>
        <table style="width: 100%" id="tbl">
            <caption class="tbl_head">
                <center class="cont_menu">
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float: right" onclick="window.print()" title="Imprimir Documento" src="../img/print_iconop.png" width="16px">
                </center>
                <center class="cont_title">LISTA BANCOS Y CAJAS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float: left;margin-top: 7px;padding: 7px" title="Nuevo Registro" onclick="auxWindow(0)">Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        &nbsp;&nbsp;&nbsp;
                        N CUENTA:<input type="text" style="margin-top: 12px" name="txt" id="txt">
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Tipo</th>
            <th>Referencia</th>
            <th># Cuenta</th>
            <th>Tipo</th>
            <th># Documento</th>
            <th>Saldo</th>
            <th>Cuenta Contable</th>
            <th>Descripcion Cuenta</th>
            <th>Estado</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                switch ($rst[byc_tipo]) {
                    case 1:
                        $tipo = 'BANCO';
                        break;
                    case 2:
                        $tipo = 'CAJA';
                        break;
                    case 3:
                        $tipo = 'CAJA CHICA';
                        break;
                }
                switch ($rst[byc_tipo_cuenta]) {
                    case 0:
                        $tp_cuenta = '';
                        break;
                    case 1:
                        $tp_cuenta = 'CORRIENTE';
                        break;
                    case 2:
                        $tp_cuenta = 'AHORROS';
                        break;
                }
                $rst_cta = pg_fetch_array($Clase_bancos_y_cajas->lista_plan_cuentas_id($rst[byc_id_cuenta]));
                switch ($rst_cta[pln_estado]) {
                    case 0:
                        $estado = 'ACTIVO';
                        break;
                    case 1:
                        $estado = 'INACTIVO';
                        break;
                }
                $rst_suma_ctsxcobrar = pg_fetch_array($Clase_bancos_y_cajas->lista_suma_ctsxcobrar($rst[byc_cuenta_contable]));
                $rst_suma_ctsxpagar = pg_fetch_array($Clase_bancos_y_cajas->lista_suma_ctsxpagar($rst[byc_cuenta_contable]));
                $suma = $rst_suma_ctsxcobrar[suma_ctasxcobrar] + $rst[byc_saldo];
                $saldo = $suma - $rst_suma_ctsxpagar[suma_ctasxpagar];
                $n++;
                $ev = "onclick='auxWindow(2, $rst[byc_id], 1)'";
                echo"<tr>
                    <td>$n</td>
                    <td $ev >$tipo</td>
                    <td $ev >$rst[byc_referencia]</td>
                    <td $ev >$rst[byc_num_cuenta]</td>
                    <td $ev >$tp_cuenta</td>
                    <td $ev >$rst[byc_documento]</td>
                    <td $ev >$saldo</td>
                    <td $ev >$rst[byc_cuenta_contable]</td>
                    <td $ev >$rst_cta[pln_descripcion]</td>
                    <td align='center'>$estado</td>
                    <td align='center'>";

                if ($rst_cta[pln_estado] == 0) {
                    if ($Prt->edition == 0) {
                        echo"<img src='../img/upd.png' width='12px' class='auxBtn' onclick='auxWindow(1, $rst[byc_id], 0)' />";
                    }
                    echo"<img src='../img/activo.png' width='12px' class='auxBtn' onclick='cambiar_estado(1, $rst_cta[pln_id])' />";
                } elseif ($rst_cta[pln_estado] == 1) {
                    echo"<img src='../img/inactivo.png' width='12px' class='auxBtn' onclick='cambiar_estado(0, $rst_cta[pln_id])' />";
                }
                echo"</td> 
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
