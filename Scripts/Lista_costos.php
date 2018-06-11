<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsClase_preciosmp.php';
//$tbl_set = 'erp_productos_set';
//$tbl = substr($tbl_set, 0, -4);
$Clase_preciosmp = new Clase_preciosmp();
$Set = new Set();
$cns_comb = $Set->lista_un_mp_mod1();
if (isset($_GET[txt], $_GET[ids])) {
    $txt = trim(strtoupper($_GET[txt]));
    $ids = $_GET[ids];
    if (!empty($txt)) {
        $texto = "and (p.mp_c like '%$txt%' or p.mp_d like '%$txt%') and p.mp_i='0'";
    } else if (!empty($ids) && $txt == '') {
        $texto = "and p.ids=$ids and p.mp_i='0'";
    }
    $cns = $Clase_preciosmp->lista_buscador_precios($texto);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
//            var ids = '<?php echo $r_tip[ids] ?>';
//            var mod = '<?php echo $mod ?>';
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

            function save(id, c, t, cod) {
                prec = $('#pre_precio' + c).val();
                prec2 = $('#pre_preciox' + c).val();
                var data = Array(
                        prec,
                        prec2
                        );
                var fields = Array(
                        'codigo=' + cod,
                        'costo1=' + prec,
                        'costo2=' + prec2,
                        cod +
                        ''
                        );

                $.ajax({
                    beforeSend: function () {
                        if ($("#pre_precio" + c).val().length == 0) {
                            $("#pre_precio" + c).css({borderColor: "red"});
                            $("#pre_precio" + c).focus();
                            return false;
                        }

                        if ($("#pre_preciox" + c).val().length == 0) {
                            $("#pre_preciox" + c).css({borderColor: "red"});
                            $("#pre_preciox" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciosmp.php',
                    data: {op: 3, 'data[]': data, id: id, t: t, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#pre_precio' + c).val(parseFloat(dat[1]).toFixed(dec));
                            $('#pre_preciox' + c).val(parseFloat(dat[2]).toFixed(dec));
                            $('#pre_precio' + c).attr('disabled', true);
                            $('#pre_preciox' + c).attr('disabled', true);
                        } else {
                            alert(dt);
                        }
                    }
                });
            }
            function habilita(c) {
                $('#pre_precio' + c).attr('disabled', false);
                $('#pre_preciox' + c).attr('disabled', false);
            }

            function actualizar_todo() {
                desc = $('#desc').val();
                $.post("actions_preciospt.php", {op: 2, id: desc}, function (dt) {
                    if (dt == 0) {
                        window.location = '../Scripts/Lista_preciospt.php';
                    } else {
                        alert(dt);
                    }
                });
            }

            function cambiar_precios() {
                if (camb.checked == true) {
                    msm = confirm("¿ Esta seguro de CAMBIAR DE COSTOS ?");
                    if (msm == true) {
                        $('.precios2').each(function () {

                            var i = this.lang;
                            var id = $('#pro_id' + i).val();
                            var val = $('#pre_preciox' + i).val();
                            if ($('#pre_precio' + i).attr('disabled') == false) {
                                alert('Primero Guarde la información');
                                return false;
                            } else {
                                if (parseFloat(val) != 0) {
                                    var fields = Array(
                                            'codigo=' + $('#codigo' + i).html(),
                                            'costo1=' + $('#pre_preciox' + i).val(),
                                            ''
                                            );
                                    $.post("actions_preciosmp.php", {op: 4, id: id, 'fields[]': fields},
                                    function (dt) {
                                        dat = dt.split('&');
                                        if (dat[0] == 0) {
                                            $('#pre_precio' + i).val(val);
                                            $('#pre_preciox' + i).val(parseFloat(0).toFixed(dec));
                                        } else {
                                            alert(dat[0]);
                                        }
                                    });
                                }
                            }
                        });
                    }
                }

            }

            function auxWindow(a)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        frm.src = '../Scripts/Lista_descuentos.php';//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        //                        look_menu();
                        break;
                    case 1:
                        //                        alert($('#txt').val() + '&' + $('#bodega').val() + '&' + $('#ivab').val());
                        frm.src = '../Scripts/Lista_costos_excel.php?txt=' + $('#txt').val() + '&bodega=' + $('#bodega').val() + '&ivab=' + $('#ivab').val();//Cambiar Form_productos
                        //                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        //                        look_menu();
                        break;
                }
            }
            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=costos.csv';
            }
            function load_file() {
                $('#frm_file').submit();
            }

            function valida() {
                if (txt.value.length < 6) {
                    alert('Debe poner almenos 6 caracteres');
                    window.location = '../Scripts/Lista_costos.php';
                    return false;
                }

            }

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
                <center class="cont_title" >INGRESO DE COSTOS</center>
                <center class="cont_finder">
                    <!--                    <form id="exp_excel" style="float:right;margin-top:6px;padding:0px" method="post" action="../Includes/export.php?tipo=3" onsubmit="return exportar_excel()"  >
                                            <input type="submit" value="Excel" class="auxBtn" />
                                            <input type="hidden" id="datatodisplay" name="datatodisplay">
                                        </form>-->
                    <div style="float:right;margin-top:0px;padding:7px;">
                        CAMBIAR PRECIOS:<input type="radio" id="camb" name="pre_precio" value="0" />
                        <button class="btn" title="Guardar" onclick="cambiar_precios()">Guardar</button>
                    </div>
                    <!--<img src="../img/xls.png" width="16px;" style="float:right" class="auxBtn" onclick="auxWindow(1)"  title="Exporta Lista"  />-->
                    <!--<font style="float:right" id="txt_load">  Exportar:</font><img src="../img/xls.png" width="16px;"-->

                    <!--<a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>-->

                    <!--                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_costos.php" method="POST" enctype="multipart/form-data">
                                            <div class="upload">
                                                ...<input type="file"  name="archivo" id="archivo" onchange="load_file()" >
                                            </div>
                                        </form>-->
                    <!--<font style="float:right" id="txt_load">Cargar Datos:</font>-->
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
                <!--<th>Bodega</th>-->
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Costo 1</th>
                <th>Costo 2</th>
                <th>Acciones</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        ?>
                        <tr>
                            <td><?php echo $n ?></td>
                            <td id="codigo<?php echo $n ?>"><?php echo $rst['mp_c'] ?></td>
                            <td><?php echo $rst['mp_d'] ?></td>
                            <td  align="center"><input type ="text" size="10"  class="precios2" id="<?php echo 'pre_precio' . $n ?>"  value="<?php echo number_format($rst['mp_p'], $dec) ?>" style="text-align:right" disabled lang="<?php echo $n ?>" /><input type="text" id="pro_id<?php echo $n ?>" value="<?php echo $rst[id] ?>" hidden></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_preciox' . $n ?>"  value="<?php echo number_format($rst['mp_r'], $dec) ?>" style="text-align:right" disabled lang="<?php echo $n ?>"/></td>
                            <td align="center">
                                <?php
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/save.png"  class="auxBtn" onclick="save('<?php echo $rst[id] ?>', '<?php echo $n ?>', 0, '<?php echo $rst[mp_c] ?>')">

                                    <?php
                                }
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/upd.png" width="16px"  class="auxBtn" onclick="habilita(<?php echo $n ?>)"
                                         <?php
                                     }
                                     ?>
                            </td>         
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

