<?php

include_once 'Conn.php';

class Set {

    var $con;

    function Set() {
        $this->con = new Conn();
    }

    //Menus

    function delete_all($tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM $tbl ");
        }
    }

    function list_structure($table) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT column_name FROM Information_Schema.Columns
                                            WHERE TABLE_NAME = '$table'
                                            AND   column_name<>'ids'
                                            ORDER BY ordinal_position ");
        }
    }

    function addField($field, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("alter table $tbl add $field character varying;
                                         alter table $tbls add $field character varying ");
        }
    }

    function addField_prod($field, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("alter table $tbl add $field character varying;
                                         alter table $tbls add $field character varying;
                                         alter table $tbls add $field" . '1' . " character varying;    
                                         alter table $tbls add $field" . '2' . " character varying;    
                                         alter table $tbls add $field" . '3' . " character varying;    
                                         alter table $tbls add $field" . '4' . " character varying;    
                                             ");
        }
    }

    function updField($old, $new, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("ALTER TABLE $tbl RENAME COLUMN  $old TO  $new;
                                         ALTER TABLE $tbls RENAME COLUMN  $old TO  $new ");
        }
    }

    function updField_prod($old, $new, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("ALTER TABLE $tbl RENAME COLUMN  $old TO  $new;
                                         ALTER TABLE $tbls RENAME COLUMN  $old TO  $new;
                                         ALTER TABLE $tbls RENAME COLUMN  $old" . '1' . " TO  $new" . '1' . ";    
                                         ALTER TABLE $tbls RENAME COLUMN  $old" . '2' . " TO  $new" . '2' . ";    
                                         ALTER TABLE $tbls RENAME COLUMN  $old" . '3' . " TO  $new" . '3' . ";    
                                         ALTER TABLE $tbls RENAME COLUMN  $old" . '4' . " TO  $new" . '4' . ";    
                                             ");
        }
    }

    function delField($field, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("ALTER TABLE $tbl  DROP COLUMN $field ;
                                         ALTER TABLE $tbls  DROP COLUMN $field ");
        }
    }

    function delField_prod($field, $tbl) {
        if ($this->con->Conectar() == true) {
            $tbls = substr($tbl, 0, -4);
            return pg_query("ALTER TABLE $tbl  DROP COLUMN $field ;
                                         ALTER TABLE $tbls  DROP COLUMN $field;
                                         ALTER TABLE $tbls  DROP COLUMN $field" . '1' . ";    
                                         ALTER TABLE $tbls  DROP COLUMN $field" . '2' . ";    
                                         ALTER TABLE $tbls  DROP COLUMN $field" . '3' . ";    
                                         ALTER TABLE $tbls  DROP COLUMN $field" . '4' . ";                 ");
        }
    }

    function insert($data, $field, $tbl) {
        if ($this->con->Conectar() == true) {
            $n = 0;
            while ($n < count($field)) {
                if ($n == count($field) - 1) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $campos.=$field[$n] . $coma;
                $datos.="'" . $data[$n] . "'" . $coma;
                $n++;
            }
            $campos;
            return pg_query(" INSERT INTO $tbl  ($campos) values ($datos) ");
        }
    }

    function update($data, $field, $tbl, $id, $s) {
        if ($this->con->Conectar() == true) {
            $n = 0;
            while ($n < count($field)) {
                if ($n == count($field) - 1) {
                    $coma = '';
                } else {
                    $coma = ',';
                }
                $campos.=$field[$n] . "='" . $data[$n] . "'" . $coma;
                $n++;
            }

            return pg_query(" UPDATE $tbl  SET  $campos where id$s=$id ");
        }
    }

    function listar($tbl, $s) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl order by id$s ");
        }
    }

    function listar2($tbl, $s) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl order by tps_id $s");
        }
    }

    function lista_all_tablas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT table_schema, table_name FROM information_schema.columns WHERE column_default ~'_seq' ");
        }
    }

    function listOneById($id, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl  WHERE ids=$id");
        }
    }

    function listOneById1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_tipos where tps_relacion='$id' order by tps_nombre");
        }
    }

    function list_produccion($tbl, $id, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl  WHERE ids=$id AND reg_tipo=$tp ORDER BY id ");
        }
    }

    function list_pedido_sec($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_a,substr(ped_a,2,6) as cod from erp_pedidos WHERE substr(ped_a,1,1)='$tp' ORDER BY substr(ped_a,2,6) DESC LIMIT 1");
        }
    }

    function del($table, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM $table WHERE ids=$id ");
        }
    }

    function lista_by_tipo($table) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $table  ");
        }
    }

    function lista_table_by_tipo($table, $tipo) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $table WHERE ids=$tipo order by id");
        }
    }

    function lista_buscador($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mp WHERE $txt order by id");
        }
    }

    function lista_table_by_tipo_finder($tipo, $code, $date, $ref) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pd.* FROM erp_pedidos pd, erp_productos pr
                                        WHERE pd.ped_d=pr.id
                                        AND pd.ids=$tipo
                                        AND (
                                        $code
                                        $date
                                        $ref
                                        )           ");
        }
    }

    function lista_one_table_code_finder($code, $tipo) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_productos 
                                        WHERE (pro_a LIKE '%$code%'  OR  pro_c LIKE '%$code%' OR pro_b LIKE '%$code%') ");
        }
    }

    function lista_productos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_productos");
        }
    }

    function lista_one_table_just_code($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_pedidos 
                                        WHERE ped_a LIKE '%$code%' ");
        }
    }

    function lista_one_by_table_code_finder($code, $tbl) {
        switch ($tbl) {
            case 'erp_insumos':
                $prf = 'ins_';
                break;
            case 'erp_productos':
                $prf = 'pro_';
                break;
            case 'erp_maquinas':
                $prf = 'maq_';
                break;
            case 'erp_pedidos':
                $prf = 'ped_';
                break;
            case 'erp_clientes':
                $prf = 'cli_';
                break;
        }
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $tbl 
                                        WHERE (" . $prf . "a LIKE '%$code%'  OR  " . $prf . "c LIKE '%$code%') ");
        }
    }

    function lista_one_data($table, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $table WHERE ids=$id");
        }
    }

    function lista_data($table) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM $table");
        }
    }

//Datos

    function list_one_data_by_id($tbl, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl  WHERE id=$id");
        }
    }

    function list_one_data_by_id_tip($tbl, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from $tbl  WHERE tps_id=$id");
        }
    }

    function del_data($table, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM $table WHERE id=$id ");
        }
    }

    function del_data_by_pedido($table, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM $table WHERE ids=$id ");
        }
    }

    function del_data_by_pedido2($table, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM $table WHERE mov_ped_id=$id ");
        }
    }

//Pedidos


    function list_aprobation($sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_pedidos  WHERE ped_f='$sts' ");
        }
    }

    function list_aprobation_varios_registros($ref, $ped, $from, $until, $sts) {

        if ($sts == 0) {
            $tx_sts = "AND (ped.ped_f='1' OR ped.ped_f='2' OR ped.ped_f='3')";
        } else {
            $tx_sts = "AND ped.ped_f='$sts'";
        }
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped.* from erp_pedidos ped, erp_productos pro 
                                            WHERE ped.ped_d=pro.id
                                            AND (
                                                    ped.ped_a='$ped'  
                                                    OR(
                                                       pro.pro_a='$ref'
                                                       AND ped.ped_b BETWEEN '$from' AND '$until'
                                                    )
                                                    OR ped.ped_b BETWEEN '$from' AND '$until'
                                                )
                                            $tx_sts   ");
        }
    }

    function list_aprobation_varios() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_pedidos  WHERE ped_f='1' OR ped_f='2' OR ped_f='3' ");
        }
    }

    function cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_pedidos SET ped_f='$sts' WHERE id=$id ");
        }
    }

    function insert_movimiento($data) {
        if ($this->con->Conectar() == true) {
            $date = date("d/m/Y");
            $hour = date("H:m:s");
            return pg_query("
                            INSERT INTO erp_mov_inventario(
                                        mov_ped_id,
                                        mov_ins_id,
                                        mov_tipo_trans,
                                        mov_fecha,
                                        mov_usuario,
                                        mov_cantidad,
                                        mov_hora,
                                        mov_obs)
                                VALUES (
                                '$data[0]',
                                '$data[1]',
                                '$data[2]',
                                '$date',
                                '$_SESSION[usuid]',
                                '$data[3]',
                                '$hour',
                                '$data[4]' )
                            ");
        }
    }

    function update_movimiento($data, $id) {
        if ($this->con->Conectar() == true) {
            $date = date("d/m/Y");
            $hour = date("H:m:s");
            return pg_query(" UPDATE erp_mov_inventario SET 
                                            mov_ped_id='$data[0]',
                                            mov_ins_id='$data[1]',
                                            mov_tipo_trans='$data[2]',
                                            mov_fecha='$date',
                                            mov_usuario='$_SESSION[usuid]',
                                            mov_cantidad='$data[3]',
                                            mov_hora='$hour',
                                            mov_obs='$data[4]' WHERE mov_id=$id ");
        }
    }

    function list_inv_pedido_ins($ped, $ins) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inventario 
WHERE mov_ped_id=$ped
AND   mov_ins_id=$ins ");
        }
    }

//Produccion       
    function list_produccion_pedido_tipo($ped, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(reg_cnt1+reg_cnt2+reg_cnt3+reg_cnt4) FROM erp_registros_produccion WHERE ids=$ped AND reg_tipo=$tp ");
        }
    }

//Movimientos de Inventarios
    function lista_transacciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transacciones ORDER BY trs_descripcion ");
        }
    }

    function lista_una_transacciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transacciones WHERE  trs_id=$id ");
        }
    }

    function lista_movimientos_inv_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inventario WHERE mov_documento='$cod' ");
        }
    }

    function lista_una_transaccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_transacciones WHERE trs_id=$id ");
        }
    }

    function lista_insumos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_insumos ORDER BY ins_a ");
        }
    }

    function lista_insumos_rango($from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_insumos WHERE ins_a>='$from' AND ins_a<='$until' ");
        }
    }

    function lista_un_movimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inventario WHERE mov_id=$id ");
        }
    }

    function lista_movimiento_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inventario WHERE mov_documento='$cod' ");
        }
    }

    function lista_secuencia_movimiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT mov_documento FROM erp_mov_inventario WHERE mov_tp_sec=1 ORDER BY mov_documento DESC LIMIT 1");
        }
    }

    function lista_movimientos_fecha($from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT *  FROM 
                                         erp_mov_inventario inv,
                                         erp_insumos ins
                                         WHERE inv.mov_prod_id=ins.id
                                         AND mov_fecha_trans BETWEEN '$from' AND '$until' ORDER BY mov_documento ASC  ");
        }
    }

