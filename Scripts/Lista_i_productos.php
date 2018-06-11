<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_productos.php'; //cambiar clsClase_productos
$Productos = new Clase_Productos();
$cns_emp = $Productos->lst_emp();
if (isset($_GET[txt1], $_GET[txt2])) {
    $cns = $Productos->lista_buscador(trim(strtoupper($_GET[txt1])), trim(strtoupper($_GET[txt2])));
} else {
    $cns = $Productos->lista();
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


            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_i_productos.php';//Cambiar Form_productos
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_productos.php?id=' + id;//Cambiar Form_productos
                        look_menu();
                        break;
                    case 2://Al dar dobleclik en la lista muestra formulario 
                        frm.src = '../Scripts/Form_i_productos.php?id=' + id + '&x=' + x;//Cambiar Form_productos
                        look_menu();
                        break;
                }
            }

            function del(id, op) {

                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_productos.php", {act: 48, id: id, op: op}, function (dt) {//cambiar actions_productos
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
                window.location = '../formatos/descargar_archivo.php?archivo=productos.csv';
            }
            function load_file() {
                var formData = new FormData($('#frm_file')[0]);
                $.ajax({
                    type: "POST",
                    url: "actions_upload_pt.php",
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
            #mn193{
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
                <center class="cont_title" >PRODUCTOS </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                     <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>

                       <form id="frm_file" name="frm_file" style="float:right">
                        <div class="upload">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>
                    
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        CODIGO/DESCRIPCION:<input type="text" name="txt1" size="30" />
                        FABRICA:
                        <select id="emp_id" name="txt2">
<?php
while ($rst_emp = pg_fetch_array($cns_emp)) {
    echo "<option value='$rst_emp[emp_id]' >$rst_emp[emp_descripcion]</option>";
}
?>  
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Unidad</th>
            <th>Ancho</th>
            <th>Largo</th>
            <th>Gramaje</th>
            <th>Peso</th>
            <th>Acciones</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
<?PHP
$n = 0;
while ($rst = pg_fetch_array($cns)) {
    $n++;
    ?>
                <tr id="fila" ondblclick="auxWindow(2,<?php echo $rst[pro_id] ?>, 1)">
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['pro_codigo'] ?></td>
                    <td><?php echo $rst['pro_descripcion'] ?></td>
                    <td><?php echo $rst['pro_uni'] ?></td>
                    <td><?php echo $rst['pro_ancho'] ?></td>
                    <td><?php echo $rst['pro_largo'] ?></td>
                    <td><?php echo $rst['pro_gramaje'] ?></td>
                    <td><?php echo $rst['pro_peso'] ?></td>
                    <td align="center">
    <?php
    if ($Prt->delete == 0) {
        ?>
                            <img src="../img/del_reg.png" width="16px"  class="auxBtn" onclick="del(<?php echo $rst[pro_id] ?>, 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="16px"  class="auxBtn" onclick="auxWindow(1,<?php echo $rst[pro_id] ?>, 0)">
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

