<?php
include_once("../Clases/clsClase_config_cuentas.php");
include_once '../Includes/permisos.php';
$Set = new Clase_config_cuentas();

if (isset($_GET[local])) {
    $emi=$_GET[local];
    $cns = $Set->lista_cuentas($emi);
}
$cns_loc = $Set->lista_bodegas();
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Formulas</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });
            function auxWindow(a, id, sts)
            {
                frm = parent.document.getElementById('bottomFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_usuario.php';
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_usuario.php?id=' + id;
                        break;
                    case 2://Cambiar Estado
                        if (confirm('Esta seguro de cambiar de Estado a este Usuario?') == true)
                        {
                            if (sts == 't')
                            {
                                sts = 'f';
                            } else {
                                sts = 't';
                            }
                            data = Array(sts);
                            $.post("actions.php", {act: 9, 'data[]': data, id: id},
                            function (dt) {
                                if (dt == 0)
                                {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_usuarios.php';
                                    parent.document.getElementById('bottomFrame').src = '';
                                } else {
                                    alert(dt);
                                }
                            })

                        }

                        break;
                    case 3://Permisos
                        frm.src = '../Scripts/Form_permisos.php?id=' + id;
                        break;
                }

            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }



            function cambios() {
                v = 0;
                $("#form_save").find('.det').each(function () {
                    if (this.value.length == 0) {
                        $(this).focus();
                        $(this).css({borderColor: "red"});
                        return v = 1;
                    }
                });

                if (v != 0) {
                    return false;
                }

                if (confirm('Esta seguro de realizar los cambios?') == true) {
                    var fields = Array();
                    $("#form_save").find('.det').each(function () {
                        n = this.lang;
                        des = $('#descrip' + n).html() + "=" + this.value;
                        fields.push(des);
                    });
                    fields.push('');

                    var data = Array();
                    $("#form_save").find('.det').each(function () {
                        n = this.lang;
                        dat = $('#cas_id' + n).val() + "&" + $('#pln_id' + n).val();
                        data.push(dat);
                    });
                    $.post("actions_config_cuentas.php", {op: 0, 'data[]': data, 'fields[]': fields},
                    function (dt) {
                        if (dt == 0)
                        {
                            parent.document.getElementById('mainFrame').src = '../Scripts/Lista_config_cuentas.php';
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }


            function load_codigo(obj) {
                n = obj.lang;
                $.post("actions_config_cuentas.php", {op: 1, id: obj.value},
                function (dt) {
                    dat = dt.split('&');
                    if (dat[3] == 1) {
                        alert('La cuenta se encuentra Anulada');
                        $('#pln_id' + n).val('');
                        $('#codigo' + n).val('');
                        $('#cta_descripcion' + n).html('');
                    } else {
                        if (dat[0] != '') {
                            $('#pln_id' + n).val(dat[0]);
                            $('#codigo' + n).val(dat[1]);
                            $('#cta_descripcion' + n).html(dat[2]);
                        } else {
                            $('#pln_id' + n).val('');
                            $('#codigo' + n).val('');
                            $('#cta_descripcion' + n).html('');
                        }
                    }
                });


            }
        </script>
        <style>
            #mn2{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .totales{
                background:#ccc;
                color:black;
                font-weight:bolder; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
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
                <center class="cont_title" >LISTA DE CONFIGURACIONES CUENTAS</center>
                <center class="cont_finder">
                    <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>-->
                    <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Guardar" onclick="cambios()" >GUARDAR</a>

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Local: <select id="local" name="local"> 
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rst_locales = pg_fetch_array($cns_loc)) {
                                echo "<option value='$rst_locales[emi_cod_punto_emision]'>$rst_locales[emi_nombre_comercial]</option>";
                            }
                            ?>
                        </select>
                        <input type="text" id="cliente" hidden>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                        <a href="#" ><img src="../img/finder.png" /></a>                                                                    
                    </form>  
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Descripcion</th>
            <th align="left">Codigo Cuenta</th>
            <th align="left">Descripcion Cuenta</th>
        </thead>
        <tbody id="form_save">
            <?php
            $n = 0;
            $grup = '';
            $grup2 = '';
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                switch ($rst[cas_tipo_doc]) {
                    case 1:
                        $doc = 'FACTURA';
                        break;
                    case 2:
                        $doc = 'NOTA DE CREDITO';
                        break;
                    case 3:
                        $doc = 'NOTA DE DEBITO';
                        break;
                    case 4:
                        $doc = 'RETENCION';
                        break;
                    case 5:
                        $doc = 'REG. FACTURA';
                        break;
                    case 6:
                        $doc = 'REG. NOTA DE CREDITO';
                        break;
                    case 7:
                        $doc = 'REG. NOTA DE DEBITO';
                        break;
                    case 8:
                        $doc = 'REG. RETENCION';
                        break;
                }
                $rst1 = pg_fetch_array($Set->lista_plan_cuentas_id($rst[pln_id]));
                if ($grup != $rst[cas_tipo_doc]) {
                    echo "<tr>
                      <th class='totales' align='center' colspan='4'>$doc</td>
                          </tr>";
                }
                if ($grup2 != $rst[cas_orden]) {
                    if ($rst[cas_orden] == 1) {
                        echo "<tr>
                      <th class='totales' align='center' colspan='4'>ANULACION $doc</td>
                          </tr>";
                    }
                }
                echo "<tr>
                    <td align='left'>$n</td>
                    <td style='width: 400px' id='descrip$n'>$rst[cas_descripcion]</td>
                    <td style='width: 400px'>
                        <input class='det' type='text' id='codigo$n'  lang='$n' value='$rst1[pln_codigo]' size='50' style='text-align: right' list='cuentas' onchange='load_codigo(this)'/>
                        <input type='hidden' id='pln_id$n'  lang='$n' value='$rst1[pln_id]'>
                        <input type='hidden' id='cas_id$n'  lang='$n' value='$rst[cas_id]'>
                                </td>
                    <td id='cta_descripcion$n'>$rst1[pln_descripcion]</td>
                </tr>";
                $grup = $rst[cas_tipo_doc];
                $grup2 = $rst[cas_orden];
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<datalist id="cuentas">
    <?php
    $cns_ctas = $Set->lista_plan_cuentas();
    while ($rst_cta = pg_fetch_array($cns_ctas)) {
        echo "<option value='$rst_cta[pln_id]'> $rst_cta[pln_codigo] $rst_cta[pln_descripcion]</option>";
    }
    ?>
</datalist>
<script>
var e='<?php echo $emi?>';
$('#local').val(e);
</script>