//        function lista_movimientos_agrupados(){
//		if($this->con->Conectar()==true){
//			return pg_query("SELECT mov_ubicacion,
//                            mov_documento,
//                            mov_fecha_trans,
//                            mov_procedencia_destino,
//                            trs_id
//                            FROM erp_mov_inventario
//                            GROUP BY mov_ubicacion,
//                            mov_documento,
//                            mov_fecha_trans,
//                            mov_procedencia_destino,
//                            trs_id");
//		}
//       }

    function lista_insumos_inventario0($fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_insumos ins
                                         WHERE EXISTS (SELECT * FROM erp_mov_inventario mi WHERE mi.mov_prod_id=ins.id AND mov_fecha_trans<='$fecha'   )  ");
        }
    }

    function lista_insumos_inventario1($ins1, $ins2, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_insumos ins
                                         WHERE EXISTS (SELECT * FROM erp_mov_inventario mi WHERE mi.mov_prod_id=ins.id AND mov_fecha_trans<='$fecha' )
                                         AND ins.ins_a>='$ins1'  AND ins.ins_a<='$ins2'");
        }
    }

    function lista_inventario_ins_bodega($id, $bdg, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mov_inventario inv,erp_transacciones tr
                                                  WHERE inv.mov_prod_id=$id 
                                                  AND inv.trs_id=tr.trs_id
                                                  AND inv.mov_ubicacion='$bdg'
                                                  AND inv.mov_fecha_trans <= '$fecha' ");
        }
    }

    function inser_inventarios($data) {
        if ($this->con->Conectar() == true) {
            $usuid = $_SESSION[usuid];
            $f_reg = date('Y-m-d');
            $h_reg = date('H:m:s');
            return pg_query("INSERT INTO erp_mov_inventario(
                            usu_id,
                            mov_fecha_registro,
                            mov_hora_registro,
                            trs_id,
                            mov_ubicacion,
                            mov_documento,
                            mov_prod_id,
                            mov_tp_prod,
                            mov_num_trans,
                            mov_fecha_trans,
                            mov_cantidad,
                            mov_procedencia_destino,
                            mov_unidad,
                            mov_v_unit,
                            mov_tp_sec)
                            VALUES(
                             $usuid,
                            '$f_reg',
                            '$h_reg',    
                             $data[0],
                             $data[1],
                            '$data[2]',
                             $data[3],    
                             $data[4],    
                            '$data[5]',    
                            '$data[6]',
                             $data[7],    
                            '$data[8]',    
                             $data[9],    
                             $data[10],
                             $data[11]  )");
        }
    }

//INDUSTRIAL
    //Materia Prima y Tipo Materia Priuma

    function insert_tppt($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_tppt (
                                tpt_siglas,
                                tpt_nombre,
                                tpt_obs,
                                tpt_tipo,
                                tpt_relacion
                                )VALUES(
                                '$data[0]',
                                '$data[1]',
                                '$data[2]',
                                '$data[3]',
                                '$data[4]' )");
        }
    }

    function upd_tppt($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_tppt 
                            SET  tpt_siglas='$data[0]',
                                 tpt_nombre='$data[1]',
                                 tpt_obs='$data[2]', 
                                 tpt_tipo='$data[3]',
                                 tpt_relacion='$data[4]'    
                                     
                            WHERE tpt_id=$id     ");
        }
    }

    function insert_mp($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mp (
                                emp_id,
                                mpt_id,
                                mp_codigo,
                                mp_referencia,
                                mp_pro1,
                                mp_pro2,
                                mp_pro3,
                                mp_pro4,
                                mp_obs,
                                mp_unidad,
                                mp_presentacion   )VALUES(
                                 $data[0],
                                 $data[1],
                                '$data[2]',
                                '$data[3]',
                                '$data[4]',
                                '$data[5]',
                                '$data[6]',
                                '$data[7]',     
                                '$data[8]',
                                '$data[9]','$data[10]' )");
        }
    }

    function upd_tpmp($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_tpmp 
                            SET  mpt_siglas='$data[0]',
                                 mpt_nombre='$data[1]',
                                 mpt_obs='$data[2]' 
                            WHERE mpt_id=$id     ");
        }
    }

    function upd_mp($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_mp 
                            SET     emp_id=$data[0],
                                    mpt_id=$data[1],
                                    mp_codigo='$data[2]', 
                                    mp_referencia='$data[3]', 
                                    mp_pro1='$data[4]',
                                    mp_pro2='$data[5]',
                                    mp_pro3='$data[6]',
                                    mp_pro4='$data[7]',    
                                    mp_obs='$data[8]',
                                    mp_unidad='$data[9]',
                                    mp_presentacion='$data[10]'
                            WHERE   mp_id=$id  ");
        }
    }

    function del_tpmp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_tpmp  WHERE mpt_id=$id     ");
        }
    }

    function del_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mp  WHERE mp_id=$id     ");
        }
    }

    function lista_tpmp() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tpmp ORDER BY mpt_id ");
        }
    }

    function lista_un_tpmp_nom($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tpmp WHERE mpt_nombre ='$txt'");
        }
    }

    function lista_un_tppt_nombre($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tppt WHERE tpt_nombre = '$txt'");
        }
    }

    function total_ingreso_egreso($id, $fec1) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_peso_total)as suma FROM erp_i_mov_inventario m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.mp_id=$id and t.trs_operacion= 0 and mov_fecha_trans<'$fec1') as ingreso,
                                   (SELECT SUM(m.mov_peso_total)as suma FROM erp_i_mov_inventario m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.mp_id=$id and t.trs_operacion= 1 and mov_fecha_trans<'$fec1') as egreso");
        }
    }

//    function total_ingreso_egreso_fact($id, $tab) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0  and m.mov_tabla=$tab) as ingreso,
//                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.mov_tabla=$tab) as egreso");
//        }
//    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

    function lista_tpmp_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tpmp WHERE mpt_nombre like '%$txt' or mpt_siglas like '%$txt%'  ");
        }
    }

///Tipo Mp
    function lista_tppt() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tppt ORDER BY tpt_nombre ");
        }
    }

    function lista_tppt_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tppt WHERE tpt_nombre like '%$txt' or tpt_siglas like '%$txt%'  ");
        }
    }

    function lista_mp($fbc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp mp,
erp_i_tpmp tmp,
erp_empresa em 
WHERE mp.emp_id=em.emp_id
AND   mp.mpt_id=tmp.mpt_id
AND   mp.emp_id=$fbc
ORDER BY mp.mp_id ");
        }
    }

    function lista_mp0() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp mp,
erp_i_tpmp tmp,
erp_empresa em 
WHERE em.emp_id=mp.emp_id
AND   mp.mpt_id=tmp.mpt_id
ORDER BY mp.mp_id ");
        }
    }

    function lista_search_mp($txt, $emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp mp,
                                                    erp_i_tpmp tmp,
                                                    erp_empresa em 
                                                    WHERE em.emp_id=mp.emp_id
                                                    AND   mp.mpt_id=tmp.mpt_id
                                                    AND   (mp.mp_codigo like '%$txt%' or mp.mp_referencia like '%$txt%')
                                                    $emp    
                                                    ORDER BY mp.mp_id ");
        }
    }

    function lista_un_tpmp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tpmp WHERE mpt_id=$id ");
        }
    }

    function lista_un_tptp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tppt WHERE tpt_id=$id ");
        }
    }

    function lista_un_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp WHERE mp_id=$id ");
        }
    }

    function lista_un_mp_mod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set where upper(split_part(mp_tipo,'&',10))='$id'");
        }
    }

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set order by split_part(mp_tipo,'&',10)");
        }
    }

    function lista_un_mp_code($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM 
erp_i_mp mp,
erp_empresa em
WHERE mp.emp_id=em.emp_id
and mp.mp_codigo='$code' ");
        }
    }

    function lista_codigo_mp($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT mp.mp_codigo FROM erp_i_mp mp,
                                            erp_i_tpmp tmp,
                                            erp_empresa em 
                                            WHERE em.emp_id=emp_id
                                            AND   mp.mpt_id=tmp.mpt_id
                                            AND   mp.mpt_id=$tp 
                                            ORDER BY mp.mp_codigo DESC LIMIT 1 ");
        }
    }

//Fabricas
    function lista_fabricas() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa WHERE emp_id>2 ORDER BY emp_descripcion ");
        }
    }

    function lista_una_fabrica_desc($desc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa WHERE emp_descripcion='$desc' ");
        }
    }

    function lista_una_fabrica($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_empresa WHERE emp_id=$id ");
        }
    }

//Egreso de Materia Prima       
    function lista_pedidos_mp_sts($sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 
emp.emp_descripcion,
pmp.emp_id,
pmp.ped_fecha,
pmp.ped_orden
FROM erp_i_pedido_mp pmp,
erp_empresa emp
WHERE pmp.emp_id=emp.emp_id
and pmp.ped_sts=$sts 
group by
emp.emp_descripcion,
pmp.emp_id,
pmp.ped_fecha,
pmp.ped_orden
ORDER BY pmp.ped_fecha
");
        }
    }

    function lista_pedidos_mp_sts_search($sts, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT 
emp.emp_descripcion,
pmp.emp_id,
pmp.ped_fecha,
pmp.ped_orden
FROM erp_i_pedido_mp pmp,
erp_empresa emp
WHERE pmp.emp_id=emp.emp_id
and pmp.ped_sts=$sts 
and (
    pmp.ped_orden like'$txt'
  or  emp.emp_descripcion like '%$txt%'        
    )
group by
emp.emp_descripcion,
pmp.emp_id,
pmp.ped_fecha,
pmp.ped_orden
ORDER BY pmp.ped_fecha
");
        }
    }

    function lista_pedidomp_sts($sts, $ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_orden FROM erp_i_pedido_mp 
WHERE ped_sts=$sts 
and ped_orden='$ped'
group by ped_orden ");
        }
    }

    function lista_movmp_code($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario
WHERE mov_documento='$code'   ");
        }
    }

    function lista_un_pedidmp($ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp pmp,
erp_empresa emp,
erp_i_mp mp
WHERE pmp.emp_id=emp.emp_id
and   pmp.mp_id=mp.mp_id
and pmp.ped_orden='$ped'
 ");
        }
    }

