<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_vendedores.php'; //cambiar clsClase_productos
include_once '../Clases/clsClase_industrial_genericos.php'; //cambiar clsClase_productos
$Clase_industrial_genericos = new Clase_industrial_genericos();

if (isset($_GET[txt])) {
    $txt = trim(strtoupper($_GET[txt]));
    $txt1 = "and (pv.det_cod_producto like '%$txt%' or pv.det_descripcion like '%$txt%')";
    $cns = $Clase_industrial_genericos->lista_totalpedidos($txt1);
} else {
    $cns = $Clase_industrial_genericos->lista_totalpedidos();
}
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
            });
            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(cod, tot, pro, m)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                if (tot <= 0) {


                    switch (cod)
                    {
                        case 0://PRODUCTOS_COMERCIALES

                            if (cod == 0) {

                                parent.document.getElementById('contenedor2').rows = "*,0%";
                                var r = confirm("Esta Seguro de realizar el registro?");
                                if (r == true) {
                                    var data = Array(
                                            cod,
                                            -tot,
                                            pro
                                            );
                                    fields = $('#frm_save').serialize();
                                    $.ajax({
                                        beforeSend: function () {
                                            loading('visible');
                                        },
                                        type: 'POST',
                                        url: 'actions_industrial_genericos.php',
                                        data: {op: 0, 'data[]': data, cod: cod, 'field[]': fields}, //op sera de acuerdo a la acion que le toque
                                        success: function (dt) {
                                            if (dt == 0) {
                                                loading('hidden');
                                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_genericos.php';
                                            } else {
                                                alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                                                loading('hidden');
                                            }
                                        }
                                    })

                                } else {
                                    return false;
                                }
                            }

                        case 3://PLUMON
                            if (cod == 3) {
                                parent.document.getElementById('contenedor2').rows = "*,0%";
                                var r = confirm("Esta Seguro de realizar el registro?");
                                if (r == true) {
                                    var data = Array(
                                            cod,
                                            -tot,
                                            pro
                                            );
                                    fields = $('#frm_save').serialize();
                                    $.ajax({
                                        beforeSend: function () {
                                            loading('visible');
                                        },
                                        type: 'POST', url: 'actions_industrial_genericos.php',
                                        data: {op: 0, 'data[]': data, cod: cod, 'field[]': fields}, //op sera de acuerdo a la acion que le toque
                                        success: function (dt) {
                                            if (dt == 0) {
                                                loading('hidden');
                                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_genericos.php';
                                            } else {
                                                alert(dt);
                                                loading('hidden');
                                            }
                                        }
                                    })
                                } else {
                                    return false;
                                }
                            }
                        case 4://PADDING
                            if (cod == 4) {
                                parent.document.getElementById('contenedor2').rows = "*,0%";
                                var r = confirm("Esta Seguro de realizar el registro?");
                                if (r == true) {

                                    var data = Array(
                                            cod,
                                            -tot,
                                            pro
                                            );
                                    fields = $('#frm_save').serialize();
                                    $.ajax({
                                        beforeSend: function () {
                                            loading('visible');
                                        },
                                        type: 'POST',
                                        url: 'actions_industrial_genericos.php',
                                        data: {op: 0, 'data[]': data, cod: cod, 'field[]': fields}, //op sera de acuerdo a la acion que le toque
                                        success: function (dt) {
                                            if (dt == 0) {
                                                loading('hidden');
                                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_genericos.php';
                                            } else {
                                                alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                                                loading('hidden');
                                            }
                                        }
                                    })
                                } else {
                                    return false;
                                }
                            }
                        case 5://ECOCAMBRELLA
                            parent.document.getElementById('contenedor2').rows = "*,0%";
                            if (cod == 5) {
                                var r = confirm("Esta Seguro de realizar el registro?");
                                if (r == true) {
                                    var data = Array(
                                            cod,
                                            -tot,
                                            pro
                                            );
                                    fields = $('#frm_save').serialize();
                                    $.ajax({
                                        beforeSend: function () {
                                            loading('visible');
                                        },
                                        type: 'POST',
                                        url: 'actions_industrial_genericos.php',
                                        data: {op: 0, 'data[]': data, cod: cod, 'field[]': fields}, //op sera de acuerdo a la acion que le toque
                                        success: function (dt) {
                                            if (dt == 0) {
                                                loading('hidden');
                                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_genericos.php';
                                            } else {
                                                alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                                                loading('hidden');
                                            }
                                        }
                                    })
                                } else {
                                    return false;
                                }
                            }
                        case 6://GEOTEXTIL
                            if (cod == 6) {

                                parent.document.getElementById('contenedor2').rows = "*,0%";
                                var r = confirm("Esta Seguro de realizar el registro?");

                                if (r == true) {
                                    var data = Array(
                                            cod,
                                            -tot,
                                            pro
                                            );
                                    fields = $('#frm_save').serialize();
                                    $.ajax({
                                        beforeSend: function () {
                                            loading('visible');
                                        },
                                        type: 'POST',
                                        url: 'actions_industrial_genericos.php',
                                        data: {op: 0, 'data[]': data, cod: cod, 'field[]': fields}, //op sera de acuerdo a la acion que le toque
                                        success: function (dt) {
                                            if (dt == 0) {
                                                loading('hidden');
                                                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_industrial_genericos.php';
                                            } else {
                                                alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                                                loading('hidden');
                                            }
                                        }
                                    })
                                } else {
                                    return false;
                                }
                            }
                    }
                } else {
                    parent.document.getElementById('bottomFrame').src = '';
                    parent.document.getElementById('contenedor2').rows = "*,0%";
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn69{
                background:black;
                color:white;
                border: solid 1px white;
            }
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
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
                <center class="cont_title" ><?PHP echo 'PEDIDOS GENERADOS ' ?></center>
                <center class="cont_finder">

                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>"/>
                        <button class="btn" title="Buscar" onclick="frmSearch.submit()">Buscar</button>
                    </form> 
                </center>
            </caption>
            <thead>
                <tr>
                    <th colspan="7">Producto terminado</th>
                </tr>
                <tr>
                    <th>No</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Inventarios</th>
                    <th>Pedidos</th>
                    <th>Deuda</th>
                </tr>
            </thead>
            <!------------------------------------->
            <tbody id="tbody">
                <?PHP
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $sum = (($rst[inventarios] - $rst[pedidos]));
                    $det = $rst[det_descripcion];
                    $ev = "onclick='auxWindow($rst[emp_id],$sum,$rst[id])'";
                    $n++;
                    ?>
                    <tr style="height: 30px" onclick="auxWindow(1,<?php echo $rst['mov_id'] ?>, 1)">
                        <td <?php echo $ev ?>><?php echo $n ?></td>
                        <td <?php echo $ev ?>><?php echo $rst['det_cod_producto'] ?></td>
                        <td <?php echo $ev ?>><?php echo $rst['det_descripcion'] ?></td>
                        <td <?php echo $ev ?>><?php echo $rst['inventarios'] ?></td>
                        <td <?php echo $ev ?>><?php echo $rst['pedidos'] ?></td>
                        <td <?php echo $ev ?>><?php echo $deu = $rst['inventarios'] - $rst['pedidos']; ?></td>

                    </tr>
                    <?PHP
                }
                ?>  

            </tbody>
        </table>            
    </body>   
</html>

