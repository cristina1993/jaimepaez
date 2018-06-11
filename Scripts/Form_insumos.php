<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set= new Set();
$tbl_set='erp_insumos_set';
$tbl=substr($tbl_set,0,-4);
$tbl_name='insumos';
$id=$_GET[id];
$tipo=$_GET[tipo];
$files=pg_fetch_array($Set->lista_one_data($tbl_set,$tipo));
if(isset($_GET[id]))
{
 $data=pg_fetch_array($Set->list_one_data_by_id($tbl,$id));
}   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title><?php echo $tbl_name?></title>
<head>
       <script>
            var tbl='<?php echo $tbl_set?>';
            var id='<?php echo $_GET[id]?>';
            var tipo='<?php echo $_GET[tipo]?>';
            var table=tbl.substring(0,tbl.length-4);
function save()
{
    var data=Array(tipo);
    var file=Array('ids');
    //var obj = document.getElementsByTagName('input');
    var obj = document.getElementsByClassName('elemento');
    var i=0;
    var x=0;
    while(i<obj.length)
    {
        var elem = document.getElementById(obj[i].id);
        if(elem.lang==0 && elem.value.length==0)
        {
            x=1;
            break;
        }    
        if(elem.type=='file')
        {
            var img=document.getElementById('im'+elem.id);
            data.push(img.src);
        }else{
            data.push(elem.value.toUpperCase().trim());
        }
        file.push(obj[i].id);
      i++;  
    };
        if(x==0)
        {
          $.post("actions.php",{act:5,'data[]':data,'field[]':file,tbl:table,id:id,s:'' },
                 function(dt){
                 if(dt==0)
                 {
                     window.history.go(0);
                 }else{
                     alert(dt);
                 }
          });      
        }else{
            alert('Existen Campos Requerido vacios \n Favor Revise ');
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
function archivo(evt,imgid){
                  var files = evt.target.files;
                  for (var i = 0, f; f = files[i]; i++) {
                    if (!f.type.match('image.*')){continue;}
                    var reader = new FileReader();
                    reader.onload = (function(theFile) {
                        return function(e){
                         document.getElementById("im"+imgid).src=e.target.result;
                        };
                    })(f);
                    reader.readAsDataURL(f);
                  }
}

function val_fecha(id)
{
fecha=document.getElementById(id);
fch=fecha.value.split('/');
ano = fch[2];
mes = fch[1];
dia = fch[0];
        valor = new Date(ano, mes, dia);
        if( isNaN(valor) || (ano.length!=4) || (mes.length!=2) || (mes>12) || (dia.length!=2) || (dia>31) ) {
          alert('Fecha incorrecta');
          fecha.focus();
        }
}
        </script>
<!--<script type="text/javascript" src="../js/functions.js"></script>        -->
</head>
<body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" ><?php echo $tbl_name?></th></tr>
            </thead>
        <tr>
            <td id="head" colspan="3" valign="top" align="left">
                <table>
                    <tr>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='E' && !empty($file[9]) )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;
                                                case 'L':
                                                    

                                                break;    
                                            }
                                          ?>
                                            <td><?php echo $file[9].$req?></td>
                                            <td>
                                                <?php echo $input?>    
                                            </td>
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td id="left" valign="top" align="left">
                <table>
                    <tr>
                        <td></td>
                        <td></td>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='T' && !empty($file[9]) )
                                        {
                                          ?>
                                            <td><?php echo $file[9]?></td>
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                    </tr>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='I' && !empty($file[9]) )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option  $selected value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;  
                                            }

                                          ?>
                                            <tr>
                                            <td><?php echo $file[9]?></td>
                                            <td><?php echo $input?></td>
                                            </tr>                                            
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                        
                </table>
            </td>
            <td id="center" valign="top" align="left">
                <table>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='C' && !empty($file[9]) )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;  
                                            }

                                          ?>
                                            <tr>
                                            <td><?php echo $file[9].$req?></td>
                                            <td>
                                                <?php echo $input?>    
                                            </td>
                                            </tr>                                            
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                        
                </table>
            </td>
            <td id="right" valign="top" align="left">
                <table>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='D' && !empty($file[9]) )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;  
                                            }

                                          ?>
                                            <tr>
                                            <td><?php echo $file[9].$req?></td>
                                            <td>
                                                <?php echo $input?>    
                                            </td>
                                            </tr>                                            
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                        
                </table>
            </td>
        </tr>
        <tr>
            <td id="foot" colspan="3" valign="top" align="left">
                <table>
                    <tr>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='P' && !empty($file[9]) )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;  
                                            }

                                          ?>
                                            <td><?php echo $file[9].$req?></td>
                                            <td>
                                                <?php echo $input?>    
                                            </td>
                                          <?php
                                        }    
                                    $n++;                    
                                    }
                                    ?>            
                        
                    </tr>
                </table>
            </td>
        </tr>        
                    <tr>
                        <td colspan="3">
                            <?php 
                            if($Prt->add==0 || $Prt->edition==0)
                            {?>
                            <button id="save" onclick="save()">Guardar</button>
                            <?php
                            }?>
                            <button id="cancel" onclick="cancelar()">Cancelar</button>
                        </td>
                    </tr>                    
        
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