// Movimientos de Materia Prima
    function lista_mov_mp($trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   trs.trs_operacion=$trs
                                        ORDER BY mov_id ");
        }
    }

    function lista_mov_mp_search($trs, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   trs.trs_operacion=$trs
                                        AND   (mp.mp_codigo like '%$txt%' OR   mp.mp_referencia like '%$txt%' )
                                        ORDER BY mi.mov_id ");
        }
    }

    function lista_mov_mp_search2($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   (mp.mp_codigo like '%$txt%'    
                                        OR   mp.mp_referencia like '%$txt%')        
                                        ORDER BY mi.mov_id ");
        }
    }

    function lista_all_mov_mp() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        ORDER BY mi.mov_fecha_trans ");
        }
    }

    function lista_inv_kardex($mp, $desde, $fecha) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   mi.mov_fecha_trans between '$desde' and '$fecha'                                        
                                        $mp
                                        ORDER BY mp.mp_id,mi.mov_fecha_trans ");
        }
    }

    function lista_total_pedido_solicitado($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(ped_det_cant) as und, sum(ped_det_peso) as peso FROM erp_i_pedido_mp WHERE ped_orden='$cod'");
        }
    }

    function lista_total_pedido_entregado($cod, $trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(mi.mov_cantidad) as und, sum(mi.mov_peso_total) as peso FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   trs.trs_operacion=$trs
					AND   mi.mov_documento='$cod' ");
        }
    }

    function lista_secuencia_transaccion($trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mov_inventario mi,
                                            erp_transacciones tr
                                            where mi.trs_id=tr.trs_id
                                            and tr.trs_operacion=$trs
                                            order by mi.mov_num_trans desc
                                            limit 1 ");
        }
    }

    function lista_mov_mp_codigo($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.mov_num_trans='$doc'
                                        AND   mi.trs_id=trs.trs_id ORDER BY mov_id ");
        }
    }

    function lista_un_movimiento_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.mov_id=$id
                                        AND   mi.trs_id=trs.trs_id ORDER BY mov_id ");
        }
    }

    function lista_sec_pedido($ped, $trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   trs.trs_operacion=$trs
					AND   mi.mov_documento='$ped' 
                                        ORDER BY mov_id ");
        }
    }

    function lista_sec_transaccion($trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inventario mi,
                                        erp_transacciones trs,
                                        erp_i_mp mp
                                        WHERE mi.mp_id=mp.mp_id
                                        AND   mi.trs_id=trs.trs_id
                                        AND   trs.trs_operacion=$trs
                                        ORDER BY mi.mov_num_trans desc limit 1 ");
        }
    }

    function del_mov($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inventario WHERE mov_id=$id  ");
        }
    }

    function del_mov_code($nm_trs) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inventario WHERE mov_num_trans='$nm_trs'  ");
        }
    }

    function del_mov_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inventario WHERE mov_documento='$doc'  ");
        }
    }

    function insert_inv_mp($data) {
        if ($this->con->Conectar() == true) {
            $fecha = date('Y-m-d');
            $hora = date('H:i');
            return pg_query("INSERT INTO erp_i_mov_inventario (
                                usu_id,
                                mov_fecha_registro,
                                mov_hora_registro,
                                mov_ubicacion,                                
                                mov_procedencia_destino,                                
                                trs_id,
                                mp_id,
                                mov_documento,
                                mov_num_trans,                                
                                mov_fecha_trans,
                                mov_cantidad,
                                mov_presentacion,
                                mov_peso_total,
                                mov_proveedor,
                                mov_peso_unit,
                                mov_tranportista,
                                mov_guia_remision
                            )VALUES(
                            $_SESSION[usuid],
                            '$fecha',
                            '$hora',
                            '',
                            '',    
                            $data[0],
                            $data[1],
                           '$data[2]',    
                           '$data[3]',
                           '$data[4]',
                            $data[5],
                           '$data[6]',
                            $data[7],
                           '$data[8]',
                            $data[9],
                           '$data[10]',
                           '$data[11]'   )");
        }
    }

//Pedido de MAteria Prima       
    function lista_pedi_mp() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp,
                                erp_empresa,
                                erp_i_mp 
                                WHERE erp_i_pedido_mp.emp_id=erp_empresa.emp_id
                                AND   erp_i_pedido_mp.mp_id=erp_i_mp.mp_id
                                ORDER BY erp_i_pedido_mp.ped_orden");
        }
    }

    function lista_pedi_mp_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp,
                                erp_empresa,
                                erp_i_mp 
                                WHERE erp_i_pedido_mp.emp_id=erp_empresa.emp_id
                                AND   erp_i_pedido_mp.mp_id=erp_i_mp.mp_id
                                AND   
                                (
                                     erp_i_pedido_mp.ped_orden='$txt'
                                OR   erp_i_mp.mp_codigo like '%$txt%'    
                                OR   erp_i_mp.mp_referencia like '%$txt%' )


                                ORDER BY erp_i_pedido_mp.ped_orden ");
        }
    }

    function lista_ped_sec() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp
                                                    ORDER BY ped_orden desc
                                                    limit 1 ");
        }
    }

    function insert_pmp($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_pedido_mp (
				  ped_orden,
				  ped_fecha,
				  emp_id,
				  mp_id,
				  ped_det_cant,
				  ped_det_peso)VALUES(
                                '$data[0]',
                                '$data[1]',
                                 $data[2],
                                 $data[3],
                                 $data[4],
                                 $data[5])");
        }
    }

    function lista_des_mp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_mp where mp_id=$id");
        }
    }

    function lista_inv_mp($mp, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(mi.mov_peso_total)as peso,
                                                    sum(mi.mov_cantidad)as unidad
                                                    from erp_i_mov_inventario mi,
                                                    erp_transacciones trs
                                                    where mi.trs_id=trs.trs_id
                                                    and mi.mp_id=$mp
                                                    and trs.trs_operacion=$tp ");
        }
    }

    function lista_inv_mp_doc($mp, $doc, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(mi.mov_peso_total)as peso,
                                                    sum(mi.mov_cantidad)as unidad
                                                    from erp_i_mov_inventario mi,
                                                    erp_transacciones trs
                                                    where mi.trs_id=trs.trs_id
                                                    and mi.mp_id=$mp
                                                    and mi.mov_documento='$doc'
                                                    and trs.trs_operacion=$tp ");
        }
    }

    function lista_inv_mp_doc_total($doc, $tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(mi.mov_peso_total)as peso,
                                                    sum(mi.mov_cantidad)as unidad
                                                    from erp_i_mov_inventario mi,
                                                    erp_transacciones trs
                                                    where mi.trs_id=trs.trs_id
                                                    and mi.mov_documento='$doc'
                                                    and trs.trs_operacion=$tp ");
        }
    }

    function lista_ped_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_pedido_mp,erp_empresa,erp_i_mp where 
                                erp_i_pedido_mp.emp_id=erp_empresa.emp_id and
                                erp_i_pedido_mp.mp_id=erp_i_mp.mp_id
                                and ped_id=$id");
        }
    }

    function lista_pedido_codgo($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_mp,
                                erp_i_mp 
                                WHERE erp_i_pedido_mp.mp_id=erp_i_mp.mp_id
                                and erp_i_pedido_mp.ped_orden='$code'
                                ORDER BY erp_i_pedido_mp.ped_orden");
        }
    }

    function upd_pmp($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_pedido_mp 
                            SET     ped_orden='$data[0]',
                                    ped_fecha='$data[1]',
                                    emp_id=$data[2], 
                                    mp_id=$data[3], 
                                    ped_det_cant=$data[4],
                                    ped_det_peso=$data[5]
                            WHERE   ped_id=$id");
        }
    }

    function del_pmp($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_pedido_mp WHERE ped_id=$id");
        }
    }

// Orden de compra
    function lista_ordenes_compra() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  
erp_i_enc_orden_compra_mp oc,
erp_i_cliente cl,
erp_empresa em
WHERE oc.cli_id=cl.cli_id
and oc.emp_id=em.emp_id
Order by oc.orc_codigo ");
        }
    }

    function lista_ordenes_compra_search($txt, $date, $sts = '') {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  
erp_i_enc_orden_compra_mp oc,
erp_i_cliente cl,
erp_empresa em
WHERE oc.cli_id=cl.cli_id
AND oc.emp_id=em.emp_id
$txt
$date   
$sts
Order by oc.orc_codigo ");
        }
    }

    function lista_ultima_orden_compra_producto($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  
erp_i_det_orden_compra_mp doc,
erp_i_enc_orden_compra_mp oc,
erp_i_cliente cl,
erp_empresa em,
erp_i_mp mp
WHERE oc.cli_id=cl.cli_id
and oc.emp_id=em.emp_id
and oc.orc_id=doc.orc_id
and mp.mp_id=doc.mp_id
and mp.mp_codigo='$code'
order by oc.orc_fecha desc ");
        }
    }

    function lista_ordenes_compra_etq() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  
erp_i_enc_orden_compra_mp oc,
erp_i_det_orden_compra_mp doc,
erp_i_mp mp 
WHERE oc.orc_id=doc.orc_id
and mp.mp_id=doc.mp_id order by oc.orc_fecha ");
        }
    }

    function lista_pesos_barcode($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_etq_orden WHERE etq_bar_code='$code'");
        }
    }

    function lista_pesos_det_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_etq_orden WHERE orc_det_id=$id ");
        }
    }

    function lista_ordenes_compra_etq_search($txt, $date) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  
erp_i_enc_orden_compra_mp oc,
erp_i_det_orden_compra_mp doc,
erp_i_mp mp 
WHERE oc.orc_id=doc.orc_id
AND mp.mp_id=doc.mp_id 
$txt
$date ");
        }
    }

    function lista_subt($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(orc_det_cant*orc_det_vu) from  
erp_i_det_orden_compra_mp
WHERE orc_id=$id ");
        }
    }

    function lista_secuencial_orden() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_enc_orden_compra_mp Order by orc_codigo desc limit 1");
        }
    }

    function lista_orden_compra_code($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_enc_orden_compra_mp WHERE orc_codigo='$code'");
        }
    }

    function lista_total_orden_compra_code($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("select oc.orc_id,sum(doc.orc_det_cant) from  erp_i_enc_orden_compra_mp oc,
	erp_i_det_orden_compra_mp doc 
	WHERE doc.orc_id=oc.orc_id
	AND   oc.orc_codigo='$code'
	group by oc.orc_id ");
        }
    }

    function lista_etq_det($det) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_i_etq_orden
