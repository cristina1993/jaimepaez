<?php
include_once '../Includes/permisos.php';
include_once("../Clases/clsPuestoTrabajo.php");
$_SESSION['ger'] = $_POST['pt_gerencia'];
$_SESSION['div'] = $_POST['pt_division'];
$_SESSION['sec'] = $_POST['sec_id'];
$Puestos = new PuestoTrabajo();
$cnsExtrusoras = $Puestos->listaExtrusoras();
$cnsImpresoras = $Puestos->listaImpresoras();
$cnsSelladoras = $Puestos->listaSelladoras();
$geren = $_GET[ger];
$divisions = $_GET[pt_division];
$seccions = $_GET[sec_id];

if (isset($_GET[id])) {
    $id = $_GET[id];
    $rstPt = pg_fetch_array($Puestos->listaUnPuestoTrabajo($id));
    $code = $rstPt[pt_codigo];
    $cnsSec = $Puestos->lista_secciones_div($rstPt[pt_division]);
    $cnsPtTrabajo = $Puestos->listaPuestoTrabajo_sec($rstPt[sec_id]);
} else {
    $id = 0;
    $rstPt['pt_turno1'] = 0;
    $rstPt['pt_turno2'] = 0;
    $rstPt['pt_turno3'] = 0;
    $ptS1 = 'checked';
    $ptS2 = '';
    $ptS3 = '';
    $sEst1 = 'checked';
    $alms = 'checked';
    $almf = '';
}
?>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>

            $(function () {
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                });
            });

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                parent.document.getElementById('mainFrame').src = '../Scripts/postLista.php?ger=' + '<?php echo $geren ?>&pt_division=' + '<?php echo $divisions ?>&sec_id=' + '<?php echo $seccions ?>';
            }

            function save(id) {
                if ($('#pt_tipo_puesto1').attr('checked') == true) {
                    tp = 0;
                } else if ($('#pt_tipo_puesto2').attr('checked') == true) {
                    tp = 1;
                } else {
                    tp = 2;
                }

                if ($('#pt_estado1').attr('checked') == true) {
                    est = 't';
                } else {
                    est = 'f';
                }
                if ($('#pt_almuerzo1').attr('checked') == true) {
                    alm = 't';
                } else {
                    alm = 'f';
                }
                if (pt_nivel.value == '0') {
                    puesto_superior = 0;
                } else {
                    puesto_superior = pt_puesto_superior.value;
                }
                var data = Array(
                        pt_gerencia.value,
                        pt_division.value,
                        sec_id.value,
                        pt_puesto.value.toUpperCase(),
                        pt_no.value,
                        pt_cargo.value.toUpperCase(),
                        code.value.toUpperCase(),
                        code.value.toUpperCase(),
                        code.value.toUpperCase(),
                        pt_turno1.value,
                        pt_turno2.value,
                        pt_turno3.value,
                        pt_responsabilidad.value.toUpperCase(),
                        tp,
                        puesto_superior,
                        pt_nivel.value,
                        est,
                        alm,
                        pt_marca.value.toUpperCase()
                        );
                var fields = Array();
                $("#frm_save").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                $.ajax({
                    beforeSend: function () {
                        //Validaciones antes de enviar
                        if (pt_division.value == 0) {
                            $("#pt_division").css({borderColor: "red"});
                            $("#pt_division").focus();
                            return false;
                        }
                        else if (sec_id.value == 0) {
                            $("#sec_id").css({borderColor: "red"});
                            $("#sec_id").focus();
                            return false;
                        }
                        else if (pt_cargo.value.length == 0) {
                            $("#pt_cargo").css({borderColor: "red"});
                            $("#pt_cargo").focus();
                            return false;
                        }
                        else if (pt_marca.value.length == 0) {
                            $("#pt_marca").css({borderColor: "red"});
                            $("#pt_marca").focus();
                            return false;
                        }
                        else if (pt_turno1.value.length == 0) {
                            $("#pt_turno1").css({borderColor: "red"});
                            $("#pt_turno1").focus();
                            return false;
                        }
                        else if (pt_turno2.value.length == 0) {
                            $("#pt_turno2").css({borderColor: "red"});
                            $("#pt_turno2").focus();
                            return false;
                        }
                        else if (pt_turno3.value.length == 0) {
                            $("#pt_turno3").css({borderColor: "red"});
                            $("#pt_turno3").focus();
                            return false;
                        }
                        else if (pt_puesto.value.length == 0) {
                            $("#pt_puesto").css({borderColor: "red"});
                            $("#pt_puesto").focus();
                            return false;
                        }
                        else if (pt_no.value.length == 0) {
                            $("#pt_no").css({borderColor: "red"});
                            $("#pt_no").focus();
                            return false;
                        }
                    },
                    type: 'POST',
                    url: 'actions_puestos_trabajo.php',
                    data: {op: 5, 'data[]': data, id: id, 'fields[]': fields}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            cancelar();
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }
                    }
                })
            }


            function ptSup(g, s)
            {
                $.post("actions_puestos_trabajo.php", {op: 2, id: g, s: s},
                function (data) {
                    $("#pt_puesto_superior").html(data);
                })
            }
