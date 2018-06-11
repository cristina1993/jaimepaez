<?php
try {
    session_start();
    include_once '../Clases/clsSecciones.php';
    include_once '../Includes/permisos.php';
    $_SESSION['dir'] = 'Secciones/sec_lista.php';
    $prt = new Permisos();
    $prt->Permit($_SESSION['usuid'], 41);
    $Sec = new Secciones();
    $id = $_GET[id];
    $x = 0;
    if (isset($_GET[id])) {
        $x = 1;
        $rst = pg_fetch_array($Sec->listaUnaSecciones($id));
        switch ($rst[sec_gerencia]) {
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
        switch ($rst[sec_area]) {
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
    }

    function nuevo() {
//  $Sec= new Secciones();
//        if ($Sec->insertSec
//                (array(
//                $_POST['col_codigo'],
//                $_POST['col_grupo'],
//                $_POST['col_descripcion'],
//                $_POST['col_referencia'],
//                $_POST['col_obs']
//                )) == true) {
//            echo "<script>alert('Registrao Correcto')</script>";
//            close();
//        } else {
//            echo pg_last_error();
//
//        }
    }

    function editar() {
        $Sec = new Secciones();
        $ext = 'f';
        $imp = 'f';
        $sel = 'f';
        $alm = 'f';
        $adm = 'f';
        $opr = 'f';
        if ($_POST[sec_ext] == 'on') {
            $ext = 't';
        }
        if ($_POST[sec_imp] == 'on') {
            $imp = 't';
        }
        if ($_POST[sec_sel] == 'on') {
            $sel = 't';
        }
        if ($_POST[sec_alm] == 'on') {
            $alm = 't';
        }
        if ($_POST[sec_adm] == 'on') {
            $adm = 't';
        }
        if ($_POST[sec_opr] == 'on') {
            $opr = 't';
        }
        if ($Sec->updateSec
                        (array($_POST['sec_descricpion'],
                    $_POST['sec_codigo'],
                    $_POST['sec_area'],
                    $_POST['sec_gerencia'],
                    $_POST['sec_nombre'],
                    $ext, $imp, $sel, $alm, $adm, $opr), $_GET[id]) == true) {
            echo "<script>alert('Actualizacion Correctamente')</script>";
            close();
        } else {
            echo pg_last_error();
        }
    }

    function eliminar() {
        $Sec = new Secciones();
        $col_id = $_GET['auxColId'];
        if ($Color->eliminarColor($col_id) == true) {
            echo "<script>alert('Color Eliminado Correctamente')</script>";
            close();
        } else {
            print_r(pg_last_error());
        }
    }

    function close() {
        echo "<script>window.history.go(0)</script>";
        echo "<script>parent.emailwindow.hide()</script>";
        echo "<script>window.close()</script>";
    }

    switch ($_POST['btn']) {
        case'Guardar':
            if ($x == 0) {
                nuevo();
            } else {
                editar();
            }
            break;
        case'Eliminar':
            eliminar();
            break;
    }
    ?>
    <html>
        <head>
            <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
            <META HTTP-EQUIV="Expires" CONTENT="-1">    
            <meta charset="utf-8">
            <script>
                function cancelar()
                {
                    window.history.go(0);
//                    parent.emailwindow.hide();
//                    window.close();
                }
                function del()
                {
                    var r = confirm("Esta Seguro de eliminar este elemento?");
                    if (r == true) {
                        return true;
                    } else {
                        return false;
                    }
                }

            </script>
            <style>
                input[type=text]{
                    text-transform: uppercase;
                }
            </style>
        </head>

        <body>
            <form action=""  onsubmit="" name="frmPerAgregar" autocomplete="off" enctype="multipart/form-data"  method="POST" id="frm_save" lang="0" >
                <table class="table" id="tbl_form">
                    <thead>
                        <tr><th colspan="9" >FORMULARIO DE CRITERIOS DE PERMISO<font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                    </thead>
                    <tr>
                        <td align="left" >Nombre Corto:</td>
                        <td><input type="text" size="15" maxlength="7" style="text-transform:uppercase " name="sec_nombre" id="sec_nombre" value="<?php echo $rst[sec_nombre] ?>" /></td>
                    </tr>
                    <tr>
                        <td align="left" >Nombre Largo:</td>
                        <td><input type="text" size="40" name="sec_descricpion" id="sec_nombre" style="text-transform:uppercase " value="<?php echo $rst[sec_descricpion] ?>" /></td>
                    </tr>
                    <tr>
                        <td  align="left" >Codigo:</td>
                        <td><input type="text" style="text-transform:uppercase" size="1" maxlength="1" name="sec_codigo" id="sec_codigo" onkeyup="this.value = this.value.replace(/[^A-Za-z]/, '')" value="<?php echo $rst[sec_codigo] ?>" />Un solo Caracter (A-Z)</td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Gerencia:</td>
                        <td hidden>
                            <select name="sec_gerencia" >
                                <option <?php echo $df ?> value="0" >Seleccione</option>
                            </select>    
                        </td>

                    </tr>
                    <tr>
                        <td  align="left" >Division:</td>
                        <td>
                            <select name="sec_area" id="sec_area" >
                                <option <?php echo $df ?> value="0" >Seleccione</option>
                                <?php
                                $cns_div = $Sec->lista_divisiones();
                                while($rst_div = pg_fetch_array($cns_div)){
                                    echo "<option value='$rst_div[div_id]'>$rst_div[div_descripcion]</option>";
                                }
                                ?>
                            </select>    
                        </td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Extrusion:</td>
                        <td align="center" hidden><input <?php echo $ext ?> type="checkbox" name="sec_ext" id="sec_ext"  /></td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Impresion:</td>
                        <td align="center" hidden><input <?php echo $imp ?> type="checkbox" name="sec_imp" id="sec_imp"  /></td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Sellado:</td>
                        <td align="center" hidden><input <?php echo $sel ?> type="checkbox" name="sec_sel" id="sec_sel"  /></td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Capacidad Almacenaje:</td>
                        <td align="center" hidden><input <?php echo $alm ?> type="checkbox" name="sec_alm" id="sec_alm"  /></td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Administrativo:</td>
                        <td align="center" hidden><input <?php echo $adm ?> type="checkbox" name="sec_adm" id="sec_adm"  /></td>
                    </tr>
                    <tr>
                        <td  align="left" hidden>Operativo:</td>
                        <td align="center" hidden><input <?php echo $opr ?> type="checkbox" name="sec_opr" id="sec_opr"  /></td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td align="left" colspan="3" >
                                <INPUT type="submit" onClick="" value="Guardar" name="btn" >
                                <INPUT type="submit" onClick="return cancelar();" value="Cancelar" name="btn" >
                                <!--<INPUT <?php echo $prt->delete ?> type="submit" onClick="return del()" value="Eliminar" name="btn" >-->
                            </td>                                        
                        </tr>
                    </tfoot>                
                </table>
            </form>
        </body>
    </html>
    <script>
        var div = '<?php echo $rst[sec_area]?>';
        $('#sec_area').val(div);
    </script>
    <?php
} catch (Exception $e) {
    echo 'Error:', $e->getMessage();
}
?>