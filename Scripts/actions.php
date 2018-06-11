<?php
session_start();
include_once '../Clases/clsClase_pagos.php';
include_once '../Clases/clsSetting.php';
include_once '../Clases/clsUsers.php';
include_once("../Clases/clsAuditoria.php");
include_once '../Clases/clsClase_preciospt.php';
$Clase_pagos = new Clase_pagos();
$Clases_preciospt = new Clase_preciospt();

$dias = date("d", mktime(0, 0, 0, (date('m') + 1), 0, date('Y')));
$from = date('Y-m-') . '01';
$until = date('Y-m-d', strtotime($from . ' + ' . ($dias - 1) . ' days'));

$prod = 0;
$act = $_REQUEST[act]; //Accion
$id = $_REQUEST[id]; //Id
//$tp = $_REQUEST[tipo_pago];///// cambio OMAR
$field = $_REQUEST[field]; //Field Name
$data = $_REQUEST[data]; //Data
$data2 = $_REQUEST[data2]; //Data
$data4 = $_REQUEST[data3]; //Data
$data5 = $_REQUEST[data4];
$old = $_REQUEST[old]; //Field Name old
$tbl = $_REQUEST[tbl]; //tbl
$s = $_REQUEST[s]; //tbl
$sts = $_REQUEST[sts];
$user = $_SESSION[usuid];
$fields = $_REQUEST[fields]; //Field Name

$Set = new Set();
$Adt = new Auditoria();
//0 add field
//1 upd  field
//2 del  field
//3 add upd  record
//4 del  record
//5 insert  data
switch ($tbl) {
    case 'erp_mp_set':
        $prf = 'mp_';
        break;
}

