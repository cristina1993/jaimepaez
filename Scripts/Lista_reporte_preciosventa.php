<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciosmp.php';
//$tbl_set = 'erp_productos_set';
//$tbl = substr($tbl_set, 0, -4);
$dec = 4;
$Clase_preciosmp = new Clase_preciosmp();
$Set = new Set();
$cns_comb = $Set->lista_un_mp_mod1();
if (isset($_GET[txt], $_GET[ids])) {
    $txt = trim(strtoupper($_GET[txt]));
    $ids = $_GET[ids];
    if (!empty($txt)) {
        $texto = "where (mp_c like '%$txt%' or mp_d like '%$txt%')";
    } else if (!empty($ids) && $txt == '') {
        $texto = "where ids=$ids";
    }

    $cns = $Clase_preciosmp->lista_productos_factura($texto);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var dec = '<?php echo $dec ?>';
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


            function exportar_excel() {
                $("#tbl2").append($("#tbl thead").eq(0).clone()).html();
                $("#tbl2").append($("#tbl tbody").clone()).html();
                $("#tbl2").append($("#tbl tfoot").clone()).html();
                $("#datatodisplay").val($("<div>").append($("#tbl2").eq(0).clone()).html());
                return true;
            }

        </script> 
        <style>
            #mn110{
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
            input[readonly]{
                background:#f8f8f8; 
            }
            input{
                background:#f8f8f8 !important; 
            }
        </style>
    </head>
    <body>
        <table style="display:none" border="1" id="tbl2">
            <tr><td colspan="15"><font size="-5" style="float:left">Tivka Systems ---Derechos Reservados</font></td></tr>
            <tr><td colspan="15" align="center"><?PHP echo 'INGRESO DE COSTOS' ?></td></tr>
            <tr>
                <td colspan="15"><?php echo 'Fecha: ' . date('Y-m-d') ?></td>
            </tr>
        </table>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <table style="width:100%" id="tbl">
            <caption  class="tbl_head">
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >PRECIO VP</center>
                <center class="cont_finder">
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" onblur="valida()"/>
                        TIPO:<select id="ids" name="ids" class="sel">
                            <?php
                            while ($rst_c = pg_fetch_array($cns_comb)) {
                                $dt = explode('&', $rst_c[mp_tipo]);
                                ?>
                                <option value="<?php echo $rst_c[ids] ?>"><?php echo $dt[9] ?></option>
                                <?php
                            }
                            ?>

                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form> 
                </center>
            </caption>
        </table>  
        <form  autocomplete="off" id="frm_save" name="frm_save">
            <table id="tbl" style="width:100%">  
                <!--Nombres de la columna de la tabla-->
                <thead id="head">
                <th>No</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio Venta</th>
                <th>Costo</th>
                <th>% Utilidad</th>
                <th>$ Utilidad</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        $pvp = round($rst['v_unit'] - $rst['descuento'], 2);
                        $p_utilidad = (1 - ($pvp / round($rst['mp_p'], 2)))* 100;
                        $v_utilidad = ($pvp - round($rst['mp_p'], 2));
                        ?>
                        <tr>
                            <td><?php echo $n ?></td>
                            <td id="codigo<?php echo $n ?>"><?php echo $rst['mp_c'] ?></td>
                            <td><?php echo $rst['mp_d'] ?></td>
                            <td align="right"><?php echo number_format($pvp, 2) ?></td>
                            <td align="right"><?php echo number_format($rst['mp_p'], 2) ?></td>
                            <td align="right"><?php echo number_format($p_utilidad, 2) ?></td>
                            <td align="right"><?php echo number_format($v_utilidad, 2) ?></td>
                        </tr>  

                        <?PHP
                    }
                    ?>
                </tbody>
            </table>   
        </form>
    </body>    
</html>
<script>
    var t = '<?php echo $ids ?>';
    $('#ids').val(t);
</script>

