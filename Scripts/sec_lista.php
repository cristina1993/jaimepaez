<?php
session_start();
include_once '../Includes/permisos.php';
include_once '../Clases/clsSecciones.php';
$Sec = new Secciones();
if (isset($_GET[show])) {
    $_SESSION[ger] = $_GET[ger];
    $_SESSION[div] = $_GET[div];
    if ($_GET[ger] != '0') {
        if ($_GET[div] == '0') {
            $cns = $Sec->listaSeccionGerencias($_GET[ger]);
            switch ($_GET[ger]) {
                case 'T': $tm = 'selected';
                    $qt = '';
                    $gy = '';
                    break;
                case 'Q': $tm = '';
                    $qt = 'selected';
                    $gy = '';
                    break;
                case 'G': $tm = '';
                    $qt = '';
                    $gy = 'selected';
                    break;
            }
        } else {
            $cns = $Sec->listaSeccionGerenciasDivision($_GET[ger], $_GET[div]);
            switch ($_GET[ger]) {
                case 'T': $tm = 'selected';
                    $qt = '';
                    $gy = '';
                    break;
                case 'Q': $tm = '';
                    $qt = 'selected';
                    $gy = '';
                    break;
                case 'G': $tm = '';
                    $qt = '';
                    $gy = 'selected';
                    break;
            }
            switch ($_GET[div]) {
                case 'P': $pl = 'selected';
                    $pt = '';
                    $gn = '';
                    $mn = '';
                    break;
                case 'C': $pl = '';
                    $pt = 'selected';
                    $gn = '';
                    $mn = '';
                    break;
                case 'G': $pl = '';
                    $pt = '';
                    $gn = 'selected';
                    $mn = '';
                    break;
                case 'M': $pl = '';
                    $pt = '';
                    $gn = '';
                    $mn = 'selected';
                    break;
            }
        }
    }
} elseif ($_SESSION[ger] != '0' && !empty($_SESSION[ger])) {
    if ($_SESSION[div] == '0') {
        $cns = $Sec->listaSeccionGerencias($_SESSION[ger]);
        switch ($_SESSION[ger]) {
            case 'T': $tm = 'selected';
                $qt = '';
                $gy = '';
                break;
            case 'Q': $tm = '';
                $qt = 'selected';
                $gy = '';
                break;
            case 'G': $tm = '';
                $qt = '';
                $gy = 'selected';
                break;
        }
    } else {
        $cns = $Sec->listaSeccionGerenciasDivision($_SESSION[ger], $_SESSION[div]);
        switch ($_SESSION[ger]) {
            case 'T': $tm = 'selected';
                $qt = '';
                $gy = '';
                break;
            case 'Q': $tm = '';
                $qt = 'selected';
                $gy = '';
                break;
            case 'G': $tm = '';
                $qt = '';
                $gy = 'selected';
                break;
        }
        switch ($_SESSION[div]) {
            case 'P': $pl = 'selected';
                $pt = '';
                $gn = '';
                $mn = '';
                break;
            case 'C': $pl = '';
                $pt = 'selected';
                $gn = '';
                $mn = '';
                break;
            case 'G': $pl = '';
                $pt = '';
                $gn = 'selected';
                $mn = '';
                break;
            case 'M': $pl = '';
                $pt = '';
                $gn = '';
                $mn = 'selected';
                break;
        }
    }
} else {
    $cns = $Sec->listaSecciones();
}
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>Lista Secciones</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                '';
            });
            
            function look_menu() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "visible";
                grid = document.getElementById('grid');
                grid.style.visibility = "visible";
            }

            function editar(a, id) {
                frm = parent.document.getElementById('bottomFrame');
                main = parent.document.getElementById('mainFrame');
                switch (a)
                {
                    case 0:
                        parent.document.getElementById('contenedor2').rows = "*,80%";
                        frm.src = '../Scripts/sec_formulario.php?id=' + id;
                        look_menu();
                        break;
                }
            }


//            function editar(id) {
//                emailwindow = dhtmlmodal.open("EmailBox", "iframe", "sec_formulario.php?id=" + id, "Editar Seccion", "width=450px,height=350px,center=1,resize=0,scrolling=1", "recal")
//            }
//            function nuevo()
//            {
//                emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'sec_formulario.php', 'Registrar Nueva Seccion', 'width=450px,height=350px,center=1', "recal")
//            }

        </script>
    </head>
    <body >
        <!--  Buscador  -->
        <!--<form method="get" >-->
            <table align="left" id="tbl"  style="width:100%">
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
                    <center class="cont_title" ><?php echo "ADMINISTRACION DE SECCIONES" ?></center>
                    <center class="cont_finder">

                    </center>
                </caption>
                <thead>                
                    <tr>
                        <th align="Center">No</th>
                        <th align="Center">Nombre Corto</th>
                        <th align="Center">Nombre Largo</th>
                        <th align="Center">Codigo</th>
                        <th align="Center">Division</th>
                        <th align="Center">Gerencia</th>
                        <th align="Center" <?php echo $prt->edition ?> >Editar</th>
                    </tr>                        
                </thead>
                <tbody>

                    <?php
                    $cn = 0;
                    while ($rst = pg_fetch_array($cns)) {
                        $cn++;
                        $rst_div = pg_fetch_array($Sec->lista_una_division($rst[sec_area]));
                        $rst_ger = pg_fetch_array($Sec->lista_una_gerencia($rst_div[ger_id]));
                        switch ($rst['sec_gerencia']) {
                            case 'T':$g = 'TAMBILLO';
                                break;
                            case 'Q':$g = 'QUITO';
                                break;
                            case 'G':$g = 'GUAYAQUIL';
                                break;
                        }
                        switch ($rst['sec_area']) {
                            case 'P':$d = 'POLIETILENO';
                                break;
                            case 'C':$d = 'POLIURETANO';
                                break;
                            case 'M':$d = 'MANTENIMIENTO';
                                break;
                            case 'G':$d = 'GENERAL';
                                break;
                        }

                        $ext = '';
                        $imp = '';
                        $sel = '';
                        $alm = '';
                        $adm = '';
                        $opr = '';
                        if ($rst[sec_ext] == 't') {
                            $ext = 'checked';
                        }
                        if ($rst[sec_imp] == 't') {
                            $imp = 'checked';
                        }
                        if ($rst[sec_sel] == 't') {
                            $sel = 'checked';
                        }
                        if ($rst[sec_alm] == 't') {
                            $alm = 'checked';
                        }
                        if ($rst[sec_adm] == 't') {
                            $adm = 'checked';
                        }
                        if ($rst[sec_opr] == 't') {
                            $opr = 'checked';
                        }
                        ?>
                        <tr>
                            <td  align="left"><?PHP echo $cn ?></td>            
                            <td  align="left"><?PHP echo $rst['sec_nombre'] ?></td>
                            <td  align="left"><?PHP echo $rst['sec_descricpion'] ?></td>          
                            <td  align="left"><?PHP echo $rst['sec_codigo'] ?></td>
                            <td  align="left"><?PHP echo $rst_div[div_descripcion] ?></td>
                            <td  align="left"><?PHP echo $rst_ger[ger_descripcion] ?></td>
                            <td  align="center" <?php echo $prt->edition ?> ><a href="javascript:editar(0,<?PHP echo $rst['sec_id']; ?>)"><img src="../img/upd.png"></img></a></td>          
                        </tr>

                        <?php
                    }
                    ?>
                </tbody>     
            </table>
        <!--</form>-->
    </body>
</html>
