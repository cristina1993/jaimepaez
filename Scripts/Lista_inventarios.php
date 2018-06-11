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

if(isset($_GET[pf]))
{
    if(!empty($_GET[pf]) && empty($_GET[pu]))
    {
        $_GET[pu]=$_GET[pf];
        $cns=$Set->lista_insumos_rango($_GET[pf],$_GET[pu]);
    }elseif(empty($_GET[pf]) && empty($_GET[pu])){
        $cns=$Set->lista_insumos();
    }
    $from=$_GET[fecha];
    $ubc=$_GET[ubic];
}else{
    $from=date('Y-m-d');
    $cns=$Set->lista_insumos();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Inventarios</title>
<head>
<script>
$(function(){
   Calendar.setup({inputField:from,ifFormat:'%Y-%m-%d',button:im_from}); 
    $("#tbl").tablesorter(
   {widgets:['stickyHeaders'],
    sortMultiSortKey: 'altKey',
    widthFixed:true});
    parent.document.getElementById('bottomFrame').src='';
    parent.document.getElementById('contenedor2').rows="*,0%";
});
function look_menu(){
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="visible";
grid=document.getElementById('grid');  
grid.style.visibility="visible";}

function auxWindow(a,id,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                main=parent.document.getElementById('mainFrame');    
                switch(a)
                {
                    case 0:
                        window.location='../Scripts/Lista_egreso_bodega_mp.php'; 
                    break;
                    case 1:
                        frm.src='../Scripts/Form_mov_mp_view.php?cod='+id+'&x='+x; 
                    break;  
                    case 2:
                        window.location='../Scripts/Lista_mov_mp.php'; 
                    break;    
                    case 3:
                        window.location='../Scripts/Lista_inventarios.php'; 
                    break;    
                    case 4:
                        frm.src='../Scripts/Form_mov_mp.php'; 
                    break;    
                    case 5:
                        window.location='../Scripts/Lista_inventarios.php?pf='+p_from.value+'&pu='+p_until.value+'&fecha='+from.value+'&ubic='+ubicacion.value;  
                    break;    
                    
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
                            <font class="sbmnu" onclick="auxWindow(0,0)" >Egreso Bodega MP</font>
                            <font class="sbmnu" onclick="auxWindow(2,0)" >Materia Prima</font>
                            <?php 
                            if($Prt4->show==0 || $Prt5->show==0 || $Prt6->show==0){
                            ?>        
                                <font class="sbmnu" onclick="" >Producto Terminado</font>
                            <?php    
                            }
                            ?>
                            <br/><br/>
                            <?php 
                            if($Prt1->show==0){
                            ?>        
                                <font class="sbmnu" onclick="auxWindow(2,0)" >Movimientos</font>
                            <?php    
                            }
                            ?>
                            <?php 
                            if($Prt2->show==0){
                            ?>        
                                <font class="sbmnu" onclick="auxWindow(3,0)" >Inventario</font>
                            <?php    
                            }
                            ?>
                            <?php 
                            if($Prt3->show==0){
                            ?>        
                                <font class="sbmnu" onclick="#" >Kardex</font>
                            <?php    
                            }
                            ?>
                            <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >Inventarios</center>
                <center class="cont_finder">
                            <font style="margin-left:1% ">
                            Fecha:<input type="text" id="from" size="10" value="<?php echo $from?>" />
                                  <img src="../img/calendar.png" id="im_from" />
                        Ubicacion:<select id="ubicacion" >
                                      <option value="0">Todas</option>
                                      <option value="1">Bodega1</option>
                                      <option value="2">Bodega2</option>
                                      <option value="3">Bodega3</option>
                                  </select>
                            </font>
                            <font  style="margin-left:3% ">
                            Producto:<input type="text" id="p_from" size="14" />
                            <input  type="hidden" id="p_until" size="14" />
                            </font>        
                                    <a href="#" style="float:none "  class="btn" title="Nuevo Registro" onclick="auxWindow(5,0,0)" >Buscar</a>
                </center>
            </caption>
                <thead>
                    <tr>
                        <th colspan="3">Producto</th>
                        <?php
                        switch($ubc)
                        {
                            case 0:
                                echo "<th colspan='3'>Bodega1</th>";
                                echo "<th colspan='3'>Bodega2</th>";
                                echo "<th colspan='3'>Bodega3</th>";
                            break;
                            case 1:echo "<th colspan='3'>Bodega1</th>";break;
                            case 2:echo "<th colspan='3'>Bodega2</th>";break;
                            case 3:echo "<th colspan='3'>Bodega3</th>";break;
                        }
                        
                        ?>
                        <th colspan="3">Total</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Codigo</th>
                        <th>Referencia</th>
                        <?php
                        switch($ubc)
                        {
                            case 0:
                                ?>
                                    <th>Cant</th>
                                    <th>V.Unit $</th>
                                    <th>V.Tot $</th>
                                    <th>Cant</th>
                                    <th>V.Unit $</th>
                                    <th>V.Tot $</th>
                                    <th>Cant</th>
                                    <th>V.Unit $</th>
                                    <th>V.Tot $</th>
                                <?php
                            break;
                           default:
                                ?>
                                    <th>Cant</th>
                                    <th>V.Unit $</th>
                                    <th>V.Tot $</th>
                                <?php
                            break;
                        }
                        
                        ?>
                        <th>Cant</th>
                        <th>V.Unit $</th>
                        <th>V.Tot $</th>
                    </tr>                
                </thead>
                <tbody>
                    <?php 
                    $n=0;
                    while($rst=pg_fetch_array($cns))
                    {$n++;
                    
                        switch($ubc)
                        {
                            case 0:
                                    $cns_b1=$Set->lista_inventario_ins_bodega($rst[id],'1',$from);
                                    $tb1=0;$vu1=0;$tvu1=0;
                                    while($rst_b1=pg_fetch_array($cns_b1))
                                    {
                                        switch($rst_b1[trs_operacion])
                                        {
                                            case 0:$tb1+=$rst_b1[mov_cantidad];$vu1+=$rst_b1[mov_v_unit];break;    
                                            case 1:$tb1-=$rst_b1[mov_cantidad];break;    
                                        }
                                        if($vu1==0){$tvu1=0;}else{$tvu1=$tb1/$vu1;}    
                                    }
                                    $cns_b2=$Set->lista_inventario_ins_bodega($rst[id],'2',$from);
                                    $tb2=0;$vu2=0;$tvu2=0;
                                    while($rst_b2=pg_fetch_array($cns_b2))
                                    {
                                        switch($rst_b2[trs_operacion])
                                        {
                                            case 0:$tb2+=$rst_b2[mov_cantidad];$vu2+=$rst_b2[mov_v_unit];break;    
                                            case 1:$tb2-=$rst_b2[mov_cantidad];break;    
                                        }
                                        if($vu2==0){$tvu2=0;}else{$tvu2=$tb2/$vu2;}    
                                    }
                                    $cns_b3=$Set->lista_inventario_ins_bodega($rst[id],'3',$from);
                                    $tb3=0;$vu3=0;$tvu3=0;
                                    while($rst_b3=pg_fetch_array($cns_b3))
                                    {
                                        switch($rst_b3[trs_operacion])
                                        {
                                            case 0:$tb3+=$rst_b3[mov_cantidad];$vu3+=$rst_b3[mov_v_unit];break;    
                                            case 1:$tb3-=$rst_b3[mov_cantidad];break;    
                                        }
                                        if($vu3==0){$tvu3=0;}else{$tvu3=$tb3/$vu3;}    
                                    }
                            break;
                            case 1:
                                    $cns_b1=$Set->lista_inventario_ins_bodega($rst[id],'1',$from);
                                    $tb1=0;$vu1=0;$tvu1=0;
                                    while($rst_b1=pg_fetch_array($cns_b1))
                                    {
                                        switch($rst_b1[trs_operacion])
                                        {
                                            case 0:$tb1+=$rst_b1[mov_cantidad];$vu1+=$rst_b1[mov_v_unit];break;    
                                            case 1:$tb1-=$rst_b1[mov_cantidad];break;    
                                        }
                                        if($vu1==0){$tvu1=0;}else{$tvu1=$tb1/$vu1;}    
                                    }
                            break;
                            case 2:
                                    $cns_b2=$Set->lista_inventario_ins_bodega($rst[id],'2',$from);
                                    $tb2=0;$vu2=0;$tvu2=0;
                                    while($rst_b2=pg_fetch_array($cns_b2))
                                    {
                                        switch($rst_b2[trs_operacion])
                                        {
                                            case 0:$tb2+=$rst_b2[mov_cantidad];$vu2+=$rst_b2[mov_v_unit];break;    
                                            case 1:$tb2-=$rst_b2[mov_cantidad];break;    
                                        }
                                        if($vu2==0){$tvu2=0;}else{$tvu2=$tb2/$vu2;}    
                                    }
                            break;
                            case 3:
                                    $cns_b3=$Set->lista_inventario_ins_bodega($rst[id],'3',$from);
                                    $tb3=0;$vu3=0;$tvu3=0;
                                    while($rst_b3=pg_fetch_array($cns_b3))
                                    {
                                        switch($rst_b3[trs_operacion])
                                        {
                                            case 0:$tb3+=$rst_b3[mov_cantidad];$vu3+=$rst_b3[mov_v_unit];break;    
                                            case 1:$tb3-=$rst_b3[mov_cantidad];break;    
                                        }
                                        if($vu3==0){$tvu3=0;}else{$tvu3=$tb3/$vu3;}    
                                    }
                            break;
                            
                        }
                        
                        ?>
                    <tr>
                            <td><?php echo $n;?></td>
                            <td><?php echo $rst[ins_a];?></td>
                            <td><?php echo $rst[ins_b];?></td>
                            
<?php
                        switch($ubc)
                        {
                            case 0:
                                ?>
                                <td align="right"><?php echo number_format($tb1,1);?></td>
                                <td align="right"><?php echo number_format($tvu1,1);?></td>
                                <td align="right"><?php echo number_format($tvu1*$tb1,1);?></td>
                                <td align="right"><?php echo number_format($tb2,1);?></td>
                                <td align="right"><?php echo number_format($tvu2,1);?></td>
                                <td align="right"><?php echo number_format($tvu2*$tb2,1);?></td>
                                <td align="right"><?php echo number_format($tb3,1);?></td>
                                <td align="right"><?php echo number_format($tvu3,1);?></td>
                                <td align="right"><?php echo number_format($tvu3*$tb3,1);?></td>
                                <?php
                            break;
                            case 1:
                                ?>
                                <td align="right"><?php echo number_format($tb1,1);?></td>
                                <td align="right"><?php echo number_format($tvu1,1);?></td>
                                <td align="right"><?php echo number_format($tvu1*$tb1,1);?></td>
                                <?php
                            break;
                            case 2:
                                ?>
                                <td align="right"><?php echo number_format($tb2,1);?></td>
                                <td align="right"><?php echo number_format($tvu2,1);?></td>
                                <td align="right"><?php echo number_format($tvu2*$tb2,1);?></td>
                                <?php
                            break;
                            case 3:
                                ?>
                                <td align="right"><?php echo number_format($tb3,1);?></td>
                                <td align="right"><?php echo number_format($tvu3,1);?></td>
                                <td align="right"><?php echo number_format($tvu3*$tb3,1);?></td>
                                <?php
                            break;
                        
                        }

?>                            
                            
                            <td align="right"><?php echo number_format($tb1+$tb2+$tb3,1);?></td>
                            <td align="right"><?php echo number_format($tvu1+$tvu2+$tvu3,1);?></td>
                            <td align="right"><?php echo number_format(($tvu1*$tb1)+($tvu2*$tb2)+($tvu3*$tb3),1);?></td>
                    </tr>        
                        <?php
                    }    
                    ?>
                </tbody>
    </table>            
</body>    
</html>


