<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set= new Set();
$data=pg_fetch_array($Set->list_one_data_by_id('erp_productos',$_GET[pro_id]));   
$data2=pg_fetch_array($Set->list_one_data_by_id('erp_pedidos',$_GET[ped_id]));      
$files=pg_fetch_array($Set->lista_one_data('erp_productos_set',$data[ids]));               
$rstRef=pg_fetch_array($Set->list_one_data_by_id("erp_productos",$data2[ped_d]));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Egreso de Materia Prima</title>
<head>
    <script>
function calculos(id)
{
   sl=document.getElementById("sol"+id);
   cnt=document.getElementById("cnt"+id);
   sd=document.getElementById("sld"+id);
   ent=document.getElementById("ent"+id);
   sd.innerHTML=((sl.innerHTML*1)-(cnt.value*1+ent.innerHTML*1)).toFixed(2);
}
    
function save(n)
{

var r=1;var sms;    
while(r<n)
{
  idt=document.getElementById("idt"+r);
  ins=document.getElementById("ins"+r);
  cnt=document.getElementById("cnt"+r);
  ent=document.getElementById("ent"+r);
  var data= Array(mov_ped_id.value,
                  ins.value,
                  mov_tipo_trans.value,
                 ((cnt.value*1)+(ent.innerHTML*1)));
          $.post("actions.php",{act:12,'data[]':data,id:idt.value },
                 function(dt){
                    if(dt!=0)
                    {
                     n=0;
                     alert(dt);
                    }    
          });      
                  
    r++;
}
if(n==0)
{
   alert(sms); 
}else{
    alert("Proceso Correcto");
   window.history.go(0);
}    
}
function cancelar()
{
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="hidden";
grid=window.parent.frames[1].document.getElementById('grid');  
grid.style.visibility="hidden";
parent.document.getElementById('bottomFrame').src='';
}


    </script>  
</head>
<body>
    <table id="tbl_form" cellpadding="0" >
        <thead>
            <tr>
                <th colspan="7" >DETALLE DE EGRESO DE BODEGA</th>
            </tr>
        </thead>
            <tr>
                <td></td>
                <td>Fecha Orden:</td>
                <td><?php echo $data2[ped_b]?></td>
                <td>Calidad:</td>
                <td>A</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td>Fecha Entrega:</td>
                <td><?php echo $data2[ped_c]?></td>
                <td>Orden:</td>
                <td>
                    <?php echo $data2[ped_a]?>
                    <input type="hidden" id="mov_ped_id" value="<?php echo $data2[id]?>" />
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td></td>
                <td>Cliente:</td>
                <td >Noperti</td>
                <td>Familia:</td>
                <td colspan="3"><?php echo $rstRef[2]?></td>
            </tr>
            <tr>
                <td></td>
                <td>Tipo de Transaccion:</td>
                <td colspan="1">
                    <select id="mov_tipo_trans">
                        <option value="0">Egreso a Consumo</option>
                    </select>
                </td>
                <td>Linea:</td>
                <td colspan="3"><?php echo $rstRef[7]?></td>
            </tr>
            <thead>
                <tr>
                    <th colspan="3"></th> 
                    <th colspan="4">Cantidades</th>
                </tr>
                <tr>
                <th>No</th>
                <th>Referencia</th>
                <th>Descripcion</th>
                <th>Solicitado</th>
                <th>Entregado</th>
                <th>Saldo</th>
                <th>Egreso</th>
                </tr>
            </thead>    
<tbody class="tbl_frm_aux" >                 
            <?php
               ?>
                                    <?php
                                    $n=2;
                                    $no=0;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='I' && !empty($file[9]) )
                                        {
                                        if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                        if($_REQUEST[op]==0)
                                        {
                                            $val=$data[$file[8]];                                            
                                        }else{
                                            $val=$data2[$file[8]];                                            
                                        }
                                            switch ($file[2]){
                                                case 'E':
                                                    $no++;
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $sol=  number_format($data2[$file[8].'1']+$data2[$file[8].'2']+$data2[$file[8].'3']+$data2[$file[8].'4'],2);
                                                            $dataInv=pg_fetch_array($Set->list_inv_pedido_ins($data2[id],$rstEnlace[id]));
                                                            $saldo=number_format($sol-$dataInv[mov_cantidad],2);                                                            
                                                            if(empty($dataInv[mov_cantidad]))
                                                            {
                                                                $dataInv[mov_cantidad]=0;
                                                            }    
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $no?>
                                                                    <input type="hidden" id="<?php echo "idt".$no ?>" value="<?php echo $dataInv[mov_id]?>" />
                                                                    <input type="hidden" id="<?php echo "ins".$no ?>" value="<?php echo $rstEnlace[id]?>" />
                                                                </td>
                                                                <td><?php echo $rstEnlace[ins_a]?></td>
                                                                <td><?php echo $rstEnlace[ins_b]?></td>
                                                                <td id="<?php echo "sol".$no ?>" align="right"><?php echo $sol?></td>
                                                                <td align="center" id="<?php echo "ent".$no ?>" ><?php echo number_format($dataInv[mov_cantidad],2)?></td>
                                                                <td id="<?php echo "sld".$no?>" align="right"><?php echo $saldo?></td>                                                                
                                                                <td align="center"><input id="<?php echo "cnt".$no ?>" style="text-align:right;" type="text" size="5" value="0" onkeyup='this.value=this.value.replace (/[^0-9-.]/,"")'   onchange="calculos(<?php echo $no?>)" /></td>
                                                            </tr>
                                                            <?php
                                                        }    
                                                    }    
                                                break;  
                                            }
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
            
            <tr>
                <td colspan="10">
                            <?php 
                            if($Prt->add==0 || $Prt->edition==0)
                            {?>
                            <button id="save" onclick="save(<?php echo $no?>)">Guardar</button>  
                            <?php
                            }?>
                            <button id="cancel" style="float:right" onclick="cancelar()">Cancelar</button>                            
<?php
if($_GET[x]==1)
{
    echo "<script> document.getElementById('save').hidden=true </script>";
}else{
    echo "<script> document.getElementById('save').hidden=false </script>";
}    
?>    
                    
                </td>
            </tr>
        </tbody>
    </table>    
</body>
</html>