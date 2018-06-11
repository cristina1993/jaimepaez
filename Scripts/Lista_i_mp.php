<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set = new Set();
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    $emp = $_GET[emp_id];
    if ($emp != 0) {
        $t_emp = "AND em.emp_id=" . $emp;
    } else {
        $t_emp = "";
    }

    $cns = $Set->lista_search_mp($txt, $t_emp);
} else {
    $cns = $Set->lista_mp0();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Materia Prima</title>
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
                        frm.src = '../Scripts/Form_i_mp.php';
                        look_menu();
                        break;
                    case 1:
                        frm.src = '../Scripts/Form_i_mp.php?id=' + id + '&x=' + x;
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
                window.location = '../formatos/descargar_archivo.php?archivo=materia_prima.csv';
            }
            function load_file() {
                var formData = new FormData($('#frm_file')[0]);
                $.ajax({
                    type: "POST",
                    url: "actions_upload_mp.php",
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
            #mn189{
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
                <center class="cont_title" >MATERIA PRIMA</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
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
                        Codigo:<input type="text" name="txt" size="25" placeholder="Referencia o Descripcion" />
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>

            </caption>
            <thead>
            <th>No</th>
            <th>Fabrica</th>
            <th>Tipo</th>
            <th>Referencia</th>
            <th>Descripcion</th>                     
            <th>Presentacion</th>
            <th>Unidad</th>
            <th>Propiedad1</th>
            <th>Propiedad2</th>
            <th>Propiedad3</th>
            <th>Procedencia</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </thead>
        <tbody>
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst[emp_descripcion] ?></td>
                    <td><?php echo $rst[mpt_nombre] ?></td>
                    <td><?php echo $rst[mp_codigo] ?></td>
                    <td><?php echo $rst[mp_referencia] ?></td>
                    <td><?php echo $rst[mp_presentacion] ?></td>
                    <td style="text-transform:lowercase" ><?php echo $rst[mp_unidad] ?></td>
                    <td><?php echo $rst[mp_pro1] ?></td>
                    <td><?php echo $rst[mp_pro2] ?></td>
                    <td><?php echo $rst[mp_pro3] ?></td>
                    <td><?php echo $rst[mp_pro4] ?></td>
                    <td><?php echo $rst[mp_obs] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="16px" class="auxBtn" onclick="del(<?php echo $rst[mp_id] ?>)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[mp_id] ?>, 0)">
                            <?php
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

