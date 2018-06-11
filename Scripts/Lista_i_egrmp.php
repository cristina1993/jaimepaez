<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set=new Set();
$vl_s=0;
if(isset($_GET[txt])){
    $vl_s=1;
    $cns=$Set->lista_pedidos_mp_sts_search(0,trim(strtoupper($_GET[txt])));    
}else{
    $cns=$Set->lista_pedidos_mp_sts(0);    
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Registro Materia Prima</title>
<head>
    <script>
$(function(){$("#tbl").tablesorter(
   {widgets:['stickyHeaders'],
    sortMultiSortKey: 'altKey',
    widthFixed:true});
    parent.document.getElementById('bottomFrame').src='';
    parent.document.getElementById('contenedor2').rows = "*,0%";
    });

function look_menu(){
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="visible";
grid=document.getElementById('grid');  
grid.style.visibility="visible";}

function auxWindow(a,code,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                main=parent.document.getElementById('mainFrame');    
                switch(a)
                {
                    case 1:
                        frm.src='../Scripts/Form_i_egmp.php?code='+code; 
                        parent.document.getElementById('contenedor2').rows = "*,85%";
                        if(x==0)
                        {
                            look_menu();
                        }  
                    break;    
                }
    
}
     
function del(id)
{
          var r=confirm("Esta Seguro de eliminar este elemento?");
          if(r == true){
             $.post("actions.php",{act:20,id:id},function(dt){
                 if(dt==0)
                  {
                      window.history.go(0);
                  }else{
                    alert(dt);  
                  }   
             }); 
          }else{
             return false;
          }
}
</script> 
<style>
    #mn27{
         background:black;
         color:white;
         border: solid 1px white;
    }
</style>
</head>
<body>
<div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
    <table style="width:100%" id="tbl">
        <caption class="tbl_head" >
                        <center class="cont_menu" >
                            <?php
                            $cns_sbm=$User->list_primer_opl(18,$_SESSION[usuid]);
                            while($rst_sbm=  pg_fetch_array($cns_sbm))
                            {
                                ?>
                                    <font class="sbmnu" id="<?php echo "mn".$rst_sbm[opl_id]?>" onclick="window.location='<?php echo "../".$rst_sbm[opl_direccion].".php"?>'" ><?php echo $rst_sbm[opl_modulo]?></font>
                                <?php
                            }
                            ?>
                            <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                        </center>
                        <center class="cont_title" >Egreso de Materia Prima</center>
                        <center class="cont_finder">
                            <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF'];?>" >
                                Codigo:<input type="text" name="txt" size="15" />
                                <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                                <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                            </form>  
                      </center>
            
                    
               </caption>
        
                <thead>
                     <th>No</th>
                     <th>Orden</th>
                     <th>Cliente</th>
                     <th>Tipo.Transaccion</th>
                     <th>Guia Transporte</th>
                     <th>Transportista</th>
                     <th>Estado</th>
                     <th>Acciones</th>
                </thead>
                <tbody id="tbody">
                    <?PHP
                    $n=0;
                    while ($rst=pg_fetch_array($cns)){
                        $n++;
                        $rst_sol=pg_fetch_array($Set->lista_total_pedido_solicitado($rst[ped_orden]));
                        $rst_ent=pg_fetch_array($Set->lista_total_pedido_entregado($rst[ped_orden],1));
                        
                        $rst_mov=  pg_fetch_array($Set->lista_movmp_code($rst[ped_orden]));
                        
                        $und=$rst_sol[und]*0.9;
                        
                        if($rst_ent[und]==0){
                            $sts="Pendiente";
                        }elseif($und>$rst_ent[und]){
                            $sts="En Proceso";
                        }elseif($und<=$rst_ent[und]){
                            $sts="Entregado";
                        }
                        if($vl_s==1 ){
                    ?>
                    <tr>
                        <td><?php echo $n?></td>
                        <td><?php echo $rst[ped_orden]?></td>
                        <td><?php echo $rst[emp_descripcion]?></td>
                        <td><?php echo 'EGRESO A CONSUMO'?></td>
                        <td><?php echo $rst[ped_orden]?></td>
                        <td><?php echo $rst_mov[mov_tranportista]?></td>
                        <td><?php echo $sts?></td>
                        <td>
                                                    <?php
                                                    if($Prt->edition==0)
                                                    {?>
                            <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,'<?php echo $rst[ped_orden]?>',0)">
                                                    <?php
                                                    }?>
                        </td>
                    </tr>  
                    <?PHP
                        }elseif($sts!="Entregado"){
                    ?>
                    <tr>
                        <td><?php echo $n?></td>
                        <td><?php echo $rst[ped_orden]?></td>
                        <td><?php echo $rst[emp_descripcion]?></td>
                        <td><?php echo 'EGRESO A CONSUMO'?></td>
                        <td><?php echo $rst[ped_orden]?></td>
                        <td><?php echo $rst[mov_tranportista]?></td>
                        <td><?php echo $sts?></td>
                        <td>
                                                    <?php
                                                    if($Prt->edition==0)
                                                    {?>
                            <img src="../img/upd.png" width="16px" class="auxBtn" onclick="auxWindow(1,'<?php echo $rst[ped_orden]?>',0)">
                                                    <?php
                                                    }?>
                        </td>
                    </tr>  
                    <?PHP
                        }
                    }
                    ?>
                </tbody>
        
        
    </table>            

</body>    
</html>

