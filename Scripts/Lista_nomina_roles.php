<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_nomina_roles.php';
$Clase_nomina_rubros = new Clase_nomina_roles();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    $desde = trim($_GET[desde]);
    $hasta = trim($_GET[hasta]);
    if (!empty($txt)) {
        $texto = "and (n.nom_periodo like '%$txt%' or nom_forma_pago like '%$txt%' or e.emp_documento like '%$txt%' or emp_apellido_paterno like '%$txt%' or emp_apellido_materno like '%$txt%' or emp_nombres like '%$txt%')";
    } else {
        $texto = "and n.nom_fec_registro between '$desde' and  '$hasta'";
    }
    $cns = $Clase_nomina_rubros->lista_buscar_nomina($texto);
} else {
    $cns = $Clase_nomina_rubros->lista_buscar_nomina();
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
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
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
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

                    case 0://Reporte
                        frm.src = '../Scripts/frm_pdf_rol_pago.php?id=' + id;//Cambiar Form_bancos_y_cajas
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        break;
                }
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
                <center class="cont_title">Nomina - Roles</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float: left;margin-top: 7px;padding: 7px" title="Nuevo Registro" onclick="auxWindow(0)">Nuevo</a>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        &nbsp;&nbsp;&nbsp;
                        BUSCAR:<input type="text" style="margin-top: 12px" name="txt" id="txt" />
                        DESDE:<input type="date" size="15" name="desde" id="desde" value="<?php echo $desde ?>" />
                        <img src="../img/calendar.png" id="im-desde"/>
                        HASTA:<input type="date" size="15" name="hasta" id="hasta" value="<?php echo $hasta ?>" />
                        <img src="../img/calendar.png" id="im-hasta"/>
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Fecha</th>
            <th>Identificacion</th>
            <th>Nombre</th>
            <th>Periodo</th>
            <th>Dias Laborados</th>
            <th>Forma de Pago</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {

                $n++;
                $ev = "onclick='auxWindow(2, $rst[rub_id], 1)'";
                echo"<tr>
                    <td>$n</td>
                    <td $ev >$rst[nom_fec_registro]</td>
                    <td $ev >$rst[emp_documento]</td>
                    <td $ev >$rst[emp_apellido_paterno] $rst[emp_apellido_materno] $rst[emp_nombres]</td>
                    <td $ev >$rst[nom_periodo]</td>
                    <td $ev >$rst[nom_dias_trabajados]</td>
                    <td $ev >$rst[nom_forma_pago]</td>
                    <td align='center'>
                    <img src='../img/orden.png' width='12px' class='auxBtn' onclick='auxWindow(0, $rst[nom_id], 0)' />
                    </td> 
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
