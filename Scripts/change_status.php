<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set= new Set();
$id=$_GET[id];
$rst=pg_fetch_array($Set->list_one_data_by_id('erp_pedidos',$id));

switch ($rst[ped_f])
{
    case 0:$sts0="checked";break;
    case 1:$sts1="checked";break;
    case 2:$sts2="checked";break;
    case 3:$sts3="checked";break;
    case 4:$sts4="checked";break;
    case 5:$sts5="checked";break;
    case 6:$sts6="checked";break;
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Cambiar Estatus</title>
<head>
    <style>
        table tr td, table th{
           font-size:16px;  
           font-family:'arial';
           font-size:12px;
           font-weight:bolder;
        }
    </style>
    <script>
        id='<?php echo $id?>';
    function save()
    {
        if(sts0.checked==true){sts=0;txt_sts='Registrar';}    
        if(sts1.checked==true){sts=1;txt_sts='Aprobar';}    
        if(sts2.checked==true){sts=2;txt_sts='Produccion';}    
        if(sts3.checked==true){sts=3;txt_sts='Terminar';}    
        if(sts4.checked==true){sts=4;txt_sts='Anular';}    
        if(sts5.checked==true){sts=5;txt_sts='Suspender';}    
        if(sts6.checked==true){sts=6;txt_sts='No Aprobar';}    
        
          var r=confirm("Esta Seguro de "+txt_sts+" este Pedido?");
          if(r==true){
             $.post("actions.php",{act:11,id:id,sts:sts},function(dt){
                 if(dt==0)
                  {
                      window.history.go(0);
                  }else{
                    alert(dt);
                  }
             });
          }
    }
    </script>
</head>
<body>
    <table align="center" cellpadding="5" cellspacing="5">
        <thead>
            <th colspan="7">Cambiar Estatus Manual</th>
        </thead>
        <tr>
            <td style="color:#000">
                Registrado:<input <?php echo $sts0 ?> type="radio" name="sts" value="0" id="sts0" />
            </td>
            <td style="color:#f37e00">
                Aprobado:<input <?php echo $sts1 ?> type="radio" name="sts" value="1" id="sts1" />
            </td>
            <td style="color:#bc0000 ">
                No Aprobado:<input <?php echo $sts6 ?> type="radio" name="sts" value="6" id="sts6" />
            </td>
            <td style="color:#00cd66 ">
                Produccion:<input <?php echo $sts2 ?> type="radio" name="sts" value="2" id="sts2" />
            </td>
            <td style="color:#00b2ee">
                Terminado:<input <?php echo $sts3 ?> type="radio" name="sts" value="3" id="sts3" />
            </td>
            <td style="color:#194052">
                Anulado:<input <?php echo $sts4 ?> type="radio" name="sts" value="4" id="sts4" />
            </td>
            <td style="color:#6c00ff ">
                Suspendido:<input <?php echo $sts5 ?> type="radio" name="sts" value="5" id="sts5" />
            </td>
        </tr>
        <tr>
            <td align="center" colspan="7">
                <button onclick="save()">Aceptar</button> 
                <button>Cancelar</button> 
            </td>
        </tr>
    </table>
</body>
</html>