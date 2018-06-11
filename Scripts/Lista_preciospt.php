<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_preciospt.php';
$Clase_preciospt = new Clase_preciospt();
if (isset($_GET[txt], $_GET[ivab])) {
    $txt = trim(strtoupper($_GET[txt]));
    $iva = $_GET[ivab];
    if (!empty($txt)) {
        $texto = "and (pro.pro_descripcion like '%$txt%' or pro.pro_codigo like '%$txt%')";
    } else if ($iva != '') {
        $texto = "and pre_iva= '$iva'";
    }
    $cns = $Clase_preciospt->lista_buscador_precios1($texto);
} else {
    $cns = $Clase_preciospt->lista_buscador_precios1($txt);
}

$cns_pre = $Clase_preciospt->lista_precios();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";

                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
            });

            function save(id, c, t) {
                prec = $('#pre_precio' + c).val();
                desc = $('#pre_descuento' + c).val();
                iva = $('#pre_iva' + c).val();
                prec2 = $('#pre_precio2' + c).val();
                var data = Array(
                        prec,
                        desc,
                        iva,
                        prec2
                        );
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if ($("#pre_precio" + c).val().length == 0) {
                            $("#pre_precio" + c).css({borderColor: "red"});
                            $("#pre_precio" + c).focus();
                            return false;
                        }
                        else if ($("#pre_descuento" + c).val().length == 0) {
                            $("#pre_descuento" + c).css({borderColor: "red"});
                            $("#pre_descuento" + c).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_preciospt.php',
                    data: {op: 0, 'data[]': data, id: id, 'fields[]': fields, t: t}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            $('#pre_precio' + c).val(parseFloat(dat[1]).toFixed(2));
                            $('#pre_iva' + c).val(dat[3]);
                            $('#pre_descuento' + c).val(dat[2]);
                            $('#pre_precio2' + c).val(parseFloat(dat[4]).toFixed(2));
                            $('#pre_precio' + c).attr('disabled', true);
                            $('#pre_descuento' + c).attr('disabled', true);
                            $('#pre_iva' + c).attr('disabled', true);
                            $('#pre_precio2' + c).attr('disabled', true);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                });
            }
            function habilita(c) {
                $('#pre_precio' + c).attr('disabled', false);
                $('#pre_precio2' + c).attr('disabled', false);
                $('#pre_descuento' + c).attr('disabled', false);
                $('#pre_iva' + c).attr('disabled', false);
            }

            function actualizar_todo() {
                desc = $('#desc').val();
                $.post("actions_preciospm.php", {op: 2, id: desc}, function (dt) {
                    if (dt == 0) {
                        window.location = '../Scripts/Lista_precios_mp.php'
                    } else {
                        alert(dt);
                    }
                });
            }

            function cambiar_precios() {

                if (confirm('Esta seguro de Aplicar los cambios Seleccionados?') == true) {
                    if (pre_precios1.checked == true) {
                        pre_precios1 = $('#pre_precios1').val();
                        $.post("actions_preciospt.php", {op: 5, id: pre_precios1}, function (dt) {
                            if (dt == 0) {
                                window.location = '../Scripts/Lista_preciospt.php'
                            } else {
                                alert(dt);
                            }
                        });
                    } else if (pre_precios2.checked == true) {
                        pre_precios2 = $('#pre_precios2').val();
                        $.post("actions_preciospt.php", {op: 6, id: pre_precios2}, function (dt) {
                            if (dt == 0) {
                                window.location = '../Scripts/Lista_preciospt.php'
                            } else {
                                alert(dt);
                            }
                        });
                    } else if (camb.checked == true) {
                        camb = $('#camb').val();
                        $.post("actions_preciospt.php", {op: 4, id: camb}, function (dt) {
                            if (dt == 0) {
                                window.location = '../Scripts/Lista_preciospt.php'
                            } else {
                                alert(dt);
                            }
                        });
                    } else {
                        alert('Nada Seleccionado \n No se aplicaran cambios');
                    }
                }
            }

            function seleccionar_todo_prec1() {
                n = 0;
                $('.precios1').each(function () {
                    $(this).attr('checked', true);
                    n++;
                })
            }

            function seleccionar_todo_prec2() {
                n = 0;
                $('.precios2').each(function () {
                    $(this).attr('checked', true);
                    n++;
                })
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
                }
            }

            function descargar_archivo() {
                window.location = '../formatos/descargar_archivo.php?archivo=precios.csv';
            }
            function load_file() {
                var formData = new FormData($('#frm_file')[0]);
                $.ajax({
                    type: "POST",
                    url: "actions_upload_precios.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (dt) {
                        alert(dt);
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
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>               
                <center class="cont_title" >CONTROL DE PRECIOS</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Asignar Descuentos" onclick="auxWindow(0)" >Asignar Descuentos</a>-->


                    <a href="#" onclick="descargar_archivo()" style="float:right;text-transform:capitalize;margin-left:15px;margin-top:10px;text-decoration:none;color:#ccc; ">Descargar Formato<img src="../img/xls.png" width="16px;" /></a>
                    <form id="frm_file" name="frm_file" style="float:right">
                        <div class="upload">
                            ...<input type="file"  name="file" id="file" onchange="load_file()" >
                        </div>
                    </form>
                    <font style="float:right" id="txt_load">Cargar Datos:</font>

                    <div style="float:right;margin-top:0px;padding:7px;">
                        Descuento todos:<input type="text"  name="desc" size="15" id="desc"/>
                        <button class="btn" title="Guardar" onclick="actualizar_todo()">Aplicar</button>
                    </div>
                    <div style="float:right;margin-top:0px;padding:7px;">
                        PRECIO 1<input type="radio" id="pre_precios1" name="pre_precio" value="0" onclick="seleccionar_todo_prec1()" onblur="desactivar1()"/>
                        PRECIO 2<input type="radio" id="pre_precios2" name="pre_precio" value="0" onclick="seleccionar_todo_prec2()"/>
                        CAMBIAR<input type="radio" id="camb" name="pre_precio" value="0" />
                        <button class="btn" title="Aplicar Cambios" onclick="cambiar_precios()">Aplicar</button>
                    </div>

                    <font style="float:right" id="txt_load">Cargar Datos:</font>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" />
<!--                        BODEGA:<select id="bodega" name="bodega">
                            <option value="">SELECCIONE</option>
                            <option value="0">Bodega1</option>
                            <option value="1">Bodega2</option>                             
                        </select>-->
                        IVA:<select id="ivab" name="ivab">
                            <option value="">SELECCIONE</option>
                            <option value="14">14%</option>
                            <option value="0">0%</option>                             
                            <option value="EX">EX</option>                             
                            <option value="NO">NO</option>                             
                        </select>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>

                    </form> 
                </center>
            </caption>
            <!--</table>-->  
            <form  autocomplete="off" id="frm_save" name="frm_save">
                <table id="tbl" style="width:100%">  
                    <!--Nombres de la columna de la tabla-->
                    <thead id="head">
                    <th>No</th>
                    <th hidden>Bodega</th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Precio 1</th>
                    <th>Precio 2</th>
                    <th>Descuento %</th>
                    <th>Iva</th>
                    <th>Acciones</th>
                    </thead>
                    <!------------------------------------->
                    <tbody id="tbody">
                        <?PHP
                        $n = 0;
                        while ($rst = pg_fetch_array($cns)) {
                            $n++;

                            if ($rst['pro_tabla'] == '0') {
                                $rst['pro_tabla'] = 'INDUSTRIAL';
                            }
                            ?>
                            <tr>
                                <td><?php echo $n ?></td>
                                <td hidden><?php echo $rst['pro_tabla'] ?></td>
                                <td><?php echo $rst['pro_codigo'] ?></td>
                                <td><?php echo $rst['pro_descripcion'] ?></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_precio' . $n ?>"  value="<?php echo number_format($rst['pre_precio'], 2) ?>" style="text-align:right" disabled /><input type="radio" class="precios1" id="<?php echo 'pre_vald_precio1' . $n ?>" name="<?php echo 'pre_precios' . $n ?>" value="" ></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_precio2' . $n ?>"  value="<?php echo number_format($rst['pre_precio2'], 2) ?>" style="text-align:right" disabled /><input type="radio" class="precios2" id="<?php echo 'pre_vald_precio2' . $n ?>" name="<?php echo 'pre_precios' . $n ?>" value=""></td>
                                <td  align="center"><input type ="text" size="10"  id="<?php echo 'pre_descuento' . $n ?>"  value="<?php echo number_format($rst['pre_descuento'], 2) ?>" style="text-align:right" disabled /></td>
                                <td align="center"><select id="<?php echo 'pre_iva' . $n ?>" value="<?php echo $rst['pre_iva'] ?>" disabled >
                                        <option value="14">14%</option>
                                        <option value="0">0%</option>                            
                                        <option value="EX">EX</option>                            
                                        <option value="NO">NO</option>                            
                                    </select>
                                    <script>
                                        idt = '<?php echo 'pre_iva' . $n ?>';
                                        pr1_id = '<?php echo 'pre_vald_precio1' . $n ?>';
                                        pr2_id = '<?php echo 'pre_vald_precio2' . $n ?>';
                                        pr1 = '<?php echo $rst['pre_vald_precio1'] ?>';
                                        $('#' + idt).val('<?php echo $rst[pre_iva] ?>');

                                        if (pr1 == 1) {
                                            $('#' + pr1_id).attr('checked', true);
                                        } else {
                                            $('#' + pr2_id).attr('checked', true);
                                        }

                                    </script>
                                </td>
                                <td align="center">
                                    <?php
                                    if ($Prt->edition == 0) {
                                        ?>
                                        <img src="../img/save.png"  class="auxBtn" onclick="save(<?php echo $rst[pre_id] ?>,<?php echo $n ?>, 0)">

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
                </table>   
            </form>
    </body>    
</html>


