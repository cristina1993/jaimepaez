<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$tbl_set='erp_registros_set';
$tbl=substr($tbl_set,0,-4);
$tbl_name='registros';
$tp='reg_tipo';
$Set= new Set();
if(isset($_GET[txt]))
{
    $ref=strtoupper(trim($_GET[txt]));
    $ped=strtoupper(trim($_GET[txt]));
    $from=$_GET[from];
    $until=$_GET[until];
    $sts=$_GET[sts];
    $tp_reg=$_GET[tp_reg];
    $cns=$Set->list_aprobation_varios_registros($ref,$ped,$from,$until,$sts);
    
}else{
    $tp_reg=$_GET[tp_reg];
    $from=date("Y-m-d");
    $until=date("Y-m-d");
    $cns=$Set->list_aprobation_varios();
}
 switch($tp_reg)
 {
     case 0:
         $title="REGISTRO DE CORTE";
     break;    
     case 1:
         $title="REGISTRO DE COSTURA";
     break;    
     case 2:
         $title="REGISTRO DE EMPAQUE";
     break;    
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




var tbl_name='<?php echo $tbl_name?>';    
var tp_reg='<?php echo $tp_reg?>';    
function auxWindow(a,id,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                switch(a)
                {
                    case 1:
                        frm.src='../Scripts/Form_'+tbl_name+'.php?id='+id+'&tp_reg='+tp_reg+'&x='+x; 
                    break;    
                }
}
function loadData(reg)
{
      window.location='Lista_registros.php?tp_reg='+reg;  
    
}
</script>    
<style>
    .sbmnu{
         position:static;
         background:white;
         color:black;
         font-weight:bolder;
         padding:5 15; 
         float:left; 
         border: solid 1px black;
         border-radius:5px 5px 5px 5px; 
         cursor:pointer; 
         margin-left:1px; 
         display:inline-block; 
    }
    .sbmnu:hover{
         background:black;
         color:white;
         border: solid 1px white;
    }
</style>
</head>
<body>
<div id="grid" onclick="alert('¡Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')" ></div>
    <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                            <font class="sbmnu" onclick="loadData(0)" >Corte</font>                                            
                            <font class="sbmnu" onclick="loadData(1)" >Costura</font>                                            
                            <font class="sbmnu" onclick="loadData(2)" >Empaque</font>                                                                        
                            <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" ><?php echo $title?></center>
                <center class="cont_finder">
                        <form style="margin-left:0%;margin-top:5px;" action="" method="GET" id="frmSearch" name="frm1" >
                            <input type="hidden" size="10" value="<?PHP echo $tp_reg?>" name="tp_reg" id="tp_reg" />
                            Desde:<input type="text" size="10" value="<?PHP echo $from?>" name="from" id="from"  />
                            <img id="im_from" src='../img/calendar.png' />
                            Hasta:<input type="text" size="10" value="<?PHP echo $until?>" name="until" id="until"  />
                            <img id="im_until" src='../img/calendar.png' />
                            <select name="sts" id="sts">
                                <option value="0" >Estatus</option>
                                <option value="1" >APROBADO</option>
                                <option value="2" >PRODUCCION</option>
                                <option value="3" >TERMINADO</option>
                            </select>
                            Pedido:<input type="text" size="20" value="" name="txt" id="txt"  />                            
                                <a href="#" style="position:absolute " class="act_btn" title="Buscar" onclick="frmSearch.submit()" ><img src="../img/finder.png" /></a>
                        </form>
                </center>
            </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pedido</th>
                        <th>Fecha</th>
                        <th>Fecha de Entrega</th>
                        <th style="width:40%" >Referencia</th>
                        <th>Solicitado</th>
                        <th>Producido</th>                        
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
                                        $sol=$rst[ped_e1]+$rst[ped_e2]+$rst[ped_e3]+$rst[ped_e4];
                                        $rstRef=pg_fetch_array($Set->list_one_data_by_id("erp_productos",$rst[ped_d]));  
                                        $do=pg_fetch_array($Set->list_produccion_pedido_tipo($rst[id],$tp_reg));
                    ?>
                    <tr>
                                        <td><?php echo $cn?></td>   
                                        <td onclick="auxWindow(1,<?php echo $rst[id]?>,0)"><?php echo $rst[ped_a]?></td>
                                        <td onclick=""><?php echo $rst[ped_b]?></td>
                                        <td onclick=""><?php echo $rst[ped_c]?></td>
                                        <td onclick=""><?php echo $rstRef[2]?></td>
                                        <td onclick="" align="right" width="50px"><?php echo $sol?></td>
                                        <td onclick="" align="right" width="50px"><?php echo $do[sum]?></td>
                                        <td align="center"  style=" text-align:center;font-weight:bolder;color:<?php echo $clr;?>"><?php echo $sts?></td>
                                        <td align="center"  >
                                            <img src="../img/upd.png" onclick="auxWindow(1,<?php echo $rst[id]?>,1)" width="24px" title="Editar" />
                                        </td>
                   </tr>
                    <?php
                }
                ?>
                </tbody>
    </table>            
</body>    
</html>

