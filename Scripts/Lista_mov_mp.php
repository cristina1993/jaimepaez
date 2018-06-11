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

if(isset($_GET[from]))
{
    $from=$_GET[from];
    $until=$_GET[until];
}else{
    $from=date('Y-m-d');
    $until=date('Y-m-d');
}
$cns=$Set->lista_movimientos_fecha($from,$until);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Inventarios</title>
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
    parent.document.getElementById('contenedor2').rows="*,50%";
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
                        look_menu();
                    break;  
                    case 5:
                        main.src='../Scripts/Lista_mov_mp.php?from='+from.value+'&until='+until.value; 
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
                <center class="cont_title" >Movimiento de Materia Prima</center>
                <center class="cont_finder">
                            <?php 
                            if($Prt->add==0){
                            ?>        
                                <a href="#"  class="btn" title="Nuevo Registro" onclick="auxWindow(4,0)">Nuevo</a>
                            <?php    
                            }
                            ?>
                            <font class="fnd" style="margin-left:1% ">
                            Desde:<input type="text" id="from" size="10" value="<?php echo $from?>" />
                                  <img src="../img/calendar.png" id="im_from" />
                            Hasta:<input type="text" id="until" size="10" value="<?php echo $until?>" />
                                  <img src="../img/calendar.png" id="im_until" />
                            </font>
                                    <a href="#" style="float:none "  class="btn" title="Nuevo Registro" onclick="auxWindow(5,0,0)" >Buscar</a>
                </center>
            </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bodega</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Procedencia/Destino</th>
                        <th>Transaccion</th>
                        <th>Referencia</th>
                        <th>Descripcion</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Valor U</th>
                        <th>Valor T</th>
                    </tr>                
                </thead>
                <tbody id="tbody">
                <?php
                $n=0;
                while($rst=pg_fetch_array($cns))
                {$n++;
                    switch($rst[mov_ubicacion])
                    {
                        case 1:$ubc="Costura";break;
                        case 2:$ubc="Bodega2";break;
                        case 3:$ubc="Bodega3";break;
                        case 4:$ubc="Otro";break;
                    }
                    switch($rst[mov_procedencia_destino])
                    {
                        case 1:$proc="Costura";break;
                        case 2:$proc="Bodega2";break;
                        case 3:$proc="Bodega3";break;
                        case 4:$proc="Otro";break;
                    }
                    switch ($rst[mov_unidad])
                    {
                        case 0:$und="Unidad";break;    
                        case 1:$und="Metros";break;    
                        case 2:$und="Kilos";break;    
                        case 3:$und="Rollos";break;    
                        case 4:$und="Otro";break;    
                    }
                    
                    $rst_trs=pg_fetch_array($Set->lista_una_transaccion($rst[trs_id]));
                ?>
                    <tr>
                                        <td ><?php echo $n?></td>   
                                        <td ><?php echo $ubc?></td>
                                        <td ><?php echo $rst[mov_documento]?></td>
                                        <td ><?php echo $rst[mov_fecha_trans]?></td>
                                        <td ><?php echo $proc?></td>
                                        <td ><?php echo $rst_trs[trs_descripcion]?></td>
                                        <td align="center" ><?php echo $rst[ins_a]?></td>
                                        <td align="left" ><?php echo $rst[ins_b]?></td>
                                        <td align="right" ><?php echo $rst[mov_cantidad]?></td>
                                        <td align="right" ><?php echo $und?></td>
                                        <td align="right" ><?php echo $rst[mov_v_unit]?></td>
                                        <td align="right" ><?php echo number_format($rst[mov_cantidad]*$rst[mov_unidad],1)?></td>
                                        
                   </tr>
                    <?php
                }
                ?>
                </tbody>
    </table>            
</body>    
</html>


