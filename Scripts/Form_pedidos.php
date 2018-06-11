<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set= new Set();
$tbl_set='erp_pedidos_set';
$tbl=substr($tbl_set,0,-4);
$tbl_name='pedidos';
$id=$_GET[id];
$tipo=$_GET[tipo];
$files=pg_fetch_array($Set->lista_one_data($tbl_set,$tipo));
$get=0;
if(isset($_GET[id]))
{
 $get=1;
 $data=pg_fetch_array($Set->list_one_data_by_id($tbl,$id));
}else{
    if($tipo==1)
    {
      $tp='I';
    }elseif($tipo==2)
    {
      $tp='C';
    }    
    $sec=pg_fetch_array($Set->list_pedido_sec($tp));
    $sc=intval($sec[cod]+1);
    if($sc>=0 && $sc<10)
    {
        $o="00000";
    }elseif($sc>=10 && $sc<100)
    {
        $o="0000"; 
    }elseif($sc>=100 && $sc<1000)
    {
        $o="000";
    }elseif($sc>=1000 && $sc<10000)
    {
        $o="00";
    }elseif($sc>=10000 && $sc<100000)
    {
        $o="0";
    }elseif($sc>=100000 && $sc<1000000)
    {
        $o="";
    }
    
   $cod=$tp.$o.($sc);
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
            if(ped_b.value>ped_c.value)
            {
                alert("La fecha de entrega no puede ser menor que la fecha de orden");
            }else{
                $.post("actions.php",{act:5,'data[]':data,'field[]':file,tbl:table,id:id,s:'' },
                       function(dt){
                       if(dt==0)
                       {
                           window.history.go(0);
                       }else{
                           alert(dt);
                       }
                });
            }
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

function load_producto(prod,id2,op){
    
          $.post("actions.php",{act:10,id:prod,id2:id2,op:op},
                 function(dt){
                 left_tbl.innerHTML=dt;    
                 calc_telas();
          });      
}

function calculos(n,val)
{
   var mt = document.getElementsByName("mat0");
   var mtz = document.getElementsByName("mat"+n);
   var aux_mtz = document.getElementsByName("aux_mat"+n);
   var k=0;
   for(k=mtz.length-1;k>=1;--k)
   {
      sm=aux_mtz[k].value*val;
      mtz[k].value=sm.toFixed(1);
      if(mt[k-1].title==1)
       {
           
          $.post("actions.php",{act:13,id:mt[k-1].value,sec:k},
                 function(dt){
                            dat=dt.split('&'); 
                            sm=mtz[dat[2]].value;                            

                        if((cod_t1.innerHTML.length==0) || (cod_t1.innerHTML==dat[0])) {
                            cod_t1.innerHTML=dat[0];
                            ref_t1.innerHTML=dat[1];               
                            sol_t1.innerHTML=((sol_t1.innerHTML*1)+(sm*1)).toFixed(1);
                        }else if((cod_t2.innerHTML.length==0) || (cod_t2.innerHTML==dat[0])){
                            cod_t2.innerHTML=dat[0];
                            ref_t2.innerHTML=dat[1];               
                            sol_t2.innerHTML=((sol_t2.innerHTML*1)+(sm*1)).toFixed(1);
                        }else if((cod_t3.innerHTML.length==0) || (cod_t3.innerHTML==dat[0])){
                          cod_t3.innerHTML=dat[0];  
                          ref_t3.innerHTML=dat[1];               
                          sol_t3.innerHTML=((sol_t3.innerHTML*1)+(sm*1)).toFixed(1);
                        }
           

          });      
           
           
           
       }
   }
}

function calc_telas()
{
        cod_t1.innerHTML=null;
        cod_t2.innerHTML=null;
        cod_t3.innerHTML=null;
        ref_t1.innerHTML=null;
        ref_t2.innerHTML=null;
        ref_t3.innerHTML=null;
        sol_t1.innerHTML=null;
        sol_t2.innerHTML=null;
        sol_t3.innerHTML=null;
        calculos(1,ped_e1.value);
        calculos(2,ped_e2.value);
        calculos(3,ped_e3.value);
        calculos(4,ped_e4.value);        
}

        </script>
        <style>
            #ttl td{
font-weight:bolder;                   
                
            }            
        </style>       