WHERE orc_det_id=$det");
        }
    }

    function lista_una_orden_total($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(orc_det_vt) FROM erp_i_det_orden_compra_mp  where orc_id=$id");
        }
    }

    function lista_historial_orden_mp($mp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_det_orden_compra_mp  where mp_id=$mp order by orc_det_id desc limit 1 offset 1");
        }
    }

    function lista_orden_total_mensual_user($user, $from, $until) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(orc_det_vt) FROM erp_i_det_orden_compra_mp doc, erp_i_enc_orden_compra_mp oc 
where doc.orc_id=oc.orc_id
and oc.usu_id=$user
and oc.orc_fecha between '$from' and '$until'");
        }
    }

    function lista_una_orden_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_enc_orden_compra_mp oc,
                                         erp_empresa em,
                                         erp_i_cliente cl 
                                         WHERE oc.emp_id=em.emp_id 
                                         AND   cl.cli_id=oc.cli_id
                                         AND oc.orc_id=$id  ");
        }
    }

    function lista_un_det_oc_mp($oc, $mp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_det_orden_compra_mp doc,
erp_i_enc_orden_compra_mp oc,
erp_i_mp mp
where oc.orc_id=doc.orc_id
and mp.mp_id=doc.mp_id
and oc.orc_codigo='$oc'
and doc.mp_id=$mp
  ");
        }
    }

    function lista_una_det_orden_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_det_orden_compra_mp doc,
erp_i_enc_orden_compra_mp oc,
erp_empresa em,
erp_i_cliente cl,
erp_i_mp mp 
WHERE oc.orc_id=doc.orc_id 
AND  oc.emp_id=em.emp_id 
AND  mp.mp_id=doc.mp_id
AND  cl.cli_id=oc.cli_id
AND  doc.orc_det_id=$id  ");
        }
    }

    function insert_orden_compra($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_enc_orden_compra_mp 
                                            (cli_id,
                                            orc_fecha,
                                            orc_codigo,
                                            emp_id,
                                            orc_descuento,
                                            orc_flete,
                                            orc_fecha_entrega,
                                            orc_factura,
                                            orc_condicion_pago,
                                            orc_direccion_entrega,
                                            usu_id,
                                            orc_guia_recepcion,
                                            orc_documento,
                                            orc_iva
                                            )VALUES(
                                            $data[0],
                                           '$data[1]',
                                           '$data[2]',
                                            $data[3],
                                            $data[4],
                                            $data[5],
                                           '$data[6]',
                                           '$data[7]',
                                           '$data[8]',
                                           '$data[9]',
                                            $_SESSION[usuid],
                                           '$data[10]',
                                           '$data[11]',
                                            $data[12]   ) ");
        }
    }

    function upd_orden_compra($data, $code) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_enc_orden_compra_mp 
                                      SET   cli_id='$data[0]',
                                            orc_fecha='$data[1]',
                                            emp_id='$data[3]',
                                            orc_descuento=$data[4],
                                            orc_flete=$data[5],
                                            orc_fecha_entrega='$data[6]',
                                            orc_factura='$data[7]',
                                            orc_condicion_pago='$data[8]',
                                            orc_direccion_entrega='$data[9]',
                                            usu_id=$_SESSION[usuid],
                                            orc_guia_recepcion='$data[10]',
                                            orc_documento='$data[11]',
                                            orc_iva=$data[12]

                                            WHERE  orc_codigo='$data[2]'   ");
        }
    }

    function upd_orden_compra_estado($sts, $obs, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_enc_orden_compra_mp SET orc_estado=$sts, orc_obs='$obs'   WHERE  orc_id=$id   ");
        }
    }

    function upd_factura_orden_compra($fac, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_enc_orden_compra_mp  SET   orc_factura='$fac'   WHERE  orc_codigo='$id'   ");
        }
    }

    function del_orden_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE from erp_i_enc_orden_compra_mp WHERE  orc_id=$id  ");
        }
    }

//Detalle de orden de compra   
    function lista_det_orden_compra($orc_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
erp_i_det_orden_compra_mp doc,
erp_i_enc_orden_compra_mp oc,
erp_i_mp mp
where doc.orc_id=oc.orc_id
and doc.mp_id=mp.mp_id
and oc.orc_id=$orc_id");
        }
    }

    function lista_det_orden_compra_oc_mp($orc_id, $mp_id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from 
erp_i_det_orden_compra_mp doc,
erp_i_enc_orden_compra_mp oc,
erp_i_mp mp
where doc.orc_id=oc.orc_id
and doc.mp_id=mp.mp_id
and doc.orc_id=$orc_id 
and doc.mp_id=$mp_id        ");
        }
    }

    function insert_det_orden_compra($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_det_orden_compra_mp 
                                            (orc_id,
                                             mp_id,
                                             orc_det_cant,
                                             orc_det_vu,
                                             orc_det_vt )VALUES(
                                            $data[0],
                                            $data[1],
                                            $data[2],
                                            $data[3],    
                                            $data[4] ) ");
        }
    }

    function upd_det_orden_compra($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_det_orden_compra_mp SET
                                             orc_id=$data[0],
                                             mp_id=$data[1],
                                             orc_det_cant=$data[2],
                                             orc_det_vu=$data[3],
                                             orc_det_vt=$data[4] where orc_det_id=$id ");
        }
    }

    function upd_guia_det_orden_compra($guia, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_det_orden_compra_mp SET orc_det_guia='$guia' where orc_det_id=$id ");
        }
    }

    function del_det_orden_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_det_orden_compra_mp where orc_det_id=$id ");
        }
    }

    function del_det_orden_compra_orc($orc) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_det_orden_compra_mp where orc_id=$orc ");
        }
    }

    function insert_etq_orden($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_etq_orden 
                                              ( orc_det_id,
                                                etq_cant,
                                                etq_peso,
                                                etq_fecha,
                                                etq_bar_code
                                                 )VALUES($data[0],
                                                                 $data[1],
                                                                 $data[2],
                                                                '$data[3]',
                                                                '$data[4]' ) ");
        }
    }

    function lista_etq_orden($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_etq_orden etq,
erp_i_det_orden_compra_mp ord,
erp_i_mp mp
WHERE ord.orc_det_id=etq.orc_det_id
AND   ord.mp_id=mp.mp_id
AND   etq.orc_det_id=$id");
        }
    }

    function lista_etq_orden_total($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(etq_peso) AS peso,mp.mp_referencia,etq.etq_fecha,etq.etq_bar_code
FROM erp_i_etq_orden etq,
erp_i_det_orden_compra_mp ord,
erp_i_mp mp
WHERE ord.orc_det_id=etq.orc_det_id
AND   ord.mp_id=mp.mp_id
AND   etq.orc_det_id=$id GROUP BY mp.mp_referencia,etq.etq_fecha,etq.etq_bar_code ");
        }
    }

    function lista_etq_orden_mov($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_etq_orden etq,
erp_i_mov_inventario mov,
erp_i_mp mp
WHERE mov.mov_id=etq.orc_det_id
AND   mov.mp_id=mp.mp_id
AND   etq.orc_det_id=$id");
        }
    }

    function lista_etq_orden_total_mov($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(etq_peso) AS peso,mp.mp_referencia,etq.etq_fecha,etq.etq_bar_code
FROM erp_i_etq_orden etq,
erp_i_mov_inventario mov,
erp_i_mp mp
WHERE mov.mov_id=etq.orc_det_id
AND   mov.mp_id=mp.mp_id
AND   etq.orc_det_id=$id GROUP BY mp.mp_referencia,etq.etq_fecha,etq.etq_bar_code ");
        }
    }

//Clientes
//////////////////////////////// Cumplimiento /////////////////////////////////////

    function insertar_cumplimiento($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cumplimiento (
                                cum_codigo,
                                cum_descripcion,
                                cum_descuento )VALUES(
                                '$data[0]',
                                '$data[1]',
                                 $data[2])");
        }
    }

    function lista_cumplimiento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cumplimiento ORDER BY cum_codigo ");
        }
    }

    function lista_cumplimiento_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cumplimiento where cum_codigo like '%$txt%' ORDER BY cum_codigo ");
        }
    }

    function lista_un_cumplimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cumplimiento WHERE cum_id=$id");
        }
    }

    function modificar_cumplimiento($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cumplimiento 
                            SET     cum_codigo='$data[0]', 
                                    cum_descripcion='$data[1]', 
                                    cum_descuento=$data[2]     
                            WHERE   cum_id=$id     ");
        }
    }

    function delete_cumplimiento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cumplimiento  WHERE cum_id=$id ");
        }
    }

/////////////////////////////////////////////////////////////////////////////////////// 
///////////////////////////////// Tipo de Pago ////////////////////////////////////////    

    function insertar_tipo_de_pago($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_tip_pago (                                
                                tip_codigo,
                                tip_descripcion,
                                tip_descuento )VALUES(
                                '$data[0]',
                                '$data[1]',
                                 $data[2])");
        }
    }

    function lista_tipo_de_pago() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tip_pago ORDER BY tip_codigo ");
        }
    }

    function lista_tipo_de_pago_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tip_pago where tip_codigo like '%$txt%' ORDER BY tip_codigo ");
        }
    }

    function lista_un_tipo_de_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_tip_pago WHERE tip_id=$id");
        }
    }

    function modificar_tipo_de_pago($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_tip_pago
                            SET     tip_codigo='$data[0]', 
                                    tip_descripcion='$data[1]', 
                                    tip_descuento=$data[2]     
                            WHERE   tip_id=$id     ");
        }
    }

    function delete_tipo_de_pago($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_tip_pago  WHERE tip_id=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////   
///////////////////////////////// Capacidad de Compra ////////////////////////////////////////    
    function insertar_capacidad_de_compra($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_capacidad_compra (                                                       
                                cap_codigo,
                                cap_monto_maximo,
                                cap_monto_minimo,
                                cap_descuento )VALUES(
                                '$data[0]',
                                $data[1],
                                $data[2],
                                $data[3])");
        }
    }

    function lista_capacidad_de_compra() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_capacidad_compra ORDER BY cap_codigo ");
        }
    }

    function lista_capacidad_compra_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_capacidad_compra where cap_codigo like '%$txt' ORDER BY cap_codigo ");
        }
    }

    function lista_una_capacidad_de_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_capacidad_compra WHERE cap_id=$id");
        }
    }

    function modificar_capacidad_de_compra($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_capacidad_compra
                            SET     cap_codigo='$data[0]', 
                                    cap_monto_maximo=$data[1], 
                                    cap_monto_minimo=$data[2],
                                    cap_descuento=$data[3]
                            WHERE   cap_id=$id     ");
        }
    }

    function delete_capacidad_de_compra($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_capacidad_compra  WHERE cap_id=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////       
///////////////////////////////// Capacidad de Compra ////////////////////////////////////////    

    function insertar_descuentos($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_descuentos (                                                           
                                des_codigo,
                                des_cap_compra,
                                des_tip_pago,
                                des_cumplimiento,
                                des_des_total )VALUES(
                                '$data[0]',
                                $data[1],
                                $data[2],
                                $data[3],
                                $data[4])");
        }
    }

    function lista_descuentos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_descuentos ORDER BY des_codigo ");
        }
    }

    function lista_un_descuento($id) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_descuentos WHERE des_id=$id");
        }
    }

    function modificar_descuentos($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_descuentos
                            SET     des_codigo='$data[0]', 
                                    des_cap_compra=$data[1], 
                                    des_tip_pago=$data[2],
                                    des_cumplimiento=$data[3],
                                    des_des_total=$data[4]    
                            WHERE   des_id=$id     ");
        }
    }

    function delete_descuentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_descuentos  WHERE des_id=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////       
