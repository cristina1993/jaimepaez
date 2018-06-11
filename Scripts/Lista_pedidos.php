<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$tbl_set='erp_pedidos_set';
$tbl=substr($tbl_set,0,-4);
$tbl_name='pedidos';
$tp='ped_tipo';
$tp0='ped_';
$Set= new Set();
$tipo=$_GET[tipo];
if(isset($_GET[tipo]))
{
  $fecha=date("Y-m-d");
  $from=date("Y-m-d", strtotime("$fecha - 7 day"));  
  $until=date("Y-m-d");
  $tipo=$_GET[tipo]=2;
  $ped=null;
  $prod=null;
  $sts='x';
  $date="pd.ped_b BETWEEN '$from' AND '$until'";
  $head=pg_fetch_array($Set->lista_one_data($tbl_set,$tipo));
  $tp_prod=explode('&',$head[1]);
  $cns=$Set->lista_table_by_tipo_finder($tipo,$code,$date,$ref);
}elseif(isset($_GET[search])){
   $tipo=$_GET[tp];
   $from=$_GET[from];
   $until=$_GET[until];
   $ped=strtoupper(trim($_GET[ped]));
   $prod=strtoupper(trim($_GET[prod]));
   $sts=$_GET[sts];
   if(!empty($ped))
   {
       $code="pd.ped_a like '%$_GET[ped]%'";
   }elseif(!empty($prod)){
       $ref="(pr.pro_a like '%$_GET[prod]%' AND  pd.ped_b BETWEEN '$_GET[from]' AND '$_GET[until]' )
             OR (pr.pro_b like '%$_GET[prod]%' AND  pd.ped_b BETWEEN '$_GET[from]' AND '$_GET[until]' ) ";
   }elseif($sts!="x")
   {
       $date="pd.ped_b BETWEEN '$_GET[from]' AND '$_GET[until]'  AND  pd.ped_f='$_GET[sts]' ";
   }else{
       $date="pd.ped_b BETWEEN '$_GET[from]' AND '$_GET[until]'";
   }
    $head=pg_fetch_array($Set->lista_one_data($tbl_set,$tipo));
    $tp_prod=explode('&',$head[1]);
    $cns=$Set->lista_table_by_tipo_finder($tipo,$code,$date,$ref);
}else{
  $fecha=date("Y-m-d");
  $from=date("Y-m-d", strtotime("$fecha - 7 day"));  
  $until=date("Y-m-d");
  $tipo=$_GET[tipo]=2;
  $ped=null;
  $prod=null;
  $sts='x';
  $date="pd.ped_b BETWEEN '$from' AND '$until'";
  $head=pg_fetch_array($Set->lista_one_data($tbl_set,$tipo));
  $cns=$Set->lista_table_by_tipo_finder($tipo,$code,$date,$ref);
  $tp_prod=explode('&',$head[1]);
}    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title><?php echo $tbl_name?></title>
<head>
<script>
$(function(){
   Calendar.setup({inputField:from,ifFormat:'%Y-%m-%d',button:im_from}); 
   Calendar.setup({inputField:until,ifFormat:'%Y-%m-%d',button:im_until});      
    $("#tbl").tablesorter(
   {widgets:['stickyHeaders'],
    sortMultiSortKey: 'altKey',
    widthFixed:true});
    parent.document.getElementById('bottomFrame').src='';
});
function look_menu(){
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="visible";
grid=document.getElementById('grid');  
grid.style.visibility="visible";}

