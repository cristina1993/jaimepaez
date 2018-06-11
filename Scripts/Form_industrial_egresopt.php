<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_industrial_egresopt.php';
$Clase_industrial_egresopt = new Clase_industrial_egresopt();
$id = $_GET[id];
if ($emisor == 1) {
    $rst = pg_fetch_array($Clase_industrial_egresopt->lista_ingreso_pedido_documento_nop($id));
    $cns_enc = $Clase_industrial_egresopt->lista_reg_nop($rst[ped_documento]);
} else {
    $rst = pg_fetch_array($Clase_industrial_egresopt->lista_ingreso_pedido_documento($id));
    $cns_enc = $Clase_industrial_egresopt->lista_reg($rst[ped_documento]);
}
$rst_enc2=  pg_fetch_array($Clase_industrial_egresopt->lista_mov_ped($rst[ped_documento]));

$rst_bod=  pg_fetch_array($Clase_industrial_egresopt->lista_emisor($rst[cli_id]));
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id =<?php echo $id; ?>;
            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#guardar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                if (id != 0) {
                    doc = document.getElementsByClassName('itm');
                    n = 0;
                    while (n < doc.length) {
                        n++;
                        if ($('#saldo' + n).val() == 0) {
                            $('#mov_cantidad' + n).attr('disabled', true);
                        }
                    }
                }
            });
            function cancelar() {

                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                window.history.go(0);
            }

            function save(id, obj) {
                o = obj.lang;
                if ($('#saldo' + o).val() == 0) {
                    $('#mov_cantidad' + o).attr('disabled', true);
                }

                var data = Array();
                n = obj.lang;
                ped_id = $('#ped_id' + n).val();
                pro_id = $('#pro_id' + n).val();
                trs_id = $('#trs_id' + n).val();
                cli_id = $('#cli_id' + n).val();
                bod_id = $('#bod_id' + n).val();
                saldo = $('#saldo' + n).val();
                ped_documento = $('#ped_documento').val();
                mov_cantidad = $('#mov_cantidad' + n).val();
                pro_codigo = $('#pro_codigo' + n).val();
                ped_cantidad1 = $('#ped_cantidad1' + n).val();
                data.push(ped_id + '&' +
                        pro_id + '&' +
                        trs_id + '&' +
                        cli_id + '&' +
                        bod_id + '&' +
                        ped_documento + '&' +
                        ped_guia_transporte.value + '&' +
                        ped_fecha_registro.value + '&' +
                        ped_guia_transporte.value + '&' +
                        ped_transportista.value + '&' +
                        mov_cantidad + '&' +
                        pro_codigo + '&' +
                        ped_cantidad1
                        );
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar

                        if (ped_guia_transporte.value.length == 0) {
                            $("#ped_guia_transporte").css({borderColor: "red"});
                            $("#ped_guia_transporte").focus();
                            return false;
                        }
                        n = obj.lang;
                        if ($('#mov_cantidad' + n).val() == 0) {
                            $('#mov_cantidad' + n).css({borderColor: "red"});
                            $('#mov_cantidad' + n).focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_pedidospt.php',
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id,dest:dest.value}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        dat = dt.split('&');
                        if (dat[0] == 0) {
                            window.location = 'Form_industrial_egresopt.php?id=' + idt.value;
                        } else {
                            alert(dat[0]); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }
//
            function saldo(obj) {

                o = obj.lang;



                if (($("#mov_cantidad" + o).val() * 1) > ($("#saldo1" + o).val() * 1)) {
                    s = $('#entregado1' + o).val();
                    $('#entregado' + o).val(s);
                    var soli = parseFloat($('#ped_cantidad1' + o).val() * 1) - parseFloat($('#entregado' + o).val() * 1);
                    soli = soli.toFixed(2);
                    $('#saldo' + o).val(soli);
                    $("#mov_cantidad" + o).val('');
                    $("#mov_cantidad" + o).focus();
                    $("#mov_cantidad" + o).css({borderColor: "red"});

                    alert('La Cantidad es mayor al Inventario');

                } else {
                    if ($('#mov_cantidad' + o).val().length == 0)
                    {
                        s = $('#entregado1' + o).val();
                        $('#entregado' + o).val(s);
                        var soli = parseFloat($('#ped_cantidad1' + o).val() * 1) - parseFloat($('#entregado' + o).val() * 1);
                        soli = soli.toFixed(2);

                        $('#saldo' + o).val(soli);
//                 
                    } else {
//                        
                        s = $('#mov_cantidad' + o).val();
//                    
                        entregado = 0;
                        var vt = parseFloat(s) + parseFloat(entregado) + parseFloat($('#entregado1' + o).val());
                        $('#entregado' + o).val(vt);
                        var soli = parseFloat($('#ped_cantidad1' + o).val() * 1) - parseFloat($('#entregado' + o).val() * 1);
                        soli = soli.toFixed(2);
                        $('#saldo' + o).val(soli);

                    }
                }
            }



        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;                
            }
            .head{
                text-align: center;
                height:22px;
            }
            #pago{
                width: 150px;
            }
            #mov_pago{
                width: 160px; 
            }

        </style>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save">
            <table id="tbl_form" >
                <thead>
                    <tr><th colspan="11" >EGRESO DE PRODUCTO TERMINADO NOPERTI</th></tr>
                </thead>
                <tr>
                    <td>Orden #:</td>
                    <td>
                        <input type="text" size="20"  id="ped_documento" value="<?php echo $rst[ped_documento] ?>" readonly />
                        <input type="hidden" value="<?php echo $id ?>" id="idt" />
                    </td>
                    <td>Fecha de Pedido:</td>
                    <td><input type="text" size="17" id="ped_fecha_registro" value="<?php echo $rst[ped_fecha_registro] ?>" readonly/></td>
                    <td>Cliente:</td>
                    <td>
                        <input type="text" size="30"  id="cli_nombre" value="<?php echo trim($rst['cli_apellidos'] . ' ' . $rst['cli_nombres'] . ' ' . $rst['cli_raz_social']) ?>" readonly />
                        <input type="hidden" size="30"  id="dest" value="<?php echo $rst_bod[cod_punto_emision]?>" />
                </tr>
                <tr>
                    <td>Transaccion:</td>
                    <td><input type="text" size="20"  id="trs_descripcion" value="<?php echo $rst[trs_descripcion] ?>" readonly /></td>
                    <td>Guia de Transporte:</td>
                    <td><input type="text" size="20"  id="ped_guia_transporte" value="<?php echo $rst_enc2[mov_guia_transporte] ?>"  /></td>
                    <td>Transportista:</td>
                    <td><input type="text" size="20"  id="ped_transportista" value="<?php echo $rst_enc2[mov_tranportista] ?>"/></td>
                </tr>
                <thead id="tabla">
                    <tr class="head">
                        <th></th>
                        <th></th>
                        <th colspan="2">Producto Terminado</th>
                        <th>Solicitado</th>
                        <th>Entregado</th>
                        <th>Saldo</th>
                        <th>Egreso</th>
                        <th></th>
                    </tr>
                </thead>
                <thead id="tabla">
                    <tr class="head">
                        <th>Item</th>
                        <th>Codigo</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Cantidad</th>
                        <th>Accciones</th>
                    </tr>
                </thead>
                <?PHP
                $n = 0;
                while ($rst1 = pg_fetch_array($cns_enc)) {
                    $n++;
                        if ($rst[bod_id] == 1) {
                            $rst1['pro_id'] = $rst1['id'];
                            $rst1['pro_codigo'] = $rst1['pro_a'];
                            $rst1['pro_descripcion'] = $rst1['pro_b'];
                        $rst2 = pg_fetch_array($Clase_industrial_egresopt->lista_cantidad_nop($rst[ped_documento], $rst1[pro_codigo]));
                        }else{
                        $rst2 = pg_fetch_array($Clase_industrial_egresopt->lista_cantidad($rst[ped_documento], $rst1[pro_codigo]));
                        }
                        if ($rst2[suma] == '') {
                            $rst2[suma] = 0;
                        }
                        $rst['saldo'] = $rst1['ped_cantidad'] - $rst2[suma];
                    ?>
                    <tr>
                        <td><input type ="hidden" size="20"  id="ped_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[ped_id] ?>" lang="1" />
                            <input type ="hidden" size="20"  id="pro_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[pro_id] ?>" lang="1" />
                            <input type ="hidden" size="20"  id="trs_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[trs_id] ?>" lang="1" />
                            <input type ="hidden" size="20"  id="cli_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[cli_id] ?>" lang="1" />
                            <input type ="hidden" size="20"  id="bod_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[bod_id] ?>" lang="1" />
                            <input type ="hidden" size="20"  id="ped_id<?PHP echo $n ?>"  value="<?PHP echo $rst1[ped_id] ?>" lang="1" />
                            <input type ="text" size="4"  id="item"  readonly value="<?PHP echo $n ?>" class="itm"/></td>
                        <td><input type ="text" size="10"  id="pro_codigo<?PHP echo $n ?>"  value="<?php echo $rst1['pro_codigo'] ?>" lang="1" readonly/></td>
                        <td><input type="text" size="30" id="pro_descripcion<?PHP echo $n ?>" value="<?php echo $rst1['pro_descripcion'] ?>" lang="1" readonly/></td>                            
                        <td><input type ="text" size="10"  id="pro_uni<?PHP echo $n ?>"  value="<?php echo $rst1['pro_uni'] ?>" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="entregado1<?PHP echo $n ?>"  value="<?PHP echo $rst2[suma] ?>" lang="1" hidden/>
                            <input type ="text" size="10"  id="ped_cantidad1<?PHP echo $n ?>"   value="<?php echo $rst1['ped_cantidad'] ?>" lang="<?PHP echo $n ?>" readonly/></td>
                        <td><input type ="text" size="10"  id="entregado<?PHP echo $n ?>"  value="<?php echo $rst2[suma] ?>" lang="1" readonly/></td>
                        <td><input type ="text" size="10"  id="saldo<?PHP echo $n ?>"  value="<?php echo $rst['saldo'] ?>" lang="1" readonly/>
                            <input type ="text" size="10"  id="saldo1<?PHP echo $n ?>"  value="<?php echo $rst['saldo'] ?>" lang="1" hidden/></td>
                        <td><input type = "text" size = "10" id = "mov_cantidad<?PHP echo $n ?>" onkeyup="saldo(this)" value = "<?php echo $rst['saldo'] ?>" lang="1"/></td>
                        <?PHP
                        if ($rst['saldo'] != 0) {
                            ?>
                            <td onclick="save(<?php echo $id; ?>, this)" lang="<?PHP echo $n ?>"><img class="auxBtn" src="../img/save.png" /></td>
                            <?PHP
                        }
                        ?>
                    </tr>
                    <?PHP
                }
                ?>
                <tfoot>
                    <tr>
                        <td colspan = "2">
                            <button id = "guardar">Guardar</button>
                            <button id = "cancelar">Cancelar</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
