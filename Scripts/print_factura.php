<?php
include_once '../Clases/clsClase_factura.php';
//require_once '../Reports/fpdf/fpdf.php';
date_default_timezone_set('America/Guayaquil');
set_time_limit(0);
$Set = new Clase_factura();
$id = $_GET[id];
$g = str_replace('-', '', trim($id));
$cns = $Set->lista_pdf_factura($g);
$rst1 = pg_fetch_array($Set->lista_ingreso_num_factura($id));
$rst_v = pg_fetch_array($Set->lista_vendedores("where vnd_id=$rst1[vendedor]"));

$fe = $rst1[fecha_emision];
?>
<style>
/*@media screen {
    #div_container{
        display:none; 
    }
}*/
/*@media screen {
    *{
        font-family:Arial;
        font-size:11px; 
    }
    .enc div{
        float:left; 
        text-align:center; 
    }
    #div_container{
        margin-top:2.5cm; 
    }
    thead tr th{
        //border:solid 1px; 
    }
    
}
@media print {*/
    *{
        font-family:Arial;
        font-size:11px;
        color:#000; 
    }
    .enc div{
        float:left; 
        text-align:center; 
    }
    #div_container{
        margin-top:2.3cm; 
    }
    thead tr th{
        //border:solid 1px; 
    }
    
//}
</style>
<script>
    
</script>
<div id="div_container" onclick="window.print()">
    <div class="enc" style="height:0.15cm">
        <div style="width:2.5cm;text-align:left ">&nbsp;</div>
        <div style="width:8.5cm;text-align:left " ><?php echo $rst1[nombre] ?></div>
        <div style="width:5.5cm;text-align:left "><?php echo $rst1[identificacion] ?></div>
        <div style="text-align:left "><?php echo substr($fe, 0, 4) . '-' . substr($fe, 4, 2) . '-' . substr($fe, 6, 2) ?></div>
    </div>
    <br>
    <div class="enc">
        <div style="width:2.5cm;text-align:left ">&nbsp;</div>
        <div style="width:9.5cm;text-align:left "><?php echo $rst1[direccion_cliente] ?></div>
        <div style="width:4cm;text-align:left "><?php echo $rst1[telefono_cliente] ?></div>
    </div>
    <br>
    <div class="enc" style="">
        <div style="width:16.5cm;text-align:left ">&nbsp;</div>
        <div style="width:3cm;text-align:left "><?php echo $rst_v[vnd_nombre] ?></div>
    </div>
    <br><br>
    <div class="enc" style="">
        <div style="width:19cm;text-align:left;margin-left:2.5cm " ><?php echo strtoupper(substr($rst1[observaciones], 0, 200)) ?></div>
    </div>    
    <br>
    <table border="0" style="margin-left:0.5cm;margin-top:0.8cm  ">
        <?php
        while ($rst = pg_fetch_array($cns)) {
            $rst_pro = pg_fetch_array($Set->lista_productos_cod($rst[cod_producto]));
            if ($rst_pro[tps_tipo] == '1&0&0') {
                $can = '';
                $k = number_format($rst[cantidad], 2);
                $pk = number_format($rst[precio_unitario], 2);
                $p = '';
            } else {
                $can = number_format($rst[cantidad], 2);
                $k = '';
                $pk = '';
                $p = number_format($rst[precio_unitario], 2);
            }
            ?>
            <tr>
                <td style="width:2cm;"><?php echo $rst[cod_producto] ?></td>
                <td style="width:3cm"><?php echo $can ?></td>
                <td style="width:6.5cm"><?php echo substr($rst[descripcion], 0, 40) ?></td>
                <td style="width:1cm"><?php echo $k ?></td>
                <td style="width:2cm" align="right"><?php echo $pk ?></td>
                <td style="width:1cm" align="right"><?php echo number_format($rst[descuento], 2) ?></td>
                <td style="width:2cm" align="right"><?php echo $p ?></td>
                <td style="width:2cm" align="right"><?php echo number_format($rst[precio_total], 2) ?></td>
            </tr>
            <?php
        }
        ?>    
    </table>    
</div>
<script>
    window.print();
</script>


