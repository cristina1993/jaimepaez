    <?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set=new Set();
$cod=$_GET[cod];
if(isset($_GET[cod]))
{
 $cns=$Set->lista_movimientos_inv_codigo($cod);
 $rst=pg_fetch_array($Set->lista_movimientos_inv_codigo($cod));
 $rst[mov_documento];
 if($rst[mov_tp_sec]==0){
    $chk="checked";
 }else{
    $chk=""; 
 }
$valida=1;
}else{
$valida=0;    
$rst[mov_fecha_trans]=date("Y-m-d");    
$secc=  pg_fetch_array($Set->lista_secuencia_movimiento());
$sc=($secc[mov_documento]+1);
    if($sc>=0 && $sc<10)
    {
        $tx="000000";
    }elseif($sc>=10 && $sc<=100){
        $tx="00000";
    }elseif($sc>=100 && $sc<=1000){
        $tx="0000";
    }elseif($sc>=1000 && $sc<=10000){
        $tx="000";
    }elseif($sc>=10000 && $sc<=100000){
        $tx="00";
    }elseif($sc>=100000 && $sc<=1000000){
        $tx="0";
    }elseif($sc>=1000000 && $sc<=10000000){
        $tx="";
    }
    
    $rst[mov_documento]=$tx.$sc;    
    $rdly="readonly";
    $chk="";
}    

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Movimientos de Inventarios</title>
<head>
    <script>
var valida='<?php echo $valida?>';        
$(function(){
  Calendar.setup({inputField:mov_fecha_trans,ifFormat:'%Y-%m-%d',button:im_mov_fecha_trans}); 
});
function save(id)
{
    if(mov_tp_sec.checked==true)
      {sec=0;}else{sec=1;}
   if(mov_procedencia_destino.value==0)
      {
       
       if(mov_procedencia_destino0.value.length!=0)
       {
         proc_dest=mov_procedencia_destino0.value;  
       }else{
         proc_dest="INVENTARIO";    
       }
       
      }else{
       proc_dest=mov_procedencia_destino.value;   
      }
      
   var data=Array(          trs_id.value,
                            mov_ubicacion.value,
                            mov_documento.value,
                            mov_prod_id.value,
                            0,
                            '000001',
                            mov_fecha_trans.value,
                            mov_cantidad.value,
                            proc_dest,
                            mov_unidad.value,
                            mov_v_unit.value,
                            sec,
                            valida );



                $.post("actions.php",{act:16,'data[]':data,id:id},
                       function(dt){
                           if(dt==0)
                           {
                             window.location="Form_mov_mp.php?cod="+mov_documento.value; 
                           }else{
                             alert(dt);
                           }
                });
                
}
var secuencia='<?php echo $rst[mov_documento]?>';
function habilta()
{
    if(mov_tp_sec.checked==true){
       mov_documento.readOnly=false;
       mov_documento.focus();
       mov_documento.select();
    }else{
       mov_documento.readOnly=true;
       mov_documento.value=secuencia;
    }
}
function calculo(cn,vu)
{
    vt=cn*vu;
    v_tot.innerHTML=vt;
}
function hbl_destino(vl)
{
    if(vl==0)
    {
        mov_procedencia_destino0.readOnly=false;
    }else{
        mov_procedencia_destino0.readOnly=true; 
        mov_procedencia_destino0.value=null;
    }
}
function validar(id)
{
    if(valida==1)
    {
        alert("Al realizar un registro ya ud ya no puede modificar el encabezado");
        document.getElementById(id).value=document.getElementById(id).lang;
        
    }else{
        document.getElementById(id).lang=document.getElementById(id).value;
    }    
}
function cancelar()
{
    if(valida==0)
    {
        if(confirm("No ha realizado ningun registro \n Esta seguro de finalizar? \n Ningun Dato se Guardara")==true){
            
            mnu=window.parent.frames[0].document.getElementById('lock_menu');  
            mnu.style.visibility="hidden";
            grid=window.parent.frames[1].document.getElementById('grid');  
            grid.style.visibility="hidden";
            parent.document.getElementById('bottomFrame').src='';
        }
    }else{
            mnu=window.parent.frames[0].document.getElementById('lock_menu');  
            mnu.style.visibility="hidden";
            grid=window.parent.frames[1].document.getElementById('grid');  
            grid.style.visibility="hidden";
            parent.document.getElementById('bottomFrame').src='';
            var f = new Date();
            desde=f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
            hasta=desde;
            parent.document.getElementById('mainFrame').src='../Scripts/Lista_mov_mp.php?from='+desde+'&until='+hasta;
        
    } 
}

    </script>
</head>

