<?php
include_once '../Includes/permisos.php';
include_once("../Clases/clsPuestoTrabajo.php");
include_once("../Clases/clsClase_empleados.php");
$Emp = new Clase_empleados();
$ger = $_REQUEST[gr];
$div = $_REQUEST[dv];
$sem = $_REQUEST[id];
$yr = $_REQUEST[year];
$Puestos = new PuestoTrabajo();
$cnsPuesto = $Puestos->listaAllPuestoTrabajoGD($ger, $div);
?>

<html>
    <head>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">    
        <meta charset="utf-8">
        <title>Formulario</title>
        <script>
            $(function () {
                posicion_aux_window();
            });
            function save(id, emp, sem, ger, div, y)
            {
                val = id.split('-')

                fecha = '<?php echo date('Y-m-d') ?>';
                switch (val[0])
                {
                    case 'id1':
                        id2 = 'id2-' + val[1];
                        var doc = document.getElementById(id2);
                        if (doc.value == '') {
                            doc.value = 0;
                        }
                        emp1 = emp
                        emp2 = doc.value
                        break;
                    case 'id2':
                        id2 = 'id1-' + val[1];
                        var doc = document.getElementById(id2);
                        if (doc.value == '') {
                            doc.value = 0;
                        }
                        emp1 = doc.value;
                        emp2 = emp;
                        break;
                }

                var data = Array(emp1, val[1], sem, fecha, emp2, 0, y);
                var fields = (
                        'año=' + '<?php echo $yr ?>' + '&' +
                        'semana=' + '<?php echo $sem ?>' + '&' +
                        'division=' + '<?php echo $div ?>' + '&' +
                        'seccion' + val[1] + '=' + $('#seccion' + val[1]).html() + '&' +
                        'cargo' + val[1] + '=' + $('#cargo' + val[1]).html() + '&' +
                        'responsabilidad' + val[1] + '=' + $('#responsabilidad' + val[1]).html() + '&' +
                        't1_numero' + val[1] + '=' + emp1 + '&' +
                        't1_codigo' + val[1] + '=' + $('#t1_codigo' + val[1]).html() + '&' +
                        't2_numero' + val[1] + '=' + emp2 + '&' +
                        't2_codigo' + val[1] + '=' + $('#t2_codigo' + val[1]).html() + '& &'
                        );

                $.post("actions_asignacion_puestos.php", {'data[]': data, ger: ger, div: div, act: 0, op: '0', 'fields': fields},
                function (dat) {
                    msbox = dat.split('*');
                    if (msbox[0] != 'error')
                    {
                        cod1 = 'id1-' + val[1]
                        emp1 = 'id1' + val[1]
                        cod2 = 'id2-' + val[1]
                        emp2 = 'id2' + val[1]
                        var val1 = document.getElementById(cod1);
                        var name1 = document.getElementById(emp1);
                        var val2 = document.getElementById(cod2);
                        var name2 = document.getElementById(emp2);
                        val1.value = msbox[0];
                        name1.innerHTML = msbox[1];
                        val2.value = msbox[2];
                        name2.innerHTML = msbox[3];

                    } else {
                        if (msbox[1] == 'duplicado')
                        {
                            alert('Empleado Ya Esta Asignado En otra seccion')
                            cod1 = 'id1-' + val[1]
                            cod2 = 'id2-' + val[1]
                            var val1 = document.getElementById(cod1);
                            var val2 = document.getElementById(cod2);
                            val1.value = msbox[2];
                            val2.value = msbox[3];
                        } else {
                            alert(msbox[1])
                        }
                    }

                })
            }

            function saveAs(sem, ger, div, y)
            {
                var r = confirm("Se creara la SEMANA  " + (parseFloat(sem) + 1) + " ,Desea continuar?");
                if (r == true) {
                    $.post("actions_asignacion_puestos.php", {sem: sem, ger: ger, div: div, op: 3, y: y},
                    function (dat) {
                        if (dat != 'error')
                        {
                            window.location = 'Form_asignacion_puestos.php?id=' + (parseFloat(sem) + 1) + '&dv=' + div + '&gr=' + ger+ '&year=' + y;
                        } else {
                            alert(dat);
                        }
                    })
                } else {
                    cancelar();
                }
            }


            function changeTurno(aptid, id)
            {
                doc1 = document.getElementById('id1-' + id);
                doc2 = document.getElementById('id2-' + id);
                emp1 = document.getElementById('id1' + id);
                emp2 = document.getElementById('id2' + id);
                $.post("actions_asignacion_puestos.php", {aptid: aptid, act: 0, op: '4'},
                function (dat) {
                    if (dat != 'error')
                    {
                        dt = dat.split('*')
                        doc1.value = dt[0]
                        doc2.value = dt[1]
                        emp1.innerHTML = dt[2]
                        emp2.innerHTML = dt[3]
                    } else {
                        alert(dat)
                    }
                })
            }

            function changeTurno2(ep1, ep2, sem, idt1, idt2)
            {
                y =<?php echo $yr ?>;
                var fields = (
                        'año=' + '<?php echo $yr ?>' + '&' +
                        'semana=' + '<?php echo $sem ?>' + '&' +
                        'division=' + '<?php echo $div ?>' + '&' +
                        'codigo1=' + ep1 + '&' +
                        'codigo2=' + ep2 + '& &'
                        );
                $.post("actions_asignacion_puestos.php", {emp1: ep1, emp2: ep2, sem: sem, act: 0, op: '5', y: y, fields: fields},
                function (dat) {
                    doc1 = document.getElementById(idt1)
                    doc2 = document.getElementById(idt2)
                    doc1.value = ep2
                    doc2.value = ep1
                    emp1.value = '';
                    emp2.value = '';
                    ident1.value = '';
                    ident2.value = '';
                })
            }

            function loadData(id)
            {
                doc = document.getElementById(id);
                if (emp1.value == '')
                {
                    emp1.value = doc.value;
                    doc.style.borderColor = 'blue'
                    ident1.value = id;
                } else {
                    if (emp2.value == '')
                    {
                        emp2.value = doc.value;
                        ident2.value = id;
                        doc.style.borderColor = 'blue'
                        ant = id
                    } else {
                        ante = document.getElementById(ant)
                        ant = id
                        ante.style.borderColor = '#000'
                        emp2.value = doc.value;
                        ident2.value = id;
                        doc.style.borderColor = 'blue'
                    }
                }
            }


            function validate(sem, ger, div, y)
            {
                dia = 0
                $.post("actions_asignacion_puestos.php", {sem: sem, ger: ger, div: div, dia: dia, act: 0, op: 7, y: y},
                function (dat) {
                    if (dat.length < 310)
                    {
//                        alert('No existen registros duplicados');
                        saveAs(sem, ger, div, y);

                    } else {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#con_clientes').show();
                        $('#clientes').html(dat);
                    }
                })
            }

            function findEmployed(emp, sem)
            {
                y =<?php echo $yr ?>;
                $.post("actions_asignacion_puestos.php", {emp1: emp, sem: sem, op: 6, y: y},
                function (dat) {
                    mensaje.innerHTML = dat
                })

            }

            function cancelar() {
                mnu = window.parent.frames[0].document.getElementById('lock_menu');
                mnu.style.visibility = "hidden";
                grid = window.parent.frames[1].document.getElementById('grid');
                grid.style.visibility = "hidden";
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('mainFrame').src = '../Scripts/Lista_asignacion_puestos.php?div_id=' + <?php echo $div ?>;
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 400) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 390) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }
        </script>
        <style>
            input[type=text]{
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <img id="charging" src="../img/load_bar.gif" />    
        <form  autocomplete="off" id="frm_save" lang="0">
            <table id="tbl_form">
                <thead>
                    <tr><th colspan="12" >FORMULARIO DE CONTROL  <font class="cerrar"  onclick="cancelar()" title="Salir del Formulario">&#X00d7;</font></th></tr>
                    <tr>
                        <td colspan="12">
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th id="mensaje"></td>                    
                                        <th> <input type="button" name="btn" value="Guardar" onclick="validate(sem.value, '<?php echo $ger ?>', '<?php echo $div ?>', '<?php echo $yr ?>')"/></th>                    
                                        <th> Semana:<input type="text" readonly style="background:#ffffff" value="<?php echo $sem ?>" id="sem" size="5" maxlength="2" required />
                                            <input hidden type="text"  value="<?php echo $ger ?>" name="ger"/>
                                            <input hidden type="text"  value="<?php echo $div ?>" name="div"/></th>
                                        <th colspan="2" style="width: 200px"></th>
                                        <th>Buscar:</th>
                                        <th ><input  id="emp10" type="text" placeholder="codigo" size="5"/></th>
                                        <th><input type="button" value="Buscar" onclick="findEmployed(emp10.value, sem.value)"  /></th>
                                        <th>Duplicados:</th>
                                        <th hidden id="err2">&nbsp;</th>
                                        <th ><input readonly id="err" type="text" size="5" style="background:#ffffff"/></th>
                                        <th><input readonly id="emp1" type="text" size="5" style="background:#ffffff"/></th>
                                        <th><input readonly id="emp2" type="text" size="5" style="background:#ffffff"/></th>
                                        <th hidden ><input  id="ident1" type="text" size="5" /></th>
                                        <th hidden><input  id="ident2" type="text" size="5"/></th>
                                        <th>
                                            <input type="button" value="<->" title="Cambiar de Puesto" onclick="changeTurno2(emp1.value, emp2.value, sem.value, ident1.value, ident2.value)"  />
                                            <input type="button" value="<-" title="Limpiar" onclick="emp1.value = '', emp2.value = '', ident1.value = '', ident2.value = '';
                                                    dc = document.getElementsByTagName('input');
                                                    dc.style.borderColor = '#000'"  />
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                    </tr>


                    <tr>
                        <td colspan="12">
                            <table id="tbl" style="width: 100%">
                                <thead>

                                    <tr>
                                        <th colspan="4">&nbsp;</th>
                                        <th colspan="4">Turno 1</th>
                                        <th colspan="3">Turno 2</th>
                                        <th colspan="4">&nbsp;</th>
                                    </tr>        
                                    <tr>
                                        <th>No</th>
                                        <th>Seccion</th>
                                        <th>Funcion</th>
                                        <th>Responsabilidad</th>
                                        <th>Almuerzo</th>
                                        <th>Numero</th>
                                        <th width="150px">Empleado</th>
                                        <th>Codigo</th>
                                        <th>Numero</th>
                                        <th width="150px">Empleado</th>
                                        <th>Codigo</th>
                                        <th>Cambiar</th>
                                    </tr>        
                                </thead>   
                                <tbody>
                                    <?php
                                    $ptid = 0;
                                    $nm1 = 1000;
                                    $nm2 = 2000;
                                    $cn = 0;
                                    while ($rst = pg_fetch_array($cnsPuesto)) {
                                        $nm1++;
                                        $nm2++;
                                        $ptid++;
                                        if ($rst['pt_turno1'] == 1 || $rst['pt_turno1'] == 2 || $rst['pt_turno1'] == 3 || $rst['pt_turno1'] == 5 || $rst['pt_turno1'] == 8) {
                                            $cod1 = $rst['pt_codigo'];
                                        } else {
                                            $cod1 = '';
                                        }
                                        if ($rst['pt_turno2'] == 4 || $rst['pt_turno2'] == 6) {
                                            $cod2 = $rst['pt_codigo2'];
                                            $readonly = "";
                                        } else {
                                            $cod2 = '';
                                            $readonly = "readonly hidden";
                                        }
                                        if ($rst['pt_turno3'] == 7) {
                                            $cod3 = $rst['pt_codigo3'];
                                        } else {
                                            $cod3 = '';
                                        }
                                        $rstAsgpt = pg_fetch_array($Puestos->listAsgPuestoTrabajo($rst[pt_id], $sem, 0, $yr));
                                        if ($rstAsgpt[asg_pt_id] != '') {

                                            $rstEmp1 = pg_fetch_array($Emp->lista_un_empleado($rstAsgpt[emp_id1]));
                                            $rstEmp2 = pg_fetch_array($Emp->lista_un_empleado($rstAsgpt[emp_id2]));
                                            $empcod1 = $rstEmp1[emp_codigo];
                                            $empcod2 = $rstEmp2[emp_codigo];
                                            if ($rstEmp1 == '') {
                                                $empName1 = '';
                                            } else {
                                                $empName1 = $rstEmp1[emp_apellido_paterno] . " " . $rstEmp1[emp_apellido_materno] . " " . $rstEmp1[emp_nombres];
                                            }

                                            if ($rstEmp2 == '') {
                                                $empName2 = '';
                                            } else {
                                                $empName2 = $rstEmp2[emp_apellido_paterno] . " " . $rstEmp2[emp_apellido_materno] . " " . $rstEmp2[emp_nombres];
                                            }
                                        } else {
                                            $empcod1 = '';
                                            $empcod2 = '';
                                            $empName1 = '';
                                            $empName2 = '';
                                        }
                                        if ($rst[pt_almuerzo] == 't') {
                                            $alm = 'SI';
                                        } else {
                                            $alm = 'NO';
                                        }
                                        if ($rst[pt_estado] == 't') {
                                            $cn++;
                                            ?>    
                                            <tr>
                                                <td align='left' id='#'><?php echo $cn ?></td>
                                                <td hidden ><input type="text" name="<?php echo $rst[pt_id] ?>"  value="<?php echo $rst[pt_id] ?>"></input></td>
                                                <td id='seccion<?php echo $rst[pt_id] ?>' align='left'><?php echo $rst[sec_nombre] ?></td>
                                                <td id='cargo<?php echo $rst[pt_id] ?>' align='left'><?php echo $rst[pt_cargo] ?></td>
                                                <td id='responsabilidad<?php echo $rst[pt_id] ?>' align='left'><?php echo $rst[pt_responsabilidad] ?></td>    
                                                <td id='alrmuerza<?php echo $rst[pt_id] ?>' align='center'><?php echo $alm ?></td>    
                                                <td id='t1_numero<?php echo $rst[pt_id] ?>' align='center'><input  id="<?php echo 'id1-' . $rst[pt_id] ?>" title="<?php echo 'id1-' . $rst[pt_id] ?>" onchange="save(this.id, this.value,<?php echo $sem ?>, '<?php echo $ger ?>', '<?php echo $div ?>', '<?php echo $yr ?>')"  type="text" value="<?php echo $empcod1 ?>"  size="5" name='<?php echo $nm1 ?>' ondblclick="loadData(this.id)" /></td>
                                                <td align='left' id="<?php echo "id1$rst[pt_id]" ?>"><?php echo $empName1 ?></td>    
                                                <td id='t1_codigo<?php echo $rst[pt_id] ?>' align='left'><?php echo $cod1 ?></td>
                                                <td id='t2_numero<?php echo $rst[pt_id] ?>' align='center'><input <?php echo $readonly ?> id="<?php echo 'id2-' . $rst[pt_id] ?>" onchange="save(this.id, this.value,<?php echo $sem ?>, '<?php echo $ger ?>', '<?php echo $div ?>', '<?php echo $yr ?>')"  type="text" value="<?php echo $empcod2 ?>"  size="5" name='<?php echo $nm2 ?>' ondblclick="loadData(this.id)" /></td>                        
                                                <td align='left'id="<?php echo "id2$rst[pt_id]" ?>"><?php echo $empName2 ?></td>    
                                                <td id='t2_codigo<?php echo $rst[pt_id] ?>' align='left'><?php echo $cod2 ?></td>
                                                <td><input <?php echo $readonly ?> type="button" onclick="changeTurno(<?php echo $rstAsgpt[asg_pt_id] ?>,<?php echo $rst[pt_id] ?>)" value="<->" id=""/></td>                                
                                            </tr>    
                                            <?php
                                        }
                                        $d = $rstS1[sec_id];
                                    } //Inicio del cliclo
                                    ?>
                                <input type="hidden" name="nm1" value="<?php echo $ptid ?>">
                                </tbody>

                            </table>

                            </body>
                            </html>

