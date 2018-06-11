<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_empresas.php';
$Emp = new Empresas();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $rst = pg_fetch_array($Emp->lista_una_empresa_id($id));
} else {
    $id = 0;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            function save(id) {
                if (cont_si.checked == true) {
                    cont = 'SI';
                } else {
                    cont = 'NO';
                }
                                       

                var data = Array(
                        emi_identificacion.value.toUpperCase(),
                        emi_nombre.value.toUpperCase(),
                        emi_nombre_comercial.value.toUpperCase(),
                        emi_dir_establecimiento_matriz.value.toUpperCase(),
                        cont,
                        emi_dir_establecimiento_emisor.value.toUpperCase(),
                        emi_cod_establecimiento_emisor.value.toUpperCase(),
                        emi_cod_punto_emision.value,
                        emi_contribuyente_especial.value,
                        emi_credencial.value,
                        emi_sec_factura.value,
                        emi_sec_notcred.value,
                        emi_sec_notdeb.value,
                        emi_sec_guia_remision.value,
                        emi_sec_retencion.value,
                        emi_telefono.value.toUpperCase(),
                        emi_ciudad.value.toUpperCase(),
                        emi_pais.value.toUpperCase()
                                );
                if (emi_identificacion.value.length == 0) {
                    $('#emi_identificacion').css({'border': 'solid 1px red'});
                    $('#emi_identificacion').focus();
                } else if (emi_nombre.value.length == 0) {
                    $('#emi_nombre').css({'border': 'solid 1px red'});
                    $('#emi_nombre').focus();
                } else if (emi_nombre_comercial.value.length == 0) {
                    $('#emi_nombre_comercial').css({'border': 'solid 1px red'});
                    $('#emi_nombre_comercial').focus();
                } else if (emi_dir_establecimiento_matriz.value.length == 0) {
                    $('#emi_dir_establecimiento_matriz').css({'border': 'solid 1px red'});
                    $('#emi_dir_establecimiento_matriz').focus();
                } else if (emi_dir_establecimiento_emisor.value.length == 0) {
                    $('#emi_dir_establecimiento_emisor').css({'border': 'solid 1px red'});
                    $('#emi_dir_establecimiento_emisor').focus();
                } else if (emi_cod_establecimiento_emisor.value.length == 0) {
                    $('#emi_cod_establecimiento_emisor').css({'border': 'solid 1px red'});
                    $('#emi_cod_establecimiento_emisor').focus();
                } else if (emi_cod_punto_emision.value.length == 0) {
                    $('#emi_cod_punto_emision').css({'border': 'solid 1px red'});
                    $('#emi_cod_punto_emision').focus();
                } else if (emi_contribuyente_especial.value.length == 0) {
                    $('#emi_contribuyente_especial').css({'border': 'solid 1px red'});
                    $('#emi_contribuyente_especial').focus();
                } else if (emi_sec_factura.value.length == 0) {
                    $('#emi_sec_factura').css({'border': 'solid 1px red'});
                    $('#emi_sec_factura').focus();
                } else if (emi_sec_notcred.value.length == 0) {
                    $('#emi_sec_notcred').css({'border': 'solid 1px red'});
                    $('#emi_sec_notcred').focus();
                } else if (emi_sec_notdeb.value.length == 0) {
                    $('#emi_sec_notdeb').css({'border': 'solid 1px red'});
                    $('#emi_sec_notdeb').focus();
                } else if (emi_sec_guia_remision.value.length == 0) {
                    $('#emi_sec_guia_remision').css({'border': 'solid 1px red'});
                    $('#emi_sec_guia_remision').focus();
                } else if (emi_sec_retencion.value.length == 0) {
                    $('#emi_sec_retencion').css({'border': 'solid 1px red'});
                    $('#emi_sec_retencion').focus();
                } else {
                    $.post("actions_empresas.php", {op: 0, 'data[]': data, id: id},
                    function (dt) {
                        if (dt == 0) {
                            redireccionar();
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }

            function redireccionar() {
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_empresas.php';
            }

            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }

//            function load_cuenta(obj) {
//                $.post("actions_empresas.php", {op:2, id: obj.value},
//                function (dt) {
//                   $()
//                });
//            }

        </script>
        <style>
            .sms{
                color:#000;
            }
        </style>
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="proceso" >   
        </div>
        <div id="cargando"></div>

        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table id="tbl_form">
            <thead>
                <tr><th colspan="2" >FORMULARIO DE REGISTRO DE DATOS EMPRESARIALES  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Ruc:</td>
                    <td><input type="text" size="50" id="emi_identificacion" name="emi_identificacion" value="<?php echo $rst[emi_identificacion] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Nombre:</td>
                    <td><input type="text" size="50" id="emi_nombre" name="emi_nombre" value="<?php echo $rst[emi_nombre] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Nombre Comercial:</td>
                    <td><input type="text" size="50" id="emi_nombre_comercial" name="emi_nombre_comercial" value="<?php echo $rst[emi_nombre_comercial] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Ciudad de la Empresa:</td>
                    <td><input type="text" size="50" id="emi_ciudad" name="emi_ciudad" value="<?php echo $rst[emi_ciudad] ?>" style="text-transform: uppercase" /></td>
                </tr>
                 <tr>
                    <td>Pais de la Empresa:</td>
                    <td><input type="text" size="50" id="emi_pais" name="emi_pais" value="<?php echo $rst[emi_pais] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Direccion de la Empresa:</td>
                    <td><input type="text" size="50" id="emi_dir_establecimiento_matriz" name="emi_dir_establecimiento_matriz" value="<?php echo $rst[emi_dir_establecimiento_matriz] ?>" style="text-transform: uppercase" /></td>
                </tr>
                 <tr>
                    <td>Telefono de la Empresa:</td>
                    <td><input type="text" size="50" id="emi_telefono" name="emi_telefono" value="<?php echo $rst[emi_telefono] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Obligado a llevar contabilidad:</td>
                    <td>
                        Si:<input type="radio" name="emi_obligado_llevar_contabilidad" id="cont_si" />
                        No:<input type="radio" name="emi_obligado_llevar_contabilidad" id="cont_no" />
                    </td>
                </tr>
                <tr>
                    <td>Direccion Punto Emision:</td>
                    <td><input type="text" size="50" id="emi_dir_establecimiento_emisor" name="emi_dir_establecimiento_emisor" value="<?php echo $rst[emi_dir_establecimiento_emisor] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Codigo Empresa:</td>
                    <td><input type="text" size="50" id="emi_cod_establecimiento_emisor" name="emi_cod_establecimiento_emisor" value="<?php echo $rst[emi_cod_establecimiento_emisor] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Codigo Punto Emision:</td>
                    <td><input type="text" size="50" id="emi_cod_punto_emision" name="emi_cod_punto_emision" value="<?php echo $rst[emi_cod_punto_emision] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Codigo Tributario:</td>
                    <td><input type="text" size="50" id="emi_contribuyente_especial" name="emi_contribuyente_especial" value="<?php echo $rst[emi_contribuyente_especial] ?>" style="text-transform: uppercase" /></td>
                </tr>
                <tr>
                    <td>Credenciales para Facturacion:</td>
                    <td>
                        <select style="width:317px;" id="emi_credencial" name="emi_credencial">
                            <option value="0">Seleccione</option>
                            <?php
                            $cns_cr = $Emp->lista_credenciales();
                            while ($rst_cr = pg_fetch_array($cns_cr)) {
                                $valor = explode('&', $rst_cr[con_valor2]);
                                if ($rst[emi_credencial] == $valor[2]) {
                                    $sel = 'selected';
                                } else {
                                    $sel = '';
                                }
                                echo "<option  $sel value='$valor[2]'>$valor[0] $valor[2]</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Inicio Secuencial Factura:</td>
                    <td><input type="text" size="50" id="emi_sec_factura" name="emi_sec_factura" value="<?php echo $rst[emi_sec_factura] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                </tr>
                <tr>
                    <td>Inicio Secuencial Nota de Credito:</td>
                    <td><input type="text" size="50" id="emi_sec_notcred" name="emi_sec_notcred" value="<?php echo $rst[emi_sec_notcred] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                </tr>
                <tr>
                    <td>Inicio Secuencial Nota de Debito:</td>
                    <td><input type="text" size="50" id="emi_sec_notdeb" name="emi_sec_notdeb" value="<?php echo $rst[emi_sec_notdeb] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                </tr>
                <tr>
                    <td>Inicio Secuencial Guia de Remision:</td>
                    <td><input type="text" size="50" id="emi_sec_guia_remision" name="emi_sec_guia_remision" value="<?php echo $rst[emi_sec_guia_remision] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                </tr>
                <tr>
                    <td>Inicio Secuencial Retencion:</td>
                    <td><input type="text" size="50" id="emi_sec_retencion" name="emi_sec_retencion" value="<?php echo $rst[emi_sec_retencion] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '')"/></td>
                </tr>
                
            </tbody>
            <tfoot >
                <tr>
                    <td style="padding:10px "><input  type="submit" name="save" id="save" value="Guardar" onclick="save('<?php echo $id ?>')"/></td>
                </tr>
            </tfoot>
        </table>
    </body>
</html>

<script>
var cont='<?php echo $rst[emi_obligado_llevar_contabilidad] ?>'
if (cont=='SI'){
  $('#cont_si').attr('checked',true);  
}else{
  $('#cont_no').attr('checked',true);  
}
</script>