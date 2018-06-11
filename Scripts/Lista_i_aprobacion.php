<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsUsers.php';
$Set = new Set();
$User=new User();
if(isset($_GET[txt])){
    $status=$_GET[txt];
}else{
    $status='0';
 }
 
 if($status=="x"){
   $cns=$Set->lista_aprobaciones();   
 }else{
   $cns=$Set->lista_aprobaciones_status($status);   
 }


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Descuentos</title>
    <head>
        <script>
            $(function() {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows="*,0%";
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://NUevo
                        frm.src = '../Scripts/Form_i_descuentos.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_i_descuentos.php?id=' + id;
                        look_menu();
                        break;
                }
            }
            function del(id)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions.php", {act: 29, id: id}, function(dt) {
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
            function aprobacion(a,id){
                if(a==1){
                     apb='APROBAR';//1
                }else{
                     apb='RECHAZAR';//2
                }
                var r = confirm("Esta Seguro de "+apb+" esta peticion?");
                if (r == true) {
                    $.post("actions.php", {act:46,id:id,sts:a}, function(dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
        </script> 
        <style>
    #mn38{
         background:black;
         color:white;
         border: solid 1px white;
    }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:80%" id="tbl" cellpadding="7">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                            <?php
                            $cns_sbm=$User->list_primer_opl(19,$_SESSION[usuid]);
                            while($rst_sbm=  pg_fetch_array($cns_sbm))
                            {
                                ?>
                                    <font class="sbmnu" id="<?php echo "mn".$rst_sbm[opl_id]?>" onclick="window.location='<?php echo "../".$rst_sbm[opl_direccion].".php"?>'" ><?php echo $rst_sbm[opl_modulo]?></font>
                                <?php
                            }
                            ?>
                </center>
                <center class="cont_title" >Aprobaciones</center>
                <center class="cont_finder">                   
                    <form method="GET" id="frmSearch" hidden name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Codigo : <input type="text" name="txt" size="19" />
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Status:<select name="txt" id="txt">
                            <option value="x">Todos</option>
                            <option value="1">Aprobado</option>
                            <option value="0">Espera</option>
                            <option value="2">Rechazado</option>
                        </select>
                        <script>document.getElementById('txt').value=<?php echo $status?></script>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>

            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <th>No</th>
                <th>Fecha Registro</th>
                <th>Solicita</th>
                <th>Cliente</th>
                <th>Ruc</th>
                <th>Caracteristica</th>
                <th>Solicitud</th>
                <th>Status</th>
                <th>Autoriza</th>
                <th>Acciones</th>                   
            </thead>
            <!--------------------------------->
        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst=pg_fetch_array($cns)){
                $n++;
                switch($rst[apb_sts]){
                    case 0:
                        $sts='Espera';
                    break;
                    case 1:
                        $sts='Aprobado';
                    break;
                    case 2:
                        $sts='Rechazado';
                    break;
                }
                switch($rst[abp_campo]){
                    case 'cli_cup_maximo':
                        $campo='cupo maximo';
                    break;
                    case 'cli_cup_mensual':
                        $campo='cupo mensual';
                    break;
                    case 'cli_cat_cliente':
                        $campo='categoria';
                    break;
                }
                $rst_sol=pg_fetch_array($User->listUnUsuario($rst[apb_solicita]));
                $rst_aut=pg_fetch_array($User->listUnUsuario($rst[apb_autoriza]));
                        ?>
                            <tr>
                                <td><?php echo $n?></td>
                                <td align="left"><?php echo $rst[apb_fecha_reg]?></td>                                
                                <td align="left"><?php echo $rst_sol[usu_person]?></td>                                
                                <td align="center"><?php echo $rst[cli_codigo]?></td>
                                <td align="left"><?php echo $rst[cli_ced_ruc]?></td>
                                <td align="left"><?php echo $campo?></td>
                                <td align="center"><?php echo $rst[apb_cambio]?></td>
                                <td align="center"><?php echo $sts?></td>
                                <td align="left"><?php echo $rst_aut[usu_person]?></td>                                
                                <td align="right">
                                    <?php 
                                    if($rst[apb_sts]==0){
                                        ?>
                                    <img class="auxBtn" src="../img/error.png" title="Rechazar" onclick="aprobacion(2,<?php echo $rst[apb_id]?>)" />                                    
                                    <img class="auxBtn" src="../img/exito.png" title="Aprobar"  onclick="aprobacion(1,<?php echo $rst[apb_id]?>)" />
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