///////////////////////////////// Direccion de Entrega CLIENTES ////////////////////////////////////////    
    function insertar_direccion_entrega($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cli_direccion_entrega (                   
                                  cde_local,
                                  cde_apellido,
                                  cde_nombre,
                                  cde_telefono,
                                  cde_celular,
                                  cde_pais,
                                  cde_provincia,
                                  cde_parroquia,
                                  cde_canton,
                                  cde_cal_principal,
                                  cde_numeracion,
                                  cde_cal_secundaria,
                                  cde_referencia,
                                  cli_id)VALUES(
                                '$data[0]',
                                '$data[1]',
                                '$data[2]',
                                '$data[3]',
                                '$data[4]',
                                '$data[5]',
                                '$data[6]',
                                '$data[7]',
                                '$data[8]',
                                '$data[9]',
                                '$data[10]',
                                '$data[11]',
                                '$data[12]',$data[13])");
        }
    }

    function lista_direccion_entrega($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cli_direccion_entrega WHERE cli_id=$id ORDER BY cde_id ");
        }
    }

    function lista_una_direccion_entrega($id) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cli_direccion_entrega ORDER BY cde_id=$id");
        }
    }

    function delete_direccion_entrega($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cli_direccion_entrega WHERE cde_id=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////  
///////////////////////////////// Direccion de Entrega CLIENTES ////////////////////////////////////////    
    function insertar_clientes($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente (      
                                  cli_fecha,
                                  cli_codigo,
                                  cli_estado,
                                  cli_tipo,
                                  cli_nombre,
                                  cli_ced_ruc,
                                  cli_raz_social,
                                  cli_cup_maximo,
                                  cli_cat_cliente,
                                  cli_pais,
                                  cli_provincia,
                                  cli_parroquia,
                                  cli_canton,
                                  cli_cal_principal,
                                  cli_numeracion,
                                  cli_cal_secundaria,
                                  cli_telefono,
                                  cli_referencia,
                                  cli_rep_apellido,
                                  cli_rep_nombre,
                                  cli_rep_telefono,
                                  cli_rep_celular,
                                  cli_rep_email,
                                  cli_con_apellido,
                                  cli_con_nombre,
                                  cli_con_telefono,
                                  cli_con_celular,
                                  cli_con_email,
                                  cli_documento,
                                  tp_id)VALUES(
                                '$data[0]',
                                '$data[1]',
                                '$data[2]',
                                '$data[3]',
                                '$data[4]',
                                '$data[5]',
                                '$data[6]',
                                 $data[7],
                                '$data[8]',
                                '$data[9]',
                                '$data[10]',                                                               
                                '$data[11]',
                                '$data[12]',
                                '$data[13]',
                                '$data[14]',
                                '$data[15]',
                                '$data[16]',
                                '$data[17]',
                                '$data[18]',
                                '$data[19]',
                                '$data[20]',                                                                     
                                '$data[21]',
                                '$data[22]',
                                '$data[23]',
                                '$data[24]',
                                '$data[25]',
                                '$data[26]',
                                '$data[27]',
                                '$data[28]',
                                 $data[29] )");
        }
    }

    function modificar_clientes($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente
                            SET     
                                  cli_fecha='$data[0]',                                  
                                  cli_codigo='$data[1]',
                                  cli_estado='$data[2]',
                                  cli_tipo='$data[3]',
                                  cli_nombre='$data[4]',                                  
                                  cli_ced_ruc='$data[5]', 
                                  cli_raz_social='$data[6]', 
                                  cli_pais='$data[9]', 
                                  cli_provincia='$data[10]', 
                                  cli_parroquia='$data[11]', 
                                  cli_canton='$data[12]', 
                                  cli_cal_principal='$data[13]', 
                                  cli_numeracion='$data[14]', 
                                  cli_cal_secundaria='$data[15]', 
                                  cli_telefono='$data[16]', 
                                  cli_referencia='$data[17]', 
                                  cli_rep_apellido='$data[18]', 
                                  cli_rep_nombre='$data[19]', 
                                  cli_rep_telefono='$data[20]', 
                                  cli_rep_celular='$data[21]', 
                                  cli_rep_email='$data[22]', 
                                  cli_con_apellido='$data[23]', 
                                  cli_con_nombre='$data[24]', 
                                  cli_con_telefono='$data[25]', 
                                  cli_con_celular='$data[26]', 
                                  cli_con_email='$data[27]',
                                  cli_documento='$data[28]',
                                  tp_id=$data[29]     
                                      
                            WHERE   cli_codigo='$id'     ");
        }
    }

    function lista_clientes() {
        if ($this->con->Conectar() == true) {
            return pg_query("select *  from  erp_i_cliente Order by cli_codigo ");
        }
    }

    function lista_clientes_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_cliente where 
                cli_codigo like '%$txt%' 
                    or cli_ced_ruc like '%$txt%'  
                        or cli_nombres like '%$txt%' 
                            or cli_apellidos like '%$txt%' 
                                or cli_raz_social like '%$txt%' 
                            
                            Order by cli_raz_social");
        }
    }

    function lista_clientes_codigo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente where cli_ced_ruc='$id' ");
        }
    }

    function lista_clientes_tipo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_clientes_tipo_codigo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_codigo,cli_ced_ruc, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_un_cliente($id) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_id=$id");
        }
    }

    function lista_un_cliente_codigo($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_codigo='$cod'");
        }
    }

    function lista_un_cliente_cedula($cod) {// sirve para cuando selecciono un registro para modificar
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cliente WHERE cli_ced_ruc='$cod'");
        }
    }

    function lista_aprobaciones() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_aprobacion apb, erp_i_cliente cl where apb.cli_codigo=cl.cli_codigo order by apb_fecha_reg");
        }
    }

    function lista_aprobaciones_status($sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_aprobacion apb, erp_i_cliente cl where apb.cli_codigo=cl.cli_codigo and apb.apb_sts=$sts order by apb_fecha_reg");
        }
    }

    function lista_una_aprobaciones($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_aprobacion apb, erp_i_cliente cl where apb.cli_codigo=cl.cli_codigo and apb.apb_id=$id ");
        }
    }

    function delete_clientes($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cliente WHERE cli_id=$id ");
        }
    }

    function insert_cambio_clientes($id, $cambio, $campo) {
        if ($this->con->Conectar() == true) {
            $fecha = date("Y-m-d");
            $usu_id = $_SESSION[usuid];
            return pg_query("INSERT INTO erp_i_aprobacion
                (cli_codigo,
                apb_cambio,
                abp_campo,apb_fecha_reg,apb_solicita)VALUES('$id','$cambio','$campo','$fecha',$usu_id)                
                
                ");
        }
    }

    function upd_aprobaciones($id, $sts) {
        if ($this->con->Conectar() == true) {
            $usu_id = $_SESSION[usuid];
            return pg_query("UPDATE erp_i_aprobacion SET apb_sts=$sts, apb_autoriza=$usu_id where apb_id=$id ");
        }
    }

    function upd_aprobaciones_clientes($id, $campo, $cambio) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET $campo='$cambio' where cli_id=$id ");
        }
    }

    function delete_aprobaciones($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_aprobacion WHERE cli_codigo='$code' ");
        }
    }

//////////////////////////////********CUPOS*******//////////////////////////////////  
    function lista_cupos() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cupos cu, erp_users us where cu.usu_id=us.usu_id order by usu_person");
        }
    }

    function lista_un_cupo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cupos cu, erp_users us where cu.usu_id=us.usu_id and cu.cup_id=$id");
        }
    }

    function lista_un_cupo_usuid($usuid) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cupos cu, erp_users us where cu.usu_id=us.usu_id and us.usu_id=$usuid");
        }
    }

    function lista_un_cupo_user($user) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_cupos cu, erp_users us where cu.usu_id=us.usu_id and usu.usu_person like '%$user%'");
        }
    }

    function insert_cupo($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cupos (usu_id,cup_mensual,cup_xorden)VALUES($data[0],$data[1],$data[2])");
        }
    }

    function upd_cupo($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cupos SET  usu_id=$data[0],cup_mensual=$data[1],cup_xorden=$data[2] WHERE cup_id=$id");
        }
    }

    function del_cupo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_cupos WHERE cup_id=$id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////  
