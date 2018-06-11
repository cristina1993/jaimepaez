<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set=new Set();
if(isset($_GET[id]) ){
$rst_d=pg_fetch_array($Set->lista_una_det_orden_compra($_GET[id]));        
$read="readonly";
}else{
    $tbl_hidden="hidden";
    $cn0="";
    $cn1="hidden";
    $rst_sec=pg_fetch_array($Set->lista_secuencial_orden(0));
    $rst_d[orc_fecha]=date("Y-m-d");
    $sec=($rst_sec[orc_codigo]+1);
    if($sec>=0 && $sec<10)
    {
       $tx_trs="0000"; 
    }elseif($sec>=10 && $sec<100){
       $tx_trs="000";  
    }elseif($sec>=100 && $sec<1000){
       $tx_trs="00";  
    }elseif($sec>=1000 && $sec<10000){
       $tx_trs="0";  
    }elseif($sec>=10000 && $sec<100000){
       $tx_trs="";  
    }
    $no_orden=$tx_trs.$sec;
    $rst_d[orc_det_id]=0;

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title></title>
<head>
<script>
function save(id){
    
                var f = new Date();
                var fecha= f.getFullYear() + "-" + (f.getMonth() +1) + "-" + f.getDate();
               if(cli_nombre.value.length==0){
                   alert('El Campo Proveedor es Obligatorio...');
                   cli_nombre.focus();
               }else if(mp_codigo.value.length==0){
                   alert('El Campo Referencia es Obligatorio...');
                   mp_codigo.focus();
               }else if(orc_det_guia.value.length==0){
                   alert('El Campo Guia es Obligatorio...');
                   orc_det_guia.focus();
               }else if(etq_peso.value.length==0){
                   alert('El Campo Peso es Obligatorio...');
                   etq_peso.focus();
               }else{
                   
                   if(orc_det_id.value==0){
                       
                                var data=Array(
                                emp_descripcion.value,        
                                cli_nombre.value,
                                orc_codigo.value,
                                mp_codigo.value,
                                orc_fecha.value,       
                                orc_det_guia.value,
                                etq_peso.value,                                
                                etq_bar_code.value  );
                                
                                $.post("actions.php",{act:45,'data[]':data,id:id},
                                       function(dt){
                                           dt0=dt.split('&');
                                       if(dt0[0]==0){
                                           window.location="Form_i_etq_orden.php?id="+dt0[1];
                                       }else{
                                           alert(dt);
                                       }
                                });
                       
                       
                       
                       
                   }else{
                                var data=Array(
                                orc_det_id.value,
                                1,
                                etq_peso.value,
                                fecha,
                                etq_bar_code.value,        
                                orc_det_guia.value);
                                $.post("actions.php",{act:33,'data[]':data,id:id},
                                       function(dt){
                                       if(dt==0){
                                           window.location="Form_i_etq_orden.php?id="+orc_det_id.value;
                                       }else{
                                           alert(dt);
                                       }
                                });
                        } 
                }  
                
}
function cerrar(){
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="hidden";
grid=window.parent.frames[1].document.getElementById('grid');  
grid.style.visibility="hidden";
parent.document.getElementById('bottomFrame').src='';
}
function finalizar(){
    cerrar();
    window.history.go(0);
}
function bar_code(){
    
    etq_bar_code.value=mp_codigo.value+orc_codigo.value+"-"+etq_peso.value;
}
function load_datos(id){
    
 $.post("actions.php",{act:44,id:id},
        function(dt){
            dat=dt.split('&');
            mp_referencia.value=dat[1];
            emp_descripcion.value=dat[3];
            orc_codigo.value=dat[4];
            if(dat[2].length==0){
               cli_nombre.placeholder="Elija un Cliente";             
               cli_nombre.readOnly=false;
            }else{
              cli_nombre.value=dat[2];              
              cli_nombre.placeholder=null;             
              cli_nombre.readOnly=true;
            }
            
       });      
}
</script>
<style>
    .sbtls{
        border-left:solid 1px #ccc;
        border-right:solid 1px #ccc;        
    }
    .sbtls input{
        text-align:right; 
    }
</style>        
</head>
<body>
    <table id="tbl_form" cellpadding="0" >
        <thead>
            <tr>
                    <th colspan="8" >FORMULARIO ETIQUETAS DE COMPRA</th>
            </tr>
        </thead>
      <tr>
          <td>Fabrica:</td>
          <td><input type="text" readonly id="emp_descripcion" value="<?php echo $rst_d[emp_descripcion]?>" /></td>
          <td>Proveedor:</td>
          <td>
              <input type="text" readonly  id="cli_nombre" list="list_clientes" value="<?php echo $rst_d[cli_nombre]?>" />
              <datalist id="list_clientes"  >
                                <?php
                                $cns_cli=$Set->lista_clientes_tipo(2);
                                while($rst_cli=pg_fetch_array($cns_cli)){
                                    if($rst_cli[cli_id]==$rst_h[cli_id])
                                    {
                                        $sel="selected";
                                    }else{
                                        $sel="";
                                    }    
                                    echo "<option $sel value='$rst_cli[cli_codigo]'>$rst_cli[cli_nombre]</option>";
                                }
                                ?>
              </datalist>
              
          </td>
          <td>Documento:</td>
          <td>
              <input type="text" readonly id="orc_codigo" value="<?php echo $rst_d[orc_codigo]?>" />
              <input type="text" readonly id="orc_det_id" value="<?php echo $rst_d[orc_det_id]?>" />
          </td>
      </tr>
      <tr>
          <td>Referencia:</td>
          <td>
              <input type="text" <?php echo $read?> id="mp_codigo" value="<?php echo $rst_d[mp_codigo]?>" list="list_mp" onchange="load_datos(this.value)" />
              <datalist id="list_mp">
              <?php
              $cns_mp=$Set->lista_mp0();
              while($rst_mp=pg_fetch_array($cns_mp)){
                  echo "<option value='$rst_mp[mp_codigo]'>$rst_mp[mp_codigo]  $rst_mp[mp_referencia]</option>";
              }
              ?>
              </datalist>    
          </td>
          <td>Descripcion:</td>
          <td><input type="text" readonly id="mp_referencia" value="<?php echo $rst_d[mp_referencia]?>" /></td>
          <td>Fecha de Orden:</td>
          <td><input type="text" readonly id="orc_fecha" value="<?php echo $rst_d[orc_fecha]?>" /></td>
      </tr>
      <tr>
          <td colspan="4" ></td>
          <td>Guia:</td>
          <td><input type="text" <?php //echo $read?> id="orc_det_guia" size="23" value="<?php echo $rst_d[orc_det_guia]?>" /></td>
      </tr>
      <thead>
            <tr>
                <th>Peso:</th>
                <th>
                    <input type="text"  id="etq_peso" size="10" onchange="bar_code()" />
                </th>
                <th>
                    <input type="text" readonly  id="etq_bar_code" size="20" />
                    <button onclick="save(0)" >+</button>                    
                </th>
            </tr>
            <tr>
                <th>No</th>
                <th>Peso</th>
                <th>Codigo</th>
            </tr>
      </thead>
      <tbody class="tbl_frm_aux" >                 
      <?php
      $cns=$Set->lista_etq_det($_GET[id]);
      $n=0;
      while($rst=pg_fetch_array($cns)){
          $n++;
          ?>
            <tr>
                <td><?php echo $n?></td>
                <td><?php echo number_format($rst[etq_peso],1)?></td>
                <td><?php echo $rst[etq_bar_code]?></td>
            </tr>
          <?php
      }
      
              ?>
          <tr>
              <td colspan="8">
                  <button id="save" onclick="finalizar()">Finalizar</button>  
                  <button id="cancel0" style="float:right" onclick="cerrar()">Cancelar</button>
              </td>
          </tr>
     </tbody>          
    </table>
</body>
</html>