<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_empresas.php';
$Emp = new Empresas();
$cns = $Emp->lista_empresas();
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Formulas</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function auxWindow(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                parent.document.getElementById('contenedor2').rows = "*,80%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_empresas.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_empresas.php?id=' + id;
                        break;

                }

            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

        </script>
        <style>
            #mn276{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                </center>
                <center class="cont_title" >EMPRESAS / PUNTOS DE EMISION</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>-->
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Ruc</th>
            <th align="left">Nombre Empresa</th>
            <th align="left">Punto de Emision</th>
            <th align="left">Direccion Empresa</th>
            <th align="left">Direccion Punto Emision</th>
            <th align="left">Codigo Empresa</th>
            <th align="left">Codigo Punto Emision</th>
            <th align="left">Codigo Tributario</th>
            <th align="left">Credencial</th>
            <th align="left">Acciones</th>
        </thead>

        <tbody id="form_save">
            <?php
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                echo "<tr>
                    <td>$n</td>
                    <td>$rst[emi_identificacion]</td>
                    <td>$rst[emi_nombre]</td>
                    <td>$rst[emi_nombre_comercial]</td>
                    <td>$rst[emi_dir_establecimiento_matriz]</td>
                    <td>$rst[emi_dir_establecimiento_emisor]</td>
                    <td>$rst[emi_cod_establecimiento_emisor]</td>
                    <td>$rst[emi_cod_punto_emision]</td>
                    <td>$rst[emi_contribuyente_especial]</td>
                    <td>$rst[emi_credencial]</td>    
                        <td>
                                  <!--<img class='auxBtn' onclick='auxWindow(2,$rst[emi_id])' src='../img/del_reg.png' width='20px'/>;-->                      
                                <img class='auxBtn' onclick='auxWindow(1,$rst[emi_id])' src='../img/upd.png' width='20px' />
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>