var tbl_name='<?php echo $tbl_name?>';    
function auxWindow(a,tipo,id,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                main=parent.document.getElementById('mainFrame');    
                switch(a)
                {
                    case 0:
                        frm.src='../Scripts/Form_'+tbl_name+'.php?tipo='+tipo; 
                        look_menu();
                    break;    
                    case 1:
                        frm.src='../Scripts/Form_'+tbl_name+'.php?id='+id+'&tipo='+tipo+'&x='+x; 
                        if(x==0)
                        {
                            look_menu();
                        }
                    break;    
                    case 2:
                        main.src='../Scripts/Set_'+tbl_name+'.php?ol=<?php echo $_SESSION[ol]?>';
                    break; 
                    case 3:
                        frm.src='../Reports/rpt_orden_produccion.php?id='+id+'&tipo='+tipo+'&x='+x;
                    break;    
                    case 4:
                        frm.src='../Scripts/change_status.php?id='+id;
                    break;    
                    case 5:
                        alert('Ud no tiene permiso para esta opcion¡');
                    break;    
                    
                }
}
function del(id,dat)
{
          data=Array(dat);
          var r=confirm("Esta Seguro de eliminar este elemento?");
          if(r==true){
             $.post("actions.php",{act:15,id:id,'data[]':data},function(dt){
                 if(dt==0)
                  {
                      window.history.go(0);
                  }else{
                    alert(dt);
                  }
             });
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
    <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                            <?php
                                    $cnsTipos=$Set->lista_by_tipo($tbl_set);
                                    $n=0;
                                    while($rst=pg_fetch_array($cnsTipos))
                                    {
                                        $val=explode('&',$rst[$tp]);
                                        if($_GET[tipo]==$rst[ids])
                                        {
                                            $selected='selected';
                                        }else{
                                           $selected=''; 
                                        }
                                        ?>
                                            <font class="sbmnu" onclick="loadData(<?php echo $rst[ids]?>,0)" ><?php echo $val[9]?></font>
                                        <?php
                                        $n++;
                                    }
                            ?>
                            <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                            <?php 
                            if($Prt->special==0)
                            {?>
                                <img class="auxBtn" src="../img/set.png" onclick="auxWindow(2)" width="16px" />
                            <?php
                            }?>
                </center>
                <center class="cont_title" ><?php echo $tbl_name?></center>
                <center class="cont_finder">
                            <?php 
                            if($Prt->add==0)
                            {?>
                    <a href="#" class="btn" style="float:left "  title="Nuevo Registro" onclick="auxWindow(0,<?php echo $_GET[tipo]?>)" >Nuevo</a>
                            <?php
                            }
                            ?>
                            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-top:3px;margin-bottom:13px;">
                                <font>Desde:</font>
                                <input type="text" id="from" name="from" size="7" value="<?php echo $from?>" />
                                <img id="im_from" src='../img/calendar.png' />
                                <font>Hasta:</font>
                                <input type="text" id="until" name="until" size="7" value="<?php echo $until?>" />
                                <img id="im_until" src='../img/calendar.png' />
                                <input type="hidden" name="tp" size="15" value="<?php echo $tipo?>" />
                                <select name="sts">
                                    <option value='x' >Estado</option>
                                    <option value=0 >Registrado</option>
                                    <option value=1 >Aprobado</option>
                                    <option value=2 >Produccion</option>
                                    <option value=3 >Terminado</option>
                                    <option value=4 >Anulado</option>
                                    <option value=5 >Suspendido</option>
                                    <option value=6 >No aprobado</option>
                                </select>
                                Pedido:<input type="text" name="ped" size="15" />
                                Producto:<input type="text" name="prod" size="15" />
                                <input class="btn" style="position:absolute;margin-to:10px;  " type="submit" name="search" value="Buscar"/>
                            </form>  
                </center>
            </caption>
        
        
        
        
                <caption  class="finder">
                        <center style="text-align:left;margin-top:3px;border-top:double 3px #ccc  ">
                        </center>
               </caption>
                <thead>
                    <tr>
                        <th colspan="6"></th>  
                       <?php
                       if($tipo==1)
                       {
                        ?>
                        <th colspan="2">Proceso1</th>
                        <th colspan="2">Proceso2</th>
                        <th colspan="2">Proceso3</th>
                        <?php
                       }else{
                        ?>
                        <th colspan="2">Corte</th>
                        <th colspan="2">Costura</th>
                        <th colspan="2">Empaque</th>
                        <?php
                       }
                        ?>
                        <th colspan="2"></th>  
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Fecha de Entrega</th>
                        <th>Referencia</th>
                        <th>Solicitado</th>
                        <th>Avance</th>
                        <th>Faltante</th>
                        <th>Avance</th>
                        <th>Faltante</th>
                        <th>Avance</th>
                        <th>Faltante</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>                
                </thead>
                <tbody id="tbody">
                <?php
                $cn=0;
                while($rst=pg_fetch_array($cns))
                {$cn++;
                    ?>
                    <tr>
                                        <td onclick="auxWindow(1,<?php echo $rst[ids]?>,<?php echo $rst[id]?>,1)" ><?php echo $cn?></td>   
                                        <?php
                                        $n=2;
                                        while($n<6)
                                        {
                                            $file=explode('&',$head[$n]);
                                            if(!empty($file[9]) && $file[3]=='0' )
                                            {
                                                if($file[2]=='I')
                                                {
                                                 $value=$rst[$file[8]];
                                                 $rst[$file[8]]="<img src='$value' width=64px />";   
                                                } 
                                                if($file[2]=='E')
                                                {
                                                 $rstEnlace=pg_fetch_array($Set->list_one_data_by_id($file[6],$rst[$file[8]]));
                                                 $rst[$file[8]]=$rstEnlace[2];
                                                }
                                                ?>
                                                    <td onclick="auxWindow(1,<?php echo $rst[ids]?>,<?php echo $rst[id]?>,1)" ><?php echo $rst[$file[8]]?></td>
                                                <?php
                                            }    
                                            $n++;
                                        }
                                        $edt=0;
                                        $sol=$rst[ped_e1]+$rst[ped_e2]+$rst[ped_e3]+$rst[ped_e4];
                                        $file=explode('&',$head[7]);
                                        switch ($rst[$file[8]])
                                        {
                                            case 0:$sts="REGISTRADO";$clr="#000";break;
                                            case 1:$sts="APROBADO";$clr="#f37e00";break;
                                            case 2:$sts="PRODUCCION";$clr="#00cd66";$edt=1;break;
                                            case 3:$sts="TERMINADO";$clr='#00b2ee';$edt=1;break;
                                            case 4:$sts="ANULADO";$clr="#194052";break;
                                            case 5:$sts="SUSPENDIDO";$clr="#6c00ff";break;
                                            case 6:$sts="NO-APROBADO";$clr="#bc0000";break;
                                        }
                                        
                                        $rstP1=pg_fetch_array($Set->list_produccion_pedido_tipo($rst[id],0));
                                        $rstP2=pg_fetch_array($Set->list_produccion_pedido_tipo($rst[id],1));
                                        $rstP3=pg_fetch_array($Set->list_produccion_pedido_tipo($rst[id],2));
                                        
                                        if($Prt->special==0)
                                        {
                                            $a=4;
                                        }else{
                                            $a=5;
                                        }
                                        ?>
<!--                                    Produccion por proceso            -->
                                        <td onclick="auxWindow(1,<?php echo $rst[ids]?>,<?php echo $rst[id]?>,1)" ><?php echo number_format($sol,1)?></td>
                                        <td align="right"> <?php echo number_format($rstP1[sum],1)?></td>
                                        <td align="right"> <?php echo number_format($sol-$rstP1[sum],1)?></td>
                                        <td align="right"> <?php echo number_format($rstP2[sum],1)?></td>
                                        <td align="right"> <?php echo number_format($sol-$rstP2[sum],1)?></td>
                                        <td align="right"> <?php echo number_format($rstP3[sum],1)?></td>
                                        <td align="right"> <?php echo number_format($sol-$rstP3[sum],1)?></td>
                                        <td align='center' onclick="auxWindow(<?php echo $a?>,0,<?php echo $rst[id]?>)" style="font-family:'arial';font-size:12px;font-weight:bolder;color:<?php echo $clr;?>;text-transform:capitalize; "><?php echo $sts?></td>
                                        <td align="right">
                                            <?php
                                            if($Prt->edition==0 && $edt==0)
                                            {?>
                                            <img class="imbtn" src="../img/upd.png" title="Editar Pedido" onclick="auxWindow(1,<?php echo $rst[ids]?>,<?php echo $rst[id]?>,0)">
                                            <?php
                                            }
                                            if($Prt->report==0)
                                            {?>
                                            <img class="imbtn" src="../img/orden.png" title="Orden de Produccion" onclick="auxWindow(3,<?php echo $rst[ids]?>,<?php echo $rst[id]?>,0)">
                                            <?php
                                            }
                                            if($_SESSION['usuid']==1 || $_SESSION['usuid']==3 || $_SESSION['usuid']==5)
                                            {?>
                                              <img class="imbtn" src="../img/b_delete.png" title="Eliminar Pedido" onclick="del(<?php echo $rst[id]?>,'<?php echo $rst[$tp0.'a']?>')">
                                              <?php
                                            }?>
                                       </td>
                                    </tr>
                    <?php
                }
                ?>
                </tbody>
    </table>            
</body>    
</html>

