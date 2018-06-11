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
    $texto = " and (p.mp_c like '%$txt%' or p.mp_d like '%$txt%') and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0') ";
    $cns = $Clase_preciosmp->lista_buscador_precios($texto);
} else if (!empty($ids) && $txt == '' && $iva == '') {
    $texto = "and p.ids=$ids and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0')";
    $cns = $Clase_preciosmp->lista_buscador_precios($texto);
} else if ($iva != '') {
    $texto = "and p.mp_h= '$iva' and p.mp_i='0' and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0')";
    $cns = $Clase_preciosmp->lista_buscador_precios($texto);
} else {
//    $cns = $Clase_preciosmp->lista_buscador_precios_cero();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            var ids = '<?php echo $r_tip[ids] ?>';
            var mod = '<?php echo $mod ?>';
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
                prec = $('#mp_c' + c).val();
                prec2 = $('#mp_d' + c).val();
                desc = $('#mp_e' + c).val();
                iva = $('#mp_f' + c).val();
                ice = $('#mp_j' + c).val();
                irbpnr = $('#mp_k' + c).val();
                cod_ice = $('#cod_ice' + c).val();
                cod_irbpnr = $('#cod_irbp' + c).val();
                ic = $('#mp_l' + c).val();
                ib = $('#mp_m' + c).val();
                var data = Array(
                        prec,
                        prec2,
                        desc,
                        iva,
                        ice,
                        irbpnr,
                        cod_ice,
                        cod_irbpnr,
                        ic,
                        ib
                        );
                var fields = Array(
                        'codigo=' + cod,
                        'precio1=' + prec,
                        'precio2=' + prec2,
                        'desc=' + desc,
                        'iva=' + iva,
                        'ice=' + ice,
                        'irbpnr=' + irbpnr,
                        'cod_ice=' + cod_ice,
                        'cod_irbpnr=' + cod_irbpnr,
                        ''
                        );
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($("#mp_c" + c).val().length == 0) {
                            $("#mp_c" + c).css({borderColor: "red"});
                            $("#mp_c" + c).focus();
                            return false;
                        }
                        else if ($("#mp_d" + c).val().length == 0) {
                            $("#mp_d" + c).css({borderColor: "red"});
                            $("#mp_d" + c).focus();
                            return false;
                        }
                        else if ($("#mp_e" + c).val().length == 0) {
                            $("#mp_e" + c).css({borderColor: "red"});
                            $("#mp_e" + c).focus();
                            return false;
                        }
                        else if ($("#mp_f" + c).val().length == 0) {
                            $("#mp_f" + c).css({borderColor: "red"});
                            $("#mp_f" + c).focus();
                            return false;
                        }
                        else if ($("#mp_j" + c).val().length == 0) {
                            $("#mp_j" + c).css({borderColor: "red"});
                            $("#mp_j" + c).focus();
                            return false;
                        }
                        else if ($("#mp_k" + c).val().length == 0) {
                            $("#mp_k" + c).css({borderColor: "red"});
                            $("#mp_k" + c).focus();
                            return false;
                        }
                        else if ($("#mp_l" + c).val().length == 0) {
                            $("#mp_l" + c).css({borderColor: "red"});
                            $("#mp_l" + c).focus();
                            return false;
                        }
                        else if ($("#mp_m" + c).val().length == 0) {
                            $("#mp_m" + c).css({borderColor: "red"});
                            $("#mp_m" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciosmp.php',
                    data: {op: 0, 'data[]': data, id: id, t: t, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#mp_c' + c).val(parseFloat(dat[1]).toFixed(dec));
                            $('#mp_d' + c).val(parseFloat(dat[2]).toFixed(dec));
                            $('#mp_e' + c).val(parseFloat(dat[3]).toFixed(dec));
                            $('#mp_f' + c).val(dat[4]);
                            $('#mp_j' + c).val(dat[5]);
                            $('#mp_k' + c).val(dat[6]);
                            $('#mp_l' + c).val(dat[9]);
                            $('#mp_m' + c).val(dat[10]);
                            $('#cod_ice' + c).val(dat[7]);
                            $('#cod_irbp' + c).val(dat[8]);
                            $('#mp_c' + c).attr('disabled', true);
                            $('#mp_d' + c).attr('disabled', true);
                            $('#mp_e' + c).attr('disabled', true);
                            $('#mp_f' + c).attr('disabled', true);
                            $('#mp_j' + c).attr('disabled', true);
                            $('#mp_k' + c).attr('disabled', true);
                            $('#mp_l' + c).attr('disabled', true);
                            $('#mp_m' + c).attr('disabled', true);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                });
            }
            function habilita(c) {
                $('#mp_c' + c).attr('disabled', false);
                $('#mp_d' + c).attr('disabled', false);
                $('#mp_e' + c).attr('disabled', false);
                $('#mp_f' + c).attr('disabled', false);
                $('#mp_j' + c).attr('disabled', true);
                $('#mp_k' + c).attr('disabled', false);
                $('#mp_l' + c).attr('disabled', false);
                $('#mp_m' + c).attr('disabled', false);
            }


            function cambiar_precios() {

                if (confirm('Esta seguro de Aplicar los cambios?') == true) {
//                    alert($('.precios2').length);
                    $('.precios2').each(function () {
                        var i = this.lang;
                        var id = this.id;
                        var val = $('#mp_d' + i).val();

                        if ($('#mp_d' + i).attr('disabled') == false) {
                            alert('Primero Guarde la información');
                        } else {
                            if ($(this).attr('checked') == true) {
                                if (parseFloat(val) != 0) {
                                    var fields = Array(
                                            'codigo=' + $('#cod' + i).html(),
                                            'precio1=' + $('#mp_c' + i).val(),
                                            ''
                                            );
                                    $.post("actions_preciosmp.php", {op: 1, id: id, 'fields[]': fields}, function (dt) {
                                        dat = dt.split('&');
                                        if (dat[0] == 0) {
                                            $('#mp_c' + i).val(val);
                                            $('#mp_d' + i).val(parseFloat(0).toFixed(dec));
                                        }
                                    });
                                }
                            }
                        }
                    })
                    $('#todos').attr('checked', false);
                    $('.precios2').attr('checked', false);

                } else {
                    $('#todos').attr('checked', false);
                    $('.precios2').attr('checked', false);
                }
            }


            function seleccionar_todo_prec2(obj) {
                n = 0;
                if ($(obj).attr('checked') == true) {
                    $('.precios2').each(function () {
                        $(this).attr('checked', true);
                        n++;
                    })
                } else {
                    $('.precios2').each(function () {
                        $(this).attr('checked', false);
                        n++;
                    })
                }
            }

            function seleccionar_todo_desc(obj) {
                n = 0;
                if ($(obj).attr('checked') == true) {
                    $('.desc').each(function () {
                        $(this).attr('checked', true);
                        n++;
                    })
                } else {
                    $('.desc').each(function () {
                        $(this).attr('checked', false);
                        n++;
                    })
                }
            }

            function actualizar_todo() {
                desc = $('#desc').val();
                i = 0;
                if (confirm('Esta seguro de Aplicar descuento?') == true) {
                    if (desc.length != 0) {
                        $('.desc').each(function () {
                            var i = this.lang;
                            var ids = this.id.replace('d', '');
                            if ($(this).attr('checked') == true) {
                                var fields = Array(
                                        'codigo=' + $('#cod' + i).html(),
                                        'descuento=' + desc,
                                        ''
                                        );
                                $.post("actions_preciosmp.php", {op: 2, tab: desc, id: ids, 'fields[]': fields}, function (dt) {
                                    if (dt == 0) {
                                        $('#mp_e' + i).val(parseFloat(desc).toFixed(dec));
                                    } else {
                                        alert(dt);
                                    }
                                });
                            }
                        });
                    }
                    $('.desc').attr('checked', false);
                    $('#todos_desc').attr('checked', false);
                } else {
                    alert('Ingrese Valor');
                }
            }

            function auxWindow(a)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        if (txt.value.length != 0 || $('#ids').val() != '') {
                            main.src = '../Scripts/Lista_reporte_precios.php?txt=' + txt.value + '&ids=' + $('#ids').val() + '&ivab=' + $('#ivab').val();
                            parent.document.getElementById('contenedor2').rows = "*,0%";
                        } else {
                            alert('Elija un tipo de producto');
                        }
                        break;
                }
            }

            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=precios.csv';
            }

            function load_file() {
                $('#frm_file').submit();
            }

            function load_impuesto(obj, op) {
                i = obj.lang;
                $.post("actions_preciosmp.php", {op: 7, id: obj.value}, function (dt) {
                    dat = dt.split('&');
                    if (dat[0] != '') {
                        if (op == 0) {
                            $('#cod_ice' + i).val(dat[0]);
                            $('#mp_l' + i).val(dat[1]);
                            $('#mp_j' + i).val(parseFloat(dat[2]).toFixed(dec));
                        } else {
                            $('#cod_irbp' + i).val(dat[0]);
                            $('#mp_m' + i).val(dat[1]);
                            $('#mp_k' + i).val(parseFloat(dat[2]).toFixed(dec));
                        }
                    } else {
                        if (op == 0) {
                            $('#cod_ice' + i).val('0');
                            $('#mp_l' + i).val('0');
                            $('#mp_j' + i).val(parseFloat(0).toFixed(dec));
                        } else {
                            $('#cod_irbp' + i).val('0');
                            $('#mp_m' + i).val('0');
                            $('#mp_k' + i).val(parseFloat(0).toFixed(dec));
                        }
                    }
                });
            }
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
                    <img class="auxBtn" style="float:right" onclick="auxWindow(0)" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >LISTA DE PRECIOS</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Asignar Descuentos" onclick="auxWindow(0)" >Asignar Descuentos</a>-->


                    <!--<a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; " title="Descargar Formato">Descargar<img src="../img/xls.png" width="16px;" /></a>-->
                    <!--                    <form id="frm_file" name="frm_file" style="float:right" action="actions_upload_precios.php" method="POST" enctype="multipart/form-data">
                                            <div class="upload" style="font-size: 10px;">
                                                ...<input type="file"  name="file" id="file" onchange="load_file()" size="5" >
                                            </div>
                                        </form>-->
                    <!--<font style="float:right; font-size: 10px; " id="txt_load">Cargar Datos:</font>-->

                    <div style="float:right;margin-top:0px;padding:7px;font-size: 10px;">
                        Descuento:<input type="text"  name="desc" size="10" id="desc" style="font-size: 11px"/>
                        <button class="btn" title="Descuento Todos" onclick="actualizar_todo()" style="font-size: 10px;">Aplicar</button>
                    </div>
                    <div style="float:right;margin-top:0px;padding:7px; ">
                        <button class="btn" title="Aplicar Cambios" onclick="cambiar_precios()" style="font-size: 10px;">Cambiar precios</button>
                    </div>

                    <!--<font style="float:right" id="txt_load">Cargar Datos:</font>-->
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        <input type="hidden" name="mod" size="15" id="mod" value="<?php echo $mod ?>" />
                        BUSCAR POR:<input type="text" name="txt" size="12" id="txt" style="font-size: 11px" value="<?php echo $txt ?>"/>
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
                        IVA:<select id="ivab" name="ivab" class="sel">
                            <option value="">SELECCIONE</option>
                            <!--<option value="14">14%</option>-->
                            <option value="12">12%</option>
                            <option value="0">0%</option>                             
                            <option value="EX">EX</option>                             
                            <option value="NO">NO</option>                             
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()" style="font-size: 10px;" >Buscar</button>

                    </form> 
                </center>
            </caption>

            <form autocomplete="off" id="frm_save" name="frm_save">
                <!--<table id="tbl" style="width:100%">-->
                <!--Nombres de la columna de la tabla-->
                <thead>
                <th>No</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Precio 1</th>
                <th>Precio 2 <input type="checkbox" id="todos"  onclick="seleccionar_todo_prec2(this)"></th>
                <th>Descuento %<input type="checkbox" id="todos_desc"  onclick="seleccionar_todo_desc(this)"></th>
                <th>Iva</th>
                <th>ICE %</th>
                <th>COD. ICE</th>
                <th>IRBPNR $</th>
                <th>COD. IRBPNR</th>
                <th>Acciones</th>
                </thead>
                <!------------------------------------->
                <tbody id="tbody">
                    <?PHP
                    $n = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $n++;
                        $ice = pg_fetch_array($Clase_preciosmp->lista_un_impuesto($rst[mp_l]));
                        $cod_ice = $ice[por_codigo];
                        $irbp = pg_fetch_array($Clase_preciosmp->lista_un_impuesto($rst[mp_m]));
                        $cod_irbp = $irbp[por_codigo];
                        if (empty($rst[mp_l])) {
                            $cod_ice = 0;
                        }
                        if (empty($rst[mp_m])) {
                            $cod_irbp = 0;
                        }
                        ?>
                        <tr>
                            <td><?php echo $n ?></td>
                            <td id="cod<?php echo $n ?>"><?php echo $rst['mp_c'] ?></td>
                            <td><?php echo $rst['mp_d'] ?></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_c' . $n ?>"  value="<?php echo number_format($rst['mp_e'], $dec) ?>" style="text-align:right" disabled /></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_d' . $n ?>"  value="<?php echo number_format($rst['mp_f'], $dec) ?>" style="text-align:right" disabled /><input type="checkbox" id="<?php echo $rst[id] ?>" lang="<?php echo $n ?>" class="precios2"></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_e' . $n ?>"  value="<?php echo number_format($rst['mp_g'], $dec) ?>" style="text-align:right" disabled /><input type="checkbox" id="d<?php echo $rst[id] ?>" lang="<?php echo $n ?>" class="desc"></td>
                            <td align="center"><select id="<?php echo 'mp_f' . $n ?>" value="<?php echo $rst['pre_iva'] ?>" disabled >
                                    <!--<option value="14">14%</option>-->
                                    <option value="12">12%</option>
                                    <option value="0">0%</option>                            
                                    <option value="EX">EX</option>                            
                                    <option value="NO">NO</option>                            
                                </select>
                                <script>
                                    idt = '<?php echo 'mp_f' . $n ?>';
                                    $('#' + idt).val('<?php echo $rst[mp_h] ?>');</script>
                            </td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_j' . $n ?>"  value="<?php echo number_format($rst['mp_j'], $dec) ?>" style="text-align:right" disabled /></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_l' . $n ?>"  value="<?php echo $cod_ice ?>" disabled list="lista_ice" style="text-align:right" onfocus="this.style.width = '400px';" onblur="this.style.width = '80px';" onchange="load_impuesto(this, 0)"  lang="<?php echo $n ?>" />
                                <input type ="hidden" size="10"  id="<?php echo 'cod_ice' . $n ?>"  value="<?php echo $rst['mp_l'] ?>" style="text-align:right" disabled /></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_k' . $n ?>"  value="<?php echo number_format($rst['mp_k'], $dec) ?>" style="text-align:right" disabled /></td>
                            <td  align="center"><input type ="text" size="10"  id="<?php echo 'mp_m' . $n ?>"  value="<?php echo $cod_irbp ?>" disabled list="lista_irbpnr" style="text-align:right" onfocus="this.style.width = '400px';" onblur="this.style.width = '80px';" onchange="load_impuesto(this, 1)"  lang="<?php echo $n ?>" />
                                <input type ="hidden" size="10"  id="<?php echo 'cod_irbp' . $n ?>"  value="<?php echo $rst['mp_m'] ?>" style="text-align:right" disabled /></td>
                            <td align="center">
                                <?php
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/save.png"  class="auxBtn" onclick="save('<?php echo $rst[id] ?>', '<?php echo $n ?>', 0, '<?php echo $rst[mp_c] ?>')">

                                    <?php
                                }
                                if ($Prt->edition == 0) {
                                    ?>
                                    <img src="../img/upd.png" width="16px"  class="auxBtn" onclick="habilita(<?php echo $n ?>)">
                                    <?php
                                }
                                ?>
                            </td>         
                        </tr>  

                        <?PHP
                    }
                    ?>
                </tbody>
            </form>
        </table>  
    </body>    
</html>
<script>
    var t = '<?php echo $ids ?>';
    $('#ids').val(t);
    var i = '<?php echo $iva ?>';
    $('#ivab').val(i);
</script>
<datalist id="lista_ice">
    <?php
    $cns_des = $Clase_preciosmp->lista_impuesto('IC');
    while ($rst_des = pg_fetch_array($cns_des)) {
        echo "<option value='$rst_des[por_id]' >$rst_des[por_codigo]  $rst_des[por_descripcion]</option>";
    }
    ?>
</datalist>

<datalist id="lista_irbpnr">
    <?php
    $cns_des = $Clase_preciosmp->lista_impuesto('IRB');
    while ($rst_des = pg_fetch_array($cns_des)) {
        echo "<option value='$rst_des[por_id]' >$rst_des[por_codigo]  $rst_des[por_descripcion]</option>";
    }
    ?>
</datalist>
