<?php
include_once '../Includes/permisos.php';
include_once '../Clases/clsClase_productos.php'; // cambiar clsClase_productos
include_once '../Clases/clsSetting.php';
$Set = new Set();
$Productos = new Clase_Productos();
if (isset($_GET[id])) {
    $id = $_GET[id];
    $x = $_GET[x];
    $rst = pg_fetch_array($Productos->lista_uno($id));
    $cns_combomp1 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp2 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp3 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp4 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp5 = $Productos->lista_combomp($rst[emp_id]);
    $cns_combomp6 = $Productos->lista_combomp($rst[emp_id]);
} else {
    $id = 0;
    $rst['pro_largo'] = 0;
    $rst['pro_ancho'] = 0;
    $rst['pro_capa'] = 0;
    $rst['pro_espesor'] = 0;
    $rst['pro_gramaje'] = 0;
    $rst['pro_peso'] = 0;
    $rst['pro_medvul'] = 0;
    $rst['pro_mf1'] = 0;
    $rst['pro_mf2'] = 0;
    $rst['pro_mf3'] = 0;
    $rst['pro_mf4'] = 0;
    $rst['pro_mf5'] = 0;
    $rst['pro_mf6'] = 0;
    $rst['suma'] = 0;
}
$cns_combo = $Productos->lst_emp();
?>
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            var id = '<?php echo $id ?>';
            $(function () {
                parent.document.getElementById('contenedor2').rows = "*,80%";
                $('#cancelar').click(function (e) {
                    e.preventDefault();
                    cancelar();
                });
                $('#frm_save').submit(function (e) {
                    e.preventDefault();
                    save(id);
                });
                $('#pro_mf1,#pro_mf2,#pro_mf3,#pro_mf4,#pro_mf5,#pro_mf6').change(function () {
                    suma();
                });
                $('#pro_largo,#pro_ancho,#pro_gramaje').change(function () {
                    calcular();
                });
                if (id == 0) {
                    mostrarr();
                    load_datos();
                }

            });
            function save(id) {
                if (pro_familia1.checked == true) {
                    fml = 1;
                } else {
                    fml = 2;
                }

                var data = Array(emp_id.value,
                        pro_codigo.value,
                        fml,
                        pro_descripcion.value,
                        pro_uni.value,
                        pro_largo.value,
                        pro_ancho.value,
                        pro_capa.value,
                        pro_espesor.value,
                        pro_gramaje.value,
                        pro_peso.value,
                        pro_medvul.value,
                        pro_mp1.value,
                        pro_mp2.value,
                        pro_mp3.value,
                        pro_mp4.value,
                        pro_mp5.value,
                        pro_mp6.value,
                        pro_mf1.value,
                        pro_mf2.value,
                        pro_mf3.value,
                        pro_mf4.value,
                        pro_mf5.value,
                        pro_mf6.value,
                        tpt_id.value
                        );
                fields = $('#frm_save').serialize();
                $.ajax({
                    beforeSend: function () {
                        if (emp_id.value == 0) {
                            $("#emp_id").css({borderColor: "red"});
                            $("#emp_id").focus();
                            return false;
                        }
                        else if (pro_codigo.value.length == 0) {
                            $("#pro_codigo").css({borderColor: "red"});
                            $("#pro_codigo").focus();
                            return false;
                        }
                        else if (pro_descripcion.value.length == 0) {
                            $("#pro_descripcion").css({borderColor: "red"});
                            $("#pro_descripcion").focus();
                            return false;
                        }
                        else if (pro_uni.value == 0) {
                            $("#pro_uni").css({borderColor: "red"});
                            $("#pro_uni").focus();
                            return false;
                        }
                        else if (pro_largo.value.length == 0) {
                            $("#pro_largo").css({borderColor: "red"});
                            $("#pro_largo").focus();
                            return false;
                        }
                        else if (pro_ancho.value.length == 0) {
                            $("#pro_ancho").css({borderColor: "red"});
                            $("#pro_ancho").focus();
                            return false;
                        }
                        else if (pro_capa.value.length == 0) {
                            $("#pro_capa").css({borderColor: "red"});
                            $("#pro_capa").focus();
                            return false;
                        }
                        else if (pro_espesor.value.length == 0) {
                            $("#pro_espesor").css({borderColor: "red"});
                            $("#pro_espesor").focus();
                            return false;
                        }
                        else if (pro_gramaje.value.length == 0) {
                            $("#pro_gramaje").css({borderColor: "red"});
                            $("#pro_gramaje").focus();
                            return false;
                        }
                        else if (pro_peso.value.length == 0) {
                            $("#pro_peso").css({borderColor: "red"});
                            $("#pro_peso").focus();
                            return false;
                        }

                        else if ($("#emp_id").val() != '7') {
                            if (pro_mp1.value == 0) {
                                $("#pro_mp1").css({borderColor: "red"});
                                $("#pro_mp1").focus();
                                return false;
                            }
                            else if ($("#suma").val() != 100) {
                                $("#suma").css({borderColor: "red"});
                                $("#suma").focus();
                                return false;
                            }
                        }

                        if (pro_mf1.value.length == 0) {
                            $("#pro_mf1").css({borderColor: "red"});
                            $("#pro_mf1").focus();
                            return false;
                        }
                        else if (pro_mf2.value.length == 0) {
                            $("#pro_mf2").css({borderColor: "red"});
                            $("#pro_mf2").focus();
                            return false;
                        }
                        else if (pro_mf3.value.length == 0) {
                            $("#pro_mf3").css({borderColor: "red"});
                            $("#pro_mf3").focus();
                            return false;
                        }
                        else if (pro_mf4.value.length == 0) {
                            $("#pro_mf4").css({borderColor: "red"});
                            $("#pro_mf4").focus();
                            return false;
                        }


                    },
                    type: 'POST',
                    url: 'actions_productos.php', //cambiar actions_productos
                    data: {op: 0, 'data[]': data, 'fields[]': fields, id: id}, //op sera de acuerdo a la acion que le toque
                    success: function (dt) {
                        if (dt == 0) {
                            insert_precios(pro_codigo.value);
                        } else {
                            alert(dt); //Controlar el erros de acuerdo al mensaje y poner un mensaje entendible para el usuario
                        }

                    }
                })

            }

            function insert_precios(cod) {
                $.post("actions_preciospt.php", {op: 3, tab: 0, cod: cod},
                function (dt) {
                    if (dt != 0) {
                        alert(dt);
                    }else{
                        window.history.go(0);
                    }
                });
            }
            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function suma() {
                var sm = parseFloat($('#pro_mf1').val() * 1) + 0;
                sm = sm.toFixed(2);
                $('#suma').val(sm);
                var su = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1);
                su = su.toFixed(2);
                $('#suma').val(su);
                var sum = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1);
                sum = sum.toFixed(2);
                $('#suma').val(sum);
                var s = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1);
                s = s.toFixed(2);
                $('#suma').val(s);
                var ss = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1) + parseFloat($('#pro_mf5').val() * 1);
                ss = ss.toFixed(2);
                $('#suma').val(ss);
                var ssm = parseFloat($('#pro_mf1').val() * 1) + parseFloat($('#pro_mf2').val() * 1) + parseFloat($('#pro_mf3').val() * 1) + parseFloat($('#pro_mf4').val() * 1) + parseFloat($('#pro_mf5').val() * 1) + parseFloat($('#pro_mf6').val() * 1);
                ssm = ssm.toFixed(2);
                $('#suma').val(ssm);
                if (sm == 100) {
                    $('#pro_mp2').hide();
                    $('#pro_mf2').hide();
                    $('#lblporcentaje1').hide();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#lblporcentaje2').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp2').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(sm);
                }
                else if (su == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#lblporcentaje1').show();
                    $('#pro_mp3').hide();
                    $('#pro_mf3').hide();
                    $('#lblporcentaje2').hide();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp3').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(su);
                }
                else if (sum == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').hide();
                    $('#pro_mf4').hide();
                    $('#lblporcentaje3').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp4').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(sum);
                }
                else if (s == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp5').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(s);
                }
                else if (ss == 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').show();
                    $('#pro_mf5').show();
                    $('#lblporcentaje4').show();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_mp6').val('0');
                    $('#pro_mf6').val('0');
                    $("#suma").val(ss);
                }
                else if (su < 100 && sum < 100 && s < 100 && ss < 100) {
                    $('#pro_mp2').show();
                    $('#pro_mf2').show();
                    $('#lblporcentaje1').show();
                    $('#pro_mp3').show();
                    $('#pro_mf3').show();
                    $('#lblporcentaje2').show();
                    $('#pro_mp4').show();
                    $('#pro_mf4').show();
                    $('#lblporcentaje3').show();
                    $('#pro_mp5').show();
                    $('#pro_mf5').show();
                    $('#lblporcentaje4').show();
                    $('#pro_mp6').show();
                    $('#pro_mf6').show();
                    $('#lblporcentaje5').show();
                }
                var a = $("#emp_id").val();
                if (a == 3 || a == 4 || a == 6) {
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                }
            }
            function calcular() {
//                var c = (parseFloat($('#pro_largo').val() * 1) * parseFloat($('#pro_ancho').val() * 1) * parseFloat($('#pro_gramaje').val() * 1)) / 1000;
//                c = c.toFixed(2);
//                $('#pro_peso').val(c);
            }
            function load_datos(obj) {
                //obj.value=3;
                $.post('actions_productos.php', {id: 3, op: 2}, function (dt) { // cambiar a actions_productos
                    dat = dt.split('&');
                    $('#pro_codigo').val(dat[0]);
                    $('#pro_mp1,#pro_mp2,#pro_mp3,#pro_mp4,#pro_mp5,#pro_mp6').html(dat[1]);
                })
            }
            function ocultar() {
                $('#pro_medvul').hide();
                $('#lblmedida1').show();
                $('#lblmedida2').hide();
                $('#lblmedidor').hide();
                $('#lblcapa').hide();
                $('#pro_capa').hide();
                $('#pro_medvul').val('0');
                $('#pro_capa').val('0');
            }
            function mostrar() {
                $('#pro_medvul').show();
                $('#lblmedida1').hide();
                $('#lblmedida2').show();
                $('#lblmedidor').show();
                $('#lblcapa').show();
                $('#pro_capa').show();
            }
            function mostrarr() {
                //var a = $("#emp_id").val();
                var a = 5;
                if (a == 0) {
                    pro_familia1.checked = true;
                    $('#lblmedida1').show();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
                } else if (a == 3) {//Plumon
                    $('#lblfamilia').show();
                    $('#lblalmohada').show();
                    $('#pro_familia1').show();
                    $('#lblrollo').show();
                    $('#pro_familia2').show();
                    $('#lblespesor').show();
                    $('#pro_espesor').show();
                    $('#lblmedida1').show();
                    $('#tbmix').show();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('0');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#suma').val('0');
                } else if (a == 5) {//Ecocambrella
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
//                    $('#lblespesor').hide();
//                    $('#pro_espesor').hide();
                    $('#lblmedida1').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#tbmix').show();
                    $('#pro_mp5').show();
                    $('#pro_mf5').show();
                    $('#lblporcentaje4').show();
                    $('#pro_mp6').show();
                    $('#pro_mf6').show();
                    $('#lblporcentaje5').show();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('0');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mp5').val('0');
                    $('#pro_mp6').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#pro_mf5').val('0');
                    $('#pro_mf6').val('0');
                    $('#suma').val('0');
                } else if (a == 6) {//Geotextil
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
                    $('#lblespesor').hide();
                    $('#pro_espesor').hide();
                    $('#lblmedida1').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#tbmix').show();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('0');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#suma').val('0');
                } else if (a == 4) {//Padding
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
                    $('#lblespesor').hide();
                    $('#pro_espesor').hide();
                    $('#lblmedida1').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#pro_mp5').hide();
                    $('#pro_mf5').hide();
                    $('#lblporcentaje4').hide();
                    $('#pro_mp6').hide();
                    $('#pro_mf6').hide();
                    $('#lblporcentaje5').hide();
                    $('#tbmix').show();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('0');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#suma').val('0');
                } else if (a == 0) {
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
                    $('#lblespesor').hide();
                    $('#pro_espesor').hide();
                    $('#lblmedida1').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#tbmix').show();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('0');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#suma').val('0');
                } else if (a == 7) {//I comercial
                    $('#lblfamilia').hide();
                    $('#lblalmohada').hide();
                    $('#pro_familia1').hide();
                    $('#lblrollo').hide();
                    $('#pro_familia2').hide();
                    $('#lblcapa').hide();
                    $('#pro_capa').hide();
                    $('#lblespesor').hide();
                    $('#pro_espesor').hide();
                    $('#lblmedida1').hide();
                    $('#lblmedida2').hide();
                    $('#lblmedidor').hide();
                    $('#pro_medvul').hide();
                    $('#tbmix').hide();
                    $('#pro_descripcion').val('');
                    $('#pro_uni').val('');
                    $('#pro_largo').val('0');
                    $('#pro_ancho').val('0');
                    $('#pro_gramaje').val('0');
                    $('#pro_peso').val('0');
                    $('#pro_espesor').val('0');
                    $('#pro_mp1').val('1');
                    $('#pro_mp2').val('0');
                    $('#pro_mp3').val('0');
                    $('#pro_mp4').val('0');
                    $('#pro_mf1').val('0');
                    $('#pro_mf2').val('0');
                    $('#pro_mf3').val('0');
                    $('#pro_mf4').val('0');
                    $('#suma').val('100');
                }
            }
        </script>
    </head>
    <body>
        <form  autocomplete="off" id="frm_save">
            <style>
                tbody{
                    float:left;
                }
                select{
                    margin:3px; 
                }
                input[type=text]{
                    text-transform: uppercase;
                }
                #emp_id{
                    width: 150px;
                }
                #pro_mp1{
                    width: 200px;
                }
                #pro_mp2{
                    width: 200px;
                }
                #pro_mp3{
                    width: 200px;
                }
                #pro_mp4{
                    width: 200px;
                }
                #pro_mp5{
                    width: 200px;
                }
                #pro_mp6{
                    width: 200px;
                }
            </style>
            <table id="tbl_form" border="0">
                <thead>
                    <tr><th colspan="5" >
                            PRODUCTOS
                            <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                        </th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><label for="male" id="lblfabrica">Fabrica:</label></td>
                        <td>
                            <select id="emp_id" onchange="load_datos(this), mostrarr(this)" disabled style="width:230px; " >
                                <?php
                                while ($rst_combo = pg_fetch_array($cns_combo)) {
                                    echo "<option value='$rst_combo[emp_id]' >$rst_combo[emp_descripcion]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Tipo:</td>
                        <td>
                            <select name="tpt_id" id="tpt_id" style="width:230px " >
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
                        <td>CODIGO:</td>
                        <td><input type="text" size="35"  id="pro_codigo" value="<?php echo $rst['pro_codigo'] ?>"  /></td>
                    </tr>
                    <tr hidden>
                        <td><label for="male" id="lblfamilia">Familia:</label></td>
                        <td>
                            <label fot="male" id="lblalmohada">ALMOHADA</label>
                            <input type="radio" size="12"  id="pro_familia1" name="pro_familia" value="1" onclick="ocultar()"/>
                            <label for="male" id="lblrollo">ROLLO</label>
                            <input type="radio" size="12"  id="pro_familia2" name="pro_familia" value="2" onclick="mostrar()" />
                        </td>
                    </tr>
                    <tr>
                        <td> Descripcion: </td>
                        <td> <input type ="text" size="35"  id="pro_descripcion"  value="<?php echo $rst['pro_descripcion'] ?>"/></td>
                    </tr>
                    <tr>
                        <td>Unidad:</td>
                        <td><select id="pro_uni">
                                <option value="0">Seleccione</option>
                                <option value="rollo">Rollo</option>
                                <option value="unidad">Unidad</option>
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
                        </td>
                    </tr>   
                    <tr>
                        <td>Medida1:</td>
                        <td><input type="text" size="15"  id="pro_largo"  value="<?php echo $rst['pro_largo'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>Medida2:</td>
                        <td><input type="text" size="15"  id="pro_ancho"  value="<?php echo $rst['pro_ancho'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>Medida3:</td>
                        <td><input type="text" size="15"  id="pro_gramaje"  value="<?php echo $rst['pro_gramaje'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr>
                        <td>Propiedad1:</td>
                        <td><input type="text" size="15"  id="pro_peso"  value="<?php echo $rst['pro_peso'] ?>"  /></td>
                    </tr>
                    <tr>
                        <td><label for="male" id="lblespesor">Propiedad2:</label></td>
                        <td><input type="text" size="15"  id="pro_espesor"  value="<?php echo $rst['pro_espesor'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblmedida1">cm</label><label for="male" id="lblmedida2"></label></td>
                    </tr>
                    <tr hidden >
                        <td><label for="male" id="lblcapa"># Capa:</label></td>
                        <td><input type="text" size="15"  id="pro_capa"  value="<?php echo $rst['pro_capa'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                    <tr hidden>
                        <td><label for="male" id="lblmedidor">Medidor de Vueltas</label> </td>
                        <td><input type="text" size="15"  id="pro_medvul" value="<?php echo $rst['pro_medvul'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /></td>
                    </tr>
                </tbody>
                <tbody id="tbmix">
                    <tr><td></td></tr>
                    <tr><td><br></td></tr>
                    <tr>
                        <td>&emsp;Composicion</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp1">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp1)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf1"  value="<?php echo $rst['pro_mf1'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" />%</td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp2">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp2)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf2"  value="<?php echo $rst['pro_mf2'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje1">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp3">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp3)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf3"  value="<?php echo $rst['pro_mf3'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje2">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp4">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp4)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf4"  value="<?php echo $rst['pro_mf4'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje3">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp5">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp5)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf5"  value="<?php echo $rst['pro_mf5'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje4">%</label></td>
                    </tr>
                    <tr>
                        <td><select id="pro_mp6">
                                <option value="0">Seleccione</option>
                                <?php
                                while ($rst_combomp = pg_fetch_array($cns_combomp6)) {
                                    echo "<option value='$rst_combomp[mp_id]' >$rst_combomp[mp_referencia]</option>";
                                }
                                ?>  
                            </select>
                        </td>
                        <td><input type="text" size="12"  id="pro_mf6"  value="<?php echo $rst['pro_mf6'] ?>" onkeyup="this.value = this.value.replace(/[^0-9.]/, '');" /><label for="male" id="lblporcentaje5">%</label></td>
                        <td></td>
                    <tr<td></td></tr>
                <td></td>
                <td><input type="text" size="10"  id="suma" readonly value="<?php echo $rst['suma'] ?>"/>%</td>
                </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <?php
                            if ($x != 1) {
                                ?>
                                <button id="guardar" >Guardar</button>    
                                <?php
                            }
                            ?>
                            <button id="cancelar" >Cancelar</button>    
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </body>
</html>    
<script>

    var emp =<?php echo $rst[emp_id] ?>;
    var mp1 =<?php echo $rst[pro_mp1] ?>;
    var mp2 =<?php echo $rst[pro_mp2] ?>;
    var mp3 =<?php echo $rst[pro_mp3] ?>;
    var mp4 =<?php echo $rst[pro_mp4] ?>;
    var mp5 =<?php echo $rst[pro_mp5] ?>;
    var mp6 =<?php echo $rst[pro_mp6] ?>;
    var mf1 =<?php echo $rst[pro_mf1] ?>;
    var mf2 =<?php echo $rst[pro_mf2] ?>;
    var mf3 =<?php echo $rst[pro_mf3] ?>;
    var mf4 =<?php echo $rst[pro_mf4] ?>;
    var mf5 =<?php echo $rst[pro_mf5] ?>;
    var mf6 =<?php echo $rst[pro_mf6] ?>;
    var fml =<?php echo $rst[pro_familia] ?>;
    var uni = '<?php echo strtolower($rst[pro_uni]) ?>';

    $('#emp_id').val(emp);
    $('#pro_uni').val(uni);
    $('#pro_mp1').val(mp1);
    $('#pro_mp2').val(mp2);
    $('#pro_mp3').val(mp3);
    $('#pro_mp4').val(mp4);
    $('#pro_mp5').val(mp5);
    $('#pro_mp6').val(mp6);

    var s = mf1 + mf2 + mf3 + mf4;
    $('#suma').val(s);

    var ssm = mf1 + mf2 + mf3 + mf4 + mf5 + mf6;
    $('#suma').val(ssm);

</script>