//
            function codigo(s, t, p, n) {
                $.post("actions_puestos_trabajo.php", {op: 3, id: s},
                function (dt) {
                    cod = 'G' + dt + t + p + n;
                    $('#code').val(cod.toUpperCase());
                });
            }
            function secFind(s, t, p) {
                $.post("actions_puestos_trabajo.php", {op: 3, id: s},
                function (dt) {
                    var cod = 'G' + dt + t + p;
                    $.post("actions_puestos_trabajo.php", {cod: cod.toUpperCase(), op: 4}, function (data) {
                        pt_no.value = data;
                    })
                })
            }

            function loadDivision(g) {
                $.post("actions_puestos_trabajo.php", {op: 0, id: g},
                function (data) {
                    $("#pt_division").html(data);
                });
            }
            function loadSec(d) {
                $.post("actions_puestos_trabajo.php", {op: 1, id: d},
                function (data) {
                    $("#sec_id").html(data);
                });
            }

        </script>        

        <style>
            input,table
            {
                text-transform: uppercase;    
            } 
        </style>
    </head>

    <body>
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="9" >FORMULARIO DE PUESTO DE TRABAJO <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                </thead>
<!--                <input type="hidden" value="<?php echo $id ?>" name="aux" id="aux" />
                <input type="hidden" value="<?php echo $rstPt[pt_puesto_superior] ?>" name="aux" id="aux2" />-->
                <tr>
                    <td class="td" align="right" >CODIGO:</td>    
                    <td><input type="text" value="<?php echo $code ?>" size="17" name="code" id="code" readonly/> </td>
                </tr>                
                <tr hidden>
                    <td class="td" align="right" >Gerencia:</td>
                    <td>
                        <select name="pt_gerencia" onchange=" loadDivision(this.value), codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value);
                                ptSup(pt_gerencia.value,
                                        sec_id.value)" id="pt_gerencia" style="width: 160px">
                                <?php
                                $cns_g = $Puestos->lista_gerencias();
                                while ($rst_g = pg_fetch_array($cns_g)) {
                                    echo " <option value='$rst_g[ger_id]'>$rst_g[ger_descripcion]</option>";
                                }
                                ?>

                        </select>
                    </td>    
                </tr>
                <tr>
                    <td class="td" align="right" >Division:</td>
                    <td>
                        <select name="pt_division" id="pt_division" onchange="loadSec(this.value), codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value);
                                ptSup(pt_gerencia.value,
                                        sec_id.value)" style="width: 160px">
                            <option value="0">Seleccione</option>
                            <?php
                            $cns_g = $Puestos->lista_divisiones_ger('2');
                            while ($rst_g = pg_fetch_array($cns_g)) {
                                echo " <option value='$rst_g[div_id]'>$rst_g[div_descripcion]</option>";
                            }
                            ?>
                        </select>
                    </td>    
                </tr>
                <tr>
                    <td class="td" align="right" >Seccion:</td>
                    <td>
                        <select name="sec_id" id="sec_id" style="width:160px" onchange="codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value);
                                ptSup(pt_gerencia.value,
                                        sec_id.value)"  >
                            <option value="0">Seleccione</option>
                            <?php
                            while ($rstSec = pg_fetch_array($cnsSec)) {
                                echo "<option  value=$rstSec[sec_id]>$rstSec[sec_descricpion]</option>";
                            }
                            ?>                            
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="td" align="right" >Cargo:</td>
                    <td>
                        <input name="pt_cargo" id="pt_cargo" value="<?php echo $rstPt[pt_cargo] ?>" list="options"  />
                        <datalist id="options">
                            <?php
                            while ($rst = pg_fetch_array($cnsExtrusoras)) {
                                echo "<option value=$rst[ext_descripcion]>$rst[ext_descripcion]</option>";
                            }
                            while ($rst = pg_fetch_array($cnsImpresoras)) {
                                echo "<option value=$rst[imp_descripcion]>$rst[imp_descripcion]</option>";
                            }
                            while ($rst = pg_fetch_array($cnsSelladoras)) {
                                echo "<option value=$rst[sell_descripcion]>$rst[sell_descripcion]</option>";
                            }
                            ?>
                        </datalist>                         
                    </td> 
                </tr>
                <tr>
                    <td class="td" align="right" >Responsabilidad:</td>
                    <td><input type="text" value="<?php echo $rstPt['pt_responsabilidad'] ?>" name="pt_responsabilidad" id="pt_responsabilidad"></input></td>
                </tr>
                <tr>
                    <td class="td" align="right" >Marca:</td>
                    <td><input type="text" value="<?php echo $rstPt['pt_marca'] ?>" name="pt_marca" id="pt_marca"  /></td>
                </tr>
                <tr>
                    <td class="td" align="right" >Turno:</td>
                    <td>
                        <input onblur="codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value)" type="text" name="pt_turno1" id="pt_turno1" value="<?php echo $rstPt['pt_turno1'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9,]/, '');"  size="2" maxlength="1" >-
                        <input type="text" name="pt_turno2" id="pt_turno2" value="<?php echo $rstPt['pt_turno2'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9,]/, '');"  size="2" maxlength="1" >-
                        <input type="text" name="pt_turno3" id="pt_turno3" value="<?php echo $rstPt['pt_turno3'] ?>"  onkeyup="this.value = this.value.replace(/[^0-9,]/, '');"  size="2" maxlength="1" >
                    </td>
                </tr>
                <tr>
                    <td class="td" align="right" >Puesto:</td>
                    <td>
                        <input onblur="codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value);
                                secFind(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value
                                        )" type="text" name="pt_puesto" id="pt_puesto"  value="<?php echo $rstPt['pt_puesto']; ?>" onkeyup="this.value = this.value.replace(/[^aA-zZ]/, '');"   maxlength="3" size="3" >
                        <input onblur="codigo(
                                        sec_id.value,
                                        pt_turno1.value,
                                        pt_puesto.value,
                                        pt_no.value)" type="text" name="pt_no" id="pt_no"  value="<?php echo $rstPt['pt_no']; ?>" onkeyup="this.value = this.value.replace(/[^0-9]/, '');"  maxlength="2" size="2" >
                    </td>
                </tr>
                <tr>
                    <td class="td" align="right" >ALMUERZO:</td>
                    <td>
                        SI<input <?php echo $alms ?> type="radio" name="pt_almuerzo" id="pt_almuerzo1" value="t" />
                        NO<input <?php echo $almf ?> type="radio" name="pt_almuerzo" id="pt_almuerzo2" value="f" />
                    </td>
                </tr>
                <tr>
                    <td class="td" align="right" >Tipo Puesto:</td>
                    <td>
                        Operativo<input  <?php echo $ptS1 ?> type="radio" name="pt_tipo_puesto" id="pt_tipo_puesto1" value=0  />
                        Administrativo<input  <?php echo $ptS2 ?> type="radio" name="pt_tipo_puesto" id="pt_tipo_puesto2" value=1  />
                        Gerencial<input  <?php echo $ptS3 ?> type="radio" name="pt_tipo_puesto" id="pt_tipo_puesto3" value=2  />
                    </td>
                </tr>
                <tr>
                    <td class="td" align="right" >Puesto Superior</td>
                    <td>
                        <select name="pt_puesto_superior" id="pt_puesto_superior"  style="width:160px">
                            <option value="0"></option>
                            <?php
                            while ($rstPtr = pg_fetch_array($cnsPtTrabajo)) {
                                echo "<option value=$rstPtr[pt_id]>$rstPtr[pt_cargo]=>$rstPtr[pt_responsabilidad]</option>";
                            }
                            ?>
                        </select>

                        Nivel<select name="pt_nivel" id="pt_nivel">
                            <option value=0>0</option>
                            <option value=1>1</option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <option value=5>5</option>
                            <option value=6>6</option>
                            <option value=7>7</option>
                            <option value=8>8</option>
                            <option value=9>9</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="td" align="right" >Estado</td>                    
                    <td>
                        Activo<input <?php echo $sEst1 ?> type="radio" name="pt_estado" id="pt_estado1" value="t"/>
                        Inactivo<input <?php echo $sEst2 ?> type="radio" name="pt_estado" id="pt_estado1" value="f"/>
                    </td>
                </tr>
                <tfoot>
                    <tr><td colspan="2">
                            <?PHP
                            if ($x != 1) {
                                ?>                 

                                <button id="guardar" onclick="save(<?php echo $id ?>, 0)">Guardar</button>    
                                <?PHP
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>
                        </td></tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>
<script>
    var ger = '<?php echo $rstPt[pt_gerencia] ?>';
    $('#pt_gerencia').val(ger);
    var dv = '<?php echo $rstPt[pt_division] ?>';
    $('#pt_division').val(dv);
    var secid = '<?php echo $rstPt[sec_id] ?>';
    $('#sec_id').val(secid);
    var alm = '<?php echo $rstPt[pt_almuerzo] ?>';
    if (alm == 't') {
        $('#pt_almuerzo1').attr('checked', true);
    } else {
        $('#pt_almuerzo2').attr('checked', true);
    }
    var tp = '<?php echo $rstPt[pt_tipo_puesto] ?>';
    if (tp == '0') {
        $('#pt_tipo_puesto1').attr('checked', true);
    } else if (tp == '1') {
        $('#pt_tipo_puesto2').attr('checked', true);
    } else {
        $('#pt_tipo_puesto3').attr('checked', true);
    }
    var ps = '<?php echo $rstPt[pt_puesto_superior] ?>';
    $('#pt_puesto_superior').val(ps);
    var niv = '<?php echo $rstPt[pt_nivel] ?>';
    $('#pt_nivel').val(niv);
    var est = '<?php echo $rstPt[pt_estado] ?>';
    if (est == 't') {
        $('#pt_estado1').attr('checked', true);
    } else {
        $('#pt_estado2').attr('checked', true);
    }
</script>