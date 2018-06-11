<?php

include_once 'Conn.php';

class Clase_pedidospt {

    var $con;

    function Clase_pedidospt() {
        $this->con = new Conn();
    }

    function lista_pedido() {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from (
                            SELECT t.*,p.pro_codigo,p.pro_descripcion,p.pro_uni FROM  erp_i_pedido_producto_terminado t,
                            erp_i_productos p
                            where t.pro_id=p.pro_id 
                            union
                            SELECT t.*,p.pro_a,p.pro_b,p.pro_c FROM  erp_i_pedido_producto_terminado t,
                            erp_productos p
                            where t.pro_id=p.id 
                            ) sbt order by sbt.ped_documento");
        }
    }
    function lista_pedido_cliente($cli) {
        if ($this->con->Conectar() == true) {
            return pg_query("select * from (
                            SELECT t.*,p.pro_codigo,p.pro_descripcion,p.pro_uni FROM  erp_i_pedido_producto_terminado t,
                            erp_i_productos p
                            where t.pro_id=p.pro_id 
                            and t.cli_id=$cli
                            union
                            SELECT t.*,p.pro_a,p.pro_b,p.pro_c FROM  erp_i_pedido_producto_terminado t,
                            erp_productos p
                            where t.pro_id=p.id 
                            and t.cli_id=$cli
                            ) sbt order by sbt.ped_documento");
        }
    }

    function lista_secuencial_documento() {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado ORDER BY ped_documento DESC LIMIT 1");
        }
    }

    function lista_ingreso_pedido_documento($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_documento='$id'");
        }
    }

    function lista_ingreso_pedido_documento_nop($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_documento='$id'");
        }
    }

    function lista_cantidad($doc, $cod) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t WHERE m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.mov_documento='$doc' and p.pro_codigo='$cod' and t.trs_operacion= 1");
        }
    }

    function lista_cantidad_nop($doc, $cod) {

        if ($this->con->Conectar() == true) {
            return pg_query("SELECT SUM(m.mov_cantidad)as suma FROM erp_i_mov_inv_pt m, erp_productos p, erp_transacciones t WHERE m.pro_id=p.id and m.trs_id=t.trs_id and m.mov_documento='$doc' and p.pro_a='$cod' and t.trs_operacion= 1");
        }
    }

    function insert_pedido($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_pedido_producto_terminado(
                pro_id,
                cli_id,
                trs_id,
                bod_id,
                ped_documento,
                ped_fecha_registro,
                ped_cantidad,
                ped_estado
            )
    VALUES ($data[0],$data[1],'20',$data[2],'$data[3]','$data[4]','$data[5]',3)");
        }
    }

    function upd_pedido($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("UPDATE erp_i_pedido_producto_terminado SET 
                ped_guia_transporte='$data[8]', 
                ped_transportista='$data[9]',
                    ped_estado='$data[11]'
                WHERE ped_id=$data[0]");
        }
    }

    function delete_industrial_ingreso($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("DELETE FROM erp_i_mov_inv_pt WHERE mov_documento='$id'");
        }
    }

    function insert_egreso($data) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_i_mov_inv_pt(
                pro_id,
                trs_id,
                cli_id,
                bod_id,
                mov_documento,
                mov_guia_transporte,
                mov_fecha_trans,
                mov_fecha_registro,
                mov_hora_registro,
                mov_cantidad,
                mov_tranportista
            )VALUES ($data[1],20,$data[3],$data[4],'$data[5]','$data[6]','$data[7]','" . date('Y-m-d') . "','" . date("H:i:s") . "','$data[10]','$data[9]')

        
        
                    ");
        }
    }

    function insert_seguimiento($data,$bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("INSERT INTO erp_seguimiento_pedido(
            seg_orden,
            seg_guia, 
            seg_fecha, 
            seg_transportista, 
            seg_aux_orden, 
            pro_id, 
            seg_cantidad, 
            pro_tipo,
            bod_id)
    VALUES ('$data[5]',
        '$data[6]',
        '$data[7]',
        '$data[9]',
        '$data[5]',
         $data[1],
         $data[10],
         0,
         $bod
         )
            
            
            ");
        }
    }

    function lista_proveedor() {
        if ($this->con->Conectar() == true) {
            return pg_query("select cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres  
from  erp_i_cliente 
where cli_tipo <>'$tp'
order by nombres");
        }
    }

    function lista_un_proveedor($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres   FROM  erp_i_cliente where cli_id=$id ");
        }
    }

    function lista_un_proveedor_codigo($cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT cli_id, trim(cli_apellidos || ' ' || cli_nombres || ' ' || cli_raz_social) as nombres   FROM  erp_i_cliente where cli_codigo='$cod' ");
        }
    }

    function lista_producto($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.bod_id=$id order by p.emp_id, p.pro_descripcion");
        }
    }

    function lista_producto_noperti($bod) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m,
 erp_productos p,
 erp_productos_set ps,
  erp_transacciones t 
  where m.pro_id=p.id
  and m.trs_id=t.trs_id 
  and p.ids=ps.ids 
  and m.bod_id=$bod
  and m.mov_tabla=1 
  order by p.pro_a");
        }
    }

    function lista_un_producto_ind($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_productos where pro_codigo='$id'");
        }
    }

    function lista_un_producto_nop($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_productos where pro_a='$id'");
        }
    }

    function lista_mov_prod_ind($bod, $cod, $opr) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT m.*,p.*,t.*,ps.pro_tipo FROM  erp_i_mov_inv_pt m,
  erp_productos p,
  erp_transacciones t 
  where m.pro_id=p.id
  and m.trs_id=t.trs_id 
  and m.bod_id=$bod
  and p.pro_a='$cod'
  and t.trs_operacion=$opr
  and m.mov_tabla=1 
  order by p.pro_a");
        }
    }

    function lista_inv_prod_nop($bod, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(sbt.ing) as total from (
SELECT sum(m.mov_cantidad) as ing FROM  erp_i_mov_inv_pt m,
 erp_productos p,
  erp_transacciones t 
  where m.pro_id=p.id
  and m.trs_id=t.trs_id 
  and m.bod_id=$bod
  and p.pro_a='$cod'
  and t.trs_operacion=0
  and m.mov_tabla=1
union
SELECT sum(-m.mov_cantidad) as ing FROM  erp_i_mov_inv_pt m,
 erp_productos p,
  erp_transacciones t 
  where m.pro_id=p.id
  and m.trs_id=t.trs_id 
  and m.bod_id=$bod
  and p.pro_a='$cod'
  and t.trs_operacion=1
  and m.mov_tabla=1
  ) sbt
   ");
        }
    }

    function lista_inv_prod_ind($bod, $cod) {
        if ($this->con->Conectar() == true) {
            return pg_query("select sum(sbt.ing) as total from (
SELECT sum(m.mov_cantidad) as ing FROM  erp_i_mov_inv_pt m,
 erp_i_productos p,
  erp_transacciones t 
  where m.pro_id=p.pro_id
  and m.trs_id=t.trs_id 
  and m.bod_id=$bod
  and p.pro_codigo='$cod'
  and t.trs_operacion=0
  and m.mov_tabla=0
union
SELECT sum(-m.mov_cantidad) as ing FROM  erp_i_mov_inv_pt m,
 erp_i_productos p,
  erp_transacciones t 
  where m.pro_id=p.pro_id
  and m.trs_id=t.trs_id 
  and m.bod_id=$bod
  and p.pro_codigo='$cod'
  and t.trs_operacion=1
  and m.mov_tabla=0
  ) sbt
   ");
        }
    }

    function lista_buscador_pedido($txt) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM erp_i_pedido_producto_terminado m, erp_i_productos p,erp_transacciones t, erp_i_cliente c where p.pro_id= m.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id $txt order by m.ped_documento desc");
        }
    }

    function lista_ultimo_ingreso_pedido($emp) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_mov_inv_pt m, erp_i_productos p, erp_transacciones t, erp_i_cliente c, erp_empresa e where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and e.emp_id=p.emp_id and mov_id =$emp");
        }
    }

    function lista_un_pedido_ind($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_i_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.pro_id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_id=$id");
        }
    }

    function lista_un_pedido_nop($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  erp_i_pedido_producto_terminado m, erp_productos p, erp_transacciones t, erp_i_cliente c where m.pro_id=p.id and m.trs_id=t.trs_id and m.cli_id=c.cli_id and m.ped_id=$id");
        }
    }

///////////////////////////////////////////////////////////////////////////////////////  

    function lista_una_bodega($id) {
        if ($this->con->Conectar() == true) {
            return pg_query("SELECT * FROM  emisor where cod_punto_emision=$id");
        }
    }

}

?>
