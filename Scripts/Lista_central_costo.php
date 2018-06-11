<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_central_costo.php'; //cambiar clsClase_productos
$Set = new Clase_central_costo();
if (isset($_GET[search])) {
    $txt = trim(strtoupper($_GET[txt]));
    if (!empty($txt)) {
        $texto = "where ctc_descripcion like '%$txt%'";
    }
    $cns = $Set->lista_central($texto);
}
$desde = date('Y-m-d');
$hasta = date('Y-m-d');
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
                Calendar.setup({inputField: desde, ifFormat: '%Y-%m-%d', button: im_desde});
                Calendar.setup({inputField: hasta, ifFormat: '%Y-%m-%d', button: im_hasta});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }


            function auxWindow(a, id, x)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Scripts/Form_central_costo.php?txt=' + $('#txt').val();//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Scripts/Form_central_costo.php?id=' + id + '&txt=' + $('#txt').val();//Cambiar Form_productos
                        parent.document.getElementById('contenedor2').rows = "*,50%";
                        look_menu();
                        break;
                    case 2://PDF
                        parent.document.getElementById('contenedor2').rows = "*,70%";
                        frm.src = '../Scripts/frm_pdf_central_costo.php?id=' + id + '&desde=' + $('#desde').val() + '&hasta=' + $('#hasta').val();
                        break;
                }
            }
            function del(id, op)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_central_costo.php", {id: id, op: op, nom: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }

            }

            function generar() {
                var n = 0;
                var data = Array();
                $('.chequeo').each(function () {
                    if ($(this).val() != '0') {
                        ids = $(this).attr('name');
                        num = $(this).val();
                        data.push(num+'--'+ids);
                        n++;
                    }
                })
                if (n == 0) {
                    alert('Seleccione al menos un registro')
                } else {
                    auxWindow(2, data);
                }
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
            .auxBtn{
                float:none; 
                color:white;
                font-weight:bolder; 
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
                <center class="cont_title" >LISTA CENTAL </center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo </a>
                    <form method="GET" style="width: 400px" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        BUSCAR POR:<input type="text" name="txt" size="15" id="txt" value="<?php echo $txt ?>" />
                        <!--<button class="btn" title="Buscar" name="search" onclick="frmSearch.submit()">Buscar</button>-->
                        <input type="submit" class="auxBtn" value="Buscar" id="search" name="search" />
                    </form>
                    <div id="cont_periodo" style="float:right;margin-top:0px;padding:0px;">
                        Perdiodo: 
                        Desde:<input type="text" id="desde" size="12" maxlength="10" value="<?php echo $desde ?>"/>
                        <img src="../img/calendar.png" id="im_desde" />
                        Hasta:<input type="text" id="hasta" size="12" maxlength="10" value="<?php echo $hasta ?>"/>
                        <img src="../img/calendar.png" id="im_hasta"/>
                        <button class="auxBtn" onclick="generar()">Generar</button>
                    </div>
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
            <th>No</th>
            <th>Descripcion</th>
            <th>Acciones</th>
            <th>Generar</th>

        </thead>
        <!------------------------------------->

        <tbody id="tbody">
            <?PHP
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                ?>
                <tr style="height: 30px">
                    <td <?php echo $ev ?>><?php echo $n ?></td>
                    <td <?php echo $ev ?>><?php echo $rst['ctc_descripcion'] ?></td>
                    <td align="center">
                        <?php
                        if ($Prt->delete == 0) {
                            ?>
                            <img src="../img/del_reg.png" width="12px" class="auxBtn" onclick="del('<?php echo $rst[ctc_descripcion] ?>', 1)">
                            <?php
                        }
                        if ($Prt->edition == 0) {
                            ?>
                            <img src="../img/upd.png" width="12px" class="auxBtn" onclick="auxWindow(1, '<?php echo $rst[ctc_descripcion] ?>', 0)">
                            <?php
                        }
                        ?>
                        <!--<img class="auxBtn" width="12px" src="../img/orden.png" onclick="auxWindow(2, '<?php echo $rst[ctc_descripcion] ?>')">-->   
                    </td>
                    <td align="center"> 
                        <!--<input type="checkbox" class="chequeo" lang="0" name="<?php echo $rst['ctc_descripcion'] ?>" id="<?php echo 'chk' . $n ?>"/>-->
                        <input type="number" class="chequeo" id="<?php echo 'num' . $n ?>" name="<?php echo $rst['ctc_descripcion'] ?>" onchange="orden()" value="0" style="width: 50px;" min="0"/>
                    </td>
                </tr>  
                <?PHP
            }
            ?>
        </tbody>
    </table>            
</body>    
</html>