</head>
<body onload="load_producto(ped_d.value,<?php echo $_GET[id]?>,1);">
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
                                        if($file[0]=='E' && !empty($file[9]) && $file[8]!='ped_e' )
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
                                                    $rdl="readOnly";   
                                                    if($file[8]=='ped_a' && $get==0)
                                                    {
                                                     $val=$cod;
                                                    }    
                                                    $input="<input $rdl class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    if($get==1)
                                                    {
                                                      $val=$data[$file[8]];  
                                                    }else{
                                                      $val=date("Y-m-d");  
                                                    }    
                                                    
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='YY-mm-dd' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%Y-%m-%d',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    if($file[6]=='0')
                                                    {
                                                      $cnsEnlace=$Set->listar($file[6]);  
                                                    }else{
                                                      $cnsEnlace=$Set->listOneById($file[7],$file[6]);  
                                                    }
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[2]</option>";
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
            <td id="left" valign="top" align="left">
                <table>
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='I' && !empty($file[9]) && $file[8]!='ped_e' )
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
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='YY-mm-dd' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                              Calendar.setup({inputField:$file[8],ifFormat:'%Y-%m-%d',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    if($file[6]==0)
                                                    {
                                                      $cnsEnlace=$Set->listar($file[6]);  
                                                    }else{
                                                      $cnsEnlace=$Set->listOneById($file[7],$file[6]);  
                                                    }
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]' onchange='load_producto(this.value,0,0)' style='width:150px'  >";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $rstTipo=  pg_fetch_array($Set->lista_one_data("erp_productos_set",$rstEnlace[ids]));
                                                        $tp=explode("&",$rstTipo[pro_tipo]);
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]' >$tp[9]==>$rstEnlace[7]</option>";
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
                <table id="left_tbl">
                    
                </table>
            </td>
            <td id="center" valign="top" align="left">
                <table cellspacing="4" cellpadding="2" style="width:460px ">
                    <tr id="ttl">
                        <td></td>
                        <td>TELA1</td>
                        <td>TELA2</td>
                        <td>TELA3</td>
                    </tr>
                    <tr>
                        <td>Codigo:</td>
                        <td id="cod_t1" ></td>
                        <td id="cod_t2"></td>
                        <td id="cod_t3"></td>
                    </tr>
                    <tr>
                        <td>REFERENCIA:</td>
                        <td id="ref_t1" ></td>
                        <td id="ref_t2"></td>
                        <td id="ref_t3"></td>
                    </tr>
                    <tr>
                        <td>INVENTARIO:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>SALDO</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>SOLICITADO</td>
                        <td id="sol_t1" align="right"></td>
                        <td id="sol_t2" align="right"></td>
                        <td id="sol_t3" align="right"></td>
                    </tr>
                    
                                    <?php
                                    $n=2;
                                    while($n<=count($files))
                                    {
                                        $file=explode('&',$files[$n]);
                                        if($file[0]=='C' && !empty($file[9]) && $file[8]!='ped_e')
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
                                                    $rdl="readOnly";   
                                                    if($file[8]=='ped_a' && $get==0)
                                                    {
                                                     $val=$cod;
                                                    }    
                                                    $input="<input class='elemento' $rdl id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='YY-mm-dd' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%Y-%m-%d',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    if($file[6]=='0')
                                                    {
                                                      $cnsEnlace=$Set->listar($file[6]);  
                                                    }else{
                                                      $cnsEnlace=$Set->listOneById($file[7],$file[6]);
                                                    }
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[2]</option>";
                                                    }    
                                                    $input.="</select>";
                                                break;  
                                            }

                                          ?>
                                            <tr>
                                            <td><?php echo $file[9].$req?></td>
                                            <td colspan="3">
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
                                        if($file[0]=='D' && !empty($file[9]) && $file[8]!='ped_e')
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
                                                    $rdl="readOnly";                                                       
                                                    if($file[8]=='ped_a' && $get==0)
                                                    {
                                                     $val=$cod;
                                                    }    
                                                    $input="<input $rdl class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='YY-mm-dd' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%Y-%m-%d',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    if($file[6]=='0')
                                                    {
                                                      $cnsEnlace=$Set->listar($file[6]);  
                                                    }else{
                                                      $cnsEnlace=$Set->listOneById($file[7],$file[6]);  
                                                    }
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[2]</option>";
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
                                        if($file[0]=='P' && !empty($file[9]) && $file[8]!='ped_e' )
                                        {
                                            if($file[5]==0){$req='<font class="req" >&#8727</font>';}else{$req='';}
                                            switch ($file[2]){
                                                case 'I':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                                break;
                                                case 'N':
                                                    $hdd="";$val=$data[$file[8]];
                                                    if($file[8]=='ped_f')
                                                    {
                                                        $hdd="hidden";
                                                        $file[9]="";
                                                        $req="";
                                                    }
                                                    if($get==0)
                                                    {
                                                      $val=0;  
                                                    }    

                                                    $input="<input class='elemento' $hdd id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/,".'""'." )'  />";
                                                break;    
                                                case 'C':
                                                    $val=$data[$file[8]];                                                    
                                                    $rdl="readOnly";   
                                                    if($file[8]=='ped_a' && $get==0)
                                                    {
                                                     $val=$cod;
                                                    }    
                                                    $input="<input class='elemento' $rdl id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                                break;  
                                                case 'F':
                                                    $val=$data[$file[8]];
                                                    $input="<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='YY-mm-dd' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%Y-%m-%d',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                                break;
                                                case 'E':
                                                    $val=$data[$file[8]];
                                                    if($file[6]=='0')
                                                    {
                                                      $cnsEnlace=$Set->listar($file[6]);  
                                                    }else{
                                                      $cnsEnlace=$Set->listOneById($file[7],$file[6]);  
                                                    }
                                                    $input="<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                                    $input.="<option value='0'>Ninguno</option>";
                                                    while($rstEnlace=pg_fetch_array($cnsEnlace))
                                                    {
                                                        $selected='';
                                                        if($rstEnlace[id]==$val)
                                                        {
                                                            $selected='selected';
                                                        }    
                                                        $input.="<option   $selected  value='$rstEnlace[id]'>$rstEnlace[2]</option>";
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
                            <button class="act_btn" id="save" onclick="save()">Guardar</button>
                            <?php
                            }?>
                            <button class="act_btn" id="cancel" onclick="cancelar()">Cancelar</button>
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