var table=tbl.substring(0,tbl.length-4);
function save()
{
    var data=Array(tipo);
    var file=Array('ids');
    var obj = document.getElementsByTagName('input');
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
            data.push(elem.value);
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