///////////////////////////////// Producto ///////////////////////////////////////////  

    function insertar_producto($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_productos (
                                  pro_codigo,
                                  pro_descripcion,
                                  pro_gramaje,
                                  pro_largo,
                                  pro_ancho,
                                  pro_peso,
                                  emp_id,
                                  pro_mp1,
                                  pro_mp2,
                                  pro_mp3,
                                  pro_mp4,
                                  pro_mf1,
                                  pro_mf2,
                                  pro_mf3,
                                  pro_mf4,
                                  pro_mftotal)VALUES(
                                '$data[0]',
                                '$data[1]',
                                 $data[2],
                                 $data[3],
                                 $data[4],
                                 $data[5],
                                 $data[6],
                                 $data[7],
                                 $data[8],
                                 $data[9],
                                 $data[10],
                                 $data[11],
                                 $data[12],
                                 $data[13],
                                 $data[14],
                                 $data[15])");
        }
    }

    function lista_productos_codigo($fbc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_codigo FROM  erp_i_productos  
                                WHERE emp_id=$fbc order by pro_codigo desc limit 1");
        }
    }

    function lista_matprima($fbc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mp mp,
                erp_empresa em 
                WHERE em.emp_id=fbc_id
                AND   mp.fbc_id=$fbc
                ORDER BY mp.mp_id ");
        }
    }

    function lista_producto() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos ORDER BY pro_codigo ");
        }
    }

    function lista_producto_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos where pro_codigo like '%$txt%' ORDER BY pro_codigo ");
        }
    }

    function lista_un_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos WHERE pro_id=$id");
        }
    }

    function lista_detalle_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro.pro_ancho,
                              pro.pro_mp1,
                              pro.pro_mp2,
                              pro.pro_mp3,
                              pro.pro_mp4,
                              pro.pro_mf1,
                              pro.pro_mf2,
                              pro.pro_mf3,
                              pro.pro_mf4,
                              pro.pro_mftotal 
                              FROM  erp_i_productos pro
                              WHERE pro_id=pro_id                                     
                              AND   pro.pro_id=$id");
        }
    }

    function modificar_producto($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE  erp_i_productos
                            SET     
                                  pro_codigo='$data[0]',                                  
                                  pro_descripcion='$data[1]',
                                  pro_gramaje=$data[2],
                                  pro_largo=$data[3],
                                  pro_ancho=$data[4],                                  
                                  pro_peso=$data[5], 
                                  emp_id=$data[6], 
                                  pro_mp1=$data[7], 
                                  pro_mp2=$data[8], 
                                  pro_mp3=$data[9], 
                                  pro_mp4=$data[10], 
                                  pro_mf1=$data[11], 
                                  pro_mf2=$data[12], 
                                  pro_mf3=$data[13], 
                                  pro_mf4=$data[14], 
                                  pro_mftotal=$data[15] 
                                  WHERE pro_id=$id");
        }
    }

    function delete_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_productos WHERE pro_id=$id ");
        }
    }

    function lista_productos_faltantes($faltante, $gramaje) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_productos WHERE pro_ancho <= $faltante and pro_gramaje= $gramaje ORDER BY pro_ancho desc");
        }
    }

///////////////////////////////// Orden de Produccion ECOCAMBRELLA////////////////////////////////////////    

    function insertar_orden_produccion($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion (  
                                  ord_num_orden,
                                  cli_id,
                                  pro_id,
                                  ord_num_rollos,
                                  ord_mp1,
                                  ord_mp2,
                                  ord_mp3,
                                  ord_mp4,
                                  ord_mf1,
                                  ord_mf2,
                                  ord_mf3,
                                  ord_mf4,
                                  ord_mftotal,
                                  ord_kg1,
                                  ord_kg2,
                                  ord_kg3,
                                  ord_kg4,
                                  ord_kgtotal,
                                  ord_fec_pedido,
                                  ord_fec_entrega,
                                  ord_anc_total,
                                  ord_refilado,                                  
                                  ord_pri_ancho,
                                  ord_pri_carril,
                                  ord_pri_faltante,
                                  ord_pro_secundario,
                                  ord_sec_ancho,
                                  ord_sec_carril,
                                  ord_rep_ancho,
                                  ord_rep_carril,
                                  ord_largo,
                                  ord_gramaje,
                                  ord_zo1,
                                  ord_zo2,
                                  ord_zo3,
                                  ord_zo4,
                                  ord_zo5,
                                  ord_zo6,
                                  ord_spi_temp,
                                  ord_upp_rol_tem_controller,
                                  ord_dow_rol_tem_controller,
                                  ord_spi_tem_controller,
                                  ord_coo_air_temp,
                                  ord_upp_rol_heating,
                                  ord_upp_rol_oil_pump,
                                  ord_dow_rol_heating,
                                  ord_dow_rol_oil_pump,
                                  ord_spi_rol_heating,
                                  ord_spi_rol_oil_pump,
                                  ord_mat_pump,
                                  ord_spi_blower,
                                  ord_sid_blower,
                                  ord_dra_blower,
                                  ord_gsm_setting,
                                  ord_aut_spe_adjust,
                                  ord_spe_mod_auto,
                                  ord_lap_speed,
                                  ord_man_spe_setting,
                                  ord_rol_mill,
                                  ord_win_tensility,
                                  ord_mas_bra_autosetting,
                                  ord_rol_mil_up_down,
                                  ord_observaciones,
                                  ord_mp5,
                                  ord_mp6,
                                  ord_mf5,
                                  ord_mf6,
                                  ord_kg5,
                                  ord_kg6  )VALUES(
                                   '$data[0]',
                                    $data[1],
                                    $data[2],
                                    $data[3],
                                    $data[4],
                                    $data[5],
                                    $data[6],
                                    $data[7],
                                    $data[8],
                                    $data[9],
                                    $data[10],
                                    $data[11],                                                               
                                    $data[12],
                                    $data[13],
                                    $data[14],
                                    $data[15],
                                    $data[16],
                                    $data[17],
                                   '$data[18]',
                                   '$data[19]',
                                    $data[20],
                                    $data[21],                                                                     
                                    $data[22],
                                    $data[23],
                                    $data[24],
                                    $data[25],
                                    $data[26],
                                    $data[27],
                                    $data[28],
                                    $data[29],
                                    $data[30],
                                    $data[31],
                                    '$data[32]',
                                   '$data[33]',
                                   '$data[34]',
                                   '$data[35]',
                                   '$data[36]',                            
                                   '$data[37]',
                                   '$data[38]',
                                   '$data[39]',                                                            
                                   '$data[40]',
                                   '$data[41]',
                                   '$data[42]',     
                                   '$data[43]',
                                   '$data[44]',
                                   '$data[45]',     
                                   '$data[46]',
                                   '$data[47]',
                                   '$data[48]',
                                   '$data[49]',
                                   '$data[50]',                            
                                   '$data[51]',
                                   '$data[52]',
                                   '$data[53]',                                                            
                                   '$data[54]',
                                   '$data[55]',
                                   '$data[56]',     
                                   '$data[57]',
                                   '$data[58]',
                                   '$data[59]',     
                                   '$data[60]',
                                   '$data[61]',
                                   '$data[62]',
                                    $data[63],
                                    $data[64],
                                    $data[65],
                                    $data[66],
                                    $data[67],
                                    $data[68]  )");
        }
    }

    function lista_secuencial_orden_produccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_orden_produccion Order by ord_num_orden desc limit 1");
        }
    }

    function lista_orden_produccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion ORDER BY ord_id ");
        }
    }

    function lista_una_orden_produccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion WHERE ord_id=$id");
        }
    }

    function lista_una_orden_produccion_numero_orden($ord_num_orden) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion WHERE ord_num_orden='$ord_num_orden'");
        }
    }

    function modificar_orden_produccion($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion
                          SET                          
                            ord_num_orden='$data[0]',                  
                              cli_id=$data[1],
                              pro_id=$data[2],
                              ord_num_rollos=$data[3],
                              ord_mp1=$data[4],
                              ord_mp2=$data[5],
                              ord_mp3=$data[6],
                              ord_mp4=$data[7],
                              ord_mf1=$data[8],
                              ord_mf2=$data[9],
                              ord_mf3=$data[10],
                              ord_mf4=$data[11],
                              ord_mftotal=$data[12],
                              ord_kg1=$data[13],
                              ord_kg2=$data[14],
                              ord_kg3=$data[15],
                              ord_kg4=$data[16],
                              ord_kgtotal=$data[17],
                              ord_fec_pedido='$data[18]',                               
                              ord_fec_entrega='$data[19]',
                              ord_anc_total=$data[20],
                              ord_refilado=$data[21],                              
                              ord_pri_ancho=$data[22],
                              ord_pri_carril=$data[23],
                              ord_pri_faltante=$data[24],
                              ord_pro_secundario=$data[25],
                              ord_sec_ancho=$data[26],
                              ord_sec_carril=$data[27],
                              ord_rep_ancho=$data[28],
                              ord_rep_carril=$data[29],
                              ord_largo=$data[30],
                              ord_gramaje =$data[31],
                              ord_zo1='$data[32]',
                              ord_zo2='$data[33]',
                              ord_zo3='$data[34]',
                              ord_zo4='$data[35]',
                              ord_zo5='$data[36]',
                              ord_zo6='$data[37]',
                              ord_spi_temp='$data[38]',
                              ord_upp_rol_tem_controller='$data[39]',
                              ord_dow_rol_tem_controller='$data[40]',
                              ord_spi_tem_controller='$data[41]',
                              ord_coo_air_temp='$data[42]',
                              ord_upp_rol_heating='$data[43]',
                              ord_upp_rol_oil_pump='$data[44]',
                              ord_dow_rol_heating='$data[45]',
                              ord_dow_rol_oil_pump='$data[46]',
                              ord_spi_rol_heating='$data[47]',
                              ord_spi_rol_oil_pump='$data[48]',
                              ord_mat_pump='$data[49]',
                              ord_spi_blower='$data[50]',
                              ord_sid_blower='$data[51]',
                              ord_dra_blower='$data[52]',
                              ord_gsm_setting='$data[53]',
                              ord_aut_spe_adjust='$data[54]',
                              ord_spe_mod_auto='$data[55]',
                              ord_lap_speed='$data[56]',
                              ord_man_spe_setting='$data[57]',
                              ord_rol_mill='$data[58]',
                              ord_win_tensility='$data[59]',
                              ord_mas_bra_autosetting='$data[60]',
                              ord_rol_mil_up_down='$data[61]',
                              ord_observaciones='$data[62]',
                                  ord_mp5=$data[63],
                                  ord_mp6=$data[64],
                                  ord_mf5=$data[65],
                                  ord_mf6=$data[66],
                                  ord_kg5=$data[67],
                                  ord_kg6=$data[68]                                  
                            WHERE   ord_id=$id");
        }
    }

    function delete_orden_produccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion WHERE ord_id=$id ");
        }
    }

    function lista_un_orden_produccion_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro.pro_ancho,
                                  pro.pro_mp1,
                                  pro.pro_mp2,
                                  pro.pro_mp3,
                                  pro.pro_mp4,
                                  pro.pro_mf1,
                                  pro.pro_mf2,
                                  pro.pro_mf3,
                                  pro.pro_mf4,
                                  pro.pro_mftotal 
                                  FROM  erp_i_productos pro
                                  WHERE pro_id=pro_id                                     
                                  AND   pro.pro_id=$id ");
        }
    }

    function lista_utlimo_seteo_maquina($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion WHERE pro_id=$id ORDER BY ord_fec_pedido desc limit 1 ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Reporte Produccion //////////////////////////////////    

    function insertar_reporte_produccion($data) {
        if ($this->con->Conectar() == true) {
            return pg_query(" INSERT INTO erp_i_reporte_produccion(   
                ord_id, 
                rep_fecha, 
                rep_pes_principal,
                rep_pes_secunadario, 
                rep_pes_reproceso, 
                rep_gramaje,
                rep_operador,
                rep_maquina
            )VALUES(
                 $data[0],
                '$data[1]',
                 $data[2],
                 $data[3],
                 $data[4],
                 $data[5],
                '$data[6]',
                '$data[7]')");
        }
    }

    function lista_reporte_produccion() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_reporte_produccion ORDER BY rep_id ");
        }
    }

    function lista_reporte_produccion_pedido($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(rep_pes_principal) FROM erp_i_reporte_produccion where ord_id=$id");
        }
    }

    function lista_reporte_produccion_search($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_reporte_produccion where rep_num_orden like '%$txt%' ORDER BY rep_id ");
        }
    }

    function lista_un_reporte_produccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_reporte_produccion WHERE rep_id=$id");
        }
    }

    function modificar_reporte_produccion($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE  erp_i_reporte_produccion
                            SET 
                                  ord_id=$data[0],                                  
                                  rep_fecha='$data[1]',     
                                  rep_pes_principal=$data[2], 
                                  rep_pes_secunadario=$data[3], 
                                  rep_pes_reproceso=$data[4], 
                                  rep_gramaje=$data[5], 
                                  rep_operador='$data[6]', 
                                  rep_maquina='$data[7]' 
                                  WHERE rep_id=$id");
        }
    }

    function delete_reporte_produccion($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_reporte_produccion WHERE rep_id=$id ");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Orden de Produccion - plumon ////////////////////////////////////////    
    function insertar_orden_produccion_plumon($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_orden_produccion_plumon (                                    
                                    orp_num_pedido,
                                    cli_id,
                                    pro_id,
                                    orp_pro_ancho,
                                    orp_pro_largo,
                                    orp_pro_peso,
                                    orp_pro_gramaje,
                                    orp_cantidad, 
                                    orp_mp1, 
                                    orp_mp2, 
                                    orp_mp3, 
                                    orp_mp4, 
                                    orp_mf1, 
                                    orp_mf2, 
                                    orp_mf3, 
                                    orp_mf4, 
                                    orp_mftotal,
                                    orp_kg1, 
                                    orp_kg2,
                                    orp_kg3,
                                    orp_kg4, 
                                    orp_kgtotal,
                                    orp_fec_pedido, 
                                    orp_fec_entrega, 
                                    orp_capa,
                                    orp_espesor,
                                    orp_med_vueltas,
                                    orp_paquetes,
                                    orp_temperatura,
                                    orp_agua,
                                    orp_resina,
                                    orp_observaciones)VALUES(
                                   '$data[0]',
                                    $data[1],
                                    $data[2],
                                    $data[3],
                                    $data[4],
                                    $data[5],
                                    $data[6],
                                    $data[7],
                                    $data[8],
                                    $data[9],
                                    $data[10],
                                    $data[11],                                                               
                                    $data[12],
                                    $data[13],
                                    $data[14],
                                    $data[15],
                                    $data[16],
                                    $data[17],
                                    $data[18],
                                    $data[19],
                                    $data[20],
                                    $data[21],                                                                     
                                   '$data[22]',
                                   '$data[23]',
                                    $data[24],
                                    $data[25],
                                    $data[26],
                                   '$data[27]',
                                    $data[28],
                                    $data[29],
                                    $data[30],
                                   '$data[31]')");
        }
    }

    function modificar_orden_produccion_plumon($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_orden_produccion_plumon SET                         
                            orp_num_pedido='$data[0]',
                            cli_id=$data[1],
                            pro_id=$data[2],
                            orp_pro_ancho=$data[3],
                            orp_pro_largo=$data[4],
                            orp_pro_peso=$data[5],
                            orp_pro_gramaje=$data[6],
                            orp_cantidad=$data[7],
                            orp_mp1=$data[8], 
                            orp_mp2=$data[9], 
                            orp_mp3=$data[10], 
                            orp_mp4=$data[11],
                            orp_mf1=$data[12], 
                            orp_mf2=$data[13], 
                            orp_mf3=$data[14], 
                            orp_mf4=$data[15], 
                            orp_mftotal=$data[16], 
                            orp_kg1=$data[17],
                            orp_kg2=$data[18], 
                            orp_kg3=$data[19], 
                            orp_kg4=$data[20],
                            orp_kgtotal=$data[21],
                            orp_fec_pedido='$data[22]',
                            orp_fec_entrega='$data[23]',
                            orp_capa=$data[24], 
                            orp_espesor=$data[25], 
                            orp_med_vueltas=$data[26], 
                            orp_paquetes='$data[27]', 
                            orp_temperatura=$data[28], 
                            orp_agua=$data[29],
                            orp_resina=$data[30], 
                            orp_observaciones='$data[31]'
                            WHERE   orp_id=$id");
        }
    }

    function delete_orden_produccion_plumon($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_orden_produccion_plumon WHERE orp_id=$id ");
        }
    }

    function lista_orden_produccion_plumon() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_plumon ORDER BY orp_id ");
        }
    }

    function lista_una_orden_produccion_plumon($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_plumon WHERE orp_id=$id");
        }
    }

    function lista_secuencial_orden_produccion_plumon() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_i_orden_produccion_plumon Order by orp_num_pedido desc limit 1");
        }
    }

    function lista_una_orden_produccion_numero_orden_plumon($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_orden_produccion_plumon WHERE orp_num_pedido='$txt'");
        }
    }

    function lista_un_orden_produccion_producto_plumon($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro.pro_ancho,
                                  pro.pro_mp1,
                                  pro.pro_mp2,
                                  pro.pro_mp3,
                                  pro.pro_mp4,
                                  pro.pro_mf1,
                                  pro.pro_mf2,
                                  pro.pro_mf3,
                                  pro.pro_mf4,
                                  pro.pro_mftotal 
                                  FROM  erp_i_productos pro
                                  WHERE pro_id=pro_id                                     
                                  AND   pro.pro_id=$id ");
        }
    }

    function lista_utlimo_seteo_maquina_plumon($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_orden_produccion_plumon WHERE pro_id=$id ORDER BY orp_fec_pedido desc limit 1 ");
        }
    }