switch ($act) {
    //STRUCTURES
    case 0:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->addField_prod($prf . $field, $tbl) == FALSE) {

                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $field));
            }
        } else {
            if ($Set->addField($prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $field));
            }
        }
        echo $sms;
        break;
    case 1:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->updField_prod($old, $prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', substr($old, 4) . '==>' . $field));
            }
        } else {
            if ($Set->updField($old, $prf . $field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', substr($old, 4) . '==>' . $field));
            }
        }
        echo $sms;
        break;
    case 2:
        $sms = 0;
        if ($prod == 1) {
            if ($Set->delField_prod($field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Eliminar', substr($field, 4)));
            }
        } else {
            if ($Set->delField($field, $tbl) == FALSE) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Eliminar', substr($field, 4)));
            }
        }
        echo $sms;
        break;
    //RECORDS
    case 3:
        $sms = 0;
        if (empty($id)) {
            if ($Set->insert($data, $field, $tbl) == false) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Insertar', $data[0]));
            }
        } else {
            if ($Set->update($data, $field, $tbl, $id, $s) == false) {
                $sms = pg_last_error();
            } else {
                $Adt->insert(array(substr($tbl, 4), 'Modificar', $data[0]));
            }
        }

        echo $sms;
        break;
    case 4:
        $sms = 0;
        $rst_set = pg_fetch_array($Set->lista_by_tipo("erp_mp_set where ids=$id"));
        $op = $rst_set[mp_tipo];
        $dat = explode('&', $op);
        $m = trim($dat[9]);
        $mod = pg_fetch_array($Set->lista_by_tipo("erp_modulos where mod_descripcion='$m' and proc_id=1"));
        $opl = pg_fetch_array($Set->lista_by_tipo("erp_option_list where mod_id='$mod[mod_id]'"));
        if ($Set->del_asig_list($opl[opl_id]) == false) {
            $sms = pg_last_error();
        } else {
            if ($Set->del_option_list($mod[mod_id]) == false) {
                $sms = pg_last_error();
            } else {
                if ($Set->del_modulos($mod[mod_id]) == false) {
                    $sms = pg_last_error();
                }
            }
        }
        if ($Set->del($tbl, $id) == false) {
            $sms = pg_last_error();
        } else {
            $Adt->insert(array(substr($tbl, 4), 'Eliminar', $data[0]));
        }
        echo $sms;
        break;
    case 5:
        $sms = 0;
        if (empty($id)) {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $dat) {
                $str[$n] = $dat;
                $n++;
            }
            if ($Set->insert($str, $field, $tbl) == false) {
                $sms = pg_last_error();
                $dat = explode(':', $sms);
                if (trim(substr($dat[2], 1, 11)) == 'Ya existe' || trim(substr($dat[2], 25, 14)) == 'already exists') {
                    $sms = 'Ya existe';
                }
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'MATERIA PRIMA/PRODUCTOS/OTROS';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            $str = $data;
            $n = 0;
            foreach ($str as $row => $dat) {
                $str[$n] = $dat;
                $n++;
            }
            if ($Set->update($str, $field, $tbl, $id, $s) == false) {
                $sms = pg_last_error();
                $dat = explode(':', $sms);
                if (trim(substr($dat[2], 1, 11)) == 'Ya existe' || trim(substr($dat[2], 25, 14)) == 'already exists') {
                    $sms = 'Ya existe';
                }
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'MATERIA PRIMA/PRODUCTOS/OTROS';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }


        if ($prf == 'ped_') {
            $rstPed = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $id));
            if ($rstPed[ped_f] == 6 || $rstPed[ped_f] == 1) {

                $Set->cambia_status($rstPed[0], 0);
            }
        }

        if ($prf == 'reg') {
            $rstPed = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $data[0]));
            $sol = $rstPed[ped_e1] + $rstPed[ped_e2] + $rstPed[ped_e3] + $rstPed[ped_e4];
            $do0 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 0));
            $do1 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 1));
            $do2 = pg_fetch_array($Set->list_produccion_pedido_tipo($data[0], 2));
            $do = ($do0[sum] + $do1[sum] + $do2[sum]) / 3;
            if ($rstPed[ped_f] == 1) {
                $Set->cambia_status($rstPed[0], 2);
            }
            if ($do >= $sol) {
                $Set->cambia_status($rstPed[0], 3);
            }
        }

        echo $sms;
        break;
    case 6:
        $cod = $_REQUEST[cod];
        $sms = 0;
        if ($Set->del_data($tbl, $id) == false) {
            $sms = pg_last_error();
        } else {
            $n = 0;
            $f = $cod;
            $modulo = 'MATERIA PRIMA';
            $accion = 'ELIMINAR';
            if ($Adt->insert_audit_general($modulo, $accion, '', $f) == false) {
                $sms = "Auditoria" . pg_last_error() . 'ok2';
            }
        }
        echo $sms;
        break;
    case 7:
        $tbl.='_set';
        $cnsCampos = $Set->listar($tbl, 's');
        while ($rstCampos = pg_fetch_array($cnsCampos)) {
            $val = explode('&', $rstCampos[$prf . 'tipo']);
            echo "<option value='$rstCampos[ids]'>$val[9]</option>";
        }
        break;
    case 8:
        $sms = 0;
        $User = new User();
        if ($id == 0) {
            if ($User->insertaUsuarios($data) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'USUARIO';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {
            if ($User->modificaUsuario($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'USUARIO';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[0]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        echo $sms;
        break;
    case 9:
        $sms = 0;
        $User = new User();
        if ($User->modificaEstado($data, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 10:
        $data = pg_fetch_array($Set->list_one_data_by_id('erp_productos', $id));
        $data2 = pg_fetch_array($Set->list_one_data_by_id('erp_pedidos', $_REQUEST[id2]));
        $files = pg_fetch_array($Set->lista_one_data('erp_productos_set', $data[ids]));
        ?>
        <tr>
            <td colspan="2" style="background:#f8f8f8;font-weight:bolder; " ></td>
            <?php
            $n = 2;
            while ($n <= count($files)) {
                $file = explode('&', $files[$n]);
                if ($file[0] == 'T' && !empty($file[9])) {
                    ?>
                    <td style="background:#f8f8f8;font-weight:bolder;font-size:12px;"><?php echo $file[9] ?></td>
                    <?php
                }
                $n++;
            }
            ?>            
        </tr>
        <tr>
            <td colspan="2" style="background:#f8f8f8;font-weight:bolder; " align="right" >Solicitado:</td>
            <td><input type="text" class='elemento' id="ped_e1" size="2" onchange="calculos(1, this.value)" value="<?php echo $data2[ped_e1] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e2" size="2" onchange="calculos(2, this.value)" value="<?php echo $data2[ped_e2] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e3" size="2" onchange="calculos(3, this.value)" value="<?php echo $data2[ped_e3] ?>" /></td>
            <td><input type="text" class='elemento' id="ped_e4" size="2" onchange="calculos(4, this.value)" value="<?php echo $data2[ped_e4] ?>" /></td>
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

                if ($_REQUEST[op] == 0) {
                    $val = $data[$file[8]];
                } else {
                    $val = $data2[$file[8]];
                }

                switch ($file[2]) {
                    case 'I':
                        $input = "<input class='elemento' type='file' lang='$file[5]' id='$file[8]'  size='$file[1]' onchange='archivo(event,this.id)' />
                                                            <img src='$val' width='128px' id='im$file[8]'/> ";
                        break;
                    case 'N':
                        $input = "<input class='elemento' id='$file[8]' lang='$file[5]'  size='$file[1]' type='text' value='$val'  onkeyup='this.value=this.value.replace (/[^0-9.]/," . '""' . " )'  />";
                        break;
                    case 'C':
                        $input = "<input readonly class='elemento' id='$file[8]' lang='$file[5]' size='$file[1]' type='text'  value='$val'  />";
                        break;
                    case 'F':
                        $input = "<input class='elemento' type='text' lang='$file[5]' id='$file[8]'  size='10' value='$val' onblur='val_fecha(this.id)' placeholder='dd/mm/YY' />
                                                              <img id='cal_$file[8]' src='../img/calendar.png' />
                                                              <script>
                                                                  Calendar.setup({inputField:$file[8],ifFormat:'%d/%m/%Y',button:cal_$file[8]});
                                                              </script>
                                                            ";
                        break;
                    case 'E':
                        $cnsEnlace = $Set->listOneById($file[7], $file[6]);

                        $input = "<select class='elemento' style='width:150px' name='mat0' title='$file[7]' lang='$file[5]' id='$file[8]' onchange='calc_telas()'  >";
                        $input.="<option value='0'>Ninguno</option>";
                        while ($rstEnlace = pg_fetch_array($cnsEnlace)) {
                            $selected = '';
                            if ($rstEnlace[id] == $val) {
                                $selected = 'selected';
                            }
                            $input.="<option  $selected value='$rstEnlace[id]'>$rstEnlace[ins_a]==>$rstEnlace[ins_b]</option>";
                        }
                        $input.="</select>";
                        break;
                }

                if ($n == 2) {
                    $val1 = $data[$file[8] . '1'];
                    $val2 = $data[$file[8] . '2'];
                    $val3 = $data[$file[8] . '3'];
                    $val4 = $data[$file[8] . '4'];
                } else {
                    $val1 = $data2[$file[8] . '1'];
                    $val2 = $data2[$file[8] . '2'];
                    $val3 = $data2[$file[8] . '3'];
                    $val4 = $data2[$file[8] . '4'];
                }
                ?>

                <tr>
                    <td><?php echo $file[9] ?></td>
                    <td><?php echo $input ?></td>
                    <td>
                        <input readonly type="hidden" lang="1"  name="aux_mat1" size="2" value="<?php echo $data[$file[8] . '1'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1"  id="<?php echo $file[8] . '1' ?>" name="mat1" size="2" value="<?php echo $val1 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat2" size="2" value="<?php echo $data[$file[8] . '2'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '2' ?>" name="mat2" size="2" value="<?php echo $val2 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat3" size="2" value="<?php echo $data[$file[8] . '3'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '3' ?>" name="mat3" size="2" value="<?php echo $val3 ?>" />
                    </td>
                    <td>
                        <input readonly type="hidden" lang="1" name="aux_mat4" size="2" value="<?php echo $data[$file[8] . '4'] ?>" />
                        <input class='elemento' style="text-align:right" readonly type="text" lang="1" id="<?php echo $file[8] . '4' ?>" name="mat4" size="2" value="<?php echo $val4 ?>" />
                    </td>
                </tr>                                            
                <?php
            }
            $n++;
        }
        ?>
        <?php
        break;
    case 11:
        $sms = 0;
        if ($Set->cambia_status($id, $sts) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 12:
        $sms = 0;
        if ($id == 0) {
            if ($Set->insert_movimiento($data) == false) {
                $sms = pg_last_error();
            }
        } else {

            if ($Set->update_movimiento($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 13:
        $rst = pg_fetch_array($Set->list_one_data_by_id("erp_insumos", $id));
        echo $rst[ins_a] . '&' . $rst[ins_b] . '&' . $_REQUEST[sec];
        break;

    case 14:
        $sms = 0;
        if ($Set->delete_all("erp_registros") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_registros_produccion") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_mov_inventario") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_pedidos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_clientes") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_insumos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_maquinas") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_productos") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_auditoria") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_sn1") == false) {
            echo $sms = pg_last_error();
        }
        if ($Set->delete_all("erp_sn3") == false) {
            echo $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 15:
        $sms = 0;
        if ($Set->del_data_by_pedido("erp_registros_produccion", $id) == true && $Set->del_data_by_pedido2("erp_mov_inventario", $id) == true && $Set->del_data("erp_pedidos", $id) == true) {
            $Adt->insert(array(substr('erp_pedidos', 4), 'Eliminar', $data[0]));
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 16:
        $sms = 0;
        if ($data[12] == 0) {
            $rst_dupl = pg_fetch_array($Set->lista_movimiento_codigo($data[2]));

            if (!empty($rst_dupl)) {
                $sms = "Documento ya existe";
            } else {

                if ($Set->inser_inventarios($data) == false) {
                    $sms = pg_last_error();
                }
            }
        } else {
            if ($Set->inser_inventarios($data) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 17:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_tpmp($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_tpmp($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 18:
        $sms = 0;
        if ($Set->del_tpmp($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 19:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_mp($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_mp($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 20:
        $sms = 0;
        if ($Set->del_mp($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 21:

        $rst_mp = pg_fetch_array($Set->lista_productos_industrial());

        $rst_code = pg_fetch_array($Set->lista_codigo_mp($_REQUEST[tp]));
        $rst_tp = pg_fetch_array($Set->lista_un_tpmp($_REQUEST[tp]));

        $cod = substr($rst_code[mp_codigo], -4);
        $code = ($cod + 1);
        if ($code >= 0 && $code < 10) {
            $txt = '000';
        } elseif ($code >= 10 && $code < 100) {
            $txt = '00';
        } elseif ($code >= 100 && $code < 1000) {
            $txt = '0';
        } elseif ($code >= 1000 && $code < 10000) {
            $txt = '';
        }

        echo $rst_tp[mpt_siglas] . $txt . $code;
        break;
    case 22:
        $sms = 0;
        $obs = '';
        if ($Set->upd_factura_orden_compra($data[11], $data[2]) == true) {
            if ($Set->insert_inv_mp($data) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        $rst_ord = pg_fetch_array($Set->lista_total_orden_compra_code($data[2]));
        $rst_mov = pg_fetch_array($Set->lista_inv_mp_doc_total($data[2], 0));
        if ($rst_ord[sum] > $rst_mov[peso]) {
            $sts = 4;
        } elseif ($rst_ord[sum] <= $rst_mov[peso]) {
            $sts = 5;
        }
        if ($Set->upd_orden_compra_estado($sts, $obs, $rst_ord[orc_id]) == false) {
            $sms = 'upd_sts' . pg_last_error();
        }
        echo $sms;
        break;
    case 222:
        $sms = 0;
        $obs = '';
        if ($Set->insert_inv_mp($data) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 225:
        $sms = 0;
        if ($Set->insert_inv_mp($data) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 23:
        $sms = 0;
        if ($Set->del_mov($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 24:
        $sms = 0;
        if ($Set->del_mov_code($_REQUEST[nm_trs]) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

//Pedido MP
    case 25:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_pmp($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_pmp($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
//caso la descripcion del producto
    case 26:
        $rst_mp = pg_fetch_array($Set->lista_des_mp($_REQUEST[mp]));
        $rst_inv_ing = pg_fetch_array($Set->lista_inv_mp($_REQUEST[mp], 0));
        $rst_inv_egr = pg_fetch_array($Set->lista_inv_mp($_REQUEST[mp], 1));
        $invp = number_format($rst_inv_ing[peso] - $rst_inv_egr[peso], 1);
        $invu = number_format($rst_inv_ing[unidad] - $rst_inv_egr[unidad], 1);
        $invpu = ($rst_inv_ing[peso] - $rst_inv_egr[peso]) / ($rst_inv_ing[unidad] - $rst_inv_egr[unidad]);
        echo $rst_mp[mp_codigo] . "&" . $invp . "&" . $rst_mp[mp_unidad] . "&" . $rst_mp[mp_presentacion] . "&" . $invu . "&" . $invpu;
        break;
    case 27:
        $sms = 0;
        if ($Set->del_pmp($id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 28:
        $rst_trs = pg_fetch_array($Set->lista_una_transaccion($id));
        $rst_sec = pg_fetch_array($Set->lista_secuencia_transaccion($rst_trs[trs_operacion]));
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

        if ($rst_trs[trs_operacion] == 0) {
            $txt0 = "000";
        } else {
            $txt0 = "100";
        }

        echo $no_trs = $txt0 . '-' . $tx_trs . $sec;
        break;
//Orden Compra Mp
    case 29:
        $sms = 0;
        $rst = $Set->lista_orden_compra_code($id);
        if (pg_num_rows($rst) == 0) {//Insertar
            if ($Set->insert_orden_compra($data) == false) {
                $sms = 'Insert' . pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_orden_compra($data, $id) == false) {
                $sms = 'Upd' . pg_last_error();
            }
        }
        echo $sms;
        break;
//Detalle Orden Compra Mp
    case 30:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_det_orden_compra($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_det_orden_compra($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        //***Reviso cupos e historial > 0
        if ($s == 1) {
            $sts = 2;
            $obs = 'Sin Orden';

            $rst = pg_fetch_array($Set->lista_una_orden_compra($data[0]));
            $rst_mp = pg_fetch_array($Set->lista_una_orden_compra($data[1]));

            $data1 = array(3,
                $data[1],
                $rst[orc_codigo],
                $rst[orc_documento],
                $rst[orc_fecha],
                $data[2],
                $rst[mp_presentacion],
                $data[2],
                $rst[cli_id],
                1,
                '',
                $data[5],);
            if ($Set->insert_inv_mp($data1) == false) {
                $sms = 'Sin orden' . pg_last_error();
            }
        } else {
            $rst_cupos = pg_fetch_array($Set->lista_un_cupo_usuid($user));
            $rst_total_orden = pg_fetch_array($Set->lista_una_orden_total($data[0]));
            $rst_total_mensual = pg_fetch_array($Set->lista_orden_total_mensual_user($user, $from, $until));
            $rst_historial_mp = pg_fetch_array($Set->lista_historial_orden_mp($data[1]));
            $dif = abs($rst_historial_mp[orc_det_vu] - $data[3]);
            $por = ($dif * 100 / $rst_historial_mp[orc_det_vu]);

            if (!empty($rst_cupos) && ($rst_cupos[cup_xorden] < $rst_total_orden[sum])) { //Si supera su cupo por orden
                $sts = 1;
                $obs = 'Supera Cupo Por Orden';
            } elseif (!empty($rst_cupos) && ($rst_cupos[cup_mensual] < $rst_total_mensual[sum])) {
                $sts = 1;
                $obs = 'Supera Cupo Mensual';
            } elseif (!empty($rst_historial_mp) && $rst_historial_mp[orc_det_vu] != $data[3] && $por > 5) {
                $sts = 1;
                $obs = 'Valor Unitario Supera Rango de Tolerancia';
            } else {
                $sts = 2;
                $obs = '';
            }
        }

        if ($Set->upd_orden_compra_estado($sts, $obs, $data[0]) == FALSE) {
            $sms = pg_last_error();
        }

        echo $sms;
        break;
    case 31:
        $sms = 0;
        $rst = pg_fetch_array($Set->lista_una_det_orden_compra($id));


        if ($Set->del_det_orden_compra($id) == true) {
            if ($Set->del_mov_doc_mp($rst[orc_codigo], $rst[mp_id]) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 32:
        $sms = 0;
        $rst_ord = pg_fetch_array($Set->lista_una_orden_compra($id));
        if ($Set->del_det_orden_compra_orc($id) == true && $Set->del_mov_doc($rst_ord[orc_codigo]) == true) {
            if ($Set->del_orden_compra($id) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 33:
        $n = 0;
        $sms = 0;
        if (!empty($_REQUEST[doc])) {
            while ($n < count($data)) {
                $dat = explode('%', $data[$n]);
                $rst_mov = pg_fetch_array($Set->lista_un_movimiento_mp($dat[0]));
                $dat[4] = $rst_mov[mp_codigo] . 'OC00000';
                $dat[5] = 1;
                if ($Set->insert_etq_orden($dat) == false) {
                    //$sms=pg_last_error();
                    print_r($dat);
                    break;
                }
                $n++;
            }
        } else {
            while ($n < count($data)) {
                $dat = explode('%', $data[$n]);
                if ($Set->insert_etq_orden($dat) == false) {
                    $sms = pg_last_error();
                    break;
                }
                $n++;
            }
        }
        echo $sms;
        break;

////////////////////////// Cumplimiento /////////////////////////////////////
    case 34:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_cumplimiento($data) == false) {
                $sms = pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_cumplimiento($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Cumplimiento
    case 35:
        $sms = 0;
        if ($Set->delete_cumplimiento($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Tipo de Pago /////////////////////////////////////
    case 36:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_tipo_de_pago($data) == false) {
                $sms = pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_tipo_de_pago($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Tipo de Pago
    case 37:
        $sms = 0;
        if ($Set->delete_tipo_de_pago($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Capacidad de Compra /////////////////////////////////////
    case 38:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_capacidad_de_compra($data) == false) {
                $sms = pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_capacidad_de_compra($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Capacidad de Compra
    case 39:
        $sms = 0;
        if ($Set->delete_capacidad_de_compra($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////  CLIENTES /////////////////////////////////////
    case 40:
        $sms = 0;
        if ($id == '0') {// Insertar
            if ($Set->insertar_clientes($data) == true) {
                if (count($data2) > 0) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
                    array_push($data2, $rst_cli[cli_id]);
                    if ($Set->insertar_direccion_entrega($data2) == false) {
                        $sms = pg_last_error();
                    }
                }
            } else {
                $sms = "Insert " . pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_clientes($data, $id) == true) {
                if (!empty($_REQUEST[campo])) {
                    if ($Set->insert_cambio_clientes($id, $_REQUEST[cambio], $_REQUEST[campo]) == false) {
                        $sms = 'Cambio' . pg_last_error();
                    }
                }
                if (count($data2) > 0) {
                    $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
                    array_push($data2, $rst_cli[cli_id]);
                    if ($Set->insertar_direccion_entrega($data2) == false) {
                        $sms = pg_last_error();
                    }
                }
            } else {
                $sms = "Editar " . pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar clientes
    case 41:
        $sms = 0;
        $rst_cli = pg_fetch_array($Set->lista_un_cliente($id));
        if ($Set->delete_direccion_entrega($id) == true && $Set->delete_aprobaciones($rst_cli[cli_codigo]) == true) {
            if ($Set->delete_clientes($id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Direccion entrega CLIENTES
    case 42:
        $sms = 0;
        if ($Set->delete_direccion_entrega($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 43:
        $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
        $cod = substr($rst[cli_codigo], -5);
        $code = ($cod + 1);
        if ($code >= 0 && $code < 10) {
            $txt = '0000';
        } elseif ($code >= 10 && $code < 100) {
            $txt = '000';
        } elseif ($code >= 100 && $code < 1000) {
            $txt = '00';
        } elseif ($code >= 1000 && $code < 10000) {
            $txt = '0';
        } elseif ($code >= 10000 && $code < 100000) {
            $txt = '';
        }
        echo $txt . $code;
        break;
    case 44:
        $rst = pg_fetch_array($Set->lista_ultima_orden_compra_producto($id));
        if (empty($rst)) {
            $rst = pg_fetch_array($Set->lista_un_mp_code($id));
        }
        $rst_sec = pg_fetch_array($Set->lista_secuencial_orden());
        $sec = ($rst_sec[orc_codigo] + 1);
        if ($sec >= 0 && $sec < 10) {
            $tx_trs = "0000";
        } elseif ($sec >= 10 && $sec < 100) {
            $tx_trs = "000";
        } elseif ($sec >= 100 && $sec < 1000) {
            $tx_trs = "00";
        } elseif ($sec >= 1000 && $sec < 10000) {
            $tx_trs = "0";
        } elseif ($sec >= 10000 && $sec < 100000) {
            $tx_trs = "";
        }
        echo $rst[mp_codigo] . "&" . $rst[mp_referencia] . "&" . $rst[cli_codigo] . "&" . $rst[emp_descripcion] . "&" . $no_orden = $tx_trs . $sec;
        ;
        break;

    case 45:
        $sms = 0;
//                                emp_descripcion.value,
//                                cli_nombre.value,
//                                orc_codigo.value,
//                                mp_codigo.value,
//                                orc_fecha.value,
//                                orc_det_guia.value,
//                                etq_peso.value,
//                                etq_bar_code.value

        $rst_cli = pg_fetch_array($Set->lista_un_cliente_codigo($data[1]));
        $rst_emp = pg_fetch_array($Set->lista_una_fabrica_desc($data[0]));
//                                            cli_id,
//                                            orc_fecha,
//                                            orc_codigo,
//                                            emp_id,
//                                            orc_descuento,
//                                            orc_flete
        if ($Set->insert_orden_compra(array($rst_cli[cli_id], $data[4], $data[2], $rst_emp[emp_id], 0, 0)) == true) {
            $rst_oc = pg_fetch_array($Set->lista_orden_compra_code($data[2]));
            $rst_mp = pg_fetch_array($Set->lista_un_mp_code($data[3]));
//                                             orc_id,
//                                             mp_id,
//                                             orc_det_cant,
//                                             orc_det_vu,
//                                             orc_det_vt
            if ($Set->insert_det_orden_compra(array($rst_oc[orc_id], $rst_mp[mp_id], 0, 0, 0)) == true) {
                $rst_doc = pg_fetch_array($Set->lista_det_orden_compra_oc_mp($rst_oc[orc_id], $rst_mp[mp_id]));
//                                                orc_det_id,
//                                                etq_cant,
//                                                etq_peso,
//                                                etq_fecha,
//                                                etq_bar_code
                if ($Set->upd_guia_det_orden_compra($data[5], $rst_doc[orc_det_id]) == true) {

                    if ($Set->insert_etq_orden(array($rst_doc[orc_det_id], 1, $data[6], $data[4], $data[7])) == false) {
                        $sms = "Etq" . pg_last_error();
                    }
                } else {
                    $sms = "Upd_Guia" . pg_last_error();
                }
            } else {
                $sms = "Detalle" . pg_last_error();
            }
        } else {
            $sms = "Orden" . pg_last_error();
        }
        echo $sms . "&" . $rst_doc[orc_det_id];
        break;
    case 46:
        $sms = 0;
        if ($Set->upd_aprobaciones($id, $sts) == false) {
            $sms = pg_last_error();
        }

        if ($sts == 1) {
            $rst = pg_fetch_array($Set->lista_una_aprobaciones($id));
            if ($Set->upd_aprobaciones_clientes($rst[cli_id], $rst[abp_campo], $rst[apb_cambio]) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    case 47:

        $sms = 0;
        if ($id == 0) {
            if ($Set->insert_cupo($data) == false) {
                $sms = pg_last_error();
            } else {
                $accion = 'Insertar';
            }
        } else {
            if ($Set->upd_cupo($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $accion = 'Modificar';
            }
        }

//**Auditoria****//
        $fields = str_replace("&", ",", $fields[0]);
        $modulo = 'Cupos';
        $accion = $accion;
        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
            $sms = "Auditoria" . pg_last_error();
        }
//***************//
        echo $sms;
        break;
    case 48:
        $sms = 0;
        if ($Set->del_cupo($id) == false) {
            $sms = pg_last_error();
        }
//**Auditoria****//
        $files_adt = array('Cupos', 'Eliminar', $files, $files);
        $Adt->insert_auditoria($files_adt);
//***************//
        echo $sms;
        break;
    case 49:
        $sms = 0;
        $rst = pg_fetch_array($Set->lista_orden_compra_code($id));
        if (!empty($rst)) {
            $rst_cli = pg_fetch_array($Set->lista_un_cliente($rst[cli_id]));
            $cns = $Set->lista_det_orden_compra($rst[orc_id]);
            $sms = $rst_cli[cli_nombre] . '&';
            while ($rst_mp = pg_fetch_array($cns)) {
                $sms.="<option value='$rst_mp[mp_id]' >$rst_mp[mp_referencia]</option>";
            }
        }
        echo $sms;
        break;
    case 50:
        $sms = 0;
        $obs = '';
        if ($Set->upd_orden_compra_estado($sts, $obs, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 51:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($rst_pro[emp_id]));
        $rst_set = pg_fetch_array($Set->lista_utlimo_seteo_maquina($_REQUEST[id]));
        $cns_mp = $Set->lista_mp($rst_pro[emp_id]);
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }

        $retorno = $rst_pro[pro_ancho] . "&" .
                $rst_pro[pro_mp1] . "&" .
                $rst_pro[pro_mp2] . "&" .
                $rst_pro[pro_mp3] . "&" .
                $rst_pro[pro_mp4] . "&" .
                $rst_pro[pro_mp5] . "&" .
                $rst_pro[pro_mp6] . "&" .
                $rst_pro[pro_mf1] . "&" .
                $rst_pro[pro_mf2] . "&" .
                $rst_pro[pro_mf3] . "&" .
                $rst_pro[pro_mf4] . "&" .
                $rst_pro[pro_mf5] . "&" .
                $rst_pro[pro_mf6] . "&" .
                ($rst_pro[pro_mf1] + $rst_pro[pro_mf2] + $rst_pro[pro_mf3] + $rst_pro[pro_mf4] + $rst_pro[pro_mf5] + $rst_pro[pro_mf6]) . "&" .
                $rst_pro[pro_largo] . "&" .
                $rst_pro[pro_gramaje] . "&" .
                $rst_fbc[emp_sigla] . "&" .
                $rst_set[ord_zo1] . "&" .
                $rst_set[ord_zo2] . "&" .
                $rst_set[ord_zo3] . "&" .
                $rst_set[ord_zo4] . "&" .
                $rst_set[ord_zo5] . "&" .
                $rst_set[ord_zo6] . "&" .
                $rst_set[ord_spi_temp] . "&" .
                $rst_set[ord_upp_rol_tem_controller] . "&" .
                $rst_set[ord_dow_rol_tem_controller] . "&" .
                $rst_set[ord_spi_tem_controller] . "&" .
                $rst_set[ord_coo_air_temp] . "&" .
                $rst_set[ord_upp_rol_heating] . "&" .
                $rst_set[ord_upp_rol_oil_pump] . "&" .
                $rst_set[ord_dow_rol_heating] . "&" .
                $rst_set[ord_dow_rol_oil_pump] . "&" .
                $rst_set[ord_spi_rol_heating] . "&" .
                $rst_set[ord_spi_rol_oil_pump] . "&" .
                $rst_set[ord_mat_pump] . "&" .
                $rst_set[ord_spi_blower] . "&" .
                $rst_set[ord_sid_blower] . "&" .
                $rst_set[ord_dra_blower] . "&" .
                $rst_set[ord_gsm_setting] . "&" .
                $rst_set[ord_aut_spe_adjust] . "&" .
                $rst_set[ord_spe_mod_auto] . "&" .
                $rst_set[ord_lap_speed] . "&" .
                $rst_set[ord_man_spe_setting] . "&" .
                $rst_set[ord_rol_mill] . "&" .
                $rst_set[ord_win_tensility] . "&" .
                $rst_set[ord_mas_bra_autosetting] . "&" .
                $rst_set[ord_rol_mil_up_down] . "&" .
                $combo;

//////////////////////////////////////////////////////////////////////////////////
        echo $retorno;
        break;
    case 52:
        $retorno;
        $cns_pro = $Set->lista_productos_faltantes($_REQUEST[faltante], $_REQUEST[gramaje]);
        while ($rst_pro = pg_fetch_array($cns_pro)) {
            $retorno.= "<option  value='$rst_pro[pro_id]'>$rst_pro[pro_descripcion]</option>";
        }
        echo $retorno;
        break;
    case 53:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $retorno = $rst_pro[pro_ancho];
        echo $retorno;
        break;
////////////////////////// Reporte Produccion /////////////////////////////////////
    case 54:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_reporte_produccion($data) == false) {
                $sms = pg_last_error();
            }
        } else {// Modificar
            if ($Set->modificar_reporte_produccion($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
// Eliminar Reporte Produccion
    case 55:
        $sms = 0;
        if ($Set->delete_reporte_produccion($id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 56:
        $rst_ord = pg_fetch_array($Set->lista_una_orden_produccion_numero_orden($_REQUEST[ord_num_orden]));
        $rst_pro = pg_fetch_array($Set->lista_un_producto($rst_ord[pro_id]));
        if ($rst_ord[ord_pro_secundario] == 0) {
            $rst_pro_secundario[pro_descripcion] = ' NINGUN PRODUCTO ';
        } else {
            $rst_pro_secundario = pg_fetch_array($Set->lista_un_producto($rst_ord[ord_pro_secundario]));
        }
        echo $rst_ord[ord_id] . "&" . $rst_pro[pro_descripcion] . "&" . $rst_pro_secundario[pro_descripcion] . "&" . $rst_ord[ord_rep_ancho];
        break;
//////////////////////////////////////////////////////////////////////////////////////////
////////////////////////// Orden Produccion - Plumon /////////////////////////////////////
    case 57:
        $sms = 0;
        if ($id == 0) {// Insertar
            if ($Set->insertar_orden_produccion_plumon($data) == false) {
                $sms = pg_last_error();
            } else {
                $accion = 'Insertar';
            }
        } else {// Modificar
            if ($Set->modificar_orden_produccion_plumon($data, $id) == false) {
                $sms = pg_last_error();
            } else {
                $accion = 'Modificar';
            }
        }
//**Auditoria****//
//        $fields = str_replace("&", ",", $fields[0]);
//        $modulo = 'Orden Produccion - Plumon';
//        $accion = $accion;
//        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//            $sms = "Auditoria" . pg_last_error();
//        }
//***************//
        echo $sms;
        break;
// Eliminar Orden Produccion - Plumon
    case 58:
        $sms = 0;
        if ($Set->delete_orden_produccion_plumon($id) == FALSE) {
            $sms = pg_last_error();
        }
//**Auditoria****//
        $files_adt = array('Orden Produccion - Plumon', 'Eliminar', $files, $files);
        $Adt->insert_auditoria($files_adt);
//***************//
        echo $sms;
        break;

    case 59:
        $rst_pro = pg_fetch_array($Set->lista_un_producto($_REQUEST[id]));
        $rst_fbc = pg_fetch_array($Set->lista_una_fabrica($rst_pro[emp_id]));
        $rst_set = pg_fetch_array($Set->lista_utlimo_seteo_maquina_plumon($_REQUEST[id]));
        $cns_mp = $Set->lista_mp($rst_pro[emp_id]);
        $combo = "<option  value='0'> - Seleccione - </option>";
        while ($rst_mp = pg_fetch_array($cns_mp)) {
            $combo.="<option value='$rst_mp[mp_id]'>$rst_mp[mp_referencia]</option>";
        }
        $retorno = $rst_pro[pro_ancho] . "&" .
                $rst_pro[pro_mp1] . "&" .
                $rst_pro[pro_mp2] . "&" .
                $rst_pro[pro_mp3] . "&" .
                $rst_pro[pro_mp4] . "&" .
                $rst_pro[pro_mf1] . "&" .
                $rst_pro[pro_mf2] . "&" .
                $rst_pro[pro_mf3] . "&" .
                $rst_pro[pro_mf4] . "&" .
                ($rst_pro[pro_mf1] + $rst_pro[pro_mf2] + $rst_pro[pro_mf3] + $rst_pro[pro_mf4]) . "&" .
                $rst_pro[pro_largo] . "&" .
                $rst_pro[pro_peso] . "&" .
                $rst_pro[pro_gramaje] . "&" .
                $rst_fbc[emp_sigla] . "&" .
                $rst_set[orp_temperatura] . "&" .
                $rst_set[orp_agua] . "&" .
                $rst_set[orp_resina] . "&" .
                $combo;
        echo $retorno;
        break;
///////////////////////////////////////////////////////////////////////////////////////
    case 60:
        $sms = 0;

        if ($data[25] == '[object Window]') {
            $data[25] = 0;
        }
        if ($id == 0) {// Insertar
            if ($Set->insertar_orden_produccion($data) == false) {
                $sms = 'Insert' . pg_last_error();
            } else {
                $accion = 'Insertar';
            }
        } else {// Modificar
            if ($Set->modificar_orden_produccion($data, $id) == false) {
                $sms = 'upd' . pg_last_error();
            } else {
                $accion = 'Modificar';
            }
        }

//        //**Auditoria****//
//        $fields = str_replace("&", ",", $fields[0]);
//        $modulo = 'Orden Produccion - Ecocambrela';
//        $accion = $accion;
//        if ($Adt->insert_audit_general($modulo, $accion, $fields) == false) {
//            $sms = "Auditoria" . pg_last_error();
//        }
//        //***************//
        echo $sms;

        break;
    case 61:
        $sms = 0;
        if ($Set->delete_orden_produccion($id)) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 62:
//echo md5($id);
        $sms = 0;
        $User = new User();
        if (md5($data[1]) == $data[2]) {
            if ($User->cambia_clave(md5($data[0]), $id) == false) {
                $sms = pg_last_error();
            }
        } else {
            $sms = 'Clave Anterior Incorrecta';
        }

        echo $sms;
        break;

    case 63:
        if ($s == 0) {
            $cns = $Set->lista_clientes_search(strtoupper($id));
            $cli = "";
            $n = 0;
            while ($rst = pg_fetch_array($cns)) {
                $n++;
                $nm = $rst[cli_raz_social];
                $cli .= "<tr ><td><input type='button' value='&#8730;' onclick=" . "load_cliente2('$rst[cli_ced_ruc]')" . " /></td><td>$n</td><td>$rst[cli_ced_ruc]</td><td>$nm</td></tr>";
            }
            echo $cli;
        } else {
            $sms;
            $rst = pg_fetch_array($Set->lista_clientes_codigo($id));
            if (!empty($rst)) {
                $sms = $rst[cli_ced_ruc] . '&' . $rst[cli_raz_social] . '&' . $rst[cli_calle_prin] . ' ' . $rst[cli_numeracion] . ' ' . $rst[cli_calle_sec] . '&' . $rst[cli_telefono] . '&' . $rst[cli_email] . '&' . $rst[cli_parroquia] . '&' . $rst[cli_canton] . '&' . $rst[cli_pais] . '&' . $rst[cli_id] . '&' . $rst[cli_tipo_cliente] . '&' . $rst[cli_estado];
            }
            echo $sms;
        }


        break;
    ///CAMBIOS CRISTINA
    case 64:
        if (strlen($_REQUEST[lt]) >= 8) {
            $tabla = 1;
            $rst = pg_fetch_array($Set->lista_un_producto_noperti_cod_lote($id, $_REQUEST[lt]));
            $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id], $tabla));
            echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_precio1[pre_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac];
        } else {
            $rst = pg_fetch_array($Set->lista_un_producto_industrial_id($id));
            if ($rst[id] != '') {
                $rst1 = pg_fetch_array($Set->total_ingreso_egreso_fact($rst[id], '0'));
                $inv = $rst1[ingreso] - $rst1[egreso];
                $rst2 = pg_fetch_array($Set->lista_costos_mov($rst[id]));
                echo $rst[id] . '&' . $rst[mp_c] . '&' . $rst[mp_d] . '&' . $rst[mp_e] . '&' . $rst_precio[mp_f] . '&' . $rst[mp_g] . '&' . $inv . '&0&' . $rst[mp_h] . '&' . $rst[mp_q] . '&' . $rst2[mov_val_unit];
            }
        }
        break;
    case 65:
        $sms = 0;
        $aud = 0;
        $nfact = str_replace('-', '', $data[25]);
        $rst = pg_fetch_array($Set->lista_una_factura_nfact($data[25]));
        if (empty($rst[num_secuencial])) {// Insertar
            $rstcliente = pg_num_rows($Set->lista_un_cliente_cedula($data[2]));
            if ($rstcliente > 0) {
                $data3 = array(
                    strtoupper($data[17]),
                    strtoupper($data[18]),
                    strtoupper($data[19]),
                    strtoupper($data[20]),
                    strtoupper($data[21]),
                    strtoupper($data[24])
                );
                if ($Set->upd_email_cliente($data3, $data[2]) == false) {
                    $sms = 'Insert_email' . pg_last_error() . $data[17] . '&' . $data[18] . '&' . $data[19] . '&' . $data[20] . '&' . $data[21] . '&' . $data[24];
                    $v = 1;
                }
            } else {
                if (strlen($data[2]) < 11) {
                    $tipo = 'CN';
                } else {
                    $tipo = 'CJ';
                }
                $rst_cod = pg_fetch_array($Clases_preciospt->lista_secuencial_cliente($tipo));
                $sec = (substr($rst_cod[cli_codigo], 2, 6) + 1);

                if ($sec >= 0 && $sec < 10) {
                    $txt = '0000';
                } else if ($sec >= 10 && $sec < 100) {
                    $txt = '000';
                } else if ($sec >= 100 && $sec < 1000) {
                    $txt = '00';
                } else if ($sec >= 1000 && $sec < 10000) {
                    $txt = '0';
                } else if ($sec >= 10000 && $sec < 100000) {
                    $txt = '';
                }

                $retorno = $tipo . $txt . $sec;

                $da = array(
                    strtoupper($data[1]),
                    strtoupper($data[2]),
                    strtoupper($data[17]),
                    strtoupper($data[19]),
                    strtoupper($data[18]),
                    strtoupper($data[20]),
                    strtoupper($data[21]),
                    $retorno,
                    strtoupper($data[24])
                );
                if ($Set->insert_cliente($da) == false) {
                    $sms = 'Insert_cli' . pg_last_error();
                    $v = 1;
                    $aud = 1;
                }
            }
            if ($v == 0) {
                $dt1 = explode('&', $data4[0]);
                $m = 0;
                $i = count($data4);

                while ($m < $i) {
                    $dt1 = explode('&', $data4[$m]);
                    $pg = 0;
                    $nfact = str_replace('-', '', $data[25]);
                    $fec = $data[28];
                    if ($dt1[1] == '9') {
                        $nf = strtotime("+$dt1[5] day", strtotime($fec));
                        $fec = date('Y-m-j', $nf);
                    }
                    $data5 = array(
                        $data[25],
                        $pg,
                        0,
                        0,
                        0,
                        $fec,
                        $dt1[1],
                        $dt1[2],
                        $dt1[3],
                        $dt1[4],
                        $dt1[5]
                    );
                    if ($dt1[1] != 0) {
                        if ($Clase_pagos->insert_pagos($data5) == false) {
                            $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                            $aud = 1;
                        }
                    }
                    $m++;
                }
                if ($data[29] == 0) {
                    $estp = '4';
                } else {
                    $estp = '3';
                }
                if ($Set->lista_cambia_status($data[30], $estp) == false) {
                    $sms = pg_last_error();
                }


                if ($Set->insert_factura($data) == true) {
                    $n = 0;
                    $i = count($data2);
                    while ($n < $i) {
                        $dt = explode('&', $data2[$n]);
                        if ($Set->insert_detalle_factura($dt) == true) {
                            $bod = $data[23];
                            $rst_cli = pg_fetch_array($Set->lista_un_cliente_cedula(strtoupper($data[2])));
                            $dat = array(
                                $dt[12],
                                25,
                                $rst_cli[cli_id],
                                $bod, ///BODEGA
                                $dt[0],
                                '0',
                                '0',
                                date('Y-m-d'),
                                date('Y-m-d'),
                                date('H:i:s'),
                                $dt[2], //cantidad
                                '',
                                date('Y-m-d'),
                                $dt[0],
                                '',
                                '',
                                $dt[13],
                                0,
                                0,
                                0,
                                0,
                                $dt[14]
                            );

                            if ($Set->insert_movimiento_pt($dat) == false) {
                                $sms = 'Insert_mov' . pg_last_error();
                                $aud = 1;
                            }
                        } else {
                            $sms = 'Insert_det' . pg_last_error();
                            $aud = 1;
                        }
                        $n++;
                    }
                } else {
                    $sms = 'Insert' . pg_last_error();
                    $accion = 'Insertar';
                    $aud = 1;
                }
            }


            ////// ctasxcobrar /////////
//            $cn_pag = $Clase_pagos->lista_detalle_pagos($data[25]);
//            $r_fac = $Set->lista_una_factura_nfact($data[25]);
//            while ($r_p = pg_fetch_array($cn_pag)){
//                $cta = array(
//                    $r_fac[com_id],
//                    date('Y-m-d'),
//                    $r_p[pag_valor],
//                    '',
//                    '',
//                    '878',/// clientes nacionales o extranjeros
//                    date('Y-m-d'),
//                    '',
//                    'PAGO',
//                    '0'
//                    );
//            if ($Clase_pagos->insert_ctasxcobrar($cta) == false) {
//                    $sms = pg_last_error();
//                }
//            }



            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'INSERTAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[25]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        } else {// Modificar
            if ($Set->elimina_detalle_factura($nfact) == true && $Set->elimina_movpt_documento($nfact) == true) {
                if ($Set->elimina_factura($data[25]) == false) {
                    $sms = 'del' . pg_last_error();
                    $aud = 1;
                }
            } else {
                $sms = 'del_det' . pg_last_error();
                $aud = 1;
            }

            if ($Set->insert_factura($data) == true) {
                $n = 0;
                $i = count($data2);
                while ($n < $i) {
                    $dt = explode('&', $data2[$n]);
                    if ($Set->insert_detalle_factura($dt) == true) {
                        $bod = $data[23];
                        $rst_cli = pg_fetch_array($Set->lista_un_cliente_cedula(strtoupper($data[2])));
                        $dat = array(
                            $dt[12],
                            25,
                            $rst_cli[cli_id],
                            $bod, ///BODEGA
                            $dt[0],
                            '0',
                            '0',
                            date('Y-m-d'),
                            date('Y-m-d'),
                            date('H:i:s'),
                            $dt[2], //cantidad
                            '',
                            date('Y-m-d'),
                            $dt[0],
                            '',
                            '',
                            $dt[13],
                            0,
                            0,
                            0,
                            0,
                            $dt[14]
                        );

                        if ($Set->insert_movimiento_pt($dat) == false) {
                            $sms = 'Insert_mov' . pg_last_error();
                            $aud = 1;
                        }
                    } else {
                        $sms = 'Insert_det' . pg_last_error();
                        $aud = 1;
                    }
                    $n++;
                }

                if ($Clase_pagos->delete_pagos($data[25]) == false) {
                    $sms = 'Delete_pagos1' . pg_last_error();
                    $aud = 1;
                } else {
                    $dt1 = explode('&', $data4[0]);
                    $m = 0;
                    $i = count($data4);
                    while ($m < $i) {
                        $dt1 = explode('&', $data4[$m]);
                        $pg = 0;
                        $nfact = str_replace('-', '', $data[25]);
                        $fec = $data[28];
                        if ($dt1[1] == '9') {
                            $nf = strtotime("+$dt1[5] day", strtotime($fec));
                            $fec = date('Y-m-j', $nf);
                        }
                        $data5 = array(
                            $data[25],
                            $pg,
                            0,
                            0,
                            0,
                            $fec,
                            $dt1[1],
                            $dt1[2],
                            $dt1[3],
                            $dt1[4],
                            $dt1[5]
                        );
                        if ($dt1[1] != 0) {
                            if ($Clase_pagos->insert_pagos($data5) == false) {
                                $sms = 'Insert_pagos2' . pg_last_error() . $data3;
                                $aud = 1;
                            }
                        }
                        $m++;
                    }
                }
            } else {
                $sms = 'Insert' . pg_last_error();
                $accion = 'Insertar';
                $aud = 1;
            }

            if ($aud == 0) {
                $n = 0;
                while ($n < count($fields)) {
                    $f = $f . strtoupper($fields[$n] . '&');
                    $n++;
                }
                $modulo = 'FACTURA';
                $accion = 'MODIFICAR';
                if ($Adt->insert_audit_general($modulo, $accion, $f, $data[25]) == false) {
                    $sms = "Auditoria" . pg_last_error() . 'ok2';
                }
            }
        }
        $rst_com = pg_fetch_array($Set->lista_una_factura_nfact($data[25]));
        echo $sms . '&' . $rst_com[com_id] . '&' . $mesaje;
        break;
///CAMBIOS CRISTINA
    case 66:
        $rst = pg_fetch_array($Set->lista_un_producto_noperti_id($id));
        $rst_precio1 = pg_fetch_array($Set->lista_precio_producto($rst[id]));
        echo $rst[pro_a] . '&' . $rst[pro_b] . '&' . $rst[pro_uni] . '&' . $rst_precio1[pre_precio] . '&' . $rst_precio1[pre_iva] . '&' . $rst_precio1[pre_descuento] . '&' . $rst_precio1[pre_ice] . '&' . $rst[pro_ad] . '&' . $rst[pro_ac];
        break;
    case 67:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dt6 = $data[6];                      // XML
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5, $dt6);
        if ($Set->upd_fac_nd_nc($dat, $id) == false) {
            $sms = 'upd_doc1' . pg_last_error();
        }
        $rst = pg_fetch_array($Set->lista_una_factura_id($id));
        echo $sms . "&" . $rst[num_documento] . '&' . $dt[4];
        break;
    case 68:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5);
        $idt = str_replace('-', '', $id);
        if ($Set->upd_retencion($dat, $idt) == false) {
            $sms = 'upd_ret' . pg_last_error();
        }
        echo $sms;
        break;
    case 69:
        $sms = 0;
        $dt0 = $Adt->sanear_string($data[0]); //Clave de acceso
        $dt1 = $Adt->sanear_string($data[1]); // Recepcion
        $dt2 = $Adt->sanear_string($data[2]); // Autorizacion
        $dt3 = $Adt->sanear_string($data[3]); // Mensaje
        $dt4 = $Adt->sanear_string($data[4]); // Numero Autorizacion
        $dt5 = $data[5];                      // Hora y fecha Autorizacion
        $dat = array($dt0, $dt1, $dt2, $dt3, $dt4, $dt5);
        if ($Set->upd_gui_rem($dat, $id) == false) {
            $sms = 'upd' . pg_last_error();
        }
        echo $sms;
        break;
    case 70:
        $rst = pg_fetch_array($Set->lista_obs_documentos($id));
        echo $rst[com_observacion];
        break;
    case 71:
        $cns = $Set->lista_factura_completo();
        while ($rst = pg_fetch_array($cns)) {
            if (empty($rst[clave_acceso])) {
                $f = $rst['fecha_emision'];
                $f2 = substr($f, -2) . substr($f, 4, 2) . substr($f, 0, 4);
                $cod_doc = "01"; //01= factura, 02=nota de credito tabla 4
                $emis[identificacion] = '1790007871001'; //Noperti
                $ambiente = 2;
                if ($rst[cod_punto_emision] == 10) {
                    $ems = '010';
                } else {
                    $ems = '00' . $rst[cod_punto_emision];
                }

                $sec = $rst[num_secuencial];
                if ($sec >= 0 && $sec < 10) {
                    $tx = "00000000";
                } else if ($sec >= 10 && $sec < 100) {
                    $tx = "0000000";
                } else if ($sec >= 100 && $sec < 1000) {
                    $tx = "000000";
                } else if ($sec >= 1000 && $sec < 10000) {
                    $tx = "00000";
                } else if ($sec >= 10000 && $sec < 100000) {
                    $tx = "0000";
                } else if ($sec >= 100000 && $sec < 1000000) {
                    $tx = "000";
                } else if ($sec >= 1000000 && $sec < 10000000) {
                    $tx = "00";
                } else if ($sec >= 10000000 && $sec < 100000000) {
                    $tx = "0";
                } else if ($sec >= 100000000 && $sec < 1000000000) {
                    $tx = "";
                }
                $secuencial = $tx . $sec;

                $codigo = "12345678"; //Del ejemplo del SRI                    
                $tp_emison = "1"; //Emision Normal                    
                $clave1 = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison);
                $cla = strrev($clave1);
                $n = 0;
                $p = 1;
                $i = strlen($clave1);
                $m = 0;
                $s = 0;
                $j = 2;
                while ($n < $i) {
                    $d = substr($cla, $n, 1);
                    $m = $d * $j;
                    $s = $s + $m;
                    $j++;
                    if ($j == 8) {
                        $j = 2;
                    }
                    $n++;
                }
                $div = $s % 11;
                $digito = 11 - $div;
                if ($digito < 10) {
                    $digito = $digito;
                } else if ($digito == 10) {
                    $digito = 1;
                } else if ($digito == 11) {
                    $digito = 0;
                }
                $clave = trim($f2 . $cod_doc . $emis[identificacion] . $ambiente . $ems . "001" . $secuencial . $codigo . $tp_emison . $digito);
                if (strlen($clave) != 49) {
                    $clave = '';
                }
                $Set->upd_fac_clave_acceso($clave, $rst[com_id]);
            }
        }
        break;
    case 72:
        $sms = 0;
        if ($Set->upd_fac_na($_REQUEST[na], $_REQUEST[fh], $id) == FALSE) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 73:
        $sms = 0;
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema($dat, $id) == false) {
            $sms = 'upd_doc2' . pg_last_error();
        }
        echo $sms;
        break;
    case 74:
        $sms = 0;
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_gui($dat, $id) == false) {
            $sms = 'upd_doc2' . pg_last_error();
        }
        echo $sms;
        break;
    case 75:
//        $sms = 0;
        $not = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_no($dat, $not) == false) {
            $sms = 'upd_doc3' . pg_last_error();
        }
//        echo $sms;
        break;
    case 76:
//        $sms = 0;
        $deb = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_ret($dat, $deb) == false) {
            $sms = 'upd_doc4' . pg_last_error();
        }
//        echo $sms;
        break;
    case 77:
//        $sms = 0;
        $not = str_replace('-', '', $id);
        $dat = 'ENVIADO';
        if ($Set->upd_env_ema_nodeb($dat, $not) == false) {
            $sms = 'upd_doc6' . pg_last_error();
        }
//        echo $sms;
        break;
    case 78:
        $sms = 0;
        if ($id == 0) {//Insertar
            if ($Set->insert_tppt($data) == false) {
                $sms = pg_last_error();
            }
        } else {//Modificar
            if ($Set->upd_tppt($data, $id) == false) {
                $sms = pg_last_error();
            }
        }
        echo $sms;
        break;
    //// secuncial erp_mp    
    case 79:
        if (!empty($id) && !empty($tbl)) {//Insertar
            $rst_sec = pg_fetch_array($Set->listar("erp_mp where mp_a='$id' and mp_b='$tbl'", ' desc limit 1'));
            $sec = substr($rst_sec[mp_c], -3) + 1;
            if ($sec > 0 && $sec < 10) {
                $txt = '00';
            } else if ($sec > 10 && $sec < 100 ) {
                $txt = '0';
            } else if ($sec > 100) {
                $txt = '';
            } else if ($sec == '') {
                $txt = '001';
            }
        }
        $rst_s1 = pg_fetch_array($Set->listar2("erp_tipos where tps_id='$id'", ' desc limit 1'));
        $rst_s2 = pg_fetch_array($Set->listar2("erp_tipos where tps_id='$tbl'", ' desc limit 1'));
        echo $rst_s1[tps_siglas] . '.' . $rst_s2[tps_siglas] . '.' . $txt . $sec;
        break;

    case 80:
        $sms = 0;
        if ($Set->update_estado($tbl, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
    case 81:
        $sms = 0;
        $j = 0;
        $i = 0;
        while ($j < count($data)) {
            $i++;
//            if (($i >= 6 && $i <= 12)||($i >= 16 && $i <= 22)) {
//                $cod = $data[$j];
//                $cta = pg_fetch_array($Set->lista_cuentas_cod($cod));
//                if (empty($cta)) {
//                    $txt = 0;
//                } else {
//                    $txt = $cta[pln_id];
//                }
//            } else {
            $txt = $data[$j];
//            }

            if ($Set->upd_configuraciones($i, $txt) == false) {
                $sms = pg_last_error();
            }

            $j++;
        }
        $dt2 = array($data2[1],
            $data2[2],
            $data2[3]);
        if ($Set->upd_configuraciones_sueldo($data2[0], $dt2) == false) {
            $sms = 'upd_sueldo' . pg_last_error();
        }
        $dt3 = array($data4[1],
            $data4[2],
            $data4[3]);
        if ($Set->upd_configuraciones_sueldo($data4[0], $dt3) == false) {
            $sms = 'upd_sueldo' . pg_last_error();
        }
        if ($Set->upd_sueldo_basico($data5) == false) {
            $sms = 'upd_sueldo_basico' . pg_last_error();
        }
        if ($Set->upd_sueldo_basico_empleado($data5) == false) {
            $sms = 'upd_sueldo_basico_emp' . pg_last_error();
        }
        $n = 0;
        while ($n < count($fields)) {
            $f = $f . strtoupper($fields[$n] . '&');
            $n++;
        }
        $modulo = 'CONFIGURACION GENERAL';
        $accion = 'MODIFICAR';
        if ($Adt->insert_audit_general($modulo, $accion, $f, '') == false) {
            $sms = "Auditoria" . pg_last_error() . 'ok2';
        }

        echo $sms;
        break;

    case 82:
        $sms = 0;
        if ($Set->update_estado($tbl, $id) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;

    case 83:
        $sms = 0;
        $val = $data[0] . '&' . $data[1] . '&' . $data[2] . '&' . $data[3] . '&' . $data[4] . '&' . $data[5] . '&' . $data[6] . '&' . $data[7];
        if ($Set->update_conf_email('8', $val) == false) {
            $sms = pg_last_error();
        }
        echo $sms;
        break;
}
?>
