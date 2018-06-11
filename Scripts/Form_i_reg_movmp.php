<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set=new Set();
$num_trs=$_GET[num_trs];
if(isset($_GET[num_trs])){
    $rst_h=pg_fetch_array($Set->lista_mov_mp_codigo($num_trs));
    $no_trs=$_GET[num_trs];
    $trsid=$rst_h[trs_id];
    $cns=$Set->lista_mov_mp_codigo($num_trs);
}else{
 $rst_h[mov_fecha_trans]=date('Y-m-d');   
 $no_trs=null;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title></title>
<head>
<script>
$(function(){
    Calendar.setup({inputField:"mov_fecha_trans",ifFormat:"%Y-%m-%d",button:"im-mov_fecha_trans"}); 
    parent.document.getElementById('contenedor2').rows = "*,80%";
});
function save(num_trs){
 if(mov_documento.value.length==0)   
 {
     alert('Guia de Recepcion es campo obligatorio');
 }else if(mp_id.value==0){
     alert('Elija una materia prima');
 }else if(mov_cantidad.value.length==0){
     alert('Cantidad es campo obligatorio');
 }else if(mov_peso_total.value.length==0){
     alert('Peso Total es campo obligatorio');
 }else if(trs_id.value==0){
     alert('Elija una Transaccion');
 }else{
  pu=(mov_cantidad.value/mov_peso_total.value)   
  var data=Array(
  trs_id.value,
  mp_id.value,
  mov_documento.value,
  mov_num_trans.value,  
  mov_fecha_trans.value,
  mov_cantidad.value,
  mp_presentacion.value,
  mov_peso_total.value,
  mov_proveedor.value,
  pu )

          $.post("actions.php",{act:225,'data[]':data},
                 function(dt){
                 if(dt==0){
                     window.location="Form_i_reg_movmp.php?num_trs="+num_trs;
                 }else{
                     alert(dt);
                 }
          });      
  }        
}
function cerrar()
{
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="hidden";
grid=window.parent.frames[1].document.getElementById('grid');  
grid.style.visibility="hidden";
parent.document.getElementById('bottomFrame').src='';
parent.document.getElementById('contenedor2').rows = "*,0%";
}
function cancelar()
{
    nm_trs=mov_num_trans.value;

    if(nm_trs.length!=0)
    {
          $.post("actions.php",{act:24,nm_trs:nm_trs},
                 function(dt){
                         if(dt==0)
                         {
                             cerrar();
                         }else{
                             alert(dt);
                         }    
          });      
    }else{
      cerrar();  
    }    
}
function finalizar()
{
    cerrar();
    window.history.go(0);
}
function crea_codigo(fbc,tp)
{
          $.post("actions.php",{act:21,fbc:fbc,tp:tp },
                 function(dt){
                         mp_codigo.value=dt;
          });      
    
}
function del(id,num_trs)
{
    if(confirm("Desea Eliminar Este Elemento?")){
        
          $.post("actions.php",{act:23,id:id },
                 function(dt){
                     if(dt==0)
                     {
                         window.location="Form_i_reg_movmp.php?num_trs="+num_trs;     
                     }    
                    
          });      
      }
}
function datos(id){
          $.post("actions.php",{act:26,mp:id},
                 function(dt){
                     det=dt.split('&');
                     mp_ref.innerHTML=det[0];
                     mov_unidad.innerHTML=det[2];
                     mp_presentacion.value=det[3];
          });      
}
function num_trs(trs){
          $.post("actions.php",{act:28,id:trs},
                 function(dt){
                 mov_num_trans.value=dt;    
          });      
}

</script>
<style>
    .reg{
        background:#015b85;
        color:white;
        font-weight:bolder;
        text-align:center; 
    }
</style>        
</head>

<body>
    <table id="tbl_form" cellpadding="0" >
        <thead>
                    <tr><th colspan="8" > 
                            REGISTRO DE MATERIA PRIMA
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th></tr>
        </thead>                    
      <tr>
          <td>Documento No:</td>
          <td><input type="text" size="25" id="mov_num_trans" readonly style="background:#ccc;" value="<?php echo $no_trs?>" /></td>
          <td>Fecha Ingreso:</td>
          <td><input type="text"  id="mov_fecha_trans" size="10" value="<?php echo $rst_h[mov_fecha_trans]?>"/>
               <img id='im-mov_fecha_trans' src='../img/calendar.png'  />
          </td>
          <td>
          Guia de Recepcion:
          </td>
          <td colspan="5">
              <input type="text"  id="mov_documento" value="<?php echo $rst_h[mov_documento]?>" />
          </td>
      </tr>
      <tr>
          <td>Transaccion</td>
          <td>
              <select id="trs_id" style="width:200px" onchange="num_trs(this.value)">
                  <option value="0">Elija Una Opcion</option>
                  <?php
                  $cns_trs=$Set->lista_transacciones();
                  while($rst_trs=pg_fetch_array($cns_trs)){
                      echo "<option value='$rst_trs[trs_id]'>$rst_trs[trs_descripcion]</option>";
                  }
                  ?>
              </select>
              <script>document.getElementById("trs_id").value=<?php echo $trsid?></script>
          </td>
          <td>
             Proveedor:
          </td>
          <td colspan="5">
                    <select id="mov_proveedor" style="width:200px" >
                        <option value="0">Elija Una Opcion</option>
                        <?php
                        $cns_cli = $Set->lista_clientes_tipo(0);
                        while ($rst_cli = pg_fetch_array($cns_cli)) {
                            echo "<option  value='$rst_cli[cli_id]'>$rst_cli[nombres]</option>";
                        }
                        ?>
                    </select>
              <script>
                  document.getElementById("mov_proveedor").value='<?php echo $rst_h[mov_proveedor]?>';
              </script>
          </td>
      </tr>
      <thead>
      <tr>
          <th>Item</th>
          <th>Descripcion</th>          
          <th>Referencia</th>
          <th>Presentacion</th>
          <th>Cantidad</th>
          <th>Unidad</th>
          <th>Peso T</th>
          <th>Accion</th>
      </tr>
      <tr>
          <th></th>
          <th>
              <select id="mp_id" style="width:200px" onchange="datos(mp_id.value)">
                  <option value="0">Elija un Opcion</option> 
                      <?php
                      $cns_trn=$Set->lista_mp0();
                            while($rst_trn=  pg_fetch_array($cns_trn))
                            {
                                echo "<option value='$rst_trn[mp_id]'  >$rst_trn[mp_referencia]</option>";
                            }
                      ?>
              </select>
          </th>
          <th id="mp_ref" style="color:black;font-size:12px;  "></th>
          <th>
              <input type="text" size="20" id="mp_presentacion"/>
          </th>
          <th><input type="text" size="5" id="mov_cantidad"  /></th>
          <th id="mov_unidad" style="color:black;font-size:12px;text-transform:lowercase" ></th>
          <th><input type="text" size="5" id="mov_peso_total"  /></th>
          <th>
            <?php 
            if($Prt->add==0 || $Prt->edition==0)
            {?>
              <button id="" onclick="save(mov_num_trans.value)" >+</button>
            <?php
            }
            ?>
          </th>
      </tr>
</thead>     
<tbody class="tbl_frm_aux" >     
      <?php
      $n=0;
      while($rst=pg_fetch_array($cns))
      {
          $n++;
          ?>
      <tr>
          <td><?php echo $n?></td>
          <td><?php echo $rst[mp_referencia]?></td>
          <td><?php echo $rst[mp_codigo]?></td>          
          <td><?php echo $rst[mov_presentacion]?></td>
          <td align="right"><?php echo number_format($rst[mov_cantidad],1)?></td>
          <td style="text-transform:lowercase"><?php echo $rst[mp_unidad]?></td>
          <td align="right"><?php echo number_format($rst[mov_peso_total],1)?></td>
          <td align="center">
          <?php
                if($Prt->delete==0)
                {?>
              <img src="../img/del_reg.png" width="14px" class="auxBtn" onclick="del(<?php echo $rst[mov_id]?>,'<?php echo $rst_h[mov_num_trans]?>')">
                <?php
                }?>
          </td>
      </tr>
          <?php
      }
      ?>
      <tr>
          <td colspan="8">
            <button id="cancel" onclick="finalizar()">Guardar</button>  
            <button id="cancel" onclick="cancelar()">Cancelar</button>
         </td>
      </tr>
</tbody>      
    </table>
</body>
</html>