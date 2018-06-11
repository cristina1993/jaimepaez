<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_horarios.php';
$Hor = new Clase_horarios;
if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $text = "where ger_codigo like '%$txt%' or ger_descripcion like '%$txt%'";
    }
//    $cns = $Hor->lista_buscardor_criterios($text);
} else {
    $txt = '';
    $cns = $Hor->lista_grupo_horarios($text);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Grupos Horarios</title>
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
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_grupos_horarios.php?txt=' + '<?php echo $txt ?>';
                        look_menu();
                        break;
                    case 1://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_grupos_horarios.php?id=' + id + '&txt=' + '<?php echo $txt ?>';
                        look_menu();
                        break;
                    case 2://Editar
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/Form_criterios_permiso.php?id=' + id + '&x=' + x + '&txt=' + '<?php echo $txt ?>';
                        look_menu();
                        break;
                }
            }
            
            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_horarios.php", {op: 3, id: id}, function (dt) {
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
                <center class="cont_title" >LISTA GRUPOS HORARIOS</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt?>"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Grupo de Horarios</th>
            <th>Horas Semana</th>
            <th>Lunes</th>
            <th>Martes</th>
            <th>Miercoles</th>
            <th>Jueves</th>
            <th>Viernes</th>
            <th>Sabado</th>
            <th>Domingo</th>
            <th>Acciones</th>
        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                if($rst['gru_lunes'] == 1){
                    $lunes = '&check;';
                } else {
                    $lunes = '';
                }
                if($rst['gru_martes'] == 1){
                    $martes = '&check;';
                } else {
                    $martes = '';
                }
                if($rst['gru_miercoles'] == 1){
                    $miercoles = '&check;';
                } else {
                    $miercoles = '';
                }
                if($rst['gru_jueves'] == 1){
                    $jueves = '&check;';
                } else {
                    $jueves = '';
                }
                if($rst['gru_viernes'] == 1){
                    $viernes = '&check;';
                } else {
                    $viernes = '';
                }
                if($rst['gru_sabado'] == 1){
                    $sabado = '&check;';
                } else {
                    $sabado = '';
                }
                if($rst['gru_domingo'] == 1){
                    $domingo = '&check;';
                } else {
                    $domingo = '';
                }
                ?>
                <tr style="height: 30px">
                    <td><?php echo $n ?></td>
                    <td><?php echo $rst['gru_horarios'] ?></td>
                    <td><?php echo $rst['gru_hrs_semana'] ?></td>
                    <td align="center" ><?php echo $lunes ?></td>
                    <td align="center" ><?php echo $martes ?></td>
                    <td align="center" ><?php echo $miercoles ?></td>
                    <td align="center" ><?php echo $jueves ?></td>
                    <td align="center" ><?php echo $viernes ?></td>
                    <td align="center" ><?php echo $sabado ?></td>
                    <td align="center" ><?php echo $domingo ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del(<?php echo $rst[gru_id] ?>)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="12px" class="auxBtn" onclick="auxWindow(1,<?php echo $rst[gru_id] ?>, 0)">
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

