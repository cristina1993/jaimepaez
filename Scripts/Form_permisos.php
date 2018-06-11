<?php
include_once '../Includes/library.php';
include_once '../Clases/clsUsers.php';
include_once("../Clases/clsAuditoria.php");
$usuario = $_GET['id'];
$Users = new User();
$rstUser = pg_fetch_array($Users->listUnUsuario($usuario));
$cnsMenu = $Users->listaMenuNoAsignados($usuario);
$cnsAgMenu = $Users->listAllPermits($usuario);
$Adt = new Auditoria();

if (isset($_POST['save'])) {
    $l0 = 't';
    $l1 = 't';
    $l2 = 't';
    $l3 = 't';
    $l4 = 't';
    $l5 = 't';
    $l6 = 't';
    if ($_POST['l0'] != on) {
        $l0 = 'f';
    }
    if ($_POST['l1'] != on) {
        $l1 = 'f';
    }
    if ($_POST['l2'] != on) {
        $l2 = 'f';
    }
    if ($_POST['l3'] != on) {
        $l3 = 'f';
    }
    if ($_POST['l4'] != on) {
        $l4 = 'f';
    }
    if ($_POST['l5'] != on) {
        $l5 = 'f';
    }
    if ($_POST['l6'] != on) {
        $l6 = 'f';
    }

    if ($Users->insertPermisos(array
                ($usuario,
                $_POST['opl_id'],
                $l0,
                $l1,
                $l2,
                $l3,
                $l4,
                $l5, $l6)) == true) {
        $rst_op = pg_fetch_array($Users->lista_una_opcion($_POST['opl_id']));
        $dtos = 'USUARIO=' . $rstUser[usu_login] . '&PROCESO=' . $rst_op[proc_descripcion] . '&MODULO=' . $rst_op[mod_descripcion] . '&OPCION=' . $rst_op[opl_modulo] . '&TOTAL=' . $l0 . '&EDITAR=' . $l1 . '&ELIMINAR=' . $l2 . '&AGREGAR=' . $l3 . '&VER=' . $l4 . '&REPORTES=' . $l5 . '&ESPECIAL=' . $l6 . '&&';
        $dmt = "$rst_op[proc_descripcion] / $rst_op[mod_descripcion] / $rst_op[opl_modulo]";
        if ($Adt->insert_audit_general('PERMISOS', 'INSERTAR', $dtos, $dmt) == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }
        echo "<script>window.history.go(-1)</script>";
    } else {
        echo pg_last_error();
    }
}
if (isset($_GET['id_p'])) {
    $rst_op = pg_fetch_array($Users->lista_una_opcion($_GET['opl_id']));
    echo $dtos = 'USUARIO=' . $rstUser[usu_login] . '&PROCESO=' . $rst_op[proc_descripcion] . '&MODULO=' . $rst_op[mod_descripcion] . '&OPCION=' . $rst_op[opl_modulo].'&&';
    $dmt = "$rst_op[proc_descripcion] / $rst_op[mod_descripcion] / $rst_op[opl_modulo]";
    if ($Adt->insert_audit_general('PERMISOS', 'ELIMINAR', $dtos, $dmt) == false) {
        $sms = "Auditoria" . pg_last_error() . 'ok2';
    }

    if ($Users->delete_permisos($_GET['id_p']) == true) {
        echo "<script>window.history.go(-1)</script>";
    } else {
        echo pg_last_error();
    }
}
?>
<html>
    <head>
        <SCRIPT LANGUAGE="JavaScript">
            $(function () {
                $("#tbl_form").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('contenedor2').rows = "*,75%";
            });

            function validate()
            {
                if (document.frmPermisos.opl_id.value == 0) {
                    alert("Elija un Modulo");
                    document.frmPermisos.opl_id.focus();
                    return false;
                }
                return true;
            }

            function habilita(form)
            {

                if (form.l0.checked == true)
                {
                    form.l1.checked = true;
                    form.l2.checked = true;
                    form.l3.checked = true;
                    form.l4.checked = true;
                    form.l5.checked = true;
                    form.l6.checked = true;
                    form.l1.disabled = true;
                    form.l2.disabled = true;
                    form.l3.disabled = true;
                    form.l4.disabled = true;
                    form.l5.disabled = true;
                    form.l6.disabled = true;

                } else {
                    form.l1.checked = false;
                    form.l2.checked = false;
                    form.l3.checked = false;
                    form.l4.checked = false;
                    form.l5.checked = false;
                    form.l6.checked = false;
                    form.l1.disabled = false;
                    form.l2.disabled = false;
                    form.l3.disabled = false;
                    form.l4.disabled = false;
                    form.l5.disabled = false;
                    form.l6.disabled = false;
                }
            }
            function salir() {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

        </script>
        <style>
            #tbl_form tr td{
                border:solid 1px #ccc; 
            }
        </style>    
    </head>
    <body>
        <form   onsubmit="return validate()" name="frmPermisos" autocomplete="off" enctype="multipart/form-data"  method="POST" >
            <table id="tbl_form"  >
                <thead>
                    <tr>
                        <th colspan="11" >
                            <font style="text-transform:capitalize"><?php echo 'PERMISOS DE ' . strtoupper($rstUser['usu_person']) ?></font>
                            <font class="cerrar"  onclick="salir()" title="Salir del Formulario">&#X00d7;</font>  
                        </th>
                    </tr>    
                    <tr>
                        <th width="20px">No</th>
                        <th align="center" >Direccion</th>
                        <th align="center" >Nombre</th>
                        <th align="center" >Total</th>
                        <th align="center" >Editar</th>
                        <th align="center" >Eliminar</th>
                        <th align="center" >Agregar</th>
                        <th align="center" >Ver</th>
                        <th align="center" >Reportes</th>
                        <th align="center" >Especial</th>
                        <th align="center" >Quitar</th>
                    </tr>
                </thead>                
                <tbody class="tbl_frm_aux" >                 
                    <?php
                    $cn = 0;
                    while ($rst = pg_fetch_array($cnsAgMenu)) {
                        $cn++;
                        ?>
                        <tr>
                            <td><?php echo $cn ?></td>
                            <td><?php echo $rst['proc_descripcion'] . '   /  ' . $rst['mod_descripcion'] ?></td>
                            <td><?php echo $rst['opl_modulo'] ?></td>
                            <?php
                            $l0 = '';
                            $l1 = '';
                            $l2 = '';
                            $l3 = '';
                            $l4 = '';
                            $l5 = '';
                            if ($rst['asg_opl_level_0'] == 't') {
                                $l0 = 'x';
                            }
                            if ($rst['asg_opl_level_1'] == 't') {
                                $l1 = 'x';
                            }
                            if ($rst['asg_opl_level_2'] == 't') {
                                $l2 = 'x';
                            }
                            if ($rst['asg_opl_level_3'] == 't') {
                                $l3 = 'x';
                            }
                            if ($rst['asg_opl_level_4'] == 't') {
                                $l4 = 'x';
                            }
                            if ($rst['asg_opl_level_5'] == 't') {
                                $l5 = 'x';
                            }
                            if ($rst['asg_opl_level_6'] == 't') {
                                $l6 = 'x';
                            }
                            ?>    
                            <td align="center"><?php echo $l0 ?></td>
                            <td align="center"><?php echo $l1 ?></td>
                            <td align="center"><?php echo $l2 ?></td>
                            <td align="center"><?php echo $l3 ?></td>
                            <td align="center"><?php echo $l4 ?></td>
                            <td align="center"><?php echo $l5 ?></td>
                            <td align="center"><?php echo $l6 ?></td>

                            <td><a href="Form_permisos.php?id_p=<?php echo $rst['aol_id'] ?>&opl_id=<?php echo $rst['opl_id'] ?>&id=<?php echo $usuario ?>" ><img src="../img/b_delete.png"></img></a></td>
                        </tr>                
                        <?php
                    }
                    ?>     
                </tbody>                
                <tfoot style="background:#006699 ">
                    <tr>
                        <th></th>
                        <th></th>
                        <th align="right" >
                            <select  name="opl_id">
                                <option value="0">Elija un Modulo</option>        
                                <?php
                                $n = 0;
                                while ($rstMenu = pg_fetch_array($cnsMenu)) {

                                    echo "<option value=$rstMenu[opl_id]>$rstMenu[proc_descripcion] /  $rstMenu[mod_descripcion] /  $rstMenu[opl_modulo]</option>";
                                }
                                ?>
                            </select>
                        </th>
                        <th align="center"><input id="l0" name='l0'  value="on" onClick="habilita(this.form)" type='checkbox' /></th>    
                        <th align="center"><input id="l1" name='l1' type='checkbox' /></th>
                        <th align="center"><input name='l2' type='checkbox' /></th>
                        <th align="center"><input name='l3' type='checkbox' /></th>
                        <th align="center"><input name='l4' type='checkbox' /></th>
                        <th align="center"><input name='l5' type='checkbox' /></th>
                        <th align="center"><input name='l6' type='checkbox' /></th>
                        <th><input  type='submit' name='save' value='Guardar' /></th>
                    </tr>  
                </tfoot>
            </table>            
        </form>
    </body>
</html>
