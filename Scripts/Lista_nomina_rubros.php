<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_rubros.php';
$Clase_nomina_rubros = new Clase_nomina_rubros();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $texto = "WHERE rub_codigo='$txt'";
    }
    $cns = $Clase_nomina_rubros->lista_buscardor_un_reg_nomina_rubros($texto);
} else {
    $cns = $Clase_nomina_rubros->lista_buscardor_nomina_rubros();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Nomina Rubros</title>
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
                        frm.src = '../Scripts/Form_nomina_rubros.php';//Cambiar Form_bancos_y_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_nomina_rubros.php?id=' + id;//Cambiar Form_bancos_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 2://Mostrar Formulario Nomina Rubros
                        frm.src = '../Scripts/Form_nomina_rubros.php?id=' + id + '&x=' + x;//Cambiar Form_bancos_cajas
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                }
            }
            
            function cambiar_estado(std, id) {
                $.post("actions_nomina_rubros.php", {op: 2, std: std, id: id},
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
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_nomina_rubros.php';
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
        <div id="grid" onclick="alert('¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
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
                <center class="cont_title">Nomina - Rubros</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float: left;margin-top: 7px;padding: 7px" title="Nuevo Registro" onclick="auxWindow(0)">Nuevo</a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        &nbsp;&nbsp;&nbsp;
                        N CUENTA:<input type="text" style="margin-top: 12px" name="txt" id="txt" />
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Grupo</th>
            <th>Descripcion</th>
            <th>Valor</th>
            <th>Tipo Valor</th>
            <th>Tipo</th>
            <th>Cuenta Contable</th>
            <th>Descripcion Cuenta</th>
            <th>Nomina</th>
            <th>IESS</th>
            <th>Combo</th>
            <th>Estado</th>
            <th>Operacion</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                switch ($rst[rub_tipo]) {
                    case 1:
                        $tipo = 'CREDITO';
                        break;
                    case 2:
                        $tipo = 'DEBITO';
                        break;
                }
                $rst_cta = pg_fetch_array($Clase_nomina_rubros->lista_plan_cuentas_id($rst[rub_cuenta_contable]));
                switch ($rst[rub_nomina]) {
                    case 1:
                        $nomina = 'SI';
                        break;
                    case 2:
                        $nomina = 'NO';
                        break;
                }
                switch ($rst[rub_iess]) {
                    case 1:
                        $iess = 'SI';
                        break;
                    case 2:
                        $iess = 'NO';
                        break;
                }
                switch ($rst[rub_combo]) {
                    case 1:
                        $combo = 'SI';
                        break;
                    case 2:
                        $combo = 'NO';
                        break;
                }
                switch ($rst[rub_estado]) {
                    case 1:
                        $estado = 'ACTIVO';
                        break;
                    case 2:
                        $estado = 'INACTIVO';
                        break;
                }
                switch ($rst[rub_tipo_valor]) {
                    case 1:
                        $unidad = 'UNIDAD';
                        break;
                    case 2:
                        $unidad = 'PORCENTAJE';
                        break;
                }
                $n++;
                $ev = "onclick='auxWindow(2, $rst[rub_id], 1)'";
                echo"<tr>
                    <td>$n</td>
                    <td $ev >$rst[rub_codigo]</td>
                    <td $ev >$rst[rub_grupo]</td>
                    <td $ev >$rst[rub_descripcion]</td>
                    <td $ev >$rst[rub_valor]</td>
                    <td $ev >$unidad</td>
                    <td $ev >$tipo</td>
                    <td $ev >$rst_cta[pln_codigo]</td>
                    <td $ev >$rst_cta[pln_descripcion]</td>
                    <td $ev align='center'>$nomina</td>
                    <td $ev align='center'>$iess</td>
                    <td $ev align='center'>$combo</td>
                    <td align='center'>$estado</td>
                    <td align='center'>$rst[rub_operacion]</td>
                    <td align='center'>";
                if ($rst[rub_estado] == 1) {
                    if ($Prt->edition == 0) {
                        echo"<img src='../img/upd.png' width='12px' class='auxBtn' onclick='auxWindow(1, $rst[rub_id], 0)' />";
                    }
                    echo"<img src='../img/activo.png' width='12px' class='auxBtn' onclick='cambiar_estado(2, $rst[rub_id])' />";
                } else if ($rst[rub_estado] == 2) {
                    echo"<img src='../img/inactivo.png' width='12px' class='auxBtn' onclick='cambiar_estado(1, $rst[rub_id])' />";
                }
                echo"</td> 
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
