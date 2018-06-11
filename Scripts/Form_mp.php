<?php
include_once '../Clases/clsSetting.php';
include_once '../Includes/permisos.php';
$Set = new Set();
$tbl_set = 'erp_mp_set';
$tbl = substr($tbl_set, 0, -4);
$tbl_name = 'mp';
$id = $_GET[id];
$tipo = $_GET[tipo];
$des = $_GET[des];
$files = pg_fetch_array($Set->lista_one_data($tbl_set, $tipo));
if (isset($_GET[id])) {
    $data = pg_fetch_array($Set->list_one_data_by_id($tbl, $id));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 5.0 Transitional//EN"> 
<html> 
    <meta charset=utf-8 />
    <title><?php echo $tbl_name ?></title>
    <head>
        <script>
            var tbl = '<?php echo $tbl_set ?>';
            var id = '<?php echo $_GET[id] ?>';
            var tipo = '<?php echo $_GET[tipo] ?>';
            var des = '<?php echo $_GET[des] ?>';
            var table = tbl.substring(0, tbl.length - 4);
            function save()
            {
                var data = Array(tipo);
                var file = Array('ids');
                //var obj = document.getElementsByTagName('input');
                var obj = document.getElementsByClassName('elemento');
                var i = 0;
                var x = 0;
                while (i < obj.length)
                {
                    var elem = document.getElementById(obj[i].id);
                    if (elem.lang == 0 && elem.value.length == 0)
                    {
                        x = 1;
                        break;
                    }
                    if (elem.type == 'file')
                    {
                        var img = document.getElementById('im' + elem.id);
                        data.push(img.src);
                    } else {
//                        if (elem.id == 'mp_c') {
//                            elem.value = elem.value + $('#aux').val();
//                            elem.value = elem.value;
//                        }
//                        if (elem.id != 'aux') {
                        data.push(elem.value.toUpperCase());
//                        }
//                        if (elem.id == 'mp_c') {
//                            cod = mp_c.value.split('.');
//                            mp_c.value = cod[0] + '.' + cod[1] + '.';
//                            aux.value = cod[2];
//                        }
                    }
//                    if (elem.id != 'aux') {
                    file.push(obj[i].id);
//                    }
                    i++;
                }
                var fields = Array();
                $("#tbl_form").find(':input').each(function () {
                    var elemento = this;
                    des = elemento.id + "=" + elemento.value;
                    fields.push(des);
                });
                ;
                loading('visible');

                if (x == 0)
                {
                    $.post("actions.php", {act: 5, 'data[]': data, 'field[]': file, tbl: table, id: id, s: '', 'fields[]': fields},
                    function (dt) {
                        if (dt == 0)
                        {
                            window.history.go(0);
                        } else {
                            if (dt == 'Ya existe') {
                                alert('Producto ya existe');
//                                cod = mp_c.value.split('.');
//                                mp_c.value = cod[0] + '.' + cod[1] + '.' + '';
//                                aux.value = cod[2];
                            } else {
                                alert(dt);
                            }
                            loading('hidden');

                        }
                    });
                } else {
                    alert('Existen Campos Requerido vacios \n Favor Revise ');
                    loading('hidden');
                }
            }
            function loading(prop) {
                $('#cargando').css('visibility', prop);
                $('#charging').css('visibility', prop);
            }
            function cancelar()
            {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
            }
            function archivo(evt, imgid) {
                var files = evt.target.files;
                for (var i = 0, f; f = files[i]; i++) {
                    if (!f.type.match('image.*')) {
                        continue;
                    }
                    var reader = new FileReader();
                    reader.onload = (function (theFile) {
                        return function (e) {
                            document.getElementById("im" + imgid).src = e.target.result;
                        };
                    })(f);
                    reader.readAsDataURL(f);
                }
            }

            function val_fecha(id)
            {
                fecha = document.getElementById(id);
                fch = fecha.value.split('/');
                ano = fch[2];
                mes = fch[1];
                dia = fch[0];
                valor = new Date(ano, mes, dia);
                if (isNaN(valor) || (ano.length != 4) || (mes.length != 2) || (mes > 12) || (dia.length != 2) || (dia > 31)) {
                    alert('Fecha incorrecta');
                    fecha.focus();
                }
            }

            function codigo()
            {
                if (mp_a.value != 0 && mp_b.value != 0) {
                    $.post("actions.php", {act: 79, id: mp_a.value, tbl: mp_b.value},
                    function (dt) {
                        dat = dt.split('.');
                        if (dt != 0)
                        {
                            mp_c.value = dat[0] + '.' + dat[1] + '.';
                            aux.value = dat[2];
                        }
                    });
                }
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
<!--<script type="text/javascript" src="../js/functions.js"></script>        -->
    </head>
    <body>
        <img id="charging" src="../img/load_bar.gif" />    
        <div id="cargando"></div>
        <table id="tbl_form" cellpadding="0" >
            <thead>
                <tr><th colspan="3"  ><?php echo 'REGISTRO DE ' . $des ?>
                        <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font>
                    </th></tr>
            </thead>
            <tr>
                <td id="head" colspan="3" valign="top" align="left">
                    <table>
                        <tr>
                            <?php
                            $n = 2;
                            while ($n <= count($files)) {
                                $file = explode('&', $files[$n]);
                                if ($file[0] == 'E' && !empty($file[9])) {
                                    if ($file[5] == 0) {
                                        $req = '<font class="req" >&#8727</font>';
                                    } else {
                                        $req = '';
                                    }
                                    switch ($file[2]) {
                                        case 'I':
                                            $val = $data[$file[8]];
                                            $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                            break;
                                        case 'N':
                                            $val = $data[$file[8]];
                                            if ($val == '') {
                                                $val = 0;
                                            }
                                            if ($file[8] == 'mp_d' || $file[8] == 'mp_e' || $file[8] == 'mp_f' || $file[8] == 'mp_g' || $file[8] == 'mp_h' || $file[8] == 'mp_j' || $file[8] == 'mp_k' || $file[8] == 'mp_l' || $file[8] == 'mp_m' || $file[8] == 'mp_p' || $file[8] == 'mp_r') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )' readonly />";
                                            } else if ($file[8] == 'mp_i') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val' hidden />";
                                            } else if ($file[8] == 'mp_q') {
                                                $input = "
                                            <select class='elemento' id='$file[8]' style='text-transform:lowercase' lang='$file[5]'>
                                            <option value=''>Seleccione</option>
                                            <option value='KG'>kg</option>
                                            <option value='LB'>lb</option>
                                            <option value='GR'>gr</option>
                                            <option value='LITRO'>litro</option>
                                            <option value='GALON'>galon</option>
                                            <option value='M'>m</option>
                                            <option value='CM'>cm</option>
                                            <option value='FT'>ft</option>
                                            <option value='IN'>in</option>
                                            <option value='UNIDAD'>UNIDAD</option>
                                            <option value='MILLAR'>MILLAR</option>
                                            <option value='ROLLO'>rollo</option>
                                            </select>
                                            <script>
                                             $('#mp_q').val('$val');
                                            </script>";
                                            } else {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                                            }
                                            break;
                                        case 'C':
                                            $val = $data[$file[8]];
                                            if ($file[8] != 'mp_y' && $file[8] != 'mp_z') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                            } else {
                                                $input = '';
                                            }
                                            break;
                                        case 'F':
                                            $val = $data[$file[8]];
                                            $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                            break;
                                        case 'E':
                                            $val = $data[$file[8]];
                                            if ($file[8] == 'mp_a') {
                                                $id = 2;
                                            } else {
                                                $id = 1;
                                            }
                                            $cnsEnlace = $Set->listOneById1($id);
                                            $input = "<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                            $input.="<option value='0'>Ninguno</option>";
                                            while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                                                $selected = '';
                                                if ($rstEnlace[tps_id] == $val) {
                                                    $selected = 'selected';
                                                }
                                                $input.="<option  $selected value='$rstEnlace[tps_id]'>$rstEnlace[tps_nombre]</option>";
                                            }
                                            $input.="</select>";
                                            break;
                                        case 'L':


                                            break;
                                    }
                                    ?>
                                    <?php
                                    if (trim($file[9]) != 'ESTADO' && trim($file[9]) != 'FECHA' && trim($file[9]) != 'IMPORTACION') {
                                        ?>
                                        <td><?php echo $file[9] . $req ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <?php echo $input ?>    
                                    </td>
                                    <?php
                                }
                                $n++;
                            }
                            ?>            
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td id="left" valign="top" align="left">
                    <table>
                        <tr>
                            <td></td>
                            <td></td>
                            <?php
                            $n = 2;
                            while ($n <= count($files)) {
                                $file = explode('&', $files[$n]);
                                if ($file[0] == 'T' && !empty($file[9])) {
                                    ?>
                                    <td><?php echo $file[9] ?></td>
                                    <?php
                                }
                                $n++;
                            }
                            ?>            
                        </tr>
                        <?php
                        $n = 2;
                        while ($n <= count($files)) {
                            $file = explode('&', $files[$n]);
                            if ($file[0] == 'I' && !empty($file[9])) {
                                if ($file[5] == 0) {
                                    $req = '<font class="req" >&#8727</font>';
                                } else {
                                    $req = '';
                                }
                                switch ($file[2]) {

                                    case 'I':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                        break;
                                    case 'N':
                                        $val = $data[$file[8]];
                                        if ($val == '') {
                                            $val = 0;
                                        }
                                        if ($file[8] == 'mp_d' || $file[8] == 'mp_e' || $file[8] == 'mp_f' || $file[8] == 'mp_g' || $file[8] == 'mp_h' || $file[8] == 'mp_j' || $file[8] == 'mp_k' || $file[8] == 'mp_l' || $file[8] == 'mp_m' || $file[8] == 'mp_p' || $file[8] == 'mp_r') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )' readonly />";
                                        } else if ($file[8] == 'mp_i') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val' hidden />";
                                        } else if ($file[8] == 'mp_q') {
                                            $input = "
                                            <select class='elemento' id='$file[8]' style='text-transform:lowercase' lang='$file[5]'>
                                            <option value=''>Seleccione</option>
                                            <option value='KG'>kg</option>
                                            <option value='LB'>lb</option>
                                            <option value='GR'>gr</option>
                                            <option value='LITRO'>litro</option>
                                            <option value='GALON'>galon</option>
                                            <option value='M'>m</option>
                                            <option value='CM'>cm</option>
                                            <option value='FT'>ft</option>
                                            <option value='IN'>in</option>
                                            <option value='UNIDAD'>UNIDAD</option>
                                            <option value='MILLAR'>MILLAR</option>
                                             <option value='ROLLO'>rollo</option>
                                            </select>
                                            <script>
                                             $('#mp_q').val('$val');
                                            </script>";
                                        } else {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                                        }
                                        break;
                                       case 'C':
                                            $val = $data[$file[8]];
                                            if ($file[8] != 'mp_y' && $file[8] != 'mp_z') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                            } else {
                                                $input = '';
                                            }
                                            break;
                                    case 'F':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                        break;
                                    case 'E':
                                        $val = $data[$file[8]];
                                        if ($file[8] == 'mp_a') {
                                            $id = 2;
                                        } else {
                                            $id = 1;
                                        }
                                        $cnsEnlace = $Set->listOneById1($id);
                                        $input = "<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                        $input.="<option value='0'>Ninguno</option>";
                                        while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                                            $selected = '';
                                            if ($rstEnlace[tps_id] == $val) {
                                                $selected = 'selected';
                                            }
                                            $input.="<option  $selected value='$rstEnlace[tps_id]'>$rstEnlace[tps_nombre]</option>";
                                        }
                                        $input.="</select>";
                                        break;
                                }
                                ?>
                                <tr>
                                    <?php
                                    if (trim($file[9]) != 'ESTADO' && trim($file[9]) != 'FECHA' && trim($file[9]) != 'IMPORTACION') {
                                        ?>
                                        <td><?php echo $file[9] . $req ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td><?php echo $input ?></td>
                                </tr>                                            
                                <?php
                            }
                            $n++;
                        }
                        ?>            

                    </table>
                </td>
                <td id="center" valign="top" align="left">
                    <table>
                        <?php
                        $n = 2;
                        while ($n <= count($files)) {
                            $file = explode('&', $files[$n]);
                            if ($file[0] == 'C' && !empty($file[9])) {
                                if ($file[5] == 0) {
                                    $req = '<font class="req" >&#8727</font>';
                                } else {
                                    $req = '';
                                }
                                switch ($file[2]) {
                                    case 'I':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                        break;
                                    case 'N':
                                        $val = $data[$file[8]];
                                        if ($val == '') {
                                            $val = 0;
                                        }

                                        if ($file[8] == 'mp_d' || $file[8] == 'mp_e' || $file[8] == 'mp_f' || $file[8] == 'mp_g' || $file[8] == 'mp_h' || $file[8] == 'mp_j' || $file[8] == 'mp_k' || $file[8] == 'mp_l' || $file[8] == 'mp_m' || $file[8] == 'mp_p' || $file[8] == 'mp_r') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )' readonly />";
                                        } else if ($file[8] == 'mp_i') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val' hidden />";
                                        } else if ($file[8] == 'mp_q') {
                                            $input = "
                                            <select class='elemento' id='$file[8]' style='text-transform:lowercase' lang='$file[5]'>
                                            <option value=''>Seleccione</option>
                                            <option value='KG'>kg</option>
                                            <option value='LB'>lb</option>
                                            <option value='GR'>gr</option>
                                            <option value='LITRO'>litro</option>
                                            <option value='GALON'>galon</option>
                                            <option value='M'>m</option>
                                            <option value='CM'>cm</option>
                                            <option value='FT'>ft</option>
                                            <option value='IN'>in</option>
                                            <option value='UNIDAD'>UNIDAD</option>
                                            <option value='MILLAR'>MILLAR</option>
                                             <option value='ROLLO'>rollo</option>
                                            </select>
                                            <script>
                                             $('#mp_q').val('$val');
                                            </script>";
                                        } else {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                                        }
                                        break;
                                      case 'C':
                                            $val = $data[$file[8]];
                                            if ($file[8] != 'mp_y' && $file[8] != 'mp_z') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                            } else {
                                                $input = '';
                                            }
                                            break;
                                    case 'F':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                        break;
                                    case 'E':
                                        $val = $data[$file[8]];
                                        if ($file[8] == 'mp_a') {
                                            $id = 2;
                                        } else {
                                            $id = 1;
                                        }
                                        $cnsEnlace = $Set->listOneById1($id);
                                        $input = "<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                        $input.="<option value='0'>Ninguno</option>";
                                        while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                                            $selected = '';
                                            if ($rstEnlace[tps_id] == $val) {
                                                $selected = 'selected';
                                            }
                                            $input.="<option  $selected value='$rstEnlace[tps_id]'>$rstEnlace[tps_nombre]</option>";
                                        }
                                        $input.="</select>";
                                        break;
                                }
                                ?>
                                <tr>
                                    <?php
                                    if (trim($file[9]) != 'ESTADO' && trim($file[9]) != 'FECHA' && trim($file[9]) != 'IMPORTACION') {
                                        ?>
                                        <td><?php echo $file[9] . $req ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <?php echo $input ?>    
                                    </td>
                                </tr>                                            
                                <?php
                            }
                            $n++;
                        }
                        ?>            

                    </table>
                </td>
                <td id="right" valign="top" align="left">
                    <table>
                        <?php
                        $n = 2;
                        while ($n <= count($files)) {
                            $file = explode('&', $files[$n]);
                            if ($file[0] == 'D' && !empty($file[9])) {
                                if ($file[5] == 0) {
                                    $req = '<font class="req" >&#8727</font>';
                                } else {
                                    $req = '';
                                }
                                switch ($file[2]) {
                                    case 'I':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                        break;
                                    case 'N':
                                        $val = $data[$file[8]];
                                        if ($val == '') {
                                            $val = 0;
                                        }
                                        if ($file[8] == 'mp_d' || $file[8] == 'mp_e' || $file[8] == 'mp_f' || $file[8] == 'mp_g' || $file[8] == 'mp_h' || $file[8] == 'mp_j' || $file[8] == 'mp_k' || $file[8] == 'mp_l' || $file[8] == 'mp_m' || $file[8] == 'mp_p' || $file[8] == 'mp_r') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )' readonly />";
                                        } else if ($file[8] == 'mp_i') {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val' hidden />";
                                        } else if ($file[8] == 'mp_q') {
                                            $input = "
                                            <select class='elemento' id='$file[8]' style='text-transform:lowercase' lang='$file[5]'>
                                            <option value=''>Seleccione</option>
                                            <option value='KG'>kg</option>
                                            <option value='LB'>lb</option>
                                            <option value='GR'>gr</option>
                                            <option value='LITRO'>litro</option>
                                            <option value='GALON'>galon</option>
                                            <option value='M'>m</option>
                                            <option value='CM'>cm</option>
                                            <option value='FT'>ft</option>
                                            <option value='IN'>in</option>
                                            <option value='UNIDAD'>UNIDAD</option>
                                            <option value='MILLAR'>MILLAR</option>
                                             <option value='ROLLO'>rollo</option>
                                            </select>
                                            <script>
                                             $('#mp_q').val('$val');
                                            </script>";
                                        } else {
                                            $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                                        }
                                        break;
                                      case 'C':
                                            $val = $data[$file[8]];
                                            if ($file[8] != 'mp_y' && $file[8] != 'mp_z') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                            } else {
                                                $input = '';
                                            }
                                            break;
                                    case 'F':
                                        $val = $data[$file[8]];
                                        $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                        break;
                                    case 'E':
                                        $val = $data[$file[8]];
                                        if ($file[8] == 'mp_a') {
                                            $id = 2;
                                        } else {
                                            $id = 1;
                                        }
                                        $cnsEnlace = $Set->listOneById1($id);
                                        $input = "<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                        $input.="<option value='0'>Ninguno</option>";
                                        while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                                            $selected = '';
                                            if ($rstEnlace[tps_id] == $val) {
                                                $selected = 'selected';
                                            }
                                            $input.="<option  $selected value='$rstEnlace[tps_id]'>$rstEnlace[tps_nombre]</option>";
                                        }
                                        $input.="</select>";
                                        break;
                                }
                                ?>
                                <tr>
                                    <?php
                                    if (trim($file[9]) != 'ESTADO' && trim($file[9]) != 'FECHA' && trim($file[9]) != 'IMPORTACION') {
                                        ?>
                                        <td><?php echo $file[9] . $req ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <?php echo $input ?>    
                                    </td>
                                </tr>                                            
                                <?php
                            }
                            $n++;
                        }
                        ?>            

                    </table>
                </td>
            </tr>
            <tr>
                <td id="foot" colspan="3" valign="top" align="left">
                    <table>
                        <tr>
                            <?php
                            $n = 2;
                            while ($n <= count($files)) {
                                $file = explode('&', $files[$n]);
                                if ($file[0] == 'P' && !empty($file[9])) {
                                    if ($file[5] == 0) {
                                        $req = '<font class="req" >&#8727</font>';
                                    } else {
                                        $req = '';
                                    }
                                    switch ($file[2]) {
                                        case 'I':
                                            $val = $data[$file[8]];
                                            $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                              <img src='$val' width='128px' id='im$file[8]'/> ";
                                            break;
                                        case 'N':
                                            $val = $data[$file[8]];
                                            if ($val == '') {
                                                $val = 0;
                                            }
                                            if ($file[8] == 'mp_d' || $file[8] == 'mp_e' || $file[8] == 'mp_f' || $file[8] == 'mp_g' || $file[8] == 'mp_h' || $file[8] == 'mp_j' || $file[8] == 'mp_k' || $file[8] == 'mp_l' || $file[8] == 'mp_m' || $file[8] == 'mp_p' || $file[8] == 'mp_r') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )' readonly />";
                                            } else if ($file[8] == 'mp_i') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val' hidden />";
                                            } else {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                                            }
                                            break;
                                          case 'C':
                                            $val = $data[$file[8]];
                                            if ($file[8] != 'mp_y' && $file[8] != 'mp_z') {
                                                $input = "<input class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                                            } else {
                                                $input = '';
                                            }
                                            break;
                                        case 'F':
                                            $val = $data[$file[8]];
                                            $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                                            break;
                                        case 'E':
                                            $val = $data[$file[8]];
                                            if ($file[8] == 'mp_a') {
                                                $id = 2;
                                            } else {
                                                $id = 1;
                                            }
                                            $cnsEnlace = $Set->listOneById1($id);
                                            $input = "<select class='elemento' lang='$file[5]' id='$file[8]'>";
                                            $input.="<option value='0'>Ninguno</option>";
                                            while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                                                $selected = '';
                                                if ($rstEnlace[tps_id] == $val) {
                                                    $selected = 'selected';
                                                }
                                                $input.="<option  $selected value='$rstEnlace[tps_id]'>$rstEnlace[tps_nombre]</option>";
                                            }
                                            $input.="</select>";
                                            break;
                                    }
                                   if (trim($file[9]) != 'ESTADO' && trim($file[9]) != 'FECHA' && trim($file[9]) != 'IMPORTACION') {
                                        ?>
                                        <td><?php echo $file[9] . $req ?></td>
                                        <?php
                                    }
                                    ?>
                                    <td>
                                        <?php echo $input ?>    
                                    </td>
                                    <?php
                                }
                                $n++;
                            }
                            ?>            

                        </tr>
                    </table>
                </td>
            </tr>        
            <tr>
                <td colspan="3">
                    <?php
                    if ($Prt->add == 0 || $Prt->edition == 0) {
                        ?>
                        <button id="save" onclick="save()">Guardar</button>
                    <?php }
                    ?>
                    <button id="cancel" onclick="cancelar()">Cancelar</button>
                </td>
            </tr>                    

        </table>   

        <?php
        if ($_GET[x] == 1) {
            echo "<script> document.getElementById('save').hidden=true </script>";
        } else {
            echo "<script> document.getElementById('save').hidden=false </script>";
        }
        ?>    
    </body>
</html>