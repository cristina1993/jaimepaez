<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciosmp.php';
$Clase_preciosmp = new Clase_preciosmp();
$Set = new Set();
$mod = $_GET['mod'];
$rst_ti = pg_fetch_array($Set->lista_titulo($mod));
$des = strtoupper($rst_ti[mod_descripcion]);
$cns_comb = $Set->lista_un_mp_mod1();
$txt = trim(strtoupper($_GET[txt]));
$ids = $_GET[ids];
$iva = $_GET[ivab];
if (!empty($txt)) {
    $texto = "(mp_c like '%$txt%' or mp_d like '%$txt%') and ids=$ids";
} else if (!empty($ids) && $txt == '' && $iva == '') {
    $texto = "ids=$ids";
} else if ($iva != '' && $ids != '') {
    $texto = "mp_h= '$iva' and ids=$ids";
}
$cns = $Clase_preciosmp->lista_buscador_agrup($texto);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var ids = '<?php echo $r_tip[ids] ?>';
            var mod = '<?php echo $mod ?>';
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });
           
        </script> 
        <style>
            #mn195{
                background:black;
                color:white;
                border: solid 1px white;
            }
            #head{
                padding: 3px 10px;  
                background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #63b8ff), color-stop(1, #00529B) );
                background:-moz-linear-gradient( center top, #63b8ff 5%, #00529B 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#63b8ff', endColorstr='#00529B');
                color:#FFFFFF; 
                font-size: 12px; 
                font-weight: bold; 
                border-left: 1px solid #f8f8f8;
                border-collapse: collapse;
                cursor:pointer;
            }
            input[type=text]{
                text-transform: uppercase;                
            }

            div.upload {
                padding:5px; 
                width: 14px;
                height: 20px;
                background-color: #568da7;        
                background-image:-moz-linear-gradient(
                    top,
                    rgba(255,255,255,0.4) 0%,
                    rgba(255,255,255,0.2) 60%);
                color:#FFFFFF; 
                overflow: hidden;
                border-radius: 4px 4px 4px 4px; 
                cursor:pointer; 
                border:solid 1px #ccc; 
            }
            div.upload:hover{
                background-color:#7198ab;        
            }
            div.upload input {
                margin-top:-20; 
                margin-left:-5; 
                display: block !important;
                width: 40px !important;
                height: 40px !important;
                opacity: 0 !important;
                overflow: hidden !important;
                cursor:pointer; 
            }    
            #txt_load{
                margin-right:5px; 
                margin-top:13px; 
            }
            #frmSearch{
                font-size: 10px;
            }
            #frm_file{
                font-size: 10px;
            }
            .sel{
                font-size: 11px;
                width: 100px;
            }
        </style>
    </head>
    <body>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php?mod=" . $mod_id . "&ids=" . $rst_sbm[opl_id] ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>    
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >LISTA DE PRECIOS</center>
                </center>
            </caption>
            <form  autocomplete="off" id="frm_save" name="frm_save">
                <table id="tbl" style="width:50%">  
                    <!--Nombres de la columna de la tabla-->
                    <thead id="head">
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Precio 1</th>
                    </thead>
                    <!------------------------------------->
                    <tbody id="tbody">
                        <?PHP
                        $n = 0;
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;
                            $rst_t = pg_fetch_array($Clase_preciosmp->lista_tipos($rst[mp_a]));
                            $rst_f = pg_fetch_array($Clase_preciosmp->lista_tipos($rst[mp_b]));
                            ?>
                            <tr>
                                <td style="font-weight: bolder"><?php echo $rst_t['tps_nombre'] ?></td>
                                <td></td>
                                <td></td>
                            </tr> 
                            <tr>
                                <td style="font-weight: bolder" align="center"><?php echo $rst_f['tps_nombre'] ?></td>
                                <td></td>
                                <td></td>
                            </tr> 
                            <?PHP
                            $cns_prod = $Clase_preciosmp->lista_productos_ab($rst[mp_a], $rst[mp_b],$texto);
                            while ($rst1 = pg_fetch_array($cns_prod)) {
                                ?>
                            <tr>
                                <td><?php echo $rst1['mp_c'] ?></td>
                                <td><?php echo $rst1['mp_d'] ?></td>
                                <td align="right"><?php echo number_format($rst1['mp_e'],2) ?></td>
                            </tr> 
                                <?PHP
                            }
                        }
                        ?>
                    </tbody>
                </table>   
            </form>
    </body>    
</html>