<body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="8" >Movimientos de Inventarios</th></tr>
            </thead>
            <tr style="">
                <td colspan="8">
                    Fecha:<input type="text" size="10" id="mov_fecha_trans" lang="<?php echo $rst[mov_fecha_trans]?>" value="<?php echo $rst[mov_fecha_trans]?>" onchange="validar(this.id)"  />
                    <img src="../img/calendar.png" id="im_mov_fecha_trans" />
          Ubicacion:<select id="mov_ubicacion" lang="<?php echo $rst[mov_ubicacion]?>" onchange="validar(this.id)" >
                        <option value="1">Costura</option>
                        <option value="2">Bodega2</option>
                        <option value="3">Bodega3</option>
                    </select>
                    <font style="float:right " >
                    Manual:<input type="checkbox" <?php echo $chk?> id="mov_tp_sec"  lang="<?php echo $rst[mov_tp_sec]?>"  onclick="habilta()" onchange="validar(this.id)" />                    
                    Documento:<input type="text" size="12" id="mov_documento" <?php echo $rdly?> lang="<?php echo $rst[mov_documento]?>"  value="<?php echo $rst[mov_documento]?>" onchange="validar(this.id)"  />
                    </font>
                </td>
            </tr>
            <tr>
                <td colspan="8">
                    <font >    
                    Tipo de Transaccion:
                    <select id="trs_id" lang="<?php echo $rst[trs_id]?>" style="width:250px; " onchange="validar(this.id)">
                        <?PHP
                        $cns_trs=$Set->lista_transacciones();
                        while($rst_trs=pg_fetch_array($cns_trs))
                        {
                            echo "<option value='$rst_trs[trs_id]' >$rst_trs[trs_codigo] - $rst_trs[trs_descripcion]</option>";
                        }
                        ?>
                    </select>
                    </font>
                    <font style="float:right">
                    Destino/Procedencia:
                    <select id="mov_procedencia_destino" lang="<?php echo $rst[mov_procedencia_destino]?>" onchange="validar(this.id);hbl_destino(this.value)" >
                        <option value="0">Otro</option>                        
                        <option value="1">Costura</option>
                        <option value="2">Bodega 2</option>
                        <option value="3">Bodega 3</option>
                    </select>
                    <input type="text" size="45" style="text-transform:uppercase" lang="<?php echo $rst[mov_procedencia_destino]?>" id="mov_procedencia_destino0" onchange="validar(this.id)" />
                    </font>
                    <script>
                       var pd='<?php echo $rst[mov_procedencia_destino] ?>';
                        
                        document.getElementById('mov_ubicacion').value=<?php echo $rst[mov_ubicacion]?>;
                        document.getElementById('trs_id').value=<?php echo $rst[trs_id]?>;
                        
                        if(pd=='1' || pd=='2' || pd=='3')
                        {
                            document.getElementById('mov_procedencia_destino').value=pd;
                            document.getElementById('mov_procedencia_destino0').value=null;
                        }else{
                            document.getElementById('mov_procedencia_destino').value='0';
                            document.getElementById('mov_procedencia_destino0').value=pd;
                        }
                    </script>    
                    
                </td>
            </tr>
            <thead>
        <tr>
            <th>Item</th>
            <th>Referencia</th>
            <th width="300px" >Descripcion</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>V.Unitario</th>
            <th>V.Total</th>
            <th>Acciones</th>
        </tr>
        </thead>            
<tbody class="tbl_frm_aux" >                 
            <tr>
                <td></td>
                <td>
                    <select id="mov_prod_id" style="width:200px ">
                        <?PHP
                        $cns_ins=$Set->lista_insumos();
                        while($rst_ins=pg_fetch_array($cns_ins))
                        {
                            echo "<option value='$rst_ins[id]' >$rst_ins[ins_a] - $rst_ins[ins_b]</option>";
                        }
                        ?>
                    </select>
                </td>
                <td></td>
                <td><input type="text" size="10" maxlength="5" id="mov_cantidad" style="text-align:right" onchange="calculo(this.value,mov_v_unit.value)" /></td>
                <td><select id="mov_unidad">
                        <option value="0">Unidad</option>
                        <option value="1">Metros</option>
                        <option value="2">Kilos</option>
                        <option value="3">Rollos</option>
                        <option value="4">Otro</option>
                    </select></td>
                    <td><input type="text" size="10" maxlength="5" id="mov_v_unit" style="text-align:right" onchange="calculo(mov_cantidad.value,this.value)" value="0" /></td>
                <td style="text-align:right;font-size:12px;font-weight:bolder " id="v_tot" ></td>
                <td>
                    <button id="save" style="float:right" onclick="save(0)">+</button>            
                </td>
            </tr>
            <?php
            $n=0;
                while($rst=pg_fetch_array($cns))
                {$n++;
                    switch ($rst[mov_unidad])
                    {
                        case 0:$und="Unidad";break;    
                        case 1:$und="Metros";break;    
                        case 2:$und="Kilos";break;    
                        case 3:$und="Rollos";break;    
                        case 4:$und="Otro";break;    
                    }
                    $rst_prod=pg_fetch_array($Set->list_one_data_by_id('erp_insumos',$rst[mov_prod_id]));
                    ?>
                        <tr>
                            <td align='center' ><?php echo $n?></td>
                            <td ><?php echo $rst_prod[ins_a]?></td>
                            <td ><?php echo $rst_prod[ins_b]?></td>
                            <td align='center' ><?php echo $rst[mov_cantidad]?></td>
                            <td align='center' ><?php echo $und?></td>
                            <td align='center' ><?php echo number_format($rst[mov_v_unit],1)." $"?></td>
                            <td align='center' ><?php echo number_format($rst[mov_cantidad]*$rst[mov_v_unit],1)." $"?></td>
                            <td></td>
                        </tr>
                    <?php
                }    
            ?>
                        <tr>
                            <td colspan="2">
                                 <button id="cancel" style="float:left" onclick="cancelar()">Finalizar</button>            
                            </td>
                            <td colspan="7"></td>                            
                        </tr>
        </tbody>
    </table>
</body>


</html>