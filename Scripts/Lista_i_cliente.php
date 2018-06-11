<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_cliente.php';
$Clase_cliente = new Clase_cliente();
if (isset($_GET[search])) {
    $txt = strtoupper(trim($_GET[txt]));
    $nm = strtoupper(trim($_GET[txt]));
    $tipo = $_GET[cli_tipo];
    $categoria = $_GET[cli_categoria];
    $estado = $_GET[cli_estado];
    if (!empty($txt)) {
        $txt = " where(cli_codigo like '%$txt%' 
or cli_ced_ruc like '%$txt%' 
or cli_apellidos like '%$txt%' 
or cli_nombres like '%$txt%' 
or cli_raz_social like '%$txt%' 
or cli_nom_comercial like '%$txt%'   )";
        $tipo = '';
        $categoria = '';
        $estado = '';
    } else {

        if ($tipo != 'x') {
            $tipo = "where cli_tipo ='$tipo'  ";
        } else {
            $tipo = '';
        }

        if ($categoria != 'x') {
            if ($tipo == '') {
                $prfix = 'where';
            } else {
                $prfix = 'and';
            }
            $categoria = $prfix . " cli_categoria ='$categoria'  ";
        } else {
            $categoria = '';
        }

        if ($estado != 'x') {
            if ($tipo == '' && $categoria == '') {
                $prfix = 'where';
            } else {
                $prfix = 'and';
            }
            $estado = $prfix . " cli_estado ='$estado'  ";
        } else {
            $estado = '';
        }
    }
    $cns = $Clase_cliente->lista_buscador_cliente($txt, $tipo, $categoria, $estado);
} else {
    $txt = '';
    $tipo = 'x';
    $categoria = 'x';
    $estado = 'x';
//    $cns = $Clase_cliente->lista_cliente();
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

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id, x) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_cliente.php';
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_cliente.php?id=' + id;
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        look_menu();
                        break;
                    case 2://Editar
                        frm.src = '../Scripts/Form_i_cliente.php?id=' + id + '&x=' + x;
                        //look_menu();
                        break;
                }

            }

            function del(id, op, cli)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_cliente.php", {act: 48, id: id, op: op, data: cli}, function (dt) {
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
                window.location = '../formatos/descargar_archivo.php?archivo=clientes.csv';
            }
            function load_file() {
                $('#frm_file').submit();
            }
        </script> 
        <style>
            #mn202{
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
            #frmSearch{
                font-size: 10px;
            }
            .sel{
                font-size: 11px;
                width: 80px;
            }
        </style>
    </head>
    <body>
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
                    <img class="auxBtn" style="float:right; font-size: 10px;" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="12px" />                            
                </center>               
                <center class="cont_title" >CLIENTES Y PROVEEDORES</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>
<!--                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>
                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_clientes.php" method="POST" enctype="multipart/form-data">
                        <div class="upload">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right; font-size: 10px" id="txt_load">Cargar Datos:</font>-->
                    <form method="GET" id="frmSearch" name="frm1" autocomplete="off" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR:<input type="text" name="txt" id="txt" size="15" value="<?php echo $nm?>" />
                        TIPO:
                        <select id="cli_tipo" name="cli_tipo" class="sel">
                            <option value="x" >Todos</option>
                            <option value="0" >CLIENTE</option>
                            <option value="1" >PROVEEDOR</option>
                            <option value="2" >AMBOS</option>                            
                        </select>
                        CATEGORIA:
                        <select id="cli_categoria" name="cli_categoria" class="sel">
                            <option value="x" >Todos</option>
                            <option value="1" >NATURAL</option>
                            <option value="2" >JURIDICO</option>
                        </select>
                        ESTADO:
                        <select id="cli_estado" name="cli_estado" class="sel">
                            <option value="x" >Todos</option>
                            <option value="0" >ACTIVO</option>
                            <option value="1" >INACTIVO</option>
                            <option value="2" >SUSPENDIDO</option>
                        </select>
                        <script>
                            $('#cli_tipo').val('<?php echo $_GET[cli_tipo] ?>');
                            $('#cli_categoria').val('<?php echo $_GET[cli_categoria] ?>');
                            $('#cli_estado').val('<?php echo $_GET[cli_estado] ?>');
                        </script>
                        <button class="btn" title="Buscar" id="search" name="search" onclick="frmSearch.submit()" style="font-size: 10px;">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Cedula/ruc</th>
            <th>Cliente</th>
            <th>F_registro</th>
            <th>Pais</th>
            <th>Canton</th>
            <th>Parroquia</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Accciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                if ($rst['cli_categoria'] == '1') {
                    $rst['cli_categoria'] = 'NATURAL';
                } else {
                    $rst['cli_categoria'] = 'JURIDICA';
                }

                if ($rst['cli_tipo'] == '0') {
                    $rst['cli_tipo'] = 'CLIENTE';
                } else if ($rst['cli_tipo'] == '1') {
                    $rst['cli_tipo'] = 'PROVEEDOR';
                } else {
                    $rst['cli_tipo'] = 'AMBOS';
                }
                if ($rst['cli_estado'] == '0') {
                    $rst['cli_estado'] = 'ACTIVO';
                } else if ($rst['cli_estado'] == '1') {
                    $rst['cli_estado'] = 'INACTIVO';
                } else {
                    $rst['cli_estado'] = 'SUSPENDIDO';
                }
                $n++;
                $ev = "onclick='auxWindow(2,$rst[cli_id],1)'";
                ?>
                <tr class="fila">
                    <td ><?php echo $n ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_codigo'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_tipo'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_categoria'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_ced_ruc'] ?></td>
                    <td <?php echo $ev ?> ><?php echo trim($rst['cli_raz_social']) ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_fecha'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_pais'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_canton'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_parroquia'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_calle_prin'] . ' ' . $rst['cli_numero'] . ' ' . $rst['cli_calle_sec'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_telefono'] ?></td>
                    <td <?php echo $ev ?> style="text-transform: lowercase"><?php echo $rst['cli_email'] ?></td>
                    <td <?php echo $ev ?> ><?php echo $rst['cli_estado'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px"  class="auxBtn" onclick="del(<?php echo $rst[cli_id] ?>, 1, '<?php echo $rst[cli_ced_ruc] ?>')">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png"  width="12px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[cli_id] ?>, 0)">
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

