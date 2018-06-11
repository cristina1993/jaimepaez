<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set= new Set();
$Prt1=new Permisos();$Prt2=new Permisos();$Prt3=new Permisos();$Prt4=new Permisos();$Prt5=new Permisos();$Prt6=new Permisos();
$Prt1->Permit($_SESSION[usuid],17);//Mov
$Prt2->Permit($_SESSION[usuid],18);//Inv
$Prt3->Permit($_SESSION[usuid],19);//Kardex
//Prod Terminado
$Prt4->Permit($_SESSION[usuid],20);//Mov
$Prt5->Permit($_SESSION[usuid],21);//Inv
$Prt6->Permit($_SESSION[usuid],22);//Kardex

if(isset($_GET[txt]))
{
    $txt=strtoupper(trim($_GET[txt]));
    $cns=$Set->lista_one_table_just_code($txt);
}else{
    $cns=$Set->list_aprobation('1');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title><?php echo $tbl_name?></title>
<head>
    <script>
$(function(){$("#tbl").tablesorter(
   {widgets:['stickyHeaders'],
    sortMultiSortKey: 'altKey',
    widthFixed:true});
    parent.document.getElementById('bottomFrame').src='';
});
var tbl_name='<?php echo $tbl_name?>';    
function look_menu(){
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="visible";
grid=document.getElementById('grid');  
grid.style.visibility="visible";}

function auxWindow(a,ped_id,pro_id,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                main=parent.document.getElementById('mainFrame');    
                switch(a)
                {
                    case 0:
                        window.location='../Scripts/Lista_egreso_bodega_mp.php'; 
                    break;
                    case 1:
                        
                        frm.src='../Scripts/Form_egreso_bodega.php?ped_id='+ped_id+'&pro_id='+pro_id+'&x='+x; 
                        if(x==0)
                        {
                            look_menu();
                        }
                    break;  
                    case 2:
                        window.location='../Scripts/Lista_mov_mp.php'; 
                    break;    
                    case 3:
                        window.location='../Scripts/Lista_mov_pt.php'; 
                    break;    
                    
                }
}
function loadData(id,dr)
{
    if(dr==1)
    {
      window.location='Lista_aprobacion.php';  
    }else if(dr==2){
      window.location='Lista_egreso_bodega_mp.php'; 
    }else{
      window.location='Lista_'+tbl_name+'.php?tipo='+id;  
    }
}

</script>    
<style>
</style>
</head>
<body>
<div id="grid" onclick="alert('¡Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
<table id="tbl" style="width:100% ">
    
            <caption class="tbl_head" >
                <center class="cont_menu" >
                            <font class="sbmnu" onclick="auxWindow(0,0)" >Egreso Bodega MP</font>
                            <?php 
                            if($Prt1->show==0 || $Prt2->show==0 || $Prt3->show==0){
                            ?>        
                                <font class="sbmnu" onclick="auxWindow(2,0)" >Materia Prima</font>
                            <?php    
                            }
                            ?>
                            <?php 
                            if($Prt4->show==0 || $Prt5->show==0 || $Prt6->show==0){
                            ?>        
                                <font class="sbmnu" onclick="auxWindow(3,0)" >Producto Terminado</font>
                            <?php    
                            }
                            ?>
                            <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >EGRESO DE BODEGA DE MATERIA PRIMA POR PEDIDO</center>
                <center class="cont_finder">
                            <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF'];?>" >
                                PEDIDO:<input type="text" name="txt" size="15" />
                                <a href="#" style="position:absolute " class="act_btn" title="Buscar" onclick="frmSearch.submit()" ><img src="../img/finder.png" /></a>                                    
                            </form>  
                </center>
            </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pedido</th>
                        <th>Fecha Orden</th>
                        <th>Fecha Entrega</th>
                        <th>Hora de Entrega</th>
                        <th>Seccion</th>
                        <th>Descripcion</th>
                        <th>Linea</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>                
                </thead>
                <tbody id="tbody">
                <?php
                $cn=0;
                while($rst=pg_fetch_array($cns))
                {$cn++;
                                        switch ($rst[ped_f])
                                        {
                                            case 0:$sts="REGISTRADO";$clr="";break;
                                            case 1:$sts="APROBADO";$clr="#f37e00";break;
                                            case 2:$sts="PRODUCCION";$clr="#00cd66";break;
                                            case 3:$sts="TERMINADO";$clr='#00b2ee';break;
                                            case 4:$sts="ANULADO";$clr="";break;
                                            case 5:$sts="SUSPENDIDO";$clr="#6c00ff";break;
                                            case 6:$sts="NO-APROBADO";$clr="#bc0000";break;
                                        }
                                        $rstRef=pg_fetch_array($Set->list_one_data_by_id("erp_productos",$rst[ped_d]));                                        
                                        if($rstRef[ids]==1)
                                        {
                                           $seccion="INDUSTRIAL";
                                        }else{
                                           $seccion="CONFECCION"; 
                                        }
                                        
                                    ?>
                                    <tr>
                                        <td><?php echo $cn?></td>   
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $rst[ped_a]?></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $rst[ped_b]?></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $rst[ped_c]?></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $seccion?></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $rstRef[2]?></td>
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,1)" ><?php echo $rstRef[7]?></td>
                                        <td align="center" style=" text-align:center;font-weight:bolder;color:<?php echo $clr;?>" ><?php echo $sts?></td>
                                        <td align="right"  >
                                           <?php 
                                           if($Prt->add==0)
                                           {
                                               ?>
                                                  <a href="#"  class="act_btn" title="Realizar Egreso de Bodega" onclick="auxWindow(1,<?php echo $rst[id]?>,<?php echo $rst[ped_d]?>,0)">Egreso</a>
                                            <?php
                                           }    
                                           ?>

                                        </td>
                                     </tr>
                   <?php
                   }
                   ?>
                </tbody>
    </table>            
</body>    
</html>

