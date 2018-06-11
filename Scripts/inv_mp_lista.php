<?php
include_once '../Includes/permisos.php';
include_once '../Includes/library2.php';
include_once '../Clases/clsMateriaPrima.php';
$MP = new MateriaPrima();
if(isset($_GET[id])){
    
}else{
   $cns=$MP->lista_inv_mp();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Inventario Materia Prima</title>
        <script>
            $(function () {
                $("#tbl").tablesorter({widgets: ['stickyHeaders'], sortMultiSortKey: 'altKey', widthFixed: true});
            });
    
    
            function auxWindow(a,id){
                var boxH=$(window).height()*0.4;
                var boxW=$(window).width()*0.3;  
                var boxHF=(boxH-20)
                switch(a)
                {
                    case 0:
                        wnd='<iframe id="frmmodal" width="'+boxW+'" height="'+boxHF+'" src="inv_mp_form.php" frameborder="0" />';                    
                        break;     
                    case 1:
                        wnd='<iframe id="frmmodal" width="'+boxW+'" height="'+boxHF+'" src="inv_mp_form.php?id='+id+' " frameborder="0" />';                    
                        break;     
                    }
                    wind=$.fallr.show({
                        content     : "<font id='titulo_ventana'>REGISTRO DE INVENTARIO</font><br />"
                            +wnd,
                        width   : boxW,
                        height  : boxH,
                        duration: 0,
                        position: 'center',
                        buttons:{
                            button1:{
                                text:'&#X00d7;',
                                onclick:function(){
                                    $.fallr.hide(); 
                                }
                            },
                            b1:{
                                text:'Cancelar',
                                onclick:function(){
                                    $.fallr.hide(); 
                                }
                            },
                            b2:{
                                text:'Finalizar',
                                onclick:function(){
                                    $.fallr.hide(); 
                                    window.history.go(0); 
                                }
                            }
                            
                        }
                    });      
                }



                function del(id)
                {
    
                    //$.fallr.show({
                    //duration:0,
                    //position: 'center',    
                    //    buttons : {
                    //        button1 : {text: 'Yes',danger: true, onclick: function(){
                    //                $.post("actions.php",{ id:id,op:1 },function(data){
                    //                if(data==0)
                    //                  {
                    //                      row=document.getElementById('row_'+id);
                    //                      row.style.display='none';
                    //                      $.fallr.hide();
                    //                  }    
                    //                    })
                    //               }},
                    //        button2 : {text: 'Cancel'}
                    //    },
                    //    content : '<p>Eliminar?</p>',
                    //});
                    //

                    if(confirm('Desea Eliminar Este Item??')==true){
                        $.post("actions.php",{ id:id,op:1 },function(data){
                            if(data==0)
                            {
                                row=document.getElementById('row_'+id);
                                row.style.display='none';
                            }    
                        })
    
                    }
    
    
    
                }
                function imprimir()
                {
                    window.print();
                    return false;
                }
        
        
        
        </script>
        <style>
            #fallr-button-b1,#fallr-button-b2{
                position:absolute !Important;
                bottom:0px !Important; 
                text-decoration:none; 
                border: 1px solid #ccc;
                text-transform: uppercase;
                font-family: Arial, Verdana;
                font-size:12px; 
                padding-left: 7px;
                padding-right: 7px;
                padding-top: 5px;
                padding-bottom: 5px;
                border-radius: 4px;
                background: #DBE1EB;
                background: linear-gradient(left, #DBE1EB, #ccc);
                color: #000;
            }
            #fallr-button-b1:hover,#fallr-button-b2:hover{
                background: #DBE1EB;
                background: linear-gradient(left, #DBE1EB, #ccc);
                color: #000;
                border-color: #000;
            }            
            #fallr-button-b1{
                right:0px;
            }
            #fallr-button-b2{
                left:0px;
            }
            
        </style>
    </head>
    <body>
        <table  id="tbl" width="70%" >
            <caption style="text-align:left;background: #f8f8f8 url(../menu/images/bg.jpg) repeat top left; " id="mnu">
                <?PHP include_once '../menu/subMenu.php'; ?>
                <center class="rows" >
                    <font size="2" >INVENTARIO DE MATERIA PRIMA</font>
                </center>
                <center class="rows" style="height:35px;padding-top:5px;text-align:left;" >
                    <button id="nuevo" onclick="auxWindow(0)">Nuevo</button>
                    <form method="get" onsubmit="">
                        <input type="text" name="txt" id="txt"  />
                        <button name="search" id="search" style="text-transform:capitalize">Buscar</button>
                    </form>
                </center>
            </caption>
            <thead>
                <tr>
                    <th class="tablesorter-headerAsc" >No</th>
                    <th >CODIGO</th>                                
                    <th >REFERENCIA</th>
                    <th >PROVEEDOR</th>
                    <th >QUITO</th>
                    <th >GUAYAQUIL</th>
                    <th >TOTAL</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mat_prim_codigo] ?></td>                        
                        <td><?php echo $rst[mat_prim_nombre] ?></td>
                        <td></td>
                        <td align="right"><?php echo number_format($quito,2) ?></td>
                        <td align="right"><?php echo number_format($guayaquil,2) ?></td>
                        <td align="right"><?php echo number_format($quito+$guayaquil,2) ?></td>
                        <td >
                            <img class="auxBtn" src="../Img/upd.png" <?php echo $prt->edition ?> onclick="auxWindow(1,<?PHP echo $rst[cyr_id]; ?>)" />
                            <img class="auxBtn" src="../Img/del.png" <?php echo $prt->delete ?> onclick="del(<?PHP echo $rst[cyr_id]; ?>)" />
                        </td>          
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </body>
    <p id="back-top" style="display: block;">
        <a href="#" >&#9650;Inicio</a>
    </p>

</html>
