<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$num_trs = $_GET[num_trs];
if (isset($_GET[num_trs])) {
    $rst_h = pg_fetch_array($Set->lista_mov_mp_codigo($num_trs));
    $no_trs = $_GET[num_trs];
    $cns = $Set->lista_mov_mp_codigo($num_trs);
} else {
    $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion(0));
    $rst_h[mov_fecha_trans] = date('Y-m-d');
    $sec0 = explode('-', $rst_sec[mov_num_trans]);
    $sec = ($sec0[1] + 1);
    if ($sec >= 0 && $sec < 10) {
        $tx_trs = "000000000";
    } elseif ($sec >= 10 && $sec < 100) {
        $tx_trs = "00000000";
    } elseif ($sec >= 100 && $sec < 1000) {
        $tx_trs = "0000000";
    } elseif ($sec >= 1000 && $sec < 10000) {
        $tx_trs = "000000";
    } elseif ($sec >= 10000 && $sec < 100000) {
        $tx_trs = "00000";
    } elseif ($sec >= 100000 && $sec < 1000000) {
        $tx_trs = "0000";
    } elseif ($sec >= 1000000 && $sec < 10000000) {
        $tx_trs = "000";
    } elseif ($sec >= 10000000 && $sec < 100000000) {
        $tx_trs = "00";
    } elseif ($sec >= 100000000 && $sec < 1000000000) {
        $tx_trs = "0";
    } elseif ($sec >= 1000000000 && $sec < 10000000000) {
        $tx_trs = "";
    }
    $no_trs = '000-' . $tx_trs . $sec;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title></title>
    <head>
        <script>
            $(function () {
                Calendar.setup({inputField: "mov_fecha_trans", ifFormat: "%Y-%m-%d", button: "im-mov_fecha_trans"});
            });
            function save(num_trs) {
                if (mov_documento.value.length == 0)
                {
                    alert('Guia de Recepcion es campo obligatorio');
                } else if (mp_id.value == 0) {
                    alert('Elija una materia prima');
                } else if (mov_cantidad.value.length == 0) {
                    alert('Cantidad es campo obligatorio');
                } else if (mov_peso_total.value.length == 0) {
                    alert('Peso Total es campo obligatorio');
                } else {
                    pu = (mov_cantidad.value / mov_peso_total.value)
                    var data = Array(
                            3,
                            mp_id.value,
                            mov_documento.value,
                            mov_num_trans.value,
                            mov_fecha_trans.value,
                            mov_cantidad.value,
                            mp_presentacion.value,
                            mov_peso_total.value,
                            mov_proveedor.value,
                            pu)
                    $.post("actions.php", {act: 22, 'data[]': data},
                    function (dt) {
                        if (dt == 0) {
                            window.location = "Form_i_reg_mp.php?num_trs=" + num_trs;
                        } else {
                            alert(dt);
                        }
                    });
                }
            }
            function cerrar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
            }
            function cancelar()
            {
                nm_trs = mov_num_trans.value;

                if (nm_trs.length != 0)
                {
                    $.post("actions.php", {act: 24, nm_trs: nm_trs},
                    function (dt) {
                        if (dt == 0)
                        {
                            cerrar();
                        } else {
                            alert(dt);
                        }
                    });
                } else {
                    cerrar();
                }
            }
            function finalizar()
            {
                cerrar();
                window.history.go(0);
            }
            function crea_codigo(fbc, tp)
            {
                $.post("actions.php", {act: 21, fbc: fbc, tp: tp},
                function (dt) {
                    mp_codigo.value = dt;
                });

            }
            function del(id, num_trs)
            {
                if (confirm("Desea Eliminar Este Elemento?")) {

                    $.post("actions.php", {act: 23, id: id},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.location = "Form_i_reg_mp.php?num_trs=" + num_trs;
                        }

                    });
                }
            }
            function datos(id) {
                $.post("actions.php", {act: 26, mp: id},
                function (dt) {
                    det = dt.split('&');
                    mp_ref.innerHTML = det[0];
                    mov_unidad.innerHTML = det[2];
                    mp_presentacion.value = det[3];
                });
            }

            function etiquetas(id, barcode, p_entregado) {
                var gap = 20;
                var boxH = $(window).height() - gap * 1.1;
                var boxW = $(window).width() * 0.35;
                var boxHF = (boxH - gap);
                wnd = '<iframe id="frmmodal" width="' + boxW + '" height="' + boxHF + '" src="Form_i_pesos_orden.php?id=' + id + '&barcode=' + barcode + '&p_entregado=' + p_entregado + '   " frameborder="0" />';

                $.fallr.show({
                    content: "<font id='titulo_ventana'>INGRESO DE PESOS</font><br/><br/>"
                            + wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }
                    }
                });

            }

            function factura() {
                var boxH = $(window).height() * 0.9;
                var boxW = $(window).width() * 0.9;
                var boxHF = (boxH - 20);

                wnd = '<iframe id="frmmodal" width="' + boxW + '" height="' + boxHF + '" src="Form_i_orden_compra.php?y=1"   " frameborder="0" />';

                $.fallr.show({
                    content: wnd,
                    width: boxW,
                    height: boxH,
                    duration: 5,
                    position: 'center',
                    buttons: {
                        button1: {
                            text: '&#X00d7;',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        },
                        button100: {
                            text: 'Guardar',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        },
                        button101: {
                            text: 'Cancelar',
                            onclick: function () {
                                $.fallr.hide();
                            }
                        }

                    }
                });

            }
            function revisa_datos(val) {
                if (val.length > 0) {
                    $.post("actions.php", {act: 49, id: val},
                    function (dt) {
                        dat = dt.split('&');
                        if (dt != 0) {
                            $('#mov_proveedor').val(dat[0]);
                            //$('#mp_id').html(dat[1]);
                        }
                    });

                }
            }
        </script>
        <style>
            #fallr-button-button100,#fallr-button-button101{
                position:absolute;
                bottom:0px; 
                text-decoration:none; 
                border: 1px solid #ccc;
                text-transform: uppercase;
                font-family: Arial, Verdana;
                font-size:12px; 
                padding-left: 7px;
                padding-right: 7px;
                padding-top: 5px;
                padding-bottom: 5px;
                border-radius: 4px;
                background: #DBE1EB;
                background: linear-gradient(left, #DBE1EB, #ccc);
                color: #000;
            }
            #fallr-button-button100:hover,#fallr-button-button101:hover{
                background: #DBE1EB;
                background: linear-gradient(left, #DBE1EB, #ccc);
                color: #000;
                border-color: #000;
            }            
            #fallr-button-button100{
                left:0px;
            }
            #fallr-button-button101{
                left:80px; 
            }
        </style>
    </head>

    <body>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="8" > REGISTRO DE MATERIA PRIMA</th></tr>
            </thead>                    
            <tr>
                <td>Documento No:</td>
                <td><input type="text" size="25" id="mov_num_trans" readonly style="background:#ccc;" value="<?php echo $no_trs ?>" /></td>
                <td>Fecha Ingreso:</td>
                <td><input type="text"  id="mov_fecha_trans" size="10" value="<?php echo $rst_h[mov_fecha_trans] ?>"/>
                    <img id='im-mov_fecha_trans' src='../img/calendar.png'  />
                </td>
                <td>
                    Guia de Recepcion:
                </td>
                <td colspan="5">
                    <input type="text"  id="mov_documento" value="<?php echo $rst_h[mov_documento] ?>" onchange="revisa_datos(this.value)" />
                    <button onclick="factura()">Factura</button>
                </td>
            </tr>
            <tr>
                <td>
                    Proveedor:
                </td>
                <td colspan="7">
                    <input type="text"  id="mov_proveedor" size="45" value="<?php echo $rst_h[mov_proveedor] ?>" />
                </td>
            </tr>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Descripcion</th>          
                    <th>Referencia</th>
                    <th>Presentacion</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Peso T</th>
                    <th>Accion</th>
                </tr>
                <tr>
                    <th></th>
                    <th>
                        <select id="mp_id" style="width:200px" onchange="datos(mp_id.value)">
                            <option value="0">Elija un Opcion</option> 
                            <?php
                            $cns_trn = $Set->lista_mp0();
                            while ($rst_trn = pg_fetch_array($cns_trn)) {
                                echo "<option value='$rst_trn[mp_id]'  >$rst_trn[mp_referencia]</option>";
                            }
                            ?>
                        </select>
                    </th>
                    <th id="mp_ref" style="color:black;font-size:12px;  "></th>
                    <th>
                        <input type="text" size="20" id="mp_presentacion"/>
                    </th>
                    <th><input type="text" size="5" id="mov_cantidad"  /></th>
                    <th id="mov_unidad" style="color:black;font-size:12px;" ></th>
                    <th><input type="text" size="5" id="mov_peso_total"  /></th>
                    <th>
                        <?php
                        if ($Prt->add == 0 || $Prt->edition == 0) {
                            ?>
                            <button id="" onclick="save(mov_num_trans.value)" >+</button>
                            <?php
                        }
                        ?>
                    </th>
                </tr>
            </thead>                  
            <tbody class="tbl_frm_aux" >     
                <?php
                $n = 0;
                while ($rst = pg_fetch_array($cns)) {
                    $n++;
                    $rst_mp = pg_fetch_array($Set->lista_un_det_oc_mp($rst_h[mov_documento], $rst[mp_id]));
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $rst[mp_referencia] ?></td>
                        <td><?php echo $rst[mp_codigo] ?></td>          
                        <td><?php echo $rst[mov_presentacion] ?></td>
                        <td align="right"><?php echo number_format($rst[mov_cantidad], 1) ?></td>
                        <td style="text-transform:lowercase"><?php echo $rst[mp_unidad] ?></td>
                        <td align="right"><?php echo number_format($rst[mov_peso_total], 1) ?></td>
                        <td align="center">
                            <?php
                            if ($Prt->delete == 0) {
                                ?>
                                <img src="../img/b_delete.png" onclick="del(<?php echo $rst[mov_id] ?>, '<?php echo $rst_h[mov_num_trans] ?>')">
                            <?php }
                            ?>
                            <img class="auxBtn" src="../img/peso.png" title="Pesos" width="20px"  onclick="etiquetas(<?php echo $rst_mp[orc_det_id] ?>, '<?php echo $rst_mp[mp_codigo] . $rst_mp[orc_codigo] ?>',<?php echo $rst[mov_peso_total] ?>)">                                    
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="8">
                        <button id="cancel" onclick="finalizar()">Finalizar</button>  
                        <button id="cancel" onclick="cancelar()">Cancelar</button>
                    </td>
                </tr>
            </tbody>                       
        </table>
    </body>
</html>