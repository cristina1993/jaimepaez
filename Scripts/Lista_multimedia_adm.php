<?php
include_once '../Clases/clsClase_multimedia.php';
include_once '../Includes/permisos.php';
$Mlt = new Multimedia();
if (isset($_GET[search])) {
    $desde = $_GET[desde];
    $hasta = $_GET[hasta];
    $cns = $Mlt->lista_multimedia_fecha($desde, $hasta);
} else {
    $desde = date('Y-m-d');
    $hasta = date('Y-m-d');
    $cns = $Mlt->lista_multimedia();
}
$rst_sms = pg_fetch_array($Mlt->lista_sms_mult());
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Tipo de pago</title>
    <head>
        <script>
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "desde", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "hasta", ifFormat: "%Y-%m-%d", button: "im-hasta"});
            });

            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function auxWindow(a, id)
            {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                parent.document.getElementById('contenedor2').rows = "*,50%";
                switch (a)
                {
                    case 0://Nuevo
                        frm.src = '../Multimedia/Form_multimedia.php';
                        look_menu();
                        break;
                    case 1://Editar
                        frm.src = '../Multimedia/Form_multimedia.php?id=' + id;
                        look_menu();
                        break;
                    case 2://Ver PDF
                        frm.src = '../Multimedia/Form_pdf_view.php?id=' + id;
                        look_menu();
                        break;
                }

            }

            function del(id, f)
            {
                var r = confirm("Esta Seguro de eliminar este elemento?");
                if (r == true) {
                    $.post("actions_multimedia.php", {op: 1, id: id, file: f}, function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    return false;
                }
            }

            function cancelar() {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_multimedia_adm.php';
            }

            function save_sms() {
                $.post("actions_multimedia.php", {op: 2, id: sms_txt.value}, function (dt) {
                    if (dt == 0) {
                        alert('Guardado Correctamente');
                    } else {
                        alert(dt);
                    }
                });
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
        </script> 
        <style>
            #mn197{
                background:black;
                color:white;
                border: solid 1px white;
            }
            .pdfs{
                font-weight:bolder;
            }
            .pdfs:hover{
                text-decoration:underline; 
                text-decoration-color:#0000CC;
                color:#0000CC;
            }
            input[readonly]{
                background:#f8f8f8;
                color:black; 
            }
            #sms_help{
                position:absolute;
                right:10px; 
                bottom:10px;
                background:#f8f8f8;
                border: solid 1px #568da7; 
                border-radius:5px;  
                box-shadow:3px 3px 7px #000; 
            }
            #sms_help textArea{
                border:none; 
            }
            #sms_help div{
                background:#568da7; 
                color:#f8f8f8; 
                text-align:center;
                width:100%; 
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando">Por Favor Espere...</div>
        <div id="grid" onclick="alert(' ¡ Tiene Una Accion Habilitada ! \n Debe Guardar o Cancelar para habilitar es resto de la pantalla')"></div>        
        <div id="sms_help">
            <div><font>Mensaje General</font></div>
            <textarea  rows="10" cols="80" id="sms_txt" title="<?php echo $rst_sms[sms_fecha] . ' ' . $rst_sms[sms_user] ?>" ><?php echo $rst_sms[sms_sms] ?></textarea><br>
            <button id="save" onclick="save_sms()">Guardar</button>
        </div>    
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
                <center class="cont_title" >MULTIMEDIA</center>
                <center class="cont_finder">
                    <a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Subir</a>
                    <form method="GET" id="frmSearch" name="frmSearch" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Desde:<input type="text" name="desde" size="12" id="desde"  readonly value="<?php echo $desde ?>"/>
                        <img src="../img/calendar.png" id="im-desde"   />
                        Hasta:<input type="text" name="hasta" size="12" id="hasta" readonly value="<?php echo $hasta ?>"/>
                        <img src="../img/calendar.png" id="im-hasta" />
                        <input type="submit" id="search" name="search" value="Buscar" class="auxBtn" style="float:none;color:white;font-weight:bolder   " />
                    </form>  
                </center>
            </caption>
            <!--Nombres de la columna de la tabla-->
            <thead>
                <tr>
                    <th width="50px">No</th>
                    <th>Archivo</th>
                    <th>Fecha/Reg</th>
                    <th>Usuario</th>
                    <th>Informacion</th>
                    <td>Acciones</td>
                </tr>
            </thead>
            <!------------------------------------->

            <tbody id="tbody">
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td onclick="auxWindow(2, '<?php echo $rst[mlt_archivo] ?>')" class="pdfs"  ><img src="../img/orden.png" width="16px"/><?php echo '  ' . $rst[mlt_archivo] ?></td>
                        <td><?php echo $rst[mlt_fecha] ?></td>
                        <td><?php echo $rst[mlt_user] ?></td>
                        <td><?php echo $rst[mlt_informacion] ?></td>
                        <td>
                            <img src="../img/del_reg.png" class="auxBtn" width="16px" onclick="del(<?php echo $rst[mlt_id] ?>, '<?php echo $rst[mlt_archivo] ?>')" />
                        </td>
                    </tr>
                    <?php
                }
                ?>                    
            </tbody>


        </table>            

    </body>    
</html>


