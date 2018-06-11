    <?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
$Set=new Set();
$cod=$_GET[cod];
if(isset($_GET[cod]))
{
$cns=$Set->lista_movimientos_inv_codigo($cod);
$rst=pg_fetch_array($Set->lista_movimientos_inv_codigo($cod));

switch($rst[mov_ubicacion])
{
    case 1:$ubc="Bodega1";break;    
    case 2:$ubc="Bodega2";break;    
    case 3:$ubc="Bodega3";break;    
}
switch($rst[mov_ubicacion])
{
    case 0:$dest="Otro";break;    
    case 1:$dest="Bodega1";break;    
    case 2:$dest="Bodega2";break;    
    case 3:$dest="Bodega3";break;    
}

$rst_trs=pg_fetch_array($Set->lista_una_transacciones($rst[trs_id]));

}else{
$rst[mov_fecha_trans]=  date("Y-m-d");    
    
}    
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
<meta charset=utf-8 />
<title>Movimientos de Inventarios</title>
<head>
    <style>
        .titulo{
         font-size:13px;    
         font-weight:bolder;
         background:#015b85;
         color:white;  
        }
    </style>
</head>

<body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3" >Movimientos de Inventarios</th></tr>
            </thead>
            <tr>
                <td colspan="8" class="titulo">
                    <font><?php echo "Ubicacion : ".$ubc?></font>
                    <font style="margin-left:15% "><?php echo "Fecha : ".$rst[mov_fecha_trans]?></font> 
                   <font style="float:right "><?php echo "Documento : ".$rst[mov_documento]?></font> 
                </td>
            </tr>
            <tr>
                <td colspan="8" class="titulo">
                    <font><?php echo "Destino/Procedencia : ".$dest?></font> 
                    <font style="float:right "><?php echo "Transaccion : ".$rst_trs[trs_descripcion]?></font>
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
        </tr>
        </thead>
<tbody class="tbl_frm_aux" >                 
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
                        </tr>
                    <?php
                }    
            ?>
        </tbody>
    </table>
</body>


</html>