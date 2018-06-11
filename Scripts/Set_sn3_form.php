<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/library.php';
$table='erp_sn3_set';
$Set= new Set();
$data=pg_fetch_all_columns($Set->list_structure($table));
$id=$_GET[id];
if(isset($_GET[id]))
{
  $rst=pg_fetch_array($Set->listOneById($id,$table));
}    
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<head>
<!--<meta charset=utf-8 />    -->
<script>
var cn=<?php echo count($data)?>;
var tbl='<?php echo $table?>';
var id='<?php echo $_GET[id]?>';
function save()
{
    var data= Array();
    var file=Array();
    var obj = document.getElementsByClassName("clase");
    var txt='';
    var i=0;
    while(i<obj.length)
    {
        var elem = document.getElementById(obj[i].id);
        if(elem.type=='checkbox')
        {
           if(elem.checked==true)
           {
             elem.value=0;    
           }else{
             elem.value=1;       
           }    
          
        }    
        txt+=elem.value+"&"; 
        var val=(obj[i].id).split("_")
        if(val[0]=="txt")
        {
          data.push(txt.substring(0,txt.length-1));
          file.push(elem.name);
          txt="";
        }
        
      i++;  
    };
  $.post("actions.php",{act:3,'data[]':data,'field[]':file,tbl:tbl,id:id,s:'s'},
         function(dt){
         if(dt==0)
         {
             cancelar();
         }else{
             alert(dt);
         }    
  });       
}
function cancelar()
{
mnu=window.parent.frames[0].document.getElementById('lock_menu');  
mnu.style.visibility="hidden";
grid=window.parent.frames[1].document.getElementById('grid');  
grid.style.visibility="hidden";
parent.document.getElementById('bottomFrame').src='';
}
function validaCampos(id)
{
  doc=document.getElementById(id);
  $.post("actions.php",{act:7,tbl:doc.value},
  function(dt){
   id2=id.split('_');   
   camp=document.getElementById('ec_'+id2[1]).innerHTML="<option value=0>Ninguno</option>"+dt;    
  });       
   
}
function submenu(id,e)
{
   mn=document.getElementById(id);
   mn.style.visibility='visible';
   mn.style.left=e.clientX;
   mn.style.top=e.clientY;
   id0=id.split('_');
   document.getElementById('txt_'+id0[1]).style.background='tan';
}
function cerrar(id)
{
   mn=document.getElementById(id);
   mn.style.visibility='hidden';
   id0=id.split('_');
   document.getElementById('txt_'+id0[1]).style.background='white';
}
</script>
<style>
    table{
      padding: 5 5 5 5;  
    }
    table tr td{
     background:white;    
    }
    .help{
     background:#ccc;
     padding:5 5 5 5 ;
     float:right; 
     color:black; 
    }
    .submenu{
        visibility:hidden; 
        position:absolute; 
        border:solid 2px tan; 
        box-shadow:1px 1px 8px 1px #ccc; 
    }
