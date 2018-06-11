<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$tbl_set='erp_sn1_set';
$tbl_name='sn1';
$tp='sn1_tipo';
$Set= new Set();
$cns_e=$Set->list_structure($tbl_set);    
$cns=$Set->listar($tbl_set,'s');
?>

<meta charset=utf-8 />
<title><?php echo $tbl_name?></title>
<script type="text/javascript">
var tbl='<?php echo $tbl_set?>';    
var tbl_name='<?php echo $tbl_name?>';
$(document).ready(function(){
        $("#table").tablesorter(
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
    
function auxWindow(a,id,x)
{
                frm=parent.document.getElementById('bottomFrame');    
                main=parent.document.getElementById('mainFrame');    
                switch(a)
                {
                    case 0:
                        frm.src='../Scripts/Set_'+tbl_name+'_form.php'; 
                        look_menu();
                    break;    
                    case 1:
                        frm.src='../Scripts/Set_'+tbl_name+'_form.php?id='+id+'&x='+x; 
                        if(x==0)
                        {
                            look_menu();
                       }
                    break;    
                    case 2:
                        main.src='../Scripts/Lista_'+tbl_name+'.php?ol=<?php echo $_SESSION[ol]?>'; 
                    break;    
                }
}

function del(id,dat)
{
    data=Array(dat);
          var r=confirm("Desea eliminar este registro?");
          if(r == true){
             $.post("actions.php",{act:4,id:id,tbl:tbl,'data[]':data},function(dt){   
                 if(dt==0)
                 {
                     window.history.go(0);
                 }else{
                     alert(dt);
                 }    
             }) 
          }
}

function actions(op)
{
old=editar.lang;

switch(op)
{
    case 0:
        var field=prompt('Nombre de la Nueva Propiedad','');
        if(field!=null)
          {
              if(field.length>0)
              {
                    $.post("actions.php",{act:0,field:field,tbl:tbl},function(dt){
                        if(dt==0)
                          {
                              window.history.go(0);
                          }else{
                              alert(dt);
                          }  
                    })
                 
              }else{
                alert('Valor Incorrecto Intente de Nuevamente');
              }            
          }
    break;    
    case 1:
        var field=prompt('Nuevo Nombre','');
        if(field!=null)
          {
              if(field.length>0)
              {
                 $.post("actions.php",{act:1,old:old,field:field,tbl:tbl},function(dt){
                     if(dt==0)
                       {
                           window.history.go(0);
                       }else{
                           alert(dt)
                       }  
                 }) 
              }else{
                alert('Valor Incorrecto Intente Nuevamente');
              }            
          }
    break;    
    case 2:
          var r=confirm("Si Elimina el campo no podra recuperar los datos registrados \n Esta Seguro de eliminar este elemento?");
          if(r == true){
            var field=prompt('Ingrese Codigo de Seguridad de Eliminacion','')
            if(field=='1'){

                 $.post("actions.php",{act:2,field:old,tbl:tbl},function(dt){
                         if(dt==0)
                           {
                               window.history.go(0);
                           }else{
                               alert(dt);
                           }  
                 })
                 
            }else{
                alert('Codigo Incorrecto');
            }
          }
    break;    
}
}

function sumbMenu(e,col)
{
                tls=document.getElementById('tools');
                var left = e.clientX;
                var top = e.clientY;
                tls.style.display='inline';
                tls.style.left=left;
                tls.style.top=top;
                editar.lang=col;
}
</script>
<body>
    <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>    
<table id="tools" style="position:absolute;display:none;"  >
<tbody>
<?php
if($Prt->edition==0)
{?>
   <tr><td id="editar" onclick="actions(1)" ><img src="../img/upd.png" />&nbsp;&nbsp;Renombrar</td></tr>
 <?php
}
if($Prt->delete==0)
{?>
  <tr><td id="eliminar"  onclick="actions(2)" ><img src="../img/b_delete.png" />&nbsp;&nbsp;Eliminar</td></tr>
 <?php
}
?>
<tr><td><button onclick="tools.style.display='none';">X</button></td></tr>
</tbody>
</table>
    <table align="left" id="dataTable" width="100%">
                <caption  class="finder">
                    <center style="height:30px ">
                        <?php   
                        if($Prt->add==0)
                        {?>
                          <img src="../img/new.png" class="auxBtn" title="Nuevo Campo" style="float:left" width="16px" onclick="actions(0)" />
                         <?php
                        }
                        if($Prt->add==0)
                        {?>
                          <img src="../img/add.png" class="auxBtn" title="Nuevo Registro" style="float:left" width="16px" onclick="auxWindow(0)" />
                         <?php
                        }
                        ?>
                        <font size="2">CONFIGURACION TABLA <?php echo $tbl_name?></font>
                        <img src="../img/print_iconop.png" title="Imprimir" class="auxBtn" width="16px" />
                        <img src="../img/table.png" title="Regresar a Tabla Productos" class="auxBtn" width="16px" onclick="auxWindow(2)" />
                    </center>
                </caption>
		<thead>
                    <tr>
                        <th>No</th>
                            <?php
                            while($rst_e=pg_fetch_array($cns_e))
                            {
                                $nm=explode('_', $rst_e[column_name]);
                            ?>
                             <th>
                                <?php
                                echo $nm[1];
                                if($nm[1]!='tipo' && $nm[1]!='a'  && $Prt->special==0)
                                {
                                  ?>
                                           <img src="../img/tool.png" width="10px" class="auxBtn" onclick="return sumbMenu(event,'<?php echo $rst_e[column_name]?>')" title="Herramientas de Campo" />
                                  <?php
                                }    
                                ?>
                             </th>
                            <?php                        
                            }
                            ?>                        
                       <th  align="Center"  >Acciones</th>
                    </tr>
                </thead>  
                <tbody>
                    <?php
                    $n=0;
                    while($rst=pg_fetch_array($cns))
                    {$n++;
                        $cns_e=$Set->list_structure($tbl_set);  
                        
                        ?>
                          <tr> 
                                <td><?php echo $n?></td>
                                <?php
                                while($rst_e=pg_fetch_array($cns_e))
                                {
                                    $value=explode('&',$rst[$rst_e[column_name]]);
                                    ?>
                                        <td onclick="auxWindow(1,<?php echo $rst[ids]?>,1)" ><?php echo $value[9]?></td>
                                    <?php
                                }
                                ?>
                                        <td align="center">
                                            <?php   
                                            if($Prt->edition==0)
                                            {?>
                                              <img src="../img/upd.png" width="22px" onclick="auxWindow(1,<?php echo $rst[ids]?>,0)">
                                             <?php
                                            }
                                            ?>
                                            <?php   
                                            if($Prt->delete==0)
                                            {?>
                                              <img src="../img/b_delete.png" onclick="del(<?php echo $rst[ids]?>,'<?php echo $rst[$tp]?>')">
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