/////////////////////////////////////////////PRODUCTOS INDUSTRIAL//////////////////////////////////////////        
    function lista_productos_industrial() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos ORDER BY pro_descripcion ");
        }
    }

    /////////////////////////////////////////////PRODUCTOS COMERCIALES MODIFICAION OMAR//////////////////////////////////////////    
    function lista_productos_noperti() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos ORDER BY pro_b");
        }
    }

    ////cambios cristina
    function lista_productos_industrial_like() {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_i_productos where pro_descripcion like '%PADDING%' or pro_descripcion like '%PLUMON%'   ORDER BY pro_descripcion ");
            return pg_query("SELECT * FROM  erp_i_productos where emp_id =4 or emp_id=3   ORDER BY pro_descripcion ");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$code'");
        }
    }

    function lista_un_producto_industrial_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where id=$code and ids<>79 and ids<>80");
        }
    }

    ////////////////////////////// MODIFICACION OMAR FACTURA ///////////////////////////   
//    function lista_producto_total($ems) {
//        if ($this->con->Conectar() == true) {
//
//            if ($ems == 1) { //Nopeti (todos los comerciales + paddin y plumos)
//                $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos 
//                           union
//                           SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos where emp_id=3 or emp_id=4) order by descripcion";
//            } elseif ($ems == 10) { //Industrial solo los industriales
//                $query = "(SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos) order by descripcion";
//            } else { //Locales todos
//                $query = "(SELECT '1' as tbl,id as id,pro_ac as lote,pro_a as codigo,pro_b as descripcion FROM  erp_productos 
//                              union
//                              SELECT '0' as tbl, pro_id as id, '' as lote ,pro_codigo as codigo,pro_descripcion as descripcion FROM  erp_i_productos) order by descripcion";
//            }
//            return pg_query($query);
//        }
//    }

    function lista_un_producto_noperti($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' or cast(id AS VARCHAR)= '$code'");
        }
    }

    function lista_un_producto_noperti_id($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where id= $code");
        }
    }

    function lista_un_producto_noperti_cod_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$code' and pro_ac='$lote'");
        }
    }

    function lista_un_producto_noperti_lote($code, $lote) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_ac='$lote' and (pro_a='$code' or or cast(id AS VARCHAR)= '$code')");
        }
    }

    function lista_precio_producto($id, $tabla) {//////
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_pro_precios where pro_id=$id and pro_tabla=$tabla");
        }
    }

/////////////////////////////////////////////CLIENTE MODIFICACION OMAR//////////////////////////////////////////  
    function insert_cliente($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_cliente 
(
  cli_apellidos,
  cli_raz_social,
  cli_fecha,
  cli_estado,
  cli_tipo,
  cli_categoria,
  cli_ced_ruc,
  cli_calle_prin,
  cli_telefono,
  cli_email,
  cli_canton,
  cli_pais,
  cli_codigo,
cli_parroquia
) values ('$data[0]',
    '$data[0]',
'" . date('Y-m-d') . "',
    0,
    0,
    1,
'$data[1]',
'$data[2]',    
'$data[3]',
'$data[4]',
'$data[5]',
'$data[6]',
'$data[7]', 
'$data[8]')");
        }
    }

//////////////////////////////////  MODIFICACION OMAR //////////////////////////////////////////////////    
    function upd_email_cliente($data, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_cliente SET
            cli_calle_prin='$data[0]',
            cli_email='$data[1]',
            cli_telefono='$data[2]',
            cli_canton='$data[3]',
            cli_pais='$data[4]',
            cli_parroquia='$data[5]',
                cli_calle_sec='',
                cli_numeracion=''
            WHERE cli_ced_ruc='$id'");
        }
    }

