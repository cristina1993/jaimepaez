<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$id = $_GET[id];
if (isset($_GET[id])) {
    $rst = pg_fetch_array($Set->lista_un_mp($id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                parent.document.getElementById('contenedor2').rows = "*,80%";
            });
            function save(id)
            {
                var data = Array(
                        fbc_id.value,
                        mpt_id.value,
                        mp_codigo.value,
                        mp_referencia.value.toUpperCase(),
                        mp_pro1.value.toUpperCase(),
                        mp_pro2.value.toUpperCase(),
                        mp_pro3.value.toUpperCase(),
                        mp_pro4.value.toUpperCase(),
                        mp_obs.value,
                        mp_unidad.value,
                        mp_presentacion.value)


                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (mpt_id.value == 0) {
                            $("#mpt_id").css({borderColor: "red"});
                            $("#mpt_id").focus();
                            return false;
                        }
                        else if (mp_referencia.value.length == 0) {
                            $("#mp_referencia").css({borderColor: "red"});
                            $("#mp_referencia").focus();
                            return false;
                        }
                        else if (mp_presentacion.value.length == 0) {
                            $("#mp_presentacion").css({borderColor: "red"});
                            $("#mp_presentacion").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions.php',
                    data: {act: 19, 'data[]': data, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            window.history.go(0);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function crea_codigo() {
                f=fbc_id.value;
                t=mpt_id.value;
                p=mp_pro4.value.toUpperCase();
                $.post("actions.php", {act: 21, fbc: f, tp: t,prov:p},
                function (dt) {
                    mp_codigo.value = dt;
                });

            }
        </script>
        <style>
            input[type=text]{
                text-transform:uppercase; 
            }
        </style>
    </head>
    <body>
        <table id="tbl_form" cellpadding="0" border="0" >
            <thead>
                <tr><th colspan="3" >
                        MATERIA PRIMA
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>  
                    </th></tr>
            </thead>
            <tr>
                <td>
                    Fabrica:
                </td>
                <td>
                    <select name="fbc_id" id="fbc_id" style="width:200px;" disabled onchange="crea_codigo(fbc_id.value, mpt_id.value)">
                        <?php
                        $cns_fbc = $Set->lista_fabricas();
                        while ($rst_fbc = pg_fetch_array($cns_fbc)) {
                            if ($rst_fbc[emp_id] == $rst[fbc_id]) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }
                            echo "<option $sel value='$rst_fbc[emp_id]'>$rst_fbc[emp_descripcion]</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tipo:</td>
                <td>
                    <select name="mpt_id" id="mpt_id" style="width:200px "  onchange="crea_codigo()">
                        <option value="0">Seleccione</option>
                        <?php
                        $cns_tp = $Set->lista_tppt();
                        while ($rst_tp = pg_fetch_array($cns_tp)) {
                            if ($rst_tp[tpt_id] == $rst[tpt_id]) {
                                $sel = "selected";
                            } else {
                                $sel = "";
                            }

                            echo "<option $sel value='$rst_tp[tpt_id]'>$rst_tp[tpt_nombre]</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Proveedor:</td>
                <td><input type="text" name="mp_pro4" id="mp_pro4" size="30" value="<?php echo $rst[mp_pro4] ?>" /></td>
            </tr>

            <tr>
                <td>Codigo:</td>
                <td><input type="text" readonly style="background:#ccc;" name="mp_codigo" id="mp_codigo" size="20" value="<?php echo $rst[mp_codigo] ?>" /></td>
            </tr>
            <tr>
                <td>Referencia:</td>
                <td><input type="text" name="mp_referencia" id="mp_referencia" size="45" value="<?php echo $rst[mp_referencia] ?>" /></td>
            </tr>
            <tr>
                <td>Unidad:</td>
                <td>
                    <select id="mp_unidad" style="text-transform:lowercase" >
                        <option value="kg">kg</option>
                        <option value="lb">lb</option>
                        <option value="gr">gr</option>
                        <option value="litro">litro</option>
                        <option value="galon">galon</option>
                        <option value="m">m</option>
                        <option value="cm">cm</option>
                        <option value="ft">ft</option>
                        <option value="in">in</option>
                    </select>
                    <script>
                        document.getElementById("mp_unidad").value =<?php echo $rst[mp_unidad] ?>
                    </script>
                </td>
            </tr>
            <tr>
                <td>Presentacion:</td>
                <td><input type="text" name="mp_presentacion" id="mp_presentacion" size="35" value="<?php echo $rst[mp_presentacion] ?>" /></td>
            </tr>
            <tr>
                <td>Propiedad1:</td>
                <td><input type="text" name="mp_pro1" id="mp_pro1" size="35" value="<?php echo $rst[mp_pro1] ?>" /></td>
            </tr>
            <tr>
                <td>Propiedad2:</td>
                <td><input type="text" name="mp_pro2" id="mp_pro2" size="35" value="<?php echo $rst[mp_pro2] ?>" /></td>
            </tr>
            <tr>
                <td>Propiedad3:</td>
                <td><input type="text" name="mp_pro3" id="mp_pro3" size="35" value="<?php echo $rst[mp_pro3] ?>" /></td>
            </tr>

            <tr>
                <td>Observaciones:</td>
                <td>
                    <textarea name="mp_obs" id="mp_obs" style="width:100%"><?php echo $rst[mp_obs] ?></textarea>    
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save('<?php echo $id ?>')">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>
    </body>
</html>