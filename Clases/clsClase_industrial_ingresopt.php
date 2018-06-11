<?php

include_once 'Conn.php';

class Clase_industrial_ingresopt {

    var $con;

    function Clase_industrial_ingresopt() {
        $this->con = new Conn();
    }

    function lista_ingreso_industrial($bod) {
        if ($this->con->Conectar() == true) {
//            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod ORDER BY mov_documento desc");
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=3 and m.bod_id=$bod ORDER BY mov_documento desc");
        }
    }

    function lista_secuencial($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt WHERE char_length(mov_documento)=14 ORDER BY mov_documento DESC LIMIT 1");
        }
    }

    function lista_siglas($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt");
        }
    }

    function lista_transaccion($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_transacciones where trs_id=$emp");
        }
    }

    function lista_buscador_industrial_ingresopt($txt, $tr) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp p, erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c  where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=$tr $txt order by m.mov_documento desc");
        }
    }

    function lista_num_productos($txt, $tr) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id, mov_tabla FROM erp_mp p,erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and t.trs_id=$tr $txt group by  mov_tabla,pro_id");
        }
    }

    function insert_industrial_ingresopt($data) {
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
                mov_tabla,
                mov_val_unit,
                mov_val_tot
            )
    VALUES ($data[0],
        $data[8],
        $data[1],
       '$data[6]',
       '$data[2]',
       '$data[3]',
        '0',
       '$data[4]',
       '" . date('Y-m-d') . "',
       '" . date("H:i:s") . "',
       '$data[5]',
       '',
       '$data[7]',
       '$data[8]',
       '$data[9]'
            )");
        }
    }

    function upd_industrial_ingreso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_mov_inv_pt SET 
                mov_fecha_entrega='$data[1]', 
                mov_num_factura='$data[2]', 
                mov_pago='$data[3]', 
                mov_direccion='$data[4]', 
                mov_val_unit='$data[5]', 
                mov_descuento='$data[6]', 
                mov_iva=$data[7], 
                mov_flete='$data[8]' 
                WHERE mov_id=$data[0]");
        }
    }

    function delete_industrial_ingreso($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

    function lista_clientes_tipo($tp) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres FROM  erp_i_cliente where cli_id=$id");
        }
    }

    function lista_productospt_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_i='0' and ids='26' order by mp_d");
        }
    }

    function lista_locales() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor order by emi_nombre_comercial asc");
        }
    }

    function lista_transferencias() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c 
                            where m.trs_id=t.trs_id 
                            and m.cli_id=c.cli_id 
                            and (t.trs_id=20 or t.trs_id=4)
                            ORDER BY m.mov_fecha_trans desc, m.mov_documento desc");
        }
    }

    function lista_transferencias_fecha($desde, $hasta, $emisor) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c, erp_mp p 
                            where m.trs_id=t.trs_id 
                            and m.cli_id=c.cli_id 
                            and m.pro_id=p.id 
                            and (t.trs_id=20)
                            and m.mov_fecha_trans between '$desde' and '$hasta' and m.bod_id=$emisor
                            ORDER BY m.mov_fecha_trans desc, m.mov_documento desc");
        }
    }

    function insert_transferencia($data,$sec) {
        if ($this->con->Conectar() == true) {
            $f = date('Y-m-d');
            $h = date('H:i');
            $usu = strtoupper($_SESSION[User]);
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_cantidad,                
                mov_tabla,                
                mov_fecha_registro,
                mov_hora_registro,
                mov_val_unit,
                mov_val_tot,
                mov_usuario,
                mov_num_fac_entrega,
                mov_doc_tipo
            )
    VALUES ('$data[0]',
            '$data[1]',
            '$data[2]',   
            '$data[3]',
            '$sec',   
            '$data[5]',
            '$data[6]',   
            '$data[7]',
            '$data[8]',
            '$f',
            '$h',
            '$data[9]',
            '$data[10]',
            '$usu',
            '$data[11]',
            '$data[12]')");
        }
    }

    //DOCUMENTOS MOVIMIENTOS
    function lista_ingresos_doc($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT pro_id,mov_tabla,sum(mov_cantidad) FROM erp_i_mov_inv_pt  where mov_documento='$doc' group by pro_id,mov_tabla ");
        }
    }

    function buscar_un_movimiento($id, $tab, $emi) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_mov_inv_pt m, erp_transacciones t, erp_i_cliente c where m.trs_id=t.trs_id and c.cli_id=m.cli_id and t.trs_id=26 and m.pro_id='$id' and m.mov_tabla='$tab' and m.bod_id=$emi ORDER BY m.pro_id,m.mov_tabla");
        }
    }

    function total_ingreso_egreso_fac($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function lista_un_mp_mod1($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp_set WHERE ids<>79 and ids<>80 order by split_part(mp_tipo,'&',10) desc");
        }
    }

    function lista_un_producto_industrial($code) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_mp where mp_c='$code'");
        }
    }

    ////////////// transferencia /////////////////////

    function insert_sec_transferencia($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_secuencial(sec_transferencias) VALUES ('$data')");
        }
    }

    function lista_un_ingreso_industrial($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_mp p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_id=$id");
        }
    }

    function lista_una_transferencia($pto) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT c.cli_raz_social FROM  erp_i_mov_inv_pt m, erp_mp p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.mov_documento='$pto' and m.trs_id=4 GROUP BY c.cli_raz_social");
        }
    }

    function lista_det_transferencia_industrial($doc) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_mp p, erp_transacciones t where m.pro_id=p.id and m.trs_id=t.trs_id and m.mov_documento='$doc' and m.trs_id=4");
        }
    }

    function lista_secuencial_transferencia() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_secuencial ORDER BY sec_id DESC LIMIT 1");
        }
    }

    function lista_un_local($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_cod_punto_emision=$id");
        }
    }

    function lista_producto_total() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_mp WHERE mp_i='0' and ids<>79 and ids<>80 ORDER BY mp_d");
        }
    }

    function lista_producto_total_bod($txt) {/// transferencia
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp p,erp_i_movpt_total m, erp_tipos t where p.id=m.pro_id and t.tps_id=cast(p.mp_a as integer) and m.mvt_cant>0 and p.mp_i='0' $txt");
        }
    }

    function lista_un_producto_id($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where mp_i='0' and ids<>79 and ids<>80 and id=$id");
        }
    }

    function total_ingreso_egreso_fact($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 $txt) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 $txt) as egreso");
        }
    }

    function lista_costos_mov($id, $txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as ingreso,
                                    (select sum(m.mov_val_tot)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as egreso,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='0' $txt) as icnt,
                                    (select sum(m.mov_cantidad)  from erp_i_mov_inv_pt m, erp_transacciones t where m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion='1' $txt) as ecnt");
        }
    }

    function lista_encab_pedidos_venta($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_local, ped_femision, ped_nom_cliente, cli_id 
                            FROM erp_reg_pedido_venta p, erp_det_ped_venta d 
                            WHERE p.ped_id=d.ped_id and p.ped_num_registro='$cod' 
                            GROUP BY ped_local, ped_femision, ped_nom_cliente, cli_id");
        }
    }

    function lista_locales_codcli($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_emisor where emi_cod_cli='$id' ");
        }
    }

    function lista_pedidos_venta($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta p, erp_det_ped_venta d WHERE p.ped_id=d.ped_id and p.ped_num_registro='$cod'");
        }
    }

    function lista_pedido_venta($id, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_reg_pedido_venta p, erp_det_ped_venta d, erp_mp m WHERE p.ped_id=d.ped_id and d.pro_id=m.id and p.ped_id=$id and p.ped_num_registro='$cod'");
        }
    }

    function lista_inventario($code, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT sum(inv.mov_cantidad) as suma FROM erp_i_mov_inv_pt inv,erp_transacciones tr 
WHERE inv.trs_id=tr.trs_id
and inv.mov_guia_transporte='$code' 
and inv.pro_id=$id 
and tr.trs_id=20");
        }
    }

    function total_ingreso_egreso_fac_destino($id, $emi, $tab) {
        if ($this->con->Conectar() == true) {
            return pg_query("select(SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id and t.trs_operacion= 0 and m.bod_id=$emi and m.mov_tabla=$tab) as ingreso,
                                   (SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_transacciones t WHERE m.trs_id=t.trs_id and m.pro_id=$id  and t.trs_operacion= 1 and m.bod_id=$emi and m.mov_tabla=$tab) as egreso");
        }
    }

    function upd_pedido($id, $ped) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_reg_pedido_venta SET 
                ped_estado='$ped' 
                WHERE ped_id=$id");
        }
    }

    function lista_id_pedido($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT ped_id FROM erp_reg_pedido_venta WHERE ped_num_registro='$cod' GROUP BY ped_id");
        }
    }

    function lista_det_pedido($cod, $id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select (SELECT sum(inv.mov_cantidad) as suma FROM erp_i_mov_inv_pt inv,erp_transacciones tr 
        WHERE inv.trs_id=tr.trs_id
        and inv.mov_guia_transporte='$cod' 
        and tr.trs_id=20) as transferencia,
       (SELECT sum(det_cantidad) as suma FROM erp_det_ped_venta WHERE ped_id=$id) as pedido");
        }
    }

    ///////////////////////// NUEVAS CONSULTAS 04-12-2015

    function lista_ruc_proveedor($ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres, cli_ced_ruc  
                             from  erp_i_cliente 
                             where cli_tipo <>'0'
                             and cli_id=$ruc
                             order by nombres");
        }
    }

    function lista_un_reg_factura($ndoc, $ruc) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos where reg_num_documento='$ndoc' and reg_ruc_cliente='$ruc'");
        }
    }

    function lista_ultimo_sec_reg_facturas() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos order by reg_num_registro desc");
        }
    }

    function insert_reg_facturas($data,$sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_documentos(reg_fregistro,
                                                            reg_tipo_documento, 
                                                            reg_num_documento,
                                                            reg_sbt12,
                                                            reg_sbt0,
                                                            reg_sbt_noiva, 
                                                            reg_sbt_excento,
                                                            reg_sbt,
                                                            reg_tdescuento,
                                                            reg_ice,
                                                            reg_irbpnr, 
                                                            reg_iva12,
                                                            reg_propina,
                                                            reg_total,
                                                            reg_ruc_cliente,
                                                            reg_num_registro,
                                                            reg_estado,
                                                            cli_id,
                                                            reg_num_ingreso)
                                                            VALUES
                                                            ('$data[0]',
                                                              $data[1],
                                                             '$data[2]',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                                 '0',
                                                             '$data[3]',
                                                             '$data[4]',
                                                              $data[5],
                                                              $data[6],
                                                             '$sec')");
        }
    }

    function insert_det_reg_facturas($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_reg_det_documentos(reg_id,
                                                                det_codigo_empresa,
                                                                det_descripcion,
                                                                det_cantidad,
                                                                det_tipo,
                                                                det_estado,
                                                                det_vunit
                                                                )
                                                                VALUES
                                                                ($data[0],
                                                                '$data[1]',
                                                                '$data[2]',  
                                                                '$data[3]',   
                                                                 $data[4],                                             
                                                                 $data[5],                                             
                                                                '$data[6]')");
        }
    }

    function lista_id_reg_facturas($sec) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos where reg_num_registro='$sec'");
        }
    }

    function lista_producto_ing_general($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from erp_mp where id=$id and mp_i='0'");
        }
    }

    function lista_un_reg_factura_tip($ndoc, $ruc, $t) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_reg_documentos where reg_num_documento='$ndoc' and reg_ruc_cliente='$ruc' and reg_tipo_documento='$t' and reg_estado<>3");
        }
    }
    
    function lista_tipo_documentos($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from  erp_tip_documentos where tdc_factura=1");
        }
    }

    
    function update_costo_uni($id,$cost) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_mp set mp_p='$cost' where id='$id'");
        }
    }

}

?>
