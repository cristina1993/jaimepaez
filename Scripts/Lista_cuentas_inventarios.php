<?php
include_once("../Clases/clsClase_cuentas_inventarios.php");
include_once '../Includes/permisos.php';
$Set = new Clase_cuentas_inventarios();
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
                    n = $(this).html();
                    $('#descrip' + n).css({borderColor: ""});
                });
                $("#form_save").find('.det').each(function () {
                    n = $(this).html();
                    if ($('#codigod' + n).val().length == 0 && $('#codigoh' + n).val().length == 0) {
                        $('#descrip' + n).css({borderColor: "red"});
                        return v = 1;
                    }
                });

                if (v != 0) {
                    alert('Ingrese un codigo en el debe o haber');
                    return false;
                }

                if (confirm('Esta seguro de realizar los cambios?') == true) {
                    var fields = Array();
                    $("#form_save").find('.det').each(function () {
                        n = $(this).html();
                        des = 'BODEGA=' + $('#bod' + n).html() + '&TRANSACCION=' + $('#descrip' + n).html() + "&DEBE=" + $('#codigod' + n).val() + "&HABER=" + $('#codigoh' + n).val();
                        fields.push(des);
                    });
                    fields.push('');

                    var data = Array();
                    $("#form_save").find('.det').each(function () {
                        n = $(this).html();
                        dat = $('#cin_id' + n).val() + "&" + $('#trs_d' + n).val() + "&" + $('#trs_h' + n).val();
                        data.push(dat);
                    });
                    $.post("actions_cuentas_inventarios.php", {op: 0, 'data[]': data, 'fields[]': fields},
                            function (dt) {
                                if (dt == 0)
                                {
                                    parent.document.getElementById('mainFrame').src = '../Scripts/Lista_cuentas_inventarios.php';
                                } else {
                                    alert(dt);
                                }
                            });
                } else {
                    return false;
                }
            }


            function load_codigo(obj, i) {
                if (i == 0) {
                    n = 'd' + obj.lang;
                } else {
                    n = 'h' + obj.lang;
                }
                $.post("actions_cuentas_inventarios.php", {op: 1, id: obj.value},
                        function (dt) {
                            dat = dt.split('&');
                            if (dat[3] == 1) {
                                alert('La cuenta se encuentra Anulada');
                                $('#trs_' + n).val('0');
                                $('#codigo' + n).val('');
                                $('#cta_descripcion' + n).html('');
                            } else {
                                if (dat[0] != '') {
                                    $('#trs_' + n).val(dat[0]);
                                    $('#codigo' + n).val(dat[1]);
                                    $('#cta_descripcion' + n).html(dat[2]);
                                } else {
                                    $('#trs_' + n).val('0');
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
                <center class="cont_title" >CONFIGURACION DE CUENTAS DE INVENTARIO</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:right;margin-top:7px;padding:7px;" title="Guardar" onclick="cambios()" >GUARDAR</a>
                </center>
            </caption>
            <thead>
            <th align="left">No</th>
            <th align="left">Bodega</th>
            <th align="left">Descripcion</th>
            <th align="left">Codigo Debe</th>
            <th align="left">Descripcion Debe</th>
            <th align="left">Codigo Haber</th>
            <th align="left">Descripcion Haber</th>
        </thead>
        <tbody id="form_save">
            <?php
            $n = 0;
            $b = 0;

            $cns = $Set->lista_transacciones();
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $rst1 = pg_fetch_array($Set->lista_plan_cuentas_id($rst[cin_debe]));
                $rst2 = pg_fetch_array($Set->lista_plan_cuentas_id($rst[cin_haber]));
                if (empty($rst1[pln_id])){
                    $rst1[pln_id]=0;
                }
                if (empty($rst2[pln_id])){
                    $rst2[pln_id]=0;
                }
                echo "<tr >
                    <td class='det' align='left'>$n</td>
                    <td style='width: 300px' align='left'  id='bod$n'>$rst[emi_nombre_comercial]</td>
                    <td style='width: 300px' id='descrip$n'>$rst[trs_descripcion]
                        <input type='hidden' id='cin_id$n'  lang='$n' value='$rst[cin_id]'>
                    </td>
                    <td style='width: 300px'>
                        <input  type='text' id='codigod$n'  lang='$n' value='$rst1[pln_codigo]' size='50' style='text-align: right' list='cuentas' onchange='load_codigo(this,0)'/>
                        <input type='hidden' id='trs_d$n'  lang='$n' value='$rst1[pln_id]'>
                    </td>
                    <td style='width: 300px' id='cta_descripciond$n'>$rst1[pln_descripcion]</td>
                    <td style='width: 300px'>
                        <input type='text' id='codigoh$n'  lang='$n' value='$rst2[pln_codigo]' size='50' style='text-align: right' list='cuentas' onchange='load_codigo(this,1)'/>
                        <input type='hidden' id='trs_h$n'  lang='$n' value='$rst2[pln_id]'>
                    </td>
                    <td style='width: 400px' id='cta_descripcionh$n'>$rst2[pln_descripcion]</td>
                </tr>";
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
    var e = '<?php echo $emi ?>';
    $('#local').val(e);
</script>