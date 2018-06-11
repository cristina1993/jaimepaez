<?php
require '../Includes/permisos.php';
include_once("../Clases/clsAuditoria.php");
$Audit = new Auditoria();
$cnsUser = $Audit->lista_usuarios();
if (isset($_GET[search])) {
    $txt = trim($_GET[txt]);
    $accion = substr($_GET[accion], 1);
    $usuario = $_GET[usuId];
    $from = $_GET['from'];
    $until = $_GET['until'];
} else {

    $txt = '';
    $accion = '0';
    $usuario = '0';
    $from = date('Y-m-d');
    $until = date('Y-m-d');
}

if (empty($txt)) {
    if ($accion == '0' && $usuario == '0') {
        $cnsAudit = $Audit->listaAuditoriaFecha($from, $until);
    } elseif ($accion != '0' && $usuario == '0') {
        $cnsAudit = $Audit->listaAuditoriaFechaAccion($from, $until, $accion);
    } elseif ($accion == '0' && $usuario != '0') {
        $cnsAudit = $Audit->listaAuditoriaFechaUsuario($from, $until, $usuario);
    } else {
        $cnsAudit = $Audit->listaAuditoriaFechaUsuarioAccion($from, $until, $usuario, $accion);
    }
} else {
    $cnsAudit = $Audit->listaAuditDoc($txt);
}
?>
<html>
    <head>
        <meta charset=utf-8 />
        <title>SCP-Auditoria</title>
        <script type="text/javascript">
            $(function () {
                $("#tbl").tablesorter(
                        {widgets: ['stickyHeaders'],
                            sortMultiSortKey: 'altKey',
                            widthFixed: true});
                parent.document.getElementById('bottomFrame').src = '';
                parent.document.getElementById('contenedor2').rows = "*,0%";
                Calendar.setup({inputField: "from", ifFormat: "%Y-%m-%d", button: "im-desde"});
                Calendar.setup({inputField: "until", ifFormat: "%Y-%m-%d", button: "im-hasta"});
                $('#con_clientes').hide();
                posicion_aux_window();
            });

            function imprimir()
            {
                menu.hidden = true
                buscador.hidden = true
                window.print()
                menu.hidden = false
                buscador.hidden = false
            }

            function posicion_aux_window() {
                var wndW = $(window).width();
                var wndH = $(window).height();
                var obj = $("#con_clientes");
                var objtx = $("#txt_salir");
                obj.css('top', (wndH - 250) / 2);
                obj.css('left', (wndW - 400) / 2);
                objtx.css('top', (wndH - 230) / 2);
                objtx.css('left', (wndW + 320) / 2);
            }


            function load_campo(id) {
                $.post("actions_auditoria.php", {op: 0, id: id},
                function (dt) {
                    if (dt != '') {
                        $('#con_clientes').css('visibility', 'visible');
                        $('#clientes').html(dt);
                        $('#con_clientes').show();
                    }
                });
            }

        </script>
        <style> 
            .disabled{color:#5b74a8}
            #mn3{
                background:black;
                color:white;
                border: solid 1px white;
            }
        </style> 

    </head>
    <body>
        <div id="con_clientes" align="center">
            <font id="txt_salir" onclick="con_clientes.style.visibility = 'hidden'">&#X00d7;</font><br>
            <table id="clientes" border="1" align="center" >
            </table>
        </div>
        <table style="width:100%" id="tbl">
            <caption class="tbl_head" >
                <center class="cont_menu" >
                    <?php
                    $cns_sbm = $User->list_primer_opl($mod_id, $_SESSION[usuid]);
                    while ($rst_sbm = pg_fetch_array($cns_sbm)) {
                        ?>
                        <font class="sbmnu" id="<?php echo "mn" . $rst_sbm[opl_id] ?>" onclick="window.location = '<?php echo "../" . $rst_sbm[opl_direccion] . ".php" ?>'" ><?php echo $rst_sbm[opl_modulo] ?></font>
                        <?php
                    }
                    ?>
                    <img class="auxBtn" style="float:right" onclick="window.print()" title="Imprimir Documento"  src="../img/print_iconop.png" width="16px" />                            
                </center>
                <center class="cont_title" >AUDITORIA</center>
                <center class="cont_finder">
                    <?php
                    if ($Prt->add == 0) {
                        ?>
                        <!--<a href="#" class="btn" style="float:left;margin-top:7px;padding:7px;" title="Nuevo Registro" onclick="auxWindow(0)" >Nuevo</a>-->
                        <?php
                    }
                    ?>
                    <form method="GET" id="frmSearch" name="frm1" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
                        Desde<input type="text" id="from" name="from" value="<?php echo $from ?>" size="10" />
                        <img src="../img/calendar.png" width="16" height="16" border="0" title="Fecha Inicial"  id="im-desde">
                        Hasta<input type="text" id="until" name="until" value="<?php echo $until ?>" size="10" />
                        <img src="../img/calendar.png" width="16" height="16" border="0" title="Fecha Final"  id="im-hasta">
                        <select name="usuId" id="usuId">
                            <option value="0">Seleccione Usuario </option>
                            <?php
                            while ($rstUser = pg_fetch_array($cnsUser)) {
                                if ($rstUser[usu_id] == $_GET['usuId']) {
                                    echo "<option selected value='$rstUser[usu_id]' >$rstUser[usu_person]</option>";
                                } else {
                                    echo "<option value='$rstUser[usu_login]' >$rstUser[usu_person]</option>";
                                }
                            }
                            ?>
                        </select>
                        <select name="accion">
                            <option value="0">Accion</option>
                            <option value="INSERTAR">Insertar</option>
                            <option value="MODIFICAR">Modificar</option>
                            <option value="ELIMINAR">Eliminar</option>
                            <option value="LOGIN">Login</option>
                        </select>
                        <input type="text" id="txt" name="txt" placeholder="Documento" />
                        <input type="submit" id="search" name="search" value="Buscar" size="5" />
                    </form>  
                </center>
            </caption>
            <thead>
            <th>No</th>
            <th  align="Center">Usuario</th>
            <th  align="Center">Fecha</th>
            <th  align="Center">Hora</th>
            <th  align="Center">Modulo</th>
            <th  align="Center">Accion</th>
            <th  align="Center" >Documento</th>
            <th  align="Center">Campo</th>
            <th  align="Center">V-Inicial</th>
            <th  align="Center">V-Final</th>
            <th  align="Center">IP</th>
        </thead>
        <tbody>
            <?php
            $n = 0;
            while ($rstAudit = pg_fetch_array($cnsAudit)) {
                $n++;
                if ($rstAudit['adt_campo']!='') {
                    $detalle = 'DETALLE';
                } else {
                    $detalle = '';
                }
                ?>
                <tr>
                    <td><?php echo $n ?></td>
                    <td align="left" ><?PHP echo $rstAudit['usu_login']; ?></td>            
                    <td align="left" ><?PHP echo $rstAudit['adt_date']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_hour']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_modulo']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_accion']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_documento']; ?></td>                      

                    <td align="left" style="color:darkred;font-weight:bolder" ondblclick="load_campo(<?PHP echo $rstAudit['adt_id'] ?>)"><?PHP echo $detalle ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_vi']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_vf']; ?></td>                      
                    <td align="left" ><?PHP echo $rstAudit['adt_ip']; ?></td>                                
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>
<p id="back-top" style="display: block;">
    <a href="#" >&#9650;Inicio</a>
</p>
</html>    

