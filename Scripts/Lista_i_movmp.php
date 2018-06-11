<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $cns = $Set->lista_mov_mp_search2(trim(strtoupper($_GET[txt])));
} else {
    $cns = $Set->lista_all_mov_mp();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Movimiento de Materia Prima</title>
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
                    case 0:
                        frm.src = '../Scripts/Form_i_reg_movmp.php';
                        look_menu();
                        break;
                }

            }

            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 20, id: id}, function (dt) {
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

            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=inv_mp.csv';
            }
            function load_file() {
                var formData = new FormData($('#frm_file')[0]);
                $.ajax({
                    type: "POST",
                    url: "actions_upload_inv_mp.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (dt) {
                        alert(dt);
                    }
                });
            }
        </script> 
        <style>
            #mn190{
                background:black;
                color:white;
                border: solid 1px white;
            }
            div.upload {
                padding:5px; 
                width: 14px;
                height: 20px;
                background-color: #568da7;        
                background-image:-moz-linear-gradient(
                    top,
                    rgba(255,255,255,0.4) 0%,
                    rgba(255,255,255,0.2) 60%);
                color:#FFFFFF; 
                overflow: hidden;
                border-radius: 4px 4px 4px 4px; 
                cursor:pointer; 
                border:solid 1px #ccc; 
            }
            div.upload:hover{
                background-color:#7198ab;        
            }
            div.upload input {
                margin-top:-20; 
                margin-left:-5; 
                display: block !important;
                width: 40px !important;
                height: 40px !important;
                opacity: 0 !important;
                overflow: hidden !important;
                cursor:pointer; 
            }    
            #txt_load{
                margin-right:5px; 
                margin-top:13px; 
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
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
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Exporta Lista"  src="../img/xls.png" width="16px" />                            
                </center>
                <center class="cont_title" >Movimiento de Materia Prima</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
                        <?php
                    }
                    ?>
                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                    <form id="frm_file" name="frm_file" style="float:right">
                        <div class="upload">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>


                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo:<input type="text" name="txt" size="15" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="5">Materia Prima</th>
                    <th colspan="4">Documento</th>
                    <th colspan="4">Transaccion</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Referencia</th>
                    <th>Descripcion</th>
                    <th>Presentacion</th>
                    <th>Unidad</th>
                    <th>Fecha Transaccion</th>
                    <th>Documento No</th>
                    <th>Guia de Recepcion</th>
                    <th>Proveedor</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Peso Unitario</th>
                    <th>Peso Total en Stock</th>
                </tr>  
            </thead>
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_prov = pg_fetch_array($Set->lista_un_cliente($rst[mov_proveedor]))
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>
                        <td><?php echo $rst[mp_presentacion] ?></td>
                        <td align="center" style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>                        
                        <td><?php echo $rst[mov_fecha_trans] ?></td>
                        <td><?php echo $rst[mov_num_trans] ?></td>
                        <td><?php echo $rst[mov_documento] ?></td>
                        <td><?php echo trim($rst_prov['cli_apellidos'] . ' ' . $rst_prov['cli_nombres'] . ' ' . $rst_prov['cli_raz_social']) ?></td>
                        <td><?php echo $rst[trs_descripcion] ?></td>
                        <td align="right"><?php echo number_format($rst[mov_cantidad], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_unit], 1) ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_total], 1) ?></td>
                    </tr>  
                    <?PHP
                }
                ?>
            </tbody>


        </table>            

    </body>    
</html>