/////////////////////////////////////////////FACTURAS//////////////////////////////////////////        
    ///CAMBIOS 30/12/2014
    function insert_factura($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO comprobantes 
(num_secuencial,
  nombre,
  identificacion,
  fecha_emision,
  num_guia_remision,
  cod_numerico,
  tipo_comprobante,
  subtotal12,
  subtotal0,
  subtotal_exento_iva,
  subtotal_no_objeto_iva,
  total_descuento,
  total_ice,
  total_iva,
  total_irbpnr,
  total_propina,
  total_valor,
  direccion_cliente,
  email_cliente,
  telefono_cliente,
  cli_ciudad,
  cli_pais,
  cod_establecimiento_emisor,
  cod_punto_emision,
  cli_parroquia,
  vendedor,
  num_documento,
  observaciones,
  ped_id,
  com_tbl
) values ($data[0],
'$data[1]',
'$data[2]',    
'$data[3]',
'$data[4]',    
'$data[5]',
'$data[6]',    
'$data[7]',
'$data[8]',    
'$data[9]',
'$data[10]',    
'$data[11]',
'$data[12]',    
'$data[13]',
'$data[14]',    
'$data[15]',
'$data[16]',    
'$data[17]',
'$data[18]',
'$data[19]',
'$data[20]',
'$data[21]', $data[22], $data[23], '$data[24]','$data[26]','$data[25]','$data[27]',$data[30],$data[31])               
                ");
        }
    }

    function insert_detalle_factura($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO detalle_fact_notdeb_notcre(
            num_camprobante,
            cod_producto,
            cod_aux,
            cantidad,
            descripcion, 
            detalle_adicional1,
            detalle_adicional2,
            precio_unitario,
            descuento, 
            precio_total,
            iva,
            ice,
            descuent
            )
    VALUES ('$data[0]',
            '$data[1]',
            '0',
            '$data[2]',
            '$data[3]',
            '$data[4]',
            '$data[5]',
            '$data[6]',
            '$data[7]',
            '$data[8]',
            '$data[9]',
            '$data[10]',
            '$data[11]')");
        }
    }

    function lista_factura($ems) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1 and cod_punto_emision=$ems order by com_id desc   ");
        }
    }

    function lista_factura_completo() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where tipo_comprobante=1  order by num_secuencial   ");
        }
    }

    function lista_una_factura($sec, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_secuencial=$sec and cod_punto_emision=$cod and tipo_comprobante=1 ");
        }
    }

    function lista_buscador_factura($txt, $tbl) {
        if ($this->con->Conectar() == true) {
            return pg_query("
                SELECT * FROM  comprobantes c
                WHERE  (c.nombre like '%$txt%' or c.identificacion like '%$txt%' or c.num_documento like '%$txt%' )
                AND c.cod_punto_emision=1 and c.tipo_comprobante=1 and com_tbl=$tbl
                    ");
        }
    }

    function lista_factura_fecha($from, $until, $cod, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where fecha_emision >=$from and fecha_emision <=$until  and cod_punto_emision=$cod and tipo_comprobante=1 and com_tbl=$tab ");
        }
    }

    function lista_una_factura_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where com_id=$id and tipo_comprobante=1");
        }
    }

    function lista_una_factura_nfact($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  comprobantes where num_documento='$id' and tipo_comprobante=1");
        }
    }

//////////////CAMBIOS 26/12/2014/////
    function lista_secuencial_documento($ems) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(num_documento,9,9) as secuencial FROM  comprobantes where substr(num_documento,1,3)='$ems' and substr(num_documento,5,3)='001' and tipo_comprobante=1 order by substr(num_documento,9,9) desc limit 1");
        }
    }

    function lista_secuencial_entrega() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT substr(num_documento,10,9) as secuencial FROM  comprobantes where tipo_comprobante=1 and com_tbl=1 order by substr(num_documento,10,9) desc limit 1");
        }
    }

    function lista_detalle_factura($fact) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  detalle_fact_notdeb_notcre where num_camprobante='$fact' and tipo_comprobante=1  ");
        }
    }

    function elimina_detalle_factura($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  detalle_fact_notdeb_notcre where num_camprobante='$num' and tipo_comprobante=1 ");
        }
    }

    function elimina_factura($nfact) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  comprobantes where num_documento='$nfact'  and tipo_comprobante=1 ");
        }
    }

//Movimoento de PT    

    function insert_movimiento_pt($data) {
//                print_r($data);
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
            pro_id,
            trs_id,
            cli_id,
            bod_id,
            mov_documento,
            mov_guia_transporte, 
            mov_num_trans,
            mov_fecha_trans,
            mov_fecha_registro,
            mov_hora_registro, 
            mov_cantidad,
            mov_tranportista,
            mov_fecha_entrega,
            mov_num_factura, 
            mov_pago,
            mov_direccion,
            mov_val_unit,
            mov_descuento,
            mov_iva, 
            mov_flete,
            mov_tabla,
            mov_val_tot)
            values(
$data[0],            
$data[1],
$data[2],
$data[3],            
'$data[4]',
'$data[5]',
'$data[6]',            
'$data[7]',
'$data[8]',
'$data[9]',            
'$data[10]',
'$data[11]',
'$data[12]',            
'$data[13]',
'$data[14]',
'$data[15]',            
'$data[16]','$data[17]','$data[18]','$data[19]', $data[20],'$data[21]')
");
        }
    }

    function elimina_movpt_documento($num) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM  erp_i_mov_inv_pt where mov_documento='$num' ");
        }
    }

///*************EMISOR*************************    

    function lista_emisor($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * FROM  erp_emisor where emi_cod_punto_emision=$cod ");
        }
    }

    //ARCHIVOS XML ********************************************************
    function upd_fac_clave_acceso($clave, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set clave_acceso='$clave'  where com_id=$id ");
        }
    }

    function upd_fac_num_fact($nfact, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set num_documento='$nfact'  where com_id=$id ");
        }
    }

    function upd_fac_na($na, $fh, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set com_estado='RECIBIDA AUTORIZADO', fecha_hora_autorizacion='$fh' , com_autorizacion='$na'  where clave_acceso='$id' ");
        }
    }

    function upd_fac_nd_nc($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes 
                set clave_acceso='$dat[0]', 
                com_estado='$dat[1] $dat[2]', 
                com_observacion='$dat[3]', 
                com_autorizacion='$dat[4]',
                fecha_hora_autorizacion='$dat[5]',                    
                xml_doc='$dat[6]'                        
                where com_id=$id ");
        }
    }

    function upd_retencion($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update detalle_retencion 
                set clave_acceso='$dat[0]', 
                com_estado='$dat[1] $dat[2]', 
                com_observacion='$dat[3]', 
                com_autorizacion='$dat[4]',
                fecha_hora_autorizacion='$dat[5]'                    
                where num_comprobante='$id' ");
        }
    }

    function upd_gui_rem($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update guia_remision 
                set clave_acceso='$dat[0]', 
                com_estado='$dat[1] $dat[2]', 
                com_observacion='$dat[3]', 
                com_autorizacion='$dat[4]',
                fecha_hora_autorizacion='$dat[5]'                    
                where num_comprobante='$id' ");
        }
    }

    function lista_obs_documentos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from comprobantes  where com_id=$id ");
        }
    }

    function upd_env_ema($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set com_estado_correo='$dat' where num_documento='$id'");
        }
    }

    function upd_env_ema_gui($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update guia_remision set guia_estado_correo='$dat' where num_comprobante='$id'");
        }
    }

    function upd_env_ema_no($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set com_estado_correo='$dat' where num_factura_modifica='$id' and tipo_comprobante=4");
        }
    }

    function upd_env_ema_ret($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update detalle_retencion set ret_estado_correo='$dat' where num_comprobante='$id'");
        }
    }

    function upd_env_ema_nodeb($dat, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("update comprobantes set com_estado_correo='$dat' where num_factura_modifica='$id' and tipo_comprobante=5");
        }
    }

    ////////////cambios para guayaquil/////////////

    function lista_prod_sec($fbc, $tp, $pro) {
        if ($this->con->Conectar() == true) {
            return pg_query("");
        }
    }

    function lista_titulo($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_modulos where mod_id=$id");
        }
    }

    function update_estado($est, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("Update erp_mp set mp_i=$est where id=$id");
        }
    }

    function lista_producto_total($ems) {
        if ($this->con->Conectar() == true) {
            return pg_query("select p.id,p.mp_c,p.mp_d from erp_mp p,erp_i_movpt_total m, erp_tipos t where p.id=m.pro_id and cast(p.mp_a as integer)=t.id and (t.tps_tipo='1&1&0' or t.tps_tipo='0&1&0') and m.mvt_cant>0");
        }
    }

    function lista_vendedores($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_vendedores $txt");
        }
    }

//    function lista_costos_mov($id) {
//        if ($this->con->Conectar() == true) {
//            return pg_query("select * from erp_i_mov_inv_pt where pro_id=$id order by mov_id desc limit 1");
//        }
//    }
    function lista_costos_mov($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
        }
    }

    function del_asig_list($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_asg_option_list WHERE opl_id=$id");
        }
    }

    function del_option_list($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_option_list WHERE mod_id=$id");
        }
    }

    function del_modulos($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_modulos WHERE mod_id=$id");
        }
    }

    function sum_entregado($id, $cod, $lot) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(d.cantidad) as suma from comprobantes c, detalle_fact_notdeb_notcre d where replace(c.num_documento,'-','')=d.num_camprobante and c.ped_id=$id and d.cod_producto='$cod'");
        }
    }

    function lista_cambia_status($id, $sts) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET ped_estado='$sts' where ped_id=$id");
        }
    }

    function upd_configuraciones($id, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_configuraciones set con_valor='$val' where con_id=$id");
        }
    }

    function lista_cuentas_cod($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * from erp_plan_cuentas where pln_codigo='$id'");
        }
    }

    function upd_configuraciones_sueldo($id, $data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_configuraciones set con_valor='$data[0]', con_valor2='$data[1]', con_valor3='$data[2]' where con_id=$id");
        }
    }

    function update_conf_email($id, $val) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_configuraciones set con_valor2='$val' where con_id=$id");
        }
    }
    
    function upd_sueldo_basico($data){
        if($this->con->Conectar() == true){
            return pg_query("UPDATE erp_configuraciones set con_valor2='$data[0]' where con_id=18");
        }
    }
    
    function upd_sueldo_basico_empleado($data){
        if($this->con->Conectar() == true){
            return pg_query("UPDATE par_empleados set emp_sueldo_inicial='$data[0]' where emp_sueldo_basico=1");
        }
    }
    
    function lista_un_producto_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and id=$id");
        }
    }

}

?>
