<?php
include_once '../Includes/permisos.php';
$ident=$rst_mod[emi_identificacion];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title>Lista Ats</title>
    <head>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css"  href="../CSS/modal-style.css" media="all" />
        <script type="text/javascript" src="../JS/jquery-fallr-2.0.min.js"></script>    
        <script>
            $(function () {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            });


            function auxWindow(o, a, m) {
                if (form_ats.value != 0 && mes_ats.value != 0) {
                    frm = parent.document.getElementById('bottomFrame');
                    main = parent.document.getElementById('mainFrame');

                    e = '<?php echo $ident ?>';
                    switch (o)
                    {
                        case 0://PDF
                            parent.document.getElementById('contenedor2').rows = "*,90%";
                            frm.src = '../Scripts/frm_ats.php?anio=' + a + '&mes=' + m + '&emi_id=' + e;
                            break;
                        case 1://XML
                            // parent.document.getElementById('contenedor2').rows = "*,0%";
                            // frm.src = 'http://186.4.200.125/tivka/ats/xml?anio=' + a + '&mes=' + m + '&emi_id=' + e;
                            // alert('../Includes/ats.php?anio=' + a + '&mes=' + m);
                            window.location = '../Includes/ats.php?anio=' + a + '&mes=' + m;

                            break;
                    }
                } else {
                    alert('Seleccione el formulario y el mes a consultar');
                }
            }

            function mostrar()
            {
                $('#lblanio_ats').show();
                $('#lblmes_ats').show();
                $('#anio_ats').show();
                $('#mes_ats').show();

            }
        </script> 
        <style>

            input[type=button]{
                text-transform: uppercase;
                width: 70px;
            }
            .cont_finder{
                height:40px; 
            } 
            .cont_finder div{
                margin-top:10px; 
                float:left; 
                margin-left:10px; 
            }
            .btn{
             margin:5px;    
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
                <center class="cont_title" >FORMULARIOS</center>
                <center class="cont_finder">
                   <form method="GET" id="frmSearch" name="frm1">
                        FORMULARIO:
                        <select id="form_ats" onchange="mostrar()">
                            <option value="0">SELECCIONE</option>
                            <option value="1">ATS</option>

                        </select>
                        <label id="lblanio_ats" hidden>AÑO:</label>
                        <select id="anio_ats" hidden>
                            <?php
                            $fec_act = date('Y-m-d');
                            $anio = date('Y');
                            $InicioYear = 2000; // Aqui coloca el año de inicio, el que estará más abajo
                            $MinYear = $InicioYear - 1;
                            $ActualYear = $anio; // Aquí coloca el año actual 
                            for ($i = $ActualYear; $i > $MinYear; $i--) {
                                echo '<option value="' . $i . '">' . $i . '</option>'; // Aqui puedes agregarle cosas como class, name, id.
                            }
                            ?>
                        </select>
                        <label id="lblmes_ats" hidden>MES:</label>
                        <select id="mes_ats" hidden>
                            <option value="0">SELECCIONE</option>
                            <option value="01">ENERO</option>
                            <option value="02">FEBRERO</option>
                            <option value="03">MARZO</option>
                            <option value="04">ABRIL</option>
                            <option value="05">MAYO</option>
                            <option value="06">JUNIO</option>
                            <option value="07">JULIO</option>
                            <option value="08">AGOSTO</option>
                            <option value="09">SEPTIEMBRE</option>
                            <option value="10">OCTUBRE</option>
                            <option value="11">NOVIEMBRE</option>
                            <option value="12">DICIEMBRE</option>
                        </select>
                    
                    <input type="button" class="btn" id="save" onclick="auxWindow(0, anio_ats.value, mes_ats.value)" value="GENERAR">
                        <input type="button" class="btn" id="save" onclick="auxWindow(1, anio_ats.value, mes_ats.value)" value="XML">
                   </form>
                </center>
            </caption>
        </table>  
    </body>    
</html>