</style>
</head>
<body>
<table border="0" align="left" style="border:solid 1px #009acd">
    <tr><td align="center" colspan="30" style="padding: 5 5 5 5;background-color:#11658d;color:white;font-size:11px  ">Formulario de Configuracion <?php echo $_GET[x]?></td></tr>
    <tbody>
        <tr>
            <?php
                $row=0;$n=0;
                while($n<count($data))
                {
                    $nm=explode('_',$data[$n]);
                    if($row==3){
                       echo "</tr><tr>";$row=0;     
                    }
                    $value=explode('&',$rst[$data[$n]]);
                    ?>
                          <td><?php echo $nm[1].':'?></td>
                          <td>
                        <table class="submenu" id="<?php echo 'tbl_'.$n?>" cellpadding="2"  >
                            <thead>
                            <th align="center" colspan="2">PROPIEDADES <button style="position:absolute;top:0px; right:0px;cursor:pointer" onclick="cerrar('<?php echo 'tbl_'.$n?>')">&#X00d7;</button></th>
                            </thead>
                            <tr>
                                <td>Posicion</td>
                                <td>
                                <select title="Posicion en Formulario" class="<?php echo "clase"?>" id="<?php echo "h_".$n?>" style="width:100px "  >
                                  <option value="E">Encabezado</option>
                                  <option value="I">Izquierda</option>
                                  <option value="C">Centro</option>
                                  <option value="D">Derecha</option>
                                  <option value="P">Pie</option>
                                </select>
                                </td>
                            </tr>  
                            <tr>
                                <td>Tamaño:</td>
                                <td>
                                   <input title="Tamaño en px en pantalla" class="<?php echo "clase"?>" type="text" name="" id="<?php echo 's_'.$n?>" size="1"   />
                                </td>
                            </tr>
                            <tr>
                                <td>Tipo:</td>
                                <td>
                                        <select title="Tipo de dato que recibie" class="<?php echo "clase"?>" id="<?php echo 't_'.$n?>" >
                                            <option value="N">Numero</option>
                                            <option value="C">Caracter</option>
                                            <option value="I">Imagen</option>
                                            <option value="F">Fecha</option>
                                            <option value="E">Enlace</option>
                                        </select>
                                </td>
                            </tr>                            
                            <tr>
                                <td>Aperece en Lista:</td>
                                <td><input title="Aparece en Lista"  class="<?php echo "clase"?>" type="checkbox"  id="<?php echo 'l_'.$n?>"  /></td>
                            </tr>
                            <tr>
                                <td>Orden:</td>
                                <td><input title="Orden en Lista" class="<?php echo "clase"?>" type="text" id="<?php echo 'o_'.$n?>" size="1" /></td>
                            </tr>
                            <tr>
                                <td>Campo Requerido:</td>
                                <td><input title="Campo Requerido" class="<?php echo "clase"?>" type="checkbox" id="<?php echo 'r_'.$n?>"  /></td>
                            </tr>
                            <tr>
                                <td>Tabla de Enlace:</td>
                                <td>
                                    <select title="Tabla de Enlace" class="<?php echo "clase"?>" id="<?php echo 'et_'.$n?>"  onchange="validaCampos(this.id)" >
                                        <option value="0">Ninguno</option>
                                        <option value="erp_maquinas">Maquinas</option>
                                        <option value="erp_insumos">Insumos</option>
                                        <option value="erp_pedidos">Pedidos</option>
                                        <option value="erp_productos">Productos</option>
                                        <option value="erp_registros">Registros</option>                                        
                                        <option value="erp_sn1">Sn1</option>                                        
                                        <option value="erp_sn2">Sn2</option>
                                    </select>    
                                </td>
                            </tr>
                            <tr>
                                <td>Campo de Enlace:</td>
                                <td>
                                    <select title="Campo de Enlace" class="<?php echo "clase"?>" id="<?php echo 'ec_'.$n?>">
                                    </select> 
                                   <input class="<?php echo "clase"?>" id="<?php echo 'f_'.$n?>" type="hidden" value="<?php echo $data[$n];?>" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button style="cursor:pointer" onclick="cerrar('<?php echo 'tbl_'.$n?>')" >Aceptar</button>
                                </td>
                            </tr>
                        </table>
                              <input class="<?php echo "clase"?>" id="<?php echo 'txt_'.$n?>" name="<?php echo $data[$n]?>"  type="text" value="<?php echo $value[9]?>" size="50" />                              
                                <?php 
                                    if($nm[1]!='tipo')
                                    {
                                     ?>
                                        <img src="../img/setting.png" class="auxBtn" onclick="submenu('<?php echo 'tbl_'.$n?>',event)" width="16px">
                                     <?php
                                    }
                                ?>                              
                              
                          </td>
                            <script>
                                h='<?php echo "h_".$n?>';
                                s='<?php echo "s_".$n?>';
                                t='<?php echo "t_".$n?>';
                                l='<?php echo "l_".$n?>';
                                o='<?php echo "o_".$n?>';
                                r='<?php echo "r_".$n?>';                                
                                et='<?php echo "et_".$n?>';
                                ec='<?php echo "ec_".$n?>';
                                document.getElementById(h).value='<?php echo $value[0]?>';
                                document.getElementById(s).value='<?php if(empty($value[1])){echo 0;}else{echo $value[1];} ?>';
                                document.getElementById(t).value='<?php echo $value[2]?>';
                                if('<?php echo $value[3]?>'==0)
                                {
                                   document.getElementById(l).checked=true; 
                                }else{
                                   document.getElementById(l).checked=false;  
                                }    
                                document.getElementById(o).value='<?php if(empty($value[4])){echo 0;}else{echo $value[4];}?>';
                                if('<?php echo $value[5]?>'==0)
                                {
                                   document.getElementById(r).checked=true; 
                                }else{
                                   document.getElementById(r).checked=false;  
                                }
                                document.getElementById(et).value='<?php echo $value[6]?>';
                                doc=document.getElementById(et);
                                $.post("actions.php",{act:7,tbl:doc.value},
                                        function(dt){
                                         doc0=document.getElementById('<?php echo "ec_".$n?>');  
                                         doc0.innerHTML="<option value=0>Ninguno</option>"+dt;
                                         doc0.value='<?php echo $value[7]?>';
                                 });       
                            </script>
                    <?php
                   $row++;$n++; 
                }
            ?>
        </tr>
    </tbody>
    <tfoot>
        <td colspan="30">
           <?php 
            if($Prt->add==0 || $Prt->edition==0)
           {?>
              <button id="save" onclick="save()">Guardar</button>
             <?php
            }?>
           <button id="cancel" onclick="cancelar()">Cancelar</button> 
        </td>
    </tfoot>
</table>
<?php
if($_GET[x]==1)
{
    echo "<script> document.getElementById('save').hidden=true </script>";
}else{
    echo "<script> document.getElementById('save').hidden=false </script>";
}    
?>    
    
</body>
</html> 